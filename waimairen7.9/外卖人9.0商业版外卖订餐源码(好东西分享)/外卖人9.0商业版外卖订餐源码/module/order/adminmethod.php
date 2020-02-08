<?php
class method   extends adminbaseclass
{
	function index(){
	 	    	 $link = IUrl::creatUrl('adminpage/order/module/orderlist');
           $this->refunction('',$link);    		   
	}
	//手动操作订单
	function doorder(){
		$dno = IReq::get('dno');
		$dotype = intval(IReq::get('dotype'));
		if(empty($dno))$this->message('请输入订单号');
		if(empty($dotype))$this->message('请选择操作类型');
		$dnoarr = explode('#',$dno); 
		foreach($dnoarr as $k=>$v){
			$cinfo = $this->mysql->select_one("select id,pstype from ".Mysite::$app->config['tablepre']."order where dno = '".$v."'"  );  
			if($cinfo['id'] > 0){
				if($dotype == 1 ){//删除订单
					$this->mysql->delete(Mysite::$app->config['tablepre'].'order'," dno = '".$v."' ");
				}elseif($dotype == 2){//完成订单
				    $data['status'] = 3;
					$data['psstatus'] = 3;
					$data['is_reback'] = 0;
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data," dno = '".$v."' ");
				}elseif($dotype == 3){//完成退款
				    $data['is_reback'] = 2;
					$data['status'] = 5;
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data," dno = '".$v."' ");
					if($cinfo['pstype'] == 2  ){			 
						$psbinterface = new psbinterface();
						if($psbinterface->psbdraworder($cinfo['id'])){
                            
						}
					}	
				}else{//结算订单
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',array('status'=>8)," dno = '".$v."' ");
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',array('status'=>3)," dno = '".$v."' ");
				}
			}	
		}
		$this->success('success');
	}
	//快速下单
	 function adminfastfoods(){
		 
		 $default_cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		$cityid	= empty($default_cityid)?0:$default_cityid;
		$where .= ' and admin_id ='.$cityid.'';
		 
   	    $data['shoplist'] = $this->mysql->getarr("select id,shopname  from ".Mysite::$app->config['tablepre']."shop where is_open = 1  ".$where." and is_pass=1 and endtime > ".time()." order by id limit 0,1000");
   	    // 营业时间	is_open 是否营业	is_pass 是否通过审核	is_recom 是否是推荐店铺 endtime

   	     Mysite::$app->setdata($data); 
   }
   
     function helpbuy(){
       
       $info = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."helpbuy " );  
       $data['info'] = $info;       
       Mysite::$app->setdata($data);     
   }
   //帮我送设置列表
    function helpmove(){      
       $info = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."helpmove " );  
       $data['info'] = $info;    
       Mysite::$app->setdata($data);     
   }
   //保存帮我买标签
   function savehelpbuybq(){   
   
	   $parentid = IReq::get('parentid');
	   if($id < 0){
	     $this->message('system_err');
	   }
	   $ids = IReq::get('ids');
	   $name = IReq::get('name');
	  
	   $ids = is_array($ids)? $ids:array($ids);
	   $name = is_array($name)?$name:array($name);
	   
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
	   	 if($tempdata['id'] > 0){
	   	 	$delids[] = $tempdata['id'];
	   	 }
	   	 $newdata[]= $tempdata;
	   }
	   
	
	 
	   if( !empty($delids) ){ 
		   $notinids = join(',',$delids);
	   }else{
		   $notinids = '';
	   } 
	
	   if(!empty($notinids)){
	   	   $this->mysql->delete(Mysite::$app->config['tablepre'].'helpbuybq',"parent_id = $parentid and id not in($notinids)");
	   }else{
	   	   $this->mysql->delete(Mysite::$app->config['tablepre'].'helpbuybq',"parent_id = $parentid");
	   }
	   if($checkdo == false) $this->message('system_err');
     
	   foreach($newdata as $key=>$value){     
	     $data['parent_id'] = $parentid;     
	     $data['name'] = $value['name'];	    
	     if($value['id']  > 0){
	       $this->mysql->update(Mysite::$app->config['tablepre'].'helpbuybq',$data,"id='".$value['id']."'");
		   }else{
                   
	       $this->mysql->insert(Mysite::$app->config['tablepre'].'helpbuybq',$data);
             
	     }
	   }
	   $this->success('success');
	}
   //保存帮我买设置     
   function savehelpbuy(){
	   
           $id = IReq::get('id');              
           $data['id'] = $id ;
	   $data['name'] = IReq::get('name');
           $data['description'] = IReq::get('description');
           $data['imgurl'] = IReq::get('img');
           $data['isnotsee'] = IReq::get('isnotsee');
           $data['orderid'] = IReq::get('orderid');
           $this->mysql->update(Mysite::$app->config['tablepre'].'helpbuy',$data,"id='".$id."'");
           $this->success('success');

   }
   //保存帮我送设置
     function savehelpmove(){
		  
           $id = IReq::get('id');              
           $data['id'] = $id ;
	   $data['name'] = IReq::get('name');          
           $data['imgurl'] = IReq::get('img');
           $data['isnotsee'] = IReq::get('isnotsee');
           $data['orderid'] = IReq::get('orderid');
           $this->mysql->update(Mysite::$app->config['tablepre'].'helpmove',$data,"id='".$id."'");
           $this->success('success');

   }
               
 /* 发布跑腿 start   */
    function okpaotuiorder(){ 
	   $id = IReq::get('id');
		if(empty($id)) $this->message('empty_ping');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paotuitask where id='".$id."'  ");  
		if(empty($checkinfo)) $this->message('empty_ping');
		$data['status'] = 2;
		$this->mysql->update(Mysite::$app->config['tablepre'].'paotuitask',$data,"id='".$id."'");  
		$this->success('success');
	}
    function quxiaopaotuiorder(){ 
	 
	   $id = IReq::get('id');
		if(empty($id)) $this->message('empty_ping');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paotuitask where id='".$id."'  ");  
		if(empty($checkinfo)) $this->message('empty_ping');
		$data['status'] = 3;
		$this->mysql->update(Mysite::$app->config['tablepre'].'paotuitask',$data,"id='".$id."'");  
		$this->success('success');
	}
   
   	 
	 function shenhaisj(){ 
	 
	   $id = IReq::get('id');
		if(empty($id)) $this->message('empty_ping');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paotuitask where id='".$id."'  ");  
		if(empty($checkinfo)) $this->message('empty_ping');
		$data['status'] = $checkinfo['status'] == 1?0:1;
		$this->mysql->update(Mysite::$app->config['tablepre'].'paotuitask',$data,"id='".$id."'");  
		$this->success('success');
	}
	 function delsjmsg(){
			 
		 $id = IFilter::act(IReq::get('id'));
			 if(empty($id))  $this->message('empty_ask');
			 $ids = is_array($id)? join(',',$id):$id;
			
			 $where = " id in($ids)";
		   $this->mysql->delete(Mysite::$app->config['tablepre'].'paotuitask',$where);
		   $this->success('success');
	   }
	   
	    /* 发布跑腿 end   */
	   
   function wavecontrol(){
     $type =  IReq::get('type');
     if($type == 'closewave'){
        //关闭声音
         ICookie::set('playwave',2,2592000);
     }else{
        //开启声音
         ICookie::set('playwave',0,2592000);
     }
     $this->success('success');
   }
   function orderlist(){
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
		if(!empty($querytype)){
			if(!empty($searchvalue)){
				$data['searchvalue'] = $searchvalue;
				$where .= ' and '.$querytype.' LIKE \'%'.$searchvalue.'%\' ';
				$newlink .= '/searchvalue/'.$searchvalue.'/querytype/'.$querytype;
				$data['querytype'] = $querytype;
			} 
		}
		if(!empty($cityid)){ 
			$data['cityid'] = $cityid;
			$where .= empty($where)?' where ord.admin_id ='.$cityid:' and ord.admin_id = '.$cityid;
			$newlink .= '/cityid/'.$cityid;
		} 
		$data['orderstatus'] = '';
		if($orderstatus > 0){
			if($orderstatus  > 4){
			    $where .= empty($where)?' where ord.status > 3 ':' and ord.status > 3 ';
			}else{
				$newstatus = $orderstatus -1;
				$where .= empty($where)?' where ord.status ='.$newstatus:' and ord.status = '.$newstatus;
			}
			$data['orderstatus'] = $orderstatus;
			$newlink .= '/orderstatus/'.$orderstatus;
		}
		$link = IUrl::creatUrl('/adminpage/order/module/orderlist'.$newlink);
		$pageshow = new page();
		$pageshow->setpage(IReq::get('page'),5);
		//order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
		//
		$default_cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		$cityid	= empty($default_cityid)?0:$default_cityid;
		$where .= ' and ord.admin_id ='.$cityid.'';
		$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." order by ord.id desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");

		$shuliang  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." ");
		$pageshow->setnum($shuliang);
		$data['pagecontent'] = $pageshow->getpagebar($link);
		$data['list'] = array();
		if($orderlist){
			foreach($orderlist as $key=>$value){
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
				if(empty($value['cxdet'])){
					$value['cxdet'] = array();
				}else{				
					$cxdet = unserialize($value['cxdet']);
					$value['cxdet'] = array();
					foreach($cxdet as $k1=>$v1){
						$vv['name'] = $v1['name'];
						if($v1['type'] == 4){
							$vv['downcost'] = $value['shopps'];
						}else{
							$vv['downcost'] = str_replace('-￥','',$v1['downcost']);
						}
						if($v1['type'] != 1){
							$value['cxdet'][] = $vv;
						} 
						
					}
				}
				$data['list'][] = $value;	
			}
		}
		$data['scoretocost'] =Mysite::$app->config['scoretocost'];

		Mysite::$app->setdata($data);
	}
	//正常订单定义打印
	function orderprint(){
		$orderid = intval(IReq::get('orderid'));
		$data['printtype'] = trim(IReq::get('printtype'));//打印类型
		$this->setstatus();
//		$data['orderinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order  where id ='".$orderid."' ");
        $data['orderinfo'] = $this->mysql->select_one("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   where ord.id ='".$orderid."' ");
		$data['orderdet'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where   order_id = ".$orderid." order by id desc ");
		
		 Mysite::$app->setdata($data);
	}
	 //跑腿订单定义打印
	function paotuiorderprint(){
		$orderid = intval(IReq::get('orderid'));
		$data['printtype'] = trim(IReq::get('printtype'));//打印类型
		$this->setstatus();
		$data['orderinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order  where id ='".$orderid."' ");
		 Mysite::$app->setdata($data);
	}
	
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
	
	function ordertoday(){
		$firstareain = IReq::get('firstarea');
		$secareain = IReq::get('secarea');
		$statustype =  intval(IReq::get('statustype'));
		$dno = IReq::get('dno');
		$data['dno'] = $dno;
		 
		$man_ispass = intval(Mysite::$app->config['man_ispass']);
		$default_statustype = $man_ispass==0?1:0;
		$statustype = in_array($statustype,array(1,2,3,4,5))?$statustype:$default_statustype;
	 
		$statustypearr = array(
		'0'=>"  and ord.status = 0   and ( ord.is_reback = 0 or  ord.is_reback = 5 )",
		'1'=>'   and ord.status = 1  and is_make = 0  and ( ord.is_reback = 0 or  ord.is_reback = 5 )',
		'2'=>'   and ord.status = 1  and is_make = 1  and ( ord.is_reback = 0 or  ord.is_reback = 5 )',
		'3'=>' and ord.status = 2 and ( ord.is_reback = 0 or  ord.is_reback = 5 )',
		'4'=>' and ord.status = 3 and ( ord.is_reback = 0 or  ord.is_reback = 5 )',
		'5'=>' and ord.is_reback > 0  and ord.is_reback !=5 ',
		);
$data['statustype'] = $statustype;
		///statustype  1   待审核
//statustype  2   待发货
//statustype  3   已发货
//statustype  4   退款处理

		$data['frinput'] = $firstareain;

		$this->setstatus();
		$nowday = date('Y-m-d',time());
	  $where = '  where      ( paytype = 0  or  (paytype=1 && paystatus=1) ) and  ord.addtime > '.strtotime($nowday.' 00:00:00').' and ord.addtime < '.strtotime($nowday.' 23:59:59');
		//查询当天所有订单数据

	 //	$where .= ' and ord.status = 0 ';
	  if(!empty($firstareain)){
	    $areainfo = $this->mysql->select_one("select adcode from ".Mysite::$app->config['tablepre']."area where id =".$firstareain." ");  	 
		$where .= " and ord.admin_id = ".$areainfo['adcode']." ";
  
	  }
	  $where .= $statustypearr[$statustype];
		//$where .= ' and ord.status = 0 ';
	  $where .= empty($dno)?'':' and ord.dno =\''.$dno.'\'';
$pageinfo = new page();
		$pageinfo->setpage(IReq::get('page'),30);
		$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." order by ord.id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
		$shuliang  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." ");
	  $data['list'] = array();
	  if($orderlist)
	  {
		  foreach($orderlist as $key=>$value)
		  {
			    $value['detlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where   order_id = ".$value['id']." order by id desc ");
			    $value['maijiagoumaishu'] = 0;
			    if($value['buyeruid'] > 0)
			    {
			    	$value['maijiagoumaishu'] =$this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$value['buyeruid']."' and  status = 3 order by id desc");
			    }
                $value['buyeraddress'] = urldecode($value['buyeraddress']);
				if(empty($value['cxdet'])){
					$value['cxdet'] = array();
				}else{				
					$cxdet = unserialize($value['cxdet']);
					$value['cxdet'] = array();
					foreach($cxdet as $k1=>$v1){
						$vv['name'] = $v1['name'];
						if($v1['type'] == 4){
							$vv['downcost'] = $value['shopps'];
						}else{
							$vv['downcost'] = str_replace('-￥','',$v1['downcost']);
						}
						if($v1['type'] != 1){
							$value['cxdet'][] = $vv;
						} 
						
					}
				}
				
			    $data['list'][] = $value;
			  
		  }
	  }
	   $pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar($newlink);
	  /*构造城市*/ 
	    $areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where id > 0 and parent_id = 0   order by orderid asc");
 	 	 $data['arealist'] = $areainfo; 

	 	 $data['showdet'] = intval(IReq::get('showdet'));
	 	 $data['playwave'] = ICookie::get('playwave'); //shoporderlist
		# print_r($data);
		 Mysite::$app->setdata($data);
	}
	 
	 function draworderinfo(){  //订单中心点击退款
	     $orderid =  IFilter::act(IReq::get('orderid'));
	     $data['oderinfo'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid." order by  id desc  limit 0,2");  
	    Mysite::$app->setdata($data); 
    }
   // 8.6 
	function systemdraworder(){ // 管理员直接在订单处 退款 ，并且生成退款记录
	    $id = intval(IReq::get('id'));
		 $type = IReq::get('type');
		 if(empty($id)) $this->message('order_noexit');
		 $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$id."'  ");
		 if(empty($orderinfo)) $this->message('order_noexit');
		switch($type){
			  case 'drawback'://退款成功
	   	      //获取退款记录
				if($orderinfo['status'] > 3){
					$this->message('订单状态不能退款');
				}
				if($orderinfo['paystatus']  != 1){
					$this->message('订单未支付');
				}
				/*if($orderinfo['is_reback']  > 0){
					$this->message('已申请退款请到退款管理里处理退款');
				}*/
				
				//直接退款限制在下单后24小时 
				if($orderinfo['status'] == 3){
					$this->message('订单已完成不能直接退款');
					/*
					$checktime = $orderinfo['sendtime']+86400;
					if($checktime < time()){ 
						$this->message('配送时间已超过24小时,退款失败');
					}
					*/
				}
				//当订单已完成 限制在多少时间
			 
			   $zengcost = $orderinfo['allcost'];
			   $is_phonenotice = IReq::get('is_phonenotice');
			   $notice_content = IReq::get('notice_content');
		 
	   	       $arr['is_reback'] = 2;//订单状态
	   	       $arr['status'] = 5;
	   	       $this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$id."'");
	 
			   if($orderinfo['paytype_name'] == 'open_acout'){
					if(!empty($orderinfo['buyeruid'])){
						$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
						if(!empty($memberinfo)){ 
							$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$zengcost,"uid ='".$orderinfo['buyeruid']."' ");
						}
						$bdliyou = $is_phonenotice==0?"管理员退款给用户":$notice_content;	
						$shengyucost = $memberinfo['cost']+$zengcost;
						$this->memberCls->addmemcostlog( $orderinfo['buyeruid'],$memberinfo['username'],$memberinfo['cost'],1,$zengcost,$shengyucost,$bdliyou,ICookie::get('adminuid'),ICookie::get('adminname') );
						$this->memberCls->addlog($orderinfo['buyeruid'],2,1,$zengcost,'退款处理',$bdliyou,$shengyucost); 
						if($is_phonenotice == 1 && !empty($memberinfo['phone']) ){
							$this->fasongmsg($notice_content,$memberinfo['phone'])  ;
							logwrite("管理员退款余额变动发送给用户成功");
						}
						
					} 
					
				}
		
			   

	   	       $ordCls = new orderclass();
			   
			   $ordCls->writewuliustatus($orderinfo['id'],19,$orderinfo['paytype']);  // 管理员退款给用户 物流信息
               $ordCls->noticeback($orderinfo['id']);	
			   
			   //退款成功给用户 下面写退款记录
			   $drawdata['uid'] = $orderinfo['buyeruid'];
			   $memberinfoone  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$drawdata['uid']."  ");
			   $drawdata['username'] = $memberinfoone['username'];			  
			   $drawdata['bkcontent'] = '平台已退款';
			   $drawdata['reason'] = '后台管理员操作退款';
			   $drawdata['content'] = '后台管理员操作退款';
			   $drawdata['addtime'] = time();
			   $drawdata['orderid'] = 	$orderinfo['id'];
			   $drawdata['shopid'] = 	$orderinfo['shopid'];
			   $drawdata['cost'] = 	 $zengcost;
			   $drawdata['status'] = 	4;
			   $drawdata['admin_id'] = 	ICookie::get('adminuid');
			   $drawdata['type'] = 1; 
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$drawdata);
			   $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$id' and status != 3");  //写配送订单 
	           if($orderinfo['pstype'] == 2){
					$psbinterface = new psbinterface();
					if($psbinterface->psbdraworder($orderinfo['id'])){

					}
				}
			  //     $ordCls->noticeback($id);
	   	  break;
	   	  case 'undrawback'://退款不成功 
				if($orderinfo['status'] > 3){
					$this->message('订单状态不能退宽');
				}
				if($orderinfo['paystatus']  != 1){
					 $this->message('订单未支付');
				}
