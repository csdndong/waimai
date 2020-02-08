<?php
/*
*   method 方法  包含所有会员相关操作
    管理员/会员  添加/删除/编辑/用户登录
    用户日志其他相关连的通过  memberclass关联
*/
class method   extends baseclass
{ 
	 function checkapp(){
  	 $uid = trim(IFilter::act(IReq::get('uid'))); 
  	 $pwd = trim(IFilter::act(IReq::get('pwd')));
  	 $member= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' "); 
  	 $backarr = array('uid'=>0);
  	 if(!empty($member)){
  		 if($member['password'] == md5($pwd)){
  			 $backarr = $member;
  		 }
  	  
  	 }
  	 return $backarr;
   }
   function applogin(){
   	 $uname = trim(IFilter::act(IReq::get('uname')));  
  	 $pwd = trim(IFilter::act(IReq::get('pwd')));  
  	 if(empty($uname)) $this->message('用户名为空');
  	 if(empty($pwd)) $this->message('密码为空');
  	 
     if(!$this->memberCls->login($uname,$pwd)){
	    	    $this->message($this->memberCls->ero()); 
	   } 
	   $uid = $this->memberCls->getuid();
	   
	   $member= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");  
	   $checkinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."locationpsy  where uid = '".$uid."' order by uid asc limit 0,1");
  	  if(empty($checkinfo)){
  	  	 	$data['uid'] = $uid;
  	  	 	$data['lat'] = 0;
  	  	 	$data['lng'] = 0;
  	  	 	$data['addtime'] = time();
  	  	 	$this->mysql->insert(Mysite::$app->config['tablepre'].'locationpsy',$data); 
  	  }
  	  /*写配送员数据*/
  	  
