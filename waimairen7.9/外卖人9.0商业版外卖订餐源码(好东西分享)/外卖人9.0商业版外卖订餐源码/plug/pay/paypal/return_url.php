<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php 
$hopedir = '../../../';
$config = $hopedir."config/hopeconfig.php";   
$cfg = include($config);  
require_once("paypal.config.php");
include($hopedir.'/lib/function.php');
$main_domain = 'api.paypal.com';
if($alipay_config['is_sanbox'] == '1'){
	
  $main_domain = 'api.sandbox.paypal.com';
}  

try{
	if(!isset($_GET['PayerID'])||!isset($_GET['paymentId'])){
		throw new Exception("参数错误");    
	}
	$ch = curl_init();
	$clientId = $alipay_config['clientId'];
	$secret = $alipay_config['secret']; 
	curl_setopt($ch, CURLOPT_URL, "https://".$main_domain."/v1/oauth2/token");
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); 
	$result = curl_exec($ch); 
	if(empty($result))throw new Exception("检测账号失败");   
	else
	{
		$json = json_decode($result); 
			$data = '{ "payer_id":"'.$_GET['PayerID'].'" }'; 
			curl_setopt($ch, CURLOPT_URL, "https://".$main_domain."/v1/payments/payment/".$_GET['paymentId']."/execute/");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:application/json',
			"Authorization: Bearer " . $json->access_token, 
			"Content-length: ".strlen($data))
			);
		  
			$result = curl_exec($ch);

			if(empty($result)) throw new Exception("验证订单失败");  
			else {
				$json_output = json_decode($result,true);
				if(!isset($json_output['id'])){
					throw new Exception("已经验证过");
				} 
			} 	 
		} 
	curl_close($ch);  
	
	
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
					throw new Exception("数据库连接失败");
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
    		if($backinfog['status'] == 0 && $backinfog['cost'] == $ddcost){
				echo 'yanzhengtongguo';
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
?>

<div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> 
				<div style="margin-top:50px;background-color:#fff;">
					<div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
						<span style="background:url('<?php echo $cfg['siteurl'];?>/upload/images/order_ok.png') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
						<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;">恭喜您，支付成功</div>
				
			    
				   </div>
					      <div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><a href="<?php echo $cfg['siteurl'];?>"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;cursor: pointer;">返回首页</span></a></div>
		
				</div> 
			</div> 
<?php
}catch(Exception $e){
?>
<div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> 
				<div style="margin-top:50px;background-color:#fff;">
					<div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
						<span style="background:url('<?php echo $cfg['siteurl'];?>/upload/images/order_err.png') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;"> <?php print_r($e->getMessage());?></div>
				
			    
				   </div>
					      <div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><a href="<?php echo $cfg['siteurl'];?>"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;cursor: pointer;">返回首页</span></a></div>
		
				</div> 
			</div> 
<?php	
}  
?>		
					 
			
  </body>
</html>