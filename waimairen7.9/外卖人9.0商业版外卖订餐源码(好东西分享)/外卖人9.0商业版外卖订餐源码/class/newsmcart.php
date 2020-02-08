<?php
/**
 *  @brief 购物车模块
 *    array(
              'goods'=>array(   //此处为单规格商品
			  商品id1=>数量,
                          商品ID2=>数量,
			  ),
              'ggoods'=>array(  //此处为有多规格时存方多规格id 
                           规格ID1=>数量,
                           规格id2=>数量,
			  ),
              'shopg'=>array(
                            店铺id1=>商品id集,
			)
              'shopgg'=>array(
                            店铺1=>规格id1的商品集
			)
 */ 
Class newsmcart{   
    private $shoptype;//私有变量店铺类型
	private $catstruct;//购物车结构
	private $cartname;//购物车名称
	private $cartnamepre ="ghcart";//购物车名称前缀
	private $goodstype;
	private $shopid;
	private $defualt_struct =  array('goods'=>array(),'ggoods'=>array(),'shopg'=>array(),'shopgg'=>array()); //默认的购物车结构 
	private $carinfo;//转换后的购物车结构
	private $mysql;
	private $errId='no_error';
	
	public function setdb($mysql){
		$this->mysql = $mysql;
		return $this;
	}
	
	public function SetShopId($shopid){
		$this->shopid = $shopid;
		return $this;
	}
	//格式化价格数据，保留两位小数
	public function formatcost($cost,$num){
	 	if($cost > 0){
			$cost = str_replace(',','',$cost);//先去掉','
			$cost = number_format($cost,$num);//再格式化，保留两位小数
			$cost = str_replace(',','',$cost);//再去掉','
		}else{
			$cost = '0.00';
		}
        return $cost;		
	}  
	public function SetGoodsType($goodstype){//1表示普通商品 2有规格商品
		$this->goodstype=$goodstype;
		return $this;
	}
	private function init(){
		// if($this->shoptype == null){
			// $this->errId = '未初始化店铺类型';
			// return false;
		// }else{
			$this->cartname = $this->cartnamepre.'_'; 
			$this->carinfo = $this->getMyCartStruct();
			// $this->setMyCart($this->carinfo); 
			return true;
		// }
	}
	private function getMyCartStruct()
	{ 
	 	$cartValue = ICookie::get($this->cartname); 
		if($cartValue == null){
			return $this->defualt_struct;
		}else{
			$cartValue = JSON::decode(str_replace(array('&','$'),array('"',','),$cartValue));
			return $cartValue;
		}
	}
	public function setMyCart($goodsInfo)
	{ 
		$tgoodsInfo = str_replace(array('"',','),array('&','$'),JSON::encode($goodsInfo)); 
	    ICookie::set($this->cartname,$tgoodsInfo,'98400'); 
		return true;
	} 
	private function onegoods($goodsid){
		if($this->goodstype == 1){
			$data = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where id=".$goodsid."  ");

			//2016/12/27新增
			$cxGoodsInfo = $this->goodscx($data);
			$data['is_cx'] = $cxGoodsInfo['is_cx'];
			$data['cxnum'] = $cxGoodsInfo['cxnum'];
			$data['cxstime1'] = $cxGoodsInfo['cxstime1'];
			$data['cxetime1'] = $cxGoodsInfo['cxetime1'];
			$data['cxstime2'] = $cxGoodsInfo['cxstime2'];
			$data['cxetime2'] = $cxGoodsInfo['cxetime2'];
		}else{
			$data = $this->mysql->select_one("select *,stock as count from ".Mysite::$app->config['tablepre']."product where id=".$goodsid."  ");

			//2016/12/27新增
			$gid = empty($data['goodsid'])?0:$data['goodsid'];
			$goodsis_cx = $this->mysql->select_one("select id,is_cx from ".Mysite::$app->config['tablepre']."goods where id=".$gid."  ");
			$data['id'] = $goodsis_cx['id'];
			$data['pid'] = $gid;
			$data['is_cx'] = $goodsis_cx['is_cx'];
			$cxGoodsInfo = $this->goodscx($data);
			$data['is_cx'] = $cxGoodsInfo['is_cx'];
			$data['cxnum'] = $cxGoodsInfo['cxnum'];
			$data['cxstime1'] = $cxGoodsInfo['cxstime1'];
			$data['cxetime1'] = $cxGoodsInfo['cxetime1'];
			$data['cxstime2'] = $cxGoodsInfo['cxstime2'];
			$data['cxetime2'] = $cxGoodsInfo['cxetime2'];
		}
		return $data;
	}
	private function checkgoods($goodsid){
		
		
	}
	//检测是否是团购商品
	private function  isgroupon($goodsid){
		return true; 
	}
	//添加商品到购物车
	public function AddGoods($goodsid){////若按周限时商品则此处需增加 判断

		if($this->init()){
			if($this->goodstype == null){
				$this->errId = '未初始化商品类型';
				return false;
			}
			$goodsid = intval($goodsid);
			$goodsinfo = $this->onegoods($goodsid);			
			if(empty($goodsinfo)){
				$this->errId = '商品不存在';
				return false;
			} 
			if($this->goodstype == 1){//正常商品
				 
			    if($goodsinfo['have_det'] == 1){
					$this->errId = '请选择规格添加商品';
					return false;
				}
				  
				$checkstock = 0;
				 
				if(isset($this->carinfo['goods'][$goodsid])){
					$checkstock = $this->carinfo['goods'][$goodsid]+1;
					if($checkstock > $goodsinfo['count']){
							$this->errId = '商品库存不足';
							return false;
						}	
					if($goodsinfo['is_cx'] == 1){	
                        if($checkstock > $goodsinfo['cxnum'] &&  $goodsinfo['cxnum']>0){
							$this->errId = '每单限购'.$goodsinfo['cxnum'].'份';
							return false;
						}						
					} 
					
				}else{
					if($goodsinfo['count'] < 1){
						$this->errId = '商品库存不足';
						return false;
					}
					$checkstock = 1;
				}
				$this->carinfo['goods'][$goodsid] = $checkstock;
				if(!isset($this->carinfo['shopg'][$goodsinfo['shopid']])){
					$this->carinfo['shopg'][$goodsinfo['shopid']][] = $goodsid;
				}elseif(!in_array($goodsid,$this->carinfo['shopg'][$goodsinfo['shopid']])){
					$this->carinfo['shopg'][$goodsinfo['shopid']][] = $goodsid;
				}
				$this->setMyCart($this->carinfo);
				//允许添加  grouponid
			}else{//规格id1
			    $checkstock = 0;
				$sonids = array();
				$sonids = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."product where goodsid=".$goodsinfo['pid']."  ");                     
			    $songnum = 0; 
				//先将该商品下的子商品在购物车中的数量之和求出
				foreach($sonids as $k=>$v){
					$songnum = $songnum + $this->carinfo['ggoods'][$v['id']];	
				}
				//因为每执行一次函数  就要增加一次商品数量  故购物车中的子商品的数量和还需加1  求出当前购物车中该商品的子商品总数
				$songnum = $songnum + 1;
				//然后拿子商品数量之和 去和限购数作比较   若大于限购数  则不能再添加
				if(isset($this->carinfo['ggoods'][$goodsid])){
					$checkstock = $this->carinfo['ggoods'][$goodsid]+1;
					if($checkstock > $goodsinfo['count']){
							$this->errId = '商品库存不足';
							return false;
						}
					if($goodsinfo['is_cx'] == 1){
						if($songnum > $goodsinfo['cxnum'] &&  $goodsinfo['cxnum']>0){
							$this->errId = '每单限购'.$goodsinfo['cxnum'].'份';
							return false;
						}
						
					} 
 				}else{ 
				    $checkstock = 1;
					if($goodsinfo['count'] < 1){
						$this->errId = '商品库存不足';
						return false;
					}
					
					
					if($goodsinfo['is_cx'] == 1){
						if($songnum > $goodsinfo['cxnum'] &&  $goodsinfo['cxnum']>0){
							$this->errId = '每单限购'.$goodsinfo['cxnum'].'份';
							return false;
						}
						
					} 
					
				}
				$fgoods =$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods where id=".$goodsinfo['goodsid']."  ");
				//$fgoods = $this->rdb->ClearSet()->Table('goods')->Where(array('id'=>$goodsinfo['goodsid']))->One();
				if(empty($fgoods)){
					$this->errId = '商品规格对应商品不存在';
					return false;
				}
				$this->carinfo['ggoods'][$goodsid] = $checkstock;
				if(!isset($this->carinfo['shopgg'][$goodsinfo['shopid']])){ 
					$this->carinfo['shopgg'][$goodsinfo['shopid']][] = $goodsid;
				}elseif(!in_array($goodsid,$this->carinfo['shopgg'][$goodsinfo['shopid']])){ 
					$this->carinfo['shopgg'][$goodsinfo['shopid']][] = $goodsid;
				}
				$this->setMyCart($this->carinfo); 
			} 
			return true;
		}else{
			return false;
		} 
	}

	//删除商品从购物车
	public function DelGoods($goodsid){
		if($this->init()){
			if($this->goodstype == null){
				$this->errId = '未初始化商品类型';
				return false;
			}
			$goodsid = intval($goodsid); 
			$goodsinfo = $this->onegoods($goodsid);
			if($this->goodstype == 1){//正常商品
				$checkstock = 0;
				if(isset($this->carinfo['goods'][$goodsid])){ 
						unset($this->carinfo['goods'][$goodsid]);
						if(!empty($goodsinfo)){
							$newdata = array();
							foreach($this->carinfo['shopg'][$goodsinfo['shopid']] as $key=>$value){
								if($goodsid != $value){
									$newdata[] = $value;
								}
							}
							$this->carinfo['shopg'][$goodsinfo['shopid']] = $newdata; 
							if(count($newdata)==0){
								unset($this->carinfo['shopg'][$goodsinfo['shopid']]);
							}
						} 
						$this->setMyCart($this->carinfo);  
				}  
				//允许添加  grouponid
			}else{//规格id1
			    $checkstock = 0;
				if(isset($this->carinfo['ggoods'][$goodsid])){ 
						unset($this->carinfo['ggoods'][$goodsid]);
						if(!empty($goodsinfo)){
							$newdata = array();
							foreach($this->carinfo['shopgg'][$goodsinfo['shopid']] as $key=>$value){
								if($goodsid != $value){
									$newdata[] = $value;
								}
							}
							$this->carinfo['shopgg'][$goodsinfo['shopid']] = $newdata; 
							if(count($newdata)==0){
								unset($this->carinfo['shopgg'][$goodsinfo['shopid']]);
							}  
						}
						$this->setMyCart($this->carinfo);  
				}  
			} 
			return true;
		}else{
			return false;
		}
	}
	//商品在购物车中减少数量
	public function DownGoods($goodsid){
		if($this->init()){
			if($this->goodstype == null){
				$this->errId = '未初始化商品类型';
				return false;
			}
			$goodsid = intval($goodsid); 
			$goodsinfo = $this->onegoods($goodsid);
			if($this->goodstype == 1){//正常商品
				$checkstock = 0;
				if(isset($this->carinfo['goods'][$goodsid])){
					$checkstock = $this->carinfo['goods'][$goodsid]-1;
					if($checkstock > 0){
						 $this->carinfo['goods'][$goodsid] = $checkstock;
						 $this->setMyCart($this->carinfo); 
					}else{
						unset($this->carinfo['goods'][$goodsid]);
						if(!empty($goodsinfo)){
							$newdata = array();
							foreach($this->carinfo['shopg'][$goodsinfo['shopid']] as $key=>$value){
								if($goodsid != $value){
									$newdata[] = $value;
								}
							}
							$this->carinfo['shopg'][$goodsinfo['shopid']] = $newdata; 
							if(count($newdata)==0){
								unset($this->carinfo['shopg'][$goodsinfo['shopid']]);
							}
						} 
						$this->setMyCart($this->carinfo); 
					} 
				}  
				//允许添加  grouponid
			}else{//规格id1
			    $checkstock = 0;
				if(isset($this->carinfo['ggoods'][$goodsid])){
					$checkstock = $this->carinfo['ggoods'][$goodsid]-1;
					if($checkstock > 0){
						 $this->carinfo['ggoods'][$goodsid] = $checkstock;
						 $this->setMyCart($this->carinfo); 
					}else{
						unset($this->carinfo['ggoods'][$goodsid]);
						if(!empty($goodsinfo)){
							$newdata = array();
							foreach($this->carinfo['shopgg'][$goodsinfo['shopid']] as $key=>$value){
								if($goodsid != $value){
									$newdata[] = $value;
								}
							}
							$this->carinfo['shopgg'][$goodsinfo['shopid']] = $newdata; 
							if(count($newdata)==0){
								unset($this->carinfo['shopgg'][$goodsinfo['shopid']]);
							}
						}
						$this->setMyCart($this->carinfo); 
					} 
				}  
			} 
			return true;
		}else{
			return false;
		}
	}
	//清除某店铺商品从购物车中
	public function DelShop(){
		if($this->init()){
			if($this->shopid == null){
				$this->errId = '未初始化店铺';
				return false;
			}
			if(isset($this->carinfo['shopg'][$this->shopid])){
				foreach($this->carinfo['shopg'][$this->shopid] as $key=>$value){
					unset($this->carinfo['goods'][$value]);
				}
				unset($this->carinfo['shopg'][$this->shopid]);
			}
			if(isset($this->carinfo['shopgg'][$this->shopid])){
				foreach($this->carinfo['shopgg'][$this->shopid] as $key=>$value){
					unset($this->carinfo['ggoods'][$value]);
				} 
				unset($this->carinfo['shopgg'][$this->shopid]);
			}
			$this->setMyCart($this->carinfo);  
			return true;
		}else{
			return false;
		}
		
		
		
	}
	//清除某类型下的所有购物车
	public function ClearCart(){
			if($this->init()){
		$this->carinfo =$this->defualt_struct;
		 
		$this->setMyCart($this->carinfo);  
	   return true;
			}
	}
	public function FindInproduct($goodsid){
		if($this->init()){   
		    //print_r($this->carinfo);  
					if(!isset($this->carinfo['shopgg'][$this->shopid])){
						return null;
					}
					$tempwhere = " shopid = '".$this->shopid."'  and id in(".join(',',$this->carinfo['shopgg'][$this->shopid]).") and goodsid=".$goodsid." " ;
					$havein = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."product  where ".$tempwhere." "); 
					if(!empty($havein)){
						$havein['count'] = $this->carinfo['ggoods'][$havein['id']];
					}
					return $havein; 
		}else{
			return null;
		}
	}
	
	public function productone($productid){
		if($this->init()){   
		    //print_r($this->carinfo); 
			
			if($this->goodstype == 1){//正常商品
				if(!isset($this->carinfo['shopg'][$this->shopid])){
					return 0;
				}  
				if(isset( $this->carinfo['goods'][$productid])){
					return  $this->carinfo['goods'][$productid];
				}else{
					return 0;
				}
			
			}else{ 
				if(!isset($this->carinfo['shopgg'][$this->shopid])){
					return 0;
				}  
				if(isset( $this->carinfo['ggoods'][$productid])){
					return  $this->carinfo['ggoods'][$productid];
				}else{
					return 0;
				}
			}
			 
				 
			 
		}else{
			return 0;
		}
	}
	
	//获取某个店铺类型下所有的购物车信息  获取店铺类型下的商品全信息
	public function ShopList(){
		if($this->init()){   
			$tempinfo = $this->carinfo['shopg'];
			$shpids = array_keys($this->carinfo['shopg']);
			$shpids2 =   array_keys($this->carinfo['shopgg']);
			$shopids = array_merge($shpids,$shpids2);
			 
			$shopids = array_flip(array_flip($shopids));  
			$backdata = array();
			
			foreach($shopids as $key=>$value){
				// 完整内容 	 
				if($this->SetShopId($value)->OneShop()){
					 $newdata  = $this->getdata(); 
					 $newdata['shopinfo']  = $this->mysql->select_one("select id,shopname,shoplogo,shortname,shoptype from ".Mysite::$app->config['tablepre']."shop   where id =  ".$value."   ");
					// $newdata['shopinfo']  = $this->rdb->ClearSet()->Table('shop')->Select(array('id','shopname','shoplogo','point','pointcount'))->Where(array('id'=>$value))->One();
				    
					 $backdata[] = $newdata;
					 
				}
			}
			$this->bkdata = $backdata;  
			return true;
		}else{
			return false;
		}
		
		
	}
	//获取某个店铺类型下所有的购物车统计 信息   店铺名 商品总数   商品总价的样式
	public function ShopTJList(){
		if($this->init()){  
			$tempinfo = $this->carinfo['shopg'];
			$shpids = array_keys($this->carinfo['shopg']);
			$shpids2 =   array_keys($this->carinfo['shopgg']);
			$shopids = array_merge($shpids,$shpids2);
			$shopids = array_flip(array_flip($shopids));  
			$backdata = array(); 
			foreach($shopids as $key=>$value){
				// 完整内容 	  
				if($this->SetShopId($value)->OneShop()){
					 $newdata['shopinfo']  = $this->mysql->select_one("select id,shopname,shoplogo,shortname,shoptype,is_open,starttime from ".Mysite::$app->config['tablepre']."shop   where id =  ".$value."   ");
				 
					 $tempinfo = $this->getdata();
					 $newdata['sum'] = $tempinfo['sum'];
					 $newdata['count'] = $tempinfo['count'];
					 $backdata[] = $newdata;
				}
			}
			$this->bkdata = $backdata;  
			return true;
		}else{
			return false;
		}
		
	}
	//获取某个店铺类型下某个店铺的购物车信息
	public function OneShop(){
		if($this->init()){
			if($this->shopid == null){
				$this->errId = '未初始化店铺';
				return false;
			} 
		 
			
		    $backdata = array('goodslist'=>array(),'sum'=>0,'count'=>0,'bagcost'=>0,'shopinfo'=>array());
			 $backdata['shopinfo']  = $this->mysql->select_one("select id,shopname,shoplogo,shortname,shoptype from ".Mysite::$app->config['tablepre']."shop   where id = (".$this->shopid.")  ");
				
		      
			$gglist = array();
			$goodsids = array();
			if(isset($this->carinfo['shopgg'][$this->shopid])){
				 
		  $tempwhere = " shopid = '".$this->shopid."'  and id in (".join(',',$this->carinfo['shopgg'][$this->shopid]).") " ;
		  
		  
		  $listtemp = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."product  where ".$tempwhere." ");
		 
		  $gglist = array(); 
		  foreach($listtemp as $key=>$value){
			  $gglist[$value['goodsid']][] = $value;
		  }
			$goodsids  = array_keys($gglist); 
			} 
			if(isset($this->carinfo['shopg'][$this->shopid])){ 
				 $goodsids = array_merge($this->carinfo['shopg'][$this->shopid],$goodsids);
			}
			$goodslist = array();
			if(count($goodsids)> 0){//若按周限时商品则此处需增加 判断
				$tempwhere = " shopid = '".$this->shopid."'   and id in (".join(',',$goodsids).")   " ;
				 $goodslist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods  where ".$tempwhere." ");
			      
			}
			$goodscxdowncost = 0; 
			foreach($goodslist as $key=>$value){//若增加限时抢购  则需增加标识
				
				if(isset($gglist[$value['id']])){
					$childarray = $gglist[$value['id']];
					$temptc = $value;
					foreach($childarray as $k=>$v){
						$temptc['gg'] = $v;
						$temptc['stock'] = $v['stock'];
						$temptc['count'] = $this->carinfo['ggoods'][$v['id']];
						$temptc['cost'] = $this->formatcost($v['cost'],2); 
						$temptc['img'] = empty($temptc['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($temptc['img']);
					    $temptc['cxinfo'] =  $this->goodscx($value);
					    if(isset($temptc['cxinfo']['is_cx'])&&$temptc['cxinfo']['is_cx'] == 1 ){
							$temptc['cost'] = $this->formatcost( $temptc['cost']*$temptc['cxinfo']['zhekou']/10, 2); 
							$goodscxdowncost += $temptc['count']*($temptc['cxinfo']['oldcost']-$temptc['cost']);
					    }
						$backdata['goodscxdowncost'] = $goodscxdowncost;    
						 
						$backdata['goodslist'][] = $temptc;  
						$backdata['sum'] = $this->formatcost($backdata['sum']+$temptc['count']*$temptc['cost'],2);
						$backdata['count'] = $backdata['count']+$temptc['count'];
						$backdata['bagcost'] = $backdata['bagcost']+$temptc['count']*$temptc['bagcost'];
					} 
				}else{
					$value['stock'] = $value['count'];
					$value['count'] = $this->carinfo['goods'][$value['id']];
					  $value['cxinfo'] = $this->goodscx($value);
						if(isset($value['cxinfo']['is_cx'])&&$value['cxinfo']['is_cx'] == 1 ){
								$value['cost'] = $this->formatcost($value['cxinfo']['cxcost'],2); 
								$goodscxdowncost += $value['count']*($value['cxinfo']['oldcost']-$value['cost']);
						  }
				    $value['img'] = empty($value['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']):getImgQuanDir($value['img']);
					$backdata['goodscxdowncost'] = $goodscxdowncost;    
					$backdata['goodslist'][] = $value;
					$backdata['sum'] = $this->formatcost($backdata['sum']+$value['count']*$value['cost'],2);
					$backdata['count'] = $backdata['count']+$value['count'];
					$backdata['bagcost'] = $backdata['bagcost']+$value['count']*$value['bagcost'];
				}
				
				/****还需排除正在团够的 现实限时抢购****/
				 
				
			}
			$this->bkdata = $backdata; 
			return true;
		}else{
			return false;
		} 
	}
	
	private function goodscx($goodsinfo){

		$newarray = array('cxcost'=>0,'oldcost'=>$goodsinfo['cost'],'zhekou'=>0,'is_cx'=>'0');	
		if($goodsinfo['is_cx'] == 1){
			$cxdata =$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$goodsinfo['id']."  ");
			$newdata = getgoodscx($goodsinfo['cost'],$cxdata);
			 
			$newarray['oldcost'] = $goodsinfo['cost'];
			$newarray['cxcost'] = $newdata['cost'];
			$newarray['zhekou'] = $newdata['zhekou'];
			$newarray['is_cx'] = $newdata['is_cx'];
			//2016/12/27新增
			$newarray['cxnum'] = $cxdata['cxnum'];
			$newarray['cxstime1'] = $cxdata['cxstime1'];
			$newarray['cxetime1'] = $cxdata['cxetime1'];
			$newarray['cxstime2'] = $cxdata['cxstime2'];
			$newarray['cxetime2'] = $cxdata['cxetime2'];
		}
		return  $newarray;
	}
	public function getdata(){
		return $this->bkdata ==null?array():$this->bkdata;
	}
	public function getError()
	{
		return $this->errId;
	}
	
	
	 
}  