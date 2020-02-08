<?php
/*
*   method 方法  包含所有会员相关操作
    管理员/会员  添加/删除/编辑/用户登录
    用户日志其他相关连的通过  memberclass关联
*/
class method   extends adminbaseclass
{
	 function index(){
	 	       $link = IUrl::creatUrl('member/memberlist');
           $this->refunction('',$link);  
	 }
	 
	 function memberlist(){

	 	 $data['username'] =  trim(IReq::get('username'));
	     $data['email'] =  trim(IReq::get('email'));
	 	 $data['groupid'] = 4;
	 	 $data['phone'] =  trim(IReq::get('phone'));
	 	 //构造查询条件
	 	 $where = '';
	 	 $where =  $this->sqllink($where,'username',$data['username'],'=');
	 	 $where =  $this->sqllink($where,'email',$data['email'],'=');
	 	 $where =  $this->sqllink($where,'group',$data['groupid'],'>');
	 	 $where =  $this->sqllink($where,'phone',$data['phone'],'=');
		 //$where =  $this->sqllink($where,'uid',13429,'=');
	 	 $data['where'] = $where;
         
       
	 	 Mysite::$app->setdata($data);
	 }



	 function memberlistshop(){
	   
	 	 $data['username'] =  trim(IReq::get('username'));
	   $data['email'] =  trim(IReq::get('email'));
	 	 $data['groupid'] = 3;
	 	 $data['phone'] =  trim(IReq::get('phone'));
	 	 //构造查询条件
	 	 $where = '';
	 	 $where =  $this->sqllink($where,'username',$data['username'],'=');
	 	 $where =  $this->sqllink($where,'email',$data['email'],'=');
	 	 $where =  $this->sqllink($where,'group',$data['groupid'],'=');
	 	 $where =  $this->sqllink($where,'phone',$data['phone'],'=');
	 	 $data['where'] = $where;
	 	 
	 	 Mysite::$app->setdata($data);  
	 }
	  
	 function adminlist(){
	 	 $grouplist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."group where type ='admin'  ");  
	 	 $temp = array();
	 	 foreach($grouplist as $key=>$value){
	 	 	  $temp[$value['id']] = $value['name'];
			  $ids[] = $value['id'];
	 	 }
	 	 $data['grouplist'] = $temp;
		 $data['ids'] = implode(',',$ids);
		  
