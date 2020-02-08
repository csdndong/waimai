<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

$hopedir = '../../../';
$config = $hopedir."config/hopeconfig.php";   
$cfg = include($config);  
$paydir = $hopedir."/plug/pay/alimobile"; 
require_once($paydir."/alipay.config.php"); 
require_once($paydir."/lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
 
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
  
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
	//解析notify_data
	//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
	$doc = new DOMDocument();	
	if ($alipay_config['sign_type'] == 'MD5') {
		$doc->loadXML($_POST['notify_data']);
	}
	
	if ($alipay_config['sign_type'] == '0001') {
		$doc->loadXML($alipayNotify->decrypt($_POST['notify_data']));
	}
	
	if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
		//商户订单号
		$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
		//支付宝交易号
		$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
		//交易状态
		$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
		 
         
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
        
		$info =  mysql_query("SELECT * from `".$cfg['tablepre']."onlinelog` where id = '".$out_trade_no."' ");
		$backinfog = mysql_fetch_assoc($info);
	 
   /****验证金额***/
$total_fee = $doc->getElementsByTagName( "total_fee" )->item(0)->nodeValue; 
   if(!empty($backinfog)){
		if($total_fee < $backinfog['cost']){
			// logwrite('金额错误'); 
			 echo "fail";
			 mysql_close($lnk);
			 exit;
		}
   
   } 
		 
   
		if($trade_status == 'TRADE_FINISHED') {
			//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
			//注意：
			//该种交易状态只在两种情况下出现
			//1、开通了普通即时到账，买家付款成功后。
			//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
	
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			if(!empty($backinfog)){
				if($backinfog['status'] == 0){
					if($backinfog['type'] == 'order'){
						//更新此状态为1
						//更新订单
						if($backinfog['paystatus'] == 0){
							mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$out_trade_no." ");  
					 
						mysql_query("UPDATE  `".$cfg['tablepre']."order` SET  `paystatus` =  1,`status` =  1,`paytype_name` =  'alimobile',`trade_no` =  ".$trade_no.",`paytime` = '".time()."' ,`addtime` = '".time()."'  where `id`=".$backinfog['upid']."");   

    			         $info = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=postmsgbypay&orderid=".$backinfog['upid']);
						echo "success";		//请不要修改或删除
						}

					}elseif($backinfog['type'] == 'acount'){
						
						
						mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 ,`dno`= '".$trade_no."' where `id`=".$out_trade_no." ");  
						mysql_query("UPDATE  `".$cfg['tablepre']."member` SET  `cost` =  `cost`+".$backinfog['cost']." where `uid`=".$backinfog['upid']."");  
						$info =  mysql_query("SELECT * from `".$cfg['tablepre']."member` where uid = ".$backinfog['upid']." ");
				  
				  
						$memberinfo = mysql_fetch_assoc($info);
						$dotime = time(); 
						$checkcost = $memberinfo['cost']+$backinfog['cost'];
						mysql_query("INSERT INTO `".$cfg['tablepre']."memberlog` (`id` ,`userid` ,`type` ,`addtype` ,`result` ,`addtime` ,`content` ,`title` ,`acount` )VALUES (NULL , '".$memberinfo['uid']."', '2', '1', '".$backinfog['cost']."', '".$dotime."', '在线充值', '使用支付宝在线充值".$backinfog['cost']."元', '".$checkcost."');");
						mysql_query("INSERT INTO `".$cfg['tablepre']."memcostlog`(`uid`,`username`,`cost`,`bdtype`,`bdcost`,`curcost`,`bdliyou`,`czuid`,`czusername`,`addtime`) VALUES('".$memberinfo['uid']."','".$memberinfo['username']."','".$memberinfo['cost']."',1,'".$backinfog['cost']."','".$checkcost."','支付宝html5充值','".$memberinfo['uid']."','".$memberinfo['username']."','".$dotime."');");
						
						$acountinfo = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=acountpayaddlog&dno=".$trade_no);

    			 }elseif($backinfog['type'] == 'yhorder'){ 
					   mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$out_trade_no." ");  
    			 	  mysql_query("UPDATE  `".$cfg['tablepre']."shophuiorder` SET  `paystatus` =  1,`status` =  1,`paytime` = '".time()."',`paytype_name` =  'alimobile' where `id`=".$backinfog['upid']."");   
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
		}else if ($trade_status == 'TRADE_SUCCESS') {
			//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
			//注意：
			//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
	
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			if(!empty($backinfog)){
				if($backinfog['status'] == 0){
					if($backinfog['type'] == 'order'){
						//更新此状态为1
						//更新订单
						if($backinfog['paystatus'] == 0){
							mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$out_trade_no." ");  
					 
						mysql_query("UPDATE  `".$cfg['tablepre']."order` SET  `paystatus` =  1,`status` =  1,`paytype_name` =  'alimobile',`trade_no` =  ".$trade_no.",`paytime` = '".time()."' ,`addtime` = '".time()."'  where `id`=".$backinfog['upid']."");   
					  
    			        $info = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=postmsgbypay&orderid=".$backinfog['upid']);
						
						echo "success";		//请不要修改或删除
						}

					}elseif($backinfog['type'] == 'acount'){
						
						
						mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1,`dno` =   ".$trade_no." where `id`=".$out_trade_no." ");  
						mysql_query("UPDATE  `".$cfg['tablepre']."member` SET  `cost` =  `cost`+".$backinfog['cost']." where `uid`=".$backinfog['upid']."");  
						$info =  mysql_query("SELECT * from `".$cfg['tablepre']."member` where uid = ".$backinfog['upid']." ");
						$memberinfo = mysql_fetch_assoc($info);
						$dotime = time(); 
						$checkcost = $memberinfo['cost']+$backinfog['cost'];
						mysql_query("INSERT INTO `".$cfg['tablepre']."memberlog` (`id` ,`userid` ,`type` ,`addtype` ,`result` ,`addtime` ,`content` ,`title` ,`acount` )VALUES (NULL , '".$memberinfo['uid']."', '2', '1', '".$backinfog['cost']."', '".$dotime."', '在线充值', '使用支付宝在线充值".$backinfog['cost']."元', '".$checkcost."');");
						mysql_query("INSERT INTO `".$cfg['tablepre']."memcostlog`(`uid`,`username`,`cost`,`bdtype`,`bdcost`,`curcost`,`bdliyou`,`czuid`,`czusername`,`addtime`) VALUES('".$memberinfo['uid']."','".$memberinfo['username']."','".$memberinfo['cost']."',1,'".$backinfog['cost']."','".$checkcost."','支付宝html5充值','".$memberinfo['uid']."','".$memberinfo['username']."','".$dotime."');");
						
						$acountinfo = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=acountpayaddlog&dno=".$trade_no);

    			 }elseif($backinfog['type'] == 'yhorder'){ 
					  mysql_query("UPDATE  `".$cfg['tablepre']."onlinelog` SET  `status` =  1 where `id`=".$out_trade_no." ");  
    			 	  mysql_query("UPDATE  `".$cfg['tablepre']."shophuiorder` SET  `paystatus` =  1,`status` =  1,`paytime` = '".time()."',`paytype_name` =  'alimobile' where `id`=".$backinfog['upid']."");   
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
			
			echo "success";		//请不要修改或删除
		}
		mysql_close($lnk);	
 
	}//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}  
?>