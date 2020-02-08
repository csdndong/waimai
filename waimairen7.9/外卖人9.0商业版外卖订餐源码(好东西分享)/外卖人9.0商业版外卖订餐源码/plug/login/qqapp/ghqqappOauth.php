<?php 
Class ghqqappOauth  {
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
	function __construct($mysql,$memberCls){
		$this->mysql = $mysql; 
		$this->memberCls = $memberCls;
	}
	public function init(){    
		 $this->app_id = '';
		 $this->app_secret = '';
		 $this->back_url = Mysite::$app->config['siteurl'].'/plug/login/qqapp/login.php'; 
		 $this->initapi();
	} 
	//初始化登录接口
	public function initapi(){ 
		 
		 $info = $this->mysql->select_one(" select * from `".Mysite::$app->config['tablepre']."otherlogin`   where `loginname`='qqapp' "); 
        
		 if(empty($info)){
			 $this->error ='QQ登录接口未安装'; 
		 }else{
			  if(empty($info['temp'])){
			 $this->error ='QQ登录接口未设置'; 
			}else{
				 $tempall = json_decode($info['temp'],true);  
				 $this->app_id = $tempall['appid'];
				 $this->app_secret =  $tempall['appkey'];
				 $this->apiinfo = $tempall;  
			} 
		}
		
	} 
	function getappid(){
		$this->init();
		return $this->app_id;
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
		$userinfo =   $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."oauth  where uid = ".$uid."  and type='qqapp' limit 0,1");
		
		  
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
	 
	public function bingphone($phone,$openid){
		 
		$this->init();
		
		$userinfo = $this->get_user_info(); 
		// print_r($userinfo);
		logwrite('fffffffff'.var_export($userinfo,true));
		if(empty($phone)){
			$this->error = '手机号错误';
			return false; 
		}
		if(empty($openid)){
			$this->error = '登陆openid错误';
			return false;
		}
		
		$oauthqqinfo =$this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."oauth  where openid ='".$openid."' and type='qqapp' "); 
		if(empty($oauthqqinfo)){
			$this->error = '无登陆信息';
			return false;
			
		} 
		// if($oauthqqinfo['uid'] > 0){
			// $checkmember =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$oauthqqinfo['uid']."'  ");
			// if(!empty($checkmember)){
				// $this->error = '请重新发起登陆';
				// return false;
			// }
		// }
		$phonemember = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phone."'  ");
		if(!empty($phonemember)){
			if($oauthqqinfo['uid'] == 0){
				$oauthqqinfo2 =$this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."oauth  where uid ='".$phonemember['uid']."' and type='qqapp' "); 
				if(!empty($oauthqqinfo2)){
					$this->error = '该手机号已绑定其它QQ号不能重复绑定';
					return false;  
				} 
				$cnewdata['uid'] = $phonemember['uid'];
				$this->mysql->update(Mysite::$app->config['tablepre'].'oauth',$cnewdata,"openid='".$openid."' and type='qqapp' ");   
			}else{
				if($phonemember['uid'] != $oauthqqinfo['uid']){
					$this->error = '该手机号已绑定其他账号';
					return false; 
				}
			} 
		}else{
			 $temp_password = 'ghwmr123456789';  
			 $checkstr = md5($phone);
			 #$arr['username'] = substr($checkstr,0,8);
			 $arr['username'] = $oauthqqinfo['username'];
			 $arr['phone'] = $phone;
			 $arr['address'] = '';
			 $arr['password'] = md5($temp_password);
			 $arr['email'] = '';
			 $arr['creattime'] = time(); 
			 $arr['score']  = empty(Mysite::$app->config['regesterscore'])?0:Mysite::$app->config['regesterscore'];
			 $arr['logintime'] = time(); 
			 #$arr['logo'] = $userinfo['figureurl_qq_2'];
			 $arr['logo'] = $oauthqqinfo['logo'];
			 $arr['loginip'] = IClient::getIp();
			 $arr['group'] = 5;
			 $arr['cost'] = 0; 
			 $arr['parent_id'] =0;
			 $arr['temp_password'] = $temp_password;
			 $this->mysql->insert(Mysite::$app->config['tablepre'].'member',$arr);   
			 $uid = $this->mysql->insertid(); 
			 if($arr['score'] > 0){
				$this->memberCls->addlog($uid,1,1,$arr['score'],'注册送积分','注册送积分'.$arr['score'],$arr['score']);
			 } 
		 $juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 2 or name = '注册送优惠券' " );  	   
         $juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 2 or name= '注册送优惠券' order by id asc " );	   
		 if($juansetinfo['status'] ==1 && !empty($juaninfo)){
	 	   //注册送优惠券		   		   	  
		   foreach($juaninfo as $key=>$value){			   
			   $juandata['uid'] = $uid;// 用户ID	
			   $juandata['username'] = $arr['username'];// 用户名
			   $juandata['name'] = $value['name'];//  优惠券名称 
			   $juandata['status'] = 1;// 状态，0未使用，1已绑定，2已使用，3无效	
			   $juandata['card'] = $nowtime.rand(100,999);
			   $juandata['card_password'] =  substr(md5($juandata['card']),0,5);
			   $juandata['limitcost']	= $value['limitcost'];	
			   
			   if($juansetinfo['timetype'] == 1){
					$juandata['creattime'] = time();
					$date = date('Y-m-d',$juandata['creattime']);
					$endtime = strtotime($date) + ($juansetinfo['days']-1)*24*60*60+86399;
					$juandata['endtime'] = $endtime;
			   }else{
					$juandata['creattime'] = $value['starttime'];
					$juandata['endtime'] =  $value['endtime'];
			   }
               if($juansetinfo['costtype'] == 1){
				    $juandata['cost'] = $value['cost'];
			   }else{
					$juandata['cost'] = rand($value['costmin'],$value['costmax']);
			   }			   			   		  	    		   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$juandata);			   
		   } 
       } 
			 $phonemember = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");
			 $cnewdata['uid'] = $phonemember['uid'];
			 $this->mysql->update(Mysite::$app->config['tablepre'].'oauth',$cnewdata,"openid='".$openid."' and type='qqapp' ");   
	
		} 
		$tjyhj = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."juan where uid='".$phonemember['uid']."' and status < 2 and  endtime > ".time()." ");  
		$phonemember['juancount'] = $tjyhj;  
		unset($phonemember['password']); 
		$phonemember['logo'] = Mysite::$app->config['siteurl'].$phonemember['logo']; 
		$expire = time() + 86400; // 设置24小时的有效期
		setcookie("app_login", "app_login", $expire);
		setcookie("app_loginphone", $phonemember['phone'], $expire);  
		$this->oauthinfo = $phonemember;
		logwrite('ssssss'.var_export($this->oauthinfo,true));
		return true;
	}
	
	//登录调用检测调用
	public function login($token,$openid){ 
		$this->init();
		/*
		if(empty($this->openid)){
			$this->checklogin();
		}  
		*/
		if(empty($token)){
			$this->error = 'access_token错误';
			return false; 
		}
		if(empty($openid)){
			$this->error = '登陆openid错误';
			return false;
		}
		$this->token['access_token'] = $token;
		$this->openid = $openid;
		$this->oauthinfo = array();
		$this->error = '';
		$userinfo = $this->get_user_info(); 
		 
		if($userinfo['ret']  == 0){
			//登录成功  
			 $oauthqqinfo =$this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."oauth  where openid ='".$this->openid."' and type='qqapp' "); 
			 $qqinfo = array();
			 if(empty($oauthqqinfo)){
					
					$data['username'] = $userinfo['nickname'];
					$data['logo'] = $userinfo['figureurl_qq_2'];  
				    $tempuid = 0;
				    $data['uid'] = $tempuid;
					$data['token'] = $this->token['access_token'];
					$data['openid'] = $this->openid;
					$data['type'] = 'qqapp';
					$data['addtime'] = time();  
					$this->mysql->insert(Mysite::$app->config['tablepre'].'oauth',$data);  
					$oauthqqinfo = $data;
					 
			}
			$this->oauthinfo = array('phone'=>'','uid'=>0);
			if(!empty($userinfo['figureurl_qq_2'])){ 
				$this->oauthinfo['logo'] =$userinfo['figureurl_qq_2'];
			}else{
				$this->oauthinfo['logo'] = Mysite::$app->config['siteurl'].Mysite::$app->config['userlogo'];
			}
			if($oauthqqinfo['uid'] == 0){
				
			}else{
				$checkuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where  uid  = '".$oauthqqinfo['uid']."'"); 
			 
				//$oauthinfo['phone'] = $checkuser['phone']; 
				if(!empty($checkuser)&& !empty($checkuser['phone'])){
					unset($checkuser['password']); 
					$datav['username'] = $userinfo['nickname'];
					$datav['logo'] = $userinfo['figureurl_qq_2'];
					$this->mysql->update(Mysite::$app->config['tablepre'].'member',$datav,"uid='".$oauthqqinfo['uid']."'"); 
					$this->oauthinfo = array_merge($checkuser,$userinfo); 
					$this->oauthinfo['phone'] = $checkuser['phone'];
					$this->oauthinfo['username'] = $userinfo['nickname'];
					if(!empty($this->oauthinfo['figureurl_qq_2'])){ 
						$this->oauthinfo['logo'] = preg_match('/(http:\/\/)|(https:\/\/)/i',$this->oauthinfo['figureurl_qq_2'])?$this->oauthinfo['figureurl_qq_2']:Mysite::$app->config['siteurl'].$this->oauthinfo['logo'];
					}else{
						$this->oauthinfo['logo'] = Mysite::$app->config['siteurl'].Mysite::$app->config['userlogo'];
					}
					$tjyhj = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."juan where uid='".$oauthqqinfo['uid']."' and status < 2 and  endtime > ".time()." ");  
					$this->oauthinfo['juancount'] = $tjyhj;
					
					$expire = time() + 86400; // 设置24小时的有效期
					setcookie("app_login", "app_login", $expire);
					setcookie("app_loginphone", $checkuser['phone'], $expire); 
				} 
				logwrite('xxxxxxxxxxxxx'.var_export($this->oauthinfo,true));
			} 
			return true; 
		}else{
			$this->error = $userinfo['msg']; 
			return false;
		} 
	}
	public function geterr(){
		return $this->error;
	}
	public function getuserinfo(){
		
		return $this->oauthinfo;
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