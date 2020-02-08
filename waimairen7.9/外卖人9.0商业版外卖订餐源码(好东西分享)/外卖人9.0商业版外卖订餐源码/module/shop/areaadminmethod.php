<?php
class method   extends areaadminbaseclass
{
	function saveopenset(){
		$cityid = $this->admin['cityid'];
		if(empty($cityid))$this->message('城市id获取失败');
		$status =  intval(IReq::get('status'));  
		if(empty($status))$this->message('营业状态获取失败');
		if($status == 1){
			$this->mysql->update(Mysite::$app->config['tablepre'].'shop',array('is_open'=>1)," id > 0 and admin_id = ".$cityid."  ");
		}elseif($status == 2){
			$this->mysql->update(Mysite::$app->config['tablepre'].'shop',array('is_open'=>0)," id > 0 and admin_id = ".$cityid."  ");
		}

		$this->success('success');	
	}
	 function cxrulelist(){
             $cityid = $this->admin['cityid'];
             
             $cxinfolist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."rule where  parentid = 1  and  cityid = ".$cityid." order by id asc " );
             $data['cxrulelist'] = $cxinfolist;
             $data['nowtime'] = time();
			 #print_r($data);exit;
             Mysite::$app->setdata($data); 
         }
	 function addcxrule(){
		    $id = intval(IReq::get('id'));      
       $cxinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."rule where  id = ".$id."   " );
       $cityid = $this->admin['cityid'];
	   $shoplist = array();
	   $shoplist = $this->mysql->getarr("select id,shopname,shoptype from ".Mysite::$app->config['tablepre']."shop where is_pass = 1 and admin_id = ".$cityid."   " );
	   foreach($shoplist as $k=>$v){
		   $v['cxclass'] = '';
		   //1满赠活动 2满减活动 3折扣活动 4免配送费 5首单立减
		   $checkcx1 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 1 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx2 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 2 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx3 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 3 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx4 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 4 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx5 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 5 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   if(!empty($checkcx1)){
			   $v['cxclass'] = $v['cxclass'].'act1  ';
		   }
		   if(!empty($checkcx2)){
			   $v['cxclass'] = $v['cxclass'].'act2  ';
		   }
		   if(!empty($checkcx3)){
			   $v['cxclass'] = $v['cxclass'].'act3  ';
		   }
		   if(!empty($checkcx4)){
			   $v['cxclass'] = $v['cxclass'].'act4  ';
		   }
		   if(!empty($checkcx5)){
			   $v['cxclass'] = $v['cxclass'].'act5  ';
		   }
		   if(in_array($v['id'],explode(',',$cxinfo['shopid']))){
			   $v['cxclass'] = $v['cxclass'].'oldshop  ';
		   }
		   if($v['shoptype']==1){
			   $psinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$v['id']."   " );
			   if($psinfo['sendtype'] == 1){
				   $shopps[] = $v;   
			   }else{
				   $platps[] = $v;   
			   }			   
		   }else{
			   $psinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$v['id']."   " );
			   if($psinfo['sendtype'] == 1){
				   $shopps[] = $v;   
			   }else{
				   $platps[] = $v;   
			   }
			   
			   
		   } 
	   }
       $data['cxsignlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
       $data['cxinfo'] = $cxinfo;
       $data['shopps'] = $shopps;	   
	   $data['platps'] = $platps;
	   $data['nowtime'] = time();
	   #print_r($data);
       Mysite::$app->setdata($data);   
	 }	 
	 function addcxrule1(){
		   $id = intval(IReq::get('id')); 
		   $cityid = $this->admin['cityid'];
		   $cxinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."rule where  id = ".$id."   " );
   
		   $shoplist = $this->mysql->getarr("select id,shopname from ".Mysite::$app->config['tablepre']."shop where  admin_id = ".$cityid."   " );
		   $data['cxsignlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
		   $data['cxinfo'] = $cxinfo;
		   $data['shoplist'] = $shoplist;
		   Mysite::$app->setdata($data);                
	 }
	 function delcxrule(){
		   $id = IReq::get('id'); 
		   if(empty($id))  $this->message('id为空');
		   $ids = is_array($id)? join(',',$id):$id;    
		   $this->mysql->delete(Mysite::$app->config['tablepre'].'rule',"id in(".$ids.")");  
		   $this->success('success');  
         }
    function savecxrule(){
			$shopidarr = IReq::get('shopid');
			if(empty($shopidarr))$this->message('请选择参与活动商家');
			$data['shopid'] = implode(',',$shopidarr);	
			$data['parentid'] = intval(IReq::get('parentid'));
			$data['shopbili'] = intval(IReq::get('shopbili'));
			if($data['shopbili']>100)$this->message('网站承担比例数值不能大于100');
			$data['cityid'] = $this->admin['cityid'];
			$data['type'] = 1;//默认购物车限制
			$cxid = intval(IReq::get('cxid'));
			$controltype = intval(IReq::get('controltype'));//1满赠活动 2满减活动 3折扣活动 4免配送费 5首单立减
			$data['controltype'] = $controltype;
			$setinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."cxruleset where  id = ".$controltype."   " );
			$data['imgurl'] = $setinfo['imgurl'];//活动图标
			$data['supporttype'] = $setinfo['supportorder'];//支持订单类型 1支持全部订单 2只支持在线支付订单
			$data['supportplatform'] = $setinfo['supportplat'];//支持平台类型 1pc 2微信 3触屏 4app
			$data['status'] =  intval(IReq::get('status'));
			$ordertype = $data['supporttype']==2?'在线支付满':'满';
			if($controltype == 1){//1满赠活动
				$data['limitcontent'] = intval(IReq::get('limitcontent_1'));
				$data['presenttitle'] = trim(IFilter::act(IReq::get('presenttitle')));
				if(empty($data['limitcontent'])) $this->message('请输入订单限制金额');
				if(empty($data['presenttitle'])) $this->message('请输入赠品名称及数量'); 
				$data['name']= $ordertype.''.$data['limitcontent'].'赠送'.$data['presenttitle'];	 
			}
			if($controltype == 2){//2满减活动
				$limitcontent = IReq::get('limitcontent_2');
				$controlcontent = IReq::get('controlcontent_2');
				$data['limitcontent'] = implode(',',$limitcontent);
				$data['controlcontent'] = implode(',',$controlcontent);			
				$name = $data['supporttype']==2?'在线支付':'';
				foreach($limitcontent as $k=>$v){
					if($controlcontent[$k] > $v) $this->message('减免金额不能大于限制金额');		
					$name .= '满'.$v.'减'.$controlcontent[$k].';';
				}
				$data['name'] = rtrim($name, ";");
			}
			if($controltype == 3){//3折扣活动
				$data['limitcontent'] = intval(IReq::get('limitcontent_3'));
				$data['controlcontent'] = IReq::get('controlcontent_3');
				$zhe = $data['controlcontent'];
				if( $zhe <= 0 || $zhe >= 10 )$this->message('折扣值请录入大于0小于10的数值');
				$data['name']= $ordertype.''.$data['limitcontent'].'享'.$zhe.'折优惠';
				if(empty($data['limitcontent'])) $this->message('请输入订单限制金额');
				if(empty($data['controlcontent'])) $this->message('请输入折扣值'); 
			}
			if($controltype == 4){//4免配送费
				$data['limitcontent'] = intval(IReq::get('limitcontent_4'));			 
				$data['name']= $ordertype.''.$data['limitcontent'].'免配送费';
				if(empty($data['limitcontent'])) $this->message('请输入订单限制金额');			 
			}
			if($controltype == 5){//5首单立减
				$data['limitcontent'] = intval(IReq::get('limitcontent_5'));	
				$data['controlcontent'] = intval(IReq::get('controlcontent_5'));
                if($data['controlcontent'] > $data['limitcontent']) $this->message('减免金额不能大于限制金额');			
				$data['name']= '新用户下单满'.$data['limitcontent'].'立减'.$data['controlcontent'].'元';	 	
			}
			if(empty($data['name'])) $this->message('促销标题不能为空');
			$limittype = intval(IReq::get('limittype'));//1不限制 2表示指定星期 3自定义日期
			$data['limittype'] = in_array($limittype,array('1,','2','3')) ? $limittype:1;
			if($data['limittype'] ==  1){
				$data['limittime'] = '';
			}elseif($data['limittype'] == 2){
				$limittime = IFilter::act(IReq::get('limittime1'));
				if(!is_array($limittime)) $this->message('errweek');
				$data['limittime'] = join(',',$limittime);
			}else{
				$starttime = IFilter::act(IReq::get('starttime'));
				$endtime = IFilter::act(IReq::get('endtime'));			
				if(empty($starttime)) $this->message('cx_starttime');
				if(empty($endtime)) $this->message('cx_endtime');        
				$data['starttime'] = strtotime($starttime.' 00:00:00');
				$data['endtime'] = strtotime($endtime.' 23:59:59');
				if($data['endtime'] <= $data['starttime']) $this->message('结束时间不能早于开始时间');     
			}  		
			if(empty($cxid)){
				$data['creattime'] = time();
				$this->mysql->insert(Mysite::$app->config['tablepre'].'rule',$data);
			}else{        
				$this->mysql->update(Mysite::$app->config['tablepre'].'rule',$data,"id='".$cxid."'");
			}
			
			$this->success('success');
    }
	
	 function cateset(){ 
			 
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
			$data['appadvlist'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."appadv where cityid = '".$this->admin['cityid']."'  order by orderid asc   limit 0,100");
			
			
          	Mysite::$app->setdata($data); 
		 
	 }
	function showshopdetail(){		//入驻资料
		$id = intval(IReq::get('id'));
		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  id='".$id."'  ");
		# print_r($shopinfo);
		$data['shopinfo']  = $shopinfo;
		 Mysite::$app->setdata($data);
	}
	//保存店铺
	function saveshop()
	{
		$subtype = intval(IReq::get('subtype'));
		$id = intval(IReq::get('uid'));
		if(!in_array($subtype,array(1,2))) $this->message('system_err');
		if($subtype == 1){
			  $username = IReq::get('username');
			  if(empty($username)) $this->message('member_emptyname');
				$testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username='".$username."'  ");
			  if(empty($testinfo)) $this->message('member_noexit');
			  if($testinfo['admin_id'] != $this->admin['cityid']) $this->message('shop_noownadmin');
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."usrlimit where  `group`='".$testinfo['group']."' and  name='editshop' ");
			  if(empty($shopinfo)) $this->message('member_noownshop');
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  uid='".$testinfo['uid']."' ");
			  if(!empty($shopinfo)) $this->message('member_isbangshop');
			  $data['shopname'] = IReq::get('shopname');
			  if(empty($data['shopname']))  $this->message('shop_emptyname');
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  shopname='".$data['shopname']."'  ");
			  if(!empty($shopinfo)) $this->message('shop_repeatname');
			  $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>$this->admin['cityid']),"uid='".$testinfo['uid']."'");	 
			  $data['uid'] = $testinfo['uid'];
			  $data['admin_id'] = $this->admin['cityid'];
			  $data['shoptype'] = intval(IReq::get('shoptype'));
			  $nowday = 24*60*60*365;
	       $data['endtime'] = time()+$nowday;
	       $data['is_pass'] = 1;
			$data['yjin']  = Mysite::$app->config['yjin']; //店铺默认佣金
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$data);
			  $this->success('success');
		}elseif($subtype ==  2){
			/*检测*/
			$data['username'] = IReq::get('username');
		  $data['phone'] = IReq::get('maphone');
      $data['email'] = IReq::get('email');
      $data['password'] = IReq::get('password');
      $sdata['shopname'] = IReq::get('shopname');
       if(empty($sdata['shopname']))  $this->message('shop_emptyname');
		   $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  shopname='".$sdata['shopname']."'  ");
			 if(!empty($shopinfo)) $this->message('shop_repeatname');
			 $password2 = IReq::get('password2');
		   if($password2 != $data['password']) $this->message('member_twopwdnoequale');
			 $uid = 0;
			 if($this->memberCls->regester($data['email'],$data['username'],$data['password'],$data['phone'],3)){
			 	 $uid = $this->memberCls->getuid();
			 	 $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>$this->admin['cityid']),"uid='".$uid."'");	 
			 }else{
			 	 $this->message($this->memberCls->ero());
			 }
      $sdata['uid'] = $uid;
      $sdata['maphone'] =  $data['phone'];
      $sdata['addtime'] = time();
      $sdata['email'] =  $data['email'];
      $sdata['shoptype'] = intval(IReq::get('shoptype'));
      $nowday = 24*60*60*365;
	     $sdata['endtime'] = time()+$nowday;
	     $sdata['admin_id'] = $this->admin['cityid'];
		 $sdata['is_pass'] = 1;
		$sdata['yjin']  = Mysite::$app->config['yjin']; //店铺默认佣金
      $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$sdata);
		  $this->success('success');
		}else{
		 $this->message('nodefined_func');
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
       $sdata['admin_id'] = $this->admin['cityid'];
      $nowday = 24*60*60*365;
	     $sdata['endtime'] = time()+$nowday;
  
  
		$shoptype =  IReq::get('shoptype') ; 
	  $temparray = explode('_',$shoptype);
	  $sdata['yjin'] = Mysite::$app->config['yjin'];
	   
	  $sdata['shoptype']  = $temparray[0];   // 店铺大类型 0为外卖 1为超市
	  $attrid =  $temparray[1];
	   
  
	   $checkshoptype =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id=".$attrid."  ");
	   if(empty($checkshoptype))  $this->message("获取店铺分类失败");
	   
	   $sdata['is_pass']  = 1;
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
	 function saveshopbq()
	{   
		 $id = IReq::get('ids');
		 $shopid = intval(IReq::get('shopid'));
		 
		 if(empty($shopid))
		 {
		 	  echo "<script>parent.uploaderror('店铺获取失败');</script>";
		 	 exit;
		 	}
		 	
		  $is_recom = intval(IReq::get('is_recom'));
		  $isforyou = intval(IReq::get('isforyou'));
          $is_selfsitecx = intval(IReq::get('is_selfsitecx'));		  
		  if($is_selfsitecx != 1){
			 $this->mysql->delete(Mysite::$app->config['tablepre'] . 'rule', "shopid = '$shopid'");  
		  }
		  $shopinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
		  if(!empty($shopinfo)){
		  	$udata['is_recom'] = $is_recom;
			$udata['is_selfsitecx'] = $is_selfsitecx;
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
	 
	function saveshopbqxxx()
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
          $is_selfsitecx = intval(IReq::get('is_selfsitecx'));
		  if($is_selfsitecx != 1){
			 $this->mysql->delete(Mysite::$app->config['tablepre'] . 'rule', "shopid = '$shopid'");  
		  }
		  if(!empty($shopinfo)){
		  	$udata['is_recom'] = $is_recom;
			$udata['is_selfsitecx'] = $is_selfsitecx;
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
	function passhop()
	{
		 $id = intval(IReq::get('id'));
		 $data['is_pass'] =  1;
		$data['yjin'] =Mysite::$app->config['yjin'];//店铺默认佣金
		 if(empty($id)) $this->message('shop_noexit');
		  $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id=".$id." ");
	    if(empty($tempattr))$this->message('shop_noexit');
	     if($tempattr['admin_id'] != $this->admin['cityid']) $this->message('shop_noownadmin');
	  	$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$id."'");
	  	$cdata['group'] = 3;
	  	$this->mysql->update(Mysite::$app->config['tablepre'].'member',$cdata,"uid='".$tempattr['uid']."'");
	  	$this->success('success');
	}
	//保存佣金    ---修改一下函数
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
		 $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id=".$shopid." ");
	    if(empty($tempattr))$this->message('shop_noexit');
	    if($tempattr['admin_id'] != $this->admin['cityid']) $this->message('shop_noownadmin');
		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
	  $this->success('success');
	}
	//店铺有效天数
	function shopactivetime(){
		$shopid = intval(IReq::get('shopid'));
		$mysetclosetime= intval(IReq::get('mysetclosetime'));
		$nowday = 24*60*60*$mysetclosetime;
		$data['endtime'] = time()+$nowday;
		 $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id=".$shopid." ");
	    if(empty($tempattr))$this->message('shop_noexit');
	    if($tempattr['admin_id'] != $this->admin['cityid']) $this->message('shop_noownadmin');
		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
		$this->success('success');
	}
	function delshop()
	{
		 $id = IReq::get('id');
		 if(empty($id))  $this->message('shop_noexit');
		  $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id=".$id." ");
		   if(empty($tempattr))$this->message('shop_noexit');
	     if($tempattr['admin_id'] != $this->admin['cityid']) $this->message('shop_noownadmin');
		  
		  
		 $ids = is_array($id)? join(',',$id):$id;
		 
		 
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'shop',"id in($ids)");
	   /*删除店铺对应商品及商品分类*/
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'goodstype',"shopid in($ids)");
     $this->mysql->delete(Mysite::$app->config['tablepre'].'goods',"shopid in($ids)");
     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopmarket'," shopid in($ids)");
     $this->mysql->delete(Mysite::$app->config['tablepre'].'shopfast'," shopid in($ids)");
		 $this->mysql->delete(Mysite::$app->config['tablepre']."shopattr"," shopid in($ids)");
		 $this->mysql->delete(Mysite::$app->config['tablepre']."shopsearch"," shopid in($ids)");
		 $this->mysql->delete(Mysite::$app->config['tablepre']."areatoadd"," shopid  in($ids) "); //
	   $this->mysql->delete(Mysite::$app->config['tablepre']."searkey"," goid  in($ids)   ");
	   $this->mysql->delete(Mysite::$app->config['tablepre']."areamarket"," shopid  in($ids) ");
	   $this->mysql->delete(Mysite::$app->config['tablepre']."areatomar"," shopid  in($ids) ");
	   $this->mysql->delete(Mysite::$app->config['tablepre']."marketcate"," shopid  in($ids) ");
	   $this->mysql->delete(Mysite::$app->config['tablepre']."shopmarket"," shopid  in($ids) "); //

	   $this->success('success');
	} 
	function resetdefualt(){
		$shopid = IReq::get('shopid');
			 $tempattr  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id=".$shopid." ");
			 $link = IUrl::creatUrl('areaadminpage/shop');
	    if(empty($tempattr))$this->message('shop_noexit',$link);
	    if($tempattr['admin_id'] != $this->admin['cityid']) $this->message('shop_noownadmin',$link);
	  ICookie::set('adminshopid',$shopid,86400);
		$link = IUrl::creatUrl('shopcenter/index');
    $this->refunction('',$link);
	} 
	function adminshoplist(){
	    $this->setstatus();
	    $where = '';
	    
	    
	    $data['shopname'] =  trim(IReq::get('shopname'));
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
	 	 $where .= "  and   admin_id = '".$this->admin['cityid']."' ";
	 	 //构造查询条件
	 	 $data['where'] = $where; 
	    
	    Mysite::$app->setdata($data);
	    
	}
    function adoptshop(){
        $this->setstatus();
        $where = ' ';

        $data['shopname'] =  trim(IReq::get('shopname'));
        $data['username'] =  trim(IReq::get('username'));
        if(!empty($data['shopname'])){
            $where .= " and shopname like '%".$data['shopname']."%'";
        }
        if(!empty($data['username'])){
            $where .= " and uid in(select uid from ".Mysite::$app->config['tablepre']."member where username='".$data['username']."')";
        }
        $where .= "  and   admin_id = '".$this->admin['cityid']."' ";
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
	}
	 
	function addshop(){
	   $this->setstatus(); 
	   $uid = $this->admin['cityid'];
 	   $areaadminone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where groupid='4'  and uid = '".$uid."'  ");  
	 	 $data['areaadminone'] = $areaadminone;
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
	    $uid = $this->admin['cityid'];
		
	    $areaadminone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where groupid='4'  and uid = '".$uid."'  ");  
	 	 $data['areaadminone'] = $areaadminone;
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
	 	  $shopinfo= $this->mysql->select_one("select noticetype,IMEI,machine_code,mKey,admin_id from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
	 	  if(empty($shopinfo))
	 	  {
	 	     echo '店铺不存在';
	 	  	 exit;
	 	   }
	 	   if($shopinfo['admin_id'] != $this->admin['cityid']){
	 	     echo '该店铺不属于您管理';
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
      $shopinfo= $this->mysql->select_one("select noticetype,IMEI,machine_code,mKey,admin_id from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
	 	  if(empty($shopinfo))
	 	  {
	 	    echo "<script>parent.uploaderror('店铺不存在');</script>";
		 	 exit;
	 	   }
	 	   if($shopinfo['admin_id'] != $this->admin['cityid']){
	 	     echo "<script>parent.uploaderror('该店铺不属于您管理');</script>";
		 	 exit;  
	 	  }
		 $data['noticetype'] = $tempvalue;
		 $data['machine_code'] = IReq::get('machine_code');
		 $data['mKey'] =  IReq::get('mKey');
		 $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
		  echo "<script>parent.uploadsucess('');</script>";
		 exit;
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
	
	 function addgd(){
       
        $cattypeid = IFilter::act(IReq::get('typeid'));//跳转属性指     typeid 为lifthelp时候是固定的生活服务 数字时候是分类
        $name = trim(IFilter::act(IReq::get('name')));// 跳转属性
        $appposition = intval(IFilter::act(IReq::get('appposition')));//1轮播图片可多个     2（固定展示区域）    3（自由展示区域 需要自定义内容）
        $id = intval(IFilter::act(IReq::get('id')));
        $orderid = intval(IFilter::act(IReq::get('orderid')));
        if(empty($cattypeid))$this->message('未选择模块');
        if(empty($name)) $this->message('未录入名称');
        if(empty($appposition))$this->message('未设置展示类型');
		
		$citywhere = "  and cityid = '".$this->admin['cityid']."'  ";

        if( $cattypeid != 'lifehelp' && $cattypeid != 'shophui' && $cattypeid != 'paotui'){
            $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype  where  id='".$cattypeid."' order by cattype asc limit 0,100");
            if(empty($checkinfo)) $this->message('未查找到分类值');
            if($id > 0){
                $checkinfo2 = $this->mysql->select_one("select param from ".Mysite::$app->config['tablepre']."appadv  where id='".$id."'  ".$citywhere." ");
                if($checkinfo2['param'] != $cattypeid) {
                    $checkaa = $this->mysql->counts("select id from " . Mysite::$app->config['tablepre'] . "appadv  where  param='" . $cattypeid . "' and  type = '".$appposition."'	 ".$citywhere."	");
                    if ($checkaa > 0) $this->message('跳转页面分类选项不可重复选择');
                }
            }else {
                $checkinfo2 = $this->mysql->counts("select id from " . Mysite::$app->config['tablepre'] . "appadv  where  param='" . $cattypeid . "' and  type = '".$appposition."'	  ".$citywhere."	");
                if ($checkinfo2 > 0) $this->message('跳转页面分类选项不可重复选择');
            }

            $data['activity'] =  empty($checkinfo['cattype'])?'waimai':'market';

        }else{
            if($cattypeid == 'lifehelp'){
                $data['activity'] =  'lifehelp';
            }
			if($cattypeid == 'shophui'){
                $data['activity'] =  'shophui';
            }
			if($cattypeid == 'paotui'){
                $data['activity'] =  'paotui';
            }

            if($id > 0){
                $checkinfo2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."appadv  where  id='".$id."'   ".$citywhere."  ");
                if($checkinfo2['param'] != $cattypeid) {
                    $checkaa = $this->mysql->counts("select id from " . Mysite::$app->config['tablepre'] . "appadv  where  param='" . $cattypeid . "' and  type = '".$appposition. "'   ".$citywhere." ");
                    if ($checkaa > 0) $this->message('跳转页面分类选项不可重复选择');
                }

            }else{
                $checkinfo2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."appadv  where  param='".$cattypeid."' and type = '".$appposition."'  ".$citywhere."  ");
                if (!empty($checkinfo2)) $this->message('跳转页面分类选项不可重复选择');
            }

        } 
		
        $data['cityid'] = $this->admin['cityid'];
        $data['orderid'] = $orderid;
        $data['name'] = $name;
        $data['type'] = $appposition;
        $data['img'] = trim(IFilter::act(IReq::get('imgurl')));
        if(empty($data['img'])) $this->message('图片为空');
        //需要转换


        $data['param'] = $cattypeid;
        if($id > 0){
            $this->mysql->update(Mysite::$app->config['tablepre'].'appadv',$data,"id='".$id."'");
        }else{
            $this->mysql->insert(Mysite::$app->config['tablepre'].'appadv',$data);
        }
        $this->success('成功');
    }
	 function addad(){
		
			$name = trim(IFilter::act(IReq::get('name')));// 跳转属性
			$appposition = intval(IFilter::act(IReq::get('appposition')));//1轮播图片可多个     2（固定展示区域）    3（自由展示区域 需要自定义内容） 
			$id = intval(IFilter::act(IReq::get('id')));
		 
			if(empty($name)) $this->message('未录入名称'); 
			if(empty($appposition))$this->message('未设置展示类型'); 
			
			$data['name'] = $name;
			$data['type'] = $appposition;
			$data['img'] = trim(IFilter::act(IReq::get('imgurl')));
			if(empty($data['img'])) $this->message('图片为空');
			//需要转换
			$data['activity'] ='';
			$data['param'] = 0; 
			if($id > 0){
				 $this->mysql->update(Mysite::$app->config['tablepre'].'appadv',$data,"id='".$id."'");
			}else{
				$this->mysql->insert(Mysite::$app->config['tablepre'].'appadv',$data); 
			}
			$this->success('成功'); 
	 }
	 function delappadv(){
		
	    $id =  intval(IFilter::act(IReq::get('id')));
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'appadv',"id  = '".$id."' "); 
	    $this->success('操作成功');
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
	  $default_cityid = $this->admin['cityid'];
	 	$moretypelist = $this->mysql->getarr("select* from ".Mysite::$app->config['tablepre']."appadv where type=2 and (   cityid='".$default_cityid."'  or  cityid = 0 ) and ( activity = 'waimai' or activity = 'market' ) order by orderid  asc");
	$data['moretypelist'] = $moretypelist;
	
 			
	 
		 Mysite::$app->setdata($data);
}
 function addshopcateadv(){
	 
	 
	 	$id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopcateadv where id=".$id."  "); 
	 
	 $data['catarr'] = array('waimai'=>'外卖','market'=>'超市');
	 
	 $default_cityid = $this->admin['cityid'];
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
 		$data['cityid'] = $this->admin['cityid'];
		 
	   	if(empty($id))
	   	{
	   		$link = IUrl::creatUrl('areaadminpage/shop/module/addshopcateadv');
	   		if(empty($data['cateid'])) $this->message('请选择分类！',$link); 
			
			$appadvone = $this->mysql->select_one("select* from ".Mysite::$app->config['tablepre']."appadv where   type=2 and param = '".$data['cateid']."' and (   cityid='".$data['cityid']."'  or  cityid = 0 ) ");
			$data['shoptype'] = $appadvone['activity'];
			if(empty($data['img'])) $this->message('请上传图片！',$link); 
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'shopcateadv',$data);
	   	}else{
	   		$link = IUrl::creatUrl('areaadminpage/shop/module/addshopcateadv/id/'.$id);
 
			if(empty($data['cateid'])) $this->message('请选择分类！',$link); 
			$appadvone = $this->mysql->select_one("select* from ".Mysite::$app->config['tablepre']."appadv where   type=2 and param = '".$data['cateid']."' and (   cityid='".$data['cityid']."'  or  cityid = 0 ) ");
		 
			$data['shoptype'] = $appadvone['activity'];
			if(empty($data['img'])) $this->message('请上传图片！',$link); 
	   		$this->mysql->update(Mysite::$app->config['tablepre'].'shopcateadv',$data,"id='".$id."'");
	   	}
	   	$link = IUrl::creatUrl('areaadminpage/shop/module/shopcateadv');
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

	 
	 
	
	
	
	 
}



?>