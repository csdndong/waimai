<?php
class method   extends baseclass
{
	//默认首页
	function index(){
	   $shop = trim(IFilter::act(IReq::get('id')));
	   $weekji  = date('w');
	    
								
		$where = intval($shop) > 0?' where a.shopid = '.$shop:'where shortname=\''.$shop.'\'';
		//获取外卖店铺
		$shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id ".$where."   ");
		
	//店铺总销量
	   	  $shopcounts = $this->mysql->counts( "select id   from ".Mysite::$app->config['tablepre']."order 	 where status = 3 and   shopid = ".$shop."" );
  			if(empty( $shopcounts )){
				$data['sellcount'] = 0;
			}else{
				$data['sellcount']  = $shopcounts;
			} 
			$data['sellcount'] = $data['sellcount']+$shopinfo['virtualsellcounts'];



		if( $shopinfo['pointcount'] != 0){
							$zongtistart = round( $shopinfo['point']/$shopinfo['pointcount'] ); // 总体评分  // 12 / 3 = 54
 						}else{
							$zongtistart = 0;
 						}
						 
						  $shopinfo['point'] = $zongtistart;
    	if(empty($shopinfo)){
			$link = IUrl::creatUrl('site/index');
		  $this->message('获取店铺信息失败',$link);
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


		//获取所有外卖分类及商品
		$com_goods =  $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."goods where shopid = ".$shopinfo['id']." and is_com = 1   ");
		 $data['com_goods'] = array();
		 foreach($com_goods as $key=>$valq){
			 	 $zongpoint = $valq['point'];
								$zongpointcount = $valq['pointcount'];
								if($zongpointcount != 0 ){
									$shopstart = intval( round($zongpoint/$zongpointcount) );
								}else{
									$shopstart= 0;
								}
									$valq['point'] = 	$shopstart;	
								
								
			 	$data['com_goods'][] = $valq;
				 
		 }
		 
		 
		$goodstype=  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodstype where shopid = ".$shopinfo['id']." and cattype = ".$shopinfo['shoptype']." order by orderid asc");
		$data['goodstype'] = array();
		$tempids = array();
		foreach($goodstype as $key=>$value){
			 $detaa = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."goods where typeid = ".$value['id']." and is_waisong = 1 and  is_live = 1 and  FIND_IN_SET( ".$weekji." , `weeks` )   and shopid=".$shopinfo['id']."  ");
			 
			 
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
					$valq['cxnum'] = $cxdata['cxnum'];
				}
				
				$valq['sellcount'] = $valq['sellcount']+$valq['virtualsellcount'];
				
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
		  $ruleinfo = $sellrule->get_rulelist();
	    
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
		 
		 
		 
	  $data['scoretocost'] = Mysite::$app->config['scoretocost'];

	  //判断收藏
	  
	   $data['collect'] = array();
	   if(!empty($this->member)){ //collectid 对应商品/店铺ID collecttype 0店铺 1商品 shopuid 店铺所有者ID orderid
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
	   
	   $data['weekji'] = $weekji;
	   
	   Mysite::$app->setdata($data);
	}   
	
	 function showgoods(){  	//菜品详情
		 $shopshowtype = ICookie::get('shopshowtype');
		 $data['shopshowtype'] = $shopshowtype;
		$id = intval( IReq::get('id') );
		$foodshow = $this->mysql->select_one( "select * from  ".Mysite::$app->config['tablepre']."goods where id= ".$id."  " );
		$shopid = $foodshow['shopid'];
		$data['shopinfo'] = $this->mysql->select_one( "select * from  ".Mysite::$app->config['tablepre']."shop where id= ".$shopid."  " );
		
		if( $shopshowtype == 'market' ){
			$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   "); 
		}else{
			$shopdet  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   "); 
		}
		 
		$nowhour = date('H:i:s',time()); 
        $nowhour = strtotime($nowhour);
		$checkinfo = $this->shopIsopen($data['shopinfo']['is_open'],$data['shopinfo']['starttime'],$shopdet['is_orderbefore'],$nowhour); 
        $data['opentype'] = $checkinfo['opentype'];
		 /* 商品评价 */
		$data['pointcount'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."comment   where shopid = ".$shopid." and  goodsid  = ".$id."   "); 
		 if($foodshow['is_cx'] == 1){
				//测算促销 重新设置金额
					$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$foodshow['id']."  ");
					$newdata = getgoodscx($foodshow['cost'],$cxdata);
					
					$foodshow['zhekou'] = $newdata['zhekou'];
					$foodshow['is_cx'] = $newdata['is_cx'];
					$foodshow['cost'] = $newdata['cost'];
					$foodshow['cxnum'] = $cxdata['cxnum'];
		 }
		 