//				if($orderinfo['is_reback'] > 0){
//					 $this->message('已退款请到退款管理里处理');
//				}
			  
				$zengcost = IReq::get('zengcost');
			   $is_phonenotice = IReq::get('is_phonenotice');
			   $notice_content = IReq::get('notice_content');
				$drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$id." order by  id desc  limit 0,2");
				 
	   	     #  if(empty($drawbacklog)) $this->message('order_emptybaklog');
	   	     #  if($drawbacklog['status'] !=  0) $this->message('order_baklogcantdoover');
	   	    #   if($orderinfo['status'] > 2) $this->message('order_cantbak');
	   	       $arr['is_reback'] = 3;//订单状态
	   	       $arr['is_make'] = 0;
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$id."'");
 
			   if($orderinfo['paytype_name'] == 'open_acout'){
					if(!empty($orderinfo['buyeruid'])){
						$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
					 
						if($is_phonenotice == 1){
							if(empty($notice_content)) $this->message("发送短信内容不能为空");	
							$this->fasongmsg($notice_content,$orderinfo['buyerphone'])  ;
							logwrite("管理员拒绝退款发送给用户成功");
						}
						
					} 
					
				} 
			   
			   
	   	       $ordCls = new orderclass();
			   
			   $ordCls->writewuliustatus($orderinfo['id'],15,$orderinfo['paytype']);  // 管理员拒绝退款给用户 物流信息   
			   //退款成功给用户 下面写退款记录
			   $drawdata['uid'] = $orderinfo['buyeruid'];
			   $memberinfoone  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$drawdata['uid']."  ");
			   $drawdata['username'] = $memberinfoone['username'];
			   $drawdata['bkcontent'] = IReq::get('reasons');
			   $drawdata['addtime'] = time();
			   $drawdata['orderid'] = 	$orderinfo['id'];
			   $drawdata['shopid'] = 	$orderinfo['shopid'];
			   $drawdata['cost'] = 	 $zengcost;
			   $drawdata['status'] = 	2;
			   $drawdata['admin_id'] = 	ICookie::get('adminuid');
			   $drawdata['type'] = 0; 
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$drawdata);
			   
	           #    $ordCls->noticeunback($id);

	   	  break;
		   default:
	   	  $this->message('nodefined_func');
	   	  break;
			
			
		}
		
		 $this->success('success');
		
	}
	function showdraworderlog(){ // 订单中心查看退款记录
		
		 $orderid =  IFilter::act(IReq::get('orderid'));
		 if(empty($orderid)) $this->message('order_noexit');
		 $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
		 if(empty($orderinfo)) $this->message('order_noexit');
		$drawbackloglist = array();
		 if($orderinfo['is_make'] == 2){
			$drawbacklog = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid = ".$orderid." and ( status = 2 or status = 4 ) order by  addtime desc");	
			foreach($drawbacklog as $k=>$val){
				$val['reason']='商家不制作订单';
				$val['content']='商家不制作订单';				
				$drawbackloglist[] = $val;
			}	
		}else{
			$drawbackloglist = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid='".$orderid."' order by addtime desc ");
		}
		 $data['drawbackloglist'] = $drawbackloglist;	 
		 Mysite::$app->setdata($data);
		
	}
	function ordercontrol()
	{
		 $id = intval(IReq::get('id'));
		 $type = IReq::get('type');
		 if(empty($id)) $this->message('order_noexit');
		 $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$id."'  ");
		 if(empty($orderinfo)) $this->message('order_noexit');
		  switch($type)
	   {
	   	  case 'un'://关闭订单
	   	      $reasons = IReq::get('reasons');
	   	      $suresend =  IReq::get('suresend'); 
	   	      if(empty($reasons)) $this->message('order_emptyclosereason');
	   	      if($orderinfo['status'] > 2)  $this->message('order_cantclose');
			  if($orderinfo['paystatus'] == 1 && $orderinfo['paytype'] != 0){
				  $this->message('在线支付订单请通过退款处理');
			  }
			  if($orderinfo['re_back'] > 0){
				  $this->message('有退款处理不能关闭');
			  } 
	   	      //更新订单	 
	   	       //写消息给买家
	   	      if(!empty($orderinfo['buyeruid']))
	   	      {
	   	      	   $detail = '';
	   	      	   $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
		             if($orderinfo['paystatus'] == 1&& $orderinfo['paytype'] != 0){
		 	            //将订单还给买家账号
		 	              /*
		 	              if(!empty($memberinfo)){
		 	               $this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$orderinfo['allcost'].',`score`=`score`+'.$orderinfo['scoredown'],"uid ='".$orderinfo['buyeruid']."' ");
		 	                $detail = '，账号余额增加'.$orderinfo['allcost'].'元';
		 	              }
		 	              $membersc = $memberinfo['score']+$orderinfo['allcost'];
		 	              $this->memberCls->addlog($orderinfo['buyeruid'],2,1,$orderinfo['allcost'],'取消订单','管理员关闭订单'.$orderinfo['dno'].'已支付金额'.$orderinfo['allcost'].'返回帐号',$membersc);
		 	              */
		 	              $this->message('order_ispaycantdo');
		 	              if($orderinfo['scoredown'] > 0){
		 	              	$memberscs = $memberinfo['score']+$orderinfo['scoredown'];
		                   $this->memberCls->addlog($orderinfo['buyeruid'],1,1,$orderinfo['scoredown'],'取消订单','管理员关闭订单'.$orderinfo['dno'].'抵扣积分'.$orderinfo['scoredown'].'返回帐号',$memberscs);
		                }
		             }elseif($orderinfo['scoredown']> 0){
		             	 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$orderinfo['scoredown'],"uid ='".$orderinfo['buyeruid']."' ");
		             	 $memberscs = $memberinfo['score']+$orderinfo['scoredown'];
		                $this->memberCls->addlog($orderinfo['buyeruid'],1,1,$orderinfo['scoredown'],'取消订单','管理员关闭订单'.$orderinfo['dno'].'抵扣积分'.$orderinfo['scoredown'].'返回帐号',$memberscs);
		             }
	   	      }
	   	      $orderdata['status'] = 5;
	   	       $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
			   
			   
			    $ordCls = new orderclass();
			   if(  $orderinfo['paystatus'] == 1   ){ 
					   $ordCls->writewuliustatus($orderinfo['id'],5,$orderinfo['paytype']);  // 管理员 操作  取消订单
			   }else{
				   $ordCls->writewuliustatus($orderinfo['id'],16,$orderinfo['paytype']);  // 管理员 操作  取消订单
			   } 
			    $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '".$orderinfo['id']."' and status !=3 ");  //写配送订单 
	   	      //还库存
	   	      $ordetinfo = $this->mysql->getarr("select ort.goodscount,go.id,go.sellcount,go.count as stroe from ".Mysite::$app->config['tablepre']."orderdet as ort left join  ".Mysite::$app->config['tablepre']."goods as go on go.id = ort.goodsid   where ort.order_id='".$id."' and  go.id > 0 ");
	          foreach($ordetinfo as $key=>$value)
		        {
			            $newdata['count'] = $value['stroe']+	$value['goodscount'];
			            $newdata['sellcount'] = $value['sellcount'] - $value['goodscount'];
			            $this->mysql->update(Mysite::$app->config['tablepre']."goods",$newdata,"id='".$value['id']."'");
		        }
				 $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$id' and status !=3 ");  //写配送订单 
				//删除配送单
		        if($suresend == 1){
		        	//短信发送
		        	   $ordCls = new orderclass();
	               $ordCls->noticeclose($id,$reasons);
	          }
			   if($orderinfo['pstype'] == 2){
					$psbinterface = new psbinterface();
					if($psbinterface->psbdraworder($orderinfo['id'])){

					}
				}
	   	  break;
	   	  case 'pass':
	   	     if($orderinfo['status'] != 0)  $this->message('order_cantpass');
	   	     if($orderinfo['is_reback'] > 0 && $orderinfo['is_reback'] < 3) $this->message('order_ispaycantdo'); 
	   	       $checkstr = Mysite::$app->config['auto_send'];
	   	       $is_autopreceipt = $this->mysql->select_one("select is_autopreceipt from ".Mysite::$app->config['tablepre']."shop where id='".$orderinfo['shopid']."' ");
			   #$allowed_is_make = Mysite::$app->config['allowed_is_make']; //  1商家必须确认制作 0订单则默认通过制作
	   	       $man_ispass = Mysite::$app->config['man_ispass'];
			   $checkflag = false;
			   if($man_ispass == 1){
					   if($is_autopreceipt['is_autopreceipt'] == 1){
						   $orderdata['is_make'] = 1;
						   if($checkstr == 888){
							   
							   $orderdata['status'] = 2; 
							   $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
							    
								  $ordCls = new orderclass();
								  $ordCls->writewuliustatus($orderinfo['id'],4,$orderinfo['paytype']);  //订单审核后自动 商家接单					  
								  $ordCls->writewuliustatus($orderinfo['id'],6,$orderinfo['paytype']);//订单审核后自动 商家接单后自动发货
							      $orderdatac['sendtime'] = time(); 
								  $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdatac,"id ='".$id."' ");
								 			
						   }else{
							    
							   
							   //自动生成配送单------|||||||||||||||-----------------------
								if($orderinfo['pstype'] == 0 && $orderinfo['is_goshop'] == 0){//网站配送自动生成配送费
								  $psdata['orderid'] = $orderinfo['id'];
								  $psdata['shopid'] = $orderinfo['shopid'];
								  $psdata['status'] =0;
								  $psdata['dno'] = $orderinfo['dno'];
								  $psdata['addtime'] = time();
								  $psdata['pstime'] = $orderinfo['posttime'];
								  $admin_id = $orderinfo['admin_id'];
                                  $psset = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$admin_id."'   ");
                                  $checkpsyset = $psset['psycostset'];
                                  $bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
                                  $psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei;

								  $this->mysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单 
								  $checkflag = true;
								}elseif($orderinfo['pstype'] == 2){ 
								   $psbinterface = new psbinterface();
								   if($psbinterface->psbnoticeorder($orderinfo['id'])){

								    } 
								}
							    //自动生成配送单结束-------------
								$orderdata['status'] = 1;
							    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
							    $ordCls = new orderclass();
							    $ordCls->writewuliustatus($orderinfo['id'],4,$orderinfo['paytype']);  //订单审核后自动 商家接单 
								
								
								
								
						   }
					   }else{
						     
								$orderdata['status'] = 1;
							 $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
					   }
					
				}else{
					$orderdata['status'] = 1;
							 $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
				}
	          if(Mysite::$app->config['man_ispass']  == 1)
	          {
	          	  $ordCls = new orderclass();
	               $ordCls->sendmess($id);
	          }
			  if($checkflag ==true){
				    $psylist =  $this->mysql->getarr("select a.*  from ".Mysite::$app->config['tablepre']."apploginps as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.admin_id = ".$orderinfo['admin_id']."");
			 	    $psCls = new apppsyclass(); 
					$psCls->SetUserlist($psylist)->sendNewmsg('订单提醒','有新订单可以处理'); 
			  }
	   	  break;
	   	  case 'send':
	   	      if($orderinfo['is_reback'] > 0 && $orderinfo['is_reback'] < 3) $this->message('order_bakpaycantdo');
	   	      if($orderinfo['status'] != 1)  $this->message('order_cantsend');
			  if($orderinfo['is_make'] == 0 && $orderinfo['pttype'] == 0) $this->message('等待商家确认制作后才能发货');
			  if($orderinfo['is_make'] == 2) $this->message('商家不制作该订单不能发货');
			  //if($orderinfo['pstype'] == 1) $this->message('商品发货订单请通过商家管理发货');
			  if($orderinfo['is_goshop']  !=1){
				if($orderinfo['pstype'] == 2) $this->message('第三方取货后自动发货');
			  }
			  if($orderinfo['shoptype'] == 100){//网站配送自动生成配送费
			      if($orderinfo['psuid'] == 0){
					  $this->message('请将跑腿订单分配给送货员才能发货');
				  }
			  }
	   	      $orderdata['status'] = 2;
	   	      $orderdata['sendtime'] = time();
	   	      $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
			  $cdata['status'] = 2;
			  $this->mysql->update(Mysite::$app->config['tablepre'].'orderps',$cdata,"orderid='".$id."'"); 
	   	      $ordCls = new orderclass();
			  $ordCls->writewuliustatus($orderinfo['id'],6,$orderinfo['paytype']);  // 管理员 操作配送发货
	          $ordCls->noticesend($id);
	   	  break;
	   	  case 'over':
	   	      if($orderinfo['is_reback'] > 0 && $orderinfo['is_reback'] < 3) $this->message('order_bakpaycantdo');
			  if( $orderinfo['is_goshop'] != 1 ){
				if($orderinfo['status'] != 2)  $this->message('order_cantover');
			  } 
			  
			  //分销返佣
			$is_open_distribution = Mysite::$app->config['is_open_distribution'];
			if($is_open_distribution == 1){
				$distribution = new distribution();
				if($distribution->operateorder($orderinfo['id'])){
					 
				}else{
					$err = $distribution->Error();
					logwrite('返佣失败，失败原因：'.$err);
				}
			}
			  
			  
			  $ordCls = new orderclass();
			  $ordCls->writewuliustatus($orderinfo['id'],9,$orderinfo['paytype']);  // 管理员 操作 完成订单
			  
			  //更新商品库存  
			  //----  直接完成配送单
	   	      $orderdata['is_acceptorder'] = 1;
	   	      $orderdata['status'] = 3;
	   	      $orderdata['suretime'] = time();
	   	     if($orderinfo['paytype']==0){
				$orderdata['paystatus'] = 1;
				$orderdata['paytime'] = time();
			 }
 				/* 记录配送员送达时候坐标 */
				if(  $orderinfo['psuid'] > 0 ){
					if(  $orderinfo['pstype'] == 0 ){
						$psylocationonfo = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$orderinfo['psuid']."' ");
						if(!empty($psylocationonfo)){
							 $orderdata['psyoverlng'] = $psylocationonfo['lng'];
							 $orderdata['psyoverlat'] = $psylocationonfo['lat'];
						}
					}
					if(  $orderinfo['pstype'] == 2 ){
						$psbinterface = new psbinterface(); 
						$psylocationonfo = $psbinterface->getpsbclerkinfo($orderinfo['psuid']);
						if( !empty($psylocationonfo) && !empty($psylocationonfo['posilnglat']) ){
							$posilnglatarr = explode(',',$psylocationonfo['posilnglat']);
							$posilng = $posilnglatarr[0];
							$posilat = $posilnglatarr[1];
							if( !empty($posilng) && !empty($posilat)  ){
								 $orderdata['psyoverlng'] = $posilng;
								 $orderdata['psyoverlat'] = $posilat;
							} 
						}
					}
				}
				
				 
				
	   	       $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
			   
				 //更新销量 
				$shuliang  = $this->mysql->select_one("select sum(goodscount) as sellcount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."'  ");
				if(!empty($shuliang) && $shuliang['sellcount'] > 0){
					$this->mysql->update(Mysite::$app->config['tablepre'].'shop','`sellcount`=`sellcount`+'.$shuliang['sellcount'].'',"id ='".$orderinfo['shopid']."' ");
				}
				
				//自动完成配送单
				$this->mysql->update(Mysite::$app->config['tablepre'].'orderps','`status`=3',"orderid ='".$orderinfo['id']."' ");
			
			  
			   
			   
	   	       //更新用户成长值
	   	       if(!empty($orderinfo['buyeruid']))
	   	       {
	   	      	   $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
//		             if(!empty($memberinfo)){
//		             	 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`total`=`total`+'.$orderinfo['allcost'],"uid ='".$orderinfo['buyeruid']."' ");
//		              }
                   if(!empty($memberinfo)){
                       $data['total']=$memberinfo['total']+$orderinfo['allcost'];
                       $data['score'] = $memberinfo['score']+Mysite::$app->config['consumption'];
                       if(Mysite::$app->config['con_extend'] > 0){
                           $allscore= $orderinfo['allcost']*Mysite::$app->config['con_extend'];
                           $data['score']+=$allscore;
                           $consumption=$allscore;
                       }
                       if(!empty($consumption)){
                           $consumption+=Mysite::$app->config['consumption'];
                       }else{
                           $consumption=Mysite::$app->config['consumption'];
                       }
                       $this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid ='".$orderinfo['buyeruid']."' ");
                       if($consumption > 0){
                           $this->memberCls->addlog($orderinfo['buyeruid'],1,1,$consumption,'消费送积分','消费送积分'.$consumption,$data['score']);
                       }
                   }
		              /*
		               // 写优惠券
		              */
		              if($memberinfo['parent_id'] > 0){

		                 $upuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$memberinfo['parent_id']."'   ");
		                 if(!empty($upuser)){
		                 	 if(Mysite::$app->config['tui_juan'] ==1){
		                  $nowtime = time();
						   $endtime = $nowtime+Mysite::$app->config['tui_juanday']*24*60*60;
						   $juandata['card'] = $nowtime.rand(100,999);
						   $juandata['card_password'] =  substr(md5($juandata['card']),0,5);
						   $juandata['status'] = 1;// 状态，0未使用，1已绑定，2已使用，3无效
						   $juandata['creattime'] = $nowtime;// 制造时间
						   $juandata['cost'] = Mysite::$app->config['tui_juancost'];// 优惠金额
						   $juandata['limitcost'] =  Mysite::$app->config['tui_juanlimit'];// 购物车限制金额下限
						   $juandata['endtime'] = $endtime;// 失效时间
						   $juandata['uid'] = $upuser['uid'];// 用户ID
						   $juandata['username'] = $upuser['username'];// 用户名
						   $juandata['name'] = '推荐送优惠券';//  优惠券名称
						   $this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$juandata);
							$this->mysql->update(Mysite::$app->config['tablepre'].'member','`parent_id`=0',"uid ='".$orderinfo['buyeruid']."' ");
							$logdata['uid'] = $upuser['uid'];
							$logdata['childusername'] = $memberinfo['username'];
							$logdata['titile'] = '推荐送优惠券';
							$logdata['addtime'] = time();
							$logdata['content'] = '被推荐下单完成';
							$this->mysql->insert(Mysite::$app->config['tablepre'].'sharealog',$logdata);
	 	                     }
	 	                 }

		            }
					if($orderinfo['shoptype'] != 100){
						if($ordCls->sendWxMsg($orderinfo['id'],7,1)){
							
						}
					}else{
						if($ordCls->sendWxMsg($orderinfo['id'],6,3)){
							
						}
					}
					if($ordCls->sendWxMsg($orderinfo['id'],2,2)){
						
					}					
	   	       }
	   	  break;
	   	  case 'del':
	   	      if($orderinfo['status'] < 4)  $this->message('order_cantdel');
	   	      $this->mysql->delete(Mysite::$app->config['tablepre'].'order',"id = '$id'");
			   $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$id' and status != 3");  //写配送订单 
			  //删除配送单
	   	  break;
	   	  case 'drawback'://退款成功
               if($orderinfo['is_reback'] == 2) $this->message('订单已退款成功不能重复操作');
               if($orderinfo['is_reback'] == 5) $this->message('用户已取消退款申请');			   
			   $admincon = IReq::get('reasons');
			   if($orderinfo['is_make'] == 2){
				   $drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$id." and status = 2");
			   }else{
				   $drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$id." and status = 0");
			   }
			   
	   	       if(empty($drawbacklog)) $this->message('order_emptybaklog');
	   	       if( $orderinfo['paytype'] == 1 &&  $orderinfo['paystatus'] == 1 ){
                   
					if($orderinfo['shoptype'] != 100){
						$drawbacklog = new drawbacklog($this->mysql,$this->memberCls);			 			 
						$kkdata = array('allcost'=>$orderinfo['allcost'],'reason'=>$admincon,'orderid'=>$orderinfo['id'],'typeid'=>'1','status'=>'4','uid'=>$orderinfo['buyeruid']); 		 
						$aa = $drawbacklog->setsavedraw($kkdata)->save();  
                        if($aa){}else{
							$msg = $drawbacklog->GetErr();
							$this->message($msg);
						}						
					}else{						 
						if($orderinfo['paytype_name'] == 'open_acout'){
							if(!empty($orderinfo['buyeruid'])){		 
								$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   "); 
								$memclas = new memberclass($this->mysql);	
								if(!empty($memberinfo)){
									$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$orderinfo['allcost'],"uid ='".$orderinfo['buyeruid']."' ");			 
								}	
								$shengyucost = $memberinfo['cost']+$orderinfo['allcost']; 
								$memclas->addmemcostlog( $orderinfo['buyeruid'],$memberinfo['username'],$memberinfo['cost'],1,$orderinfo['allcost'],$shengyucost,"管理员退款给用户",ICookie::get('adminuid'),ICookie::get('adminname') );				 
								$memclas->addlog($orderinfo['buyeruid'],2,1,$orderinfo['allcost'],'退款处理','用户取消跑腿订单',$shengyucost);  
							} 
						}	
						$orderClass = new orderclass();
						$orderClass->writewuliustatus($orderinfo['id'],14,$orderinfo['paytype']);   
						$this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>2,'status'=>4),"id='".$orderinfo['id']."'");
						$data['uid'] = $orderinfo['buyeruid'];
						$data['username'] = $orderinfo['buyername']; 
						$data['orderid'] = $orderinfo['id'];
						$data['shopid'] = $orderinfo['shopid'];		
						$data['status'] = 4;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
						$data['addtime'] = time();
						$data['cost'] = $orderinfo['allcost'];
						$data['admin_id'] = $orderinfo['admin_id'];
						$data['type'] = 1;
						$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);	
						$orderClass->noticeback($orderinfo['id']);
					}				   
					 					 
				} 
				if($orderinfo['pstype'] == 2){
					$psbinterface = new psbinterface();
					if($psbinterface->psbdraworder($orderinfo['id'])){

					}
				}
				//还库存
	   	      $ordetinfo = $this->mysql->getarr("select ort.goodscount,go.id,go.sellcount,go.count as stroe from ".Mysite::$app->config['tablepre']."orderdet as ort left join  ".Mysite::$app->config['tablepre']."goods as go on go.id = ort.goodsid   where ort.order_id='".$orderinfo['id']."' and  go.id > 0 ");
	          foreach($ordetinfo as $key=>$value)
		        {
						if($value['product_id'] > 0){
							 $this->mysql->update(Mysite::$app->config['tablepre'].'product',"`stock` = `stock`+".$value['goodscount'],"id='".$value['product_id']."'");
						}
			            $newdata['count'] = $value['stroe']+	$value['goodscount'];
			            $newdata['sellcount'] = $value['sellcount'] - $value['goodscount'];
			            $this->mysql->update(Mysite::$app->config['tablepre']."goods",$newdata,"id='".$value['id']."'");
		        }
	   	  break;
	   	  case 'undrawback'://退款不成功
				$zengcost = IReq::get('zengcost');
			   $is_phonenotice = IReq::get('is_phonenotice');
			   $notice_content = IReq::get('notice_content');
	   	       $drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$id." order by  id desc  limit 0,2");
	   	       if(empty($drawbacklog)) $this->message('order_emptybaklog');
	   	       if($drawbacklog['status'] !=  0) $this->message('order_baklogcantdoover');
			   if($orderinfo['is_reback'] == 2) $this->message('订单已退款成功不能重复操作');
	   	       if($orderinfo['status'] > 3) $this->message('订单已取消不能操作');
	   	       $arr['is_reback'] = 3;//订单状态
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$id."'");
	   	       $data['bkcontent'] = IReq::get('reasons');
	   	       $data['status'] = 2;//
			   $data['admin_id'] = ICookie::get('adminuid');
	   	       $this->mysql->update(Mysite::$app->config['tablepre'].'drawbacklog',$data,"id='".$drawbacklog['id']."'"); 
			  
			   if($orderinfo['paytype_name'] == 'open_acout'){
					if(!empty($orderinfo['buyeruid'])){
						$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'  ");
					 
						if($is_phonenotice == 1){
							if(empty($notice_content)) $this->message("发送短信内容不能为空");	
							$this->fasongmsg($notice_content,$orderinfo['buyerphone']);
							logwrite("管理员拒绝退款发送给用户成功");
						}
						
					} 
					
				} 
			   
			   
	   	       $ordCls = new orderclass();
			   
			   $ordCls->writewuliustatus($orderinfo['id'],15,$orderinfo['paytype']);  // 管理员拒绝退款给用户 物流信息
			   
	           #    $ordCls->noticeunback($id);

	   	  break;
	   	  case 'psyuan':
			   if($orderinfo['is_goshop'] == 1) $this->message('到店买单订单不需要配送');
	   	       if($orderinfo['status'] > 2)  $this->message('order_baklogcantdoover'); 
	   	       if(!empty($orderinfo['psuid'])) $this->message('order_setpsyuan');
	   	       $userid = intval(IReq::get('userid'));
	   	       if(empty($userid)) $this->message('order_emptypsyuan');
	   	       $memberinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$userid."' and `group` =2 ");
	   	      
	   	       if(empty($memberinfo)) $this->message('order_emptypsyuan');
	   	      
			    //自动生成配送单------|||||||||||||||-----------------------
				if($orderinfo['shoptype'] == 100){//网站配送自动生成配送费
//					$checkpsset =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$orderinfo['admin_id']."' ");
//					if(empty($checkpsset) || $checkpsset['pttopsb'] == 1){
//						$this->message('已分配到第三方配送系统');
//					}else{
						$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderinfo['id']."' "); 
						if(!empty($checkinfo)){
							$this->message('已经生成配送单不能重复生成');
						} 
						$orderdata['psuid'] = $memberinfo['uid'];
						$orderdata['psusername'] = $memberinfo['username'];
						$orderdata['psemail'] = $memberinfo['email'];
						$orderdata['is_make'] = 1;
						if($orderinfo['paytype'] == 1 && $orderinfo['paystatus'] == 0){
						   $this->message('请待用户支付再选择配送员');
						}
						$this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'"); 
						$psdata['orderid'] = $orderinfo['id'];
						$psdata['shopid'] = $orderinfo['shopid'];
						$psdata['status'] =1;
						$psdata['dno'] = $orderinfo['dno'];
						$psdata['addtime'] = time();
						$psdata['pstime'] = $orderinfo['posttime']; 
						$psdata['psuid'] = $memberinfo['uid'];
						$psdata['psusername'] = $memberinfo['username'];
						$psdata['psemail'] = $memberinfo['email'];
						$psdata['picktime'] = time();
						$admin_id = $orderinfo['admin_id'];
                                                $psset = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$admin_id."'   ");
                                                $checkpsyset = $psset['psycostset'];
                                                $bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
                                                $psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei; 
						$this->mysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单
						  //自动生成配送单结束-------------
						$statusdata['orderid']    =  $orderinfo['id'];
						$statusdata['statustitle'] =  "配送员已抢单";
						$statusdata['ststusdesc']  =  $memberinfo['username'].'抢单成功,联系电话'.$memberinfo['phone']; 
						$statusdata['addtime']     =  time();
						 
						$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);  
