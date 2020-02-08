<?php 

/**
 * @class baseclass 
 * @描述   基础类
 */
class wxbaseclass extends wmrclass
{ 
	 public $memberCls;
	 public $member;
	 public $pageCls;
	 public $admin;
	 public $digui;
	 public $CITY_ID;
	 public $CITY_NAME;
	 public $platpsinfo;
	 function init(){
         //主要是检测权限 
	 	     $this->memberCls = new memberclass($this->mysql); 
	 	     $this->member = $this->memberCls->getinfo();  
			 
	 	     $this->pageCls = new page();
	 	     $this->admin =  $this->memberCls->getadmininfo();  
	 	     $this->digui = array();//递归处理数组 
	 	     $controller = Mysite::$app->getController();
			 $action = Mysite::$app->getAction();  
	 	    
			 
			 
			 
			 
	 	     $data['member'] = $this->member; 
	 	     $data['admininfo'] = $this->admin;   
 	 	     $logintype = ICookie::get('logintype');  
			 
			 $cityId = 0;
			$CITY_ID = ICookie::get('CITY_ID');
			if(!empty($CITY_ID)){
				$CITY_IDArr = explode('_',$CITY_ID);
				$cityId = $CITY_IDArr[2];
			}
		 #	print_R($cityId);exit;
			 $this->CITY_ID = $cityId;
			 $lng = ICookie::get('lng');
			 $lat = ICookie::get('lat');
			 $data['lng']=$lng;
			 $data['lat']=$lat;
	 
			$CITY_NAME = ICookie::get('CITY_NAME');
			if(!empty($CITY_NAME)){
				$CITY_NameArr = explode('_',$CITY_NAME);
				$CITY_NAME = $CITY_NameArr[2];
			}
			$this->CITY_NAME = $CITY_NAME;
			 
			$data['CITY_ID'] = $this->CITY_ID;
			$data['CITY_NAME'] = $this->CITY_NAME;
			$id = IFilter::act(IReq::get('id'));
		 	if(empty($cityId)){
		 		$shop = $this->mysqlcache->longTime()->select_one("select admin_id from ".Mysite::$app->config['tablepre']."shop  where id ='".$id."' ");
		 		$cityId = $shop['admin_id'];
		 	}
			$platpsinfo =  $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$cityId."' ");  
			$this->platpsinfo = $platpsinfo;
			$data['platpsinfo'] = $platpsinfo;
			if( !strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){    //判断是微信浏览器不
				$data['WeChatType'] = 0;
			}else{
				$data['WeChatType'] = 1;//微信端
			}
			$shangou = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$cityId."' and name = 'shangou' ");
		    $say = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$cityId."' and name = 'say' ");
		    $paotui = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$cityId."' and name = 'paotui' ");
			$data['shangou'] = $shangou['is_show']; 
		    $data['say'] = $say['is_show']; 
		    $data['paotui'] = $paotui['is_show'];
			$wxopenid  = ICookie::get('wxopenid');
			$action = Mysite::$app->getAction();
			$datatype = IFilter::act(IReq::get('datatype'));
			$ulogin = intval(IFilter::act(IReq::get('ulogin')));
            $loadaction=array('index','noticelist','distribution_fxcode','forgetpwd','ajaxnoticelist','setpwd','notice','sharehb','memsharej','shopshow','dwLocation','mkshopshow','mkcatefoods','loadindexcontent','indexshoplistdata','shoplistdata','saveloation','shoplist','specialpagelistdata','loginmode','choice','marketshop','specialpage','marketlistdata','waimai','marketlist','paotui','togethersay','togethersaydata','foodshow','getshopmorecomment','getshopcomment','getdetailinfo','commentwxuser','foodsgg','wxbdphone','shopcart','memsharehb','search','searchresult','xieyi');
			if($datatype == 'json' || in_array($action,$loadaction)  ){

			}else{
                if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ //判断是微信浏览器不
                    if($this->member['uid'] <= 0){
                        if(Mysite::$app->config['wxLoginType']==0){
                            //微信自动登录
                            $this->wxlogin();
                            $this->setwxlogin(0);
                        }else{
                            $arract=array('reg','forpwd','forgetnextpwd','setlogin','qqbdphone','login');
                            if($ulogin != 1 &&  !in_array($action,$arract)){
                                 $myurl = Mysite::$app->config['siteurl'].$_SERVER["REQUEST_URI"];	  
                                 ICookie::set('wx_login_link',$myurl,86400);
                                $link = IUrl::creatUrl('wxsite/loginmode');
                                $this->message('',$link);
                            }
                        }
                    }
                }
			}
		    $this->doshare();
			$checkmodule =  $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."module  where name='".$controller."' and install=1 limit 0,20");
	 	     if(empty($checkmodule) && !in_array($controller,array('site','market'))){ 
	 	         $this->message('未安装此模版'); 
	 	     }   
	 	     $data['moduleid']= $checkmodule['id'];
	 	     $data['moduleparent'] = $checkmodule['parent_id']; 
	 	     $id = intval(IFilter::act(IReq::get('id'))); 
	 	     $data['id'] = $id; 
			 $data['member'] = $this->member; 
	 	     Mysite::$app->setdata($data);
	 }
	 /***设置分享***/
	 function doshare(){
		$sharedata['signPackage'] = '';
		if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){
			$wxclass = new wx_s();
			$signPackage = $wxclass->getSignPackage();
			$sharedata['signPackage'] = $signPackage;
			$sharedata['signPackage']['shareimg'] = Mysite::$app->config['share_img'];
		}
		#print_r($sharedata);
		Mysite::$app->setdata($sharedata);
	 } 
	 //判断微信登录
	 public function setwxlogin($loginmode){
         $code = IFilter::act(IReq::get('code'));

         $userinfo = array();
         $token_link = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . Mysite::$app->config['wxappid'] . '&secret=' . Mysite::$app->config['wxsecret'] . '&code=' . $code . '&grant_type=authorization_code';
         $token = json_decode($this->curl_get_content($token_link), TRUE);
        
		 if(isset($token['access_token'])){
			 //logwrite("token".var_export($token,true));
             $userinfo['openid'] = $token['openid'];
             $userinfo['unionid'] = $token['unionid'];
             $userinfo['access_token'] = $token['access_token'];
             $userinfo['refresh_token'] = $token['refresh_token'];
             $expires_in = $token['expires_in'];
             if($expires_in < 1){
                 //刷新CODE
                 $refresh_link = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . Mysite::$app->config['wxappid'] . '&grant_type=refresh_token&refresh_token=' . $userinfo['refresh_token'];
                 $refresh = json_decode($this->curl_get_content($refresh_link), TRUE);
                 if (isset($refresh['access_token'])) {
                     $userinfo['openid'] = $refresh['openid'];
                     $userinfo['unionid'] = $refresh['unionid'];
                     $userinfo['access_token'] = $refresh['access_token'];
                     $userinfo['refresh_token'] = $refresh['refresh_token'];
                     $expires_in = $refresh['expires_in'];
                 } else {
                     echo $refresh['errcode'];
                     exit;
                 }
             }
         }else{
             echo $token['errcode'];
             exit;
         }
         $check_link = 'https://api.weixin.qq.com/sns/auth?access_token=' . $userinfo['access_token'] . '&openid=' . $userinfo['openid'];
         $checkopen = json_decode($this->curl_get_content($check_link), TRUE);
         if($checkopen['errcode'] == 0){
//logwrite("checkopen".var_export($checkopen,true));
         }else{
             echo $checkopen['errcode'];
             exit;
         }
		 
	 
		 
         //获取用户信息
         $getlink = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $userinfo['access_token'] . '&openid=' . $userinfo['openid'];
 
		 $wxuser = json_decode($this->curl_get_content($getlink), TRUE);
 
		 if(isset($wxuser['openid'])){
			// logwrite("wxuser".var_export($wxuser,true));

         }else{
             echo $wxuser['errcode'];
             exit;
         }
         // if($loginmode==0){
		$this->setLoginInfo($wxuser,$userinfo);
             //$newlink = Mysite::$app->config['siteurl']."/index.php?ctrl=wxsite&action=myaccount";
             //header("location:".$newlink);
         // }else{
             // $data['wxuser'] = $wxuser;
             // $data['userinfo'] = $userinfo;
             // return $data;
         // }
     }
	public function setLoginInfo($wxuser,$userinfo){
		//logwrite((var_export($wxuser,true)));
		//构造微信APP登录 xiaozu_wxappoauth
		$wxoauth['openid'] = $wxuser['openid']; 
		$wxoauth['username'] =  $wxuser['nickname'];
		$wxoauth['imgurl'] = $wxuser['headimgurl'];
        $flag = 0;
        $is_user = array();
        // if($wxuser['phone']>0){
            // $is_user = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$wxuser['phone']."'  ");
        // }
		$uid = 0;
		$oauthinInsetFlag = false;
        $oauthinfo=$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$wxuser['openid']."'  "); 
		if(empty($oauthinfo)){// oauthinfo 不存在 
				$mbdata['wxusername'] = $this->strFilter($wxuser['nickname']);
				$mbdata['wxuserlogo'] = $wxuser['headimgurl'];
                $mbdata['uid'] = 0;
                $mbdata['openid'] = $wxoauth['openid'];
                $mbdata['is_bang'] = 0;
                $mbdata['access_token'] = $userinfo['access_token'];
                $mbdata['expires_in'] = $userinfo['expires_in']+time();
                $mbdata['refresh_token'] = $userinfo['refresh_token'];
				 
                $this->mysql->insert(Mysite::$app->config['tablepre'].'wxuser',$mbdata);
				$oauthinfo=$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$wxuser['openid']."'  "); 
                $flag = 1;
				$oauthinInsetFlag = true;
		}else{ //oauthinfo 存在
			$mbdata['wxusername'] = $this->strFilter($wxuser['nickname']);
			$mbdata['wxuserlogo'] = $wxuser['headimgurl'];
            $mbdata['access_token'] = $userinfo['access_token'];
			$mbdata['expires_in'] = $userinfo['expires_in']+time();
			$mbdata['refresh_token'] = $userinfo['refresh_token']; 
			$this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$mbdata,"openid='".$wxuser['openid']."'");
			$oauthinfo['wxusername'] = $this->strFilter($wxuser['nickname']);
			$oauthinfo['wxuserlogo'] = $wxuser['headimgurl'];
            $oauthinfo['access_token'] = $userinfo['access_token'];
			$oauthinfo['expires_in'] = $userinfo['expires_in']+time();
			$oauthinfo['refresh_token'] = $userinfo['refresh_token'];
			
		}
		//获取   $oauthinfo 成功
		if($oauthinfo['uid'] == 0){
			$membercheck = '';
		}else{
			$membercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$oauthinfo['uid']."' ");
		}
		if(empty($membercheck)){//用户信息不存在
			$temp_password = 'ghwmr123456789';
			$arr['username'] = $wxoauth['openid'];
			$arr['phone'] = '';
			$arr['address'] = '';
			$arr['temp_password'] = $temp_password;
			$arr['password'] = md5($temp_password);
			$arr['email'] = '';
			$arr['creattime'] = time();
			$arr['score'] =0;
			$arr['logintime'] = time();
			$arr['loginip'] ='';
			$arr['group'] = 10;
			$arr['logo'] = $wxoauth['imgurl'];
			$arr['sex'] = $wxoauth['sex'];  //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
			$arr['uid'] = $oauthinfo['uid'];
			$newusername = $this->strFilter($wxoauth['username']);
			$newusername = empty($newusername)?'x'.time():$newusername;
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
			$membercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$uid."' "); 
			$flag = 1;
		}else{
			$flag = 2;
		}
		if($oauthinInsetFlag == true){
			$this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',array('uid'=>$membercheck['uid']),"openid='".$wxuser['openid']."'");
		}
		//获取会员信息成功
		$fxinfo =  $this->mysql->select_one("select fxpid,addtime from ".Mysite::$app->config['tablepre']."fxpid where openid ='".$oauthinfo['openid']."' ");
		if($fxinfo['fxpid'] > 0 && $oauthinfo['uid'] != $fxinfo['fxpid'] ){
			$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('fxpid'=>$fxinfo['fxpid'],'befxtime'=>$fxinfo['addtime']),"uid='".$oauthinfo['uid']."'");
		} 
		if(Mysite::$app->config['wxLoginType']==0){
			
			 ICookie::set('checklogins',$flag,86400);
			 ICookie::set('logintype','wx',86400);
			 ICookie::set('wxopenid',$wxuser['openid'],86400); 
			 $this->member = $membercheck;
			 #ICookie::set('email',$userinfo['email'],86400);
			 ICookie::set('memberpwd',$membercheck['password'],86400);
			 ICookie::set('membername',$membercheck['username'],86400);
			 ICookie::set('uid',$uid,86400); 
			 if(empty($membercheck['phone'])){
				    session_start();
					ICookie::set('bindwxopenid',$wxuser['openid'],1800); 
					$_SESSION['bindingwxlogin'] =time(); 
					$link = IUrl::creatUrl('wxsite/wxbdphone');
					$this->message('',$link);
			}else{ 
				$defaultlink = IUrl::creatUrl('wxsite/member');
				$weblink = ICookie::get('wx_login_link');
				$link = empty($weblink)? $defaultlink:$weblink;
				$this->message('',$link);
			} 
		}else{
			if(empty($membercheck['phone'])){ 
				session_start();
				ICookie::set('bindwxopenid',$wxuser['openid'],1800); 
				$_SESSION['bindingwxlogin'] =time(); 
				$link = IUrl::creatUrl('wxsite/wxbdphone');
				$this->message('',$link);
			}else{ 
				ICookie::set('checklogins',$flag,86400);
				ICookie::set('logintype','wx',86400);
				ICookie::set('wxopenid',$wxuser['openid'],86400); 
				$this->member = $membercheck;
				 #ICookie::set('email',$userinfo['email'],86400);
				ICookie::set('memberpwd',$membercheck['password'],86400);
				ICookie::set('membername',$membercheck['username'],86400);
				ICookie::set('uid',$uid,86400); 
				$defaultlink = IUrl::creatUrl('wxsite/member');
				$weblink = ICookie::get('wx_login_link');
				$link = empty($weblink)? $defaultlink:$weblink;
				$this->message('',$link);
			} 
			
		}
	 }



    public function wxlogin(){
        if(is_mobile_request()){
            if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){
                $wxopenid  = ICookie::get('wxopenid');
                $code = IFilter::act(IReq::get('code'));
                $state = IFilter::act(IReq::get('state'));
                $doinsert = 0;
                if(empty($wxopenid)){
                    //echo 'openid 为空';
                    if(empty($code)){
                        //跳转到微信OPenlink
                        $this->getwxcode();
                    }
                }else{
                    $wxinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$wxopenid."'");
                    if(empty($wxinfo)){
                        /*未关注用户不可登录*/
                        if(empty($code)){
                            $this->getwxcode();
                        }
                    }
                }
            }else{

            }
        }else{
            if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){
                $wxopenid  = ICookie::get('wxopenid');
                $code = IFilter::act(IReq::get('code'));
                $doinsert = 0;
                if(empty($wxopenid)){
                    //echo 'openid 为空';
                    if(empty($code)){
                        //跳转到微信OPenlink
                        $this->getwxcode();
                    }
                }else{
                    $wxinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$wxopenid."'");
                    if(empty($wxinfo)){
                        /*未关注用户不可登录*/
                        if(empty($code)){
                            $this->getwxcode();
                        }
                    }
                }
            }else{
            }
        }
    }
	 public function getwxcode(){
	 	    $myurl = Mysite::$app->config['siteurl'].$_SERVER["REQUEST_URI"];
			$action = Mysite::$app->getAction();
			if($action != 'setlogin' &&$action != 'makeorder' &&$action != 'login'){ 
				ICookie::set('wx_login_link',$myurl,86400);
			}
			 
	 	    $newurl = urlencode($myurl);
	 	    $getlink = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".Mysite::$app->config['wxappid']."&redirect_uri=".$newurl."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
	 	    header("location:".$getlink);
	 	    exit;
	 }
}
?>