<?php
class method   extends areaadminbaseclass
{ 
	 
	 function index(){
	    
		
	   $mftime = strtotime(date('Y-m',time()));
		 $metime = time();//strtotime(date('Y-m',time()).'-'.date('t',time()).' 23:59:59 ');//,"lasttime"=>mktime(23,59,59,$m,$d,$y)); 
		 $dftime = strtotime(date('Y-m-d',time())); 
		 $detime = time();//今天订单将配送时间做为当前时间 
  
	 $areawhere = " and  admin_id = ".$this->admin['cityid']." ";
	 
	 	/* 代理区域 */
	$data['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id = 0  ".$areawhere."  ");
	 
	    // 今日总订单	
     $tjdata['dayallorder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  where posttime > $dftime and posttime < $detime  ".$areawhere." ");
     //今日待审核订单	  
	   $tjdata['dayworder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  where posttime > $dftime and posttime < $detime  and status = 0   ".$areawhere." ");
     /// 今日已审核订单
     $tjdata['dayporder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  where posttime > $dftime and posttime < $detime  and status > 0 and status < 4   ".$areawhere." ");
     // 本月已完成订单	 
     $tjdata['monthallorder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  where addtime > $mftime and addtime < $metime  and status = 3   ".$areawhere." ");//
     /// 已完成订单总量 
	   $tjdata['allorder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  where  status = 3  ".$areawhere." "); 
    //已上线商家
    $tjdata['onlineshop'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where  is_pass = 1    ".$areawhere."   ");
    //待审核商家
     $tjdata['wshop'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where  is_pass = 0   ".$areawhere." ");
    //普通会员
      $tjdata['pmember'] = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."member   ");
	  
	  //配送员数量
      $tjdata['psymember'] = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."member  where  `group` = 2   ".$areawhere." ");
 	  
    //商城订单
      $tjdata['market'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where shopid=0 ");
	  
	  
	  
    //商品数量
	$areashop = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop  where  is_pass = 1    ".$areawhere."   ");
	$areashopids = array();
	foreach($areashop as $key=>$value ){
		$areashopids[] = $value['id'];
	}
	$areashopids = implode(',',$areashopids);
	#print_r($areashopids);
	$tjdata['marketg'] = 0;
	if(  !empty($areashopids) ){
		$tjdata['marketg'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."goods where shopid in (".$areashopids.")  ");
	} 
	  
	  
      $data['tjdata'] = $tjdata; 
	    $data['serverurl'] = Mysite::$app->config['serverurl'];  
		
		 
	    Mysite::$app->setdata($data);  
	 }
} 
?>