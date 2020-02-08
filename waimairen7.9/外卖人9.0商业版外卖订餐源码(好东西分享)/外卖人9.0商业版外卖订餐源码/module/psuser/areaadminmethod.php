<?php
class method   extends areaadminbaseclass
{
	function memberlistps(){
	   
	 	 $this->checkadminlogin();
	 	 $data['username'] =  trim(IReq::get('username'));
	   $data['email'] =  trim(IReq::get('email'));
	 	 $data['groupid'] = 2;
	 	 $data['phone'] =  trim(IReq::get('phone'));
	 	 //构造查询条件
	 	 $where = '';
	 	 $where =  $this->sqllink($where,'username',$data['username'],'=');
	 	 $where =  $this->sqllink($where,'email',$data['email'],'=');
	 	 $where =  $this->sqllink($where,'group',$data['groupid'],'=');
	 	 $where =  $this->sqllink($where,'phone',$data['phone'],'=');
	 	 $where =  $this->sqllink($where,'admin_id',$this->admin['cityid'],'=');
	 	 $data['where'] = $where;
	 	 
	 	 Mysite::$app->setdata($data);  
	 }
	  
	 function setps(){
	 	$this->checkadminlogin();
		$uid =  intval(IReq::get('uid'));
	 	  if(empty($uid))
	 	  {
	 	  	 echo '配送员不存在';
	 	  	 exit;
	 	   }
	 	  $psyinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$uid."  and   `group` =2");
	 	  if(empty($psyinfo))
	 	  {
	 	     echo '配送员不存在';
	 	  	 exit;
	 	  } 
	    $areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   order by orderid asc"); 
		$parentids = array();
		foreach($areainfo as $key=>$value){
		  $parentids[] = $value['parent_id'];
		} 
		 $parentids = array_unique($parentids);
		 $data['parent_ids'] = $parentids;
	 	 $this->getgodigui($areainfo,0,0);
	 	 $data['arealist'] = $this->digui;  
	 	 //店铺可配送区域
	 	 $choicelist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."areapsy where psyuid = ".$uid."   order by areaid asc limit 0,1000");
	 	 $temp = array();
	 	 foreach($choicelist as $key=>$value){
	 	   $temp[]= $value['areaid'];
	 	 }
	 	 $data['choiceid']  = $temp;  
	 	 $data['psyinfo'] = $psyinfo;
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
	 
	 
	 function psytj(){ 
	 
	 
		$searchvalue = IReq::get('searchvalue');
		$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime'));
		$areaid = intval(IReq::get('areaid'));
		$newlink = '';
		$where= ' where `group`=2';
		$where2 = '';
		$where3 = "";
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
			 $where2 .= ' and  posttime > '.strtotime($starttime.' 00:00:01').' ';
			 $where3 .=' and  picktime > '.strtotime($starttime.' 00:00:01').' ';
			 $newlink .= '/starttime/'.$starttime;
		}
		$data['endtime'] = '';
		if(!empty($endtime))
		{
			 $data['endtime'] = $endtime;
			 $where2 .= ' and  posttime < '.strtotime($endtime.' 23:59:59').' ';
			 $where3 .=' and  picktime < '.strtotime($endtime.' 23:59:59').' ';
			$newlink .= '/endtime/'.$endtime;
		}
	/* 	if(!empty($areaid)){
			if($areaid > 0){
				$areainfo = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."area where id='".$areaid."'  ");
				if(!empty($areainfo)){
					if($areainfo['admin_id'] > 0){ */
					 $admin_id = $this->admin['cityid'];
						$where .= ' and admin_id = \''.$admin_id.'\' ';
					/* }
				}
			} 
		} */
	 
	  
		$pageinfo = new page();
		$pageinfo->setpage(IReq::get('page'),100);
		 
		$memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member ".$where."   order by uid asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		 
		$list = array();
		if(is_array($memberlist))
		{
	     foreach($memberlist as $key=>$value)
	     {
	     	  //判断店铺配送类型
	     	   

	     	  // $value['sendtype'] = empty($sendtype)?'网站配送':'自送';
	         $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype ='0' and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	         $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype !='0'  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

	         $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
	          
	         $value['online'] = $line['doallcost']; 
	         $value['unline'] = $shoptj['doallcost'];  
			  $tempc  =  $this->mysql->select_one("select sum(psycost) as tjcost from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$value['uid']." and status =3  ".$where3." "); 
			  $value['tjcost'] = isset($tempc['psycost'])?0:$tempc['tjcost'];
			  $list[] = $value;
		   }
		}

		$data['list'] =$list; 
		//print_r($data);
		$shuliang  = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."member ".$where."  ");
	 
		Mysite::$app->setdata($data);
      
   	
   }
   function getpsyorderlist(){
	   
	    $psuid = intval(trim(IReq::get('psuid')));
	   	$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime'));
	      $where = ' where  psuid = \''.$psuid.'\' ';
		   $where .= ' and  posttime > '.strtotime($starttime.' 00:00:01').' ';
		    $where .= ' and  posttime < '.strtotime($endtime.' 23:59:51').' ';
			$where .=" and status = 3 ";
	   
		$orderlist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order ".$where."   order by id asc  limit 0,1000");
		 
		foreach($orderlist as $key=>$value){
			
			 $tempc  =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps  where orderid = ".$value['id']." and status =3    "); 
			
			$psycost = isset($tempc['psycost'])?$tempc['psycost']:0;
			$checkstatu  = $value['paystatus'] ==0?'未付':'已支付';
			
			if( $value['paytype']  == 0  ){
				$paytypetext = 	"货到付款";			
			}
			if( $value['paytype']  == 1 && $value['paystatus'] == 1  ){
				if( $value['paytype_name']  == 'open_acout'  ){
					$paytypetext = 	"余额支付";	
				}
				if( $value['paytype_name']  == 'weixin'  ){
					$paytypetext = 	"微信支付";	
				}
				if( $value['paytype_name']  == 'alimobile' || $value['paytype_name']  == 'alipay'   ){
					$paytypetext = 	"支付宝支付";	
				}	
				if( $value['paytype_name']  == ''  ){
					$paytypetext = 	"在线支付";	
				}				
			}
			
			
			echo ' <table class="xqOrderlist showdetmm_'.$value['id'].'"  width="100%" align="center" border="" cellpadding="0" cellspacing="0" bordercolor="#6596a9" >'; 
			echo '   <tr align="center">';
			echo '    <td><div><span  style=" color:#bceafe; font-size:14px; ">单号: </span><span><font color=red>'.$value['dno'].'</font></span></div></td>';
			echo '<td><div><span  style=" color:#bceafe; font-size:14px; ">商家: </span><span>'.$value['shopname'].'</span></div></td>';
			echo '	  <td><div><span  style=" color:#bceafe; font-size:14px; ">支付方式: </span><span>'.$paytypetext.'</span></div></td>';
			
            echo '  <td ><div><span  style=" color:#bceafe; font-size:14px; ">总价:</span> <span>'.$value['allcost'].'</span></div></td>';
             echo '  <td colspan="2" ><div><span  style=" color:#bceafe; font-size:14px; ">配送员提成:</span> <span>'.$psycost.'</span></div></td>';
             
            echo '</tr> ';
			echo '</table>';
		} 
		 
	 exit;
   }
	 
	 function ordertodaytable(){
	    $areaid = intval(IReq::get('areaid'));
		$statustype =  intval(IReq::get('statustype'));
		$data['typeid']  = $typeid;
		$dno = IReq::get('dno');
		$data['dno'] = $dno;
		$data['statustype'] = $statustype;

		$statustype = in_array($statustype,array(1,2,3,4,5))?$statustype:0;
		$statustypearr = array(
		'0'=>' ',
		'1'=>'    and ord.status = 0 ',
		'2'=>' and ord.pstype = 0 and ord.is_goshop =0 and ord.status = 1 and (ord.psuid = \'0\' or ord.psuid is null) ',// and ord.pstype = 0 and ord.is_goshop =0   and ord.status = 1
		'3'=>'   and ord.status > 0 and ord.status < 3 and (( ord.pstype = 0 and ord.is_goshop =0 ) or ord.pstype=1 or ord.is_goshop=1)',
		'4'=>'    and ord.status =3   ',
		); 
	    
		$this->setstatus();
		$nowday = date('Y-m-d',time());
		$where = '  where ord.posttime > '.strtotime($nowday.' 00:00:00').' and ord.posttime < '.strtotime($nowday.' 23:59:59');
		//查询当天所有订单数据
	/* 	$areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area  where id = '".$areaid."' order by orderid asc");
	 
		if(!empty($areainfo)){
		  if($areainfo['admin_id'] > 0){
		     $where .= " ord.admin_id = '".$areainfo['admin_id']."' ";
		  }
		} 
		 */
		
			 $admin_id = $this->admin['cityid'];
			$where .= ' and ord.admin_id = \''.$admin_id.'\' ';
		
		$where .= $statustypearr[$statustype];
	 
		//$where .= ' and ord.status = 0 ';
		$where .= empty($dno)?'':' and ord.dno =\''.$dno.'\'';
		 

		$orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." order by ord.id desc limit 0,1000");
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
			    $data['list'][] = $value;
				 
			}
		}
	  /*构造城市*/



	 


	 #	$data['showdet'] = intval(IReq::get('showdet')); 
			Mysite::$app->setdata($data);
	 
	#	Mysite::$app->setAction('order');
		 
	
	}
	 
	 
	 
	 function savesetps(){
	 	  $this->checkadminlogin(); 
	 	 $uid =  intval(IReq::get('uid'));
		  if(empty($uid)){ 
			 echo "<script>parent.uploaderror('配送员不存在');</script>";
		 	 exit; 
		  }
		 $psyinfo= $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$uid."  and   `group` =2");
	 	  if(empty($psyinfo))
	 	  {
	 	     	echo "<script>parent.uploaderror('配送员不存在');</script>";
		 	 exit; 
	 	  }  
	   $areaids = IReq::get('areaids');
	   
	   if(is_array($areaids)){ 
	 	   //首先清理数据存在的区域数据
	 	   $this->mysql->delete(Mysite::$app->config['tablepre']."areapsy"," psyuid='".$uid."'  "); 
	 	   $checkareais = $areaids;
	 	   while(count($checkareais) > 0){
	 	   	   $checkarealist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where id in(".join(',',$checkareais).")    order by id desc limit 0,1000");
	 	   	   $checkareais = array();
	 	   	   foreach($checkarealist as $key=>$value){
	 	   	   	  $areadata['psyuid'] = $uid;
	 	   	   	  $areadata['areaid'] = $value['id']; 
	 	   	      $this->mysql->insert(Mysite::$app->config['tablepre']."areapsy",$areadata);
	 	   	      if($value['parent_id'] > 0){
	 	   	        $checkareais[] = $value['parent_id'];
	 	   	      }
	 	   	   }
	 	       $checkareais = array_unique($checkareais);
	 	   } 
  	  }else{ 
  		    echo "<script>parent.uploaderror('配送区域未选择');</script>";
		 	    exit;  
  	  } 
	   echo "<script>parent.uploadsucess('');</script>";
		 exit; 
	 }
	 function psymapdddd(){
	 	   $searchvalue = IReq::get('searchvalue');
		$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime'));
		$admin_id = intval(IReq::get('admin_id'));
		$newlink = '';
		$where= ' where `group`=2';
		$where2 = '';
		$where3 = "";
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
			 $where2 .= ' and  posttime > '.strtotime($starttime.' 00:00:01').' ';
			 $where3 .=' and  picktime > '.strtotime($starttime.' 00:00:01').' ';
	   	 $newlink .= '/starttime/'.$starttime;
		}
		$data['endtime'] = '';
		if(!empty($endtime))
		{
			 $data['endtime'] = $endtime;
			 $where2 .= ' and  posttime < '.strtotime($endtime.' 23:59:59').' ';
			 $where3 .=' and  picktime < '.strtotime($endtime.' 23:59:59').' ';
	   	 $newlink .= '/endtime/'.$endtime;
		}
		 
			$where .= ' and admin_id = \''.$this->admin['cityid'].'\' ';
		 
		$data['admin_id'] = $admin_id;
		$link = IUrl::creatUrl('areaadminpage/psuser/module/outpstjorder'.$newlink);
	  $data['outlink'] =IUrl::creatUrl('areaadminpage/psuser/module/outpstjorder/outtype/query'.$newlink);
	  $data['outlinkch'] =IUrl::creatUrl('areaadminpage/psuser/module/outpstjorder'.$newlink);
		$pageinfo = new page();
		$pageinfo->setpage(IReq::get('page'));
		 
		$memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member ".$where."   order by uid asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		 
		$list = array();
		if(is_array($memberlist))
		{
	     foreach($memberlist as $key=>$value)
	     {
	     	  //判断店铺配送类型
	     	   

	     	  // $value['sendtype'] = empty($sendtype)?'网站配送':'自送';
	         $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype ='outpay' and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	         $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype !='outpay'  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

	         $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
	          
	         $value['online'] = $line['doallcost'];//$line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额
	          
	         $value['unline'] = $shoptj['doallcost'];//$shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown; 
			 
			  $tempc  =  $this->mysql->select_one("select sum(psycost) as tjcost from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$value['uid']." and status =3  ".$where3." "); 
			  $value['tjcost'] = isset($tempc['psycost'])?0:$tempc['tjcost'];
			 
		       $list[] = $value;
		   }
		}

		$data['list'] =$list; 
		//print_r($data);
		$shuliang  = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."member ".$where."  ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar($link);
		Mysite::$app->setdata($data);
      
   	
   }
   
   function psymap(){ 
		$data['arealist']= $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area  where parent_id =0  and adcode = '".$this->admin['cityid']."'  order by id asc   limit 0,2000 ");
	   Mysite::$app->setdata($data);
	}
	function getpsorder(){ 
	
	   $userid = intval(IReq::get('userid')); 
	   $this->setstatus();
	    $todatay = strtotime(date('Y-m-d',time()));
      $endtime = $todatay + 86399;
		  $data['orderlist'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where psuid='".$userid."' and posttime > ".$todatay." and posttime < ".$endtime." ");
		 
		
	   Mysite::$app->setdata($data);
		
	}
   
   function outpstjorder()
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
		   $where2 .= !empty($starttime)? ' and  posttime > '.strtotime($starttime.' 00:00:01').' ':'';

		   $endtime = trim(IReq::get('endtime'));
		   $where2 .= !empty($endtime)? ' and  posttime < '.strtotime($endtime.' 23:59:59').' ':'';

		}else{
		   $searchvalue = trim(IReq::get('searchvalue'));
		   $where .= !empty($searchvalue)? ' where username = \''.$searchvalue.'\'':'';
		 //   $data['searchvalue'] = $searchvalue;
	   //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

		   $starttime = trim(IReq::get('starttime'));
		   $where2 .= !empty($starttime)? ' and  posttime > '.strtotime($starttime.' 00:00:01').' ':'';

		   $endtime = trim(IReq::get('endtime'));
		   $where2 .= !empty($endtime)? ' and  posttime < '.strtotime($endtime.' 23:59:59').' ':'';
		  
		}
		$where .= empty($where)?' where admin_id =\''.$this->admin['cityid'].'\'':' and admin_id =\''.$this->admin['cityid'].'\'';
		$where.= empty($where)?' where `group`=2 ':' and `group`=2 ';
	  $memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member ".$where."   order by uid asc  limit 0,2000 ");
		$list = array();
		if(is_array($memberlist))
		{
	     foreach($memberlist as $key=>$value)
	     {
 
	     	   $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype ='outpay' and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
	         $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype !='outpay'  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

	         $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
	          
	         $value['online'] = $line['doallcost'];//$line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额
	          
	         $value['unline'] = $shoptj['doallcost'];//$shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown; 
		       $list[] = $value;
		   }
		}
		 $outexcel = new phptoexcel();
		 $titledata = array('配送员','订单总数','线上交易金额','线下交易金额');
		 $titlelabel = array('username','orderNum','online','unline');
		// $datalist = $this->mysql->getarr("select card,card_password,cost from ".Mysite::$app->config['tablepre']."card where id > 0 ".$where."   order by id desc  limit 0,2000 ");
		 $outexcel->out($titledata,$titlelabel,$list,'','配送员结算');
	} 
	function setpsyuan(){
	 	$uid = intval(IReq::get('uid'));
	 	$admin_id = intval(IReq::get('adminid'));
	 	if(empty($uid)) $this->message('member_noexit'); 
	 	$data['admin_id'] = $admin_id;
	 	$this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$uid."'");	  
	 	$this->success('success');
	}
	function selectps(){
   	  $orderid = intval(IReq::get('orderid'));
	 	  $data['orderinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."");
	 	  if(!empty($data['orderinfo']['psuid'])){
	 	    echo '配送员已选择';
	 	    exit;
	 	  }
      $memberlist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."member where `group` = 2 and admin_id='".$data['orderinfo']['admin_id']."' limit 0,1000");
      $data['memberlist'] = array();
      $nowdata = strtotime(date('Y-m-d',time()));
   	 
   	 $mintime = $nowdata;
   	 $maxtime = $nowdata+86400;
   	 $where = ' where  status < 4 and posttime > '.$mintime.' and posttime < '.$maxtime;
      foreach($memberlist as $key=>$value){
        $test = $where.' and psuid='.$value['uid'];
        $value['shuliang'] = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."order ".$test." limit 0,1000");
        $data['memberlist'][] = $value;
      }
      Mysite::$app->setdata($data);
   }
   function selectorder(){
   	 $uid = intval(IReq::get('uid'));
   	 if(empty($uid)){
   	   echo '暂无数据';
   	   exit;
   	 }
   	 $nowdata = strtotime(date('Y-m-d',time()));
   	 
   	 $mintime = $nowdata;
   	 $maxtime = $nowdata+86400;
   	 $where = ' where psuid='.$uid.' and status < 4 and posttime > '.$mintime.' and posttime < '.$maxtime;
   	 $orderlist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."order ".$where." limit 0,1000");
   	 
   	 $htmls = '';
   	 foreach($orderlist as $key=>$value){
   	 	$htmls .='<ul><li class="shopname">'.$value['shopname'].'</li>';
   	 	$htmls .='<li class="addressa">'.$value['buyeraddress'].'</li>';
   	 	$htmls .='<li class="posttime">'.date('H:i:s',$value['posttime']).'</li></ul>';
   	 }
   	 echo $htmls;
   	 exit;
   }
   function psyposition(){ 
		$data['arealist']= $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area  where parent_id =0 and admin_id = '".$this->admin['cityid']."' order by id asc  limit 0,2000 ");
	  Mysite::$app->setdata($data);
	}
	 
	 function ajaxpsy(){ 
	  //$this->message('xxx');
	  /***获取所有配送员定位地址*/
	  /**统计配送员信息*/
	  //xiaozu_areapsy
	  //xiaozu_locationpsy
	  //psuid	psusername
	  //http://192.168.0.109/index.php?contrller=adminpage&action=psuser&module=ajaxpsy&areaid=0&random=633&datatype=json
	  $areaid = intval(IReq::get('areaid'));
	  
	  $where = '';
	  if(!empty($areaid)){
	  	$checkinfo = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."area where id = '".$areaid."'");
	  	if(!empty($checkinfo)){
	  	  $where .= " where admin_id ='".$checkinfo['adcode']."'";
	  	}
	    // $where = " where a.uid in (select psyuid from ".Mysite::$app->config['tablepre']."areapsy  where areaid ='".$areaid."' )";
	  }
	  //xiaozu_locationpsy
	  $datalist = $this->mysql->getarr("select a.*,b.username from ".Mysite::$app->config['tablepre']."locationpsy as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid ".$where." order by a.uid asc  limit 0,100 ");
	  $data['psylist'] = array();
	  $todatay = strtotime(date('Y-m-d',time()));
    $endtime = $todatay + 86399;
	  if(is_array($datalist)){
	    foreach($datalist as $key=>$value){
			if($value['username'] != ''){
				$value['waitps'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where psuid='".$value['uid']."' and status = 1 and posttime > ".$todatay." and posttime < ".$endtime."  ");
				$value['overps'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where psuid='".$value['uid']."' and status > 1 and status < 4 and posttime > ".$todatay." and posttime < ".$endtime."  ");
				
				$data['psylist'][] = $value;
			}	
	    }
	  }
	  if(count($data['psylist']) < 1){
	  	  $this->message('无配送员数据');
	  }
	  $this->success($data['psylist']);
	  
	  
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
	
	
	
	function psyyj(){ 
		$searchvalue = IReq::get('searchvalue');
		$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime')); 
		$newlink = '';
		
		 $cityid = $this->admin['cityid'];
		
		$where= " where `group`=2   and ( admin_id = '".$cityid."' or admin_id = 0 )    ";
		$where2 = '';
		$where3 = "";
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
			 $where2 .= ' and  posttime > '.strtotime($starttime.' 00:00:01').' ';
			 $where3 .=' and  picktime > '.strtotime($starttime.' 00:00:01').' ';
			 $newlink .= '/starttime/'.$starttime;
		}
		$data['endtime'] = '';
		if(!empty($endtime))
		{
			 $data['endtime'] = $endtime;
			 $where2 .= ' and  posttime < '.strtotime($endtime.' 23:59:59').' ';
			 $where3 .=' and  picktime < '.strtotime($endtime.' 23:59:59').' ';
			$newlink .= '/endtime/'.$endtime;
		} 
		$link = IUrl::creatUrl('/areaadminpage/psuser/module/psyyj'.$newlink);
		$pageinfo = new page();
		$pageinfo->setpage(IReq::get('page'),10); 
		$memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member ".$where."   order by uid asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize().""); 
		$list = array();
		 
		if(is_array($memberlist))
		{
			foreach($memberlist as $key=>$value)
			{ 
				$shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype ='0' and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
				$line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype !='0'  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000"); 
				$value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数 
				$value['online'] = $line['doallcost']; 
				$value['unline'] = $shoptj['doallcost'];  
				$tempc  =  $this->mysql->select_one("select sum(psycost) as tjcost from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$value['uid']." and status =3  ".$where3." and dotype < 3 "); 
				$tempc2  =  $this->mysql->select_one("select sum(psycost) as tjcost from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$value['uid']." and status =3  ".$where3." and dotype = 3 "); 
				$value['tjcost'] = isset($tempc['psycost'])?0:$tempc['tjcost']; 
				$value['tjcost'] =$value['tjcost']- $tempc2['tjcost'];
				$value['outdetail'] =IUrl::creatUrl('areaadminpage/psuser/module/psyout/uid/'.$value['uid'].$newlink);
				$list[] = $value;
			}
		} 
		$data['memberlist'] =$list;  
		$shuliang  = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."member ".$where."  ");
	    $pageinfo->setnum($shuliang);
	    $data['pagecontent'] = $pageinfo->getpagebar($link); 
	    Mysite::$app->setdata($data); 
	}
	function psyout(){
		
	    $starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime')); 
		$uid = intval(IReq::get('uid'));
		if(empty($uid)){
			header("Content-Type: text/html; charset=UTF-8");
			 echo '用户不存在';
			 exit;
			
		}
		$memberinfo = $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."member where uid = '".$uid."' ");
		if(empty($memberinfo)){
			header("Content-Type: text/html; charset=UTF-8");
			 echo '用户不存在';
			 exit;
		}
		
		$where3 = "";
		 
	 
		if(!empty($starttime)) $where3 .=' and  picktime > '.strtotime($starttime.' 00:00:01').' ';
	 
		if(!empty($endtime)) $where3 .=' and  picktime < '.strtotime($endtime.' 23:59:59').' '; 
		
		$tempc  =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$uid." and status =3  ".$where3."   limit 0,4000 "); 
	 	  
		
	 
		$list = array();
		if(is_array($tempc))
		{
			 foreach($tempc as $key=>$value)
			 { 
			    if($value['dotype'] == 2){
					$value['dotypename'] = '后台增加'; 
				}elseif($value['dotype'] == 3){
					$value['dotypename'] = '后台减少'; 
				}else{
					$value['dotypename'] = '配送订单';
				}
				$value['addtime'] = date('Y-m-d',$value['addtime']);
				$value['picktime'] = date('Y-m-d',$value['picktime']);
				 
			   $list[] = $value;
				  // $list[] = $value1;
		   }
		} 
		 $outexcel = new phptoexcel();
		 $titledata = array('描述','资金类型','收入','创建时间','取货时间');
		 $titlelabel = array('dno','dotypename','psycost','addtime','picktime'); 
		 $outexcel->out($titledata,$titlelabel,$list,'',$memberinfo['username'].'配送员配送详情'); 
	}
	

}
