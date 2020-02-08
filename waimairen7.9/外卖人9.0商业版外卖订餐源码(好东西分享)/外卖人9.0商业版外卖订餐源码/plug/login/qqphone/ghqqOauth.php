<?php 
Class ghqqOauth  {
	//模板展示
	public $app_id;//登录APPID
	public $app_secret;//登录密钥
	public $redirect;//跳转页面
	public $login_type;//登录类型
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
		 $this->memberCls = new memberclass();
		 $this->app_id = '101315592';
		 $this->app_secret = '3a8c091d8f2a5544a980834142f888b4';
		 $this->back_url = Mysite::$app->config['siteurl'].'/plug/login/qqphone/login.php';
		 $this->initapi();
	} 
	//初始化登录接口
	public function initapi(){ 
		 $info = $this->mysql->select_one("select * from `".Mysite::$app->config['tablepre']."otherlogin`   where `loginname`='qqphone'  order by id desc");
		#print_r($info);exit;
		 if(empty($info)){
			 echo 'QQ登录接口未安装';
			 exit;
		 }
		 if(empty($info['temp'])){
			 echo 'QQ登录接口未设置';
			 exit;
		 }
		 $tempall = json_decode($info['temp'],true);
		#print_r($tempall);exit;
		 $this->app_id = $tempall['appid'];
	     $this->app_secret =  $tempall['appkey'];
		 $this->apiinfo = $tempall;
	} 
	public function gettable(){
		return 'mobile_url';
	}
	 
	//获取登录前缀
	public function webtoplink(){
		 
		   return $this->mobile_url;
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
		logwrite("qq登录获取code值::".$linkpre);
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
		logwrite(var_export($userinfo,true));
        $userinfo['openid'] = $this->openid;
        ICookie::set('qquser',$userinfo,86400);
		if($userinfo['ret']  == 0){
                if(Mysite::$app->config['wxLoginType']==0){
                    //自动登录
                    $this->getuserinfo($userinfo);
                    $newlink = Mysite::$app->config['siteurl']."/index.php?ctrl=wxsite&action=myaccount";
                    header("location:".$newlink);
                }else{

                    $is_bdphone =$this->mysql->select_one("select a.phone  from ".Mysite::$app->config['tablepre']."member as a left join ".Mysite::$app->config['tablepre']."oauth as b on a.uid=b.uid  where b.openid ='".$this->openid."' and b.type='qq' ");

                    if(empty($is_bdphone['phone'])){
                        $link = IUrl::creatUrl('wxsite/qqbdphone');
                        $this->refunction('',$link);
                    }else{
                        $this->getuserinfo($userinfo);
                        $defaultlink = IUrl::creatUrl('wxsite/member');
                        $weblink = ICookie::get('wx_login_link');
                        $newlink = empty($weblink)? $defaultlink:$weblink;
                        header("location:".$newlink);
                    }
                }
		}else{
			echo $userinfo['msg'];
			exit;
			return false;
		} 
	}
    /***  构造 数据  ***/
    public function getuserinfo($userinfo){

        //登录成功
        $oauthqqinfo =$this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."oauth  where openid ='".$userinfo['openid']."' and type='qq' ");
        $qqinfo = array();
        if($userinfo['phone']>0){
            $is_user = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."member where phone='".$userinfo['phone']."'  ");
        }
        if(empty($oauthqqinfo)){
            if(empty($is_user)){
				$temp_password = 'ghwmr123456789';
                $arr['username'] = $userinfo['nickname'];
                $arr['phone'] = $userinfo['phone'];
                $arr['address'] = '';
				$arr['temp_password'] = $temp_password;
                $arr['password'] = md5($temp_password);
                $arr['email'] = '';
                $arr['creattime'] = time();
                $arr['score']  = empty(Mysite::$app->config['regesterscore'])?0:Mysite::$app->config['regesterscore'];
                $arr['logintime'] = time();
                $arr['loginip'] ='';
                $arr['group'] = 9;
                $arr['logo'] = $userinfo['figureurl_qq_2'];
                $arr['sex'] = $userinfo['gender'];
                $newusername = $userinfo['nickname'];
                $checkusername ='x';
                $k = 0;
                while(!empty($checkusername)){
                    $newusername = $k==0? $newusername:$newusername.$k;
                    $checkusername = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username ='".$newusername."' ");
                    $k = $k+1;
                    if(empty($checkusername)){
                        break;
                    }
                }
                $arr['username'] = $newusername;
                $this->mysql->insert(Mysite::$app->config['tablepre']."member",$arr);
                $uid = $this->mysql->insertid();
				if($arr['score'] > 0){
					$datass['userid'] =  $uid;
					$datass['type'] = 1;
					$datass['addtype'] = 1;
					$datass['result'] = $arr['score'];
					$datass['addtime'] = time();
					$datass['title'] = '注册送积分';
					$datass['content'] ='注册送积分'.$arr['score'];  
					$datass['acount'] = $arr['score'];
					$this->mysql->insert(Mysite::$app->config['tablepre'].'memberlog',$datass);
					logwrite('插入积分数据');
				}
				logwrite('送积分结束');
				 $juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 2 or name = '注册送优惠券' " );$juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 2 or name= '注册送优惠券' order by id asc " );
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
				 
            }else{
                $uid = $is_user['uid'];
               //$cnewdata['username'] = $userinfo['nickname'] ;
                $cnewdata['logo'] = $userinfo['figureurl_qq_2'];
                $this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$uid."'");
            }
            $data['uid'] = $uid;
            $data['token'] = $this->token['access_token'];
            $data['openid'] = $userinfo['openid'];
            $data['type'] = 'qq';
            $data['addtime'] = time();
            $this->mysql->insert(Mysite::$app->config['tablepre'].'oauth',$data);
            $flag = 1;
        }else{

            $mbdata['token'] = $this->token['access_token'];
            $mbdata['openid'] = $this->openid;
            $this->mysql->update(Mysite::$app->config['tablepre'].'oauth',$mbdata,"openid='".$this->openid."'");
            $membercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$oauthqqinfo['uid']."' ");

            $yuid = $oauthqqinfo['uid'];
            if(!empty($membercheck)){
                if(empty($membercheck['username'])){
                    $newusername = $userinfo['nickname'];
                    $checkusername ='x';
                    $k = 0;
                    while(!empty($checkusername)){
                        $newusername = $k==0? $newusername:$newusername.$k;
                        $checkusername = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username ='".$newusername."' ");
                        $k = $k+1;
                        if(empty($checkusername)){
                            break;
                        }
                    }
                    $cnewdata['username'] = $newusername;
                }
                if(empty($is_user)){
                    if(!empty($userinfo['phone'])) $cnewdata['phone'] = $userinfo['phone'];
                }else{
                    $wx['uid'] = $is_user['uid'];
                    $this->mysql->update(Mysite::$app->config['tablepre'].'oauth',$wx,"openid='".$userinfo['openid']."'");
                    $oauthqqinfo['uid'] = $is_user['uid'];
//                    $cnewdata['username'] = $newusername;

                    $tcuser['cost'] = 0;
                    $tcuser['score'] = 0;
                    $this->mysql->update(Mysite::$app->config['tablepre'].'member',$tcuser,"uid='".$yuid."'");
                    $juan['uid'] = $oauthqqinfo['uid'];
                    $this->mysql->update(Mysite::$app->config['tablepre'].'juan',$juan,"uid='".$yuid."'");
                    $address['userid'] = $oauthqqinfo['uid'];
                    $this->mysql->update(Mysite::$app->config['tablepre'].'address',$address,"userid='".$yuid."'");
                    $orderdata['buyeruid'] = $oauthqqinfo['uid'];
                    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"buyeruid='".$yuid."'");
                }
                $cnewdata['logo'] = $userinfo['figureurl_qq_2'];;
                $cnewdata['sex'] = $userinfo['gender'];
                $cnewdata['cost'] = $is_user['cost']+$membercheck['cost'];
                $cnewdata['score'] = $is_user['score']+$membercheck['score'];
                $this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$oauthqqinfo['uid']."'");
                $flag = 2;
                $uid = $oauthqqinfo['uid'];

            }else{
				$temp_password = 'ghwmr123456789';
                $arr['username'] =$userinfo['nickname'];
                $arr['phone'] = $userinfo['phone'];
                $arr['address'] = '';
				$arr['temp_password'] = $temp_password;
                $arr['password'] = md5($temp_password);
                $arr['email'] = '';
                $arr['creattime'] = time();
                $arr['score'] =0;
                $arr['logintime'] = time();
                $arr['loginip'] ='';
                $arr['group'] = 9;
                $arr['logo'] = $userinfo['figureurl_qq_2'];
                $arr['sex'] = $userinfo['gender'];  //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                $newusername = $userinfo['nickname'];
                $checkusername ='x';
                $k = 0;
                while(!empty($checkusername)){
                    $newusername = $k==0? $newusername:$newusername.$k;
                    $checkusername = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username ='".$newusername."' ");
                    $k = $k+1;
                    if(empty($checkusername)){
                        break;
                    }
                }
                $arr['username'] = $newusername;
                $this->mysql->insert(Mysite::$app->config['tablepre']."member",$arr);
                $uid = $this->mysql->insertid();
                $data['uid'] = $uid;
                $this->mysql->update(Mysite::$app->config['tablepre'].'oauth',$data,"openid='".$userinfo['openid']."'");
                $flag = 1;
            }
        }

        ICookie::set('checklogins',$flag,86400);
        ICookie::set('logintype','qq',86400);
        ICookie::set('adtoken',$this->token['access_token'],86400);
        ICookie::set('adopenid',$userinfo['openid'],86400);
        ICookie::set('nickname',$userinfo['openid'],86400);
        $userinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'  ");
        #ICookie::set('email',$userinfo['email'],86400);
        ICookie::set('memberpwd',$userinfo['password'],86400);
        ICookie::set('membername',$userinfo['username'],86400);
        ICookie::set('uid',$uid,86400);
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
		logwrite("--------------");
		logwrite($code);
		logwrite("--------------");
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