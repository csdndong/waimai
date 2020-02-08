<?php
class method   extends adminbaseclass
{
	 function savearea(){
	   limitalert();

	  $id = intval(IReq::get('uid'));
		$data['name'] = IReq::get('name');
		$data['orderid']  = intval(IReq::get('orderid'));
		$data['pin'] = IReq::get('pin');
		$data['parent_id'] = intval(IReq::get('parent_id'));
		$data['imgurl'] = IReq::get('imgurl');
		$data['is_com'] = intval(IReq::get('is_com'));
		if(empty($id))
		{
			$data['lng'] = 0;
		  $data['lat'] = 0;
			$link = IUrl::creatUrl('area/adminarealist');
			if(empty($data['name']))  $this->message('area_emptyname',$link);
			if(empty($data['pin']))	$this->message('area_emptyfirdstword',$link);
			//if($data['parent_id'] == 0 && empty($data['imgurl']))$this->message('area_emptyimg',$link);
      if($data['parent_id'] > 0){
        $checkarea = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id =".$data['parent_id']."");
        $data['admin_id'] = $checkarea['admin_id'];
      }
			if(!$this->mysql->insert(Mysite::$app->config['tablepre'].'area',$data)){
			   $this->message('system_err');
			} 
		}else{
			$link = IUrl::creatUrl('area/adminarealist/id/'.$id);
			if(empty($data['name']))  $this->message('area_emptyname',$link);
			if(empty($data['pin']))	$this->message('area_emptyfirdstword',$link);
			//if($data['parent_id'] == 0 && empty($data['imgurl']))$this->message('area_emptyimg',$link);
			if($data['parent_id'] > 0){
        $checkarea = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id =".$data['parent_id']."");
        $data['admin_id'] = $checkarea['admin_id'];
      }
			$this->mysql->update(Mysite::$app->config['tablepre'].'area',$data,"id='".$id."'");
		}
		$link = IUrl::creatUrl('area/adminarealist');
		$this->success('success',$link);
	 }
	 function adminareacost(){
	 	 $areaid = intval(IReq::get('areaid'));
		 $type = trim(IReq::get('type'));
		 $cost  = IFilter::act(IReq::get('cost'));
		 $cost = intval($cost*10)/10;
		 if($areaid < 1) $this->message('area_iderr');
		 if(!in_array($type,array('add','del','updat'))) $this->message('nodefined_func');
		 $areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id =".$areaid."");
		 if(empty($areainfo)) $this->message('area_empty');
		 $insertdata['cost'] = $cost;
	   $this->mysql->update(Mysite::$app->config['tablepre'].'areatoadd',$insertdata," shopid = 0 and areaid ='".$areaid."'");
	 	 $this->success('success');
	 }
	 function adminpsset(){
		$data['pestypearr'] = array(
	 	   '1'=>'网站统一配送费',
	 	   '2'=>'根据不同区域统一配送费',
	 	   '3'=>'不计算配送费',
	 	   '4'=>'百度地图根据距离测算配送费',
	 	   '5'=>'根据菜品数计算配送费'
		);
		$cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		if( empty($cityid) ){
			$this->message("请先设置网站自营默认城市",'/index.php?ctrl=adminpage&action=system&module=siteset');
		}
		$platpssetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
		$platpssetinfo['radiusvalue'] = unserialize( $platpssetinfo['radiusvalue'] );
		$data['psinfo'] = $platpssetinfo; 
		Mysite::$app->setdata($data);
	 }
	 function savepsset(){
		
		 $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
	 	 
	     $locationradius =  IFilter::act(IReq::get('locationradius'));
  	 	 
		 if($savearray['locationradius'] > 30){
			 $this->message('配送半径最大30公里');
		 }
	 	 $temparray = array();
	 	 for($i=0;$i<$locationradius;$i++){
	 	   $temparray[$i] = IFilter::act(IReq::get('radiusvalue'.$i));
	 	 }
 		 $savearray['locationradius']  = $locationradius; 
	 	 $savearray['radiusvalue'] = serialize($temparray); 
		 $savearray['psycostset']  = intval(IFilter::act(IReq::get('psycostset'))); 
		 $savearray['psycost']  = intval(IFilter::act(IReq::get('psycost'))); 
		 $savearray['psybili']  = trim(IFilter::act(IReq::get('psybili'))); 
		 	
		$platpssetinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
		 
		if( !empty($platpssetinfo) ){
			 $this->mysql->update(Mysite::$app->config['tablepre'].'platpsset',$savearray,"cityid='".$cityid."'");	 
		}else{
			 $savearray['cityid'] = $cityid;
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'platpsset',$savearray);  
		} 
	    $this->success('success'); 
	}
	 function adminarealist(){
	 	 $areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   order by orderid asc");
	  //	print_r($areainfo);
		//&nbsp;&nbsp;&nbsp;&nbsp;
		$parentids = array();
		foreach($areainfo as $key=>$value){
		  $parentids[] = $value['parent_id'];
		}
		//去重
		$parentids = array_unique($parentids);
		$data['parent_ids'] = $parentids;
	 	 $this->getgodigui($areainfo,0,0);
	 	 $data['arealist'] = $this->digui;

	  
	  $adminlist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."admin where groupid='4' ");  
	 	 $temparr = array();
	 	 foreach($adminlist as $key=>$value){
	 	    $temparr[$value['uid']] = $value['username'];
	 	 }
	 	 $data['adminlist'] = $temparr;
	   $data['adminall'] = $adminlist;
	 
	 	 Mysite::$app->setdata($data);
	 }
	 function setareaadmin(){
	 		$areaid =  intval(IReq::get('areaid'));
	 	  $admin_id =  intval(IReq::get('adminid')); 
	 	  if(empty($areaid)) $this->message('area_empty');
	 	  $checkarea =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id = '".$areaid."' ");  
	 	  if(empty($checkarea)) $this->message('area_empty');
	 	  if($checkarea['parent_id'] > 0) $this->message('area_isnotparent');
	 	  $this->getgodiguiupdata($areaid,$admin_id); 
	 	  $this->success('success'); 
   }
   function getgodiguiupdata($areaid,$admin_id){
   	   $this->mysql->update(Mysite::$app->config['tablepre'].'area',array('admin_id'=>$admin_id),"id='".$areaid."'");	 
   	   $arraylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id = '".$areaid."' ");  
	 	   if(count($arraylist) > 0){
	 	      foreach($arraylist as $key=>$value){ 
	 	              $this->getgodiguiupdata($value['id'],$admin_id); 
	 	      } 
	 	   }
	 }
	 function setps(){
		$shopid =  intval(IReq::get('shopid'));
	 	  if(empty($shopid))
	 	  {
	 	  	 echo '店铺不存在';
	 	  	 exit;
	 	   }
	 	  $shopinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
	 	  if(empty($shopinfo))
	 	  {
	 	     echo '店铺不存在';
	 	  	 exit;
	 	  }
	 	  $tempinfo = array();
	 	  if($shopinfo['shoptype'] == 0){ 
	 	     $tempinfo = 	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid=".$shopid."  ");
	 	  }else{
	 	  	$tempinfo = 	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid=".$shopid."  ");
	 	  }
         $data['shopid'] = $shopid;
	   $data['shopinfo'] = $shopinfo;
	   $data['shopdetinfo'] = $tempinfo;
	   
	   
	   //城市信息 
	    $areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where id > 0 and parent_id = 0  order by orderid asc"); 
	 	 $data['citylist'] = $areainfo; 
	 	 
	 	 
	 	 //配送费  
	 	 $data['shopvalues'] = array();
	 	 if(empty($tempinfo['pradiusvalue'])){
	 	 	 $data['shopvalues'] = unserialize($this->platpsinfo['radiusvalue']);
	 	 }else{
	 	 	$data['shopvalues'] =  unserialize($tempinfo['pradiusvalue']);
	 	 }
	 	 $data['dolenth'] = count($data['shopvalues']);
	 	 
	 	  
	   Mysite::$app->setdata($data);
	}
	function savesetps(){
		$shopid =  intval(IReq::get('shopid'));
		if(empty($shopid)){ 
			 echo "<script>parent.uploaderror('请先完善店铺的基本信息');</script>";
		 	 exit; 
		}
		$shopinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
		if(empty($shopinfo)){
			echo "<script>parent.uploaderror('请先完善店铺的基本信息');</script>";
		 	 exit; 
		}
		 $tempinfo = array();
	 	  if(empty($shopinfo['shoptype'])){ 
	 	     $tempinfo = 	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid=".$shopid."  ");
	 	  }else{
	 	  	$tempinfo = 	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid=".$shopid."  ");
	 	  }
	  if(empty($tempinfo)){
	  	echo "<script>parent.uploaderror('请先完善店铺的基本信息');</script>";
		 	 exit; 
	  }
	  $data['sendtype'] = intval(IReq::get('sendtype'));
	   
	  if($data['sendtype'] == 1){//更改为商家配送时  要检测改店铺是否有参与平台设置的免配送费活动
		   $checkinfo1 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$shopid.",shopid) and  parentid = 1  and controltype = 4 and ( limittype < 3 or ( limittype = 3 and endtime > ".time()." ) )  ");
	       if(!empty($checkinfo1)) {
			   echo "<script>parent.uploaderror('该店铺参与有平台设置的免配送费活动，更改配送方式前请先删除该店铺参与的平台设置的免配送费活动');</script>";
		 	 exit; 
		   }
	  }else{//更改为平台配送时  要检测改店铺是否有商家设置的免配送费活动
		   $checkinfo2 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."rule where  shopid = ".$shopid." and  parentid = 0  and controltype = 4 and ( limittype < 3 or ( limittype = 3 and endtime > ".time()." ) )  ");  
	       if(!empty($checkinfo2)) {
			   echo "<script>parent.uploaderror('该店铺参与有店铺设置的免配送费活动，更改配送方式前请先删除该店铺参与的店铺设置的免配送费活动');</script>";
		 	 exit; 
		   }
	  }
	  
	  $data['pscost'] =  intval(IReq::get('pscost'));
	  $data['pradius'] = IReq::get('pradius');
	  if($data['pradius'] > 30){
		  echo "<script>parent.uploaderror('配送半径最大30公里');</script>";
		 	 exit; 
	  }	 
	  if(empty($tempinfo['pradiusvalue'])){
	  $tempdo = array();
	  for($i=0;$i< $data['pradius'];$i++){
	    $tempdo[$i] = 0;
	  }
	  $data['pradiusvalue'] = serialize($tempdo); 
	  } 
	 if(empty($shopinfo['shoptype'])){ 
	 	   $this->mysql->update(Mysite::$app->config['tablepre'].'shopfast',$data,"shopid='".$shopid."'");
	 }else{
	 	  $this->mysql->update(Mysite::$app->config['tablepre'].'shopmarket',$data,"shopid='".$shopid."'");
	 }

		$a=trim(IReq::get('psbaccid'));
	if(!empty($a)){
		$shopdata['pradiusa'] = intval(IReq::get('pradius'));
		$shopdata['psblink'] = trim(IReq::get('psblink'));
		$shopdata['psbaccid'] = trim(IReq::get('psbaccid'));
		$shopdata['psbcode'] = trim(IReq::get('psbcode'));
		$shopdata['psbversion'] = trim(IReq::get('psbversion'));
		$shopdata['psbkey'] = trim(IReq::get('psbkey'));
		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$shopdata,"id='".$shopid."'");
	}

	 //更新店铺配送设置
	 /*更新店铺所在城市*/

	 $cityid = intval(IReq::get('admin_id'));
     $this->mysql->update(Mysite::$app->config['tablepre'].'shop',array('admin_id'=>$cityid,'pradiusa'=>$data['pradius']),"id='".$shopid."'");

     $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>$cityid),"uid='".$shopinfo['uid']."'");

	   echo "<script>parent.uploadsucess('');</script>";
		 exit;
	  
	}
	function shoparea(){
		$shopid = intval(IReq::get('shopid'));
		$where = "  id > 0 and parent_id = 0   ";
		$areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where ".$where."  order by id asc");
		$data['citylist'] = $areainfo;
		$data['shopid'] = $shopid;
		Mysite::$app->setdata($data);
	}
	function savesetshoparea(){
		limitalert();
		$shopid = intval(IReq::get('shopid'));
		$shopdata['admin_id'] = trim(IReq::get('admin_id'));
		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$shopdata,"id='".$shopid."'");
		//更新店铺配送设置
		/*更新店铺所在城市*/
		echo "<script>parent.uploadsucess('');</script>";
		exit;

	}
	 //递归转换保存到数据中
	 /*
	 arraylist 数据数据
	 $nowid  当前接点的parent_ID,
	 $nowkey  当前循环次数
	 */
	 function getgodigui($arraylist,$nowid,$nowkey){
	 	   if(count($arraylist) > 0){
	 	      foreach($arraylist as $key=>$value){
	 	         if($value['parent_id'] == $nowid){
	 	             $value['space'] = $nowkey;
	 	             $donextkey = $nowkey+1;
	 	             $donextid = $value['id'];
	 	             $this->digui[] = $value;
	 	              $this->getgodigui($arraylist,$donextid,$donextkey);
	 	         }
	 	      }

	 	   }
	 }
  function baidumap(){
    $id = intval(IReq::get('id'));
		 $areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where  id = '$id' order by id asc");
		 if(empty($areainfo)){
		   echo '获取信息失败';
		   exit;
		 }
		 $is_name = 1;
		 $data['arealng'] = 0;
		 $data['arealat'] = 0;
		 $data['nowcityname'] = '';
		 if(empty($areainfo['parent_id'])){
		    $data['nowcityname'] = $areainfo['name'];
		    $checklng = intval($areainfo['lng']);
		    if(!empty($checklng)){
		      $is_name = 2;
		      $data['arealng'] = $areainfo['lng'];
		      $data['arealat'] = $areainfo['lat'];
		    }

		 }else{
		 	   $data['nowcityname'] = $areainfo['name'];
		 	    $pinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where  id = '".$areainfo['parent_id']."' order by id asc");
		 	    if(empty($pinfo)){
		         echo '获取上级地址失败';
		         exit;
		      }
		     $checklng = intval($areainfo['lng']);
		     if(!empty($checklng)){
		        $is_name = 2;
		        $data['arealng'] = $areainfo['lng'];
		        $data['arealat'] = $areainfo['lat'];
		     }else{
		     	   $checklng2 = intval($pinfo['lng']);
		     	   if(!empty($checklng2)){
		     	     $is_name = 2;
		           $data['arealng'] = $pinfo['lng'];
		           $data['arealat'] = $pinfo['lat'];
		         }else{

		           echo '未设置上级地址百度坐标';
		           exit;
		         }
		     }



		 }
		 $data['arealng'] = $data['arealng'] == 0?Mysite::$app->config['baidulng']:$data['arealng'];
		 $data['arealat'] = $data['arealat'] == 0?Mysite::$app->config['baidulat']:$data['arealat'];
      
		 $data['is_name'] = $is_name;
		 $data['myareaid'] = $id;
		 $data['baidumapkey'] = Mysite::$app->config['baidumapkey'];
		 Mysite::$app->setdata($data);
  }
  function savemaplocation(){
	  limitalert();
  	 $id = intval(IReq::get('id'));
		if($id < 1){
		  $this->json('area_empty');
		}
		 $data['lng'] = IReq::get('lng');
		$data['lat'] = IReq::get('lat');
		if(empty($data['lng'])) $this->message('emptylng');
		if(empty($data['lat'])) $this->message('emptylat');
		$this->mysql->update(Mysite::$app->config['tablepre'].'area',$data,"id='".$id."'");
		
	  $areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where  id = '".$id."' order by id asc");
	  if(!empty($areainfo)){
	    $areasearch = new areasearch($this->mysql);    
      $areasearch->setdata($areainfo['name'],'1',$id,$areainfo['lat'],$areainfo['lng']);
      $areasearch->del();
      if($areasearch->save()){
    	
      }else{
       $checkinfo = $areasearch->error(); 
      } 
    }
	  $this->success('success');
  }
  function searchkey(){
  	$data['searchvalue'] = '';
  	$data['typearr'] = array('0'=>'未定义','1'=>'区域地址','2'=>'店铺','3'=>'用户搜索');
  	$where = "";
  	$searchvalue =  trim(IReq::get('searchvalue'));
  	$typeid = intval(IReq::get('typeid'));
  	$newlink = "";
  	$data['typeid'] = "";
  	if(!empty($searchvalue)){
  	  $data['searchvalue'] = $searchvalue;
  	  $where = "  where   datacontent like '%".$searchvalue."%' ";
  	  $newlink = "/searchvalue/".$searchvalue;
  	}
  	if(in_array($typeid,array(1,2,3))){
  	   $data['typeid'] = $typeid;
  	  $where = empty($where)?"  where  datatype='".$typeid."' ":$where."  and datatype='".$typeid."'";
  	  $newlink .= "/typeid/".$typeid;
  	}

  	
   
  	$link = IUrl::creatUrl('/adminpage/area/module/searchkey'.$newlink);
	    
	    	$this->pageCls->setpage(IReq::get('page'),15);
	    	
	    	 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
	    	 //
	    	$data['list'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."positionkey    ".$where." order by datatype,parent_id   limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."");
	    	$shuliang  = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."positionkey    ".$where."   ");
	    	$this->pageCls->setnum($shuliang);
	    	
	    	$data['pagecontent'] = $this->pageCls->getpagebar($link);
   
  	Mysite::$app->setdata($data);
  }
  function delsearch(){
	  limitalert();
    $datatype =  intval(IReq::get('dataintype'));
  	$parent_id = intval(IReq::get('parent_id'));
  	$areasearch = new areasearch($this->mysql);   
  	$areasearch->setdata('',$datatype,$parent_id);
  	$areasearch->del();
  	$this->success('success'); 
  }
  function clearsearch(){
	  limitalert();
  	 $this->mysql->delete(Mysite::$app->config['tablepre'].'positionkey','datatype > 0');
  	 $this->success('success');
  }
  
	function setshow(){
		limitalert();
	  $id = intval(IReq::get('id'));
	  $type = intval(IReq::get('type'));
	  if(empty($id))$this->message('area_empty');
	  $data['show'] = $type != 1?0:1;
	  $this->mysql->update(Mysite::$app->config['tablepre'].'area',$data,"id='".$id."'");
	  $this->success('success');
	}
	function delarea()
	{limitalert();
		 $uid = intval(IReq::get('id'));
		 if(empty($uid))  $this->message('area_empty');
		 $areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where  parent_id = '".$uid."' order by id asc");
		 if(!empty($areainfo)) $this->message('area_isextchild');

	   $this->mysql->delete(Mysite::$app->config['tablepre'].'area',"id = '$uid'"); 
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'areashop',"areaid = '$uid'"); 

	   $this->success('success');;
	}
	function addarealist(){
		 $areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   order by orderid asc");
	  //	print_r($areainfo);
		//&nbsp;&nbsp;&nbsp;&nbsp;
		$parentids = array();
		foreach($areainfo as $key=>$value){
		  $parentids[] = $value['parent_id'];
		}
		//去重
		$parentids = array_unique($parentids);
		$data['parent_ids'] = $parentids;
	 	 $this->getgodigui($areainfo,0,0);
	 	 $data['arealist'] = $this->digui;
	 	 Mysite::$app->setdata($data);
	}
	
	/**  
	  * @method 新增平台地图配送范围
	  * &date 2016-12-20
	  **/
	function platformpsrange(){
		$arealnglatstring = '';
		
		  $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
 		  if( empty($cityid) ){
			  $this->message("请先设置网站自营默认城市",'/index.php?ctrl=adminpage&action=system&module=siteset');
		  }
		   $platpssetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
		if( !empty($platpssetinfo) ){
			$arealnglatstring =$platpssetinfo['waimai_psrange'];
		}
		 
		$temparealnglatarr = array();
		if( !empty($arealnglatstring) ){
			$arealnglatarr = explode('#',$arealnglatstring);
			$temparealnglatarr = $arealnglatarr;
		}
		
		$data['arealnglatarr'] = $temparealnglatarr;
 	 	 Mysite::$app->setdata($data);
	}
	
	function sublnglatpsrange(){
		//limitalert();
		 $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0; 
	       if( empty($cityid) ){
			  $this->message("请先设置网站自营默认城市");
		  }
	 	 
		 $savearray['waimai_psrange']  = trim(IFilter::act(IReq::get('waimai_psrange'))); 
		#print_r($savearray);exit;	
		 $platpssetinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
		 
		 if( !empty($platpssetinfo) ){
			 $this->mysql->update(Mysite::$app->config['tablepre'].'platpsset',$savearray,"cityid='".$cityid."'");	 
		 }else{
			 $savearray['cityid'] = $cityid;
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'platpsset',$savearray);  
		 } 
	    $this->success('success');
		 
 	}
	//测试配送对接是否正常
	function testpsblink(){ 
		$psblink  =  trim(IReq::get('psblink'));//trim(FD('psblink')); 
		$bizid = trim(IReq::get('bizid')); //trim('bizid');
		$key =  trim(IReq::get('psbkey'));//trim('psbkey');
		// print_r($psblink);
		$code =  trim(IReq::get('psbcode'));//trim('psbcode'); 
		$cityid = intval(Mysite::$app->config['default_cityid']);
	 
		$psbinterface = new psbinterface();
		if($psbinterface->testlink($psblink,$bizid,$key,$code)){
			$this->success('success');
		}else{
			$this->message($psbinterface->err());
		}
	
	}
	//获取配送站数据
	function getstaionlist(){
		$psbinterface = new psbinterface();
		if($psbinterface->stationlist()){ 
			$data['stationlist'] = $psbinterface->getstationlist();
		//	Mysite::$app->setdata($data);
			$this->success($data);
		}else{
			$this->message($psbinterface->err());
		}
		
	}
	//获取配送宝的配送圈和配送组信息
	function getstationchild(){
		$stationid  =  intval(IReq::get('stationid'));
		$psbinterface = new psbinterface();
		if($psbinterface->getstationchild($stationid)){ 
			$data['psgroup'] = $psbinterface->getpsgroup();
			$data['bizdistrict'] = $psbinterface->getbizdistrict(); 
			 
			$this->success($data);
		}else{
			$this->message($psbinterface->err());
		}
	} 
	function addacounttopsb(){
		$stationid  =  intval(IReq::get('stationid'));
		$shopid = intval(IReq::get('shopid'));
		$psgroupid = intval(IReq::get('psgroupid'));
		$bizdistrictid = intval(IReq::get('bizdistrictid'));
		$psbinterface = new psbinterface();
		if($psbinterface->createacount($shopid,$stationid,$psgroupid,$bizdistrictid)){  
			 $this->success('success');
		}else{
			$this->message($psbinterface->err());
		}
	}
	
	
	
	
	
} 