		$foodshow['sellcount'] = $foodshow['sellcount']+$foodshow['virtualsellcount']; 
		$data['shopdet'] = $shopdet;
		$data['foodshow']  = $foodshow;
		
		/* 配送费 */
		$newshoparray = array_merge($data['shopinfo'],$shopdet);
      	 	 $tempinfo =   $this->pscost($newshoparray); 
                      $backdata['pstype'] = $tempinfo['pstype'];
                      $backdata['pscost'] = $tempinfo['pscost'];
           $data['psinfo'] = $backdata;
		
		
		
		$data['productinfo'] = !empty($foodshow)?unserialize($foodshow['product_attr']):array(); 
		
		$smardb = new newsmcart(); 
		 $smardb->setdb($this->mysql)->SetShopId($shopid);
		$data['nowselect'] = array();
		if($foodshow['have_det'] == 0){
			$tempinfo = $smardb->SetGoodsType(1)->productone($id);
		 
			$data['carnum'] =  $tempinfo;
		}else{
			$nowselect =$smardb->FindInproduct($id);
			$data['nowselect'] = $nowselect;
			$data['carnum'] = $nowselect['count'];
		}
	 

		//获取product 在goodsid中的商品
		$data['attrids'] = array();
		if(!empty($nowselect)){
			$data['attrids'] = explode(',',$nowselect['attrids']);
		}
		
