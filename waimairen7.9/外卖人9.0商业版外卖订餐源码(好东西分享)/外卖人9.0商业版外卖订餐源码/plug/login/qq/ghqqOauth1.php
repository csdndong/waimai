<?php 
Class ghqqOauth  {
	//模板展示
	public $app_id;//登录APPID
	public $app_secret;//登录密钥
	public $redirect;//跳转页面
	public $login_type;//登录类型
	public $web_url ='https://graph.qq.com/oauth2.0/authorize';//网站QQ登录
    public $mobile_url = 'https://graph.qq.com/oauth2.0/authorize';//手机网站QQ登录
	public $back_url;//回调地址
	public $wdb;
	public $config;
	public $error;
	public $openid;//用户开放ID
	public $token;
	public $apiinfo;
	public $mysql;
	public function init(){   
		 spl_autoload_register('Mysite::autoload');   
		 $this->mysql = new mysql_class();  
		 $this->app_id = '101315592';
		 $this->app_secret = '3a8c091d8f2a5544a980834142f888b4';
		 $this->back_url = Mysite::$app->config['siteurl'].'/plug/login/qq/login.php';
		 $this->initapi();
	} 
	//初始化登录接口
	public function initapi(){ 
		 $info = $this->mysql->select_one(" select * from `".Mysite::$app->config['tablepre']."otherlogin`   where `loginname`='qq'  "); 
        
		 if(empty($info)){
			 echo 'QQ登录接口未安装';
			 exit;
		 }
		 if(empty($info['temp'])){
			 echo 'QQ登录接口未设置';
			 exit;
		 }
		 
		 $tempall = json_decode($info['temp'],true);
		 $this->app_id = $tempall['appid'];
	     $this->app_secret =  $tempall['appkey'];
		 $this->apiinfo = $tempall;  
	} 
	public function gettable(){
		return 'oauthqq';
	}
	 
	//获取登录前缀
	public function webtoplink(){
		 
		   return $this->web_url; 
	   //获取前缀
	}
	//根据UID获取登录信息
	public function Byuid($uid){
		if(empty($this->apiinfo)){
			$this->error ='不存在的安装信息';
			return $this->defaultuser();
		}
        if($this->apiinfo['install'] ==0){
			 
			return $this->defaultuser();
		} 
		$userinfo =   $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."oauth  where uid = ".$uid."  and type='qq' limit 0,1");
        
		if(empty($userinfo)){
			return $this->defaultuser();
		}else{
			return $userinfo;
		}
		
	}
	private function defaultuser(){
	    return array('uid'=>0,'username'=>'guest','role'=>'guest');
	}
	
	//获取跳转获取code值
	private function dologinlink(){
		//构造登录连接
		$linkpre = $this->webtoplink(); 
		$linkpre .='?response_type=code&client_id='.$this->app_id.'&redirect_uri='.URLEncode($this->back_url).'&state='.$this->getstate();   
		header("Location:$linkpre");
	}
	private function getstate(){
	    //构造  state值
		$state = ICookie::get('qqstate');//构造state值得
		if(empty($state)){
		    //将state 转换为其他值得
			$newdata = md5(time());//使用的是直接md5加密可换成天方法
			$state = $newdata;
			ICookie::set('qqstate',$state,600);
		}
		return $state;
	}
	//返回验证state是否一致  不一致则  不调用登录  重新   调用登录代码
	private function checkstate(){  
		$nowstate = ICookie::get('qqstate');
		$state = $_GET['state'];
		if($state != $nowstate){
		   return false;
		}
		return true;
	} 
	
	//判断是否登录
	private function checklogin(){
		$logintype = ICookie::get('logintype');//构造state值得
		//$openid = ICookie::get(PlugName);//构造state值得 
		if($logintype == 'qq'){ 
			$this->openid = ICookie::get('qq_openid'); 
			//检测$this->openid 对应用户 的token是否过期  未过期 则  可以  查询数据库内数据
			$checkinfo = $this->openid;
			//检测令牌 
			if(empty($checkinfo)){ 
				// $this->openid = $this->get_open_id();
				// if(empty($this->openid)){
				// }
				$this->get_open_id();
			}else{//
				
			}
		}else{
			$this->get_open_id();
		}
		
	}
	 
	
	
	//登录调用检测调用
	public function login(){ 
		$this->init();
		if(empty($this->openid)){
			$this->checklogin();
		}  
		$userinfo = $this->get_user_info(); 
		if($userinfo['ret']  == 0){
			//登录成功  
			 $oauthqqinfo =$this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."oauth  where openid ='".$this->openid."' and type='qq' "); 
			 $qqinfo = array();
			 if(empty($oauthqqinfo)){
				 
				    $tempuid = intval(ICookie::get('uid'));
				    $data['uid'] = $tempuid;
					$data['token'] = $this->token['access_token'];
					$data['openid'] = $this->openid;
					$data['type'] = 'qq';
					$data['addtime'] = time();
					$this->mysql->insert(Mysite::$app->config['tablepre'].'oauth',$data);  
					if(!empty($tempuid)){
						 
						$link = IUrl::creatUrl('member/base');
						$this->refunction('',$link);
					}else{  
					  ICookie::set('adlogintype','qq',86400); 
					  ICookie::set('adtoken',$this->token['access_token'],86400); 
					  ICookie::set('adopenid',$this->openid,86400); 
					  ICookie::set('nickname',$this->openid,86400); 
					  	$link = IUrl::creatUrl('member/bandaout');
						$this->refunction('',$link);
					 
				    } 
			 }else{
				 
					if($oauthqqinfo['uid'] == 0){
						$tempuid = intval(ICookie::get('uid'));
						if(!empty($tempuid)){
							$this->mysql->update(Mysite::$app->config['tablepre'].'oauth',array('uid'=>$this->member['uid']),"openid='".$this->openid."' and type = 'qq'");
							 
							$link = IUrl::creatUrl('member/base');
							$this->refunction('',$link);
						}else{
							ICookie::set('adlogintype','qq',86400); 
							ICookie::set('adtoken',$this->token['access_token'],86400); 
							ICookie::set('adopenid',$this->openid,86400);
							ICookie::set('nickname',$this->openid,86400); 
							$link = IUrl::creatUrl('member/bandaout');
							$this->refunction('',$link);
						}
					}else{   
						  if($tempuid > 0){
							   
							   $link = IUrl::creatUrl('member/base');/*跳转到用户中心*/ 
							   $this->refunction('',$link);
						  }else{  
							$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where  uid  = '".$oauthqqinfo['uid']."'"); 
							if(empty($userinfo)){
								 $link = IUrl::creatUrl('member/login');/*跳转到用户中心*/ 
								$this->refunction('账号未查找到,关联账号是否被删除',$link); 
							}
							$data['loginip'] = IClient::getIp();
							$data['logintime'] = time();
							$checktime = date('Y-m-d',time());
							$checktime = strtotime($checktime);
							 
						   $this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$userinfo['uid']."'");	 
						   ICookie::set('logintype','qq',86400);
							ICookie::set('uid',$userinfo['uid'],86400);  
							$link = IUrl::creatUrl('member/base');/*跳转到用户中心*/
							$this->refunction('',$link);
							} 
						}
			 }
			 
			  
			return true;
			/***  构造 数据  ***/
		}else{
			echo $userinfo['msg'];
			exit;
			return false;
		} 
	}
	public static function refunction($msg,$info=''){
   	  $newrul = empty($info)?Mysite::$app->config['siteurl']:$info;
	    header("Content-Type:text/html;charset=utf-8"); 
	    if(!empty($msg))
	    {
	    	 $lngcls = new languagecls();
	 			 $msg = $lngcls->show($msg);
			   echo '<script>alert(\''.$msg.'\');location.href=\''.$newrul.'\';</script>';
		  }else{
		     echo '<script>location.href=\''.$newrul.'\';</script>';
	  	}
      exit;
   }
   public static function success($msg,$link=''){
   	   $datatype = IFilter::act(IReq::get('datatype')); 
	 		if($datatype == 'json'){
	 			 echo json_encode(array('error'=>false,'msg'=>$msg)); 
	       exit; 
	 		}else{
	 			 self::refunction($msg,$link); 
	 		}
    	
   }
	/**
	* [get_access_token 获取access_token]
	* @param [string] $code [登陆后返回的$_GET['code']]
	* @return [array] [expires_in 为有效时间 , access_token 为授权码 ; 失败返回 error , error_description ]
	*/
	private function get_access_token()
	{
		$code = trim($_GET['code']);
		if(empty($code)){
			$this->dologinlink();
		}else{
			 
			if($this->checkstate()){
				
				$token_url = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&'.'client_id='.$this->app_id.'&redirect_uri='.urlencode($this->back_url).'&client_secret='.$this->app_secret.'&code='.$code;
				 
				$token = array();
				//expires_in 为access_token 有效时间增量
				parse_str($this->_curl_get_content($token_url), $token); 
				if(isset($token['access_token'])){
					if($token['expires_in'] < 1){
						 $this->token = $token;
						 $this->refreshtoken();
					}else{ 
						$this->token = $token;
					}
				}else{
					print_r($token);
					exit;
				} 
			}else{
				$this->dologinlink();
			}
		}
		//获取access_token
		
	}
	private function refreshtoken(){
			$token_url = 'https://graph.qq.com/oauth2.0/token?grant_type=refresh_token&'.'client_id='.$this->app_id.'&redirect_uri='.urlencode($this->back_url).'&client_secret='.$this->app_secret.'&refresh_token='.$this->token['refresh_token'];
			$token = array();
				//expires_in 为access_token 有效时间增量
			parse_str($this->_curl_get_content($token_url), $token); 
			if(isset($token['access_token'])){
					 
						$this->token = $token;
					 
			}else{
				$this->token = array();
					print_r($token);
					exit;
			}  
	}
		 
	/**
	* [get_open_id 获取用户唯一ID，openid]
	* @param [string] $token [授权码]
	* @return [array] [成功返回client_id 和 openid ;失败返回error 和 error_msg]
	*/
	private function get_open_id()
	{
		if(empty($this->token)){
			$this->get_access_token(); 
		}
	    $str = $this->_curl_get_content('https://graph.qq.com/oauth2.0/me?access_token='.$this->token['access_token']);
	   
		  if(strpos($str,"callback") !== false){
				 $lpos = strpos($str,"(");
				 $rpos = strrpos($str,")");
				 
				  $str = substr($str,$lpos + 1,$rpos-$lpos-1); 
				  $user = json_decode($str, TRUE);    
				  $this->openid = $user['openid'];
		  }
		//	
		//	return $user;
	}
			   
	/**
	* [get_user_info 获取用户信息]
	* @param [string] $token [授权码]
	* @param [string] $open_id [用户唯一ID]
	* @return [array] [ret：返回码，为0时成功。msg为错误信息,正确返回时为空。...params]
	*/
	private function get_user_info()
	{ 
		$user_info_url = 'https://graph.qq.com/user/get_user_info?access_token='.$this->token['access_token'].'&oauth_consumer_key='.$this->app_id.'&openid='.$this->openid.'&format=json';
	    $info = json_decode($this->_curl_get_content($user_info_url), TRUE); 
		return $info;
	}
			   
	private function _curl_get_content($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		//设置超时时间为3s
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}
	public function __call($method,$arg){  
      print_r('请求函数不存在');
	}
 
} 
?>