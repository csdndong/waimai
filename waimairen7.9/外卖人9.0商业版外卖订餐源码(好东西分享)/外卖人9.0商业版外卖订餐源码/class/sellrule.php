<?php

/**
 * @class sellrule
 * @brief 促销规则
 */
class sellrule
{
    /*2017年9月份升级后  优惠活动涉及到的xiaozu_cxruleset、xiaozu_rule表说明如下：
	1.xiaozu_cxruleset表是对五种促销类型中的支持订单、支持平台、和促销图标做统一化设置
	  字段说明：
      id：类型id  1满赠活动 2满减活动 3折扣活动 4免配送费 5首单立减	   
	  imgurl：活动图标地址
      supportorder：支持订单类型  1支持全部订单 2只支持在线支付订单',
	  supportplat：支持平台 1pc 2微信 3触屏 4app
    2.xiaozu_rule表示对每种类型活动的具体限制
	  controltype：1满赠活动 2满减活动 3折扣活动 4免配送费 5首单立减
	  imgurl ：活动图标
	  supporttype：支持订单类型 1支持全部订单 2只支持在线支付订单
	  supportplatform：支持平台类型 1pc 2微信 3触屏 4app
	  limitcontent：限制金额
	  presenttitle：赠品名称
	  name：活动标题
	  status：启用状态 0关闭 1开始
	  controlcontent：减费用   折扣数
	  limittype: 
	    1不限制
	    2表示指定星期(limittime:周几1,2,3,4,5,6,7) 
	    3自定义日期(开始时间：starttime   结束时间：endtime)
	*/
    private $ruletype = array('1'=>'购物车总价');//网关地址
    private $rulecontrol = array('1'=>'满赠活动','2'=>'满减活动','3'=>'折扣活动','4'=>'免配送费','5'=>'首单立减');    
    private  $shopid = ''; //店铺ID
    private  $cartcost = 0;//购物车总金额
    private  $weekday = '';//当天周几
    private  $maketime = '';//下单时间
    private  $shoptype = '';
    private  $firstorder = 0;
    private  $platform = 0;//1pc端，2微信端，3触屏端，4客户端（安卓苹果）
    private  $paytype = 0;//1货到付款  2在线支付
    private  $rulelist = array();
    private $time = 0;
    //初始化函数
    function __construct(){
        $this->mysql = new mysql_class();
        $this->tablepre = Mysite::$app->config['tablepre'];
    }
    public function setdata($shopid,$cartcost=0,$shoptype='1',$userid=0,$platform=0,$paytype=0,$bagcost=0){//设置规则数据;
        $this->shopid = $shopid;
        $this->cartcost = $cartcost;
        $this->shoptype = $shoptype;
        $this->maketime = time();
        $this->weekday = date('w')==0?7:date('w');
        $this->platform=$platform;
        $this->paytype=$paytype;
		$this->bagcost = $bagcost;
        if($userid > 0){
            $order = $this->mysql->select_one("select * from ".$this->tablepre."order where buyeruid='".$userid."'");
            if(!empty($order)){
                $this->firstorder=1;
            }else{
                $this->firstorder=0;
            }
        }
    }
    public function get_rulelist(){
  	    $list = array();//开启中不限制时间的   或者  限制时间又在时间范围内的
        $listtemp = $this->mysql->getarr("select * from ".$this->tablepre."rule where FIND_IN_SET(".$this->shopid.",shopid)  and status = 1 and ( limittype =1 or ( limittype = 2 and  FIND_IN_SET(".$this->weekday.",limittime) ) or ( limittype = 3 and endtime > ".$this->maketime." and starttime < ".$this->maketime.")) order by id desc ");         
		if( !empty($listtemp) ){
			foreach($listtemp as $key=>$value){
				$value['imgurl'] = getImgQuanDir($value['imgurl']);
				$list[] = $value;
			}
		}
		return $list;
    }
	public function get_ruledet($id){
  	    $list = array();
		$cxdet = array();
        $list = $this->mysql->select_one("select name,imgurl,controltype,limitcontent,controlcontent,supporttype from ".$this->tablepre."rule where id='".$id."' ");
		if($list['controltype']==2){
			$limitarr = explode(',',$list['limitcontent']);
			$controarr = explode(',',$list['controlcontent']);
			arsort($limitarr);//对数组的值从大到小排序			 			  
			foreach($limitarr as $k=>$v){
				$ccost = $this->cartcost + $this->bagcost;
				if($ccost >= $v){
					 $limit = $v;					  
					 $contrl = $controarr[$k];
					 $list['name'] = $list['supporttype'] == 2?'在线支付满'.$limit.'减'.$contrl:'满'.$limit.'减'.$contrl;
					 break;
				}
			}			
		}	
		
		$img = getImgQuanDir($list['imgurl']);
		$det = array('img'=>$img,'name'=>$list['name'],'type'=>$list['controltype']);
		$cxdet[$id] = $det;		 
		return $cxdet;
    }
    //返回规则数据

