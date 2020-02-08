<?php 

/**
 * @class baseclass 
 * @描述   基础类
 */
class baseclass extends wmrclass
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
	 	     #print_r($data['member']);
	 	     $data['admininfo'] = $this->admin;  
			$cityId = 0;
			$CITY_ID = ICookie::get('CITY_ID');
			if( !empty($CITY_ID) ){
				$CITY_IDArr = explode('_',$CITY_ID);
				$cityId = $CITY_IDArr[2];
			}
			if(  ( $controller == 'site' && $action == 'index' )    ||   ( $controller == 'market' && $action == 'index' )   ||   ( $controller == 'site' && $action == 'showcart' )   ||   ( $controller == 'shop' && $action == 'index' )   ) {   
				if( empty($cityId)  ) {
 					$link = IUrl::creatUrl('site/guide'); 
					$this->message('',$link);
				}
			}
			$this->CITY_ID = $cityId;
			$CITY_NAME = ICookie::get('CITY_NAME');
			if( !empty($CITY_NAME) ){
				$CITY_NameArr = explode('_',$CITY_NAME);
				$CITY_NAME = $CITY_NameArr[2];
			}
			$this->CITY_NAME = $CITY_NAME;
			$data['CITY_ID'] = $this->CITY_ID;
			$data['CITY_NAME'] = $this->CITY_NAME;
	 	    $platpsinfo =  $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$cityId."' ");
	 	    $shopid = intval(IReq::get('shopid'));
	 	    if(!empty($shopid) && empty($cityId) && empty($platpsinfo)){
	 	        $shopadmin_id=  $this->mysqlcache->longTime()->select_one("select admin_id from ".Mysite::$app->config['tablepre']."shop  where id={$shopid}");
	 	        $platpsinfo = $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$shopadmin_id['admin_id']."' ");
	 	    }
            $this->platpsinfo = $platpsinfo;
			$data['platpsinfo'] = $platpsinfo;
			$cshopid = ICookie::get('adminshopid');
			if(!empty($cshopid)){
				$cshopinfo =  $this->mysqlcache->longTime()->select_one("select admin_id from ".Mysite::$app->config['tablepre']."shop  where  id = ".$cshopid."");  
				$cplatpsinfo = $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$cshopinfo['admin_id']."' ");				 
				$data['cplatpsinfo'] = $cplatpsinfo;
			}
 			$checkmodule =  $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."module  where name='".$controller."' and install=1 limit 0,20");  
	 	    if(empty($checkmodule) && !in_array($controller,array('site','market'))){ 
	 	         $this->message('未安装此模版'); 
	 	     }  
	 	     $openid =   IFilter::act(IReq::get('openid'));  //openid='.$this->obj->FromUserName.'&='.$time.'&= 
		   	  $actime =  IFilter::act(IReq::get('actime')); 
		   	  if(!empty($openid) && !empty($actime)){
		   	  	if($controller == 'wxsite'){
		   	     $sign =  IFilter::act(IReq::get('sign')); 
		   	    $mycode = Mysite::$app->config['wxtoken'];
		   	    $checkstr = md5($mycode.$actime);
		   	    $checkstr = substr($checkstr,3,15); 
		   	     
		   	    if($checkstr == $sign && !empty($openid)){
		   	   	 
		   	   	  $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid ='".$openid."' ");
		   	   	  if(!empty($checkinfo)){
		   	   	       ICookie::set('logintype','wx',86400);
		   	   	       ICookie::set('wxopenid',$openid,86400);
		   	   	       $backinfo = IFilter::act(IReq::get('backinfo')); 
		   	   	       if(empty($backinfo)){
		   	   	       $link = IUrl::creatUrl('wxsite/index'); 
		   	   	      }else{
		   	   	        	$newtr = '';
		   	   	         
		   	   	        	$testinfo = explode(',',$backinfo); 
		   	   	        	
                       foreach($testinfo as $key=>$value){
                       	if(!empty($value)){
                            $newtr .= chr($value);
                          }
                       }
                  
		   	   	      	$link = $newtr;
		   	   	       
		   	   	      	if(empty($link)){
		   	   	      		 $link = IUrl::creatUrl('wxsite/index'); 
		   	   	      	}
		   	   	      }
		   	   	       
		   	   	      $this->message('',$link);
		   	      } 
		   	    } 
		   	  }
		   	 }
		   	   
	 	      
	 	     $data['moduleid']= $checkmodule['id']; 
	 	     $data['moduleparent'] = $checkmodule['parent_id']; 
	 	     $id = intval(IFilter::act(IReq::get('id'))); 
	 	     $data['id'] = $id; 
	 	      
	 	     Mysite::$app->setdata($data);  
	}
	public function setLoginInfo($wxuser,$userinfo){
		#logwrite((var_export($wxuser,true)));
		//构造微信APP登录 xiaozu_wxappoauth
		$wxoauth['openid'] = $wxuser['openid']; 
		$wxoauth['username'] = $wxuser['nickname'];
		$wxoauth['imgurl'] = $wxuser['headimgurl'];
        $flag = 0;
        $is_user = array();
        if($wxuser['phone']>0){
            $is_user = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$wxuser['phone']."'  ");
        }
		 $uid = 0;
        $oauthinfo=$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$wxuser['openid']."'  ");
		#print_r($oauthinfo);exit;
		if(empty($oauthinfo)){//写用户数据

                if(empty($is_user)){
                    $temp_password = 'ghwmr123456789';
                    $arr['username'] = $wxoauth['username'];
                    $arr['phone'] = $wxuser['phone'];
                    $arr['address'] = '';
                    $arr['temp_password'] = $temp_password;
                    $arr['password'] = md5($temp_password);
                    $arr['email'] = '';
                    $arr['creattime'] = time();
                    $arr['score']  =empty(Mysite::$app->config['regesterscore'])?0:Mysite::$app->config['regesterscore'];
                    $arr['logintime'] = time();
                    $arr['loginip'] ='';
                    $arr['group'] = 10;
                    $arr['logo'] = $wxoauth['imgurl'];
                    $arr['sex'] = $wxuser['sex'];  //用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
                    
                    $this->mysql->insert(Mysite::$app->config['tablepre']."member",$arr);
                    $uid = $this->mysql->insertid();
					if($arr['score'] > 0){
						$this->memberCls->addlog($uid,1,1,$arr['score'],'注册送积分','注册送积分'.$arr['score'],$arr['score']);
					}
					$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 2 or name = '注册送优惠券' " );
					$juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 2 or name= '注册送优惠券' order by id asc " );
					if($juansetinfo['status'] ==1 && !empty($juaninfo)){
					//注册送优惠券		
					logwrite('送优惠券条件');
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
							//logwrite('送优惠券即将结束');
							$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$juandata);
							//logwrite('送优惠券已结束');
						} 
					}
					
                }else{
                    $uid = $is_user['uid'];
//                    $cnewdata['username'] = $wxoauth['username'] ;
//                    $cnewdata['logo'] = $wxoauth['imgurl'];
//                    $this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$uid."'");
                }
				
				$mbdata['wxusername'] = $wxuser['nickname'];
				$mbdata['wxuserlogo'] = $wxuser['headimgurl'];
                $mbdata['uid'] = $uid;
                $mbdata['openid'] = $wxoauth['openid'];
                $mbdata['is_bang'] = 0;
                $mbdata['access_token'] = $userinfo['access_token'];
                $mbdata['expires_in'] = $userinfo['expires_in']+time();
                $mbdata['refresh_token'] = $userinfo['refresh_token'];
				
                $this->mysql->insert(Mysite::$app->config['tablepre'].'wxuser',$mbdata);
                $flag = 1;
		}else{//更新用户数据
			$mbdata['wxusername'] = $wxuser['nickname'];
			$mbdata['wxuserlogo'] = $wxuser['headimgurl'];
            $mbdata['access_token'] = $userinfo['access_token'];
			$mbdata['expires_in'] = $userinfo['expires_in']+time();
			$mbdata['refresh_token'] = $userinfo['refresh_token']; 
			$this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$mbdata,"openid='".$wxuser['openid']."'");

			$membercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$oauthinfo['uid']."' ");
            $yuid = $membercheck['uid'];
			if(!empty($membercheck)){
				if(empty($membercheck['username'])){
					$newusername = $wxoauth['username'];
                   $cnewdata['username'] = $newusername;
				}
                if(empty($is_user)){
                    if(!empty($wxuser['phone'])) $cnewdata['phone'] = $wxuser['phone'];
                }else{
                    $wx['uid'] = $is_user['uid'];

                    $this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$wx,"openid='".$wxuser['openid']."'");
                    $oauthinfo['uid'] = $is_user['uid'];  
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
                $cnewdata['cost'] = $is_user['cost']+$membercheck['cost'];
                $cnewdata['score'] = $is_user['score']+$membercheck['score'];
				$this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$oauthinfo['uid']."' ");
                $flag = 2;
				$uid = $oauthinfo['uid'];
			}else{
                if(empty($is_user)){
                    $temp_password = 'ghwmr123456789';
                    $arr['username'] = $wxoauth['openid'];
                    $arr['phone'] = $wxuser['phone'];
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
                }else{
                    $uid = $is_user['uid'];
                }
                $wx['uid'] = $uid;
                $this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$wx,"openid='".$wxuser['openid']."'");
                $flag = 1;
            }
		}
         ICookie::set('checklogins',$flag,86400);
		 ICookie::set('logintype','wx',86400);
		 ICookie::set('wxopenid',$wxuser['openid'],86400);
		 $userinfo=  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'  ");
		 $this->member = $userinfo;
         #ICookie::set('email',$userinfo['email'],86400);
         ICookie::set('memberpwd',$userinfo['password'],86400);
         ICookie::set('membername',$userinfo['username'],86400);
         ICookie::set('uid',$uid,86400);
	 } 

	 
}
?>