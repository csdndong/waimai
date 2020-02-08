<?php
class method   extends baseclass
{  
	  
	 function index(){ 
			$weekji = date('w');
	 	    $desk = intval(IFilter::act(IReq::get('desk')));
	 	     $desk = in_array($desk,array(0,1,2,3,4))? $desk: 0;
	 	    $data['desk'] = $desk;
	 	    $areaids = intval(IFilter::act(IReq::get('areaids')));
	 	    $data['areaids'] = $areaids; 
	 	     $areaid = intval(IFilter::act(IReq::get('areaid')));
	 	    $data['areaid'] = $areaid;
	 	    
	 	    $locationtype = Mysite::$app->config['locationtype']; 
	      $data['goodstypedoid'] = array();
	      $attrshop = array();
		    $data['attrinfo'] = array(); 
		      $where = ' where is_goshop = 1 ';  
		    $tempwhere = array(); 
        $templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_search =1  order by orderid asc limit 0,1000");
		    foreach($templist as $key=>$value){
	     	  $value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id = ".$value['id']." order by orderid asc  limit 0,20"); 
	     	  $value['is_now'] = isset($seardata[$value['id']])?$seardata[$value['id']]:0; 
	     	  $data['attrinfo'][] = $value;
	     	  // print_r($value['id']);
	     	  $doid= intval(IFilter::act(IReq::get('goodstype_'.$value['id']))); 
	     	  if($doid > 0){
	     	     $data['goodstypedoid'][$value['id']] = $doid;
	     	     
	     	      $tempwhere[] = $doid;
	     	    
	     	  }
	     	  
	 	    }
	 	    //personcount
	 	   
	 	    $personarr = array(
	 	    '0'=>'',
	 	    '1'=>' and a.personcount > 0 and a.personcount < 5 ',
	 	    '2'=>' and a.personcount > 4 and a.personcount < 9 ',
	 	    '3'=>' and a.personcount > 8 and a.personcount < 13 ',
	 	    '4'=>' and a.personcount > 12' 
	 	    );
	 	    $where .= $personarr[$desk];
	 	    if(count($tempwhere) > 0){
	 	    	  $where .= " and a.shopid in (select shopid from ".Mysite::$app->config['tablepre']."shopsearch where second_id in(".join($tempwhere).")  ) ";
	 	    }
	 	    if($areaids > 0){
	 	    	if($areaid > 0){
	 	    		$where .= " and a.shopid in (select shopid from ".Mysite::$app->config['tablepre']."areashop where areaid = ".$areaid." )";  
	 	    	}else{
	 	        $where .=" and a.shopid in (select shopid from ".Mysite::$app->config['tablepre']."areashop where areaid = ".$areaids." )";
	 	      }
	 	    }
	 	    // // shopid	parent_id	second_id	cattype 
	 	    //$this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopsearch   order by id asc limit 0,1000");
	 	         //获取搜索属性性结束 
	 	         //获取展示属性
	 	        $data['searchgoodstype'] =  $templist;
	 	       //print_r($data['attrinfo']);
	 	       // print_r($data['searchgoodstype']);
		         $data['mainattr'] = array(); 
             $templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_main =1 and type!='input' order by orderid asc limit 0,1000");
              //print_r($templist);
		         foreach($templist as $key=>$value){
	          	 $value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id = ".$value['id']." order by orderid asc  limit 0,20");  
	          	 $data['mainattr'][] = $value;
	 	         }  
	 	    $data['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id = 0  order by id asc limit 0,1000");
	 	      
	 	    $data['areadet'] = array();
	 	    if($areaids > 0){ 
	 	        $data['areadet'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id = ".$areaids." order by id asc limit 0,1000");
	 	      
	 	    }
	 	    
	 	    
	 	    
	 	    
	      $shopsearch = IFilter::act(IReq::get('shopsearch'));
		    $data['shopsearch'] = $shopsearch; 
		   // $where = empty($where)?' where is_waimai = 1':$where.' and is_waimai=1';
		   
		    $where .= "  and b.admin_id = '".$this->CITY_ID."'  ";
		   
		    $list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id ".$where."    order by sort asc limit 0,100");  
		    $nowhour = date('H:i:s',time()); 
		    $nowhour = strtotime($nowhour);
		    $templist = array();
		    if(is_array($list)){//转换数据
		       foreach($list as $key=>$value){ 
		           	if($value['id'] > 0){
		        	     $checkinfo = $this->shopIsopen($value['is_open'],$value['starttime'],$value['is_orderbefore'],$nowhour); 
		        	     $value['opentype'] = $checkinfo['opentype'];
		        	     $value['newstartime']  =  $checkinfo['newstartime'];  
		        	     
		        	      $ps  = $this->pscost($value);
		        	     $value['pscost'] = $ps['pscost'];
		        	     
		        	    //每个店铺属性 
		        	     $value['attrdet'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  cattype = 0 and shopid = '".$value['id']."' ");//每个商品的属性值
		        	     $tempinfo = array();
		        	     foreach($value['attrdet'] as $keys=>$valx){
		        	    	  $tempinfo[] = $valx['attrid'];
		        	     } 
		        	     $value['servertype'] = join(',',$tempinfo); 
		         	     $templist[] = $value;
		             }
		       } 
	      } 
		    $data['shoplist'] = $templist;   
        Mysite::$app->setdata($data); 
	 	 
	 }
	 
	 function show(){
	  $shop = trim(IFilter::act(IReq::get('id')));
	  $weekji = date('w');
		$where = intval($shop) > 0?' where a.shopid = '.$shop:'where shortname=\''.$shop.'\'';
		//获取外卖店铺
		$shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id ".$where."   ");
    	if(empty($shopinfo)){
			$link = IUrl::creatUrl('site/index');
		  $this->message('获取店铺信息失败',$link);
		}
		//年费检测
		if($shopinfo['endtime'] < time()){
			$link = IUrl::creatUrl('site/index');
		   $this->message('店铺已关门',$link);
		}

		$nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($shopinfo['postdate'])?unserialize($shopinfo['postdate']):array();
		$data['pstimelist'] = array();
		$checknow = time();
		$whilestatic = $shopinfo['befortime'];
		$nowwhiltcheck = 0;
		while($whilestatic >= $nowwhiltcheck){
		    $startwhil = $nowwhiltcheck*86400;
			foreach($timelist as $key=>$value){
				$stime = $startwhil+$nowhout+$value['s'];
				$etime = $startwhil+$nowhout+$value['e'];
				if($stime  > $checknow){
					$tempt = array();
					$tempt['value'] = $value['s']+$startwhil;
					$tempt['s'] = date('H:i',$nowhout+$value['s']);
					$tempt['e'] = date('H:i',$nowhout+$value['e']);
					$tempt['d'] = date('Y-m-d',$stime);
					$tempt['i'] =  $value['i'];
					$data['pstimelist'][] = $tempt;
				}
			}
		 
			$nowwhiltcheck = $nowwhiltcheck+1;
		}
		
		
		
		$nowhour = date('H:i:s',time());
	  $nowhour = strtotime($nowhour);
	  $data['shopinfo'] = $shopinfo;
	  
	#  print_r( $data['shopinfo'] );
	  
		$data['shopopeninfo'] = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopinfo['is_orderbefore'],$nowhour);


		//获取所有外卖分类及商品
		$data['com_goods'] =  $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."goods where shopid = ".$shopinfo['id']." and is_com = 1   ");
		$goodstype=  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodstype where shopid = ".$shopinfo['id']." and cattype = ".$shopinfo['shoptype']." order by orderid asc");
		$data['goodstype'] = array();
		$tempids = array();
		foreach($goodstype as $key=>$value){
			// $value['det'] = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."goods where typeid = ".$value['id']." and is_dingtai = 1 and    FIND_IN_SET( ".$weekji." , `weeks` )   and shopid=".$shopinfo['id']."  ");
			  $detaa = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."goods where typeid = ".$value['id']." and is_dingtai = 1 and    FIND_IN_SET( ".$weekji." , `weeks` )   and shopid=".$shopinfo['id']."  ");
			 
			 
			 foreach ( $detaa as $keyq=>$valq ){
				 
				   /* 商品星级计算 */
				 $zongpoint = $valq['point'];
								$zongpointcount = $valq['pointcount'];
								if($zongpointcount != 0 ){
									$shopstart = intval( round($zongpoint/$zongpointcount) );
								}else{
									$shopstart= 0;
								}
									$valq['point'] = 	$shopstart;	
				 
				 
				if($valq['is_cx'] == 1){
				//测算促销 重新设置金额
					$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
					$newdata = getgoodscx($valq['cost'],$cxdata);
					
					$valq['zhekou'] = $newdata['zhekou'];
					$valq['is_cx'] = $newdata['is_cx'];
					$valq['cost'] = $newdata['cost'];
				}
				$value['det'][] =$valq; 
			} 
			 
			 
			 $tempids[] = $value['id'];
			$data['goodstype'][] =$value;
		}
		//获取主属性

	  $data['mainattr'] = array();
     $templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = ".$shopinfo['shoptype']." and parent_id = 0 and is_main =1  order by orderid asc limit 0,1000");
		 foreach($templist as $key=>$value){
	  	 $value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id = ".$value['id']." order by orderid asc  limit 0,20");
	  	 $data['mainattr'][] = $value;
	 	 }
	 	 
	  //获取店铺主属性
		$data['shopattr'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr  where  cattype = ".$shopinfo['shoptype']." and shopid = '".$shopinfo['id']."'  order by firstattr asc limit 0,1000");

		//获取店铺商品属性
		 $data['goodsattr'] = array();
		 $goodsattr = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign  where  type = 'goods'  order by id asc limit 0,1000");

		 foreach($goodsattr as $key=>$value){
		   $data['goodsattr'][$value['id']] = $value['imgurl'];

		 }

	   $data['psinfo'] = 	 $this->pscost($shopinfo);
	    $sellrule =new sellrule();
		    $sellrule->setdata($shopinfo['shopid'],1000,$shopinfo['shoptype']);
		    $ruleinfo = $sellrule->getdata();
		 $data['ruledata'] = array();
		 if(isset($ruleinfo['cxids']) && !empty($ruleinfo['cxids'])){
		 	   //获取所有规则数据
		 	 $data['ruledata'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."rule  where  id in(".$ruleinfo['cxids'].")  order by id asc limit 0,1000");


		 }
		 $cximglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign  where  type = 'cx'  order by id asc limit 0,1000");

		 $data['ruleimg'] = array();
		 foreach($cximglist as $key=>$value){
		    $data['ruleimg'][$value['id']] = $value['imgurl'];
		 }

		 $data['cxlist'] = $ruleinfo;

		$data['weekji']  =  $weekji;

	  $data['scoretocost'] = Mysite::$app->config['scoretocost'];

	  //判断收藏
	   $data['collect'] = array();
	   if(!empty($this->memberinfo)){ //collectid 对应商品/店铺ID collecttype 0店铺 1商品 shopuid 店铺所有者ID orderid
	  	 $data['collect'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."collect where collectid=".$shopinfo['id']." and collecttype = 0 and uid=".$this->member['uid']." ");
	   }

	  //获取备注
	   $bzinfo = Mysite::$app->config['orderbz'];
	   $data['bzlist'] = array();
	   if(!empty($bzinfo)){
	 	    $data['bzlist'] = unserialize($bzinfo);
	   }
	   /*个人地址列表  */
	   $addresslist = array();
	   if($this->member['uid'] > 0){
	   	  	$addresslist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."address where userid=".$this->member['uid']."  ");
	   } 
	   $data['addresslist'] = $addresslist;
	   
	   //获取促销规则数据
	   
	   
	   $data['paylist']  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id desc  ");  
	   
	   /*优惠卷*/
	   $data['juanlist'] = array();
	   if(!empty($this->member['uid'] )){
	        $data['juanlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid ='".$this->member['uid']."'  and status = 1 and endtime > ".time()."  order by id desc limit 0,20");
	   }
	   Mysite::$app->setdata($data);
	 }
	 function makeorder(){
	    $subtype = intval(IReq::get('subtype'));
	   $info['shopid'] = intval(IReq::get('shopid'));//店铺ID
		 $info['remark'] = IFilter::act(IReq::get('content'));//备注
		 $info['paytype'] = IFilter::act(IReq::get('paytype'));//支付方式 
	 	 $info['username'] = IFilter::act(IReq::get('contactname')); 
		 $info['mobile'] = IFilter::act(IReq::get('phone'));
		 $info['addressdet'] = IFilter::act(IReq::get('addressdet'));
		 $info['senddate'] =  '';
		 $info['minit'] = IFilter::act(IReq::get('minit')); 
		 $info['juanid']  = intval(IReq::get('juanid'));//优惠劵ID
		 $info['ordertype'] = 1;//订单类型 
		 $peopleNum = IFilter::act(IReq::get('personcount'));  
		 $info['othercontent'] = empty($peopleNum)?'':serialize(array('人数'=>$peopleNum));  
		 $info['userid'] = !isset($this->member['score'])?'0':$this->member['uid'];
	   if(Mysite::$app->config['allowedguestbuy'] != 1){
	     if($info['userid']==0) $this->message('member_nologin');
	   } 
		 $shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");   
		 if(empty($shopinfo)) $this->message('店铺不存在');
		 /*监测验证码*/
		 $checksend = Mysite::$app->config['ordercheckphone'];
    if($checksend == 1){
    	  if(empty($this->member['uid'])){
    	  	  $checkphone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."mobile where phone ='".$info['mobile']."'   order by addtime desc limit 0,50");
    	  	  if(empty($checkphone)) $this->message('member_emailyan');
    	  	  if(empty($checkphone['is_send'])){ 
    	  	    $mycode = IFilter::act(IReq::get('phonecode'));
    	  	    if($mycode == $checkphone['code']){
    	  	       $this->mysql->update(Mysite::$app->config['tablepre'].'mobile',array('is_send'=>1),"phone='".$info['mobile']."'");  
    	  	    }else{
    	  	       $this->message('member_emailyan');
    	  	    } 
    	  	  } 
    	  }
    }
    if(empty($info['username'])) 		  $this->message('emptycontact'); 
	  if(!IValidate::suremobi($info['mobile']))   $this->message('errphone'); 
    $info['ipaddress'] = "";
    $ip_l=new iplocation(); 
     $ipaddress=$ip_l->getaddress($ip_l->getIP());  
     if(isset($ipaddress["area1"])){
		   $info['ipaddress']  = $ipaddress['ip'].mb_convert_encoding($ipaddress["area1"],'UTF-8','GB2312');//('GB2312','ansi',);
	   } 
     $info['cattype'] = 0;//
   
	 if($shopinfo['is_open'] != 1) $this->message('店铺暂停营业'); 
	  $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$info['minit'],$shopinfo['befortime']); 
	  if($tempdata['is_opentime'] ==  2) $this->message('选择的配送时间段，店铺未设置');
	  if($tempdata['is_opentime'] == 3) $this->message('选择的配送时间段已超时');
	   $info['sendtime'] = $tempdata['is_posttime'];
	   $info['postdate'] = $tempdata['is_postdate'];
			
			
			
