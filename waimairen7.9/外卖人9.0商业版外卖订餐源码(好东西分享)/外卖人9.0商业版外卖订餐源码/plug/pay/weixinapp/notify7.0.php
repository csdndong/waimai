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
		   {"appid":"wx9e8e0ee1e7bc9ecb","attach":"14284541672118","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y",
		   "mch_id":"1234454802","nonce_str":"j0ojkmvgf16o6t0dz45pb8h9j9zgh5wj","openid":"oSPqZjocUN11cSbParngKfCZIJUw",
		   "out_trade_no":"302","result_code":"SUCCESS","return_code":"SUCCESS","sign":"7490C1E78C0E05A83A9902E033DB56E6","time_end":"20150408084938","total_fee":"1",
		   "trade_type":"JSAPI",
		   "transaction_id":"1001600750201504080051156176"}
		   
		   
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
			  $info =  mysql_query("SELECT * from `".$cfg['tablepre']."order` where id = ".$result['out_trade_no']." ");
              $backinfog = mysql_fetch_assoc($info);
			  
			  
			  if(!empty($backinfog)){ 
			     if($backinfog['paystatus'] == 1){
					 
				 }else{
					mysql_query("UPDATE  `".$cfg['tablepre']."order` SET  `paystatus` =  1,`status` =  1 ,`trade_no` = ".$result['transaction_id']."   where `id`=".$result['out_trade_no']."");  
					
					 $temporder = mysql_query("SELECT * from `".$cfg['tablepre']."order` where id = ".$result['out_trade_no']." ");
						$orderinfo = mysql_fetch_assoc($temporder); 
					 		$orderCLs = new orderclass();

							 $orderCLs->writewuliustatus($orderinfo['id'],3,$orderinfo['paytype']);  //在线支付成功状态	 
							 if($orderinfo['is_make']  == 1 ){
								 $orderCLs->writewuliustatus($orderinfo['id'],4,$orderinfo['paytype']);  //商家自动确认接单	  
								  $auto_send = Mysite::$app->config['auto_send'];
									  if($auto_send == 1){
										 $this->writewuliustatus($orderid,6,$orderinfo['paytype']);//订单审核后自动 商家接单后自动发货
									  }
							 }
					 
					
					
					$info = file_get_contents($cfg['siteurl']."/index.php?ctrl=site&action=postmsgbypay&orderid=".$result['out_trade_no']);
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
