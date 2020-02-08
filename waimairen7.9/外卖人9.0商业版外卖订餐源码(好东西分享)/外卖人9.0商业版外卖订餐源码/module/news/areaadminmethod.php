<?php
class method   extends areaadminbaseclass
{
	   
	
	/* 2016.03.27 新增网站通知模块 */
	
	/* 网站通知 */
  function addnotice(){
 		$id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."information where id=".$id."  and type=1 ");
 
		Mysite::$app->setdata($data);
   }
   function savenotice(){
      $id = IReq::get('uid');
	   	$data['addtime'] = strtotime(IReq::get('addtime').' 00:00:00');
	   	$data['title'] = IReq::get('title');
	   	$data['content'] = IReq::get('content');
	   	$data['orderid'] = IReq::get('orderid');
	   	$data['img'] = IReq::get('img');
		$data['type'] = 1; // type 1为网站通知 2为生活服务
		$data['cityid']	= $this->admin['cityid'];
	   	if(empty($id))
	   	{
	   		$link = IUrl::creatUrl('areaadminpage/news/module/addnotice');
	   		if(empty($data['title'])) $this->message('通知标题不能为空！',$link);
	   		if(empty($data['content'])) $this->message('通知内容不能为空！',$link);
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'information',$data);
	   	}else{
	   		$link = IUrl::creatUrl('areaadminpage/news/module/addnotice/id/'.$id);
	   		if(empty($data['title'])) $this->message('通知标题不能为空！',$link);
	   		if(empty($data['content'])) $this->message('通知内容不能为空！',$link);
	   		$this->mysql->update(Mysite::$app->config['tablepre'].'information',$data,"id='".$id."'");
	   	}
	   	$link = IUrl::creatUrl('areaadminpage/news/module/information');
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
	$uploaddir =IFilter::act(IReq::get('uploaddir'));
	 $_FILES['imgFile'] = $_FILES['head'];
        $inputname = trim(IReq::get('inputname'));
        $inputname = empty($inputname)?'imgFile':$inputname;	
        if(is_array($_FILES)&& isset($_FILES[$inputname]))
        {
            $uploaddir = empty($uploaddir)?'other':$uploaddir;
 			$areaadmin_cityid = $this->admin['cityid'];
			if( !empty($areaadmin_cityid) ){
				$uploadpath = 'images/'.$areaadmin_cityid.'/'.$uploaddir.'/'; 
			}else{
				$uploadpath = 'images/'.$uploaddir.'/'; 
			}
		 
			$upload = new upload($uploadpath);
			$filedir = $upload->getSigImgDir(); 
			$filedir = Mysite::$app->config['imgserver'].$filedir;
			
            if($upload->errno!=15&&$upload->errno!=0){
                $this->message($upload->errmsg());
            }else{
                $this->success($filedir);

            }
        }else{
			$this->message('参数错误');
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
	   	$data['addtime'] = strtotime(IReq::get('addtime').' 00:00:00');
	   	$data['title'] = IReq::get('title');
	   	$data['content'] = IReq::get('content');
	   	$data['orderid'] = IReq::get('orderid');
	   	$data['img'] = IReq::get('img');
	   	$data['phone'] = IReq::get('phone');
	   	$data['describe'] = IReq::get('describe');
		$data['type'] = 2; // type 1为网站通知 2为生活服务
		$data['cityid']	= $this->admin['cityid'];
		 
	   	if(empty($id))
	   	{
	   		$link = IUrl::creatUrl('areaadminpage/news/module/addlifeass');
	   		if(empty($data['title'])) $this->message('生活服务标题不能为空！',$link);
	   		if(empty($data['describe'])) $this->message('生活服务简介不能为空！',$link);
	   		
	   		if(empty($data['phone'])) $this->message('生活服务联系电话不能为空！',$link);
			 
			 if(empty($data['content'])) $this->message('生活服务内容不能为空！',$link);
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'information',$data);
	   	}else{
	   		$link = IUrl::creatUrl('areaadminpage/news/module/addlifeass/id/'.$id);
	   		if(empty($data['title'])) $this->message('生活服务标题不能为空！',$link);
	   		if(empty($data['describe'])) $this->message('生活服务简介不能为空！',$link); 
			if(empty($data['phone'])) $this->message('生活服务联系电话不能为空！',$link); 
			if(empty($data['content'])) $this->message('生活服务内容不能为空！',$link); 
	   		$this->mysql->update(Mysite::$app->config['tablepre'].'information',$data,"id='".$id."'");
	   	}
	   	$link = IUrl::creatUrl('areaadminpage/news/module/lifeassistant');
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

 
	 
	
}



