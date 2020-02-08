<?php
class method   extends adminbaseclass
{
	   function index(){
	 	       $link = IUrl::creatUrl('/adminpage/news/module/newslist');
           $this->refunction('',$link);
	 }
	 function savenewstype(){
	 	$id = intval(IReq::get('uid'));
		$data['name'] = IReq::get('name');
		$data['orderid']  = intval(IReq::get('orderid'));
		$data['type'] = intval(IReq::get('type'));
		$data['parent_id'] = intval(IReq::get('parent_id'));
		$data['displaytype'] = intval(IReq::get('displaytype'));
		if(empty($data['name'])) $this->message('news_emptynewstypename');
		if(empty($id))
		{
			$this->mysql->insert(Mysite::$app->config['tablepre'].'newstype',$data);
		}else{
			$this->mysql->update(Mysite::$app->config['tablepre'].'newstype',$data,"id='".$id."'");
		}
		$this->success('success','');
   }
   function yiqisaylist(){
	

		$pageinfo = new page();
	 	$pageinfo->setpage(IReq::get('page'));
		$address=ICookie::get('addressname');
		#print_r(ICookie::get('addressname'));
		$data['list'] = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."wxback   order by id desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");

		$togethersaylist1 = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid  where is_top=0 order by addtime desc   limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
		$togethersaylist = array();
		foreach($togethersaylist1 as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");  // 评价总数
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");   //赞总数
			$value['beijubaoshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxuserjubao where commentid  = ".$value['id']."  ");   //赞总数
			$value['address']=$address;
			$togethersaylist[] = $value;
		}
		$data['togethersaylist'] = $togethersaylist;
		
			$shuliang  = $this->mysql->counts("select a.id from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid  where is_top=0 ");
		$pageinfo->setnum($shuliang);
			$data['pagecontent'] = $pageinfo->getpagebar();
		#print_r($data['togethersaylist']);
		Mysite::$app->setdata($data);
		
	}
        function showwxusercomm(){//设置是否展示
		
		$id = IFilter::act(IReq::get('id'));
		$checkinfo = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment where id = '".$id."' ");
		if( !empty($checkinfo) ){
			if( $checkinfo['is_show'] == 1 ){
				$data['is_show'] = 0;				
			}else{
				$data['is_show'] = 1;				
			}
			$this->mysql->update(Mysite::$app->config['tablepre'].'wxcomment',$data,"id='".$id."'");
				$this->success('success');
		}else{
			$this->message('未找到对应的说说');
		}

	}
	function delwxusersay(){
   	  $id = IFilter::act(IReq::get('id'));
		  if(empty($id))  $this->message('获取ID失败');
		  $ids = is_array($id)? join(',',$id):$id;
		 if(empty($ids))  $this->message('获取ID失败');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'wxcomment'," id in($ids)");   //删除留言
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'wxreplycomment'," parentid  in($ids)");// 删除留言所对应的 回复
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'wxpjzan'," commentid in($ids)");//删除留言所对应的 赞 记录
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'wxuserjubao'," commentid in($ids)");//删除留言所对应的 
	    $this->success('success','');
        }
        //批量上传图片
	public function piliang(){
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
		 $uploaddir = "pliang";
		$json = new Services_JSON();
		$uploadpath = 'upload/'.$uploaddir.'/';
		$filepath = '/upload/'.$uploaddir.'/';
         $upload = new upload($uploadpath,array('gif','jpg','jpge','doc','png'));//upload 自动生成压缩图片
         $file = $upload->getfile();
         if($upload->errno!=15&&$upload->errno!=0){
			 
			  $this->message($upload->errmsg());   
		   }else{
			   //写到商品库表中   
               $data['imagename']= $file[0]['saveName'];
			   $data['imageurl']= $filepath.$file[0]['saveName'];
			   $data['addtime'] = time();
			
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'imglist',$data); 
				$this->success($file[0]['saveName']); 
		   } 
		}else{
		 
			 $this->message('未定义的上传类型'); 
		}
	}
        function glywxmsg(){
		
		$togethersaylist1 = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."admin  as b  on a.uid = b.uid  where is_top=1 order by addtime desc ");
	#	print_r($togethersaylist);
		$togethersaylist = array();
		foreach($togethersaylist1 as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");  // 评价总数
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");   //赞总数
			$value['beijubaoshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxuserjubao where commentid  = ".$value['id']."  ");   //赞总数
			$togethersaylist[] = $value;
		}
		$data['togethersaylist'] = $togethersaylist;
		Mysite::$app->setdata($data);
	}
	function saveglywxmsg(){
		
			$uid =  ICookie::get('adminuid');  
	
		  $data['usercontent'] = IReq::get('glyfabucontent');	 	
		  if(empty($data['usercontent'])) $this->message('发表主题不能为空');
		  	if(!(IValidate::len($data['usercontent'],0,500)))  $this->message('发表主题不能大于500字');
			
			$checkinfo = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."admin where uid = '".$uid."' ");
			if(empty($checkinfo)){
				$this->message("获取管理员错误");
			}
			$data['userimg'] =IReq::get('userimg');
			$data['uid'] = $uid;
			$data['is_top'] = 1;
			$data['addtime'] = time();
			 $this->mysql->insert(Mysite::$app->config['tablepre'].'wxcomment',$data); 
 
	    $this->success('操作成功'); 
   }
   function chakansayimgs(){
		
		$sayid = IReq::get('sayid');
	   $commentone = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment  where id = '".$sayid."'  order by addtime desc ");
		$userimg = $commentone['userimg'];
		$userimages = explode('@',$userimg);
		$data['userimages'] = $userimages;
		Mysite::$app->setdata($data);
	}	
	function glyuploadmoreimg(){
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
			$uploaddir = empty($uploaddir)?'says':$uploaddir;
 			$default_cityid = Mysite::$app->config['default_cityid'];
			if( !empty($default_cityid) ){
				$uploadpath = 'images/'.$default_cityid.'/'.$uploaddir.'/'; 
			}else{
				$uploadpath = 'images/'.$uploaddir.'/'; 
			}
			
 			$upload = new upload($uploadpath);//upload 自动生成压缩图片 
			$filedir = $upload->getSigImgDir(); 
			$filedir = Mysite::$app->config['imgserver'].$filedir;
			
		 
		 
			if($upload->errno!=15&&$upload->errno!=0){
			 
				$this->message($upload->errmsg());   
			}else{
			  
				$this->success($filedir); 
			} 
		}else{
		 
			 $this->message('未定义的上传类型'); 
		}
	}
	
	
	
   function delnews(){
   	  $id = IFilter::act(IReq::get('id'));
		  if(empty($id))  $this->message('news_empty');
		  $ids = is_array($id)? join(',',$id):$id;
		 if(empty($ids))  $this->message('news_empty');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'news'," id in($ids)");
	    $this->success('success','');
  }
   function delnewstype(){
		 $id = IFilter::act(IReq::get('id'));
		  if(empty($id))  $this->message('news_emptytype');
		 $ids = is_array($id)? join(',',$id):$id;
		 if(empty($ids))  $this->message('news_emptytype');
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'newstype'," id in($ids)");
	   $this->success('success','');
   }
   function addnews(){
    $id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."news where id=".$id."  ");

    $data['typlist']= $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."newstype  order by orderid desc ");
    $temptypeid = array();
    if(!empty($id)){
		$tempid = $data['info']['typeid'];
     while ($tempid > 0) {
        $getstr =     $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."newstype where id=".$tempid."  ");
        if(!empty($getstr)){
        	$tempid = $getstr['parent_id'];
        	$temptypeid[] = $getstr['id'];
        }else{
        	$temptypeid[] = $tempid;
        	$tempid = 0;
        }
      }
		 $data['allids'] = $temptypeid;
		}else{
			$data['allids'] = array();
		}

		Mysite::$app->setdata($data);
   }
   function savenews(){
      $id = IReq::get('uid');
	   	$data['addtime'] = strtotime(IReq::get('addtime').date('H:i:s'));
	   	$data['title'] = IReq::get('title');
	   	$data['content'] = IReq::get('content');
	   	$data['orderid'] = IReq::get('orderid');
	   	$data['typeid'] = IReq::get('typeid');
	   	$data['seo_key'] = IFilter::act(IReq::get('seo_key'));
	   	$data['seo_content'] = IFilter::act(IReq::get('seo_content'));

	   	if(empty($id))
	   	{
	   		$link = IUrl::creatUrl('adminpage/news/module/addnews');
	   		if(empty($data['content'])) $this->message('news_emptycontent',$link);
	   		if(empty($data['title'])) $this->message('news_emptytitle',$link);
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'news',$data);
	   	}else{
	   		$link = IUrl::creatUrl('adminpage/news/module/addnews/id/'.$id);
	   		if(empty($data['content'])) $this->message('news_emptycontent',$link);
	   		if(empty($data['title'])) $this->message('news_emptytitle',$link);
	   		$this->mysql->update(Mysite::$app->config['tablepre'].'news',$data,"id='".$id."'");
	   	}
	   	$link = IUrl::creatUrl('adminpage/news/module/newslist');
	    $this->success('success',$link);
   }
   function addnewstype()
	{
	  $id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."newstype where id=".$id."  ");
		$mydatinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."newstype ");
		$data['typeoption'] = $this->huannewtype($mydatinfo,0,0,$data['info']['parent_id']);

	 	Mysite::$app->setdata($data);
	}

	function huannewtype($mydatinfo,$parent_id,$grade,$nowid=0){
		$htmlcontent = '';
		$tempshow = '';
		for($i = 0;$i< $grade;$i++)
		 {
		 	 $tempshow .="&nbsp&nbsp&nbsp&nbsp";
		 }
		foreach($mydatinfo as $key=>$value){
			if($value['parent_id'] == $parent_id){
			     if($value['type'] == 2)
			     {
			     	 $onoption = $nowid == $value['id']?'selected':'';
				     $htmlcontent .='<option value="'.$value['id'].'" '.$onoption.'>'.$tempshow.$value['name'].'</option>';
				     $htmlcontent .= $this->huannewtype($mydatinfo,$value['id'],$grade+1,$nowid);
				   }
			}
		}
		return $htmlcontent;
	}
	
	
	/* 2016.03.27 新增网站通知模块 */
	
	/* 网站通知 */
  function addnotice(){
		$id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."information where id=".$id."  and type=1 ");
 
		Mysite::$app->setdata($data);
   }
   function savenotice(){
      $id = IReq::get('uid');
	   	$data['addtime'] = strtotime(IReq::get('addtime').date('H:i:s'));
	   	$data['title'] = IReq::get('title');
	   	$data['content'] = IReq::get('content');
	   	$data['orderid'] = IReq::get('orderid');
	   	$data['img'] = IReq::get('img');
		$data['type'] = 1; // type 1为网站通知 2为生活服务
		$data['cityid'] = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
	   	if(empty($id))
	   	{
	   		$link = IUrl::creatUrl('adminpage/news/module/addnotice');
	   		if(empty($data['title'])) $this->message('通知标题不能为空！',$link);
	   		if(empty($data['content'])) $this->message('通知内容不能为空！',$link);
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'information',$data);
	   	}else{
	   		$link = IUrl::creatUrl('adminpage/news/module/addnotice/id/'.$id);
	   		if(empty($data['title'])) $this->message('通知标题不能为空！',$link);
	   		if(empty($data['content'])) $this->message('通知内容不能为空！',$link);
	   		$this->mysql->update(Mysite::$app->config['tablepre'].'information',$data,"id='".$id."'");
	   	}
	   	$link = IUrl::creatUrl('adminpage/news/module/information');
	    $this->success('success',$link);
   }
 function delnotice(){
   	  $id = IFilter::act(IReq::get('id'));
		  if(empty($id))  $this->message('获取数据失败');
		  $ids = is_array($id)? join(',',$id):$id;
		 if(empty($ids))  $this->message('未选中数据');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'information'," id in($ids)");
	    $this->success('success','');
  }
   public function noticeupload()
	 {
 
			$_FILES['imgFile'] = $_FILES['head'];
 			$json = new Services_JSON();
			$uploaddir = 'news';
			
			$default_cityid = Mysite::$app->config['default_cityid'];
			if( !empty($default_cityid) ){
				$uploadpath = 'images/'.$default_cityid.'/'.$uploaddir.'/'; 
			}else{
				$uploadpath = 'images/'.$uploaddir.'/'; 
			}
			 
		   $upload = new upload($uploadpath);//upload
		   $filedir = $upload->getSigImgDir();
		   $filedir = Mysite::$app->config['imgserver'].$filedir;
		   if($upload->errno!=15&&$upload->errno!=0) {
				  $this->message($upload->errmsg());
			  }else{ 
				  $this->success($filedir);
			  }
	 }
   
   	/* 生活服务 */
  function addlifeass(){
		$id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."information where id=".$id." and type = 2 ");
 
		Mysite::$app->setdata($data);
   }
   function savelifeass(){
      $id = IReq::get('uid');
	   	$data['addtime'] = strtotime(IReq::get('addtime').date('H:i:s'));
	   	$data['title'] = IReq::get('title');
	   	$data['content'] = IReq::get('content');
	   	$data['orderid'] = IReq::get('orderid');
	   	$data['img'] = IReq::get('img');
	   	$data['phone'] = IReq::get('phone');
	   	$data['describe'] = IReq::get('describe');
		$data['type'] = 2; // type 1为网站通知 2为生活服务
		$data['cityid'] = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		 
	   	if(empty($id))
	   	{
	   		$link = IUrl::creatUrl('adminpage/news/module/addlifeass');
	   		if(empty($data['title'])) $this->message('生活服务标题不能为空！',$link);
	   		if(empty($data['describe'])) $this->message('生活服务简介不能为空！',$link);
	   		
	   		if(empty($data['phone'])) $this->message('生活服务联系电话不能为空！',$link);
			 
			 if(empty($data['content'])) $this->message('生活服务内容不能为空！',$link);
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'information',$data);
	   	}else{
	   		$link = IUrl::creatUrl('adminpage/news/module/addlifeass/id/'.$id);
	   		if(empty($data['title'])) $this->message('生活服务标题不能为空！',$link);
	   		if(empty($data['describe'])) $this->message('生活服务简介不能为空！',$link); 
			if(empty($data['phone'])) $this->message('生活服务联系电话不能为空！',$link); 
			if(empty($data['content'])) $this->message('生活服务内容不能为空！',$link); 
	   		$this->mysql->update(Mysite::$app->config['tablepre'].'information',$data,"id='".$id."'");
	   	}
	   	$link = IUrl::creatUrl('adminpage/news/module/lifeassistant');
	    $this->success('success',$link);
   }
 function dellifeass(){
   	  $id = IFilter::act(IReq::get('id'));
		  if(empty($id))  $this->message('获取数据失败');
		  $ids = is_array($id)? join(',',$id):$id;
		 if(empty($ids))  $this->message('未选中数据');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'information'," id in($ids)");
	    $this->success('success','');
  }


    //8.4新增编辑帮助中心
    function addhelpcenter(){
        $id = intval(IReq::get('id'));
        $data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."helpcenter where id=".$id."  ");
        Mysite::$app->setdata($data);
    }

    //8.4新增删除帮助中心
    function delhelpcenter(){
        $id = IFilter::act(IReq::get('id'));
        if(empty($id))  $this->message('删除失败');
        $ids = is_array($id)? join(',',$id):$id;
        if(empty($ids))  $this->message('删除失败');
        $this->mysql->delete(Mysite::$app->config['tablepre'].'helpcenter'," id in($ids)");
        $this->success('success');
    }

    //8.4新增保存帮助中心
    function savehelpcenter(){
        $id = IReq::get('uid');
        $data['addtime'] = strtotime(IReq::get('addtime').' 00:00:00');
        $data['title'] = IReq::get('title');
        $data['content'] = IReq::get('content');
        $data['orderid'] = IReq::get('orderid');
        $data['seo_key'] = IFilter::act(IReq::get('seo_key'));
        $data['seo_content'] = IFilter::act(IReq::get('seo_content'));

        if(empty($id))
        {
            $link = IUrl::creatUrl('adminpage/news/module/addhelpcenter');
            if(empty($data['content'])) $this->message('内容不能为空',$link);
            if(empty($data['title'])) $this->message('标题不能为空',$link);
            $this->mysql->insert(Mysite::$app->config['tablepre'].'helpcenter',$data);
        }else{
            $link = IUrl::creatUrl('adminpage/news/module/addhelpcenter/id/'.$id);
            if(empty($data['content'])) $this->message('内容不能为空',$link);
            if(empty($data['title'])) $this->message('标题不能为空',$link);
            $this->mysql->update(Mysite::$app->config['tablepre'].'helpcenter',$data,"id='".$id."'");
        }
        $link = IUrl::creatUrl('adminpage/news/module/helpcenter');
        $this->success('success',$link);
    }
  
	 
	
}