//					}
				}else{ 
					if($orderinfo['pstype'] == 2){
						 $this->message('第三方配送系统配送');
					}	
					if($orderinfo['paytype'] == 1 && $orderinfo['paystatus'] == 0){
					   $this->message('请待用户支付再选择配送员');
					}
					if($orderinfo['psuid']> 0){
						  $this->message('订单已被抢');
					}
					$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderinfo['id']."' "); 
					if(empty($checkinfo)){
						$this->message('订单未生成配送单不能分配');
					}
					 
					 $data['psuid'] = $memberinfo['uid'];
					 $data['psusername'] = $memberinfo['username']; 
					 $data['psemail'] = $memberinfo['email'];
					 $this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$orderinfo['id']."' and (psuid = 0 or psuid is null)");
				   
					 $statusdata['orderid']    = $orderinfo['id'];
				     $statusdata['statustitle'] =  "配送员已抢单";
					 $statusdata['ststusdesc']  =  $data['psusername'].'抢单成功,联系电话'.$memberinfo['phone']; 
					 $statusdata['addtime']     =  time();
					 $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);  
					 $psdata['psuid'] = $memberinfo['uid'];
					 $psdata['psusername'] = $memberinfo['username'];
					 $psdata['psemail'] = $memberinfo['email'];
					 $psdata['status'] =1;
					 $psdata['picktime'] = time();
					 $this->mysql->update(Mysite::$app->config['tablepre'].'orderps',$psdata,"orderid='".$orderinfo['id']."'");
					
				}
			   //memberinfo
			    $psinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."apploginps where uid='".$memberinfo['uid']."'   "); 
					
				$psCls = new apppsyclass();
				$tempuser[] = $psinfo;
				$psCls->SetUserlist($tempuser)->sendNewmsg('订单提醒','有订单分配到您账号处理'); 
			break;
		   case 'resend':
				// if($orderinfo['psbflag'] == 1){
					// $this->message('状态不能重新发布');
				// } 
				if($orderinfo['pstype'] == 2){ 
					$psbinterface = new psbinterface();
					if($orderinfo['shoptype'] == 100){
						if($psbinterface->paotuitopsb($orderinfo['id'])){
						
						}else{
							$this->message($psbinterface->err());
						}
					}else{
						if($psbinterface->psbnoticeorder($orderinfo['id'])){
						 
						}else{
							$this->message($psbinterface->err());
						}
					}
					
				}
			   
		  break;
	   	  default:
	   	  $this->message('nodefined_func');
	   	  break;
	   	}

		 $this->success('success');
		//返回json_code;
	}
	 function fasongmsg($notice_content,$phone){
	   $contents = $notice_content;     
		$phonecode = new phonecode($this->mysql,0,$phone);
		if(strlen($contents) > 498){ 
			$content1 = substr($contents,0,498);
			$phonecode->sendother($content1);  
			$content2 = substr($contents,498,strlen($contents));
			$phonecode->sendother($content2);  
		}else{
			$phonecode->sendother($contents); 
		} 
   }
	function ajaxcheckorder(){
	  $data = array();
		$nowday = date('Y-m-d',time());

	  $where = '  where ord.addtime > '.strtotime($nowday).' and ord.addtime < '.strtotime($nowday.' 23:59:59');
		 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
		$where .= ' and ord.status < 2 ';

		$firstarea = intval(IReq::get('firstarea'));


		if(!empty($firstareain)){
	      $where .= " and FIND_IN_SET('".$firstareain."',`areaids`)";
	  }
		$shuliang1  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."order as ord left join ".Mysite::$app->config['tablepre']."shop as sp on ord.shopid = sp.id ".$where." and (ord.is_make = 0 or (ord.is_make = 1 and sp.is_autopreceipt = 1)) and ( paytype = 0  or  (paytype=1 && paystatus=1) )");
		$shuliang2  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."paotuitask as ord   ".$where." ");
		$shuliang = $shuliang1+$shuliang2;
		if($shuliang > 0){
			$this->success('success');
		}else{
			$this->message('success');
		}
	}
	function outorderlimit(){
		$outtype = IReq::get('outtype');
		if(!in_array($outtype,array('query','ids')))
		{
		  	header("Content-Type: text/html; charset=UTF-8");
			 echo '查询条件错误';
			 exit;
		}
		$where = '';
 		if($outtype == 'ids')
		{
			  $id = trim(IReq::get('id'));
			  if(empty($id))
			  {
			  	 header("Content-Type: text/html; charset=UTF-8");
			  	 echo '查询条件不能为空';
			  	 exit;
			  }
			   $doid = explode('-',$id);
			  $id = join(',',$doid);
			  $where .= ' where ord.id in('.$id.') ';
		}else{
		   $starttime = intval(IReq::get('starttime'));
		   $endtime = intval(IReq::get('endtime'));
		   $status = intval(IReq::get('status'));
		   $where .= '  where ord.posttime > '.$starttime.' and ord.posttime < '.$endtime;
		   if(!empty($status))
		   {
		   	 $where .= ' and ord.status ='.$status.'';
		    }else{
		     $where .= ' and ord.status > 1  and ord.status < 4 ';
		   }

		}

	
		$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." order by ord.id desc limit 0,1000");

	  $print_rdata = array();
	  if($orderlist)
	  {
		  foreach($orderlist as $key=>$value)
		  {
			    $detlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where   order_id = ".$value['id']." order by order_id desc ");
			    //id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername 购买热名称 buyeraddress 联系地址  联系电话 buyertemphone 备用电话 sendtime 配送时间要求 sendcost 配送费用 cost 总价 addtime 制造时间 status 状态 paytype 支付类型outpay货到支付，open_acout账户余额支付，other调用paylist paystatus 支付状态1已支付 content 订单备注 allcost
			    if(is_array($detlist)){
			       foreach($detlist as $keys=>$val){
			         // id  order_id  goodsid  goodsname      status  shopid  is_send 是否是赠品 1赠品
			           $newdata = array();
			    	     $newdata['dno'] = $value['dno'];
			    	     $newdata['shopname'] = $value['shopname'];
			    	     $newdata['area1'] = $value['area1'];
			    	     $newdata['area2'] = $value['area2'];
			    	     $newdata['goodsname'] = $val['goodsname'];
			    	     $newdata['goodscount'] = $val['goodscount'];
			    	     $newdata['goodscost'] = $val['goodscost'];
			    	     $newdata['buyerphone'] = $value['buyerphone'];
			    	     $newdata['sendtime'] = $value['sendtime'];
			    	     $newdata['buyeraddress'] = $value['buyeraddress'];
			    	     $newdata['buyername'] = $value['buyername'];
			    	      $newdata['content'] = $value['content'];
			    	     $print_rdata[] = $newdata;
			      }
			    }

		  }
	  }


	 // array('下单用户','店铺名','地址1','地址2','订单详情','商品数量','单价','联系电话','送餐时间','详细地址','备注');


		 $outexcel = new phptoexcel();
		 $titledata = array('订单编号','下单用户','店铺名','地址1','地址2','商品名称','商品数量','单价','联系电话','送餐时间','详细地址','备注');
		 $titlelabel = array('dno','buyername','shopname','area1','area2','goodsname','goodscount','goodscost','buyerphone','sendtime','buyeraddress','content');


		// $datalist = $this->mysql->getarr("select card,card_password,cost from ".Mysite::$app->config['tablepre']."card where id > 0 ".$where."   order by id desc  limit 0,2000 ");
		 $outexcel->out($titledata,$titlelabel,$print_rdata,'','订单导出');

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
		   '1'=>'退款中,待平台处理',
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
	 function saveorderbz(){
	 	 $this->checkadminlogin();
	 		$arrtypename = IReq::get('typename');
			$arrtypename = is_array($arrtypename) ? $arrtypename:array($arrtypename);
		  $siteinfo['orderbz'] =   serialize($arrtypename);
		  $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);
	    $this->success('success');
	 }
	  function savedrawsm(){
	 	 $this->checkadminlogin();
	 		$arrtypename = IReq::get('typename');
			$arrtypename = is_array($arrtypename) ? $arrtypename:array($arrtypename);
		  $siteinfo['drawsmlist'] =   serialize($arrtypename);
		  $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);
	    $this->success('success');
	 }
	function ordercomment(){
	   $searchvalue = IReq::get('searchvalue');
	   $querytype = IReq::get('querytype');
	   $newlink = '';
	   $where = '';
	   $data['searchvalue'] = '';
	   $data['querytype'] = '';
	   if(!empty($querytype))
	   {
	   	 if(!empty($searchvalue))
	   	 {
	   	   $data['searchvalue'] = $searchvalue;
	   	   $where .= ' where '.$querytype.' LIKE \'%'.$searchvalue.'%\' ';
	   	   $newlink = IUrl::creatUrl('adminpage/order/module/ordercomment/searchvalue/'.$searchvalue.'/querytype/'.$querytype);
	   	   $data['querytype'] = $querytype;
	   	 }
	   }
	  $pageinfo = new page();
    $pageinfo->setpage(IReq::get('page'));
    //comment:  id  orderid  orderdetid  shopid  goodsid  uid  content  addtime  replycontent  replytime  point 评分 is_show
    //orderdet: id  order_id  goodsid  goodsname  goodscost  goodscount  status  shopid
		$list = $this->mysql->getarr("select com.*,sh.shopname,b.username,ort.goodsname from ".Mysite::$app->config['tablepre']."comment  as com left join ".Mysite::$app->config['tablepre']."member as b on com.uid = b.uid left join ".Mysite::$app->config['tablepre']."shop as sh on sh.id = com.shopid left join ".Mysite::$app->config['tablepre']."orderdet as ort on ort.id = com.orderdetid ".$where." order by com.id desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
		$data['list'] = array();
		foreach($list as  $key=>$value){
			if( !empty($value['virtualname']) ){
				$value['username'] = $value['virtualname'];
				$goodsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods   where id = '".$value['goodsid']."'   ");
				if( empty($goodsinfo) ){
					$goodsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."product   where goodsid = '".$value['goodsid']."'   ");
					$value['goodsname'] = $goodsinfo['goodsname'].'【'.$attrname.'】'.'【刷】';
				}else{
					$value['goodsname'] = $goodsinfo['name'].'【刷】';
				}
				
			}
			$data['list'][] = $value;
		}
		 
		 
		 
		$shuliang  = $this->mysql->counts("select com.id from ".Mysite::$app->config['tablepre']."comment  as com left join ".Mysite::$app->config['tablepre']."member as b on com.uid = b.uid left join ".Mysite::$app->config['tablepre']."shop as sh on sh.id = com.shopid left join ".Mysite::$app->config['tablepre']."orderdet as ort on ort.id = com.orderdetid ".$where);
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar($newlink);
		 Mysite::$app->setdata($data);
	}
	function delcommlist()
	{
	   $id = IReq::get('id');
		 if(empty($id))  $this->message('评论为空');
		 $ids = is_array($id)? join(',',$id):$id;
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'comment'," id in($ids) ");
	   $this->success('success');
	}
	
  function autodel(){
      $dayminitime = time();
      $this->mysql->delete(Mysite::$app->config['tablepre'].'orderdet',"order_id in(select id from  ".Mysite::$app->config['tablepre']."order where status in(0,4,5) and paystatus != 1   and posttime < ".$dayminitime." order by id desc )");
      $this->mysql->delete(Mysite::$app->config['tablepre'].'order',"status in(0,4,5) and paystatus != 1 and  posttime < ".$dayminitime);
      $this->mysql->update(Mysite::$app->config['tablepre'].'order','`status`=2'," is_reback =0 and status = 1 and posttime < ".$dayminitime."");
      $this->mysql->update(Mysite::$app->config['tablepre'].'order','`status`=3,`suretime`='.time().''," is_reback =0 and  status = 2  and posttime < ".$dayminitime."");
 
      $link = IUrl::creatUrl('adminpage/order/module/orderlist');//sendtime 发货时间
	 	  $this->message('',$link);
	}

  function drawbacklog(){
	    	$username = IReq::get('username');	    	
	    	$orderstatus = intval(IReq::get('orderstatus'));
	    	$starttime = IReq::get('starttime');
	    	$endtime = IReq::get('endtime');
	    	$nowday = date('Y-m-d',time());
	    	$starttime = empty($starttime)? $nowday:$starttime;
	    	$endtime = empty($endtime)? $nowday:$endtime;
	        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59') ;
	    	$data['starttime'] = $starttime;
	    	$data['endtime'] = $endtime;
	    	$newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
	    	$data['username'] ='';	    	 	 
			if(!empty($username)){				  
				 $where .= ' and buyername like "%'.$username.'%" ';
				 $newlink .= '/username/'.$username;
				 $data['username'] = $username;
			} 
		    $data['orderstatus'] = '';
	    	if($orderstatus > 0){ 
	          	$where .= empty($where)?' where is_reback ='.$orderstatus:' and is_reback = '.$orderstatus;	
			}	 
		    $data['orderstatus'] = $orderstatus;
		    $newlink .= '/orderstatus/'.$orderstatus;
	    	$link = IUrl::creatUrl('/adminpage/order/module/drawbacklog'.$newlink);
	    	$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),5);
			$list = $this->mysql->getarr("select id,is_reback,is_make,status from ".Mysite::$app->config['tablepre']."order   ".$where." and is_reback > 0 and status != 3 order by  id desc  limit ".$pageshow->startnum().", ".$pageshow->getsize()." ");
		
			$data['list'] = array();
			$statusarr = array('1'=>'待平台处理','2'=>'退款成功','3'=>'退款失败','4'=>'待商家处理','5'=>'用户取消退款');
			
			foreach($list as $key=>$value){
			    if($value['is_make'] == 2){
					$drawbackloginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid = ".$value['id']." and ( status = 2 or status = 4 ) order by  id desc limit 1 ");			
				    $drawbackloginfo['reason']='商家不制作订单';
					$drawbackloginfo['content']='商家不制作订单,等待平台退款处理';
				}else{
					$drawbackloginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid = ".$value['id']." and status = 0  order by  id desc limit 1 ");
					/* if($drawbackloginfo['status'] == 4){
						$drawbackloginfo['reason']='平台强制退款';
						$drawbackloginfo['content']='平台强制退款';
					}	 */				
				}
				if(empty($drawbackloginfo)){
					$drawbackloginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid = ".$value['id']."  ");				    
					$drawbackloginfo['reason'] = empty($drawbackloginfo['reason'])?'后台管理员操作退款':$drawbackloginfo['reason'];
					$drawbackloginfo['content'] = empty($drawbackloginfo['content'])?'后台管理员操作退款':$drawbackloginfo['content'];
				}
				$drawbackloginfo['orderstatus'] = $statusarr[$value['is_reback']];
				$drawbackloginfo['is_reback'] = $value['is_reback'];
				$data['list'][] = $drawbackloginfo;
				#print_r($drawbackloginfo);exit;
			}
		 
			$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order    ".$where."  and is_reback > 0 and status != 3 order by  id desc");
			 
			$pageshow->setnum($shuliang);
	    	$data['pagecontent'] = $pageshow->getpagebar($link);
			 
			Mysite::$app->setdata($data);
	  
  }
  
  function showdrawbacklog(){
     $id = IFilter::act(IReq::get('id'));
     $link = IUrl::creatUrl('adminpage/order/module/drawbacklog');
     if(empty($id)) $this->message('id获取失败',$link);
     $drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where id=".$id." order by  addtime desc");
	 #print_r($drawbacklog);exit;
     $data['oderinfo'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$drawbacklog['orderid']."' ");
	 if($data['oderinfo']['is_make'] == 2){			
		$drawbacklog['reason']='商家不制作订单';
		$drawbacklog['content']='商家不制作订单';					
	}
     $data['orderdet'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$drawbacklog['orderid']."' ");
     $this->setstatus();
     $data['drawbacklog'] = $drawbacklog;
     Mysite::$app->setdata($data);
  }
   
  function showcommlist(){ 
	  $id = IReq::get('id');
		if(empty($id)) $this->message('empty_ping');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."comment where id='".$id."'  ");  
		if(empty($checkinfo)) $this->message('empty_ping');
		$data['is_show'] = $checkinfo['is_show'] == 1?0:1;
		$this->mysql->update(Mysite::$app->config['tablepre'].'comment',$data,"id='".$id."'");  
		$this->success('success');
	}
	 
     
	 function backcomment()
	 {
		  $id = intval(IReq::get('askbackid'));
	   	if(empty($id)) $this->message('获取失败');
	   	$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."comment where id='".$id."'  ");
	   	if(empty($checkinfo)) $this->message('评论不存在');
		  if(!empty($checkinfo['replycontent']))  $this->message('已回复过');
		  $where = " id='".$id."' ";
	   	$data['replycontent'] = IFilter::act(IReq::get('askback'));
	  	if(empty($data['replycontent'])) $this->message('请填写回复内容');
		  $data['replytime'] = time();
		  $this->mysql->update(Mysite::$app->config['tablepre'].'comment',$data,$where);
		  $this->success('success');
   }
  
	function shophuiorder(){
        $this->setstatus();
	    	$querytype = IReq::get('querytype');
	    	$searchvalue = IReq::get('searchvalue');
	    	$orderstatus = intval(IReq::get('orderstatus'));
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
	    		}//IUrl::creatUrl('admin/asklist/commentlist
	    	}
	     $data['orderstatus'] = '';

	    	if($orderstatus > 0)
	    	{
	    	   if($orderstatus  > 4)
	          {
	          	$where .= empty($where)?' where ord.status > 3 ':' and ord.status > 3 ';
	          }else{
	          	$newstatus = $orderstatus;
	          	$where .= empty($where)?' where ord.status ='.$newstatus:' and ord.status = '.$newstatus;
	          }
	          $data['orderstatus'] = $orderstatus;
	          $newlink .= '/orderstatus/'.$orderstatus;
	    	}
	    	$link = IUrl::creatUrl('/adminpage/order/module/shophuiorder'.$newlink);
	    	$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),5);
	    	 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
	    	 //
	    	 
	    	$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."shophuiorder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.uid   ".$where." order by ord.addtime desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
	    	$shuliang  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."shophuiorder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.uid   ".$where." ");
	    	$pageshow->setnum($shuliang);
	    	$data['pagecontent'] = $pageshow->getpagebar($link);
	   	$data['list'] = array();
			  if($orderlist)
			  {
				foreach($orderlist as $key=>$value)
				{
					$value['shopinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where   id = ".$value['shopid']." order by id desc ");
					
					$data['list'][] = $value;
				}
			 }
		
	#	 print_r( $data['list'] );
	     $data['scoretocost'] =Mysite::$app->config['scoretocost'];
	     Mysite::$app->setdata($data);
	}
	 
	function shophuiautodel(){
      $dayminitime = time();
      
      $this->mysql->delete(Mysite::$app->config['tablepre'].'shophuiorder',"status in(0) and paystatus != 1  ");
   
      $link = IUrl::creatUrl('adminpage/order/module/shophuiorder');
	 	  $this->message('',$link);
	}
	