	   $info['paytype'] = $info['paytype']==1?1:0;
	   $info['areaids'] = '';  
	   $info['shopinfo'] = $shopinfo;
	   if($subtype == 1){
	   	$info['allcost'] = 0 ;
	   	$info['bagcost'] = 0;
	   	$info['allcount'] = 0; 
	   	$info['goodslist'] = array();
	   }else{
	   if(empty($info['shopid'])) $this->message('shop_noexit');
			$smardb = new newsmcart();
		 $carinfo = array();
		 if($smardb->setdb($this->mysql)->SetShopId($info['shopid'])->OneShop()){
			   $carinfo = $smardb->getdata(); 
		 }else{ 
		     $this->message($smardb->getError());
		 } 
	   
	   
		  
		    if(count($carinfo['goodslist']) ==0) $this->message('shop_emptycart'); 
		     
	 	     $info['allcost'] = $carinfo['sum']; 
	       $info['goodslist']   = $carinfo['goodslist'];
	     $info['bagcost'] = 0;
	     $info['allcount'] = 0;
	  }
	   $info['shopps'] = 0;  
	   $info['pstype'] = 0;
	   $info['cattype'] = 1;//表示不是预订 
	   $info['is_goshop']=1;    
	   $info['subtype'] = $subtype; 
	   $orderclass = new orderclass();
	   $orderclass->orderyuding($info);
	   $orderid = $orderclass->getorder();
	   if($info['userid'] ==  0){ 
	  	  ICookie::set('orderid',$orderid,86400);
	   }
	   if($subtype == 2){
	    $smardb->DelShop($info['shopid']);
	   } 
	   
		 $this->success($orderid);   
	  	exit;
		 
		 
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
	  
	 
}



?>