<?php
class method   extends adminbaseclass
{

	 function  asklist(){

	    $this->asktype();
	   $searchvalue = IReq::get('searchvalue');
	   $querytype = intval(IReq::get('typeid'));
	   $data['typeid'] = '';
	   $data['username'] = '';
       $newlink ='';
       $where ='';
       
    //    	   if(!empty($querytype))
	   // {

	   	 if(!empty($searchvalue))
	   	 {
	   	   $data['username'] = $searchvalue;
	   	   $data['typeid'] = $querytype;
	   	   $where .= ' where b.username LIKE \'%'.$searchvalue.'%\' or com.typeid = '.$querytype.' ';
	   	    
	   	   $newlink = IUrl::creatUrl('adminpage/ask/module/asklist/username/'.$searchvalue.'/typeid/'.$querytype);
	   	   
	   	 }elseif(!empty($querytype))
	   	 {
	   	 
	   	   $data['typeid'] = $querytype;
	   	   $where .= ' where  com.typeid = '.$querytype.' ';
	   	    
	   	   $newlink = IUrl::creatUrl('adminpage/ask/module/asklist/typeid/'.$querytype);
	   	   
	   	 }
                 
	   // }
	  $pageinfo = new page();
      $pageinfo->setpage(IReq::get('page'));

      		$list = $this->mysql->getarr("select com.*,b.* from ".Mysite::$app->config['tablepre']."ask  as com left join ".Mysite::$app->config['tablepre']."member as b on com.uid = b.uid 
      			 ".$where." order by com.id desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");

      		foreach($list as $key =>$value)
      		{
      			
      			$data['list'][] = $value;
      			
      			
      		}
		
	#print_r($data['list']);
		$shuliang  = $this->mysql->counts("select com.id from ".Mysite::$app->config['tablepre']."ask as com left join ".Mysite::$app->config['tablepre']."member as b on b.uid = com.uid  ".$where);
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar($newlink);
		
	   Mysite::$app->setdata($data);
	 }
	 
	 
	 
	 function  shopmsglist(){
	    
	   $data['where'] = '';

	   Mysite::$app->setdata($data);
	 }
	 
	   function shenhaisj(){ 
	   $id = IReq::get('id');
		if(empty($id)) $this->message('empty_ping');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."messages where id='".$id."'  ");  
		if(empty($checkinfo)) $this->message('empty_ping');
		$data['is_pass'] = $checkinfo['is_pass'] == 1?0:1;
		$this->mysql->update(Mysite::$app->config['tablepre'].'messages',$data,"id='".$id."'");  
		$this->success('success');
	}
 function delsjmsg(){

     $id = IFilter::act(IReq::get('id'));
		 if(empty($id))  $this->message('empty_ask');
		 $ids = is_array($id)? join(',',$id):$id;
		
		 $where = " id in($ids)";
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'messages',$where);
	   $this->success('success');
   }
   
	 
	 
	 
	 function backask()
	 {
		  $id = intval(IReq::get('askbackid'));
	   	if(empty($id)) $this->message('empty_ask');
	   	$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."ask where id='".$id."'  ");
	   	if(empty($checkinfo)) $this->message('ask_empty');
		  if(!empty($checkinfo['replycontent']))  $this->message('ask_isreplay');
		  $where = " id='".$id."' ";
	   	$data['replycontent'] = IFilter::act(IReq::get('askback'));
	  	if(empty($data['replycontent'])) $this->message('ask_emptyreplay');
		  $data['replytime'] = time();
		  $this->mysql->update(Mysite::$app->config['tablepre'].'ask',$data,$where);
		  $this->success('success');
   }
   function delask(){

     $id = IFilter::act(IReq::get('id'));
		 if(empty($id))  $this->message('empty_ask');
		 $ids = is_array($id)? join(',',$id):$id;
		 $adminuid = ICookie::get('adminuid');
		 $where = " id in($ids)";
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'ask',$where);
	   $this->success('success');
   }
	 function asktype(){
	 	  $data['typelist'] =array('0'=>'店铺留言','1'=>'建议','2'=>'问题','3'=>'催单','4'=>'投诉申告','5'=>'其他');
                  #print_r($data);
	   	Mysite::$app->setdata($data);
	 }
	 function savepme(){
	 	$message = trim(IReq::get('message'));
		if(empty($message)) $this->message('ask_emptyperreplay');
		$data['usercontent'] = $message;
		$data['uid'] = 0;
		$data['backusername'] = '网站客服';
		$data['userimg'] = '';
		$data['creattime'] = time();
		$data['backtime'] = 0;
		$data['backuid'] = 0;
		$this->mysql->insert(Mysite::$app->config['tablepre'].'pmes',$data);  //写消息表
		$this->success('success');
	 }
	 function delpmes(){
	   	$id = IReq::get('id');
		  if(empty($id)) $this->json('ask_emptyper');
	  	$id = is_array($id)?$id:array($id);
	  	$tempids = join(',',$id);
	  	if(empty($tempids)) $this->json('数据合并出错');
	  	$this->mysql->delete(Mysite::$app->config['tablepre'].'pmes'," id in($tempids) ");
			$this->success('success');
	 }
	 function backpme(){
	 	$id = intval(IReq::get('id'));
		if(empty($id)) $this->message('ask_emptyper');
		$message = trim(IReq::get('message'));
		if(empty($message)) $this->message('ask_emptyperreplay');
		$data['backcontent'] 	= $message;
		$data['backuid'] = '';
		$data['backimg']	='';
		$data['backtime'] = time();
		$this->mysql->update(Mysite::$app->config['tablepre'].'pmes',$data,"id='".$id."'");
		$this->success('success');
   }
   /* 8.2 新增 */
   function complaintlist(){
        $searchvalue = IReq::get('shopname');
        $searchphone = IReq::get('phone');
        $typeidContent = trim(IReq::get('typeidContent'));
        $newlink= '';
        $where= '';
        if($typeidContent != null && $typeidContent != ''  && $typeidContent != '选择投诉类型' )
        {
            $data['typeidContent'] = $typeidContent;
            $newlink .= '/typeidContent/'.$typeidContent;
            $where .= " and  typeidContent= '".$typeidContent."' ";
        }
        if(!empty($searchvalue) )
        {
            $data['searchvalue'] = $searchvalue;
            $newlink .= '/shopname/'.$searchvalue;
            $where .= " and  shopname like '%".$searchvalue."%'";
        }
        if(!empty($searchphone))
        {
            $data['searchphone'] = $searchphone;
            $newlink .= '/phone/'.$searchphone;
            $where .= " and  phone like '%".$searchphone."%'";
        }
        $data['where'] = " id > 0 ".$where;
         $typelist = unserialize( Mysite::$app->config['refundreasonlist'] );
		$data['typelist'] = $typelist;
        $page=new page();//实例化分页类
        $page->setpage(intval(IReq::get('page')),10);//赋初始值（偏移值、每页个数）
        $data['list']=$this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopreport where   ".$data['where']."   order by id desc    limit ".$page->startnum().", ".$page->getsize());
        $pageCount=$this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shopreport where   ".$data['where']." order by id desc");
        $page->setnum($pageCount);//总页数
        $pagelink = IUrl::creatUrl('adminpage/ask/module/complaintlist'.$newlink);
        $data['pagecontent']=$page->getpagebar($pagelink);//显示分页
        Mysite::$app->setdata($data);
    }
	function saveREreason(){
		$this->checkadminlogin();
	 		$arrtypename = IReq::get('typename');
			$arrtypename = is_array($arrtypename) ? $arrtypename:array($arrtypename);
		  $siteinfo['refundreasonlist'] =   serialize($arrtypename);
		  $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);
	    $this->success('success');
	} 
	
	function delshopreport(){

        $id = IFilter::act(IReq::get('id'));
        if(empty($id))  $this->message('投诉不存在');
        $ids = is_array($id)? join(',',$id):$id;
        $where = " id in($ids)";
        $this->mysql->delete(Mysite::$app->config['tablepre'].'shopreport',$where);
        $this->success('success');
    }
	 
   
}



?>