<?php
class method   extends baseclass
{
	function index(){
               
	 	      if(empty($this->member['uid'])){
	 	    		 $link = IUrl::creatUrl('order/guestorder');
             $this->refunction('',$link);
	 	    	}elseif(!empty($this->member['uid'])){
	 	    	 $link = IUrl::creatUrl('order/usersorder');
           $this->refunction('',$link);
          }
	}
	 
	function printbyshop(){
	   $shopid =   intval(IFilter::act(IReq::get('shopid')));
	   if(empty($shopid)){
	     echo '店铺ID错误';
	     exit;
	   }
	   $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id = ".$shopid."   ");
	   if(empty($shopinfo)){
	     echo '店铺信息获取失败';
	     exit;
	   }
	   $data['contactname'] =  trim(IFilter::act(IReq::get('contactname')));
	   $data['phone'] = trim(IFilter::act(IReq::get('phone')));
	   $data['address'] = trim(IFilter::act(IReq::get('address')));
	   $data['shopinfo'] = $shopinfo;
	   $ids = IFilter::act(IReq::get('ids'));
	   	$data['goodslist'] = array();
	   	$sumcost = 0;
	   	$bagcost = 0;
	   
	   if(!empty($ids)){
		   if(empty($ids)){
			 echo '商品ID错误';
			 exit;
		   }
		   $num = IFilter::act(IReq::get('nums'));
		   if(empty($num)){
			 echo '商品数量错误';
			 exit;
		   }
		   $tempids = explode(',',$ids);
		   $tempnum = explode(',',$num);
		   if(count($tempids) != count($tempnum)){
			 echo '商品数量和商品ID不一致';
		   }
		   $newid = array();
		   $idtonum = array();
		   foreach($tempids as $key=>$value){
			  if(!empty($value)){
				   $check1 = intval($value);
				   $check2 = intval($tempnum[$key]);
				   if($check1 > 0 && $check2 > 0){
					   $newid[] = $value;
					   $idtonum[$value] = $check2;
				   }
			  }
		   }
		   $whereid = join(',',$newid);
		   if(empty($whereid)){
			  echo '数据错误';
			  exit;
		   }

			$orderlist = $this->mysql->getarr("select id,name,cost,bagcost from ".Mysite::$app->config['tablepre']."goods where shopid =".$shopid." and id in(".$whereid.") ");
		   
			foreach($orderlist as $key=>$value){
			   $value['shuliang'] = $idtonum[$value['id']];
			   $sumcost += $value['cost']*intval($idtonum[$value['id']]);
			   $value['xiaoij'] = $value['cost']*intval($idtonum[$value['id']]);
			   $bagcost += $value['bagcost']*intval($idtonum[$value['id']]);
			   $data['goodslist'][] = $value;
			}
		}
		
		$pids = IFilter::act(IReq::get('pids')); 
		$pnum = IFilter::act(IReq::get('pnum'));
		if(empty($ids)&&empty($pids)){
			  echo '数据错误';
			  exit;
		}
		
		
		
		if(!empty($pids)){
				$temppids = explode(',',$pids);
				$temppnum = explode(',',$pnum);
				$pnewid = array();
				$pidtonum = array();
				foreach($temppids as $key=>$value){
					if(!empty($value)){
					   $check1 = intval($value);
					   $check2 = intval($temppnum[$key]);
					   if($check1 > 0 && $check2 > 0){
						   $newid[] = $value;
						   $pidtonum[$value] = $check2;
					   }
				  }
			   }
			   $whereid = join(',',$newid);
			   if(!empty($whereid)){
				   $tempclist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."product where shopid =".$shopid." and id in(".$whereid.") ");
				   foreach($tempclist as $key=>$value){
					   $dosee = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where shopid =".$shopid." and id =".$value['goodsid']." ");
					   $dosee['gg'] = $value;
					   $dosee['shuliang'] = $pidtonum[$value['id']];
					   $dosee['name'] = $dosee['name'].$value['attrname'];
					   $dosee['xiaoij'] = $value['cost']*intval($pidtonum[$value['id']]);
					   $dosee['cost'] = $value['cost'];
					   $sumcost += $dosee['cost']*intval($pidtonum[$value['id']]);
					    $bagcost += $dosee['bagcost']*intval($pidtonum[$value['id']]);  
						$data['goodslist'][] =$dosee;
				   }
				   
			   }
			
		}
		
		
		
	   	$data['bagcost'] = $bagcost;
	   	$data['sumcost'] = $sumcost;

	   Mysite::$app->setdata($data);
	}

  function fastfoodshop(){
   	 $id = IFilter::act(IReq::get('shopid'));
   	 $shopinfo =  $this->mysql->select_one("select id,shopname,starttime,shoptype,address,phone from ".Mysite::$app->config['tablepre']."shop  where   id = ".$id." order by id desc ");
   	 if(empty($shopinfo)){
   	   echo '店铺数据为空';
   	   exit;
   	 }
   	 if($shopinfo['shoptype'] == 0){
   	 		$shoptype = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."goodstype where shopid='".$id."' order by orderid asc ");
   	 		$data['shopdet'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid='".$id."' ");
   	 }else{
   	 	  $shoptype = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."marketcate where shopid = '".$id."' and parent_id != 0 order by orderid asc  ");
   	 	  $data['shopdet'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$id."' ");
   	 }
   	 if(empty($data['shopdet'])){
   	 	echo '店铺未设置商品详情';
   	 	exit;
   	 }
	 $nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($data['shopdet']['postdate'])?unserialize($data['shopdet']['postdate']):array();
		$data['pstimelist'] = array();
		$checknow = time();
		$whilestatic =$data['shopdet']['befortime'];
		$nowwhiltcheck = 0;
		while($whilestatic >= $nowwhiltcheck){
		    $startwhil = $nowwhiltcheck*86400;
			foreach($timelist as $key=>$value){
				$stime = $startwhil+$nowhout+$value['s'];
				$etime = $startwhil+$nowhout+$value['e'];
				if($etime  > $checknow){
					$tempt = array();
					$tempt['value'] = $value['s']+$startwhil;
					$tempt['s'] = date('H:i',$nowhout+$value['s']);
					$tempt['e'] = date('H:i',$nowhout+$value['e']);
					$tempt['d'] = date('Y-m-d',$stime);
					$tempt['s'] = $tempt['d'].' '.$tempt['s'];
					$tempt['i'] =  $value['i'];
					$tempt['cost'] =  isset($value['cost'])?$value['cost']:0; 
					$tempt['name'] = $stime > $checknow?$tempt['s'].'-'.$tempt['e']:'立即配送';
					$data['pstimelist'][] = $tempt;
				}
			}
		 
			$nowwhiltcheck = $nowwhiltcheck+1;
		}
	$goodslist = array();
	$tempgoodslist = $this->mysql->getarr("select id,name,cost,bagcost,count,typeid,have_det from ".Mysite::$app->config['tablepre']."goods where   shopid=".$id." order by id asc limit 0,1000  ");
   	foreach($tempgoodslist as $key=>$value){
		if($value['have_det'] ==1) {
			$detlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."product where   shopid=".$id." and goodsid= ".$value['id']." "); 
			foreach($detlist as $k=>$v){
				$newtemp = $value;
				$newtemp['product_id'] = $v['id'];
				$newtemp['name'] = $value['name']."【".$v['attrname']."】"; 
				$newtemp['cost'] = $v['cost'];
				if($value['is_cx'] == 1){
				//测算促销 重新设置金额
					$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$value['id']."  ");
					$newdata = getgoodscx($value['cost'],$cxdata);
					
					$newtemp['zhekou'] = $newdata['zhekou'];
					$newtemp['is_cx'] = $newdata['is_cx'];
					$newtemp['cost'] =  round( $newdata['cost'] ,2);
				}
				
				
				$newtemp['count'] = $v['stock'];
				$goodslist[] =$newtemp;
			}
			
		}else{
			$value['product_id'] = 0;
			if($value['is_cx'] == 1){
				//测算促销 重新设置金额
					$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$value['id']."  ");
					$newdata = getgoodscx($value['cost'],$cxdata);
					
					$newtemp['zhekou'] = $newdata['zhekou'];
					$newtemp['is_cx'] = $newdata['is_cx'];
					$newtemp['cost'] =  round( $newdata['cost'] ,2);
				}
			$goodslist[] = $value;
		}
	}


	$data['shop'] = $shopinfo;
   	 $data['goodstype'] = $shoptype;
