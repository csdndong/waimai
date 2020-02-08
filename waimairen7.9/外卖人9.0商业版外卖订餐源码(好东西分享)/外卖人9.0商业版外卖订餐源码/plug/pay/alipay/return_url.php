<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */
$hopedir = '../../../';
$config = $hopedir."config/hopeconfig.php";   
$cfg = include($config); 
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

function dosengprint($msg,$machine_code,$mKey){
  	$xmlData = '<xml>
 <mKey><![CDATA['.$mKey.']]></mKey >
<machine_code><![CDATA['.$machine_code.']]></machine_code > 
<Content><![CDATA['.$msg.']]></Content >
</xml>';

//第一种发送方式，也是推荐的方式：
$url = 'http://app.waimairen.com/print.php';  //接收xml数据的文件
$header[] = "Content-type: text/xml";        //定义content-type为xml,注意是数组
$ch = curl_init ($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
$response = curl_exec($ch);
if(curl_errno($ch)){
    print curl_error($ch);
}
curl_close($ch);  
return $response;
  	
  }







?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号
	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号
	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];
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
   $info =  mysql_query("SELECT * from `".$cfg['tablepre']."onlinelog` where id = ".$out_trade_no." ");
   $backinfog = mysql_fetch_assoc($info);

    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
	 
    }
    else {
      
    }
	   
	 $showdata = array('type'=>'','url'=>'','info'=>array(),'status'=>1);	 
	 if(!empty($backinfog)){
			if($backinfog['type']=='order'){ 
					$inlink = $cfg['siteurl'].'/index.php?ctrl=site&action=waitpay&orderid='.$backinfog['upid']; 
				    $temporder = mysql_query("SELECT * from `".$cfg['tablepre']."order` where id = ".$backinfog['upid']." ");
					$orderinfo = mysql_fetch_assoc($temporder); 
					if($backinfog['source'] == 1 || $backinfog['source'] == 2){
							$inlink = $cfg['siteurl'].'/index.php?ctrl=wxsite&action=subshow&orderid='.$backinfog['upid'];
					}	
					$showdata['type'] = 'order';
					$showdata['info'] = $orderinfo;
					$showdata['source'] = $backinfog['source'];//获取返回类型
					$showdata['inlink'] = $inlink; 
				 
				 
			}elseif($backinfog['type']=='acount') {
				    $inlink = $cfg['siteurl'].'/index.php?ctrl=member&action=payoncard';
					$showdata['type'] = 'aount';
					$showdata['info'] = array();
					$showdata['inlink'] = $inlink;  
			}elseif($backinfog['type']=='yhorder') {
					$inlink = $cfg['siteurl'].'/index.php?ctrl=member&action=payoncard';
					$showdata['type'] = 'yhorder';
					$showdata['info'] = array();
					$showdata['inlink'] = $inlink;  
			}
		
	}
	mysql_close($lnk);  

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    $showdata = array('status'=>0);
    $inlink = $cfg['siteurl'].'/index.php?ctrl=site&action=index';
    $showdata['inlink'] = $inlink; 
}
?>
        <title>支付宝即时到账交易接口</title>
	</head>
    <body style="height:100%;width:100%;margin:0px;">
	
	    <?php if($showdata['status'] == 1){?>
		   <!--设置返回按钮--->
		
		    <?php if($showdata['type'] == 'order'){
				
				    if($showdata['source']  > 2){
											
				
				
				?>  
			<div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> 
				<div style="margin-top:50px;background-color:#fff;">
					<div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
						<span style="background:url('<?php echo $cfg['siteurl'];?>/upload/images/order_ok.png') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
						<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;">恭喜您，订单支付成功</div>
				
			    
				   </div>
					<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">单号:</font><span style="padding-left:20px;font-size:12px;display: inline-block;"><?php echo $showdata['info']['dno'];?></span></div>
					<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">总价:</font><span style="padding-left:20px;color:red;font-weight:bold;font-size:15px;">￥<?php echo $showdata['info']['allcost'];?>元</span></div> 
					<div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;cursor: pointer;" onClick="window.waimai.goCtrl('orderdet','<?php echo $showdata['info']['id'];?>');">立即返回</span></div>
				</div> 
			</div> 
			
					<?php }else{?>
					<div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> 
				<div style="margin-top:50px;background-color:#fff;">
					<div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
						<span style="background:url('<?php echo $cfg['siteurl'];?>/upload/images/order_ok.png') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
						<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;">恭喜您，订单支付成功</div>
				
			    
				   </div>
					<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">单号:</font><span style="padding-left:20px;font-size:12px;display: inline-block;"><?php echo $showdata['info']['dno'];?></span></div>
					<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">总价:</font><span style="padding-left:20px;color:red;font-weight:bold;font-size:15px;">￥<?php echo $showdata['info']['allcost'];?>元</span></div> 
					<div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><a href="<?php echo $showdata['inlink'];?>"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;cursor: pointer;">立即返回</span></a></div>
				</div> 
			</div>
					
					
					<?php }?>
			
			<?php }else{ ?>
			<div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> 
				<div style="margin-top:50px;background-color:#fff;">
					<div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
						<span style="background:url('<?php echo $cfg['siteurl'];?>/upload/images/order_ok.png') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
						<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;">恭喜您，充值成功</div>
				
			    
				   </div> 
			<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">充值金额:</font><span style="padding-left:20px;color:red;font-weight:bold;font-size:15px;">￥<?php echo $backinfog['cost'];?>元</span></div> 
					<div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><a href="<?php echo $showdata['inlink'];?>"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;cursor: pointer;">立即返回</span></a></div>
				</div> 
			</div> 
			
			 <?php }?>
			
		<?php }else{ ?>
		
	<div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> 
		 
	   <div style="margin-top:50px;background-color:#fff;">
			 <div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
			    <span style="background:url('<?php echo $cfg['siteurl'];?>/upload/images/nocontent.png') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
				<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;">sorry,支付宝支付失败</div>
				
			    
			</div>
			<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">原因:</font><span style="padding-left:20px;font-size:12px;display: inline-block;">取消支付或者验证失败</span></div>
			 
			<div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><a href="<?php echo $showdata['inlink'];?>"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;">立即返回</span></a></div>
	   </div>
   
   
   </div>
	
	    <?php }?>
    </body>
</html>