<?php 
/**
 * @class userctlord
 * @brief 用户操作订单类
 */
class userctlord
{ 
	private $mysql;
	private $orderid;
	private $buyeruid;
	private $err = ''; 
	private $goods = ''; 
	private $orderinfo;
	private $orderdetinfo;
	//构造函数
	function __construct($orderid,$buyeruid,$mysql)
	{
		$this->mysql = $mysql; 
		$this->orderid = $orderid;
		$this->buyeruid = $buyeruid; 
	}
	//获取订单信息
	private function orderinfo(){
		if($this->orderid == null || $this->orderid == ""){ 
			$this->err = '订单不存在';
			return false;
		} 
		if($this->buyeruid == null || $this->buyeruid == "" || $this->buyeruid==0){
			$this->err = '用户id不存在';
			return false;
		}
		$this->orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$this->orderid."'   "); 
		if(empty($this->orderinfo)){
			$this->err = '订单不存在';
			return false;
		}
		if($this->orderinfo['buyeruid'] != $this->buyeruid){
			$this->err = '订单不属于您';
			return false;
		}
		return true;
	}
	/*释放商品库存默认增加赠品未释放*/
	private function releasestroe(){
		$ordetinfo = $this->mysql->getarr("select ort.goodscount,go.id,go.sellcount,go.count as stroe from ".Mysite::$app->config['tablepre']."orderdet as ort left join  ".Mysite::$app->config['tablepre']."goods as go on go.id = ort.goodsid  where ort.order_id='".$this->orderinfo['id']."' and ort.is_send = 0 ");
	 	foreach($ordetinfo as $key=>$value){  
			 $this->mysql->update(Mysite::$app->config['tablepre'].'goods','`count`=`count`+'.$value['goodscount'].' ,`sellcount`=`sellcount`-'.$value['goodscount'],"id='".$value['id']."'");
		} 
	}
	private function orderdetinfo(){
		$this->orderdetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$this->orderid."'   "); 
	}
	//用户取消订单
	public function unorder(){
		if($this->orderinfo() == false){
			return false;
		}
		if($this->orderinfo['status'] > 2 ){
			$this->err = '订单状态不可取消';
			return false;
		} 
		if($this->orderinfo['paystatus'] == 1 && $this->orderinfo['paytype'] == 1){
			$this->err = '订单已支付，取消订单请用户到用户中心申请退款';
			return false; 
		}
		if($this->orderinfo['is_reback'] ==1 || $this->orderinfo['is_reback'] ==2 || $this->orderinfo['is_reback'] ==4 ){
			$this->err = '订单退款中';
			return false;
		}
		if($this->orderinfo['is_make'] == 1){
			$this->err = '订单已确认制作';
			return false;
		}
		$data['status'] = 4;
        $this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$this->orderinfo['id']."'");
        if($this->orderinfo['scoredown'] > 0){  
            $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$this->orderinfo['scoredown'],"uid ='".$this->orderinfo['buyeruid']."' ");             
        } 
		if(!empty($this->orderinfo['yhjids'])){
			$yhjidarr = explode(',',$this->orderinfo['yhjids']);
			foreach($yhjidarr as $k=>$v ){
				$jdata['status'] =1;
			    $this->mysql->update(Mysite::$app->config['tablepre'].'juan',$jdata,"id='".$v."'");
			}			 
		} 
		$this->releasestroe(); 
		$ordCls = new orderclass();
		$ordCls->writewuliustatus($this->orderinfo['id'],12,$this->orderinfo['paytype']);  // 用户取消订单
		$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '".$this->orderinfo['id']."' and status !=3 ");  //写配送订单  
		return true;
	}
	//用户删除订单   
	/***特别注意：用户端删除订单不是真正的删除，只是在用户端隐藏，不让用户看到***/
	public function delorder(){ 
		$data['is_userhide'] = 1;
		if($this->orderinfo() == false){
			return false;
		}
		if($this->orderinfo['status'] == 3 || ($this->orderinfo['status'] > 3 && $this->orderinfo['reback'] !=1 && $this->orderinfo['reback'] !=4)){
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$this->orderinfo['id']."'");
			return true;
		}else{
			$this->err = '订单状态不可删除';
			return false;
		} 	
	}
	//用户确认收货
	function sureorder(){
		if($this->orderinfo() == false){
			return false;
		}
		if($this->orderinfo['status'] !=  2 && $this->orderinfo['is_ziti'] == 0){
			$this->err = '订单状态不能确认收货';
			return false;
		}
		if($this->orderinfo['is_reback'] ==1 || $this->orderinfo['is_reback'] ==2 || $this->orderinfo['is_reback'] ==4 ){
			$this->err = '订单退款中';
			return false;
		} 
		 $data['is_acceptorder'] = 1;
		 $data['status'] = 3;
		 $data['suretime'] = time();
		 if($this->orderinfo['paytype']==0){
			$data['paystatus'] = 1;
			$data['paytime'] = time();
		 }
		 
		 /* 记录配送员送达时候坐标 */
				if(  $this->orderinfo['psuid'] > 0 ){
					if(  $this->orderinfo['pstype'] == 0 ){
						$psylocationonfo = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$this->orderinfo['psuid']."' ");
						if(!empty($psylocationonfo)){
							 $data['psyoverlng'] = $psylocationonfo['lng'];
							 $data['psyoverlat'] = $psylocationonfo['lat'];
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
								 $data['psyoverlng'] = $posilng;
								 $data['psyoverlat'] = $posilat;
							}
						}
					}
				}
				
		 
		 

		 $this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$this->orderinfo['id']."'");
		 $this->mysql->update(Mysite::$app->config['tablepre'].'orderps','`status`=3',"orderid ='".$this->orderinfo['id']."' "); 
		 $ordCls = new orderclass();
		 $ordCls->writewuliustatus($this->orderinfo['id'],10,$this->orderinfo['paytype']);  // 用户确认收货
		 //通知商家
		if($ordCls->sendWxMsg($this->orderinfo['id'],2,2)){
			
		}
        //分销返佣
        $is_open_distribution = Mysite::$app->config['is_open_distribution'];
        if($is_open_distribution == 1){
			$distribution = new distribution();
			if($distribution->operateorder($this->orderinfo['id'])){
				 
			}else{
				$err = $distribution->Error();
				logwrite('返佣失败，失败原因：'.$err);
			}
		}
		
		//消费送积分
		if($this->orderinfo['buyeruid'] > 0){
			$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$this->orderinfo['buyeruid']."'");
			$arr['score'] = $memberinfo['score']+Mysite::$app->config['consumption'];
			if(Mysite::$app->config['con_extend'] > 0){
				$allscore= $this->orderinfo['allcost']*Mysite::$app->config['con_extend'];
				$arr['score']+=$allscore;
				$consumption=$allscore;
			}
			if(!empty($consumption)){
				$consumption+=Mysite::$app->config['consumption'];
			}else{
				$consumption=Mysite::$app->config['consumption'];
			}
			$this->mysql->update(Mysite::$app->config['tablepre'].'member',$arr,"uid ='".$this->orderinfo['buyeruid']."' ");
			if($consumption > 0){
				$memberCls=new memberclass($this->mysql);
				$memberCls->addlog($this->orderinfo['buyeruid'],1,1,$consumption,'消费送积分','消费送积分'.$consumption,$arr['score']);
			}
		}
		return true; 
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