<?php 
//说明支付宝配置接口文件
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
 $setinfo = array(
        'name'=>'支付宝app支付',
        'apiname'=>'alipayapp',
        'logourl'=>'alipay.gif',
        'forpay'=>4,
  ); 
  if(file_exists($logindir.'/'.$idtype.'/key/rsa_private_key.pem')){
	$rsa_private_key = file_get_contents($logindir.'/'.$idtype.'/key/rsa_private_key.pem');
  }else{
	$rsa_private_key='';
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
   if(file_exists($logindir.'/'.$idtype.'/key/pk8.txt')){
	$pk8 = file_get_contents($logindir.'/'.$idtype.'/key/pk8.txt');
   }else{
	 $pk8 = '';
   } 
   
 
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
  
  $str = str_replace(' ', '', $pk8); 
  $str = str_replace('-----BEGINPUBLICKEY-----', '', $str); 
  $str = str_replace(PHP_EOL, '', $str); 
  $str = str_replace('-----ENDPUBLICKEY-----', '', $str); 
  $str = str_replace(array("\r","\n","\r\n"),"",$str);
  // print_r($str);
  $pk8 =chunk_split($str, 64, PHP_EOL);
  
 $plugsdata = array(
  	'0'=> array('title'=>'合作伙伴身份（PID）',	'name'=>'partner',	'desc'=>'','type'=>'input'),
	'1'=> array('title'=>'支付宝账号',	'name'=>'seller_email',	'desc'=>'','type'=>'input'),
	'3'=> array('title'=>'开发者私钥',	'name'=>'rsa_private_key',	'desc'=>'注意不要有空格','type'=>'textarea','values'=>$rsa_private_key),
	'4'=> array('title'=>'开发者公钥',	'name'=>'rsa_public_key',	'desc'=>'注意不要有空格','type'=>'textarea','values'=>$rsa_public_key),
	'5'=> array('title'=>'pk8',	'name'=>'pk8',	'desc'=>'注意不要有空格','type'=>'textarea','values'=>$pk8),
	'6'=> array('title'=>'支付宝公钥',	'name'=>'alipay_public_key',	'desc'=>'注意不要有空格','type'=>'textarea','values'=>$alipay_public_key),
  );
   
?>