<?php
include "db.php";
include "testurl.php";

if(empty($_POST))
    exit();

$_POST['url'] = trim($_POST['url']);
if (empty($_POST['url']))
    error("请填写URL！");
if (empty($_POST['mobile']))
    error("请填写手机号！");

if (strtolower($_POST['invitation']) != strtolower('ServerMonitor')) {
    error("邀请码不正确。目前服务处于内测阶段");
}

$retval = test_url($_POST['url'], $_POST['includestr']);
if ($retval != 0) {
    error("抱歉，您指定的URL不正常，错误信息为：".status2name($retval));
}
$url = addslashes($_POST['url']);

$mobile = $_POST['mobile'];
if (!is_numeric($mobile)) {
    error("手机号码必须是数字！");
}
$rs = mysql_query("SELECT COUNT(*) FROM host WHERE `url`='$url' AND `mobile`='$mobile'");
$row = mysql_fetch_array($rs);
if ($row[0] > 0)
    error("你已经监控了此URL！如需修改，请联系我：bojieli@gmail.com");

$rs = mysql_query("SELECT COUNT(*) FROM host WHERE `mobile`='$mobile'");
$row = mysql_fetch_array($rs);
if ($row[0] >= 100)
    error("每个手机号至多只能设置100个短信通知，谢谢！如果需要移除不用的URL，请联系我：bojieli@gmail.com");

$includestr = addslashes(trim($_POST['includestr']));
mysql_query("INSERT INTO host SET `url`='$url',`status`='0',`mobile`='$mobile',`includestr`='$includestr'");
if (!($id = mysql_insert_id()))
    error("内部错误，添加失败，请重试。");

include "sms.php";
$msg = '您已成功添加URL: '.shortenurl($url,80).' [ServMon@LUG]';
sendSms($msg, array($id=>$mobile));

$adminmobile = '18715009901';
if ($mobile != $adminmobile) {
    $adminmsg = $mobile.'添加了URL: '.shortenurl($url,80);
    sendSms($adminmsg, array($id=>$adminmobile));
}

success("添加成功，您将收到一条短信通知。");

function error($msg) {
    echo json_encode(array('status'=>false,'msg'=>$msg));
    exit();
}
function success($msg) {
    echo json_encode(array('status'=>true,'msg'=>$msg));
    exit();
}
