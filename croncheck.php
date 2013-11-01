<?php
// This file should be run every minute by crontab
include "db.php";
include "config.php";
include "sms.php";
include "testurl.php";
date_default_timezone_set('Asia/Chongqing');
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

echo "Test start: ".date('Y-m-d H:i:s')."\n";
test_all();
echo "Test end: ".date('Y-m-d H:i:s')."\n\n";

function test_all() {
    global $conf;
    $rs = mysql_query("SELECT * FROM host");
    if (!$rs) {
        echo "Error Connecting database\n";
        return;
    }
    while ($row = mysql_fetch_array($rs)) {
        echo "Test URL ".$row['url']."\n";
        $status = test_url($row['url'], $row['includestr']);
        update_last_probe($row['id']);
        if ($status != $row['status'])
            notify_change($row['id'], $row['url'], $row['mobile'], $status);
    }
}

function update_last_probe($id) {
    mysql_query("UPDATE host SET lastprobe='".time()."' WHERE id='$id'");
}

function notify_change($id, $url, $mobile, $status) {
    if (!is_numeric($id) || !is_numeric($status))
        return;
    global $error_detail;
    mysql_query("INSERT INTO host_log SET `id`='$id',`time`='".time()."',`status`='$status',`detail`='".addslashes($error_detail)."'");
    mysql_query("UPDATE host SET `status`='$status' WHERE `id`='$id'");

    $msg = status2name($status).': '.shortenurl($url,80).' [ServMon@LUG]';
    echo $msg."\n";

    mail('servmon@blog.ustc.edu.cn', $msg, "Detail: $error_detail");

    // 24 hours maximum 10 msgs for each host
    $rs = mysql_query("SELECT COUNT(*) FROM sms_log WHERE `id`='$id' AND `time`>'".(time()-86400)."'");
    if (!$rs) {
        echo "Database query failed, not sending SMS\n";
        return;
    }
    $row = mysql_fetch_array($rs);
    if ($row[0] > 10) {
        echo "SMS limit for ID $id exceeded, not sending SMS\n";
        return;
    }
    echo $row[0]."\n";

    sendSms($msg, array($id=>$mobile));
}

