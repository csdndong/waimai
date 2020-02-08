<?php
class method   extends baseclass
{

	 function adminpsset(){
	 	   $data['pestypearr'] = array(
	 	   '1'=>'网站统一配送费',
	 	   '2'=>'根据不同区域统一配送费',
	 	   '3'=>'不计算配送费',
	 	   '4'=>'百度地图根据距离测算配送费',
	 	   '5'=>'根据菜品数计算配送费'
	 	   );
	     Mysite::$app->setdata($data);
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
	 function shopidtoad(){//添加店铺配送区域
	 	  $this->checkshoplogin();
	 		$areaid = intval(IReq::get('areaid'));//,areaid,cost
	   	$types =  IFilter::act(IReq::get('types'));

	   	$shopid = ICookie::get('adminshopid');
	   	if(!in_array($types,array('add','del'))) $this->message('nodefined_func');
	   	$checkarea = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id ='".$areaid."'   ");
	   	if(empty($shopid)) $this->message('shop_noexit');
	   	if(empty($checkarea)) $this->message('area_empty');
	    $shopinfo =	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id = '".$shopid."' "); 
	   	if($types == 'add'){
	   		//循环添加 区域店铺表
	   		 $whiledata = $checkarea;
	   		 $tempcheckid = array();
	   		 while(!empty($whiledata)){
	   		 	  $checkarea = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."areashop where areaid ='".$whiledata['id']."'  and shopid = '".$shopid."' ");
	   		 	  if($shopinfo['admin_id'] > 0){
	   		 	    if($checkarea['admin_id'] != $shopinfo['admin_id']) $this->message('area_adminiderr');
	   		 	  }
	   		 	  if(empty($areatocost)){//价格区间不存在 
	   	  	     	 $parentinfo['shopid'] = $shopid;
	   	  	     	 $parentinfo['areaid'] = $whiledata['id'];
			  	       $this->mysql->insert(Mysite::$app->config['tablepre']."areashop",$parentinfo); 
	   	  	  } 
	   	      $tempcheckid[] = $whiledata['id'];
	   	  	  if($whiledata['parent_id'] == 0){
	   	  	    break;
	   	  	  }
	   	  	  if(in_array($whiledata['parent_id'],$tempcheckid)){
	   	  	    break;
	   	  	  }
	   	  	  $whiledata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id ='".$whiledata['parent_id']."'   "); 
	   		 }

	   	}else{
	   		  //删除配送地址 
	   		  $whiledata = $checkarea;
	   		  $tempcheckid = array();
	   		  while(!empty($whiledata)){
	   		  	     //检测该区域是否被删除
	   		  	     $checkdeldata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."areashop where areaid in(select id from ".Mysite::$app->config['tablepre']."area where  parent_id ='".$whiledata['id']."')  and shopid = '".$shopid."' ");
	   		  	     if(!empty($checkdeldata)){
	   		  	       break;
	   		  	     }

	   		          $this->mysql->delete(Mysite::$app->config['tablepre']."areashop","areaid ='".$whiledata['id']."' and shopid = '".$shopid."'"); 
	   		          $tempcheckid[] = $whiledata['id'];
	   		          if($whiledata['parent_id'] == 0){
	   	  	             break;
	   	  	        }
	   	  	        if(in_array($whiledata['parent_id'],$tempcheckid)){
	   	  	             break;
	   	  	        }
	   	  	        $whiledata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id ='".$whiledata['parent_id']."'   ");
	   		  }


	   	}
	  	$this->success('success');
   }
   function shoptoadcost(){
   	$this->checkshoplogin();
     	$areaid = intval(IReq::get('areaid'));//,areaid,cost
	   	$cost =  IFilter::act(IReq::get('cost'));
	   	$cost = intval($cost*10)/10;
	   	if(empty($areaid)) $this->message('area_empty');
	    $shopid = ICookie::get('adminshopid');
	   	if(empty($shopid)) $this->message('shop_noexit');
	   	$data['cost'] = $cost;
	     $shopinfo =	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id = '".$shopid."' ");
	  if($shopinfo['shoptype'] == 0){
	   	   $this->mysql->update(Mysite::$app->config['tablepre'].'areatoadd',$data,"shopid='".$shopid."' and areaid = '".$areaid."'");
	  }elseif($shopinfo['shoptype'] ==  1){
	  		$this->mysql->update(Mysite::$app->config['tablepre'].'areatomar',$data,"shopid='".$shopid."' and areaid = '".$areaid."'");
	  }
	    $this->success('success');
   }
   function shoptoappcost(){
   	  $this->checkshoplogin();
     	$gongli = intval(IReq::get('gongli'));//,areaid,cost
	   	$cost =  intval(IFilter::act(IReq::get('cost')));


	    $shopid = ICookie::get('adminshopid');
	   	if(empty($shopid)) $this->message('shop_noexit');

	     $shopinfo =	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id = '".$shopid."' ");
	     if($shopinfo['shoptype'] == 0){
	    	//pradius
	    	 $fastfood = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid=".$shopid."  ");
	    	 $pradius = empty($fastfood['pradius'])?1:intval($fastfood['pradius']);
	    	 $tempdoid = empty($fastfood['pradiusvalue'])?array():unserialize($fastfood['pradiusvalue']);
	    	 $result = array();
	    	 for($i=0;$i< $pradius;$i++){
	    	 	   if($i == $gongli){
	    	 	       $result[$i] = $cost;
	    	 	   }else{
	    	 	       $result[$i]=  isset($tempdoid[$i])? $tempdoid[$i]:0;
	    	 	   }
	    	 }
	    	 $data['pradiusvalue'] =  serialize($result);


	   	   $this->mysql->update(Mysite::$app->config['tablepre'].'shopfast',$data,"shopid='".$shopid."' ");
	    }elseif($shopinfo['shoptype'] ==  1){
	    	$fastfood = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid=".$shopid."  ");
	    	 $pradius = empty($fastfood['pradius'])?1:intval($fastfood['pradius']);
	    	 $tempdoid = empty($fastfood['pradiusvalue'])?array():unserialize($fastfood['pradiusvalue']);
	    	 $result = array();
	    	 for($i=0;$i< $pradius;$i++){
	    	 	   if($i == $gongli){
	    	 	       $result[$i] = $cost;
	    	 	   }else{
	    	 	       $result[$i]=  isset($tempdoid[$i])? $tempdoid[$i]:0;
	    	 	   }
	    	 }
	    	 $data['pradiusvalue'] =  serialize($result);

	   	   $this->mysql->update(Mysite::$app->config['tablepre'].'shopmarket',$data,"shopid='".$shopid."' ");
	    }
	    $this->success('success');
   }
   
   function setmydefadid(){
	     $this->checkmemberlogin();
       	 $addressid = intval(IReq::get('addressid'));
		 if(empty($addressid)) $this->message('默认值错误');
		 $checkdata =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where id='".$addressid."' and userid = '".$this->member['uid']."' ");  
		 if(empty($checkdata)) $this->message('该地址不属于你该账号');
		    $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>0),'userid = '.$this->member['uid'].' ');
	     $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>1),'userid = '.$this->member['uid'].' and id='.$addressid.'');
		 $this->success('success');
		 
   }
   
   function useraddress(){
   	 $this->checkmemberlogin();
   	 $data['addressid'] = intval(IReq::get('addressid'));
	   $data['area_grade'] = empty(Mysite::$app->config['area_grade'])?1:Mysite::$app->config['area_grade'];
	   $data['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area  where  parent_id = 0 limit 0,100");

	   $temparea =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   ");
	   $areatoname = array();
	   foreach($temparea as $key=>$value){
	   	$areatoname[$value['id']] = $value['name'];
	   }
	   $areatoname[0] = '';
	   $data['areatoname'] = $areatoname;
	   $data['addresslimit'] = Mysite::$app->config['addresslimit'];//限制绑定店铺数量
		 Mysite::$app->setdata($data);
   }
   //id	userid	username	address 完整地址：选择地址与详细地址	phone	otherphone	contactname	default	
   //areaid1 区域1ID	areaid2 区域2ID	areaid3 区域3ID	sex 1时是男性，值为2时是女性，值为0时是未知	bigadr 选择的地址	
   //detailadr 详细地址	lat 地址坐标	lng 地址坐标	addtime
  function saveaddress(){
	   
   	$this->checkmemberlogin();
   	 $addressid = intval(IReq::get('addressid'));
   	 $laiyuan = intval(IReq::get('laiyuan'));  // 地址来源，  1为PC端，PC端无坐标，不在微信端显示     手机端不传此参数，默认为0
	   
	   $arr['tag'] =  intval(IReq::get('tag'));
		 if(empty($addressid))
		 {
		 	 $checknum = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."address where userid='".$this->member['uid']."' ");
				 if(Mysite::$app->config['addresslimit'] < $checknum)$this->message('member_addresslimit');

			 $arr['userid'] = $this->member['uid'];
			 $arr['username'] = $this->member['username']; 
			
			 
			 $arr['address'] =  IFilter::act(IReq::get('add_new'));
			 $arr['phone'] = IFilter::act(IReq::get('phone'));
			 $arr['otherphone'] = '';
			 $arr['contactname'] = IFilter::act(IReq::get('contactname'));
			 $check_message = IFilter::act(IReq::get('check_message'));
			 $arr['sex'] =  IFilter::act(IReq::get('sex'));
			 $arr['default'] = $checknum == 0?1:0;
			 $arr['addtime'] = time();
			 
			 if(!(IValidate::len($arr['contactname'],2,6)))$this->message('contactlength');
			
			 
			 if(!(IValidate::phone($arr['phone'])))$this->message('errphone');
			if(strlen($arr['phone']) != "11")$this->message('errphone');
			
			if(!(IValidate::len(IFilter::act(IReq::get('add_new')),3,50)))$this->message('member_addresslength');
			 
			 $areacode = Mysite::$app->config['areacode'];
			 if( $areacode  == 1 ){ 
					$phonecls = new phonecode($this->mysql,9,$arr['phone']); 
					if($phonecls->checkcode($check_message)){  
					}else{
						$this->message($phonecls->getError());
					}  
			 }
					 $arr['bigadr'] =  IFilter::act(IReq::get('bigadr'));
					 $arr['lat'] =  IFilter::act(IReq::get('lat'));
					 $arr['lng'] =  IFilter::act(IReq::get('lng'));
					 $arr['detailadr'] =  IFilter::act(IReq::get('detailadr'));
			 if($laiyuan == 0 &&  Mysite::$app->config['addAreaType'] == 0 ){	
				 
					if( empty($arr['bigadr']) ||  $arr['bigadr'] == '点击选择地址' ) $this->message('请选择地址！');
					if( empty($arr['detailadr'])  ) $this->message('请填写详细地址！');
					if( empty($arr['lat'])  ) $this->message('获取地图坐标失败，请重新获取！');
					if( empty($arr['lng'])  ) $this->message('获取地图坐标失败，请重新获取！');
			 
			  }
			 
			 if(!empty($arr['otherphone'])&&!(IValidate::phone($arr['otherphone'])))$this->message('errphone');
			 if( !empty($arr['lng']) &&  !empty($arr['lat'])  ){		  
				$content =   file_get_contents('https://restapi.amap.com/v3/geocode/regeo?output=json&location='.$arr['lng'].','.$arr['lat'].'&key='.Mysite::$app->config['map_webservice_key'].'&radius=1000&extensions=all'); 
				$backinfox  = json_decode($content,true);
				if( $backinfox['status'] == 1 && $backinfox['info'] == 'OK'){
					$arr['adcode'] = $backinfox['regeocode']['addressComponent']['adcode']; 
				}  		
			}
			 $this->mysql->insert(Mysite::$app->config['tablepre'].'address',$arr);
			 $addid = $this->mysql->insertid();
			 //$this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>0),'userid = '.$this->member['uid'].' ');
	         //$this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>1),'userid = '.$this->member['uid'].' and id='.$addid.'');
			 
			 $this->success($addid);
		 }else{
		    
			 $arr['address'] =  IFilter::act(IReq::get('add_new'));
			 $arr['phone'] = IFilter::act(IReq::get('phone'));
			 $arr['otherphone'] = '';
			 $arr['contactname'] = IFilter::act(IReq::get('contactname'));
			 $arr['sex'] =  IFilter::act(IReq::get('sex'));
			 $arr['addtime'] = time();
			  
			 if(!(IValidate::len($arr['contactname'],2,6)))$this->message('contactlength');
			 if(!(IValidate::len(IFilter::act(IReq::get('add_new')),3,50)))$this->message('member_addresslength');
			
			 if(!(IValidate::phone($arr['phone'])))$this->message('errphone');
			 if(strlen($arr['phone']) != "11")$this->message('errphone');
			
			 $check_message = IFilter::act(IReq::get('check_message')); 
			 if(Mysite::$app->config['areacode']==1){
					$phonecls = new phonecode($this->mysql,9,$arr['phone']); 
					if($phonecls->checkcode($check_message)){  
					}else{
						$this->message($phonecls->getError());
					} 
			 }
			 
	 
			 $arr['bigadr'] =  IFilter::act(IReq::get('bigadr'));
			 $arr['lat'] =  IFilter::act(IReq::get('lat'));
			 $arr['lng'] =  IFilter::act(IReq::get('lng'));
			 $arr['detailadr'] =  IFilter::act(IReq::get('detailadr'));
	        if($laiyuan == 0 &&  Mysite::$app->config['addAreaType'] == 0 ){		 
				if( empty($arr['bigadr']) ||  $arr['bigadr'] == '点击选择地址' ) $this->message('请选择地址！');
				if( empty($arr['detailadr'])  ) $this->message('请填写详细地址！');
				if( empty($arr['lat'])  ) $this->message('获取地图坐标失败，请重新获取！');
				if( empty($arr['lng'])  ) $this->message('获取地图坐标失败，请重新获取！');
	          }
			 if(!empty($arr['otherphone'])&&!(IValidate::phone($arr['otherphone'])))$this->message('errphone');
			if( !empty($arr['lng']) &&  !empty($arr['lat'])  ){		  
				$content =   file_get_contents('https://restapi.amap.com/v3/geocode/regeo?output=json&location='.$arr['lng'].','.$arr['lat'].'&key='.Mysite::$app->config['map_webservice_key'].'&radius=1000&extensions=all'); 
				$backinfox  = json_decode($content,true);
				if( $backinfox['status'] == 1 && $backinfox['info'] == 'OK'){
					$arr['adcode'] = $backinfox['regeocode']['addressComponent']['adcode']; 
				}  		
			}
			$this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,'userid = '.$this->member['uid'].' and id='.$addressid.'');
			$this->success($addressid);
		 }
		$this->success('success');
  }
  function deladdress(){
	$this->checkmemberlogin();
	$uid = intval(IReq::get('addressid'));
	if(empty($uid)) $this->message('member_noexitaddress');
    $cinfo =  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where  id = '".$uid."'  and  userid='".$this->member['uid']."'");
	if($cinfo['default'] == 1)$this->message('请先将该地址设置为非默认地址');
	$this->mysql->delete(Mysite::$app->config['tablepre'].'address',"id = ".$uid." and  userid='".$this->member['uid']."'");
	$this->success('success');
  }
  function editaddress(){
  	$this->checkmemberlogin();
  	$what = trim(IFilter::act(IReq::get('what')));
		$addressid = intval(IReq::get('addressid'));
		if(empty($addressid)) $this->message('member_noexitaddress');
  	if($what == 'default')
  	{
  		$arr['default'] = 0;
  		$this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"userid='".$this->member['uid']."'");
  		$arr['default'] = 1;
  		$this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
  		 $this->success('success');
  	}elseif($what == 'addr')
  	{
  		$arr['address'] = IFilter::act(IReq::get('controlname'));
  		if(!(IValidate::len($arr['address'],3,50))) $this->message('member_addresslength');
  		$this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
  	  $this->success('success');
  	}elseif($what == 'phone')
  	{
  		$arr['phone'] = IFilter::act(IReq::get('controlname'));
  		if(!IValidate::phone($arr['phone'])) $this->message('errphone');
  		$this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
  		 $this->success('success');
  	}
  	elseif($what == 'bak_phone')
  	{
  		$arr['otherphone'] = IFilter::act(IReq::get('controlname'));
  		if(!IValidate::phone($arr['otherphone']))$this->message('errphone');

  		$this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
  		 $this->success('success');
  	}
  	elseif($what == 'recieve_name')
  	{
  	 	$arr['contactname'] =  IFilter::act(IReq::get('controlname'));
  	 	if(!(IValidate::len($arr['contactname'],2,6))) $this->message('contactlength');
  		$this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,"id='".$addressid."' and userid='".$this->member['uid']."' ");
  		 $this->success('success');
  	}else{
  		 $this->message('nodefined_func');
  	}
  }
  function shopbaidumap(){
  	$this->checkshoplogin();
		 $shopid = ICookie::get('adminshopid');
		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  id = '".$shopid."' order by id asc");
	   $data['dlng'] = empty($shopinfo['lng'])||$shopinfo['lng']=='0.000000'?Mysite::$app->config['baidulng']:$shopinfo['lng'];
	   $data['dlat'] = empty($shopinfo['lat'])||$shopinfo['lat']=='0.000000'?Mysite::$app->config['baidulat']:$shopinfo['lat'];
		 $data['baidumapkey'] = Mysite::$app->config['baidumapkey'];
		  Mysite::$app->setdata($data);
  }
  function savemapshoplocation(){
  	$this->checkshoplogin();
	  $data['lng'] = IReq::get('lng');
		$data['lat'] = IReq::get('lat');
		$shopid = ICookie::get('adminshopid');
		if(empty($data['lng'])) $this->message('emptylng');
		if(empty($data['lat'])) $this->message('emptylat');
		if(empty($shopid)) $this->message('emptycookshop');
		$shopid = ICookie::get('adminshopid');
		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
			 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  id = '".$shopid."' order by id asc");
		if(!empty($shopinfo)){
	    $areasearch = new areasearch($this->mysql);    
      $areasearch->setdata($shopinfo['shopname'],'2',$shopinfo['id'],$data['lat'],$data['lng']);
      $areasearch->del();
      $areasearch->save();
      $areasearch->setdata($shopinfo['address'],'2',$shopinfo['id'],$data['lat'],$data['lng']);
      $areasearch->save();
    }
		
		
		
		
	  $this->success('success');
	}
	function setshoparea(){
		$this->checkshoplogin(); 
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
	
	//发送手机验证码(送货地址页面pc)
		function sendAddressPhone(){
		$phone = IFilter::act(IReq::get('phone'));
		$phonecls = new phonecode($this->mysql,9,$phone); 
		if($phonecls->sendcode()){  
			echo 'areashowsend(\''.$phone.'\','.$phonecls->getTime().')';
			exit;
		}else{
			echo 'noshow(\''.$phonecls->getError().'\')';
			exit;
		} 
		
	}
	//发送手机验证码(送货地址页面wx)
		function areaAddressPhone(){
		$phone = IFilter::act(IReq::get('phone'));
		$phonecls = new phonecode($this->mysql,9,$phone); 
		if($phonecls->sendcode()){  
			echo 'areashowsend(\''.$phone.'\','.$phonecls->getTime().')';
			exit;  
		}else{ 
			echo 'Tmsg(\''.$phonecls->getError().'\')';
			exit;
		}  
	}
	
	function ghmap(){
 	   $data['dlng'] = IFilter::act(IReq::get('lng'));
	   $data['dlat'] = IFilter::act(IReq::get('lat'));
	   $data['cur_cityname'] = IFilter::act(IReq::get('cityname'));
 		  Mysite::$app->setdata($data);
  }
	

}



?>