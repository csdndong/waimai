<?php
class method   extends areaadminbaseclass
{
	function index(){
	 	    	 $link = IUrl::creatUrl('areaadminpage/order/module/orderlist');
           $this->refunction('',$link);
	}
	//快速下单
	 function adminfastfoods(){
		 
		 $cityid = $this->admin['cityid'];
   	    $data['shoplist'] = $this->mysql->getarr("select id,shopname  from ".Mysite::$app->config['tablepre']."shop where is_open = 1 and admin_id = '".$cityid."' and is_pass=1 and endtime > ".time()." order by id limit 0,1000");
   	    // 营业时间	is_open 是否营业	is_pass 是否通过审核	is_recom 是否是推荐店铺 endtime

   	     Mysite::$app->setdata($data);

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
	    	$starttime = IReq::get('starttime');
	    	$endtime = IReq::get('endtime');
	    	$nowday = date('Y-m-d',time());
	    	$starttime = empty($starttime)? $nowday:$starttime;
	    	$endtime = empty($endtime)? $nowday:$endtime;
			$admin_id = $this->admin['cityid'];
	      $where = '  where ord.admin_id = '.$admin_id.' and  ord.addtime > '.strtotime($starttime.' 00:00:00').' and ord.addtime < '.strtotime($endtime.' 23:59:59');
		 
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
	          	$newstatus = $orderstatus -1;
	          	$where .= empty($where)?' where ord.status ='.$newstatus:' and ord.status = '.$newstatus;
	          }
	          $data['orderstatus'] = $orderstatus;
	          $newlink .= '/orderstatus/'.$orderstatus;
	    	}
	    	$link = IUrl::creatUrl('/areaadminpage/order/module/orderlist'.$newlink);
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
		
	#	 print_r( $data['list'] );
	     $data['scoretocost'] =Mysite::$app->config['scoretocost'];
	     Mysite::$app->setdata($data);
	}
	//正常订单定义打印
	function orderprint(){
		$orderid = intval(IReq::get('orderid'));
		$data['printtype'] = trim(IReq::get('printtype'));//打印类型		 
		$this->setstatus();
		$data['orderinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order  where id ='".$orderid."' ");
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
	  $where = '  where    ord.admin_id = '.$this->admin['cityid'].' and    ( paytype = 0  or  (paytype=1 && paystatus=1) ) and  ord.addtime > '.strtotime($nowday.' 00:00:00').' and ord.addtime < '.strtotime($nowday.' 23:59:59');
		//查询当天所有订单数据
 
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
		 Mysite::$app->setdata($data);
	}
	 
	function ordertotal()
	{
		$data['buyerstatus'] = array(
		'0'=>'待处理订单',
		'1'=>'审核通过,待发货',
		'2'=>'订单已发货',
		'3'=>'订单完成',
		'4'=>'买家取消订单',
		'5'=>'卖家取消订单'
		);
		$querytype = IReq::get('querytype');
		$searchvalue = IReq::get('searchvalue');
		$orderstatus = intval(IReq::get('orderstatus'));
		$starttime = IReq::get('starttime');
		$endtime = IReq::get('endtime');
		$nowday = date('Y-m-d',time());
		$starttime = empty($starttime)? $nowday:$starttime;
		$endtime = empty($endtime)? $nowday:$endtime;
	  $where = '  where ord.admin_id = '.$this->admin['cityid'].' and   ord.suretime > '.strtotime($starttime.' 00:00:00').' and ord.suretime < '.strtotime($endtime.' 23:59:59');
		$data['starttime'] = $starttime;
		$data['endtime'] = $endtime;
		$data['querytype'] = '';
		$data['searchvalue'] = '';
		if(!empty($querytype))
		{
			if(!empty($searchvalue)){
				 $data['searchvalue'] = $searchvalue;
	   	   $where .= ' and '.$querytype.' =\''.$searchvalue.'\' ';
	   	   $data['querytype'] = $querytype;
			}
		}

		$data['list'] = $this->mysql->getarr("select count(ord.id) as shuliang,ord.status,sum(allcost) as allcost,sum(scoredown) as scorecost from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." group by ord.status order by ord.id desc limit 0, 10");

		Mysite::$app->setdata($data);
	}
	function orderyjin()
	{
		$searchvalue = IReq::get('searchvalue');
		$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime'));
		
		$quyuguanli = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."admin where groupid = 4 limit 0,1000");
  	    $data['quyuguanli'] = $quyuguanli;
		
		$newlink = '';
		$where= '';
		$where2 = '';
		$where3='';
		$data['searchvalue'] = '';
		if(!empty($searchvalue))
		{
			   $data['searchvalue'] = $searchvalue;
	   	   $where .= ' where shopname = \''.$searchvalue.'\' ';
	   	   $newlink .= '/searchvalue/'.$searchvalue;
		}
		$data['starttime'] = '';
		if(!empty($starttime))
		{
			 $data['starttime'] = $starttime;
			 $where2 .= ' and  suretime > '.strtotime($starttime.' 00:00:01').' ';
			 $where3 .= ' and  jstime > '.strtotime($starttime.' 00:00:01').' ';
	   	 $newlink .= '/starttime/'.$starttime;
		}
		$data['endtime'] = '';
		if(!empty($endtime))
		{
			 $data['endtime'] = $endtime;
			 $where2 .= ' and  suretime < '.strtotime($endtime.' 23:59:59').' ';
			 $where3 .= ' and  jstime < '.strtotime($endtime.' 23:59:59').' ';
	   	 $newlink .= '/endtime/'.$endtime;
		}
		
		$admin_id = $this->admin['cityid'];
  	 if(!empty($admin_id)){
			$where .=empty($where)?' where admin_id = \''.$admin_id.'\'': ' and admin_id = \''.$admin_id.'\' ';
			$newlink .= '/admin_id/'.$admin_id; 
		}
		
		$data['admin_id'] = $admin_id;
		
		$link = IUrl::creatUrl('areaadminpage/order/module/orderyjin'.$newlink);
	    $data['outlink'] =IUrl::creatUrl('areaadminpage/order/module/outtjorder/outtype/query'.$newlink);
	    $data['outlinkch'] =IUrl::creatUrl('areaadminpage/order/module/outtjorder'.$newlink);
		$pageinfo = new page();
		$pageinfo->setpage(IReq::get('page')); 
		$shoplist = $this->mysql->getarr("select id,shopname,yjin,shoptype from ".Mysite::$app->config['tablepre']."shop ".$where."   order by id asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		$list = array();
		if(is_array($shoplist))
		{
	     foreach($shoplist as $key=>$value)
	     {
	     	  //判断店铺配送类型
	     	  if($value['shoptype'] ==0){
	     	     $sendtype = $this->mysql->value(Mysite::$app->config['tablepre']."shopfast","sendtype","shopid = '".$value['id']."'");//$table,$row,$where=""
	     	  }elseif($value['shoptype'] ==  1){
	     	     $sendtype = $this->mysql->value(Mysite::$app->config['tablepre']."shopmarket","sendtype","shopid = '".$value['id']."'");//$table,$row,$where=""
	     	  }

	     	   $value['sendtype'] = $sendtype==1?'自送':'网站配送';
	         $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopdowncost) as shopdowncost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as allcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype =0 and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	         $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopdowncost) as shopdowncost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as allcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype !=0  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

	         $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
	         $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $line['score']/Mysite::$app->config['scoretocost']:0;
	         $value['onlinescore'] = $scordedown;
	         $value['online'] = $line['allcost'];//在线支付总金额
	         $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $shoptj['score']/Mysite::$app->config['scoretocost']:0;
	         $value['unlinescore'] = $scordedown;
	         $value['unline'] = $shoptj['allcost'] ;
	         $value['yhjcost'] = $line['yhcost'] +$shoptj['yhcost'];//使用优惠券			 
	         $value['cxcost'] = $line['cxcost'] +$shoptj['cxcost'];// 店铺优惠
			 $value['platdowncost'] = $line['shopdowncost'] +$shoptj['shopdowncost'];//网站部分
			 $value['shopdowncost'] = $value['cxcost'] - $value['platdowncost'];//平台部分 
	         $value['score'] = $value['unlinescore'] +$value['onlinescore']; //  使用积分
	         $value['bagcost'] = $line['bagcost'] +$shoptj['bagcost'];//   打包费
	         $value['pscost'] = $line['pscost'] +$shoptj['pscost'];//   配送费
	         $value['allcost'] = $line['allcost'] +$shoptj['allcost'] ;
	         $value['goodscost'] = $line['shopcost'] +$shoptj['shopcost'];
			 $jsinfo= $this->mysql->select_one("select sum(yjcost) as yjcost,sum(acountcost) as acountcost from ".Mysite::$app->config['tablepre']."shopjs  where shopid ='".$value['id']."'".$where3."  ");
				#print_r($jsinfo);
				if(!empty($jsinfo)){
					$value['yje']=$jsinfo['yjcost'];
					$value['jse']=$jsinfo['acountcost'];
				}else{
					$value['yje']='未结算';
					$value['jse']='未结算';
				}
				
		       $value['outdetail'] =IUrl::creatUrl('areaadminpage/order/module/outdetail/outtype/query/shopid/'.$value['id'].$newlink);
		       $list[] = $value;
		   }
		}

		$data['list'] =$list;

		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop ".$where."  ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar($link);
		Mysite::$app->setdata($data);
	}
	 function draworderinfo(){  //订单中心点击退款
	  $orderid =  IFilter::act(IReq::get('orderid'));
	   $data['oderinfo'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid." order by  id desc  limit 0,2");
	   
	   
	   
	    Mysite::$app->setdata($data); 
	}
  
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
					$this->message('订单状态不能退宽');
				}
				if($orderinfo['paystatus']  != 1){
					$this->message('订单未支付');
				}
				if($orderinfo['is_reback'] > 0){
					 $this->message('有退款单未处理');
				}
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
			   $zengcost = IReq::get('zengcost');
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
			   
			   $ordCls->writewuliustatus($orderinfo['id'],14,$orderinfo['paytype']);  // 管理员退款给用户 物流信息
			   
			   
			   //退款成功给用户 下面写退款记录
			   $drawdata['uid'] = $orderinfo['buyeruid'];
			   $memberinfoone  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$drawdata['uid']."  ");
			   $drawdata['username'] = $memberinfoone['username'];
			   $drawdata['bkcontent'] = IReq::get('reasons');
			   $drawdata['addtime'] = time();
			   $drawdata['orderid'] = 	$orderinfo['id'];
			   $drawdata['shopid'] = 	$orderinfo['shopid'];
			   $drawdata['cost'] = 	 $zengcost;
			   $drawdata['status'] = 	1;
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
				if($orderinfo['is_reback'] > 0){
					 $this->message('订单已退款成功不能重复操作');
				}
				$zengcost = IReq::get('zengcost');
			   $is_phonenotice = IReq::get('is_phonenotice');
			   $notice_content = IReq::get('notice_content');
				$drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$id." order by  id desc  limit 0,2");
				 
	   	     #  if(empty($drawbacklog)) $this->message('order_emptybaklog');
	   	     #  if($drawbacklog['status'] !=  0) $this->message('order_baklogcantdoover');
	   	    #   if($orderinfo['status'] > 2) $this->message('order_cantbak');
	   	       $arr['is_reback'] = 3;//订单状态
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
		 $drawbackloglist = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid='".$orderinfo['id']."' order by addtime desc ");
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

				//写消息给买家
				if(!empty($orderinfo['buyeruid'])){
					$detail = '';
					$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
					if($orderinfo['paystatus'] == 1&& $orderinfo['paytype'] != 0){ 
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
				$ordCls->writewuliustatus($orderinfo['id'],5,$orderinfo['paytype']);  // 管理员 操作  取消订单

				$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '".$orderinfo['id']."' and status !=3 ");  //写配送订单 
				//还库存
				$ordetinfo = $this->mysql->getarr("select ort.goodscount,go.id,go.sellcount,go.count as stroe from ".Mysite::$app->config['tablepre']."orderdet as ort left join  ".Mysite::$app->config['tablepre']."goods as go on go.id = ort.goodsid   where ort.order_id='".$id."' and  go.id > 0 ");
				foreach($ordetinfo as $key=>$value){
					$newdata['count'] = $value['stroe']+	$value['goodscount'];
					$newdata['sellcount'] = $value['sellcount'] - $value['goodscount'];
					$this->mysql->update(Mysite::$app->config['tablepre']."goods",$newdata,"id='".$value['id']."'");
				}
				$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$id' and status !=3 ");  //写配送订单
			 
			if($orderinfo['pstype'] == 2  ){
                $psbinterface = new psbinterface();
                if($psbinterface->psbdraworder($orderinfo['id'])){

                } 
			}			
				if($suresend == 1){
					//短信发送
					$ordCls = new orderclass();
					$ordCls->psbunorder($id,$reasons);
				}
	   	  break;
	   	 case 'pass':
				if($orderinfo['status'] != 0)  $this->message('order_cantpass');
				if($orderinfo['is_reback'] > 0 && $orderinfo['is_reback'] < 3) $this->message('order_ispaycantdo');
				$checkstr = Mysite::$app->config['auto_send'];
				$is_autopreceipt = $this->mysql->select_one("select is_autopreceipt from ".Mysite::$app->config['tablepre']."shop where id='".$orderinfo['shopid']."' ");
				#$allowed_is_make = Mysite::$app->config['allowed_is_make']; //  1商家必须确认制作 0订单则默认通过制作
				$man_ispass = Mysite::$app->config['man_ispass'];
				if($man_ispass == 1){
				if($is_autopreceipt['$is_autopreceipt'] == 1){
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
							$admin_id = $orderinfo['admin_id'];
                                                        $psset = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$admin_id."'   ");
                                                        $checkpsyset = $psset['psycostset'];
                                                        $bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
                                                        $psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei; 
							$psdata['psycost'] = $checkpsyset == 1?Mysite::$app->config['psycost']:$bilifei; 
							$this->mysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单 
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
	   	  break;
	   	  case 'send':
				if($orderinfo['is_reback'] > 0 && $orderinfo['is_reback'] < 3) $this->message('order_bakpaycantdo');
			    if($orderinfo['status'] != 1)  $this->message('order_cantsend');
				  if($orderinfo['is_make'] == 0) $this->message('等待商家确认制作后才能发货');
				  if($orderinfo['is_make'] == 2) $this->message('商家不制作该订单不能发货');
				  if($orderinfo['pstype'] == 1) $this->message('商品发货订单请通过商家管理发货'); 
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

				$ordCls = new orderclass();
				$ordCls->writewuliustatus($orderinfo['id'],9,$orderinfo['paytype']);  // 管理员 操作 完成订单


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
				
				 
				 
				 
				//更新销量 
				$shuliang  = $this->mysql->select_one("select sum(goodscount) as sellcount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."'  ");
				if(!empty($shuliang) && $shuliang['sellcount'] > 0){
					$this->mysql->update(Mysite::$app->config['tablepre'].'shop','`sellcount`=`sellcount`+'.$shuliang['sellcount'].'',"id ='".$orderinfo['shopid']."' ");
				}
				//自动完成配送单
				$this->mysql->update(Mysite::$app->config['tablepre'].'orderps','`status`=3',"orderid ='".$orderinfo['id']."' "); 
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$id."'");
				//更新用户成长值
				if(!empty($orderinfo['buyeruid']))
				{
					$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
//					if(!empty($memberinfo)){
//						$this->mysql->update(Mysite::$app->config['tablepre'].'member','`total`=`total`+'.$orderinfo['allcost'],"uid ='".$orderinfo['buyeruid']."' ");
//					}
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
				} 
	   	  break;
	   	  case 'del':
	   	      if($orderinfo['status'] < 4)  $this->message('order_cantdel');
	   	      $this->mysql->delete(Mysite::$app->config['tablepre'].'order',"id = '$id'");
			   $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$id' and status != 3");  //写配送订单 
	   	  break;
	   	  case 'drawback'://退款成功
	   	      //获取退款记录 
				$zengcost = IReq::get('zengcost');
				$is_phonenotice = IReq::get('is_phonenotice');
				$notice_content = IReq::get('notice_content'); 
				$drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$id." order by  id desc  limit 0,2");
				  if(empty($drawbacklog)) $this->message('order_emptybaklog');
	   	        if($drawbacklog['status'] != 0) $this->message('order_baklogcantdoover');
	   	        if($orderinfo['status'] > 2) $this->message('order_cantbak');
			    if($orderinfo['is_reback'] > 1) $this->message('订单已退款成功不能重复操作');
				$arr['is_reback'] = 2;//订单状态
				$arr['status'] = 4;
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$id."'");
				$data['bkcontent'] = IReq::get('reasons');
				$data['status'] = 1;//
				$data['admin_id'] = ICookie::get('adminuid') ;
				$data['cost'] = $zengcost;
				$this->mysql->update(Mysite::$app->config['tablepre'].'drawbacklog',$data,"id='".$drawbacklog['id']."'");

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
						if($is_phonenotice == 1){
							$this->fasongmsg($notice_content,$orderinfo['buyerphone']); 
						} 
					}  
				} 
				if($orderinfo['pstype'] == 2){ 
					$psbinterface = new psbinterface();
					if($psbinterface->psbdraworder($orderinfo['id'])){
						 
					}
				}
				$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$id' and status != 3");  //写配送订单  
				$ordCls = new orderclass(); 
				$ordCls->writewuliustatus($orderinfo['id'],14,$orderinfo['paytype']);  // 管理员退款给用户 物流信息 
				$ordCls->noticeback($id);
	   	  break;
	   	  case 'undrawback'://退款不成功
				$zengcost = IReq::get('zengcost');
				$is_phonenotice = IReq::get('is_phonenotice');
				$notice_content = IReq::get('notice_content');
				$drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$id." order by  id desc  limit 0,2");
				if(empty($drawbacklog)) $this->message('order_emptybaklog');
				if($drawbacklog['status'] !=  0) $this->message('order_baklogcantdoover');
				if($orderinfo['is_reback'] == 2) $this->message('订单已退款成功不能重复操作');
				if($orderinfo['status'] > 3) $this->message('订单已取消不用操作'); 
				$arr['is_reback'] = 0;//订单状态
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$id."'");
				$data['bkcontent'] = IReq::get('reasons');
				$data['status'] = 2;//
				$data['admin_id'] = ICookie::get('adminuid') ; 
				$this->mysql->delete(Mysite::$app->config['tablepre'].'drawbacklog',"orderid = '$id' ");
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
	           #    $ordCls->noticeunback($id); 
	   	  break;
	   	   case 'psyuan':
				if($orderinfo['is_goshop'] == 1) $this->message('到店买单订单不需要配送');
				if($orderinfo['status'] > 2)  $this->message('order_baklogcantdoover'); 
				if(!empty($orderinfo['psuid'])) $this->message('order_setpsyuan'); 
				if($orderinfo['pstype'] == 2) $this->message('第三方分配订单'); 
				$userid = intval(IReq::get('userid'));
				if(empty($userid)) $this->message('order_emptypsyuan');
				$memberinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$userid."' and `group` =2 ");

				if(empty($memberinfo)) $this->message('order_emptypsyuan');

				//自动生成配送单------|||||||||||||||-----------------------
				if($orderinfo['shoptype'] == 100){//网站配送自动生成配送费
					$checkpsset =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$orderinfo['admin_id']."' "); 
					if(empty($checkpsset) || $checkpsset['pttopsb'] == 1){
						$this->message('已分配到第三方配送系统');
					}else{ 
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
					}
				}else{ 
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
		$where .= ' and ord.status < 2';

		$firstarea = intval(IReq::get('firstarea'));


		if(!empty($firstareain)){
	      $where .= " and FIND_IN_SET('".$firstareain."',`areaids`)";
	  }
		$shuliang1  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."order as ord   ".$where." and ord.admin_id = ".$this->admin['cityid']."  and ord.is_make = 0 ");
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
		   $where .= '  where ord.suretime > '.$starttime.' and ord.suretime < '.$endtime;
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
	function outtjorder()
	{
		$outtype = IReq::get('outtype');
		if(!in_array($outtype,array('query','ids')))
		{
		  	header("Content-Type: text/html; charset=UTF-8");
			 echo '查询条件错误';
			 exit;
		}
		$where = '';
		$where2 = '';
		$where3='';
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
			  $where .= ' where id in('.$id.') ';

			  $searchvalue = trim(IReq::get('searchvalue'));
			  $where .= !empty($searchvalue)? ' and shopname = \''.$searchvalue.'\'':'';
		 //   $data['searchvalue'] = $searchvalue;
	   //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

		   $starttime = trim(IReq::get('starttime'));
		   $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';
$where3 .= !empty($starttime)? ' and  jstime > '.strtotime($starttime.' 00:00:01').' ':'';
		   $endtime = trim(IReq::get('endtime'));
		   $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';
$where3 .= !empty($starttime)? ' and  jstime > '.strtotime($starttime.' 00:00:01').' ':'';
		}else{
		   $searchvalue = trim(IReq::get('searchvalue'));
		   $where .= !empty($searchvalue)? ' where shopname = \''.$searchvalue.'\'':'';
		 //   $data['searchvalue'] = $searchvalue;
	   //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

		   $starttime = trim(IReq::get('starttime'));
		  $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';
$where3 .= !empty($starttime)? ' and  jstime > '.strtotime($starttime.' 00:00:01').' ':'';
		   $endtime = trim(IReq::get('endtime'));
		   $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';
$where3 .= !empty($endtime)? ' and  jstime < '.strtotime($endtime.' 23:59:59').' ':'';	
		}
		$admin_id = intval(IReq::get('admin_id'));
  	 if(!empty($admin_id)){
			$where .= empty($where)?' where admin_id = \''.$admin_id.'\'':' and admin_id = \''.$admin_id.'\' ';
			 
		}
		
		
		$shoplist = $this->mysql->getarr("select id,shopname,yjin from ".Mysite::$app->config['tablepre']."shop ".$where."   order by id asc  limit 0,2000");
		$list = array();
		if(is_array($shoplist))
		{
	     foreach($shoplist as $key=>$value)
	     {



	     	    $sendtype = $this->mysql->value(Mysite::$app->config['tablepre']."shopfast","sendtype","shopid = '".$value['id']."'");//$table,$row,$where=""

	     	   $value['sendtype'] = empty($value['sendtype'])?'网站配送':'自送';

	         $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(shopdowncost) as shopdowncost,sum(allcost) as allcost,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype =0 and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	         $line= $this->mysql->select_one("select count(id) as shuliang,sum(shopdowncost) as shopdowncost,sum(allcost) as allcost,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype =1  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");


	     	   $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数

	         $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $line['score']/Mysite::$app->config['scoretocost']:0;
	         $value['onlinescore'] = $scordedown;
	         $value['online'] = $line['allcost'];//在线支付总金额
	         $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $shoptj['score']/Mysite::$app->config['scoretocost']:0;
	         $value['unlinescore'] = $scordedown;
	         $value['unline'] = $shoptj['allcost'];
	         $value['yhjcost'] = $line['yhcost'] +$shoptj['yhcost'];//使用优惠券
	         $value['cxcost'] = $line['cxcost'] +$shoptj['cxcost'];// 店铺优惠
	         $value['platcxcost'] = $line['shopdowncost'] +$shoptj['shopdowncost'];
			 $value['shopcxcost'] = $value['cxcost'] - $value['platcxcost'];
			 $value['score'] = $value['unlinescore'] +$value['onlinescore']; //  使用积分
	         $value['bagcost'] = $line['bagcost'] +$shoptj['bagcost'];//   打包费
	         $value['pscost'] = $line['pscost'] +$shoptj['pscost'];//   配送费
	         $value['allcost'] = $line['shopcost'] +$shoptj['shopcost'] - $value['cxcost'];
	         $value['goodscost'] = $line['shopcost'] +$shoptj['shopcost'];
	          $jsinfo= $this->mysql->select_one("select sum(yjcost) as yjcost,sum(acountcost) as acountcost from ".Mysite::$app->config['tablepre']."shopjs  where shopid ='".$value['id']."'".$where3."  ");
				if(!empty($jsinfo)){
					$value['yje']=$jsinfo['yjcost'];
					$value['jse']=$jsinfo['acountcost'];
				}else{
					$value['yje']='未结算';
					$value['jse']='未结算';
				}

		       $list[] = $value;
		      // $list[] = $value1;
		   }
		}
		 $outexcel = new phptoexcel();
		 $titledata = array('店铺名称','配送方式','订单数量','线上支付','线下支付','优惠券','总促销','平台承担','商家承担','积分抵扣','配送费','商品总价','打包费','佣金');
		 $titlelabel = array('shopname','sendtype','orderNum','online','unline','yhjcost','cxcost','platcxcost','shopcxcost','score','pscost','goodscost','bagcost','yje');
		// $datalist = $this->mysql->getarr("select card,card_password,cost from ".Mysite::$app->config['tablepre']."card where id > 0 ".$where."   order by id desc  limit 0,2000 ");
		 $outexcel->out($titledata,$titlelabel,$list,'','商家结算');
	}
	//导出商家结算详情
	function outdetail()
	{
		// 订单号    时间    订单内容    配送费用  总价
		   $shopid =  intval(IReq::get('shopid'));

		   if(empty($shopid))
		   {
		   	  header("Content-Type: text/html; charset=UTF-8");
			  	 echo '店铺获取失败';
			  	 exit;
		   }
		   $shoplist = $this->mysql->select_one("select id,shopname,yjin,shoptype from ".Mysite::$app->config['tablepre']."shop  where id='".$shopid."'   order by id asc  limit 0,2000");
		   if(empty($shoplist))
		   {
		   	  header("Content-Type: text/html; charset=UTF-8");
			  	 echo '店铺获取失败';
			  	 exit;
		   }
		   
		 
		   $where = '';
		   $where2 = '';
		   $starttime = trim(IReq::get('starttime'));
		   $where2 .= !empty($starttime)? ' and  posttime > '.strtotime($starttime.' 00:00:01').' ':'';

		   $endtime = trim(IReq::get('endtime'));
		   $where2 .= !empty($endtime)? ' and  posttime < '.strtotime($endtime.' 23:59:59').' ':'';
 	 
		   $orderlist = $this->mysql->getarr("select id,cxids,dno,allcost,yhjcost,cxcost,shopdowncost,bagcost,shopps,shopcost,addtime,posttime,pstype ,paytype,paystatus from ".Mysite::$app->config['tablepre']."order where shopid = '".$shopid."' and  status = 3 ".$where2." order by id asc  limit 0,2000");
		   $list = array();
		   if(is_array($orderlist))
		   {
		      foreach($orderlist as $key=>$value)
		      {
		   	     $detlist = $this->mysql->getarr("select goodsname,goodscount as shuliang from ".Mysite::$app->config['tablepre']."orderdet  where order_id = '".$value['id']."' and shopid > 0  order by id asc  limit 0,5");
		   	     $detinfo = '';
		   	     if(is_array($detlist))
		         {
		         	   foreach($detlist as $keys=>$val)
		         	   {

		         	   	  $detinfo .= $val['goodsname'].'/'.$val['shuliang'].'份,';
		         	   }
		         }
				 $value['cxname'] = '';
				 if(!empty($value['cxids'])){
					$value['cxname'] = ':id('.$value['cxids'].')';
					$tempb = explode(',',$value['cxids']);
					$arrx = array();
					foreach ($tempb as $m => $v) {
						if (empty($val)) {
							continue;
						}
						$arrx[] = $val;
					}
					if(count($arrx) > 0){
						$newlist = $this->mysql->getarr("select name from ".Mysite::$app->config['tablepre']."rule where FIND_IN_SET(id,'".join(',',$arrx)."')    order by id desc "); 
						foreach($newlist as $c=>$d){
							 $value['cxname'] = $d['name'].'/'.$value['cxname'];
						}
					}
				 }
				 
		         $value['content'] = $detinfo;
		         $value['payname'] = $value['paytype'] == 0?'货到支付':'在线支付';
		         $value['dotime'] = date('Y-m-d H:i:s',$value['addtime']);
		         $value['posttime'] = date('Y-m-d H:i:s',$value['posttime']);
		         $value['pstype'] = $value['pstype'] == 0?'平台':'自送';
				 $value['cxcost'] = $value['cxcost'];//总促销
				 $value['platcx'] = $value['shopdowncost'];//店铺承担
				 $value['shopdowncost'] = $value['cxcost'] - $value['shopdowncost'];
				
				 $value['yhjcost'] = $value['yhjcost'];//使用优惠券抵扣金额
				 $value['scoredown'] = $value['scoredown'];//抵扣积分
		         $list[] = $value;

		   	  }
		   }
		   // 超市商品总价 marketps 超市配送配送  店铺商品总价 shopps 店铺配送费 pstype 配送方式 0：平台1：个人
		 $outexcel = new phptoexcel();
		 $titledata = array('订单编号','订单总价','总促销','商家承担促销','平台承担促销','优惠券金额','积分抵扣','配送类型','店铺商品总价','店铺配送费','打包费','订单详情','支付方式','下单时间','配送时间','促销规则');
		 $titlelabel = array('dno','allcost','cxcost','shopdowncost','platcx','yhjcost','scoredown','pstype','shopcost','shopps','bagcost','content','payname','dotime','posttime','cxname');
		 $outexcel->out($titledata,$titlelabel,$list,'','商家结算详情'.$shoplist['shopname']); 
		 
		 
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
	   	   $newlink = IUrl::creatUrl('areaareaadminpage/order/module/ordercomment/'.$searchvalue.'/querytype/'.$querytype);
	   	   $data['querytype'] = $querytype;
	   	 }
	   }
	  $pageinfo = new page();
    $pageinfo->setpage(IReq::get('page'));
    //comment:  id  orderid  orderdetid  shopid  goodsid  uid  content  addtime  replycontent  replytime  point 评分 is_show
    //orderdet: id  order_id  goodsid  goodsname  goodscost  goodscount  status  shopid
		$data['list'] = $this->mysql->getarr("select com.*,sh.shopname,b.username,ort.goodsname from ".Mysite::$app->config['tablepre']."comment  as com left join ".Mysite::$app->config['tablepre']."member as b on com.uid = b.uid left join ".Mysite::$app->config['tablepre']."shop as sh on sh.id = com.shopid left join ".Mysite::$app->config['tablepre']."orderdet as ort on ort.id = com.orderdetid ".$where." order by com.id desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
		$shuliang  = $this->mysql->counts("select com.id from ".Mysite::$app->config['tablepre']."comment  as com left join ".Mysite::$app->config['tablepre']."member as b on com.uid = b.uid left join ".Mysite::$app->config['tablepre']."shop as sh on sh.id = com.shopid left join ".Mysite::$app->config['tablepre']."orderdet as ort on ort.id = com.orderdetid ".$where);
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar($newlink);
		 Mysite::$app->setdata($data);
	}
  function autodel(){
      $dayminitime = time();
      $this->mysql->delete(Mysite::$app->config['tablepre'].'orderdet',"order_id in(select id from  ".Mysite::$app->config['tablepre']."order where status in(0,4,5) and paystatus != 1   and suretime < ".$dayminitime." order by id desc )");
      $this->mysql->delete(Mysite::$app->config['tablepre'].'order',"status in(0,4,5) and paystatus != 1 and  suretime < ".$dayminitime);
      $this->mysql->update(Mysite::$app->config['tablepre'].'order','`status`=2'," is_reback =0 and status = 1 and suretime < ".$dayminitime."");
      $this->mysql->update(Mysite::$app->config['tablepre'].'order','`status`=3,`suretime`='.time().''," is_reback =0 and  status = 2  and suretime < ".$dayminitime."");
 
      $link = IUrl::creatUrl('areaadminpage/order/module/orderlist');//sendtime 发货时间
	 	  $this->message('',$link);
	}

  function drawbacklog(){
	  
	   
	    	$querytype = IReq::get('querytype');
	    	$searchvalue = IReq::get('searchvalue');
	    	$orderstatus = intval(IReq::get('orderstatus'));
	    	$starttime = IReq::get('starttime');
	    	$endtime = IReq::get('endtime');
	    	$nowday = date('Y-m-d',time());
	    	$starttime = empty($starttime)? $nowday:$starttime;
	    	$endtime = empty($endtime)? $nowday:$endtime;
	      $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59');
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
				$newstatus = $orderstatus -1;
	          	$where .= empty($where)?' where status ='.$newstatus:' and status = '.$newstatus;
				
				
			}
	 
	          $data['orderstatus'] = $orderstatus;
	          $newlink .= '/orderstatus/'.$orderstatus;
	     
	    	$link = IUrl::creatUrl('/areaadminpage/order/module/drawbacklog'.$newlink);
	    	$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),5);
	    	 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
	    	 //
	    	 
	    	$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."drawbacklog   ".$where."  order by  id desc  limit ".$pageshow->startnum().", ".$pageshow->getsize()." ");
			 $data['list'] = array();
			 foreach($list as $key=>$value){
				 $value['orderinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id = ".$value['orderid']." ");
				 $data['list'][] = $value;
			 }
		 
			 $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."drawbacklog    ".$where."  order by  id desc");
			$pageshow->setnum($shuliang);
	    	$data['pagecontent'] = $pageshow->getpagebar($link);
			 
			Mysite::$app->setdata($data);
	 
	  
	  /* 
	  
		 $pageinfo = new page();
		 $pageinfo->setpage(IReq::get('page'));
		 $list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."drawbacklog  order by  id desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
		 $data['list'] = array();
		 foreach($list as $key=>$value){
			 $value['orderinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id = ".$value['orderid']." ");
			 $data['list'][] = $value;
		 }
	 
		 $shuliang  = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."drawbacklog  order by  id desc");
		 $pageinfo->setnum($shuliang);
		 $data['pagecontent'] = $pageinfo->getpagebar();
		 Mysite::$app->setdata($data); */
  }
  
  function showdrawbacklog(){
     $id = IFilter::act(IReq::get('id'));
     $link = IUrl::creatUrl('areaadminpage/order/module/drawbacklog');
     if(empty($id)) $this->message('id获取失败',$link);
     $drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where id=".$id." order by  id desc  limit 0,2");
     if(empty($drawbacklog)) $this->message('退款申请获取失败',$link);
     $data['oderinfo'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$drawbacklog['orderid']." order by  id desc  limit 0,2");
     $data['orderdet'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id=".$drawbacklog['orderid']." order by  id desc  limit 0,2");
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
	 function areadtoji(){
  	 
     $searchvalue = IReq::get('searchvalue');
		$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime'));
		$admin_id = intval(IReq::get('admin_id'));
		$newlink = '';
		$where= ' where `groupid`=4';
		$where2 = '';
		$data['searchvalue'] = '';
		if(!empty($searchvalue))
		{
			   $data['searchvalue'] = $searchvalue;
	   	   $where .= ' and username = \''.$searchvalue.'\' ';
	   	   $newlink .= '/searchvalue/'.$searchvalue;
		}
		$data['starttime'] = '';
		if(!empty($starttime))
		{
			 $data['starttime'] = $starttime;
			 $where2 .= ' and  suretime > '.strtotime($starttime.' 00:00:01').' ';
	   	 $newlink .= '/starttime/'.$starttime;
		}
		$data['endtime'] = '';
		if(!empty($endtime))
		{
			 $data['endtime'] = $endtime;
			 $where2 .= ' and  suretime < '.strtotime($endtime.' 23:59:59').' ';
	   	 $newlink .= '/endtime/'.$endtime;
		}
		  
		$link = IUrl::creatUrl('areaadminpage/order/module/outareatjorder'.$newlink);
	  $data['outlink'] =IUrl::creatUrl('areaadminpage/order/module/outareatjorder/outtype/query'.$newlink);
	  $data['outlinkch'] =IUrl::creatUrl('areaadminpage/order/module/outareatjorder'.$newlink);
		$pageinfo = new page();
		$pageinfo->setpage(IReq::get('page')); 
		$memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."admin ".$where."   order by uid asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		 
		$list = array();
		if(is_array($memberlist))
		{
	     foreach($memberlist as $key=>$value)
	     {
	     	  //判断店铺配送类型
	     	   

	     	  // $value['sendtype'] = empty($sendtype)?'网站配送':'自送';
	         $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$value['uid']."' and paytype =0 and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	         $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$value['uid']."' and paytype =1  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

	         $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
	          
	         $value['online'] = $line['doallcost'];//$line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额
	          
	         $value['unline'] = $shoptj['doallcost'];//$shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown; 
		       $list[] = $value;
		   }
		}

		$data['list'] =$list; 
		//print_r($data);
		$shuliang  = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."admin ".$where."  ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar($link);
		Mysite::$app->setdata($data);
      
   	
   }
   function outareatjorder()
	{
		$outtype = IReq::get('outtype');
		if(!in_array($outtype,array('query','ids')))
		{
		  	header("Content-Type: text/html; charset=UTF-8");
			 echo '查询条件错误';
			 exit;
		}
		$where = '';
		$where2 = '';
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
			  $where .= ' where uid in('.$id.') ';

			  $searchvalue = trim(IReq::get('searchvalue'));
			  $where .= !empty($searchvalue)? ' and username = \''.$searchvalue.'\'':'';
		 //   $data['searchvalue'] = $searchvalue;
	   //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

		   $starttime = trim(IReq::get('starttime'));
		   $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';

		   $endtime = trim(IReq::get('endtime'));
		   $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';

		}else{
		   $searchvalue = trim(IReq::get('searchvalue'));
		   $where .= !empty($searchvalue)? ' where username = \''.$searchvalue.'\'':'';
		 //   $data['searchvalue'] = $searchvalue;
	   //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

		   $starttime = trim(IReq::get('starttime'));
		   $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';

		   $endtime = trim(IReq::get('endtime'));
		   $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';
		   $admin_id = intval(IReq::get('admin_id'));
		   if(!empty($admin_id)){
		       $where .= !empty($where)? ' and admin_id =\''.$admin_id.'\'':' where admin_id =\''.$admin_id.'\'';
		   }
		}
		$where.= empty($where)?' where `groupid`=4 ':' and `groupid`=4 ';
	  $memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."admin ".$where."   order by uid asc  limit 0,2000 ");
		$list = array();
		if(is_array($memberlist))
		{
	     foreach($memberlist as $key=>$value)
	     {
 
	     	   $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$value['uid']."' and paytype =0  and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	         $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$value['uid']."' and paytype =1  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

	         $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
	          
	         $value['online'] = $line['doallcost'];//$line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额
	          
	         $value['unline'] = $shoptj['doallcost'];//$shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown; 
		       $list[] = $value;
		   }
		}
		 $outexcel = new phptoexcel();
		 $titledata = array('区域管理员','订单总数','线上交易金额','线下交易金额');
		 $titlelabel = array('username','orderNum','online','unline');
		// $datalist = $this->mysql->getarr("select card,card_password,cost from ".Mysite::$app->config['tablepre']."card where id > 0 ".$where."   order by id desc  limit 0,2000 ");
		 $outexcel->out($titledata,$titlelabel,$list,'','区域管理员结算');
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
	    	$link = IUrl::creatUrl('/areaadminpage/order/module/shophuiorder'.$newlink);
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
	function tjshophui()
	{
		$data['buyerstatus'] = array(
		'0'=>'未完成',
		'1'=>'已完成',
		
		);
		$querytype = IReq::get('querytype');
	
		$searchvalue = IReq::get('searchvalue');
	
		$orderstatus = intval(IReq::get('orderstatus'));
		$starttime = IReq::get('starttime');
		$endtime = IReq::get('endtime');
		$nowday = date('Y-m-d',time());
		$starttime = empty($starttime)? $nowday:$starttime;
		$endtime = empty($endtime)? $nowday:$endtime;
	  $where = '  where ord.addtime > '.strtotime($starttime.' 00:00:00').' and ord.completetime < '.strtotime($endtime.' 23:59:59');
		$data['starttime'] = $starttime;
		$data['endtime'] = $endtime;
		$data['querytype'] = '';
		$data['searchvalue'] = '';
		if(!empty($querytype))
		{
			if(!empty($searchvalue)){
				 $data['searchvalue'] = $searchvalue;
	   	   $where .= ' and '.$querytype.' =\''.$searchvalue.'\' ';
	   	   $data['querytype'] = $querytype;
			}
		}
			if($querytype == 'ord.status' && $searchvalue == 0 ){
			$tmpwhere = 'and ord.status = 0';
			  $data['querytype'] = $querytype;
			  $data['searchvalue'] = $searchvalue;
		}
		
		

		$data['list'] = $this->mysql->getarr("select count(ord.id) as shuliang,ord.status,ord.shopname,ord.username,ord.huiname,sum(xfcost) as xfcost,sum(yhcost) as yhcost,sum(sjcost) as sjcost from ".Mysite::$app->config['tablepre']."shophuiorder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.uid   ".$where." ".$tmpwhere." group by ord.status order by ord.id desc limit 0, 10");

		Mysite::$app->setdata($data);
	}
	
	function shophuiautodel(){
      $dayminitime = time();
      $this->mysql->delete(Mysite::$app->config['tablepre'].'shophuiorder',"status in(0) and paystatus != 1  ");
   
      $link = IUrl::creatUrl('areaadminpage/order/module/shophuiorder');
	 	  $this->message('',$link);
	}
	
function saveset(){   //保存后台跑腿信息 到配置文件中
	 	
 	 	  $siteinfo['is_ptorderbefore'] = intval(IReq::get('is_ptorderbefore'));
	 	  $siteinfo['pt_orderday'] = intval(IReq::get('pt_orderday'));
		  
		  
	 	  $siteinfo['km'] = IReq::get('km');
	 	  $siteinfo['kmcost'] = IReq::get('kmcost');
	 	  $siteinfo['addkm'] = IReq::get('addkm');
	 	  $siteinfo['addkmcost'] = IReq::get('addkmcost');
		  
		  
		  $siteinfo['kg'] = IReq::get('kg');
	 	  $siteinfo['kgcost'] = IReq::get('kgcost');
	 	  $siteinfo['addkg'] = IReq::get('addkg');
	 	  $siteinfo['addkgcost'] = IReq::get('addkgcost');
 		  $cityid = $this->admin['cityid'];
	 	  $siteinfo['cityid'] = $cityid;
		  if(empty( $siteinfo['km'] ) ) $this->message("重量初始公斤值不能为空");
		  
		  
		  
		  if(empty( $siteinfo['kg'] ) ) $this->message("距离初始公里值不能为空");
 
		

	      $paotuiinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset  where cityid = '".$cityid."'    ");
		  if(empty($paotuiinfo)){
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'paotuiset',$siteinfo);
		  }else{
			  $this->mysql->update(Mysite::$app->config['tablepre'].'paotuiset',$siteinfo,"  id > 0 and cityid = '".$cityid."' ");  
		  } 

		   $this->success('success');
	 } 
 function setpaotui(){
		$cityid = $this->admin['cityid'];
	 
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
		
		 $cityid = $this->admin['cityid'];
		$paotuiinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset where cityid = '".$cityid."' ");
		if(empty($paotuiinfo)){
			$this->message('请先完善重量，距离等配置信息');
		}
		
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
		 $cityid = $this->admin['cityid'];
		$nowdelid =  intval(IFilter::act(IReq::get('id')));
	#$this->message($nowdelid);
	$paotuiinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset where cityid = '".$cityid."'  ");
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
		 $this->mysql->update(Mysite::$app->config['tablepre'].'paotuiset',$savedata,"  id > 0 and cityid = '".$cityid."'  ");
	 
		
		$this->success('success'); 
	}
	/***** 
	
	
		2016.3.5新增
	
	
	***************/
	//根据日期筛选出 可结算的店铺  100条记录一个页面.
	 function shopjsadd(){  
	      $daytime = IFilter::act(IReq::get('daytime'));
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
		  $data['showtime'] = date('Y-m-d',$nowmintime);//显示日期
		  Mysite::$app->setdata($data);
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
		  $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(allcost) as allcost,sum(shopps) as shopps   from ".Mysite::$app->config['tablepre']."order  ".$where2." and paytype =0 and shopcost > 0 and status = 3  order by id asc  limit 0,1000");
	      $line= $this->mysql->select_one("select count(id) as shuliang,sum(allcost) as allcost,sum(shopps) as shopps   from ".Mysite::$app->config['tablepre']."order  ".$where2." and paytype !=0  and paystatus =1 and shopcost > 0 and status = 3     order by id asc  limit 0,1000");

		 
		 
		 $newdata['onlinecount'] = $line['shuliang'];
		 $newdata['onlinecost'] = $line['allcost'];
		 $newdata['unlinecount'] = $shoptj['shuliang'];
		 $newdata['unlinecost'] = $shoptj['allcost'];
		 $yjbl =   empty($shopdet['yjin'])?Mysite::$app->config['yjin']:$shopdet['yjin'];
		 $newdata['yjb'] = empty($yjbl)?0:$yjbl; 
		 $yjcost =  ($shoptj['allcost']+$line['allcost'])*$yjbl*0.01;
		  $newdata['acountcost'] =  0;
		 if($sendtype == 0){//平台配送 
			  $newdata['acountcost'] = $line['allcost']+$shoptj['allcost']-$yjcost-$shoptj['shopps']-$line['shopps'];//  线上金额-佣金比例+线下金额-线上配送费=平台配送金额 
		 }else{//自行配送
			 $newdata['acountcost'] = $line['allcost']-$yjcost-$line['shopps'];
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
		 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`+'.$newdata['acountcost'],"uid ='".$shopinfo['uid']."' ");
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
		 $this->success('success'); 
	 }
	 //根据日期获取已结算信息
	 function shopjsover(){
		     $jstime = IFilter::act(IReq::get('daytime')); //结算日
			 $nowtime = time(); 
			 $nowmintime =  strtotime($jstime);
			 $checktime = $nowtime - $nowmintime;
			 if($checktime < 259200 || $checktime > 457141240){
				  $nowmintime = strtotime(date('Y-m-d',$nowtime- 259200));
			 }
			  $where = " where jstime = ".$nowmintime;
		      $pageshow = new page();
			  $pageshow->setpage(IReq::get('page'),10);  
			  $txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."shopjs  ".$where." order by addtime desc   limit ".$pageshow->startnum().", ".$pageshow->getsize().""); 
			  $shuliang  = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shopjs ".$where."  order by id asc  ");
			  $pageshow->setnum($shuliang);
			  $data['pagecontent'] = $pageshow->getpagebar();
			  $tempdata = array(); 
			  if(is_array($txlist)){
				  foreach($txlist as $key=>$value){
					  $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where id=".$value['shopid']." ");
					  $value['shopname'] = isset($info['shopname'])?$info['shopname']:'未定义';
					  $value['name'] =  date('Y-m-d',$value['jstime']).'日结算金额';
					  $value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
					  $tempdata[] = $value;
				  }
			  }
			  $data['jslist'] = $tempdata;
			  $data['daytime'] = date('Y-m-d',$nowmintime);
			  Mysite::$app->setdata($data);
	 }
	 //店铺提现记录
	 function shoptx(){
		  $pageshow = new page();
	      $pageshow->setpage(IReq::get('page'),10); 
		  $shopname = trim(IFilter::act(IReq::get('shopname'))); //店铺名称
		  $starttime = IFilter::act(IReq::get('starttime')); //开始时间 
		  $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
		  $where = " where type = 0 ";//仅获取提现记录
		  if(!empty($shopname)){
			  $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where shopname='".$shopname."'  ");
		      if(!empty($info)) $where.=" and shopuid = ".$info['uid']." ";
		  }
		  if(!empty($starttime)) $where.=" and addtime > ".strtotime($starttime)." "; 
		  if(!empty($endtime)) $where.=" and addtime < ".strtotime($endtime)." "; 
		
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
	 //店铺资金记录
	 function shoptxtlog(){
		 $pageshow = new page();
	      $pageshow->setpage(IReq::get('page'),10); 
		  $shopname = trim(IFilter::act(IReq::get('shopname'))); //店铺名称
		  $starttime = IFilter::act(IReq::get('starttime')); //开始时间 
		  $endtime =  IFilter::act(IReq::get('endtime')); //结束时间
		  $where = " where id > 0 ";//仅获取提现记录
		  if(!empty($shopname)){ 
			  $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."shop where shopname='".$shopname."'  ");
		      if(!empty($info)) $where.=" and shopuid = ".$info['uid']." ";
		  }
		  if(!empty($starttime)) $where.=" and addtime > ".strtotime($starttime)." "; 
		  if(!empty($endtime)) $where.=" and addtime < ".strtotime($endtime)." "; 
		
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
		 $uid = intval(IFilter::act(IReq::get('uid'))); //店铺名称
		 $cost = intval(IFilter::act(IReq::get('cost'))); //店铺名称
	    $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'  ");
		if(empty($userinfo)){
			$this->message('用户不存在');
		}
		$checkcost = intval($cost);
		if($checkcost < 1){
			$this->message('充值金额不能小于1');
		}
		 
		$newdata['cost'] = $checkcost;
		$newdata['type'] = 1;
		$newdata['status'] = 2;
		$newdata['addtime'] = time();
		$newdata['shopid'] = 0;
        $newdata['shopuid'] =  $uid;
		$newdata['changetype'] = 2;
		$newdata['name'] = '账号充值'; 
		$newdata['yue'] = $userinfo['shopcost']+$checkcost;
        $this->mysql->update(Mysite::$app->config['tablepre'].'member','`shopcost`=`shopcost`+'.$checkcost,"uid ='".$uid."' ");
		$this->mysql->insert(Mysite::$app->config['tablepre'].'shoptx',$newdata);
	    $this->success('success'); 
	 }
	 
	 
	 
	 /***** 
	
	
		2016.3.5结束
	
	
	***************/
	
	function historyorderlist(){
			$talbepre = Mysite::$app->config['tablepre'];
		$checkinfo = $this->mysql->query("show tables like '".Mysite::$app->config['tablepre']."historylog';");
		$checkinfo = $this->mysql->assoc($checkinfo);
		if(empty($checkinfo)){//表示这个表不存在
		
			$data['noinstall'] = 1;
		}else{
			$data['noinstall'] = 0;
			$this->setstatus();
	    	$querytype = IReq::get('querytype');
	    	$searchvalue = IReq::get('searchvalue');
	    	$orderstatus = intval(IReq::get('orderstatus'));
	    	$starttime = IReq::get('starttime');
	    	$endtime = IReq::get('endtime');
			
			
	    	// $nowday = date('Y-m-d',time());
			
			
			
			$BeginDate = date('Y-m-01',time());//找到第一个订单所在月份和天数
			
			$nowday = date('Y-m-d', (strtotime("$BeginDate -2 month")-1));
			
			
	    	$starttime = empty($starttime)? $nowday:$starttime;
	    	$endtime = empty($endtime)? $nowday:$endtime;
			$admin_id = $this->admin['cityid'];
	        $where = '  where ord.admin_id = '.$admin_id.' and  ord.addtime > '.strtotime($starttime.' 00:00:00').' and ord.addtime < '.strtotime($endtime.' 23:59:59');
		 
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
	          	$newstatus = $orderstatus -1;
	          	$where .= empty($where)?' where ord.status ='.$newstatus:' and ord.status = '.$newstatus;
	          }
	          $data['orderstatus'] = $orderstatus;
	          $newlink .= '/orderstatus/'.$orderstatus;
	    	}
	    	$link = IUrl::creatUrl('/areaadminpage/order/module/historyorderlist'.$newlink);
	    	$pageshow = new page();
	    	$pageshow->setpage(IReq::get('page'),5);
	    	 //order: id  dno 订单编号 shopuid 店铺UID shopid 店铺ID shopname 店铺名称 shopphone 店铺电话 shopaddress 店铺地址 buyeruid 购买用户ID，0未注册用户 buyername
	    	 //
	    	 
	    	$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."temporder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." order by ord.id desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
	    	$shuliang  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."temporder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." ");
	    	$pageshow->setnum($shuliang);
	    	$data['pagecontent'] = $pageshow->getpagebar($link);
	   	$data['list'] = array();
			  if($orderlist)
			  {
				foreach($orderlist as $key=>$value)
				{
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
		}
	     Mysite::$app->setdata($data);
	}

}
?>