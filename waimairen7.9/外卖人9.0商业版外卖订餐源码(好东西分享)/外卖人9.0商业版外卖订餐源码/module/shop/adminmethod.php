<?php
class method   extends adminbaseclass
{
	//保存商品属性
	 function savegoodsattr(){
	 	 $this->checkadminlogin();
	 		$arrtypename = IReq::get('typename');
			$arrtypename = is_array($arrtypename) ? $arrtypename:array($arrtypename);
		  $siteinfo['goodsattr'] =   serialize($arrtypename);
		  $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);
	    $this->success('success');
	 }
	 
	 function shopjsset(){
		 $info = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."jscompute where id > 0  ");
		 $data['ptyj'] = $info[0];   //平台配送情况下的佣金设置
		 $data['sjyj'] = $info[1];   //商家配送情况下的佣金设置
		 $data['ptjs'] = $info[2];   //平台配送情况下的结算设置
		 $data['sjjs'] = $info[3];   //商家配送情况下的结算设置	
         		 
		 Mysite::$app->setdata($data);
	 }
	 function saveshopjsset(){
		$type = IReq::get('des'); 
		$data['pscost'] = IReq::get('pscost'); 
		$data['bagcost'] = IReq::get('bagcost'); 
		$data['shopdowncost'] = IReq::get('shopdowncost'); 
		$this->mysql->update(Mysite::$app->config['tablepre'].'jscompute',$data,"id='".$type."'");
		$this->success('success'); 
	 }
	 
	function savemoreshop(){  //批量添加店铺
		
      $sdata['shopname'] = IReq::get('shopname');
		$data['username'] = IReq::get('username');
		  $data['phone'] = IReq::get('maphone');
       $data['password'] = IReq::get('password');
       if(empty($sdata['shopname']))  $this->message('shop_emptyname');
		   $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  shopname='".$sdata['shopname']."'  ");
			 if(!empty($shopinfo)) $this->message('shop_repeatname');
 			 $uid = 0;
			 if($this->memberCls->regester($data['email'],$data['username'],$data['password'],$data['phone'],3)){
			 	$uid = $this->memberCls->getuid(); 
			 	$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>intval(IReq::get('admin_id'))),"uid='".$uid."'");
			 	
			 }else{
			 	 $this->message($this->memberCls->ero());
			 }
      $sdata['uid'] = $uid;
      $sdata['maphone'] =  $data['phone'];
      $sdata['addtime'] = time();
       $sdata['admin_id'] = intval(IReq::get('admin_id'));
      $nowday = 24*60*60*365;
	     $sdata['endtime'] = time()+$nowday;
	     $sdata['yjin'] = Mysite::$app->config['yjin'];

  
		$shoptype =  IReq::get('shoptype') ; 
	  $temparray = explode('_',$shoptype);
	   
	  $sdata['shoptype']  = $temparray[0];   // 店铺大类型 0为外卖 1为超市
	  $attrid =  $temparray[1];
	   
  
	   $checkshoptype =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id=".$attrid."  ");
	   if(empty($checkshoptype))  $this->message("获取店铺分类失败");
	   
	   
	    $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$sdata);
	  
	   $shopid = $this->mysql->insertid();
	    
	   $attrdata['shopid'] = $shopid;
	   $attrdata['cattype'] = $checkshoptype['cattype'];
	   $attrdata['firstattr'] = $checkshoptype['parent_id'];
	   $attrdata['attrid'] = $checkshoptype['id'];
	   $attrdata['value'] = $checkshoptype['name']; 
	   
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'shopattr',$attrdata);
	  
		  $this->success('success');
		
	}
	//保存店铺
	function saveshop()
	{
		$laiyuan = intval(IReq::get('laiyuan')); // 申请来源。1为微信端，主要用于判断微信端用户是否开过店
		$subtype = intval(IReq::get('subtype'));
		$id = intval(IReq::get('uid'));
		if(!in_array($subtype,array(1,2))) $this->message('system_err');
		if($subtype == 1){
			  $username = IReq::get('username');
			  if(empty($username)) $this->message('member_emptyname');
				$testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username='".$username."'  ");
			  if(empty($testinfo)) $this->message('member_noexit');
			  
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  uid='".$testinfo['uid']."' ");
			  if(!empty($shopinfo)) $this->message('member_isbangshop');
			  $data['shopname'] = IReq::get('shopname');
			  if(empty($data['shopname']))  $this->message('shop_emptyname');
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  shopname='".$data['shopname']."'  ");
			  $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>intval(IReq::get('admin_id')),'group'=>'3'),"uid='".$testinfo['uid']."'");
			  if(!empty($shopinfo)) $this->message('shop_repeatname');
			  $data['uid'] = $testinfo['uid'];
			 
			   $data['admin_id'] = intval(IReq::get('admin_id'));
			   $data['is_pass']  = 1; 
			   $data['pradiusa']  = 2; 
			   $data['yjin']  = Mysite::$app->config['yjin']; //店铺默认佣金
			   if(empty($data['admin_id']))  $this->message('请选择所属城市！');
			   $nowday = 24*60*60*365;
	           $data['endtime'] = time()+$nowday;			  
			   $shoptype =  IReq::get('shoptype') ; 
			   $temparray = explode('_',$shoptype);	   
			   $sdata['shoptype']  = $temparray[0];   // 店铺大类型 0为外卖 1为超市
			   $attrid =  $temparray[1];
			   $checkshoptype =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id=".$attrid."  ");
			   if(empty($checkshoptype))  $this->message("获取店铺分类失败"); 
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$data);  
			   $shopid = $this->mysql->insertid();	    
			   $attrdata['shopid'] = $shopid;
			   $attrdata['cattype'] = $checkshoptype['cattype'];
			   $attrdata['firstattr'] = $checkshoptype['parent_id'];
			   $attrdata['attrid'] = $checkshoptype['id'];
			   $attrdata['value'] = $checkshoptype['name']; 
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'shopattr',$attrdata);
			   $this->success('success');
		}elseif($subtype ==  2){
			/*检测*/
			  $data['username'] = IReq::get('username');
		      $data['phone'] = IReq::get('maphone');
			  $data['email'] = IReq::get('email');
			  $data['password'] = IReq::get('password');
			  $sdata['shopname'] = IReq::get('shopname');
			  $sdata['address'] = IReq::get('shopaddress');
		      if(empty($sdata['shopname']))  $this->message('shop_emptyname');
			     $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  shopname='".$sdata['shopname']."'  ");
				 if(!empty($shopinfo)) $this->message('shop_repeatname');
				 $password2 = IReq::get('password2');
			  if($password2 != $data['password']) $this->message('member_twopwdnoequale');
				 $uid = 0;
				 if($this->memberCls->regester($data['email'],$data['username'],$data['password'],$data['phone'],3)){
					$uid = $this->memberCls->getuid(); 
					$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>intval(IReq::get('admin_id'))),"uid='".$uid."'");
					
				 }else{
					 $this->message($this->memberCls->ero());
				 }
			  $sdata['uid'] = $uid;
			  $sdata['maphone'] =  $data['phone'];
			  $sdata['addtime'] = time();
			  $sdata['email'] =  $data['email'];    
			  $sdata['admin_id'] = intval(IReq::get('admin_id'));
			  $nowday = 24*60*60*365;
	          $sdata['endtime'] = time()+$nowday;
  
  
			$shoptype =  IReq::get('shoptype') ; 
			$temparray = explode('_',$shoptype);
	   
	  $sdata['shoptype']  = $temparray[0];   // 店铺大类型 0为外卖 1为超市
	  $attrid =  $temparray[1];
	   $sdata['is_pass']  = 1;
			$sdata['yjin']  = Mysite::$app->config['yjin']; //店铺默认佣金
	   if(empty($sdata['admin_id']))  $this->message('请选择所属城市！');
	   $checkshoptype =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id=".$attrid."  ");
	   if(empty($checkshoptype))  $this->message("获取店铺分类失败");
	   
	   
	    $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$sdata);
	  
	   $shopid = $this->mysql->insertid();
	    
	   $attrdata['shopid'] = $shopid;
	   $attrdata['cattype'] = $checkshoptype['cattype'];
	   $attrdata['firstattr'] = $checkshoptype['parent_id'];
	   $attrdata['attrid'] = $checkshoptype['id'];
	   $attrdata['value'] = $checkshoptype['name']; 
	   
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'shopattr',$attrdata);
	 
	 
	   if($laiyuan == 1){
		   $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('shopid'=>$shopid),"uid='".$this->member['uid']."'");
	   }
	   $this->success('success');
	   
		}else{
		 $this->message('system_err');
		}
	}
	 function shopbiaoqian()
	 {
	 	  $this->setstatus();
	 	  $shopid =  intval(IReq::get('id'));
	 	  if(empty($shopid))
	 	  {
	 	  	 echo 'shop_noexit';
	 	  	 exit;
	 	   }
	 	  $shopinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
	 	  if(empty($shopinfo))
	 	  {
	 	     echo 'shop_noexit';
	 	  	 exit;
	 	  }
	 	  $fastfood = array();
	 	  if($shopinfo['shoptype'] == 0){
	 	     $fastfood = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid=".$shopid."  ");
	   	}else{
			 $fastfood = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid=".$shopid."  ");

		}
	 	  $attrinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype='".$shopinfo['shoptype']."' and  parent_id = 0 and is_admin = 1  order by orderid desc limit 0,1000");//获取所有后台控制属性
	 	  $data['attrlist'] = array(); //每个主属性  --对应子属性
	    foreach($attrinfo as $key=>$value){
	  	   $value['det'] =  $this->mysql->getarr("select id,name,instro from ".Mysite::$app->config['tablepre']."shoptype where  cattype='".$shopinfo['shoptype']."' and   parent_id = ".$value['id']." order by id desc ");
	  	   $data['attrlist'][] = $value;
	    }
	 	  $shopsetatt = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where    cattype='".$shopinfo['shoptype']."' and  shopid = '".$shopid."'  limit 0,1000");
	    $myattr = array();
	    foreach($shopsetatt as $key=>$value){
	  	    $myattr[$value['firstattr'].'-'.$value['attrid']] = $value['value'];
	    }
	    $data['myattr'] = $myattr;
	 	  $data['fastfood'] = $fastfood;
	 	  $data['shopid'] = $shopid;
	 	  $data['shopinfo'] = $shopinfo;
		  
		  
		  
		  $data['ztylist'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."specialpage where  is_custom = 0 and showtype = 0 and is_show = 1 order by orderid asc ");
		 
		  
	    Mysite::$app->setdata($data);
	  }
	  function saveshopbq()
	{
             
		 $id = IReq::get('ids');
		 $shopid = intval(IReq::get('shopid'));
                
		 if(empty($shopid))
		 {
		 	  echo "<script>parent.uploaderror('店铺获取失败');</script>";
		 	 exit;
		 	}
		 	//fis_com
		  $is_recom = intval(IReq::get('is_recom'));
		  $isforyou = intval(IReq::get('isforyou'));
                  
		  $shopinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
		  if(!empty($shopinfo)){
		  	$udata['is_recom'] = $is_recom;
            
			$udata['isforyou'] = $isforyou;
		    	$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$udata,"id='".$shopid."'");
		  }
	  if($shopinfo['shoptype'] == 0){
		   $fastfood = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid=".$shopid."  ");
		   if(count($fastfood) > 0){
		 	  $data['is_com'] = intval(IReq::get('fis_com'));
		 	  $data['is_hot'] = intval(IReq::get('fis_hot'));
		 	  $data['is_new'] = intval(IReq::get('fis_new'));
			  $data['is_hui'] = intval(IReq::get('fis_hui'));
                         
		 	   $this->mysql->update(Mysite::$app->config['tablepre'].'shopfast',$data,"shopid='".$shopid."'");
		   }
	  }else{
		    $fastfood = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid=".$shopid."  ");
		   if(count($fastfood) > 0){
		 	
		 	  $data['is_hui'] = intval(IReq::get('fis_hui'));
			
		 	   $this->mysql->update(Mysite::$app->config['tablepre'].'shopmarket',$data,"shopid='".$shopid."'");
		   }
	  }

		$attrinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = '".$shopinfo['shoptype']."' and parent_id = 0 and is_admin = 1  order by orderid desc limit 0,1000");
		$tempinfo = array();
		foreach($attrinfo as $key=>$value){
			    $tempinfo[] = $value['id'];
		}
		if(count($tempinfo) > 0){
			//删除店铺属性是前台控制部分
			 $this->mysql->delete(Mysite::$app->config['tablepre']."shopattr"," shopid='".$shopid."' and firstattr in(".join(',',$tempinfo).") ");
		   //写店铺数据
		  foreach($attrinfo as $key=>$value){
			     //shopid     value ;
			     $attrdata['shopid'] = $shopid;
			     $attrdata['cattype'] = $shopinfo['shoptype'];
			     $attrdata['firstattr']  = $value['id'];
			     $inputdata = IFilter::act(IReq::get('mydata'.$value['id']));

			     //shopid  cattype     value;
			     if($value['type'] == 'input'){
			     	 $attrdata['attrid'] = 0;
			     	 $attrdata['value'] = $inputdata;
			     	 $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     }elseif($value['type'] == 'img'){
			     	 $temp = array();
			     	 $temp = is_array($inputdata)?$inputdata:array($inputdata);
			     	 $ids = join(',',$temp);
			     	 if(empty($ids)){
			     	    continue;
			     	 }
			     	 $tempattr  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where id in(".$ids.")   order by orderid desc limit 0,1000");
			     	 foreach($tempattr as $ky=>$val){
			     	 	$attrdata['attrid'] = $val['id'];
			     	  $attrdata['value'] = $val['name'];
			     	  $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     	 }
			     }elseif($value['type'] =='checkbox'){
			     	 $temp = array();
			     	 $temp = is_array($inputdata)?$inputdata:array($inputdata);
			     	 $ids = join(',',$temp);
			     	 if(empty($ids)){
			     	    continue;
			     	 }
			     	 $tempattr  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where id in(".$ids.")   order by orderid desc limit 0,1000");
			     	 foreach($tempattr as $ky=>$val){
			     	 	$attrdata['attrid'] = $val['id'];
			     	  $attrdata['value'] = $val['name'];
			     	  $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     	 }
			     }elseif($value['type'] = 'radio'){
			     	 //$this->json($inputdata);
			     	  $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id in(".intval($inputdata).")   order by orderid desc limit 0,1000");
			     	  if(empty($tempattr)){
			     	    continue;
			     	  }

			     	  $attrdata['attrid'] = $tempattr['id'];
			     	  $attrdata['value'] = $tempattr['name'];
			     	  $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     }else{
			      continue;
		       }
		  }
		  //is_search
		  $this->mysql->delete(Mysite::$app->config['tablepre']."shopsearch"," shopid='".$shopid."'  and parent_id in(".join(',',$tempinfo).") ");
		  foreach($attrinfo as $key=>$value){
		  	if($value['is_search'] == 1 && $value['type'] != 'input'){
		  		$inputdata = IFilter::act(IReq::get('mydata'.$value['id']));
		  		$temp = is_array($inputdata)?$inputdata:array($inputdata);
		  		foreach($temp as $ky=>$val){
		  			$searchdata['shopid'] = $shopid;
		  			$searchdata['parent_id'] = $value['id'];
		  			$searchdata['cattype'] = $shopinfo['shoptype'];
		  			$searchdata['second_id'] = intval($val);
		  			if($val > 0){
		  				 $this->mysql->insert(Mysite::$app->config['tablepre']."shopsearch",$searchdata);
		  			}
		  		}

		  	}
		  }
		}
		 echo "<script>parent.uploadsucess('');</script>";
		 exit;
	}
	
	function saveshopbq85()
	{
		 $id = IReq::get('ids');
		 $shopid = intval(IReq::get('shopid'));
		 if(empty($shopid))
		 {
		 	  echo "<script>parent.uploaderror('店铺获取失败');</script>";
		 	 exit;
		 	}
		 	//fis_com
		  $is_recom = intval(IReq::get('is_recom'));
		  $shopinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
		  if(!empty($shopinfo)){
		  	$udata['is_recom'] = $is_recom;
		    	$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$udata,"id='".$shopid."'");
		  }
	  if($shopinfo['shoptype'] == 0){
		   $fastfood = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid=".$shopid."  ");
		   if(count($fastfood) > 0){
		 	  $data['is_com'] = intval(IReq::get('fis_com'));
		 	  $data['is_hot'] = intval(IReq::get('fis_hot'));
		 	  $data['is_new'] = intval(IReq::get('fis_new'));
			  $data['is_hui'] = intval(IReq::get('fis_hui'));
		 	   $this->mysql->update(Mysite::$app->config['tablepre'].'shopfast',$data,"shopid='".$shopid."'");
		   }
	  }else{
		    $fastfood = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid=".$shopid."  ");
		   if(count($fastfood) > 0){
		 	
		 	  $data['is_hui'] = intval(IReq::get('fis_hui'));
			
		 	   $this->mysql->update(Mysite::$app->config['tablepre'].'shopmarket',$data,"shopid='".$shopid."'");
		   }
	  }

		$attrinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = '".$shopinfo['shoptype']."' and parent_id = 0 and is_admin = 1  order by orderid desc limit 0,1000");
		$tempinfo = array();
		foreach($attrinfo as $key=>$value){
			    $tempinfo[] = $value['id'];
		}
		if(count($tempinfo) > 0){
			//删除店铺属性是前台控制部分
			 $this->mysql->delete(Mysite::$app->config['tablepre']."shopattr"," shopid='".$shopid."' and firstattr in(".join(',',$tempinfo).") ");
		   //写店铺数据
		  foreach($attrinfo as $key=>$value){
			     //shopid     value ;
			     $attrdata['shopid'] = $shopid;
			     $attrdata['cattype'] = $shopinfo['shoptype'];
			     $attrdata['firstattr']  = $value['id'];
			     $inputdata = IFilter::act(IReq::get('mydata'.$value['id']));

			     //shopid  cattype     value;
			     if($value['type'] == 'input'){
			     	 $attrdata['attrid'] = 0;
			     	 $attrdata['value'] = $inputdata;
			     	 $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     }elseif($value['type'] == 'img'){
			     	 $temp = array();
			     	 $temp = is_array($inputdata)?$inputdata:array($inputdata);
			     	 $ids = join(',',$temp);
			     	 if(empty($ids)){
			     	    continue;
			     	 }
			     	 $tempattr  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where id in(".$ids.")   order by orderid desc limit 0,1000");
			     	 foreach($tempattr as $ky=>$val){
			     	 	$attrdata['attrid'] = $val['id'];
			     	  $attrdata['value'] = $val['name'];
			     	  $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     	 }
			     }elseif($value['type'] =='checkbox'){
			     	 $temp = array();
			     	 $temp = is_array($inputdata)?$inputdata:array($inputdata);
			     	 $ids = join(',',$temp);
			     	 if(empty($ids)){
			     	    continue;
			     	 }
			     	 $tempattr  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where id in(".$ids.")   order by orderid desc limit 0,1000");
			     	 foreach($tempattr as $ky=>$val){
			     	 	$attrdata['attrid'] = $val['id'];
			     	  $attrdata['value'] = $val['name'];
			     	  $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     	 }
			     }elseif($value['type'] = 'radio'){
			     	 //$this->json($inputdata);
			     	  $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id in(".intval($inputdata).")   order by orderid desc limit 0,1000");
			     	  if(empty($tempattr)){
			     	    continue;
			     	  }

			     	  $attrdata['attrid'] = $tempattr['id'];
			     	  $attrdata['value'] = $tempattr['name'];
			     	  $this->mysql->insert(Mysite::$app->config['tablepre']."shopattr",$attrdata);
			     }else{
			      continue;
		       }
		  }
		  //is_search
		  $this->mysql->delete(Mysite::$app->config['tablepre']."shopsearch"," shopid='".$shopid."'  and parent_id in(".join(',',$tempinfo).") ");
		  foreach($attrinfo as $key=>$value){
		  	if($value['is_search'] == 1 && $value['type'] != 'input'){
		  		$inputdata = IFilter::act(IReq::get('mydata'.$value['id']));
		  		$temp = is_array($inputdata)?$inputdata:array($inputdata);
		  		foreach($temp as $ky=>$val){
		  			$searchdata['shopid'] = $shopid;
		  			$searchdata['parent_id'] = $value['id'];
		  			$searchdata['cattype'] = $shopinfo['shoptype'];
		  			$searchdata['second_id'] = intval($val);
		  			if($val > 0){
		  				 $this->mysql->insert(Mysite::$app->config['tablepre']."shopsearch",$searchdata);
		  			}
		  		}

		  	}
		  }
		}
		 echo "<script>parent.uploadsucess('');</script>";
		 exit;
	}
	function passhop()
	{
		 $id = intval(IReq::get('id'));
		 $data['is_pass'] =  1;
		 $data['admin_id'] = Mysite::$app->config['default_cityid'] ;
        $data['starttime'] ="08:00-14:30";//店铺默认营业时间
        $data['yjin'] =Mysite::$app->config['yjin'];//店铺默认佣金
		 if(empty($id)) $this->message('shop_noexit');
		  $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id=".$id." ");
	    if(empty($tempattr))$this->message('shop_noexit');
	  	$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$id."'");
	  	$cdata['group'] = 3;
	  	$this->mysql->update(Mysite::$app->config['tablepre'].'member',$cdata,"uid='".$tempattr['uid']."'");
	  	$this->success('success');
	}
	//保存佣金
	function savesetshopyjin(){
		 $yjin = IReq::get('yjin');
		 $data['zitiyjb'] = IReq::get('zitiyjb');
		 $data['zitilimityj'] = IReq::get('zitilimityj');
		 $data['zitianyj'] = IReq::get('zitianyj');
		 $shopid = intval(IReq::get('shopid'));
		 if($yjin <0 || $data['zitiyjb'] <0|| $data['zitilimityj'] <0|| $data['zitianyj']<0  )$this->message('请输入不小于0的数值');
		 if(empty($shopid)) $this->message('shop_noexit');
		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id=".$shopid."  ");
		 if(empty($shopinfo)){
			 $this->message('店铺对应账号不存在');
		 }
		 $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member  where uid=".$shopinfo['uid']." ");
		 if(empty($userinfo)) $this->message('店铺对应账号不存在');
		 $cdata['backacount'] = trim(IReq::get('backacount'));
		 if(empty($cdata['backacount'])) $this->message('提现账号不能为空');
		  $this->mysql->update(Mysite::$app->config['tablepre'].'member',$cdata,"uid='".$userinfo['uid']."'");
		 $data['yjin'] = round($yjin, 2);//$yjin;
		 $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
		 $this->success('success');
	}
	//店铺排序
	function adminshoppx(){
		$shopid = intval(IReq::get('id'));
		$data['sort'] = intval(IReq::get('pxid'));
		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
	  $this->success('success');
	}
	//店铺有效天数
	function shopactivetime(){
		$shopid = intval(IReq::get('shopid'));
		$mysetclosetime= intval(IReq::get('mysetclosetime'));
        if($mysetclosetime>7486) $this->message("最大为7486天");
        if($mysetclosetime<0) $this->message("最小为0天");
		$nowday = 24*60*60*$mysetclosetime;
		$data['endtime'] = time()+$nowday;
		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
		$this->success('success');
	}
	function delshop()
	{
		 $id = IReq::get('id');
		 if(empty($id))  $this->message('shop_noexit');
		 $ids = is_array($id)? join(',',$id):$id;
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'shop',"id in($ids)");
	    $sdata['shopid'] = 0;
		 if(is_array($id)){
			foreach($id as $k=>$val){
				$memberinfo = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."member where shopid=".$val." ");  
				$this->mysql->update(Mysite::$app->config['tablepre'].'member',$sdata,"uid = ".$memberinfo['uid']."");	
			} 
		 }else{
			$memberinfo = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."member where shopid=".$id." ");  
			$this->mysql->update(Mysite::$app->config['tablepre'].'member',$sdata,"uid = ".$memberinfo['uid']."");
		 }
	   /*删除店铺对应商品及商品分类*/
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'goodstype',"shopid in($ids)");
     $this->mysql->delete(Mysite::$app->config['tablepre'].'goods',"shopid in($ids)");
     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopmarket'," shopid in($ids)");
     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopfast'," shopid in($ids)");
		 $this->mysql->delete(Mysite::$app->config['tablepre']."shopattr"," shopid in($ids)");
		 $this->mysql->delete(Mysite::$app->config['tablepre']."shopsearch"," shopid in($ids)");
 	   $this->mysql->delete(Mysite::$app->config['tablepre']."searkey"," goid  in($ids)   ");
 	   //$this->mysql->delete(Mysite::$app->config['tablepre']."areatomar"," shopid  in($ids) ");
	   $this->mysql->delete(Mysite::$app->config['tablepre']."marketcate"," shopid  in($ids) ");
	   $this->mysql->delete(Mysite::$app->config['tablepre']."shopmarket"," shopid  in($ids) "); //

	   $this->success('success');
	}
	//删除店铺属性
	function delshoptype(){
		 $id = IReq::get('id');
		 if(empty($id))  $this->message('shop_noexit');
		 $ids = is_array($id)? join(',',$id):$id;
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'shoptype'," id in($ids) ");
	   $this->success('success');
	}
	//保存店铺属性
	function saveshoptype(){
		$id = intval(IReq::get('id'));
		$data['name'] = trim(IReq::get('name'));
		$data['type'] = trim(IReq::get('type'));
		$data['parent_id'] = 0;
		$data['cattype'] = intval(IReq::get('cattype'));
		$data['is_search']  = intval(IReq::get('is_search'));
		$data['is_search']  = intval(IReq::get('is_search'));
		$data['is_main']  = intval(IReq::get('is_main'));
		$data['is_admin']  = intval(IReq::get('is_admin'));
		$data['instro']  = IReq::get('instro');
		$data['orderid']  = IReq::get('orderid');
		if(empty($data['name'])) $this->message('shop_emptyattr');
		if(empty($data['type'])) $this->message('shop_emptydatatype');
		if(empty($id))
		{
			$this->mysql->insert(Mysite::$app->config['tablepre'].'shoptype',$data);
		}else{
			$this->mysql->update(Mysite::$app->config['tablepre'].'shoptype',$data,"id='".$id."'");
		}
		$this->success('success');
	}
	//保存店铺二级属性
	function saveshopdettype(){
		 $id = IReq::get('id');
	   if($id < 0){
	     $this->message('system_err');
	   }
	   $ids = IReq::get('ids');
	   $name = IReq::get('name');
	   $instro = IReq::get('instro');
	   $cattype = IReq::get('cattype');
	   $ids = is_array($ids)? $ids:array($ids);
	   $name = is_array($name)?$name:array($name);
	   $instro = is_array($instro)?$instro:array($instro);
	   /*检测数据是否合法;*/
	   $checkdo = true;
	   $newdata = array();
	   $delids = array();
	   foreach($name as $key=>$value){
	   	 if(empty($value)){
	   	 	$checkdo = false;
	   	 	break;
	   	 }
	   	 $tempdata = array();
	   	 $tempdata['name'] = $value;
	   	 $tempdata['id'] = isset($ids[$key])?$ids[$key]:0;
	   	 $tempdata['instro'] = isset($instro[$key])?$instro[$key]:'';
	   	 if($tempdata['id'] > 0){
	   	 	$delids[] = $tempdata['id'];
	   	 }
	   	 $newdata[]= $tempdata;
	   }
	   $notinids = join(',',$delids);
	   if(!empty($notinids)){
	   	   $this->mysql->delete(Mysite::$app->config['tablepre'].'shoptype',"parent_id = $id and id not in($notinids)");
	   }else{
	   	   $this->mysql->delete(Mysite::$app->config['tablepre'].'shoptype',"parent_id = $id");
	   }
	   if($checkdo == false) $this->message('system_err');
	   foreach($newdata as $key=>$value){
	     $data['type'] = 0;
	     $data['parent_id'] = $id;
	     $data['cattype'] = $cattype;
	     $data['is_search'] = 0;
	     $data['is_main'] = 0;
	     $data['is_admin'] = 0;
	     $data['name'] = $value['name'];
	     $data['instro'] = $value['instro'];
	     if($value['id']  > 0){
	       $this->mysql->update(Mysite::$app->config['tablepre'].'shoptype',$data,"id='".$value['id']."'");
		   }else{
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'shoptype',$data);
	     }
	   }
	   $this->success('success');
	}
	function resetdefualt(){
		$shopid = IReq::get('shopid');
	    ICookie::set('adminshopid',$shopid,86400);
		$link = IUrl::creatUrl('shopcenter/index');
        $this->refunction('',$link);
	}

	function savegoodssign(){
		$id = intval(IReq::get('uid'));
		$data['name'] = IReq::get('name');
		$data['imgurl'] = IReq::get('img');
		$data['type']  = IReq::get('typename');
		$data['instro']  = IReq::get('instro');
		$data['typevalue'] = IReq::get('typevalue');
		if(empty($data['name'])) $this->message('shop_emptysignname');
		if(empty($data['imgurl'])) $this->message('shop_emptysignimg');
		if(empty($id))
		{
			$this->mysql->insert(Mysite::$app->config['tablepre'].'goodssign',$data);
		}else{
			$this->mysql->update(Mysite::$app->config['tablepre'].'goodssign',$data,"id='".$id."'");
		}
		$this->success('success');
	}
	function delgoodssign()
	{
	   $id = IReq::get('id');
		 if(empty($id))  $this->message('shop_emptysign');
		 $ids = is_array($id)? join(',',$id):$id;
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'goodssign'," id in($ids) ");
	   $this->success('success');
	}

	function adminshoplist(){
	    $this->setstatus();
		$default_cityid = empty(Mysite::$app->config['default_cityid'])?0:Mysite::$app->config['default_cityid'];
	    $where = " and ( admin_id = '".$default_cityid."' or admin_id = 0 ) "; 
	    $data['shopname'] =  trim(IReq::get('shopname'));
	   $data['username'] =  trim(IReq::get('username'));
	 	 $data['phone'] = trim(IReq::get('phone'));
	 	 $data['cityid'] = intval(IReq::get('cityid'));
	 	 if(!empty($data['shopname'])){
 		    $where .= " and shopname like '%".$data['shopname']."%'";
	 	 }
	 	 if(!empty($data['username'])){
	 	   $where .= " and uid in(select uid from ".Mysite::$app->config['tablepre']."member where username='".$data['username']."')";
	 	 }
	 	 if(!empty($data['phone'])){
	 	    $where .=" and phone='".$data['phone']."'";
	 	 }
	 	  if(!empty($data['cityid'])){
	 	    $where .=" and admin_id='".$data['cityid']."'";
	 	 }
		
	 	 //构造查询条件
	 	 $data['where'] = $where;
	    Mysite::$app->setdata($data);
	    
	}
	function setstatus(){
	    $data['shoptype'] = array('0'=>'外卖','1'=>'超市');
	   Mysite::$app->setdata($data);
	}
	function adminshopwati(){
	   $this->setstatus();
	   $adminlist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."admin where groupid='4' ");  
	 	 $temparr = array();
	 	 foreach($adminlist as $key=>$value){
	 	    $temparr[$value['uid']] = $value['username'];
	 	 }
		$where = ' and admin_id in('.Mysite::$app->config['default_cityid'].',0)';
		$data['where'] = $where;
	 	 $data['adminlist'] = $temparr;
	    Mysite::$app->setdata($data);
	   
	}
	function shoptype(){
		 $this->setstatus();
	}
	function addshop(){
	   $this->setstatus(); 
	   $citylist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where id > 0 and parent_id = 0 ");  
	 	 $data['citylist'] = $citylist;
		 $catparent = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where  type='checkbox' order by cattype asc limit 0,100");
			$catlist = array();
			foreach($catparent as $key=>$value){
				$tempcat   = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where parent_id = '".$value['id']."'  limit 0,100");
				foreach($tempcat as $k=>$v){
					 $catlist[] = $v;
				}
			}
			$data['catarr'] = array('0'=>'外卖','1'=>'超市');
			$data['catlist'] = $catlist; 
	 
	 	 Mysite::$app->setdata($data);  
	}
	function moreaddshop(){
	   $this->setstatus(); 
	$citylist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where id > 0 and parent_id = 0 ");  
	 	 $data['citylist'] = $citylist;
		 $catparent = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where  type='checkbox' order by cattype asc limit 0,100");
			$catlist = array();
			foreach($catparent as $key=>$value){
				$tempcat   = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where parent_id = '".$value['id']."'  limit 0,100");
				foreach($tempcat as $k=>$v){
					 $catlist[] = $v;
				}
			}
			$data['catarr'] = array('0'=>'外卖','1'=>'超市');
			$data['catlist'] = $catlist; 
	 
	 	 Mysite::$app->setdata($data);  
	}
	function setnotice(){
		 $shopid =  intval(IReq::get('shopid'));
	 	  if(empty($shopid))
	 	  {
	 	  	 echo '店铺不存在';
	 	  	 exit;
	 	   }
	 	  $shopinfo= $this->mysql->select_one("select noticetype,IMEI,machine_code,mKey from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
	 	  if(empty($shopinfo))
	 	  {
	 	     echo '店铺不存在';
	 	  	 exit;
	 	   }
	   $data['IMEI'] = $shopinfo['IMEI'];
	   $data['shopid'] = $shopid;
	   $data['machine_code'] = $shopinfo['machine_code'];
	   $data['mKey'] = $shopinfo['mKey'];
	   $data['noticetype'] = explode(',',$shopinfo['noticetype']);
	    
	   Mysite::$app->setdata($data);
	}
	
	function saveshoppnotice(){
		$pstype = IReq::get('pstype');
		 $shopid = intval(IReq::get('shopid'));
		  $data['IMEI'] = IReq::get('IMEI');
		 if(empty($shopid))
		 {
		 	  echo "<script>parent.uploaderror('店铺获取失败');</script>";
		 	 exit;
		 	}
		 $tempvalue = '';
		 if(is_array($pstype)){
		 	$tempvalue = join(',',$pstype);
		 }

		 $data['noticetype'] = $tempvalue;
		 $data['machine_code'] = IReq::get('machine_code');
		 $data['mKey'] =  IReq::get('mKey');
		 $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
		  echo "<script>parent.uploadsucess('');</script>";
		 exit;
	}
	function savelunadv(){
			$shopid = ICookie::get('adminshopid');
	    $imglist =IFilter::act(IReq::get('imglist'));
	    $links = IUrl::creatUrl('shop/shoplunadv');
	    if(empty($imglist)) $this->message('empty_img',$links);
	    $data['imglist'] = join(',',$imglist);
	    $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
	    $this->success('success',$links);
	}

	//  商品库 start
	function goodslibrary(){
		
		$listtype = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodslibrarycate   order by orderid asc  ");
		//获取该菜单下的所有商品
		$alllist = array();
		if(is_array($listtype))
		{
			foreach($listtype as $key=>$value)
			{
				$value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodslibrary where typeid = '".$value['id']."' order by good_order asc limit 0,1000  ");
				$alllist[]= $value;
			}
		}
	/* 	this->checkshoplogin();
		$shopid = ICookie::get('adminshopid');
		$shopinfo = $this->shopinfo();
		if(empty($shopinfo)) $this->message('shop_noexit');
		$shoptype = $shopinfo['shoptype'];
		if(empty($shopid)) $this->message('emptycookshop');
		if(empty($shoptype)){
	        $listtype = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodstype where shopid = '".$shopid."'  order by orderid asc  ");
    }elseif($shoptype ==1){
    	   $listtype = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."marketcate where shopid = '".$shopid."'  order by orderid asc  ");
    }
		//获取该菜单下的所有商品
		$alllist = array();
		if(is_array($listtype))
		{
			foreach($listtype as $key=>$value)
			{
				$value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where typeid = '".$value['id']."' and shopid=".$shopid." order by good_order asc limit 0,1000  ");
				$alllist[]= $value;
			}
		}
		$data['list'] =$alllist;
		//获取所有的  商品标签 goodssign 
		$goodssign = $this->mysql->getarr("select id,imgurl,name,instro from ".Mysite::$app->config['tablepre']."goodssign where type = 'goods'  order by id asc  ");
		$checksign = array();
		if(is_array($goodssign)){
		  foreach($goodssign as $key=>$value){
		  	$checksign[] = $value['id'];
		  }
		}
		$data['goodssign'] = $goodssign;
		$data['checksign'] = $checksign;
		$data['showshu'] = count($goodssign);
		$data['jsondata'] = json_encode($goodssign); */
		
		$data['list'] =$alllist;
		
		 Mysite::$app->setdata($data);
	}
	//导入数据界面
	function doinputexcel(){
		$newfilename = IFilter::act(IReq::get('newfilename'));
		$curtypeid = intval(IFilter::act(IReq::get('curtypeid')) );
		
			$typeidone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodslibrarycate where id = ".$curtypeid."  order by id asc  ");
	#print_r($typeidone);
		if(isset($_FILES['inputExcel'])){
		 
			$filename = time();//$HTTP_POST_FILES['inputExcel']['name'];
			//上传到服务器上的临时文件名
	    	$tmp_name = $_FILES['inputExcel']['tmp_name']; 
			
			$filepre = $_FILES['inputExcel']['name'];
			$filepre = explode('.',$filepre);
			$filepre = $filepre[1]; 
            $uploadpath = hopedir.'upload/excel/';  
			//move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
			$newfilename = $uploadpath.$filename.'.'.$filepre;
			$result=move_uploaded_file($tmp_name,$uploadpath.$filename.'.'.$filepre);//假如上传到当前目录下
			$excelclass = new phptoexcel();
			$newarray = $excelclass->getexcel($newfilename,array('goodsname','goodscost','goodsinstro'),array(0,1,2));
		#	print_r($newarray);
			/* Array
			(
				[0] =&gt; Array
					(
						[goodsname] =&gt; 张三
						[goodscost] =&gt; 5
					)

				[1] =&gt; Array
					(
						[goodsname] =&gt; 王伟
						[goodscost] =&gt; 6
					)

				[2] =&gt; Array
					(
						[goodsname] =&gt; 李慧慧
						[goodscost] =&gt; 7
					)

			) */
		
			foreach( $newarray as $key=>$value ){
				$data['name'] = $value['goodsname'];
				$data['cost'] = $value['goodscost'];
				$data['instro'] = $value['goodsinstro'];
				$data['typeid'] = $typeidone['id'];
				/* print_r($data);
				exit; */
				$this->mysql->insert(Mysite::$app->config['tablepre'].'goodslibrary',$data); 
			}
			 
			 echo "<script>parent.closemydo();</script>";
			 exit;
			 
		   
		}
		/*
		$filename = $HTTP_POST_FILES['inputExcel']['name'];
		//上传到服务器上的临时文件名
		$tmp_name = $_FILES['inputExcel']['tmp_name'];
		
		$msg = uploadFile($filename,$tmp_name);
		*/
		$data['newfilename'] = $newfilename;
		$data['curtypeid'] = $curtypeid;
		 Mysite::$app->setdata($data);
		
		
	}
	function savegoodstype(){
		$data['name'] = IFilter::act(IReq::get('name')); 
		$data['orderid'] = intval(IReq::get('orderid')); 
	
		if(!(IValidate::len($data['name'],1,10)))$this->message('goods_namelenth'); 

	   	$this->mysql->insert(Mysite::$app->config['tablepre'].'goodslibrarycate',$data);
	
		$this->success('success');
	}
	function editgoodstype()
	{
		
		$what = trim(IFilter::act(IReq::get('what'))); 
		$addressid = intval(IReq::get('addressid'));
		if(empty($addressid))$this->message('goods_emptytype');//$this->json(array('error'=>true,'message'=>''));  
  	if($what == 'name')
  	{ 
  		$arr['name'] = IFilter::act(IReq::get('controlname')); 
  		if(!(IValidate::len($arr['name'],2,10))) $this->message('gods_typelenth');// $this->json(array('error'=>true,'message'=>''));   
  		$this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrarycate',$arr,"id='".$addressid."' "); 
  		
  	  $this->success('success');// $this->json(array('error'=>false,'message'=>'操作完成')); 
  	}elseif($what == 'orderid')
  	{
  		$arr['orderid'] = intval(IReq::get('controlname'));  
  		$this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrarycate',$arr,"id='".$addressid."'  "); 
  		
  		$this->success('操作成功');// $this->json(array('error'=>false,'message'=>'操作完成'));
  	}elseif($what == 'allinfo'){
  		$arr['name'] = IFilter::act(IReq::get('name')); 
  		$arr['orderid'] = intval(IFilter::act(IReq::get('orderid'))); 
  	$this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrarycate',$arr,"id='".$addressid."'  "); 
  	  $this->success('success'); 
  	}else{
  		 
  		 
  		$this->message('nodefined_func');//  		$this->json(array('error'=>true,'message'=>'提交失败')); 
  	}
	}
	function delgoodstype()
	{ 
		
		 $uid = intval(IReq::get('addressid'));	 
		 if(empty($uid))  $this->message('goods_emptytype');//$this->json(array('err'=>true,'msg'=>'删除ID不能为空')); 
		
		    $checkshuliang = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."goodslibrarycate where id = '$uid' ");
		    if($checkshuliang < 1) $this->message('goods_emptytype');//$this->json(array('err'=>true,'msg'=>''));  
		    $this->mysql->delete(Mysite::$app->config['tablepre'].'goodslibrary',"typeid = '$uid' ");   
	      $this->mysql->delete(Mysite::$app->config['tablepre'].'goodslibrarycate',"id = '$uid' ");   
	  
	   $this->success('success'); 
	}
	function addgoods(){
	
  	$data['name'] = trim(IFilter::act(IReq::get('name')));
		$data['typeid'] = IFilter::act(IReq::get('typeid'));
		$data['cost'] = IFilter::act(IReq::get('cost'));
		$data['good_order'] = IFilter::act(IReq::get('good_order'));
		$data['img'] = '';
		
		if(!(IValidate::len($data['name'],2,50))) $this->message('goods_titlelenth');  
	  $chekcount = $data['cost']*100;
	  if($data['cost'] < 1) $this->message('goods_cost');	 
	
    $data['instro'] = '';
    $this->mysql->insert(Mysite::$app->config['tablepre'].'goodslibrary',$data);  
    $id = $this->mysql->insertid();

    $info = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodslibrary where id = '$id'");
    if(empty($info)) $this->message('goods_empty');
    $this->success($info); 
  }
	function goodsone(){	
		$id = intval(IFilter::act(IReq::get('gid')));
		if(empty($id)) $this->message('goods_empty'); 
		$goodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodslibrary where id=".$id."");
		if(empty($goodsinfo)) $this->message('goods_empty');
	
	        $listtype = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodslibrarycate   order by orderid asc  ");
    
	 $data['goodsinfo'] = $goodsinfo;
	 $data['listtype'] = $listtype;
	
	 Mysite::$app->setdata($data); 
	}
	function savegoodsall(){
		$gid = intval(IFilter::act(IReq::get('gid')));
		$link = IUrl::creatUrl('adminpage/shop/module/goodslibrary'); 
		if(empty($gid)) $this->message('goods_empty',$link); 
		$goodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodslibrary where  id=".$gid."");
		if(empty($goodsinfo)) $this->message('goods_empty',$link);  
		//构造数据

		$data['typeid'] = intval(IFilter::act(IReq::get('typeid')));
		$data['instro'] = IReq::get('instro');  
		$this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrary',$data,"id='".$gid."' ");
		$data['id'] = $gid;
		$goodsinfo['typeid'] = $data['typeid']; 
		echo "<script>parent.refreshgoods(".json_encode($goodsinfo).");</script>";
		exit; 
    
	}
	
	function userupload()
	 {
	 	 $link = IUrl::creatUrl('member/login');
	 	  if($this->member['uid'] == 0&&$this->admin['uid'] == 0)  $this->message('未登录',$link);
	 	  $_FILES['imgFile'] = $_FILES['head'];
	 
	  	$json = new Services_JSON();
      $uploadpath = 'images/goodspub/';
       $upload = new upload($uploadpath); 
 	  $filedir = $upload->getSigImgDir(); 
			
      if($upload->errno!=15&&$upload->errno!=0) {
		      $this->message($upload->errmsg());
		  }else{		  	
		  	     $gid = intval(IFilter::act(IReq::get('gid')));
		  	     $data['img'] = $filedir;
		        $this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrary',$data,"id='".$gid."'");
		  	 
			 $filedir = Mysite::$app->config['imgserver'].$filedir;
		      $this->success($filedir);
		  }
	 }
	 function delgoods(){

	   $uid = intval(IReq::get('id'));	 
		 if(empty($uid))  $this->message('goods_empty');//(array('error'=>true,'msg'=>'')); 
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'goodslibrary',"id = '$uid'");    
	   $this->success('success'); 
  }
	function delgoodsimg(){
	  $id = intval(IReq::get('id'));

	  $goodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodslibrary where id ='".$id."' ");  
		if(empty($goodsinfo)) $this->message('goods_empty');
		if(!empty($goodsinfo['img'])){
			IFile::unlink(hopedir.$goodsinfo['img']);
			$udata['img'] = '';
			$this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrary',$udata,"id='".$id."'"); 
			
		}
		$this->success('操作成功');
		
	  
	  
	}
	function updategoods(){ 

		$controlname =  trim(IFilter::act(IReq::get('controlname')));
		$goodsid =  intval(IReq::get('goodsid'));
		$values =  trim(IReq::get('values'));
		if(empty($goodsid)) $this->message('goods_empty');
		switch($controlname)
	   {
	   	  case 'name'://更新商品标题
	   	  if(!(IValidate::len($values,2,50))) $this->message('goods_titlelenth');  
	   	  $data['name'] = $values;
	   	  break;
	   	  case 'instro':
	   	   if(!(IValidate::len($values,0,200))) $this->message('goods_instrolenth');  
	   	   $data['instro'] = $values;
	   	  break;
	   	  case 'cost':
	   	  $values = $values * 100;
	   	  $kk = intval($values);
	   	  if($kk < 0) $this->message('goods_cost');
	   	  $data['cost'] = $values/100;
	   	  break;
		    case 'good_order':
	   	  $values = $values;
	   	  $kk = intval($values);
	   	  if($kk < 0) $this->message('good_order');
	   	  $data['good_order'] = $values;
	   	  break;	   
	   	  case 'typeid':
	   	  $values = intval($values);
	   	  if(empty($values)) $this->message('goods_typeid');
	   	  $shopinfo = $this->shopinfo();
	   	  $checkshuliang = 0;
	   	 
	   	      $checkshuliang = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."goodslibrarycate where id = '$values' ");
	   	 
	   	  if($checkshuliang < 1) $this->message('goods_typeid');
	   	  $data['typeid'] = $values;
	   	  break;
	   	  default:
	   	  $this->message('nodefined_func');
	   	  break;
	   }
	  $this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrary',$data,"id='".$goodsid."' ");
	  $this->success('success'); 
	}
	
	function delkuimg(){ 
		$imglujing = trim(IReq::get('imglujing'));
		IFile::unlink(hopedir.$imglujing);
		
		
		
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'imglist',"imageurl = '$imglujing'");
		
		$this->success('success');  
	}
	function uploadkuimggoods(){
		
				$gid = intval(IFilter::act(IReq::get('gid')));
			
				$data['img']= trim(IFilter::act(IReq::get('imglujing')));
		  	  
		        $this->mysql->update(Mysite::$app->config['tablepre'].'goodslibrary',$data,"id='".$gid."'");
		  	 
		      $this->success('success');
	
		
	}
	function selectmarketimg(){
		 $data['gid'] = intval(IReq::get('gid'));
	   $this->pageCls->setpage(intval(IReq::get('page')),18);   
		$total = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."imglist      ");
		$data['imglist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."imglist      order by addtime desc limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()." ");
		 
	  	$link = IUrl::creatUrl('adminpage/shop/module/selectmarketimg/gid/'.$data['gid']);//index.php?ctrl=&action=&module=
	 
		$data['pagecontent'] =  $this->pageCls->multi($total, 18, intval(IReq::get('page')), $link);
		$data['page'] = intval(IReq::get('page'));
		Mysite::$app->setdata($data); 
	}
	 
	//普通列表
	function showimglist(){ 
		$this->pageCls->setpage(intval(IReq::get('page')),18);   
		$total = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."imglist      ");
		$data['imglist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."imglist      order by addtime desc limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()." ");
		 
	  	$link = IUrl::creatUrl('adminpage/shop/module/showimglist');//index.php?ctrl=&action=&module=
		  
		$data['pagecontent'] =  $this->pageCls->multi($total, 18, intval(IReq::get('page')), $link);
		$data['page'] = intval(IReq::get('page'));
		Mysite::$app->setdata($data); 
		
	}
	//幻灯片
	function hshowimglist(){
		$this->pageCls->setpage(intval(IReq::get('page')),18);   
		$total = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."imglist      ");
		$data['imglist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."imglist      order by addtime desc limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()." "); 
	 
		Mysite::$app->setdata($data); 
		
	}
		function showshopdetail(){		//入驻资料
		$id = intval(IReq::get('id'));
		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  id='".$id."'  ");
		# print_r($shopinfo);
		$data['shopinfo']  = $shopinfo;
		 Mysite::$app->setdata($data);
	}
	function goodsgg(){ 
		$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),10);
	    	 
	    	$templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodsgg where parent_id =0  order by orderid asc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
	    	$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."goodsgg  where parent_id = 0 ");
	    	$pageshow->setnum($shuliang);
			$memcostloglist = array();
			foreach($templist as $key=>$value){
				 $tempc = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodsgg where parent_id =".$value['id']."  order by orderid asc limit 0,30");
	    	     $value['det'] = $tempc;
				 $memcostloglist[] = $value;
			}
	    	$data['pagecontent'] = $pageshow->getpagebar();
			$data['gglist'] = $memcostloglist;
			 
	     Mysite::$app->setdata($data);
	}
	//编辑规格
	function editgoodsgg(){
		$id = intval(IReq::get('id')); 
		$data['gginfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodsgg where id =".$id." and parent_id = 0  order by orderid asc limit 0,30");
		$data['ggdet'] = array();
		if(!empty($data['gginfo'])){
			$data['ggdet']=$this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodsgg where parent_id =".$id."  order by orderid asc limit 0,30");
		}
		$data['shoptype'] = array(0=>'餐饮',1=>'超市');
		 Mysite::$app->setdata($data);
		
	}
	//删除规格
	function delgoodsgg(){
		$id = intval(IReq::get('id'));
		if($id < 1) $this->message('规格ID错误');
		$this->mysql->delete(Mysite::$app->config['tablepre'].'goodsgg',"id = '$id'");
		$this->mysql->delete(Mysite::$app->config['tablepre'].'goodsgg',"parent_id = '$id'");
		$this->success('success');
	}
	//保存主规格
	function savemaingg(){
		$id = intval(IReq::get('id')); 
		if($id > 0){
			$gginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodsgg where id =".$id." and parent_id = 0  order by orderid asc limit 0,30");
		    if(empty($gginfo)){
				 $this->message('保存的规格ID 不是规格名属性');
			}
		}
		$data['name'] =  trim(IFilter::act(IReq::get('name')));
		$data['orderid'] = intval(IFilter::act(IReq::get('orderid')));
		if($id > 0){ 
			  $this->mysql->update(Mysite::$app->config['tablepre'].'goodsgg',$data,"id='".$id."' "); 
		}else{
			$data['parent_id'] = 0;
			$data['shoptype'] = intval(IFilter::act(IReq::get('shoptype'))); 
			$this->mysql->insert(Mysite::$app->config['tablepre'].'goodsgg',$data);
			$id = $this->mysql->insertid(); 
		}
		$link = IUrl::creatUrl('adminpage/shop/module/editgoodsgg/id/'.$id);
	    $this->success('success',$link); 
	}
	function savechildgg(){
		$parent_id = intval(IReq::get('parent_id')); 
		if($parent_id < 1) $this->message('所属规格不存在');
		$maingg = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodsgg where id =".$parent_id." and parent_id = 0  order by orderid asc limit 0,30");
		if(empty($maingg)) $this->message('所属规格不存在');
		$id = intval(IReq::get('id')); 
		$data['name'] = trim(IReq::get('name'));
		$data['orderid'] = trim(IReq::get('orderid'));
		if($id > 0){
			$childgg = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodsgg where id =".$id."    order by orderid asc limit 0,30");
			if($childgg['parent_id'] != $maingg['id']){
				$this->message('编辑规格值与所属规格不一致');
			}
		   $this->mysql->update(Mysite::$app->config['tablepre'].'goodsgg',$data,"id='".$id."' "); 
		}else{
			$data['parent_id'] = $maingg['id'];
			$data['shoptype'] = $maingg['shoptype'];
			$this->mysql->insert(Mysite::$app->config['tablepre'].'goodsgg',$data);
		}
		$this->success('success');  
	}
	function delgoodschildgg(){
		 $id = intval(IReq::get('id')); 
		 if($id < 1) $this->message('规格属性错误'); 
		 $this->mysql->delete(Mysite::$app->config['tablepre'].'goodsgg',"id = '$id'"); 
		 $this->success('success');  
	}
function savesearchwords(){   //向配置文件中保存搜索关键词
	 	 $this->checkadminlogin();
	 		$typename = IReq::get('typename');
			$typename = is_array($typename) ? $typename:array($typename);
		  $siteinfo['searchwords'] =   serialize($typename);
		  $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);
	    $this->success('success');
	 }

	 
	
