<?php 

/**
 * @class Cart
 * @brief 天气预报
 */
class memberclass
{
	private $error = ''; 
	private $uid = 0;
	 
	 //普通用户登录
	 
	protected $mysql; 
	 function __construct($mysql)
	 {
	 	$this->mysql = $mysql;
	 }
	 
  function login($uname,$pwd){
  	   /*登录时用户可以使用用户名和手机号登录
	    *登录时，先根据手机号和用户名去查找用户，如果结果为空，说明登录账号错误
		*如果结果不为空，说明用户存在，然后根据输入的密码去查找，如果查找不到说明密码不正确
	   **/
	   $md5c = md5($pwd);
	   $checkuserinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where  username='".$uname."' or phone='".$uname."'   ");
	   $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where password = '".$md5c."' and ( username='".$uname."' or phone='".$uname."' )  ");
	   $uname=$uname;
	   if(empty($checkuserinfo)){
	   	  $this->error = '登录账号错误';
	      return false;//and password  = '".$md5c."'
	   }elseif(empty($userinfo)){
	   	  $this->error = '密码错误';
	   	  return false;
	   }else{
	   //更新用户信息 
	      $data['loginip'] = IClient::getIp();
	      $data['logintime'] = time();
	      $checktime = date('Y-m-d',time());
	      $checktime = strtotime($checktime);
              $loginscore = Mysite::$app->config['loginscore'];
              $maxdayscore = Mysite::$app->config['maxdayscore'];
              $userlogininfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."memberlog  where  userid ='".$userinfo['uid']."' and addtime >  '".$checktime."' and content  like '%用户登录赠送积分%' ");
           if($loginscore > 0 &&  empty($userlogininfo) && $maxdayscore > 0) {
                     $this->xzscore($uname);
                     $score = $maxdayscore>$loginscore?$loginscore:$maxdayscore;
	      	     $data['score'] = $userinfo['score']+$score;
	             $mess['content'] = '用户登录赠送积分'.$score.'总积分'.$data['score'];
                 $this->addlog($userinfo['uid'],1,1,$score,'每天登录',$mess['content'],$data['score']);
	          }
	      $this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$userinfo['uid']."'");	
	      $logintype = ICookie::get('adlogintype');
	 	    $token = ICookie::get('adtoken');
	 	    $openid = ICookie::get('adopenid'); 
	 	    if(!empty($logintype)){
	 	 	       $apiinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."otherlogin where loginname='".$logintype."'  ");
	 	 	     if(!empty($apiinfo)){ 
	 	 	   	     $this->mysql->update(Mysite::$app->config['tablepre'].'oauth',array('uid'=>$userinfo['uid']),"openid='".$openid."' and type = '".$logintype."'");  
	 	 	   	     ICookie::clear('adlogintype'); 
	             ICookie::clear('adtoken');  
	             ICookie::clear('adopenid'); 
	 	 	     }
	 	    }
	      #ICookie::set('email',$userinfo['email'],86400);
        ICookie::set('memberpwd',$pwd,86400);
        ICookie::set('membername',$userinfo['username'],86400);
        ICookie::set('uid',$userinfo['uid'],86400);
		ICookie::clear('logintype');
		ICookie::clear('wxopenid');
        $this->uid = $userinfo['uid']; 
        return true;
  	 }
  }
  function xzscore(){
      $time = date('Y-m-d',time());
      $uinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where  email='".$uname."' or username='".$uname."' or phone='".$uname."'   ");
      $userscoreinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."memberlog  where  userid ='".$uinfo['uid']."' and addtime >  '".strtotime($time.' 00:00:00')."' and addtime <  '".strtotime($time.' 23:59:59')."'  "); 
      return true;
  }
  //普通用户注册
  function regester($email,$tname,$password,$phone,$group,$userlogo='',$address='',$cost=0,$score=0,$admin_id,$invitecode){  
  	if(empty($phone)){
  	  $this->error = '手机号不能为空';
  	  return false;
  	}
    if(!empty($phone)){
    	if(!(IValidate::suremobi($phone)))
     	{
     		$this->error = 'errphone';
     		return false; 
     	}
		/*member表中group字段  3是商家会员 其余的是普通会员*/
	  $checkmember = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."member where phone='".$phone."'  ");      
      if($checkmember > 0)
      {
        	$this->error = 'exitphone';
     	  	return false; 
      } 
    	
    }
  	if(!(IValidate::len($tname,2,20)))
    {
  		 $this->error = '用户名长度2到20';
  		 return false; 
  	}  
    if(!(IValidate::len($password,6,20)))
    {
     	$this->error = 'member_pwdlen6to20';
  		return false; 
    }  
    if(!empty($invitecode) ){   	 	  
	    if(Mysite::$app->config['is_open_distribution'] == 1){
		    $checkmemberx = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."member where invitecode='".$invitecode."'  ");      
		    if(empty($checkmemberx)){
				$this->error = '请填写有效的邀请码';
				return false; 
		    } else{
				$arr['fxpid'] = $checkmemberx['uid'];
			}
	    }else{
		    $this->error = '网站未开启分销';
			return false; 
	    }	
    }
     $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username='".$tname."' ");
     if(!empty($userinfo)){
     	$this->error = 'member_repeatname';
  		return false; 
     }  
     $arr['username'] = $tname;
     $arr['phone'] = $phone;
     $arr['address'] = $address;
     $arr['password'] = md5($password);
     $arr['email'] = $email;
     $arr['creattime'] = time(); 
     $arr['score']  = $score == 0?Mysite::$app->config['regesterscore']:$score;
     $arr['logintime'] = time(); 
     $arr['logo'] = $userlogo;
     $arr['loginip'] = IClient::getIp();
     $arr['group'] = $group;
     $arr['cost'] = $cost; 
     $arr['admin_id'] = $admin_id; 
     $arr['parent_id'] = intval(ICookie::get('logincode'));
     $arr['temp_password'] = $password;
     $this->mysql->insert(Mysite::$app->config['tablepre'].'member',$arr);
     $this->uid = $this->mysql->insertid();
     
     if($arr['score'] > 0){
        $this->addlog($this->uid,1,1,$arr['score'],'注册送积分','注册送积分'.$arr['score'],$arr['score']);
     }
     
     
     $logintype = ICookie::get('adlogintype');
	 	 $token = ICookie::get('adtoken');
	 	 $openid = ICookie::get('adopenid'); 
	 	 if(!empty($logintype)){
	 	 	   $apiinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."otherlogin where loginname='".$logintype."'  ");
	 	 	   if(!empty($apiinfo)){
	 	 	   	//更新
	 	 	   	  $tempuid = $this->uid;
	 	 	   	  $this->mysql->update(Mysite::$app->config['tablepre'].'oauth',array('uid'=>$tempuid),"openid='".$openid."' and type = '".$logintype."'"); 
	          ICookie::set('logintype',$logintype,86400);  
	 	 	   }
	 	 }
         $juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 2 or name = '注册送优惠券' " );  	   
         $juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 2 or name= '注册送优惠券' order by id asc " );	   
		 if($juansetinfo['status'] ==1 && !empty($juaninfo)){
	 	   //注册送优惠券		   		   	  
		   foreach($juaninfo as $key=>$value){			   
			   $juandata['uid'] = $this->uid;// 用户ID	
			   $juandata['username'] = $arr['username'];// 用户名
			   $juandata['name'] = $value['name'];//  优惠券名称 
			   $juandata['status'] = 1;// 状态，0未使用，1已绑定，2已使用，3无效	
			   $juandata['card'] = $nowtime.rand(100,999);
			   $juandata['card_password'] =  substr(md5($juandata['card']),0,5);
			   $juandata['limitcost']	= $value['limitcost'];	
			   $juandata['type'] = 2;
			   $juandata['paytype'] = $juansetinfo['paytype'];
			   if($juansetinfo['timetype'] == 1){
					$juandata['creattime'] = time();
					$date = date('Y-m-d',$juandata['creattime']);
					$endtime = strtotime($date) + ($juansetinfo['days']-1)*24*60*60+86399;
					$juandata['endtime'] = $endtime;
			   }else{
					$juandata['creattime'] = $juansetinfo['starttime'];
					$juandata['endtime'] =  $juansetinfo['endtime'];
			   }
               if($juansetinfo['costtype'] == 1){
				    $juandata['cost'] = $value['cost'];
			   }else{
					$juandata['cost'] = rand($value['costmin'],$value['costmax']);
			   }			   			   		  	    		   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$juandata);			   
		   } 
       }