	 	  Mysite::$app->setdata($data);  
	 }
	  
	 //后台添加管理员
	 function saveadmin(){
	 	  // limitalert();
	 	    $uid = IReq::get('uid'); 
		    $username = IReq::get('username');
		    $password = IReq::get('password');
		    $groupid = IReq::get('groupid'); 
		   if(empty($uid))
		   {
		   	  if(empty($username)) $this->message('member_emptyname'); 
		   	  if(empty($username)) $this->message('member_emptypwd'); 
		   	  $testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where username='".$username."' ");  
		   	  if(!empty($testinfo)) $this->message('member_repeatname'); 
		   	 	$arr['username'] = $username; 
	     	  $arr['password'] = md5($password);  
	     	  $arr['time'] = time();   
	     	  $arr['groupid'] = $groupid;   
	     	  $this->mysql->insert(Mysite::$app->config['tablepre'].'admin',$arr);  
		   }else{
		   	  $testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where username='".$username."' ");  
		   	  if(empty($testinfo)) $this->message('member_editfail'); 
		   	  if(!empty($password)){ 
		   	     $arr['password'] = md5($password); 
		   	  }
		   	  $arr['groupid'] = $groupid;   
		   	  $this->mysql->update(Mysite::$app->config['tablepre'].'admin',$arr,"uid='".$uid."'");	 
		   }
		   $this->success('success');
		  // $this->json(array('error'=>false));
	 }
	 //后台删除管理管理员
	 function deladmin(){
	 	 #limitalert();
		 #$tmsg = limitalert();
		#if(!empty($tmsg)) $this->message($tmsg);
		 
	    $uid = intval(IReq::get('id'));	 
		  if(empty($uid))  $this->message('member_emptyuid'); 
		  if($uid  == 1) $this->message('member_cantdel');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'admin',"uid = '$uid'"); 
		$this->mysql->delete(Mysite::$app->config['tablepre'].'applogin',"uid = '$uid'");		
	    $this->success('success');
	 } 
	 //后台保存会员
	 function savemember()
   {  
	  	  $uid = intval(IReq::get('uid'));
		  $data['username'] = IReq::get('username');
	 	  $data['password'] = IReq::get('password');
	  	  $data['phone'] = IReq::get('phone');
	  	  $data['address'] = IReq::get('address');
	   	  $data['email'] = IReq::get('email');
		  $data['group'] = IReq::get('group');
		 # print_r($data['group']);exit;
		  $data['score']=  IReq::get('score');
          $checkuserinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where  username='".$uname."' or phone='".$uname."'   ");
		  if($uid > 0){			  
			  $checkmem = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");
			  $checkmem1 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid !='".$uid."' and username = '".$data['username']."' ");
			  if(!empty($checkmem1))$this->message("用户名已存在");
			   if(!empty($data['phone'])){
			  $checkmem2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid !='".$uid."' and phone = '".$data['phone']."' and `group` = '".$data['group']."'");
			  if(!empty($checkmem2))$this->message("手机号已存在");
			   }
			  if( !empty($checkmem) ){
				  $cost = $checkmem['cost'];
				  $score = $checkmem['score'];
			  }else{
				  $this->message("获取用户信息失败");
			  }
		  }else{
			  $checkmem1 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username = '".$data['username']."' ");
			  if(!empty($checkmem1))$this->message("用户名已存在");
			  $checkmem2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone = '".$data['phone']."' and  `group` = '".$data['group']."' ");
			  if(!empty($checkmem2))$this->message("手机号已存在");
			  $cost = IReq::get('cost'); 
			  $score = IReq::get('score'); 
		  }
	  	
		$is_zengjian = IReq::get('is_zengjian'); // 增加/减少积分  0未选择  1增加  2减少
	    $jf_zengjian = IReq::get('jf_zengjian');
		if( $is_zengjian == 0  ){
			$data['cost'] = $cost;
		}
                if( $jf_zengjian == 0  ){
			$data['score'] = $score;
		}
		
		if( $is_zengjian == 1  ){
			$yuecost = IReq::get('yuecost'); 
			if($yuecost>10000)$this->message("单次余额增加不可超过10000");	
			$data['cost'] = $cost+$yuecost;
		}
		if( $is_zengjian == 2 ){
			$yuecost = IReq::get('yuecost'); 
			$data['cost'] = $cost-$yuecost;
			if($data['cost'] < 0){
			  $this->message("减少金额大于用户余额");	
			}
		} 
		
		 
		if( $jf_zengjian == 1  ){
			$yuejf = IReq::get('yuejf'); 
			if($yuecost>100000)$this->message("单次积分增加不可超过100000");	
			$data['score'] = $score+$yuejf;
		}
		if( $jf_zengjian == 2 ){
			$yuejf = IReq::get('yuejf'); 
			$data['score'] = $score-$yuejf;
			if($data['score'] < 0){
			  $this->message("减少积分大于用户积分");	
			}
		} 
		
		 
		 
		  if(empty($data['username'])) $this->message('member_emptyname');
		  if(empty($uid))
	    {
	    	  if($this->memberCls->regester($data['email'],$data['username'],$data['password'],$data['phone'],$data['group'],'',$data['address'],$data['cost'],$data['score'],$data['admin_id']))
	    	  {
					
		
	    	  	$this->success('success'); 
	    	  }else{
	    	  	$this->message($this->memberCls->ero());
	    	  }  
	    }else{
			
		  $default_cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		  $data['admin_id'] = $default_cityid;
		    
	      if($this->memberCls->modify($data,$uid))
	      {			$is_phonenotice = IReq::get('is_phonenotice');  //是否短信通知 1为通知  0不通知
					$notice_content = trim(IReq::get('notice_content')); 
					
					 
					 $bdliyou = $is_phonenotice==0?"管理员直接操作变动":$notice_content;
					 if($is_zengjian > 0){
						 $this->memberCls->addlog($uid,2,$is_zengjian,$yuecost,'管理员操作金额',$bdliyou,$data['cost']);
						$this->memberCls->addmemcostlog( $uid,$checkmem['username'],$checkmem['cost'],$is_zengjian,$yuecost,$data['cost'],$bdliyou,ICookie::get('adminuid'),ICookie::get('adminname') );
					 }
					 if($jf_zengjian > 0){
						$this->memberCls->addlog($uid,1,$jf_zengjian,$yuejf,'平台修改','',$data['score']);						 
					 }
					 
						if( $is_phonenotice == 1 ){
							
								 $this->fasongmsg($notice_content,$data['phone'])  ;
 								 $this->success('success'); 
								 
							
						}
					
	  
	      	 
	      }else{
	    	 	$this->message($this->memberCls->ero());
	      }
			  
	    }
	    $this->success('操作成功'); 
   }
	function fasongmsg($notice_content,$phone){ 
		$contents = $notice_content;    
		$phonecode = new phonecode($this->mysql,0,$phone); 
		if(strlen($contents) > 498){
			$content1 = substr($contents,0,498);
			$phonecode->sendother($content1);  
			$content2 = substr($contents,498,strlen($contents));
			$phonecode->sendother($content2);  
		}else{
			$phonecode->sendother($contents);  
		}   
	}
   //后台删除会员
   function delmember()
 	{ 
	
		 #limitalert();

		if(!empty($tmsg)) $this->message($tmsg);
	
		 $uid = intval(IReq::get('id'));	 
		 if(empty($uid))  $this->message('member_emptyuid'); 
	     
	   /*删除相关店铺**/ 
     /*用户直接相关部分*/
    $this->mysql->delete(Mysite::$app->config['tablepre'].'member',"uid = '$uid'"); 
    $this->mysql->delete(Mysite::$app->config['tablepre'].'oauth',"uid = '$uid'");
    $this->mysql->delete(Mysite::$app->config['tablepre'].'giftlog',"uid = '$uid'"); 
    $this->mysql->delete(Mysite::$app->config['tablepre'].'address',"userid = '$uid'"); 
    $this->mysql->delete(Mysite::$app->config['tablepre'].'comment',"uid = '$uid'");  
    $this->mysql->delete(Mysite::$app->config['tablepre'].'collect',"uid = '$uid'");  
    $this->mysql->delete(Mysite::$app->config['tablepre'].'card',"uid = '$uid'");  
    $this->mysql->delete(Mysite::$app->config['tablepre'].'ask',"uid = '$uid'");  
    $this->mysql->delete(Mysite::$app->config['tablepre'].'juan',"uid = '$uid'");  
    $this->mysql->delete(Mysite::$app->config['tablepre'].'memberlog',"userid = '$uid'");
	$this->mysql->delete(Mysite::$app->config['tablepre'].'applogin',"uid = '$uid'");
    $data['is_bang'] = 0;
    $this->mysql->update(Mysite::$app->config['tablepre'].'wxuser', $data,"uid = '$uid'");
    $testinfo = $this->mysql->select_one("select id from ".Mysite::$app->config['tablepre']."shop where uid='".$uid."' "); 
	  if(!empty($testinfo))
	  {
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shop',"id = '".$testinfo['id']."'");   
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopfast',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopattr',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopsearch',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'areatoadd',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'areashop',"shopid = '".$testinfo['id']."'");  
	     /*2017-06-27  start*/
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'shopjs',"shopid = '".$testinfo['id']."'");  
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'shoptx',"shopid = '".$testinfo['id']."'");  
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'shopreal',"shopid = '".$testinfo['id']."'");  
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'shopsearch',"shopid = '".$testinfo['id']."'");  
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'shopwait',"shopid = '".$testinfo['id']."'");  
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'ask',"shopid = '".$testinfo['id']."'");  
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'comment',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'drawbacklog',"shopid = '".$testinfo['id']."'");
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'excomment',"shopid = '".$testinfo['id']."'");
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'drawbacklog',"shopid = '".$testinfo['id']."'");
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"shopid = '".$testinfo['id']."'");
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'product',"shopid = '".$testinfo['id']."'");
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"shopid = '".$testinfo['id']."'");
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'shophui',"shopid = '".$testinfo['id']."'");
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'shophuiorder',"shopid = '".$testinfo['id']."'"); 
		 /*2017-06-27   end*/
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'goods',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'goodstype',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'order',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'orderdet',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'searkey'," type=1 and goid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'rule',"shopid = '".$testinfo['id']."'");  
   }  
	   $this->success('success'); 
	 }
	 //后台保存会员组
	 function savegroup(){
	 	  // limitalert();
	    	$id = intval(IReq::get('id'));	  
	 	    $data['name'] = IReq::get('name');
	 	    $type = IReq::get('type');
	 	    if(empty($data['name']))  $this->message('member_group_noexit'); 
	 	    $data['type'] = $type == 1?'admin':'font';
	 	    if(empty($id)){
	 	    	 $testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."group where name='".$data['name']."' ");  
		   	   if(!empty($testinfo)) $this->message('member_group_repeat'); 
		       $this->mysql->insert(Mysite::$app->config['tablepre'].'group',$data);  
	 	    }else{
	 	    	 $this->mysql->update(Mysite::$app->config['tablepre'].'group',$data,"id='".$id."'");	
	 	    }
	 	  $this->success('success'); 
	 }
	 //后台删除会员组
	 function delgroup(){
	 	  
		  /* limitalert();
		  	 
		 $tmsg = limitalert();
		if(!empty($tmsg)) $this->message($tmsg); */
		  
		  
	     $uid = intval(IReq::get('id'));	 
		  if(empty($uid))  $this->message('member_emptyuid'); 
		  if(in_array($uid,array(1,3,5))) $this->message('member_group_system');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'group',"id = '$uid'");   
	    $this->success('success'); 
	 } 
	 //后台管理员登出
	 function adminloginout(){
	 	 ICookie::clear('adminname'); 
	    ICookie::clear('adminpwd');  
	    ICookie::clear('adminuid');  
	    ICookie::clear('adminshopid'); 
      $link = IUrl::creatUrl('member/adminlogin');
      $this->refunction('',$link); 
	 } 
	function adminmodify(){
		 limitalert();
		 
	  $oldpwd = trim(IReq::get('oldpwd'));
		$pwd  = trim(IReq::get('pwd'));
		if(empty($oldpwd))
		{
			 $this->message('emptyoldpwd');
		}
		if(empty($pwd))
		{
			 $this->message('emptynewpwd');
		}
		if($this->admin['password'] != md5($oldpwd))
		{
			 $this->message('oldpwderr');
		}
		 $arr['password'] = md5($pwd);
		 $this->mysql->update(Mysite::$app->config['tablepre'].'admin',$arr,"uid='".$this->admin['uid']."'");	 
		 $this->success('success');
	} 
	         
  function membergrade(){
    	
    	$configs = new config('membergrade.php',hopedir);   
	    $data['membergrade'] = $configs->getInfo();
	    
      Mysite::$app->setdata($data); 
  }
  function savemembergrade(){
	  limitalert();
  	$gradename =   IFilter::act(IReq::get('gradename')); 
  	$from =   IFilter::act(IReq::get('from')); 
  	$to =   IFilter::act(IReq::get('to')); 
  	if(!is_array($gradename)) $this->message('member_grade_formaterr');
  	if(count($gradename) != count($from)){
  	    $this->message('member_grade_counterr');
  	}
  	if(count($gradename) != count($to)){
  		 $this->message('member_grade_jifenerr');
  	}
  	$newarray = array();
  	foreach($gradename as $key=>$value){
  	   $temp['gradename'] = $value;
  	   $temp['from'] = $from[$key];
  	   $temp['to'] = $to[$key];
  	   $newarray[] = $temp;
  	}
  	 
  	
  	$configData = var_export($newarray,true);
		$configStr = "<?php return {$configData}?>"; 
		$fileObj   = new IFile(hopedir.'/config/membergrade.php','w+'); 
		$fileObj->write($configStr);
   
	  $this->success('success');
  }
   function gradeinstro(){
   	 
  	 $configs = new config('membergrade.php',hopedir);   
	   $data['membergrade'] = $configs->getInfo();
	   
	   //总长度900px;
	   
	   $data['perlong'] = intval(900/(count($data['membergrade'])));
	    
	   
      Mysite::$app->setdata($data); 
     
	 }
	 
	 function setmemsafepwd(){
		 
		  $uid = intval($this->admin['uid']) ;
		   $data['id'] = $uid;	  
	  
		  $testinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."safepwd where adminuid='".$uid."' ");  
		  print_r($testinfo);
		   Mysite::$app->setdata($data); 
	 }
  // 保存用户验证密码
  function savememsafepwd(){
	  limitalert();
	  $uid = IFilter::act(IReq::get('uid')) ; 
     
	  $checksafeinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."safepwd where adminuid='".$uid."' ");  
	  if( !empty($checksafeinfo) ){
		  $this->message('管理员已设置过验证密码');
	  }

	  $pwd = IFilter::act(IReq::get('password')); 
	  if(empty($pwd))
		{
			 $this->message('验证密码为空！');
		}
	  $data['adminuid'] = $uid;
	  $data['password'] = md5($pwd);
	  $data['addtime']  = time();
	  $data['type']     = 0;
	  $this->mysql->insert(Mysite::$app->config['tablepre'].'safepwd',$data);  
	  $this->success('success');
  }
  function xiugaimemsafepwd(){
	  limitalert();
	 # print_r($this->admin['uid']);
	 # exit;
	  	$oldpwd  = trim(IReq::get('oldpwd'));
	  	$md5oldpwd  = md5(trim(IReq::get('oldpwd')));
	  	$newpwd  = trim(IReq::get('newpwd'));
		if(empty($oldpwd))
		{
			 $this->message('旧密码不能为空！');
		}
		$checksafeinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."safepwd where adminuid='".$this->admin['uid']."'   ");  
		if( empty($checksafeinfo ) ){
			 $this->message('用户不存在！');
		}
	
		if($checksafeinfo['password'] != md5($oldpwd))
		{
			 $this->message('旧密码不正确！');
		}
		if(empty($newpwd))
		{
			 $this->message('新密码不能为空！');
		}
		
		 $arr['password'] = md5($newpwd);
		 $this->mysql->update(Mysite::$app->config['tablepre'].'safepwd',$arr,"adminuid='".$this->admin['uid']."'");	 
		 $this->success('success');
  }
  function memcostloglist(){
      
	    	$querytype = IReq::get('querytype');
	    	$searchvalue = IReq::get('searchvalue');
	    	$starttime = IReq::get('starttime');
	    	$endtime = IReq::get('endtime');
	    	$nowday = date('Y-m-d',time());
	    	$starttime = empty($starttime)? $nowday:$starttime;
	    	$endtime = empty($endtime)? $nowday:$endtime;
	      $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59');
	    	$data['starttime'] = $starttime;
	    	$data['endtime'] = $endtime;
	    	$newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
	    	 $data['searchvalue'] ='';
	    	 $data['querytype'] ='';
	    	if(!empty($querytype))
	    	{
	    		if(!empty($searchvalue)){
	    			 $data['searchvalue'] = $searchvalue;
	       	   $where .= ' and '.$querytype.' LIKE \'%'.$searchvalue.'%\' ';
	       	   $newlink .= '/searchvalue/'.$searchvalue.'/querytype/'.$querytype;
	       	   $data['querytype'] = $querytype;
	    		}//IUrl::creatUrl('admin/asklist/commentlist
	    	}
	      
	    	$link = IUrl::creatUrl('/adminpage/member/module/memcostloglist'.$newlink);
	    	$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),10);
	    	 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
	    	 //
	    	 
	    	$memcostloglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."memcostlog ".$where."  order by addtime desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
	    	$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."memcostlog  ".$where." ");
	    	$pageshow->setnum($shuliang);
	    	$data['pagecontent'] = $pageshow->getpagebar($link);
			$data['memcostloglist'] = $memcostloglist;
			 
	     Mysite::$app->setdata($data);
	}
  
}



?>