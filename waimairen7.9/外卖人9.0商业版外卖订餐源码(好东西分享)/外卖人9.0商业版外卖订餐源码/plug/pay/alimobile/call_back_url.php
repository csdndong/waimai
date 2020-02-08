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
header("Content-Type:text/html;charset=utf-8");
$hopedir = '../../../';
$paydir = $hopedir."/plug/pay/alimobile";
$config = $hopedir."config/hopeconfig.php";   
$cfg = include($config); 
require_once($paydir."/alipay.config.php");
require_once($paydir."/lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="/templates/m7/public/wxsite/css/pay-font-awesome.css"/>
	<link rel="stylesheet" href="/templates/m7/public/wxsite/css/pay-font-awesome.min.css"/>
	<link rel="stylesheet" href="/templates/m7/public/wxsite/css/pay-index.css"/>
		<?php if($cfg['color']=='green'){ ?>
		<style>
			.titCon {
				background-color: #01cd88!important;
			}
			.cipuSubsucCon .cipuSubsucBot b{
				background: #01cd88!important;
				border: 1px solid #01cd88!important;
			}
		</style>
		<?php }else if($cfg['color']=='yellow'){ ?>
		<style>
			.titCon {
				background-color: #ff7600!important;
			}
			.cipuSubsucCon .cipuSubsucBot b{
				background: #ff7600!important;
				border: 1px solid #ff7600!important;
			}

		</style>
		<?php }else{?>

		<style>
			.titCon {
				background-color: #ff6e6e!important;
			}
			.cipuSubsucCon .cipuSubsucBot b{
				background: #ff6e6e!important;
				border: 1px solid #ff6e6e!important;
			}
		</style>
		<?php } ?>



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
	$result = $_GET['result'];
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
				
				    $inlink = $cfg['siteurl'].'/index.php?ctrl=wxsite&action=costlog';  // 支付成功后跳转到余额明细页面
					$showdata['type'] = 'aount';
					$showdata['info'] = array();
					$showdata['inlink'] = $inlink;  
			}elseif($backinfog['type']=='yhorder') {
				
				    $inlink = $cfg['siteurl'].'/index.php?ctrl=wxsite&action=subpayhui';  // 支付成功后跳转到余额明细页面
					$showdata['type'] = 'yhorder';
					$showdata['info'] = array();
					$showdata['inlink'] = $inlink;  
			}
		
	}
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	mysql_close($lnk);  
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
     $showdata = array('status'=>0);
    $inlink = $cfg['siteurl'].'/index.php?ctrl=wxsite&action=index';
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
				<script> 
				window.onload=function(){
					//alert('window'); 进入界面就加载该函数
					window.waimai.goCtrl('postmsg','0');
				}
				</script>
			<div class="titCon">
				<div class="titBox">
					<div class="titL"><i class="fa fa-angle-left"></i></div>
					<div class="titC">
						<h2>支付结果</h2></div>
					<div class="titR"><i></i></div>
				</div>
			</div>
			<div class="cipuSubsucCon">
				<div class="cipuSubsucTop">
					<i style="background-image: url(/upload/images/icon_zfcg.png);"></i>
					<h2>订单支付成功</h2>
				</div>
				<div class="cipuSubsucCen">
					<ul>
						<li>订单标号：<span style="color: #333;"><?php echo $showdata['info']['dno'];?></span></li>
						<li>订单金额：<span>￥<?php echo $showdata['info']['allcost'];?>元</span></li>
					</ul>
				</div>
				<div class="cipuSubsucBot">
					<a href="<?php echo $cfg['siteurl']?>/index.php?ctrl=wxsite&action=ordershow&orderid=<?php echo $backinfog['upid'] ?>"  style="color:#fff;text-decoration:none;"><span> 查看订单</span></a>
					<b><a href="<?php echo $cfg['siteurl']?>" style="color:#fff;text-decoration:none;">返回首页</a></b>
				</div>
			</div>




			
					<?php }else{?>

			<div class="titCon">
				<div class="titBox">
					<div class="titL"><i class="fa fa-angle-left"></i></div>
					<div class="titC">
						<h2>支付结果</h2></div>
					<div class="titR"><i></i></div>
				</div>
			</div>
			<div class="cipuSubsucCon">
				<div class="cipuSubsucTop">
					<i style="background-image: url(/upload/images/icon_zfcg.png);"></i>
					<h2>订单支付成功</h2>
				</div>
				<div class="cipuSubsucCen">
					<ul>
						<li>订单标号：<span style="color: #333;"><?php echo $showdata['info']['dno'];?></span></li>
						<li>订单金额：<span>￥<?php echo $showdata['info']['allcost'];?>元</span></li>
					</ul>
				</div>
				<div class="cipuSubsucBot">
					<a href="<?php echo $cfg['siteurl']?>/index.php?ctrl=wxsite&action=ordershow&orderid=<?php echo $backinfog['upid'] ?>"  style="color:#fff;text-decoration:none;"><span> 查看订单</span></a>
					<b><a href="<?php echo $cfg['siteurl']?>" style="color:#fff;text-decoration:none;">返回首页</a></b>
				</div>
			</div>
					
					<?php }?>
			
			<?php }else{ ?>

			<div class="titCon">
				<div class="titBox">
					<div class="titL"><i class="fa fa-angle-left"></i></div>
					<div class="titC">
						<h2>支付结果</h2></div>
					<div class="titR"><i></i></div>
				</div>
			</div>
			<div class="cipuSubsucCon">
				<div class="cipuSubsucTop">
					<i style="background-image: url(/upload/images/icon_zfcg.png);"></i>
					<h2>恭喜您，充值成功</h2>
				</div>
				<div class="cipuSubsucCen">
					<ul>
						<li>充值金额:<span style="color: #333;">￥<?php echo $backinfog['cost'];?>元</span></li>

					</ul>
				</div>
				<div class="cipuSubsucBot">
					<a href="<?php echo $cfg['siteurl']?>/index.php?ctrl=wxsite&action=member"  style="color:#fff;text-decoration:none;"><span> 用户中心</span></a>
					<b><a href="<?php echo $cfg['siteurl']?>" style="color:#fff;text-decoration:none;">返回首页</a></b>
				</div>
			</div>

			
			 <?php }?>
			
		<?php }else{ ?>

			<div class="titCon">
				<div class="titBox">
					<div class="titL"><i class="fa fa-angle-left"></i></div>
					<div class="titC">
						<h2>支付结果</h2></div>
					<div class="titR"><i></i></div>
				</div>
			</div>
			<div class="cipuSubsucCon">
				<div class="cipuSubsucTop">
					<i style="background-image: url(/upload/images/icon_zfcg.png);"></i>
					<h2>订单支付失败</h2>
				</div>
				<div class="cipuSubsucCen">
					<h3 style="color:red;">原因:取消支付或者验证失败</h3>
				</div>
				<div class="cipuSubsucBot">
					<b><a href="<?php echo $cfg['siteurl']?>" style="color:#fff;text-decoration:none;">返回首页</a></b>
				</div>
			</div>


	
	    <?php }?>
    </body>
</html>