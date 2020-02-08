<?php 
//说明支付宝配置接口文件

if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
 $setinfo = array(
        'name'=>'微信支付',
        'apiname'=>'weixin',
        'logourl'=>'',
        'forpay'=>2,
  ); 
 $module=IReq::get('module');
  if($module == 'installpay'){
	 include_once($logindir.'/'.$idtype.'/lib/WxPay.Config.php');
	 $plugsdata = array(
		'0'=> array('title'=>'开发者ID(AppID)',	'name'=>'APPID',	'desc'=>'','type'=>'input','values'=>WxPayConfig::APPID),
		'1'=> array('title'=>'商户号',	'name'=>'MCHID',	'desc'=>'','type'=>'input','values'=>WxPayConfig::MCHID),
		'3'=> array('title'=>'API32位密钥',	'name'=>'KEY',	'desc'=>'','type'=>'input','values'=>WxPayConfig::KEY),
		'4'=> array('title'=>'开发者密码AppSecret',	'name'=>'APPSECRET',	'desc'=>'','type'=>'input','values'=>WxPayConfig::APPSECRET)
	  ); 
  }else{
	include_once($logindir.'/weixin/lib/WxPay.Config.php');
	$plugsdata = array(
		'0'=> array('title'=>'开发者ID(AppID)',	'name'=>'APPID',	'desc'=>'','type'=>'input','values'=>WxPayConfig::APPID),
		'1'=> array('title'=>'商户号',	'name'=>'MCHID',	'desc'=>'','type'=>'input','values'=>WxPayConfig::MCHID),
		'3'=> array('title'=>'API32位密钥',	'name'=>'KEY',	'desc'=>'','type'=>'input','values'=>WxPayConfig::KEY),
		'4'=> array('title'=>'开发者密码AppSecret',	'name'=>'APPSECRET',	'desc'=>'','type'=>'input','values'=>WxPayConfig::APPSECRET)
	  ); 
  }
?>