//         print_R($goodslist);exit;
   	 $data['goods'] = $goodslist;
   	 Mysite::$app->setdata($data);
   }
   //快速选择地址
   function areashow(){
   	  $shopid = intval(IFilter::act(IReq::get('shopid')));

   	  //获取店铺所有地址
   	  $shoptype = 'shop';
   	  $shopset = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast  where shopid=".$shopid."");
   	  if(empty($shopset)){
   	  	  $shoptype = 'market';
   	      $shopset = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket  where shopid=".$shopid."");
   	   }
   	    
   	    $shoparea =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."areashop  where shopid=".$shopid."");
   	 
   	  if(empty($shoparea)){
   	    echo '店铺区域数据不存在';
   	    exit;
   	  }
   	  $where = '';
   	  $tempids = array();
   	  foreach($shoparea as $key=>$value){
   	      $tempids[] = $value['areaid'];
   	  }
   	  $where = join(',',$tempids);
   	  if(empty($where)){
   	     echo '店铺区域ID值获取失败';
   	     exit;
   	  }


   	  $id =IFilter::act(IReq::get('id'));
   	  $parent_id = 0;
	  	if($id > 0){
	 	     $checkinfo2 =  $this->mysql->select_one("select id,name,parent_id from ".Mysite::$app->config['tablepre']."area where parent_id=".$id."  and id in(".$where.") ");
	 	     if(empty($checkinfo2)){
	 	     	  //构造返回数据
	 	     	  $areainfo = '';
	 	     	  $areaid = $id;
	 	     	  for($i=0;$i<10;$i++){
	 	     	      $getarea = $this->mysql->select_one("select id,name,parent_id from ".Mysite::$app->config['tablepre']."area where id=".$id." limit 0,1");
	 	     	      if(empty($getarea)){
	 	     	        break;
	 	     	      }
	 	     	      $areainfo = $getarea['name'].$areainfo;
	 	     	      if($getarea['parent_id']==0){
	 	     	         break;
	 	     	      }
	 	     	      $id = $getarea['parent_id'];

	 	     	  }
	 	     	  echo "<script>parent.setarea('".$areainfo."','".$areaid."');</script>";

	 	              exit;
	       }
	       $check1 =  $this->mysql->select_one("select id,name,parent_id from ".Mysite::$app->config['tablepre']."area where  id=".$id);

	       $parent_id = $check1['parent_id'];
	    }

	    $data['parent_id'] = $parent_id;
	    $data['id'] = empty($id)?'0':$id;
	    $data['where'] = $where;
	    $data['shopid'] = $shopid;
      Mysite::$app->setdata($data);
   }
   public static function checkshopopentime($is_orderbefore,$posttime,$starttime){
  	$maxnowdaytime = strtotime(date('Y-m-d',time()));
  	$daynottime = 24*60*60 -1;
  	$findpostime = false;
  	for($i=0;$i <= $is_orderbefore;$i++){
  		if($findpostime == false){
  		   $miniday = $maxnowdaytime+$daynottime*$i;
  		   $maxday = $miniday+$daynottime;
  		   $tempinfo = explode('|',$starttime);
  		   foreach($tempinfo as $key=>$value){
  		   	  if(!empty($value)){
  		   	    $temp2 = explode('-',$value);
  		   	    if(count($temp2) > 1){
  		   	    	$minbijiaotime = date('Y-m-d',$miniday);
  		   	    	$minbijiaotime = strtotime($minbijiaotime.' '.$temp2[0].':00');

  		   	    	$maxbijiaotime = date('Y-m-d',$maxday);
  		   	    	$maxbijiaotime = strtotime($maxbijiaotime.' '.$temp2[1].':00');

  		   	    	if($posttime > $minbijiaotime && $posttime < $maxbijiaotime){
  		   	    		$findpostime = true;
  		   	    		break;
  		   	    	}
  		   	    }
  		   	  }
  		   }

  	  }

  	}
    return $findpostime;
   }
	
   //快劫下单提交
   function makeorder(){
		$info['shopid'] = intval(IReq::get('shopid'));//店铺ID
		$info['remark'] = IFilter::act(IReq::get('remark'));//备注
		$info['paytype'] = 0;//默认调用货到支付
		$info['dikou'] = 0;//不计算抵扣
		$info['username'] = IFilter::act(IReq::get('contactname')); //
		$info['mobile'] = IFilter::act(IReq::get('phone'));
		$info['addressdet'] = IFilter::act(IReq::get('address'));//
		$info['senddate'] =  IFilter::act(IReq::get('senddate'));//
		 $info['minit'] = IFilter::act(IReq::get('minit')); //
		$info['juanid']  = 0;//优惠劵ID 不计算优惠券
		$info['ordertype'] = 7;//订单类型
		$info['othercontent'] = ''; 
		
		 
		$goodsdata= array();
	   	$bagsum = 0;
	   	$goodssum = 0;
	   	$goodsnum = 0;
		
		$ids = IFilter::act(IReq::get('ids'));
		if(!empty($ids)){
				if(empty($ids)) $this->message('goods_empty');
				$num = IFilter::act(IReq::get('nums'));
				if(empty($num)) $this->message('goods_count');
				$tempids = explode(',',$ids);
				$tempnum = explode(',',$num);
				if(count($tempids) != count($tempnum)) $this->message('goods_counttoid');
				$newid = array();
				$idtonum = array();
				foreach($tempids as $key=>$value){
					if(!empty($value)){
					   $check1 = intval($value);
					   $check2 = intval($tempnum[$key]);
					   if($check1 > 0 && $check2 > 0){
						   $newid[] = $value;
						   $idtonum[$value] = $check2;
					   }
				  }
			   }
			   $whereid = join(',',$newid);
			   if(empty($whereid))  $this->message('shop_emptycart');
			   $goodslist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid =".$info['shopid']." and id in(".$whereid.") ");
				foreach($goodslist as $key=>$value){
				   $value['shuliang'] = $idtonum[$value['id']];
					 $value['count'] = $idtonum[$value['id']];
					  
					 if($value['is_cx'] == 1){
					//测算促销 重新设置金额
						$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$value['id']."  ");
						$newdata = getgoodscx($value['cost'],$cxdata);
						
						$value['zhekou'] = $newdata['zhekou'];
						$value['is_cx'] = $newdata['is_cx'];
						$value['cost'] =  round( $newdata['cost'] ,2);
					}
					 
				   $goodssum += $value['cost']*intval($idtonum[$value['id']]);
				   $value['xiaoij'] = $value['cost']*intval($idtonum[$value['id']]);
				   $bagsum += $value['bagcost']*intval($idtonum[$value['id']]);
				   $value['count'] = $value['shuliang'];
				   $goodsnum += $value['shuliang'];
				   $goodsdata[] = $value;
				}
		}
		
		
		

	   

        $pids = IFilter::act(IReq::get('pids')); 
		$pnum = IFilter::act(IReq::get('pnum'));
		if(empty($ids)&&empty($pids)){
			$this->message('未选择任何商品');
		}
		if(!empty($pids)){
				$temppids = explode(',',$pids);
				$temppnum = explode(',',$pnum);
				$pnewid = array();
				$pidtonum = array();
				foreach($temppids as $key=>$value){
					if(!empty($value)){
					   $check1 = intval($value);
					   $check2 = intval($temppnum[$key]);
					   if($check1 > 0 && $check2 > 0){
						   $newid[] = $value;
						   $pidtonum[$value] = $check2;
					   }
				  }
			   }
			   $whereid = join(',',$newid);
			   if(!empty($whereid)){
				   $tempclist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."product where shopid =".$info['shopid']." and id in(".$whereid.") ");
				   foreach($tempclist as $key=>$value){
					   $dosee = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where shopid =".$info['shopid']." and id =".$value['goodsid']." ");
					   $dosee['gg'] = $value;
					   $dosee['count'] = $pidtonum[$value['id']];
					    
					 if($value['is_cx'] == 1){
					//测算促销 重新设置金额
						$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$value['id']."  ");
						$newdata = getgoodscx($value['cost'],$cxdata);
						
						$value['zhekou'] = $newdata['zhekou'];
						$value['is_cx'] = $newdata['is_cx'];
						$value['cost'] =  round( $newdata['cost'] ,2);
					}
					 
					   $goodssum += $value['cost']*intval($pidtonum[$value['id']]);
					    $bagsum += $dosee['bagcost']*intval($pidtonum[$value['id']]); 
						  $goodsnum += $dosee['count'];
						    $goodsdata[] =$dosee;
				   }
				   
			   }
			
		}
		  $shop = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id = ".$info['shopid']."   ");

		 if(empty($info['shopid'])) $this->message('shop_noexit');

		 if($shop['shoptype'] == 1){
		 	    $shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");
	   }else{
	  		 $shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");
	 	 }


	   $nowID= intval(IFilter::act(IReq::get('areaid')));//$areaid intval(ICookie::get('myaddress'));