function saveset(){   //保存后台跑腿信息 到配置文件中
	 	 
 	 	  $siteinfo['is_ptorderbefore'] = intval(IReq::get('is_ptorderbefore'));
	 	  $siteinfo['pt_orderday'] = intval(IReq::get('pt_orderday'));
		  
		  if( $siteinfo['is_ptorderbefore'] == 1 && $siteinfo['pt_orderday'] < 1) $this->message("请输入大于0的可支持预订天数");
			  
		  
	 	  $siteinfo['km'] = IReq::get('km');
	 	  $siteinfo['kmcost'] = IReq::get('kmcost');
	 	  $siteinfo['addkm'] = IReq::get('addkm');
	 	  $siteinfo['addkmcost'] = IReq::get('addkmcost');
		  
		  
		  $siteinfo['kg'] = IReq::get('kg');
	 	  $siteinfo['kgcost'] = IReq::get('kgcost');
	 	  $siteinfo['addkg'] = IReq::get('addkg');
	 	  $siteinfo['addkgcost'] = IReq::get('addkgcost');
		  
		  $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
	 	  $siteinfo['cityid'] = $cityid;
 		  
		  if(empty( $siteinfo['km'] ) ) $this->message("重量初始公斤值不能为空");
		  
		  
		  
		  if(empty( $siteinfo['kg'] ) ) $this->message("距离初始公里值不能为空");
 
		

	      $paotuiinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset where cityid = '".$cityid."' ");
		  if(empty($paotuiinfo)){
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'paotuiset',$siteinfo);
		  }else{
			  $this->mysql->update(Mysite::$app->config['tablepre'].'paotuiset',$siteinfo,"  id > 0 and cityid = '".$cityid."' ");  
		  } 

		   $this->success('success');
	 } 
 function setpaotui(){
	 
		$cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
	 
		 $paotuiinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset  where cityid = '".$cityid."'  ");
  		 $data['paotuiinfo'] = $paotuiinfo;
		 $postdate = $paotuiinfo['postdate'];
		
		 $nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		 $timelist = !empty($postdate)?unserialize($postdate):array(); 
		
		$data['pstimelist'] = array();
		foreach($timelist as $key=>$value){
		     $tempt = array();
			 $tempt['s'] = date('H:i',$nowhout+$value['s']);
		     $tempt['e'] = date('H:i',$nowhout+$value['e']);
		     $tempt['i'] =  $value['i'];
			 $data['pstimelist'][] = $tempt;
		}
		 Mysite::$app->setdata($data);
	 }   
