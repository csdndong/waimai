<?php
class method   extends baseclass
{  
    function wtest(){
		$dd = $this->getweatherinfo($a,$b);
        print_r($dd);exit; 
	}
	function testwx(){
		 /* $paywechat = new paytowechat();
		 $info = $paywechat->payToUser('oogjk1CRsbLNZQLyF0Qve3VxvthI','22571');
		 print_r($info);exit; */
		 
		  $ordcls = new orderclass();
		 $info = $ordcls->sendWxMsg(44672,1,2);
		 print_r($info);exit; 
		 //$orderid = 44661;

	}
	function xiaochengxu(){
		$areado =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."area where parent_id = 0"); 
		$data['areaone'] = $areado;  
		$data['recomshop'] =  $this->mysql->getarr("select b.id,b.shortname,b.shopname,b.shoplogo,a.shopid from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where   b.is_open = 1 and b.is_pass = 1 and a.is_com =1 limit 0,32");  
		$this->success($data);	
	}
	 
	function updateshoporder(){
		$shopinfo =  $this->mysql->getarr("select  id from ".Mysite::$app->config['tablepre']."shop where id>0"); 	 
		foreach($shopinfo as $k=>$v){
			$ordernum = $this->mysql->select_one("select count(id) as shuliang  from " . Mysite::$app->config['tablepre'] . "order	 where  status = 3 and  shopid = " .$v['id'] . "");
			$data['ordercount'] = $ordernum['shuliang'];
			$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data," id=".$v['id']." ");	
		}
		$this->success('遍历更新店铺订单总数成功！'); 
	}

	function xiaochengxuqq(){
		$this->message("你想干什么呢~"); 
	}

	function index(){		 
		$data['goodstypedoid'] = array();
		$attrshop = array();
		$data['attrinfo'] = array(); 
		$cate  = intval(IFilter::act(IReq::get('cate'))); 
		$data['cate'] = $cate;
		$data['mainattr'] = array(); 
		$templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_main =1  order by orderid asc limit 0,1000");
		foreach($templist as $key=>$value){
			$value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id = ".$value['id']." order by orderid asc  limit 0,20");  
			$data['mainattr'][] = $value;
		} 
		$shopsearch = IFilter::act(IReq::get('shopsearch'));
		$data['shopsearch'] = $shopsearch; 
		if(!empty($shopsearch)) $where .= empty($where)?" where shopname like '%".$shopsearch."%' ":" and shopname like '%".$shopsearch."%' ";
		$where .= "  and b.admin_id = '".$this->CITY_ID."'  ";
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;
		$limitarr['shoptype'] = 1;
		$limitarr['shopcat'] = $cate;   
		$datalistx = $this->Tdata($this->CITY_ID,$limitarr,array('juli'=>'asc'),$lat,$lng,1); 
		$data['shoplist'] = $datalistx;   
		$locationtype = Mysite::$app->config['locationtype'];
		$nowareaid = ICookie::get('myaddress');
		if( empty($lng) || empty($lat) ){
			Mysite::$app->setAction('guide'); 
			$this->guide();
		}else{
		    Mysite::$app->setAction('index'); 
		}
		/* 分类改变 */
 		 $moretypelist = $this->mysql->getarr("select* from ".Mysite::$app->config['tablepre']."appadv where type=2 and activity = 'waimai' and param != 'lifehelp'  and   cityid='".$this->CITY_ID."'  and is_show=1  order by orderid  asc");
      #print_r($moretypelist);
		$data['moretypelist']  = $moretypelist;
		Mysite::$app->setdata($data);       
	}
	function paotuiorder(){
		if(empty($this->member['uid'])){
			$link = IUrl::creatUrl('order/guestorder');
			$this->refunction('',$link);
		}elseif(!empty($this->member['uid'])){
			$link = IUrl::creatUrl('order/usersorder');
			$this->refunction('',$link);
		}
	}
	function appdown(){
		 
	}
	function mobileban(){		 
	 
	}
	function xiugaiaddress(){
		$locationtype = Mysite::$app->config['locationtype']; 
		$psinfo['locationtype'] = $locationtype;
		$data['areainfo'] = '';
		$nowID = ICookie::get('myaddress');
		$data['locationtype'] = $psinfo['locationtype'];
		if($psinfo['locationtype'] == 1){
			//百度地图
			$data['areainfo'] = ICookie::get('mapname');
			if(empty($data['areainfo'])){
				$link = IUrl::creatUrl('site/guide');
				$this->message('请先选择您所在区域在进行下单',$link); 
			} 
		}else{ 
			$data['areainfo'] = ICookie::get('mapname');
			if(empty($nowID)){
				$link = IUrl::creatUrl('site/guide');
				$this->message('请先选择您所在区域在进行下单',$link); 
			}  
		}
		$data['myaddressslist'] = array();
		if(!empty($nowID)){
			$area_grade = Mysite::$app->config['area_grade']; 
			$temp_areainfo = '';
			if($area_grade > 1){
				$areainfocheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id=".$nowID."");
				if(!empty($areainfocheck)){
					$areainfocheck1 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id=".$areainfocheck['parent_id']."");
					if(!empty($areainfocheck1)){
						$temp_areainfo = $areainfocheck1['name'];
						if($area_grade > 2){
							$areainfocheck2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id=".$areainfocheck1['parent_id']."");
							if(!empty($areainfocheck2)){
							    $temp_areainfo = $areainfocheck2['name'].$temp_areainfo;
							}
						}
					} 
					$data['areainfo'] = $temp_areainfo.$data['areainfo'];
				} 
			} 
			if($this->member['uid'] > 0 &&nowID > 0){ 
			    $data['myaddressslist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."address  where areaid".$area_grade."=".$nowID.""); 
			}
		}
		$addid = intval(IReq::get('addid'));
		$addinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where id = ".$addid." and userid = ".$this->member['uid']."  ");
		$data['addinfo']	=  $addinfo;
		Mysite::$app->setdata($data); 	
	} 
	function app(){
	}
	  
	function ajaxshopinfo(){
		$shop_id = intval(IReq::get('shop_id'));
		$data['shopinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where  id='".$shop_id."' ");  
		if(empty($data['shopinfo'])){
			echo 'not find';
			exit;
		}
		$data['attr'] = array(); 
		$templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0   order by orderid asc limit 0,1000");
		foreach($templist as $key=>$value){
			$value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id = ".$value['id']." order by orderid asc  limit 0,20");  
			$data['attr'][] = $value;
		} 
		$nowhour = date('H:i:s',time());
		$data['nowhour'] = strtotime($nowhour); 
		$checkinfo = $this->shopIsopen($data['shopinfo']['is_open'],$data['shopinfo']['starttime'],$data['shopinfo']['is_orderbefore'],$nowhour); 
		$data['shopinfo']['opentype'] = $checkinfo['opentype'];
		$data['shopinfo']['newstartime']  =  $checkinfo['newstartime'];  
		$data['shopinfo']['attrdet'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  cattype = 0 and shopid = '".$data['shopinfo']['id']."' "); 
		Mysite::$app->setdata($data); 
	}
	function collect(){ 
		$nowhour = date('H:i:s',time());
		$data['nowhour'] = strtotime($nowhour);
		$data['mainattr'] = array(); 
		$templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_main =1  order by orderid asc limit 0,1000");
		foreach($templist as $key=>$value){
			$value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id = ".$value['id']." order by orderid asc  limit 0,20");  
			$data['mainattr'][] = $value;
		}  
		$data['signlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type = 'shop'  order by id asc limit 0,100 "); 
		$this->gettopcollect(); 
		Mysite::$app->setdata($data); 
	}
	function gettopcollect(){
		$data['collectlist'] ='';
		if(!empty($this->member['uid'])){
			$where = " where uid=".$this->member['uid']."  and collecttype = '0' "; //条件
			$shoucangl = $this->mysql->getarr("select collectid from ".Mysite::$app->config['tablepre']."collect ".$where." order by id desc limit 0, 5");
			if(count($shoucangl) > 0 ){
                $ids = '';
			foreach($shoucangl as $key=>$value){
			    $ids .= $value['collectid'].',';
			}
			$ids = substr($ids,0,strlen($ids)-1);//FIND_IN_SET( ".$nowadID.", areaid ) 
			$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id and  FIND_IN_SET( id, '".$ids."' )   order by sort asc limit 0,100");   
			$nowhour = date('H:i:s',time()); 
			$nowhour = strtotime($nowhour);
			$templist = array();
			if(is_array($list)){
				foreach($list as $keys=>$values){  
					if($values['id'] > 0){
						$checkinfo = $this->shopIsopen($values['is_open'],$values['starttime'],$values['is_orderbefore'],$nowhour); 
						$values['opentype'] = $checkinfo['opentype'];
						$values['newstartime']  =  $checkinfo['newstartime'];

						$values['attrdet'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  cattype = 0 and shopid = ".$values['id']."");
						$templist[] = $values;
					}
				} 
			}
			$data['collectlist']  = $templist;
			}
			 
		} 
		 Mysite::$app->setdata($data);  
	}
	 
	function guide(){
	 	$areainfo = $this->mysql->getarr("select id,name,parent_id,lat,lng,is_com from ".Mysite::$app->config['tablepre']."area   order by orderid asc");  	   
		$parentids = array();
		foreach($areainfo as $key=>$value){
		    $parentids[] = $value['parent_id'];
		}
		//去重
		$parentids = array_unique($parentids);
		$data['parent_ids'] = $parentids; 
	 	$psset = Mysite::$app->config['psset'];
	    if(!empty($psset)){
			//$psinfo = unserialize($psset);
			$locationtype = Mysite::$app->config['locationtype']; 
			$licationmudule = Mysite::$app->config['licationmudule']; 
			$psinfo['locationtype'] = $locationtype;
			if($psinfo['locationtype'] == 1){
				$surec = IFilter::act(IReq::get('surec'));
				$data['searchvalue'] = '';
				$data['result'] = array(); 
				// $data['mianbaoxue'] = $mianbaoxue;
				$data['sitetitle'] = '确定我的位置'; 
				$arealist =  $this->mysql->getarr("select id,name,parent_id,lat,lng,is_com from ".Mysite::$app->config['tablepre']."area where parent_id = 0 order by orderid asc ");  
				$data['arealist'] = array();
				foreach($arealist as $key=>$value){
					$temparr =  $this->getcoarr($value,$areainfo,$parentids);
					$value['comarea'] = $temparr;
					$data['arealist'][] = $value;
				}
				$cookmalist = ICookie::get('cookmalist');
				$cooklnglist = ICookie::get('cooklnglist');
				$cooklatlist = ICookie::get('cooklatlist');
				$data['cookmalist'] = empty($cookmalist)?array():explode(',',$cookmalist);
				$data['cooklnglist'] = empty($cooklnglist)?array():explode(',',$cooklnglist);
				$data['cooklatlist'] = empty($cooklatlist)?array():explode(',',$cooklatlist); 
				$cookmalist = ICookie::get('cookshuliang'); 
				$data['cookshuliang'] = empty($cookmalist)?array():explode(',',$cookmalist); 
				Mysite::$app->setdata($data);
				Mysite::$app->setAction('baidusearchmap');

			}
	    }
	   
		$where = "  where c.id > 0 and c.adcode > 0 and s.stationis_open = 0 ";		
	    $citylist = $this->mysql->getarr("select adcode,name from ".Mysite::$app->config['tablepre']."area   where id > 0 and parent_id = 0   order by  orderid asc");  
	    $data['citylist'] = $citylist;
	   
	   /* 历史记录 */
	    $cook_adrlistcookie = $_COOKIE['cook_adrlistcookie'];
		$adrarr = explode('#',$cook_adrlistcookie);
		$adrtemparr = array(); 
		if( !emptY($adrarr) ){
			foreach($adrarr as $key=>$value){
				$adrtemparr[] = explode(',',$value);
			}
		}
 		$newadrtemparr = array();
		$valueflag = array(); 
		if(!empty($adrtemparr)){
			foreach($adrtemparr as $key=>$value){ 
				if( !empty($value[0]) &&  !empty($value[1]) &&  !empty($value[2]) &&  !empty($value[3])  ){
					if( in_array($value[2],$valueflag) ){

					}else{
						$valueflag[] = $value[2];
						$newadrtemparr[] = $value;
					} 
				} 
			}
		} 
 		$data['cook_adrlistcookie'] = $newadrtemparr;
 	    Mysite::$app->setdata($data);	 
	} 
    function getcoarr($nowarr,$arealist,$parentids){
  	    $temparray = array();
  	    if(in_array($nowarr['id'],$parentids)){
  	  	    foreach($arealist as $key=>$value){
  	  	  	    if($value['parent_id'] == $nowarr['id']){
  	  	            $checkarray = $this->getcoarr($value,$arealist,$parentids);
					if(count($checkarray) > 0){
					   $temparray = array_merge($temparray,$checkarray); 
					}
				}
  	  	    }
  	    }else{
			if($nowarr['is_com'] == 1){
			    $temparray[] = $nowarr;
			}
  	    }
  	    return $temparray;
    }
	
	 
	 
	function ajaxchangecity(){
		$areaid = intval(IFilter::act(IReq::get('areaid')));
		$backdata = array('flag'=>0,'nav'=>array(),'arealist'=>array());//flag =1 时表示选择 该地址
		if(empty($areaid)){
			$cityinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where parent_id =0  order by id asc limit 0,50");
			//需要的数据  ID name  lng lat
		    $backdata['nav'][] = $cityinfo;  
		    $backdata['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id =".$cityinfo['id']."  order by id asc limit 0,50");
	       	   
	    }else{
			$checkareaid = $areaid;
			$dataareaids = array();
			$checkchild =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where parent_id =".$areaid."  order by id asc limit 0,50");//监测是否包含子项
			if(empty($checkchild)){
				$backdata['flag'] = 1;
				$cityinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id ='".$areaid."'   order by id desc limit 0,50");
				ICookie::set('lng',$cityinfo['lng'],2592000,'/','');  
				ICookie::set('lat',$cityinfo['lat'],2592000,'/','');  
				ICookie::set('mapname',$cityinfo['name'],2592000,'/','');  
				ICookie::set('myaddress',$areaid,2592000,'/','');  
				$backdata['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id =".$cityinfo['parent_id']."  order by id asc limit 0,50");
			}else{
			    $backdata['arealist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id =".$areaid."  order by id asc limit 0,50");
			}
			while($checkareaid > 0){ 
				$cityinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id ='".$checkareaid."'   order by id desc limit 0,50");
				if(empty($cityinfo)){
				    break;
				}
				if(in_array($checkareaid,$dataareaids)){
					break;
				}
				$dataareaids[] = $checkareaid;
				$checkareaid = $cityinfo['parent_id']; 
				$mianbaoxue[] = $cityinfo;
			} 
			$mianbaoxue =    array_reverse($mianbaoxue);  
			$backdata['nav'] = $mianbaoxue;
	    }
	   	$this->success($backdata); 
	} 
	function newajaxshop(){
		$cpid = intval(IFilter::act(IReq::get('cpid')));
		$areaid = intval(IFilter::act(IReq::get('areaid')));
		$lng = trim(IFilter::act(IReq::get('lng')));
		$lat = trim(IFilter::act(IReq::get('lat')));
		$areapre = '';
		$shopareaid = 0;
		if(!empty($areaid)){//当提交区域ID时
			//  $myaddress = ICookie::get('myaddress'); 
			$areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id =".$areaid."  order by id asc limit 0,50");
			$lng = ICookie::get('lng'); 
			$lat = ICookie::get('lat');  
			$shopareaid = $areainfo['id'];
		//获取域名前缀  
		}elseif(!empty($lng)){//当存在坐标时候
			$myaddress = ICookie::get('myaddress'); 
			$areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id =".$myaddress."  order by id asc limit 0,50");

			$shopareaid = $areainfo['id'];
		}else{
			$myaddress = ICookie::get('myaddress'); 
			$areainfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id =".$myaddress."  order by id asc limit 0,50");
			$lng = $areainfo['lng']; 
			$lat = $areainfo['lat']; 
			$shopareaid = $areainfo['id'];
		}

		if(empty($lng)){
		    $this->message('请联系站长,未标记该区域地图坐标');
		}
		$where =   ' where id > 0 and endtime > '.time().'  ';
		$where .= ' and id in(select shopid from '.Mysite::$app->config['tablepre'].'areashop where areaid = '.$shopareaid.' )';
		//店铺构造数据  店铺名  营业时间 联系地址  联系电话   店铺标签    简介  logo 
		if(!empty($cpid)){
		    $where .=  ' and  id in(select shopid from '.Mysite::$app->config['tablepre'].'shopattr where attrid = '.$cpid.' ) ';
		} 
		$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id ".$where."    order by b.id limit 0,1000");  
		$nowhour = date('H:i:s',time()); 
		$nowhour = strtotime($nowhour);
		$templist = array();
		$shopdoid = array();
		if(is_array($list)){//转换数据
			foreach($list as $key=>$value){ 
				if($value['id'] > 0){
					$checkinfo = $this->shopIsopen($value['is_open'],$value['starttime'],$value['is_orderbefore'],$nowhour);  
					$value['opentype'] = $checkinfo['opentype'];
					$value['newstartime']  =  $checkinfo['newstartime'];  
					$psinfo = $this->pscost($value); 
					$value['pscost'] = $psinfo['pscost'];  
					$value['shoplogo'] = empty($value['shoplogo'])? getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($value['shoplogo']);
					//每个店铺属性  
					$shopdoid[] = $value['id'];
					$templist[] = $value;
				}
			} 
		}  
		$this->success(array('areapre'=>$areapre,'list'=>$templist)); 
	}
	function searchdet(){
	    $areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   order by orderid asc");  	  
		$parentids = array();
		foreach($areainfo as $key=>$value){
		    $parentids[] = $value['parent_id'];
		}
		$parentids = array_unique($parentids);
		$data['parent_ids'] = $parentids; 
		Mysite::$app->setdata($data);
	}
	function searchchild(){
		$areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   order by orderid asc");  
		$parentids = array();
		foreach($areainfo as $key=>$value){
		    $parentids[] = $value['parent_id'];
		}
		$parentids = array_unique($parentids);
		$data['parent_ids'] = $parentids; 
		Mysite::$app->setdata($data);
	}
	//设置地区值
	function setlocationlink(){ 
		$areaid = IFilter::act(IReq::get('areaid'));
		$arealist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where id = '".$areaid."'  ");
		
	 
		ICookie::set('lng',$arealist['lng'],2592000);  
		ICookie::set('lat',$arealist['lat'],2592000);  
		ICookie::set('mapname',$arealist['address'],2592000);  
		ICookie::set('myaddress',$areaid,2592000);  
		$cookmalist = ICookie::get('cookmalist');
		$cooklnglist = ICookie::get('cooklnglist');
		$cooklatlist = ICookie::get('cooklatlist');
		$check = explode(',',$cookmalist); 
		if(!in_array($arealist['address'],$check)){
			$cookmalist = empty($cookmalist)?  $arealist['address'].',':$arealist['address'].','.$cookmalist;
			$cooklatlist = empty($cooklatlist)?  $arealist['lat'].',':$arealist['lat'].','.$cooklatlist;
			$cooklnglist = empty($cooklnglist)?  $arealist['lng'].',':$arealist['lng'].','.$cooklnglist;
			ICookie::set('cookmalist',$cookmalist,2592000);  
			ICookie::set('cooklatlist',$cooklatlist,2592000);   
			ICookie::set('cooklnglist',$cooklnglist,2592000);   
		}
		 
			$link = IUrl::creatUrl('site/index');
			$this->message('',$link);
		 
	}
	//通过百度地图设置地区值
	function setuserlng(){
		//setuserlng&mapname=郑州市七十一中&lat=34.784754&lng=113.654217
		$shopid = IFilter::act(IReq::get('shopid'));
		$lng = IFilter::act(IReq::get('lng'));
		$lat = IFilter::act(IReq::get('lat'));
		$mapname = IFilter::act(IReq::get('mapname'));
		$maptype = intval(IFilter::act(IReq::get('maptype')));
		$checklng = intval($lng);
		if(empty($checklng)){
			$link = IUrl::creatUrl('site/guide');
		    $this->message('',$link);
		}
		$checklat = intval($lat);
		if(empty($checklat)){
			$link = IUrl::creatUrl('site/guide');
		 	$this->message('',$link);
		}
		ICookie::set('lng',$lng,2592000);  
		ICookie::set('lat',$lat,2592000);  
		ICookie::set('mapname',$mapname,2592000);  
		ICookie::clear('myaddress');
		$cookmalist = ICookie::get('cookmalist');
		$cooklnglist = ICookie::get('cooklnglist');
		$cooklatlist = ICookie::get('cooklatlist');
		$cookshuliang = ICookie::get('cookshuliang');
		$check = explode(',',$cookmalist); 
		if(!in_array($mapname,$check)){
			$cookmalist = empty($cookmalist)?  $mapname.',':$mapname.','.$cookmalist;
			$cooklatlist = empty($cooklatlist)?  $lat.',': $lat.','.$cooklatlist;
			$cooklnglist = empty($cooklnglist)?  $lng.',':$lng.','.$cooklnglist;
			$shuliang =  $this->mysql->counts("select b.id from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on b.id=a.shopid where b.is_open=1  and  endtime > ".time()." and a.is_waimai =  1 and SQRT((`lat` -".$lat.") * (`lat` -".$lat." ) + (`lng` -".$lng." ) * (`lng` -".$lng." )) < (`pradius`*0.01094 -0.01094 ) order by b.id asc limit 0,1000"); 
			$cookshuliang = empty($cookshuliang)?  $shuliang.',':$shuliang.','.$cookshuliang;
			ICookie::set('cookmalist',$cookmalist,2592000);  
			ICookie::set('cooklatlist',$cooklatlist,2592000);   
			ICookie::set('cooklnglist',$cooklnglist,2592000);   
			ICookie::set('cookshuliang',$cookshuliang,2592000);  
			/*楼口增加数量存放*/
		}
		if($maptype == 2){
			$link = IUrl::creatUrl('plate/index');
			$this->message('',$link);
		}else if($maptype == 1){
			$link = IUrl::creatUrl('market/index');
			$this->message('',$link);
		}else{
			$link = IUrl::creatUrl('site/index');
			$this->message('',$link);
		}	
	}
	//验证码
	function getCaptcha(){ 
	  	$width      = intval(IReq::get('w')) == 0 ? 130 : IFilter::act(IReq::get('w'));
	   	$height     = intval(IReq::get('h')) == 0 ? 45  : IFilter::act(IReq::get('h'));
	   	$wordLength = intval(IReq::get('l')) == 0 ? 5   : IFilter::act(IReq::get('l'));
	   	$fontSize   = intval(IReq::get('s')) == 0 ? 25  : IReq::get('s'); 
	   	$ValidateObj = new Captcha();
	   	$ValidateObj->width  = $width;
	   	$ValidateObj->height = $height;
	   	$ValidateObj->maxWordLength = $wordLength;
	   	$ValidateObj->minWordLength = $wordLength;
	   	$ValidateObj->fontSize      = $fontSize;
	   	$ValidateObj->CreateImage($text); 
	   	exit;
	}
	 //构造地址的select
	function getparentarea(){
		$findid = intval(IReq::get('areaid')); 
		$defaultid = IFilter::act(IReq::get('defaultid')); 
		$deids = empty($defaultid)?array():explode(',',$defaultid);
		$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area  where  parent_id ='".$findid."' limit 0,100");  
		$content = '';
		if(is_array($list)){
		    foreach($list as $key=>$value){
		   	    $extentd = in_array($value['id'],$deids)?'selected':''; 
			    $content .= '<option value="'.$value['id'].'" data="'.$value['id'].'" '.$extentd.'>'.$value['name'].'</option>	';
		    }
		}
		echo $content;
		exit;
		
	}
	
	//获取地址坐标
	function  mapjson(){ 
		$searchvalue= IReq::get('searchvalue');
		$citycode = IReq::get('citycode');
	    $cityname = IReq::get('cityname'); 
		$content =   file_get_contents('http://api.map.baidu.com/place/v2/search?ak='.Mysite::$app->config['baidumapkey'].'&output=json&query='.$searchvalue.'&page_size=10&page_num=0&scope=1&region='.$cityname); 
		echo $content;
		exit; 
	}
  
	function  ajaxlngtlat(){
		$lng= IFilter::act(IReq::get('lng'));
		$lat= IFilter::act(IReq::get('lat'));
		$maptype = intval(IFilter::act(IReq::get('maptype')));
		$content =   file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.Mysite::$app->config['baidumapkey'].'&location='.$lat.','.$lng.'&output=json&pois=0'); 
		$backinfo  = json_decode($content,true);
		$where =  '   and   SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradius`*0.01094 -0.01094 ) ' ;
		if($maptype == 0){
		    $shuliang =  $this->mysql->counts("select b.id from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where  b.is_pass = 1  and  b.endtime > ".time()."  ".$where."  and a.is_waimai =  1  order   by b.id asc  ");
		}elseif($maptype == 2){
		    $shuliang =  $this->mysql->counts("select b.id from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where  b.is_pass = 1  and  b.endtime > ".time()."  ".$where."  and a.is_goshop =  1  order   by b.id asc  ");
		}else{
		    $shuliang =  $this->mysql->counts("select b.id from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where  b.is_pass = 1  and  b.endtime > ".time()."  ".$where."    order   by b.id asc  ");
		} 
		$data['store_num'] = $shuliang;	 
		$data['region_name'] = $backinfo['result']['business'];
		$data['region_addr'] = $backinfo['result']['formatted_address'];
		$data['lng'] = $lng;
		$data['lat'] = $lat;
		$data['city'] = $backinfo['addressComponent']['city'];		 
		if(!empty($data['region_name'])){
			$dizhishu =  $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."positionkey where  datacontent ='".$data['region_name']."'");
			if($dizhishu < 1){
				$areasearch = new areasearch($this->mysql);   
				$myshu =  $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."positionkey where  datatype =3 ");
				$nowid = $myshu+1;
				$areasearch->setdata($data['region_name'],'3',$nowid,$lat,$lng); 
				$areasearch->save();
			}
		}
		if(!empty($data['region_addr'])){
			$dizhishu =  $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."positionkey where  datacontent ='".$data['region_addr']."'");
			if($dizhishu < 1){
				$areasearch = new areasearch($this->mysql);   
				$myshu =  $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."positionkey where  datatype =3 ");
				$nowid = $myshu+1;
				$areasearch->setdata($data['region_addr'],'3',$nowid,$lat,$lng); 
				$areasearch->save();
			}
		}


		$this->success($data);
	} 
	 
	function getsearmap(){
		$searchvalue = trim(IFilter::act(IReq::get('searchvalue')));
		//http://api.map.baidu.com/place/v2/search?q=饭店&region=北京&output=json&ak=E4805d16520de693a3fe707cdc962045&
		$content =   file_get_contents('http://api.map.baidu.com/place/v2/search?ak='.Mysite::$app->config['baidumapkey'].'&output=json&query='.$searchvalue.'&page_size=12&page_num=0&scope=1&region='.Mysite::$app->config['cityname']); 
		$list = json_decode($content,true);
		$backdata = array();
		if($list['message'] == 'ok'){
			if($list['total'] > 1){
				foreach($list['results'] as $key=>$value){
					$temp['datacontent'] =  $value['name'];
					$temp['dataaddress'] =  $value['address'];
					$temp['lng'] =  $value['location']['lng'];
					$temp['lat'] =  $value['location']['lat'];
					$temp['parent_id'] = 0;
					$backdata[] = $temp;
				}	   	     
			}

		}
		$this->success($backdata);  
	}
	
	
  
	function ajaxbaidu(){
		$searchvalue= IFilter::act(IReq::get('searchvalue'));
		$cityname= IFilter::act(IReq::get('cityname'));
		$pagenum= intval(IReq::get('pagenum'));
		$content =   file_get_contents('http://api.map.baidu.com/place/v2/search?ak='.Mysite::$app->config['baidumapkey'].'&output=json&query='.$searchvalue.'&page_size=12&page_num='.$pagenum.'&scope=1&region='.$cityname); 
		$arealist  = json_decode($content,true);
		if($arealist['status'] == 0){
			$tempval = $arealist['results'];
			$temparray = array();
			$bili = intval(Mysite::$app->config['servery']/1000);
			$bili = $bili*0.009;
			foreach($tempval as $key=>$value){
				$lng = $value['location']['lng'];
				$lat = $value['location']['lat']; 
				$shuliang =  $this->mysql->counts("select b.id from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on b.id=a.shopid where   b.is_open=1  and  endtime > ".time()." and a.is_waimai =  1 and   SQRT((`lat` -".$lat.") * (`lat` -".$lat." ) + (`lng` -".$lng." ) * (`lng` -".$lng." )) < (`pradius`*0.01094 -0.01094 )  order by b.id asc limit 0,1000"); 
				$arealist['results'][$key]['shuliang'] = $shuliang;
			}
		}
		$arealist['pagenum'] = $pagenum;
		echo 'searchbackdata('.json_encode($arealist).')';
		exit;
	}
	function addcart(){  
		$smardb = new newsmcart(); 
		$shopid = intval(IReq::get('shopid'));
		$goods_id = intval(IReq::get('goods_id')); 
		$gdtype = intval(IReq::get('gdtype'));//1普通商品 2规格商品  
		$gdtype = in_array($gdtype,array(1,2))?$gdtype:1;
		if(!in_array($gdtype,array(1,2))){
		    $this->message('未定义的商品类型'); 
		}
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->SetGoodsType($gdtype)->AddGoods($goods_id)){
		    $this->success('添加购物车成功'); 
		}else{
		    $this->message($smardb->getError());   
		}  
		$this->success($goods_id); 
	}
	//减少购物车数量
	function downcart()
	{  
		$smardb = new newsmcart(); 
		$shopid = intval(IReq::get('shopid'));
		$goods_id = intval(IReq::get('goods_id'));
		$num = intval(IReq::get('num'));
		if($shopid < 0) $this->message('店铺获取失败'); 
		if($goods_id < 0) $this->message('获取商品失败');
		if($num <  1)$this->message('商品数量失败');
		 $gdtype = intval(IReq::get('gdtype'));//1普通商品 2规格商品  
	    $gdtype = in_array($gdtype,array(1,2))?$gdtype:1; //1普通商品 2规格商品 对应的 商品子ID  
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->SetGoodsType($gdtype)->DownGoods($goods_id)){
			 $this->success('添加购物车成功'); 
		}else{
			$this->message($smardb->getError());   
		}  
		$this->success('操作成功');
	}
	//删除某店铺某商品
	function delcartgoods(){ 
		$smardb = new newsmcart(); 
	    $shopid = intval(IReq::get('shopid')); 
	    $goods_id = intval(IReq::get('goods_id')); 
	    $gdtype = intval(IReq::get('gdtype'));//1普通商品 2规格商品 对应的 商品子ID  
	    $gdtype = in_array($gdtype,array(1,2))?$gdtype:1; //1普通商品 2规格商品 对应的 商品子ID  
	    if($smardb->setdb($this->mysql)->SetShopId($shopid)->SetGoodsType($gdtype)->DelGoods($goods_id)){
			 $this->success('添加购物车成功'); 
		}else{
			$this->message($smardb->getError());   
		}   
        $this->success('操作成功');
	}
	//删除某店铺所有商品
	function delshopcart(){
		$smardb = new newsmcart(); 
		$shopid = intval(IReq::get('shopid')); 
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->DelShop()){
			 $this->success('添加购物车成功'); 
		}else{
			$this->message($smardb->getError());   
		}  
	}
	function selectproduct(){//显示商品类型 
	    $shopid = intval(IReq::get('shopid')); 
	    $goods_id = intval(IReq::get('goods_id')); 
		$goodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where id=".$goods_id." and shopid=".$shopid."");
		
		$data['productinfo'] = !empty($goodsinfo)?unserialize($goodsinfo['product_attr']):array(); 
		
		$smardb = new newsmcart(); 
		$nowselect = $smardb->setdb($this->mysql)->SetShopId($shopid)->FindInproduct($goods_id);
		$data['nowselect'] = $nowselect;
		//获取product 在goodsid中的商品
		$data['attrids'] = array();
		if(!empty($nowselect)){
			$data['attrids'] = explode(',',$nowselect['attrids']);
		}
		$productlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."product where goodsid=".$goods_id." and shopid=".$shopid."");
		$data['productlist'] = $productlist;
		$data['goodsinfo'] = $goodsinfo;
	    Mysite::$app->setdata($data); 
	}
	 function doselectproduct(){
		$shopid = intval(IReq::get('shopid')); 
	    $goods_id = intval(IReq::get('goods_id')); 
		$fgg =  trim(IReq::get('fgg'));
	    $ggdetids =  trim(IReq::get('ggdetids'));
		$type =intval(IReq::get('type'));//但默认选择全部的时候 则select_one 否则select_array()

		if(empty($ggdetids)) $this->message("选择规格");
		if($type == 1){ 
			$ggdetids = explode(',',$ggdetids); 
			$ggwhere = '';
			foreach($ggdetids as $key=>$value){ 
				$ggwhere .= "  and   FIND_IN_SET( ".$value.",`attrids`)    "; 
			}
			 
			//and   `attrids` = '".join(',',$ggdetids)."' 
			$productlist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."product where goodsid=".$goods_id." ".$ggwhere."  and shopid=".$shopid."");
				
			$zigoodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where id=".$productlist['goodsid']."  and shopid=".$shopid."");
			if($zigoodsinfo['is_cx'] == 1){
				//测算促销 重新设置金额
				$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$zigoodsinfo['id']."  ");
			
				$newdata = getgoodscx($productlist['cost'],$cxdata);
				 
				$productlist['zhekou'] = $newdata['zhekou'];
				$productlist['is_cx'] = $newdata['is_cx'];
				$productlist['oldcost'] = $productlist['cost'];
				$productlist['cost'] = $this->formatcost($newdata['cost'],2) ;
					
				}
			 
			$smardb = new newsmcart(); 
			$nowselect = $smardb->setdb($this->mysql)->SetShopId($shopid)->productone($productlist['id']);
			$productlist['goodcartnum'] = $nowselect;
		  # print_r($productlist); 
			 $this->success($productlist);
		}else{
	 
			$ggdetids = explode(',',$ggdetids);
			sort($ggdetids); 
			$tempid = join(',',$ggdetids);  // 																								  
			$productlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."product where goodsid=".$goods_id."  and  FIND_IN_SET('".$tempid."',`attrids`) and shopid=".$shopid."");
		    $canchoiceid = array();
      	    foreach($productlist as $key=>$value){
				if($value['stock'] > 0){
					$tempids = explode(',',$value['attrids']);
					foreach($tempids as $k=>$v){
						if(!in_array($v,$canchoiceid)){
							$canchoiceid[] = $v;
						}
					}
				}
			}
			$this->success(join(',',$canchoiceid));
		}
		
	}
	//输入框修改数量
	function modifycart()
	{  
		// $shopid = intval(IReq::get('shopid'));
		// $goods_id = intval(IReq::get('goods_id'));
		// $targetnum = intval(IReq::get('num')); 
		// if($shopid < 0) $this->message('店铺获取失败'); 
		// if($goods_id < 0) $this->message('获取商品失败'); 
		// if($targetnum < 1) $this->message('请执行删除该商品操作');
		  // $Cart = new smCart;   
		// $carinfo = $Cart->getMyCart(); 
		// if(!isset($carinfo['list'][$shopid]['data'][$goods_id]['count'])){
			 // $this->message('此商品未添加到购物车');
		// }
		// $js = $targetnum - $carinfo['list'][$shopid]['data'][$goods_id]['count'];
		// $num = $js;
		// $checkinfo = $Cart->add($goods_id,$num,$shopid);
		
		// if($checkinfo == false)
		// {
		   // $this->message($Cart->getError());
		// } 
		 $this->message('此函数已禁止');
	}
	//清楚购物车所有商品
	function clearcart(){
		$smardb = new newsmcart(); 
		$smardb->setdb($this->mysql)->ClearCart();
		$this->success('清空所有商品成功'); 
	      
	}
	//显示小型购物车
	function smallcat(){
	    $shopid = intval(IReq::get('shopid'));  
	  	$shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'  ");
		if(empty($shopinfo)){
			$link = IUrl::creatUrl('site/index');
		   $this->message('店铺不存在',$link);
		}
		//年费检测
		if($shopinfo['endtime'] < time()){
			$link = IUrl::creatUrl('site/index');
		   $this->message('店铺已关门',$link);
		}
		$nowhour = date('H:i:s',time());
		$nowhour = strtotime($nowhour);
		$data['shopinfo'] = $shopinfo; 
		$data['shopopeninfo'] = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopinfo['is_orderbefore'],$nowhour);
		
		$smardb = new newsmcart();
		$carinfo = array();
		 if($smardb->setdb($this->mysql)->SetShopId($shopid)->OneShop()){
			   $carinfo = $smardb->getdata(); 
			   $cxclass = new sellrule();  
			   $cxclass->setdata($shopid,$carinfo['sum'],0); 
				$cxinfo = $cxclass->getdata(); 
				$carinfo['cx'] = $cxinfo;  
				$tempinfo =   $this->pscost($shopinfo); 
				$carinfo['pstype'] = $tempinfo['pstype'];
			   $carinfo['pscost'] = $cxinfo['nops'] == true?0:$tempinfo['pscost'];
				$carinfo['limitcost'] = $shopinfo['limitcost']; 
			  
		 }else{
			 $link = IUrl::creatUrl('site/index');
		     $this->message($smardb->getError(),$link);
		 } 
			 
	    $data['carinfo'] = $carinfo;  
	    $data['shopid'] = $shopid;    
	    Mysite::$app->setdata($data); 
	} 
	function smallcatding(){
		$shopid = intval(IReq::get('shopid'));  
	    $shopcheckinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");   
	    $smardb = new newsmcart();
		$carinfo = array();
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->OneShop()){
			$carinfo = $smardb->getdata();
			$cxclass = new sellrule();  
			$cxclass->setdata($shopid,$carinfo['sum'],0); 
			$cxinfo = $cxclass->getdata(); 
			$carinfo['cx'] = $cxinfo;  
			$tempinfo =   $this->pscost($shopcheckinfo); 
			$carinfo['pstype'] = $tempinfo['pstype'];
			$carinfo['pscost'] = $cxinfo['nops'] == true?0:$tempinfo['pscost'];
			$carinfo['limitcost'] = $shopcheckinfo['limitcost'];
		}else{
			$link = IUrl::creatUrl('site/index');
			$this->message($smardb->getError(),$link);
		} 
		$data['cartinfo'] = $carinfo;
		$data['shopinfo'] = $shopcheckinfo;
		$data['cxdata'] = $carinfo['cx']; 
		$data['shopid'] = $shopid;    
		Mysite::$app->setdata($data); 
	}
	function marketcart(){
		$shopid = intval(IReq::get('shopid'));  
		$where = " where shopid = '".$shopid."' ";

		$shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id ".$where."      limit 0,100");  
		$data['findtype'] = 0;
		if(empty($shopinfo)){
			$shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id    order by sort asc limit 0,100");  
			$data['findtype'] = 1;
		}	   
	    $data['shopinfo'] = $shopinfo;    
	    $nowhour = date('H:i:s',time());
	    $nowhour = strtotime($nowhour);
	    $data['shopinfo'] = $shopinfo;
		$data['shopopeninfo'] = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopinfo['is_orderbefore'],$nowhour);
	    $smardb = new newsmcart();
		$carinfo = array();
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->OneShop()){
			$carinfo = $smardb->getdata();
			$cxclass = new sellrule();  
			$cxclass->setdata($shopid,$carinfo['sum'],1); 
			$cxinfo = $cxclass->getdata(); 
			$carinfo['cx'] = $cxinfo;  
			$tempinfo =   $this->pscost($shopinfo); 
			$carinfo['pstype'] = $tempinfo['pstype'];
			$carinfo['pscost'] = $cxinfo['nops'] == true?0:$tempinfo['pscost'];
			$carinfo['limitcost'] = $shopinfo['limitcost'];			  
		}else{
			$link = IUrl::creatUrl('site/index');
			$this->message($smardb->getError(),$link);
		} 
			 
	    $data['carinfo'] = $carinfo; 
	    $data['shopid'] = $shopid;    
	    Mysite::$app->setdata($data); 
	}
	function showcatax(){
		$shopid = intval(IReq::get('shopid'));  
	    if(empty($shopid)){
	   	 	$link = IUrl::creatUrl('site/index');
	     	$this->message('未选择对应店铺',$link); 
	    }
	    $Cart = new smCart;    
	    $carinfo = $Cart->getMyCart();  
	    if(!isset($carinfo['list'][$shopid]['data'])){
	    	$link = IUrl::creatUrl('site/index');
	     	$this->message('对应店铺购物车商品为空',$link);  
	    }   
	    $showtype = trim(IReq::get('showtype'));  
	    $data['showtype'] = $showtype;
	    if($showtype == 'market'){ 
	   	    $data['shopinfo'] =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");   
	    }else{
	        $data['shopinfo'] =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");   
	    }
	   
	    if(empty($data['shopinfo'])) {
	   		$link = IUrl::creatUrl('site/index');
	     	$this->message('未选择对应店铺111',$link); 
	    }
	    $data['shopid'] = $shopid; 
	    $data['scoretocost'] = Mysite::$app->config['scoretocost'];
	    $data['cartinfo'] = $carinfo;
	    $cxclass = new sellrule();  
	    foreach($carinfo['list'] as $key=>$value){ 
			if($value['shopinfo']['shoptype'] == '1'){
				$shopcheckinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$key."'    ");  
			}else{
			   $shopcheckinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$key."'    ");  
			}
			$cxclass->setdata($key,$value['sum'],$value['shopinfo']['shoptype']); 
			$cxinfo = $cxclass->getdata();  
			
			$data['cartinfo']['list'][$key]['cx'] = $cxinfo; 
			$tempinfo =   $this->pscost($shopcheckinfo); 
			$data['cartinfo']['list'][$key]['pstype'] = $tempinfo['pstype'];
			$data['cartinfo']['list'][$key]['pscost'] = $cxinfo['nops'] == true?0:$tempinfo['pscost'];
	    }      
		$checkps = $this->pscost($data['shopinfo']); 
		if($checkps['canps'] != 1){
			$link = IUrl::creatUrl('site/guide');
			$this->message('该店铺不在配送范围内',$link); 
		}
		$locationtype = Mysite::$app->config['locationtype']; 
		$psinfo['locationtype'] = $locationtype;
		$data['areainfo'] = '';
		$nowID = ICookie::get('myaddress');
		$data['locationtype'] = $psinfo['locationtype'];
		if($psinfo['locationtype'] == 1){
			//百度地图 
			$data['areainfo'] = ICookie::get('mapname');
			if(empty($data['areainfo'])){
				$link = IUrl::creatUrl('site/guide');
				$this->message('请先选择您所在区域在进行下单',$link); 
			} 
		}else{ 
			$data['areainfo'] = ICookie::get('mapname');
			if(empty($nowID)){
				$link = IUrl::creatUrl('site/guide');
				$this->message('请先选择您所在区域在进行下单',$link); 
			} 
			$checkareaid = $nowID;
			$dataareaids = array();
			$areadata = array();
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
				$areadata[] = $temp_check['name']; 
			}
			$areadata = array_reverse($areadata);
			$data['areainfo'] = join('',$areadata);	   
		}

		$data['myaddressslist'] = array();
		if($this->member['uid'] > 0 ){ 
			$data['myaddressslist'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where     userid = ".$this->member['uid']." and `default` =1 "); 
		}
	    $data['juanlist'] = array();
	    if(!empty($this->member['uid'] )){
	        $data['juanlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid ='".$this->member['uid']."'  and status = 1 and endtime > ".time()."  order by id desc limit 0,20");
	    }
	    $data['paylist']  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id desc  ");   
        $nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($data['shopinfo']['postdate'])?unserialize($data['shopinfo']['postdate']):array();
		$data['pstimelist'] = array();
		$checknow = time();
		$whilestatic = $data['shopinfo']['befortime'];
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
					$tempt['i'] =  $value['i'];
					$tempt['cost'] =  isset($value['cost'])?$value['cost']:0; 
					$tempt['name'] = $stime > $checknow?$tempt['s'].'-'.$tempt['e']:'立即配送'; 
					$data['pstimelist'][] = $tempt;
				}
			}
			$nowwhiltcheck = $nowwhiltcheck+1;
		}
	  	  
	   Mysite::$app->setdata($data); 
	}
	function showcart(){
		$shopid = intval(IReq::get('shopid'));  
	    if(empty($shopid)){
	   	 	$link = IUrl::creatUrl('site/index');
	     	$this->message('未选择对应店铺',$link); 
	    } 
	    $smardb = new newsmcart();
		$carinfo = array();
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->OneShop()){
			$carinfo = $smardb->getdata();	  
		}else{
			$link = IUrl::creatUrl('site/index');
		    $this->message($smardb->getError(),$link);
		} 
	    $showtype = trim(IReq::get('showtype'));  
	    if($showtype == 'market'){ 
	   	    $data['shopinfo'] =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");   
	    }else{
	        $data['shopinfo'] =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");   
	    }  
	    if(empty($data['shopinfo'])) {
	   		$link = IUrl::creatUrl('site/index');
	     	$this->message('未选择对应店铺',$link); 
	    }
	    $data['shopid'] = $shopid; 
	    $data['scoretocost'] = Mysite::$app->config['scoretocost'];
	    $psset = Mysite::$app->config['psset'];
	    if(empty($psset)){
	   	    $link = IUrl::creatUrl('site/index');
	        $this->message('网站未设置配送方式，请联系管理员',$link); 
	   }     
        $locationtype = Mysite::$app->config['locationtype']; 
	  	$psinfo['locationtype'] = $locationtype;
		$checkps = $this->pscost($data['shopinfo']); 
		$data['areainfo'] = '';
		$nowID = ICookie::get('myaddress');
		$data['locationtype'] = $psinfo['locationtype'];
		if($psinfo['locationtype'] == 1){
			//百度地图
			$data['areainfo'] = ICookie::get('mapname');
			if(empty($data['areainfo'])){
				$link = IUrl::creatUrl('site/guide');
				$this->message('请先选择地址后下单',$link); 
			} 
		}else{ 
			$data['areainfo'] = ICookie::get('mapname');
			if(empty($nowID)){
				$link = IUrl::creatUrl('site/guide');
				$this->message('请先选择地址后下单',$link); 
			}  
		}
		$data['myaddressslist'] = array();
		if(!empty($nowID)){
			$area_grade = Mysite::$app->config['area_grade']; 
			$temp_areainfo = '';
			if($area_grade > 1){
				$areainfocheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id=".$nowID."");
				if(!empty($areainfocheck)){
					$areainfocheck1 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id=".$areainfocheck['parent_id']."");
					if(!empty($areainfocheck1)){
						$temp_areainfo = $areainfocheck1['name'];
							if($area_grade > 2){
							$areainfocheck2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id=".$areainfocheck1['parent_id']."");
							if(!empty($areainfocheck2)){
							$temp_areainfo = $areainfocheck2['name'].$temp_areainfo;
							}
						}
					} 
					$data['areainfo'] = $temp_areainfo.$data['areainfo'];
				} 
			} 
			if($this->member['uid'] > 0 &&nowID > 0){ 
			    $data['myaddressslist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."address  where areaid".$area_grade."=".$nowID.""); 
			}
	    }
	 
	    $paylist  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$this->CITY_ID."'    ");  
	  
	  	$data['paytype'] = $paylist['paytype']; 
	    $nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($data['shopinfo']['postdate'])?unserialize($data['shopinfo']['postdate']):array();
		$data['pstimelist'] = array();
		$checknow = time(); 
		$whilestatic = $data['shopinfo']['befortime'];
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
					$tempt['i'] =  $value['i'];
					$tempt['cost'] =  isset($value['cost'])?$value['cost']:0; 
					$tempt['name'] = $stime > $checknow?$tempt['s'].'-'.$tempt['e']:'立即配送';
					$data['pstimelist'][] = $tempt;
				}
			}
		 
			$nowwhiltcheck = $nowwhiltcheck+1;
		}
		if(!empty($this->member['uid'] )){
		    $data['juanlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid ='".$this->member['uid']."'  and status = 1 and endtime > ".time()."  order by id desc limit 0,20");
		}
		$waimai_psrangearr = $this->platpsinfo['waimai_psrange'];
		$waimai_psrangearr = explode('#',$waimai_psrangearr);
		$data['waimai_psrange_arr'] = $waimai_psrangearr;
	    Mysite::$app->setdata($data); 
	}
	function smallcat2(){
		$buyerlng = trim(IReq::get('buyerlng'));   
		$buyerlat = trim(IReq::get('buyerlat'));   
		$shopid = intval(IReq::get('shopid'));   
		$data['shopxinxi'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where id = '".$shopid."'    ");
		$data['shopinfo'] = array();
		if($data['shopxinxi']['shoptype'] ==1){
	   	    $shopcheckinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");  
		}else{
		    $shopcheckinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");  
		}
		$smardb = new newsmcart();
		$carinfo = array();
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->OneShop()){
			$carinfo = $smardb->getdata();
			$cxclass = new sellrule();  
            $paytype = intval(IReq::get('paytype'));
			$cxclass->setdata($shopid,$carinfo['sum'],0,$this->member['uid'],1,$paytype);//pc端下单
			$cxinfo = $cxclass->getdata(); 
			$carinfo['cx'] = $cxinfo;  
			$tempinfo =   $this->pscost($shopcheckinfo,$buyerlng,$buyerlat); 
			$carinfo['pstype'] = $tempinfo['pstype'];
			$carinfo['pscost'] = $cxinfo['nops'] == true?0:$tempinfo['pscost'];
			$carinfo['areacost'] = 0;//$shopcheckinfo['pscost'];
			$carinfo['limitcost'] = $shopcheckinfo['limitcost'];	  
		}else{
			$link = IUrl::creatUrl('site/index');
			$this->message($smardb->getError(),$link);
		} 
		$data['cartinfo'] = $carinfo;
		$data['shopinfo'] = $shopcheckinfo;
		$data['shopid'] = $shopid; 
		$data['juanlist'] = array();
	    if(!empty($this->member['uid'] )){
	        $data['juanlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid ='".$this->member['uid']."'  and status = 1 and endtime > ".time()."  order by id desc limit 0,20");
	    }
 	    Mysite::$app->setdata($data); 
		
	}	
	/*购物车部分结束**************************/
	function ajaxareadata(){
		$areaid = intval(IReq::get('areaid'));
		$typeid = intval(IReq::get('typeid'));
		$arealist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id = ".$areaid." order by orderid asc ");  
		$this->success($arealist);
	}
	function single(){
		$data['show'] = IFilter::act(IReq::get('show'));
		$data['id'] =  intval(IFilter::act(IReq::get('id')));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."single where id ='".$data['id']."' or code='".$data['show']."' order by id asc ");  
		if(empty($data['info'])){
		    $data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."single   order by id asc ");  
		} 
		Mysite::$app->setdata($data);
	}
	function news(){
		$data['id'] =  intval(IFilter::act(IReq::get('id')));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."news where id ='".$data['id']."'  order by id desc ");  
		if(empty($data['info'])){
		    $data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."news   order by id asc ");  
		} 
		Mysite::$app->setdata($data);
	}
	function newstype(){
		$data['id'] =  intval(IFilter::act(IReq::get('id')));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."newstype where id ='".$data['id']."'  order by id asc ");  
		if(empty($data['info'])){
		    $data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."newstype where  parent_id > 0 order by id desc ");  
		} 
		Mysite::$app->setdata($data);
	}
    function phonecode(){
        $phone = IFilter::act(IReq::get('phone')); 
	    $phonecls = new phonecode($this->mysql,6,$phone); 
		if($phonecls->sendcode()){  
			echo 'showsend(\''.$phone.'\','.$phonecls->getTime().')';
			exit;  
		}else{ 
			echo 'showmessage(\''.$phonecls->getError().'\')';
			exit;
		}  
    }
    function setphone(){
		$checksend = Mysite::$app->config['ordercheckphone'];
		if($checksend == 1){
			if(!empty($this->member['uid'])){
				echo 'removesend()';
				exit;
			} 
			$phone = IFilter::act(IReq::get('phone'));
			$phonecls = new phonecode($this->mysql,6,$phone); 
			if($phonecls->sendcode()){  
				echo 'showsend(\''.$phone.'\','.$phonecls->getTime().')';
				exit;  
			}else{ 
				echo 'alert(\''.$phonecls->getError().'\')';
				exit;
			}   
		}else{
			echo '';
			exit;
		}
    }
   function waitpay(){  
    	$userid = empty($this->member['uid'])?0:$this->member['uid']; 
	  	$orderid = intval(IReq::get('orderid')); 
	  	if(empty($orderid)) $this->message('订单获取失败');
	  	if($userid == 0){
	  		//ICookie::get('Captcha')
	  		$neworderid = ICookie::get('orderid');
	  	 
	  		if($orderid != $neworderid) $this->message('订单无查看权限1');
	  	}
	  	$data['orderinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");  //获取主单
	  	if(empty($data['orderinfo'])) $this->message('订单数据获取失败');
	  	if($userid > 0 && $this->admin['uid'] ==  0){
	  		if($data['orderinfo']['buyeruid'] !=  $userid) $this->message('无查看权限2');
	  	}
	  	$data['orderdetlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id=".$orderid."  order by id asc limit 0,50");//获取子单
	  	$paytypelist = array(0=>'货到支付',1=>'在线支付');  
	   	$paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist  where type = 0 or type =1  order by id asc limit 0,50");
	  	  
	  	$data['paylist'] = $paylist; 
	  	$data['paytypearr'] = $paytypelist; 
	  	$data['buyerstatus'] = array(
	  	'0'=>'等待处理',
	  	'1'=>'订单处理中',
	  	'2'=>'订单已发货',
	  	'3'=>'订单完成',
	  	'4'=>'订单已取消',
	  	'5'=>'订单已取消'
	  	);
	  	 $weixin  = array('flag'=>0,'msg'=>'');	  	 
	  	 $data['weixin'] = $weixin;
		 
     Mysite::$app->setdata($data); 	
    	
   }
   //微信支付订单
   function wx_orderpay(){
	    $paylist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where  loginname = 'weixin'  order by id asc limit 0,50");
	    if(empty($paylist)){
		    $this->message('');
	    }
	    $weixindir = hopedir.'/plug/pay/weixin/'; 
	    $orderid = intval(IReq::get('orderid')); 
	    $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");  //获取主单
	    if(empty($orderinfo)){
		    $this->message('');
	    } 
	    if($orderinfo['paystatus'] == 0 && $orderinfo['paytype'] == 1 && $orderinfo['status'] < 3 ){ 
		    require_once $weixindir."lib/WxPay.Api.php";
			require_once $weixindir."WxPay.NativePay.php"; 
		    $input = new WxPayUnifiedOrder(); 
			$input->SetBody("支付订单".$orderinfo['dno']);
			$input->SetAttach($dno);
			$input->SetOut_trade_no($orderinfo['id']);
			$input->SetTotal_fee($orderinfo['allcost']*100);
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetGoods_tag("订餐");
			$input->SetNotify_url(Mysite::$app->config['siteurl']."/plug/pay/weixin/notify.php");
			$input->SetTrade_type("NATIVE");
			$input->SetProduct_id($dno['id']); 
			$NativePay = new NativePay();
			$result = $NativePay->GetPayUrl($input);   
			if(isset($result['code_url'])){
				$this->success($result['code_url']);
			}else{
				$this->message('');
			} 
			 
	    }else{
		    $this->message('');
	    }   
    }
   //定时器轮训是否支付
   function ajaxcheckpay(){
	    $orderid = intval(IReq::get('orderid')); 
	    $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");  //获取主单
		if(empty($orderinfo)){
		  $this->message('');
	    } 
	    if($orderinfo['paystatus'] == 1 && $orderinfo['paytype'] == 1 && $orderinfo['status'] < 3 ){ 
			$this->success('success');
	    }else{
		    $this->message('');
	    }
    }
   function gotopay(){
		$errdata = array('paysure'=>false,'reason'=>'','url'=>''); 
		$orderid = intval(IReq::get('orderid')); 
	    $payerrlink = IUrl::creatUrl('site/waitpay/orderid/'.$orderid);
	    $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");  //获取主单
	    $errdata['dno'] = $orderinfo['dno'];
		if($orderid == 0){
			$backurl = IUrl::creatUrl('site/index');  
			$errdata['url'] = $backurl;
			$errdata['reason'] = '订单获取失败';
			$errdata['paysure'] = false;  
			$this->showpayhtml($errdata);  
		}

		$payerrlink = IUrl::creatUrl('site/waitpay/orderid/'.$orderid);     
		$userid = empty($this->member['uid'])?0:$this->member['uid']; 
		if($userid == 0){
			$neworderid = ICookie::get('orderid');
			if($orderid != $neworderid) {
				$errdata['url'] = $payerrlink;
				$errdata['reason'] = '订单操作无权限';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
			}
		}
		if(empty($orderinfo)) { 
			$errdata['url'] = $payerrlink;
			$errdata['reason'] = '订单不存在';
			$errdata['paysure'] = false;  
			$this->showpayhtml($errdata);  
		}
		if($userid > 0){
			if($orderinfo['buyeruid'] !=  $userid) {
				$errdata['url'] = $payerrlink;
				$errdata['reason'] = '订单不属于您不能支付';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
			}
		}
		if($orderinfo['paytype'] == 0){
			if($orderinfo['buyeruid'] !=  $userid) {
				$errdata['url'] = $payerrlink;
				$errdata['reason'] = '此订单是货到支付订单不可操作';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
			} 
		}
		if($orderinfo['status']  > 2){
			$errdata['url'] = $payerrlink;
			$errdata['reason'] = '此订单已发货或者其他状态不可操作';
			$errdata['paysure'] = false;  
			$this->showpayhtml($errdata);  
			 
		}
		$paydotype = IFilter::act(IReq::get('paydotype'));
		$paylist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where  loginname = '".$paydotype."' and (type = 0 or type=1) order by id asc limit 0,50");
		if(empty($paylist)){
			$errdata['url'] = $payerrlink;
			$errdata['reason'] = '不存在的支付类型';
			$errdata['paysure'] = false;  
			$this->showpayhtml($errdata);  
		}
		if($orderinfo['paystatus'] == 1){
			$errdata['url'] = $payerrlink;
			$errdata['reason'] = '订单已支付，不能重复付款';
			$errdata['paysure'] = false;  
			$this->showpayhtml($errdata); 
		}
		$paydir = hopedir.'/plug/pay/'.$paydotype;
	 	if(!file_exists($paydir.'/pay.php'))
		{ 
			$errdata['url'] = $payerrlink;
			$errdata['reason'] = '支付文件不存在';
			$errdata['paysure'] = false;  
			$this->showpayhtml($errdata); 
		}

      $dopaydata = array('type'=>'order','upid'=>$orderid,'cost'=>$orderinfo['allcost'],'source'=>0,'paydotype'=>$paydotype);//支付数据
      include_once($paydir.'/pay.php');   
       exit;
	}

	function showpayhtml($data){
		if($data['paysure']){
			$url = Mysite::$app->config['siteurl'].'/index.php?ctrl=site&action=paysuccess&paysure=1&reason=paysuccess&dno='.$data['reason']['dno'];
		}else{
			$url = Mysite::$app->config['siteurl'].'/index.php?ctrl=site&action=paysuccess&paysure=0&reason='.$data['reason'].'&dno='.$data['dno'];
		}
		header("location:".$url);

	}
	function  paysuccess(){
		$dno = IReq::get('dno');
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where dno=".$dno."  ");
		$data['dno'] = intval(IReq::get('dno'));
		$data['reason'] = IReq::get('reason');
		$data['paysure'] = intval(IReq::get('paysure'));
		$data['orderinfo'] = $orderinfo;
		Mysite::$app->setdata($data);
	}
	function showpayhtml1111($data){
		$tempcontent = '';
		//array('paysure'=>false,'reason'=>'','url'=>'');
		if($data['paysure'] == true){
			$tempcontent = '<div style="margin-top:50px;background-color:#fff;">
			 <div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
			    <span style="background:url(\'http://'.Mysite::$app->config['siteurl'].'/upload/images/order_ok.png\') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
				<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;">恭喜您，支付订单成功</div>
			</div>
			<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">单号:</font><span style="padding-left:20px;font-size:12px;display: inline-block;">'.$data['reason']['dno'].'</span></div>
			<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">总价:</font><span style="padding-left:20px;color:red;font-weight:bold;font-size:15px;">￥'.$data['reason']['allcost'].'元</span></div> 
			<div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><a href="'.$data['url'].'"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;">立即返回</span></a></div>
	   </div>';
		}else{
			$tempcontent = '<div style="margin-top:50px;background-color:#fff;">
			 <div style="height:30px;width:80%;padding-left:10%;padding-right:10%;padding-top:10%;">
			    <span style="background:url(\''.Mysite::$app->config['siteurl'].'/upload/images/nocontent.png\') left no-repeat;height:30px;width:30px;background-size:100% 100%;display: inline-block;"></span>
				<div style="position:absolute;margin-left:50px;  margin-top: -30px; font-size: 20px;  font-weight: bold;  line-height: 20px;">sorry,支付订单失败</div>
			</div>
			<div style="width:80%;margin:0px auto;padding-top:10px;"><font style="font-size:12px;">原因:</font><span style="padding-left:20px;font-size:12px;display: inline-block;">'.$data['reason'].'</span></div> 
			<div style="width:80%;margin:0px auto;padding-top:30px;text-align:right;"><a href="'.$data['url'].'"><span style="font-size:20px;color:#fff;padding:5px;background-color:red;  cursor: pointer;">立即返回</span></a></div>
	   </div>';
		}
		$html = '<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8">  
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>支付返回信息</title> 
	 
	 
 
 <script>
 	 
</script>

 
</head>
<body style="height:100%;width:100%;margin:0px;"> 
   <div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> '.$tempcontent.'    </div>
	 
</body>
</html>'; 
print_r($html);
exit;
      
    }


	function updatearea(){
		  $this->mysql->getarr("TRUNCATE TABLE  `xiaozu_areatoadd`");
		  $this->mysql->getarr("TRUNCATE TABLE  `xiaozu_areashop`");
		 $tempaa = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   order by id asc limit 0,2000");
		 foreach($tempaa as $key=>$value){
		   	$temp['areaid'] = $value['id'];
		   	$temp['shopid'] =0;
		 	 $this->mysql->insert(Mysite::$app->config['tablepre'].'areashop',$temp); 
		 	 $tk['areaid'] =$value['id'];
		 	 $tk['shopid'] = 0;
		 	 $tk['cost'] = 0;
		 	  $this->mysql->insert(Mysite::$app->config['tablepre'].'areatoadd',$tk); 
		 } 
	  $udata['cattype'] = 0;
		$this->mysql->update(Mysite::$app->config['tablepre'].'goodstype',$udata," id > 0 ");
	}
	//在线支付返回处理
	function payback(){
		//在线支付返回处理代码
		$paytype = trim(IFilter::act(IReq::get('paytype'))); 
		if(empty($paytype)){
		    $this->error('未定义的支付接口'); 
		}
		$paydir = hopedir.'/plug/pay/'.$paytype;
		if(!file_exists($paydir.'/back.php')){ 
		    $this->message('支付方式方式不存在');
		} 
		$paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id asc limit 0,50");
		if(is_array($paylist)){
			foreach($paylist as $key=>$value){
			    $paytypelist[] =$value['loginname'];		 
			}
		}
		if(!in_array($paytype,$paytypelist)){
		    $this->message('未定义的支付方式');
		} 
		include_once($paydir.'/back.php');  
	}
	function noticeurl(){
		$paytype = trim(IFilter::act(IReq::get('paytype'))); 
		if(empty($paytype)){
		  $this->message('未定义的支付接口'); 
		}
		$paydir = hopedir.'/plug/pay/'.$paytype;
		if(!file_exists($paydir.'/notice.php')){ 
			$this->message('支付方式方式不存在');
		} 
        $paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id asc limit 0,50");
        if(is_array($paylist)){
		    foreach($paylist as $key=>$value){
			    $paytypelist[] =$value['loginname'];		 
		    }
		}
		if(!in_array($paytype,$paytypelist)){
		    $this->message('未定义的支付方式');
		} 
        include_once($paydir.'/notice.php');  
	}
	function ceju(){
		$mi = $this->GetDistance(IFilter::act(IReq::get('lat')),IFilter::act(IReq::get('lng')), IFilter::act(IReq::get('dlat')),IFilter::act(IReq::get('dlng')), 1); 
		$tempmi = $mi;
		$mi = $mi > 1000? round($mi/1000,2).'km':$mi.'m';
		$this->success($mi);
	}
	function searchposition(){
		$position = IFilter::act(IReq::get('position'));
		$areainfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area   order by orderid asc");   
		$parentids = array();
		foreach($areainfo as $key=>$value){
		  $parentids[] = $value['parent_id'];
		}
		$parentids = array_unique($parentids);	 
		$data['list'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where  id not in(".join(',',$parentids).") and name like '%".$position."%' order by orderid asc  ");
	    Mysite::$app->setdata($data); 	
	}
	function makeshow(){
		$id = intval(IFilter::act(IReq::get('id')));
		$actime = IFilter::act(IReq::get('actime'));
		$sign = IFilter::act(IReq::get('sign'));
		$status = intval(IFilter::act(IReq::get('status')));
		if($id < 1){
			echo '获取失败';
			exit;
		}
		if(empty($actime)){
			echo '检测不通过';
			exit;
		}
		if(empty($sign)){
			echo '检测不通过';
			exit;
		}
		$orderinfo =    $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$id."'   ");
		if(empty($orderinfo)){
			echo '订单获失败';
			exit;
		}
		$tempstr = md5($orderinfo['dno'].$actime);
		$tempstr = substr($tempstr,3,15);
		$tempstr = md5($orderinfo['shopuid'].$tempstr);
		$tempstr = substr($tempstr,3,15);
		if($sign != $tempstr){
			echo '验证不通过';
			exit;
		}
		if($orderinfo['status'] != 1){
			echo '订单状态不可操作制作与否';
			exit;
		}
	    $dolink = IUrl::creatUrl('site/sendorder/id/'.$id.'/sign/'.$sign.'/actime/'.$actime);
		if(!empty($orderinfo['is_make'])){
			echo '订单已处理过,不需再次操作<br>';
			if($orderinfo['is_make'] == 1){
				echo '已确认制作<br>';
				echo '若需要立即发货,<a href="'.$dolink.'">点击发货</a>';
				exit;
			}else{
				echo '已取消制作该订单,等待管理员处理';
				exit;
			}
		}else{	     
			if($status == 1){
				$newdata['is_make'] = 1;
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$newdata,"id='".$id."'");
				echo '已确认制作<br>';
				echo '若需要立即发货,<a href="'.$dolink.'">点击发货</a>';
				exit;
			}elseif($status == 2){
				$newdata['is_make'] = 2;
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$newdata,"id='".$id."'");
				echo '已取消制作该订单,等待管理员处理';
				exit;
			}else{
				echo '提交操作数据失败';
				exit;
			}
	    }
	    exit; 
	}
	function sendorder(){
		$id = intval(IFilter::act(IReq::get('id')));
		$actime = IFilter::act(IReq::get('actime'));
		$sign = IFilter::act(IReq::get('sign'));
		$status = intval(IFilter::act(IReq::get('status')));
		if($id < 1){
			echo '获取失败';
			exit;
		}
		if(empty($actime)){
			echo '检测不通过';
			exit;
		}
		if(empty($sign)){
			echo '检测不通过';
			exit;
		}
		$orderinfo =    $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$id."'   ");
		if(empty($orderinfo)){
			echo '订单获失败';
			exit;
		}
		$tempstr = md5($orderinfo['dno'].$actime);
		$tempstr = substr($tempstr,3,15);
		$tempstr = md5($orderinfo['shopuid'].$tempstr);
		$tempstr = substr($tempstr,3,15);	 
		if($sign != $tempstr){
			echo '验证不通过';
			exit;
		}
		if($orderinfo['status'] != 1){
			echo '订单状态已发货或者不能发货';
			exit;
		}
		if($orderinfo['is_make'] != 1){
			echo '订单制作状态错误'; 
			exit;
		}
		$newdata['status'] = 2;
		$this->mysql->update(Mysite::$app->config['tablepre'].'order',$newdata,"id='".$id."'");
		echo  '操作成功';
		exit;
  }
  function catalog(){
	  	$tempareaid = ICookie::get('myaddress'); 
	  	
	    $areaid =0;
	    if(empty($tempareaid)){
	    	$areaid = 2;
	    }else{  
	    	$dataareaids = array();
	    	while($tempareaid > 0){
				$areaid = $tempareaid;
				$temp_check =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id ='".$tempareaid."'   order by id desc limit 0,50");
				if(empty($temp_check)){
				    break;
				}
				if(in_array($temp_check['parent_id'],$dataareaids)){
				    break;
				}
				if($temp_check['parent_id'] == 0){
				    break;
				}
				$dataareaids[] = $tempareaid;
				$tempareaid = $temp_check['parent_id'];  
	        } 
	    } 
       
      
       
       
       
      //获取所有菜品分类
		$caiplist = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."shoptype where parent_id = 51   order by orderid asc limit 0,50"); 
		//获取所有二级区域名称
		$arealist = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."area where parent_id = '".$areaid."'   order by id asc limit 0,50"); 
		$shoplist =  $this->mysql->getarr("select id,shopname from ".Mysite::$app->config['tablepre']."shop where   id in(select shopid from ".Mysite::$app->config['tablepre']."areashop where areaid = ".$areaid.") "); 
		$tempshoplist=array();
        foreach($shoplist as $k=>$val){
			//获取每个店铺的  菜品 值   区域值 
			$temp_cp = $this->mysql->getarr("select attrid  from ".Mysite::$app->config['tablepre']."shopattr  where shopid = '".$val['id']."'   order by attrid asc limit 0,50"); 
			$temp_cpids = array();
			foreach($temp_cp as $key=>$value){
			    $temp_cpids[] = $value['attrid'];
			}
			$vk['cpids'] = join(',',$temp_cpids); 
			$tempb = $this->mysql->getarr("select areaid  from ".Mysite::$app->config['tablepre']."areashop  where shopid = '".$val['id']."'   order by areaid asc limit 0,100"); 
			$dotem = array();
			foreach($tempb as $vc=>$vb){
			    $dotem[] =$vb['areaid'];
			}
			$vk['areaids'] = join(',',$dotem);
			$vk['id'] = $val['id'];
			$vk['shopname'] = $val['shopname']; 
			$tempshoplist[] = $vk;
        } 
		$data['shopdata'] = $tempshoplist;
		$data['caiplist'] = $caiplist; 
		$data['arealist'] = $arealist; 
		Mysite::$app->setdata($data);
	}
	
	function changeshop(){
		$id = intval(IFilter::act(IReq::get('id')));
		$link = IUrl::creatUrl('site/index/');
		if($id < 1){ 
		    $this->message('获取店铺ID失败',$link);
		}
		$grade = Mysite::$app->config['area_grade'];
		$temp_where = '';
		$doarea = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id in(select id from ".Mysite::$app->config['tablepre']."area where parent_id =0) "); 
		if($grade == 1){
		    $where = ' and areaid  in(select id from '.Mysite::$app->config['tablepre'].'area where parent_id =0)';
		}elseif($grade == 2){
		    $where = ' and areaid  in(select id from '.Mysite::$app->config['tablepre'].'area where parent_id in(select id from '.Mysite::$app->config['tablepre'].'area where parent_id =0)) ';
		}elseif($grade == 3){
		    $where = ' and areaid   in(select id from '.Mysite::$app->config['tablepre'].'area where parent_id in(select id from '.Mysite::$app->config['tablepre'].'area where parent_id in(select id from '.Mysite::$app->config['tablepre'].'area where parent_id =0))) ';
		} 
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."areatoadd where shopid=".$id." ".$where."");
		if(empty($checkinfo)){
		    $this->message('获取店铺区域信息失败',$link);
		} 
		$arealist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id = ".$checkinfo['areaid']." order by orderid asc "); 
		if(empty($arealist)){
		    $this->message('获取店铺区域信息失败',$link);
		} 
		ICookie::set('lng',$arealist['lng'],2592000);  
		ICookie::set('lat',$arealist['lat'],2592000);  
		ICookie::set('mapname',$arealist['name'],2592000);  
		ICookie::set('myaddress',$checkinfo['areaid'],2592000);  
		$cookmalist = ICookie::get('cookmalist');
		$cooklnglist = ICookie::get('cooklnglist');
		$cooklatlist = ICookie::get('cooklatlist');
		$check = explode(',',$cookmalist); 
		if(!in_array($arealist['name'],$check)){
			$cookmalist = empty($cookmalist)?  $arealist['name'].',':$arealist['name'].','.$cookmalist;
			$cooklatlist = empty($cooklatlist)?  $arealist['lat'].',':$arealist['lat'].','.$cooklatlist;
			$cooklnglist = empty($cooklnglist)?  $arealist['lng'].',':$arealist['lng'].','.$cooklnglist;
			ICookie::set('cookmalist',$cookmalist,2592000);  
			ICookie::set('cooklatlist',$cooklatlist,2592000);   
			ICookie::set('cooklnglist',$cooklnglist,2592000);   
		}
		$link = IUrl::creatUrl('shop/index/id/'.$id);
		$this->message('',$link); 
	}
	function shoplist(){
		$data['cpid'] = intval(IFilter::act(IReq::get('cpid'))); 
		$data['qisong'] = intval(IFilter::act(IReq::get('qisong'))); 
		$data['renjun'] = intval(IFilter::act(IReq::get('renjun'))); 
		$data['orderby'] = intval(IFilter::act(IReq::get('orderby'))); 
		$psset = Mysite::$app->config['psset'];
		//店铺的cattype 
		$locationtype = 0; 
		$attrshop = array();	        
	 	         //获取搜索属性性结束
		$qisongarray = array(
		'0'=>'',
		'1'=>' a.limitcost > 0 and a.limitcost < 51 ',
		'2'=>' a.limitcost > 50 and a.limitcost < 101 ',
		'3'=>' a.limitcost > 100   '
		);
		$renjunarray = array(
		'0'=>'',
		'1'=>' a.personcost > 0 and a.personcost < 11 ',
		'2'=>' a.personcost > 10 and a.personcost < 51  ',
		'3'=>' a.personcost > 50'
		);
		$orderarray = array(
		'0'=>'  sort asc',
		'1'=>' a.personcost desc',
		'2'=>' a.limitcost desc'
		);
		$orderinfo = in_array($data['orderby'],array(0,1,2))?$orderarray[$data['orderby']]:'sort asc';
		
		$templist = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."shoptype where  (cattype = 1 or  cattype=0) and parent_id = 0 and is_search = 1 and type ='checkbox'  order by orderid asc limit 0,1000");
	 
		$data['caipin'] = array();
		if(is_array($templist) &&count($templist) > 0){
			$doc = array();
			foreach($templist as $key=>$value){
				$doc[] = $value['id'];
			}
			$data['attrinfo']  = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id  in(".join(',',$doc).")  ");
		} 			
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;
		$order = intval(IReq::get('renjun'));//   0综合排序 1好评优先 2起送价最低 3销量
		$order = in_array($order,array(1,2,3))? $order:0; 
		$orderarray = array( 
			//默认距离由近到远排序					   
			'0' =>array('juli'=>'asc'),
			//按好评由高到低排序
			'1'=>array('ping'=>'desc'),
			//按起送价由低到高排序
			'2'=>array('limitcost'=>'asc'),
			//按销量由高到低排序           
			'3'=>array('sell'=>'desc'),			   
		);
		$limitarr['shopcat'] = $data['cpid']; 
		$shopsearch =  trim(IReq::get('shopsearch'));
		$limitarr['search_input'] = $shopsearch; 
		$datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,1); 
		$data['shoplist'] = $datalistx;    
		$data['cpid'] = empty($data['cpid'])?'default':$data['cpid']; 
		$data['qisong'] = empty($data['qisong'])?'default':$data['qisong']; 
		$data['renjun'] = empty($data['renjun'])?'default':$data['renjun']; 
		$data['orderby'] = empty($data['orderby'])?'default':$data['orderby'];
		$data['lng'] = $lng;
		$data['lat'] = $lat; 
		$data['search_input'] = $shopsearch;
		Mysite::$app->setdata($data); 
	}

	
	 function twojiguide(){
	  	$position = IFilter::act(IReq::get('position')); 
	    $list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id = ".$position." order by orderid asc  "); 		
		$data['list'] = array();
		foreach($list as $key=>$value){
			$value['sanjiguide'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id = ".$value['id']." order by orderid asc  ");   
			$data['list'][] = $value;
		}
	    Mysite::$app->setdata($data); 
	 }
	
	 //其他页面 设置地区值
	function qitesetlocationlink(){ 
		$areaid = IFilter::act(IReq::get('areaid'));
		$arealist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id = ".$areaid." order by orderid asc ");
		ICookie::set('lng',$arealist['lng'],2592000);  
		ICookie::set('lat',$arealist['lat'],2592000);  
		ICookie::set('mapname',$arealist['name'],2592000);  
		ICookie::set('myaddress',$areaid,2592000);  
		$cookmalist = ICookie::get('cookmalist');
		$cooklnglist = ICookie::get('cooklnglist');
		$cooklatlist = ICookie::get('cooklatlist');
		$check = explode(',',$cookmalist); 
		if(!in_array($arealist['name'],$check)){
			$cookmalist = empty($cookmalist)?  $arealist['name'].',':$arealist['name'].','.$cookmalist;
			$cooklatlist = empty($cooklatlist)?  $arealist['lat'].',':$arealist['lat'].','.$cooklatlist;
			$cooklnglist = empty($cooklnglist)?  $arealist['lng'].',':$arealist['lng'].','.$cooklnglist;
			ICookie::set('cookmalist',$cookmalist,2592000);  
			ICookie::set('cooklatlist',$cooklatlist,2592000);   
			ICookie::set('cooklnglist',$cooklnglist,2592000);   
		}
		$link = IUrl::creatUrl('site/shoplist');
		$this->message('',$link);
	}
	
				   
 /* 发布跑腿 start   */
	function fabupaotui(){
		$data['content'] = trim(IFilter::act(IReq::get('ptcontent')));
		$data['buyername'] = trim(IFilter::act(IReq::get('name')));
		$data['buyeraddress'] = trim(IFilter::act(IReq::get('address')));
		$data['buyerphone'] = trim(IFilter::act(IReq::get('phone')));		
		$data['addtime'] = time();
		$data['ordertype'] = 1;//订单类型
		$data['shoptype'] = 100;//订单类型
		$data['buyeruid']  = $this->member['uid'];
		$data['dno'] = time().rand(1000,9999);
		$data['posttime'] = time();	
		$data['ipaddress'] = "";
		$panduan = Mysite::$app->config['man_ispass'];
		$data['status'] = 0;
		if($panduan != 1 && $data['paytype'] == 0){
		    $data['status'] = 1;
		} 
		$ip_l=new iplocation(); 
		$ipaddress=$ip_l->getaddress($ip_l->getIP());  
		if(isset($ipaddress["area1"])){
		    $data['ipaddress']  = $ipaddress['ip'].mb_convert_encoding($ipaddress["area1"],'UTF-8','GB2312');//('GB2312','ansi',);
		} 
		if(!(IValidate::len($data['content'],5,500)))$this->message('内容太简单');
		if(!(IValidate::len($data['buyername'],2,10)))$this->message('姓名太短');
		if(!(IValidate::len($data['buyeraddress'],2,100)))$this->message('地址不详细');
		if(!IValidate::suremobi($data['buyerphone']))   $this->message('请输入正确的手机号'); 		 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'order',$data);
		$orderid = $this->mysql->insertid(); 
		$checksend = Mysite::$app->config['man_ispass'];
		if($checksend != 1){ 
			$orderclass = new orderclass();
			$orderclass->sendmess($orderid);
		}
		$this->success('success!');			
	}
	  
	//8.6
	function postmsgbypay(){   // 普通订单 在线支付成功 后返回数据处理
	    $orderid = intval(IReq::get('orderid')); 		
		if(empty($orderid)) {
			echo '订单号错误';
			exit;
		} 
		$checkflag = false;
		/* 更新订单物流信息 */
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id = ".$orderid."   ");
		$shopinfo = $this->mysql->select_one("select is_autopreceipt from ".Mysite::$app->config['tablepre']."shop where id=".$orderinfo['shopid']."  "); 
		//在线支付，支付成功后通知商家有新订单
		$ordCls = new orderclass();
		//获取主单
		$orderCLs = new orderclass();
		$orderCLs->writewuliustatus($orderinfo['id'],3,$orderinfo['paytype']);  //在线支付成功状态	 
		//如果商家开启自动接单功能，支付成功后订单状态默认制作
		if($shopinfo['is_autopreceipt']  == 1 ){
			$datakk['is_make'] = 1;
			$datakk['maketime'] = time();
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$datakk,"id ='".$orderid."' ");
			$orderCLs->writewuliustatus($orderinfo['id'],4,$orderinfo['paytype']);  //商家自动确认接单	  
			$auto_send = Mysite::$app->config['auto_send'];
			if($auto_send == 888){
 
			}else{
						//自动生成配送单----------------------------
				if($orderinfo['shoptype'] != 100){
					if($orderinfo['pstype'] == 0 ){//网站配送自动生成配送费 
						$checkflag = true;
					}elseif($orderinfo['pstype'] == 2 && $orderinfo['is_ziti'] == 0 ){
						$psbinterface = new psbinterface();
						if($psbinterface->psbnoticeorder($orderid)){
							$checkflag = false;
						}
					}
				}else{
					logwrite("跑腿订单调起配送宝start:".$orderid);
					$checkflag = false;
					$psbinterface = new psbinterface();
					if($psbinterface->paotuitopsb($orderid)){
						
					}
				}  
			}
			//微信推送消息
			if($orderinfo['shoptype'] != 100){
				if($ordCls->sendWxMsg($orderinfo['id'],4,1)){
					
				}
			}
		}else{
			if($orderinfo['shoptype'] == 100){ 
				$checkflag = false;
				$psbinterface = new psbinterface();
				if($psbinterface->paotuitopsb($orderid)){
					
				} 
			}
			//微信推送消息
			if($orderinfo['shoptype'] != 100){
				if($ordCls->sendWxMsg($orderinfo['id'],2,1)){
					
				}
				if($ordCls->sendWxMsg($orderinfo['id'],1,2)){
			
				}
			}else{
				if($ordCls->sendWxMsg($orderinfo['id'],2,3)){
					
				}
			}			
		}
	    if($checkflag == true){
			$psylist =  $this->mysql->getarr("select a.*  from ".Mysite::$app->config['tablepre']."apploginps as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.admin_id = ".$orderinfo['admin_id']."");
			$psCls = new apppsyclass(); 
			$psCls->SetUserlist($psylist)->sendNewmsg('订单提醒','有新订单可以处理'); 
		}
		$orderCLs->sendmess($orderid); 
		echo 'success';
		exit;
	   
	}


	function acountpayaddlog(){   // 在线充值 成功 后 数据处理
		 $dno = trim(IReq::get('dno')); 
		 if(empty($dno)) {
			echo '获取失败';
			exit;
		} 
		$acountonlinelog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."onlinelog where dno = '".$dno."'  and status = 1   ");
		if(empty($acountonlinelog)){
			echo '交易记录不存在';
			exit;
		}
		if( $acountonlinelog['used'] == 1){
			echo '已使用';
			exit;
		}
		mysql_query("UPDATE  `".Mysite::$app->config['tablepre']."onlinelog` SET  `used` =  1 where `id`=".$acountonlinelog['id']."");
		$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid = ".$acountonlinelog['upid']."   ");
	
		$rechargecost = $acountonlinelog['cost'];
		
 		
		$rechargeone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."rechargecost where cost  =".$rechargecost." order by orderid asc  ");
		$liyouContent = '';
		if( !empty($rechargeone)){
			
			if( $rechargeone['is_sendcost']== 1  && $rechargeone['sendcost'] > 0  ){ // 充值赠送账户余额
				
				mysql_query("UPDATE  `".Mysite::$app->config['tablepre']."member` SET  `cost` =  `cost`+".$rechargeone['sendcost']." where `uid`=".$memberinfo['uid'].""); 
			    mysql_query("INSERT INTO `".Mysite::$app->config['tablepre']."memberlog` (`id` ,`userid` ,`type` ,`addtype` ,`result` ,`addtime` ,`content` ,`title` ,`acount` )VALUES (NULL , '".$memberinfo['uid']."', '2', '1', '".$rechargeone['sendcost']."', '".time()."', '在线充值赠送账户余额', '使用微信支付充值".$rechargecost."元赠送".$rechargeone['sendcost']."元账户余额', '".$memberinfo['cost']."');");
						
				$cost = $memberinfo['cost']+$acountonlinelog['cost']+$rechargeone['sendcost'];
				$docost = $memberinfo['cost']+$rechargeone['sendcost'];
				$liyouContent = '在线充值'.$acountonlinelog['cost'].'元,赠送'.$rechargeone['sendcost'].'元';
				$memberCls = new memberclass($this->mysql);  
				$memberCls->addmemcostlog( $memberinfo['uid'],$memberinfo['username'],$memberinfo['cost'],1,$rechargeone['sendcost'],$docost,$liyouContent,$memberinfo['uid'],$memberinfo['username'] );
				
				
			}  
			if( $rechargeone['is_sendjuan']== 1  && $rechargeone['sendjuancost'] > 0  ){ // 充值赠送优惠券
 				$liyouContent .= ',赠送'.$rechargeone['sendjuancost'].'元优惠券'; 
				$where = "  where type = 3 and name = '充值送优惠券' and id = '".$rechargeone['juanid']."' " ;
				$checkinfosendjuan = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuan ".$where."  order by id asc  ");
                                
				 $checkinfoalljuan = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where name = '充值送优惠券' and type = 3  order by id asc  ");
				if($checkinfoalljuan['timetype'] == 1){
					$checkinfotime['creattime'] = time();
					$checkinfotime['endtime'] = time()+$checkinfoalljuan['days']*24*60*60;
				}else{
					$checkinfotime['creattime'] = $checkinfosendjuan['starttime'];
					$checkinfotime['endtime'] = $checkinfosendjuan['endtime'];
				}
				if($checkinfoalljuan['costtype'] == 1){
				   $checkinfocost['cost'] = $checkinfosendjuan['cost'];
				}else{
					$checkinfocost['cost'] = $rechargeone['sendjuancost'];
				}
                $sendjuanlist = array();
				if($rechargeone['is_sendjuan'] == 1){
					$temparray = array();
					$temparray['limitcost']  = $checkinfosendjuan['limitcost'];
					$temparray['cost']  = $checkinfocost['cost']; 
					$temparray['creattime']  = $checkinfotime['creattime']; 
					$temparray['endtime']  = $checkinfotime['endtime']; 
					$sendjuanlist[] = $temparray; 
					$juanliststring = '';
					if( !empty($sendjuanlist) && $rechargeone['is_sendjuan'] == 1 ){
						foreach($sendjuanlist as $key=>$val){ 
							$data['status'] = 1;
							$data['creattime'] = $val['creattime'];
							$data['cost'] = $val['cost'];
							$data['limitcost'] = $val['limitcost'];
							$data['endtime'] = $val['endtime'];
							$data['uid'] = $memberinfo['uid'];
							$data['username'] = $memberinfo['username'];
							$data['bangphone'] = $memberinfo['phone'];
							$data['name'] = "满".$val['limitcost']."元减".$val['cost']."元";
							$data['type'] = 3;
							$data['paytype'] = $checkinfosendjuan['paytype'];
							$juanliststring .= "-----".$data['name'];
							$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$data);  
						}
					}
					$liyouContent .= $juanliststring;

				}	
			}  
		}  
		echo 'success';
		exit;
		
	}
	 
	/****loginforliansuo***/
	function loginfor(){
		if( strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')   ){    //判断是微信浏览器不 
			$code = $_GET['code'];
			if(isset($_GET['code']) && !empty($code)){
				   $link =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".Mysite::$app->config['wxappid']."&secret=".Mysite::$app->config['wxsecret']."&code=".$code."&grant_type=authorization_code";
				    $wxclass = new wx_s();
					$info=  $wxclass->vpost($link);
					$info = json_decode($info,true);
				    if(isset($info['access_token'])){ 
					      if($info['expires_in'] < 1){ 
							  $link =  "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".Mysite::$app->config['wxappid']."&grant_type=refresh_token&refresh_token=".$info['refresh_token'];  
							   $tempb= $wxclass->vpost($link);
							  $info = json_decode($tempb,true); 
						  }
						  
						 
						   $checkget = $wxclass->showuserinfo($info['openid']);
						   if($checkget){
								$wxbkuser = $wxclass->getone(); 
								$loginfor = "http://liansuo.waimairen.com/index.php?ctrl=site&action=loginforback";
								$tempinfo = '';
								// for($wxbkuser as $key=>$value){
									// $tempinfo = empty($wxbkuser)?$key.'='.$value:'&'.$key.'='.$value;
								// }
								$checkinfo = $this->bbbvpost($loginfor,$wxbkuser); 
								if($checkinfo['result'] == 'ok'){
									$linkdata = http_build_query($checkinfo['cook']);	 
									header("location:http://liansuo.waimairen.com/index.php?".$linkdata);
								    exit;
								}else{
									print_r($checkinfo);
									exit;
								} 
						   }else{
							   print_r($wxclass->err());
							   
						   } 
						  exit;
				   }else{ 
					    $myurl = Mysite::$app->config['siteurl'].'/index.php?ctrl=site&action=loginfor';  
						$newurl = urlencode($myurl); 
						$getlink = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".Mysite::$app->config['wxappid']."&redirect_uri=".$newurl."&response_type=code&scope=snsapi_base&state=123#wechat_redirect"; 
						header("location:".$getlink);
						exit;
				   } 
			}else{
				$myurl = Mysite::$app->config['siteurl'].'/index.php?ctrl=site&action=loginfor';  
				$newurl = urlencode($myurl); 
				$getlink = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".Mysite::$app->config['wxappid']."&redirect_uri=".$newurl."&response_type=code&scope=snsapi_base&state=123#wechat_redirect"; 
				header("location:".$getlink);
				exit;
			} 
		} 
	}
	 
	function bbbvpost($url,$data='',$cookie=''){ // 模拟提交数据函数 
		$options = array(  
			'http' => array(  
				'method' => 'POST',  
				'content' => http_build_query($data),  
			),  
		);  

		$result = file_get_contents($url, false, stream_context_create($options));  
		$cookies = array();
		foreach ($http_response_header as $hdr) {
			if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
				parse_str($matches[1], $tmp);
				$cookies += $tmp;
			}
		} 
		$newarray['result'] = $result;
		$newarray['cook'] = $cookies;
		return $newarray; 
	}
	
	//8.5新增
	function checkOpenCity(){
		$adcode = trim(IReq::get('adcode')); 
		$areainfoone = array();
		if( !empty($adcode) ){
		    $areacodeone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."areacode where id=".$adcode."  "); 
			if( !empty($areacodeone) ){
				$adcodeid = $areacodeone['id'];
				$pid = $areacodeone['pid'];
				$areainfoone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where  adcode=".$adcodeid." or adcode=".$pid."  ");  
			}
		} 
		$this->success($areainfoone);
	} 
	 
	//获取搜索省市区编码列表
	function getcitylist(){
		$searchval = trim(IReq::get('searchval'));
		$areacodelist = array();
		if( !empty($searchval) ){
			$where = " where name like '%".$searchval."%'";
			$areacodelist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area  ".$where." "); 
		}
		$this->success($areacodelist);
	} 
	//选择地址检测
	function checkadrinfo(){
		$lng = trim(IFilter::act(IReq::get('lng')));
		$lat = trim(IFilter::act(IReq::get('lat')));
		$address = trim(IFilter::act(IReq::get('address')));
		$adcode = trim(IFilter::act(IReq::get('adcode'))); 
		if( !empty($adcode) ){
			$areacodeone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where adcode=".$adcode."  "); 
				if( empty($areacodeone) ){
					$this->message("所属城市未开通");
				}else{
					ICookie::set('lat',$lat,86400*365*10);  
					ICookie::set('lng',$lng,86400*365*10);  
					ICookie::set('addressname',$address,86400*365*10); 
					ICookie::set('mapname',$address,86400*365*10); 
					$city_id = "CITY_ID_".$areacodeone['adcode'];
					$city_name = "CITY_NAME_".$areacodeone['name'];
					ICookie::set('CITY_ID',$city_id,86400*365*10);
					ICookie::set('CITY_NAME',$city_name,86400*365*10);
				}  
		}else{
			$this->message("获取所选城市失败");
		}
		
		$this->success('success');
	}
	 function testemail(){
		ini_set('display_errors',1);            //错误信息
		ini_set('display_startup_errors',1);    //php启动错误信息
		error_reporting(-1); 
		print_r(Mysite::$app->config['emailname']);
		echo '<br>';
		print_r(Mysite::$app->config['emailpwd']);
		$smtp = new ISmtp(Mysite::$app->config['smpt'],465,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],true); 
		$title = '测试发送';
		$tempcontent = '发送邮件';
		$smtp->send('zhaojq5099@foxmail.com', Mysite::$app->config['emailname'],$title,$tempcontent, "" , "HTML" , "" , "");  
		exit;
	}
	
	function  testcache(){ 
		$checkinfo =memData::init()->getkey('ceshi');
		print_r($checkinfo);
		exit;
	} 
}



?>