<?php 
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
//说明支付宝配置接口文件
 $setinfo = array(
        'name'=>'paypal',
        'apiname'=>'paypal',
        'logourl'=>'btn_buynowCC_LG.gif',
        'forpay'=>1,
  ); 
 $plugsdata = array(
  	'0'=> array('title'=>'paypalbusinessemail',	'name'=>'business',	'desc'=>''),
  	'1'=> array('title'=>'currency_code',	'name'=>'currency_code',	'desc'=>''),
	'2'=> array('title'=>'clientId',	'name'=>'clientId',	'desc'=>''),
	'3'=> array('title'=>'secret',	'name'=>'secret',	'desc'=>''),
	'4'=>array('title'=>'沙箱环境录入1','name'=>'is_sanbox','desc'=>'')
  ); 
?>