<?php
function sendSms($msg,$mobiles)
{
    $url="http://umess.ustc.edu.cn/uMessApi.php?wsdl";//接口地址

    for ($retry=0; $retry<3; $retry++) {
        try {
            $client=new SoapClient($url,array('encoding'=>'UTF-8'));
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            continue;
        }
        break;
    }
    if (empty($client))
        return array('done'=>0,'failed'=>count($mobiles));

    //远程调用
    try {
    	$client->wsClientSetCharset('UTF-8');
    	$client->wsCsLogin('huodong','hzbjlsjr2012');
    	$messageId=$client->wsCreateMessage($messageTitle='',$msg,$messageFromAddress="",$messageFromName="",$messageContentFormat="plaintext");
    } catch(Exception $e) {
	return $e;
    }
    if ($messageId == 'soap_fault')
        return 'soap_fault';
    $i=0;
    $j=0;

    foreach($mobiles as $id => $mobile)
    {
	try {
        	$client->wsMessageAddReceiver($messageId,'mobile',$mobile,'sms',$messagePriority=1,$sendTime=null);
        	$re=$client->wsMessageSend($messageId);
	} catch(Exception $e) {
		return $e;
	}
        if($re)
        {
            $i++;
            $status='1';
        }
        else
        {
            $j++;
            $status='0';
        }
        mysql_query("INSERT INTO sms_log SET `status`='$status',`id`='".addslashes($id)."',`time`='".time()."',`msg`='".addslashes($msg)."',`mobile`='".addslashes($mobile)."'");
    }

    $client->wsMessageClose($messageId);
    return array('done'=>$i,'failed'=>$j);
}

function shortenurl($url, $length) {
    if (strlen($url) <= $length)
        return $url;
    else
        return substr($url,0,$length-3) . '...';
}
