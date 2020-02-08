<?php 
date_default_timezone_set("Asia/Hong_Kong"); //时间区域
// ini_set('display_errors',1);            //错误信息
// ini_set('display_startup_errors',1);    //php启动错误信息
// error_reporting(-1); 
header("Content-Type:text/html;charset=utf-8"); //输出格式 
$hopedir = '../../../';
define('hopedir', $hopedir);  
$config = $hopedir."config/hopeconfig.php";   
$cfg = include($config); 
$mmc = include('paypal.config.php');
include($hopedir.'/lib/function.php');
 logwrite('paypal_captrue');
$main_domain = 'api.paypal.com';
if($alipay_config['is_sanbox'] == '1'){
	
  $main_domain = 'api.sandbox.paypal.com';
}
$clientId = $alipay_config['clientId'];
$secret = $alipay_config['secret'];  
try{
	if(!isset($_GET['paypayid']) || empty($_GET['paypayid'])){
		throw new Exception("参数错误"); 
	}
	$ch = curl_init();  
	curl_setopt($ch, CURLOPT_URL, "https://".$main_domain."/v1/oauth2/token");
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSLVERSION , 6); //NEW ADDITION
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); 
	$result = curl_exec($ch); 
	if(empty($result)) throw new Exception("检测账号失败");  
	else
	{
		$json = json_decode($result,true);
		
		curl_setopt($ch, CURLOPT_URL, "https://".$main_domain."/v1/payments/payment/".$_GET['paypayid']);
		curl_setopt($ch, CURLOPT_POST, FALSE);
								// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type:application/json',
		"Authorization: Bearer " . $json['access_token'])
		// "Content-length: ".strlen($data))
		);
	  
		$result = curl_exec($ch); 
		if(empty($result)) throw new Exception("获取订单失败");   
		else {
				$json_output = json_decode($result,true);
				$json_output = json_decode($result,true);
				if(!isset($json_output['id'])){
					throw new Exception("已经验证过");
				} 
		}  
	} 
	curl_close($ch); //THIS CODE IS NOW WORKING!
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
   $invoice =  $json_output['transactions'][0]['invoice_number'];
   $txn_id = $json_output['id'];
   $ddcost =  $json_output['transactions'][0]['amount']['total'];
 
   $info =  mysql_query("SELECT * from `".$cfg['tablepre']."onlinelog` where id = ".$invoice." ");
   $backinfog = mysql_fetch_assoc($info); 
   if(!empty($backinfog)){
    		if($backinfog['status'] == 0 && $backinfog['cost'] == $mc_gross){
				if($backinfog['type'] == 'order'){
    			 	//更新此状态为1
    			 	//更新订单
    			 	 mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$invoice." ");  
    			 	 mysql_query("UPDATE  `".$cfg['tablepre']."order` SET  `paystatus` =  1,`status` =  1,`paytype_name` =  'paypal',`trade_no` =  '".$txn_id."'  where `id`=".$backinfog['upid']."");  
    			 	  
					 $info = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=postmsgbypay&orderid=".$backinfog['upid']);
    			 	  
    			 	 
    		   }elseif($backinfog['type'] == 'acount'){ 
					mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$invoice." ");  
					mysql_query("UPDATE  `".$cfg['tablepre']."member` SET  `cost` =  `cost`+".$backinfog['cost']." where `uid`=".$backinfog['upid']."");  
					$info =  mysql_query("SELECT * from `".$cfg['tablepre']."member` where uid = ".$backinfog['upid']." ");
					$memberinfo = mysql_fetch_assoc($info);
					$dotime = time(); 
    		  		mysql_query("INSERT INTO `".$cfg['tablepre']."memberlog` (`id` ,`userid` ,`type` ,`addtype` ,`result` ,`addtime` ,`content` ,`title` ,`acount` )VALUES (NULL , '".$memberinfo['uid']."', '2', '1', '".$backinfog['cost']."', '".$dotime."', '在线充值', '使用paypal在线充值".$backinfog['cost']."元', '".$memberinfo['cost']."');");
					 
					 $acountinfo = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=acountpayaddlog&id=".$invoice);

    		    }elseif($backinfog['type'] == 'yhorder'){ 
					   mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$invoice." ");  
    			 	  mysql_query("UPDATE  `".$cfg['tablepre']."shophuiorder` SET  `paystatus` =  1,`status` =  1   where `id`=".$backinfog['upid']."");   
						$orderinfo =  mysql_query("SELECT * from `".$cfg['tablepre']."shophuiorder` where id = ".$backinfog['upid']." ");
						$orderinfo = mysql_fetch_assoc($orderinfo);
						
						if($orderinfo['uid'] > 0 && $orderinfo['givejifen'] > 0){
							$memberinfo =  mysql_query("SELECT * from `".$cfg['tablepre']."member` where uid = ".$orderinfo['uid']." "); 
							$memberinfo = mysql_fetch_assoc($memberinfo);
							if(!empty($memberinfo)){
								$sorce = $orderinfo['givejifen'];
								mysql_query("UPDATE  `".$cfg['tablepre']."member` SET  `score`=`score`+".$sorce." where `uid`=".$orderinfo['uid']."");  
								$allscore = $memberinfo['score']+$sorce;  
								mysql_query("INSERT INTO `".$cfg['tablepre']."memberlog` (`id` ,`userid` ,`type` ,`addtype` ,`result` ,`addtime` ,`content` ,`title` ,`acount` )VALUES (NULL , '".$memberinfo['uid']."', '1', '1', '".$sorce."', '".time()."', '优惠买单', '赠送积分".$sorce."元', '".$memberinfo['cost']."');");
							}
							
						}  
					 
					 
				}
				
			}
   } 
   mysql_close($lnk);  
   echo json_encode(array('error'=>false,'msg'=>'ok'));
   exit;  
}catch(Exception $e){ 
	echo json_encode(array('error'=>true,'msg'=>$e->getMessage()));
	exit; 
} 
?>