// 根据前台注册的手机号检测此手机号数据库中是否领取过优惠券，如果有则更新UID和username status=1   
// 如果前台新注册的用户 存在分享者 shareuid > 0 则考虑返增推广分享者优惠券
$checkphonejuan =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where bangphone='".$phone."' and uid=0 and status = 0  "); 
if( !empty($checkphonejuan) ){
	$tdata['uid'] = $this->uid;
	$tdata['username'] = $tname;
	$tdata['status'] = 1;
	$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$tdata,"bangphone='".$phone."' and uid=0 and status = 0 ");	
}
$checksharejuan =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where bangphone='".$phone."' and uid='".$this->uid."' and shareuid > 0  "); 
if( !empty($checksharejuan) ){
	foreach( $checksharejuan as $key=>$jval ){
		$sharemember =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$jval['shareuid']."'   "); 
		if( !empty($sharemember) ){
			$srdata['name'] = $jval['name'];
			$srdata['limitcost'] = $jval['limitcost'];
			$srdata['cost'] = $jval['cost'];
			$srdata['uid'] = $sharemember['uid'];
			$srdata['username'] = $sharemember['username'];
			$srdata['status'] = 1;
			$srdata['bangphone'] = $sharemember['phone'];
			$srdata['type'] = 6;  //返增优惠券类型
			$srdata['endtime'] = $jval['endtime'];   
			$srdata['creattime'] = $jval['creattime'];      
			$srdata['paytype'] = $jval['paytype'];   
			$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$srdata);
		}
	}
}

  	return true; 	
  }
  function modify($array,$uid){
  	    $savedata = $array;
      	$testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");   
			  if(empty($testinfo)) 
			  {
			  	$this->error = 'member_noexit';
     		return false; 
			  } 
			  if(isset($array['password'])){
			  	 if(empty($array['password'])) 
			     {
			    	   unset($savedata['password']);
			     }else{
					 $savedata['temp_password'] =$savedata['password'];
			  	    $savedata['password'] =md5($savedata['password']);

			     }
			  }   
			  if(isset($array['phone']) && $array['phone'] != $testinfo['phone']){
			    //检测移动电话
			    $checkuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$array['phone']."' and  `group` =  '".$group."'  ");
			    if(!empty($checkuser))
			    {
			        $this->error = 'exitphone';
     	      	return false; 
			     }  
			  }
			   if(isset($array['username']) && $array['username'] != $testinfo['username']){
			    //检测移动用户名
			    $checkuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username='".$array['username']."' ");
			     if(!empty($checkuser)){
			      $this->error = 'member_repeatname';
     	      	return false; 
			     } 
			  }
			   if(isset($array['email']) && $array['email'] != $testinfo['email']){
			    //检测邮箱
			       $checkuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where email='".$array['email']."' ");
			       if(!empty($checkuser)){
			          $this->error = 'exitemail';
     	        	return false; 
			       } 
			  }
	     $this->mysql->update(Mysite::$app->config['tablepre'].'member',$savedata,"uid='".$uid."'");	 
	     return true; 	
  }
  function getuid(){
  	return $this->uid;
  } 
  //检测用户名是否注册
  function checkname($uname){ 
  	if(empty($uname)){
  			$this->error = 'member_emptyname';
  	return false;
  	}
  	$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username='".$uname."' ");
  	if(empty($userinfo)){
  	return true;
  	}else{
  			$this->error = 'member_repeatname';
  	return false;
  	}
  }
  function checkemail($uname){
  	if(empty($uname)){
  		  $this->error = 'erremail';
     	return false;
  	}
  	$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where email='".$uname."' ");
  	if(empty($userinfo)){
  	   return true;
  	}else{
  		$this->error = 'exitemail';
  	   return false;
  	}
  }
  //退出登录
  function  loginout(){
  	  ICookie::clear('membername'); 
	    ICookie::clear('memberpwd');  
	    ICookie::clear('email'); 
      ICookie::clear('uid'); 
      ICookie::clear('logintype');
      ICookie::clear('wxopenid');
	  ICookie::clear('wxcode');
	  ICookie::clear('wxuser');
  }
  //获取用户信息
	function getinfo(){
	       $uid = ICookie::get('uid');    
		
		   $phone = ICookie::get('phone');
	 	   $password = ICookie::get('memberpwd');   
	 	   $logintype = ICookie::get('logintype');  
	 	   $userinfo = array();  
	 	    if(!empty($logintype)){
	 	  	if($logintype == 'wx'){
	 	  		 $wxopenid  = ICookie::get('wxopenid');  
	 	  		 $apiinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$wxopenid."'  ");
	 	  		 $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$apiinfo['uid']."' ");  
					
				if(!empty($apiinfo) && empty($userinfo)){
					 //写入局到
					  
					    $arr['username'] = $apiinfo['wxusername'];
						$arr['phone'] = '';
						$arr['address'] = '';
						$arr['password'] = md5($apiinfo['openid']);
						$arr['email'] = '';
						$arr['creattime'] = time(); 
						$arr['score']  =0;
						$arr['logintime'] = time(); 
						$arr['logo'] = $apiinfo['wxuserlogo'];
						$arr['loginip'] ='';
						$arr['group'] = 10; 
						$arr['uid'] = $apiinfo['uid'];
						$this->mysql->insert(Mysite::$app->config['tablepre'].'member',$arr);  
						$cuid = $this->mysql->insertid();
						$this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',array('uid'=>$cuid),"openid='".$apiinfo['openid']."'");								
						$userinfo = $arr; 
				}
                if($userinfo['username'] == $apiinfo['openid']){
					   $userinfo['username'] = $apiinfo['wxusername'];
					   $userinfo['logo'] = $apiinfo['wxuserlogo'];
				}			 
	 	  	}else{
	 	  	  $apiinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."otherlogin where loginname='".$logintype."'  ");
		 
	 	  	  if(empty($apiinfo)){
	 	  		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' and password  = '".md5($password)."'"); 
	 	  	  }else{
	 	  		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' "); 
	 	  	  } 
	 	  	}
	 	   }else{ 
	 	  	//获取微信消息
	 	  	 $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' "); 
	 	   } 
		   if($logintype == 'fastlogin'){		  
				$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' and phone = '".$phone."' "); 	
			}
	 	   if( empty($userinfo) || !isset($userinfo['uid']) ){
	 	        $userinfo = array('uid'=>0,'username'=>'guest','group'=>'2');
	        }
		#  print_r($userinfo);
		#  exit;	
	    //获取用户成长值等级
      $gradelink = hopedir.'/config/membergrade.php';
	    if(file_exists($gradelink)){
	         $tempinfo = include(hopedir.'/config/membergrade.php'); 
	         if($userinfo['uid'] == 0){
	           $userinfo['gradename'] = '游客'; 
	         }else{
	         	 $check =   false;
	         	 foreach($tempinfo as $key=>$value){
	         	     if($userinfo['total'] >= $value['from'] && $userinfo['total'] < $value['to']){
	         	     	 $check = true;
	         	     	 $userinfo['gradename'] = $value['gradename'];
	         	     	 $userinfo['gradeinstro'] = $value['from'].'-'.$value['to'];
	         	       break;
	         	     }
	         	 }
	         	 if($check == false){
	         	     $userinfo['gradename'] = '未定义';
	         	     	 $userinfo['gradeinstro'] = $userinfo['total'];
	         	 }
	         }
	    }else{
	       $userinfo['gradename'] = '未定义'; 
	    } 
      return $userinfo;
	}	
	function getadmininfo(){
		$adminname =  ICookie::get('adminname'); 
	    $adminpwd = ICookie::get('adminpwd');  
	    $adminuid = ICookie::get('adminuid');  
	    $userinfo = array(); 
	    if(!empty($adminuid)){
	 	  	  $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where uid='".$adminuid."' and password  = '".md5($adminpwd)."'"); 
	 	  	  $userinfo['group'] = $userinfo['groupid'];
	 	  } 
	 	  if(empty($userinfo)||!isset($userinfo['uid'])){
	 	      $userinfo = array('uid'=>0,'username'=>'guest','group'=>'2');
	    }
	    return $userinfo;
	}  
	function findpwd($uname){
	  if(!(IValidate::email($uname))){
	  	$this->error='erremail';
	    return false;
	  } 
     $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where email='".$uname."' ");
     if(empty($memberinfo))
     {
     	 $this->error='member_noexit';
	     return false;
     } 
     $title = '找回'.Mysite::$app->config['sitename'].'帐号密码'; 
     $sign = $this->mymd5($memberinfo['password'],$memberinfo['username']);
     $emaildata['findpwdurl'] = IUrl::creatUrl("member/resetpwd/uid/".$memberinfo['uid']."/username/".urlencode($memberinfo['username'])."/sign/".$sign);
     
     $default_tpl = new config('tplset.php',hopedir);  
	 	 $tpllist = $default_tpl->getInfo(); 
	 	 if(!isset($tpllist['emailfindtpl']) || empty($tpllist['emailfindtpl'])) {
	 	 	$this->error='管理员未设置邮箱发送信息,请联系客服';
	    return false;
	 	 } 
	 	 $emaildata['username'] = $memberinfo['username'];
	 	 $emaildata['email'] = $memberinfo['email']; 
	 	 $emaildata['sitename'] = Mysite::$app->config['sitename'];
	 	 $emaildata['siteurl'] = Mysite::$app->config['siteurl'];  
	 	 $smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
     $content =  Mysite::$app->statichtml($tpllist['emailfindtpl'],$emaildata);  
     $info = $smtp->send($memberinfo['email'], Mysite::$app->config['emailname'],$title,$content , "" , "HTML" , "" , "");  
     return true;
	}
	//邮箱找回密码
	function resetpwd($username,$sign,$uid,$newpwd,$newpwd2){
	 if(empty($uid)){
		 $this->error='member_noexit';
	 return false;
	 } 
     $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");
     if(empty($memberinfo)){
    	  $this->error='member_noexit';
	      return false;
     }   
     $checksign = $this->mymd5($memberinfo['password'],$memberinfo['username']);
     if($checksign != $sign){
		$this->error='member_emailyan';
		return false;
     }   
      if(!empty($newpwd)){
		if($newpwd == $newpwd2){
			$arr['password'] = md5($newpwd);
			$this->mysql->update(Mysite::$app->config['tablepre'].'member',$arr,"uid='".$memberinfo['uid']."'");
			$this->error ='ok';
			return true;
		}else{
		    $this->error='member_twopwdnoequale'; 
		} 
      }
		return true;	
	}
	/*
	*param 说明:
	  userid 用户ID   type 1表示积分2表示资金
	  addtype 1表示增加2表示减少
	  result 表示 结果
	*/
	function addlog($userid,$inputype=1,$addtype=1,$result=0,$logtitle='',$logcontent='',$allcost=0){
		if(empty($userid)){
		  $this->error('member_noexit');
		  return false;
		}
		$data['userid'] =  $userid;
		$data['type'] = $inputype;
		$data['addtype'] = $addtype;
		$data['result'] = $result;
		$data['addtime'] = time();
		$data['title'] = $logtitle;
		$data['content'] = $logcontent;  
		$data['acount'] = $allcost;
		$this->mysql->insert(Mysite::$app->config['tablepre'].'memberlog',$data);	
		return true;
	}
	
	
	/*
	*param 说明:
	 id	uid 用户uid	username 用户名	cost 剩余金额	bdtype 变动类型 1为增加 2为减少	bdcost 本次充值/扣除金额	
	 curcost 当前金额	bdliyou 变动原因	czuid 操作用户uid	czusername 操作用户名称	addtime 操作日期
	*/
	function addmemcostlog($uid,$username,$cost,$bdtype,$bdcost,$curcost,$bdliyou,$czuid,$czusername){
		if(empty($uid)){
		  $this->error('member_noexit');
		  return false;
		}
		$data['uid'] =  $uid;
		$data['username'] =  $username;
		$data['cost'] =  $cost;
		$data['bdtype'] =  $bdtype;
		$data['bdcost'] =  $bdcost;
		$data['curcost'] =  $curcost;
		$data['bdliyou'] =  $bdliyou;
		$data['czuid'] =  $czuid;
		$data['czusername'] =  $czusername;
		$data['addtime'] =  time();	 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'memcostlog',$data);  
		return true;
 	}
	 function mymd5($string1,$string2){	
	  $kk = md5($string1);
	  $kk = md5($kk.$string2);
	  return  substr($kk,6,15); 
     }
	  public function ero(){
		return $this->error;
	  }	  
  /* 新增 代理商 */
  /* 代理商登录信息 */
	function findareapwd($uname){
	  if(!(IValidate::email($uname))){
	  	$this->error='erremail';
	    return false;
	  } 
     $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where email='".$uname."' ");
     if(empty($memberinfo))
     {
     	 $this->error='member_noexit';
	     return false;
     } 
     $title = '找回'.Mysite::$app->config['sitename'].'帐号密码'; 
     $sign = $this->mymd5($memberinfo['password'],$memberinfo['username']);
     $emaildata['findpwdurl'] = IUrl::creatUrl("member/resetpwd/uid/".$memberinfo['uid']."/username/".urlencode($memberinfo['username'])."/sign/".$sign);
     
     $default_tpl = new config('tplset.php',hopedir);  
	 	 $tpllist = $default_tpl->getInfo(); 
	 	 if(!isset($tpllist['emailfindtpl']) || empty($tpllist['emailfindtpl'])) {
	 	 	$this->error='管理员未设置邮箱发送信息,请联系客服';
	    return false;
	 	 } 
	 	 $emaildata['username'] = $memberinfo['username'];
	 	 $emaildata['email'] = $memberinfo['email']; 
	 	 $emaildata['sitename'] = Mysite::$app->config['sitename'];
	 	 $emaildata['siteurl'] = Mysite::$app->config['siteurl'];  
	 	 $smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
     $content =  Mysite::$app->statichtml($tpllist['emailfindtpl'],$emaildata); 
    
     $info = $smtp->send($memberinfo['email'], Mysite::$app->config['emailname'],$title,$content , "" , "HTML" , "" , "");  
     return true;
	}
	function getareaadmininfo(){
		  $agentadminname =  ICookie::get('agentadminname');  
	      $agentadminpwd  =  ICookie::get('agentadminpwd');  
	      $agentadminuid  =  ICookie::get('agentadminuid');  
		  	 
	    $userinfo = array(); 
	    if(!empty($agentadminuid)){
	 	  	  $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where uid='".$agentadminuid."' and password  = '".md5($agentadminpwd)."'"); 
	 	  	   
			  $userinfo['group'] = $userinfo['groupid'];
	 	  } 
	 	  if(empty($userinfo)||!isset($userinfo['uid'])){
	 	      $userinfo = array('uid'=>0,'username'=>'guest','group'=>'2');
	    }
	    return $userinfo;
	} 

	function updatememjuaninfo($phoneyan){
		
					
		// 根据前台注册的手机号检测此手机号数据库中是否领取过优惠券，如果有则更新UID和username status=1   
		// 如果前台新注册的用户 存在分享者 shareuid > 0 则考虑返增推广分享者优惠券
		$checkphonejuan =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where bangphone='".$phoneyan."' and uid=0 and status = 0  "); 
		$memberinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phoneyan."'    "); 
		if( !empty($checkphonejuan) ){
			$tdata['uid'] = $memberinfo['uid'];
			$tdata['username'] = $tname;
			$tdata['status'] = 1;
			$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$tdata,"bangphone='".$phoneyan."' and uid=0 and status = 0 ");	
		}
		$checksharejuan =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where bangphone='".$phoneyan."' and uid='".$memberinfo['uid']."' and shareuid > 0  "); 
		if( !empty($checksharejuan) ){
			foreach( $checksharejuan as $key=>$jval ){
				$sharemember =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$jval['shareuid']."'   "); 
				if( !empty($sharemember) ){
					$srdata['name'] = $jval['name'];
					$srdata['limitcost'] = $jval['limitcost'];
					$srdata['cost'] = $jval['cost'];
					$srdata['uid'] = $sharemember['uid'];
					$srdata['username'] = $sharemember['username'];
					$srdata['status'] = 1;
					$srdata['bangphone'] = $sharemember['phone'];
					$srdata['type'] = 4;  //返增优惠券类型
					$srdata['endtime'] = $jval['endtime'];   
					$srdata['creattime'] = time();   
					$srdata['paytype'] = $jval['paytype'];   
					$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$srdata);
				}
			}
		}
			
		
	}
	
  
  
}
?>