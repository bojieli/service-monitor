<?php
// This file should be run every minute by crontab
include "db.php";
include "config.php";
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
        $status = test_url($row['url'], $row['includestr'], $row['ip_version']);
        save_full_log($row['id'], $status);
        update_last_probe($row['id']);
        $orig_status = mysql_result(mysql_query("SELECT status FROM host WHERE id='" . $row['id'] . "'"), 0);
        if ($status != $orig_status)
            notify_change($row['id'], $row['url'], $row['mobile'], $row['email'], $status);
    }
}

function save_full_log($id, $status) {
    global $elapsed_time;
    mysql_query("INSERT INTO full_log SET id=$id, status=$status, time=".time().", response_time=".(int)($elapsed_time*1000));
}

function update_last_probe($id) {
    global $elapsed_time;
    mysql_query("UPDATE host SET lastprobe='".time()."',response_time=".(int)($elapsed_time*1000)." WHERE id='$id'");
}

function notify_change($id, $url, $mobile, $email, $status) {
    if (!is_numeric($id) || !is_numeric($status))
        return;
    global $error_detail;
    mysql_query("UPDATE host SET `status`='$status', `last_status_change`='".time()."' WHERE `id`='$id'");
    mysql_query("INSERT INTO host_log SET `id`='$id',`time`='".time()."',`status`='$status',`detail`='".addslashes($error_detail)."'");

    $msg = status2name($status).": $url [ServMon@LUG]";
    echo $msg."\n";

    mail($email, $msg, "Detail: $error_detail");

/*
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
    */
}

