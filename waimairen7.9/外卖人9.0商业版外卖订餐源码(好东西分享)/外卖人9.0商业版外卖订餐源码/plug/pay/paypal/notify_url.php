<?php
  //reading raw POST data from input stream. reading pot data from $_POST may cause serialization issues since POST data may contain arrays 
 date_default_timezone_set("Asia/Hong_Kong"); //时间区域
header("Content-Type:text/html;charset=utf-8"); //输出格式 
$hopedir = '../../../';
define('hopedir', $hopedir);  
$config = $hopedir."config/hopeconfig.php";   
$cfg = include($config); 
$mmc = include('paypal.config.php');
include($hopedir.'/lib/function.php');
 logwrite('paypal_notify');
 
 //plug/pay/paypal/notify_url.php?cmd=_notify-validate&transaction_subject=&txn_type=web_accept&payment_date=00%3A01%3A14+Aug+09%2C+2016+PDT&last_name=buyer&residence_country=CN&pending_reason=unilateral&item_name=order&payment_gross=63.00&mc_currency=USD&payment_type=instant&protection_eligibility=Ineligible&verify_sign=A0tMvs5YXZLkWzGtbvMFtzut2zPuAhVXOx.MAbDyj2KK4Z.BKdkRro.F&payer_status=verified&test_ipn=1&tax=0.00&payer_email=187165099-buyer%40qq.com&txn_id=7LN28255D19116340&quantity=1&receiver_email=187165099%40qq.com&first_name=test&invoice=32&payer_id=TG62HE69WMQVL&item_number=&handling_amount=0.00&payment_status=Pending&shipping=0.00&mc_gross=63.00&custom=&charset=windows-1252&notify_version=3.8&ipn_track_id=43df3f8fa6d46
  $raw_post_data = file_get_contents('php://input');
  $raw_post_array = explode('&', $raw_post_data);
  $myPost = array();
  foreach ($raw_post_array as $keyval)
  {
      $keyval = explode ('=', $keyval);
      if (count($keyval) == 2)
         $myPost[$keyval[0]] = urldecode($keyval[1]);
  }
  // read the post from PayPal system and add 'cmd'
 $req = 'cmd=_notify-validate';
  $newdata['cmd'] = '_notify-validate';
  if(function_exists('get_magic_quotes_gpc'))
  {
       $get_magic_quotes_exits = true;
  } 
  foreach ($myPost as $key => $value)
  {        
  
		
       if($get_magic_quotes_exits == true && get_magic_quotes_gpc() == 1)
       { 
			  $newdata[$key] = stripslashes($value);
            $value = urlencode(stripslashes($value)); 
       }
       else
       {
			 $newdata[$key] = $value;
            $value = urlencode($value);
       }
	 
      $req .= "&$key=$value";
  }
 
 // $newdata = $_GET;
 
 // print_r($_GET);
 
 // $reg = '';
 // $i= 0;
 // foreach($newdata    as $key=>$value){
	 // if($i == 0){
		 // $value = urlencode($value);
		  // $req  = "$key=$value";
	 // }else{
		  // $value = urlencode($value);
	     // $req .= "&$key=$value";
	 // }
	 // $i++;
	 
 // }
$main_domain = 'www.paypal.com';
if($mmc['is_sanbox'] == '1'){ 
  $main_domain = 'www.sandbox.paypal.com';
}
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://'.$main_domain.'/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $newdata);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.paypal.com'));
// In wamp like environment where the root authority certificate doesn't comes in the bundle, you need
// to download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path 
// of the certificate as shown below.
  curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
$response = curl_exec($ch);
curl_close($ch); 
  
 
logwrite('curl2:result '.$response); 
	 
/*
file_put_contents(dirname(__FILE__) . '/payresp/rc_req.txt', print_r($req, true));
file_put_contents(dirname(__FILE__) . '/payresp/rc_resp.txt', print_r($res, true));
file_put_contents(dirname(__FILE__) . '/payresp/rc_post.txt', print_r($_POST, true));
*/

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$invoice = $_POST['invoice']; 
  //打印机信息结束
if (strcmp ($response, "VERIFIED") == 0) {//VERIFIED
	// check the payment_status is Completed
	// check that txn_id has not been previously processed
	// check that receiver_email is your Primary PayPal email
	// check that payment_amount/payment_currency are correct
	// process payment
	
	if($payment_status != 'Completed'){
		 logwrite($receiver_email.'状态:'.$payment_status.',返回ID'.$txn_id);  
		 exit;
	}
   if($receiver_email  !=  $alipay_config['business']	){
		logwrite($receiver_email.'和设置不一致 ,返回ID'.$txn_id);
		exit;
    }
	// payment_amount   invoice
	 logwrite('支付成功'.$invoice);
	 
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
 
   $info =  mysql_query("SELECT * from `".$cfg['tablepre']."onlinelog` where id = ".$invoice." ");
   $backinfog = mysql_fetch_assoc($info);
   
   if(!empty($backinfog)){
    		if($backinfog['status'] == 0 && $backinfog['cost'] == $mc_gross){
				if($backinfog['type'] == 'order'){
    			 	//更新此状态为1
    			 	//更新订单
					if($backinfog['paystatus'] == 0){
							 mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$invoice." ");  
    			 	 mysql_query("UPDATE  `".$cfg['tablepre']."order` SET  `paystatus` =  1,`status` =  1,`paytype_name` =  'paypal',`trade_no` =  '".$txn_id."',`paytime` = '".time()."' ,`addtime` = '".time()."' where `id`=".$backinfog['upid']."");  
    			 	  
					 $info = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=postmsgbypay&orderid=".$backinfog['upid']);
						}
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
    			 	  mysql_query("UPDATE  `".$cfg['tablepre']."shophuiorder` SET  `paystatus` =  1,`status` =  1,`paytime` = '".time()."',`paytype_name` =  'paypal'   where `id`=".$backinfog['upid']."");   
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
}else if (strcmp ($response, "INVALID") == 0) {
	// log for manual investigation
	logwrite('支付失败');
}
?>