	   unset($member['password']);
	   $this->success($member); 
   }
	function waitorder(){
		//获取所有属于我的能配送的订单
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		} 
		 
		$where = " where status = 0 and shopid in (select  id from ".Mysite::$app->config['tablepre']."shop where admin_id = ".$backinfo['admin_id']." ) and dotype  < 2 ";
		/**** 待抢配送单***/
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待评价','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$psstatus = array('0'=>'待抢订单','1'=>'待取订单','2'=>'已取订单','3'=>'配送完成','4'=>'关闭','5'=>'关闭');
		$psorderlist =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  ".$where." order by pstime desc  "); //and ".$newwherearray[$gettype]."
		$templist = array();
	   foreach($psorderlist as $key=>$value){
			
			$value['addtime'] = date('H:i',$value['addtime']);
			
		    $orderinfo = $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."order where id='".$value['orderid']."'  order by id desc  ");
			//  支付类型   支付时间   支付状态    店铺名称  店铺地址   店铺电话  
			//买家地址   买家电话   
			//is_reback   提成费用
			//  
			$value['paystatus'] = $orderinfo['paystatus'];
			$value['paytype'] = $orderinfo['paytype']==1?'在线支付':'货到支付';
			$value['paystatusname'] = $orderinfo['paystatus'] == 1?'已支付':'未支付';
			$value['shopname'] = $orderinfo['shopname'];
			$value['shopphone'] = $orderinfo['shopphone'];
			$value['shopaddress'] = $orderinfo['shopaddress'];
			$value['buyername'] = $orderinfo['buyername'];
			$value['buyeraddress'] = $orderinfo['buyeraddress'];
			$value['buyerphone'] = $orderinfo['buyerphone'];
			$value['orderstatus'] = $statusarr[$orderinfo['status']];
			if($orderinfo['is_reback'] == 1){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 2){
				$value['orderstatus'] = '退款成功';
			}elseif($orderinfo['is_reback'] == 3){
				$value['orderstatus'] = '退款失败';
			}elseif($orderinfo['is_reback'] == 4){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 5){
				$value['orderstatus'] = '用户取消退款申请';
			}
			$value['shoptype'] = $orderinfo['shoptype'];
			if($orderinfo['shoptype'] == 100){//跑腿订单
				$value['ordertype'] = '跑腿订单';
				$value['pttype'] = $orderinfo['pttype'] == 1?'帮我送':'帮我取';
				$value['ptkg'] = $orderinfo['ptkg'];//
				$value['postyear'] = date('Y-m-d',$orderinfo['sendtime']);
				$value['postdate'] = $orderinfo['postdate'];
			}else{
				$value['ordertype'] = '普通订单';
				$value['pttype'] = '';
				$value['ptkg'] = '';
				$value['postyear'] = date('Y-m-d',$value['pstime']);
				$value['postdate'] =  $orderinfo['postdate'];
			}
	
		    $templist[] = $value;
		 
			
		 
		}
		$this->success($templist); 
		// echo '<br>';
		// print_r($psorderlist);
		// exit;
		
		
		/*
		
     
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待评价','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		} 
		$todatay = strtotime(date('Y-m-d',time()));
		$endtime = $todatay + 86399;//最近一周订单//where buyeruid = ".$backinfo['uid']."   and addtime > ".$todatay."
      
		$where = ' where posttime > '.$todatay.' and status > 0 and status < 3 and (psuid = 0 or psuid is null) and is_goshop = 0 and pstype = 0 and is_make < 2   ';//find_in_set('aa@email.com', emails);
     
        and posttime < '.$endtime.' 
    
		$orderlist =  $this->mysql->getarr("select id,addtime,posttime,dno,allcost,status,is_make,daycode,shopname,is_ping,shopaddress,buyeraddress from ".Mysite::$app->config['tablepre']."order ".$where." order by posttime desc  "); //and ".$newwherearray[$gettype]."
     
		$backdatalist = array();
		foreach($orderlist as $key=>$value){
			$value['showstatus'] = $statusarr[$value['status']];//waitorder 
			$value['creattime'] =  date('m-d H:i',$value['addtime']);
			$value['dotime'] =  date('m-d H:i',$value['posttime']);
			$value['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
			if($value['status'] ==  1){
				if($value['is_make'] == 0){
					$value['showstatus'] = '商家未制作';
				}elseif($value['is_make'] ==2){
					$value['showstatus'] = '无效订单';
					$value['status'] = 4;
        	     
				}elseif($value['is_make'] == 1){
					$value['showstatus'] = '商家已制作';
				}
			}elseif($value['status'] == 3){
				if(empty($value['is_ping'])){
					$value['showstatus'] ='待评价';
				}
         
			}
     
			$backdatalist[] = $value;
		} 
		$this->success($backdatalist); */
	}
	function getmyorder(){
		
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		} 
		 
		$where = " where status = 1 and psuid = ".$backinfo['uid']."     ";
		/**** 待抢配送单***/
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待评价','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$psstatus = array('0'=>'待抢订单','1'=>'待取订单','2'=>'已取订单','3'=>'配送完成','4'=>'关闭','5'=>'关闭');
		$psorderlist =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  ".$where." and dotype  < 2 order by pstime desc  "); //and ".$newwherearray[$gettype]."
		$templist = array();
	   foreach($psorderlist as $key=>$value){
			
			$value['addtime'] = date('H:i',$value['addtime']);
			
		    $orderinfo = $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."order where id='".$value['orderid']."'  order by id desc  ");
			//  支付类型   支付时间   支付状态    店铺名称  店铺地址   店铺电话  
			//买家地址   买家电话   
			//is_reback   提成费用
			//  
			$value['paystatus'] = $orderinfo['paystatus'];
			$value['paytype'] = $orderinfo['paytype']==1?'在线支付':'货到支付';
			$value['paystatusname'] = $orderinfo['paystatus'] == 1?'已支付':'未支付';
			$value['shopname'] = $orderinfo['shopname'];
			$value['shopphone'] = $orderinfo['shopphone'];
			$value['shopaddress'] = $orderinfo['shopaddress'];
			$value['buyername'] = $orderinfo['buyername'];
			$value['buyeraddress'] = $orderinfo['buyeraddress'];
			$value['buyerphone'] = $orderinfo['buyerphone'];
			$value['orderstatus'] = $statusarr[$orderinfo['status']];
			if($orderinfo['is_reback'] == 1){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 2){
				$value['orderstatus'] = '退款成功';
			}elseif($orderinfo['is_reback'] == 3){
				$value['orderstatus'] = '退款失败';
			}elseif($orderinfo['is_reback'] == 4){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 5){
				$value['orderstatus'] = '用户取消退款申请';
			}
			$value['shoptype'] = $orderinfo['shoptype'];
			if($orderinfo['shoptype'] == 100){//跑腿订单
				$value['ordertype'] = '跑腿订单';
				$value['pttype'] = $orderinfo['pttype'] == 1?'帮我送':'帮我取';
				$value['ptkg'] = $orderinfo['ptkg'];//
				$value['postyear'] = date('Y-m-d',$orderinfo['sendtime']);
				$value['postdate'] = $orderinfo['postdate'];
			}else{
				$value['ordertype'] = '普通订单';
				$value['pttype'] = '';
				$value['ptkg'] = '';
				$value['postyear'] = date('Y-m-d',$value['pstime']);
				$value['postdate'] =  $orderinfo['postdate'];
			}
	
		    $templist[] = $value;
		 
			
		 
		}
		$this->success($templist); 
		/*
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待完成','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		} 
		$todatay = strtotime(date('Y-m-d',time()));
		$endtime = $todatay + 86399;//最近一周订单//where buyeruid = ".$backinfo['uid']."   and addtime > ".$todatay."
		$where = ' where posttime > '.$todatay.' and posttime < '.$endtime.' and status > 0 and status < 4  and is_goshop = 0 and pstype = 0  and psuid = \''.$backinfo['uid'].'\' and is_make < 2 ';//find_in_set('aa@email.com', emails);
      
		$orderlist =  $this->mysql->getarr("select id,addtime,posttime,dno,allcost,status,is_make,daycode,shopname,is_ping,shopaddress,buyeraddress from ".Mysite::$app->config['tablepre']."order ".$where." order by posttime desc  "); //and ".$newwherearray[$gettype]."
		$backdatalist = array();
		foreach($orderlist as $key=>$value){ 
			$value['showstatus'] = $statusarr[$value['status']];
			$value['creattime'] =  date('m-d H:i',$value['addtime']);
			$value['dotime'] =  date('m-d H:i',$value['posttime']);
			$value['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
			if($value['status'] ==  1){
				if($value['is_make'] == 0){
					$value['showstatus'] = '商家未制作';
				}elseif($value['is_make'] ==2){
					$value['showstatus'] = '无效订单';
					$value['status'] = 4;
        	     
				}elseif($value['is_make'] == 1){
					$value['showstatus'] = '商家已制作';
				}
			} 
			
     
			$backdatalist[] = $value;
		} 
     $this->success($backdatalist);  */
    }
	function getwaitsendorder(){
		
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		} 
		 
		$where = " where status = 2 and psuid = ".$backinfo['uid']."     ";
		/**** 待抢配送单***/
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待评价','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$psstatus = array('0'=>'待抢订单','1'=>'待取订单','2'=>'已取订单','3'=>'配送完成','4'=>'关闭','5'=>'关闭');
		$psorderlist =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  ".$where." and dotype  < 2 order by pstime desc  "); //and ".$newwherearray[$gettype]."
		$templist = array();
	   foreach($psorderlist as $key=>$value){
			
			$value['addtime'] = date('H:i',$value['addtime']);
			
		    $orderinfo = $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."order where id='".$value['orderid']."'  order by id desc  ");
			//  支付类型   支付时间   支付状态    店铺名称  店铺地址   店铺电话  
			//买家地址   买家电话   
			//is_reback   提成费用
			//  
			$value['paystatus'] = $orderinfo['paystatus'];
			$value['paytype'] = $orderinfo['paytype']==1?'在线支付':'货到支付';
			$value['paystatusname'] = $orderinfo['paystatus'] == 1?'已支付':'未支付';
			$value['shopname'] = $orderinfo['shopname'];
			$value['shopphone'] = $orderinfo['shopphone'];
			$value['shopaddress'] = $orderinfo['shopaddress'];
			$value['buyername'] = $orderinfo['buyername'];
			$value['buyeraddress'] = $orderinfo['buyeraddress'];
			$value['buyerphone'] = $orderinfo['buyerphone'];
			$value['orderstatus'] = $statusarr[$orderinfo['status']];
			if($orderinfo['is_reback'] == 1){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 2){
				$value['orderstatus'] = '退款成功';
			}elseif($orderinfo['is_reback'] == 3){
				$value['orderstatus'] = '退款失败';
			}elseif($orderinfo['is_reback'] == 4){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 5){
				$value['orderstatus'] = '用户取消退款申请';
			}
			$value['shoptype'] = $orderinfo['shoptype'];
			if($orderinfo['shoptype'] == 100){//跑腿订单
				$value['ordertype'] = '跑腿订单';
				$value['pttype'] = $orderinfo['pttype'] == 1?'帮我送':'帮我取';
				$value['ptkg'] = $orderinfo['ptkg'];//
				$value['postyear'] = date('Y-m-d',$orderinfo['sendtime']);
				$value['postdate'] = $orderinfo['postdate'];
			}else{
				$value['ordertype'] = '普通订单';
				$value['pttype'] = '';
				$value['ptkg'] = '';
				$value['postyear'] = date('Y-m-d',$value['pstime']);
				$value['postdate'] =  $orderinfo['postdate'];
			}
	
		    $templist[] = $value;
		 
			
		 
		}
		$this->success($templist); 
	}
	function getoverorder(){
		
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		} 
		 $startdate = trim(IFilter::act(IReq::get('startdate')));   
		$enddate = trim(IFilter::act(IReq::get('enddate')));  
        $stardate = empty($startdate)?strtotime(date('Y-m-d',time())):strtotime($startdate);
	    $enddate = empty($enddate)?strtotime(date('Y-m-d',time()))+86400:strtotime($enddate);
		
		$where = " where status = 3 and psuid = ".$backinfo['uid']."   and picktime > ".$stardate." and picktime < ".$enddate."  and dotype  < 2 ";
		/**** 待抢配送单***/
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待评价','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$psstatus = array('0'=>'待抢订单','1'=>'待取订单','2'=>'已取订单','3'=>'配送完成','4'=>'关闭','5'=>'关闭');
		$psorderlist =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  ".$where." order by pstime desc  "); //and ".$newwherearray[$gettype]."
		$templist = array();
	   foreach($psorderlist as $key=>$value){
			
			$value['addtime'] = date('H:i',$value['addtime']);
			
		    $orderinfo = $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."order where id='".$value['orderid']."'  order by id desc  ");
			//  支付类型   支付时间   支付状态    店铺名称  店铺地址   店铺电话  
			//买家地址   买家电话   
			//is_reback   提成费用
			//  
			$value['paystatus'] = $orderinfo['paystatus'];
			$value['paytype'] = $orderinfo['paytype']==1?'在线支付':'货到支付';
			$value['paystatusname'] = $orderinfo['paystatus'] == 1?'已支付':'未支付';
			$value['shopname'] = $orderinfo['shopname'];
			$value['shopphone'] = $orderinfo['shopphone'];
			$value['shopaddress'] = $orderinfo['shopaddress'];
			$value['buyername'] = $orderinfo['buyername'];
			$value['buyeraddress'] = $orderinfo['buyeraddress'];
			$value['buyerphone'] = $orderinfo['buyerphone'];
			$value['orderstatus'] = $statusarr[$orderinfo['status']];
			if($orderinfo['is_reback'] == 1){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 2){
				$value['orderstatus'] = '退款成功';
			}elseif($orderinfo['is_reback'] == 3){
				$value['orderstatus'] = '退款失败';
			}elseif($orderinfo['is_reback'] == 4){
				$value['orderstatus'] = '退款中';
			}elseif($orderinfo['is_reback'] == 5){
				$value['orderstatus'] = '用户取消退款申请';
			}
			$value['shoptype'] = $orderinfo['shoptype'];
			if($orderinfo['shoptype'] == 100){//跑腿订单
				$value['ordertype'] = '跑腿订单';
				$value['pttype'] = $orderinfo['pttype'] == 1?'帮我送':'帮我取';
				$value['ptkg'] = $orderinfo['ptkg'];//
				$value['postyear'] = date('Y-m-d',$orderinfo['sendtime']);
				$value['postdate'] = $orderinfo['postdate'];
			}else{
				$value['ordertype'] = '普通订单';
				$value['pttype'] = '';
				$value['ptkg'] = '';
				$value['postyear'] = date('Y-m-d',$value['pstime']);
				$value['postdate'] =  $orderinfo['postdate'];
			}
	
		    $templist[] = $value;
		 
			
		 
		}
		$this->success($templist); 
	}
   /*定义输入配送选择订单按钮*/
   function joinorder(){
   }
   /*定义发货按钮*/
   function sendorder(){
   }
	function location(){ 
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		}
		$lat = trim(IFilter::act(IReq::get('lat'))); 
		$lng = trim(IFilter::act(IReq::get('lng'))); 
		if(!empty($lat)){
			$data['lat'] = $lat;
			$data['lng'] = $lng;
			$data['addtime'] = time(); 
			$this->mysql->update(Mysite::$app->config['tablepre'].'locationpsy',$data,"uid='".$backinfo['uid']."'"); 
  	  	    $channelid = trim(IFilter::act(IReq::get('channelid')));
  	        
  	  	     
  	    }
		$userid = trim(IFilter::act(IReq::get('userid')));
		$xmuserid = trim(IFilter::act(IReq::get('xmuserid')));
		if(!empty($userid) || !empty($xmuserid) ){
			$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."apploginps where uid='".$backinfo['uid']."' "); 
			
			$Mdata['channelid'] = '';
			$Mdata['userid'] = $userid;
			$Mdata['xmuserid'] = $xmuserid;
			$Mdata['uid']=$backinfo['uid'];
			$Mdata['addtime'] = time();
			if(!empty($checkinfo)){ 
				$this->mysql->update(Mysite::$app->config['tablepre'].'apploginps',$Mdata,"uid='".$backinfo['uid']."'"); 
			}else{
				$this->mysql->insert(Mysite::$app->config['tablepre'].'apploginps',$Mdata);  //插入新数据
				
			}
		}  
		$this->success('暂无订单'); 
	}
	function psyapptj(){
			$backinfo = $this->checkappMem(); 
			if(empty($backinfo['uid'])){
				$this->message('nologin');
			}
			$showlisttype = intval(IFilter::act(IReq::get('showlisttype'))); 
			$starttime = 0;
			$endtime = 0; 
			$date=date('Y-m-d');  
			$w=date('w',strtotime($date));//本周
			$first=1;
			$now_start=date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days'));
			$now_end=date('Y-m-d',strtotime("$now_start +6 days")); 
			$starttime = strtotime($now_start);
			$endtime = strtotime($now_end)+86399; 
			
			$where2 .= ' and  picktime  > '.$starttime.' and picktime < '.$endtime; 
		 
			$alltj= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype<2 and   status = 3 ".$where2." order by id asc  limit 0,1000");
	 
			$newdata['weekshuliang'] = intval($alltj['shuliang']);
		//	$newdata['weektccost'] = round($alltj['tccost'],2);
			$alltj1= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype=2 and   status = 3 ".$where2." order by id asc  limit 0,1000");
			 
			$alltj2= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype=3 and   status = 3 ".$where2." order by id asc  limit 0,1000");
			 
			$newdata['weekshuliang'] = intval($alltj['shuliang']);
			 $ttcost =  $alltj['tccost']+$alltj1['tccost']-$alltj2['tccost'];
			$newdata['weektccost'] = round($ttcost,2);
		   //本月
		    $BeginDate=date('Y-m-01', strtotime(date("Y-m-d",time())));
		    $enddata = date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
		    $starttime = strtotime($BeginDate);
			$endtime = strtotime($enddata)+86399; 
			
			$where2 .= ' and  picktime  > '.$starttime.' and picktime < '.$endtime;  
			$alltj= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype<2  and   status = 3 ".$where2." order by id asc  limit 0,1000");
			$alltj1= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype =2  and   status = 3 ".$where2." order by id asc  limit 0,1000");
			$alltj2= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype =3  and   status = 3 ".$where2." order by id asc  limit 0,1000");
	 
			$newdata['monthshuliang'] = intval($alltj['shuliang']); 
		    $ttcost =  $alltj['tccost']+$alltj1['tccost']-$alltj2['tccost'];
			$newdata['monthccost'] = round($ttcost,2); 
			
		 
		    $BeginDate= date("Y-m-d",time()); 
		    $starttime = strtotime($BeginDate);
			$endtime = strtotime($BeginDate)+86399; 
			
			$where2 .= ' and  picktime  > '.$starttime.' and picktime < '.$endtime;  
			$alltj= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype<2 and   status = 3 ".$where2." order by id asc  limit 0,1000");
			$alltj1= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype =2 and   status = 3 ".$where2." order by id asc  limit 0,1000");
			$alltj2= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype=3 and   status = 3 ".$where2." order by id asc  limit 0,1000");
	   
			$newdata['dayshuliang'] = intval($alltj['shuliang']);
		    $ttcost =  $alltj['tccost']+$alltj1['tccost']-$alltj2['tccost'];
			$newdata['dayccost'] = round($ttcost,2);   
		 
		$this->success($newdata);
		
	}
	function daytj(){
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		}
		 
		$startdate = IFilter::act(IReq::get('startdate'));
		$enddate = IFilter::act(IReq::get('enddate'));
		$starttime = 0;
		$endtime = 0;
		if(!empty($startdate)){
			$starttime = strtotime($startdate);
		}
		if(!empty($enddate)){
			$endtime = strtotime($enddate)+86399;
		}
		$where2 = '   ';
		if($starttime > 0){
			$where2 .= ' and  picktime  > '.$starttime;
		}
		if($endtime > 0){
			$where2 .=' and picktime < '.$endtime;
		}  
		$alltj= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype<2 and   status = 3 ".$where2." order by id asc  limit 0,1000");
		$alltj1= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype=2 and   status = 3 ".$where2." order by id asc  limit 0,1000");
		$alltj2= $this->mysql->select_one("select  count(id) as shuliang,sum(psycost) as tccost  from ".Mysite::$app->config['tablepre']."orderps  where psuid = '".$backinfo['uid']."' and dotype=3 and   status = 3 ".$where2." order by id asc  limit 0,1000"); 
		$newdata['shuliang'] = intval($alltj['shuliang']);
		$ttcost =  $alltj['tccost']+$alltj1['tccost']-$alltj2['tccost'];
		$newdata['tccost'] = round($ttcost,2);   
		$this->success($newdata);
	}
	function costlog(){
		
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		} 
		 $startdate = trim(IFilter::act(IReq::get('startdate')));   
		$enddate = trim(IFilter::act(IReq::get('enddate')));  
        $stardate = empty($startdate)?strtotime(date('Y-m-d',time())):strtotime($startdate);
	    $enddate = empty($enddate)?strtotime(date('Y-m-d',time()))+86400:strtotime($enddate)+86400;
		
		$page = intval(IFilter::act(IReq::get('page')));
		$pagesize = intval(IFilter::act(IReq::get('pagesize'))); 
		$pagesize = $pagesize > 0? $pagesize:10; 
		$this->pageCls->setpage(intval(IReq::get('page')),$pagesize);  
		
		 
		
		$where = " where status = 3 and psuid = ".$backinfo['uid']."   and addtime > ".$stardate." and addtime < ".$enddate."    ";
		/**** 待抢配送单***/
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待评价','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$psstatus = array('0'=>'待抢订单','1'=>'待取订单','2'=>'已取订单','3'=>'配送完成','4'=>'关闭','5'=>'关闭');
		$psorderlist =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  ".$where." order by id desc  limit   ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."   "); //and ".$newwherearray[$gettype]."
		
	 
		$templist = array();
		if(is_array($psorderlist)){
		   foreach($psorderlist as $key=>$value){
				// id 	orderid 	shopid 	psuid 	psusername 	psemail 	status 	dno 	addtime 	pstime 	psycost 	picktime 	dotype
				$newdata = array();
				$newdata['addtime'] = date('Y-m-d H:i',$value['addtime']);
				
				if($value['dotype'] == 2){
					$newdata['name'] = '后台增加配配送员收入';
					$newdata['cost'] = $value['psycost'];
					$newdata['dno'] = $value['dno'];
					$newdata['dotype'] = "2";
				}elseif($value['dotype'] == 3){
					$newdata['name'] = '后台减少配送员收入';
					$newdata['cost'] = $value['psycost'];
					$newdata['dno'] = $value['dno'];
					$newdata['dotype'] = "3";
				}else{
					$newdata['name'] = '配送订单';
					$newdata['cost'] = $value['psycost'];
					$newdata['dno'] = '单号'.$value['dno'];
					$newdata['dotype'] = "1";
				} 
				$templist[] = $newdata; 
			}
		}
		$this->success($templist); 
	}
	function orderlist(){
		
		
		
	}
   //8.6修改
    function ordercontrol(){
   	 $backinfo = $this->checkappMem(); 
  	 if(empty($backinfo['uid'])){
  	   $this->message('nologin');
  	 }
  	 $dotype = trim(IFilter::act(IReq::get('dotype'))); //joinorder;  sendorder 
  	 $orderid = intval(IFilter::act(IReq::get('orderid')));
  	 if(empty($orderid)) $this->message('订单获取失败');
  	 if($dotype == 'joinorder'){//加入订单
			$ordercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' "); 
			if(empty($ordercheck)){
				$this->message('订单不存在');
			}
			$checka = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderid."'  "); 
			if(empty($checka)){
				$this->message('配送单不存在');
			}
			if($ordercheck['psuid'] > 0){
				$this->message('配送单已被抢');
			}  
			if($checka['psuid'] > 0){
				$this->message('配送单已被抢');
			}
			if ($ordercheck['is_reback'] >0 && $ordercheck['is_reback'] !=3  && $ordercheck['is_reback'] !=5) {
				$this->message('订单退款中');
			}
			$data['psuid'] = $backinfo['uid'];
  	 	    $data['psusername'] = $backinfo['username']; 
  	 	    $data['psemail'] = $backinfo['phone'];
  	 	    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$orderid."' and (psuid = 0 or psuid is null)");
  	 	   
  	 	    $statusdata['orderid']    = $orderid;
			$statusdata['statustitle'] =  "配送员已抢单";
			$statusdata['ststusdesc']  =  $data['psusername'].'抢单成功,联系电话'.$backinfo['phone']; 
			$statusdata['addtime']     =  time();
			$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);  
			$psdata['psuid'] = $backinfo['uid'];
			$psdata['psusername'] = $backinfo['username'];
			$psdata['psemail'] = $backinfo['phone'];
			$psdata['status'] =1;
			
			$psset = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$ordercheck['admin_id']."'   ");
			$checkpsyset = $psset['psycostset'];
			$bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
			$psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei;
			
			$this->mysql->update(Mysite::$app->config['tablepre'].'orderps',$psdata,"orderid='".$orderid."'"); 
			if($ordercheck['buyeruid'] > 0){
				$appCls = new appbuyclass();  
				$appcheck =  $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$ordercheck['buyeruid']."'   ");  
				$tempuser[] = $appcheck;
				$appCls->SetUserlist($tempuser)->sendNewmsg('配送员已抢单',$data['psusername'].'抢单成功,联系电话'.$backinfo['phone']);

			}
  	 	    $this->success('抢单成功');
  	 	     
  	 }elseif($dotype == 'pickorder'){
		    $ordercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' "); 
			if($ordercheck['is_reback'] == 1){
				$this->message('该订单在退款中不能配送');
			}  
			if($ordercheck['status'] == 3){
				$this->message('订单已完成');
			}
			if($ordercheck['is_make'] == 2){
				$this->message('商家不制作改订单不能取单');
			}
			if($ordercheck['status'] > 3){ 
				$this->message('订单已关闭');
			}
			if ($ordercheck['is_reback'] >0 && $ordercheck['is_reback'] !=3  && $ordercheck['is_reback'] !=5) {
				$this->message('订单退款中');
			}
		    $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderid."' "); 
  	 	    if($checkinfo['psuid'] !=$backinfo['uid']){
				 $this->message('该订单被他人领取取单失败');
			}
			if($checkinfo['status'] != 1){
				 $this->message('配送单不在待取状态');
			} 
		    $psdata['status'] =2;
			$psdata['picktime'] = time();//取单时间
			$this->mysql->update(Mysite::$app->config['tablepre'].'orderps',$psdata,"orderid='".$orderid."'"); 
		    $statusdata['orderid']    = $orderid;
			$statusdata['statustitle'] =  "配送员已取货";
			$statusdata['ststusdesc']  =  "配送员开始配送"; 
			$statusdata['addtime']     =  time();
			$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);  
			if($ordercheck['status'] != 2){
				$orderdata['status'] = 2;
				$orderdata['sendtime'] = time();
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$orderid."'");
			}
			if($ordercheck['buyeruid'] > 0){
				$appCls = new appbuyclass();  
				$appcheck =  $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$ordercheck['buyeruid']."'   ");  
				$tempuser[] = $appcheck;
				$appCls->SetUserlist($tempuser)->sendNewmsg('配送员已取货','配送员开始配送');
				
			 }
		    $this->success('取单成功'); 
	 }elseif($dotype == 'unpickorder'){//取消配送
	        $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderid."' "); 
  	 	    if($checkinfo['psuid'] !=$backinfo['uid']){
				 $this->message('该订单被他人领取取单失败');
			}
			if($checkinfo['status'] > 1){
				 $this->message('配送单已取单或者完成不能取消配送');
			} 
			$psdata['psuid'] = 0;
			$psdata['psusername'] ='';
			$psdata['psemail'] = '';
			$psdata['status'] =0;
			$psdata['addtime'] = time();
			$this->mysql->update(Mysite::$app->config['tablepre'].'orderps',$psdata,"orderid='".$orderid."'"); 
			
			$data['psuid'] =0;
  	 	    $data['psusername'] = ''; 
  	 	    $data['psemail'] = '';
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"orderid='".$orderid."'"); 
			
			 
		    $statusdata['orderid']    = $orderid;
			$statusdata['statustitle'] =  "配送取消配送";
			$statusdata['ststusdesc']  =  "等待配送员抢单配送"; 
			$statusdata['addtime']     =  time();
			$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);  
			
			 $ordercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' "); 
			if($ordercheck['buyeruid'] > 0){
				$appCls = new appbuyclass();  
				$appcheck =  $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$ordercheck['buyeruid']."'   ");  
				$tempuser[] = $appcheck;	
				$appCls->SetUserlist($tempuser)->sendNewmsg('配送取消配送','等待配送员抢单配送');
			}
			
			$this->success('取消配送成功'); 
	 
	 }elseif($dotype =='sendorder'){ 
			$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderid."' "); 
  	 	    if($checkinfo['psuid'] !=$backinfo['uid']){
				 $this->message('该订单不属于您处理');
			}
			if($checkinfo['status'] != 2){
				 $this->message('配送单不在待取状态');
			} 
  	 	   $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' "); 
  	 	   if($checkinfo['psuid'] != $backinfo['uid']){
  	 	     $this->message('该订单不属于您');
  	 	   }
  	 	   if($checkinfo['is_make'] == 2){
  	 	     $this->message('商家不制作该订单');
  	 	   }
  	 	    
		  if ($ordercheck['is_reback'] >0 && $ordercheck['is_reback'] !=3  && $ordercheck['is_reback'] !=5) {
				$this->message('订单退款中');
			}
		   if($checkinfo['status'] == 3){
				$this->message('订单已完成');
			} 
			if($checkinfo['status'] > 3){ 
				$this->message('订单已关闭');
			}
  	 	   // $data['status'] = 2;
		   // $data['sendtime'] = time();
		   // $this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$orderid."'");
		   $statusdata['orderid']    = $orderid;
		   $statusdata['statustitle'] =  "配送员已送达";
		   $statusdata['ststusdesc']  =  "配送完成"; 
		   $statusdata['addtime']     =  time();
		   $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata); 
		   $psdata['status'] =3;
		   $this->mysql->update(Mysite::$app->config['tablepre'].'orderps',$psdata,"orderid='".$orderid."'");  
		   
		   
		   
		   
		    $orderdata['is_acceptorder'] = 1;
	   	      $orderdata['status'] = 3;
	   	      $orderdata['suretime'] = time();
			  
	   	     /* 记录配送员送达时候坐标 */
				if(  $checkinfo['psuid'] > 0 ){
					if(  $checkinfo['pstype'] == 0 ){
						$psylocationonfo = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$checkinfo['psuid']."' ");
						if(!empty($psylocationonfo)){
							 $orderdata['psyoverlng'] = $psylocationonfo['lng'];
							 $orderdata['psyoverlat'] = $psylocationonfo['lat'];
						}
					}
					if(  $checkinfo['pstype'] == 2 ){
						$psbinterface = new psbinterface(); 
						$psylocationonfo = $psbinterface->getpsbclerkinfo($checkinfo['psuid']);
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
				
	   	       $this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$orderid."'");
			    //分销返佣
				$is_open_distribution = Mysite::$app->config['is_open_distribution'];
				if($is_open_distribution == 1){
					$distribution = new distribution();
					if($distribution->operateorder(orderid)){
						 
					}else{
						$err = $distribution->Error();
						logwrite('返佣失败，失败原因：'.$err);
					}
				}
				 //更新销量 
				$shuliang  = $this->mysql->select_one("select sum(goodscount) as sellcount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$checkinfo['id']."'  ");
				if(!empty($shuliang) && $shuliang['sellcount'] > 0){
					$this->mysql->update(Mysite::$app->config['tablepre'].'shop','`sellcount`=`sellcount`+'.$shuliang['sellcount'].'',"id ='".$checkinfo['shopid']."' ");
				}
				
				
				
				 //更新用户成长值
	   	       if(!empty($checkinfo['buyeruid']))
	   	       {
	   	      	   $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$checkinfo['buyeruid']."'   ");
		             if(!empty($memberinfo)){
		             	 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`total`=`total`+'.$checkinfo['allcost'],"uid ='".$checkinfo['buyeruid']."' ");
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
	 	    $this->mysql->update(Mysite::$app->config['tablepre'].'member','`parent_id`=0',"uid ='".$checkinfo['buyeruid']."' ");
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
		    if($checkinfo['buyeruid'] > 0){
				$appCls = new appbuyclass();  
				$appcheck =  $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$checkinfo['buyeruid']."'   ");  
				$tempuser[] = $appcheck;
				$appCls->SetUserlist($tempuser)->sendNewmsg('配送员已送达','配送完成');
		   }
  	 	   $this->success('操作成功');
  	 }elseif($dotype == 'overorder'){
  	 	  $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' "); 
  	 	   if($checkinfo['psuid'] != $backinfo['uid']){
  	 	     $this->message('该订单不属于您');
  	 	   }
  	 	   if($checkinfo['is_make'] == 2){
  	 	     $this->message('商家不制作该订单');
  	 	   }
  	 	   if($checkinfo['status'] != 2){
  	 	      $this->message('该订单不在配送状态');
  	 	   }
		   if ($checkinfo['is_reback'] >0 && $checkinfo['is_reback'] !=3  && $checkinfo['is_reback'] !=5) {
				$this->message('订单退款中');
			}
  	 	   $data['status'] = 3;
		   if($checkinfo['paytype']==0){
				$data['paystatus'] = 1;
				$data['paytime'] = time();
			 }
  	 	    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$orderid."'");
			//分销返佣
			$is_open_distribution = Mysite::$app->config['is_open_distribution'];
			if($is_open_distribution == 1){
				$distribution = new distribution();
				if($distribution->operateorder(orderid)){
					 
				}else{
					$err = $distribution->Error();
					logwrite('返佣失败，失败原因：'.$err);
				}
			}
		   $this->success('操作成功');
  	 }else{
  	    $this->message('未定义的操作');
  	 }
	}
	 
	function appbuyerone(){
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
		   $this->message('nologin');
		}
		$statusarr = array('0'=>'新订单','1'=>'待发货','2'=>'待评价','3'=>'已完成','4'=>'关闭','5'=>'关闭');
		$psstatus = array('0'=>'待抢订单','1'=>'待取订单','2'=>'已取订单','3'=>'配送完成','4'=>'关闭','5'=>'关闭');
	
		$orderid = intval(IFilter::act(IReq::get('orderid'))); 
		if(empty($orderid)){
			$this->message('订单不存在');
		}
		$backdata =  $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."orderps  where orderid = ".$orderid." order by id desc  "); 
		if(empty($backdata)){
			$this->message('配送单不存在');
		}
	    $orderinfo = $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  order by id desc  ");
		if(empty($orderinfo)){
			$this->message('订单不存在');
		}
		$backdata['addtime'] = date('H:i',$backdata['addtime']);
		$backdata['paystatus'] = $orderinfo['paystatus'];
		$backdata['paytype'] = $orderinfo['paytype']==1?'在线支付':'货到支付';
		$backdata['paystatusname'] = $orderinfo['paystatus'] == 1?'已支付':'未支付';
		$backdata['shopname'] = $orderinfo['shopname'];
		$backdata['shopphone'] = $orderinfo['shopphone'];
		$backdata['shopaddress'] = $orderinfo['shopaddress'];
		$backdata['buyername'] = $orderinfo['buyername'];
		$backdata['buyeraddress'] = $orderinfo['buyeraddress'];
		$backdata['buyerphone'] = $orderinfo['buyerphone'];
		$backdata['orderstatus'] = $statusarr[$orderinfo['status']];
		$backdata['orderaddtime'] = date('m-d H:i',$orderinfo['addtime']);
		if($orderinfo['is_reback'] == 1){
			$backdata['orderstatus'] = '退款中';
		}elseif($orderinfo['is_reback'] == 2){
			$backdata['orderstatus'] = '退款成功';
		}elseif($orderinfo['is_reback'] == 3){
			$backdata['orderstatus'] = '退款失败';
		}
		$backdata['shoptype'] = $orderinfo['shoptype'];
		if($orderinfo['shoptype'] == 100){//跑腿订单
			$backdata['ordertype'] = '跑腿订单';
			$backdata['pttype'] = $orderinfo['pttype'] == 1?'帮我送':'帮我取';
			$backdata['ptkg'] = $orderinfo['ptkg'];//
			$backdata['postyear'] = date('Y-m-d',$orderinfo['sendtime']);
			$backdata['postdate'] = $orderinfo['postdate'];
		}else{
			$backdata['ordertype'] = '普通订单';
			$backdata['pttype'] = '';
			$backdata['ptkg'] = '';
			$backdata['postyear'] = date('Y-m-d',$backdata['pstime']);
			$backdata['postdate'] =  $orderinfo['postdate'];
		}
		$backdata['content'] =  $orderinfo['content'];
		$backdata['bagcost'] =  $orderinfo['bagcost'];
		$backdata['cxcost'] =  $orderinfo['cxcost'];
		$backdata['yhjcost'] =  $orderinfo['yhjcost'];
		$backdata['shopps'] =  $orderinfo['shopps'];
		$backdata['allcost'] =  $orderinfo['allcost'];
		
		$templist =   $this->mysql->getarr("select id,order_id,goodsname,goodscost,goodscount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderid."' ");  
		$newdatalist = array();
		$shuliang = 0;
		foreach($templist as $key=>$value){
			  $value['goodscost'] = $value['goodscost'];
			$newdatalist[] = $value;
			
			$shuliang += $value['goodscount'];
		}  
		$backdata['det'] = $newdatalist;
		
		$this->success($backdata);
		/*
		$where = " where status = 3 and psuid = ".$backinfo['uid']."   and picktime > ".$stardate." and picktime < ".$enddate." ";
		 
		$psorderlist =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  ".$where." order by pstime desc  "); //and ".$newwherearray[$gettype]."
		$templist = array();
	   foreach($psorderlist as $key=>$value){
			
			$value['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
			
		   //  支付类型   支付时间   支付状态    店铺名称  店铺地址   店铺电话  
			//买家地址   买家电话   
			//is_reback   提成费用
			//  
		
	
		    $templist[] = $value;
		 
			
		 
		}
		
		
		
		
		
		
		
		
		
		
		
		
		$statusarr = array('0'=>'新订单不可操作','1'=>'抢单配送','2'=>'确认发货','3'=>'确认完成','4'=>'已完成','5'=>'关闭');
		$paytypelist = array('0'=>'货到支付','1'=>'在线支付');  
		$shoptypearr = array(
			 '0'=>'外卖',
			 '1'=>'超市',
			 '2'=>'其他',
			 );
			   $paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id asc limit 0,50");
			   if(is_array($paylist)){
				 foreach($paylist as $key=>$value){
					$paytypelist[$value['loginname']] = $value['logindesc'];
				 }
			 }
		 
		$orderid = trim(IFilter::act(IReq::get('orderid'))); 
		 
		 $order= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' "); //cxids 促销规则ID集	cxcost  yhjcost  bagcost 
		if(empty($order)){
			$this->message('订单不存在');
		} 

		$backdata['dno'] = $order['dno'];
		$backdata['addtime'] = date('Y-m-d H:i:s',$order['addtime']);
		$backdata['id'] = $order['id'];
		$backdata['allcost'] = $order['allcost'];
		$backdata['shopcost'] = $order['shopcost'];
		$backdata['shopname'] = $order['shopname']; 
		$backdata['showstatus'] = $statusarr[$order['status']];
		$backdata['buyerphone'] = $order['buyerphone'];
		$backdata['shopphone'] = $order['shopphone'];
		$backdata['shopaddress'] = $order['shopaddress'];
		$backdata['buyeraddress'] = $order['buyeraddress'];
		$backdata['posttime'] =  date('Y-m-d H:i:s',$order['posttime']);
		$backdata['psstatus'] = 0;
		if($order['status'] == 0){
		   $backdata['psstatus'] = 0;
		}elseif($order['status'] == 1){
			 if($order['is_make'] == 0){
					$backdata['psstatus'] = 1;
					if($order['psuid']> 0){
						$backdata['psstatus'] = 2;
					}
			 }elseif($order['is_make'] ==2){
			   $backdata['psstatus'] = 5;
			  
		   }elseif($order['is_make'] == 1){
			   $backdata['psstatus'] = 1;
			   if($order['psuid'] > 0){
						$backdata['psstatus'] = 2;
				 }
		   }
		}elseif($order['status'] == 2){
			 $backdata['psstatus'] = 3;
		   if($order['is_make'] ==2){
			   $backdata['psstatus'] = 5;
			  
		   }
			
		}elseif($order['status'] == 3){
			  $backdata['psstatus'] =4;
		}else{
			  $backdata['psstatus'] = 5;
		}
		$backdata['showstatus'] = $statusarr[$backdata['psstatus']];
		$backdata['is_ping'] = $order['is_ping'];
		$backdata['is_make'] = $order['is_make'];
		$backdata['status'] = $order['status'];
		$temlist = array();
		$dotem =   empty($order['paystatus'])?'未支付':'已支付'; 
		$templist[]['mytext'] = '订单编号：'.$order['dno'];
		// $templist[]['mytext'] = '买家地址：'.$order['buyeraddress'];
		 $templist[]['mytext'] = '买家电话：'.$order['buyerphone'];
		// $templist[]['mytext'] = '配送时间：'.date('Y-m-d H:i:s',$order['posttime']);
		$temppaytype = isset($paytypelist[$order['paytype']])? $paytypelist[$order['paytype']]:'未定义';
		$backdata['showpaytyepname'] = $temppaytype;
		$backdata['showpaydo'] = $dotem;
		// $templist[]['mytext'] = '支付类型：'.$temppaytype;
		// $templist[]['mytext'] = '支付状态：'.$dotem; 

		//   $templist[]['mytext'] = '店铺名：'.$order['shopname'];
		//    $templist[]['mytext'] = '店铺地址：'.$order['shopaddress'];
		 $templist[]['mytext'] = '店铺电话：'.$order['shopphone'];
		  $templist[]['mytext'] = '备注：'.$order['content'];
		if($order['bagcost'] > 0){
			$templist[]['mytext'] = '打包费：'.$order['bagcost'];
		}
		if($order['cxcost'] > 0){
			$templist[]['mytext'] = '促销优惠：'.$order['cxcost'];
		}
		if($order['yhjcost'] > 0){
			$templist[]['mytext'] = '优惠券抵扣：'.$order['yhjcost'];
		}
		if($order['shopps'] > 0){
			$templist[]['mytext'] = '配送费：'.$order['shopps'];
		}
		$backdata['itemlist'] = $templist;
		 
		$templist =   $this->mysql->getarr("select id,order_id,goodsname,goodscost,goodscount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderid."' ");  
		$newdatalist = array();
		$shuliang = 0;
		foreach($templist as $key=>$value){
			  $value['goodscost'] = $value['goodscost'];
			$newdatalist[] = $value;
			
			$shuliang += $value['goodscount'];
		}
		//$newgoods = array('id'=>'0','order_id'=>$orderid,'goodsname'=>'总价','goodscount'=>$shuliang,'goodscost'=>$order['allcost']);
		//$newdatalist[] = $newgoods;

		$backdata['det'] = $newdatalist;

		$this->success($backdata); */
	}
   function checkappMem(){
  	$uid = trim(IFilter::act(IReq::get('uid'))); 
  	$pwd = trim(IFilter::act(IReq::get('pwd')));
  	$mapname = trim(IFilter::act(IReq::get('mapname'))); 
  	$uid = empty($uid)?ICookie::get('appuid'):$uid;
  	$pwd = empty($pwd)?ICookie::get('apppwd'):$pwd; 
  	     $member= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' "); 
  	     $backarr = array('uid'=>0);
  	     if(!empty($member)){
  	     	  if($member['password'] == md5($pwd)){
  	     	  	  if($member['group'] == 2){ 
  	     		       $backarr = $member; 
  	     		       ICookie::set('appuid',$member['uid'],86400);
                   ICookie::set('apppwd',$pwd,86400); 
                   ICookie::set('appmapname',$mapname,86400);
               }
  	     	  } 
  	       
  	     } 
     
  	return $backarr;
  }
  function appuserinfo(){
  	$backinfo = $this->checkappMem(); 
  	if(empty($backinfo['uid'])){
  	   $this->message('nologin');
  	} 
  	unset($backinfo['password']);
  	$this->success($backinfo);
  }
  function modify(){
  	$backinfo = $this->checkappMem(); 
  	if(empty($backinfo['uid'])){
  	   $this->message('nologin');
  	} 
  	$oldpwd = IFilter::act(IReq::get('oldpwd'));  
  	$newpwd = IFilter::act(IReq::get('newpwd'));  
  	$surepwd = IFilter::act(IReq::get('surepwd'));  
  	if(empty($oldpwd)){
  	  $this->message('旧密码不能为空'); 
  	}
  	if(empty($newpwd)){
  	   $this->message('新密码不能为空');
  	}
  	if($newpwd != $surepwd){
  	  $this->message('新密码和确认密码不一致');
  	}
  	if($backinfo['password'] != md5($oldpwd)){
  	  $this->message('旧密码错误');
  	}
  	$newdata['password'] = md5($newpwd);
  	 $this->mysql->update(Mysite::$app->config['tablepre'].'member',$newdata,"uid='".$backinfo['uid']."'"); 
  	
    unset($backinfo['password']);
  	$this->success($backinfo); 
  }
  //修改密码  显示用资料
  function mypsordertj(){
    $this->checkmemberlogin();
    $stime = IFilter::act(IReq::get('stime'));
		$etime = IFilter::act(IReq::get('etime')); 
		$where2 = '';
		 
		 $nowdata = strtotime(date('Y-m-d',time()));
   	 
   	 $mintime = $nowdata;
   	 $maxtime = $nowdata+86399;
   	 
		$stime = empty($stime)? $mintime:strtotime($stime.' 00:01');
		$etime = empty($etime)? $maxtime: strtotime($etime.' 23:59'); 
		$where2 .= ' and  addtime  > '.$stime.' and addtime < '.$etime;
		//$this->setdata(array('sitetitle'=>'一个月前订单'));
		 $data['allorder']=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$this->member['uid']."'  and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
		 $data['unline']=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$this->member['uid']."' and paytype ='outpay' and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	     $data['line']= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$this->member['uid']."' and paytype !='outpay'  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

	    
		$data['stime'] = $stime;
		$data['etime'] = $etime; 
	
	 
	  Mysite::$app->setdata($data);
  }
  //配送员订单列表
  function mypsorder(){
  	$this->checkmemberlogin();
  	$stime = IFilter::act(IReq::get('stime'));
		$etime = IFilter::act(IReq::get('etime')); 
		$where = '';
		 
		 $nowdata = strtotime(date('Y-m-d',time()));
   	 
   	 $mintime = $nowdata;
   	 $maxtime = $nowdata+86399;
   	 
		$stime = empty($stime)? $mintime:strtotime($stime.' 00:01');
		$etime = empty($etime)? $maxtime: strtotime($etime.' 23:59'); 
		$where .= ' and  posttime  > '.$stime.' and posttime < '.$etime;
		//$this->setdata(array('sitetitle'=>'一个月前订单'));
		$this->setstatus();
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')),8);
		$data['list'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where psuid='".$this->member['uid']."'   ".$where." order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where psuid='".$this->member['uid']."'    ".$where."   ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar();
		$data['pageall'] = $pageinfo->totalpage();
		$data['pagenow']  = intval(IReq::get('page')) == 0?1:intval(IReq::get('page')) ;
		$data['allcount'] = $shuliang;
		$data['nowtime'] = time();
		$data['stime'] = $stime;
		$data['etime'] = $etime; 
		 
		$link = IUrl::creatUrl('psuser/mypsorder/stime/'.date('Y-m-d',$stime).'/etime/'.date('Y-m-d',$etime).'/page/@page@');

		$data['pagelink'] = $link;
		 
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
		   $paytypelist = array('outpay'=>'货到支付','open_acout'=>'账号余额支付');
		   $paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id asc limit 0,50");
		   if(is_array($paylist)){
		     foreach($paylist as $key=>$value){
		   	    $paytypelist[$value['loginname']] = $value['logindesc'];
		     }
	     }
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
		   '4'=>'APP',
		   '5'=>'手机网站',
		   '6'=>'卖家取消订单'
		   );
		   $data['backarray'] = array(
		   '0'=>'',
		   '1'=>'退款中..',
		   '2'=>'退款成功',
		   '3'=>''
		   );
	    $data['paytypearr'] = $paytypelist;
	  	Mysite::$app->setdata($data);
	}
   //新增函数
	//配送员能查看今天已配送完成的订单
	function showorderpsok(){
		$psyid = IFilter::act(IReq::get('psuid'));
		$where = " where ordps.status = 3 and ordps.psuid =".$psyid." ";
		$daystatus = strtotime(date("Y-m-d",time())."00:00:00");
		$dayend = strtotime(date("Y-m-d",time())."23:59:59");
		$where .=" and ordps.picktime > ".$daystatus." and ordps.picktime < ".$dayend;
		$psorderinfo = $this->mysql->getarr("select ordps.dno,ordps.addtime,ordps.picktime,ordps.psycost,ord.id as orderid, ord.shopname,ord.shopphone,ord.paystatus,ord.shopps,ord.postdate as pstime,ord.dnos,ord.allcost,ord.shopaddress,ord.buyeraddress,ord.buyerphone from ".Mysite::$app->config['tablepre']."orderps as ordps left join ".Mysite::$app->config['tablepre']."order as ord  on ordps.dno = ord.dno ".$where." ");
		$this->success($psorderinfo);
	}
	
	
	function start_work(){
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		}
		$channelid = trim(IFilter::act(IReq::get('channelid')));
		$userid = trim(IFilter::act(IReq::get('userid')));
		$xmuserid =  trim(IFilter::act(IReq::get('xmuserid')));
		if(!empty($userid) || !empty($xmuserid) ){
			$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."apploginps where uid='".$backinfo['uid']."' "); 
		
			$Mdata['channelid'] = $channelid; 
			$Mdata['uid']=$backinfo['uid'];
			$Mdata['addtime'] = time(); 
			if(!empty($checkinfo)){ 
				if(!empty($userid)){
					$Mdata['userid'] = $checkinfo['userid'] !=$userid?$userid:$checkinfo['userid'];
				}
				if(!empty($xmuserid)){
					$Mdata['xmuserid'] =  $checkinfo['xmuserid'] !=$xmuserid?$xmuserid:$checkinfo['xmuserid'];
				}
				$this->mysql->update(Mysite::$app->config['tablepre'].'apploginps',$Mdata,"uid='".$backinfo['uid']."'"); 
			}else{
				$Mdata['userid'] = $userid;
				$Mdata['xmuserid'] = $xmuserid;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'apploginps',$Mdata);  //插入新数据
				
			}
		} 
		$this->success('ok');
		
	}
	
	function close_work(){
		$backinfo = $this->checkappMem(); 
		if(empty($backinfo['uid'])){
			$this->message('nologin');
		}
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."apploginps where uid='".$backinfo['uid']."' "); 
		$this->mysql->delete(Mysite::$app->config['tablepre'].'apploginps',"uid='".$backinfo['uid']."'");
		$this->success('ok');
	}
  
  
}