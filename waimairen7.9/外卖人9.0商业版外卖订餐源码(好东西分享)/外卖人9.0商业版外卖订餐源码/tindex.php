<?php
/*
 $computer=new COM("ASPComm.EUCPCom");
//$result = $computer->Register("3SDK-GHK-0130-QDRMT","683369","亿美软通","周风涛","64678505","13811028685","zhoufengtao@emay.net.cn","64678505","北","1000000");
//echo $result;
 // $balance;
$result = $computer->SendSMS("3SDK-GHK-0130-QDRMT", '18538129330', "测试", 0);
///$result = $computer->GetBalance("3SDK-GHK-0130-QDRMT",$balance);
echo $result;
//var_dump($balance);
//print_r($balance);
*/
phpinfo();
/*
define('hopedir', dirname(__FILE__).DIRECTORY_SEPARATOR);  
 
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息
error_reporting(-1); 
 
$pubKey = file_get_contents('plug/pay/alipayapp/key/alipay_public_key.pem');
 
 
  
  print_r($pubKey);
     $res = openssl_get_publickey($pubKey);
	 print_r($res);
    $result = (bool)openssl_verify('xxx', base64_decode('ccc'), $res);
	
    openssl_free_key($res);  */    
?>