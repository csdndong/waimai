 <?php 

/**
 * @class drawbacklog
 * @brief 退款申请类 
 $obj = get_class($usercls); //获取类名
 print_r($obj); 
 */
 /**
 ==================退款流程说明==========================

 ********涉及到的数据库表中字段说明*******

 order表中  关于退款的字段 is_reback 字段说明：
 0 正常状态
 1 退款中，待平台处理
 2 退款成功
 3 拒绝退款 
 4 退款中，待商家处理
 5 用户取消退款申请
 
 drawbacklog表中  关于退款的字段 type，status 字段说明：
 type：退款类型
 0 用户申请退款
 1 管理员操作退款
 
 status：退款申请处理状态
 0 用户申请退款
 1 用户取消退款
 2 商家同意退款
 3 商家不同意退款
 4 平台已退款
 配置文件中 shenhedrawback 该字段控制   退款申请是否需要平台审核
 为1时   退款申请需要等平台审核  后台审核后退款资金才会返回到用户账户
 为0时   不需平台审核  商家未制作订单申请退款后  退款资金直接返回用户账户   
         商家制作后订单申请退款   商家同意后不需平台审核  退款资金直接返回用户账户 
		 
 ******退款流程说明******
 用户申请退款 立即插入drawbacklog表中一条申请退款记录数据  
 若是商家未制作订单：
     用户申请退款后  判断后台配置是否需要平台审核退款申请  需要的话退款申请到平台 平台审核退款完成后 此时退款完成
     不需要平台审核退款申请的话   直接完成退款 不需后台审核 退款直接返回用户账户
 
 若是商家已制作订单：
     用户申请退款后，退款申请到商家等待商家处理，
	 商家同意情况下：
		 商家同意退款后完成退款 然后判断后台配置是否需要平台审核退款申请  需要的话退款申请到平台 平台审核退款完成后 此时退款返回用户账户
		 不需要平台审核退款申请的话   商家同意退款后直接完成退款 不需后台审核 退款直接返回用户账户 	 
	 商家不同意情况下：
	     商家拒绝退款后 插入drawbacklog表中一条商家拒绝退款记录数据  用户可继续申请退款
 用户取消退款申请：插入drawbacklog表中一条用户取消退款申请记录数据  用户自己取消后用户不可再次申请退款 订单流程继续		 
	 
 **/
