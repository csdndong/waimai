<?php
class method   extends areaadminbaseclass
{
	 
	 
	 function wxkefu(){
		$cityid = $this->admin['cityid'];
		$platpssetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
  		$data['station'] = $platpssetinfo;
		Mysite::$app->setdata($data); 
	}
	 function savewxkefu(){
		 
		 $cityid = $this->admin['cityid'];
	 	 $platpssetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
		 
	     $data['wxkefu_open'] =  intval(IFilter::act(IReq::get('wxkefu_open'))); 
	     $data['wxkefu_ewm'] =  trim(IFilter::act(IReq::get('wxkefu_ewm'))); 
	     $data['wxkefu_phone'] =  trim(IFilter::act(IReq::get('wxkefu_phone'))); 
		 if( !empty($platpssetinfo) ){
			 $this->mysql->update(Mysite::$app->config['tablepre'].'platpsset',$data,"cityid='".$cityid."'");	 
		 } else{
			 $data['cityid'] = $cityid;
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'platpsset',$data);  
		} 
	    $this->success('success'); 
		 
	 }

	 
	 
	 public function adminupload()
	 {
	 	 $func = IFilter::act(IReq::get('func'));
		 $obj = IReq::get('obj');
		$uploaddir =IFilter::act(IReq::get('uploaddir'));
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
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
		     echo "<script>parent.".$func."(true,'".$obj."','".json_encode($upload->errmsg())."');</script>";
		   }else{
		      echo "<script>parent.".$func."(false,'".$obj."','".$filedir."');</script>";

		   }
		   exit;
	   }
	   $data['obj'] = $obj;
	   $data['uploaddir'] = $uploaddir;
	   $data['func'] = $func;
	   Mysite::$app->setdata($data);
	 }

	public function uploadapp(){
		$func = IFilter::act(IReq::get('func'));
		$obj = IReq::get('obj');
		$uploaddir =IFilter::act(IReq::get('dir'));
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
			$uploaddir = empty($uploaddir)?'goods':$uploaddir;
			$json = new Services_JSON();
			$uploadpath = 'upload/'.$uploaddir.'/';
			$filepath = '/upload/'.$uploaddir.'/';
			$upload = new upload($uploadpath,array('gif','jpg','jpge','doc','png'));//upload 自动生成压缩图片
			$file = $upload->getfile();
			if($upload->errno!=15&&$upload->errno!=0){
				echo "<script>parent.".$func."(true,'".$obj."','".json_encode($upload->errmsg())."');</script>";
			}else{
				echo "<script>parent.".$func."(false,'".$obj."','".$filepath.$file[0]['saveName']."');</script>";

			}
			exit;
		}
		$data['obj'] = $obj;
		$data['uploaddir'] = $uploaddir;
		$data['func'] = $func;
		Mysite::$app->setdata($data);
	}

	public function saveupload()
	 {
		  $json = new Services_JSON();
      $uploadpath = 'upload/goods/';
		  $filepath = '/upload/goods/';
      $upload = new upload($uploadpath,array('gif','jpg','jpge','png'));//upload
      $file = $upload->getfile();
     if($upload->errno!=15&&$upload->errno!=0) {
			$msg = $json->encode(array('error' => 1, 'message' => $upload->errmsg()));

		  }else{
			$msg = $json->encode(array('error' => 0, 'url' => $filepath.$file[0][saveName], 'trueurl' => $upload->returninfo['name']));
		 }
		 echo $msg;
		 exit;
	 }
    function specialpage(){
        $data['name'] =  trim(IReq::get('name'));
        $where = '';
        if(!empty($data['name'])){
            $where .= " and name like '%".$data['name']."%'";
        }
        //构造查询条件
        $data['where'] = $where;
        $data['cityid']	= $this->admin['cityid'];
        Mysite::$app->setdata($data);
    }


    function addspecialpage(){
        $id =  intval(IReq::get('id'));
        $catparent = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where  type='checkbox' order by cattype asc limit 0,100");
        $catlist = array();
        foreach($catparent as $key=>$value){
            $tempcat   = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where parent_id = '".$value['id']."'  limit 0,100");

            foreach($tempcat as $k=>$v){
                $catlist[] = $v;
            }
        }
        if($id>0){
            $tempcat   = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage  where id = '".$id."'  ");
            $data['ztyinfo'] = $tempcat;
        }else{
			$data['ztyinfo'] = array('name'=>'','ruleintro'=>'','zttype'=>'','id'=>'','listids'=>'','showtype'=>'','cx_type'=>'','zdylink'=>'');	
		}
        $data['catarr'] = array('0'=>'外卖','1'=>'超市');
        $data['catlist'] = $catlist;
        Mysite::$app->setdata($data);
    }

    function getshoplist(){
        $id =  intval(IReq::get('shopclassid'));
        $search_shop =  IReq::get('search_shop');
        $cityid = $this->admin['cityid'];
        $where = 'where admin_id = '.$cityid;
        if(!empty($search_shop)){
            $where.=' and shopname like "%'.$search_shop.'%"';
        }
        if($id>1){
            $where .= "  and id in((select shopid from ".Mysite::$app->config['tablepre']."shopattr where attrid = ".$id." ))";
        }
        $shoplist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop  {$where} and is_pass=1 order by sort asc");
        if(!empty($shoplist)){
            $this->success($shoplist);
        }else{
            $this->message("该分类下暂无店铺");
        }
    }


    function getgoods(){
        $id =  intval(IReq::get('shopclassid'));
        $search_shop =  IReq::get('search_shop');
        $cityid = $this->admin['cityid'];
        $where = 'where admin_id = '.$cityid;
        if(!empty($search_shop)){
            $where.=' and shopname like "%'.$search_shop.'%"';
        }
        if($id>1){
            $where .= "  and id in((select shopid from ".Mysite::$app->config['tablepre']."shopattr where attrid = ".$id." ))";
        }
        $shoplist = $this->mysql->getarr("select id,shopname from ".Mysite::$app->config['tablepre']."shop  {$where} and is_pass=1 order by sort asc");
        if(!empty($shoplist)){
            foreach($shoplist as $v){
                $v['goods'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods  where shopid = {$v['id']}");
                $t[] = $v;
            }
        }
        $data['list'] = $t;
        #print_r($data);exit;
        $this->success($data);

    }





    function addspecialpagenext(){
        $data['name'] =  IReq::get('name');
        $data['opttype'] =  IReq::get('opttype');
        $data['params'] =  IReq::get('params');
        $data['shoptype'] =  IReq::get('shoptype');
        $data['cxtype'] =  IReq::get('cxtype');
        $data['oriid'] =  intval(IReq::get('oriid'));
        if($data['oriid']>0){
            $tempcat   = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage  where id = '".$data['oriid']."'  ");
            $data['ztyinfo'] = $tempcat;
        }else{
			$data['ztyinfo'] = array('name'=>'','ruleintro'=>'','zttype'=>'','id'=>'','listids'=>'','showtype'=>'','cx_type'=>'','zdylink'=>'');	
		}
        Mysite::$app->setdata($data);
    }


    function savezty(){  // 保存或者更新 专题页
        $id = intval(IReq::get('ztyid'));
        $data['name'] = IReq::get('name');
        $data['specialimg'] = IReq::get('img');
        $data['color'] = IReq::get('color');
        #$data['is_show'] = 0;
        
        $params = IReq::get('params');
        $shoptype = IReq::get('shoptype');
        $cxtype = intval(IReq::get('cxtype'));
        $type =  intval(IReq::get('opttype'));
        $data['zttype'] = $type;
        switch($type){
            case 1:
                $data['listids'] = $params;
                $data['is_custom'] = 0;
                $data['showtype'] =0;
                break;
            case 2:
                $data['listids'] = $params;
                $data['is_custom'] = 0;
                $data['showtype'] =1;
                break;
            case 3:
                if($shoptype==0){
                    $data['cx_type'] =6;
                }else{
                    $data['cx_type'] =7;
                }
                $data['listids'] =$params;
                $data['showtype'] =0;
                $data['is_custom'] = 1;
                break;
            case 4:
                $data['showtype'] =$params;
                $data['is_custom'] = 1;
                if($params==1){
                    $data['cx_type'] = 1;
                }else{
                    $data['cx_type'] = $cxtype;
                }
                break;
            case 5:
                $data['is_custom'] = 1;
                $data['showtype'] =0;
                $data['cx_type'] = $params;
                break;
            case 6:
                $data['showtype'] = 2;
                $data['is_custom'] = 0;
                $data['zdylink'] = $params;
                break;
        }

        $data['ruleintro'] = IReq::get('content');
        $data['cityid'] =$this->admin['cityid'];
        if(empty($id)){
            $linkurl = IUrl::creatUrl('areaadminpage/other/module/specialpage');
        }else{
            $linkurl =  IUrl::creatUrl('areaadminpage/other/module/addspecialpage/id/'.$id);
        }

        if(empty($data['name'])) $this->message('名称不能为空',$linkurl);
        if(empty($data['color'])) $this->message('专题页背景色调不能为空',$linkurl);
        if( $data['is_custom'] !=0 && $data['is_custom'] !=1) $this->message('请选择活动格式',$linkurl);
        if( $data['showtype'] !=0 && $data['showtype'] !=1 && $data['showtype'] !=2  ) $this->message('请选择活动针对对象',$linkurl);


        if( $data['showtype'] == 2 ) {
            if( empty($data['zdylink']) ) {
                $this->message('请填写跳转链接',$linkurl);
            }
        }

        if( $data['is_custom'] == 1 ){
            if( $data['showtype'] != 2 ) {
                if($data['cx_type'] <=0 && $data['cx_type'] >1) $this->message('请选择对象对应活动类型',$linkurl);
            }
        }
        if($data['is_custom'] == 0){
            $data['cx_type'] = 0;
        }

        if(empty($id))
        {
			$data['is_show'] = 0;
            $data['is_bd'] = 1;
            $link = IUrl::creatUrl('areaadminpage/other/module/specialpage');
            $this->mysql->insert(Mysite::$app->config['tablepre'].'specialpage',$data);
            $this->success('success',$link);
        }else{
            $link = IUrl::creatUrl('areaadminpage/other/module/addspecialpage/id/'.$id);
            $this->mysql->update(Mysite::$app->config['tablepre'].'specialpage',$data,"id='".$id."'");
            $this->success('success',$link);
        }
        //   	$link = IUrl::creatUrl('adminpage/other/module/specialpage');
        //   $this->success('success',$link);

    }




    function delspecialpage(){  //删除专题页
		#$this->message("您暂无权限删除，请联系管理员");
	
		$id = IReq::get('id');
		if(empty($id))  $this->message('未选中');
		$ids = is_array($id)? join(',',$id):$id;
		$this->mysql->delete(Mysite::$app->config['tablepre'].'specialpage'," id in($ids) ");
		$this->success('success');
	}
	function setstatus(){
	    $data['shoptype'] = array('0'=>'外卖','1'=>'超市');
	   Mysite::$app->setdata($data);
	}
	function selectztyobj(){	//专题页选择对象
	    $this->setstatus();
	    $where = '';
	    $goodswhere = '';
	     
	    
	    $data['shopname'] =  trim(IReq::get('shopname'));
	    $data['name'] =  trim(IReq::get('name'));
	   $data['username'] =  trim(IReq::get('username'));
	 	 $data['phone'] = trim(IReq::get('phone'));
	 	 if(!empty($data['shopname'])){
 		    $where .= " and shopname like '%".$data['shopname']."%'";
	 	 }
	 	 if(!empty($data['username'])){
	 	   $where .= " and uid in(select uid from ".Mysite::$app->config['tablepre']."member where username='".$data['username']."')";
	 	 }
	 	 if(!empty($data['phone'])){
	 	    $where .=" and phone='".$data['phone']."'";
	 	 }
	 	 
	 	 //构造查询条件
	 	 $data['where'] = $where; 
	    
		
		 if(!empty($data['shopname'])){
 		    $goodswhere .= " and shopname like '%".$data['shopname']."%'";
	 	 }
		 if(!empty($data['name'])){
	 	    $goodswhere .= " and name like '%".$data['name']."%'";
	 	 }
		
		
		$id = IReq::get('id');
		$data['id'] = $id;
		$ztyinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage where id=".$id."  ");
		$data['ztyinfo'] = $ztyinfo;
		
		$this->pageCls->setpage(intval(IReq::get('page')),60); 
	   
	  if($ztyinfo['showtype'] ==0){ 
 			$selectlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where."  order by sort asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
 			$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where." ");
	  }     
	   
	   if($ztyinfo['showtype'] == 1){
 			$selectlist = $this->mysql->getarr("select a.*,b.shopname from ".Mysite::$app->config['tablepre']."goods as a left join  ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.id > 0 and b.id > 0 ".$goodswhere."  order by a.good_order asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()." ");
			
			$shuliang  = $this->mysql->counts("select a.id from ".Mysite::$app->config['tablepre']."goods as a left join  ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.id > 0 ".$goodswhere."  ");
		 
	   }
	  #  print_r($selectlist);
	   $this->pageCls->setnum($shuliang); 
	  $data['pagecontent'] = $this->pageCls->getpagebar();
 		$data['selectlist'] = $selectlist;
 
	    Mysite::$app->setdata($data);
	    
	}
	function saveselectztyobj(){	//选择专题页对象 集
		$id = IReq::get('id');
		$zjtype = IReq::get('zjtype');
		
		
		$selectobjids = IReq::get('selectobjids');  //160,156, 
		$temparray  = explode(',',$selectobjids);
	/* 	$seobjids = implode(',',$temparray); // 160,156 */
		
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage where id=".$id."  ");
		if(!empty($checkinfo)){
			
			
			$yuanlaiids = explode( ',',$checkinfo['listids'] );
		    $tempids = array_diff($yuanlaiids,$temparray);
		 
			if($zjtype == 1){
				foreach($temparray as $key=>$value){
						$tempids[] = $value;
				}
			
			}
			 $templistids = array();
			foreach($tempids as  $key=>$value){
				if(!empty($value)){
					$templistids[] = $value;
				}
			}
			
			$data['listids'] = count($templistids >0)? join(',',$templistids):'';
			
			$this->mysql->update(Mysite::$app->config['tablepre'].'specialpage',$data,"id='".$id."'");
			$this->success('success');
		}
		
	}
	 
	
	
}
