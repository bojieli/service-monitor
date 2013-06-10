<?php
print_r($_POST);
if (empty($_POST['token']))
    exit();
if ($_POST['token'] !== 'M2La02Jd334Os3Nx')
    exit();

include_once "sms.php";
$msg = substr($_POST['msg'],0,100);
$mobiles = explode(',', $_POST['mobile']);
var_dump(sendSms($msg, $mobiles));
?>