#	  if(empty($nowID)) $this->message('area_empty');
		 if(empty($shopinfo))   $this->message('shop_noexit');
		 $checkps = 	 $this->pscost($shopinfo);
 		 $info['cattype'] = 0;//
		  if(empty($info['username'])) 		  $this->message('emptycontact');
		 if(empty($info['addressdet'])) $this->message('emptyaddress');
	   $info['userid'] = 0;
	  $info['ipaddress'] = "";
	   $ip_l=new iplocation(); 
     $ipaddress=$ip_l->getaddress($ip_l->getIP());  
     if(isset($ipaddress["area1"])){
		   $info['ipaddress']  = $ipaddress['ip'];//('GB2312','ansi',);
	   } 

	  $checkareaid = $nowID;
	  $dataareaids = array();
	  while($checkareaid > 0){

	  	 $temp_check =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id ='".$checkareaid."'   order by id desc limit 0,50");
	  	 if(empty($temp_check)){
	  	   break;
	  	 }
	  	 if(in_array($checkareaid,$dataareaids)){
	  	   break;
	  	 }
	  	 $dataareaids[] = $checkareaid;
	  	 $checkareaid = $temp_check['parent_id'];

	  }
	  $info['areaids'] = join(',',$dataareaids);
	  $info['userid'] = 0;
	  $userid = 0; 
	  
	    if($shopinfo['is_open'] != 1) $this->message('店铺暂停营业'); 
	  $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$info['minit'],$shopinfo['befortime']); 
	  if($tempdata['is_opentime'] ==  2) $this->message('选择的配送时间段，店铺未设置');
	  if($tempdata['is_opentime'] == 3) $this->message('选择的配送时间段已超时');
	   $info['sendtime'] = $tempdata['is_posttime'];
	   $info['postdate'] = $tempdata['is_postdate'];  
	   $info['addpscost'] =  $tempdata['cost'];
	   
	   $info['shopinfo'] = $shopinfo;
	   $info['allcost'] = $goodssum;
	   $info['bagcost'] = $bagsum;
	   $info['allcount'] = $goodsnum;
	   $info['shopps'] = IFilter::act(IReq::get('pscost')); ; 
	   $info['buyerlng'] = IFilter::act(IReq::get('buyerlng')); ; 
	   $info['buyerlat'] = IFilter::act(IReq::get('buyerlat')); ; 
	   $info['goodslist']   = $goodsdata;
	   $info['pstype'] = $checkps['pstype'];
	   $info['cattype'] = 0;//表示不是预订 
	 
	   $info['is_goshop']=0;
	    if($shopinfo['limitcost'] > $info['allcost']) $this->message('商品总价低于最小起送价'.$shopinfo['limitcost']);   
	   $orderclass = new orderclass();
	   $orderclass->makenormal($info);
	   $orderid = $orderclass->getorder();
	   if($userid ==  0){
	  	   ICookie::set('orderid',$orderid,86400);
	  }
	  