class drawbacklog
{
	private $error = ''; 
	private $orderid = ''; 
	private $logtype = array('0'=>'退款到支付宝','1'=>'退款到账户');
	private $typeidlist = array('0','1');//0用户申请退款  1后台管理员操作退款
	private $statuslist = array('0','1','2','3','4'); 
	private $actionclas; 
	private $drawdata; 
	protected $mysql; 
	/**
	 *  @brief Brief
	 *  
	 *  @param [in] $mysql 数据库操作
	 *  @param [in] $usercls 传用户类 操作'
	 *  @return Return_Description
	 *  
	 *  @details Details
	 */
	function __construct($mysql,$usercls=null){
	 	$this->mysql =$mysql;  
		$this->actionclas = $usercls;//memberclass   //print_r($this->actionclas->getadmininfo());//获取管理员信息   getinfo//获取  
	}
	public function setsavedraw($data){  //      
		$this->drawdata = $data;		
		return $this;
	}
	//添加退款记录
	public function save(){ 
		$allcost =  $this->drawdata['allcost'];//退款金额
		$orderid =  $this->drawdata['orderid'];// 订单号
		$reason = $this->drawdata['reason']; //退款原因
		$content = $this->drawdata['content']; //退款详细内容说明
		$typeid = $this->drawdata['typeid']; //退款类型 0用户自己申请退款  1后台管理员直接退款 
		$status = $this->drawdata['status']; //退款状态 0 用户申请退款（待商家处理）   1 用户取消退款（退款结束）   2 同意退款（退款成功）   3 不同意退款（退款失败） 
		$uid = $this->drawdata['uid'];   
	    logwrite($status.'1111');
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' ");
		  
		if(empty($orderinfo)){
            $this->error = '订单不存在';
            return false;
		}
		
		
		if(!in_array($typeid,$this->typeidlist)){
			$this->error = '未定义退款类型';
			return false;
		}
		if(!in_array($status,$this->statuslist)){
			$this->error = '未定义退款状态';
			return false;
		}
		if($status == 0 || $status == 3){ //用户申请退款和商家拒绝退款时   理由不能为空
			if(empty($reason)){
				$this->error = '未选择退款原因';
				return false;
			}
		}
		if($orderinfo['allcost'] != $allcost ) { 
		    $this->error = "退款金额错误";
			return false; 
		}
		 
        $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");	 
		if( empty($memberinfo) ){
			$this->error = '获取用户信息失败';
			 return false;
		}
		if($orderinfo['buyeruid'] != $memberinfo['uid']){
			$this->error = '购买用户和用户不一致';
			return false;
		}
		if($orderinfo['paystatus'] != 1){
			$this->error = '该订单未支付';
			return false;
		}
			
		if($orderinfo['paytype'] == 0||empty($orderinfo['paytype'])){
			$this->error = '货到支付订单';
			return false;
		}
		
		if($status == 0){//判断有户是否可以申请退款
			if($orderinfo['status'] < 1 || $orderinfo['status'] >= 3 || ($orderinfo['is_make'] ==1 && Mysite::$app->config['allowreback'] != 1 ) ){
				$this->error = '订单状态不能申请退款';
				return false;
		    }
			if($orderinfo['is_reback'] == 1|| $orderinfo['is_reback'] == 4 ){
				$this->error = '订单退款申请正在处理中';
				return false;
		    }
			if($orderinfo['is_reback'] == 5 ){
				$this->error = '订单已取消退款申请，不可再次申请退款';
				return false;
		    }
			if(empty($content)){
				$this->error = '请填写退款详细内容说明';
			    return false;
			}
			if(empty($reason)){
				$this->error = '请填写退款原因';
			    return false;
			}
		} 	
		if($status == 1){//判断有户是否可以取消退款申请
			if($orderinfo['is_reback'] == 5 || $orderinfo['is_reback'] == 0 || $orderinfo['is_reback'] == 2 || $orderinfo['status'] < 1 || $orderinfo['status'] >= 3){
				$this->error = '订单状态不能取消退款申请';
				return false;
			}
		}
		if($status == 2 || $status == 4 ){//判断商家和平台是否可以同意退款
			if($orderinfo['is_reback'] == 5 || $orderinfo['is_reback'] == 0 || $orderinfo['is_reback'] == 2 || $orderinfo['status'] < 1  ){
				$this->error = '订单状态不能退款';
				return false;
			}
		}
		if($status == 3){//判断商家拒绝退款
			if(empty($reason)){
				$this->error = '请填写拒绝退款理由';
				return false;
			}
		}
		if($status == 4){//判断平台退款
			if(empty($reason)){
				$this->error = '请填写处理说明';
				return false;
			}
		}
		$data['uid'] = $memberinfo['uid'];
		$data['username'] = $memberinfo['username'];
		$data['reason'] = $reason;
		$data['orderid'] = $orderinfo['id'];
		$data['shopid'] = $orderinfo['shopid'];		
		$data['content'] = $content;	
		$data['status'] = $status;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
		$data['addtime'] = time();
		$data['cost'] = $orderinfo['allcost'];
		$data['admin_id'] = $orderinfo['admin_id'];
		$data['type'] = $typeid;
		$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);   //写退款记录	 
        