		$productlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."product where goodsid=".$id." and shopid=".$shopid."");
		$data['productlist'] = $productlist;
		// print_r($data['productlist']);
		// print_r($foodshow);
	 
		 $pageinfo = new page();
 	  $pageinfo->setpage(intval(IReq::get('page')),5);  
		$temparray = $this->mysql->getarr("select * from  ".Mysite::$app->config['tablepre']."comment where is_show = 0 and  goodsid=".$id." and shopid=".$shopid." order by addtime desc    limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()." ");
		
		$commentlist = array();
		foreach($temparray as $key=>$value){
			$memberinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."member where uid = ".$value['uid']." ");
			$value['username'] =$memberinfo['username'];
			$phone = $memberinfo['phone'];
			$value['phone'] = substr($phone,0,3).'****'.substr($phone,7,4) ;
			
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
			
			
			$commentlist[] = $value;
		}
		$data['commentlist'] = $commentlist;
		
	 $pingjiashuliang  = $this->mysql->counts("select id from  ".Mysite::$app->config['tablepre']."comment where  is_show = 0 and  goodsid=".$id." and shopid=".$shopid." order by addtime desc");
		$pageinfo->setnum($pingjiashuliang);
		$data['pagecontent'] = $pageinfo->getpagebar(); 
 
 
 
		$shuliang = $this->mysql->select_one("select count(id) as zongshu , sum(point) as pointzongshu from  ".Mysite::$app->config['tablepre']."comment where goodsid=".$id." and shopid=".$shopid." order by addtime desc  ");
	 
		$zongshu =  $shuliang['zongshu']; 
		$pointzongshu =  $shuliang['pointzongshu'];
		if($pointzongshu != 0){
			$haoping = round( $zongshu/$pointzongshu,3 )*5*100;
		}else{
			$haoping = 0;
		}
	    $data['haoping'] = $haoping;   // 计算好评率
		Mysite::$app->setdata($data);
	  
	
   }
	
 function showgoodszzz(){		//展示商品详情
   
  	  $psset = Mysite::$app->config['psset'];
	    $locationtype = 0;  
      if(!empty($psset)){
	      	 $psinfo = unserialize($psset);
	      	 $locationtype = $psinfo['locationtype']; 
	    }
	    $id = intval(IFilter::act(IReq::get('id'))); 

      $goodsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where   id =".$id." and shoptype = 0 order by id asc limit 0,100");  
      if(empty($goodsinfo)){
      	$link = IUrl::creatUrl('shop/index'); 
      	$this->message('数据获取失败',$link);
      }
      $shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where b.id = ".$goodsinfo['shopid']."    order by sort asc limit 0,100");  
	
  	$this->pageCls->setpage(intval(IReq::get('page')),5);
                 $commentlist = $this->mysql->getarr("select a.*,b.username,b.phone,b.logo,c.name from ".Mysite::$app->config['tablepre']."comment as a left join  ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid left join ".Mysite::$app->config['tablepre']."goods as c on a.goodsid = c.id  where a.shopid=".$shopinfo['id']." and a.goodsid = ".$id." and a.is_show  =0 order by a.id desc   limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."");
				$data['commentlist'] = array();
				foreach($commentlist as $key=>$value){
					
								$zongpoint = $value['point'];
								$zongpointcount = $value['pointcount'];
								if($zongpointcount != 0 ){
									$shopstart = intval( round($zongpoint/$zongpointcount) );
								}else{
									$shopstart= 0;
								}
					$value['point'] = 	$shopstart;	
					$data['commentlist'] =$value;
					
				}
			 
			 $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."comment   where shopid=".$goodsinfo['shopid']."  and is_show  =0 ");
                  $this->pageCls->setnum($shuliang);
              $data['pagecontent'] = $this->pageCls->ajaxbar('getPingjia');  //商品评论
#print_r($data['commentlist']);

	 $data['findtype'] = 0;
	    if(empty($shopinfo)){
	      	 $shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id    order by sort asc limit 0,100");  
	     	  $data['findtype'] = 1;
	    }
		 $data['catinfo'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodstype where   id =".$goodsinfo['typeid']."  order by orderid asc limit 0,100");  
		 $data['hoptgoods'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where   typeid =".$data['catinfo']['id']." and shopid = ".$goodsinfo['shopid']."  and is_hot =1 order by id asc limit 0,100"); 
      
		
		#print_r(  $data['hoptgoods']  );

	   $data['goodsinfo']	= $goodsinfo;
	   $data['shopinfo']	= $shopinfo;
	   $data['defaultgoods'] = '/upload/images/default.jpg';
	   
	   	
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



	  $data['scoretocost'] = Mysite::$app->config['scoretocost'];

	   
       Mysite::$app->setdata($data);
  }
	function addcollect(){
		$this->checkmemberlogin();
		$collectid = intval(IReq::get('collectid'));
		$type = intval(IReq::get('type'));
		if(empty($this->member['uid']))$this->message('member_nologin');
		if(empty($collectid))$this->message('collect_err');
		$data['collecttype'] = empty($type)? 0:1;
		$data['collectid'] = $collectid;
		$data['uid'] = $this->member['uid'];
		//收藏商品
		if($data['collecttype'] == 1)
		{
			$goodsinfo = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."goods where id=".$collectid."  ");  
			if(empty($goodsinfo)) $this->message('collect_err');
		}else{
		//收藏店铺 shopuid
		  $goodsinfo = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."shop where id=".$collectid."  ");  
		 if(empty($goodsinfo)) $this->message('collect_err');
		}
		
		 $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."collect where uid=".$data['uid']." and collectid=".$data['collectid']."  and collecttype = '".$data['collecttype']."' ");  
		 if(!empty($checkinfo))
		 {
		 		$this->success('success');
		 }
		 $data['shopuid'] = $goodsinfo['uid'];
		 $this->mysql->insert(Mysite::$app->config['tablepre'].'collect',$data);  
	   $this->success('success');
	}
	function delcollect()
	{
		$this->checkmemberlogin();
		$collectid = intval(IReq::get('collectid'));
		$type = intval(IReq::get('type'));
		if(empty($this->member['uid']))$this->message('member_nologin');
		if(empty($collectid))$this->message('collect_empty');
		$collecttype = empty($type)? 0:1;
		$this->mysql->delete(Mysite::$app->config['tablepre'].'collect',"uid='".$this->member['uid']."'  and collectid = '".$collectid."' and collecttype ='".$collecttype."'");   
		$this->success('success');
	}    
	function makeorder(){ 
	     $info['shopid'] = intval(IReq::get('shopid'));//店铺ID
		 $info['remark'] = IFilter::act(IReq::get('remark'));//备注
		 $info['paytype'] = IFilter::act(IReq::get('paytype'));//支付方式
		 $info['dikou'] = intval(IReq::get('dikou'));//抵扣金额
	 	 $info['username'] = IFilter::act(IReq::get('username')); 
		 $info['mobile'] = IFilter::act(IReq::get('mobile'));
		 $info['addressdet'] = IFilter::act(IReq::get('addressdet'));
		 $info['senddate'] =  IFilter::act(IReq::get('senddate'));
		 $info['minit'] = IFilter::act(IReq::get('minit')); 
		 $info['juanid']  = intval(IReq::get('juanid'));//优惠劵ID
		 $info['ordertype'] = 1;//订单类型 
		 $peopleNum = IFilter::act(IReq::get('peopleNum'));  
		 $info['othercontent'] ='';//empty($peopleNum)?'':serialize(array('人数'=>$peopleNum)); 
		 
		 
		  $info['buyerlng'] = IFilter::act(IReq::get('buyerlng')); 
		  $info['buyerlat'] = IFilter::act(IReq::get('buyerlat')); 
		  
		 if(empty($info['shopid'])) $this->message('shop_noexit');
	 
		 $smardb = new newsmcart();
		 $carinfo = array();
		 if($smardb->setdb($this->mysql)->SetShopId($info['shopid'])->OneShop()){
			   $carinfo = $smardb->getdata(); 
		 }else{ 
		     $this->message($smardb->getError());
		 } 
		 
		 if(count($carinfo['goodslist']) == 0)$this->message('购物车商品为空'); 
		 
		 
		 if($carinfo['shopinfo']['shoptype'] == 1){
		 	  $shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");   
	   }else{
	  		 $shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");   
	 	 } 
	   
		 if(empty($shopinfo))   $this->message('shop_noexit');
		 $checkps = 	 $this->pscost($shopinfo,$info['buyerlng'],$info['buyerlat']); 
		 
	 
 	    if($checkps['canps'] != 1) $this->message('shop_notinps');  
		 $info['cattype'] = 0;//
		  if(empty($info['username'])) 		  $this->message('emptycontact'); 
	  	if(!IValidate::suremobi($info['mobile']))   $this->message('errphone'); 
		 if(empty($info['addressdet'])) $this->message('emptyaddress');
	   $info['userid'] = !isset($this->member['score'])?'0':$this->member['uid'];
	    if(Mysite::$app->config['allowedguestbuy'] != 1){
	     if($info['userid']==0) $this->message('member_nologin');
	   }
	   
	   $info['ipaddress'] = "";
	   $ip_l=new iplocation(); 
     $ipaddress=$ip_l->getaddress($ip_l->getIP());  
     if(isset($ipaddress["area1"])){
         if(function_exists(mb_convert_encoding)){
             $info['ipaddress']  = $ipaddress['ip'].mb_convert_encoding($ipaddress["area1"],'UTF-8','GB2312');//('GB2312','ansi',);
         }else if(function_exists(iconv)){
             $info['ipaddress']  = $ipaddress['ip'].iconv('GB2312',$ipaddress["area1"],'UTF-8');//('GB2312','ansi',);
         }else{
             $info['ipaddress']='0';
         }
	   } 
	  //area1 二级地址名称	area2 三级地址名称	area3
	  
	  $nowID= intval(ICookie::get('myaddress'));
	  if(!empty($nowID)){
	 // if(empty($nowID)) $this->message('area_empty');
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
	 }else{
	   $info['areaids'] = '';
	 }
	  if($shopinfo['is_open'] != 1) $this->message('店铺暂停营业'); 
	   $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$info['minit'],$shopinfo['befortime']); 
	   if($tempdata['is_opentime'] ==  2) $this->message('选择的配送时间段，店铺未设置');
	   if($tempdata['is_opentime'] == 3) $this->message('选择的配送时间段已超时');
	   $info['sendtime'] = $tempdata['is_posttime'];
	   $info['postdate'] = $tempdata['is_postdate'];
	   $info['addpscost'] =  $tempdata['cost'];
	   
	 
	  
	    
	  $checksend = Mysite::$app->config['ordercheckphone'];
    if($checksend == 1){
    	  if(empty($this->member['uid'])){
				$mycode = IFilter::act(IReq::get('phonecode'));
				$phonecls = new phonecode($this->mysql,6,$info['mobile']); 
				if($phonecls->checkcode($mycode)){  
				}else{
						$this->message($phonecls->getError());
				} 
    	  }
    }
    $paytype = $info['paytype'] == 1?1:0;
	 
 
	 if($paytype === 'undefined') 	$this->message("未开启任何支付方式，请联系管理员！");
    
	   $info['shopinfo'] = $shopinfo;
	   $info['allcost'] = $carinfo['sum'];
	   $info['bagcost'] = $carinfo['bagcost'];
	   $info['allcount'] = $carinfo['count'];
	   $info['shopps'] = $checkps['pscost']; 
	   $info['goodslist']   = $carinfo['goodslist'];
	   $info['pstype'] = $checkps['pstype'];
	   $info['cattype'] = 0;//表示不是预订 
	   //检测库存
		 foreach($info['goodslist'] as $key=>$value){  
	         if($value['stock'] < $value['count']){
				 $this->message('商品库存不足');
			 }
	     }
        $info['platform']=1;
	   $info['is_goshop']=0;
	    if($shopinfo['limitcost'] > $info['allcost']) $this->message('商品总价低于最小起送价'.$shopinfo['limitcost']);   
	   $orderclass = new orderclass();
	   $orderclass->makenormal($info);
	   $orderid = $orderclass->getorder();
	   if($info['userid'] ==  0){ 
	  	   ICookie::set('orderid',$orderid,86400);
	   }
	   
	   
	   $smardb->DelShop($info['shopid']);
		$this->success($orderid);  
	 } 
	//添加商品分类  
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
   function collect(){
   	$this->checkmemberlogin();
   	$pageinfo = new page();
    $data['shoptypelist']= $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype order by orderid asc ");  
	  $pageinfo->setpage(intval(IReq::get('page')),100);   
		
		$data['list'] = $this->mysql->getarr("select co.collectid,co.orderid,sh.*,sf.* from ".Mysite::$app->config['tablepre']."collect as co left join   ".Mysite::$app->config['tablepre']."shop as sh on sh.id = co.collectid     left join   ".Mysite::$app->config['tablepre']."shopfast as sf on sf.shopid = co.collectid    where co.uid = '".$this->member['uid']."' and co.collecttype=0 order by co.orderid asc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");   
		$shuliang  = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."collect where uid = '".$this->member['uid']."' and collecttype=0");
		$pageinfo->setnum($shuliang);
		$data['pagecontent'] = $pageinfo->getpagebar(); 
		 Mysite::$app->setdata($data); 
  }
  function savepxmyF(){
  	$this->checkmemberlogin();
		$data['orderid'] =intval(IReq::get('pxid'));	 
		$collecttype = intval(IReq::get('collecttype'));  
		$collectid  = intval(IReq::get('collectid'));
		if($collectid < 1) $this->message('collect_empty');
		if($collecttype > 1) $this->message('collect_empty');
		$this->mysql->update(Mysite::$app->config['tablepre'].'collect',$data,"collectid='".$collectid."' and uid='".$this->memberinfo['uid']."' and collecttype=".$collecttype." "); 
	  $this->success('success');
	}
	function gotocollect(){
		$this->checkmemberlogin();
		  $collectid = intval(IReq::get('collectid'));	
		  $checkshop = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid = ".$collectid."");
		  if(empty($checkshop)){
		  	$checkshop2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid = ".$collectid."");
		  	if(empty($checkshop2)){
		  	  $this->message('shop_noexit');
		  	}else{
		  			$link = IUrl::creatUrl('market/shopshow/id/'.$collectid);
		    $this->message('',$link); 
		  	}
		  }else{
		  	$link = IUrl::creatUrl('shop/index/id/'.$collectid);
		    $this->message('',$link);
		  } 
	}
	function checkshopphone()
	{
		 $uname = IFilter::act(IReq::get('uname'));       
	    $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where maphone='".$uname."' ");
	    if(empty($userinfo))
	    {
	       $this->success('success');
	    }else{ 
	    	 $this->message('exitphone');
	    } 
	}
	function checkshopname()
	{
		  $uname = IFilter::act(IReq::get('uname'));       
	    $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where shopname='".$uname."' ");
	     if(empty($userinfo))
	    {
	       $this->success('success');
	    }else{ 
	    	 $this->message('shop_exit');
	    } 
	}
	function openshop(){
		if(empty($this->member['uid'])){
			  $link = IUrl::creatUrl('member/index');
		  $this->message('member_nologin',$link);
		 
		}
		$this->setstatus();
  }
		function saveopen()
		{  
			if(empty($this->member['uid'])){
				  $link = IUrl::creatUrl('member/index');
			  $this->message('member_nologin',$link);
			 
			}
		 	 $maphone = IFilter::act(IReq::get('maphone'));  
	     $shopname = IFilter::act(IReq::get('shopname')); 
	     $address = IFilter::act(IReq::get('address'));  
	     $shoptype =  IFilter::act(IReq::get('shoptype'));
		 $ruzhutype = intval(IReq::get('ruzhutype')); 
		 $qiyeimgurl = IFilter::act(IReq::get('qiyeimgurl'));
		 $zmimgurl = IFilter::act(IReq::get('zmimgurl'));
		 $fanimgurl = IFilter::act(IReq::get('fanimgurl'));
		 $foodimgurl = IFilter::act(IReq::get('foodimgurl'));
		 $jkimgurl = IFilter::act(IReq::get('jkimgurl'));
		 $sqimgurl = IFilter::act(IReq::get('sqimgurl'));
		 $qtshuoming = IFilter::act(IReq::get('qtshuoming'));
		 $admin_id = empty($this->CITY_ID)?0:$this->CITY_ID;
		 if($ruzhutype == 1){
			 if(empty($qiyeimgurl))  $this->message('请上传营业执照');
		 }
		 
		 
	     if(!(IValidate::phone($maphone)))$this->message('errphone');   
	     if(!(IValidate::len($shopname,4,50)))$this->message('shop_shopnamelenth');   
	     if(!(IValidate::len($address,4,50)))$this->message('shop_addresslenth'); 
	     $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where uid='".$this->member['uid']."' ");
	     if(!empty($userinfo))$this->message('shop_exit'); 
	     $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where maphone='".$maphone."' ");
	     if(!empty($userinfo))$this->message('exitphone');    
	     $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where shopname='".$shopname."' "); 
	     if(!empty($userinfo))$this->message('shop_repeatname');  
	     $arr['shopname'] = $shopname;
	     $arr['maphone'] = $maphone;
	     $arr['uid'] = $this->member['uid'];
	     $arr['address'] = $address;
	     $arr['addtime'] = time(); 
	     $arr['is_open'] = '0'; 
	     $arr['shoptype'] = $shoptype;
	     $nowday = 24*60*60*365;
		   $data['endtime'] = time()+$nowday;
		   
		 $arr['admin_id'] = $admin_id;
		 $arr['ruzhutype']  = $ruzhutype;
		 $arr['shoplicense']  = $qiyeimgurl;

	     $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$arr);  
	    $this->success('success');
		}
	
	
	
	function saveshangjia()
	{  
	$username = IFilter::act(IReq::get('username'));  	
	$mobile = IFilter::act(IReq::get('mobile'));  
	$qq = IFilter::act(IReq::get('qq'));  
     $resname = IFilter::act(IReq::get('resname')); 
     $addr = IFilter::act(IReq::get('addr'));    
	if(empty($username) || $username=="请输入您的姓名" )   $this->message('姓名不能为空！');
	if(!(IValidate::len($username,1,50)))$this->message('member_addresslength');	
	if(empty($mobile) || $mobile=="请输入您的手机号"  )   $this->message('手机号不能为空！');
	 if(!(IValidate::phone($mobile)))$this->message('errphone');  	
	if(empty($resname)  || $resname=="请输入店铺名称" )   $this->message('店铺名称不能为空！');
	if(!(IValidate::len($resname,1,50)))$this->message('shop_shopnamelenth');	
	if(empty($addr)  || $addr=="店铺的详细地址" )   $this->message('店铺的详细地址不能为空！');
	 if(!(IValidate::len($addr,1,255)))$this->message('shop_addresslenth');     	
			if(Mysite::$app->config['allowedcode'] == 1)
		     {
		    	   $Captcha = IFilter::act(IReq::get('Captcha'));
				   if(empty($Captcha) || $Captcha=="输入验证码" )   $this->message('验证码不能为空！');
		    	  if($Captcha != ICookie::get('Captcha')) 	$this->message('member_codeerr'); 
		     }		   
	
	  $arr['username'] = $username;
     $arr['phone'] = $mobile; 
    
	 if(empty($qq) || $qq == "请输入您的QQ(选填)"){
		$arr['qq'] = '' ;   
	 }else{		 
		 $arr['qq'] = $qq;		 
	 }
	 	 
     $arr['shopname'] = $resname;
     $arr['shopaddress'] = $addr;
	  $arr['addtime'] = time(); 
     $arr['is_pass'] = '0'; 	 
	 $this->mysql->insert(Mysite::$app->config['tablepre'].'messages',$arr);  
	 $this->success('提交成功，请等待管理员审核!');
	}
	
	
	
	
	
	
	
	function mangeshop(){
		$this->checkmemberlogin();
	    $id =  intval(IFilter::act(IReq::get('id')));
	    $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where   is_pass=1 and uid=".$this->member['uid']." ");
	    
	    $link = IUrl::creatUrl('member/index');
	    if(empty($shopinfo)) $this->message('shop_noexit',$link);
	    $link =  IUrl::creatUrl('shopcenter/useredit'); ///http://192.168.0.104/index.php?ctrl=&action=;
	    ICookie::set('adminshopid',$shopinfo['id'],86400); 
	    $this->success('',$link);
	}
	  
	 
	function setstatus(){
	    $data['shoptype'] = array('0'=>'外卖','1'=>'超市');
	   Mysite::$app->setdata($data);
	} 
	function wx_code(){
		 $weixindir = hopedir.'/plug/pay/weixin/'; 
	     $goodsid = intval(IReq::get('goodsid')); 
		 if(empty($goodsid)){
			 $this->message('商品错误');
		 }
		 $goodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where   id=".$goodsid." ");
		 if(empty($goodsinfo)){
			 $this->message('商品不存在');
		 }
		 
		
		 if(empty($goodsinfo['wx_url'])){ 
			  require_once $weixindir."lib/WxPay.Api.php";
			  require_once $weixindir."WxPay.NativePay.php"; 
			  require_once $weixindir."lib/WxPay.Config.php";
			  $notify = new NativePay();
			  $url1 = $notify->GetPrePayUrl($goodsinfo['id']); 
			  $udata['wx_url'] = urlencode($url1);  
			  $this->mysql->update(Mysite::$app->config['tablepre'].'goods',$udata," id = ".$goodsinfo['id']);  
			  $this->success(urlencode($url1));
		 }else{ 
			  $this->success($goodsinfo['wx_url']);
		 }		 
		
	}
}
?>