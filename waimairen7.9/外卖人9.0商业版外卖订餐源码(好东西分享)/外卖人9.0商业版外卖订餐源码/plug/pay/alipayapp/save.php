<?php   
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
$rsa_private_key = trim(IReq::get('rsa_private_key'));

$str = str_replace(' ', '', $rsa_private_key);  
$str = str_replace(PHP_EOL, '', $str);  
$str = str_replace(array("\r","\n","\r\n"),"",$str);
$rsa_private_key =  '-----BEGIN RSA PRIVATE KEY-----'.PHP_EOL
.chunk_split($str, 64, PHP_EOL)
.'-----END RSA PRIVATE KEY-----'.PHP_EOL; 
$res = openssl_get_privatekey($rsa_private_key);
if(empty($res)){ 
	$this->message('rsa_private_key格式错误');
}
file_put_contents($logindir."/".$idtype."/key/rsa_private_key.pem",$rsa_private_key);

$rsa_public_key = trim(IReq::get('rsa_public_key'));

$str = str_replace(' ', '', $rsa_public_key);  
$str = str_replace(PHP_EOL, '', $str);  
$str = str_replace(array("\r","\n","\r\n"),"",$str);
$rsa_public_key =  '-----BEGIN PUBLIC KEY-----'.PHP_EOL
.chunk_split($str, 64, PHP_EOL)
.'-----END PUBLIC KEY-----'.PHP_EOL; 
$res = openssl_get_publickey($rsa_public_key);
if(empty($res)){ 
	$this->message('rsa_public_key格式错误');
}
file_put_contents($logindir."/".$idtype."/key/rsa_public_key.pem",$rsa_public_key);
$alipay_public_key = trim(IReq::get('alipay_public_key'));

$str = str_replace(' ', '', $alipay_public_key);  
$str = str_replace(PHP_EOL, '', $str);  
$str = str_replace(array("\r","\n","\r\n"),"",$str);
$alipay_public_key =  '-----BEGIN PUBLIC KEY-----'.PHP_EOL
.chunk_split($str, 76, PHP_EOL)
.'-----END PUBLIC KEY-----'.PHP_EOL; 
$res = openssl_get_publickey($alipay_public_key);
if(empty($res)){ 
	$this->message('alipay_public_key格式错误');
}
file_put_contents($logindir."/".$idtype."/key/alipay_public_key.pem",$alipay_public_key);

$pk8 = trim(IReq::get('pk8')); 
$str = str_replace(' ', '', $pk8);  
$str = str_replace(PHP_EOL, '', $str);  
$str = str_replace(array("\r","\n","\r\n"),"",$str);
$pk8 =  chunk_split($str, 64, PHP_EOL);  
file_put_contents($logindir."/".$idtype."/key/pk8.txt",$pk8);



$incFile = fopen($logindir."/".$idtype."/alipay.config.php","w+") or die("请设置plug\pay\alipayapp\alipay.config.php的权限为777"); 
$setting .= "<?php  \r\n";
$setting .=" \$alipay_config['partner']		= '".IReq::get('partner')."';\r\n";
$setting .=" \$alipay_config['private_key_path']			= 'key/rsa_private_key.pem';\r\n";
$setting .=" \$alipay_config['ali_public_key_path']    = 'key/alipay_public_key.pem';\r\n";
$setting .=" \$alipay_config['sign_type']= strtoupper('RSA');\r\n"; 
$setting .= " \$alipay_config['input_charset'] = strtolower('utf-8');\r\n";
$setting .= " \$alipay_config['cacert']    = getcwd().'\\\\cacert.pem';\r\n";
$setting .= " \$alipay_config['transport']    = 'http';\r\n";
$setting .= "?>";
$savedata['partner']=IReq::get('partner'); 
$savedata['seller_email'] = IReq::get('seller_email'); 
 if(fwrite($incFile, $setting)){
       
        $taskinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where loginname='".$idtype."'  "); 
        
        	  include_once($logindir.'/'.$idtype.'/set.php');
        	  $ssdata['loginname'] = $idtype;
        	  $ssdata['logindesc'] = $setinfo['name']; 
        	  $ssdata['logourl'] = Mysite::$app->config['siteurl'].'/plug/pay/'.$idtype.'/images/'.$setinfo['logourl']; 
        	  $ssdata['temp'] = json_encode($savedata);
        	  $ssdata['type'] = 4;
        	  if(empty($taskinfo))
        {
        	 
  	      	$this->mysql->insert(Mysite::$app->config['tablepre'].'paylist',$ssdata);  //写消息表	 
        }	else{ 
        	   unset($ssdata['loginname']);
        	 	 $this->mysql->update(Mysite::$app->config['tablepre'].'paylist',$ssdata,"loginname='".$idtype."'"); 
        }
		$this->success('ok');
        // echo "<meta charset='utf-8' />";
         // echo "<script>parent.uploadsucess('配置成功');</script>";   
         // exit; 
        	
  }else{
        // echo "Error";
		$this->message('error');
  } 
 
?>