<?php 
//说明网站支付设置接口
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
 $setinfo = array(
        'name'=>'支付宝手机支付',
        'apiname'=>'alimobile',
        'logourl'=>'',
        'forpay'=>2,
  ); 
  //include_once(
  if(file_exists($logindir.'/'.$idtype.'/key/rsa_private_key.pem')){
  $rsa_private_key = file_get_contents($logindir.'/'.$idtype.'/key/rsa_private_key.pem');
  }else{
	  $rsa_private_key = '';
  }
  if(file_exists($logindir.'/'.$idtype.'/key/rsa_public_key.pem')){
  $rsa_public_key = file_get_contents($logindir.'/'.$idtype.'/key/rsa_public_key.pem');
  }else{
	  $rsa_public_key = '';
  }
  if(file_exists($logindir.'/'.$idtype.'/key/alipay_public_key.pem')){
	$alipay_public_key = file_get_contents($logindir.'/'.$idtype.'/key/alipay_public_key.pem');
  }else{
	  $alipay_public_key = '';
  }
// print_r($logindir.'/'.$idtype.'/key/rsa_private_key.pem');
// $res = openssl_get_privatekey($rsa_private_key);
//	echo $pubKey;
	 // print_r($res); //openssl_pkey_get_public
 //分割字符串函数
   
 
  //print_r($str);
  $str = str_replace(' ', '', $rsa_private_key); 
  $str = str_replace('-----BEGINRSAPRIVATEKEY-----', '', $str); 
  $str = str_replace(PHP_EOL, '', $str); 
  $str = str_replace('-----ENDRSAPRIVATEKEY-----', '', $str); 
  $str = str_replace(array("\r","\n","\r\n"),"",$str);
  $rsa_private_key =chunk_split($str, 64, PHP_EOL);
  
  $str = str_replace(' ', '', $rsa_public_key); 
  $str = str_replace('-----BEGINPUBLICKEY-----', '', $str); 
  $str = str_replace(PHP_EOL, '', $str); 
  $str = str_replace('-----ENDPUBLICKEY-----', '', $str); 
   $str = str_replace(array("\r","\n","\r\n"),"",$str);
  $rsa_public_key =chunk_split($str, 64, PHP_EOL);
  
  
  $str = str_replace(' ', '', $alipay_public_key); 
  $str = str_replace('-----BEGINPUBLICKEY-----', '', $str); 
  $str = str_replace(PHP_EOL, '', $str); 
  $str = str_replace('-----ENDPUBLICKEY-----', '', $str); 
  $str = str_replace(array("\r","\n","\r\n"),"",$str);
  // print_r($str);
  $alipay_public_key =chunk_split($str, 76, PHP_EOL);
  
  // print_r($str); 
  
  // $certificateCApemContent =  '-----BEGIN RSA PRIVATE KEY-----'.PHP_EOL
    // .chunk_split($str, 64, PHP_EOL)
    // .'-----END RSA PRIVATE KEY-----'.PHP_EOL;
	// print_r($certificateCApemContent);
	
	//76位
	//var_export($,true)
	// print_r($alipay_public_key);
	 // $str = str_replace(' ', '', $alipay_public_key); 
	// $str = str_replace('-----BEGINPUBLICKEY-----', '', $str);  
   // $str = str_replace(PHP_EOL, '', $str); 
  // $str = str_replace('-----ENDPUBLICKEY-----', '', $str); 
   
   // var_dump($str);
  
	 // $certificateCApemContent =  '-----BEGIN PUBLIC KEY-----'.PHP_EOL
    // .chunk_split($str, 77, PHP_EOL)
    // .'-----END PUBLIC KEY-----'.PHP_EOL;
	// print_r($certificateCApemContent);
 
 $plugsdata = array(
  	'0'=> array('title'=>'合作伙伴身份（PID）',	'name'=>'partner',	'desc'=>'','type'=>'input'),
  	'1'=> array('title'=>'MD5密钥',	'name'=>'key',	'desc'=>'','type'=>'input'),
  	'2'=> array('title'=>'支付宝账号',	'name'=>'seller_email',	'desc'=>'','type'=>'input'),
	'3'=> array('title'=>'开发者私钥',	'name'=>'rsa_private_key',	'desc'=>'注意不要有空格','type'=>'textarea','values'=>$rsa_private_key),
	'4'=> array('title'=>'开发者公钥',	'name'=>'rsa_public_key',	'desc'=>'注意不要有空格','type'=>'textarea','values'=>$rsa_public_key),	
	'5'=> array('title'=>'支付宝公钥',	'name'=>'alipay_public_key',	'desc'=>'注意不要有空格','type'=>'textarea','values'=>$alipay_public_key) 
 ); 
 
  
?>