<?php
    class wxlogin{
        private $appid ='wx00b49b22fd448ac0';
        private $appsecret ='3d486bb6274d7099078e26c8a0ff0728';
        private $openid;

        //初始化数据
        function __construct(){
            spl_autoload_register('Mysite::autoload');
            $this->mysql = new mysql_class();
            $this->memberCls = new memberclass();
            $this->back_url = Mysite::$app->config['siteurl'].'/plug/login/wx/login.php';
            $this->initapi();
        }

        
        //初始化登录接口
        public function initapi(){ 
             $info = $this->mysql->select_one("select * from `".Mysite::$app->config['tablepre']."otherlogin`   where `loginname`='wx'  order by id desc");
            #print_r($info);exit;
             if(empty($info)){
                 echo '微信登录接口未安装';
                 exit;
             }
             if(empty($info['temp'])){
                 echo '微信登录接口未设置';
                 exit;
             }
             $tempall = json_decode($info['temp'],true);
            #print_r($tempall);exit;
             $this->appid = $tempall['appid'];
             $this->appsecret =  $tempall['appsecret'];
             $this->apiinfo = $tempall;
        } 
        


        //获取code
        function getcode(){
            $backurl = $this->back_url;
            $backurl = urlencode($backurl);
            $url = 'https://open.weixin.qq.com/connect/qrconnect?appid='.$this->appid.'&redirect_uri='.$backurl.'&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect';
            header("location:".$url);
        }


        //获取access_tokin
        function getacc($code){
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code';
            $res = file_get_contents($url);
            $tokininfo = json_decode($res,true);
            return $tokininfo;
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
           

        function get_user_info($access_token){
            $aaa = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'];
            $user = file_get_contents($aaa);
            $userinfo = json_decode($user,true);
            $this->token = $access_token['access_token'];
            return $userinfo;
        }
        //获取用户信息
        function setuserinfo($userinfo){
            
            
            if($userinfo['openid']>0){
                $is_user = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where wxopenid='".$userinfo['openid']."'  ");
            }
            
            $oauthinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$userinfo['openid']."'  ");
            if(empty($oauthinfo)){
                if(empty($is_user)){
                    $temp_password = 'ghwmr123456789';
                    $arr['username'] = $userinfo['nickname'];
                    $arr['phone'] = $is_user['phone'];
                    $arr['wxopenid'] = $userinfo['openid'];
                    $arr['address'] = '';
                    $arr['temp_password'] = $temp_password;
                    $arr['password'] = md5($temp_password);
                    $arr['email'] = '';
                    $arr['creattime'] = time();
                    $arr['score']  = empty(Mysite::$app->config['regesterscore'])?0:Mysite::$app->config['regesterscore'];
                    $arr['logintime'] = time();
                    $arr['loginip'] ='';
                    $arr['group'] = 10;
                    $arr['logo'] = $userinfo['headimgurl'];
                    $arr['sex'] = $userinfo['sex'];
                    
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
                           $juandata['limitcost']   = $value['limitcost'];  
                           
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
                   $cnewdata['username'] = $userinfo['nickname'] ;
                    $cnewdata['logo'] = $userinfo['headimgurl'];
                    $this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$uid."'");
                }
                $data['uid'] = $uid;
                $data['wxuserlogo'] = $userinfo['headimgurl'];
                $data['wxusername'] = $userinfo['nickname'];
                $data['access_token'] = $this->token;
                $data['openid'] = $userinfo['openid'];
                
                $this->mysql->insert(Mysite::$app->config['tablepre'].'wxuser',$data);
                $flag = 1;
            }else{

                $mbdata['access_token'] = $this->token;
                $mbdata['openid'] = $this->openid;
                $mbdata['wxusername'] = $userinfo['nickname'];
                $mbdata['wxuserlogo'] = $userinfo['headimgurl'];
                
                $this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$mbdata,"uid='".$is_user['uid']."'");
                $membercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$oauthinfo['uid']."' ");

                $yuid = $oauthinfo['uid'];
                if(!empty($membercheck)){
                    if(empty($membercheck['username'])){
                        $newusername = $userinfo['nickname'];
                        
                        $cnewdata['username'] = $newusername;
                    }
                    if(empty($is_user)){
                        if(!empty($userinfo['phone'])) $cnewdata['phone'] = $userinfo['phone'];
                    }else{
                        $wx['uid'] = $is_user['uid'];
                        $this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$wx,"uid='".$is_user['uid']."'");
                        $oauthinfo['uid'] = $is_user['uid'];
    //                    $cnewdata['username'] = $newusername;

                        $tcuser['cost'] = 0;
                        $tcuser['score'] = 0;
                        $this->mysql->update(Mysite::$app->config['tablepre'].'member',$tcuser,"uid='".$yuid."'");
                        $juan['uid'] = $oauthinfo['uid'];
                        $this->mysql->update(Mysite::$app->config['tablepre'].'juan',$juan,"uid='".$yuid."'");
                        $address['userid'] = $oauthinfo['uid'];
                        $this->mysql->update(Mysite::$app->config['tablepre'].'address',$address,"userid='".$yuid."'");
                        $orderdata['buyeruid'] = $oauthinfo['uid'];
                        $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"buyeruid='".$yuid."'");
                    }
                        $cnewdata['logo'] = $userinfo['headimgurl'];;
                        $cnewdata['sex'] = $userinfo['sex'];
                        $cnewdata['cost'] = $is_user['cost']+$membercheck['cost'];
                        $cnewdata['score'] = $is_user['score']+$membercheck['score'];
                        $this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$oauthinfo['uid']."'");
                        $flag = 2;
                        $uid = $oauthinfo['uid'];

                }else{
                    $temp_password = 'ghwmr123456789';
                    $arr['username'] =$userinfo['nickname'];
                    $arr['phone'] = $is_user['phone'];
                    $arr['wxopenid'] = $userinfo['openid'];
                    $arr['address'] = '';
                    $arr['temp_password'] = $temp_password;
                    $arr['password'] = md5($temp_password);
                    $arr['email'] = '';
                    $arr['creattime'] = time();
                    $arr['score'] =0;
                    $arr['logintime'] = time();
                    $arr['loginip'] ='';
                    $arr['group'] = 10;
                    $arr['logo'] = $userinfo['headimgurl'];
                    $arr['sex'] = $userinfo['sex'];  //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                    
                    
                    $this->mysql->insert(Mysite::$app->config['tablepre']."member",$arr);
                    $uid = $this->mysql->insertid();
                    $data['uid'] = $uid;
                    $this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$data,"openid='".$userinfo['openid']."'");
                    $flag = 1;
                }
            }

                ICookie::set('checklogins',$flag,86400);
                ICookie::set('logintype','wx',86400);
                ICookie::set('adtoken',$userinfo['access_token'],86400);
                ICookie::set('wxopenid',$userinfo['openid'],86400);
                ICookie::set('nickname',$userinfo['nickname'],86400);
                $userinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'  ");
                #ICookie::set('email',$userinfo['email'],86400);
                ICookie::set('memberpwd',$userinfo['password'],86400);
                ICookie::set('userlogo',$userinfo['headimgurl'],86400);
                ICookie::set('membername',$userinfo['username'],86400);
                ICookie::set('uid',$uid,86400);

        }

        //登录调用检测调用
        public function login(){
            
            $userinfo = array();
            //获取code
            $code = $_GET['code'];
            if($code>0){
                //获取access_tokin
                $access_token = $this->getacc($code);
                if($access_token['errcode']>0){
                    //获取access_tokin失败
                    print_r($access_token);
                    exit;
                }else{
                    //获取用户信息
                    $userinfo = $this->get_user_info($access_token);
                    $this->openid = $userinfo['openid'];
                    $this->setuserinfo($userinfo);
                }

            }else{
                $this->getcode();
            }

            
            
            if(!empty($userinfo)){
                $is_bdphone =$this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."member as a left join ".Mysite::$app->config['tablepre']."wxuser as b on a.uid=b.uid  where b.openid ='".$this->openid."'  ");
                

                if(empty($is_bdphone['phone'])){
                    $link = IUrl::creatUrl('member/pcbdphone');
                    $this->refunction('',$link);
                }else{
                    
                    $defaultlink = IUrl::creatUrl('member/index');
                    
                    header("location:".$defaultlink);
                    
                }

            }else{
                echo $userinfo['msg'];
                exit;
                return false;
            } 
        }
    }
    
?>