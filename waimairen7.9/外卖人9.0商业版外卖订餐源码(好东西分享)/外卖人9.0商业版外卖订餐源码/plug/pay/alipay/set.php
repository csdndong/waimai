<?php 
//说明支付宝配置接口文件
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
 $setinfo = array(
        'name'=>'支付宝PC支付',
        'apiname'=>'alipay',
        'logourl'=>'alipay.gif',
        'forpay'=>1,
  ); 
 $plugsdata = array(
  	'0'=> array('title'=>'合作伙伴身份（PID）',	'name'=>'partner',	'desc'=>'','type'=>'input'),
  	'1'=> array('title'=>'MD5密钥',	'name'=>'key',	'desc'=>'','type'=>'input'),
  	'2'=> array('title'=>'支付宝账号',	'name'=>'seller_email',	'desc'=>'','type'=>'input') 
  );
   
?>