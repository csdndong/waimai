<?php
class method   extends baseclass
{
	 function backask()
	 {
		  $id = intval(IReq::get('askbackid'));
	   	if(empty($id)) $this->message('ask_empty');
	   	$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."ask where id='".$id."'  ");
	   	if(empty($checkinfo)) $this->message('ask_empty');
		  if(!empty($checkinfo['replycontent']))  $this->message('ask_isreplay');
		  $where = " id='".$id."' ";
	    	 $shopid = ICookie::get('adminshopid');
	    	 if(empty($shopid)) $this->message('ask_notownreplay');
	    	 if($checkinfo['shopid'] != $shopid) $this->message('ask_notownreplay');
	   	$data['replycontent'] = IFilter::act(IReq::get('askback'));
	  	if(empty($data['replycontent'])) $this->message('ask_emptyreplay');
		  $data['replytime'] = time();
		  $this->mysql->update(Mysite::$app->config['tablepre'].'ask',$data,$where);
		  $this->success('success');
   }
   function delask(){
     $id = IFilter::act(IReq::get('id'));
		 if(empty($id))  $this->message('ask_empty');
		 $ids = is_array($id)? join(',',$id):$id;
		 $where = " id in($ids)";
	   	   $this->checkshoplogin();
	    	 $shopid = ICookie::get('adminshopid');
	    	if(!empty($shopid)){
	    		 $where = $where." and shopid = ".$shopid;
	    	}

	   $this->mysql->delete(Mysite::$app->config['tablepre'].'ask',$where);
	   $this->success('success');
   }
   function delmyask(){
   	  $this->checkmemberlogin();
   	  $id = intval(IFilter::act(IReq::get('id')));
   	   if(empty($id))  $this->message('ask_empty');
   	   $this->mysql->delete(Mysite::$app->config['tablepre'].'ask'," id= '".$id."' and uid = '".$this->member['uid']."'  ");
   	    $this->success('success');
   }
   function saveask()
	 {
	 	/*
	 	 if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
	 	    
	 	 }else{
	 	 	 // $this->message('ask_postbyweb');
	   	}  */
	  	$shopid = intval(IReq::get('shopid'));
	  	$data['content'] = trim(IFilter::act(IReq::get('content')));
	  	$type = intval(IReq::get('type'));//留言类型 shop
	    if(empty($data['content'])) $this->message('ask_emptycontent');
	    if(strlen($data['content']) > 200) $this->message('ask_contentlength');
	    $data['shopid'] = empty($shopid)?'0':$shopid;
	    $data['uid'] = $this->member['uid'];
	    $data['typeid'] = empty($type)?3:$type;
	    $data['addtime'] = time();
	    $this->mysql->insert(Mysite::$app->config['tablepre'].'ask',$data);
		  $this->success('success');
   }
   function myask(){
   	$this->checkmemberlogin();
     $this->asktype();
   }
	 function asktype(){
	 	  $data['typelist'] =array('0'=>'店铺留言','1'=>'建议','2'=>'问题','3'=>'催单','4'=>'投诉申告','5'=>'其他');
	   	Mysite::$app->setdata($data);
	 }
	 function deluserpms(){
	 	$this->checkmemberlogin();
	 	  $id = IReq::get('id');
	 	  $uid = $this->member['uid'];
	 	  if(empty($uid)) $this->message('member_nolimit');
	 	  $this->mysql->delete(Mysite::$app->config['tablepre'].'pmes'," id ='".$id."' and uid = '".$uid."' ");
	 	  $this->success('success');
	 }
	 function saveuserpmes(){
	 	$this->checkmemberlogin();
	   $uid = $this->member['uid'];
	   if(empty($uid)) $this->message('member_nolimit');
	 	 $data['usercontent'] = trim(IFilter::act(IReq::get('message')));
	   $data['userimg'] = trim(IFilter::act(IReq::get('image')));
	   $data['uid'] = $this->member['uid'];
	   $data['username'] = $this->member['username'];
	   $data['creattime'] = time();
	   $data['backuid'] = 0;
	   $data['backcontent']='';
	   $data['backimg'] = '';
	   $data['backtime']=0;
	   $data['backusername'] = '网站客服';
	   if(empty($data['usercontent'])) $this->message('ask_emptypercontent');
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'pmes',$data);
	   $this->success('success');
	 }
   function newshopask(){
	 	   //$this->checkshoplogin();

	 	 $shopid = intval(IFilter::act(IReq::get('id')));
     $type = trim(IFilter::act(IReq::get('showtype')));
     $data['list'] = array();
     $data['pagecontent'] = '';
     if($type == 'shop'){
       	$this->pageCls->setpage(intval(IReq::get('page')),10);
                 $data['list'] = $this->mysql->getarr("select a.*,b.username,b.logo  from ".Mysite::$app->config['tablepre']."ask as a left join  ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid    where a.shopid=".$shopid."  order by id desc limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."");
             $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."ask   where shopid=".$shopid." ");
                  $this->pageCls->setnum($shuliang);
              $data['pagecontent'] = $this->pageCls->ajaxbar('getliuyan');
     }
     Mysite::$app->setdata($data);
  }
  	 
	  function pmessage(){
	  $this->checkmemberlogin();
	  }

}



?>