		//跑腿订单退款通知配送宝
		if($orderinfo['shoptype'] == 100){
			$psbinterface = new psbinterface();
			if($psbinterface->psbdraworder($orderinfo['id'])){
				
			}
		} 
		//普通订单商家同意退款后通知配送宝 			
		if($orderinfo['pstype'] == 2 && $orderinfo['is_make'] == 1 && $status == 2 ){			 
			$psbinterface = new psbinterface();
			if($psbinterface->psbdraworder($orderinfo['id'])){

			}
		}
		if($orderinfo['is_make'] ==1 && $status ==0){
			$appCls = new appclass();		
			
			
			if(!empty($orderinfo['shopuid'])){
				
				$shopuserlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."applogin where uid='".$orderinfo['shopuid']."' ");
				$appCls->SetUid($orderinfo['shopuid'])->SetUserlist($shopuserlist)->sendNewmsg(Mysite::$app->config['sitename'].'退款通知','您有新的退款订单等待处理');
				$ordCls = new orderclass();
				if($ordCls->sendWxMsg($orderinfo['id'],3,2)){
					logwrite('微信推送2-3成功');
				}else{
					logwrite('微信推送2-3失败');
				}
			}	
		}
		$this->changeorder();	 
		return true;
	 
	}
	
	
	
	public function changeorder(){//改变订单状态并写订单状态改变记录
		$orderid =  $this->drawdata['orderid'];// 订单号
		$status = $this->drawdata['status']; //退款状态 0 用户申请退款（待商家处理） 1 用户取消退款（退款结束） 2 同意退款（退款成功）3 不同意退款（退款失败） 
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' ");
		#print_r($orderinfo);exit;
		$allowreback = Mysite::$app->config['allowreback'];//配置文件中  商家制作后是否支持退款 1支持  0不支持
		$shenhedrawback = Mysite::$app->config['shenhedrawback'];//配置文件中  退款申请是否需要平台审核  1需要   0不需要		 
		$ordercls = new orderclass();
		
		if(empty($orderinfo)){
			$this->error = '订单信息获取失败';
			return false;
		}
		if($status == 0){//用户申请退款（待商家处理）		
			if($orderinfo['is_make'] == 0){//未制作订单	
				if($shenhedrawback == 0 ){//不需要平台审核
				    if($orderinfo['paytype_name'] == 'open_acout'){//余额支付直接退
						$ordercls->writewuliustatus($orderinfo['id'],16,$orderinfo['paytype']);//写订单申请退款记录
						if($this->returncost($orderid) ){
							$arr['is_reback'] = 2;
							$arr['status'] = 4;
							$this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$orderinfo['id']."'");
							 
							$scdata['uid'] = $orderinfo['buyeruid'];
							$scdata['username'] = $orderinfo['buyername'];
							$scdata['reason'] = '平台已退款';
							$scdata['orderid'] = $orderinfo['id'];
							$scdata['shopid'] = $orderinfo['shopid'];		
							$scdata['content'] = '平台已退款';
							$scdata['status'] = 4;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
							$scdata['addtime'] = time();
							$scdata['cost'] = $orderinfo['allcost'];
							$scdata['admin_id'] = $orderinfo['admin_id'];
							$scdata['type'] = 1;
							$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$scdata);   //写退款记录 记录退款成功
							$ordercls->writewuliustatus($orderinfo['id'],19,$orderinfo['paytype']);//写订单日志 记录退款成功
							$ordercls->noticeback($orderinfo['id']);
							}
					}else{//不是余额支付的 就便是后台不需要审核  也要强制审核
						$arr['is_reback'] = 1;
						$this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$orderinfo['id']."'");
						$ordercls->writewuliustatus($orderinfo['id'],16,$orderinfo['paytype']);//写订单日志 记录等待平台审核处理  暂不执行退款   平台审核后退款					
					}
				}else{//需要平台审核
					$arr['is_reback'] = 1;
		            $this->mysql->update(Mysite::$app->config['tablepre'].'order',$arr,"id='".$orderinfo['id']."'");
					$ordercls->writewuliustatus($orderinfo['id'],16,$orderinfo['paytype']);//写订单日志 记录等待平台审核处理  暂不执行退款   平台审核后退款 
				}
			}else{//已制作订单
				if($allowreback == 0){
					$this->error = '订单已制作，不能申请退款';
			        return false;
				}else{  
					$data1['is_reback'] = 4;
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data1,"id='".$orderinfo['id']."'");
					$ordercls->writewuliustatus($orderinfo['id'],13,$orderinfo['paytype']);//写订单日志  记录等待商家审核处理 
				}	
			}
		}
		if($status == 1){ //  用户取消退款（退款结束）	 
			$data2['is_reback'] = 5;
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data2,"id='".$orderinfo['id']."'");
			$ordercls->writewuliustatus($orderinfo['id'],18,$orderinfo['paytype']);//写订单日志  记录等待商家审核处理	
		}
		if($status == 2){// 商家同意退款（退款成功）
		    //$ordercls->noticeback($orderinfo['id']);			
			if($shenhedrawback == 0 && $orderinfo['paytype_name'] == 'open_acout'){ //不需平台审核
				if($this->returncost($orderinfo['id'])){	 
					$data3['is_reback'] = 2;
                    $data3['status'] = 4;					
				    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$data3,"id='".$orderinfo['id']."'");   
					$ordercls->writewuliustatus($orderinfo['id'],19,$orderinfo['paytype']);//写订单日志  记录退款成功
                    $scdata['uid'] = $orderinfo['buyeruid'];
					$scdata['username'] = $orderinfo['buyername'];
					$scdata['reason'] = '平台已退款';
					$scdata['orderid'] = $orderinfo['id'];
					$scdata['shopid'] = $orderinfo['shopid'];		
					$scdata['content'] = '平台已退款';
					$scdata['status'] = 4;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
					$scdata['addtime'] = time();
					$scdata['cost'] = $orderinfo['allcost'];
					$scdata['admin_id'] = $orderinfo['admin_id'];
					$scdata['type'] = 1;
					$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$scdata);   //写退款记录
                    $ordercls->noticeback($orderinfo['id']);					
				}else{
					$this->error = '退款失败';
                    return false;
				}
			}else{//需要平台审核
				$data4['is_reback'] = 1;				
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data4,"id='".$orderinfo['id']."'");
				$ordercls->writewuliustatus($orderinfo['id'],17,$orderinfo['paytype']);//写订单日志 记录等待平台审核
			}
		}
		if($status == 3){//3 商家不同意退款（退款失败） 
			$data5['is_reback'] = 3;
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data5,"id='".$orderinfo['id']."'");
			$statusdata['orderid']     =  $orderinfo['id'];
			$statusdata['statustitle'] =  "商家拒绝退款";
            $statusdata['ststusdesc']  =  '拒绝理由：'.$this->drawdata['reason'];
			$statusdata['addtime']     =  time();
			$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata); //写订单日志 记录商家拒绝退款
			$ordercls->noticeunback($orderinfo['id']);
		}	
		if($status == 4){//4 平台退款
			if($this->returncost($orderinfo['id'])){	
			    $data4['is_reback'] = 2;
                $data4['status'] = 4;					
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data4,"id='".$orderinfo['id']."'");   
			    $ordercls->writewuliustatus($orderinfo['id'],19,$orderinfo['paytype']);//写订单日志  记录退款成功	 
			}
			$ordercls->noticeback($orderinfo['id']);
		}
		return true;
	}
	
	
	public function returncost($orderid){//同意退款后  恢复用户本订单涉及到的资金、积分、优惠券等信息
		
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' ");
		if(empty($orderinfo)){
            $this->error = '订单不存在';
            return false;
		}
		$zengcost = $orderinfo['allcost'];		
		if($orderinfo['status'] == 3  && $orderinfo['is_make'] != 2 ){
			$this->error = '订单状态不能退款';
			return false;
		}  
		if($orderinfo['is_reback'] == 2){
			$this->error = '订单已退款成功不能重复操作';
			return false;
		}  
        if($orderinfo['is_reback'] == 5 && $orderinfo['is_make'] != 2){
			$this->error = '用户已取消退款申请';
			return false;
		}  	
		 
		if($orderinfo['paytype_name'] == 'open_acout'){
			if(!empty($orderinfo['buyeruid'])){		 
				$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   "); 
				if($this->actionclas == null){
					$this->actionclas = new memberclass($this->mysql);	
				}
				if(!empty($memberinfo)){
					$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$zengcost,"uid ='".$orderinfo['buyeruid']."' ");
					if($orderinfo['scoredown'] > 0){
						$shengyujf = $memberinfo['score']+$orderinfo['scoredown']; 
						$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$orderinfo['scoredown'],"uid ='".$orderinfo['buyeruid']."' ");
					    $this->actionclas->addlog($orderinfo['buyeruid'],1,1,$orderinfo['scoredown'],'退款返积分','退款返积分'.$orderinfo['scoredown'],$shengyujf);
					}
				}	
				$shengyucost = $memberinfo['cost']+$zengcost; 
				$this->actionclas->addmemcostlog( $orderinfo['buyeruid'],$memberinfo['username'],$memberinfo['cost'],1,$zengcost,$shengyucost,"管理员退款给用户",ICookie::get('adminuid'),ICookie::get('adminname') );				 
				$this->actionclas->addlog($orderinfo['buyeruid'],2,1,$zengcost,'退款处理','商家拒绝接单',$shengyucost);  
			    
			} 
		}else{
			//非余额支付的在线支付已付订单 如果商家不制作  需要等待平台退款
			if(!empty($orderinfo['buyeruid'])){
			    $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   ");
				if(!empty($memberinfo)){
				     $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$orderinfo['scoredown'],"uid ='".$orderinfo['buyeruid']."' ");
				}
			}
		}		
		
        $ff['is_reback'] = 2;
		$this->mysql->update(Mysite::$app->config['tablepre'].'order',$ff,"id ='".$orderid."' ");		
		return true;
	}

    public function shopcnetersave(){
		if($this->drawdata  == null ){
			$allcost =  IFilter::act(IReq::get('allcost')); 
			$orderid = intval(IFilter::act(IReq::get('orderid')));    // 订单号
			$data['reason'] = trim(IFilter::act(IReq::get('reason'))); //退款原因
			$data['content'] = trim(IFilter::act(IReq::get('content'))); //退款详细内容说明
			$typeid = intval(IFilter::act(IReq::get('typeid'))); //支付类型
		}else{
			$allcost =  $this->drawdata['allcost']; 
			$orderid =  $this->drawdata['orderid'];// 订单号
			$data['reason'] = $this->drawdata['reason']; //退款原因
			$data['content'] = $this->drawdata['content']; //退款详细内容说明
			$typeid = $this->drawdata['typeid']; //支付类型
			
			
		}
		if(!in_array($typeid,$this->typeidlist)){
			$this->error = '未定义退款类型';
			return false;
		}
		if(empty($data['reason'])){
			$this->error = '未选择退款原因';
			return false;
		}
	 
				
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' "); 		
		if(empty($orderinfo)){
				$this->error = '订单不存在';
				return false;
		}  
	 
		if($orderinfo['allcost'] != $allcost ) { $this->error = "退款金额错误";return false; }
	 
		$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."' "); 
		
		if(empty($memberinfo)){
			$this->error = '获取会员信息错误';
			return false;
		}
		
		
		if($orderinfo['paystatus'] != 1){
				$this->error = '该订单未支付';
				return false;
		}
		if($orderinfo['status'] < 1 && $orderinfo['status'] < 3){
				$this->error = '订单状态不能申请退款';
				return false;
		} 
		if($orderinfo['paytype'] == 0||empty($orderinfo['paytype'])){
				$this->error = '货到支付订单';
				return false;
		}
		if(!empty($orderinfo['is_reback'])){
				$this->error = '已申请退款';
				return false;
		}
		 
		 if(empty($data['content'])){
			 $this->error = '请填写退款详细内容说明';
			 return false;
		 } 
	 
		$checklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid='".$orderinfo['id']."' "); 
		if(!empty($checklog)){
			$this->error = '已申请过退款';
			return false;
		}
		$data['orderid'] = $orderinfo['id'];
		$data['shopid'] = $orderinfo['shopid'];
		$data['uid'] = $memberinfo['uid'];
		$data['username'] = $memberinfo['username'];
		$data['status'] = 0;
		$data['addtime'] = time();
		$data['cost'] = $orderinfo['allcost'];
		$data['admin_id'] = $orderinfo['admin_id'];
		$data['type'] = $typeid;
		$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);   
		$udata['is_reback'] = 1;
		$this->mysql->update(Mysite::$app->config['tablepre'].'order',$udata,"id='".$orderinfo['id']."'"); 
		$ordCls = new orderclass();
		$ordCls->writewuliustatus($orderinfo['id'],13,$orderinfo['paytype']);  // 管理员 操作配送发货
		return true;
	}
	
	
	//返回错误
	public function GetErr(){
		return $this->error;
	}  
	//返回退款类型
	public function gettype(){
		return $this->logtype;
		
	} 
	/**
	 *  @brief Brief
	 *  
	 *  @param [in] $type 操作类型  0-同意退款  1表示取消退款
	 *  			$role 操作者   admin-后台管理员  areaadmin-区域管理员  shop-店铺
	 *  			$roleid 操作者ID   $role=admin-后台管理ID   $role=areaadmin-区域管理员uid   $role=shop-店铺所有者ID 
	 *  @return true/false;
	 *  
	 *  @details Details
	*/
	public function control($type=0,$role='uid',$roleid='0',$orderid){  
		$drawbacklog = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid=".$orderid." order by  id desc  limit 0,2");
		if(empty($drawbacklog)){
				$this->error = '退款记录为空';
				return false;
		}
		if($drawbacklog['status'] != 0){
			$this->error = '退款记录已处理过';
			return false;
		}
		if($type == 0){
		    if($role == 'uid'){
				//店铺  同意
				if($drawbacklog['type']==0){
					$this->error = '退款到支付宝需要网站后台处理';
					return false;
				} 
			}
			$data['bkcontent'] = IReq::get('reasons');
			$data['status'] = 2;//
			$this->mysql->update(Mysite::$app->config['tablepre'].'drawbacklog',$data,"id='".$drawbacklog['id']."'");  
		   
		}elseif($type==1){
			$data['status'] = 3;//
			$this->mysql->update(Mysite::$app->config['tablepre'].'drawbacklog',$data,"id='".$drawbacklog['id']."'"); 
		}
		return true; 
	}  
	
	private function CkMem(){
		$memberinfo = $this->GetMem;
	}
	private function CkAdmin(){
		$memberinfo = $this->GetAdmin;
	}
	//返回用户信息
	private function GetMem(){
		return $this->actionclas->getinfo();
	}
	//放回 管理员信息
	private function GetAdmin(){
		return $this->actionclas->getadmininfo();
	}  
}
?>