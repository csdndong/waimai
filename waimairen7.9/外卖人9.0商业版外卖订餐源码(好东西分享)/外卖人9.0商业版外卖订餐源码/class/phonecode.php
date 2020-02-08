<?php  
/**
 * modify 2016-11-23
 * @class 手机验证码
 * @brief 促销规则 
 */
class  phonecode
{
	
	private $mysql;//数据库连接
	//短信主要类容
	private $maincontent = array(
		0=>'用户注册，验证码:',
		1=>'登录，验证码:',
		2=>'找回密码，验证码:',
		3=>'更换手机号，验证码:',
		4=>'手机快捷登录，验证码:',
		5=>'更换密码，验证码:',
		6=>'下单，验证码:',
		7=>'APP调用用户登录验证',
		8=>'新绑定手机号',
		9=>'您好，尊敬的会员,验证码为：',
	);
	   
    
 	public   $qianming ;//短信签名
	private $sendtype;//验证发送类型
	private $typearray = array(0,1,2,3,4,5,6,7,8,9);//用户检测代码
	private $phone;//手机号
	private   $limittime = 180;//短信有效时间
	private $code;//验证码;
	private $timelong = 0;//短信失效时间
	private $sure;
	private $errId;
	
	
	
		//初始化函数  type 0.用户注册  1登录验证 2.找回密码   3更换手机号  4手机快捷登录   5更换密码
		//IFilter::act(IReq::get('phone'));
		//初始化  $mysql 数据库连接  $sendtype 验证码类型=  phone 接受手机号  
	function __construct($mysql,$type,$phone='',$sure=''){ 
		// logwrite(var_export($_SERVER,true));
	  	 $this->mysql = $mysql;  
		 $this->sendtype =  $type;
		 $this->phone = empty($phone)?intval(IFilter::act(IReq::get('phone'))):$phone; 
		 $msgqianming = '';
		 $msgqianming = Mysite::$app->config['msgqianming'];
		 if( !empty($msgqianming) ){
			 $this->qianming =  $msgqianming;
		 }else{
			 $this->qianming =  Mysite::$app->config['sitename'];
		 }
		 $this->aliqianming =  Mysite::$app->config['aliqm'];
		 $this->sure = $sure;
		 $this->timelong = 0;
		 $this->aliarr = array(
			0=>Mysite::$app->config['alicode2'],
			1=>Mysite::$app->config['alicode3'],
			2=>Mysite::$app->config['alicode4'],
			3=>Mysite::$app->config['alicode5'],
			4=>Mysite::$app->config['alicode6'],
			5=>Mysite::$app->config['alicode7'],
			6=>Mysite::$app->config['alicode8'],
			7=>Mysite::$app->config['alicode9'],
			8=>Mysite::$app->config['alicode10'],
			9=>Mysite::$app->config['alicode1'], 
		 );
		 //$this->tablepre = Mysite::$app->config['mobileapp']; 
	}
	public function sendother($msg){
		// ignore_user_abort(true);  
// set_time_limit(0);    
		$contents =  '【'.$this->qianming.'】'.$msg; 
		$APIServer = 'http://www.waimairen.com/sendtophone.php?apiuid='.Mysite::$app->config['apiuid'];  
		$weblink = $APIServer.'&key='.trim(Mysite::$app->config['sms86ac']).'&code='.trim(Mysite::$app->config['sms86pd']).'&hm='.$this->phone.'&msgcontent='.urlencode($contents).'&msgtype=user'; 
		logwrite('短信发送链接sendother:'.$weblink);  
		$str = $this->vpost($weblink);
	   
		 logwrite('短信发送结果sendother:'.$str);   
	}
	function vpost($url,$data='',$cookie=''){   
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
           // echo 'Errno:'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据   
    }
	//
	public function sendcode(){
		  
		 if(!IValidate::suremobi($this->phone)){
			 $this->errId = '手机号格式错误';
			  return false;
		 }  
		 if(!in_array($this->sendtype,$this->typearray)){
			  $this->errId = '未定义的发送类型';
			  return false;
		 } 
         if($this->sendtype == 2){
			  $checkmember = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$this->phone."'   order by uid desc limit 0,1");
			  if(empty($checkmember)){
				    $this->errId = '手机号对应用户不存在';
					return false; 
			  } 
		 }elseif($this->sendtype == 3){
			  $checkmember = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$this->phone."'   order by uid desc limit 0,1");
			  if(empty($checkmember)){
				    $this->errId = '手机号对应用户不存在';
					return false; 
			  } 
		 }elseif($this->sendtype == 4){
			 
			  
			  
			  
		 }elseif($this->sendtype == 5){
			  $checkmember = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$this->phone."'   order by uid desc limit 0,1");
			  if(empty($checkmember)){
				    $this->errId = '手机号对应用户不存在';
					return false; 
			  } 
		 }elseif($this->sendtype == 0){
			  $checkmember = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$this->phone."'   order by uid desc limit 0,1");
			  if(!empty($checkmember)){
				    $this->errId = '手机号对应用户已存在';
					return false; 
			  } 
		 }elseif($this->sendtype == 8 && $this->sure != 1){
			 /*$checkmember = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$this->phone."'   order by uid desc limit 0,1");
			  if(!empty($checkmember)){
				    $this->errId = '手机号对应用户已存在';
					return false; 
			  } */
		 } 
		 $checkphone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."mobileapp where phone ='".$this->phone."'  and type='".$this->sendtype."' order by addtime desc limit 0,1");
		 if(!empty($checkphone)){
			//$checktime = time()-$this->limittime; 
			if($checkphone['addtime'] > time()){
				// $this->errId = '验证码还未失效';
				 $this->code = $checkphone['code'];
				 $this->timelong = $checkphone['addtime'] - time();
				 return true;
			}
		 }
		 $mintime = strtotime(date('Y-m-d',time()));
		$checkcounts = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."mobileapp where phone ='".$this->phone."' and type='".$this->sendtype."' and addtime > ".$mintime." ");
		if($checkcounts > 3){
			$this->errId = '每个手机号每天发送同类型验证码不能超过3次';
			 
			return false; 
		}
	     $this->code = rand(1000,9999);  
		 $data['phone'] = $this->phone;
		 $data['addtime'] = time()+$this->limittime;
		 $data['code'] = $this->code;
		 $data['type'] = $this->sendtype;
		 $this->mysql->insert(Mysite::$app->config['tablepre'].'mobileapp',$data);   
         if(Mysite::$app->config['smstype'] == 1){
			 $smsParams = array(
			'code'=>(string)$this->code,			
			);
			 $this->timelong = $this->limittime;	 
			$aliCls=new alidayuClass();
			$resp=$aliCls->sendTextMessage($this->aliqianming, $smsParams, $this->phone, $this->aliarr[$this->sendtype]);
			if(   $resp->Code == 'OK'  ){
				logwrite('发送成功：'.$this->phone.":".$this->maincontent[$this->sendtype].$this->code);
			}else{
				logwrite('大于错误信息:'.$resp->Message.'错误CODE:'.$resp->Code);
			}

		 }else{
			 $contents =  '【'.$this->qianming.'】'.$this->maincontent[$this->sendtype].$this->code; 
			 $APIServer = 'http://www.waimairen.com/sendtophone.php?apiuid='.Mysite::$app->config['apiuid'];  
			 $weblink = $APIServer.'&key='.trim(Mysite::$app->config['sms86ac']).'&code='.trim(Mysite::$app->config['sms86pd']).'&hm='.$this->phone.'&msgcontent='.urlencode($contents).'&msgtype=check'; 
			 
			 $contentcccc =  file_get_contents($weblink);  
			 logwrite('短信发送链接:'. $weblink);   
			  $this->timelong = $this->limittime;	 
			 logwrite('短信发送结果:'.$contentcccc);  
		 } 
		 ICookie::set('getbackphonecode',$this->code,90);
	     return true; 
	}
	//校验验证码是否有效   $Inputcode 校验code
	public function checkcode($Inputcode){

		 #$checkcancode = Mysite::$app->config['allowedcode'];
		 $checkcancode = Mysite::$app->config['regestercode'];		  
//		 if($this->sendtype != 4){
//			 if($checkcancode != 1 && $this->sendtype != 7){ 
//					return true; 
//			 } 
//		 }


		 if(!IValidate::suremobi($this->phone)){
			 $this->errId = '手机号格式错误';
			  return false;
		 } 

		 if(!in_array($this->sendtype,$this->typearray)){
			  $this->errId = '未定义的发送类型';
			  return false;
		 } 

		 if(empty($Inputcode)){
			   $this->errId = '未设置验证码';
			  return false;
		 }

//                   print_R($this->phone);print_R($this->sendtype);
		 $checkphone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."mobileapp where phone ='".$this->phone."' and type='".$this->sendtype."'  order by addtime desc limit 0,1");
                 if(!empty($checkphone)){

			$checktime = time()-$this->limittime; 
			if($checkphone['addtime'] > $checktime){ 
			     if($Inputcode != $checkphone['code']){
					 $this->errId = '验证码错误';
					return false;
				 }
				 $this->code = $checkphone['code'];
				 return true;
			}else{
				$this->errId = '验证码已失效';
				return false;
			}
		 }else{
			  $this->errId = '该手机号未发送验证码';
			  return false;
		 }
	}
	public function getCode(){
		return $this->code; 
	}
	public function getError()
	{
		return $this->errId;
	}
	public function getTime(){
		return  $this->timelong;
	}
    

}
?>