//保存配送时间
	function savepostdate(){
       
		$starthour =  intval(IFilter::act(IReq::get('starthour')));
		$startminit =  intval(IFilter::act(IReq::get('startminit')));
		$endthour =  intval(IFilter::act(IReq::get('endthour')));
		$endminit = intval(IFilter::act(IReq::get('endminit')));
		$instr = trim(IFilter::act(IReq::get('instr')));
		
		 $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		 
		
		$paotuiinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset where cityid = '".$cityid."' ");
		
		
 		$bigetime = $starthour*60*60+$startminit*60;
		$endtime = $endthour*60*60+$endminit*60;
		if($bigetime < 1){
		   $this->message('配送时间段起始时间必须从凌晨1分开始');
		}
		if($bigetime > $endtime){
			  $this->message('开始时间段必须大于结束时间');
		}
		if($endtime >=86400) $this->message('配送时间段结束时间最大值23:59');
		$nowlist = !empty($paotuiinfo['postdate'])?unserialize($paotuiinfo['postdate']):array();
		//postdata数据结构   array(  '0'=>array('s'=>shuzi,e=>'shuzi'),'1'=>array('s'=>shuzi,e=>'shuzi')
		$checkshu = count($nowlist);
		if($checkshu > 0){ 
		   $checknowendo  =  $nowlist[$checkshu-1]['e'];
		   if($checknowendo > $bigetime) $this->message('已设置配送时间段已包含提交的开始时间');
		}
		$tempdata['s'] = $bigetime;
		$tempdata['e'] = $endtime;
		$tempdata['i'] = $instr;
		
		$ptpostdta = unserialize($paotuiinfo['postdate']);
		
		$ptpostdta[] = $tempdata;
		
		
		$savedata['postdate'] = serialize($ptpostdta);
 		 
		  
		 
		  if(empty($paotuiinfo)){
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'paotuiset',$savedata);
		  }else{
			  $this->mysql->update(Mysite::$app->config['tablepre'].'paotuiset',$savedata,"  id > 0 and cityid = '".$cityid."' ");  
		  } 
 
		$this->success('success');
	}
	//删除配送时间
	function delpostdate(){
		 
		 $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		$nowdelid =  intval(IFilter::act(IReq::get('id')));
	#$this->message($nowdelid);
	$paotuiinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset   where cityid = '".$cityid."'  ");
	$tempshopinfo = $paotuiinfo['postdate'];
	
		if(empty($tempshopinfo)) $this->message('未设置配送时间段');
		$nowlist = unserialize($tempshopinfo);
		//postdata数据结构   array(  '0'=>array('s'=>shuzi,e=>'shuzi'),'1'=>array('s'=>shuzi,e=>'shuzi')
	  
		$newdata = array();
		foreach($nowlist as $key=>$value){
			if($key != $nowdelid){
			    $newdata[] = $value;
			}
		}  
		$savedata['postdate'] = serialize($newdata);
		 $this->mysql->update(Mysite::$app->config['tablepre'].'paotuiset',$savedata,"  id > 0  and cityid = '".$cityid."'  ");
	 
		
		$this->success('success'); 
	}
	/***** 
	
	
		2016.3.5新增
	
	
	***************/
	//根据日期筛选出 可结算的店铺  100条记录一个页面.
	 function shopjsadd(){  
	      /*$daytime = IFilter::act(IReq::get('daytime'));
	      $nowtime = time(); 
		  $nowmintime =  strtotime($daytime);
		  $checktime = $nowtime - $nowmintime;
		  if($checktime < 259200 || $checktime > 457141240){
			  $nowmintime = strtotime(date('Y-m-d',($nowtime- 259200)));
		  }
		  
		  $pageshow = new page();
	      $pageshow->setpage(IReq::get('page'),100); 
		  $where  = " where   id not in(select shopid from ".Mysite::$app->config['tablepre']."shopjs where jstime =".$nowmintime."  ) ";
	      $data['shoplist'] =   $this->mysql->getarr("select id,shopname,uid  from ".Mysite::$app->config['tablepre']."shop  ".$where." order by id asc   limit ".$pageshow->startnum().", ".$pageshow->getsize().""); 
	      $shuliang  = $this->mysql->counts("select id,shopname  from ".Mysite::$app->config['tablepre']."shop ".$where." order by id asc  ");
	      $pageshow->setnum($shuliang);
	      $data['pagecontent'] = $pageshow->getpagebar();
		  $data['showtime'] = date('Y-m-d',$nowmintime);//显示日期*/
		  Mysite::$app->setdata(array());
	 }
	  //生成 结算单
	 function makejsorder(){
		 $jstime = IFilter::act(IReq::get('daytime'));
		 $shopid = intval( IFilter::act(IReq::get('shopid')) );
		 if(empty($jstime)){
			 $this->message('请输入结算时间');
		 }
		 $nowtime = time(); 
		 $nowmintime =  strtotime($jstime);
		 $checktime = $nowtime - $nowmintime;
		 if($checktime < 259200 || $checktime > 457141240){
			  $this->message('只能结算3天前的订单');
		 }
		 if(empty($shopid)){
			 $this->message('店铺ID错误');
		 }
		 
		 
		 
		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."'  ");
		 if(empty($shopinfo))  $this->message('店铺为空');
		 $shopdet = array();//默认 店铺配送
		 $sendtype = 1;
		 if($shopinfo['shoptype'] == 0){
			   $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid='".$shopid."' ");//$table,$row,$where=""
	     }else{
			   $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$shopid."' ");//$table,$row,$where="" 
		 }
		 //
		 if($shopdet['sendtype'] == 0){//平台配送
			 $sendtype = 0;
		 } 
		 
		
		  /***检测是否 生成过结算单***/
		 $checkinfo = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shopjs  where shopid = ".$shopid." and jstime =".$nowmintime."  ");
		 if($checkinfo > 0){
			 $this->message('已生成结算单');
		 }
		  
		 
		
		 $maxtime = $nowmintime +86400;
		 //将所有 在配送时间段里的
		 $canwhere = " where shopid = '".$shopid."'  and sendtime >= ".$nowmintime." and sendtime < ".$maxtime." and status >  1 and status < 3 and is_reback > 0 ";
		 $checkcanjs = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order ".$canwhere."  ");
		  if($checkcanjs > 0){
			  $this->message('店铺内存在退款订单请先处理后才能生成');
		  }
		  /***清理订单***/
		  $this->mysql->update(Mysite::$app->config['tablepre'].'order',array('status'=>3),"  status =2 and  shopid=".$shopid." and sendtime >= ".$nowmintime." and sendtime < ".$maxtime." ");  
		
		  
		  
		  $where2  = " where shopid = '".$shopid."'  and sendtime >= ".$nowmintime." and sendtime < ".$maxtime." ";
		  $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(allcost) as allcost,sum(shopps) as shopps,sum(bagcost) as bagcost   from ".Mysite::$app->config['tablepre']."order  ".$where2." and paytype =0 and shopcost > 0 and status = 3  order by id asc  limit 0,1000");
	      $line= $this->mysql->select_one("select count(id) as shuliang,sum(allcost) as allcost,sum(shopps) as shopps,sum(bagcost) as bagcost   from ".Mysite::$app->config['tablepre']."order  ".$where2." and paytype !=0  and paystatus =1 and shopcost > 0 and status = 3     order by id asc  limit 0,1000");

		 
		 
		 $newdata['onlinecount'] = $line['shuliang'];
		 $newdata['onlinecost'] = $line['allcost'];
		 $newdata['unlinecount'] = $shoptj['shuliang'];
		 $newdata['unlinecost'] = $shoptj['allcost'];
		 $yjbl =   $shopinfo['yjin']< 1?Mysite::$app->config['yjin']:$shopinfo['yjin'];
		 $newdata['yjb'] = empty($yjbl)?0:$yjbl; // 15.00
		$yjcost =  ($shoptj['allcost']+$line['allcost']-$shoptj['shopps']-$line['shopps']-$shoptj['bagcost']-$line['bagcost'])*$yjbl*0.01;
		  $newdata['acountcost'] =  0;
		 if($sendtype == 0){//平台配送 
			  $newdata['acountcost'] = $line['allcost']+$shoptj['allcost']-$yjcost-$shoptj['shopps']-$line['shopps'];//  线上金额-佣金比例+线下金额-线上配送费=平台配送金额 
		 }else{//自行配送
			 $newdata['acountcost'] = $line['allcost']-$yjcost;
		 }
		 $newdata['yjcost'] = $yjcost;
		 $newdata['pstype'] = $sendtype;
		 $newdata['shopid'] =$shopinfo['id'];
		 $newdata['shopuid'] =$shopinfo['uid'];
		
		 $newdata['addtime'] = time();
		 $newdata['jstime'] = $nowmintime;
		 
	 
		 
		 $this->mysql->insert(Mysite::$app->config['tablepre'].'shopjs',$newdata);
		 $orderid = $this->mysql->insertid(); 
		 /***自动  更新用户 账号余额***/ 
		 $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$shopinfo['uid']."' ");
		// $this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`+'.$newdata['acountcost'],"uid ='".$shopinfo['uid']."' ");
		 $newdatac['cost'] = $newdata['acountcost'];
		 $newdatac['type'] = 3;
		 $newdatac['status'] = 2;
		 $newdatac['addtime'] = time()+1;
		 $newdatac['shopid'] = 0;
		 $newdatac['changetype'] = 2;
         $newdatac['shopuid'] =  $shopinfo['uid'];
		 $newdatac['name'] = $jstime.'日结算转入';
		 $newdatac['yue'] = $memberinfo['shopcost']+$newdata['acountcost'];
		 $newdatac['jsid'] = $orderid;
		 //账号余额 
		 $this->mysql->insert(Mysite::$app->config['tablepre'].'shoptx',$newdatac); 
		 
		 
		 
		 //结算闪慧订单 
		$where2  = " where shopid = '".$shopid."'  and addtime >= ".$nowmintime." and addtime < ".$maxtime." and status =1 and paystatus = 1 ";
		$huitj= $this->mysql->select_one("select count(id) as shuliang,sum(sjcost) as allcost  from ".Mysite::$app->config['tablepre']."shophuiorder  ".$where2."    order by id asc  limit 0,1000");

		  
		  
		$cnewdata['onlinecount'] = $huitj['shuliang'];
		$cnewdata['onlinecost'] = $huitj['allcost'];
		$cnewdata['unlinecount'] =0;
		$cnewdata['unlinecost'] = 0;
		// $yjbl =   empty($shopdet['yjin'])?Mysite::$app->config['yjin']:$shopdet['yjin'];
		// $cnewdata['yjb'] = empty($yjbl)?0:$yjbl; 
		// $yjcost =  ($huitj['allcost'])*$yjbl*0.01; 
		$cnewdata['acountcost'] = $huitj['allcost']; 
		$cnewdata['yjcost'] = 0;
		$cnewdata['pstype'] = 0;
		$cnewdata['shopid'] =$shopinfo['id'];
		$cnewdata['shopuid'] =$shopinfo['uid'];

		$cnewdata['addtime'] = time();
		$cnewdata['jstime'] = $nowmintime;
		$this->mysql->insert(Mysite::$app->config['tablepre'].'shopjs',$cnewdata);
		$orderid = $this->mysql->insertid(); 
		/***自动  更新用户 账号余额***/  
		$this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`+'.$cnewdata['acountcost'].'+'.$newdata['acountcost'],"uid ='".$shopinfo['uid']."' ");

		$newdatacb['cost'] = $cnewdata['acountcost'];
		$newdatacb['type'] = 3;
		$newdatacb['status'] = 2;
		$newdatacb['addtime'] = time()+2;
		$newdatacb['shopid'] = 0;
		$newdatacb['changetype'] = 2;
		$newdatacb['shopuid'] =  $shopinfo['uid'];
		$newdatacb['name'] = $jstime.'优惠买单日结算转入';
		$newdatacb['yue'] = $memberinfo['shopcost']+$newdata['acountcost']+$cnewdata['acountcost'];
		$newdatacb['jsid'] = $orderid;
		//账号余额 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'shoptx',$newdatacb); 



		$this->success('success'); 
	}
	 
	 //店铺提现记录
	  function shoptx(){
		  $pageshow = new page();
	      $pageshow->setpage(IReq::get('page'),10);
		  $shopname = trim(IFilter::act(IReq::get('shopname'))); //店铺名称
		  $status = IReq::get('status'); //状态
		  $starttime = IFilter::act(IReq::get('starttime')); //开始时间 
		  $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
         $newlink = '';
		 if(empty($status)){
		 $where = " where type = 0 and status = 1 " ;
		 }else{
		  $where = " where type = 0 ";//仅获取提现记录
		  }
		  if(!empty($shopname)){
			  $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where shopname='".$shopname."'  ");
		      if(!empty($info)) $where.=" and shopuid = ".$info['uid']." ";
              $newlink .= '/shopname/'.$shopname;
		  }
		  if(!empty($status)){
              $where.=" and status = ".$status." ";
              $newlink .= '/status/'.$status;
			  $data['status'] = $status;
          }
		  if(!empty($starttime)){
              $where.=" and addtime > ".strtotime($starttime)." ";
              $newlink .= '/starttime/'.$starttime.'/endtime/'.$endtime;
          }
		  if(!empty($endtime)){
              $where.=" and addtime < ".strtotime($endtime)." ";
              $newlink .= '/endtime/'.$endtime;
          }
		   

         $data['outlink'] =IUrl::creatUrl('adminpage/order/module/outshoptx/outtype/query'.$newlink);
         $data['outlinkch'] =IUrl::creatUrl('adminpage/order/module/outshoptx'.$newlink);
		  $link = IUrl::creatUrl('/adminpage/order/module/shoptx'.$newlink);
	      $txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."shoptx  ".$where."  order by addtime desc   limit ".$pageshow->startnum().", ".$pageshow->getsize().""); 
	      $shuliang  = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shoptx  ".$where."  order by id asc  ");
	      $pageshow->setnum($shuliang);
	      $data['pagecontent'] = $pageshow->getpagebar($link);
		  $tempdata = array();
		  $typearray = array(0=>'提现申请',1=>'账号充值',2=>'取消提现');
		  $statusarray = array(0=>'空',1=>'申请',2=>'处理成功',3=>'已取消');
		  if(is_array($txlist)){
			  foreach($txlist as $key=>$value){
				   $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where uid=".$value['shopuid']." ");
				   $value['shopname'] = isset($info['shopname'])?$info['shopname']:'未定义';
				   $wxarr = array();
				   if(!empty($info['wxopenid'])){
					   $wxarr['wxusername'] = $info['wxusername'];
					   $wxarr['wxopenid'] = $info['wxopenid'];
					   $wxarr['wxuserlogo'] = $info['wxuserlogo'];
				   }
				   $value['wxuserinfo'] = $wxarr;				   
				   $memberinfo =$this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."member where uid=".$value['shopuid']." ");
				   if(empty($memberinfo)){
					   $value['backacount'] ='';
				   }else{
					   $value['backacount'] = $memberinfo['backacount']; 
				   } 
				//  $value['name'] = isset($typearray[$value['type']])?$typearray[$value['type']]:'未定义';
				  $value['statusname'] = isset($statusarray[$value['status']])?$statusarray[$value['status']]:'未定义';
				  $value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
				  $tempdata[] = $value;
			  }
		  }
		  $data['txlist'] = $tempdata;
		  $data['shopname'] = $shopname;
		  $data['starttime'] = $starttime;
		  $data['endtime'] = $endtime;
		   Mysite::$app->setdata($data);
	 }
         //取消店铺的提现申请
	  function shopuntx(){
		  $txid =  intval(IFilter::act(IReq::get('txid'))); //店铺名称
		 $txinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptx where id='".$txid."'  ");
		 if(empty($txinfo)){
			$this->message('提现信息获取失败');
		 }
		 if($txinfo['status'] != 1){
			 $this->message('提现已受理不能取消');
		 }
		 if($txinfo['type'] != 0){
			 $this->message('不是店铺提现不能取消');
		 }
		 
		 $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$txinfo['shopuid']."'  ");
		 if(empty($userinfo)){
			 $this->message('用户不存在');
		 }
	     $this->mysql->update(Mysite::$app->config['tablepre'].'shoptx','`status`=3',"id ='".$txid."' ");
		 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`+'.$txinfo['cost'],"uid ='".$txinfo['shopuid']."' ");
		  
		 $newdata['cost'] = $txinfo['cost'];
		 $newdata['type'] = 2;
		 $newdata['status'] = 2;
		 $newdata['addtime'] = time();
		 $newdata['shopid'] = 0;
         $newdata['shopuid'] =  $txinfo['shopuid'];
		 $newdata['name'] = '提现失败';
		 $newdata['changetype'] = 2;
		 $newdata['yue'] = $userinfo['shopcost']+$txinfo['cost'];
		 $this->mysql->insert(Mysite::$app->config['tablepre'].'shoptx',$newdata);
		 $orderid = $this->mysql->insertid(); 
		$info = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptx  where id = ".$orderid." ");
		$this->success($info);
	 }
	 //分站提现
	 function stationtx(){
		  $pageshow = new page();
	      $pageshow->setpage(IReq::get('page'),10);
		  $cityid = trim(IFilter::act(IReq::get('cityid'))); //分站名称
		  $status = IReq::get('status'); //状态
		  $starttime = IFilter::act(IReq::get('starttime')); //开始时间 
		  $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
          $newlink = '';
		  $where = ' where id > 0 ';
		  if(!empty($cityid)){
		      $where.=" and cityid = ".$cityid." ";
              $newlink .= '/cityid/'.$cityid;
			  $data['cityid'] = $cityid;
		  }
		  if(!empty($status)){
              $where.=" and status = ".$status." ";
              $newlink .= '/status/'.$status;
			  $data['status'] = $status;
          }
		  if(!empty($starttime)){
              $where.=" and addtime > ".strtotime($starttime)." ";
              $newlink .= '/starttime/'.$starttime.'/endtime/'.$endtime;
          }
		  if(!empty($endtime)){
              $where.=" and addtime < ".strtotime($endtime)." ";
              $newlink .= '/endtime/'.$endtime;
          }
		   
     
          $data['outlink'] =IUrl::creatUrl('adminpage/order/module/outstationtx/outtype/query'.$newlink);
          $data['outlinkch'] =IUrl::creatUrl('adminpage/order/module/outstationtx'.$newlink);
		  $link = IUrl::creatUrl('/adminpage/order/module/stationtx'.$newlink);
	      $txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."txapply  ".$where."  order by addtime desc   limit ".$pageshow->startnum().", ".$pageshow->getsize().""); 
	      $shuliang  = $this->mysql->counts("select *  from ".Mysite::$app->config['tablepre']."txapply  ".$where."  order by id asc  ");
	      $pageshow->setnum($shuliang);
	      $data['pagecontent'] = $pageshow->getpagebar($link);
		  $tempdata = array();
		  $typearray = array(0=>'提现申请',1=>'账号充值',2=>'取消提现');
		  $statusarray = array(0=>'空',1=>'申请',2=>'处理成功',3=>'已取消');
		  if(is_array($txlist)){
			  foreach($txlist as $key=>$value){	
				  $value['statusname'] = isset($statusarray[$value['status']])?$statusarray[$value['status']]:'未定义';
				  $value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
				  $tempdata[] = $value;
			  }
		  }
		  $data['txlist'] = $tempdata;  
		  $data['starttime'] = $starttime;
		  $data['endtime'] = $endtime;
		 
		   Mysite::$app->setdata($data);
	 }
	  //导出分站提现记录查询结果
    function outstationtx(){
        $outtype = IReq::get('outtype');
        if(!in_array($outtype,array('query','ids')))
        {
            header("Content-Type: text/html; charset=UTF-8");
            echo '查询条件错误';
            exit;
        }
        $where = " where id > 0 ";// 
        if($outtype == 'ids')
        {
            $id = trim(IReq::get('id'));
            if(empty($id))
            {
                header("Content-Type: text/html; charset=UTF-8");
                echo '查询条件不能为空';
                exit;
            }
            $doid = explode('-',$id);
            $id = join(',',$doid);
            $where .= ' and id in('.$id.') ';

        }

        $cityid = trim(IFilter::act(IReq::get('cityid'))); //分站cityid
        $starttime = IFilter::act(IReq::get('starttime')); //开始时间
        $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
        $status = trim(IFilter::act(IReq::get('status')));

        if(!empty($cityid)) $where.=" and cityid = ".$cityid." ";
		if(!empty($status)) $where.=" and status = ".$status." ";
        if(!empty($starttime)) $where.=" and addtime > ".strtotime($starttime)." ";
        if(!empty($endtime)) $where.=" and addtime < ".strtotime($endtime)." ";
         
        $txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."txapply  ".$where."  order by addtime desc ");
         
		$statusarray = array(0=>'空',1=>'申请',2=>'处理成功',3=>'已取消');
        $list = array();
        if(is_array($txlist)){
            foreach($txlist as $key=>$value){				  
				  $value['statusname'] = isset($statusarray[$value['status']])?$statusarray[$value['status']]:'未定义';
				  $value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
				  $list[] = $value;
			  }
        }
        $outexcel = new phptoexcel();
        $titledata = array('id','分站名称','账号','提现金额','账号余额','状态','提交时间');
        $titlelabel = array('id','stationname','backacount','txcost','cost','statusname','adddate');
        $outexcel->out($titledata,$titlelabel,$list,'','分站提现');
    }
 
	 function shoptx85(){
		  $pageshow = new page();
	      $pageshow->setpage(IReq::get('page'),10); 
		  $shopname = trim(IFilter::act(IReq::get('shopname'))); //店铺名称
		  $starttime = IFilter::act(IReq::get('starttime')); //开始时间 
		  $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
         $newlink = '';
		  $where = " where type = 0 ";//仅获取提现记录
		  if(!empty($shopname)){
			  $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where shopname='".$shopname."'  ");
		      if(!empty($info)) $where.=" and shopuid = ".$info['uid']." ";
              $newlink .= '/shopname/'.$shopname;
		  }
		  if(!empty($starttime)){
              $where.=" and addtime > ".strtotime($starttime)." ";
              $newlink .= '/starttime/'.$starttime.'/endtime/'.$endtime;
          }
		  if(!empty($endtime)){
              $where.=" and addtime < ".strtotime($endtime)." ";
              $newlink .= '/endtime/'.$endtime;
          }

         $data['outlink'] =IUrl::creatUrl('adminpage/order/module/outshoptx/outtype/query'.$newlink);
         $data['outlinkch'] =IUrl::creatUrl('adminpage/order/module/outshoptx'.$newlink);

	      $txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."shoptx  ".$where."  order by addtime desc   limit ".$pageshow->startnum().", ".$pageshow->getsize().""); 
	      $shuliang  = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shoptx  ".$where."  order by id asc  ");
	      $pageshow->setnum($shuliang);
	      $data['pagecontent'] = $pageshow->getpagebar();
		  $tempdata = array();
		  $typearray = array(0=>'提现申请',1=>'账号充值',2=>'取消提现');
		  $statusarray = array(0=>'空',1=>'申请',2=>'处理成功',3=>'已取消');
		  if(is_array($txlist)){
			  foreach($txlist as $key=>$value){
				   $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where uid=".$value['shopuid']." ");
				   $value['shopname'] = isset($info['shopname'])?$info['shopname']:'未定义';
				   
				   $memberinfo =$this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."member where uid=".$value['shopuid']." ");
				   if(empty($memberinfo)){
					   $value['backacount'] ='';
				   }else{
					   $value['backacount'] = $memberinfo['backacount']; 
				   } 
				//  $value['name'] = isset($typearray[$value['type']])?$typearray[$value['type']]:'未定义';
				  $value['statusname'] = isset($statusarray[$value['status']])?$statusarray[$value['status']]:'未定义';
				  $value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
				  $tempdata[] = $value;
			  }
		  }
		  $data['txlist'] = $tempdata;
		  $data['shopname'] = $shopname;
		  $data['starttime'] = $starttime;
		  $data['endtime'] = $endtime;
		   Mysite::$app->setdata($data);
	 }
    //导出店铺提现记录查询结果
    function outshoptx(){
        $outtype = IReq::get('outtype');
        if(!in_array($outtype,array('query','ids')))
        {
            header("Content-Type: text/html; charset=UTF-8");
            echo '查询条件错误';
            exit;
        }
        $where = " where type = 0 ";//仅获取提现记录
        if($outtype == 'ids')
        {
            $id = trim(IReq::get('id'));
            if(empty($id))
            {
                header("Content-Type: text/html; charset=UTF-8");
                echo '查询条件不能为空';
                exit;
            }
            $doid = explode('-',$id);
            $id = join(',',$doid);
            $where .= ' and id in('.$id.') ';

        }

        $shopname = trim(IFilter::act(IReq::get('shopname'))); //店铺名称
        $starttime = IFilter::act(IReq::get('starttime')); //开始时间
        $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
        $status  =  IFilter::act(IReq::get('status')); 
        

        if(!empty($shopname)){
            $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where shopname='".$shopname."'  ");
            if(!empty($info)) $where.=" and shopuid = ".$info['uid']." ";

        }
        if(!empty($starttime)) $where.=" and addtime > ".strtotime($starttime)." ";
        if(!empty($endtime)) $where.=" and addtime < ".strtotime($endtime)." ";
        if(!empty($status))  $where.="  and status = ".$status." ";     


        $txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."shoptx  ".$where."  order by addtime desc ");
        $statusarray = array(0=>'空',1=>'申请',2=>'处理成功',3=>'已取消');
        $list = array();
        if(is_array($txlist)){
            foreach($txlist as $key=>$value){
                $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where uid=".$value['shopuid']." ");
                $value['shopname'] = isset($info['shopname'])?$info['shopname']:'未定义';

                $memberinfo =$this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."member where uid=".$value['shopuid']." ");
                if(empty($memberinfo)){
                    $value['backacount'] ='-';
                }else{
                    $value['backacount'] = $memberinfo['backacount'];
                }
                $value['statusname'] = isset($statusarray[$value['status']])?$statusarray[$value['status']]:'未定义';
                $value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
                $list[] = $value;
            }
        }

        $outexcel = new phptoexcel();
        $titledata = array('id','店铺名称','提现说明','账号','提现金额','账号余额','状态','提交时间');
        $titlelabel = array('id','shopname','name','backacount','cost','yue','statusname','adddate');
        $outexcel->out($titledata,$titlelabel,$list,'','商家结算');
    }
	  //管理员确认提现
	 function adminpsstx(){
		 $txid =  intval(IFilter::act(IReq::get('txid'))); //店铺名称
		 $txinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptx where id='".$txid."'  ");
		 if(empty($txinfo)){
			$this->message('提现信息获取失败');
		 }
		 if($txinfo['status'] != 1){
			 $this->message('提现已受理不能重复处理');
		 }
		 if($txinfo['type'] != 0){
			 $this->message('不是店铺提现不能操作');
		 }  
	     $this->mysql->update(Mysite::$app->config['tablepre'].'shoptx','`status`=2',"id ='".$txid."' ");
		 $this->success('success'); 
	 }
	  //管理员确认一键打款
	 function admintxwechat(){
		 $txid =  intval(IFilter::act(IReq::get('txid'))); //店铺名称
		 $txinfo =  $this->mysql->select_one("select status,type from ".Mysite::$app->config['tablepre']."shoptx where id='".$txid."'  ");
		 if(empty($txinfo)){
			$this->message('提现信息获取失败');
		 }
		 if($txinfo['status'] != 1){
			 $this->message('提现已受理不能重复处理');
		 }
		 if($txinfo['type'] != 0){
			 $this->message('不是店铺提现不能操作');
		 } 
		 $wxopenid = IFilter::act(IReq::get('wxopenid'));//微信openid
		 $pay_wechat = new paytowechat();
		 if($pay_wechat->payToUser($wxopenid,$txid)){
			$this->mysql->update(Mysite::$app->config['tablepre'].'shoptx','`status`=2',"id ='".$txid."' ");
			$this->success('一键打款成功，请留意微信零钱');  
		 }else{
			$this->success('一键打款失败');
		 }
	 }
	 //取消店铺的提现申请
	  function saveshoptxreason(){
		 $txid =  intval(IFilter::act(IReq::get('txid'))); //
		 $refusereason = trim(IFilter::act(IReq::get('reason')));
		
		 $txinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptx where id='".$txid."'  ");
		 if(empty($txinfo)){
			$this->message('提现信息获取失败');
		 }
		 if($txinfo['status'] != 1){
			 $this->message('提现已受理不能取消');
		 }
		 if($txinfo['type'] != 0){
			 $this->message('不是店铺提现不能取消');
		 } 
		 $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$txinfo['shopuid']."'  ");
		 if(empty($userinfo)){
			 $this->message('用户不存在');
		 }
		 $this->mysql->update(Mysite::$app->config['tablepre']."shoptx','`status`=3,`refusereason`='".$refusereason."'","id ='".$txid."'");
		 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`+'.$txinfo['cost'],"uid ='".$txinfo['shopuid']."' ");
		  
		 $newdata['cost'] = $txinfo['cost'];
		 $newdata['type'] = 2;
		 $newdata['status'] = 2;
		 $newdata['addtime'] = time();
		 $newdata['shopid'] = 0;
		 $newdata['changetype'] = 2;
         $newdata['shopuid'] =  $txinfo['shopuid'];
		 $newdata['name'] = '提现失败';
		 $newdata['yue'] = $userinfo['shopcost']+$txinfo['cost'];
		 $newdata['refusereason'] = $refusereason;
		 $this->mysql->insert(Mysite::$app->config['tablepre'].'shoptx',$newdata);
		 $id = $this->mysql->insertid(); 
		 $info = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptx  where id = ".$id." ");
		 $this->success($info);
	}
	 //确认分站提现
	  function stationpsstx(){
		 $txid =  intval(IFilter::act(IReq::get('txid')));  
		 $txinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."txapply where id='".$txid."'  ");
		 if(empty($txinfo)){
			$this->message('提现信息获取失败');
		 }
		 if($txinfo['status'] != 1){
			 $this->message('提现已受理不能重复处理');
		 }		  
	     $this->mysql->update(Mysite::$app->config['tablepre'].'txapply','`status`=2',"id ='".$txid."' ");
		 $this->success('success'); 
	 }
	 //取消分站的提现申请
	  function savestationtxreason(){
		 $txid =  intval(IFilter::act(IReq::get('txid'))); //提现id
		 $refusereason = trim(IFilter::act(IReq::get('reason')));
		 $txinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."txapply where id='".$txid."'  ");
		 if(empty($txinfo)){
			$this->message('提现信息获取失败');
		 }
		 if($txinfo['status'] != 1){
			 $this->message('提现已受理不能取消');
		 } 
		 $admininfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."admin where uid='".$txinfo['stationuid']."'  ");
		 if(empty($admininfo)){
			 $this->message('分站不存在');
		 }
	     $this->mysql->update(Mysite::$app->config['tablepre'].'txapply',"`status`=3,`refusereason`= '".$refusereason."',`cost`=`cost`+".$txinfo['txcost']." ","id ='".$txid."' ");  
		 $newdata['adminid'] = $txinfo['cityid'];
		 $newdata['oldcost'] = $admininfo['cost'];
		 $newdata['changetype'] = 2;
		 $newdata['changecost'] = $txinfo['txcost'];
		 $newdata['nowcost'] = $admininfo['cost'] + $txinfo['txcost'];
         $newdata['changereason'] =  '退回取消'.date('Y-m-d',$txinfo['addtime']).'提现申请资金';
		 $newdata['changeperson'] = ICookie::get('adminname');
		 $newdata['refusereason'] = $refusereason;
		 $newdata['addtime'] = time();
		 $this->mysql->insert(Mysite::$app->config['tablepre'].'stationcostlog',$newdata);
		 $this->mysql->update(Mysite::$app->config['tablepre'].'admin','`cost`=`cost`+'.$txinfo['txcost'],"uid ='".$txinfo['stationuid']."' ");	 
		 $this->success('success'); 
	 }
	 
	 //店铺资金记录
	 function shoptxtlog(){
		 $pageshow = new page();
	      $pageshow->setpage(IReq::get('page'),10); 
		  $shopname = trim(IFilter::act(IReq::get('shopname'))); //店铺名称
		  $starttime = IFilter::act(IReq::get('starttime')); //开始时间 
		  $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
		  $where = " where id > 0 ";//仅获取提现记录
		 $newlink='';
		  if(!empty($shopname)){ 
			  $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where shopname='".$shopname."'  ");
		      if(!empty($info)) $where.=" and shopuid = ".$info['uid']." ";
			  $newlink.='/shopname/'.$shopname;
		  }
		  if(!empty($starttime)){
			  $where.=" and addtime > ".strtotime($starttime)." ";
			  $newlink.='/starttime/'.$starttime;
		  }
		  if(!empty($endtime)){
			  $where.=" and addtime < ".strtotime($endtime)." ";
			  $newlink.='/endtime/'.$endtime;

		  }
		  $link = IUrl::creatUrl('/adminpage/order/module/shoptxtlog'.$newlink);
	      $txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."shoptx  ".$where."  order by addtime desc   limit ".$pageshow->startnum().", ".$pageshow->getsize().""); 
	      $shuliang  = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shoptx  ".$where."  order by id asc  ");
	      $pageshow->setnum($shuliang);
	      $data['pagecontent'] = $pageshow->getpagebar($link);
		  $tempdata = array();
		  $typearray = array(0=>'提现申请',1=>'账号充值',2=>'取消提现');
		  $statusarray = array(0=>'空',1=>'申请',2=>'处理成功',3=>'已取消');
		  if(is_array($txlist)){
			  foreach($txlist as $key=>$value){
				   $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where uid=".$value['shopuid']." ");
				   $value['shopname'] = isset($info['shopname'])?$info['shopname']:'未定义';
					
				//  $value['name'] = isset($typearray[$value['type']])?$typearray[$value['type']]:'未定义';
				  $value['statusname'] = isset($statusarray[$value['status']])?$statusarray[$value['status']]:'未定义';
				  $value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
				  $tempdata[] = $value;
			  }
		  }
		  $data['txlist'] = $tempdata;
		   $data['shopname'] = $shopname;
		  $data['starttime'] = $starttime;
		  $data['endtime'] = $endtime;
		  Mysite::$app->setdata($data); 
	 } 
	 function adminpay(){
		 $dotype = intval(IFilter::act(IReq::get('dotype')));
		 if(empty($dotype)){
			 $uid = intval(IFilter::act(IReq::get('uid'))); //店铺名称
			 $cost = trim(IFilter::act(IReq::get('cost'))); //店铺名称
			 	$reason = trim(IFilter::act(IReq::get('reason')));
			$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'  ");
			if(empty($userinfo)){
				$this->message('用户不存在');
			}
			$checkcost = trim($cost);
			if($checkcost < 0.01){
				$this->message('充值金额不能小于 0.01');
			}
			 
			$newdata['cost'] = $checkcost;
			$newdata['type'] = 1;
			$newdata['status'] = 2;
			$newdata['addtime'] = time();
			$newdata['shopid'] = 0;
			$newdata['changetype'] = 2;
			$newdata['shopuid'] =  $uid;
			$newdata['name'] =empty($reason)?'账号充值':$reason; 
			$newdata['yue'] = $userinfo['shopcost']+$checkcost;
			$this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`+'.$checkcost,"uid ='".$uid."' ");
			$this->mysql->insert(Mysite::$app->config['tablepre'].'shoptx',$newdata);
			$this->success('success'); 
		}elseif($dotype == 1){
		    $uid = intval(IFilter::act(IReq::get('uid'))); //店铺名称
		    $cost = trim(IFilter::act(IReq::get('cost'))); //店铺名称
			$reason = trim(IFilter::act(IReq::get('reason')));
			$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'  ");
			if(empty($userinfo)){
				$this->message('用户不存在');
			}
			$checkcost = trim($cost);
			if($checkcost < 0.01){
				$this->message('充值金额不能小于0.01');
			}
			if(empty($reason)){
				$this->message('录入减少原因');
			}
			if($userinfo['shopcost'] < $cost){
				$this->message('账号余额小于扣除金额');
			}
			$newdata['cost'] = $checkcost;
			$newdata['type'] = 0;
			$newdata['status'] = 2;
			$newdata['addtime'] = time();
			$newdata['shopid'] = 0;
			$newdata['changetype'] = 1;
			$newdata['shopuid'] =  $uid;
			$newdata['name'] = $reason; 
			$newdata['yue'] = $userinfo['shopcost']-$checkcost;
			$this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`-'.$checkcost,"uid ='".$uid."' ");
			$this->mysql->insert(Mysite::$app->config['tablepre'].'shoptx',$newdata);
			$this->success('success'); 
		}else{
			$this->message('未定义的方式');
		}
	 }
	 
	 
	 
	 /***** 
	
	
		2016.3.5结束
	
	
	***************/
	function orderhistory(){
		if($this->checkhistoryset()){
			
		}
		$this->setstatus(); 
		$querytype = IReq::get('querytype');
		$searchvalue = IReq::get('searchvalue');
		$orderstatus = intval(IReq::get('orderstatus'));
		$cityid = intval(IReq::get('cityid'));
		$starttime = IReq::get('starttime');
		$endtime = IReq::get('endtime');
		$BeginDate = date('Y-m-01',time());//找到第一个订单所在月份和天数
			
			$nowday = date('Y-m-d', (strtotime("$BeginDate -2 month")-1));
		
		
		
		$starttime = empty($starttime)? $nowday:$starttime;
		$endtime = empty($endtime)? $nowday:$endtime;
		$where = '  where ord.addtime > '.strtotime($starttime.' 00:00:00').' and ord.addtime < '.strtotime($endtime.' 23:59:59');
		$data['starttime'] = $starttime;
		$data['endtime'] = $endtime;
		$newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
		$data['searchvalue'] ='';
		$data['querytype'] ='';
		if(!empty($querytype)){
			if(!empty($searchvalue)){
				$data['searchvalue'] = $searchvalue;
				$where .= ' and '.$querytype.' LIKE \'%'.$searchvalue.'%\' ';
				$newlink .= '/searchvalue/'.$searchvalue.'/querytype/'.$querytype;
				$data['querytype'] = $querytype;
			} 
		}
		if(!empty($cityid)){ 
			$data['cityid'] = $cityid;
			$where .= empty($where)?' where ord.admin_id ='.$cityid:' and ord.admin_id = '.$cityid;
			$newlink .= '/cityid/'.$cityid;
		} 
		$data['orderstatus'] = '';
		if($orderstatus > 0){
			if($orderstatus  > 4){
			    $where .= empty($where)?' where ord.status > 3 ':' and ord.status > 3 ';
			}else{
				$newstatus = $orderstatus -1;
				$where .= empty($where)?' where ord.status ='.$newstatus:' and ord.status = '.$newstatus;
			}
			$data['orderstatus'] = $orderstatus;
			$newlink .= '/orderstatus/'.$orderstatus;
		}
		$link = IUrl::creatUrl('/adminpage/order/module/orderhistory'.$newlink);
		$pageshow = new page();
		$pageshow->setpage(IReq::get('page'),5);
		//order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
		//
		$default_cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		$cityid	= empty($default_cityid)?0:$default_cityid;
		$where .= ' and ord.admin_id ='.$cityid.'';
		$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."temporder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." order by ord.id desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");

		$shuliang  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."temporder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." ");
		$pageshow->setnum($shuliang);
		$data['pagecontent'] = $pageshow->getpagebar($link);
		$data['list'] = array();
		if($orderlist){
			foreach($orderlist as $key=>$value){
				$value['detlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."temporderdet where   order_id = ".$value['id']." order by id desc ");
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
				if(empty($value['cxdet'])){
					$value['cxdet'] = array();
				}else{				
					$cxdet = unserialize($value['cxdet']);
					$value['cxdet'] = array();
					foreach($cxdet as $k1=>$v1){
						$vv['name'] = $v1['name'];
						if($v1['type'] == 4){
							$vv['downcost'] = $value['shopps'];
						}else{
							$vv['downcost'] = str_replace('-￥','',$v1['downcost']);
						}
						if($v1['type'] != 1){
							$value['cxdet'][] = $vv;
						} 
						
					}
				}
				$data['list'][] = $value;	
			}
		}
		$data['scoretocost'] =Mysite::$app->config['scoretocost'];

		Mysite::$app->setdata($data);
		
	}
	//$msg 内容
	//$code 表示字符
	function testdata($msg,$code){ 
			$nowdate = date('Y-m-d',time());
			if(!file_exists(hopedir.'bkmovedata/movedata'.$code.'.php')){ 
				if(!is_dir(hopedir.'bkmovedata')){
					mkdir(hopedir.'bkmovedata', 0777);
				}
				$fp = @fopen(hopedir.'bkmovedata/movedata'.$code.'.php', 'w');
				@fclose($fp); 
			}
			$file=fopen(hopedir.'bkmovedata/movedata'.$code.'.php',"a+"); 
			fwrite($file, $msg); 
			fclose($file); 
	}
	function startmove(){
		//开始转移
		$logid = intval(IReq::get('logid'));
		$loginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog   where id = '".$logid."'  ");
		if(empty($loginfo)){
			$this->message('历史转移记录不存在');
		}
		if($loginfo['status'] > 0){
			$this->message('开始执行转移');
		}
		//更新状态为  1
		
		$cdata['excutime'] = time();
		$cdata['status'] = 1;
		$this->mysql->update(Mysite::$app->config['tablepre'].'historylog',$cdata," id = '".$logid."' ");
		
		$data['link'] = IUrl::creatUrl('/adminpage/order/module/moverorder/datatype/json');
		$data['linktitle'] = '开始转移订单数据';
		$this->success($data); 
		
	}
	
	//移动订单
	function moverorder(){
		$page = intval(IReq::get('page'));
		$page = empty($page)?1:$page; 
		$logid = intval(IReq::get('logid'));
		$loginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog   where id = '".$logid."'  ");
		if(empty($loginfo)){
			$this->message('历史转移记录不存在');
		}
		if($loginfo['status'] > 1){
			$this->message('已转移订单完毕');
		}
		$starnum = ($page-1)*1000;
		
		$datalist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where id <= ".$loginfo['maxorderid']."    order by id asc limit ".$starnum.", 1000 ");
		if(empty($datalist)){
			//更新historylog 为   2 
		 
			$cdata['status'] = 2;
			$this->mysql->update(Mysite::$app->config['tablepre'].'historylog',$cdata," id = '".$logid."' ");
			$data['link'] = IUrl::creatUrl('/adminpage/order/module/moverorderdet/datatype/json');
			$data['linktitle'] = '开始转移订单详情数据'; 
			$this->success($data); 
		}else{
			foreach($datalist as $key=>$value){
				//将数据插入 临时订单中
				$this->mysql->insert(Mysite::$app->config['tablepre'].'temporder',$value); 
			}
			$this->testdata(var_export($datalist,true),date('Y-m-d',($loginfo['startdotime']-1)).'_order_page_'.$page);
			$page = $page+1;
			$data['link'] = IUrl::creatUrl('/adminpage/order/module/moverorder/datatype/json/page/'.$page);
			$data['linktitle'] = '转移订单第'.$page.'数据';
			$this->success($data); 
		}

	}
	//移动订单详情
	function moverorderdet(){
		$page = intval(IReq::get('page'));
		$page = empty($page)?1:$page; 
		$logid = intval(IReq::get('logid'));
		$loginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog   where id = '".$logid."'  ");
		if(empty($loginfo)){
			$this->message('历史转移记录不存在');
		}
		if($loginfo['status'] > 2){
			$this->message('订单详情已转移完毕');
		}
		$starnum = ($page-1)*1000;
		
		$datalist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id <= ".$loginfo['maxorderid']."    order by order_id asc limit ".$starnum.", 1000 ");
		if(empty($datalist)){
			$cdata['status'] = 3;
			$this->mysql->update(Mysite::$app->config['tablepre'].'historylog',$cdata," id = '".$logid."' ");
			$data['link'] = IUrl::creatUrl('/adminpage/order/module/moveorderstatus/datatype/json');
			$data['linktitle'] = '开始转移订单详情数据'; 
			$this->success($data); 
		}else{
			foreach($datalist as $key=>$value){
				//将数据插入 临时订单中
				$this->mysql->insert(Mysite::$app->config['tablepre'].'temporderdet',$value); 
			}
			$this->testdata(var_export($datalist,true),date('Y-m-d',($loginfo['startdotime']-1)).'_orderdet_page_'.$page);
			$page = $page+1;
			$data['link'] = IUrl::creatUrl('/adminpage/order/module/moverorderdet/datatype/json/page/'.$page);
			$data['linktitle'] = '转移订单第'.$page.'数据';
			$this->success($data); 
		}
	}
	function moveorderstatus(){
		$page = intval(IReq::get('page'));
		$page = empty($page)?1:$page; 
		$logid = intval(IReq::get('logid'));
		$loginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog   where id = '".$logid."'  ");
		if(empty($loginfo)){
			$this->message('历史转移记录不存在');
		}
		if($loginfo['status'] > 3){
			$this->message('订单状态已转移完毕');
		}
		$starnum = ($page-1)*1000;
		
		$datalist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderstatus where orderid <= ".$loginfo['maxorderid']."    order by orderid asc limit ".$starnum.", 1000 ");
		if(empty($datalist)){
			$cdata['status'] = 4;
			$this->mysql->update(Mysite::$app->config['tablepre'].'historylog',$cdata," id = '".$logid."' ");
			$data['link'] = IUrl::creatUrl('/adminpage/order/module/moveold/datatype/json');
			$data['linktitle'] = '处理旧数据'; 
			$this->success($data); 
		}else{
			foreach($datalist as $key=>$value){
				//将数据插入 临时订单中
				$this->mysql->insert(Mysite::$app->config['tablepre'].'temporderstatus',$value); 
			}
			$this->testdata(var_export($datalist,true),date('Y-m-d',($loginfo['startdotime']-1)).'_orderstatus_page_'.$page);
			$page = $page+1;
			$data['link'] = IUrl::creatUrl('/adminpage/order/module/moveorderstatus/datatype/json/page/'.$page);
			$data['linktitle'] = '转移订单第'.$page.'数据';
			$this->success($data); 
		}
	}
	
	function moveold(){
		$logid = intval(IReq::get('logid'));
		$loginfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog   where id = '".$logid."'  ");
		if(empty($loginfo)){
			$this->message('历史转移记录不存在');
		}
		if($loginfo['status'] > 4){
			$this->message('开始执行转移');
		}
		//清除旧数据
		$cdata['status'] = 50;
		$this->mysql->update(Mysite::$app->config['tablepre'].'historylog',$cdata," id = '".$logid."' ");
		$this->mysql->delete(Mysite::$app->config['tablepre'].'order',"id <= '".$loginfo['maxorderid']."'");
		$this->mysql->delete(Mysite::$app->config['tablepre'].'orderdet',"order_id <= '".$loginfo['maxorderid']."'");
		$this->mysql->delete(Mysite::$app->config['tablepre'].'orderstatus',"orderid <= '".$loginfo['maxorderid']."'"); 
		
		$this->success('ok');
	}
	  
	function orderhistorylog(){
		if($this->checkhistoryset()){
			
		}
		
		$data['historystatus'] = array(
		0=>'未执行',
		1=>'开始执行',
		2=>'完成订单转移',
		3=>'完成订单详情转移',
		4=>'完成订单状态转移',
		50=>'完成删除',
		);
		
		//苹果系统12
		$link = IUrl::creatUrl('/adminpage/order/module/orderhistorylog');
		$pageshow = new page();
		$pageshow->setpage(IReq::get('page'),10);
		//order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
		//
		 
		$orderlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."historylog    order by id desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");

		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."historylog   ");
		$pageshow->setnum($shuliang);
		$data['pagecontent'] = $pageshow->getpagebar($link);
		$data['list'] = $orderlist; 
		Mysite::$app->setdata($data); 
	}
	function addhistorylog(){
		//开始计算  订单转移的 时间
		//第一步  查看是否有历史记录  若无  则调用 订单的 第一个ID 的 构造时间判断月份  计算当月最后一天
		
		//$BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
		//echo $BeginDate;
		//echo "<br/>";
		//echo date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
		
		
		$lastlog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog    order by id desc   ");
		if(empty($lastlog)){//当最后一天记录 不存在的时候 选择第一个订单开始所在月份作为开始处理月 
			 
			$firstorder = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order    order by id asc  ");
			$BeginDate = date('Y-m-01',$firstorder['addtime']);//找到第一个订单所在月份和天数
			$lasttime = strtotime(date('Y-m-d', strtotime("$BeginDate +1 month -1 day")))+86400;
			//求出最大订单ID  
			$maxorder = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order  where addtime < ".$lasttime."  order by id desc  ");//使用降序  
			$maxorderid = $maxorder['id'];

		}else{
			$BeginDate = date('Y-m-01',$lastlog['startdotime']);
			$lasttime = strtotime(date('Y-m-d', strtotime("$BeginDate +1 month -1 day")))+86400;
			//求出最大订单ID  
			$maxorder = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order  where addtime < ".$lasttime."  order by id desc  ");//使用降序  
			$maxorderid = $maxorder['id'];
		}
		$data['maxday'] = date('Y-m-d',$lasttime-1);
		$data['maxorderid'] = $maxorderid;
		
		// print_r($data);
		
		
		Mysite::$app->setdata($data); 
		
		
		
	}
	
	function savehistorylog(){
		//
		$domonthday = trim(IReq::get('domonthday'));
		$checkmaxorderid = trim(IReq::get('maxorderid'));
		$checktime = strtotime($domonthday)+86400;
		
		$checkonnn = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog  where startdotime >= ".$checktime."  order by id desc   ");
		if(!empty($checkonnn)){
			$this->message('该日期下的记录已转移过');
		}
		$checkonnn = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog  where status != 50  order by id desc   ");
		//print_r($checkonnn);
		if(!empty($checkonnn)){
			$this->message('还存在未处理完成的记录不能新建');
		}
		
		
		
		$lastlog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."historylog    order by id desc   ");
		if(empty($lastlog)){//当最后一天记录 不存在的时候 选择第一个订单开始所在月份作为开始处理月 
			 
			$firstorder = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order    order by id asc  ");
			$BeginDate = date('Y-m-01',$firstorder['addtime']);//找到第一个订单所在月份和天数
			$lasttime = strtotime(date('Y-m-d', strtotime("$BeginDate +1 month -1 day")))+86400;
			//求出最大订单ID  
			$maxorder = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order  where addtime < ".$lasttime."  order by id desc  ");//使用降序  
			$maxorderid = $maxorder['id'];

		}else{
			$BeginDate = date('Y-m-01',$lastlog['startdotime']);
			$lasttime = strtotime(date('Y-m-d', strtotime("$BeginDate +1 month -1 day")))+86400;
			//求出最大订单ID  
			$maxorder = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order  where addtime < ".$lasttime."  order by id desc  ");//使用降序  
			$maxorderid = $maxorder['id'];
		}
		if($checktime != $lasttime){
			$this->message('选择时间错误');
		}
		if($checkmaxorderid != $maxorderid ){
			$this->message('订单ID错误');
		}
		$checkstime = time() - 63*86400;
		if($checkstime < $checktime){
			$this->message('执行保留最近3个月内的订单');
		} 
		$newdata['addtime'] = time();
		$newdata['startdotime'] = $lasttime;
		$newdata['maxorderid'] = $maxorderid; 
		$newdata['status'] = 0;
		$newdata['excutime'] = 0; 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'historylog',$newdata);
		$this->success('ok');
		
	}
	function historylogdel(){ 
		$id = intval(IReq::get('id'));
		$this->mysql->delete(Mysite::$app->config['tablepre'].'historylog',"   id='".$id."' and status = 0 "); 
		$link = IUrl::creatUrl('adminpage/order/module/orderhistorylog');
		$this->success('ok');
	}
	function checkhistoryset(){
		//判断记录表是否存在 ---不存在创建记录表
		//判断 临时数据表是否存在  ---不存在创建历史表
		$talbepre = Mysite::$app->config['tablepre'];
		$checkinfo = $this->mysql->query("show tables like '".Mysite::$app->config['tablepre']."historylog';");
		$checkinfo = $this->mysql->assoc($checkinfo);
		if(empty($checkinfo)){//表示这个表不存在
		    //创建历史订单表 记录表
			$this->mysql->query("CREATE TABLE IF NOT EXISTS `".Mysite::$app->config['tablepre']."historylog` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `addtime` int(12) NOT NULL COMMENT '创建时间',
  `startdotime` 

int(12) NOT NULL COMMENT '操作多少月份之前的订单',
  `maxorderid` int(20) NOT NULL COMMENT '最大订单ID',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '0.

未执行 1开始执行  2。完成xiaozu_order转移  3完成xiaozu_orderdet 转移  4完成小组_orderstatus 转移   50 完成删除',
  `excutime` int(12) NOT NULL COMMENT '开

始执行时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
      
			
			
			$checkorder = $this->mysql->query("show tables like '".Mysite::$app->config['tablepre']."temporder';");
			$checkorder = $this->mysql->assoc($checkorder);
			if(empty($checkorder)){
				 //创建历史订单表 
				$info = $this->mysql->getarr("show create table `".Mysite::$app->config['tablepre']."order`"); 
				$sqldata = $info['0']['Create Table'];
				$ordersqldata = str_replace(Mysite::$app->config['tablepre']."order",Mysite::$app->config['tablepre']."temporder",$sqldata); 
				// print_r($ordersqldata);
				$this->mysql->query($ordersqldata);
			}else{
				// echo Mysite::$app->config['tablepre']."temporder".'已安装';
			}
			
			$checkorderdet = $this->mysql->query("show tables like '".Mysite::$app->config['tablepre']."temporderdet';");
			$checkorderdet = $this->mysql->assoc($checkorderdet);
			if(empty($checkorderdet)){
				//创建历史订单详情表 
				$info = $this->mysql->getarr("show create table `".Mysite::$app->config['tablepre']."orderdet`"); 
				$sqldata = $info['0']['Create Table'];
				$ordersqldata = str_replace(Mysite::$app->config['tablepre']."orderdet",Mysite::$app->config['tablepre']."temporderdet",$sqldata); 
				// print_r($ordersqldata);
				$this->mysql->query($ordersqldata);
			}else{
				// echo Mysite::$app->config['tablepre']."temporderdet".'已安装';
			}
			
			$checkorderstatus = $this->mysql->query("show tables like '".Mysite::$app->config['tablepre']."temporderstatus';");
			$checkorderstatus = $this->mysql->assoc($checkorderstatus);
			if(empty($checkorderstatus)){
				//创建历史订单状态表 
				$info = $this->mysql->getarr("show create table `".Mysite::$app->config['tablepre']."orderstatus`"); 
				$sqldata = $info['0']['Create Table'];
				$ordersqldata = str_replace(Mysite::$app->config['tablepre']."orderstatus",Mysite::$app->config['tablepre']."temporderstatus",$sqldata); 
				// print_r($ordersqldata);
				$this->mysql->query($ordersqldata);
			}else{
				// echo Mysite::$app->config['tablepre']."temporderstatus".'已安装';
			} 
		}
		// print_r($checkinfo);
		return true;
	}

}
?>