    //计算最大化数据
    function maxdata($data,$gzdata)
    {    
        
        $makedata = array('downcost'=>0,'shopdowncost'=>0,'cx_shoudan'=>0,'cx_manjian'=>0,'cx_zhekou'=>0,'surecost'=>$this->cartcost,'cxids'=>'','zid'=>array(),'gzdata'=>$gzdata,'nops'=>false);//计算结果
        $shoptype = $this->mysql->select_one("select shoptype from ".$this->tablepre."shop where id='".$this->shopid."'");
        if($shoptype['shoptype']==1){
            $sendtype = $this->mysql->select_one("select sendtype from ".$this->tablepre."shopmarket where shopid='".$this->shopid."'");
        }else{
            $sendtype = $this->mysql->select_one("select sendtype from ".$this->tablepre."shopfast where shopid='".$this->shopid."'");
        }
        if(isset($data['downcost']))
        {   //判断是否有首单立减
			if(isset($data['firstdown'])){				 
				$shoudanjiancost = 0;
				$findcost = 0;
				foreach($data['firstdown'] as $key=>$value)//最大计算
				{
					if($value > $findcost)
					{   $findcost = $value;									 
					}
				}
				$shoudanjiancost = $findcost;			
			}
			//有首单立减的话，不能再享受满减优惠
			if($shoudanjiancost == 0){
				$findcost = 0;
				$findid = '';
				foreach($data['downcost'] as $key=>$value)//最大计算
				{
					if($value > $findcost)
					{
						$findcost = $value;
						$findid = $key;
					}
				}
				$makedata['surecost'] = $makedata['surecost'] - $findcost;
				$makedata['cxids'] = empty($findid)? $makedata['cxids']:$makedata['cxids'].$findid.',';
				$makedata['cx_manjian'] = $findcost;
				$cxdet = $this->get_ruledet($findid);			 
				$cxdet[$findid]['downcost']= '-¥'.number_format($findcost,2);	
				$makedata['cxdet'][] = $cxdet[$findid];	
				$makedata['downcost'] += $findcost;
				$makedata['shopdowncost'] += $findcost * $data['shopbili'][$findid] *0.01 ;
			} 
        }
		
        if(isset($data['zhekou']))
        {
            $findcost = 10;
            $findid = '';
            foreach($data['zhekou'] as $key=>$value)//最小计算
            {
                if($value < $findcost)
                {
                    $findcost = $value;
                    $findid = $key;
                }
            }
            $down = round($findcost*$makedata['surecost']*0.1,2);
	        $down1 = round($makedata['surecost']*(1-$findcost*0.1),2);
			$makedata['cx_zhekou'] = $down1;
			$cxdet = $this->get_ruledet($findid);
			$cxdet[$findid]['downcost']= '-¥'.number_format($down1,2);
			$makedata['cxdet'][] = $cxdet[$findid];
            $makedata['downcost'] += $makedata['surecost'] - $down;
	        $makedata['shopdowncost'] += $down1 * $data['shopbili'][$findid] *0.01 ;
            $makedata['surecost'] =  $down;
            $makedata['cxids'] = empty($findid)? $makedata['cxids']:$makedata['cxids'].$findid.',';

        }
		 
        if(isset($data['zpin'])){
            $ids = array();
            $findid = '';
            $findcost = 0;
		    foreach($data['zpin'] as $key=>$value)//最大计算
            {    
                if($value['kc'] > $findcost ){
                    $findcost = $value['kc'];
                    $findid = $key;	 
				}
            }
			$ids[$findid] = $data['zpin'][$findid];			
			$cxdet = $this->get_ruledet($findid);			
			$cxdet[$findid]['downcost']= $data['zpin'][$findid]['presenttitle'];			
			$makedata['cxdet'][] = $cxdet[$findid]; 
            $makedata['cxids'] = empty($findid)? $makedata['cxids']:$makedata['cxids'].$findid.',';
            $makedata['zid'] = $ids;
        }
		//免配送费的活动   需要重新筛选   如果是店铺配送至使用店铺设置的免配送费   如果是平台配送  只使用平台设置的免配送费
		$data1['nops'] = array();
		if(isset($data['nops'])){
			foreach($data['nops'] as $k=>$v){
				 $onecx =  $this->mysql->select_one("select parentid from ".$this->tablepre."rule where id='".$k."' ");
				 if($onecx['parentid'] == 0 && $sendtype['sendtype'] == 1 ){ //商家设置的免配送费  只适用于商家配送的订单
					 $data1['nops'][$k] = '';
				 }
				 if($onecx['parentid'] == 1 && $sendtype['sendtype'] != 1 ){ //平台设置的免配送费  只适用于平台配送的订单
					 $data1['nops'][$k] = '';
				 } 
			}	
		}
		 
        if(isset($data1['nops']) && !empty($data1['nops'])){
            $findid = '';
            foreach($data1['nops'] as $key=>$value)//最多计算
            {
                $findid .= $key.',';
            }
            $findid = empty($findid)?'':substr($findid,0,strlen($findid)-1);
			$cxdet = $this->get_ruledet($findid);
			$cxdet[$findid]['downcost']= 'exempt';	//免配送费
            $makedata['cxdet'][] = $cxdet[$findid];				
            $makedata['cxids'] = empty($findid)? $makedata['cxids']:$makedata['cxids'].$findid.',';
            $makedata['nops'] = true;
        }
		if(isset($data['firstdown'])){
            $findcost = 0;
            $findid = '';
            foreach($data['firstdown'] as $key=>$value)//最大计算
            {
                if($value > $findcost)
                {
                    $findcost = $value;
                    $findid = $key;
                }
            }
            $makedata['surecost'] = $makedata['surecost'] - $findcost;
			$makedata['cx_shoudan'] = $findcost;
            $makedata['cxids'] = empty($findid)? $makedata['cxids']:$makedata['cxids'].$findid.',';
            $cxdet = $this->get_ruledet($findid);			 
			$cxdet[$findid]['downcost']= '-¥'.number_format($findcost,2);	
			$makedata['cxdet'][] = $cxdet[$findid];			
			$makedata['downcost'] += $findcost;
			$makedata['shopdowncost'] += $findcost * $data['shopbili'][$findid] *0.01 ;
            
        }
        $makedata['cxids'] = strlen($makedata['cxids']) > 1 ? substr($makedata['cxids'],0,strlen($makedata['cxids'])-1):'';
        
		 
		return $makedata;
    }

