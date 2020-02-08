<?php   
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}     

$incFile = fopen($logindir."/".$idtype."/lib/WxPay.Config.php","w+") or die("请设置plug\pay\weixin\lib\WxPay.Config.php的权限为777"); 
 
$APPID = trim(IReq::get('APPID'));
$MCHID = trim(IReq::get('MCHID'));
$KEY = trim(IReq::get('KEY'));
$APPSECRET = trim(IReq::get('APPSECRET'));
if(strlen($KEY) != 32){
	$this->message('微信KEY位数错误');
}
$setting .= "<?php ".PHP_EOL;
$setting .= "class WxPayConfig".PHP_EOL;
$setting .= "{".PHP_EOL;
$setting .= "const APPID = '".$APPID."';".PHP_EOL;
$setting .= "const MCHID = '".$MCHID."';".PHP_EOL;
$setting .= "const KEY = '".$KEY."';".PHP_EOL;
$setting .= "const APPSECRET = '".$APPSECRET."';".PHP_EOL;
$setting .= "const SSLCERT_PATH = '/plug/pay/weixin/cert/apiclient_cert.pem';".PHP_EOL;
$setting .= "const SSLKEY_PATH = '/plug/pay/weixin/cert/apiclient_key.pem';".PHP_EOL;
$setting .= "const CURL_PROXY_HOST = \"0.0.0.0\";".PHP_EOL;
$setting .= "const CURL_PROXY_PORT = 0;".PHP_EOL;
$setting .= "const REPORT_LEVENL = 0;".PHP_EOL;
$setting .= "}".PHP_EOL;
$setting .= "?>";
 if(fwrite($incFile, $setting)){    
          $taskinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where loginname='".$idtype."'  ");  
        	  include_once($logindir.'/'.$idtype.'/set.php');
        	  $ssdata['loginname'] = $idtype;
        	  $ssdata['logindesc'] = $setinfo['name']; 
        	  $ssdata['logourl'] = Mysite::$app->config['siteurl'].'/plug/pay/'.$idtype.'/images/'.$setinfo['logourl']; 
        	  $ssdata['temp'] = json_encode(array());
        	  $ssdata['type'] = 2;
        	  if(empty($taskinfo))
        {
        	 
  	      	$this->mysql->insert(Mysite::$app->config['tablepre'].'paylist',$ssdata);  //写消息表	 
        }	else{ 
        	   unset($ssdata['loginname']);
        	 	 $this->mysql->update(Mysite::$app->config['tablepre'].'paylist',$ssdata,"loginname='".$idtype."'"); 
        }
		$this->success('ok');
 }else{
	 $this->message('读写文件失败');
 }
		/*
        echo "<meta charset='utf-8' />";
         echo "<script>parent.uploadsucess('配置成功');</script>";   
         exit; 
		 
		*/
        	
   
 
?>