<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
	$hopedir = '../../../';
$weixindir = $hopedir.'plug/pay/weixin/';
require_once $weixindir."lib/WxPay.Api.php";
require_once $weixindir.'lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler($hopedir."/log/wxpay".date('Y-m-d').".log");
$log = Log::Init($logHandler, 15);




class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
		   
			$hopedir = '../../../';
			$config = $hopedir."config/hopeconfig.php";   
			$cfg = include($config); 
		
		   //订单号 
		   /*   $result结果集
		   {"appid":"wx9e8e0ee1e7bc9ecb","attach":"14284541672118","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1234454802","nonce_str":"j0ojkmvgf16o6t0dz45pb8h9j9zgh5wj","openid":"oSPqZjocUN11cSbParngKfCZIJUw","out_trade_no":"302","result_code":"SUCCESS","return_code":"SUCCESS","sign":"7490C1E78C0E05A83A9902E033DB56E6","time_end":"20150408084938","total_fee":"1","trade_type":"JSAPI","transaction_id":"1001600750201504080051156176"}
		   
		   
		   */
			 
			 $lnk = mysql_connect($cfg['dbhost'], $cfg['dbuser'], $cfg['dbpw']) or die ('Not connected : ' . mysql_error()); 
  			 $version = mysql_get_server_info(); 
  			 if($version > '4.1' && $cfg['dbcharset']) {
						  	mysql_query("SET NAMES '".$cfg['dbcharset']."'");
  			 } 
  			 if($version > '5.0') {
				     mysql_query("SET sql_mode=''");
  			 } 
 		    if(!@mysql_select_db($cfg['dbname'])){ 
				 	if(@mysql_error()) {
								echo '数据库连接失败';exit;
					 } else {
								mysql_select_db($cfg['dbname']);
	     			 }
  			 }
			 
			$checked =  explode('_',$result['attach'] );
			if( count($checked) > 1 ){    // 微信支付  在线充值
				
					$uid = $checked[1]; 
				   $info =  mysql_query("SELECT * from `".$cfg['tablepre']."member` where uid = ".$uid." "); 
				   $memberinfo = mysql_fetch_assoc($info); 
				   $checkinfo =mysql_query("SELECT * from `".$cfg['tablepre']."onlinelog` where dno = '".$result['transaction_id']."' "); 
				   $checkinfo = mysql_fetch_assoc($checkinfo);
				   if(empty($checkinfo)){
					    $acountcost = $result['total_fee']*0.01;
						$dotime = time();  
						mysql_query("INSERT INTO `".$cfg['tablepre']."onlinelog` (`id` ,`type` ,`upid` ,`dno` ,`cost` ,`status` ,`addtime` ,`source`    )VALUES (NULL , 'acount', '".$memberinfo['uid']."', '".$result['transaction_id']."', '".$acountcost."', '1' , '".$dotime."','0');");
				 
						mysql_query("UPDATE  `".$cfg['tablepre']."member` SET  `cost` =  `cost`+".$acountcost." where `uid`=".$memberinfo['uid']."");   
						$checkcost = $memberinfo['cost']+$acountcost;
						mysql_query("INSERT INTO `".$cfg['tablepre']."memberlog` (`id` ,`userid` ,`type` ,`addtype` ,`result` ,`addtime` ,`content` ,`title` ,`acount` )VALUES (NULL , '".$memberinfo['uid']."', '2', '1', '".$acountcost."', '".$dotime."', '在线充值', '使用微信支付充值".$acountcost."元', '".$checkcost."');"); 
						 
						mysql_query("INSERT INTO `".$cfg['tablepre']."memcostlog`(`uid`,`username`,`cost`,`bdtype`,`bdcost`,`curcost`,`bdliyou`,`czuid`,`czusername`,`addtime`) VALUES('".$memberinfo['uid']."','".$memberinfo['username']."','".$memberinfo['cost']."',1,'".$acountcost."','".$checkcost."','微信充值','".$memberinfo['uid']."','".$memberinfo['username']."','".$dotime."');");
						$acountid = mysql_insert_id($lnk); 
						$acountinfo = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=acountpayaddlog&dno=".$result['transaction_id']);

						 
				   }
				 
				
			}else{
			 
			 if($result['attach'] == 'a'){    // 微信支付  闪惠订单 
					$orderdno = substr($result['out_trade_no'] , 2);
 				    $info =  mysql_query("SELECT * from `".$cfg['tablepre']."shophuiorder` where id = ".$orderdno." ");
					$backinfog = mysql_fetch_assoc($info);
					
					$total_fee = $result['total_fee']*0.01;
					
				    if(!empty($backinfog) && $backinfog['status'] == 0 && $total_fee >=  $backinfog['sjcost']){ 
						mysql_query("UPDATE  `".$cfg['tablepre']."shophuiorder` SET  `paystatus` =  1,`status` =  1,`completetime` =  ".time().",`paytime` = '".time()."',`paytype_name` = 'weixin'  where `id`=".$orderdno.""); 


						if($backinfog['uid'] > 0 && $backinfog['givejifen'] > 0){ 
							$memberinfo =  mysql_query("SELECT * from `".$cfg['tablepre']."member` where uid = ".$backinfog['uid']." "); 
							$memberinfo = mysql_fetch_assoc($memberinfo);
							if(!empty($memberinfo)){
								$sorce = $backinfog['givejifen'];
								mysql_query("UPDATE  `".$cfg['tablepre']."member` SET  `score`=`score`+".$sorce." where `uid`=".$backinfog['uid']."");  
								$allscore = $memberinfo['score']+$sorce;  
								mysql_query("INSERT INTO `".$cfg['tablepre']."memberlog` (`id` ,`userid` ,`type` ,`addtype` ,`result` ,`addtime` ,`content` ,`title` ,`acount` )VALUES (NULL , '".$memberinfo['uid']."', '1', '1', '".$sorce."', '".time()."', '优惠买单', '赠送积分".$sorce."元', '".$memberinfo['cost']."');");
							}
							
						}  
				    }
			 }else{    // 微信支付  普通订单
				 
			  $info =  mysql_query("SELECT * from `".$cfg['tablepre']."order` where id = ".$result['out_trade_no']." ");
              $backinfog = mysql_fetch_assoc($info);
			  $total_fee = $result['total_fee']*0.01;
			  if(!empty($backinfog) && $backinfog['status'] == 0&& $total_fee >=  $backinfog['allcost'] && $backinfog['paystatus'] == 0){
			  	mysql_query("UPDATE  `".$cfg['tablepre']."order` SET  `paystatus` =  1,`status` =  1 ,`trade_no` = ".$result['transaction_id']." ,`paytype_name` = 'weixin',`paytime` = '".time()."' ,`addtime` = '".time()."'  where `id`=".$result['out_trade_no']."");  
			 
					$info = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=postmsgbypay&orderid=".$result['out_trade_no']);
			  }
			  
			  }
			  
		}
			  mysql_close($lnk);  
			 
			 
		
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
