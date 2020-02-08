<?php
/*
*   method 方法  包含所有会员相关操作
    管理员/会员  添加/删除/编辑/用户登录
    用户日志其他相关连的通过  memberclass关联
*/
class method   extends areaadminbaseclass
{ 
	 function index(){  
	 	       $link = IUrl::creatUrl('member/memberlist');
           $this->refunction('',$link);  
	 } 
	 function memberlistshop(){
	   $this->checkadminlogin();
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
	 	 if(empty($data['username']) && empty($data['email']) && empty($data['phone'])){
	 	 	$where =  $this->sqllink($where,'admin_id',$this->admin['cityid'],'='); 
	 	 }
	  	 
 	 	 $data['where'] = $where; 
	 
	 	 Mysite::$app->setdata($data);  
	 }
	 function memberlistps(){
	   $this->checkadminlogin();
	 	 $data['username'] =  trim(IReq::get('username'));
	     $data['email'] =  trim(IReq::get('email'));
	 	 $data['groupid'] = 2;
	 	 $data['phone'] =  trim(IReq::get('phone'));
	 	 //构造查询条件
	 	 $where = '';
	 	 $where =  $this->sqllink($where,'username',$data['username'],'=');
	 	 $where =  $this->sqllink($where,'email',$data['email'],'=');
	 	 $where =  $this->sqllink($where,'group',$data['groupid'],'=');
	 	 $where =  $this->sqllink($where,'phone',$data['phone'],'=');
	 	 $where =  $this->sqllink($where,'admin_id',$this->admin['cityid'],'=');
	 	 $data['where'] = $where;
	  
	 	 Mysite::$app->setdata($data);  
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
	  	$data['score'] = IReq::get('score');
	  	$data['cost'] = IReq::get('cost');  
		  if(!IValidate::email($data['email'])) $this->message('erremail');
		  if(!IValidate::phone($data['phone'])) $this->message('errphone');
		  if(!in_array($data['group'],array('2','3'))) $this->message('member_onlysave');
		   
		  if(empty($data['username'])) $this->message('member_emptyname');
		  if(empty($uid))
	    {
	    	  if($this->memberCls->regester($data['email'],$data['username'],$data['password'],$data['phone'],$data['group'],'','',$data['cost'],$data['score']))
	    	  {
	    	  	//getuid
	    	  	$tempuid = $this->memberCls->getuid();
	    	  	$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>$this->admin['cityid']),"uid='".$tempuid."'");	 
	    	  	$this->success('success'); 
	    	  }else{
	    	  	$this->message($this->memberCls->ero());
	    	  }  
	    }else{
	    	 $testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");  
	    	 if($testinfo['admin_id'] != $this->admin['cityid']) $this->message('member_onlysave');
	      if($this->memberCls->modify($data,$uid))
	      {
	      	 $this->success('success'); 
	      }else{
	    	 	$this->message($this->memberCls->ero());
	      }
			  
	    }
	    $this->success('success'); 
   }
   //后台删除会员
   function delmember()
 	{ 
		 $uid = intval(IReq::get('id'));	 
		 if(empty($uid))  $this->message('member_noexit'); 
	      $testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");
	   if(empty($testinfo)) $this->message('member_noexit');
	   if($testinfo['admin_id'] != $this->admin['cityid']) $this->message('member_onlydel');
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
    $testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where uid='".$uid."' ");
	  if(!empty($testinfo))
	  {
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shop',"id = '".$testinfo['id']."'");   
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopfast',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopattr',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopsearch',"shopid = '".$testinfo['id']."'");    
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'areashop',"shopid = '".$testinfo['id']."'");  
	     
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'goods',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'goodstype',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'order',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'orderdet',"shopid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'searkey'," type=1 and goid = '".$testinfo['id']."'");  
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'rule',"shopid = '".$testinfo['id']."'");  
   }  
	   $this->success('success'); 
	 }
	  
	 
	function adminmodify(){
		$this->checkadminlogin();
	  $oldpwd = trim(IReq::get('oldpwd'));
		$pwd  = trim(IReq::get('pwd'));
		$tempuid = $this->admin['uid'];
		if(empty($tempuid))
		{
			 $this->message('未登录');
		}
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
		 $this->mysql->update(Mysite::$app->config['tablepre'].'admin',$arr,"uid='".$tempuid."'");	 
		 $this->success('success');
	} 
	         
  function membergrade(){
    	$this->checkadminlogin();
    	$configs = new config('membergrade.php',hopedir);   
	    $data['membergrade'] = $configs->getInfo();
	    
      Mysite::$app->setdata($data); 
  } 
  
  
    
 
  
}



?>