    /*返回数据格式为：
       array(
             'downcost'=>'10',优惠金额
             'surecost'=>'10',实际金额
             'cxids'=>'1,2,3',满足条件的促销规则id集
             'zid'=>array('赠品标题',库存,赠品ID集
             );
       同类型规则 采用 取最大值规则;
     */
    public function getdata(){
        $list = $this->get_rulelist();
        #print_r( $list);
        if(empty($list))
        {
            return array('downcost'=>0,'shopdowncost'=>0,'surecost'=>$this->cartcost,'cxids'=>'','zid'=>'','nops'=>false);
        }
        //制造满足条件规则
        /*
            data['zhekou'] = array('id'=>'折扣价格');
            data['downcost'] = array('id'=>'减少金额');
            data['zpin'] = array('id'=>'赠品IDS');
        */
        $datas = array();
        //构造促规则标题
        $gzdata = array();
        foreach($list as $key=>$value)
        {    
            if($value['type'] == 1)
            {
                $supporttype=explode(',',$value['supporttype']);
               # print_r($supporttype);
                $supportplatform=explode(',',$value['supportplatform']);
                if(in_array($this->platform,$supportplatform) || $this->platform == 0){//判断在那个端购买		                         
                        if((in_array('1',$supporttype)) || (in_array('2',$supporttype) && $this->paytype == 1)) {//判断是否是在线支付
                            //表示购物车总价  //默认都是 购物车总价
							//满减时  由于limtcontent是字符串  需判断限制金额最小值是否大于购物车总价
							
							
							if($value['controltype'] == 2){
								$limitarr = explode(',',$value['limitcontent']);
								$controarr = explode(',',$value['controlcontent']);															 
								arsort($limitarr);//对数组的值从大到小排序								 
								foreach($limitarr as $k=>$v){
									$ccost = $this->cartcost + $this->bagcost;									 
									if($ccost >= $v){
										$value['limitcontent'] = $v;
										$contrl = $controarr[$k];
										break;
									} 
								} 	                             							 
								
							}
							$ccost = $this->cartcost + $this->bagcost;	
                            if ($value['limitcontent'] <= $ccost) {//商品总价 > 当前购物车
                                
							    $gzdata[$value['id']] = $value['name'];								 
                                if ($value['limittype'] == 1) {//不指定具体 星期       
									if ($value['controltype'] == 1) {//赠品	
                                        if(!empty($value['presenttitle']) ) {
                                            $datas['zpin'][$value['id']] = array('presenttitle' => $value['presenttitle'], 'kc' => $value['limitcontent']);											
                                        } else {
                                            unset($gzdata[$value['id']]);
                                        }
                                    } elseif ($value['controltype'] == 2) {//减肥用
									    $datas['downcost'][$value['id']] = $contrl;										   
										$datas['shopbili'][$value['id']] = $value['shopbili'];
                                    } elseif ($value['controltype'] == 3) {//折扣
                                        
                                        $datas['zhekou'][$value['id']] = $value['controlcontent'];
										$datas['shopbili'][$value['id']] = $value['shopbili'];
                                                                               
                                    } elseif ($value['controltype'] == 4) {//免配送
                                        $datas['nops'][$value['id']] = $value['controlcontent'];
                                    } elseif ($value['controltype'] == 5) {//新用户首单立减                                         
										if($this->firstorder==0){	 
											$datas['firstdown'][$value['id']] = $value['controlcontent'];
											$datas['shopbili'][$value['id']] = $value['shopbili'];											 
										}	 
                                    }

                                } elseif ($value['limittype'] == 2) {//制定星期
								
                                    if (!empty($value['limittime'])) {
										
                                        if (in_array($this->weekday, explode(',', $value['limittime']))) {
                                            if ($value['controltype'] == 1) {//赠品
											 
                                                if (!empty($value['presenttitle'])) {
                                                    $datas['zpin'][$value['id']] = array('presenttitle' => $value['presenttitle'], 'kc' => $value['limitcontent']);
                                                } else {
                                                    unset($gzdata[$value['id']]);
                                                }
												
												
                                            } elseif ($value['controltype'] == 2) {//减肥用
												 
												$datas['downcost'][$value['id']] = $contrl;										   
										        $datas['shopbili'][$value['id']] = $value['shopbili'];
												
                                            } elseif ($value['controltype'] == 3) {//折扣
                                               
                                                $datas['zhekou'][$value['id']] = $value['controlcontent'];
												$datas['shopbili'][$value['id']] = $value['shopbili'];
                                            } elseif ($value['controltype'] == 4) {//免配送
                                                $datas['nops'][$value['id']] = $value['controlcontent'];
                                            }  elseif ($value['controltype'] == 5) {//新用户首单立减
												if($this->firstorder==0){
												$datas['firstdown'][$value['id']] = $value['controlcontent'];
												$datas['shopbili'][$value['id']] = $value['shopbili'];
												}	 
											}
                                        }
                                    }
                                } elseif ($value['limittype'] == 3) {//指定时间段
                                    if ( $value['endtime'] > $this->maketime  && $value['starttime'] <  $this->maketime  ) {                                       
										if ($value['controltype'] == 1) {//赠品
											if (!empty($value['presenttitle'])) {
												$datas['zpin'][$value['id']] = array('presenttitle' => $value['presenttitle'], 'kc' => $value['limitcontent']);
											} else {
												unset($gzdata[$value['id']]);
											}
										} elseif ($value['controltype'] == 2) {//减肥用
											$datas['downcost'][$value['id']] = $contrl;										   
										    $datas['shopbili'][$value['id']] = $value['shopbili'];
										} elseif ($value['controltype'] == 3) {//折扣
                                                                                   
											$datas['zhekou'][$value['id']] = $value['controlcontent'];
											$datas['shopbili'][$value['id']] = $value['shopbili'];
										} elseif ($value['controltype'] == 4) {//免费配送
											$datas['nops'][$value['id']] = $value['controlcontent'];
										} elseif ($value['controltype'] == 5) {//新用户首单立减
											if($this->firstorder==0){
											$datas['firstdown'][$value['id']] = $value['controlcontent'];
											$datas['shopbili'][$value['id']] = $value['shopbili'];
											}	 
										}
                                    }
                                }
                            }
                        }
					}
				}
			}
 #print_r($datas);
        return $this->maxdata($datas,$gzdata);
    }

}
?>