/*
*	8.3新增功能 
*	2016-06-04------ 
*	zem   
*/
	 
	function virtualinfo(){	//增加店铺虚拟信息
	    $this->setstatus();
	    $where = '';
	    $goodswhere = '';
	     
	    
	    $data['shopname'] =  trim(IReq::get('shopname'));
	    $data['name'] =  trim(IReq::get('name'));
	   $data['username'] =  trim(IReq::get('username'));
	   $data['shop_type'] =  intval(IReq::get('shop_type'));
	 	 $data['phone'] = trim(IReq::get('phone'));
	 	 if(!empty($data['shopname'])){
 		    $where .= " and shopname like '%".$data['shopname']."%'";
	 	 } 
 		 if(!empty($data['shop_type'])){
			 $newshoptype = $data['shop_type']-1;
 		    $where .= " and shoptype = '".$newshoptype."'  ";
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
	 
		
		$this->pageCls->setpage(intval(IReq::get('page')),60); 
	 
 			$selectlist = $this->mysql->getarr("select id,shopname,phone,shoptype,uid,virtualsellcounts from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where."  order by sort asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
 			$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where." ");
 	   
	   $this->pageCls->setnum($shuliang); 
	  $data['pagecontent'] = $this->pageCls->getpagebar();
 		$data['selectlist'] = $selectlist;
 
	    Mysite::$app->setdata($data);
	    
	}
 	function saveshopsellcount(){   //保存店铺虚拟总销量
		$shopid = intval(IReq::get('shopid'));
		$virtualsellcounts= intval(IReq::get('virtualsellcounts'));
		$data['virtualsellcounts'] = $virtualsellcounts;
 		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
		$this->success('success');
	}
	
	
	function virtualgoods(){	//增加商品虚拟信息
	    $this->setstatus();
	    $where = '';
	    $goodswhere = '';
	     $shopid =  intval(IReq::get('id'));
 		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop   where id = '".$shopid."'  ");
		if(empty($shopinfo)){
			echo "获取店铺失败";
			exit;
		}
		$data['shopinfo'] = $shopinfo;
 	    $data['name'] =  trim(IReq::get('name'));
 	 	 
 	 	 //构造查询条件
	 	 $data['where'] = $where; 
	     
		 if(!empty($data['name'])){
	 	    $goodswhere .= " and name like '%".$data['name']."%'";
	 	 }
	 
		
		$this->pageCls->setpage(intval(IReq::get('page')),60); 
	 
  $selectlist1 = $this->mysql->getarr("select id,name,sellcount,virtualsellcount from ".Mysite::$app->config['tablepre']."goods  where shopid = '".$shopinfo['id']."'  ".$goodswhere."  order by good_order asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
	$selectlist3 =array();
  $selectlist2 = $this->mysql->getarr("select id,goodsid,goodsname,attrname from ".Mysite::$app->config['tablepre']."product  where shopid = '".$shopinfo['id']."'  ".$goodswhere."  order by id asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
	foreach($selectlist2 as $key=>$val){ 
		$val['name'] = $val['goodsname'].'【'.$val['attrname'].'】';
		$val['id'] = $val['goodsid'];
		$selectlist3[] = $val; 
	}
 	$selectlist = array_merge($selectlist1,$selectlist3);
 $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."goods  where shopid = '".$shopinfo['id']."'   ".$goodswhere." ");
 	   
	   $this->pageCls->setnum($shuliang); 
	  $data['pagecontent'] = $this->pageCls->getpagebar();
 		$data['selectlist'] = $selectlist;
 
	    Mysite::$app->setdata($data);
	    
	}
	 
	function savevirtualgoodcom(){  //后台保存添加商品虚拟评价
		
		$goodid = intval(IReq::get('goodid'));
		$point = intval(IReq::get('point'));
		$content = trim(IReq::get('content'));
		$addtime = trim(IReq::get('addtime'));
		$virtualname = trim(IReq::get('virtualname'));   // 新增   虚拟人名称
		
		 $goodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods   where id = '".$goodid."'  ");
		 if(empty($goodsinfo)) $this->message('获取商品信息失败');
		 if(empty($point)) $this->message('请对商品进行评分');
		 if(empty($virtualname)) $this->message('请填写评论人');
		
		
		$data['goodsid'] = $goodid;
		$data['shopid'] = $goodsinfo['shopid'];
		$data['content'] = $content;
		$data['addtime'] = strtotime($addtime);
		$data['point'] = $point;
		$data['is_show'] = 0;
		$data['virtualname'] = $virtualname; 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'comment',$data);
		$this->success('success');
	}
	
 /* 新增店铺分类广告 */
	 
function shopcateadv(){
		$where = '';  
		$cateid = intval(IReq::get('cateid'));
		$data['cateid'] = $cateid;
		if( $cateid > 0 ){
			$where = '  cateid = "'.$cateid.'" and ';
		}
		$data['where'] = $where;
	
	$data['catarr'] = array('waimai'=>'外卖','market'=>'超市');
	 $default_cityid = empty(Mysite::$app->config['default_cityid'])?0:Mysite::$app->config['default_cityid'];
	 	$moretypelist = $this->mysql->getarr("select* from ".Mysite::$app->config['tablepre']."appadv where type=2 and (   cityid='".$default_cityid."'  or  cityid = 0 ) and ( activity = 'waimai' or activity = 'market' ) order by orderid  asc");
	$data['moretypelist'] = $moretypelist; 

		 Mysite::$app->setdata($data);
}
 function addshopcateadv(){
	 
	 
	 	$id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopcateadv where id=".$id."  ");
 
	 
	 
	 $data['catarr'] = array('waimai'=>'外卖','market'=>'超市');
	 
	 $default_cityid = empty(Mysite::$app->config['default_cityid'])?0:Mysite::$app->config['default_cityid'];
	$moretypelist = $this->mysql->getarr("select* from ".Mysite::$app->config['tablepre']."appadv where type=2 and (   cityid='".$default_cityid."'  or  cityid = 0 ) and ( activity = 'waimai' or activity = 'market' ) order by orderid  asc");
	$data['moretypelist'] = $moretypelist;
	
 			
		 Mysite::$app->setdata($data);
	    	
	 
 }
	 
function saveshopcateadv(){
	$id = IReq::get('uid');
	   	$data['addtime'] = time();
	   	$data['title'] = IReq::get('title');
 	   	$data['orderid'] = IReq::get('orderid');
	   	$data['img'] = IReq::get('img');
 	   	$data['desc'] = IReq::get('desc');
 	   	$data['cateid'] = intval(IReq::get('cateid'));
 	   	$data['link'] = trim(IReq::get('link'));
//                print_R($data['cateid']);exit;
 		$data['cityid'] = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;

	   	if(empty($id))
	   	{
	   		$link = IUrl::creatUrl('adminpage/shop/module/addshopcateadv');
	   		if(empty($data['cateid'])) $this->message('请选择分类！',$link); 
			
			$appadvone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."appadv where   type=2 and param = '".$data['cateid']."' and (   cityid='".$data['cityid']."'  or  cityid = 0 ) ");
			
                        $data['shoptype'] = $appadvone['activity'];
			if(empty($data['img'])) $this->message('请上传图片！',$link); 
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'shopcateadv',$data);
                       
	   	}else{
	   		$link = IUrl::creatUrl('adminpage/shop/module/addshopcateadv/id/'.$id);
 
			if(empty($data['cateid'])) $this->message('请选择分类！',$link); 
			$appadvone = $this->mysql->select_one("select* from ".Mysite::$app->config['tablepre']."appadv where   type=2 and param = '".$data['cateid']."' and (   cityid='".$data['cityid']."'  or  cityid = 0 ) ");

			$data['shoptype'] = $appadvone['activity'];
			if(empty($data['img'])) $this->message('请上传图片！',$link); 
	   		$this->mysql->update(Mysite::$app->config['tablepre'].'shopcateadv',$data,"id='".$id."'");
                         
	   	}
	   	$link = IUrl::creatUrl('adminpage/shop/module/shopcateadv');
	    $this->success('success',$link);
}	 

  function delshopcateadv(){
   	  $id = IFilter::act(IReq::get('id'));
		  if(empty($id))  $this->message('未选择');
		  $ids = is_array($id)? join(',',$id):$id;
		 if(empty($ids))  $this->message('获取失败');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'shopcateadv'," id in($ids)");
	    $this->success('success','');
  }
	function createwm(){
		$shopid = IFilter::act(IReq::get('shopid'));
		if(!empty($shopid)){
			$shop = $this->mysql->select_one("select is_bdwx from ".Mysite::$app->config['tablepre']."shop where id = '".$shopid."' ");
			if($shop['is_bdwx']==1){
				$wxuser = $this->mysql->select_one("select username,userlogo from ".Mysite::$app->config['tablepre']."wxuser where shopid = '".$shopid."' ");
				if(!empty($wxuser)){
					$this->success($wxuser);
				}else{
					$wx_s = new wx_s;
					$ifmake = $wx_s->creatPassEwm($shopid);
					#print_r($ifmake);exit;
					if($ifmake == false){
						$this->message("生成二维码数据失败");					
					}else{
						$this->success($ifmake);
					}
				}
			}else{
				$wx_s = new wx_s;
				$ifmake = $wx_s->creatPassEwm($shopid);
				#print_r($ifmake);exit;
				if($ifmake == false){
					$this->message("生成二维码数据失败");					
				}else{
					$this->success($ifmake);
				}
			}			
		}
	}
	function updateshopbd(){
		$shopid = IFilter::act(IReq::get('shopid'));
		if(!empty($shopid)){
			$sarr['is_bdwx'] = 0;
			$sarr['wxopenid'] = '';
			$sarr['wxusername'] = '';
			$sarr['wxuserlogo'] = '';
			$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$sarr," id=".$shopid." ");
			$this->mysql->delete(Mysite::$app->config['tablepre'].'wxuser',"shopid=".$shopid." ");
			$this->success('解绑成功');
		}
	} 
	function checkbdwx(){
		$shopid = IFilter::act(IReq::get('shopid'));
		if(!empty($shopid)){
			$shop = $this->mysql->select_one("select is_bdwx,wxopenid,wxusername,wxuserlogo from ".Mysite::$app->config['tablepre']."shop where id = '".$shopid."' ");
			if($shop['is_bdwx']==1){
				$wx_s = new wx_s();
				if($wx_s->showuserinfo($shop['wxopenid'])){
					$wxuserinfo = $wx_s->getone();
					#print_r($wxuserinfo);exit;
					$sparr['wxusername'] = $wxuserinfo['nickname'];
					$sparr['wxuserlogo'] = $wxuserinfo['headimgurl'];
					$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$sparr," id=".$shopid." ");
					$this->success($shop);
				}else{
					$sarr['is_bdwx'] = 0;
					$sarr['wxopenid'] = '';
					$sarr['wxusername'] = '';
					$sarr['wxuserlogo'] = '';
					$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$sarr," id=".$shopid." ");
					$this->mysql->delete(Mysite::$app->config['tablepre'].'wxuser',"shopid=".$shopid." ");
					$this->message("用户信息获取失败");
				}
			}
		}
	} 
	
} 
?>