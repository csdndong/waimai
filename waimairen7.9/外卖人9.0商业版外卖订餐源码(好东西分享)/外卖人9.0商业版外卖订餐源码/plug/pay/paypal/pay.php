<?php  
//获取支付内容 
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
if(empty($dopaydata)){
   $this->message('支付数据为空',$payerrlink);
}
//写在线支付数据 
 
 
 if( strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')    ){ 
	//echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$links = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<title>paypal支付提示</title>
<script src="http://m6.waimairen.com/templates/m7/public/js/jquerynew.js" type="text/javascript" language="javascript"></script>
</head>
<body style="width:100%;height:100%;overflow:hidden;">
<div style="position:absolute;margin-top:30%;">
   <div style="margin: 0 auto;width:80%;">
		<div style="line-height:42px;font-size:40px;width:100%;text-align:center;">请复制以下内容到浏览器内打开</div>
       <textarea name="xxxx" id="xxx"   rows="5" style="border:none;word-break:break-all;line-height:42px;font-size:40px;" >
	   <?php echo $links;?>
		</textarea>
   </div>


</div> 
<script language="javascript">
$(function(){  
	  var winwidth = $(window).width();
 		 
		var paddwidth = Number(winwidth)*0.8;//(winwidth)/2;
  			  $('#xxx').css({'width':paddwidth+'px'});

}); 

 
</script>
	<?php
   exit;
  }
 
 
 
 
 
 
 
 
$dopaydata['status'] = 0;
$dopaydata['addtime'] = time();
unset($dopaydata['paydotype']);
 $this->mysql->insert(Mysite::$app->config['tablepre'].'onlinelog',$dopaydata);  //id type upid  cost  status addtime
$newid = $this->mysql->insertid();
require_once($paydir."/paypal.config.php"); 
$payment_type = "1";
$out_trade_no = $newid;
$subject = $dopaydata['type'] == 'order'?'支付订单':'在线充值';
$total_fee = $dopaydata['cost'];
$body = $_POST['WIDbody'];
$show_url = $_POST['WIDshow_url'];
$anti_phishing_key = "";
$exter_invoke_ip = ""; 
$main_domain = 'www.paypal.com';
if($alipay_config['is_sanbox'] == '1'){ 
  $main_domain = 'www.sandbox.paypal.com';
}
?> 
<form action="https://<?php echo $main_domain?>/cgi-bin/webscr" method="post" id="paypal">   
    <input type="hidden" name="cmd" value="_xclick">   
  <input type="hidden" name="business" value="<?php  echo $alipay_config['business']?>">  
    <input type="hidden" name="item_name" value="<?php  echo $dopaydata['type']?>">   
    <input type="hidden" name="amount" value="<?php echo $total_fee?>">   
    <input type="hidden" name="currency_code" value="<?php  echo $alipay_config['currency_code']?>">   
    <input type="hidden" name="return" value="<?php echo $return_url?>">   
    <input type="hidden" name="invoice" value="<?php echo $out_trade_no?>">   
    <input type="hidden" name="charset" value="utf-8">   
  
    <input type="hidden" name="no_shipping" value="1">   
    <input type="hidden" name="no_note" value="">   
    <input type="hidden" name="notify_url" value="<?php echo  $notify_url?>">   
    <input type="hidden" name="rm" value="<?php echo $out_trade_no?>">   
    <input type="hidden" name="cancel_return"value="<?php echo $cancel_return?>">   
    <input type="submit" value="submit">   
</form> 
<script>
	 window.onload=dosubmit();  
	function dosubmit(){
	 document.getElementById('paypal').submit();
	}
</script>
<?php
exit; 
?>