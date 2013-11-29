<?php
include "db.php";
include "testurl.php";

if(empty($_POST))
    exit();

$_POST['url'] = trim($_POST['url']);
if (empty($_POST['url']))
    error("请填写URL！");
if (empty($_POST['email']))
    error("请填写 Email!");

$retval = test_url($_POST['url'], $_POST['includestr']);
if ($retval != 0) {
    error("抱歉，您指定的URL不正常，错误信息为：".status2name($retval));
}
$url = addslashes($_POST['url']);

$email = addslashes($_POST['email']);
$rs = mysql_query("SELECT COUNT(*) FROM host WHERE `url`='$url' AND `email`='$email'");
$row = mysql_fetch_array($rs);
if ($row[0] > 0)
    error("你已经监控了此URL！如需修改，请联系我：lug@ustc.edu.cn");

$rs = mysql_query("SELECT COUNT(*) FROM host WHERE `email`='$email'");
$row = mysql_fetch_array($rs);
if ($row[0] >= 100)
    error("每个 Email 至多只能设置100个通知，谢谢！如果需要移除不用的URL，请联系我：lug@ustc.edu.cn");

$includestr = addslashes(trim($_POST['includestr']));
mysql_query("INSERT INTO host SET `url`='$url',`status`='0',`email`='$email',`includestr`='$includestr'");
if (!($id = mysql_insert_id()))
    error("内部错误，添加失败，请重试。");

$msg = '您已成功添加URL: '.shortenurl($url,80).' [ServMon@LUG]';
mail($email, $msg, "RT");

success("添加成功，您将收到一封邮件通知。");

function error($msg) {
    echo json_encode(array('status'=>false,'msg'=>$msg));
    exit();
}
function success($msg) {
    echo json_encode(array('status'=>true,'msg'=>$msg));
    exit();
}
