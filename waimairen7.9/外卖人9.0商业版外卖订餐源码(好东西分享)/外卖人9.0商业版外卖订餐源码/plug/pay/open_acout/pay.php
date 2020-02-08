<?php 
if(!defined('IN_WaiMai')) {
	exit('Access Denied');
}
/*if(Mysite::$app->config['open_acout'] != 1){
			$errdata['url'] = $payerrlink;
				$errdata['reason'] = '网站未开启支付，不能支付s';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata); 
}*/
if($userid == 0){
		        $errdata['url'] = $payerrlink;
				$errdata['reason'] = '未登录账号不可使用余额支付';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
}



if($this->member['cost'] < $dopaydata['cost']){
			    $errdata['url'] = $payerrlink;
				$errdata['reason'] = '账号余额不足支付此订单';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);   
}
if($dopaydata['type'] == 'order'){
		/*
		$paypwd = IFilter::act(IReq::get('paypwd'));
		if(empty($this->member['safepwd'])){
		 $link = IUrl::creatUrl('member/paylog/'); 
		$this->message('余额支付请先设置支付密码',$link);  
		} 
		$checkmd5 = md5($paypwd);
		if($checkmd5 != $this->member['safepwd']){
			$link = IUrl::creatUrl('site/waitpay/orderid/'.$orderid); 
		$this->success('支付密码错误',$payerrlink);  
		}*/
		$orderid = $dopaydata['upid'];
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");  //获取主单
		$shopinfo = $this->mysql->select_one("select is_autopreceipt from ".Mysite::$app->config['tablepre']."shop where id=".$orderinfo['shopid']."  ");  //获取主单
		//	print_r($orderinfo);
			if(empty($orderinfo)){
					$errdata['url'] = $payerrlink;
					$errdata['reason'] = '订单数据获取失败';
					$errdata['paysure'] = false;  
					$this->showpayhtml($errdata);  
			}
		//更新用户数据
		$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`-'.$orderinfo['allcost'],"uid ='".$this->member['uid']."' ");
		//更新订单数据 
		$orderCLs = new orderclass();
		$orderCLs->writewuliustatus($orderinfo['id'],3,$orderinfo['paytype']);  //在线支付成功状态
		if($orderinfo['shoptype']!=100){
			if($orderCLs->sendWxMsg($orderinfo['id'],2,1)){
				
			}
		}else{
			if($orderCLs->sendWxMsg($orderinfo['id'],2,3)){
				
			}
		}	
		if($shopinfo['is_autopreceipt'] == 1){
			//logwrite(222);
			$orderdata['is_make'] = 1;
			$orderdata['maketime'] = time();
			$orderinfo['is_make'] = 1;
			$orderCLs->writewuliustatus($orderinfo['id'],4,$orderinfo['paytype']);  //商家制作
		}
		//logwrite(333); 
		$orderdata['paystatus'] = 1;
		if($orderinfo['status'] == 0){
		   // if($orderinfo['shoptype'] != 100){
			    $orderdata['status'] = 1;
		   // }
		}
		$orderdata['paytime'] = time();
		$orderdata['addtime'] = time();
		$orderdata['paytype_name'] = $dopaydata['paydotype'];
		$this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"id ='".$orderid."' ");
		
		$accost = $this->member['cost']-$orderinfo['allcost'];
		$this->memberCls->addlog($this->member['uid'],2,2,$orderinfo['allcost'],'余额支付订单','支付订单'.$orderinfo['dno'].'帐号金额减少'.$orderinfo['allcost'].'元', $accost);
		$this->memberCls->addmemcostlog($orderinfo['buyeruid'],$this->member['username'],$this->member['cost'],2,$orderinfo['allcost'],$accost,"下单余额消费",$this->member['uid'],$this->member['username']);

		$checkflag = false;
		 

		
		if($orderinfo['is_make']  == 1 ){
			$auto_send = Mysite::$app->config['auto_send'];
			if($auto_send == 1){
				//logwrite('auto_send');
//				$orderCLs->writewuliustatus($orderinfo['id'],6,$orderinfo['paytype']);//订单审核后自动 商家接单后自动发货
//				$orderdatac['sendtime'] = time();
//				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdatac,"id ='".$orderid."' ");
			}else{
				//自动生成配送单
				//自动生成配送单------|||||||||||||||-----------------------  8.6
				//logwrite('shoptype');
				if($orderinfo['shoptype'] != 100){
					if($orderinfo['pstype'] == 0 ){//网站配送自动生成配送费
						 
						$orderpsinfo  =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where  orderid ='".$orderid."'   ");
						if(empty($orderpsinfo)){
							$psdata['orderid'] = $orderinfo['id'];
							$psdata['shopid'] = $orderinfo['shopid'];
							$psdata['status'] =0;
							$psdata['dno'] = $orderinfo['dno'];
							$psdata['addtime'] = time();
							$psdata['pstime'] = $orderinfo['posttime']; 
							$admin_id = $orderinfo['admin_id'];
							$psset = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$admin_id."'   ");
							$checkpsyset = $psset['psycostset'];
							$bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
							$psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei; 
							$this->mysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单  
						} 
						$checkflag = true;
					}elseif($orderinfo['pstype'] == 2 && $orderinfo['is_ziti'] == 0 ){
						$psbinterface = new psbinterface();
						if($psbinterface->psbnoticeorder($orderid)){
							$checkflag = false;
						}else{
							logwrite('error'.$psbinterface->err());
						}
					}
				}else{
					//生成跑腿订单的办法调用

						$psbinterface = new psbinterface();
						if($psbinterface->paotuitopsb($orderid)){
							$checkflag = false;
						}else{
							logwrite('error'.$psbinterface->err());
						}

				}
				//自动生成配送单结束------------- 
			}
		}else{
			//logwrite($orderinfo['id'].'is_make'.$orderinfo['is_make']);
			if($orderinfo['shoptype'] == 100){
			 
					$psbinterface = new psbinterface();
					if($psbinterface->paotuitopsb($orderid)){
						$checkflag = false;
					}else{
						logwrite('error'.$psbinterface->err());
					}

			}
		}
		if($orderinfo['shoptype'] != 100){
			if($orderCLs->sendWxMsg($orderinfo['id'],4,1)){
				
			}
		}
		$orderCLs->sendmess($orderid); 
		$errdata['url'] = $payerrlink;
		$errdata['reason'] = $orderinfo;
		$errdata['paysure'] = true; 
		$errdata['paytype'] = 'order';  		
		$errdata['source'] = $dopaydata['source'];
		if($checkflag ==true){
			$psylist =  $this->mysql->getarr("select a.*  from ".Mysite::$app->config['tablepre']."apploginps as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.admin_id = ".$orderinfo['admin_id']."");
			$psCls = new apppsyclass(); 
			$psCls->SetUserlist($psylist)->sendNewmsg('订单提醒','有新订单可以处理'); 
		}
		$this->showpayhtml($errdata); 

	exit;
}elseif($dopaydata['type'] == 'yhorder'){
	$orderid = $dopaydata['upid'];
	$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophuiorder where id=".$orderid."  ");  //获取主单
    $orderinfo['allcost']=$orderinfo['sjcost'];
	if(empty($orderinfo)){
			$errdata['url'] = $payerrlink;
			$errdata['reason'] = '订单数据获取失败';
			$errdata['paysure'] = false;  
			$this->showpayhtml($errdata);  
	} 
	$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`-'.$orderinfo['sjcost'],"uid ='".$this->member['uid']."' ");
	//更新订单数据 
	$orderdata['paystatus'] = 1; 
    $orderdata['status'] = 1; 
    $orderdata['completetime'] = time();
	$orderdata['paytime'] = time();
    $orderdata['paytype'] =3;
	$orderdata['paytype_name'] ='open_acout';
	$this->mysql->update(Mysite::$app->config['tablepre'].'shophuiorder',$orderdata,"id ='".$orderid."' ");
	 $accost = $this->member['cost']-$orderinfo['sjcost'];
	$this->memberCls->addlog($this->member['uid'],2,2,$orderinfo['sjcost'],'余额优惠买单','余额优惠买单'.$orderinfo['id'].'帐号金额减少'.$orderinfo['sjcost'].'元', $accost);
	$this->memberCls->addmemcostlog($this->member['uid'],$this->member['username'],$this->member['cost'],2,$orderinfo['sjcost'],$accost,"余额优惠买单消费",$this->member['uid'],$this->member['username']);
	$errdata['url'] = $payerrlink;
	$errdata['reason'] = $orderinfo;
	$errdata['paysure'] = true;  
	$errdata['paytype'] = 'yhorder';  
	$errdata['source'] = $dopaydata['source'];   
	if($orderinfo['uid'] > 0){ 
		if($orderinfo['givejifen'] > 0){
			$sorce = $orderinfo['givejifen'];
			$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$sorce,"uid ='".$this->member['uid']."' ");
			$allscore = $this->member['score']+$sorce; 
			$this->memberCls->addlog($this->member['uid'],1,1,$sorce,'优惠买单','赠送积分',$allscore);
		} 
	} 
	$url = Mysite::$app->config['siteurl']."/index.php?ctrl=wxsite&action=subpayhui&orderid={$orderinfo['id']}";
	header("location:{$url}");
	exit; 
}else{
	$errdata['url'] = $payerrlink;
				$errdata['reason'] = '不支持的订单类型';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata); 
				exit;
}
?>