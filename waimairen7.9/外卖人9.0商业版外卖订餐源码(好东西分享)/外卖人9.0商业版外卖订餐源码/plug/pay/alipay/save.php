<?php   
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
 
$incFile = fopen($logindir."/".$idtype."/alipay.config.php","w+") or die("请设置plug\pay\alipay\alipay.config.php的权限为777");
$notify_url = Mysite::$app->config['siteurl'].'/plug/pay/alipay/notify_url.php';
$return_url = Mysite::$app->config['siteurl'].'/plug/pay/alipay/return_url.php';
$setting .= "<?php  \r\n";
$setting .=" \$alipay_config['partner']		= '".IReq::get('partner')."';\r\n";
$setting .=" \$alipay_config['key']			= '".IReq::get('key')."';\r\n";
$setting .=" \$alipay_config['sign_type']    = strtoupper('MD5');\r\n";
$setting .=" \$alipay_config['input_charset']= strtolower('utf-8');\r\n"; 
$setting .= " \$alipay_config['transport'] = 'http';\r\n";
$setting .= " \$alipay_config['cacert']    = getcwd().'\\\\cacert.pem';\r\n";
$setting .= " \$notify_url= '".$notify_url."';\r\n";
$setting .= " \$return_url= '".$return_url."';\r\n";
$setting .= " \$seller_email= '".IReq::get('seller_email')."';\r\n";
$setting .= "?>";
$savedata['partner']=IReq::get('partner');
$savedata['key']=IReq::get('key');
$savedata['seller_email']=IReq::get('seller_email');
 if(fwrite($incFile, $setting)){
       
        $taskinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where loginname='".$idtype."'  "); 
        
        	  include_once($logindir.'/'.$idtype.'/set.php');
        	  $ssdata['loginname'] = $idtype;
        	  $ssdata['logindesc'] = $setinfo['name']; 
        	  $ssdata['logourl'] = Mysite::$app->config['siteurl'].'/plug/pay/'.$idtype.'/images/'.$setinfo['logourl']; 
        	  $ssdata['temp'] = json_encode($savedata);
        	  $ssdata['type'] = 1;
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
 
?>