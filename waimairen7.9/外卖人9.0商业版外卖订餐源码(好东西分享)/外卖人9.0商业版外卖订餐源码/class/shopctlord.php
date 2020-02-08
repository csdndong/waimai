<?php 
/**
 * @class shopctlord
 * @brief 店铺操作订单类
 */ 
class shopctlord
{ 
	private $mysql;
	private $orderid;
	private $shopid;
	private $err = ''; 
	private $goods = ''; 
	private $orderinfo;
	private $orderdetinfo;
	private $memberCls;
	//构造函数
	function __construct($orderid,$shopid,$mysql,$reason)
	{
		$this->mysql = $mysql; 
		$this->orderid = $orderid;
		$this->shopid = $shopid; 
		$this->reason = $reason;
	}
	//获取订单信息
	private function orderinfo(){
		if($this->orderid == null || $this->orderid == ""){ 
			$this->err = '订单不存在';
			return false;
		} 
		if($this->shopid == null || $this->shopid == ""){
			$this->err = '店铺不存在';
			return false;
		}
		$this->orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$this->orderid."' and shopid=".$this->shopid."  "); 
		
		
		if(empty($this->orderinfo)){
			$this->err = '订单不存在';
			return false;
		}
		return true;
	}
	/*释放商品库存默认增加赠品未释放*/
	private function releasestroe()
	{
		$ordetinfo = $this->mysql->getarr("select ort.goodscount,go.id,go.sellcount,go.count as stroe from ".Mysite::$app->config['tablepre']."orderdet as ort left join  ".Mysite::$app->config['tablepre']."goods as go on go.id = ort.goodsid  where ort.order_id='".$this->orderinfo['id']."' and ort.is_send = 0 ");
	 	foreach($ordetinfo as $key=>$value)
		{  
			 $this->mysql->update(Mysite::$app->config['tablepre'].'goods','`count`=`count`+'.$value['goodscount'].' ,`sellcount`=`sellcount`-'.$value['goodscount'],"id='".$value['id']."'");
		} 
	}
	private function orderdetinfo(){
		$this->orderdetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$this->orderid."'   "); 
	}
	//商家取消订单
	public function unorder(){

		$reason = trim(IFilter::act(IReq::get('reason')));
		if($this->orderinfo() == false){
			return false;
		}
		if($this->orderinfo['status'] != 0){
			$this->err = '订单已处理过不能被商家取消';
			return false;
		}
		if($this->orderinfo['is_reback'] > 0 && $this->orderinfo['is_reback'] != 3 && $this->orderinfo['is_reback'] != 5){
			$this->err = '有退款操作,不能被商家取消';
			return false;
		} 
		$detail = ''; 
		 if($this->orderinfo['paystatus'] == 1&& $this->orderinfo['paytype'] != 0){ 
		     $this->err = '订单已支付，取消订单请用户到用户中心申请退款';
		 	   return false; 
		 }elseif($this->orderinfo['scoredown'] > 0){
		 	 $data['status'] = 4; 
		    $this->mysql->update(Mysite::$app->config['tablepre']."order",$data,"id='".$this->orderinfo['id']."'");
			$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$this->orderinfo['scoredown'],"uid ='".$this->orderinfo['buyeruid']."' ");	
		 }else{

		 	 $data['status'] = 4; 
		    $this->mysql->update(Mysite::$app->config['tablepre']."order",$data,"id='".$this->orderinfo['id']."'");
		 } 
		$this->releasestroe();
		$ordCls = new orderclass(); 
		$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '".$this->orderid."' and status !=3 ");  //删除不为完成的配送单
		$ordCls->noticeclose($this->orderid,$reason); 
		return true;
	}
	//商家制作订单
	public function makeorder(){ 
		if($this->orderinfo() == false){
			return false;
		}
		if($this->orderinfo['is_make']){
			$this->err = '订单制作状态已处理';
			return false;
		}
		if($this->orderinfo['is_reback'] > 0 && $this->orderinfo['is_reback'] != 3 && $this->orderinfo['is_reback'] != 5){
			$this->err = '退款处理';
			return false;
		}
		if($this->orderinfo['status'] > 3){
			$this->err = '订单已取消';
			return false;
		}
		if($this->orderinfo['status'] == 2){
			$this->err = '订单已发货';
			return false;
		}
		if($this->orderinfo['status'] == 3){
			$this->err = '订单已完成';
			return false;
		}
		if($this->orderinfo['status'] == 0){
			$this->err = '订单还未通过审核，不能受理';
			return false;
		}
		  
		if($this->orderinfo['paytype'] == 1){
			if($this->orderinfo['paystatus'] == 0){
				$this->err = '订单未支付，等待支付后确认制作';
				return false; 
			} 
		}
		$udata['is_make'] = 1;
		if($this->orderinfo['is_ziti'] == 1){
			$udata['status'] = 2;
		}
		$udata['maketime'] = time();
		$this->mysql->update(Mysite::$app->config['tablepre'].'order',$udata,"id='".$this->orderid."'");
		$ordCls = new orderclass();
		$ordCls->writewuliustatus($this->orderinfo['id'],4,$this->orderinfo['paytype']);
		$ordCls->noticemake($this->orderid); 
		if($this->orderinfo['pstype'] == 2 && $this->orderinfo['is_ziti'] == 0){ 
			$psbinterface = new psbinterface();
			if($psbinterface->psbnoticeorder($this->orderid)){
				logwrite('商家制作后通知到配送宝');
			}else{
				logwrite($psbinterface->err());
			}
		}
		return true;
	}
	//商家不制作订单
	public function unmakeorder(){
		if($this->orderinfo() == false){
			return false;
		}
		if($this->orderinfo['is_make']){
			$this->err = '订单制作状态已处理';
			return false;
		}
		if($this->orderinfo['is_reback'] > 0 && $this->orderinfo['is_reback'] != 3 && $this->orderinfo['is_reback'] != 5){
			$this->err = '退款处理中';
			return false;
		}
		if($this->orderinfo['status'] > 3){
			$this->err = '订单已取消';
			return false;
		}
		if($this->orderinfo['status'] == 2){
			$this->err = '订单已发货';
			return false;
		}
		if($this->orderinfo['status'] == 3){
			$this->err = '订单已完成';
			return false;
		}
		if($this->orderinfo['status'] == 0){
			$this->err = '订单还未通过审核，不能受理';
			return false;
		} 
		
		//商家不制作   对于在线支付已付的订单  要进行退款处理;
		if( $this->orderinfo['paytype'] == 1 &&  $this->orderinfo['paystatus'] == 1 ){	 
			$ordCls = new orderclass();	
			if($this->orderinfo['paytype_name'] == 'open_acout'){
				$udata['is_make'] = 2;
				$udata['status'] = 5;				 
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$udata,"id='".$this->orderid."'");       		
				$ordCls->noticeunmake($this->orderinfo['id']); 	
				$ordCls->writewuliustatus($this->orderid,5,$this->orderinfo['paytype']);  //商家不接单  物流状态
				$drawbacklog = new drawbacklog($this->mysql,$this->memberCls);			 			 
				$check = $drawbacklog->returncost($this->orderid);			
				if(!$check){
					$this->err = $drawbacklog->GetErr();
					return false; 
				} 
				$data['uid'] = $this->orderinfo['buyeruid'];
				$data['username'] = $this->orderinfo['buyername'];	 
				$data['orderid'] = $this->orderinfo['id'];
				$data['shopid'] = $this->orderinfo['shopid'];		
				$data['status'] = 4;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败	4平台同意退款
				$data['addtime'] = time();
				$data['cost'] = $this->orderinfo['allcost'];
				$data['admin_id'] = $this->orderinfo['admin_id'];
				$data['type'] = 1;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);   //写退款日志 记录商家同意退款		
				$ordCls->noticeback($this->orderinfo['id']);
				$ordCls->writewuliustatus($this->orderid,19,$this->orderinfo['paytype']);  //商家不接单  物流状态 
			}else{
				$data4['is_reback'] = 1;	
                $data4['is_make'] = 2;				
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data4,"id='".$this->orderid."'");
				$ordCls->writewuliustatus($this->orderid,20,$this->orderinfo['paytype']);  //商家不接单  物流状态 
                $ordCls->noticeunmake($this->orderinfo['id']); 		
                $data['uid'] = $this->orderinfo['buyeruid'];
				$data['username'] = $this->orderinfo['buyername'];	 
				$data['orderid'] = $this->orderinfo['id'];
				$data['shopid'] = $this->orderinfo['shopid'];		
				$data['status'] = 2;//  退款状态 0待处理 1退款结束 2退款成功 3退款失败	
				$data['addtime'] = time();
				$data['cost'] = $this->orderinfo['allcost'];
				$data['admin_id'] = $this->orderinfo['admin_id'];
				$data['type'] = 0;
				$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);   //写退款日志 记录商家同意退款				
			}
			
		}
		if( $this->orderinfo['paytype'] == 0){
			$udata['is_make'] = 2;
			$udata['status'] = 5;
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$udata,"id='".$this->orderid."'");  
			$ordCls = new orderclass();	     		
			$ordCls->noticeunmake($this->orderinfo['id']); 	
			$ordCls->writewuliustatus($this->orderid,5,$this->orderinfo['paytype']);  //商家不接单  物流状态 
            //$ordCls->noticeback($this->orderinfo['id']);	
			//恢复积分
			$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$this->orderinfo['buyeruid']."'   "); 
			$allscore = $memberinfo['score'] + $this->orderinfo['scoredown'];
			$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$this->orderinfo['scoredown'],"uid ='".$this->orderinfo['buyeruid']."' ");
			$this->memberCls->addlog($this->orderinfo['buyeruid'],1,1,$this->orderinfo['scoredown'],'订单不制作返还积分','商家不制作订单返还积分'.$this->orderinfo['scoredown'],$allscore);			
		}
        
		//恢复优惠券		
		$yhjids = $this->orderinfo['yhjids'];		
		if(!empty($yhjids)){
			$yhjarr = explode(',',$yhjids);
			#print_r(yhjarr);exit;
			foreach($yhjarr as $k=>$v){
				$yhjdata['status'] = 0;
				$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$yhjdata,"id ='".$v."' ");
			}
		}	
		return true;
		
	}
	public function SetMemberls($membercls){
		$this->memberCls = $membercls;
		return $this;
	}
	//商家订单发货
	public function sendorder(){
	
		
		if($this->orderinfo() == false){
			return false;
		}
		 
		if($this->orderinfo['is_reback'] > 0 && $this->orderinfo['is_reback'] != 3 && $this->orderinfo['is_reback'] != 5){
			$this->err = '订单申请退款';
			return false;
		} 
		if($this->orderinfo['status'] > 3){
			$this->err = '订单已取消';
			return false;
		}
		if($this->orderinfo['status'] == 0){
			$this->err = '订单状态未审核';
			return false;
		}
		if($this->orderinfo['status'] == 2){
			$this->err = '订单已发货';
			return false;
		}
		if($this->orderinfo['status'] == 3){
			$this->err = '订单已完成';
			return false;
		}
		if($this->orderinfo['is_make'] == 0){
			$this->err = '商家未确认制作不能发货';
			return false;
		}	 
		if($this->orderinfo['is_make'] == 2){
			$this->err = '商家已取消制作不能发货';
			return false;
		}
		if($this->orderinfo['pstype'] == 0 || $this->orderinfo['pstype'] == 2){
			$this->err = '此订单由配送员取货默认发货';
			return false;
		} 
		$udata['status'] = 2;
		$udata['sendtime'] = time();
		$this->mysql->update(Mysite::$app->config['tablepre'].'order',$udata,"id='".$this->orderid."'"); 
		$ordCls = new orderclass();
		$ordCls->writewuliustatus($this->orderinfo['id'],6,$this->orderinfo['paytype']);
		$ordCls->noticesend($this->orderid);  	 
		return true;
	}
	//商家删除订单
	public function delorder(){
		if($this->orderinfo() == false){
			return false;
		}
		
		if($this->orderinfo['is_reback'] > 0 && $this->orderinfo['is_reback'] != 3 && $this->orderinfo['is_reback'] != 5){
			$this->err = '订单申请退款中';
			return false;
		}  
		if($this->orderinfo['status'] !=4 && $this->orderinfo['status'] !=5 ){
		 	   $this->err = '订单状态不可彻底删除';
		 	   return false;
		}  
		$this->mysql->delete(Mysite::$app->config['tablepre'].'order',"id='".$this->orderinfo['id']."'");   
		$this->mysql->delete(Mysite::$app->config['tablepre'].'orderdet',"order_id='".$this->orderinfo['id']."'");   
		$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '".$this->orderinfo['id']."' and status != 3"); 
		return true;
	}
	//商家完成订单
	public function wancheng(){
		if($this->orderinfo() == false){
			return false;
		} 
		if($this->orderinfo['is_reback'] > 0 && $this->orderinfo['is_reback'] != 3 && $this->orderinfo['is_reback'] != 5){
			$this->err = '订单申请退款';
			return false;
		} 
		if($this->orderinfo['status'] > 3){
			$this->err = '订单已取消';
			return false;
		}
		if($this->orderinfo['status'] == 0){
			$this->err = '订单状态未审核';
			return false;
		}
		if($this->orderinfo['status'] == 1 && $this->orderinfo['is_ziti'] == 0){
			$this->err = '订单未发货';
			return false;
		} 
		if($this->orderinfo['is_make'] == 0){
			$this->err = '订单商家还未确认制作';
			return false;
		}
		if($this->orderinfo['is_make'] == 2){
			$this->err = '订单已取消制作';
			return false;
		}
		 if($this->orderinfo['status'] == 3){
			$this->err = '订单已完成';
			return false;
		} 
		if($this->orderinfo['pstype'] !=1 && $this->orderinfo['is_ziti'] == 0 ){
			$this->err = '平台配送订单由配送员处理';
			return false;
		} 
		$ordCls = new orderclass();
		$ordCls->writewuliustatus($this->orderinfo['id'],9,$this->orderinfo['paytype']);  // 商家 操作 完成订单 
		$orderdata['is_acceptorder'] = 1; 
		$orderdata['status'] = 3;
		$orderdata['suretime'] = time();
		
		if($this->orderinfo['paytype']==0){
			$orderdata['paystatus'] = 1;
			$orderdata['paytime'] = time();
		 }
 				/* 记录配送员送达时候坐标 */
				if(  $this->orderinfo['psuid'] > 0 ){
					if(  $this->orderinfo['pstype'] == 0 ){
						$psylocationonfo = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$this->orderinfo['psuid']."' ");
						if(!empty($psylocationonfo)){
							 $orderdata['psyoverlng'] = $psylocationonfo['lng'];
							 $orderdata['psyoverlat'] = $psylocationonfo['lat'];
						}
					}
					if(  $this->orderinfo['pstype'] == 2 ){
						$psbinterface = new psbinterface(); 
						$psylocationonfo = $psbinterface->getpsbclerkinfo($this->orderinfo['psuid']);
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
					
				 
		$this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id='".$this->orderid."'");
		//分销返佣
        $is_open_distribution = Mysite::$app->config['is_open_distribution'];
        logwrite('商家完成订单，后台分销状态'.$is_open_distribution);
		if($is_open_distribution == 1){
			logwrite('商家完成订单，后台分销状态开启');
			$distribution = new distribution();
			if($distribution->operateorder($this->orderid)){
				 logwrite('返佣成功');
			}else{
				$err = $distribution->Error();
				logwrite('返佣失败，失败原因：'.$err);
			}
		}
		//更新销量 
		$shuliang  = $this->mysql->select_one("select sum(goodscount) as sellcount from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$this->orderinfo['id']."'  ");
		if(!empty($shuliang) && $shuliang['sellcount'] > 0){
			$this->mysql->update(Mysite::$app->config['tablepre'].'shop','`sellcount`=`sellcount`+'.$shuliang['sellcount'].'',"id ='".$this->orderinfo['shopid']."' ");
		} 
		//更新用户成长值
		if(!empty($this->orderinfo['buyeruid'])){
			$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$this->orderinfo['buyeruid']."'   "); 
			if(!empty($memberinfo)){
				$data['total']=$memberinfo['total']+$this->orderinfo['allcost'];
				$data['score'] = $memberinfo['score']+Mysite::$app->config['consumption'];
				if(Mysite::$app->config['con_extend'] > 0){
					$allscore= $this->orderinfo['allcost']*Mysite::$app->config['con_extend'];
					$data['score']+=$allscore;
					$consumption=$allscore;
				}
				if(!empty($consumption)){
					$consumption+=Mysite::$app->config['consumption'];
				}else{
					$consumption=Mysite::$app->config['consumption'];
				}
				$this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid ='".$this->orderinfo['buyeruid']."' ");
				if($consumption > 0){
					$this->memberCls->addlog($this->orderinfo['buyeruid'],1,1,$consumption,'消费送积分','消费送积分'.$consumption,$data['score']);
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
							$this->mysql->update(Mysite::$app->config['tablepre'].'member','`parent_id`=0',"uid ='".$this->orderinfo['buyeruid']."' ");
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
			if($this->orderinfo['shoptype'] != 100){
				if($ordCls->sendWxMsg($this->orderinfo['id'],7,1)){
					
				}
			}
			if($ordCls->sendWxMsg($this->orderinfo['id'],2,2)){
				
			}
		}
		return true; 
	}
	//同意退款
	public function reback(){
		 
		if($this->orderinfo() == false){
			return false;
		} 
		if($this->orderinfo['is_reback'] == 0){
			$this->err = '订单未申请退款';
			return false;
		} 
		if($this->orderinfo['status'] > 3){
			$this->err = '订单已取消';
			return false;
		} 
		if($this->orderinfo['status'] == 3){
			$this->err = '订单已完成';
			return false;
		} 
		$drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$this->orderid." and status = 0 order by  id desc  limit 0,2");
		
		
		if(empty($drawbacklog)){
			$this->err = '退款申请不存在';
			return false;
		}
		$data = array('allcost'=>$this->orderinfo['allcost'],'orderid'=>$this->orderinfo['id'],'typeid'=>'1','status'=>'2','uid'=>$this->orderinfo['buyeruid']); 		 
		$drawback = new drawbacklog($this->mysql,$this->memberCls); 			 
		if($drawback->setsavedraw($data)->save()){			
			return true;
		}else{
			$this->err = '退款失败';
			return false;
		}
		 
		
	}
	//不同意退款
	public function unreback(){
		
		
		if($this->orderinfo() == false){
			return false;
		} 
		if($this->orderinfo['is_reback'] == 0){
			$this->err = '订单未申请退款';
			return false;
		} 
		$drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$this->orderid." and status = 0");
		 
		if(empty($drawbacklog)){
			$this->err = '退款申请不存在';
			return false;
		}
		if($this->orderinfo['is_reback'] == 2){
			$this->err = '已退款成功';
			return false;
		}
		if($this->orderinfo['is_reback'] == 1){
			$this->err = '用户已取消退款申请';
			return false;
		} 
		if(empty($this->reason)){
			$this->err = '拒绝退款理由不能为空';
			return false;
		} 	 
		$drawback = new drawbacklog($this->mysql);	
		$ddata=array('allcost'=>$this->orderinfo['allcost'],'orderid'=>$this->orderinfo['id'],'reason'=>$this->reason,'typeid'=>'1','status'=>'3','uid'=>$this->orderinfo['buyeruid']);		 	 
 
		if($drawback->setsavedraw($ddata)->save()){	 	
			return true; 
		} 
		 
		
	}
	
	
	 
	public function Error()
	{
		return $this->err;
	}
	//写消息
	private function wirtemess($mes)
	{
		 
	}
	
	

	 
}
?>