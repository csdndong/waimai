<?php
class method   extends adminbaseclass
{
	//分站列表
	 function  stationlist(){
 	    	$querytype = IReq::get('querytype');
	    	$searchvalue = IReq::get('searchvalue');
 	    	 $status = intval(IReq::get('status'));
 	    	 $cityid = intval(IReq::get('cityid'));
	       $where = '  where mem.uid > 0 and mem.groupid = 4  ';
	     
	    	 $data['searchvalue'] ='';
	    	 $data['querytype'] ='';
			 $newlink = '';
	    	if(!empty($querytype))
	    	{
	    		if(!empty($searchvalue)){
	    			 $data['searchvalue'] = $searchvalue;
	       	   $where .= ' and '.$querytype.' LIKE \'%'.$searchvalue.'%\' ';
	       	   $newlink .= '/searchvalue/'.$searchvalue.'/querytype/'.$querytype;
	       	   $data['querytype'] = $querytype;
	    		} 
	    	}
			
			$data['status'] = '';  
	    	if($status > 0)
	    	{ 
	          	$newstatus = $status -1;
	          	$where .=  ' and st.stationis_open = '.$newstatus; 
	          $data['status'] = $status;
	          $newlink .= '/status/'.$status;
	    	}
			$data['cityid'] = '';  
	    	if($cityid > 0)
	    	{  
 	          $where .=  ' and st.cityid = '.$cityid; 
	          $data['cityid'] = $cityid;
	          $newlink .= '/cityid/'.$cityid;
	    	}
	    
	    	$link = IUrl::creatUrl('/adminpage/station/module/stationlist'.$newlink);
	    	$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),10);
	    	 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
	    	 //
	    	 
	    	$stationlist = $this->mysql->getarr("select *,mem.uid from ".Mysite::$app->config['tablepre']."admin as mem left join  ".Mysite::$app->config['tablepre']."stationadmininfo as st on mem.uid = st.uid   ".$where." order by mem.uid desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
	    	$data['stationlist'] = array();
			foreach($stationlist as $k=>$v){
			    $psset = $this->mysql->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$v['cityid']."' ");
				#print_r($psset);exit;
				$v['is_allow_ziti'] = $psset['is_allow_ziti'];
				$data['stationlist'][] = $v;
			}
			
			$shuliang  = $this->mysql->counts("select mem.uid from ".Mysite::$app->config['tablepre']."admin as mem left join  ".Mysite::$app->config['tablepre']."stationadmininfo as st on mem.uid = st.uid   ".$where." ");
	    	$pageshow->setnum($shuliang);
	    	$data['pagecontent'] = $pageshow->getpagebar($link);
	    # print_r($data);exit;
   	     Mysite::$app->setdata($data);
	 } 
	 function paytypeset(){
		 $cityid = intval(IReq::get('cityid'));
		
		 $station = $this->mysql->select_one("select cityid,paytype from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$cityid."' ");  
		 
		 if(empty($station)){
		 	 echo "<script>parent.uploaderror('请先完善该分站的配送半径和配送范围等配送设置信息');</script>";
		 	 exit;
		 }  	  
		 $data['paytype'] = explode(',',$station['paytype']);
		 $data['cityid'] = $station['cityid']; 
		 Mysite::$app->setdata($data); 
	 }
	 function savepaytypeset(){
		 $cityid = intval(IReq::get('cityid'));
		 $paytype = IReq::get('paytype');
		 if(empty($paytype)){
			 echo "<script>parent.uploaderror('请至少勾选一种支付方式');</script>";
		 	 exit;
		 }  
		 $station = $this->mysql->select_one("select cityid from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$cityid."' ");  
		 if(empty($station)){
			  echo "<script>parent.uploaderror('请先完善该分站的配送半径和配送范围等配送设置信息');</script>";
		 	 exit;
		 }
		 $data['paytype'] = implode(',',$paytype); 
		 $this->mysql->update(Mysite::$app->config['tablepre'].'platpsset',$data,"cityid='".$cityid."'");
		 echo "<script>parent.uploadsucess('');</script>";
		 exit;
	 }
	 function managestation(){
		 $id = intval(IReq::get('id'));
		 $data['id'] = $id;
		 $data['citylist'] = array();
		 $temparr =	$this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where   id > 0 and parent_id = 0  order by orderid asc    ");
		 if( !empty($temparr) ){
			 foreach($temparr as $key=>$value){
				 $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."stationadmininfo where cityid='".$value['id']."' "); 				 
				 if( empty($checkinfo) ){   
					  $data['citylist'][] = $value;
				 } 
			 }
		 } 
		 
		 Mysite::$app->setdata($data);
	 }
	 //城市列表
	  function citylist(){
	  
        $newlink= ''; 
        $where = "  id > 0 and parent_id = 0   ";
        $page=new page();//实例化分页类
        $page->setpage(intval(IReq::get('page')),10);//赋初始值（偏移值、每页个数）
        $data['citylist']=	$this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where   ".$where."   order by orderid asc    limit ".$page->startnum().", ".$page->getsize());
        $pageCount	=$this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."area where   ".$where." order by id desc");
        $page->setnum($pageCount);//总页数
        $pagelink = IUrl::creatUrl('adminpage/station/module/citylist'.$newlink);
        $data['pagecontent']=$page->getpagebar($pagelink);//显示分页
        Mysite::$app->setdata($data);
	 
 	 }
	 //删除城市
	function delcity()
	{limitalert();

		 $uid = intval(IReq::get('id'));
		 if(empty($uid))  $this->message('area_empty');
 	     $this->mysql->delete(Mysite::$app->config['tablepre'].'area',"id = '$uid'"); 
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'areashop',"areaid = '$uid'");  
	     $this->success('success');;
	} 
	//删除分站
	function delstation()
	{limitalert();

		 $uid = intval(IReq::get('id'));
		 if(empty($uid))  $this->message('未选中');
 	    $this->mysql->delete(Mysite::$app->config['tablepre'].'stationadmininfo',"uid = '$uid'"); 
 	     $this->mysql->delete(Mysite::$app->config['tablepre'].'admin',"uid = '$uid'"); 
 	     $this->success('success');
	} 
	//新增或者编辑城市
	function savecity(){
	   
#limitalert();
	   $id = intval(IReq::get('uid'));
		$data['name'] = IReq::get('name');
		$data['orderid']  = intval(IReq::get('orderid'));
		$data['pin'] = strtoupper(IReq::get('pin'));
		$data['parent_id'] = 0;
		$data['adcode'] =  intval(IReq::get('adcode'));
		$data['procode'] =  intval(IReq::get('procode'));
   		if(empty($id))
		{ 
			$data['lng'] = 0;
		   $data['lat'] = 0;
			$link = IUrl::creatUrl('station/citylist');
			if(empty($data['name']))  $this->message('area_emptyname',$link);
			if(empty($data['pin']))	$this->message('area_emptyfirdstword',$link);
			if(empty($data['adcode']))  $this->message('获取区域编码失败1',$link);
			if(empty($data['procode']))	$this->message('获取区域编码失败2',$link);
        
			
		    $adreinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where adcode='".$data['adcode']."' ");  
			if( !empty($adreinfo) ){
				$this->message('此城市已添加过，请勿重新添加');
			}
		
			if(!$this->mysql->insert(Mysite::$app->config['tablepre'].'area',$data)){
			   $this->message('system_err');
			} 
		}else{
			$link = IUrl::creatUrl('area/adminarealist/id/'.$id);
			if(empty($data['name']))  $this->message('area_emptyname',$link);
			if(empty($data['pin']))	$this->message('area_emptyfirdstword',$link);
			if(empty($data['adcode']))  $this->message('获取区域编码失败1',$link);
			if(empty($data['procode']))	$this->message('获取区域编码失败2',$link);
			 
			$this->mysql->update(Mysite::$app->config['tablepre'].'area',$data,"id='".$id."'");
		}
		$link = IUrl::creatUrl('station/citylist');
		$this->success('success',$link);
	 }	
	 //添加分站
	  
	 
	 function savestationadmin(){
		$is_selfsitecx = intval(IReq::get('is_selfsitecx'));	
		$is_allow_ziti = intval(IReq::get('is_allow_ziti'));					
		$uid = IReq::get('uid'); 
		$username = trim(IReq::get('username'));
		$password = trim(IReq::get('password'));
		$stationname = trim(IReq::get('stationname'));
		$stationusername = trim(IReq::get('stationusername'));
		$stationphone = trim(IReq::get('stationphone'));
		$stationlnglat = trim(IReq::get('stationlnglat'));
		$stationaddress = trim(IReq::get('stationaddress'));
		$orderid = intval(IReq::get('orderid'));
		$stationis_open = trim(IReq::get('stationis_open')); 
		$cityid = trim(IReq::get('cityid')); 
		$checkinfoxx = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$cityid."' ");
		if(empty($checkinfoxx)){
			//构造轮播图   
			$lbdata['advtype']='weixinlb';
			$lbdata['img']="/upload/goods/indexlb.png";
			$lbdata['linkurl']='#';
			$lbdata['module'] =Mysite::$app->config['sitetemp'];
			$lbdata['is_show'] =1;
			$lbdata['cityid'] =$cityid;			 
			for($i=1;$i<=5;$i++){
				$lbdata['sort'] =$i;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'adv',$lbdata); 
			}
			 
		
			//构造分类图标 及 分类颜色  及分类背景图
			$classnamearr = array(
				'1'=>'精选美食','2'=>'快餐速食','3'=>'甜点饮品','4'=>'炸鸡汉堡','5'=>'火锅外送','6'=>'果蔬生鲜','7'=>'送药上门','8'=>'鲜花蛋糕',
				'9'=>'包子粥铺','10'=>'无辣不欢','11'=>'品牌连锁','12'=>'异国料理','13'=>'暖胃粉面','14'=>'能量西餐','15'=>'送药上门','16'=>'同城跑腿'
				);
			$defaulrinfo = $this->mysql->select_one("select name,id from ".Mysite::$app->config['tablepre']."shoptype  where parent_id>0   limit 1"); 
			for($i=1;$i<=20;$i++){
				$data['name'] = $classnamearr[$i];
				$data['img']="/upload/goods/classimg".$i.".png";
				$data['type']=2;
				$data['param']=$defaulrinfo['id'];
				$data['activity']='waimai';
				$data['is_show'] =1;
				$data['orderid'] =$i;
				$data['cityid'] =$cityid;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'appadv',$data);
			}  
			$this->mysql->delete(Mysite::$app->config['tablepre'].'stationskin',"cityid = ".$cityid.""); 
			for($i=1;$i<=2;$i++){
				$climgdata['cityid']=$cityid;
				$climgdata['imgurl']='/upload/goods/classximg'.$i.'.png';
				$climgdata['is_show']='0';
				if($i == 2){
					$climgdata['is_show']='1';
				}
				$climgdata['is_gourl']='0';
				$climgdata['type']=$i;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'stationskin',$climgdata);
			}
			//构造专题页数据
			$spdata['name'] = '商家入驻';
			$spdata['showtype'] = 0;
			$spdata['is_custom'] = 1;
			$spdata['cx_type'] = 10;
			$spdata['is_show'] = 1;
			$spdata['cityid'] = $cityid;
			$spdata['is_bd'] = 2;
			$spdata['zttype'] = 1;
			$spdata['ztystyle'] = 2;
			$this->mysql->delete(Mysite::$app->config['tablepre'].'specialpage',"cityid = ".$cityid.""); 
			$this->mysql->insert(Mysite::$app->config['tablepre'].'specialpage',$spdata); 
			$spid = $this->mysql->insertid();   
			$imgurl = Mysite::$app->config['siteurl'].'/upload/app/388880.png';
			$data1['cityid'] = $cityid;
			$data1['is_show'] = 1;
			$this->mysql->delete(Mysite::$app->config['tablepre'].'ztyimginfo',"cityid = ".$cityid.""); 
			for($x=1; $x<=3; $x++) {
				$ww = 6 - $x;
				for($aa=1; $aa<=$ww; $aa++){
					$data1['type'] = $x;
					$cn = $aa - 1; 
					$data1['sort'] = $cn;
					$data1['ztyid'] = $spid;
					$data1['indeximg'] = Mysite::$app->config['siteurl'].'/upload/app/'.$x.'8888'.$cn.'.png';
					$this->mysql->insert(Mysite::$app->config['tablepre'].'ztyimginfo',$data1);  
				}			
			}
			$checkinfox = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."ztymode where cityid='".$cityid."' ");
			if(empty($checkinfox)){
				$zmdata['cityid'] = $cityid;
				$zmdata['type'] = 1;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'ztymode',$zmdata); 
			}	
            	
		}
  
		//构造底部导航按钮
		$shangou = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$cityid."' and name = 'shangou' ");
		$say = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$cityid."' and name = 'say' ");
		$paotui = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."btninfo  where cityid = '".$cityid."' and name = 'paotui' "); 
		$ssxdata['is_show'] = 1;
		$ssxdata['cityid'] = $cityid;
		if(empty($shangou)){
			$ssxdata['name'] = 'shangou';
			$this->mysql->insert(Mysite::$app->config['tablepre'].'btninfo',$ssxdata);  
		}
		if(empty($say)){
			$ssxdata['name'] = 'say';
			$this->mysql->insert(Mysite::$app->config['tablepre'].'btninfo',$ssxdata);  
		}	
		if(empty($paotui)){
			$ssxdata['name'] = 'paotui';
			$this->mysql->insert(Mysite::$app->config['tablepre'].'btninfo',$ssxdata);  
		}			
		$ssdata['is_allow_ziti'] = $is_allow_ziti;
		if( !empty($checkinfoxx) ){			   
			$this->mysql->update(Mysite::$app->config['tablepre'].'platpsset',$ssdata,"cityid='".$cityid."'");
		}else{
			$ssdata['cityid'] = $cityid;
			$ssdata['paytype'] = '1,2';
			$ssdata['is_allow_ziti'] = '1';
			$this->mysql->insert(Mysite::$app->config['tablepre'].'platpsset',$ssdata);  
		} 
		 
		//如果不允许分站自行设置优惠促销，则删除该分站下的促销活动			
		if($is_selfsitecx == 0){
			$this->mysql->delete(Mysite::$app->config['tablepre'] . 'rule', "cityid = '$cityid'");
		}
		 
		if(empty($username)) $this->message('member_emptyname');    
		if(empty($uid)){		   	  
			if(empty($password)) $this->message('member_emptypwd'); 
			$testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where username='".$username."' ");  
			if(!empty($testinfo)) $this->message('member_repeatname'); 

			if(empty($stationname)){
			   $this->message('分站名称不能为空');
			}
			if(empty($stationusername)){
			   $this->message('分站负责人不能为空');
			}
			if(empty($stationphone)){
			   $this->message('分站负责人电话不能为空');
			} 
			if(empty($cityid)){
			   $this->message('请选择所属城市');
			}

			 

			if(empty($stationaddress)){
			   $this->message('请填写分站地址');
			}
			$arr['username'] = $username; 
			$arr['password'] = md5($password);  
			$arr['time'] = time();   
			$arr['groupid'] = 4;   
			$this->mysql->insert(Mysite::$app->config['tablepre'].'admin',$arr);  
			$stationuid = $this->mysql->insertid();   
			$adddata = array();
			$adddata['uid'] = $stationuid;
			$adddata['stationname'] = $stationname;
			$adddata['stationusername'] = $stationusername;
			$adddata['stationphone'] = $stationphone;
			$adddata['cityid'] = $cityid;
			$adddata['stationlnglat'] = $stationlnglat;
			$adddata['stationaddress'] = $stationaddress;
			$adddata['orderid'] = $orderid;
			$adddata['is_selfsitecx'] = $is_selfsitecx;
			$adddata['stationis_open'] = $stationis_open; 
			if( !empty($checkinfo) ){			   
				$this->mysql->update(Mysite::$app->config['tablepre'].'stationadmininfo',$adddata,"cityid='".$cityid."'");
			}else{
				$this->mysql->insert(Mysite::$app->config['tablepre'].'stationadmininfo',$adddata);  
			}  			 			
	    }else{
			$testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where username='".$username."' ");  
			if(empty($testinfo)) $this->message('member_editfail'); 
			if(!empty($password)){ 
			    $arr['password'] = md5($password); 
			}
			if(empty($stationname)){
			    $this->message('分站名称不能为空');
			}
			if(empty($stationusername)){
			    $this->message('分站负责人不能为空');
			}
			if(empty($stationphone)){
			    $this->message('分站负责人电话不能为空');
			}
			if(empty($stationaddress)){
			$this->message('请填写分站地址');
			}
			if( !empty($arr) ){
			$this->mysql->update(Mysite::$app->config['tablepre'].'admin',$arr,"uid='".$testinfo['uid']."'");	 
			}
			$checkcityinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."stationadmininfo where uid='".$testinfo['uid']."' ");  
			if(empty($checkcityinfo)){
				if(empty($cityid)){
					$this->message('请选择所属城市');
				}
				$checkinfo2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."stationadmininfo where cityid='".$cityid."' ");  
				$adddata = array();
				$adddata['uid'] = $testinfo['uid'];
				$adddata['stationname'] = $stationname;
				$adddata['stationusername'] = $stationusername;
				$adddata['stationphone'] = $stationphone;
				$adddata['cityid'] = $cityid;
				$adddata['stationlnglat'] = $stationlnglat;
				$adddata['stationaddress'] = $stationaddress;
				$adddata['orderid'] = $orderid;
				$adddata['is_selfsitecx'] = $is_selfsitecx;
				$adddata['stationis_open'] = $stationis_open;
				if( !empty($checkinfo2) ){
					$this->mysql->update(Mysite::$app->config['tablepre'].'stationadmininfo',$adddata,"cityid='".$cityid."'");
				}else{
					$this->mysql->insert(Mysite::$app->config['tablepre'].'stationadmininfo',$adddata); 
				}
				 
			}else{ 
				$updataarr = array();
				$updataarr['stationname'] = $stationname;
				$updataarr['stationusername'] = $stationusername;
				$updataarr['stationphone'] = $stationphone;
				$updataarr['stationlnglat'] = $stationlnglat;
				$updataarr['stationaddress'] = $stationaddress;
				$updataarr['orderid'] = $orderid;
				$updataarr['stationis_open'] = $stationis_open;
				$updataarr['is_selfsitecx'] = $is_selfsitecx;
				$this->mysql->update(Mysite::$app->config['tablepre'].'stationadmininfo',$updataarr,"uid='".$testinfo['uid']."'");  
			}
	    }
	    $this->success('success');
		   
	 }
	 //分站商家
	 function stationshoplist(){
	    $this->setstatus();
	    $where = ' and admin_id > 0 ';
	     
	    
	    $data['shopname'] =  trim(IReq::get('shopname'));
	   $data['username'] =  trim(IReq::get('username'));
	 	 $data['phone'] = trim(IReq::get('phone'));
	 	 $data['cityid'] = intval(IReq::get('cityid'));
	 	 if(!empty($data['shopname'])){
	 	 #  $where .= " and shopname='".$data['shopname']."'";
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
		   $data['buyerstatus'] = array(
		   '0'=>'待处理订单',
		   '1'=>'待发货',
		   '2'=>'订单已发货',
		   '3'=>'订单完成',
		   '4'=>'买家取消订单',
		   '5'=>'卖家取消订单'
		   );
		   $paytypelist = array(0=>'货到支付',1=>'在线支付','weixin'=>'微信支付');
		   
	     $data['shoptype'] = array(
	     '0'=>'外卖',
	     '1'=>'超市',
	     '2'=>'其他',
	     );
	     $data['ordertypearr'] = array(
		   '0'=>'网站',
		   '1'=>'网站',
		   '2'=>'电话',
		   '3'=>'微信',
		   '4'=>'AndroidAPP',
		   '5'=>'手机网站',
		   '6'=>'iosApp',
		   '7'=>'后台客服下单',
		   '8'=>'商家后台下单',
		   '9'=>'html5手机站'
		   ); 
		   $data['backarray'] = array(
		   '0'=>'',
		   '1'=>'退款中..',
		   '2'=>'退款成功',
		   '3'=>'拒绝退款'
		   );
		   
		    $data['payway'] = array(
		   'open_acout'=>'余额支付',
		   'weixin'=>'微信支付',
		   'alipay'=>'支付宝',
		   'alimobile'=>'手机支付宝'
		   );
		   
	    $data['paytypearr'] = $paytypelist;
	  	Mysite::$app->setdata($data);
	}
	 
	//获取搜索省市区编码列表
	function getcitylist(){
		$searchval = trim(IReq::get('searchval'));
		$areacodelist = array();
		if( !empty($searchval) ){
			$where = " where name like '%".$searchval."%'";
			$areacodelist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."areacode  ".$where." "); 
		}
		$this->success($areacodelist);
	}
	 
	//分站订单列表查看
	   function stationorderlist(){
        $this->setstatus();
	    	$querytype = IReq::get('querytype');
	    	$searchvalue = IReq::get('searchvalue');
	    	$orderstatus = intval(IReq::get('orderstatus'));
	    	$cityid = intval(IReq::get('cityid'));
	    	$starttime = IReq::get('starttime');
	    	$endtime = IReq::get('endtime');
	    	$nowday = date('Y-m-d',time());
	    	$starttime = empty($starttime)? $nowday:$starttime;
	    	$endtime = empty($endtime)? $nowday:$endtime;
	      $where = '  where ord.addtime > '.strtotime($starttime.' 00:00:00').' and ord.addtime < '.strtotime($endtime.' 23:59:59');
	    	$data['starttime'] = $starttime;
	    	$data['endtime'] = $endtime;
	    	$newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
	    	 $data['searchvalue'] ='';
	    	 $data['querytype'] ='';
	    	if(!empty($querytype))
	    	{
	    		if(!empty($searchvalue)){
	    			 $data['searchvalue'] = $searchvalue;
	       	   $where .= ' and '.$querytype.' LIKE \'%'.$searchvalue.'%\' ';
	       	   $newlink .= '/searchvalue/'.$searchvalue.'/querytype/'.$querytype;
	       	   $data['querytype'] = $querytype;
	    		} 
	    	}
			if(!empty($cityid))
	    	{ 
			   $data['cityid'] = $cityid;
	       	   $where .= empty($where)?' where ord.admin_id ='.$cityid:' and ord.admin_id = '.$cityid;
	       	   $newlink .= '/cityid/'.$cityid;
 	    	} 
	     
	     $data['orderstatus'] = '';

	    	if($orderstatus > 0)
	    	{
	    	   if($orderstatus  > 4)
	          {
	          	$where .= empty($where)?' where ord.status > 3 ':' and ord.status > 3 ';
	          }else{
	          	$newstatus = $orderstatus -1;
	          	$where .= empty($where)?' where ord.status ='.$newstatus:' and ord.status = '.$newstatus;
	          }
	          $data['orderstatus'] = $orderstatus;
	          $newlink .= '/orderstatus/'.$orderstatus;
	    	}
	    	$link = IUrl::creatUrl('/adminpage/station/module/stationorderlist'.$newlink);
	    	$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),5);
	    	 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
	    	 //
	    	 
	    	$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." order by ord.id desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
	    	$shuliang  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." ");
	    	$pageshow->setnum($shuliang);
	    	$data['pagecontent'] = $pageshow->getpagebar($link);
	   	$data['list'] = array();
			  if($orderlist)
			  {
				foreach($orderlist as $key=>$value)
				{
					$value['detlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where   order_id = ".$value['id']." order by id desc ");
                    $value['buyeraddress'] = urldecode($value['buyeraddress']);
					 
					$shopinfo = $this->mysql->getarr("select ziti_time from ".Mysite::$app->config['tablepre']."shop where id = ".$value['shopid']."  ");
					if($value['status'] == 0)  $ordstatus = '待处理';
					if($value['status'] == 1) $ordstatus = '待发货';
					if($value['status'] == 2) $ordstatus = '已发货';
					if($value['status'] > 3) $ordstatus = '已取消';	
					if($value['is_make'] == 0) $ordstatus = '待商家制作';					
					if($value['is_ziti'] == 1){
						if($value['is_make'] == 1) $ordstatus = '商家已接单';		
						if($value['posttime'] - time() <= $shopinfo['ziti_time']*60 )$ordstatus = '待用户自取';		
					}
					if($value['is_reback'] == 1) $ordstatus = '退款中 待平台处理';
					if($value['is_reback'] == 2) $ordstatus = '退款成功';
					if($value['is_reback'] == 4) $ordstatus = '退款中 待商家处理';
					if($value['status'] == 3) $ordstatus = '已完成';
					$value['ordstatus'] = $ordstatus;
					$data['list'][] = $value;
				}
			 }
		
	#	 print_r( $data['list'] );
	     $data['scoretocost'] =Mysite::$app->config['scoretocost'];
	     Mysite::$app->setdata($data);
	}
	
	
	 
	 
	 
	  
}