//	   $link = IUrl::creatUrl('site/waitpay/orderid/'.$orderid);
	  $this->success($orderid);
//		exit;
   }
   function wavecontrol(){
     $type =  IReq::get('type');
     if($type == 'closewave'){
        //关闭声音
         ICookie::set('playwave',2,2592000);
     }else{
        //开启声音
         ICookie::set('playwave',0,2592000);
     }
     $this->success('成功');
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
   function setstatus(){
		   $data['buyerstatus'] = array(
		   '0'=>'待处理订单',
		   '1'=>'待发货',
		   '2'=>'订单已发货',
		   '3'=>'订单完成',
		   '4'=>'买家取消订单',
		   '5'=>'卖家取消订单'
		   );
		   $paytypelist = array(0=>'货到支付',1=>'在线支付');
		   
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
		   '3'=>''
		   );
	    $data['paytypearr'] = $paytypelist;
	  	Mysite::$app->setdata($data);
	}


	 

	/************用户订单部分**************/
	function usersorder(){
		$this->checkmemberlogin();
	  $data['actiondo'] = 'order';
		$orderdatediff = intval(IReq::get('orderdatediff'));
		$stime = IFilter::act(IReq::get('stime'));
		$etime = IFilter::act(IReq::get('etime'));
		$status = intval(IReq::get('status'));
		$where = '';
		if($orderdatediff == 1){
			$etime = time() - 2592000;
			$stime = time() - 2592000*3;
		}else{
			$stime = empty($stime)? time() - 2592000:strtotime($stime.' 00:01');
			$etime = empty($etime)? time(): strtotime($etime.' 23:59');
		}
		if($status == 1){
			$where .= ' and status > 0 and status < 4';
		}elseif($status == 2){
			$where .= ' and status = 3 and is_ping = 1';
		}

		$oldtime = time() - 2592000;
		$where .= ' and  addtime  > '.$stime.' and addtime < '.$etime;
		//$this->setdata(array('sitetitle'=>'一个月前订单'));
		$this->setstatus();
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')),8);
		$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and is_userhide !=1 and shoptype=0 ".$where." order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		$data['list'] = array();
		foreach($list as $key=>$value){
			$value['wuliuinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderstatus where   orderid = ".$value['id']." order by addtime desc   ");
			$data['list'][] = $value;
		}
		#print_r($data['list']);
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype=0   ".$where." ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar();
		$data['pageall'] = $pageinfo->totalpage();
		$data['pagenow']  = intval(IReq::get('page')) == 0?1:intval(IReq::get('page')) ;
		$data['allcount'] = $shuliang;
		$data['nowtime'] = time();
		$data['stime'] = $stime;
		$data['etime'] = $etime;
		$data['status'] = $status;
		$data['orderdatediff'] = $orderdatediff;
		$status = empty($status)?5:$status;
		$link = IUrl::creatUrl('member/order/status/'.$status.'/stime/'.date('Y-m-d',$stime).'/etime/'.date('Y-m-d',$etime).'/orderdatediff/'.$orderdatediff.'/page/@page@');

		$data['pagelink'] = $link;
	  Mysite::$app->setdata($data);
	}
	
	
	/************跑腿订单部分**************/
	function usersptorder(){
		$this->checkmemberlogin();
	  $data['actiondo'] = 'order';
		$orderdatediff = intval(IReq::get('orderdatediff'));
		$stime = IFilter::act(IReq::get('stime'));
		$etime = IFilter::act(IReq::get('etime'));
		$status = intval(IReq::get('status'));
		$where = '';
		if($orderdatediff == 1){
			$etime = time() - 2592000;
			$stime = time() - 2592000*3;
		}else{
			$stime = empty($stime)? time() - 2592000:strtotime($stime.' 00:01');
			$etime = empty($etime)? time(): strtotime($etime.' 23:59');
		}
		if($status == 1){
			$where .= ' and status > 0 and status < 4';
		}elseif($status == 2){
			$where .= ' and status = 3 and is_ping = 1';
		}

		$oldtime = time() - 2592000;
		$where .= ' and  addtime  > '.$stime.' and addtime < '.$etime;
		//$this->setdata(array('sitetitle'=>'一个月前订单'));
		$this->setstatus();
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')),8);
		$data['list'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype=100 ".$where." order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		#print_r($data['list']);
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype=100   ".$where." ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar();
		$data['pageall'] = $pageinfo->totalpage();
		$data['pagenow']  = intval(IReq::get('page')) == 0?1:intval(IReq::get('page')) ;
		$data['allcount'] = $shuliang;
		$data['nowtime'] = time();
		$data['stime'] = $stime;
		$data['etime'] = $etime;
		$data['status'] = $status;
		$data['orderdatediff'] = $orderdatediff;
		$status = empty($status)?5:$status;
		$link = IUrl::creatUrl('member/order/status/'.$status.'/stime/'.date('Y-m-d',$stime).'/etime/'.date('Y-m-d',$etime).'/orderdatediff/'.$orderdatediff.'/page/@page@');
		$data['actiondo'] = 'usersptorder';
		$data['pagelink'] = $link;
	  Mysite::$app->setdata($data);
	}
	
	
	function userorderdet(){
		$this->checkmemberlogin();
     	$orderid = intval(IReq::get('orderid'));
		
		if(empty($orderid)) $this->message('order_noexit');
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
		if(empty($orderinfo)) $this->message('order_noexit');
		
		if($orderinfo['paytype'] == 1 && $orderinfo['paystatus'] == 0 && $orderinfo['status'] < 3){
			$checktime = time() - $orderinfo['addtime'];
			if($checktime > 900){
				//说明该订单可以关闭
				$cdata['status'] = 4;
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
			    $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '".$orderid."' and status != 3 ");
				/*更新订单 状态说明*/
				$statusdata['orderid']     =  $orderid;
				$statusdata['addtime']     =  $orderinfo['addtime']+900;
				$statusdata['statustitle'] =  "自动关闭订单";
				$statusdata['ststusdesc']  =  "在线支付订单，未支付自动关闭"; 		
				$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata); 
				$orderinfo['status'] = 4;
				
			} 
		}
		
		
		
		
		$orderinfo['addtime'] = date('Y-m-d H:i:s',$orderinfo['addtime']);
		$orderinfo['posttime'] =  date('Y-m-d H:i:s',$orderinfo['posttime']);
		$orderinfo['suretime'] = $orderinfo['suretime'] < 1?'--': date('Y-m-d H:i:s',$orderinfo['suretime']);
		$orderinfo['pscost'] = $orderinfo['shopps'] ;
		$orderinfo['goodscost'] = $orderinfo['shopcost'];
		$orderinfo['excontent'] = $orderinfo['content'];
	#	$statusarray = array('0'=>'预定中','1'=>'已预定','2'=>'配送','3'=>'完成','4'=>'取消','5'=>'取消');
	#	$orderinfo['status'] = $statusarray[$orderinfo['status']];
		$orderinfo['status'] =  $orderinfo['status'];
		if(!empty($orderinfo['othertext'])){
			$tempinfo = unserialize($orderinfo['othertext']);
			$orderinfo['excontent'].=',其他要求：';
			foreach($tempinfo as $key=>$value){
				$orderinfo['excontent'] .= $key.':'.$value.',';
			}
		}
		$orderdetinfo = $this->mysql->getarr("select *,goodscount*goodscost as sum from ".Mysite::$app->config['tablepre']."orderdet    where  order_id='".$orderid."'  ");
		$backinfo['order'] = $orderinfo;
		
		$tempwuliu = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderstatus where   orderid = ".$orderid." order by addtime asc   ");
		$orderwuliustatus = array();
		foreach($tempwuliu as  $key=>$value){
			$value['addtime'] = date('m-d H:i',$value['addtime'] );
			$orderwuliustatus[] = $value;
		}
		$backinfo['orderwuliustatus'] = $orderwuliustatus;
		
		$backinfo['orderdet'] = $orderdetinfo;
	 
		$this->success($backinfo);
	}

	function usermorder(){
		$this->checkmemberlogin();
		$orderdatediff = intval(IReq::get('orderdatediff'));
		$stime = IFilter::act(IReq::get('stime'));
		$etime = IFilter::act(IReq::get('etime'));
		$status = intval(IReq::get('status'));
		$where = '';
		if($orderdatediff == 1){
			$etime = time() - 2592000;
			$stime = time() - 2592000*3;
		}else{
			$stime = empty($stime)? time() - 2592000:strtotime($stime.' 00:01');
			$etime = empty($etime)? time(): strtotime($etime.' 23:59');
		}
		if($status == 1){
			$where .= ' and status > 0 and status < 4';
		}elseif($status == 2){
			$where .= ' and status = 3 and is_ping = 1';
		}

		$oldtime = time() - 2592000;
		$where .= ' and  addtime  > '.$stime.' and addtime < '.$etime;
		//$this->setdata(array('sitetitle'=>'一个月前订单'));
		$pageinfo = new page();
		$this->setstatus();
		$pageinfo->setpage(intval(IReq::get('page')),8);
		$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype=1 ".$where." order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		$data['list'] = array();
		foreach($list as $key=>$value){
			$value['wuliuinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderstatus where   orderid = ".$value['id']." order by addtime desc   ");
			$data['list'][] = $value;
		}
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and  shoptype=1   ".$where." ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar();
		$data['pageall'] = $pageinfo->totalpage();
		$data['pagenow']  = intval(IReq::get('page')) == 0?1:intval(IReq::get('page')) ;
		$data['allcount'] = $shuliang;
		$data['nowtime'] = time();
		$data['stime'] = $stime;
		$data['etime'] = $etime;
		$data['status'] = $status;
		$data['orderdatediff'] = $orderdatediff;
			$status = empty($status)?5:$status;
		$link = IUrl::creatUrl('order/usermorder/status/'.$status.'/stime/'.date('Y-m-d',$stime).'/etime/'.date('Y-m-d',$etime).'/orderdatediff/'.$orderdatediff.'/page/@page@');
	  $data['actiondo'] = 'ordermarket';
		$data['pagelink'] = $link;
		 Mysite::$app->setdata($data);

	}
	function userunorder(){
		$this->checkmemberlogin();
		$orderid = intval(IReq::get('orderid')); 
		$userctlord = new userctlord($orderid,$this->member['uid'],$this->mysql);
		if($userctlord->unorder() == false){
			$this->message($userctlord->Error());
		}else{
			$this->success('success');
		}   
	}
	
	function acceptorder(){
		$this->checkmemberlogin();
		$orderid = intval(IReq::get('orderid'));
		$userctlord = new userctlord($orderid,$this->member['uid'],$this->mysql);
		if($userctlord->sureorder() == false){
			$this->message($userctlord->Error());
		}else{
			$this->success('success');
		}  
	}
	function userdelorder(){  // 用户删除订单
		$this->checkmemberlogin();
		$orderid = intval(IReq::get('orderid'));
		$userctlord = new userctlord($orderid,$this->member['uid'],$this->mysql);
		if($userctlord->delorder() == false){
			$this->message($userctlord->Error());
		}else{
			$this->success('success');
		}   
		 
	}
	
 
	function waitpiont()
	{
		$this->checkmemberlogin();
		$this->setstatus();
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')));
			$showtime = time()-7*24*60*60;
		$where = ' and   (status = 3 or status =2) and is_ping = 0 and posttime >'.$showtime;
		$data['list'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."'  ".$where." order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' ".$where." ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar();
		$data['pageall'] = $pageinfo->totalpage();
		$data['pagenow']  = intval(IReq::get('page')) == 0?1:intval(IReq::get('page')) ;
		$data['allcount'] = $shuliang;
		 Mysite::$app->setdata($data);
	}
	//我已点评订单
	function overpiont(){
		//id	orderid	orderdetid	shopid	goodsid	uid	content	addtime	replycontent	replytime	point 评分	is_show
		$this->checkmemberlogin();
		$this->setstatus();
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')));
			$showtime = time()-7*24*60*60;
		if(empty($this->member['uid'])) $this->message('member_nologin');
		$where = ' and   status = 3 and is_ping = 1 and posttime >'.$showtime;
		$data['list'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."'  ".$where." order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' ".$where." ");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar();
		$data['pageall'] = $pageinfo->totalpage();
		$data['pagenow']  = intval(IReq::get('page')) == 0?1:intval(IReq::get('page')) ;
		$data['allcount'] = $shuliang;
		 Mysite::$app->setdata($data);
	}


	 function ordercomdet(){
	 	$this->checkmemberlogin();
		$orderid = intval(IReq::get('orderid'));
		if(empty($orderid)) $this->message('order_noexit');
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' and buyeruid = '".$this->member['uid']."' ");
		if(empty($orderinfo)) $this->message('order_noexit');
		if(!in_array($orderinfo['status'],array(2,3))) $this->message('empty_ping');
		$orderinfo['addtime'] = date('Y-m-d H:i:s',$orderinfo['addtime']);
		$orderinfo['posttime'] =  date('Y-m-d H:i:s',$orderinfo['posttime']);
		$orderinfo['suretime'] = $orderinfo['suretime'] < 1?'--': date('Y-m-d H:i:s',$orderinfo['suretime']);
		$orderinfo['pscost'] = $orderinfo['shopps'];
		$orderinfo['goodscost'] = $orderinfo['shopcost'];
		$orderinfo['excontent'] = $orderinfo['content'];
		$statusarray = array('0'=>'预定中','1'=>'已预定','2'=>'配送','3'=>'完成','4'=>'取消','5'=>'取消');
		$orderinfo['status'] = $statusarray[$orderinfo['status']];
		if(!empty($orderinfo['othertext'])){
			$tempinfo = unserialize($orderinfo['othertext']);
			$orderinfo['excontent'].=',其他要求：';
			foreach($tempinfo as $key=>$value){
				$orderinfo['excontent'] .= $key.':'.$value.',';
			}
		}
		$orderdetinfo = $this->mysql->getarr("select *,goodscount*goodscost as sum from ".Mysite::$app->config['tablepre']."orderdet    where  order_id='".$orderid."'  ");
		$temparray = array();
		foreach($orderdetinfo as $key=>$value){
			   $value['comment'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."comment where orderid='".$orderid."' and orderdetid = '".$value['id']."' ");
			   $temparray[] = $value;
		}
		$backinfo['order'] = $orderinfo;
		$backinfo['orderdet'] = $temparray;
//		$this->success($backinfo);// $this->json(array('error'=>false,'message'=>$));
		 Mysite::$app->setdata($backinfo);
	}
	function saveping(){
		$this->checkmemberlogin();
		$orderdetid = intval(IReq::get('orderdetid'));
	  $point = intval(IReq::get('point'));
	  $pointcontent = trim(IFilter::act(IReq::get('pointcontent')));
	  $data['point'] = in_array($point,array(1,2,3,4,5))?$point:5;
	  $orderdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderdet where id='".$orderdetid."'  ");
	  if(empty($orderdet)) $this->message('order_noexit');
	  if($orderdet['status'] == 1) $this->message('order_isping');
	  $orderinfo  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderdet['order_id']."' and buyeruid = '".$this->member['uid']."'  and (status = 2 or status = 3) ");//
	  if(empty($orderinfo))$this->message('order_cantping');
	  if($orderinfo['is_ping'] == 1) $this->message('order_isping');
	  if($orderinfo['status'] == 2){//更新订单标志
	  	$umdata['status'] = 3;
	  	$umdata['suretime'] = time();
	  	$this->mysql->update(Mysite::$app->config['tablepre'].'order',$umdata,"id='".$orderinfo['id']."'");
	  	//更新帐号成长值
	  	if(!empty($orderinfo['buyeruid']))
	    {
	   	      	   $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
		             if(!empty($memberinfo)){
		             	 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`total`=`total`+'.$orderinfo['allcost'],"uid ='".$orderinfo['buyeruid']."' ");
		              }
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
	  }

	  $data['orderid'] = $orderinfo['id'];
	  $data['orderdetid'] = $orderdetid;
	  $data['shopid'] = $orderinfo['shopid'];
	  $data['goodsid'] = $orderdet['goodsid'];
	  $data['uid'] = $this->member['uid'];
	  $data['content'] = $pointcontent;
	  $data['point'] = $point;
	  $data['addtime'] = time();
	  //更新订单详情表数据
	  $udata['status'] = 1;
	  $this->mysql->update(Mysite::$app->config['tablepre'].'orderdet',$udata,"id='".$orderdetid."'");
	  //将评数据写到 数据表中/
	  $this->mysql->insert(Mysite::$app->config['tablepre'].'comment',$data);
	  /*写日志*/
	  $issong = 1;
	  if(intval(Mysite::$app->config['commentday']) > 0){//检测是否赠送积分
	     	   $uptime = Mysite::$app->config['commentday']*24*60*60;
	     	   $uptime = $orderinfo['addtime'] +$uptime;
	     	   if($uptime > time()){
	     	      $issong = 0;
	     	   }else{
	     	      $issong = 1;
	     	   }
	  }else{
              $issong = 1;
          }
	  $fscoreadd = 0;
	  if(intval(Mysite::$app->config['commenttype']) > 0 && $issong == 1)
	  { //赠送积分 大于0赠送积分到用户帐号  赠送基础积分
	    $scoreadd = Mysite::$app->config['commenttype'];
	    $checktime = date('Y-m-d',time());
	    $checktime = strtotime($checktime);
	    $checklog = $this->mysql->select_one("select sum(result) as jieguo from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and   userid = ".$this->member['uid']." and addtype =1 and  addtime > ".$checktime);
	    if(Mysite::$app->config['maxdayscore'] > 0){
	    	$checkguo = $checklog['jieguo']+$scoreadd;
	    	if($checkguo < Mysite::$app->config['maxdayscore']){
	    		//最大值小于当前和
	    	}elseif(Mysite::$app->config['maxdayscore'] > $checklog['jieguo']){
	    		//最大指 大于 已增指
	    		$scoreadd = Mysite::$app->config['maxdayscore'] - $checklog['jieguo'];
	    	}else{
	    		$scoreadd = 0;
	    	}
	    }
            $maxdayscore = Mysite::$app->config['maxdayscore'];
	    if($scoreadd > 0 && $maxdayscore > 0){
                $scoreadd = $maxdayscore>$scoreadd?$scoreadd:$maxdayscore;
	  	   $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$scoreadd,"uid='".$this->member['uid']."'");
	  	   $fscoreadd =$scoreadd;
         $memberallcost = $this->member['score']+$scoreadd;
         $this->memberCls->addlog($this->member['uid'],1,1,$scoreadd,'评价商品','评价商品'.$orderdet['goodsname'].'获得'.$scoreadd.'积分',$memberallcost);
      }
	  }
	  // 查询子订单是否所有的状态都为 1，  是的话更新订单标志
	  $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."' and status = 0");
	  if($shuliang < 1)//订单已评价完毕
	  {
		  
		  
		  
		  
			$this->mysql->update(Mysite::$app->config['tablepre'].'order','`is_ping`=1',"id='".$orderinfo['id']."'");
		  
			$ordCls = new orderclass();
			$ordCls->writewuliustatus($orderinfo['id'],11,$orderinfo['paytype']);  // 用户已评价订单，完成订单
		 

	     if(intval(Mysite::$app->config['commentscore']) > 0 && $issong ==  1){//扩张积分 大于0
	     	   $scoreadd = intval(Mysite::$app->config['commentscore'])*$orderinfo['allcost'];
	     	   $checktime = date('Y-m-d',time());
	         $checktime = strtotime($checktime);
	         $checklog = $this->mysql->select_one("select sum(result) as jieguo from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and   userid = ".$this->member['uid']." and addtype =1 and  addtime > ".$checktime);
	         if(Mysite::$app->config['maxdayscore'] > 0){
	    	       $checkguo = $checklog['jieguo']+$scoreadd;
	    	       if($checkguo < Mysite::$app->config['maxdayscore']){
	    	           	//最大值小于当前和
	    	       }elseif(Mysite::$app->config['maxdayscore'] > $checklog['jieguo']){
	    		        //最大指 大于 已增指
	    		      $scoreadd = Mysite::$app->config['maxdayscore'] - $checklog['jieguo'];
	    	       }else{
	    	         	$scoreadd = 0;
	    	       }
	         }
                 $maxdayscore = Mysite::$app->config['maxdayscore'];
	         if($scoreadd > 0 && $maxdayscore > 0){
                     $scoreadd = $maxdayscore>$scoreadd?$scoreadd:$maxdayscore;
	         	   $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$scoreadd,"uid='".$this->member['uid']."'");
	  	         $memberallcost = $this->member['score']+$scoreadd+$fscoreadd;
               $this->memberCls->addlog($this->member['uid'],1,1,$scoreadd,'评价完订单','评价完订单'.$orderinfo['dno'].'奖励，'.$scoreadd.'积分',$memberallcost);
	         }
	     }
	  }
	  //店铺平分
	  $newpoint['point'] = 5;
	  $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."comment where shopid='".$orderinfo['shopid']."' ");
	  $scorall = $this->mysql->select_one("select sum(point) as allpoint from ".Mysite::$app->config['tablepre']."comment where shopid='".$orderinfo['shopid']."' ");
	  if($shuliang > 0)
	  {
	  	$newpoint['point'] = intval($scorall['allpoint']/$shuliang);
	  }
	  $newpoint['pointcount'] = $shuliang;
	  $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$newpoint,"id='".$orderinfo['shopid']."'");
	  //商品评分
	  $newpoint['point'] = 5;
	  $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."comment where goodsid='".$orderdet['goodsid']."' ");
	  $scorall = $this->mysql->select_one("select sum(point) as allpoint from ".Mysite::$app->config['tablepre']."comment where goodsid='".$orderdet['goodsid']."' ");
	  if($shuliang > 0)
	  {
	  	$newpoint['point'] = intval($scorall['allpoint']/$shuliang);
	  }
	  $newpoint['pointcount'] = $shuliang;
	  //pointcount `$key`
	  $this->mysql->update(Mysite::$app->config['tablepre'].'goods',$newpoint,"id='".$orderdet['goodsid']."'");
	  $this->success('success');
	}
	function yijianping(){
		$orderid = intval( IFilter::act(IReq::get('orderid')) );
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");  
		if($orderinfo['is_ping'] == 1) $this->message('order_isping');		
	    if(empty($orderinfo)) $this->message('获取此订单失败'); 
		$orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."' and is_send = 0 ");  
		$data['orderid'] =   $orderinfo['id']; 
		 
		$data['shopid'] = $orderinfo['shopid'];
		if(empty($this->member['uid'])) $this->message('获取用户失败');
		$data['uid'] = $this->member['uid'];
		$data['addtime'] = time();
		$data['is_show'] = 0;
		$shoppointnum =  trim( IFilter::act(IReq::get('shoppointnum')) );
		$shopsudupointnum =  intval( IFilter::act(IReq::get('shopsudupointnum')) ); 
		
		if(empty($shoppointnum)) $this->message('请评论总体评价');
		if(empty($shopsudupointnum)) $this->message('请评论配送服务');
		$memberscore = $this->member['score'];
	    foreach($orderdet as $key=>$value){
			$data['point'] = intval( IFilter::act(IReq::get('goodsid_'.$value['id'])) );
			$data['content'] =  trim( IFilter::act(IReq::get('content_'.$value['id'])) );
			$data['orderdetid'] = $value['id'];
			$data['goodsid'] =   $value['goodsid'];
			if(!empty($data['point']) || !empty($data['content']) ){
				#print_r($data);exit;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'comment',$data);
				$udata['status'] = 1;
				$this->mysql->update(Mysite::$app->config['tablepre'].'orderdet',$udata,"id='".$value['id']."'");
				
				
						  //商品评分
				  $goodinfo  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where id='".$value['goodsid']."'  "); 
				  $goodpointcount = $goodinfo['pointcount']; 
				  $goodnewpoint['point'] = intval($goodinfo['point']+$data['point']);
				  $goodnewpoint['pointcount'] = intval($goodpointcount+1);
				   $this->mysql->update(Mysite::$app->config['tablepre'].'goods',$goodnewpoint,"id='".$value['goodsid']."'");
					
				
				
				 /*写日志*/
				  $issong = 1;
				  if(intval(Mysite::$app->config['commentday']) > 0){//检测是否赠送积分
						   $uptime = Mysite::$app->config['commentday']*24*60*60;
						   $uptime = $orderinfo['addtime'] +$uptime;
						   if($uptime < time()){
							  $issong = 0;
						   }else{
							  $issong = 1;
						   }
				  }else{  //0时一直有效
					  $issong = 1;
				  }
				  $fscoreadd = 0;
				  
				 if(intval(Mysite::$app->config['commenttype']) > 0 && $issong == 1)
				  { //赠送积分 大于0赠送积分到用户帐号  赠送基础积分
					$scoreadd = Mysite::$app->config['commenttype'];
					$checktime = date('Y-m-d',time());
					$checktime = strtotime($checktime);
					$checklog = $this->mysql->select_one("select sum(result) as jieguo from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and   userid = ".$this->member['uid']." and addtype =1 and  addtime >= ".$checktime);
						if(Mysite::$app->config['maxdayscore'] > 0){
							$checkguo = $checklog['jieguo']+$scoreadd;
							if($checkguo < Mysite::$app->config['maxdayscore']){
								//最大值小于当前和
							}elseif(Mysite::$app->config['maxdayscore'] > $checklog['jieguo']){
								//最大指 大于 已增指
								$scoreadd = Mysite::$app->config['maxdayscore'] - $checklog['jieguo'];
							}else{
								$scoreadd = 0;
							}
						}
						$maxdayscore = Mysite::$app->config['maxdayscore'];
						#print_r($scoreadd);
						if($scoreadd > 0){
							if($maxdayscore > 0){
								$scoreadd = $maxdayscore>$scoreadd?$scoreadd:$maxdayscore;
							}
						   $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$scoreadd,"uid='".$this->member['uid']."'");
						   $fscoreadd =$scoreadd;
						 $memberscore = $memberscore+$scoreadd;
						$this->memberCls->addlog($this->member['uid'],1,1,$scoreadd,'评价商品','评价商品'.$orderdet['goodsname'].'获得'.$scoreadd.'积分',$memberscore);
					   }
				  }

			}
		}
	
		    $ordata['point'] = $shoppointnum;
			$ordata['is_ping'] = 1;
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$ordata,"id='".$orderinfo['id']."'");
		   
		

		// 查询子订单是否所有的状态都为 1，  是的话更新订单标志
	  $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."' and status = 0");
	  if($shuliang < 1)//订单已评价完毕
	  { 
	     if(intval(Mysite::$app->config['commentscore']) > 0 && $issong == 1){//扩张积分 大于0
		 
				   $scoreadd = intval(intval(Mysite::$app->config['commentscore'])*$orderinfo['allcost']);
				   $checktime = date('Y-m-d',time());
				 $checktime = strtotime($checktime);
				 $checklog = $this->mysql->select_one("select sum(result) as jieguo from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and   userid = ".$this->member['uid']." and addtype =1 and  addtime > ".$checktime);
				 if(Mysite::$app->config['maxdayscore'] > 0){
					   $checkguo = $checklog['jieguo']+$scoreadd;
					   if($checkguo < Mysite::$app->config['maxdayscore']){
							//最大值小于当前和
					   }elseif(Mysite::$app->config['maxdayscore'] > $checklog['jieguo']){
							//最大指 大于 已增指
						  $scoreadd = Mysite::$app->config['maxdayscore'] - $checklog['jieguo'];
					   }else{
							$scoreadd = 0;
					   }
				 }
                                 $maxdayscore = Mysite::$app->config['maxdayscore'];            
				 if($scoreadd > 0 && $maxdayscore > 0){
                                     $scoreadd = $maxdayscore>$scoreadd?$scoreadd:$maxdayscore;
					
					   $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$scoreadd,"uid='".$this->member['uid']."'");
					 $memberallcost = $this->member['score']+$scoreadd+$fscoreadd;
				   $this->memberCls->addlog($this->member['uid'],1,1,$scoreadd,'评价完订单','评价完订单'.$orderinfo['dno'].'奖励，'.$scoreadd.'积分',$memberallcost);
				 }
			 }
		  }
		  $shopinfo  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$orderinfo['shopid']."' ");
		  $shuliangx = $shopinfo['point'];
		  $pointcount = $shopinfo['pointcount'];
		  $psservicepoint = $shopinfo['psservicepoint'];
		  $psservicepointcount = $shopinfo['psservicepointcount'];
	
		  $newpoint['point'] = intval($shoppointnum+$shuliangx);
		  $newpoint['pointcount'] = intval($pointcount+1);
		  $newpoint['psservicepoint'] = intval($psservicepoint+$shopsudupointnum);
		  $newpoint['psservicepointcount'] = intval($psservicepointcount+1);
		  
		   $tjshop  = $this->mysql->select_one("select sum(goodscount) as sellcount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderinfo['id']."'  ");
			if(!empty($tjshop) && $tjshop['sellcount'] > 0){
				 $newpoint['sellcount'] = $shopinfo['sellcount']+$tjshop['sellcount']; 
			}
	
		  $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$newpoint,"id='".$orderinfo['shopid']."'");
		$this->mysql->update(Mysite::$app->config['tablepre'].'orderps','`status`=3',"orderid='".$orderinfo['id']."'");
		$psbinterface = new psbinterface();
		$psbinterface->pingpsb($orderinfo['id'],$shopsudupointnum,'');
		$this->success('success');
		
	} 
  function guestorderlist(){
  	   $this->setstatus();
  	    $phone = IFilter::act(IReq::get('phone'));
  	    $link = IUrl::creatUrl('order/guestorder');
  	    $Captcha = IFilter::act(IReq::get('Captcha'));
  	     $type = IFilter::act(IReq::get('type'));
		    if(Mysite::$app->config['allowedcode'] == 1)
		    {
		 	     if($Captcha != ICookie::get('Captcha')) 	$this->message('member_codeerr',$link);
		    }
		    if(!(IValidate::suremobi($phone)))$this->message('errphone');
		    $data['phone'] = $phone;
		    $data['Captcha'] = $Captcha;
		    $data['where'] = ' buyerphone = \''.$phone.'\'';
			if(empty($type)){
				$data['where'] .=  ' and shoptype=0';
			}else if($type == 1){
				$data['where'] .=  ' and shoptype=1';
			}else if($type == 100){
				$data['where'] .=  ' and shoptype=100';
			}
		
		    $data['type'] = $type;
  	    Mysite::$app->setdata($data);
  }
   
  function commentshop(){
    $shopid = intval(IFilter::act(IReq::get('shopid')));
    $type = trim(IFilter::act(IReq::get('type')));
    $data['list'] = array();
    if($type == 'shop'){
       	$this->pageCls->setpage(intval(IReq::get('page')),5);
                 $list = $this->mysql->getarr("select a.*,b.username,b.logo,c.name from ".Mysite::$app->config['tablepre']."comment as a left join  ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid left join ".Mysite::$app->config['tablepre']."goods as c on a.goodsid = c.id  where a.shopid=".$shopid." and a.is_show  =0 order by a.id desc   limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."");
				$data['list'] = array();
				foreach($list as $key=>$value){
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
			 $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."comment   where shopid=".$shopid."  and is_show  =0 ");
                  $this->pageCls->setnum($shuliang);
              $data['pagecontent'] = $this->pageCls->ajaxbar('getPingjia');
    }
    Mysite::$app->setdata($data);
  }
   function drawuserorder(){
		 
		$orderid = intval(IReq::get('orderid'));  
		if(!empty($orderid)){ 
	       	$order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");   
	        $data['order'] = $order;
			if($order['is_reback'] > 0){
				$drawbacklog =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid='".$order['id']."'  ");   
				 $data['drawbacklog'] = $drawbacklog;
			}   
	        Mysite::$app->setdata($data); 
		}else{
			$data['order'] = '';
			Mysite::$app->setdata($data);
		}
	}
	function savedrawbacklog(){
		if(empty($this->member['uid'])){
			$this->message('member_nologin');
		}
	 	 
		$drawbacklog = new drawbacklog($this->mysql,$this->memberCls);
		 
		$check = $drawbacklog->save();
		if($check == true){
			
			$this->success('success');  
		}else{
			 $msg = $drawbacklog->GetErr();
			$this->message($msg);
		} 
	}

}?>