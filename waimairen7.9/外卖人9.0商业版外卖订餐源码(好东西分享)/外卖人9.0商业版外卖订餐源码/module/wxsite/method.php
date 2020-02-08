<?php
/*
*   method 方法  包含所有会员相关操作
    管理员/会员  添加/删除/编辑/用户登录
    用户日志其他相关连的通过  memberclass关联
*/
class method   extends wxbaseclass{   
    /********首页购物车相关代码开始*********/
	//首页购物车页面
	 
	function index_cart(){ 
        $link = IUrl::creatUrl('wxsite/loginmode');
	    if($this->member['uid'] == 0)  $this->message('',$link); 	
		$smardb = new newsmcart();
		$smardb->setdb($this->mysql)->ShopTJList();
		$carinfo = $smardb->getdata();
		$nowhour = date('H:i:s',time());
		$nowhour = strtotime($nowhour); 
		$cartlist = array();
		if(!empty($carinfo)){	             
			foreach($carinfo as $k=>$v){
				 
				$smardb->setdb($this->mysql)->SetShopId($v['shopinfo']['id'])->OneShop();
				$det = $smardb->getdata(); 
			 
				$det['goodslistx'] = array();
				if(!empty($det['goodslist'])){
					foreach($det['goodslist'] as $k1=>$v1){
						if($v1['count'] <= $v1['stock']){
							$vv['id'] = $v1['id'];
							$vv['name'] = $v1['name'];
							$vv['count'] = $v1['count'];
							$vv['cost'] = $v1['cost'];
							$vv['img'] = getImgQuanDir($v1['img']);
							$vv['have_det'] = $v1['have_det'];
							$vv['is_live'] = $v1['is_live'];
							$vv['attrname'] = $v1['have_det'] == 1?$v1['gg']['attrname']:'';
							$vv['oldcost'] = $v1['is_cx'] == 1?$v1['cxinfo']['oldcost']:$v1['cost'];
							$vv['is_cx'] = $vv['cost'] < $vv['oldcost']?1:0;
							$det['goodslistx'][] = $vv;
						}
					}
				} 
				$det['shopinfo']['shoplogo'] = empty($det['shopinfo']['shoplogo'])?Mysite::$app->config['shoplogo']:$det['shopinfo']['shoplogo'];
				$det['shopinfo']['shoplogo'] = getImgQuanDir($det['shopinfo']['shoplogo']);
				$shoptype = $det['shopinfo']['shoptype'] == 0 ?'shopfast':'shopmarket'; 
				$det['shopinfo']['shoptypex'] = $det['shopinfo']['shoptype'] == 0 ? 'wm':'mk';
				$shopdetinfo = $this->mysql->select_one("select limitcost,is_orderbefore from  ".Mysite::$app->config['tablepre'].$shoptype." where shopid='".$det['shopinfo']['id']."' ");
				$det['shopinfo']['limitcost'] = $shopdetinfo['limitcost'];
				$checkinfo = $this->shopIsopen($v['shopinfo']['is_open'],$v['shopinfo']['starttime'],$shopdetinfo['is_orderbefore'],$nowhour);
				$det['shopinfo']['opentype'] = $checkinfo['opentype']; 
				unset($v['shopinfo']);
				unset($v['sum']);
				unset($v['count']);
				unset($det['goodslist']);
				$det['goodscxdowncost'] = $this->formatcost($det['goodscxdowncost'],2);
				$det['bagcost'] = $this->formatcost($det['bagcost'],2);
				$det['sum'] = $this->formatcost($det['sum'] + $det['bagcost'],2);
				$det['chacost'] = $det['sum'] < $det['shopinfo']['limitcost']?$this->formatcost($det['shopinfo']['limitcost'] - $det['sum'],2):0;			 
				if(!empty($det['goodslistx'])){
					$v = $det; 
				    $cartlist[] = $v;
				}
			}
		}
		#print_r($cartlist);exit; 
		$data['list'] = $cartlist;
		Mysite::$app->setdata($data); 
	}
	function clearallcart(){
		$smardb = new newsmcart();		
		if($smardb->setdb($this->mysql)->ClearCart()){
		    $this->success();
		}else{
			$this->message($smardb->getError());
		}
	}
	function clearoneshopcart(){
		$shopid = IReq::get('shopid');
		$smardb = new newsmcart();	
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->DelShop()){
		    $this->success();
		}else{
			$this->message($smardb->getError());
		}
	}
	/********首页购物车相关代码结束*********/
	//分销中心
	function distribution_center(){	   	 		 		 		
		if($this->member['uid'] == 0)  $this->message('未登录');
		$data['userlogo'] = empty($this->member['logo'])?Mysite::$app->config['userlogo']:$this->member['logo'];
	    $data['username'] = $this->member['username'];		 
		$data['cost'] = $this->formatcost($this->member['fxcost'],2);
	    $allordercost = $this->mysql->select_one("select sum(ordercost) as allordercost, sum(yjbcost) as allyjcost from  ".Mysite::$app->config['tablepre']."fxincomelog where uid=".$this->member['uid']." ");
	    $data['allordercost'] = $this->formatcost($allordercost['allordercost'],2);
	    $data['allyjcost'] = $this->formatcost($allordercost['allyjcost'],2);
		if(empty($this->member['invitecode'])){		
            $invitecode	= $this->recursion();			 
           	$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('invitecode'=>$invitecode),"uid='".$this->member['uid']."'");
            $data['invitecode'] = $invitecode;			
		}else{
			$data['invitecode'] = $this->member['invitecode'];
		}
	    Mysite::$app->setdata($data);
    }
	//递归函数--随机生成邀请码并检测其唯一性后返回邀请码
	function recursion(){
		$roundnumber = rand(100000,999999);	 
		$cinfo = $this->mysql->select_one("select uid from  ".Mysite::$app->config['tablepre']."member where invitecode=".$roundnumber." ");
		if(empty($cinfo)){
			return	$roundnumber;		
		}else{
			$this->recursion();
		}
	}	
	//分销二维码
	function distribution_fxcode(){
	    $uid = IReq::get('uid');
		$uid = empty($uid)?$this->member['uid']:$uid;
		if(empty($uid))  $this->message('用户uid获取失败');
	    $member = $this->mysql->select_one("select uid,logo,username,fxcode,invitecode from  ".Mysite::$app->config['tablepre']."member where uid=".$uid." "); 
		#print_r($member);exit;
		$data['uid'] = $uid;
		$data['userlogo'] = empty($member['logo'])?Mysite::$app->config['userlogo']:$member['logo'];
	    $data['username'] = $member['username'];
		$data['fxcode'] = $member['fxcode'];		 
 		$data['invitecode'] = $member['invitecode'];
        // $wxclass = new wx_s();
		// $signPackage = $wxclass->getSignPackage();
 		// $data['signPackage'] = $signPackage;
		$shareinfo['title'] = Mysite::$app->config['fxcodetit'];
		$shareinfo['describe'] = Mysite::$app->config['fxcodedes'];
		$shareinfo['img'] = getImgQuanDir($data['fxcode']);
		$shareinfo['url'] = Mysite::$app->config['siteurl'].'/index.php?ctrl=wxsite&action=distribution_fxcode&uid='.$uid;
		$data['shareinfo'] = $shareinfo;			     
		Mysite::$app->setdata($data);
    }
	//我的佣金
	function distribution_myyj(){
		if($this->member['uid'] == 0)  $this->message('未登录');
		$stime = strtotime(date('Y-m-d', time()));
		$todaycost = $this->mysql->select_one("select  sum(yjbcost) as yjbcost from  ".Mysite::$app->config['tablepre']."fxincomelog where uid=".$this->member['uid']." and addtime > ".$stime." and addtime < ".time()." ");
		$data['todaycost'] = $this->formatcost($todaycost['yjbcost'],2);//今日收益佣金
		$allcost = $this->mysql->select_one("select  sum(yjbcost) as yjbcost from  ".Mysite::$app->config['tablepre']."fxincomelog where uid=".$this->member['uid']."  ");
		$data['allcost'] = $this->formatcost($allcost['yjbcost'],2);//累计收益佣金
		$data['cantxcost'] = $this->formatcost($this->member['fxcost'],2);//可提现金额
		$txcost = $this->mysql->select_one("select  sum(reallycost) as reallycost from  ".Mysite::$app->config['tablepre']."distributiontxlog where uid=".$this->member['uid']." and status = 1 ");
		$data['txcost'] = $this->formatcost($txcost['reallycost'],2);//已提现佣金（扣除手续费后的实际提现到账金额）
		Mysite::$app->setdata($data);	
	}
	//我的下线
	function distribution_myjunior(){	     
   		$link = IUrl::creatUrl('wxsite/index');
	    if($this->member['uid'] == 0)  $this->message('',$link);
		$data['checkmember'] = $this->mysql->getarr("select uid from ".Mysite::$app->config['tablepre']."member where fxpid='".$this->member['uid']."' ");
		Mysite::$app->setdata($data);
    }
	//我的下线列表  直接实例化distribution类  调用getmemberlist函数  按顺序传三个参数  1.当前登录用户uid  2.页码  3.下线等级	
	function myjuniorlist(){		
		$page = intval(IReq::get('page'));
		$grade = intval(IReq::get('grade'));		 
		$distribution = new distribution();
		$memberlist = $distribution->getmemberlist($this->member['uid'],$page,$grade);
		$data['memberlist'] = $memberlist;
        Mysite::$app->setdata($data);			
	}
	//分销佣金申请提现
	function dofxtx(){	         	
		$uid = $this->member['uid'];
		$txtype = IFilter::act(IReq::get('txtype'));
		$txcost = IFilter::act(IReq::get('txcost'));		 
		$zfbaccount = IFilter::act(IReq::get('zfbaccount'));
		$zfbusername = IFilter::act(IReq::get('zfbusername'));
		$cardusername = IFilter::act(IReq::get('cardusername'));
		$cardnumber = IFilter::act(IReq::get('cardnumber'));
		$bankname = IFilter::act(IReq::get('bankname'));		
		$txdata = array(
		    'uid'=>$uid,
			'txtype'=>$txtype,
			'txcost'=>$txcost,
			'zfbaccount'=>$zfbaccount,
			'zfbusername'=>$zfbusername,
			'cardusername'=>$cardusername,
			'cardnumber'=>$cardnumber,
			'bankname'=>$bankname,
		);
		$distribution = new distribution();
		if($distribution->tixian($txdata)){
			$this->success("提现申请成功");
		}else{
			$this->message($distribution->Error());
		}			
	}
	//制作分销二维码
	function makefxcode(){
		$uid = IFilter::act(IReq::get('uid'));
		$member = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."member where uid = '".$uid."'");
		 
		if(!empty($member)){
			$wx_s = new wx_s();
			$ifmake = $wx_s->makefxcode($uid);
			if($ifmake == true){
			   if($wx_s->get_fxcodeurl($uid)){
				   $this->success("生成二维码数据成功");
			   }else{
				   $this->message("生成二维码数据失败");	
			   }
			}else{
				$this->message("wx_s生成二维码数据失败");			
			} 
		}	
	}
	//检测二维码是否存在
	function checkcode(){
		$uid = IReq::get('uid');	  
	    $member = $this->mysql->select_one("select fxcode from  ".Mysite::$app->config['tablepre']."member where uid=".$uid." ");  
 		if(!empty($member['fxcode'])){
			$imgx = explode('/',$member['fxcode']); 
			$img = end($imgx);
			$file = hopedir."/images/user/wxcode/".$img;
			if(file_exists($file)){
				$this->success($member['fxcode']);
			}else{
				$this->message("生成失败，请稍后重试~");
			}
		}else{
			$wx_s = new wx_s();
			if( $wx_s->makefxcode($uid) ){
				if($wx_s->get_fxcodeurl($uid)){
					$member = $this->mysql->select_one("select fxcode from  ".Mysite::$app->config['tablepre']."member where uid=".$uid." ");  
 				   $this->success($member['fxcode']);
			   }else{
				   $this->message("生成二维码数据失败");	
			   }
			}else{
				$this->message("生成失败，请稍后重试！");
			} 	
		}
	}
	//提现记录
	function distribution_txlog(){		
		if($this->member['uid'] == 0)  $this->message('未登录');
		$data['checklog'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."distributiontxlog where uid =".$this->member['uid']." ");
		Mysite::$app->setdata($data);		
	}
	//提现记录列表
	function txloglist(){
		if($this->member['uid'] == 0)  $this->message('未登录');
		$page = intval(IReq::get('page'));		 
		$distribution = new distribution();
		$loglist = $distribution->gettxloglist($this->member['uid'],$page);
		$data['loglist'] = $loglist;		 
        Mysite::$app->setdata($data);	
	}
	//提现详情
	function fxtxdet(){
		if($this->member['uid'] == 0)  $this->message('未登录');
		$id = intval(IReq::get('id'));	
		$distribution = new distribution();
		$data['logdet'] = $distribution->gettxlogdet($id);
		Mysite::$app->setdata($data);			
	}
	//佣金明细
	function distribution_yjdet(){
		if($this->member['uid'] == 0)  $this->message('未登录');
		$data['checklog'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."fxincomelog where uid = ".$this->member['uid']." ");		 
		Mysite::$app->setdata($data);					
	}
	//佣金明细列表
	function yjloglist(){
		if($this->member['uid'] == 0)  $this->message('未登录');
		$page = intval(IReq::get('page'));	
	    $searchvalue = trim(IReq::get('searchvalue'));		
		$distribution = new distribution();
		$loglist = $distribution->getyjloglist($this->member['uid'],$page,$searchvalue);
		$data['loglist'] = $loglist;
        Mysite::$app->setdata($data);	
	}
	//佣金排名
	function yjranking(){		 
		if($this->member['uid'] == 0)  $this->message('未登录');
		$distribution = new distribution();
		$data = $distribution->yjranking($this->member['uid']);//array('list'=>排名列表,selfranking=>自身排名);    	
		Mysite::$app->setdata($data);	
	}
    function setlogin(){ 
		if(Mysite::$app->config['wxLoginType']==0){ 
			if($this->member['uid'] < 1){
				$this->wxlogin();
				$this->setwxlogin(1);
			}
			if(empty($this->member['phone'])){
					session_start();
					$_SESSION['bindingwxlogin'] =time(); 
					$link = IUrl::creatUrl('wxsite/wxbdphone');
					$this->message('',$link);
			}else{ 
				$defaultlink = IUrl::creatUrl('wxsite/member');
				$weblink = ICookie::get('wx_login_link');
				$link = empty($weblink)? $defaultlink:$weblink;
				$this->message('',$link);
			}
		}else{
			$this->wxlogin();
			$this->setwxlogin(1); 
		}  
    }
   
    function savewxbd(){
        $phone = IFilter::act(IReq::get('phone'));
        $code = IFilter::act(IReq::get('code'));
        $codec = ICookie::get('bindingwxcode');
		
		$openid = ICookie::get('bindwxopenid');
		if(empty($openid)){
			$this->message("请重新授权登录已过期");
		}
		
		$oauthinfo=$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where openid='".$openid."'  "); 
		if(empty($oauthinfo)){
			$this->message("授账号信息不存在");
		}
		$membercheck = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$oauthinfo['uid']."' ");
		if(empty($membercheck)){
			$this->message("账号信息不存在");
		}
		if(!empty($membercheck['phone'])){
			// if($phone != $membercheck['phone']){
				$this->message("该账号已绑定手机号");
			// }
		} 
		// if(!empty($memberphone)){
				// $this->message("该手机号已绑定其他账号，请先解绑");
		// }
        $checklogins = 0;
        $phonecode = new phonecode($this->mysql,8,$phone);
		if($phonecode->checkcode($code)){
			
		}else{
		    $err = $phonecode->getError(); 
		    $this->message($err);
		}  
		$memberphone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$phone."' ");
		$checklogins = 1;
		if(!empty($memberphone)){//当绑定的手机号 存在对应手机账号时 
			if($oauthinfo['uid'] != $memberphone['uid']){//不是微信绑定时
				// $this->message("该手机号已绑定其他账号，请先解绑");
				$oauthinfo2 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where uid='".$memberphone['uid']."'  "); 
				if(empty($oauthinfo2)){//说明没绑定其他手机账号 合并会员信息
					$yuid = $membercheck['uid'];//将
					$wx['uid'] = $memberphone['uid']; 
					$this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$wx,"openid='".$oauthinfo['openid']."'");
					$oauthinfo['uid'] = $memberphone['uid'];  
					$tcuser['cost'] = 0;
					$tcuser['score'] = 0;

					$this->mysql->update(Mysite::$app->config['tablepre'].'member',$tcuser,"uid='".$yuid."'");
					$juan['uid'] = $memberphone['uid'];
					$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$juan,"uid='".$yuid."'");
					$address['userid'] = $memberphone['uid'];
					$this->mysql->update(Mysite::$app->config['tablepre'].'address',$address,"userid='".$yuid."'");
					$orderdata['buyeruid'] = $memberphone['uid'];
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',$orderdata,"buyeruid='".$yuid."'"); 
					$cnewdata['cost'] = $memberphone['cost']+$membercheck['cost'];
					$cnewdata['score'] = $memberphone['score']+$membercheck['score'];
					$this->mysql->update(Mysite::$app->config['tablepre'].'member',$cnewdata,"uid='".$memberphone['uid']."' ");
					$membercheck =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$phone."' ");
					$checklogins = 2;
				}else{
					//
					$this->message("该手机号已绑定其他微信账号，请先解绑");
				}
			}else{
				$this->message("该账号已绑定手机号");
			}
		}else{//当不存在对应手机号时:更新账号手机号 
			$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('phone'=>$phone),"uid='".$membercheck['uid']."'");  
			$membercheck =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone ='".$phone."' ");
		}
		//写cookies
		 
		ICookie::set('logintype','wx',86400);
		ICookie::set('wxopenid',$oauthinfo['openid'],86400);  
		 #ICookie::set('email',$userinfo['email'],86400);
		ICookie::set('memberpwd',$membercheck['password'],86400);
		ICookie::set('membername',$membercheck['username'],86400);
		ICookie::set('uid',$membercheck['uid'],86400);   
        // $checklogins = ICookie::get('checklogins');
		
		  ICookie::clear('bindwxopenid');
		
		/* 更新绑定手机号有关优惠券信息 */
		$memberCls = new memberclass($this->mysql);  
		$memberCls->updatememjuaninfo($phone);
        $this->success($checklogins);
    }
	function updateusername(){	
	    
		if($this->member['uid'] == 0)  $this->message('未登录');	
       
		if($this->member['md_flag'] == 1){
            $this->message('用户仅能修改一次用户名');
        }
        $data['username'] =  trim(IFilter::act(IReq::get('username')));	 
		if(!(IValidate::len($data['username'],5,24))) $this->message('用户名长度不符合要求'); 
        if(empty($data['username'])) $this->message('新用户名为空');
        $checkmid =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username='".$data['username']."' ");
	    if(!empty($checkmid)) $this->message('用户名已存在');
        $data['md_flag'] = 1;
		
        $this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$this->member['uid']."'");
        $this->success('success');	
	}
	function quxiaotk(){
		$orderid = IFilter::act(IReq::get('orderid'));
        $orderinfo = $this->mysql->select_one("select id,allcost,admin_id,shopid,buyeruid,buyername,status,is_reback,shoptype from ".Mysite::$app->config['tablepre']."order where id ='".$orderid."'  ");
		if(empty($orderinfo))$this->message("订单不存在");
		if($orderinfo['shoptype'] == 100){
			$data['uid'] = $orderinfo['buyeruid'];
			$data['username'] = $orderinfo['buyername'];			 
			$data['orderid'] = $orderinfo['id'];
			$data['shopid'] = $orderinfo['shopid'];		
			$data['status'] = 1;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
			$data['addtime'] = time();
			$data['cost'] = $orderinfo['allcost'];
			$data['admin_id'] = $orderinfo['admin_id'];
			$data['type'] = 0;
			$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);   //写退款记录	
			$orderClass = new orderclass();
			$orderClass->writewuliustatus($orderinfo['id'],16,$orderinfo['paytype']);  
            $this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>5),"id='".$orderinfo['id']."'");
			$psbinterface = new psbinterface();
			if($psbinterface->psbqxdraworder($orderinfo['id'])){
			}
            $this->success('取消退款申请成功');			
		}else{
			$drawback = new drawbacklog($this->mysql);	
			$ddata=array('allcost'=>$orderinfo['allcost'],'orderid'=>$orderinfo['id'],'typeid'=>'1','status'=>'1','uid'=>$orderinfo['buyeruid']);		 	 
			$msg = $drawback->setsavedraw($ddata)->save();
			if($msg){	  
			    $psbinterface = new psbinterface();
				if($psbinterface->psbqxdraworder($orderinfo['id'])){
				}
				$this->success('取消退款申请成功');
			}else{
				$this->message($msg);
			}
		}		
	}
    function orderhide(){
		$orderid = IFilter::act(IReq::get('orderid'));
		$orderinfo = $this->mysql->select_one("select id,status,is_reback from ".Mysite::$app->config['tablepre']."order where id ='".$orderid."'  ");
		if(!empty($orderinfo)){
			if($orderinfo['status']<3){
				$this->message("订单状态未完成不可删除");
			}else{
				if($orderinfo['is_reback'] == 1 || $orderinfo['is_reback'] == 4 ){
					$this->message("订单状态涉及退款操作不可删除");
				}
			}
			$data['is_userhide'] = 1; 
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$data,"id='".$orderid."'");
			$this->success('订单删除成功');
		}else{
			$this->message("订单数据获取失败");
		}
	}
    function loginmode(){
        if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){
            $data['is_wxlogin'] = 1;
        }else{
            $data['is_wxlogin'] = 0;
        }
        Mysite::$app->setdata($data);
    }
    function saveqqbd(){
        $phone = IFilter::act(IReq::get('phone'));
        $code = IFilter::act(IReq::get('code'));
        //$codec = ICookie::get('safecode');
		#print_r($codec);exit;
        $pass = intval(IReq::get('pass'));
		$phonecls = new phonecode($this->mysql,8,$phone);
		if($phonecls->checkcode($code)){
			$is_user = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."member where phone ='".$phone."'  ");
			if(!empty($is_user)){
				$is_qquser = $this->mysql->select_one("select openid from ".Mysite::$app->config['tablepre']."oauth where uid ='".$is_user['uid']."'  ");
				if(!empty($is_qquser)){
					$this->message("该手机号已绑定其他qq账号，请先解绑");
				}
			}
			$qquser = ICookie::get('qquser');
			$qquser['phone'] = $phone;
			include(hopedir.'/plug/login/qqphone/ghqqOauth.php');
			$ghqqOauth = new ghqqOauth();
			//print_r(111);
			$ghqqOauth->init();
			//print_r(222);
			$ghqqOauth->getuserinfo($qquser);
			//print_r(333);exit;
			$checklogins = ICookie::get('checklogins');
			/* 更新绑定手机号有关优惠券信息 */
			$memberCls = new memberclass($this->mysql);  
			$memberCls->updatememjuaninfo($phone);
			$this->success($checklogins);	
		}else{

			 $this->message($phonecls->getError());
		}
    }
	function postmsg(){
		$orderid = intval(IReq::get('orderid'));	
		if(empty($orderid)) $this->message('订单ID错误');		
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
		if(empty($orderinfo)) $this->message('订单不存在');	  
		$orderclass = new orderclass();		
		$orderclass->sendmess($orderinfo['id']); 	
		$link = IUrl::creatUrl('wxsite/subshow/orderid/'.$orderid); 		
		$this->message('',$link);
	}


	 function choice(){
	  #	$this->checkwxweb();
	  	$id =IFilter::act(IReq::get('id'));   
	 	  if($id > 0){
	 	     $checkinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where id=".$id."  ");
	 	     if(empty($checkinfo)){
	 	          	$link = IUrl::creatUrl('wxsite/choice');
	    	        $this->message('',$link);
	 	     }
	 	     $checkinfo2 =  $this->mysql->select_one("select id,name,parent_id from ".Mysite::$app->config['tablepre']."area where parent_id=".$id."  "); 
	 	     if(empty($checkinfo2)){
	 	                ICookie::set('lng',$checkinfo['lng'],2592000);  
	                	ICookie::set('lat',$checkinfo['lat'],2592000);  
	    	            ICookie::set('mapname',$checkinfo['name'],2592000);  
	    	            ICookie::set('addressname',$checkinfo['name'],2592000);  
	    	            ICookie::set('myaddress',$checkinfo['id'],2592000);  
	    	            $cookmalist  = ICookie::get('cookmalist');
	    	            $cooklnglist = ICookie::get('cooklnglist');
	    	            $cooklatlist = ICookie::get('cooklatlist');
	    	            $link = IUrl::creatUrl('wxsite/shoplist/areaid/'.$checkinfo['id']);
	    	            $this->message('',$link); 
	       }
	    }

		$cook_adrlistcookie = $_COOKIE['cook_adrlistcookie'];
		$adrarr = explode('#',$cook_adrlistcookie);
		$adrtemparr = array(); 
		if(!empty($adrarr)){
			foreach($adrarr as $key=>$value){
				$adrtemparr[] = explode(',',$value);
			}
		}
 		$newadrtemparr = array();
		$valueflag = array(); 
		if(!empty($adrtemparr)){
			foreach($adrtemparr as $key=>$value){
				if( !empty($value[0]) &&  !empty($value[1]) &&  !empty($value[2]) &&  !empty($value[3])){
					 if(in_array($value[2],$valueflag)){
						 
					 }else{
						 $valueflag[] = $value[2];
						 $newadrtemparr[] = $value;
					 }
				 } 
			}
		} 
 		 $data['cook_adrlistcookie'] = $newadrtemparr;
		# print_r($data['cook_adrlistcookie']);
		Mysite::$app->setdata($data);
	 }
	 function appdown(){
		 
		 
	 }
	 function saveloation(){ 
			ICookie::clear('lat');
			ICookie::clear('lng');
			ICookie::clear('mapname');
			ICookie::clear('addressname');
			ICookie::clear('CITY_ID');
			ICookie::clear('CITY_NAME');
			$adcode = IFilter::act(IReq::get('adcode'));  
			 
			$lat = IFilter::act(IReq::get('lat'));   
			$lng = IFilter::act(IReq::get('lng')); 
			$addressname = IFilter::act(IReq::get('addressname')); 
			
			 
			
			ICookie::set('lat',$lat,2592000);  
			ICookie::set('lng',$lng,2592000);  
			ICookie::set('addressname',$addressname,2592000);  
			ICookie::set('mapname',$addressname,2592000);  
			
			$data['areainfoone'] = array();
			
			if( !empty($adcode) ){
				$areacodeone =  $this->mysql->select_one("select id,pid from ".Mysite::$app->config['tablepre']."areacode where id=".$adcode." order by id asc  ");
				if( !empty($areacodeone) ){
					$adcodeid = $areacodeone['id'];
					$pid = $areacodeone['pid'];
   					$areainfoone =  $this->mysql->select_one("select adcode,name from ".Mysite::$app->config['tablepre']."area where  adcode=".$adcodeid." or adcode=".$pid." order by adcode desc ");
					if( !empty($areainfoone) ){
						$city_id = "CITY_ID_".$areainfoone['adcode'];
						$city_name = "CITY_NAME_".$areainfoone['name'];
						ICookie::set('CITY_ID',$city_id,2592000);  
						ICookie::set('CITY_NAME',$city_name,2592000);  
						$data['areainfoone']  = $areainfoone;
 					}
				}
			}
			$this->success($data);
	}
	function dwLocation(){  // 定位当前位置 
		ICookie::clear('lat');
		ICookie::clear('lng');
		ICookie::clear('addressname');
		ICookie::clear('CITY_ID');
		ICookie::clear('CITY_NAME');
		$link = IUrl::creatUrl('wxsite/index');
	    $this->message('',$link);
	}
	
	function index(){ 
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$addressname = ICookie::get('addressname');
		$lat = empty($lat)?34.802461:$lat;
		$lng = empty($lng)?113.597715:$lng;
		if(empty($addressname)){
		    $addressname = '' ;
		}   
		$data['lat'] = $lat;
		$data['lng'] = $lng; 
		$data['addressname'] = $addressname;  
        $data['weatherinfo'] = $this->getweatherinfo($lat,$lng);       
		Mysite::$app->setdata($data);  
	}


    function loadindexcontent(){ 
		$data['uid']= $this->member['uid'];		 
		$juannotice = $this->mysqlcache->select_one("select actid from ".Mysite::$app->config['tablepre']."userjuannotice where   uid='".$data['uid']."' order by id desc limit 1  ");
		$juaninfo = array();
		if($juannotice['actid'] > 0){
			$juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where   uid='".$data['uid']."' and status = 0 and endtime > ".time()."  and actid = ".$juannotice['actid']."  limit 3  ");
		}
        $data['juaninfo'] = $juaninfo;
        $platpssetinfo = $this->mysqlcache->longTime()->select_one("select cityid,wxkefu_open,wxkefu_ewm,wxkefu_logo,wxkefu_phone from ".Mysite::$app->config['tablepre']."platpsset where   cityid='".$this->CITY_ID."'   ");
        $data['platpssetinfo'] = $platpssetinfo;
        $moretypelist = $this->mysqlcache->longTime()->getarr("select* from ".Mysite::$app->config['tablepre']."appadv where type=2 and   cityid='".$this->CITY_ID."'  and is_show=1  order by orderid  asc");
        $newmoretypelist = array();
		 
		if( !empty($moretypelist) ){
			foreach($moretypelist as $key=>$value){
		 	if( $value['param'] == 'weblink' ){
				$catelink = $value['link'];
			    $ajaxflag = false;	
			}else{
					if( $value['activity'] == 'waimai' ){
						#$catelink = IUrl::creatUrl('/wxsite/waimai/typeid/'.$value['param'].'');
						$catelink = IUrl::creatUrl('/wxsite/shoplist/typelx/wm/typeid/'.$value['param'].'');
						$ajaxflag = true;
					}else if( $value['activity'] == 'market' ){
						#$catelink = IUrl::creatUrl('/wxsite/marketlist/typeid/'.$value['param'].'');
						$catelink = IUrl::creatUrl('/wxsite/shoplist/typelx/mk/typeid/'.$value['param'].'');
						$ajaxflag = true;
					}else if( $value['activity'] == 'lifehelp' ){
						$catelink = IUrl::creatUrl('/wxsite/lifeasslist');
						$ajaxflag = false;
					}else if( $value['activity'] == 'shophui' ){
						$catelink = IUrl::creatUrl('/wxsite/shophui');
						$ajaxflag = false;
					}else if( $value['activity'] == 'paotui' ){
						$catelink = IUrl::creatUrl('/wxsite/paotui');
						$ajaxflag = false;
					}else if( $value['activity'] == 'marketlist' ){
						$catelink = IUrl::creatUrl('/wxsite/marketshop');
						$ajaxflag = false;
					}
			}
				$value['catelink'] = $catelink;
				$value['ajaxflag'] = $ajaxflag;
				$newmoretypelist[] = $value;
			}
		}
		
		$data['moretypelist']  = $newmoretypelist;
		$order = intval(IReq::get('order'));//   0综合排序 1好评优先 2起送价最低 3销量
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
        $lng = ICookie::get('lng');
        $lat = ICookie::get('lat');
        $addressname = ICookie::get('addressname');

        $lat = empty($lat)?0:$lat;
        $lng = empty($lng)?0:$lng;        
        if(empty($addressname)){
            $addressname = '' ;
        }
        $data['lat'] = $lat;
        $data['lng'] = $lng;
        $data['addressname'] = $addressname;
        $ztymode =   $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."ztymode where cityid='".$this->CITY_ID."'  ");
        if(empty($ztymode)){
			$limits = 3;
			$ztymode['type']=0;
		}else if($ztymode['type']==1){
            $limits = 5;
        }else if($ztymode['type']==2){
            $limits = 4;
        }else{
            $limits = 3;
        }
        $ztylist =   $this->mysqlcache->longTime()->getarr("select b.id,b.imgwidth,b.imgheight,a.indeximg from ".Mysite::$app->config['tablepre']."ztyimginfo as a left join ".Mysite::$app->config['tablepre']."specialpage as b on a.ztyid = b.id where a.is_show=1  and  a.type={$ztymode['type']} and b.is_bd=2   and     b.cityid='".$this->CITY_ID."'     order by a.sort  asc limit {$limits} ");
        $data['ztylist'] = $ztylist;
        $data['ztymode'] = $ztymode; 
		
		
		$templist = array();
		
		//判断平台类型  //2微信端,3web端
		$source = 3;
		if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ 
			$source = 2;
		}
		 
		//查看是否开启注册送优惠券  开启的话首页弹出注册送优惠券图片   不开启的话不弹出
		$juansetinfo = $this->mysqlcache->select_one("select status from ".Mysite::$app->config['tablepre']."alljuanset where type = 2 or name = '注册送优惠券' " );
		$data['isopen'] = $juansetinfo['status'];
		$catid = intval(IReq::get('catid'));//店铺分类id		   
		  
		$sendtype = intval(IReq::get('sendtype')); //2平台配送   1店铺配送
		$cxtype = intval(IReq::get('cxtype'));	//1送赠品  2满减  3折扣  4免配送费 
		$limitarr['sendtype'] = $sendtype;
		$limitarr['cxtype'] = $cxtype;  
		#print_r($sendtype);
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat; 
		$datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,$source);  
		$tempc = array();
		foreach($datalistx as $key=>$value){
			if($value['isforyou'] == 1&& $value['canps'] == 1){
				$tempc[] = $value;
			}
		}
		$data['fyshoplist'] = array_slice($tempc,0,6);
		$sort = array(
			'direction' => 'SORT_ASC',
			'field'     => 'sort',
		);
		$arrSort = array();
		foreach($tempc as $uniqid => $row){
			foreach($row as $key=>$value){
				$arrSort[$key][$uniqid] = $value;
			}
		}
		if($sort['direction']){
			array_multisort($arrSort[$sort['field']], constant($sort['direction']), $tempc);
		}
		$cityid = $this->CITY_ID; 
		$data['flinfo'] = $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."stationskin  where cityid = ".$cityid." and type = 1 ");
		$data['flxinfo'] = $this->mysqlcache->longTime()->select_one("select * from ".Mysite::$app->config['tablepre']."stationskin  where cityid = ".$cityid." and type = 2 ");
		
		 
		$data['shoplist']  = array_slice($datalistx,0,10);//首次加载10个，之后每上滑一次加载10个。
		#print_r($data['shoplist']);exit;
		$platpssetinfo = $this->mysqlcache->longTime()->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$this->CITY_ID."'  ");
		$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
		$data['is_allow_ziti'] = empty($platpssetinfo['is_allow_ziti'])?0:$platpssetinfo['is_allow_ziti'];  
        /*购物车数量*/ 
		$smardb = new newsmcart();
		$smardb->setdb($this->mysql)->ShopTJList();
		$carinfo = $smardb->getdata();
		$cart_count = 0;
		if(!empty($carinfo)){			 
			foreach($carinfo as $k=>$v){
                $cart_count += $v['count']; 
			}
		}
		$data['cart_count'] = $cart_count;				
        Mysite::$app->setdata($data);
    }
    function indexshoplistdata(){  	 
		$order = intval(IReq::get('order'));//   0综合排序 1好评优先 2起送价最低 3销量
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

		//判断平台类型  //2微信端,3web端
		$source = 3;
		if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ 
			$source = 2;
		}	   
		  
		$sendtype = intval(IReq::get('sendtype')); //2平台配送   1店铺配送
		$cxtype = intval(IReq::get('cxtype'));	//1送赠品  2满减  3折扣  4免配送费 
		$limitarr['sendtype'] = $sendtype;
		$limitarr['cxtype'] = $cxtype;
		#print_r($sendtype);	
			//exit;
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat; 
		$datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,$source);
		#print_r($limitarr);
		$page = intval(IReq::get('page'));
		$starnum = ($page - 1 ) * 10; 	 
		$data['shoplist']  = array_slice($datalistx,$starnum,10);
		$platpssetinfo = $this->mysqlcache->longTime()->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$this->CITY_ID."'  ");
		$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
        Mysite::$app->setdata($data);
    }
   function closejuan(){
	     $this->mysql->delete(Mysite::$app->config['tablepre'].'userjuannotice',"uid ='".$this->member['uid']."'");
		 $this->success('success');
    }

 

    function waimai(){
 	 	  ICookie::set('shopshowtype','waimai',2592000);  
		   $typeid = intval(IReq::get('typeid'));
		   if(!empty($typeid)) {
			      $link = IUrl::creatUrl('wxsite/shoplist/typelx/wm/typeid/'.$typeid);
		   }else{
			    $link = IUrl::creatUrl('wxsite/shoplist/');
		   }
	 	
	     $this->message('',$link); 
	 }
	 function dingtai(){
 	 	  ICookie::set('shopshowtype','dingtai',2592000);  
	 	   if(!empty($typeid)) {
			      $link = IUrl::creatUrl('wxsite/shoplist/typelx/yd/typeid/'.$typeid);
		   }else{
			    $link = IUrl::creatUrl('wxsite/shoplist/');
		   }
	 	
	     $this->message('',$link); 
	 }
	 function marketlist(){
	 	 
	 	  ICookie::set('shopshowtype','market',2592000);  
	 	     $typeid = intval(IReq::get('typeid'));
	 	    if(!empty($typeid)) {
			      $link = IUrl::creatUrl('wxsite/shoplist/typelx/mk/typeid/'.$typeid);
		   }else{
			    $link = IUrl::creatUrl('wxsite/shoplist/');
		   }
	 	
	     $this->message('',$link); 
	 }

	 function wmrtest(){
	 	$typelx = IFilter::act(IReq::get('typelx')); 
		
		 if(!empty($typelx)){
			 if($typelx == 'wm'){
				 ICookie::set('shopshowtype','waimai',2592000); 
				 $shopshowtype = 'waimai';
			 }
			 if($typelx == 'mk'){
				 ICookie::set('shopshowtype','market',2592000); 
				 $shopshowtype = 'market';
			 }
			  if($typelx == 'yd'){
				 ICookie::set('shopshowtype','dingtai',2592000); 
				 $shopshowtype = 'dingtai';
			 }
		 }else{
			 $shopshowtype = ICookie::get('shopshowtype');
		 }
	 	  if(!in_array($shopshowtype,array('waimai','market','dingtai'))){
	 	     ICookie::set('shopshowtype','waimai',2592000);  
	 	     $shopshowtype = 'waimai';
	 	  }
	 	  $areaid = IFilter::act(IReq::get('areaid'));  
		  if( $areaid <= 0 ){
				ICookie::clear('myaddress');
			}
	 	  $data['typeid'] = IFilter::act(IReq::get('typeid'));  
	 	  if($shopshowtype == 'market'){
	 	  	 $templist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 1 and parent_id = 0 and is_search = 1 and type ='checkbox'  order by orderid asc limit 0,1000"); 
		     $data['caipin'] = array();
	       if(!empty($templist)){
		 	      $data['caipin']  = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."shoptype where parent_id='".$templist['id']."'  ");
		     }
	 	  }else{
	 	     $templist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_search = 1 and type ='checkbox'  order by orderid asc limit 0,1000");
	   
		   $data['caipin'] = array();
	       if(!empty($templist)){
		 	      $data['caipin']  = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."shoptype where parent_id='".$templist['id']."'  ");
		     }
		  }
		
		  $data['shopshowtype'] = $shopshowtype;
		  $shopsearch = IFilter::act(IReq::get('search_input')); 
		  $data['search_input'] = $shopsearch;
		  
		  $data['areaid'] = $areaid;  
		  Mysite::$app->setdata($data);
	 } 
	 /**2017年9月份 外面人改版升级新增模块  超市便利店独立模块  不限制超市分类  显示全部超市店铺**/
	function marketshop(){
		 //获取头部轮播图
		$data['imglist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."adv where `advtype` = 'chaoshilb' and cityid=".$this->CITY_ID." " ); 
        $goodstype = array();
		$goodstype1 = array('id'=>0,'name'=>'全部');
		$goodstypex  = $this->mysql->select_one("select id  from ".Mysite::$app->config['tablepre']."shoptype  where parent_id = 0 and  is_search = 1  and cattype = 1 and type = 'checkbox'     ");
		$goodstype = $this->mysql->getarr("select id,name  from ".Mysite::$app->config['tablepre']."shoptype where parent_id = '".$goodstypex['id']."' ");
		array_unshift($goodstype,$goodstype1);
		$data['goodstype'] =  $goodstype;
		Mysite::$app->setdata($data); 
	}
	function marketlistdata(){ 
		//2微信端,3web端
		$source = 3;
		if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ 
			$source = 2;
		}  
		$shopcat = intval(IReq::get('shopcat'));
		$shopcat = $shopcat > 0?$shopcat:0;	  
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$lat = empty($lat)?34.802461:$lat;
		$lng = empty($lng)?113.597715:$lng;
		$limitarr['shoptype'] = 2;
		if($shopcat > 0){
			$limitarr['shopcat'] =$shopcat;
		} 	 	
		$datalistx = $this->Tdata($this->CITY_ID,$limitarr,array('juli'=>'asc'),$lat,$lng,$source); 
			/*获取店铺*/
		$pageinfo = new page();
		$data['page'] = intval(IReq::get('page'));
		$pageinfo->setpage(intval(IReq::get('page'))); 
		$starnum = $pageinfo->startnum();
		$pagesize = $pageinfo->getsize();
		$templist = array();	
		for($k = 0;$k<$pagesize;$k++){
			$checknum = $starnum+$k;
			if(isset($datalistx[$checknum])){
				$templist[] = $datalistx[$checknum];
			}else{
				break;
			}
		}   
		 
		$templistx = array();
		$da = date("w");
		foreach($templist as $k=>$v){
			$goodslist = array();
			$goodslist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where is_com = 1  and shopid =".$v['id']." and is_live = 1 and FIND_IN_SET(".$da.",weeks) order by good_order asc limit 3");
			$comlist = array();
			if(!empty($goodslist)){
				foreach($goodslist as $kk=>$vv){
					$cxinfo = $this->goodscx($vv);					 
					$value['id'] = $vv['id'];				 
					$value['name'] = $vv['name'];
					$value['img'] = empty($vv['img'])?getImgQuanDir(Mysite::$app->config['goodlogo']): getImgQuanDir($vv['img']);
					$value['is_cx'] = $cxinfo['is_cx'];
					$value['oldcost'] = floatval(round($cxinfo['oldcost'],2));					 
					$value['cost'] = floatval(round($cxinfo['cxcost'],2));					
					$value['zhekou'] = $cxinfo['zhekou'];
					$comlist[] = $value;
				}
			}
			$v['comgoodslist'] = $comlist;			 
			$templistx[] = $v;
		} 
 		
        $data['shoplist'] = $templistx;			 	
		Mysite::$app->setdata($data);  
	 }
	 function goodscx($goodsinfo){
		#print_r($goodsinfo);
		$newarray = array('cxcost'=>$goodsinfo['cost'],'oldcost'=>$goodsinfo['cost'],'zhekou'=>0,'is_cx'=>0,'cxnum'=>0);	
		if($goodsinfo['is_cx'] == 1){
			$cxdata =	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$goodsinfo['id']."  ");
			#print_r($cxdata);
			$newdata = getgoodscx($goodsinfo['cost'],$cxdata);
			#print_r($newdata);
			$newarray['oldcost'] = $goodsinfo['cost'];
			$newarray['cxcost'] = $newdata['cost'];
			$newarray['zhekou'] = $newdata['zhekou'];
			$newarray['is_cx'] = $newdata['is_cx'];
			//2016/12/27新增
			$newarray['cxnum'] = $cxdata['cxnum'];
			#print_r($newarray);
		}
		
		return  $newarray;
	}
	 function shoplist(){
		 if( !strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){    //判断是微信浏览器不
			 $data['wxType'] = 2;
		 }else{
			 $data['wxType'] = 1;
		 }
	 	$typelx = IFilter::act(IReq::get('typelx'));
		 if(!empty($typelx)){
			 if($typelx == 'wm'){
				 ICookie::set('shopshowtype','waimai',2592000); 
				 $shopshowtype = 'waimai';
			 }
			 if($typelx == 'mk'){
				 ICookie::set('shopshowtype','market',2592000); 
				 $shopshowtype = 'market';
			 }
			  if($typelx == 'yd'){
				 ICookie::set('shopshowtype','dingtai',2592000); 
				 $shopshowtype = 'dingtai';
			 }
		 }else{
			 $shopshowtype = ICookie::get('shopshowtype');
		 }

		#  print_r($shopshowtype);
	 	  if(!in_array($shopshowtype,array('waimai','market','dingtai'))){
	 	     ICookie::set('shopshowtype','waimai',2592000);  
	 	     $shopshowtype = 'waimai';
	 	  }
	 	  $areaid = IFilter::act(IReq::get('areaid'));  
		  if( $areaid <= 0 ){
				ICookie::clear('myaddress');
			}
	 	  $data['typeid'] = IFilter::act(IReq::get('typeid'));  
		  
 		
 	
 		$catewhere = "  and   cityid = '".$this->CITY_ID."' "; 
		$shopcateinfo =array();
		$shopcateinfo = $this->mysql->select_one("select img,link from ".Mysite::$app->config['tablepre']."shopcateadv where cateid='".$data['typeid']."' ".$catewhere." order by orderid asc  ");  //暂时只读取一条
		$data['shopcateinfo']  = $shopcateinfo;
	 
		  
	 	  if($shopshowtype == 'market'){
	 	  	 $templist = $this->mysql->select_one("select id from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 1 and parent_id = 0 and is_search = 1 and type ='checkbox'  order by orderid asc limit 0,1000");
		     $data['caipin'] = array();
	       if(!empty($templist)){
		 	      $data['caipin']  = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."shoptype where parent_id='".$templist['id']."'  ");
		     }
	 	  }else{
	    
	 	     $templist = $this->mysql->select_one("select id from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_search = 1 and type ='checkbox'  order by orderid asc limit 0,1000");
	   
		   $data['caipin'] = array();
	       if(!empty($templist)){
		 	      $data['caipin']  = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."shoptype where parent_id='".$templist['id']."'  ");
		     }
		  }
		
		  $data['shopshowtype'] = $shopshowtype;
		  $shopsearch = IFilter::act(IReq::get('search_input')); 
		  $data['search_input'] = $shopsearch;
		  $platpssetinfo = $this->mysqlcache->longTime()->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$this->CITY_ID."'  ");
		$data['is_allow_ziti'] = empty($platpssetinfo['is_allow_ziti'])?0:$platpssetinfo['is_allow_ziti'];
		  $data['areaid'] = $areaid;  
		  Mysite::$app->setdata($data);  
	 }
	 function testshop(){
		 ini_set('display_errors',1);            //错误信息
		ini_set('display_startup_errors',1);    //php启动错误信息
		error_reporting(-1);  
		echo $this->CITY_ID;
		 $data['shoplist'] = $this->Tdata($this->CITY_ID,array(),array('sell'=>'desc'),'34.788678','113.664677',3);
		 //Tdata($cityid,$limitarr,$paixuarr,$lat,$lng,$source,$limitjuli=0)
		 print_r($data['shoplist']);
		 Mysite::$app->setdata($data);  
	 }
	 
	 
	function shoplistdata(){
		$typelx = IFilter::act(IReq::get('typelx')); 
		if(!empty($typelx)){
			 if($typelx == 'wm'){
				 ICookie::set('shopshowtype','waimai',2592000); 
				 $shopshowtype = 'waimai';
			 }
			 if($typelx == 'mk'){
				 ICookie::set('shopshowtype','market',2592000); 
				 $shopshowtype = 'market';
			 }
			  if($typelx == 'yd'){
				 ICookie::set('shopshowtype','dingtai',2592000); 
				 $shopshowtype = 'dingtai';
			 }
		}else{			 
			 $shopshowtype = ICookie::get('shopshowtype');		 
		} 
		
		//返回的所有店铺数据
		$templist = array();
		
		//判断平台类型  //2微信端,3web端
		$source = 3;
		if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ 
			$source = 2;
		}
		$order = intval(IReq::get('order'));//   0综合排序 1好评优先 2起送价最低 3销量
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
		
		$catid = intval(IReq::get('catid'));//店铺分类id		   
		  
		$sendtype = intval(IReq::get('sendtype')); //2平台配送   1店铺配送
		$cxtype = intval(IReq::get('cxtype'));	//1送赠品  2满减  3折扣  4免配送费 
		  
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;
		
		//超市
		if($shopshowtype == 'market'){
			// $where = '';
			// $shopsearch = IFilter::act(IReq::get('search_input')); 
			// $shopsearch = urldecode($shopsearch); 
			// if(!empty($shopsearch)) $where=" and b.shopname like '%".$shopsearch."%' "; 
		   // $areaid= intval(IFilter::act(IReq::get('areaid')));  
			$limitarr['shoptype'] = 2;
			$limitarr['shopcat'] = $catid;
			$limitarr['sendtype'] = $sendtype;
			$limitarr['cxtype'] = $cxtype;
			
			//2微信端,3web端
			
		    $datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,$source); 
			/*获取店铺*/
			$pageinfo = new page();
			$pageinfo->setpage(intval(IReq::get('page'))); 
			$starnum = $pageinfo->startnum();
			$pagesize = $pageinfo->getsize();
			
			for($k = 0;$k<$pagesize;$k++){
				$checknum = $starnum+$k;
				if(isset($datalistx[$checknum])){
					$templist[] = $datalistx[$checknum];
				}else{
					break;
				}
			}  
	 	}else{
			
			
			$limitarr['shoptype'] = 1;
			$limitarr['shopcat'] = $catid;
			$limitarr['sendtype'] = $sendtype;
			$limitarr['cxtype'] = $cxtype; 
			if($shopshowtype == 'dingtai'){
				$limitarr['is_goshop'] = 1;
			}else{
				$limitarr['is_waimai'] = 1;
			} 
		    $datalistx = $this->Tdata($this->CITY_ID,$limitarr,$orderarray[$order],$lat,$lng,$source); 
			/*获取店铺*/
			$pageinfo = new page();
			$pageinfo->setpage(intval(IReq::get('page'))); 
			$starnum = $pageinfo->startnum();
			$pagesize = $pageinfo->getsize();
			
			for($k = 0;$k<$pagesize;$k++){
				$checknum = $starnum+$k;
				if(isset($datalistx[$checknum])){
					$templist[] = $datalistx[$checknum];
				}else{
					break;
				}
			}  
			
		 
		}

		$data['shopshowtype'] = $shopshowtype;
		$data['shoplist']  = $templist; 
		$platpssetinfo = $this->mysqlcache->longTime()->select_one("select is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$this->CITY_ID."'  ");
		$data['ztimg']  = getImgQuanDir(Mysite::$app->config['ztimg']);
		Mysite::$app->setdata($data); 
	 
	} 
	 
	 function ztshow(){
		 $id = IFilter::act(IReq::get('id')); 
		 $shopid = IFilter::act(IReq::get('shopid')); 
		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."' "); 
		 if($shopinfo['shoptype'] == 0){
			 $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid='".$shopid."' "); 		 
		 }else{
			 $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$shopid."' "); 
		 }
		  
		 $newshoparray = array_merge($shopinfo,$shopdet);
		 $tempinfo =  $this->pscost($newshoparray); 
		 $data['pscost'] = $tempinfo['pscost'];	
		 $data['is_show_ziti'] = $tempinfo['is_allow_ziti'];
		 $nowhour = time();
      	 $data['openinfo'] = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopdet['is_orderbefore'],$nowhour);
         $data['shopinfo'] = $shopinfo;	
		 $data['shopdet'] = $shopdet;	
		 $ztinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopzt where id='".$id."' and shopid='".$shopid."'  ");
		 $data['ztinfo'] = $ztinfo;
		 if(empty($ztinfo))$this->message('店铺专题信息获取失败'); 
		 $newdet = array();
		 $weekji = date('w');
		 $goodsinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid='".$shopid."' and  FIND_IN_SET( ".$weekji." , `weeks` )   and  id in(".$ztinfo['goodids']." )  and is_live = 1  order by good_order asc ");					
		 foreach ( $goodsinfo as $k=>$valq ){
			if($valq['is_cx'] == 1){
			//测算促销 重新设置金额
				$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
				$newdata = getgoodscx($valq['cost'],$cxdata);						
				$valq['zhekou'] = $newdata['zhekou'];
				$valq['is_cx'] = $newdata['is_cx'];
				$valq['oldcost'] = round($valq['cost'],2);
				$valq['cost'] = round($newdata['cost'],2);
				$valq['cxnum'] = $cxdata['cxnum'];
			}
			if($valq['have_det'] == 1){
				$price=array(); 
				$gooddet = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$valq['id']."  ");
				foreach ( $gooddet as $keyd=>$vald ){
					$price[] = $vald['cost'];
				}					
				$valq['cost'] = $this->formatcost(min($price),2);//获取多规格商品中价格最小的价格作为展示价格				
				$valq['oldcost'] = $valq['cost'];
				if($valq['zhekou']>0){
					$valq['cost'] = $this->formatcost(round(($valq['cost']*$valq['zhekou']*0.1),2),2);
				}
			}
			$valq['sellcount'] = $valq['sellcount']+$valq['virtualsellcount'];
			$newdet[]=$valq;
		}
		$data['goodsinfo'] = $newdet;	
 
		Mysite::$app->setdata($data); 
		 
	 }
	 function shopshow(){//店铺详情
	 $weblink = ICookie::set('wx_login_link','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		 $areaid = ICookie::get('myaddress');
	 		$typelx = IFilter::act(IReq::get('typelx')); 
			
		 if(!empty($typelx)){
			 if($typelx == 'wm'){
				 ICookie::set('shopshowtype','waimai',2592000); 
				 $shopshowtype = 'waimai';
			 }
			 if($typelx == 'mk'){
				 ICookie::set('shopshowtype','market',2592000); 
				 $shopshowtype = 'market';
			 }
			  if($typelx == 'yd'){
				 ICookie::set('shopshowtype','dingtai',2592000); 
				 $shopshowtype = 'dingtai';
			 }
		 }else{
			 
			 $shopshowtype = ICookie::get('shopshowtype');
			 
		 }
		 if( !empty($typelx) ){
			 $data['typelx'] = $typelx;			 
		 }else{
			 if( $shopshowtype == 'waimai' ){
				 $data['typelx'] = 'wm';
			 }
			if( $shopshowtype == 'market' ){
				 $data['typelx'] = 'mk';
			 } 
			if( $shopshowtype == 'dingtai' ){
				 $data['typelx'] = 'yd';
			 } 			 
		 }
		 $id = intval(IReq::get('id'));     
		 $gid = intval(IReq::get('gid'));     //gid参数 新增，从专题页商品列表 点击立即抢购 直接进入店铺并且加入购物车
		 $data['goodinfo'] = array();
		 if( $gid > 0 ){
			 $goodinfo = $this->mysql->select_one("select id,have_det from ".Mysite::$app->config['tablepre']."goods where id='".$gid."' and shopid='".$id."' ");
			 if( !empty($goodinfo) ){
				 $data['goodinfo'] = $goodinfo;
			 }
		 }
		 
		 
		 $data['id'] = $id;
		 		 /*  $wxclass = new wx_s();
		  	 	$signPackage = $wxclass->getSignPackage();
		  	  	$data['signPackage'] = $signPackage; 
		  	 	print_r($data['signPackage']); */
		  	 	$shopinfo1 = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$id."'  ");
		  	 	  #print_r($shopinfo1);
		  	 	if(empty($shopinfo1) ){
		  	 		$shopinfo1['shopname'] = Mysite::$app->config['sitename'];
		  	 		$shopinfo1['shoplogo'] = Mysite::$app->config['sitelogo'];
		  	 		$shopinfo1['intr_info'] = Mysite::$app->config['sitename'];
		  	 	}
		  	 	$data['shopinfo1'] = $shopinfo1;
		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$id."' ");   //店铺基本信息
		 $weekji = date('w');
		 /*专题数据开始*/ 
		 $data['ztinfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopzt where shopid='".$id."' and is_show = 1 order by sort asc");       
	 	 $ztinfocount = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shopzt where shopid='".$id."' and is_show = 1 ");    
		 $data['ztinfocount'] = $ztinfocount > 0?$ztinfocount:0;
		 /*专题数据结束*/ 
		 /*折扣数据开始*/
		 $zkgoodinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid='".$id."' and    FIND_IN_SET( ".$weekji." , `weeks` )    and is_live = 1 and is_cx = 1  order by good_order asc ");
		 $cxgoodslist = array();
		 foreach ( $zkgoodinfo as $k1=>$v1 ){		 
				//测算促销 重新设置金额
				$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$v1['id']."  ");
				
				$newdata = getgoodscx($v1['cost'],$cxdata);
                			
				$v1['zhekou'] = $newdata['zhekou'];
				$v1['is_cx'] = $newdata['is_cx'];
				$v1['cost'] = round($newdata['cost'],2);
				$v1['cxnum'] = $cxdata['cxnum'];
				
				if($v1['have_det'] == 1){
					$price=array(); 
					$gooddet = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$v1['id']."  ");
					 
					foreach ( $gooddet as $k2=>$v2 ){
						$price[] = $v2['cost'];
					}	
					$v1['cost'] = $this->formatcost(min($price),2);//获取多规格商品中价格最小的价格作为展示价格
					
					if($v1['zhekou']>0){
						$v1['newcost'] = $this->formatcost(round(($v1['cost']*$v1['zhekou']*0.1),2),2);
					}
					
				}
				$v1['sellcount'] = $v1['sellcount']+$v1['virtualsellcount'];
				 
				if($v1['is_cx'] == 1){
					$cxgoodslist[]=$v1;
				}
				
			}
		 $zhegoods = array();	
         if(!empty($cxgoodslist)){
             $zhe['id'] = '-1';
			 $zhe['is_zheclass'] = '1';
			 $zhe['shopid'] = $id;
			 $zhe['name'] = '折扣';
			 $zhe['orderid'] = '0';
			 $zhe['cattype'] = '0';
			 $zhe['det'] = $cxgoodslist;
			 $zhegoods = $zhe; 
		 }
		 
		 /*折扣数据结束*/
		 
	 	 if($shopshowtype == 'market'){
      	 	 if(empty($shopinfo)){
      	 	 	//需要进行跳转
      	 	 	 $link = IUrl::creatUrl('wxsite/index'); 
      	     $this->message('',$link); 
      	 	 } 
			 $checid = intval(Mysite::$app->config['plateshopid']);
			 if($checid == $id){
				  $link = IUrl::creatUrl('wxsite/index'); 
				$this->message('',$link); 
			 }
      	 	 $shopdet = array();
      	 	 $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$id."' "); 
      	 	 
      	 	$nowhour = time();
      	 	 $data['openinfo'] = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopdet['is_orderbefore'],$nowhour);

			$data['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$shopinfo['id']."' ");//收藏
      	 	 $data['shopinfo'] = $shopinfo;
      	 	 $data['shopdet'] = $shopdet;
			$availableonetypeidarr = array();
			$availabletwotypeidarr = array();
			$ccgoodstype=  $this->mysql->getarr("select id,parent_id from ".Mysite::$app->config['tablepre']."marketcate where shopid = ".$shopinfo['id']."   and parent_id > 0 order by orderid asc");
			foreach($ccgoodstype as $kk=>$vv){
				$ccgood = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."goods where shopid='".$id."' and    FIND_IN_SET( ".$weekji." , `weeks` )   and typeid =".$vv['id']." and is_live = 1  order by good_order asc ");
				if(!empty($ccgood)){
					$availableonetypeidarr[] = $vv['parent_id'];
					$availabletwotypeidarr[] = $vv['id'];	
				}			
			}
			$availableonetypeidstr = empty($availableonetypeidarr)?'0':implode(',',array_unique($availableonetypeidarr));
			$availabletwotypeidstr = empty($availabletwotypeidarr)?'0':implode(',',array_unique($availabletwotypeidarr));	
			$goodstype=  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."marketcate where shopid = ".$shopinfo['id']."   and parent_id = 0  and id in (".$availableonetypeidstr.")  order by orderid asc");	 
			$data['goodstype'] = array();
			$tempids = array();
			foreach($goodstype as $key=>$value){
				$soncate = array();
				$soncatearray = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."marketcate where shopid = ".$id."  and parent_id = ".$value['id']."  and id in (".$availabletwotypeidstr.")  order by orderid asc");
				foreach($soncatearray as $key1=>$val){
					$val['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid='".$id."' and    FIND_IN_SET( ".$weekji." , `weeks` )   and typeid =".$val['id']." and is_live = 1  order by good_order asc ");						
						$newdet=array();
						foreach ( $val['det'] as $k=>$valq ){	
							if($valq['is_cx'] == 1){
							//测算促销 重新设置金额
								$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
								$newdata = getgoodscx($valq['cost'],$cxdata);						
								$valq['zhekou'] = $newdata['zhekou'];
								$valq['is_cx'] = $newdata['is_cx'];
								$valq['cost'] = round($newdata['cost'],2);
								$valq['cxnum'] = $cxdata['cxnum'];
							}
							if($valq['have_det'] == 1){
								$price=array(); 
								$gooddet = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$valq['id']."  ");
								#print_r($gooddet);
								foreach ( $gooddet as $keyd=>$vald ){
									$price[] = $vald['cost'];
								}	
								$valq['cost'] = $this->formatcost(min($price),2);//获取多规格商品中价格最小的价格作为展示价格
								if($val['zhekou']>0){
									$val['newcost'] = $this->formatcost(round(($val['cost']*$val['zhekou']*0.1),2),2);
								}
							}
							$valq['sellcount'] = $valq['sellcount']+$valq['virtualsellcount'];
							$valq['cost'] = $this->formatcost($valq['cost'],2);
							$newdet[]=$valq;
						}
						$val['det']=	$newdet;								
						$tempids[] = $val['id'];							
						$soncate[] = $val;
						$value['tempids']  = implode(',',$tempids);
													
					}
					$value['soncate']  = $soncate;
					
					$data['goodstype'][]  = $value;
					
				}
 
				$shopdet['id'] = $id; 
				$shopdet['shoptype']=1;
				$newshoparray = array_merge($shopinfo,$shopdet);
				$tempinfo =   $this->pscost($newshoparray); 
				$backdata['pstype'] = $tempinfo['pstype'];
				$backdata['pscost'] = $tempinfo['pscost'];
				$backdata['is_allow_ziti'] = $tempinfo['is_allow_ziti'];
				$data['psinfo'] = $backdata;
				$data['mainattr'] = array(); 
				$data['shopattr'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr  where  cattype = ".$shopinfo['shoptype']." and shopid = '".$shopinfo['id']."'  order by firstattr asc limit 0,1000");
	 	 }else{			 
      	 	 if(empty($shopinfo)){
      	 	 	//需要进行跳转
      	 	 	 $link = IUrl::creatUrl('wxsite/shoplist'); 
      	     $this->message('',$link); 
      	 	 } 
      	 	  
      	 	 $shopdet = array();
      	 	 if(empty($shopinfo['shoptype'])){
      	 	 	 $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid='".$id."' "); 
      	 	 }elseif($shopinfo['shoptype'] == 1){
      	 	 	 $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$id."' "); 
      	 	 }
      	  $nowhour = time();

      	 	 $data['openinfo'] = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopdet['is_orderbefore'],$nowhour);

      	   $data['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$shopinfo['id']."' ");//收藏
		  
      	 	 $data['shopinfo'] = $shopinfo;
      	 	 $data['shopdet'] = $shopdet;
      	 	  $templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodstype  where shopid='".$id."' order by orderid asc  ");
      	 	  $data['goodstype'] = array();
			  if(!empty($zhegoods) && $shopinfo['shoptype'] == 0 ){
				  $data['goodstype'][] = $zhegoods;
			  }
			$wheretype  = '';
			if( $shopshowtype == 'dingtai' ){				
			  $wheretype = "and is_dingtai = 1 and    FIND_IN_SET( ".$weekji." , `weeks` )    ";
			  
			}else{
				 $wheretype = "and is_waisong = 1 and    FIND_IN_SET( ".$weekji." , `weeks` )    ";
			  
			}
			  
      	 	 foreach($templist as $key=>$value){
      	 	 	$value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid='".$id."'  ".$wheretype."  and typeid =".$value['id']."  and is_live = 1 order by good_order asc");
				$newdet=array();
				foreach ( $value['det'] as $k=>$val ){
					if($val['is_cx'] == 1){
					//测算促销 重新设置金额
						$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$val['id']."  ");
						$newdata = getgoodscx($val['cost'],$cxdata);						
						$val['zhekou'] = $newdata['zhekou'];
						$val['is_cx'] = $newdata['is_cx'];
						$val['cost'] = round($newdata['cost'],2);
						$val['cxnum'] = $cxdata['cxnum'];
					}
					if($val['have_det'] == 1){
						$price=array(); 
						$gooddet = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$val['id']."  ");
						#print_r($gooddet);
						foreach ( $gooddet as $keyd=>$vald ){
							$price[] = $vald['cost'];
						}	
						$val['cost'] = $this->formatcost(min($price),2);//获取多规格商品中价格最小的价格作为展示价格
						if($val['zhekou']>0){
							$val['newcost'] = $this->formatcost(round(($val['cost']*$val['zhekou']*0.1),2),2);
						}
					}
					$val['sellcount'] = $val['sellcount']+$val['virtualsellcount'];
					$val['cost'] = $this->formatcost($val['cost'],2);
					if($val['is_cx'] != 1){//折扣商品已显示在折扣分类中，在其他分类中过滤掉折扣商品
						$newdet[]=$val;
					}
					
				}
			$value['det']=	$newdet;
			 
      	 	  if(!empty($value['det'])){
				$data['goodstype'][]  = $value;
				}
      	 	 }
			
      	 	$shopdet['id'] = $id; 
      	 	$shopdet['shoptype']=1;
      	 	$newshoparray = array_merge($shopinfo,$shopdet);
      	 	$tempinfo =   $this->pscost($newshoparray); 
		    $backdata['pstype'] = $tempinfo['pstype'];
		    $backdata['is_allow_ziti'] = $tempinfo['is_allow_ziti'];
		    if( $shopshowtype == 'dingtai'){
			    $backdata['pscost'] =0;
		    }else{
			    $backdata['pscost'] = $tempinfo['pscost'];
		    }          
            $data['psinfo'] = $backdata;
            $data['mainattr'] = array(); 
            $templist = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."shoptype where  cattype = ".$shopinfo['shoptype']." and parent_id = 0 and is_main =1  order by orderid asc limit 0,1000");
      		foreach($templist as $key=>$value){
				$value['det'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where parent_id = ".$value['id']." order by orderid asc  limit 0,20");
				$data['mainattr'][] = $value;
      	 	} 
      	 	$data['shopattr'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr  where  cattype = ".$shopinfo['shoptype']." and shopid = '".$shopinfo['id']."'  order by firstattr asc limit 0,1000");
	 	}
		 $data['shopshowtype'] = $shopshowtype;
		 $data['weekji']  =$weekji ;
		 $d = (date("w") ==0) ?7:date("w") ; 
		 $cxrule = $this->mysql->getarr("select name,id,imgurl,parentid,controltype from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$id.",shopid)   and status = 1   and ( limittype = 1 or ( limittype = 2 and    FIND_IN_SET(".$d.",limittime)  )  or ( limittype = 3 and endtime > ".time()." and starttime < ".time().")) ");
		 //筛选掉不符合配送条件的免配送费活动
		 foreach($cxrule as $k=>$v){
			 if($v['controltype'] == 4){
				 if($v['parentid'] == 0 && $shopdet['sendtype'] == 1){
					 $newrule[] = $v;
				 }
				 if($v['parentid'] == 1 && $shopdet['sendtype'] != 1){
					 $newrule[] = $v;
				 }
			 }else{
				 $newrule[] = $v;
			 }
		 }
		  
		 $data['cxlist'] = $newrule;		  
	 	 Mysite::$app->setdata($data); 
		 
	  if($shopinfo['shoptype'] == 1){
	 
		Mysite::$app->setAction('mkshopshow');
	  }else{ 
		Mysite::$app->setAction('shopshow');
	  }
		 
		 
   } 
   
   function foodsgg(){  	//8.6新增  规格弹窗
		 $shopshowtype = ICookie::get('shopshowtype');
		 $data['shopshowtype'] = $shopshowtype;
		$id = intval( IReq::get('id') );
       $data['goodsid']=$id;
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
		#print_r($data['productinfo']);
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
		 
		$temparray = $this->mysql->getarr("select * from  ".Mysite::$app->config['tablepre']."comment where goodsid=".$id." and shopid=".$shopid." order by addtime desc limit 10 ");
		$commentlist = array();
		foreach($temparray as $key=>$value){
			$memberinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."member where uid = ".$value['uid']." ");
			$value['username'] =$memberinfo['username'];
			$commentlist[] = $value;
		}
		$data['commentlist'] = $commentlist;
 
		$shuliang = $this->mysql->select_one("select count(id) as zongshu , sum(point) as pointzongshu from  ".Mysite::$app->config['tablepre']."comment where goodsid=".$id." and shopid=".$shopid." order by addtime desc  ");
	 
		$zongshu =  $shuliang['zongshu']; 
		$pointzongshu =  $shuliang['pointzongshu'];
		if($pointzongshu != 0){
			$haoping = round( $zongshu/$pointzongshu,3)*5*100;
		}else{
			$haoping = 0;
		}
	    $data['haoping'] = $haoping;   // 计算好评率
		Mysite::$app->setdata($data);
	  
	
   }
   
   /* 8.2 改变函数 */
  function foodshow(){  	//菜品详情
		$shopshowtype = ICookie::get('shopshowtype');
		$data['shopshowtype'] = $shopshowtype;
		$id = intval( IReq::get('id') );
		$data['goodsid']=$id;
		$wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
		$data['signPackage'] = $signPackage;
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
		$data['pointcount'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."comment   where shopid = ".$shopid." and  goodsid  = ".$id." and is_show = 0 "); 	
        $foodshow['goodattr']= empty($foodshow['goodattr'])?$data['shopinfo']['goodattrdefault']:$foodshow['goodattr'];		
		$foodshow['oldcost'] = $foodshow['cost'];
		if($foodshow['is_cx'] == 1){		 
			$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$foodshow['id']."  ");
			$newdata = getgoodscx($foodshow['cost'],$cxdata);
			$foodshow['zhekou'] = $newdata['zhekou'];
			$foodshow['is_cx'] = $newdata['is_cx'];
			$foodshow['cost'] = $this->formatcost($newdata['cost'],2);
			$foodshow['cxnum'] = $cxdata['cxnum'];
		}
		$foodshow['sellcount'] = $foodshow['sellcount']+$foodshow['virtualsellcount'];
        $foodshow['pointcount']=  $data['pointcount'];
		$foodshow['img']= empty($foodshow['img'])?'':$foodshow['img'];
		$data['shopdet'] = $shopdet;
		$data['foodshow']  = $foodshow;
		/* 配送费 */
		$newshoparray = array_merge($data['shopinfo'],$shopdet);
		$tempinfo =   $this->pscost($newshoparray); 
		$backdata['pstype'] = $tempinfo['pstype'];
		$backdata['pscost'] = $tempinfo['pscost'];
		$backdata['is_allow_ziti'] = $tempinfo['is_allow_ziti'];
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
		$temparray = $this->mysql->getarr("select * from  ".Mysite::$app->config['tablepre']."comment where goodsid=".$id." and shopid=".$shopid." order by addtime desc limit 10 ");
		$commentlist = array();
		foreach($temparray as $key=>$value){
			$memberinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."member where uid = ".$value['uid']." ");
			$value['username'] =$memberinfo['username'];
			$commentlist[] = $value;
		}
		$data['commentlist'] = $commentlist; 
		$shuliang = $this->mysql->select_one("select count(point) as pointzongshu from  ".Mysite::$app->config['tablepre']."comment where goodsid=".$id." and shopid=".$shopid." order by addtime desc  ");
		$commentlist = $this->mysql->select_one("select count(point) as zongshu from  ".Mysite::$app->config['tablepre']."comment where goodsid=".$id." and shopid=".$shopid." and point = 5 order by addtime desc  ");
		$zongshu =  $commentlist['zongshu']; 
		$pointzongshu =  $shuliang['pointzongshu'];
		if($pointzongshu != 0){
			$haoping = round(($zongshu/$pointzongshu) * 100);
		}else{
			$haoping = 0;
		}
	    $data['haoping'] = $haoping;   // 计算好评率
		Mysite::$app->setdata($data);	
   }
   
    /* 8.2新增函数 */
  function showshoprealimg(){
        $parent_id= IFilter::act(IReq::get('parent_id'));
        $shoprealimg = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoprealimg where  parent_id = ".$parent_id);//商家实景分类图片
        $data['shoprealimg']=$shoprealimg;
        Mysite::$app->setdata($data);
    }

  /* 8.2 改变函数 */
   function getdetailinfo(){
	   	$typelx = IFilter::act(IReq::get('typelx')); 
	    $data['typelx'] = $typelx;
	    $shopid = IFilter::act(IReq::get('shopid'));
	    $shopinfo = $this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."shop  where id = ".$shopid." ");
	    if(empty($shopinfo)) $this->message('获取店铺数据失败');
	    $data['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$shopinfo['id']."' ");//收藏
		$shopreal = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopreal where  shopid = ".$shopid." ");//商家实景分类
		$data['shopreal']=array();
        foreach($shopreal as $key=>$val){
            $shoprealimgx = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoprealimg where  parent_id = ".$val['id']);//商家实景分类图片
            $shoprealimg = array();
			foreach($shoprealimgx as $k=>$v){
			    $v['img'] = getImgQuanDir($v['img']);
				$shoprealimg[] = $v;
			}
			$imgcount = $this->mysql->select_one("select count(id) as count from ".Mysite::$app->config['tablepre']."shoprealimg where  parent_id = ".$val['id']);//商家实景分类图片总数
            $val['shoprealimg']=$shoprealimg;
            $val['imgcount']=$imgcount['count'];
            $data['shopreal'][]=$val;
        }
	    $shopshowtype = $shopinfo['shoptype'];
	    if( $shopshowtype == 1 ){
			$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   "); 
		}else{
			$shopdet  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   "); 
		}
		/* 店铺星级计算 */
		$zongpoint = $shopinfo['point'];
		$zongpointcount = $shopinfo['pointcount'];
		 
		if($zongpointcount != 0 ){
			$shopstart = intval( round($zongpoint/$zongpointcount) );
		}else{
			$shopstart= 0;
		}
		$d = date("w") ==0?7:date("w");
        $time = time();	
		$cxinfo = $this->mysql->getarr("select id,name,imgurl,controltype,parentid from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$shopid.",shopid)  and status = 1 and ( limittype =1 or ( limittype = 2 and  FIND_IN_SET(".$d.",limittime) ) or ( limittype = 3 and endtime > ".$time." and starttime < ".$time.")) order by id desc ");          
		$data['cxlist'] = $cxinfo;
		$areaid = ICookie::get('myaddress');	 
		$newshoparray = array_merge($shopinfo,$shopdet);
      	$tempinfo =   $this->pscost($newshoparray); 
	    $backdata['pstype'] = $tempinfo['pstype'];
	    $backdata['pscost'] = $tempinfo['pscost'];
		$backdata['is_allow_ziti'] = $tempinfo['is_allow_ziti'];
        $data['psinfo'] = $backdata;
		$data['shopstart'] = $shopstart;
	    $data['shopinfo'] = $shopinfo;
	    $data['shopdet'] = $shopdet;
	    Mysite::$app->setdata($data);
   }
   function getshopcomment(){
	   	$typelx = IFilter::act(IReq::get('typelx')); 
		$data['typelx'] = $typelx;
	    $shopid = IFilter::act(IReq::get('shopid'));
	    $shopinfo = $this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."shop  where id = ".$shopid." "); 
		 
		$data['shopinfo'] = $shopinfo;
		$shopshowtype = $shopinfo['shoptype'];
	    if( $shopshowtype == 1 ){
			$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   "); 
		}else{
			$shopdet  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   "); 
		}
		$data['shopdet'] = $shopdet;
	    if(empty($shopinfo)) $this->message('获取店铺数据失败');
	    $data['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$shopinfo['id']."' ");//收藏
		  
	    if( $shopinfo['pointcount'] != 0){
			$zongtistart = intval( round( $shopinfo['point']/$shopinfo['pointcount'] )); // 总体评分  // 12 / 3 = 54
			$zonghefen =   round( $shopinfo['point']/$shopinfo['pointcount'],1); // 综合评分 
		}else{
			$zongtistart = 0;
			$zonghefen = 0;
		}
		if( $shopinfo['pointcount'] != 0){
			$psfuwustart = intval(round( $shopinfo['psservicepoint']/$shopinfo['psservicepointcount'] )); // 配送服务  
		}else{
			$psfuwustart = 0;
		}
		 
		$data['zonghefen'] = $zonghefen > 5?5:$zonghefen;
		$data['zongtistart'] = $zongtistart > 5?5:$zongtistart;
		$data['psfuwustart'] = $psfuwustart > 5?5:$psfuwustart;
		$pageinfo = new page();
	    $pageinfo->setpage(intval(IReq::get('page')),5); 
	    $commentlist = $this->mysql->getarr("select  com.*,sh.shopname,b.username,ort.goodsname   from  ".Mysite::$app->config['tablepre']."comment  as com left join ".Mysite::$app->config['tablepre']."member as b on com.uid = b.uid left join ".Mysite::$app->config['tablepre']."shop as sh on sh.id = com.shopid left join ".Mysite::$app->config['tablepre']."orderdet as ort on ort.id = com.orderdetid  where  com.shopid=".$shopid." order by com.addtime desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
	 	$commentlist = array();
		foreach($temparray as $key=>$value){
			$memberinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."member where uid = ".$value['uid']." ");
			#print_r($memberinfo);
			$goodinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."goods where id = ".$value['goodsid']." ");
			$value['username'] =$memberinfo['username'];
			$value['userlogo'] = empty($memberinfo['logo'])?getImgQuanDir(Mysite::$app->config['userlogo']):getImgQuanDir($memberinfo['logo']);
			#$value['goodname'] =$value['goodsname'];
			$value['goodpoint'] =$goodinfo['point']; 
			$commentlist[] = $value;
		}
		$data['commentlist'] = $commentlist;
 
		$shuliang = $this->mysql->select_one("select count(id) as zongshu , sum(point) as pointzongshu from  ".Mysite::$app->config['tablepre']."comment where   shopid=".$shopid." order by addtime desc  ");
		$zongshu =  $shuliang['zongshu']; 
		$pointzongshu =  $shuliang['pointzongshu'];
		if($pointzongshu != 0){
			$haoping = round( $zongshu/$pointzongshu,3 )*100;
		}else{
			$haoping = 0;
		}
	    $data['haoping'] = $haoping;   // 计算好评率
		
	 
		
	#   print_r( $data['commentlist'] );
	   Mysite::$app->setdata($data);
   }
    function getshopmorecomment(){
	   	$typelx = IFilter::act(IReq::get('typelx')); 
			$data['typelx'] = $typelx;
	   $shopid = IFilter::act(IReq::get('shopid'));
	   $goodid = IFilter::act(IReq::get('goodid'));
	    $shopinfo = $this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."shop  where id = ".$shopid." "); 
		$data['shopinfo'] = $shopinfo;
	
		
		  $pageinfo = new page();
	  $pageinfo->setpage(intval(IReq::get('page')),5); 
	 if( !empty($goodid) ){ 
	   $temparray = $this->mysql->getarr("select * from  ".Mysite::$app->config['tablepre']."comment where  shopid=".$shopid." and is_show = 0 and goodsid = ".$goodid." order by addtime desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
	 }else{
		  $temparray = $this->mysql->getarr("select * from  ".Mysite::$app->config['tablepre']."comment where  shopid=".$shopid." and is_show = 0 order by addtime desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
	 }
		$commentlist = array();
		foreach($temparray as $key=>$value){
			$memberinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."member where uid = ".$value['uid']." ");
			$goodinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."goods where id = ".$value['goodsid']." "); 
			#print_r($goodinfo);
			$value['userlogo'] = empty($memberinfo['logo'])?getImgQuanDir(Mysite::$app->config['userlogo']):getImgQuanDir($memberinfo['logo']);
			if(empty($goodinfo)){
				$goodinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."product where goodsid  = ".$value['goodsid']." ");
				$value['goodname'] =$goodinfo['goodsname'];
			}else{
				$value['goodname'] =$goodinfo['name'];
			}
			$value['username'] =$memberinfo['username'];
			
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
			$value['goodpoint'] =$goodinfo['point']; 
			$value['addtime'] = date('Y-m-d H:i',$value['addtime']);  
			$value['huifutime'] = date('Y-m-d H:i',$value['replytime']);  
			$commentlist[] = $value;
		}
		#print_r($commentlist);
		$data['commentlist'] = $commentlist;
 #  print_r($data['commentlist']);
		
	/* 	$datas = json_encode($data);
		  echo 'showmoreorder('.$datas.')';
		  exit; 
			 */
		
	#   print_r( $data['commentlist'] );
	   Mysite::$app->setdata($data);
   }
   
   function gowei(){
   	 
   	 $id = IFilter::act(IReq::get('id'));    

   	$data['scoretocost'] = Mysite::$app->config['scoretocost']; 
   	//	id	card 优惠劵卡号	card_password 优惠劵密码	status 状态，0未使用，1已绑定，2已使用，3无效	creattime 制造时间	cost 优惠金额	limitcost 购物车限制金额下限	endtime 失效时间	uid 用户ID	username 用户名	usetime 使用时间	name
   	$data['juanlist'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid='".$this->member['uid']."' and endtime > ".time()." and status = 1   ");
     
   	 $shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where shopid = ".$id."   ");  
		 if(empty($shopinfo)){
		 	 $shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where shopid = ".$id."   ");  
		 } 
		 
		$nowtime = time();
		 $timelist = array();
		 $info = explode('|',$shopinfo['starttime']);
		 $info = is_array($info)?$info:array($info);
		 $data['is_open'] = 0;
	  
     if($shopinfo['is_open'] == 0  || $shopinfo['is_pass'] == 0){
		 	  $data['is_open'] = 0;
		 }
		 
		 
		 
		$nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($shopinfo['postdate'])?unserialize($shopinfo['postdate']):array();
		$data['timelist'] = array();
		$checknow = time();
		$whilestatic = $shopinfo['befortime'];
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
				//	$tempt['s'] = $tempt['d'].' '.$tempt['s'];
					$tempt['s'] = $tempt['s'];
					$tempt['i'] =  $value['i'];
					$tempt['cost'] =  isset($value['cost'])?$value['cost']:0;
					
					$tempt['name'] = $stime > $checknow?$tempt['s'].'-'.$tempt['e']:'立即配送';
					$data['timelist'][] = $tempt;
				}
			}
		 
			$nowwhiltcheck = $nowwhiltcheck+1;
		}  
		 	
		  $data['lat'] = ICookie::get('lat');
		  $data['lng'] = ICookie::get('lng');
     $data['deaddress'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where userid = ".$this->member['uid']." and `default`=1   "); 
   	 $data['shopinfo'] = $shopinfo;
	
    	Mysite::$app->setdata($data); 
  }
  function goweishop(){//购物车 
   	 $id = IFilter::act(IReq::get('id'));    
   	$data['scoretocost'] = Mysite::$app->config['scoretocost']; 
   	//	id	card 优惠劵卡号	card_password 优惠劵密码	status 状态，0未使用，1已绑定，2已使用，3无效	creattime 制造时间	cost 优惠金额	limitcost 购物车限制金额下限	endtime 失效时间	uid 用户ID	username 用户名	usetime 使用时间	name
   	$data['juanlist'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid='".$this->member['uid']."' and endtime > ".time()." and status = 1   ");
     
   	 $shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where shopid = ".$id."   ");  
		 if(empty($shopinfo)){
		 	 $shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where shopid = ".$id."   ");  
		 }  
		 $nowtime = time();
		 $timelist = array();
		 $info = explode('|',$shopinfo['starttime']);
		 $info = is_array($info)?$info:array($info);
		 $data['is_open'] = 0;
	  
     if($shopinfo['is_open'] == 0  || $shopinfo['is_pass'] == 0){
		 	  $data['is_open'] = 0;
		 }
		 
		 
		 
		$nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($shopinfo['postdate'])?unserialize($shopinfo['postdate']):array();
		$data['timelist'] = array();
		$checknow = time();
		$whilestatic = $shopinfo['befortime'];
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
				//	$tempt['s'] = $tempt['d'].' '.$tempt['s'];
					$tempt['s'] =  $tempt['s'];
					$tempt['i'] =  $value['i'];
					$tempt['cost'] =  isset($value['cost'])?$value['cost']:0;
					
					$tempt['name'] = $stime > $checknow?$tempt['s'].'-'.$tempt['e']:'立即配送';
					$data['timelist'][] = $tempt;
				}
			}
		 
			$nowwhiltcheck = $nowwhiltcheck+1;
		} 
	    	
		  $data['lat'] = ICookie::get('lat');
		  $data['lng'] = ICookie::get('lng');
     $data['deaddress'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where userid = ".$this->member['uid']." and `default`=1   ");  
	 $data['shopinfo'] = $shopinfo;
    	Mysite::$app->setdata($data); 
   }
   function shopgoodslist(){//点菜
   	    $id = IFilter::act(IReq::get('id'));    
   	    $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$id."' ");
   	    $data['shopinfo'] = $shopinfo;
   	    if(empty($shopinfo)){
	 	 	$link = IUrl::creatUrl('wxsite/shoplist'); 
	        $this->message('',$link); 
	 	} 
	 	$data['goodstype'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodstype where shopid='".$id."' ");
	 	$data['goodslist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid='".$id."' ");
   	    Mysite::$app->setdata($data); 
   }
    function creatzttime($shopid){
	    $zttime = array();		 
	    $shopinfo =  $this->mysql->select_one("select starttime,is_open,ziti_time from ".Mysite::$app->config['tablepre']."shop where id = ".$shopid."   ");
		 
	    if($shopinfo['is_open'] == 1 && !empty($shopinfo['starttime'])){
		    $shopoptimes = explode('|',$shopinfo['starttime']);//Array([0] => 01:00-12:00[1] => 13:00-14:00[2] => 14:02-23:00)
		    $timearr = array();
			$flagkey = 'x';
			foreach($shopoptimes as $k=>$v){//01:00-12:00
				$atime = explode('-',$v); //Array([0] => 01:00[1] => 12:00)
				$dftime = strtotime($atime[0]) + $shopinfo['ziti_time'] * 60;
				$detime = strtotime($atime[1]);
				if(time() >= strtotime($atime[0]) && time() <= strtotime($atime[1]) ){
					$flagkey = $k;
				}
				while($dftime <= ($detime - $shopinfo['ziti_time'] * 60)) {
				    $timearr[$k][] = date('H:i',$dftime);
					$dftime = $dftime + 20 *60;
				} 
			}	
			 
			if($flagkey === 'x'){ //不在任何一个时间段内
				foreach($timearr as $k1=>$v1){
					if(strtotime($v1[0]) > time() ){
						foreach($v1 as $k2=>$v2){
							$zttime[] = $v2;
						}
					}	
				}
			}else{//在某个时间段内   该时间段需要重新生成自提时间				
				$nowtimed = $shopoptimes[$flagkey];
				
				$wtime = explode('-',$nowtimed);
				$rtime = time() + $shopinfo['ziti_time'] * 60;
				$ytimearr = array();
				while($rtime <= (strtotime($wtime[1]) - $shopinfo['ziti_time'] * 60)) {
				    $ytimearr[] = date('H:i',$rtime);
					$rtime = $rtime + 20 *60;
				}
				$delkey = 0;
				
				while($delkey <= $flagkey ) {
				    unset($timearr[$delkey]);
					$delkey = $delkey + 1;
				}
				$ptime = array(); 
				foreach($timearr as $k3=>$v3){
					foreach($v3 as $k4=>$v4){
						$ptime[] = $v4;
					}
				}
				 
				$zttime = array_merge($ytimearr,$ptime);

			}
			 
	    }	
		
	    return $zttime; 
    }
   function shopcart(){//购物车
        $weblink = ICookie::set('wx_login_link',Mysite::$app->config['siteurl'].$_SERVER["REQUEST_URI"]);
        $this->checkwxweb();
	    $link = IUrl::creatUrl('wxsite/shopcart');
		$id = IFilter::act(IReq::get('id'));
		$data['scoretocost'] = Mysite::$app->config['scoretocost'];
		$data['memberscoret'] = $this->member['score'] ;
		if($this->member['score'] >= $data['scoretocost'] ){
			$data['scoretext'] = '选择抵扣积分';
		}else{
			$data['scoretext'] = '共'.$this->member['score'].'积分,满'.$data['scoretocost'].'积分可用';
		}
        $data['zttimelist'] = $this->creatzttime($id);	     
		$data['juanlist'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan  where uid='".$this->member['uid']."' and creattime < ".time()." and endtime > ".time()." and status < 2   ");
		$data['wxjuanlist'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."wxuserjuan  where uid='".$this->member['uid']."' and endtime > ".time()." and lqstatus = 1 and status = 0   ");
   	    $shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where shopid = ".$id."   ");
		$shopinfo['shoplogo'] = empty($shopinfo['shoplogo'] )?Mysite::$app->config['shoplogo']:$shopinfo['shoplogo'] ;
		$shopinfo['shoplogo'] = getImgQuanDir($shopinfo['shoplogo']);
		if(empty($shopinfo)){
		 	$shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where shopid = ".$id."   ");
		}
		$nowtime = time();
		$timelist = array();
		$info = explode('|',$shopinfo['starttime']);
		$info = is_array($info)?$info:array($info);
		$data['is_open'] = 0;
        if($shopinfo['is_open'] == 0  || $shopinfo['is_pass'] == 0){
		 	$data['is_open'] = 0;
		}
		$nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($shopinfo['postdate'])?unserialize($shopinfo['postdate']):array();
		$data['timelist'] = array();
		$checknow = time();
		$whilestatic = $shopinfo['befortime'];
		
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
					$tempt['cost'] =  isset($value['cost'])?$this->formatcost($value['cost'],2):'0';
					$tempt['name'] = $tempt['s'].'-'.$tempt['e'];
					$tempt['name1']= $tempt['s'].'-'.$tempt['e'];
                    $datatimelist[] = $tempt;
				}
			}
			$nowwhiltcheck = $nowwhiltcheck+1;
		}		
		foreach($datatimelist as $k=>$v){
			$dtime = date("H:i",time());
			$timearr = explode('-',$v['name']);
			if($k == 0 && $dtime>$timearr[0] && $dtime<$timearr[1]){
				$v['name']='立即配送';					 
			}
			$data['timelist'][]=$v;
		}
        if(empty($this->member['uid']) || $this->member['uid'] ==  0){
			$data['deaddress']  = array();
			$cdata['id'] = 0;
			$cdata['default'] = 1;
			$cdata['contactname'] = ICookie::get('wxguest_username');
			$cdata['phone'] = ICookie::get('wxguest_phone');
			$cdata['address']  = ICookie::get('wxguest_address');
			if(empty($cdata['contactname'])){
				$data['deaddress'] = array();
			}else{
				$data['deaddress'] = $cdata;
			}
		}else{
			$data['deaddress'] =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where userid = ".$this->member['uid']." and `default`=1   ");
			$areaid = ICookie::get('myaddress');
			$template = $this->pscost($shopinfo,$data['deaddress']['lng'],$data['deaddress']['lat']);			 
			$data['deaddress']['newpscost'] = $template['pscost'];
			$data['deaddress']['canps'] = $template['canps'];
		}
		$data['lat'] = ICookie::get('lat');
		$data['lng'] = ICookie::get('lng');
		$juli = $this->GetDistance2($data['lat'], $data['lng'], $shopinfo['lat'], $shopinfo['lng']); 
	    #print_r($data['lat'].'----'.$data['lng']);exit;
		$data['juli'] = $juli;
		$data['addressname'] =  ICookie::get('addressname');
		$data['shopinfo'] = $shopinfo;
		$data['allow_ziti'] = ($this->platpsinfo['is_allow_ziti'] == 1 && $shopinfo['is_ziti'] == 1 )?1:0;
		$waimai_psrangearr = $this->platpsinfo['waimai_psrange'];
		$waimai_psrangearr = explode('#',$waimai_psrangearr);
		$data['waimai_psrange_arr'] = $waimai_psrangearr;
		$data['canpaytype'] = explode(',',$this->platpsinfo['paytype']);
        $data['canpaytypestr'] = $this->platpsinfo['paytype'];  	
        $data['ziti_phone'] = $this->member['phone'];  	
		$data['zdy_ztimg'] = getImgQuanDir(Mysite::$app->config['ztimg']);
		$data['zdy_shoppsimg'] = getImgQuanDir(Mysite::$app->config['shoppsimg']);
		$data['zdy_psimg'] = getImgQuanDir(Mysite::$app->config['psimg']);		
      	Mysite::$app->setdata($data);
   }
   function getjuan(){
	  $this->checkwxweb();
	  $id = intval( IReq::get('id') );
	  $wxuserjuan = $this->mysql->select_one("select a.*,b.cartdesrc from ".Mysite::$app->config['tablepre']."wxuserjuan as a left join ".Mysite::$app->config['tablepre']."wxjuan as b on a.juanid = b.id  where a.id='".$id."' and a.uid='".$this->member['uid']."'  ");
	  if(empty($wxuserjuan)) $this->message('获取用户失败！');
	  $data['wxuserjuan']  = $wxuserjuan;
	  Mysite::$app->setdata($data);
   }
   
    function wxgetjuan(){
	$this->checkwxweb();
	 $id = intval( IReq::get('id') );
	 $wxuserjuan = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuserjuan where id='".$id."'");
	 
	   if(empty($wxuserjuan)) $this->message('获取优惠卷失败！');
	 
	   $data['lqstatus']  = 1;
	    $this->mysql->update(Mysite::$app->config['tablepre'].'wxuserjuan',$data,"id='".$id."'");

		
	   $this->success('success'); 
	}
	

	function member(){//用户中心  
		$wxuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where uid='".$this->member['uid']."'");
		if(!empty($this->member['shopid'])){
			$shopinfo = $this->mysql->select_one("select is_pass from ".Mysite::$app->config['tablepre']."shop where id='".$this->member['shopid']."'");
			#print_r($shopinfo);
			$data['shoppass'] = $shopinfo['is_pass'];
		}
		$data['wxuserbangd'] =  $wxuser['is_bang'];
		$data['is_showbd'] = 1;
		if(empty($wxuser)){
			$data['is_showbd'] = 0;
		}else{
			if($wxuser['is_bang'] == 1){
				$data['is_showbd'] = 0;
			}
		}
		$data['userinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$this->member['uid']."' ");
		$data['phone'] =  substr($data['userinfo']['phone'], 0, 3).'****'.substr($data['userinfo']['phone'], 7);
		$data['juanshu'] = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."juan where uid='".$this->member['uid']."'  and uid >0 and status < 2 and  endtime > ".time()."  order by id asc "); 
		$data['wxjuanshu'] = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."wxuserjuan where uid='".$this->member['uid']."'  and uid >0  and lqstatus = 1 order by id asc limit 0,50");
		Mysite::$app->setdata($data); 
   	
    }
	function bangdmem(){
	   #print_r($this->member);exit;
	       $wxuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where uid='".$this->member['uid']."'");
		   $oauther = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."oauth where uid='".$this->member['uid']."'");
		  	$link = IUrl::creatUrl('wxsite/member');
		if(empty($wxuser) && empty($oauther)) $this->message('未关注我们，不可绑定帐号',$link);
	}
	function wxbangduser(){
		
	   $wxbanguser = trim(IReq::get('wxbanguser'));
	   $wxbangpsw = trim(IReq::get('wxbangpsw'));
	   #print_r($wxbanguser);
	   $wxuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where uid='".$this->member['uid']."'");
	   $oauther = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."oauth where uid='".$this->member['uid']."'");
	  
		if(empty($wxuser) && empty($oauther)) $this->message('未关注我们，不可绑定帐号');
		
//		if($wxuser['is_bang'] == 1) $this->message('已绑订帐号不可重复绑定');
	   
	    if(empty($wxbanguser)) $this->message('绑定帐号失败,帐号为空');
	   if(empty($wxbangpsw )) $this->message('绑定帐号失败,密码为空');
		//已 注册用户绑定		
		$info =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where (email='".$wxbanguser."' or username='".$wxbanguser."') ");//wmr2
			#print_r($this->member);
		  #print_r($info);
			 if(empty($info)) $this->message('绑定帐号失败,帐号未查找到');
			  if(!empty($info['is_bang'])) $this->message('帐号已绑定其他帐号');
			  if( $info['password'] != md5($wxbangpsw) ) $this->message('帐号绑订失败,密码错误');//怎么样绑订定微信号
	   if($info['is_bang'] == 1) $this->message('该账号已绑定其他微信用户');
			 $data['uid'] = $info['uid'];
			 $data['is_bang'] = 1;
			 $this->mysql->update(Mysite::$app->config['tablepre'].'wxuser',$data,"uid='".$this->member['uid']."'");  
			 
			 
			 //删除默认绑定帐号
			 $temuser  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$wxuser['uid']."' ");
			 #print_r($temuser);exit;
			 $all['score'] = $temuser['score']+$info['score'];
			 $all['cost'] =  $temuser['cost'] +$info['cost']; 
			 $all['is_bang'] = 1;
			 $this->mysql->update(Mysite::$app->config['tablepre'].'member',$all,"uid='".$info['uid']."'");  
			 $this->success('绑定帐号成功');
			
	 
		#	print_r($this->member['uid']);
		
	//绑定未注册的用户   插入用户信息	
	/*
	 $arr['username'] = $tname;
     $arr['phone'] = $phone;
     $arr['address'] = $address;
     $arr['password'] = md5($password);
     $arr['email'] = $email;
     $arr['creattime'] = time(); 
     $arr['score']  = $score == 0?Mysite::$app->config['regesterscore']:$score;
     $arr['logintime'] = time(); 
     $arr['logo'] = $userlogo;
     $arr['loginip'] = IClient::getIp();
     $arr['group'] = $group;
     $arr['cost'] = $cost; 
     $arr['parent_id'] = intval(ICookie::get('logincode'));  
     $this->mysql->insert(Mysite::$app->config['tablepre'].'member',$arr);   
     #$this->uid = $this->mysql->insertid();
     $this->uid =  $wxuser['uid'];
	
     if($arr['score'] > 0){
        $this->addlog($this->uid,1,1,$arr['score'],'注册送积分','注册送积分'.$arr['score'],$arr['score']);
     }
     
     
     $logintype = ICookie::get('adlogintype');
	 	 $token = ICookie::get('adtoken');
	 	 $openid = ICookie::get('adopenid'); 
	 	 if(!empty($logintype)){
	 	 	   $apiinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."otherlogin where loginname='".$logintype."'  ");
	 	 	   if(!empty($apiinfo)){
	 	 	   	//更新
	 	 	   	  $tempuid = $this->uid;
	 	 	   	  $this->mysql->update(Mysite::$app->config['tablepre'].'oauth',array('uid'=>$tempuid),"openid='".$openid."' and type = '".$logintype."'"); 
	          ICookie::set('logintype',$logintype,86400);  
	 	 	   }
	 	 }
	 	 if(Mysite::$app->config['regester_juan'] ==1){
	 	   //注册送优惠券
	 	   $nowtime = time();
	 	   $endtime = $nowtime+Mysite::$app->config['regester_juanday']*24*60*60;
	 	   $juandata['card'] = $nowtime.rand(100,999);
       $juandata['card_password'] =  substr(md5($juandata['card']),0,5);	
       $juandata['status'] = 1;// 状态，0未使用，1已绑定，2已使用，3无效	
       $juandata['creattime'] = $nowtime;// 制造时间	
       $juandata['cost'] = Mysite::$app->config['regester_juancost'];// 优惠金额	
       $juandata['limitcost'] =  Mysite::$app->config['regester_juanlimit'];// 购物车限制金额下限	
       $juandata['endtime'] = $endtime;// 失效时间	
       $juandata['uid'] = $this->uid;// 用户ID	
       $juandata['username'] = $arr['username'];// 用户名	
       $juandata['name'] = '注册账号赠送优惠券';//  优惠券名称 
	 	   $this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$juandata); 
	 	 
	 	 }
	 */	 
		
		
		
		
   }
   function paycart(){
	   
   }
   
   	function payonline(){
		//在线支付
		$this->checkmemberlogin();
		$paytype='alimobile';
	 	$cost = intval(IReq::get('cost'));
	 	if($cost < 10) $this->message('card_limit');
	 	$paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id asc limit 0,50");
		if(is_array($paylist)){
		  foreach($paylist as $key=>$value){
			   $paytypelist[] =$value['loginname'];		 
		  }
	  }
		if(!in_array($paytype,$paytypelist)){
		  $this->message('other_nodefinepay');
		} 
		$paydir = hopedir.'/plug/pay/'.$paytype;
	 	if(!file_exists($paydir.'/pay.php'))
    { 
      	$this->message('other_notinstallapi');
    } 
	 	$dopaydata = array('type'=>'acount','upid'=>$this->member['uid'],'cost'=>$cost);//支付数据
    include_once($paydir.'/pay.php');  
	}
	
	/* 8.3新增 */
	function rechargepayonline(){ 
		//余额充值用手机支付宝在线支付
		$this->checkmemberlogin();
		$cost = intval(IFilter::act( IReq::get('cost') ));
	 	$paytype =  'alimobile';
  	 	 
  	 	$paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id asc limit 0,50");
		if(is_array($paylist)){
		  foreach($paylist as $key=>$value){
			   $paytypelist[] =$value['loginname'];		 
		  }
	    }
		if(!in_array($paytype,$paytypelist)){
		  $this->message('other_nodefinepay');
		} 
		$paydir = hopedir.'/plug/pay/'.$paytype;
	
	 	if(!file_exists($paydir.'/pay.php'))
    { 
      	$this->message('other_notinstallapi');
    } 
	 	$dopaydata = array('type'=>'acount','upid'=>$this->member['uid'],'cost'=>$cost);//支付数据
    include_once($paydir.'/pay.php');  
	}
	
	
   	function exchangcard(){		//充值卡充值
		$this->checkmemberlogin();
		$card = trim(IFilter::act(IReq::get('card')));
		$password = trim(IFilter::act(IReq::get('password')));
		if(empty($card)) $this->message('card_emptycard');
		if(empty($password)) $this->message('card_emptycardpwd');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."card where card ='".$card."'  and card_password = '".$password."' and uid =0 and status = 0");
		if(empty($checkinfo)) $this->message('充值卡不存在,请再核对下');
		$arr['uid'] = $this->member['uid'];
		$arr['status'] =  1;
		$arr['username'] = $this->member['username'];
                $arr['usetime'] = time();
               
		$this->mysql->update(Mysite::$app->config['tablepre'].'card',$arr,"card ='".$card."'  and card_password = '".$password."' and uid =0 and status = 0");
		//`$key`
		$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$checkinfo['cost'],"uid ='".$this->member['uid']."' ");
		$allcost = $this->member['cost']+$checkinfo['cost'];
       $this->memberCls->addlog($this->member['uid'],2,1,$checkinfo['cost'],'充值卡充值','使用充值卡'.$checkinfo['card'].'充值'.$checkinfo['cost'].'元',$allcost);
	   
	   
	   $this->memberCls->addmemcostlog($this->member['uid'],$this->member['username'],$this->member['cost'],1,$checkinfo['cost'],$allcost,$this->member['username']."使用充值卡充值",$this->member['uid'],$this->member['username']);
 	   
 		$this->success('success');
	}
    function memcard(){
        $this->checkwxweb();

	 	$link = IUrl::creatUrl('wxsite/shoplist'); 
	  if($this->member['uid'] == 0)  $this->message('',$link); 
	  $tarelist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."address where userid='".$this->member['uid']."'   order by id asc limit 0,50");
	  $arelist = array();
	  $areaid=array();
	  foreach($tarelist as $key=>$value){
	     $areaid[] = $value['areaid1'];
	     $areaid[] = $value['areaid3'];
	     $areaid[] = $value['areaid2'];  
	  }
	  if(count($areaid) > 0){ 
	     $areaarr = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."area  where id in(".join(',',$areaid).")  order by id asc limit 0,1000"); 
	     foreach($areaarr as $key=>$value){
	  	    $arelist[$value['id']] = $value['name'];
	     } 
	  }
	  $data['arealist'] = $tarelist;
	  $data['areaarr'] = $arelist;
	  
	  /* 8.3新增 */
	    
	  $rechargelist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."rechargecost where cost > 0 order by cost asc limit 0,10000");
	  $data['rechargelist'] = array();
	  foreach($rechargelist as $key=>$value ){
		  $totalsendcost = '';
		  if( $value['is_sendcost'] == 1 ){
			  $totalsendcost = $totalsendcost+$value['sendcost'];
		  }
		  if( $value['is_sendjuan'] == 1 ){
			  $totalsendcost = $totalsendcost+$value['sendjuancost'];
		  }
		  $value['totalsendcost'] = $totalsendcost;
		  $data['rechargelist'][] = $value;
	  }
	  #print_r($data['rechargelist']#);
	  
	  
	  Mysite::$app->setdata($data); 
   }
   function address(){
       $this->checkwxweb();

   	$link = IUrl::creatUrl('wxsite/shoplist'); 
	  if($this->member['uid'] == 0)  $this->message('',$link); 
	  $tarelist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."address where userid='".$this->member['uid']."'   order by id asc limit 0,50");
	  $arelist = array();
	  $areaid=array();
	  foreach($tarelist as $key=>$value){
	     $areaid[] = $value['areaid1'];
	     $areaid[] = $value['areaid3'];
	     $areaid[] = $value['areaid2'];  
	  }
	  if(count($areaid) > 0){ 
	     $areaarr = $this->mysql->getarr("select id,name from ".Mysite::$app->config['tablepre']."area  where id in(".join(',',$areaid).")  order by id asc limit 0,1000"); 
	     foreach($areaarr as $key=>$value){
	  	    $arelist[$value['id']] = $value['name'];
	     } 
	  }
	  $data['arealist'] = $tarelist;
	  $data['areaarr'] = $arelist;
	  Mysite::$app->setdata($data); 
   }
   function editaddress(){
		$addressid = intval(IReq::get('id'));
		$data['addressid'] = $addressid;
    	$link = IUrl::creatUrl('wxsite/index'); 
    	$data['backtype'] = IFilter::act(IReq::get('backtype')); 
    	$data['shopid'] = IFilter::act(IReq::get('shopid'));     
 	   $data['addressid'] = IFilter::act(IReq::get('id'));
		  $data['lat'] = ICookie::get('lat');
		  $data['lng'] = ICookie::get('lng');
		#  print_r($data);
		   Mysite::$app->setdata($data);
	   if($this->member['uid'] == 0)  $this->message('',$link); 
   }
   function bkaddress(){ 
   	 $link = IUrl::creatUrl('wxsite/index'); 
	    if($this->member['uid'] == 0)  $this->message('',$link); 
	    $data['shopid'] = IFilter::act(IReq::get('shopid'));   
	    $data['backtype'] =  IFilter::act(IReq::get('backtype'));   
	    $tarelist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."address where userid='".$this->member['uid']."'   order by id asc limit 0,50");
	    $arelist = array(); 
	    $data['arealist'] = $tarelist; 
	    Mysite::$app->setdata($data);  
   }
  function myajaxadlist(){  
		if( $this->checkbackinfo() ){
			if($this->member['uid'] == 0)  $this->message('请先登录！'); 
	    }
		$data['shopid'] = IFilter::act(IReq::get('shopid'));   
	    $data['backtype'] =  IFilter::act(IReq::get('backtype'));   

		
		if(  empty($this->member['uid']) || $this->member['uid'] ==  0){	 
			$tarelist = array();
				$cdata['id'] = 0;
				$cdata['default'] = 1;
				$cdata['contactname'] = ICookie::get('wxguest_username');
				$cdata['phone'] = ICookie::get('wxguest_phone');
				$cdata['address']  = ICookie::get('wxguest_address');
				if(empty($cdata['contactname'])){
					$tarelist = array();
				}else{
					$tarelist[] = $cdata;
				}
			}else{
	    $tarelist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."address where userid='".$this->member['uid']."'    order by id asc limit 0,50"); 
			
			 
			} 	  

	  $this->success($tarelist);
   }
   function savemyaddress(){
			//	if( $this->checkbackinfo() ){
			if($this->member['uid'] == 0) {
				 $username = IFilter::act(IReq::get('contactname'));
				 $phone = IFilter::act(IReq::get('phone'));
				 $address =  IFilter::act(IReq::get('add_new'));
				 
				ICookie::set('wxguest_username',$username,86400);
				ICookie::set('wxguest_phone',$phone,86400);
				ICookie::set('wxguest_address',$address,86400);
				#	$this->message(ICookie::get('wxguest_username'));
				  $this->success('success');
			}
	//	 }
       	 $addressid = intval(IReq::get('addressid'));
		 if(empty($addressid))
		 {
		 	 $checknum = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."address where userid='".$this->member['uid']."' ");
		 if(Mysite::$app->config['addresslimit'] < $checknum)$this->message('member_addresslimit');
       
	     $arr['userid'] = $this->member['uid'];
	     $arr['username'] = $this->member['username'];

	     $arr['address'] =  IFilter::act(IReq::get('add_new'));
	     $arr['phone'] = IFilter::act(IReq::get('phone'));
	     $arr['otherphone'] = '';
	     $arr['contactname'] = IFilter::act(IReq::get('contactname'));
	     $arr['sex'] =  IFilter::act(IReq::get('sex'));
	     $arr['default'] = 1;

	     if(!(IValidate::len(IFilter::act(IReq::get('add_new')),3,50)))$this->message('member_addresslength');
	     if(!(IValidate::phone($arr['phone'])))$this->message('errphone');
	     if(!empty($arr['otherphone'])&&!(IValidate::phone($arr['otherphone'])))$this->message('errphone');
	     if(!(IValidate::len($arr['contactname'],2,6)))$this->message('contactlength');
	      // print_r($arr);exit;
		  $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>0),'userid = '.$this->member['uid'].' ');
	     $this->mysql->insert(Mysite::$app->config['tablepre'].'address',$arr);
	     $this->success('success');
		 }else{
	     $arr['address'] =  IFilter::act(IReq::get('add_new'));
	     $arr['phone'] = IFilter::act(IReq::get('phone'));
	     $arr['otherphone'] = '';
	     $arr['contactname'] = IFilter::act(IReq::get('contactname'));
	     $arr['sex'] =  IFilter::act(IReq::get('sex'));
		  $arr['default'] = 1;
		  
	      if(!(IValidate::len(IFilter::act(IReq::get('add_new')),3,50)))$this->message('member_addresslength');
	     if(!(IValidate::phone($arr['phone'])))$this->message('errphone');
	     if(!empty($arr['otherphone'])&&!(IValidate::phone($arr['otherphone'])))$this->message('errphone');
	     if(!(IValidate::len($arr['contactname'],2,6)))$this->message('contactlength');
	     // print_r($arr);exit;
		    $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>0),'userid = '.$this->member['uid'].' ');
	     $this->mysql->update(Mysite::$app->config['tablepre'].'address',$arr,'userid = '.$this->member['uid'].' and id='.$addressid.'');
	     $this->success('success');
		 }
		$this->success('success');
   }
   function setmydefadid(){
	     if( $this->checkbackinfo() ){
			if($this->member['uid'] == 0)  $this->message('未登录获取地区信息失败'); 
		 }
       	 $addressid = intval(IReq::get('addressid'));
		 if(empty($addressid)) $this->message('默认值错误');
		 $checkdata =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where id='".$addressid."' and userid = '".$this->member['uid']."' ");  
		 if(empty($checkdata)) $this->message('该地址不属于你该账号');
		    $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>0),'userid = '.$this->member['uid'].' ');
	     $this->mysql->update(Mysite::$app->config['tablepre'].'address',array('default'=>1),'userid = '.$this->member['uid'].' and id='.$addressid.'');
		 $this->success('success');
		 
   }



   function order(){
	    
	    $this->checkwxweb();
   		$link = IUrl::creatUrl('wxsite/index');
	    if($this->member['uid'] == 0)  $this->message('',$link);
		$data['checkorder'] = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype != 100 and is_userhide !=1  ");
		Mysite::$app->setdata($data);
   }
   function ptorder(){
	    $this->checkwxweb();
   		$link = IUrl::creatUrl('wxsite/index');
	    if($this->member['uid'] == 0)  $this->message('',$link);
		$data['checkorder'] = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype = 100 and is_userhide !=1  ");
		Mysite::$app->setdata($data);
   }
   function userorder(){
	 $link = IUrl::creatUrl('wxsite/index');
	 if($this->member['uid'] == 0)  $this->message('',$link); 
	  $pageinfo = new page();
	  $pageinfo->setpage(intval(IReq::get('page')),5);  
	  //
	  $datalist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype != 100 and is_userhide !=1 order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
	  $temparray = array('0'=>'外卖','1'=>'超市','2'=>'其他','100'=>'跑腿订单');
	  $backdata = array();
	   
	  foreach($datalist as $key=>$value){

        //自动关闭订单
          if($value['paytype'] == 1 && $value['paystatus'] == 0 && $value['status'] < 3){
              $checktime = time() - $value['addtime'];
              if($checktime > 900){
                  //说明该订单可以关闭
				 //退返本单的优惠券及积分
				 $yhjids = $value['yhjids'];
				 if(!empty($yhjids)){
						$yhjarr = explode(',',$yhjids);
						foreach($yhjarr as $k=>$v){
							$yhjdata['status'] = 0;
							$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$yhjdata,"id ='".$v."' ");
						}
					} 
			     if($value['scoredown'] > 0){
					$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$value['scoredown'],"uid ='".$value['buyeruid']."' ");
				    $jfdata['userid'] =  $value['buyeruid'];
					$jfdata['type'] = 1;
					$jfdata['addtype'] = 1;
					$jfdata['result'] = $value['scoredown'];
					$jfdata['addtime'] = time();
					$jfdata['title'] = '超时订单退还积分';
					$jfdata['content'] = '超时订单退还积分'.$value['scoredown'];  
					$jfdata['acount'] = $this->member['score'] + $value['scoredown'];
		 		    $this->mysql->insert(Mysite::$app->config['tablepre'].'memberlog',$jfdata);
				 }		
                  $cdata['status'] = 4;
                  $this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$value['id']."'");
                  $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = {$value['id']} and status != 3");
                  /*更新订单 状态说明*/
                  $statusdata['orderid']     =  $value['id'];
                  $statusdata['addtime']     =  $value['addtime']+900;
                  $statusdata['statustitle'] =  "订单已取消";
                  $statusdata['ststusdesc']  =  "订单支付超时，系统已自动取消订单";
                  $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
				  //返回商品数量
					$goosinfo =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id = ".$value['id']."");
					if(!empty($goosinfo)){
						foreach($goosinfo as $k=>$val){
							if($val['goodsid'] > 0 && $val['goodscount'] > 0){
								if($val['product_id'] > 0){
									 $this->mysql->update(Mysite::$app->config['tablepre'].'product',"`stock` = `stock`-".$val['goodscount'],"id='".$val['product_id']."'");
								}
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`count` = `count`+".$val['goodscount'],"id='".$val['goodsid']."'");
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`sellcount` = `sellcount`-".$val['goodscount'],"id='".$val['goodsid']."'");
							}
						}
					}				
              }
          }

				$listdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$value['id']."'");  
				$value['det'] = '';
				foreach($listdet as $k=>$v){
					$value['det'] .= $v['goodsname'].',';
				}
				$shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$value['shopid']."'");  
				 
				$value['shoplogo'] = $shopinfo['shoplogo'];
				/*判断订单显示状态*/
				if($value['pstype'] == 1){//商家配送情况
					if($value['status'] == 3){
						$orderstatus = '订单已完成';
					}elseif($value['status'] > 3 ){
						$orderstatus = '订单关闭';
					}else{
						if($value['paytype'] == 1 && $value['paystatus'] == 0){//在线支付未付
							$orderstatus = '待付款';
						}else{
							if($value['is_make'] == 0){
								$orderstatus = '待商家接单';
							}elseif($value['is_make'] == 1){
								if($value['is_ziti'] == 1){
									if($value['posttime']-time() <= $shopinfo['ziti_time']*60 ){
										$orderstatus = '等待到店自取';
									}else{
										$orderstatus = '商家已接单';
									}
								}else{
									if($value['status'] == 2){
										$orderstatus = '配送中';
									}else{
										$orderstatus = '商家已接单';
									}
								}	
							}else{
								$orderstatus = '商家未接单';
							}
						}	
					}
				}else{//平台配送情况
					if($value['status'] > 3){
						$orderstatus = '订单关闭';
					}else{
						if($value['status'] == 3){
							$orderstatus = '订单已完成';
						}else{
							if($value['paytype'] == 1 && $value['paystatus'] == 0){//在线支付未付
							     $orderstatus = '待付款';
							}else{
								if($value['is_make'] == 0){
									$orderstatus = '待商家接单';
								}elseif($value['is_make'] == 1){
									if($value['is_ziti'] == 1){
										if($value['posttime']-time() <= $shopinfo['ziti_time']*60 ){
											$orderstatus = '等待到店自取';
										}else{
											$orderstatus = '商家已接单';
										}
									}else{
										if($value['psuid'] > 0){
											if($value['psstatus'] == 1){
												$orderstatus = '配送员已接单';
											}
											if($value['psstatus'] == 2){
												$orderstatus = '配送员已到店';
											}
											if($value['psstatus'] == 3){
												$orderstatus = '配送中';
											}
										}else{
											$orderstatus = '商家已接单';
										}
									}
									
								}else{
									$orderstatus = '商家未接单';
								}
							}							
						}
					}					 
				}
				if($value['is_reback'] == 2){
					$orderstatus = '订单关闭';
				}
				$value['orderwuliustatus'] = $orderstatus;
				$value['addtime'] = date('Y-m-d H:i',$value['addtime']);
				$backdata[] =$value;
		}
		$data['orderlist'] = $backdata;
		Mysite::$app->setdata($data);
	 
	}
	function userptorder(){
	 $link = IUrl::creatUrl('wxsite/index');
	 if($this->member['uid'] == 0)  $this->message('',$link); 
	  $pageinfo = new page();
	  $pageinfo->setpage(intval(IReq::get('page')),5);  
	  //
	  $datalist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype = 100 and is_userhide !=1 order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
	   
	  $backdata = array();
	   
	  foreach($datalist as $key=>$value){

        //自动关闭订单
          if($value['paytype'] == 1 && $value['paystatus'] == 0 && $value['status'] < 3){
              $checktime = time() - $value['addtime'];
              if($checktime > 900){
                  //说明该订单可以关闭
				 //退返本单的优惠券及积分
				 $yhjids = $value['yhjids'];
				 if(!empty($yhjids)){
						$yhjarr = explode(',',$yhjids);
						foreach($yhjarr as $k=>$v){
							$yhjdata['status'] = 0;
							$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$yhjdata,"id ='".$v."' ");
						}
					} 
			      		
                  $cdata['status'] = 4;
                  $this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$value['id']."'");
                  $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = {$value['id']} and status != 3");
                  /*更新订单 状态说明*/
                  $statusdata['orderid']     =  $value['id'];
                  $statusdata['addtime']     =  $value['addtime']+900;
                  $statusdata['statustitle'] =  "订单已取消";
                  $statusdata['ststusdesc']  =  "订单支付超时，系统已自动取消订单";
                  $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
				   
              }
          }

				 
				 
				/*判断订单显示状态*/
				if($value['is_reback'] == 0 || $value['is_reback'] == 3 ){ //未申请退款或者拒绝退款 
					if($value['status'] > 3){
						$orderstatus = '已取消';
					}elseif($value['status'] == 3){
						$orderstatus = '已完成';
					}else{ 
						if($value['paytype'] == 1 && $value['paystatus'] == 0){//在线支付未付
							 $orderstatus = '待支付';
						}else{
							if($value['psstatus'] == 0){
								$orderstatus = '待接单';
							}elseif($value['psstatus'] == 1){
								$orderstatus = '待取货';								
							}elseif($value['psstatus'] == 2||$value['psstatus'] == 3){
								$orderstatus = '配送中';								
							}else{
								$orderstatus = '已完成';	
							}  
						}							 
					}					 
				}else{
					$orderstatus = $value['is_reback'] == 2? '已退款':'退款中';  
				}
				
				$value['orderwuliustatus'] = $orderstatus;
				$value['addtime'] = date('Y-m-d H:i',$value['addtime']);
				$backdata[] =$value;
		}
		$data['orderlist'] = $backdata;		 
		Mysite::$app->setdata($data);
	 
	}
	function ordershow(){
		$link = IUrl::creatUrl('wxsite/index'); 
		if($this->member['uid'] == 0)  $this->message('未登录',$link); 
		$orderid = intval(IReq::get('orderid')); 
		$wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
 		$data['signPackage'] = $signPackage;
		$shareinfo = $this->mysql->select_one("select title,img,`describe`  from ".Mysite::$app->config['tablepre']."juanshowinfo where id =1  order by orderid asc  ");
		if( empty($shareinfo) ){
			$shareinfo['title'] = Mysite::$app->config['sitename'];
			$shareinfo['img'] = Mysite::$app->config['sitelogo'];
			$shareinfo['describe'] = Mysite::$app->config['sitename'];
		}
		$data['shareinfo'] = $shareinfo;
              # print_R($data);exit;
		$where = "  where type=2 and addtime < ".time()."  and is_open = 1 and juannum > 0 ";
 		$checkinfosendjuan = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanrule ".$where." order by orderid asc ");
		$data['checkinfosendjuan'] = $checkinfosendjuan;
		
		$orderwuliustatus = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderstatus where   orderid = ".$orderid." order by id desc limit 0,10 ");
		$data['orderwuliustatus'] = $orderwuliustatus;
		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 4 or name = '下单送优惠券' " );  	   
        $data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 4 or name= '下单送优惠券' order by id asc " ); 
	    $data['sendjuanstatus'] = $juansetinfo['status'];
		
		if(!empty($orderid)){
	  	 
	     	$order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");
			 
            //自动关闭订单
            if($order['paytype'] == 1 && $order['paystatus'] == 0 && $order['status'] < 3){
                $checktime = time() - $order['addtime'];
                if($checktime > 900){
                    //说明该订单可以关闭
                    //退返本单的优惠券及积分
					 $yhjids = $order['yhjids'];
					 if(!empty($yhjids)){
							$yhjarr = explode(',',$yhjids);
							foreach($yhjarr as $k=>$v){
								$yhjdata['status'] = 0;
								$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$yhjdata,"id ='".$v."' ");
							}
						}  
					 if($order['scoredown'] > 0){
						$this->mysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`+'.$order['scoredown'],"uid ='".$order['buyeruid']."' ");
						$jfdata['userid'] =  $order['buyeruid'];
						$jfdata['type'] = 1;
						$jfdata['addtype'] = 1;
						$jfdata['result'] = $order['scoredown'];
						$jfdata['addtime'] = time();
						$jfdata['title'] = '超时订单退还积分';
						$jfdata['content'] = '超时订单退还积分'.$order['scoredown'];  
						$jfdata['acount'] = $this->member['score'] + $order['scoredown'];
						$this->mysql->insert(Mysite::$app->config['tablepre'].'memberlog',$jfdata);
					 }		
					
					
					
					if($order['yhjids']>0){
                        $jdata['status'] =1;
                        $this->mysql->update(Mysite::$app->config['tablepre'].'juan',$jdata,"id='".$order['yhjids']."'");
                    }
                    $cdata['status'] = 4;
                    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
                    $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$orderid' and status != 3");
                    /*更新订单 状态说明*/
                    $statusdata['orderid']     =  $orderid;
                    $statusdata['addtime']     =  $order['addtime']+900;
                    $statusdata['statustitle'] =  "自动关闭订单";
                    $statusdata['ststusdesc']  =  "在线支付订单，未支付自动关闭";
                    $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
                    $order['status'] = 4;
					//返回商品数量
					$goosinfo =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id = ".$orderid."");
					if(!empty($goosinfo)){
						foreach($goosinfo as $k=>$val){
							if($val['goodsid'] > 0 && $val['goodscount'] > 0){
								if($val['product_id'] > 0){
									 $this->mysql->update(Mysite::$app->config['tablepre'].'product',"`stock` = `stock`-".$val['goodscount'],"id='".$val['product_id']."'");
								}
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`count` = `count`+".$val['goodscount'],"id='".$val['goodsid']."'");
								$this->mysql->update(Mysite::$app->config['tablepre'].'goods',"`sellcount` = `sellcount`-".$val['goodscount'],"id='".$val['goodsid']."'");
							}
						}
					}					
                }
            } 
         
            $data['paytype'] = $order['paytype'];
	     	if(empty($order)){
	     		$data['order'] = '';
	     		Mysite::$app->setdata($data);
	     	}else{
                $scoretocost =Mysite::$app->config['scoretocost'];
                $order['scoredown'] =  $order['scoredown']/$scoretocost;//抵扣积分
	     	     $order['ps'] = $order['shopps'];
	     	     // 超市商品总价	 超市配送配送	shopcost 店铺商品总价	shopps 店铺配送费	pstype 配送方式 0：平台1：个人	bagcost  
       	        $orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$order['id']."'");  
	            $order['cp'] = count($orderdet); 
	            $buyerstatus= array(
	     	     '0'=>'等待处理',
	     	     '1'=>'订餐成功处理中',
	     	     '2'=>'订单已发货',
	     	     '3'=>'订单完成',
	     	     '4'=>'订单已取消',
	     	     '5'=>'订单已取消'
	     	     );
	     	     $paytypelist = array(0=>'货到支付',1=>'在线支付');  
	     	      
	     	     $paytypearr = $paytypelist; 
	     	      $order['is_acceptorder'] = $order['is_acceptorder'];
	     	      $order['surestatus'] = $order['status'];
	     	      $order['basetype'] = $order['paytype'];
	     	      $order['basepaystatus'] =$order['paystatus'];
 
	     	     $order['paytype'] =  $order['paytype'];
				 $shopinfo =  $this->mysql->select_one("select ziti_time from ".Mysite::$app->config['tablepre']."shop where id='".$order['shopid']."'");
				 $order['is_show_quhuo'] = 0;  	             
				 if($order['is_reback'] != 2 && $order['status'] < 3 && $order['is_make'] == 1 ){
					if($order['posttime']-time() <= $shopinfo['ziti_time']*60 && $order['is_ziti'] == 1){//自提单
						$order['is_show_quhuo'] = 1;
					}
					if($order['pstype'] == 1 && $order['status'] == 2){//商家配送 已发货
						$order['is_show_quhuo'] = 1;
					}
					 
				 } 
				 
	     	     $order['paystatus'] = $order['paystatus'] ;
	     	     $order['addtime'] = date('Y-m-d H:i:s',$order['addtime']);
	     	     $order['posttime'] = date('Y-m-d H:i:s',$order['posttime']);
	     	     $order['cxdet'] = unserialize($order['cxdet']);
	     	    
				
				
	     	    $data['order'] = $order;
	            $data['orderdet'] = $orderdet;
				
			    $data['psbpsyinfo'] = array();
			 
				if(   $order['psuid'] > 0 ){
					if(  $order['status'] < 3   ){
						if(  $order['pstype'] == 2  ){
							 $psbinterface = new psbinterface(); 
							$data['psbpsyinfo'] = $psbinterface->getpsbclerkinfo($order['psuid']);
							 #$data['psbpsyinfo']['posilnglat'] = '113.552398,34.808074';
							if( !empty($data['psbpsyinfo']) && !empty($data['psbpsyinfo']['posilnglat']) ){
								$posilnglatarr = explode(',',$data['psbpsyinfo']['posilnglat']);
								$posilng = $posilnglatarr[0];
								$posilat = $posilnglatarr[1];
								if( !empty($posilng) && !empty($posilat)  ){
									$data['psbpsyinfo']['posilnglatarr'] = $posilnglatarr;
								}else{
									$data['psbpsyinfo'] = array();
								}
								
							}
						}else if(   $order['pstype'] == 0    ){
							$data['psbpsyinfo'] = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$order['psuid']."' ");
							if( !empty($data['psbpsyinfo'])  &&  !empty($data['psbpsyinfo']['lng'])  &&  !empty($data['psbpsyinfo']['lat'])      ){
								$data['psbpsyinfo']['posilnglat'] = $data['psbpsyinfo']['lng'].','.$data['psbpsyinfo']['lat'];
							}else{
								 $data['psbpsyinfo'] = array();
							}
						}else{
							$data['psbpsyinfo'] = array();
						}
					}
					 
				 }
				 
	            Mysite::$app->setdata($data);
	           
	       }
	  }else{
	  	$data['order'] = '';
	  	Mysite::$app->setdata($data);
	  }
	}
	
	
	/* 展示订单--位置信息 */
	function routemapshow(){
		$link = IUrl::creatUrl('wxsite/index'); 
		if($this->member['uid'] == 0)  $this->message('未登录',$link); 
		$orderid = intval(IReq::get('id')); 
		$order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");
		#print_r($order);
		$data['order'] = $order;
		Mysite::$app->setdata($data);
	}
	function ajaxroutemapshow(){
		$orderid = intval(IReq::get('id')); 
		$order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid.""); 
		$data['psbpsyinfo'] = array();
		if( !empty($order) ){
			
		}
		
		$data['order'] = $order;
		$data['psbpsyinfo'] = array();
		if(   $order['psuid'] > 0   ){
			if(  $order['status'] < 3   ){
					if(  $order['pstype'] == 2   ){
						 $psbinterface = new psbinterface(); 
						$data['psbpsyinfo'] = $psbinterface->getpsbclerkinfo($order['psuid']);
						if( !empty($data['psbpsyinfo']) && !empty($data['psbpsyinfo']['posilnglat']) ){
							$posilnglatarr = explode(',',$data['psbpsyinfo']['posilnglat']);
							$posilng = $posilnglatarr[0];
							$posilat = $posilnglatarr[1];
							if( !empty($posilng) && !empty($posilat)  ){
								$data['psbpsyinfo']['posilnglatarr'] = $posilnglatarr;
							}else{
								$data['psbpsyinfo'] = array();
							}
							
						}
					}else if(   $order['pstype'] == 0    ){
						$data['psbpsyinfo'] = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$order['psuid']."' ");
						if( !empty($data['psbpsyinfo'])  &&  !empty($data['psbpsyinfo']['lng'])  &&  !empty($data['psbpsyinfo']['lat'])      ){
							$data['psbpsyinfo']['posilnglat'] = $data['psbpsyinfo']['lng'].','.$data['psbpsyinfo']['lat'];
						}else{
							 $data['psbpsyinfo'] = array();
						}
					}else{
						$data['psbpsyinfo'] = array();
					}
			 }else if(  $order['status'] == 3 &&  (  $order['pstype'] == 0 ||  $order['pstype'] == 2  ) ){
						
						$psyoverlng = $order['psyoverlng'];
						$psyoverlat = $order['psyoverlat'];
						$data['psbpsyinfo']['clerkid'] = $order['psuid'];
						$data['psbpsyinfo']['posilnglat'] = $psyoverlng.','.$psyoverlat;
						$data['psbpsyinfo']['posilnglatarr'] = explode(',',$data['psbpsyinfo']['posilnglat']);
						
			 }
		 }
		
		
		$this->success($data);
				
	}
	
	/*评价订单*/
	function commentorder(){
        $this->checkwxweb();
	  	$link = IUrl::creatUrl('wxsite/index'); 
	    if($this->member['uid'] == 0)  $this->message('未登录',$link); 
	    $link = IUrl::creatUrl('wxsite/order'); 
	    $orderid = intval(IReq::get('orderid'));  
	    if(!empty($orderid)){
	  	 
	     	 $order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");   

	     	if(empty($order)){
	     		$data['order'] = '';
	     		Mysite::$app->setdata($data);
	     	}else{
	     	     $data['order'] =$order;
       	     $orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$order['id']."' and is_send = 0 ");   
			 #print_r($orderdet);
	           $data['orderdet'] = $orderdet;
	           $tempcoment = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."comment where orderid='".$order['id']."'");  
	           $data['comment'] = array();
	           foreach($tempcoment as $key=>$value){
	             $data['comment'][$value['orderdetid']] = $value;
	           } 
	           //  id		orderdetid	shopid	goodsid	uid	content	addtime	replycontent	replytime	 评分	is_show 0展示，1不展示
	           Mysite::$app->setdata($data);
	           
	       }
	    }else{
	  	  $data['order'] = '';
	  	  Mysite::$app->setdata($data);
	    } 
	   
	}
	//积分操作
	function gift(){
        $this->checkwxweb();

		if($this->member['uid'] == 0)  $this->message('未登录',$link); 
	  	$link = IUrl::creatUrl('wxsite/index'); 
		$giftlog = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."memberlog where type = 1 and userid = '".$this->member['uid']."' order by addtime desc ");  //积分增减记录
	    $data['giftlog'] = $giftlog;
		Mysite::$app->setdata($data);
	}
	//积分记录
	function giftlog(){
		 $data['logstat'] = array('0'=>'待处理','1'=>'已审核<br>待发货','2'=>'已发货<br>配送中','3'=>'兑换成功','4'=>'已取消兑换'); 
		 Mysite::$app->setdata($data);
	}
	function dhgift(){
		$giftid = intval(IReq::get('giftid'));
		$giftinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."gift where id = ".$giftid."    ");   
		$data['giftinfo']  = $giftinfo;
		Mysite::$app->setdata($data);
	}
	//兑换产品列表
	function giftlist(){
        $this->checkwxweb();

		$data['list'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."adv where `advtype` = 'wxgift' and cityid = ".$this->CITY_ID." " );
		$data['list']['img'] = getImgQuanDir($data['list']['img']);
		Mysite::$app->setdata($data);
	}
	function juan(){
        $this->checkwxweb();
		// 状态，0未使用，1已绑定，2已使用，3无效	 制造时间	 优惠金额	 购物车限制金额下限	 失效时间	uid 用户ID	username 用户名	usetime 使用时间	name
		$cximglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where uid =".$this->member['uid']." and status !=2   order by id desc   ");
		#print_r($cximglist);exit;
		$backdata = array();
		//status 状态，0未使用，1已绑定，2已使用，3无效
		//spotordtype 支持频道 1外卖频道 2超市频道 3跑腿 空或1,2,3时全支持
		foreach($cximglist as $key=>$value){			
			$checkpaytype = 0;	
            $tiaojian = '';			
			if(!empty($value['spotordtype']) && $value['spotordtype'] !='1,2,3' ){
				$tiaojian = '仅限';
				if($value['spotordtype'] == '1') $tiaojian .='外卖频道';
				if($value['spotordtype'] == '2') $tiaojian .='超市频道';
				if($value['spotordtype'] == '3') $tiaojian .='跑腿频道';
				if($value['spotordtype'] == '1,2') $tiaojian .='外卖频道、超市频道';
				if($value['spotordtype'] == '1,3') $tiaojian .='外卖频道、跑腿频道';
				if($value['spotordtype'] == '2,3') $tiaojian .='超市频道、跑腿频道';					
				$tiaojian .='使用';
			}else{
				$tiaojian .='';
			}
			if($value['paytype'] =='1'){
				$tiaojian .=empty($tiaojian)?'仅限在线支付使用。':'；仅限在线支付使用。';
			}elseif($value['paytype'] =='2'){
				$tiaojian .=empty($tiaojian)?'仅限货到支付使用。':'；仅限货到支付使用。';
			}else{
				$tiaojian .='。';
			}
			if((empty($value['spotordtype']) || $value['spotordtype'] =='1,2,3') && ($value['paytype'] == '' || $value['paytype'] == '1,2' )){
				$tiaojian = '';
			}
			$checktime = $value['endtime']-time();
			if($checktime < 86400 && $checktime > 0 ){
				$value['willbeexpired'] = 1;
			}else{
				$value['willbeexpired'] = 0;		
			}
			$value['tiaojian'] = $tiaojian;
			$value['creattime'] = date('Y-m-d',$value['creattime']);
			if($value['endtime'] < time()){
				$value['status'] = $value['status']==2?2:4;
			}
			$value['endtime'] = date('Y-m-d',$value['endtime']);
			$backdata[] = $value;
		}
		$disabled = array();
		$available = array();
		 
		foreach($backdata as $k=>$v){
		    if($v['status'] > 1){
				$disabled[] = $v;
			}
			if($v['status'] < 2){
				$available[] = $v;
		    }
		}
		 
		 
		$data['available'] = $available;//可用
		$data['disabled'] = $disabled;//不可用	
	    #print_r($data);exit;
		Mysite::$app->setdata($data);
	}
  function cart(){   
		$shopid = intval(IReq::get('shopid'));  
		$shopshowtype = ICookie::get('shopshowtype');
		$backdata = array(); 
		$smardb = new newsmcart();
		$carinfo = array();
		if($smardb->setdb($this->mysql)->SetShopId($shopid)->OneShop()){
		   $carinfo = $smardb->getdata(); 
		   
		   $backdata['list'] = $carinfo['goodslist'];
		   $backdata['sumcount'] =$carinfo['count'];
		   $backdata['sum'] =$this->formatcost($carinfo['sum'],2);
		   $backdata['goodscxdowncost'] =$carinfo['goodscxdowncost'];
			if($carinfo['shopinfo']['shoptype'] ==1){
			    $shopcheckinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");
			}else{
			     $shopcheckinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$shopid."'    ");  
			}
			   
		    if($shopshowtype == 'dingtai'){
			    $backdata['bagcost'] = 0;
		    }else{
			    $backdata['bagcost'] =$this->formatcost($carinfo['bagcost'],2);
		    }
		 
			$cxclass = new sellrule();  
			if( !strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')    ){    //判断是微信浏览器不
			    $platform=3;//触屏
			}else{
			    $platform=2;//微信
			}
            $paytype = intval(IReq::get('paytype'));		 
            $cxclass->setdata($shopid,$carinfo['sum'],$carinfo['shopinfo']['shoptype'],$this->member['uid'],$platform,$paytype,$backdata['bagcost']);
			$cxinfo = $cxclass->getdata();
			$backdata['cx_shoudan'] = $cxinfo['cx_shoudan'];
			$backdata['cx_manjian'] = $cxinfo['cx_manjian'];
			$backdata['cx_zhekou'] = $cxinfo['cx_zhekou'];
            $backdata['surecost'] = $cxinfo['surecost'];
        	$backdata['downcost'] = $cxinfo['downcost'];
            $backdata['cxdet'] = $cxinfo['cxdet'];
			if(!empty( $backdata['list'])){
				foreach($backdata['list']  as $v){
					if(!empty($v)){
						if($v['cxinfo']['is_cx'] == 1 && $v['cxinfo']['cxcost']>0){
							$backdata['cx_shoudan'] = 0;
							$backdata['cx_manjian'] = 0;
							$backdata['cx_zhekou'] = 0;
							$backdata['downcost'] = 0;
							$backdata['surecost'] = $carinfo['sum'];
							$backdata['cxdet'] = array();
							$cxinfo['nops'] = false;
							break;
						}
					}
				}
			}
             
			$backdata['gzdata'] = isset($cxinfo['gzdata'])?$cxinfo['gzdata']:array();
			$backdata['zpinfo'] = $cxinfo['zid'];
			$areaid = ICookie::get('myaddress'); 
			$shopinfo = array_merge($carinfo['shopinfo'],$shopcheckinfo);
			$tempinfo =   $this->pscost2($shopinfo);
			 
			$backdata['pstype'] = $tempinfo['pstype'];			 
			if($shopshowtype == 'dingtai'){
			    $backdata['pscost'] = 0;
			}else{
			    $backdata['pscost'] = $this->formatcost($tempinfo['pscost'],2);
			}
			$backdata['canps'] = $tempinfo['canps'];
            $backdata['nops'] = $cxinfo['nops'];
			$this->success($backdata);
		 }else{
			 $this->message(array());
		 } 
	
		  
	}

    function fabupaotui(){
		$this->checkwxweb();
		if($this->member['uid'] == 0)  $this->message('未登录');
		$adcode = intval(IFilter::act(IReq::get('adcode')));   
		if( empty($adcode) )  $this->message("获取所属城市失败");
		$ptinfoset = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset  where cityid = '".$adcode."' ");
        $demandcontent = trim(IFilter::act(IReq::get('demandcontent')));  // 需求内容
        $movegoodstype = trim(IFilter::act(IReq::get('movegoodstype')));
        $movegoodscost = trim(IFilter::act(IReq::get('movegoodscost')));
		 // 取货地址： 地址 补充地址  lng lat 
		$getaddress = trim(IReq::get('getdetaddress')); 		 
		$getlng = trim(IReq::get('getlng'));
		$getlat = trim(IReq::get('getlat'));
		 // 收货地址： 地址 补充地址  lng lat 
		$quname = trim(IReq::get('quname'));
		$shouname = trim(IReq::get('shouname'));
		$shouaddress = trim(IReq::get('shouetaddress'));		 
		$shoulng = trim(IReq::get('shoulng'));
		$shoulat = trim(IReq::get('shoulat'));
		$getphone = trim(IReq::get('getphone'));  // 取货电话
		$shouphone = trim(IReq::get('shouphone'));  // 收货电话
 		$minit = trim(IReq::get('minit'));			// 收/取 货时间
		$ptkg = trim(IReq::get('ptkg'));	// 货 公斤 数
		$ptkm = trim(IReq::get('ptkm'));	//  收取货 地址 两地 距离 km
 		$allkgcost = trim(IReq::get('allkgcost'));		// 重量价格
 		$allkmcost = trim(IReq::get('allkmcost'));		// 距离价格
 		$farecost = trim(IReq::get('farecost'));		// 加价（小费）
 		$allcost = trim(IReq::get('allcost'));		// 总价格
		$pttype = trim(IReq::get('pttype'));		// 	1为帮我送 2为帮我买
		$platpaytype =  $this->mysql->select_one("select paytype from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$adcode."' "); 
		$paytypestr = $platpaytype['paytype'];
		$paytypearr = explode(',',$paytypestr);
		if(!in_array(1,$paytypearr))$this->message("未开启在线支付，请联系管理员！");	
		$is_default = trim(IReq::get('is_default'));//是否是默认就近购买 1是 0不是
		$paytype = trim(IReq::get('paytype'));		//  支付方式，默认为在线支付
		if(empty($demandcontent) && $pttype==2 ) $this->message('请简要填写需求内容'); 	
	    //除了帮我买默认就近购买的情况下  都需要验证取货(购买)地址
		if($pttype==2 && $is_default == 1){ 
		    $getaddress = '就近购买';
		}else{
			if( empty($getaddress) || empty($getlng) || empty($getlat)  )  $this->message("请选择取货地址");
		}
 		if( empty($shouaddress) || empty($shoulng) || empty($shoulat)  )  $this->message("请选择收货地址");
		if($pttype==1){
			if( empty($getphone) )  $this->message("请填写取货电话");
			if(!IValidate::suremobi($getphone))   $this->message('请输入正确的手机号'); 	
		}
		
		if( empty($shouphone) )  $this->message("请填写收货电话");
		if(!IValidate::suremobi($shouphone))   $this->message('请输入正确的手机号'); 	
		if(empty($demandcontent) && $pttype==1 ) $this->message('请简要填写需求内容'); 	
		if( $minit == 0 ){
			$data['sendtime'] = time();
		    $data['postdate'] = '立即取货';
		}else{ 
			$tempdata = $this->getOpenPosttime($ptinfoset['is_ptorderbefore'],time(),$ptinfoset['postdate'],$minit,$ptinfoset['pt_orderday']); 
		    if($tempdata['is_opentime'] ==  2) $this->message('选择的配送时间段，店铺未设置');
		    if($tempdata['is_opentime'] == 3) $this->message('选择的配送时间段已超时');
		    $data['sendtime'] = $tempdata['is_posttime'];
		    $data['postdate'] = $tempdata['is_postdate'];
		    $info['addpscost'] =  $tempdata['cost'];
		}
 		$data['pttype'] = $pttype;  // 1为帮我送  2为帮我买	
		$data['admin_id'] = $adcode;
		$data['content'] = $demandcontent;
		$data['shopaddress']  = $getaddress;   
		$data['buyeraddress']  = $shouaddress;  
		if($pttype==1){
			$data['shopphone']  = $getphone;			//取件电话
			$data['shopname']  = $quname;
		}
		$data['buyerphone']  = $shouphone;			//收件电话
		$data['addtime'] = time();
		if($this->checkbackinfo()){
		    $data['ordertype'] = 3;//订单类型 
		}else{
		    $data['ordertype'] = 5;
		}
		$data['shoptype'] = 100;//订单类型		 
		$data['paytype'] = $paytype; 
		$data['paystatus'] = 0; 
		$data['movegoodstype'] = $movegoodstype;
		$data['movegoodscost'] = $movegoodscost;
		$data['ptkg'] = $ptkg;
		$data['ptkm'] = $ptkm;
		$data['allkgcost'] = $allkgcost;
		$data['allkmcost'] = $allkmcost;
		$data['farecost'] = $farecost;
		$data['allcost'] = $allcost;
		/*检测订单金额是否小于后台最低金额*/
		if($data['pttype'] == 1){
            $checkcost = $ptinfoset['kmcost'] + $ptinfoset['kgcost'];
		}else{
			$checkcost = $ptinfoset['kmcost'];	
		}
        if($data['allcost'] < $checkcost )$this->message('总金额计算错误，请刷新页面重新下单'); 	
		$data['dno'] = time().rand(1000,9999);
		$data['pstype']  = 2;
		$data['buyeruid']  = $this->member['uid'];
		$data['buyername'] = $shouname;
		$data['shoplat'] = $shoulat;//店铺lat坐标
		$data['shoplng'] = $shoulng;//店铺lng坐标 pstype
		$data['buyerlat'] = $getlat;//用户lat坐标
		$data['buyerlng'] = $getlng;//用户lng坐标
		//$data['is_make'] = 1; 
		/* 计算两点之间的距离  并且 判断是否与前台的  千米距离金额是否一致 */
		$juli = $this->GetDistance($getlat,$getlng, $shoulat,$shoulng, 1,1); 
		$tempmi = $juli;
		$juli = round($juli/1000,1);		
		$tmpallkmcost =  0;
		if( $juli <= $ptinfoset['km']  ){
			$tmpallkmcost = $ptinfoset['kmcost'];
		}else{
			$addjuli = $juli-$ptinfoset['km'];
			$addnum = floor( ($addjuli/$ptinfoset['addkm']));
			$addcost = $addnum*$ptinfoset['addkmcost'];
			$tmpallkmcost = $ptinfoset['kmcost']+$addcost;
		}
		if( $ptkg <= $ptinfoset['kg']  ){
			$tmpallkgcost = $ptinfoset['kgcost'];
		}else{
			$addkg = $ptkg-$ptinfoset['kg'];
			$addkgnum = floor( ($addkg/$ptinfoset['addkg']));
			$addkgcost = $addkgnum*$ptinfoset['addkgcost'];
			$tmpallkgcost = $ptinfoset['kgcost']+$addkgcost; 
		}
		if($pttype == 2){
			$tmpallkgcost =  0;
		}			
 		if($tmpallkgcost != $allkgcost )$this->message("获取重量总金额错误");
		$panduan = Mysite::$app->config['man_ispass'];
		$data['status'] = 0;
		if($panduan != 1 && $data['paytype'] == 0){
			$data['status'] = 1;
		} 	
		$data['ipaddress'] = "";
		$ip_l=new iplocation(); 
		$ipaddress=$ip_l->getaddress($ip_l->getIP());  
		if(isset($ipaddress["area1"])){
			if(function_exists(mb_convert_encoding)){
				$data['ipaddress']  = $ipaddress['ip'].mb_convert_encoding($ipaddress["area1"],'UTF-8','GB2312');//('GB2312','ansi',);
			}else if(function_exists(iconv)){
				$data['ipaddress']  = $ipaddress['ip'].iconv('GB2312',$ipaddress["area1"],'UTF-8');//('GB2312','ansi',);
			}else{
				$data['ipaddress']='0';
			}
		}
		 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'order',$data);
		$orderid = $this->mysql->insertid(); 
		$orderClass = new orderClass();
		$orderClass->writewuliustatus($orderid,1,$data['paytype']);	
		if($orderClass->sendWxMsg($orderid,1,3)){	
		}
		$this->success($orderid);
	}
	
	function paotui() {
        $helpbuyinfo = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "helpbuy where isnotsee = 0 order by orderid asc");
        $helpmoveinfo = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "helpmove where isnotsee =  0 order by orderid asc ");
        $data['helpbuyinfo'] = $helpbuyinfo;
        $data['helpmoveinfo'] = $helpmoveinfo;
        Mysite::$app->setdata($data);
	 }
	 
	 
	function paotuiorder(){
		$link = IUrl::creatUrl('wxsite/index'); 
	    if($this->member['uid'] == 0)  $this->message('',$link); 
	    $pageinfo = new page();
	    $pageinfo->setpage(intval(IReq::get('page')),20);  
		$datalist = $this->mysql->getarr("select id,shopname,pttype,paystatus,psstatus,status,is_goshop,allcost,content,addtime,status,is_ping,shoptype,is_reback,buyeruid from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and shoptype = 100 and is_userhide != 1 order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
	    $temparray = array('0'=>'外卖','1'=>'超市','2'=>'其他');
	    $backdata = array();
	    $statusarr = array('0'=>'待抢单','1'=>'待取货','3'=>'待送达','4'=>'已送达'); 
	    $goshoparr = array('0'=>'待抢单','1'=>'待购买','3'=>'待送达','4'=>'已送达');
	    foreach($datalist as $key=>$value){  
			$listdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$value['id']."'");  
			$value['det'] = '';
			$value['psstatus'] = empty($value['psstatus'])?0:$value['psstatus'];
			foreach($listdet as $k=>$v){
				$value['det'] .= $v['goodsname'].',';
			}
			if($value['pttype'] == 1){
				$value['pttype']="帮我送";
				$value['seestatus'] = isset($statusarr[$value['psstatus']])?$statusarr[$value['psstatus']]:'';
			}elseif($value['pttype'] == 2){
				$value['pttype']="帮我买";
				$value['seestatus'] = isset($goshoparr[$value['psstatus']])?$goshoparr[$value['psstatus']]:'';
			}
			if($value['paystatus'] == 0){
			$value['seestatus'] = "待付款";
			}
			if($value['is_reback'] == 1 || $value['is_reback'] == 4){
				$value['seestatus'] = "退款中";
			}
			if($value['status'] > 3){
				$value['seestatus'] = "关闭";
			}  
			$value['addtime'] = date('Y-m-d',$value['addtime']);
			$backdata[] =$value;
		}
		$this->success($backdata);
	}
    function paotuidetail(){//跑腿详情
        $link = IUrl::creatUrl('wxsite/index');
        if ($this->member['uid'] == 0) $this->message('未登录', $link);
        $orderid = intval(IReq::get('orderid'));
        $wxclass = new wx_s();
        $signPackage = $wxclass->getSignPackage();
        $data['signPackage'] = $signPackage;
       $juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 4 or name = '下单送优惠券' " ); 
       $juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 4 or name= '下单送优惠券' order by id asc " ); 
       $data['juaninfo'] =$juaninfo;
       $data['sendjuanstatus'] = $juansetinfo['status'];
//       print_R($juansetinfo['status']);exit;
       
        if( !strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){    //判断是微信浏览器不
                $data['WeChatType'] = 0;
        }else{
                $data['WeChatType'] = 1;//微信端
        }
//                        print_R($data);exit;
        $shareinfo = $this->mysql->select_one("select title,img,`describe`  from " . Mysite::$app->config['tablepre'] . "juanshowinfo where id=1 order by orderid asc  ");

        if (empty($shareinfo)) {
            $shareinfo['title'] = Mysite::$app->config['sitename'];
            $shareinfo['img'] = Mysite::$app->config['sitelogo'];
            $shareinfo['describe'] = Mysite::$app->config['sitename'];
        }
		$data['drawbacklog'] = $this->mysql->select_one("select id  from " . Mysite::$app->config['tablepre'] . "drawbacklog where orderid = ".$orderid."   ");
        $data['shareinfo'] = $shareinfo;
		#print_r($data);exit;
        $where = "  where type=2 and addtime < " . time() . "  and is_open = 1 and juannum > 0 ";
        $checkinfosendjuan = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "juanrule " . $where . " order by orderid asc ");
        $data['checkinfosendjuan'] = $checkinfosendjuan;
        if (!empty($orderid)) {
            $order = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "order where buyeruid='" . $this->member['uid'] . "' and id = " . $orderid . " ");//" . $orderid . "
            if($order['paytype'] == 1 && $order['paystatus'] == 0 && $order['status'] < 3){
                $checktime = time() - $order['addtime'];
                if($checktime > 900){
                    //说明该订单可以关闭
                    $cdata['status'] = 4;
                    $this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
                    $this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$orderid' and status != 3");
                    /*更新订单 状态说明*/
                    $statusdata['orderid']     =  $orderid;
                    $statusdata['addtime']     =  $order['addtime']+900;
                    $statusdata['statustitle'] =  "自动关闭订单";
                    $statusdata['ststusdesc']  =  "在线支付订单，未支付自动关闭";
                    $this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata);
                    $order['status'] = 4;
                }
            }
			$orderwuliustatus = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "orderstatus where   orderid = " . $orderid . " order by addtime desc limit 0,10 ");
			$data['orderwuliustatus'] = $orderwuliustatus;
            $data['paytype'] = $order['paytype'];
            if (empty($order)) {
                $data['order'] = '';
                Mysite::$app->setdata($data);
            } else {
                $scoretocost = Mysite::$app->config['scoretocost'];
                $order['scoredown'] = $order['scoredown'] / $scoretocost;//抵扣积分
                $order['ps'] = $order['shopps'];
                // 超市商品总价	 超市配送配送	shopcost 店铺商品总价	shopps 店铺配送费	pstype 配送方式 0：平台1：个人	bagcost
                $orderdet = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "orderdet where order_id='" . $order['id'] . "'");
                $order['cp'] = count($orderdet);
                $buyerstatus = array(
                    '0' => '等待处理',
                    '1' => '订餐成功处理中',
                    '2' => '订单已发货',
                    '3' => '订单完成',
                    '4' => '订单已取消',
                    '5' => '订单已取消'
                );
                $paytypelist = array(0 => '货到支付', 1 => '在线支付');

                $paytypearr = $paytypelist;
                $order['is_acceptorder'] = $order['is_acceptorder'];
                $order['surestatus'] = $order['status'];
                $order['basetype'] = $order['paytype'];
                $order['basepaystatus'] = $order['paystatus'];
                #  $order['status'] = $buyerstatus[$order['status']];
                $order['paytype'] = $order['paytype'];
                #   $order['paystatus'] = $order['paystatus']==1?'已支付':'未支付';
                $order['paystatus'] = $order['paystatus'];
                $order['addtime'] = date('Y-m-d H:i:s', $order['addtime']);
                $order['posttime'] = date('Y-m-d H:i:s', $order['posttime']);


                $data['order'] = $order;
               
                $data['orderdet'] = $orderdet;

                $data['psbpsyinfo'] = array();

                if ($order['psuid'] > 0 && $order['shoptype'] == 100) {
                    if ($order['psstatus'] > 0) {
                        if ($order['pstype'] == 2) {
                            $psbinterface = new psbinterface();
                            $data['psbpsyinfo'] = $psbinterface->getpsbclerkinfo($order['psuid']);

                            if (!empty($data['psbpsyinfo']) && !empty($data['psbpsyinfo']['posilnglat'])) {
                                $posilnglatarr = explode(',', $data['psbpsyinfo']['posilnglat']);
                                $posilng = $posilnglatarr[0];
                                $posilat = $posilnglatarr[1];
                                if (!empty($posilng) && !empty($posilat)) {
                                    $data['psbpsyinfo']['posilnglatarr'] = $posilnglatarr;
                                } else {
                                    $data['psbpsyinfo'] = array();
                                }

                            }
                        } else if ($order['pstype'] == 0) {
                            $data['psbpsyinfo'] = $this->mysql->select_one("select uid,lng,lat from " . Mysite::$app->config['tablepre'] . "locationpsy where uid='" . $order['psuid'] . "' ");
                            if (!empty($data['psbpsyinfo']) && !empty($data['psbpsyinfo']['lng']) && !empty($data['psbpsyinfo']['lat'])) {
                                $data['psbpsyinfo']['posilnglat'] = $data['psbpsyinfo']['lng'] . ',' . $data['psbpsyinfo']['lat'];
                            } else {
                                $data['psbpsyinfo'] = array();
                            }
                        } else {
                            $data['psbpsyinfo'] = array();
                        }
                    } else if ($order['status'] == 3 && ($order['pstype'] == 0 || $order['pstype'] == 2)) {

                        $psyoverlng = $order['psyoverlng'];
                        $psyoverlat = $order['psyoverlat'];
                        $data['psbpsyinfo']['clerkid'] = $order['psuid'];
                        $data['psbpsyinfo']['posilnglat'] = $psyoverlng . ',' . $psyoverlat;
                        $data['psbpsyinfo']['posilnglatarr'] = explode(',', $data['psbpsyinfo']['posilnglat']);

                    }
                }
				#print_R($data);
                Mysite::$app->setdata($data);

            }
        } else {
            $data['order'] = '';
            Mysite::$app->setdata($data);
        }
    }
	function mypaotui(){
   		$link = IUrl::creatUrl('wxsite/index'); 
	    if($this->member['uid'] == 0)  $this->message('',$link); 
   }
	
    /* 发布跑腿 end   */
	
	
	

    function makeorder(){
   		 
		$this->checkwxweb();
		if( $this->checkbackinfo() ){
			if($this->member['uid'] == 0)  $this->message('未登录'); 
	    }  
		$addressinfo['contactname'] = IFilter::act(IReq::get('wmbuyername')); 
		$addressinfo['phone'] = IFilter::act(IReq::get('wmbuyerphone')); 
		$addressinfo['address'] = IFilter::act(IReq::get('wmbuyeraddress'));  
		if(empty($addressinfo['contactname']))$this->message('收货人姓名不能为空');
		if(empty($addressinfo['phone']))$this->message('收货人电话不能为空');
		if(empty($addressinfo['address']))$this->message('收货人地址不能为空');
		$info['shopid'] = intval(IReq::get('shopid'));//店铺ID
		if(empty($info['shopid'])) $this->message('店铺ID错误');
		$is_ziti = IFilter::act(IReq::get('is_ziti')); 
		$zttime = IFilter::act(IReq::get('zttime')); 
		$ztphone = IFilter::act(IReq::get('ztphone')); 
		$shopzttime = $this->mysql->select_one("select ziti_time from ".Mysite::$app->config['tablepre']."shop  where id = ".$info['shopid']."  "); 		 
		if($is_ziti == 1){
			if(empty($zttime)) $this->message('请选择自取时间');
			if(time() > strtotime($zttime) )   $this->message('自取时间超时，请重新选择自取时间');
			
		}
		$info['is_ziti'] = $is_ziti; 
		$info['buyerlng'] = IFilter::act(IReq::get('buyerlng')); 
		$info['buyerlng'] = IFilter::act(IReq::get('buyerlng')); 
		//if(empty($addressinfo) && $is_ziti != 1) $this->message('未设置默认地址');		   
		$info['buyerlng'] = IFilter::act(IReq::get('buyerlng')); 
		$info['buyerlat'] = IFilter::act(IReq::get('buyerlat')); 
		$nowID = ICookie::get('CITY_ID');
		if(!empty($nowID)){
		    $nowID = explode('_', $nowID);
		    $nowID = end($nowID);
			$a = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."stationadmininfo where cityid =".$nowID."");
	        if($a['stationis_open'] == 1) $this->message('分站已关闭');
        } 	 
		$info['username'] = $is_ziti == 1?$this->member['username']:$addressinfo['contactname']; 
		$info['mobile'] = $is_ziti == 1?$ztphone:$addressinfo['phone'];
		
		$info['addressdet'] = $addressinfo['address'];	 
		
		$info['remark'] = IFilter::act(IReq::get('remark'));//备注
		$info['paytype'] =  IFilter::act(IReq::get('paytype'));//支付方式
		$info['dikou'] =  intval(IReq::get('dikou'));//抵扣金额
		$info['minit'] = IFilter::act(IReq::get('minit')); 
		$info['juanid']  =  intval(IReq::get('juanid'));//优惠劵ID
		if($this->checkbackinfo()){
			$info['ordertype'] = 3;//订单类型 
		}else{
			$info['ordertype'] = 5;
		}
		$peopleNum = IFilter::act(IReq::get('peopleNum'));  
		$info['othercontent'] ='';//empty($peopleNum)?'':serialize(array('人数'=>$peopleNum)); 		  
		  
		$smardb = new newsmcart();
		$carinfo = array();
		if($smardb->setdb($this->mysql)->SetShopId($info['shopid'])->OneShop()){
		    $carinfo = $smardb->getdata(); 
		}else{ 
		    $this->message($smardb->getError());
		}
		 
		if(count($carinfo['goodslist'])==0) $this->message('对应店铺购物车商品为空');
		 
		if($carinfo['shopinfo']['shoptype'] == 1){
		 	$shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");   
		}else{
	        $shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");   
	    }

		if(empty($shopinfo))   $this->message('店铺获取失败');
		$areaid = ICookie::get('myaddress');
	    $temp = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where userid = ".$this->member['uid']." and `default`=1   ");
	    $checkps = 	 $this->pscost2($shopinfo,$temp['lng'],$temp['lat']);
		if($checkps['canps'] != 1 && $is_ziti != 1) $this->message('该店铺不在配送范围内');
		$info['cattype'] = 0;//
		if(empty($info['username']))$this->message('联系人不能为空'); 
	  	if(!IValidate::suremobi($info['mobile']))   $this->message('请输入正确的手机号'); 
		if(empty($info['addressdet']) && $is_ziti != 1) $this->message('详细地址为空');
	    $info['userid'] = !isset($this->member['score'])?'0':$this->member['uid'];
	    if(Mysite::$app->config['allowedguestbuy'] != 1){
	        if($info['userid']==0) $this->message('禁止游客下单');
	    }
	    $info['ipaddress'] = "";
	    $ip_l=new iplocation(); 
        $ipaddress=$ip_l->getaddress($ip_l->getIP());  
        if(isset($ipaddress["area1"])){
			if(function_exists(mb_convert_encoding)){
			    $info['ipaddress']  = $ipaddress['ip'];//('GB2312','ansi',);
			}else if(function_exists(iconv)){
			    $info['ipaddress']  = $ipaddress['ip'].iconv('GB2312',$ipaddress["area1"],'UTF-8');//('GB2312','ansi',);
			}else{
			    $info['ipaddress']='0';
			}
	    }	   
	    $info['areaids'] = '';
	    if($shopinfo['is_open'] != 1) $this->message('店铺暂停营业'); 
	    $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$info['minit'],$shopinfo['befortime']); 
	    if($tempdata['is_opentime'] ==  2 && $is_ziti != 1) $this->message('该商家当前时间不支持配送');
	    if($tempdata['is_opentime'] == 3 && $is_ziti != 1) $this->message('选择的配送时间段已超时');
	    $info['sendtime'] = $tempdata['is_posttime'];
	    $info['postdate'] = $is_ziti == 1?$zttime:$tempdata['is_postdate'];
//附加配送费启用   直接加在配送费上
		#$info['addpscost'] = $is_ziti == 1?0:$tempdata['cost'];
		$info['is_hand'] =  IFilter::act(IReq::get('is_hand'));
		 
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
        $paytype = $info['paytype'] == 1?1:0;	 
	    $info['shopinfo'] = $shopinfo;
	    $info['allcost'] = $this->formatcost($carinfo['sum'],2);
	    $info['bagcost'] = $this->formatcost($carinfo['bagcost'],2);
	    $info['allcount'] = $carinfo['count'];
		$surepscost = $checkps['pscost']+$tempdata['cost'];//基础配送费+附加配送费
	    $info['shopps'] = $is_ziti == 1?0:$surepscost; 
	    $info['goodslist']   = $carinfo['goodslist'];	  
	    $info['pstype'] = $checkps['pstype'];
	    $info['cattype'] = 0;//表示不是预订 
	    $info['cx_manjian'] =  $this->formatcost(IReq::get('cx_manjian'),2);
	    $info['cx_zhekou'] =  $this->formatcost(IReq::get('cx_zhekou'),2);
	    $info['cx_shoudan'] =  $this->formatcost(IReq::get('cx_shoudan'),2);
	    $info['cx_nopsf'] = $is_ziti == 1?0:IReq::get('cx_nopsf'); 	   
		foreach($info['goodslist'] as $key=>$value){  
	        if($value['stock'] < $value['count']){
				$this->message($value['name'].'商品库存不足');
			}
	    }
		if( !strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')    ){    //判断是微信浏览器不
			$info['platform']=3;//触屏
		}else{
			$info['platform']=2;//微信
		}
	    $info['is_goshop']=0;
	    if($shopinfo['limitcost'] > $info['allcost']) $this->message('商品总价低于最小起送价'.$shopinfo['limitcost']);   
         
	    $orderclass = new orderclass();
	    $orderclass->makenormal($info);
	    $orderid = $orderclass->getorder();
	    if($info['userid'] ==  0){ 
	  	    ICookie::set('orderid',$orderid,86400);
	    }
	    if( $paytype == 0 ){
		    $smardb->DelShop($info['shopid']);
	    }
		$this->success($orderid);  
		exit; 
	}
	 

	function makeorder2(){
		$this->checkwxweb();
		if( $this->checkbackinfo() ){
			if($this->member['uid'] == 0)  $this->message('未登录'); 
	    } 
		
		
			if(  empty($this->member['uid']) || $this->member['uid'] ==  0){	 
				$addressinfo  = null;
				$cdata['id'] = 0;
				$cdata['default'] = 1;
				$cdata['contactname'] = ICookie::get('wxguest_username');
				$cdata['phone'] = ICookie::get('wxguest_phone');
				$cdata['address']  = ICookie::get('wxguest_address');
				if(empty($cdata['contactname'])){
					
				}else{
					$addressinfo = $cdata;
				}
			}else{
		  $addressinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."address  where userid = ".$this->member['uid']." and `default`=1   "); 
   	
			} 
		 
		  if(empty($addressinfo)) $this->message('未设置默认地址');
		  	 $info['username'] = $addressinfo['contactname']; 
		 $info['mobile'] = $addressinfo['phone'];
		 $info['addressdet'] = $addressinfo['address'];
		 $subtype = intval(IReq::get('subtype'));
	   $info['shopid'] = intval(IReq::get('shopid'));//店铺ID
		 $info['remark'] = IFilter::act(IReq::get('content'));//备注
		 $info['paytype'] = IFilter::act(IReq::get('paytype'));//'outpay';//支付方式 
		if( $info['paytype'] == '' ) $this->message('未开启任何支付方式，请联系管理员！');
	// $info['senddate'] =  IFilter::act(IReq::get('senddate'));
		 $info['minit'] = IFilter::act(IReq::get('minit')); 
		 $info['juanid']  = intval(IReq::get('juanid'));//优惠劵ID
		 if($this->checkbackinfo()){
			$info['ordertype'] = 3;//订单类型 
		 }else{
			 $info['ordertype'] = 5;
		 }
		 $peopleNum = IFilter::act(IReq::get('personcount'));  
		 if($peopleNum < 1) $this->message('选择消费人数');
		 $info['othercontent'] = empty($peopleNum)?'':serialize(array('人数'=>$peopleNum));  
		 $info['userid'] = !isset($this->member['score'])?'0':$this->member['uid'];
	   if(Mysite::$app->config['allowedguestbuy'] != 1){
	     if($info['userid']==0) $this->message('member_nologin');
	   } 
		 $shopinfo=   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.shopid = '".$info['shopid']."'    ");   
		 if(empty($shopinfo)) $this->message('店铺不存在');
		 /*监测验证码*/
		 
    if(empty($info['username'])) 		  $this->message('emptycontact'); 
	  if(!IValidate::suremobi($info['mobile']))   $this->message('errphone'); 
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
     $info['cattype'] = 0;//
     
	  
	  	   if($shopinfo['is_open'] != 1) $this->message('店铺暂停营业'); 
	   $tempdata = $this->getOpenPosttime($shopinfo['is_orderbefore'],$shopinfo['starttime'],$shopinfo['postdate'],$info['minit'],$shopinfo['befortime']); 
	   if($tempdata['is_opentime'] ==  2) $this->message('选择的配送时间段，店铺未设置');
	   if($tempdata['is_opentime'] == 3) $this->message('选择的配送时间段已超时');
	   $info['sendtime'] = $tempdata['is_posttime'];
	   $info['postdate'] = $tempdata['is_postdate'];
	   $info['addpscost'] = $tempdata['cost'];
			
		if($info['paytype'] == 'undefined') 	$this->message("未开启任何支付方式，请联系管理员！");	  
		
			
	   $info['paytype'] = $info['paytype'] == 1?1:0;
	   
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
		 
		 if(count($carinfo['goodslist'])==0) $this->message('对应店铺购物车商品为空');
		   
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
	      $smardb->delshop($info['shopid']);
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
   function subshow(){
	    
	    $orderid = intval(IReq::get('orderid'));  
		$userid = empty($this->member['uid'])?0:$this->member['uid']; 
		$orderid = intval(IReq::get('orderid')); 
		if(empty($orderid)) $this->message('订单获取失败');
		if($userid == 0){ 
			$neworderid = ICookie::get('orderid'); 
			if($orderid != $neworderid) $this->message('订单无查看权限');
		} 
		if($orderid < 1){ 
		    $this->message('订单获取失败');
		}
		$order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");   
	 
	    $platpaytype =  $this->mysql->select_one("select paytype from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$order['admin_id']."' "); 
		$paytypestr = $platpaytype['paytype'];
		$paytypearr = explode(',',$paytypestr);		
		if(!in_array(1,$paytypearr)) $this->message("网站未开启在线支付，请联系管理员！");
	 
		if($order['paytype'] == 1 && $order['paystatus'] == 0 && $order['status'] < 3){
			$checktime = time() - $order['addtime'];
			if($checktime > 900){
				//说明该订单可以关闭
				if($order['yhjids']>0){
					$jdata['status'] =1;
					$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$jdata,"id='".$order['yhjids']."'");
				}
				$cdata['status'] = 4;
				$this->mysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
				$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps',"orderid = '$orderid' and status != 3");
				/*更新订单 状态说明*/
				$statusdata['orderid']     =  $orderid;
				$statusdata['addtime']     =  $order['addtime']+900;
				$statusdata['statustitle'] =  "自动关闭订单";
				$statusdata['ststusdesc']  =  "在线支付订单，未支付自动关闭"; 		
				$this->mysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata); 
				$order['status'] = 4;
				
			} 
		}
	 
	 
	 
	 
	 
		$order['ps'] = $order['shopps'];
		// 超市商品总价	 超市配送配送	shopcost 店铺商品总价	shopps 店铺配送费	pstype 配送方式 0：平台1：个人	bagc 
	  if(empty($order)){ 
	  	 $this->message('订单获取失败');
	  } 
  	$orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$order['id']."'");  
	  $order['cp'] = count($orderdet); 
	  $buyerstatus= array(
		'0'=>'等待处理',
		'1'=>'订餐成功处理中',
		'2'=>'订单已发货',
		'3'=>'订单完成',
		'4'=>'订单已取消',
		'5'=>'订单已取消'
		);
		 
		$paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist where type = 0 or type=2  order by id asc limit 0,50");
		if(is_array($paylist)){
		  foreach($paylist as $key=>$value){
			    $paytypelist[$value['loginname']] = $value['logindesc'];
		  }
	  }
	  $data['paylist'] = $paylist;
	  
	  $data['order'] = $order;
	   
	  
	  	
	if( $this->checkbackinfo() ){
 
	 if($order['paystatus'] == 0 && $order['paytype'] == 1&& isset($paytypelist['weixin'])){ 
		   $wxopenid = ICookie::get('wxopenid');  
		   
		   $weixindir = hopedir.'/plug/pay/weixin/'; 
		    require_once $weixindir."lib/WxPay.Config.php"; 
		   require_once $weixindir."lib/WxPay.Api.php";
		   require_once $weixindir."WxPay.JsApiPay.php";  
		  
	 
		   $tools = new JsApiPay(); 
		    
		if(empty($wxopenid)){
			$openId = $tools->GetOpenid();
			$wxopenid = $openId;
		}
 
    $data['showwxpay'] = empty($wxopenid)?0:1;
	# print_r("openId:".$wxopenid);
	 
	//②、统一下单
	$input = new WxPayUnifiedOrder();
	$input->SetBody("支付订单".$order['dno']);
	$input->SetAttach($order['dno']);
	$input->SetOut_trade_no($order['id']);
	$input->SetTotal_fee($order['allcost']*100);
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetTimeStamp(time());
	$input->SetGoods_tag('订餐');
	$input->SetNotify_url(Mysite::$app->config['siteurl']."/plug/pay/weixin/notify.php");
	$input->SetTrade_type("JSAPI");
	$input->SetOpenid($wxopenid);
	 //$url = Mysite::$app->config['siteurl'].'/plug/pay/weixin/jsapi.php';
	 
			try{
				$ordermm = WxPayApi::unifiedOrder($input); 
				if($ordermm['return_code'] == 'SUCCESS'){
					$jsApiParameters = $tools->GetJsApiParameters($ordermm);  
					 
						$data['wxdata'] = $jsApiParameters; 
				}else{
					 $data['wxerror']  = $ordermm['return_msg'];
				} 
			 		 
			}catch (Exception $e) {  
			    $data['wxerror']  = $e->getmessage();
			}
			 
		   }
	}

	  Mysite::$app->setdata($data); 
	   if($this->checkbackinfo()){
	 
		Mysite::$app->setAction('subshow');
	  }else{
		 Mysite::$app->setAction('mobilesubshow'); 
	  }
	  
	}
	function shop(){
			$link = IUrl::creatUrl('wxsite/index'); 
	    if($this->member['uid'] == 0)  $this->message('未登录',$link);  
	    	$nowdata = date('Y-m-d',time());
	  $mintime = strtotime($nowdata);
	  $maxtime = strtotime($nowdata.' 23:59:59');
	  $where = ' and  posttime > '.$mintime.' and posttime < '.$maxtime;//发货时间
	  
	   $tjlist = $this->mysql->getarr("select count(id) as shuliang,status from ".Mysite::$app->config['tablepre']."order where shopuid=".$this->member['uid']." ".$where."  group by status order by id asc limit 0,50");
	  $data['tj'] = array();
	  foreach($tjlist as $key=>$value){
	    $data['tj'][$value['status']] = $value['shuliang'];
	  }
	   Mysite::$app->setdata($data);
	}
	function shopordert(){
	   //shopuid
	    
	}
	function shopordertoday(){
		$nowdata = date('Y-m-d',time());
	  $mintime = strtotime($nowdata);
	  $maxtime = strtotime($nowdata.' 23:59:59');
	  $where = '  posttime > '.$mintime.' and posttime < '.$maxtime;//发货时间
	  $status  = intval(IFilter::act(IReq::get('status')));
	  $status  =  in_array($status,array(1,2,3))? $status:1; 
	  $where .=' and status ='.$status;
	  $where .=' and shopuid ='.$this->member['uid']; 
	  $buyerstatus= array(
		'0'=>'等待处理',
		'1'=>'等待发货',
		'2'=>'已发货，待完成',
		'3'=>'订单完成',
		'4'=>'订单已取消',
		'5'=>'订单已取消'
		);
		$data['buyerstatus'] = $buyerstatus;
		$data['where'] = $where;
		$arraystatus = array(
		'1'=>'今日待发货订单',
		'2'=>'今日已发货订单',
		'3'=>'今日完成订单'
		);
		$data['orderbt'] = $arraystatus[$status]; 
	  Mysite::$app->setdata($data); 
	}
	function shopshoworder(){
		
		$this->checkwxweb();
		$link = IUrl::creatUrl('wxsite/index'); 
	 if($this->member['uid'] == 0)  $this->message('未登录',$link); 
	  $orderid = intval(IReq::get('id'));  
	  if(!empty($orderid)){
	  	 
	     	$order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where shopuid='".$this->member['uid']."' and id = ".$orderid."");   
	     
	     	if(empty($order)){
	     		$data['order'] = '';
	     		Mysite::$app->setdata($data);
	     	}else{
	     	     $order['ps'] = $order['shopps'];
	     	     // 超市商品总价	 超市配送配送	shopcost 店铺商品总价	shopps 店铺配送费	pstype 配送方式 0：平台1：个人	bagcost  
       	     $orderdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$order['id']."'");  
	            $order['cp'] = count($orderdet); 
	            $buyerstatus= array(
	     	     '0'=>'等待处理',
		'1'=>'等待发货',
		'2'=>'已发货，待完成',
		'3'=>'订单完成',
		'4'=>'订单已取消',
		'5'=>'订单已取消'
	     	     );
	     	     $paytypelist = array(0=>'货到支付',1=>'在线支付','weixin'=>'微信支付');  
	     	     $paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist   order by id asc limit 0,50");
	     	     if(is_array($paylist)){
	     	       foreach($paylist as $key=>$value){
	     	     	    $paytypelist[$value['loginname']] = $value['logindesc'];
	     	       }
	            }
	     	     $paytypearr = $paytypelist; 
	     	      $order['surestatus'] = $order['status'];
	     	     $order['status'] = $buyerstatus[$order['status']];
	     	     $order['paytype'] = $paytypearr[$order['paytype']];
	     	     $order['paystatus'] = $order['paystatus']==1?'已支付':'未支付';
	     	     $order['addtime'] = date('Y-m-d H:i:s',$order['addtime']);
	     	     $order['posttime'] = date('Y-m-d H:i:s',$order['posttime']);
	     	  
	     	     
	     	     $data['order'] = $order;
	           $data['orderdet'] = $orderdet;
	          
	           Mysite::$app->setdata($data);
	           
	       }
	  }else{
	  	$data['order'] = '';
	  	Mysite::$app->setdata($data);
	  }
	}
	function shopcontrol(){
		$this->checkmemberlogin();
		$controlname =trim(IFilter::act(IReq::get('controlname')));
		$orderid = intval(IReq::get('orderid')); 
		$ordertempinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id = ".$orderid."");
		if($ordertempinfo['shopuid'] != $this->member['uid']) $this->message('您不能操作此订单');
		$shopid = $ordertempinfo['shopid']; 
		$shopinfo = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."shop where id = ".$shopid."");
		switch($controlname)
		{
			case 'unorder': 
			 
	     $reason = trim(IFilter::act(IReq::get('reason')));
	     if(empty($reason)) $this->message('关闭理由不能为空');
	   	 $ordercontrol = new ordercontrol($orderid);
	   	 if($ordercontrol->sellerunorder($shopinfo['uid'],$reason))
	   	 {
				 $this->success('操作成功');
	     }else{
				  $this->message($ordercontrol->Error());
		   }  
			break;
			case 'sendorder': 
		  $ordercontrol = new ordercontrol($orderid);
		  if($ordercontrol->sendorder($shopinfo['uid']))
		  {
				$this->success('操作成功');
		  }else{
				 $this->message($ordercontrol->Error());
		  } 
			break;
			case 'shenhe': 
		  $ordercontrol = new ordercontrol($orderid);
		  if($ordercontrol->shenhe($shopinfo['uid']))
		  {
					$this->success('操作成功');
		  }else{
				 $this->message($ordercontrol->Error());
		  }
			break;
			case 'delorder':
			$ordercontrol = new ordercontrol($orderid);
		  if($ordercontrol->sellerdelorder($shopinfo['uid']))
		  {
				$this->success('操作成功');
		  }else{
			   $this->message($ordercontrol->Error());
		  } 
			break;
			case 'domake':
			if($ordertempinfo['status'] != 1){
			  $this->message('订单状态不可操作是否制作');
			} 		
			if(!empty($ordertempinfo['is_make'])){
				 $this->message('订单已设置过是否制作，如要取消 请联系网站客服');
			}
			$newdata['is_make'] = 1;
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$newdata,"id='".$orderid."'");
			$this->success('操作成功');
			break;
			case 'unmake':
			if($ordertempinfo['status'] != 1){
			  $this->message('订单状态不可操作是否制作');
			} 		
			if(!empty($ordertempinfo['is_make'])){
				 $this->message('订单已设置过是否制作，如要取消 请联系网站客服');
			}
			$newdata['is_make'] = 2;
			$this->mysql->update(Mysite::$app->config['tablepre'].'order',$newdata,"id='".$orderid."'");
			$this->success('操作成功');
			break;
			default:
			$this->message('未定义的操作');
			break;
		}
	}
	function ajaxlocation(){
		
	 	$lat = IFilter::act(IReq::get('lat'));   
	 	$lng = IFilter::act(IReq::get('lng'));  
	  
	 	$content =   file_get_contents('http://api.map.baidu.com/geoconv/v1/?coords='.$lng.','.$lat.'&&from=1&to=5&ak='.Mysite::$app->config['baidumapkey']);
	 	$backinfo = json_decode($content,true);
	 	//Array ( [status] => 0 [result] => Array ( [0] => Array ( [x] => 113.6778066454 [y] => 34.799780450303 ) ) )
	 	if($backinfo['status'] == 0){
	 	   $data['lat'] = $backinfo['result'][0]['y'];
	 	   $data['lng'] = $backinfo['result'][0]['x'];
	 	   ICookie::set('lat',$backinfo['result'][0]['y'],2592000);  
	     ICookie::set('lng',$backinfo['result'][0]['x'],2592000);  
	 	   $this->success($data);
	 	}else{
	 		$this->message('失败');
	 	} 
	 	 
	}
	function locationshop(){
		 ICookie::clear('myaddress');
		 $link = IUrl::creatUrl('wxsite/shoplist');
	    $this->message('',$link); 
	}
		function getwxuaerlocation(){
		
	 	$lat = IFilter::act(IReq::get('lat'));   
	 	$lng = IFilter::act(IReq::get('lng'));  
	 # $lng = 113.6778066454;
	 #  $lat = 34.799780450303;
	  //http://api.map.baidu.com/geocoder/v2/?ak=E4805d16520de693a3fe707cdc962045&callback=renderReverse&location=39.983424,116.322987&output=json&pois=1
	 	$content =   file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.Mysite::$app->config['baidumapkey'].'&location='.$lat.','.$lng.'&output=json&pois=0&coordtype=wgs84ll');
	 	$backinfo = json_decode($content,true);
	 	//Array ( [status] => 0 [result] => Array ( [0] => Array ( [x] => 113.6778066454 [y] => 34.799780450303 ) ) )
		#print_r($backinfo['result']['addressComponent']);
		
	 	if($backinfo['status'] == 0){
	 	   $data['cityname'] = $backinfo['result']['addressComponent']['city'];
	 	   $data['areaname'] = $backinfo['result']['addressComponent']['district'];
	 	   $data['streetname'] = $backinfo['result']['addressComponent']['street'];		  
	 	   $this->success($data);
	 	}else{
	 		$this->message('失败');
	 	} 
	 	 
	}
	
	function checkwxuser(){
		/*
	  $logintype = ICookie::get('logintype');  
	  if($logintype == 'wx'){
	  }else{
	  	$this->message('Not allowed');
	  }*/
	  if(Mysite::$app->config['wxLoginType'] == 1 && $this->member['uid'] <= 0 ){
		   $link = IUrl::creatUrl('wxsite/login');
		  $this->message('',$link);
	  }
	  
	  
	}
	function showpayhtml($data){
        if(!empty($data['id'])){
         $orderinfo = $this->mysql->select_one("select shoptype,shopid from ".Mysite::$app->config['tablepre']."order where id = ".$data['id']."");        
		  if($orderinfo['shoptype']==100){
              $act = 'paotuidetail';
			  $ccurl = Mysite::$app->config['siteurl'].'/index.php?ctrl=wxsite&action=paotui';
          }else{
              $act = 'ordershow';
			  if($orderinfo['shoptype']==0){
				  $ccurl = Mysite::$app->config['siteurl'].'/index.php?ctrl=wxsite&action=shopshow&typelx=wm&id='.$orderinfo['shopid'];
			  }else{
				  $ccurl = Mysite::$app->config['siteurl'].'/index.php?ctrl=wxsite&action=shopshow&typelx=mk&id='.$orderinfo['shopid'];
			  }
			  
          }
        }
		$tempcontent = '';
		//array('paysure'=>false,'reason'=>'','url'=>'');
		$color = Mysite::$app->config['color'];
		if($color == 'green'){
			$colorhtml = '<style>
									.titCon {
									    background-color: #01cd88!important;
									}
									.cipuSubsucCon .cipuSubsucBot b{
										background: #01cd88!important;
										border: 1px solid #01cd88!important;
									}

							</style>';
		}else if($color == 'yellow'){
			$colorhtml = '<style>
									.titCon {
									    background-color: #ff7600!important;
									}
									.cipuSubsucCon .cipuSubsucBot b{
										background: #ff7600!important;
										border: 1px solid #ff7600!important;
									}

							</style>';
		}else{
			$colorhtml = '<style>
									.titCon {
									    background-color: #ff6e6e!important;
									}
									.cipuSubsucCon .cipuSubsucBot b{
										background: #ff6e6e!important;
										border: 1px solid #ff6e6e!important;
									}

							</style>';
		}


		if($data['paysure'] == true){
		$tempcontent = '<div class="titCon">
							<div class="titBox">
								 
								 <div class="titC" style= "width: 100%;">
									<i>
									<a href="'.$ccurl.'">
									<img src="'.Mysite::$app->config['siteurl'].'/templates/m7/public/wxsite/images/top04.png">
									</a>
									</i>
									<h2 style="t">支付结果</h2>
								 </div>
								 
							</div>
						</div>
						<div class="cipuSubsucCon">
								<div class="cipuSubsucTop">
									<i style="background-image: url(/upload/images/icon_zfcg.png);"></i>
									<h2>订单支付成功</h2>
								</div>
								<div class="cipuSubsucCen">
									<ul>
										<li>订单编号：<span style="color: #333;">'.$data['reason']['dno'].'</span></li>
										<li>订单金额：<span>￥'.$data['reason']['allcost'].'</span></li>
									</ul>
								</div>
								<div class="cipuSubsucBot">
										<a href="'.Mysite::$app->config['siteurl'].'/index.php?ctrl=wxsite&action='.$act.'&orderid='.$data['id'].'"  style="color:#fff;text-decoration:none;"><span> 查看订单</span></a>
									<b><a href="'.Mysite::$app->config['siteurl'].'" style="color:#fff;text-decoration:none;">返回首页</a></b>
								</div>
							</div>';
		}else{
	   $tempcontent = '<div class="titCon">
							<div class="titBox">
								 
								 <div class="titC" style= "width: 100%;">
										<i>
										<a href="'.$ccurl.'">
										<img src="'.Mysite::$app->config['siteurl'].'/templates/m7/public/wxsite/images/top04.png">
										</a>
										</i>										
										<h2>支付结果</h2></div>
								  
							</div>
						</div>
						<div class="cipuSubsucCon">
								<div class="cipuSubsucTop">
									<i style="background-image: url(/upload/images/icon_zfcg.png);"></i>
									<h2>订单支付失败</h2>
								</div>
								<div class="cipuSubsucCen">
									<h3 style="color:red;">原因:'.$data['reason'].'</h3>
								</div>
								<div class="cipuSubsucBot">
									<a href="'.Mysite::$app->config['siteurl'].'/index.php?ctrl=wxsite&action='.$act.'&orderid='.$data['id'].'"  style="color:#fff;text-decoration:none;"><span> 查看订单</span></a>
									<b><a href="'.Mysite::$app->config['siteurl'].'" style="color:#fff;text-decoration:none;">返回首页</a></b>
								</div>
							</div>';
		}

		$html = '<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>支付返回信息</title> 
	 
	 '.$colorhtml.'
 
 <script>
 	 
</script>
<link rel="stylesheet" href="/templates/m7/public/wxsite/css/pay-font-awesome.css"/>
<link rel="stylesheet" href="/templates/m7/public/wxsite/css/pay-font-awesome.min.css"/>
<link rel="stylesheet" href="/templates/m7/public/wxsite/css/pay-index.css"/>
</head>
<body style="height:100%;width:100%;margin:0px;"> 
   <div style="max-width:400px;margin:0px;margin:0px auto;min-height:300px;"> '.$tempcontent.'    </div>
	 
</body>
</html>'; 
print_r($html);
exit;
      
    }
	function gotopay(){
		
	   	$orderid = intval(IReq::get('orderid')); 
	   		$payerrlink = IUrl::creatUrl('wxsite/subshow/orderid/'.$orderid);    
			$errdata = array('paysure'=>false,'reason'=>'','url'=>'');
		$errdata['id'] = $orderid;
		  if(empty($orderid)){
				$backurl = IUrl::creatUrl('wxsite/index');  
				$errdata['url'] = $backurl;
				$errdata['reason'] = '订单获取失败';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);   

		  } 
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
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");  //获取主单
	//	print_r($orderinfo);
		if(empty($orderinfo)){
			$errdata['url'] = $payerrlink;
				$errdata['reason'] = '订单数据获取失败';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
		} 
		if($userid > 0){
			if($orderinfo['buyeruid'] !=  $userid){
				$errdata['url'] = $payerrlink;
				$errdata['reason'] = '订单不属于您';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
			} 
		}
		if($orderinfo['paytype'] == 0){
			 
				$errdata['url'] = $payerrlink;
				$errdata['reason'] = '此订单是货到支付订单不可操作';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
			  
		}
		if($orderinfo['status']  > 2){
			 
				$errdata['url'] = $payerrlink;
				$errdata['reason'] = '此订单已发货或者其他状态不可操作';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
			 
		}
		//
		$paydotype = IFilter::act(IReq::get('paydotype'));
		 
	 
		 $paylist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where  loginname = '".$paydotype."' and (type = 0 or type=2) order by id asc limit 0,50");
	
		if(empty($paylist)){
			$errdata['url'] = $payerrlink;
				$errdata['reason'] = '不存在的支付类型';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata);  
		}			 
		 
		if($orderinfo['paystatus'] == 1){
			$errdata['url'] = $payerrlink;
				$errdata['reason'] = '此订单已支付';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata); 
		 
		}
		$paydir = hopedir.'/plug/pay/'.$paydotype;
	 	if(!file_exists($paydir.'/pay.php'))
		{ 
			$errdata['url'] = $payerrlink;
				$errdata['reason'] = '支付方式文件不存在';
				$errdata['paysure'] = false;  
				$this->showpayhtml($errdata); 
			 
		} 
		$dopaydata = array('type'=>'order','upid'=>$orderid,'cost'=>$orderinfo['allcost'],'source'=>2,'paydotype'=>$paydotype);//支付数据 
		include_once($paydir.'/pay.php');  
		if( strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')    ){
			#print_R($linkurl);]
			$linkurl = base64_encode($linkurl);
			$gourl = IUrl::creatUrl('wxsite/wxalimobile/payurl/'.$linkurl.'');
			header("Location: $gourl");
                        //若微信端无法跳转，换用下面这段代码
//                        echo '<script>location.href=\''.$gourl.'\';</script>';
//                        exit;

		}
		//调用方式  直接调用支付方式
		exit;
	}
	 
	function wxalimobile(){
		$payurl = trim(IFilter::act(IReq::get('payurl')));
		$payurl = base64_decode($payurl);
		if( !strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')    ){
 			header("Location: $payurl");
			exit;
		} else{
			$data['payurl'] = $payurl;
			Mysite::$app->setdata($data);
		}
		
	}
	function drawbacklog(){
		$link = IUrl::creatUrl('wxsite/index'); 
		if($this->member['uid'] == 0)  $this->message('未登录',$link); 
		$orderid = intval(IReq::get('orderid'));  
		 
		$statusarr = array('0'=>'正常状态','1'=>'待平台处理','2'=>'退款成功','3'=>'退款失败','4'=>'待商家处理','5'=>'退款结束');
		$titlearr = array('0'=>'提交申请退款','1'=>'退款关闭','2'=>'商家同意退款','3'=>'商家拒绝退款','4'=>'退款成功');
		if(!empty($orderid)){ 
	       	$order = $this->mysql->select_one("select id,status,is_reback,paytype_name from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");   
            $data['showbtn'] = 1;
			if($order['status'] == 3 ){
				$data['showbtn'] = 0;
			}
			$data['backacount'] = '账户余额';
			if($order['paytype_name'] == 'weixin'){
				$data['backacount'] = '微信';
			}elseif($order['paytype_name'] == 'alipay' || $order['paytype_name'] == 'alimobile'){
				$data['backacount'] = '支付宝';
			} 
			$data['order'] = $order;
			if($order['is_reback'] > 0){
				$drawbacklog =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid='".$order['id']."' order by id DESC ");   
				$data['drawbacklog'] = array();
				foreach($drawbacklog as $k=>$v){
					$value['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
					$value['title'] = $titlearr[$v['status']];					
					
					if($v['status'] == 0){
						$value['content1'] = '退款理由：'.$v['reason'];
					    $value['content2'] = '退款说明：'.$v['content'];
					}
					if($v['status'] == 1){
						$value['content1'] = '你已取消了退款申请';
                        $value['content2'] = '';						
					}
					if($v['status'] == 2){
						$value['content1'] = '商家同意退款，等待平台处理';
						$value['content2'] = '';
					}
					if($v['status'] == 3){
						$value['content1'] = '拒绝理由：'.$v['reason'];
                        $value['content2'] = '';						
					}
					if($v['status'] == 4){
						$value['content1'] = '平台已退款，退款金额将在1~3个工作日内原路返回';
                        $value['content2'] = '';						
					}
					$data['drawbacklog'][] = $value; 
				}
				$data['status'] = $statusarr[$order['is_reback']];
				$data['nowstatus'] = $drawbacklog[0]['status'];
				if($drawbacklog[0]['status'] ==1 || $drawbacklog[0]['status'] ==2 ){
					$data['showbtn'] = 0;
				}
				$data['cost'] = $drawbacklog[0]['cost'];
			}   
	        Mysite::$app->setdata($data); 
		}else{
			$data['order'] = '';
			Mysite::$app->setdata($data);
		}
		 
	}
	function drawback(){
		$link = IUrl::creatUrl('wxsite/index'); 
		if($this->member['uid'] == 0)  $this->message('未登录',$link); 
		$orderid = intval(IReq::get('orderid')); 
        #print_r($orderid);exit;		
        $order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where buyeruid='".$this->member['uid']."' and id = ".$orderid."");   
        if(empty($order))  $this->message('订单获取失败'); 
        $data['order'] = $order;
		if($order['is_reback'] > 0){
			$data['drawbacklog'] =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."drawbacklog where orderid='".$order['id']."' order by id DESC ");   
	    }
		
		Mysite::$app->setdata($data); 		 
	}
	function savedrawbacklog(){
		if(empty($this->member['uid'])){
			$this->message('member_nologin');
		}
	 	$orderid = IReq::get('orderid');
		$drawdata['allcost'] = IReq::get('allcost') ;//退款金额
		$drawdata['orderid'] = $orderid;// 订单号
		$drawdata['reason'] = IReq::get('reason'); //退款原因
		$drawdata['content'] = IReq::get('content');  //退款详细内容说明
		$drawdata['typeid'] = 0; //退款类型 0用户自己申请退款  1后台管理员直接退款 
		$drawdata['status'] = 0; //退款状态
		$drawdata['uid'] = $this->member['uid'] ; 
		if(empty($drawdata['reason'])) $this->message('请选择退款原因');
		if(empty($drawdata['content']))$this->message('请填写退款详细内容说明');
		$orderinfo =   $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."' order by id DESC ");
#print_r($orderinfo);		
		if($orderinfo['shoptype'] == 100){ 
			$data['uid'] = $drawdata['uid'];
			$data['username'] = $orderinfo['buyername'];
			$data['reason'] = $drawdata['reason'];
			$data['orderid'] = $orderid;
			$data['shopid'] = $orderinfo['shopid'];		
			$data['content'] = $drawdata['content'];	
			$data['status'] = 0;//  退款状态 0待处理 1退款结束 2商家同意退款 3退款失败 4平台退款
			$data['addtime'] = time();
			$data['cost'] = $orderinfo['allcost'];
			$data['admin_id'] = $orderinfo['admin_id'];
			$data['type'] = 0;
			$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);   //写退款记录	 	
			$shenhedrawback = Mysite::$app->config['shenhedrawback'];//退款是否需要平台审核 1需要审核
			$orderClass = new orderClass();			
			if( $shenhedrawback == 1){//需要审核 
	              $orderClass->writewuliustatus($orderid,13,$data['paytype']);   
				  $this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>1),"id='".$orderid."'");
				  $psbinterface = new psbinterface();
				  if($psbinterface->psbdraworder($orderinfo['id'])){
				  }
				  $this->success('success');  
			}else{//不需要审核
				if($orderinfo['paytype_name'] == 'open_acout'){
					if(!empty($orderinfo['buyeruid'])){		 
						$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$orderinfo['buyeruid']."'   "); 
						$memclas = new memberclass($this->mysql);	
						if(!empty($memberinfo)){
							$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$data['cost'],"uid ='".$orderinfo['buyeruid']."' ");			 
						}	
						$shengyucost = $memberinfo['cost']+$data['cost']; 
						$memclas->addmemcostlog( $orderinfo['buyeruid'],$memberinfo['username'],$memberinfo['cost'],1,$data['cost'],$shengyucost,"管理员退款给用户",ICookie::get('adminuid'),ICookie::get('adminname') );				 
						$memclas->addlog($orderinfo['buyeruid'],2,1,$data['cost'],'退款处理','用户取消跑腿订单',$shengyucost);  
					} 
					$orderClass->writewuliustatus($orderid,14,$data['paytype']);   
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>2,'status'=>4),"id='".$orderid."'");
					$data['status'] = 4;
					$data['type'] = 1;
					$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);	
				}else{
					$orderClass->writewuliustatus($orderid,13,$data['paytype']);   
					$this->mysql->update(Mysite::$app->config['tablepre'].'order',array('is_reback'=>1,'status'=>4),"id='".$orderid."'");
					$data['status'] = 2;
					$data['type'] = 1;
					$this->mysql->insert(Mysite::$app->config['tablepre'].'drawbacklog',$data);		
				}	
				$psbinterface = new psbinterface();
				if($psbinterface->psbdraworder($orderinfo['id'])){
				}
				$this->success('success');  
			}			
		}else{
			#print_r($drawdata);exit;
			$drawbacklog = new drawbacklog($this->mysql);		 
			$check = $drawbacklog->setsavedraw($drawdata)->save();
			if($check == true){
				$this->success('success');  
			}else{
				$msg = $drawbacklog->GetErr();
				$this->message($msg);
		    } 			
		}
	}
	//	一起说 列表
	function togethersay(){

	#	$this->checkwxweb();
		
		$wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
		#print_r( $signPackage );
		$data['signPackage'] = $signPackage;
		
		
		$togethersaylist1 = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid where a.is_top=0  and is_show=1   order by addtime desc ");
	#	print_r($togethersaylist);
		$togethersaylist = array();
		foreach($togethersaylist1 as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");
			$wxuserimages = $value['userimg'];
			$value['wxuserimgarr']  = explode('@',$wxuserimages);
			$togethersaylist[] = $value;
		}
		$data['togethersaylist'] = $togethersaylist;
		
	#	print_r($togethersaylist);
		
		$togethersaylist2 = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."admin  as b  on a.uid = b.uid where a.is_top=1  and is_show=1   order by addtime desc ");
	#	print_r($togethersaylist);
		$togethersaycomlist = array();
		foreach($togethersaylist2 as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");
				$wxuserimages = $value['userimg'];
			$value['wxuserimgarr']  = explode('@',$wxuserimages);
			$togethersaycomlist[] = $value;
		}
		$data['togethersaycomlist'] = $togethersaycomlist;
		/* print_r( $data['togethersaycomlist'] );
		exit; */
		
		Mysite::$app->setdata($data);
	}
	function togethersaydata(){
		#$this->checkwxweb();
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')));
		$wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
		#print_r( $signPackage );
		$data['signPackage'] = $signPackage;
		$togethersaylist1 = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid where a.is_top=0  and is_show=1   order by addtime desc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
			#print_r($togethersaylist1);exit;
		$togethersaylist = array();
		foreach($togethersaylist1 as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");
			$wxuserimages = $value['userimg'];
			$value['wxuserimgarr']  = explode('@',$wxuserimages);
			$togethersaylist[] = $value;
		}
		$data['togethersaylist'] = $togethersaylist;

		#	print_r($togethersaylist);

		$togethersaylist2 = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."admin  as b  on a.uid = b.uid where a.is_top=1  and is_show=1   order by addtime desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
		#	print_r($togethersaylist);
		$togethersaycomlist = array();
		foreach($togethersaylist2 as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");
			$wxuserimages = $value['userimg'];
			$value['wxuserimgarr']  = explode('@',$wxuserimages);
			$togethersaycomlist[] = $value;
		}
		$data['togethersaycomlist'] = $togethersaycomlist;
		/* print_r( $data['togethersaycomlist'] );
		exit; */
		Mysite::$app->setdata($data);
	}
	function commentwxuser(){
        $this->checkwxweb();
		$id = intval(IFilter::act(IReq::get('id')));
		$data['id'] = $id;
		$checkinfo  = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment where id = ".$id."   ");
		if($checkinfo['is_top']==0){			
			$wxcommentone = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid where a.id = ".$id."  order by addtime desc "); //获取单独的评论
			
		}else{
			$wxcommentone = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."admin  as b  on a.uid = b.uid where a.id = ".$id."  order by addtime desc "); //获取单独的评论
		}
		$data['userimages'] = explode('@',$wxcommentone['userimg']);
		
		$wxreplylist = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxreplycomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid where a.parentid = ".$wxcommentone['id']."  order by addtime desc "); //获取其它微信用户回复的评论
		 
		$data['zongzanshu']  = $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$wxcommentone['id']."  ");
		$data['wxreplylist'] = $wxreplylist;
		$usercontent = $wxcommentone['usercontent']; 
		$usercontent=str_replace("\r","",$usercontent);
		$usercontent=str_replace("\n","",$usercontent);
 		$wxcommentone['usercontent'] = strip_tags($usercontent);
		$data['wxcommentone'] = $wxcommentone;
	#print_r($wxcommentone);
		$wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
		#print_r( $signPackage );
		$data['signPackage'] = $signPackage;
		$data['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$id."  ");
		Mysite::$app->setdata($data);
	}
	//微信用户本人留言
	 function saveuserpmes(){
		$this->checkwxweb();
	   $uid = $this->member['uid'];	
	   $media_ids = trim(IFilter::act(IReq::get('media_ids')));
	 
	   $wxclass = new wx_s();
	   	$accessToken = $wxclass->gettoken();
	   $mediaarr = explode(',',$media_ids);
	   $filename = array();
	   if(!empty($media_ids)){
		   if( is_array($mediaarr) ){
			   foreach ( $mediaarr as $key=>$value ){
				   $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$value;
				   $upwxfilename = $wxclass->saveMedia($url);
				   $filename[] = $upwxfilename;			
				}	
				$filename = $filename;
				$data['userimg'] = implode('@',$filename);			
		   }else{
				   $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$media_ids;
				   $upwxfilename = $wxclass->saveMedia($url);
				   $data['userimg'] = $upwxfilename;
		   }
	   }else{
		   
		   $data['userimg']  = '';
		   
	   }
	   $data['usercontent'] = trim( IReq::get('message') ); 
	   $data['uid'] = $uid;
	   $data['addtime'] = time();
	    
	  $this->mysql->insert(Mysite::$app->config['tablepre'].'wxcomment',$data);
	   $this->success('success');
	 }
	//微信用户本人留言
	 function savehuifupj(){
		$this->checkwxweb();
	   $uid = $this->member['uid'];	
	   $data['content'] = trim(IFilter::act(IReq::get('message')));
	   $data['parentid'] = intval(IFilter::act(IReq::get('parentid')));
	   $data['cityname'] = trim(IFilter::act(IReq::get('cityname')));
	   $data['areaname'] = trim(IFilter::act(IReq::get('areaname')));
	   $data['streetname'] = trim(IFilter::act(IReq::get('streetname')));
	   $data['kejian'] = intval(IFilter::act(IReq::get('kejianvalue')));
	   $data['uid'] = $uid;
	   $data['addtime'] = time();
	   if(empty($data['content'])) $this->message('评价内容不能为空');
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'wxreplycomment',$data);
	   $this->success('success');
	 }
	//微信用户点赞
	function saveuserzanjia(){
	   $data['uid'] = intval(IFilter::act(IReq::get('uid')));
	   $data['commentid'] = intval(IFilter::act(IReq::get('commentid')));
	   $pingjiaone = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment where id =".$data['commentid']." ");
	   if(empty($pingjiaone)) $this->message('获取评价对象错误'); 
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'wxpjzan',$data);
	    $this->success('success');
	}
		//微信用户取消点赞
	function saveuserzanjian(){
	   $data['uid'] = intval(IFilter::act(IReq::get('uid')));
	   $data['commentid'] = intval(IFilter::act(IReq::get('commentid')));
	   $pingjiaone = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment where id =".$data['commentid']." ");
	   if(empty($pingjiaone)) $this->message('获取评价对象错误'); 
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'wxpjzan',$data);
	   	 $this->mysql->delete(Mysite::$app->config['tablepre'].'wxpjzan',"commentid ='".$data['commentid']."' and uid = '".$data['uid']."' ");   
	    $this->success('success');
	}
	//微信用户举报
	function savejubaowxuser(){
	   $data['uid'] = intval(IFilter::act(IReq::get('uid')));
	   $data['commentid'] = intval(IFilter::act(IReq::get('jubaoid')));
	   $pingjiaone = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment where id =".$data['commentid']." ");
	   if(empty($pingjiaone)) $this->message('获取评价对象错误'); 
	   $getjubaowxuser = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxuserjubao where uid =".$data['uid']." and commentid = ".$data['commentid']." ");
	   if(!empty($getjubaowxuser)) $this->message('你已经举报过啦~'); 
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'wxuserjubao',$data);
	    $this->success('success');
	}
	//微信用户删除
	function saveshanchuwxuser(){
	   $uid = intval(IFilter::act(IReq::get('uid')));
	   $shanchuid = intval(IFilter::act(IReq::get('shanchuid')));
	   $pingjiaone = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."wxcomment where id =".$shanchuid." and uid= ".$uid." ");
	   if(empty($pingjiaone)) $this->message('获取评价对象错误'); 
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'wxcomment',"uid ='".$uid."' and id = '".$shanchuid."' ");    
	    $this->success('success');
	}
	function wxmsglist(){
		$uid = $this->member['uid'];	
		$togethersaylist1 = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid where a.is_top=0 and a.uid = '".$uid."'  order by addtime desc ");
		$togethersaylist = array();
		foreach($togethersaylist1 as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");
			$togethersaylist[] = $value;
		}
		$data['togethersaylist'] = $togethersaylist;
		
		
		$systemsaylist = $this->mysql->getarr(" select * from ".Mysite::$app->config['tablepre']."wxcomment as a left join ".Mysite::$app->config['tablepre']."member  as b  on a.uid = b.uid where a.is_top=1 order by addtime desc ");
		$systemmsg = array();
		foreach($systemsaylist as $key=>$value){
			$value['pingjiazongshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxreplycomment where parentid  = ".$value['id']."  ");
			$value['zongzanshu'] =  $this->mysql->counts(" select id from ".Mysite::$app->config['tablepre']."wxpjzan where commentid  = ".$value['id']."  ");
			$systemmsg[] = $value;
		}
		$data['systemmsg'] = $systemmsg;
		
		Mysite::$app->setdata($data);
		
	}
	//发表主题页面
	function fabiaozhuti(){
        $this->checkwxweb();
		#print_r(ICookie::get('wxCurUrl'));
		$wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
		#print_r( $signPackage );
		$data['signPackage'] = $signPackage;
		Mysite::$app->setdata($data);
		
	}

	
	
		//微信收藏商家
		function collectshopdata(){		// 首页获取附近商家列表（外卖和超市）
		$typelx = IFilter::act(IReq::get('typelx')); 
		
		 if(!empty($typelx)){
			 if($typelx == 'wm'){
				 ICookie::set('shopshowtype','waimai',2592000); 
				 $shopshowtype = 'waimai';
			 }
			 if($typelx == 'mk'){
				 ICookie::set('shopshowtype','market',2592000); 
				 $shopshowtype = 'market';
			 }
			  if($typelx == 'yd'){
				 ICookie::set('shopshowtype','dingtai',2592000); 
				 $shopshowtype = 'dingtai';
			 }
		 }else{
			 
			 $shopshowtype = ICookie::get('shopshowtype');
			 
		 }
	  
		
		$cxsignlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
		$cxarray  =  array();
		foreach($cxsignlist as $key=>$value){
		   $cxarray[$value['id']] = $value['imgurl'];
		}
 
            	  $where = '';  
            
				       $lng = 0;
            	         $lat = 0;
            	          
            	            $lng = ICookie::get('lng');
            	            $lat = ICookie::get('lat');
						    $lng = empty($lng)?0:$lng;
							$lat =empty($lat)?0:$lat;
 #  $where = empty($where)?'   and  SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ': $where.' and SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ';
            	         
            	         $lng = trim($lng);
            	         $lat = trim($lat);
            	         $lng = empty($lng)?0:$lng;
						 $lat =empty($lat)?0:$lat;
						 
                        $orderarray = array(
                            '0' =>" (2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*(".$lat."-lat)/360),2)+COS(3.1415926535898*".$lat."/180)* COS(lat * 3.1415926535898/180)*POW(SIN(3.1415926535898*(".$lng."-lng)/360),2))))*1000  ASC      ",
						 	// 	'0'=>'   sort asc      ',                       
                          ); 
				   
            			 /*获取店铺*/
            		  $pageinfo = new page();
            		  $pageinfo->setpage(intval(IReq::get('page'))); 
					   $where .= $qsjarray[$qsjid];
					      $where .= $qsjarray[$qsjid];
            	  $list =   $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and endtime > ".time()."  ".$where."    order by ".$orderarray[0]." ");
			 
            			$nowhour = date('H:i:s',time()); 
                  $nowhour = strtotime($nowhour);
                  $templist = array();
                   $cxclass = new sellrule();  
                  if(is_array($list)){
            			    foreach($list as $keys=>$values){  
            			     
            			    		if($values['id'] > 0){
				 
				 $values['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$values['id']."'  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."  ");//收藏
				 if(!empty($values['collect'])){		 
									
						$templist111 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where cattype = ".$values['shoptype']." and  parent_id = 0    order by orderid asc limit 0,1000"); 
										$attra = array();
										 $attra['input'] = 0;
										 $attra['img'] = 0;
										 $attra['checkbox'] = 0; 
										 foreach($templist111 as $key=>$vall){
											  if($vall['type'] == 'input'){
											   $attra['input'] =  $attra['input'] > 0?$attra['input']:$vall['id'];
											  }elseif($vall['type'] == 'img'){
												 $attra['img'] =  $attra['img'] > 0?$attra['img']:$vall['id'];
											  }elseif($vall['type'] == 'checkbox'){
												$attra['checkbox'] =  $attra['checkbox'] > 0?$attra['checkbox']:$vall['id'];
											  }
										 } 
						#		print_r($attra);
						#		echo("11111111");								
										
										if($values['shoptype'] == 1 ){
											$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$values['id']."   ");
										}else{
											$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$values['id']."   ");
										}
									if(!empty($shopdet)){
									 	$values = array_merge($values,$shopdet);
										 
            			    	  $values['shoplogo'] = empty($values['shoplogo'])? Mysite::$app->config['imgserver'].Mysite::$app->config['shoplogo']:Mysite::$app->config['imgserver'].$values['shoplogo'];
            			          $checkinfo = $this->shopIsopen($values['is_open'],$values['starttime'],$values['is_orderbefore'],$nowhour); 
            			          $values['opentype'] = $checkinfo['opentype'];
            			          $values['newstartime']  =  $checkinfo['newstartime'];  
								  
							


					$attrdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  cattype = ".$values['shoptype']." and shopid = ".$values['id']."");
            			          $cxclass->setdata($values['id'],1000,$values['shoptype']); 
						#		 print_r($attrdet); 
							  

			  
								  
								  $mi = $this->GetDistance($lat,$lng, $values['lat'],$values['lng'], 1); 
							          $tempmi = $mi;
								  $mi = $mi > 1000? round($mi/1000,2).'km':$mi.'m';
													  
									$values['juli'] = 		$mi;
                                                                        
                                        $checkps = 	 $this->pscost($values); 
            			          $values['pscost'] = $checkps['pscost'];
                                                                        
                                     /* $valuelist = empty($values['pradiusvalue'])? unserialize($this->platpsinfo['radiusvalue']):unserialize($values['pradiusvalue']);
					$juliceshi = intval($mi/1000);
					if(is_array($valuelist)){
						foreach($valuelist as $k=>$v){
                                                          
							if($juliceshi == $k){
							  $cvalue['pscost'] = $v;
                                                               
								$cvalue['canps'] = 1;
							}
						}
					}*/
								  
								   
//	 $shopcounts = $this->mysql->select_one( "select count(id) as shuliang  from ".Mysite::$app->config['tablepre']."order	 where   status = 3 and  shopid = ".$values['id']."" );
         $shopcounts = $this->mysql->select_one( "select sellcount as shuliang  from ".Mysite::$app->config['tablepre']."shop	 where    id = ".$values['id']."" );
									
								  	$values['ordercount']  = $values['ordercount']+$values['virtualsellcounts'];
                                                                       
									
								      $cxinfo = $this->mysql->getarr("select name,id,signid from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$values['id'].",shopid)   and status = 1 and starttime  < ".time()." and endtime > ".time()." ");
								  $values['cxlist'] = array();
								  
								    foreach($cxinfo as $k1=>$v1){
								    if(isset($cxarray[$v1['signid']])){
										 $v1['imgurl'] = $cxarray[$v1['signid']];
										 $values['cxlist'][] = $v1;
									}
								  }
								    /* 店铺星级计算 */
								$zongpoint = $values['point'];
								$zongpointcount = $values['pointcount'];
								if($zongpointcount != 0 ){
									$shopstart = intval( round($zongpoint/$zongpointcount) );
								}else{
									$shopstart= 0;
								}
									$values['point'] = 	$shopstart;	
								     $values['attrdet'] = array();
            			          foreach($attrdet as $k=>$v){
            			          	   if($v['firstattr'] == $attra['input']){
            			          	   	$values['attrdet']['input'] = $v['value'];
            			          	   }elseif($v['firstattr'] == $attra['img']){
            			          	   	$values['attrdet']['img'][] = $v['value'];
            			          	   }elseif($v['firstattr'] == $attra['checkbox']){
            			          	   	 	$values['attrdet']['checkbox'][] = $v['value'];
            			          	   } 
            			          }
								  
						#		 print_r($values['attrdet']);
								 
            			          $templist[] = $values;
            			     }
							 
				 } 
            	       } 
					   
					   
							}
					   
            	    }
            	    $data  = $templist; 
 #print_r($data);
	  $datas = json_encode($data);
	  echo 'showmoreshop('.$datas.')';
      exit; 
	    $this->success($data);
	 } 
	 
		
	
	 function saveshangjia(){
		$regagree = IFilter::act(IReq::get('regagree'));
		if(empty($regagree))	$this->message('请阅读入驻协议后勾选接受！');
		$username = IFilter::act(IReq::get('username'));  	
		$mobile = IFilter::act(IReq::get('mobile'));  
		$qq = IFilter::act(IReq::get('qq'));  
		 $resname = IFilter::act(IReq::get('resname')); 
		 $addr = IFilter::act(IReq::get('addr'));    
		if(empty($username))   $this->message('姓名不能为空！');
		if(!(IValidate::len($username,1,50)))$this->message('member_addresslength');	
		if(empty($mobile))   $this->message('手机号不能为空！');
		 if(!(IValidate::phone($mobile)))$this->message('errphone');  	
		if(empty($resname))   $this->message('店铺名称不能为空！');
		if(!(IValidate::len($resname,1,50)))$this->message('shop_shopnamelenth');	
		if(empty($addr))   $this->message('店铺的详细地址不能为空！');
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
		  
		 $this->success('shangjiasuccess');
	 }


    function login(){
        ICookie::set('sendphonetime',time()+600,600);
        $data = array();
        if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger') ){    //判断是微信浏览器不
            $data['is_wx'] = 1;
        }else{
            $data['is_wx'] = 0;
        }
		if( $this->member['uid'] > 0 ){
				$myurl = ICookie::get('wx_login_link');
                                
				if(empty($myurl)){
                                    
					$link = IUrl::creatUrl('wxsite/member');
				}else{
                                    
					$link = $myurl;
				}
				
				$this->message('',$link); 
		}
		session_start();
		$_SESSION['fastloginphone']=time();
		$info = $this->mysql->select_one(" select id from `".Mysite::$app->config['tablepre']."otherlogin`   where `loginname`='qqphone' ");
        $weblink = ICookie::get('wx_login_link');
        $defaultlink = IUrl::creatUrl('wxsite/member');
        $data['web_extend_link'] = empty($weblink)? $defaultlink:$weblink;
        $data['is_installqq'] = $info;
        Mysite::$app->setdata($data);
    }
	function reg(){
		$weblink = ICookie::set('wx_login_link','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		#$this->checkwxweb();		  
		session_start();
		$_SESSION['regesterphone']=time();
	}
	function setpwd(){
		$data['uid'] = IFilter::act(IReq::get('uid'));
		Mysite::$app->setdata($data); 
	}
	function loginout(){
	  $uid = $this->member['uid'];
	  $temp_password = $this->member['temp_password'];
	  $password = $this->member['password'];
	  $this->memberCls->loginout();
      if($temp_password == 'ghwmr123456789' && $password == md5($temp_password) ){
		  $link = IUrl::creatUrl('wxsite/setpwd/uid/'.$uid);
	  }else{
		  $link = IUrl::creatUrl('wxsite/index');		 
	  }	   
      $this->message('',$link);  
	} 
	function checkwxweb(){
        $myurl = Mysite::$app->config['siteurl'].$_SERVER["REQUEST_URI"];
        $action = Mysite::$app->getAction();
        if($action != 'setlogin' && $action != 'makeorder' && $action != 'login' && $action !='member' && $action != 'shopcart'  && $action != 'reg'){
			$checkinfo =  ICookie::get('wx_login_link');
			 
			$myurl = !empty($checkinfo)?$checkinfo:$myurl;
            ICookie::set('wx_login_link',$myurl,86400);
        }
		#print_R($myurl);exit;
        if( $this->member['uid'] <= 0 ){
			if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ //判断是微信浏览器不 
                        if(Mysite::$app->config['wxLoginType']==0){
							  $link = IUrl::creatUrl('wxsite/setlogin');
								$this->message('',$link);
						}
			}
                $link = IUrl::creatUrl('wxsite/loginmode');
               $this->message('',$link);
        }
        
	}
	function checkbackinfo(){
		if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){    //判断是微信浏览器不
			return true;
		}else{
			return false;
		}
	}
	/* 闪惠 */
	  function shophui(){
	 	$this->checkwxweb();
		  $shopsearch = IFilter::act(IReq::get('search_input')); 
		  $data['search_input'] = $shopsearch;
		  Mysite::$app->setdata($data);  
	 }
    //8.3修改闪惠商家列表  lzh 2016-6-6
    function shophuilistdata(){
        
        $where = '';
        $shopsearch = IFilter::act(IReq::get('search_input'));
        $shopsearch = urldecode($shopsearch);
        if(!empty($shopsearch)) $where=" and shopname like '%".$shopsearch."%' ";
        $areaid= intval(IFilter::act(IReq::get('areaid')));
        $templist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_main =1  order by orderid asc limit 0,1000");
        $attra['input'] = 0;
        $attra['img'] = 0;
        $attra['checkbox'] = 0;
        foreach($templist as $key=>$value){
            if($value['type'] == 'input'){
                $attra['input'] =  $attra['input'] > 0?$attra['input']:$value['id'];
            }elseif($value['type'] == 'img'){
                $attra['img'] =  $attra['img'] > 0?$attra['img']:$value['id'];
            }elseif($value['type'] == 'checkbox'){
                $attra['checkbox'] =  $attra['checkbox'] > 0?$attra['checkbox']:$value['id'];
            }
        }
        /*获取店铺*/ $pageinfo = new page();
        $pageinfo->setpage(intval(IReq::get('page')),2000);


        $shopxinxi = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1 and ".time()." < endtime and is_open =1  ".$where."  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");

        $list = array();
        foreach ($shopxinxi as $key=>$value){
            $shoplists = array();
            if($value['shoptype'] == 0){
                $shopfast =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where shopid = ".$value['id']." and  is_hui=1 and is_shophui=1 ");

                if(!empty($shopfast)){
                    $shoplists = array_merge( $value , $shopfast);
                    $list[] = $shoplists;
                }


            }else{
                $shopmarket =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid = ".$value['id']." and  is_hui=1 and is_shophui=1");
                if(!empty($shopmarket)){
                    $shoplists = array_merge( $value , $shopmarket);
                    $list[] = $shoplists;
                }
            }


        }
        $nowhour = date('H:i:s',time());
        $nowhour = strtotime($nowhour);
        $templist = array();
        if(is_array($list)){
            foreach($list as $keys=>$values){
                if($values['id'] > 0){
                    $shopshui = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophui where  status = 1  and starttime < ".time()." and endtime > ".time()." and shopid = ".$values['id']."");
                    if(!empty($shopshui)){

                        $values['shopshui']=$shopshui;
                        $firstday = strtotime( date('Y-m-01 00:00:00', strtotime(date("Y-m-d H:i:s")))	);   //当月第一天
                        $shopcounts = $this->mysql->select_one( "select count(id) as shuliang  from ".Mysite::$app->config['tablepre']."order	 where suretime >= ".$firstday."   and status = 3 and  shopid = ".$values['id']."" );
                        if(empty( $shopcounts['shuliang']  )){
                            $values['ordercount'] = 0;//月销量
                        }else{
                            $values['ordercount']  = $shopcounts['shuliang'];
                        }
                        $lng = ICookie::get('lng');
                        $lat = ICookie::get('lat');
						
                        $lng = empty($lng)?0:$lng;
                        $lat =empty($lat)?0:$lat;

                        $mi = $this->GetDistance($lat,$lng, $values['lat'],$values['lng'], 1);

                        $mi = $mi > 1000? round($mi/1000,2).'km':$mi.'m';
                        $values['juli'] = $mi;//店铺距离
                        $values['shoplogo'] = empty($values['shoplogo'])? Mysite::$app->config['imgserver'].Mysite::$app->config['shoplogo']:Mysite::$app->config['imgserver'].$values['shoplogo'];
                        $checkinfo = $this->shopIsopen($values['is_open'],$values['starttime'],$values['is_orderbefore'],$nowhour);
                        $values['opentype'] = $checkinfo['opentype'];
                        $values['newstartime']  =  $checkinfo['newstartime'];
                        $attrdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  cattype = 0 and shopid = ".$values['id']."");

                        $checkps = 	 $this->pscost($values);
                        $values['pscost'] = $checkps['pscost'];


                        $cxinfo = $this->mysql->getarr("select name,id,imgurl from ".Mysite::$app->config['tablepre']."rule where   shopid = ".$values['id']." and status = 1 and starttime  < ".time()." and endtime > ".time()." ");

                         
                        $values['cxlist'] = $cxinfo;
                        /* 店铺星级计算 */
                        $zongpoint = $values['point'];
                        $zongpointcount = $values['pointcount'];
                        if($zongpointcount != 0 ){
                            $shopstart = intval( round($zongpoint/$zongpointcount) );
                        }else{
                            $shopstart= 0;
                        }
                        $values['point'] = 	$shopstart;
                        $values['attrdet'] = array();
                        foreach($attrdet as $k=>$v){
                            if($v['firstattr'] == $attra['input']){
                                $values['attrdet']['input'] = $v['value'];
                            }elseif($v['firstattr'] == $attra['img']){
                                $values['attrdet']['img'][] = $v['value'];
                            }elseif($v['firstattr'] == $attra['checkbox']){
                                $values['attrdet']['checkbox'][] = $v['value'];
                            }
                        }

                        $templist[] = $values;
                    }
                }
                
            }
        }
//        $data  = $templist;
		
		   $data['shoplist']  = $templist; 
		   //print_r($templist);
			Mysite::$app->setdata($data);
		
     //   $this->success($data);
    }
	 function subpayhui(){
//		$userid = empty($this->member['uid'])?0:$this->member['uid'];
		$orderid = intval(IReq::get('orderid')); 
		if(empty($orderid)) $this->message('闪慧买单获取失败');
		
	  if($orderid < 1){ 
	  	 $this->message('订单获取失败');
	  }
		$order = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophuiorder where uid='".$this->member['uid']."' and id = ".$orderid."");   

		// 超市商品总价	 超市配送配送	shopcost 店铺商品总价	shopps 店铺配送费	pstype 配送方式 0：平台1：个人	bagc 
	  if(empty($order)){ 
	  	 $this->message('订单获取失败');
	  } 
	
	  $data['order'] = $order;
	
	
	
	if( $this->checkbackinfo() && $order['paystatus'] != 1  ){

	   $wxopenid = ICookie::get('wxopenid');  
	   $weixindir = hopedir.'/plug/pay/weixin/'; 
	   require_once $weixindir."lib/WxPay.Api.php";
       require_once $weixindir."WxPay.JsApiPay.php";
	   
	   
	   $tools = new JsApiPay();
	   if(empty($wxopenid)){
		   $wxopenid = $tools->GetOpenid();
	   }
       
	 
$input = new WxPayUnifiedOrder();
$input->SetBody("支付闪慧买单");
$input->SetAttach('a');
$input->SetOut_trade_no('a_'.$orderid);
$input->SetTotal_fee($order['sjcost']*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetTimeStamp(time());
$input->SetGoods_tag('闪慧');
$input->SetNotify_url(Mysite::$app->config['siteurl']."/plug/pay/weixin/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($wxopenid);
 //$url = Mysite::$app->config['siteurl'].'/plug/pay/weixin/jsapi.php';
$ordermm = WxPayApi::unifiedOrder($input);
//
#print_r($ordermm);exit;
$jsApiParameters = $tools->GetJsApiParameters($ordermm);
		$data['wxdata']  = $jsApiParameters;

/* {     "appId":"wx252c7ddb87971418",
		"nonceStr":"elp5is5mebhjdgrtsptptuel0z37rf62",
		"package":"prepay_id=wx20151109163411c5a9c269ed0695027503",
		"signType":"MD5",
		"timeStamp":"\"1447058052\"",
		"paySign":"FCED56BB6B00DEA6C4ED23CA3BAB5CF3"} */

}

	  Mysite::$app->setdata($data);

         if($this->checkbackinfo()){
             Mysite::$app->setAction('subpayhui');
         }else{
             Mysite::$app->setAction('mobilesubpayhui');
         }
	}
	//闪惠商家详情  8.3更新   lzh  2016-6-6
    function shophuishow(){
        $shopid = IFilter::act(IReq::get('id'));
        $shopinfo = $this->mysql->select_one("select *  from  ".Mysite::$app->config['tablepre']."shop  where id = ".$shopid." ");
        if(empty($shopinfo)) $this->message('获取店铺数据失败');
        $data['collect'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."collect where  collecttype = 0 and uid = ".$this->member['uid']." and collectid  = '".$shopinfo['id']."' ");//收藏
        $shopreal = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopreal where  shopid = ".$shopid." limit 0,4");//商家实景分类
        $data['shopreal']=array();
        foreach($shopreal as $key=>$val){
            $shoprealimg = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoprealimg where  parent_id = ".$val['id']);//商家实景分类图片
            $imgcount = $this->mysql->select_one("select count(id) as count from ".Mysite::$app->config['tablepre']."shoprealimg where  parent_id = ".$val['id']);//商家实景分类图片总数
            $val['shoprealimg']=$shoprealimg;
            $val['imgcount']=$imgcount['count'];

            $data['shopreal'][]=$val;
        }
        $shopshowtype = $shopinfo['shoptype'];
        if( $shopshowtype == 1 ){
            $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   ");
        }else{
            $shopdet  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast as a left join ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id  where a.shopid = ".$shopid."   ");
        }
        /* 店铺星级计算 */
        $zongpoint = $shopinfo['point'];
        $zongpointcount = $shopinfo['pointcount'];
        if($zongpointcount != 0 ){
            $shopstart = intval( round($zongpoint/$zongpointcount) );
        }else{
            $shopstart= 0;
        }

        $cxsignlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
        $cxarray  =  array();
        foreach($cxsignlist as $key=>$value){
            $cxarray[$value['id']] = $value['imgurl'];
        }

        $cxinfo = $this->mysql->getarr("select name,id,signid from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$shopinfo['id'].",shopid) and status = 1 and starttime  < ".time()." and endtime > ".time()." ");
        $cxlist = array();
        $data['shophui'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophui where  status = 1 and shopid = ".$shopinfo['id']."");
        foreach($cxinfo as $k1=>$v1){
            if(isset($cxarray[$v1['signid']])){
                $v1['imgurl'] = $cxarray[$v1['signid']];
                $cxlist[] = $v1;
            }
        }
        $data['cxlist'] = $cxlist;

        $areaid = ICookie::get('myaddress');

        $newshoparray = array_merge($shopinfo,$shopdet);
        $tempinfo =   $this->pscost($newshoparray);
        $backdata['pstype'] = $tempinfo['pstype'];
        $backdata['pscost'] = $tempinfo['pscost'];
        $data['psinfo'] = $backdata;
        $data['shopstart'] = $shopstart;
        $data['shopinfo'] = $shopinfo;
        $data['shopdet'] = $shopdet;
        Mysite::$app->setdata($data);
    }
	 
	 //闪惠买单
	function huisubshow(){
		$id = intval(IReq::get('id')); 
		$list = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$id."'");
		$data['shopid'] = $list['id'];
		if(empty($list)) $this->message("获取商家失败");
		if( $list['shoptype'] == 0 ){
			$shopinfo  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop as a left join ".Mysite::$app->config['tablepre']."shopfast as b on a.id = b.shopid where a.id='".$id."'");
		}else{
			$shopinfo  = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop as a left join ".Mysite::$app->config['tablepre']."shopmarket as b on a.id = b.shopid where a.id='".$id."'");
		}
		#print_r($shopinfo);
		
		$weeknum = date("w"); //今天星期几
		$nowtime = time();
        if( $shopinfo['is_shophui']==1 && $shopinfo['is_hui']==1 ){
			$shophuiinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophui where shopid = '".$shopinfo['id']."' and status=1  and starttime <= ".$nowtime." and endtime >=".$nowtime);
			
			#print_r($shophuiinfo);
			if(!empty($shophuiinfo)){
				if( !empty($shophuiinfo['limitweek']) &&  !empty($shophuiinfo['limittimes']) ){
							$weekarray = explode(',',$shophuiinfo['limitweek']);
					
					
								$datey = date('Y-m-d',$nowtime);
								   $info =explode(',',$shophuiinfo['limittimes']);
								   #print_r($info);
								   $find = false;
								   foreach($info as $kc=>$val)
								   {
									  if(!empty($val))
									  {
										$checkinfo = explode('-',$val);
										if(!empty($checkinfo[1]))
										{
											   $time1 = strtotime($datey.' '.$checkinfo[0].':00');
											   $time2 = strtotime($datey.' '.$checkinfo[1].':00');
											   if($nowtime > $time1 && $nowtime < $time2)
											   {
												   $find = true;
												   break;
											   }
										}
									  }
								   }
					
					
							if( in_array($weeknum,$weekarray)  &&  $nowtime >= $shophuiinfo['starttime']  &&  $nowtime <= $shophuiinfo['endtime'] && $find==true ){
								$is_shophui = 1;  	// 当前时间有闪慧
							}else{
								$is_shophui = 0;
							}
				}else{
					$is_shophui  = 1;
				}
			}else{
				
				$shophuiinfo = '';
				$is_shophui  = 0;
				
			}
		}else{
			$shophuiinfo = '';
			$is_shophui  = 0;
		}
        $paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist where type = 0 or type=2  order by id asc limit 0,50");
        if(is_array($paylist)){
            foreach($paylist as $key=>$value){
                $paytypelist[$value['loginname']] = $value['logindesc'];
            }
        }
        $data['paylist'] = $paylist;
		$data['is_shophui'] = $is_shophui;
		$data['shophuiinfo'] = $shophuiinfo;
		$data['shopinfo'] = $shopinfo;
		#print_r($shopinfo);
		  Mysite::$app->setdata($data); 
		
	}	
	 /* 
	 
	 id	name 规则名称	\
	 limittype 是否指定具体时间1否2指定星期3指定小时	
	 limitweek 具体时间：周几	
	 limittimes 限制每天具体时间	
	 mjlimitcost 每满费用金额	
	 limitzhekoucost 折扣限制金额	
	 controltype 规则类型：1赠，3折扣，2减费用	
	 controlcontent 限制内容填写赠品ID，折扣率，费用等大于0	
	 starttime 开始时间	
	 endtime 结束时间	
	 status 状态1有效 2无效	
	 shopid 店铺id	
	 
	 
		
-- 
-- 表的结构 `xiaozu_shophuiorder`
-- 

CREATE TABLE `xiaozu_shophuiorder` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `dno` varchar(25) NOT NULL COMMENT '买单号',
  `shopid` int(11) NOT NULL COMMENT '店铺ID',
  `shopname` varchar(255) NOT NULL COMMENT '店铺名称',
  `xfcost` decimal(10,2) NOT NULL COMMENT '消费金额',
  `yhcost` decimal(10,2) NOT NULL COMMENT '优惠金额',
  `sjcost` decimal(10,2) NOT NULL COMMENT '实际支付金额',
  `huiid` int(11) NOT NULL COMMENT '闪慧ID',
  `huiname` varchar(255) NOT NULL COMMENT '闪慧名称',
  `huitype` int(1) NOT NULL COMMENT '2是每满减 3是折扣',
  `huilimitcost` decimal(10,2) NOT NULL COMMENT '最低达到金额限制',
  `huicost` decimal(10,2) NOT NULL COMMENT '减金额',
  `paytype` int(11) NOT NULL COMMENT '1是微信支付',
  `paystatus` int(1) NOT NULL default '0' COMMENT '0是未付1是已付',
  `status` int(1) NOT NULL default '0' COMMENT '0是未完成是已完成',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  `completetime` int(11) NOT NULL default '0' COMMENT '支付买单完成时间',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


	 */
	 function makeshophuiorder(){
		 $uid = $this->member['uid'];
	
		 if( $uid > 0 ){
			 $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."' ");
			 $username = $memberinfo['username'];			
		 }
		 
		 $shopid = intval( IFilter::act(IReq::get('shopid')) ); 
		 $huiid =  intval(IFilter::act(IReq::get('huiid')) ); 
		 $xfcost =  IFilter::act(IReq::get('xfcost')) ;  //消费金额
//		 $buyorderphone = trim(IFilter::act(IReq::get('buyorderphone')));		 // 买单人 联系号
		 $yhcost =  0 ;  //优惠金额
		 $sjcost =  0 ;  //实际支付金额
		
		 $paytype =  intval(IFilter::act(IReq::get('paytype')) ); 
		  if(empty($xfcost)) $this->message('消费金额为空');
		  if($xfcost > 0){
			  
		  }else{
			   $this->message('消费金额不能为0');
		  }
//		  if(empty($buyorderphone)) $this->message('买单人联系电话不能为空');
//		  if(!(IValidate::suremobi($buyorderphone)))  $this->message('买单人联系电话错误');
		
		
		 
								 
		 
		 $shopone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."' "); // 店铺信息\
		 if( empty($shopone) ) $this->message("获取商户信息失败");
		 if($shopone['shoptype'] == 0){
			$shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop as a left join ".Mysite::$app->config['tablepre']."shopfast as b on a.id = b.shopid where a.id='".$shopid."' "); // 店铺信息
		 }else{
			$shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop as a left join ".Mysite::$app->config['tablepre']."shopmarket as b on a.id = b.shopid where a.id='".$shopid."' "); // 店铺信息
		 }
		if( $shopinfo['is_shophui'] == 1 ){
			 if( $huiid > 0 ){  
				$shophuiinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophui where id='".$huiid."' ");  //闪慧信息
				if(!empty($shophuiinfo) && $shophuiinfo['shopid'] == $shopid) {
					if( $shophuiinfo['controltype'] == 2 ){
						$checkcost = $shophuiinfo['mjlimitcost']; // 每满费用金额
						if( $xfcost >= $checkcost  ){
							$yhcost = floor($xfcost/$checkcost)*$shophuiinfo['controlcontent']; 
						} 
						$data['huilimitcost'] = $shophuiinfo['mjlimitcost'];
						
					}
					if( $shophuiinfo['controltype'] == 3 ){
						$checkcost = $shophuiinfo['limitzhekoucost']; // 打折金额限制
						if( $xfcost >= $checkcost  ){
							$yhcost = $xfcost*((100-$shophuiinfo['controlcontent'])/100);
							 
						}else{
							$this->messqge("消费金额未达到条件");
						}
						
						$data['huilimitcost'] = $shophuiinfo['limitzhekoucost'];
						
					} 
					$data['huiid'] = $shophuiinfo['id'];
					$data['huiname'] = $shophuiinfo['name'];
					$data['huitype'] = $shophuiinfo['controltype'];					
					$data['huicost'] = $shophuiinfo['controlcontent']; 
				}else{
					$data['huiid'] = '';
					$data['huiname'] ='';
					$data['huitype'] = '';
					$data['huilimitcost'] = '';
					$data['huicost'] = '';
				}
			 }
		 }
		 if($yhcost > $xfcost){
			  $this->message('优惠金额不能大于消费金额');
		 }
		 $sjcost = $xfcost-$yhcost;
		 $data['uid'] = $uid;
		 $data['username'] = $username;
		 $data['dno'] = time().rand(1000,9999);
		 $data['shopid'] = $shopid;
		 $data['shopname'] = $shopinfo['shopname'];
		 $data['admin_id'] = $shopinfo['admin_id'];
		 $data['xfcost'] = $xfcost;
		 $data['buyorderphone'] = 0;
		 $data['yhcost'] = $yhcost;
		 $data['sjcost'] = $sjcost;
		 if( $shopinfo['is_shgift'] == 1 ){
			 $data['givejifen'] = floor($sjcost/$shopinfo['sendgift']);
		 }else{
			 $data['givejifen'] =  0;
		 }
			 
		 $data['paytype'] = $paytype;
		 $data['paystatus'] = 0;
		 $data['status'] = 0;
		 $data['addtime'] = time();
		 $data['completetime'] =0; 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'shophuiorder',$data);    
		$orderid = $this->mysql->insertid(); 
		$this->success($orderid);  
	 }
	function locationshop1111111111(){
	    ICookie::clear('myaddress');
	    $link = IUrl::creatUrl('wxsite/shoplist');
	    $this->message('',$link); 
	}
  
	  
	function getsearmap(){
		//{"error":false,"msg":[{"datatype":"3","parent_id":"31","datacode":"henanshengzhengzhoushierqiqumianfanglu","datacontent":"\u6cb3\u5357\u7701\u90d1\u5dde\u5e02\u4e8c\u4e03\u533a\u68c9\u7eba\u8def","lat":"34.76177","lng":"113.637355"},{"datatype":"3","parent_id":"35","datacode":"henanshengzhengzhoushijinshuiqubeicangzhongli1hao","datacontent":"\u6cb3\u5357\u7701\u90d1\u5dde\u5e02\u91d1\u6c34\u533a\u5317\u4ed3\u4e2d\u91cc1\u53f7","lat":"34.776774","lng":"113.654387"},{"datatype":"3","parent_id":"39","datacode":"henanshengzhengzhoushijinshuiqujinshuilu612hao","datacontent":"\u6cb3\u5357\u7701\u90d1\u5dde\u5e02\u91d1\u6c34\u533a\u91d1\u6c34\u8def6-12\u53f7","lat"
		 
	/* Array ( 
	[status] => 0 
	[message] => ok 
	[total] => 160 
	[results] => Array ( 
		[0] => Array ( 
			[name] => 郑州大学(新校区) 
			[location] => Array ( 
					[lat] => 34.822975 
					[lng] => 113.542962 
					) 
					[address] => 河南省郑州市高新区科学大道100号 
					[street_id] => fc7675243777ff844f776ea6 
					[telephone] => (0371)67783111 
					[detail] => 1 
					[uid] => fc7675243777ff844f776ea6 
						) 
		)
	)					 */
		 
		$searchvalue = trim(IFilter::act(IReq::get('searchvalue')));
		//http://api.map.baidu.com/place/v2/search?q=饭店&region=北京&output=json&ak=E4805d16520de693a3fe707cdc962045&
	   $content =   file_get_contents('http://api.map.baidu.com/place/v2/search?ak='.Mysite::$app->config['baidumapkey'].'&output=json&query='.$searchvalue.'&page_size=20&page_num=0&scope=1&region='.Mysite::$app->config['cityname']); 
	   $list = json_decode($content,true);
	   $backdata = array();
	   if($list['message'] == 'ok'){
		  
	   	  if($list['total'] >= 1){
	   	  	foreach($list['results'] as $key=>$value){
	   	  	    $temp['address']    =  $value['name'];
	   	  	    $temp['detaddress'] =  $value['address'];
	   	  	    $temp['lng'] =  $value['location']['lng'];
	   	  	    $temp['lat'] =  $value['location']['lat'];
	   	  	    $temp['parent_id'] = 0;
	   	  	    $backdata[] = $temp;
	   	  	}	   	     
	   	  }
	    
	   }
	  // print_r($list);
	  
	  
	   
	  $datas = json_encode($backdata);
	  echo 'showaddresslist('.$datas.')';
      exit; 
	  
	  
	   $this->success($backdata);

	}
	function forpwd(){
        session_start();
		$_SESSION['forgetpwd'] =time();
	}
	// 找回密码 验证手机号
	function forgetpwd(){
        if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
			// ajax 请求的处理方式 
		}else{ 
			echo 'noshow(\'验证失败\')';
			exit;
		};
		session_start();
		$checktime = 		$_SESSION['forgetpwd'];
		if(empty($checktime)){
			echo 'noshow(\'验证失败\')';
			exit;
		}
		$time = time()-300;
		if($checktime < $time){
			echo 'noshow(\'验证失败\')';
			exit;
		}
		 
	   $checkcode =    ICookie::get('regphonecode');
	   $checkphone =   ICookie::get('regphone');
	   $checktime =   ICookie::get('regtime'); 

      if(!empty($checkcode)){
      	  $backtime = $checktime-time();
		  	 if($backtime > 0){ 
		  	   echo 'showsend(\''.$checkphone.'\','.$backtime.')';
		  	   exit;
		  	 }
		  } 
    	if(!empty($this->member['uid'])){
    	  echo 'noshow(\'已登录\')';
    	  exit;
    	} 
      $phone = IFilter::act(IReq::get('phone')); 
	  if(empty($phone)){
		   echo 'noshow(\'请填写手机号\')';
    	  exit;
	  }
      if(!(IValidate::suremobi($phone))){
      		echo  'noshow(\'手机格式错误\')';
      		exit;
      }
	  $userinfoarray = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phone."' ");
	  if( count($userinfoarray) > 1 )
      {
        	 echo 'noshow(\'此手机号绑定多个用户！\')';
        	 exit;
      }
      $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phone."' ");
      if(empty($userinfo))
      {
        	 echo 'noshow(\'未找到此手机号的用户！\')';
        	 exit;
      } 
      $makecode =  mt_rand(10000,99999);
	   $contents =  '您的验证码为：'.$makecode; 
       $phonecode = new phonecode($this->mysql,2,$phone);
		 $phonecode->sendcode();
      
      ICookie::set('getbackphonecode',$makecode,90);
      ICookie::set('getbackphone',$phone,90);
      $longtime = time()+90;
      ICookie::set('regtime',$longtime,90);
      echo 'showsend(\''.$phone.'\',90,\''.$userinfo['uid'].'\')';
      exit; 
	}
	function fornextzhpwd(){  
		
		$pwdyzm = intval( IFilter::act(IReq::get('pwdyzm')) );
		$phoneyan =  IFilter::act(IReq::get('phone')) ;
	 
		$datauid = intval( IFilter::act(IReq::get('datauid')) );
		if(empty($phoneyan)) $this->message('请输入您的手机号');
		$userinfoarray = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phoneyan."' ");
		  if( count($userinfoarray) > 1 )
		  {
			$this->message('此手机号绑定多个用户！');
		  }
		  $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phoneyan."'   ");
		  if(empty($userinfo))
		  {
			$this->message('未找到此手机号的用户！');
		  } 
		
		if(empty($pwdyzm)) $this->message('请输入您收到的验证码');
		 if(!empty($phoneyan)){
		    $phonecode = new phonecode($this->mysql,2,$phone);
		    if($phonecode->sendcode()){
				
			}else{
				$this->message($phonecode->getError());
			}	   	     
		 }
		 
		  $lastuserinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$datauid."'   ");
		   if(empty($lastuserinfo))
		  {
			$this->message('未找到此用户！');
		  } 
		$this->success($lastuserinfo['uid']);
		
		
	} 
	function forgetnextpwd(){ //手机验证 
		
		$uid = intval( IFilter::act(IReq::get('id')) ) ;
		$data['uid'] = $uid;
		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'   ");
		 if(empty($userinfo))
		  {
			$this->message('获取用户信息失败！');
		  }
		Mysite::$app->setdata($data);
		 
	}
	function updatepwd(){   //手机验证 重新设置密码 
		
		$uid = intval( IFilter::act(IReq::get('uid')) ) ;
		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'   ");
		 if(empty($userinfo))
		  {
			$this->message('获取用户信息失败！');
		  }
		$pwd =  IFilter::act(IReq::get('pwd'))  ;
		$repwd =  IFilter::act(IReq::get('repwd'))  ;
		 if(!(IValidate::len($pwd,6,20)))
		{
			$this->message('member_pwdlen6to20');
		}  
    
		 if($pwd != $repwd){
		     $this->message('member_twopwdnoequale');
		 }
		 
		 $data['password'] = md5($pwd);
	     $data['temp_password'] = '';
		$this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$uid."'");
		$this->success('success');
	}
	function costlog(){  //余额明细
		$uid = $this->member['uid'];
		if(empty($uid)) $this->message('获取用户信息失败');
		$costloglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."memberlog where userid = ".$uid." and  type = 2  order by addtime desc ");
		$data['costloglist'] = $costloglist;
		 
		Mysite::$app->setdata($data);
	}

        
	function subbalancepay(){ // 余额在线支付页面
	
		$cost = round(IReq::get('cost'),2);
		$data['cost'] = $cost;
		
		
		
				 
		 /* 8.3新增 */
		 
		$rechargeone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."rechargecost where cost  =".$cost." order by orderid asc  ");
	
		$sendname = '';
		if( !empty($rechargeone) ){
			if($rechargeone['is_sendcost']  == 0 && $rechargeone['is_sendjuan'] == 1  ){
				$sendname .= '充值'.$rechargeone['cost'].'元赠送';
			}
			if($rechargeone['is_sendcost']  == 1 ){
				$sendname .= '充值'.$rechargeone['cost'].'元赠送'.$rechargeone['sendcost'].'元';
			}
			if($rechargeone['is_sendcost']  == 1 && $rechargeone['is_sendjuan'] == 1   ){
				$sendname .= '+';
			}
			if(  $rechargeone['is_sendjuan']  == 1  ){
				$sendname .= $rechargeone['sendjuancost'].'优惠券';
			}
		}
 		$data['sendname'] = $sendname;
		
		
		
		
	  $paylist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist where type = 0 or type=2  order by id asc limit 0,50");
		if(is_array($paylist)){
		  foreach($paylist as $key=>$value){
			    $paytypelist[$value['loginname']] = $value['logindesc'];
		  }
	  }
	  $data['paylist'] = $paylist;
 
  
	if( $this->checkbackinfo() ){

 	   $wxopenid = ICookie::get('wxopenid');  
	   $weixindir = hopedir.'/plug/pay/weixin/'; 
	   require_once $weixindir."lib/WxPay.Api.php";
       require_once $weixindir."WxPay.JsApiPay.php";
	   
	     $tools = new JsApiPay();
	   if(empty($wxopenid)){
		   
           $wxopenid = $tools->GetOpenid();
	   }

$dno = 'acount_'.$this->member['uid'];
$acountid = 'acount_'.time();
	 
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("支付订单".$dno);
$input->SetAttach($dno);
$input->SetOut_trade_no($acountid);
$input->SetTotal_fee($cost*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetTimeStamp(time());
$input->SetGoods_tag('在线充值');
$input->SetNotify_url(Mysite::$app->config['siteurl']."/plug/pay/weixin/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($wxopenid);
 //$url = Mysite::$app->config['siteurl'].'/plug/pay/weixin/jsapi.php';
$ordermm = WxPayApi::unifiedOrder($input);
// 
   
$jsApiParameters = $tools->GetJsApiParameters($ordermm);
		  
		$data['wxdata'] = $jsApiParameters;
		 
} 
	
		Mysite::$app->setdata($data);
	}
	function catefoods(){	//外卖点击分类ajax获取分类下的所有商品
	    $weekji = date('w');
		$shopid = intval( IFilter::act(IReq::get('shopid')) ) ;
		$parentid = intval( IFilter::act(IReq::get('parentid')) ) ;
		$curcateid = intval( IFilter::act(IReq::get('curcateid')) ) ;
		$shoptype = intval( IFilter::act(IReq::get('shoptype')) ) ;

		if($shoptype == 1 ){ 
			$cateinfo = $this->mysql->select_one("select shopid,name from ".Mysite::$app->config['tablepre']."marketcate where id = ".$curcateid." and shopid = ".$shopid." ");
			$shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$cateinfo['shopid']." ");
			$shopdet = $this->mysql->select_one("select is_orderbefore from ".Mysite::$app->config['tablepre']."shopmarket where shopid = ".$shopinfo['id']." ");
		}else{
			$cateinfo = $this->mysql->select_one("select shopid,name from ".Mysite::$app->config['tablepre']."goodstype where id = ".$curcateid." ");
			$shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$cateinfo['shopid']." ");
			$shopdet = $this->mysql->select_one("select is_orderbefore from ".Mysite::$app->config['tablepre']."shopfast where shopid = ".$shopinfo['id']." ");
		}

		$data['shopinfo'] = $shopinfo;
		// print_r($data['shopinfo']);
		$nowhour = date('H:i:s',time()); 
        $nowhour = strtotime($nowhour);

		$checkinfo = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopdet['is_orderbefore'],$nowhour); 
        $data['opentype'] = $checkinfo['opentype'];
		$type = intval( IFilter::act(IReq::get('type')) ) ;  // type 商品展示模板 1表示默认模板 
		$catefoodslist = array();
		$detaa = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where typeid='".$curcateid."' and is_waisong = 1 and shopid = ".$shopid." and    FIND_IN_SET( ".$weekji." , `weeks` )  and is_live = 1  order by good_order asc ");
 		
			 foreach ( $detaa as $keyq=>$valq ){
				if($valq['is_cx'] == 1){
				//测算促销 重新设置金额
					$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
					$newdata = getgoodscx($valq['cost'],$cxdata);
					
					$valq['zhekou'] = $newdata['zhekou'];
					$valq['is_cx'] = $newdata['is_cx'];
					$valq['cost'] = $newdata['cost'];
					$valq['cxnum'] = $cxdata['cxnum'];
				}
                if($valq['have_det'] == 1){
					$price=array(); 
					$det = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$valq['id']."  ");
					foreach ( $det as $keyd=>$vald ){
					    $price[] = $vald['cost'];
					}	
					$valq['cost'] = min($price);//获取多规格商品中价格最小的价格作为展示价格
				}
				$valq['sellcount'] = $valq['sellcount']+$valq['virtualsellcount'];				
				$catefoodslist[] =$valq; 
			}		
		$data['cateinfo'] = $cateinfo;
		$data['catefoodslist'] = $catefoodslist;
		
		Mysite::$app->setdata($data);
	}
	function mkcatefoods(){	//超市点击分类ajax获取分类下的所有商品
	    $weekji = date('w');		 
		$shopid = intval( IFilter::act(IReq::get('shopid')) ) ;
		$curcateid = intval( IFilter::act(IReq::get('curcateid')) ) ;
		$shoptype = intval( IFilter::act(IReq::get('shoptype')) ) ;
		$type = intval( IFilter::act(IReq::get('type')) ) ;  // type 商品展示模板 1表示默认模板 
		if($shoptype == 1 ){
			$parentid = intval( IFilter::act(IReq::get('parentid')) ) ;
		 
 			if(!empty($curcateid)){				
				$where = " and  id = ".$curcateid."   ";
			}else{
				$where = "";				
			}
			$soncatelist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."marketcate where shopid = ".$shopid."  ".$where." order by orderid asc ");	
			
				foreach($soncatelist as $key=>$value){
					$temparray = array();
					$detaa = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where typeid='".$value['id']."'  and shopid = ".$shopid."  and    FIND_IN_SET( ".$weekji." , `weeks` ) and is_live = 1    order by good_order asc ");
					
						foreach ( $detaa as $keyq=>$valq ){
							if($valq['is_cx'] == 1){
							//测算促销 重新设置金额
								$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
								$newdata = getgoodscx($valq['cost'],$cxdata);
								
								$valq['zhekou'] = $newdata['zhekou'];
								$valq['is_cx'] = $newdata['is_cx'];
								$valq['cost'] = $newdata['cost'];
								$valq['cxnum'] = $cxdata['cxnum'];
							}
							if($valq['have_det'] == 1){
								$price = array();
							$det = $this->mysql->getarr("select cost from ".Mysite::$app->config['tablepre']."product where goodsid=".$valq['id']."  ");
							foreach ( $det as $keyd=>$vald ){
								$price[] = $vald['cost'];
							}	
							$valq['cost'] = min($price);//获取多规格商品中价格最小的价格作为展示价格
							}
							$valq['sellcount'] = $valq['sellcount']+$valq['virtualsellcount'];
							$temparray[] =$valq; 
							
							$value['det'] = $temparray;
						}
						$catefoodslist[] = $value;
					
					 
				}
			
	
		 	$parentcateinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."marketcate where id = ".$parentid." ");
			#print_r($catefoodslist);
		 	$cateinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."marketcate where id = ".$curcateid." ");
		 	$shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$parentcateinfo['shopid']." ");
		 	$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where shopid = ".$shopinfo['id']." ");
		} 
		$data['shopinfo'] = $shopinfo;
		$nowhour = date('H:i:s',time()); 
        $nowhour = strtotime($nowhour);
		$checkinfo = $this->shopIsopen($shopinfo['is_open'],$shopinfo['starttime'],$shopdet['is_orderbefore'],$nowhour); 
        $data['opentype'] = $checkinfo['opentype'];
	 
	
		
		$data['cateinfo'] = $cateinfo;
		$data['shopdet'] = $shopdet;
	 
		$data['catefoodslist'] = $catefoodslist;
  	 #print_r($catefoodslist);
		Mysite::$app->setdata($data);
	}
	function search(){  //搜索 商家和商品页面
		$searchname = IFilter::act(IReq::get('searchname'))   ;
		$data['searchname'] = $searchname;
		$uid = $this->member['uid'];
        $searchArr=ICookie::get('searchlist');
		if($uid > 0){
			$searchloglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."searchlog where uid = ".$uid." order by searchtime desc limit 0,10 ");
            //删除两个数组中重复的数据
            if(!empty($searchArr)){
                foreach($searchloglist as $k => $v){
                    foreach($searchArr as $key => $val){
                        if($v['searchval'] == $val['searchval']){
                            unset($searchArr[$key]);
                        }
                    }
                }
                $searchloglist = array_merge($searchloglist,$searchArr);
            }
			$sort = array(
				'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
				'searchtime'     => 'sort',       //排序字段
			);
			$arrSort = array();
			foreach($searchloglist AS $uniqid => $row){
				foreach($row AS $key=>$value){
					$arrSort[$key][$uniqid] = $value;
				}
			}
			if($sort['direction']){
				array_multisort($arrSort[$sort['field']], constant($sort['direction']), $searchloglist);
			}
			$data['searchloglist'] = $searchloglist;
		}else{
            if(!empty($searchArr)){
                $data['searchloglist'] = $searchArr;
            }
        }
		Mysite::$app->setdata($data);
	}
	function searchresult(){   //ajax搜索 商家和商品结果
		$searchname = IFilter::act(IReq::get('searchname'))   ;
		$uid = $this->member['uid'];
        $searchlist = ICookie::get('searchlist');
        if(empty($searchlist)){
            $searchlist = array();
            array_unshift($searchlist,array('searchval'=>$searchname,'searchtime'=>time()));
        }else{
            foreach($searchlist as  $val){
                $temp[]=$val['searchval'];
            }
            if(!in_array($searchname,$temp)){
                array_unshift($searchlist,array('searchval'=>$searchname,'searchtime'=>time()));
            }
        }
        ICookie::set("searchlist",$searchlist);
		
		
		if($uid > 0){
			$sdata['uid'] = $uid;
			$sdata['searchval'] = $searchname;
			$sdata['searchtime'] = time();
			$checksearch = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."searchlog where searchval = '".$searchname."' ");
		 
			if(empty($checksearch)){
				 $this->mysql->insert(Mysite::$app->config['tablepre'].'searchlog',$sdata);   // 插入用户 搜索记录 
			}
		} 
			
		$cxsignlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
		$cxarray  =  array();
		foreach($cxsignlist as $key=>$value){
		   $cxarray[$value['id']] = $value['imgurl'];
		}
				

        /* 搜索店铺 结果  */
		$where = '';  
		$shopsearch = IFilter::act(IReq::get('searchname')); 
		$shopsearch		 = urldecode($shopsearch); 
		if(!empty($shopsearch)) $where=" and shopname like '%".$shopsearch."%' "; 

		$lng = 0;
		$lat = 0;

		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat');
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;
        #$where = empty($where)?'   and  SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ': $where.' and SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ';  	         
		$lng = trim($lng);
		$lat = trim($lat);
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;

		/*获取店铺*/
		$where = Mysite::$app->config['plateshopid'] > 0? $where.' and  id != '.Mysite::$app->config['plateshopid'] .' ':$where;
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page'))); 

		$tempdd = array();
		 
		$tempdd[] =   $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and (   admin_id='".$this->CITY_ID."'  or  admin_id = 0 )     and endtime > ".time()."  ".$where." ");		
		$platpsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$this->CITY_ID."' "); 		 
		$nowhour = date('H:i:s',time()); 
		$nowhour = strtotime($nowhour);
		$templist = array();
		$cxclass = new sellrule();  
		
		
		//cattype = ".$values['shoptype']."
		$templist111 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where    parent_id = 0    order by orderid asc limit 0,1000"); 
		$attra = array();  
		foreach($templist111 as $key=>$vall){
			if(isset($attra[$vall['cattype']])){
				if($vall['type'] == 'input'){
					$attra[$vall['cattype']]['input'] =  $attra[$vall['cattype']]['input'] > 0?$attra[$vall['cattype']]['input']:$vall['id'];
				}elseif($vall['type'] == 'img'){
					$attra[$vall['cattype']]['img'] =  $attra[$vall['cattype']]['img'] > 0?$attra[$vall['cattype']]['img']:$vall['id'];
				}elseif($vall['type'] == 'checkbox'){
					$attra[$vall['cattype']]['checkbox'] =  $attra[$vall['cattype']]['checkbox'] > 0?$attra[$vall['cattype']]['checkbox']:$vall['id'];
				}
			}else{
				if($vall['type'] == 'input'){
					$attra[$vall['cattype']]['input'] =  $vall['id'];
				}elseif($vall['type'] == 'img'){
					$attra[$vall['cattype']]['img'] =  $vall['id'];
				}elseif($vall['type'] == 'checkbox'){
					$attra[$vall['cattype']]['checkbox'] = $vall['id'];
				}
			}
		} 
		
		foreach($tempdd as $key=>$list){
			if(is_array($list)&& !empty($list)){
			    foreach($list as $keys=>$values){  
					// print_r($values['id']);
					if($values['id'] > 0){ 
						// print_r($values);
						if($values['shoptype'] == 1 ){
						    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$values['id']."   ");
						}else{
						    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$values['id']."   ");
						}
						if(empty($shopdet)){
							continue;
						}
						$values = array_merge($values,$shopdet);
						$values['shoplogo'] = empty($values['shoplogo'])? Mysite::$app->config['imgserver'].Mysite::$app->config['shoplogo']:Mysite::$app->config['imgserver'].$values['shoplogo'];
						$checkinfo = $this->shopIsopen($values['is_open'],$values['starttime'],$values['is_orderbefore'],$nowhour); 
						$values['opentype'] = $checkinfo['opentype'];
						$values['newstartime']  =  $checkinfo['newstartime'];
 						
						$attrdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  `cattype` = '".$values['shoptype']."' and `shopid` = '".$values['id']."' ");
						
						//$cxclass->setdata($values['id'],1000,$values['shoptype']); 
						$checkps = 	 $this->pscost($values,$lat,$lng); 
						$values['pscost'] = $checkps['pscost'];
						$mi = $this->GetDistance($lat,$lng, $values['lat'],$values['lng'], 1); 
						$tempmi = $mi;
						$julicc = $mi/1000;
						$mi = $mi > 1000? round($mi/1000,2).'km':$mi.'m';
						$values['juli'] = 		$mi;
						 
						//判断店铺是否超出配送区域
						if($julicc > $values['pradiusa']){
						    $values['outrange'] = 1;
						}else{
						    $values['outrange'] = 0;
						}	
						 
						$shopcounts = $this->mysql->select_one( "select count(id) as shuliang  from ".Mysite::$app->config['tablepre']."order	 where status = 3 and  shopid = ".$values['id']."" );
						if(empty( $shopcounts['shuliang']  )){
						    $values['ordercount'] = 0;
						}else{
						    $values['ordercount']  = $shopcounts['shuliang'];
						}                                                 
						$values['ordercount']  = $values['ordercount']+$values['virtualsellcounts'];
						$time = time();
					    $d = date("w") ==0?7:date("w");		
						$cxinfo = $this->mysql->getarr("select id,name,imgurl,controltype,parentid from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$values['id'].",shopid) and status = 1 and ( limittype =1 or ( limittype = 2 and  FIND_IN_SET(".$d.",limittime) ) or ( limittype = 3 and endtime > ".$time." and starttime < ".$time.")) order by id desc  ");
						$values['cxlist'] = $cxinfo;
						// print_r($cxinfo);
						// echo 'a';
						/* 店铺星级计算 */
						$zongpoint = $values['point'];
						$zongpointcount = $values['pointcount'];
						if($zongpointcount != 0 ){
						     $shopstart = intval( round($zongpoint/$zongpointcount) );
						}else{
						     $shopstart= 0;
						}
						$values['ztimg'] = '';
						if($platpsinfo['is_allow_ziti'] == 1 && $values['is_ziti'] == 1 ){
							$values['ztimg'] = getImgQuanDir(Mysite::$app->config['ztimg']);							
						}
						$values['point'] = 	$shopstart;						
						$values['attrdet'] = array();
						foreach($attrdet as $k=>$v){
							if(isset($attra[$values['shoptype']]['input']) && $v['firstattr'] == $attra[$values['shoptype']]['input']){
							    $values['attrdet']['input'] = $v['value'];
							}elseif(isset($attra[$values['shoptype']]['img']) && $v['firstattr'] == $attra[$values['shoptype']]['img']){
							    $values['attrdet']['img'][] = $v['value'];
							}elseif(isset($attra[$values['shoptype']]['checkbox']) && $v['firstattr'] == $attra[$values['shoptype']]['checkbox']){
							    $values['attrdet']['checkbox'][] = $v['value'];
							} 
						}
						$templist[] = $values;						
					}
				} 
			}

		}
		// echo 'mmm';
		$shop1 = array();
		$shop2 = array();
		foreach($templist as $sk=>$sv){
			if($sv['outrange'] == 0){
				$shop1[] = $sv;
			}else{
				$shop2[] = $sv;
			}
		}
		
		$data['shopsearchlist'] = array_merge($shop1,$shop2); 
		#print_r($data['shopsearchlist']);exit;
		/* 搜索商品列表 */
		$weekji = date('w');
		$goodwhere = '';  
		$goodssearch = IFilter::act(IReq::get('searchname')); 
		$goodssearch = urldecode($goodssearch); 
		if(!empty($goodssearch)) $goodlistwhere=" and name like '%".$goodssearch."%' "; 
		 
		$lng = ICookie::get('lng');
		$lat = ICookie::get('lat'); 
		#$goodwhere = empty($goodwhere)?'   and  SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ': $goodwhere.' and SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ';
		$lng = trim($lng);
		$lat = trim($lat);
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;
		$templist11 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where  cattype = 0 and parent_id = 0 and is_main =1  order by orderid asc limit 0,1000"); 
		$attra['input'] = 0;
		$attra['img'] = 0;
		$attra['checkbox'] = 0; 
		foreach($templist11 as $key=>$value){
			if($value['type'] == 'input'){
			    $attra['input'] =  $attra['input'] > 0?$attra['input']:$value['id'];
			}elseif($value['type'] == 'img'){
			    $attra['img'] =  $attra['img'] > 0?$attra['img']:$value['id'];
			}elseif($value['type'] == 'checkbox'){
			    $attra['checkbox'] =  $attra['checkbox'] > 0?$attra['checkbox']:$value['id'];
			}
		} 
		/*获取店铺*/
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')));
		$goodwhere = Mysite::$app->config['plateshopid'] > 0? $goodwhere.' and  id != '.Mysite::$app->config['plateshopid'] .' ':$goodwhere;
		$list =   $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1     and endtime > ".time()."  ".$goodwhere." ");
		$nowhour = date('H:i:s',time()); 
		$nowhour = strtotime($nowhour);
		$goodssearchlist = array();
		$cxclass = new sellrule();

		if(is_array($list)){
			foreach($list as $keys=>$vatt){  

				if($vatt['id'] > 0){
					$detaa = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where shopid='".$vatt['id']."'  and shoptype = ".$vatt['shoptype']."  and    FIND_IN_SET( ".$weekji." , `weeks` )  ".$goodlistwhere."   order by good_order asc ");
					if(!empty($detaa)){						
						foreach ( $detaa as $keyq=>$valq ){
							$mi = $this->GetDistance($lat,$lng, $vatt['lat'],$vatt['lng'], 1);  
							$julicc = $mi/1000; 
							//判断店铺是否超出配送区域
							if($julicc > $vatt['pradiusa']){
								$values['outrange'] = 1;
							}else{
								$values['outrange'] = 0;
							}	
							
							
							 	
							if($valq['is_cx'] == 1){
								//测算促销 重新设置金额
								$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
								$newdata = getgoodscx($valq['cost'],$cxdata);

								$valq['zhekou'] = $newdata['zhekou'];
								$valq['is_cx'] = $newdata['is_cx'];
								$valq['cost'] = $newdata['cost'];
								$valq['cxnum'] = $cxdata['cxnum'];
							}
                            $valq['sellcount'] = $valq['sellcount'] + $valq['virtualsellcount'];

							if( $shoptype == 1 ){
							    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$valq['shopid']."   ");
							}else{
							    $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$valq['shopid']."   ");
							}
							$checkinfo = $this->shopIsopen($vatt['is_open'],$vatt['starttime'],$shopdet['is_orderbefore'],$nowhour); 
							$valq['opentype'] = $checkinfo['opentype'];
							$temparray[] =$valq; 
							$vakk = $temparray;
						}
					}	
					$goodssearchlist = $vakk;
				}
			} 
		}

		$goods1 = array();
		$goods2 = array();		 
        foreach($goodssearchlist as $gk=>$gv){
			if($gv['outrange'] == 0){
				$goods1[] = $gv;
			}else{
				$goods2[] = $gv;
			}
		}
		$data['goodssearchlist']  = array_merge($goods1,$goods2);	
		Mysite::$app->setdata($data);
	}
	
	function qkmemsearchlog(){  //清空会员搜索记录
		
		$uid = $this->member['uid'];
		if($uid > 0){
			 $this->mysql->delete(Mysite::$app->config['tablepre'].'searchlog',"uid ='".$uid."'");
            ICookie::set('searchlist',array());
			 $this->success('success');
		}else{
            ICookie::set('searchlist',array());
			$this->success('success');
		}
		
	}
	function pthelpme(){  // 跑腿----帮我送/买
	    $id = intval(IReq::get('id'));
        if (!empty($id)) {
            $title = $this->mysql->select_one(" select * from " . Mysite::$app->config['tablepre'] . "helpbuy where id = " . $id . " ");
            $bqlist = $this->mysql->getarr(" select * from " . Mysite::$app->config['tablepre'] . "helpbuybq where parent_id = " . $id . " order by id asc");
        }
        $data['goods'] = IReq::get('goods');
        $data['title'] = $title;
        $data['bqlist'] = $bqlist;
        $data['movegoodsname'] = IReq::get('movegoods');
        $data['movegoodscost'] = IReq::get('cost');
        $data['movegoodsweight'] = IReq::get('weight');

			 $lng = ICookie::get('lng');
			 $lat = ICookie::get('lat');
			 $mapname = ICookie::get('mapname');
			 $city_id = $this->CITY_ID;
			 $city_name = $this->CITY_NAME;
	 
		if( !empty($city_id) && !empty($city_name) ){
			$where = " where  cityid = '".$city_id."' ";
		}
		
		$data['lng'] = $lng;
		$data['lat'] = $lat;
		$data['mapname'] = $mapname;
	
 		$pttype = intval(IReq::get('pttype')); 
		$data['pttype'] = $pttype;  // 1为帮我送  2为帮我买
		
		$data['ptsetinfo'] = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset ".$where."  "); 
		 
		  $postdate =  $data['ptsetinfo']['postdate'];
		  $befortime = $data['ptsetinfo']['pt_orderday'];
		  $is_ptorderbefore = $data['ptsetinfo']['is_ptorderbefore'];
		  if($is_ptorderbefore==0)$befortime=0;
 $nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($postdate)?unserialize($postdate):array();
		$data['pstimelist'] = array();
		$checknow = time();
		
		 
		$whilestatic = $befortime;
		$nowwhiltcheck = 0;
		while($whilestatic >= $nowwhiltcheck){
		    $startwhil = $nowwhiltcheck*86400;
			foreach($timelist as $key=>$value){
				$stime = $startwhil+$nowhout+$value['s'];
				$etime = $startwhil+$nowhout+$value['e'];
				if($etime  >= $checknow){
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
		//$data['endtime']=date('Y-m-d H:i:s',$data['endtime']);
		//$data['checknow']=date('Y-m-d H:i:s',$data['checknow']);
		#print_r($data);
		
		Mysite::$app->setdata($data);
	}
	function pthelpme85(){  // 跑腿----帮我送/买
	
			 $lng = ICookie::get('lng');
			 $lat = ICookie::get('lat');
			 $mapname = ICookie::get('mapname');
			 $city_id = $this->CITY_ID;
			 $city_name = $this->CITY_NAME;
	 
		if( !empty($city_id) && !empty($city_name) ){
			$where = " where  cityid = '".$city_id."' ";
		}
		
		$data['lng'] = $lng;
		$data['lat'] = $lat;
		$data['mapname'] = $mapname;
	
 		$pttype = intval(IReq::get('pttype')); 
		$data['pttype'] = $pttype;  // 1为帮我送  2为帮我买
		
		$data['ptsetinfo'] = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset ".$where."  "); 
		 
		  $postdate =  $data['ptsetinfo']['postdate'];
		  $befortime = $data['ptsetinfo']['pt_orderday'];
		  
 $nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($postdate)?unserialize($postdate):array();
		$data['pstimelist'] = array();
		$checknow = time();
		
		 
		$whilestatic = $befortime;
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
	
	
function checkpaotuicost(){
	
		$adcode = intval(IReq::get('adcode'));
 		if( empty($adcode) ){
			$this->message("获取所在城市失败");
		}
 				$areacodeone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."areacode where id=".$adcode."  "); 
				if( !empty($areacodeone) ){
					$adcodeid = $areacodeone['id'];
					$pid = $areacodeone['pid'];
   					$areainfoone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."area where  adcode=".$adcodeid." or adcode=".$pid." order by adcode desc ");   
					if( empty($areainfoone) ){
						 $this->message("获取所在城市失败~");
 					}
					
				}
		 
		 
		$where = "  where   cityid = '".$areainfoone['adcode']."'  "; 
		$ptinfoset = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."paotuiset ".$where."   "); 
	 
	 
		if(empty($ptinfoset) ){
			
			$this->message("获取失败");
			
		}
	 

	 
		$postdate =  $ptinfoset['postdate'];
		$befortime = $ptinfoset['pt_orderday'];
		$is_ptorderbefore=$ptinfoset['is_ptorderbefore'];
		if($is_ptorderbefore == 0){
			$befortime=0;
		}
		$nowhout = strtotime(date('Y-m-d',time()));//当天最小linux 时间
		$timelist = !empty($postdate)?unserialize($postdate):array();		 
		$ptinfoset['pstimelist'] = array();
		$checknow = time();

		 
		$whilestatic = $befortime;//预定天数		 
		$nowwhiltcheck = 0;
		while($whilestatic >= $nowwhiltcheck){
		    $startwhil = $nowwhiltcheck*86400;
			foreach($timelist as $key=>$value){
				$stime = $startwhil+$nowhout+$value['s'];
				$etime = $startwhil+$nowhout+$value['e'];
				if($etime  >= $checknow){
					$tempt = array();
					$tempt['value'] = $value['s']+$startwhil;
					$tempt['s'] = date('H:i',$nowhout+$value['s']);
					$tempt['e'] = date('H:i',$nowhout+$value['e']);
					$tempt['d'] = date('Y-m-d',$stime);
					$tempt['i'] =  $value['i'];
					$tempt['cost'] =  isset($value['cost'])?$value['cost']:0;
					$tempt['name'] = $tempt['s'].'-'.$tempt['e'];
					$ptinfoset['pstimelist'][] = $tempt;
                    $ptinfoset['starttime'] = $nowhout+$value['s'];
					$ptinfoset['endtime'] = $nowhout+$value['e'];
					$ptinfoset['checknow'] = $checknow;
				}
			}
		 
			$nowwhiltcheck = $nowwhiltcheck+1;
		}
		  #print_r($ptinfoset);exit;
	   
		$this->success($ptinfoset);
		
		
	
	
}
	
	
	function specialpage(){ //专题页
		$id = intval(IReq::get('id'));
		$data['id'] = $id;
		$ztyinfo = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."specialpage where id = ".$id."  ");
        #print_r($ztyinfo);exit;
		$data['ztyinfo'] = $ztyinfo;
		$data['addressname'] = ICookie::get('addressname');
		 
		/* 
								<{if $items['cx_type'] == 6}>【外卖订餐】<{/if}>
								<{if $items['cx_type'] == 7}>【在线超市】<{/if}>
								<{if $items['cx_type'] == 8}>【预定点菜】<{/if}>
								<{if $items['cx_type'] == 9}>【跑腿服务】<{/if}>
								<{if $items['cx_type'] == 9}>【商家入驻】<{/if}>
		*/
		if( $ztyinfo['showtype'] == 2 && $ztyinfo['is_custom'] == 0 ){
			$zdyurl = $ztyinfo['zdylink'];
			$link = IUrl::creatUrl($zdyurl); 
			$this->message('',$link);
		}else{	
			if($ztyinfo['is_custom'] == 1 && $ztyinfo['showtype'] == 0 && $ztyinfo['cx_type'] > 5 ){
                if($ztyinfo['listids']=='marketshop'){
                    $link = IUrl::creatUrl('wxsite/marketshop');
                    $this->message('',$link);
                }
                //8.7专题页修改
                if($ztyinfo['cx_type'] == 6){
                    $link = IUrl::creatUrl("wxsite/waimai/typeid/{$ztyinfo['listids']}");
                }
                //8.7专题页修改
                if($ztyinfo['cx_type'] == 7){
                    $link = IUrl::creatUrl("wxsite/marketlist/typeid/{$ztyinfo['listids']}");
                }
//
//				if($ztyinfo['cx_type'] == 6){
//					$link = IUrl::creatUrl('wxsite/waimai');
//				}
//				if($ztyinfo['cx_type'] == 7){
//					$link = IUrl::creatUrl('wxsite/marketlist');
//				}
				if($ztyinfo['cx_type'] == 8){
					$link = IUrl::creatUrl('wxsite/dingtai'); 
				}
				if($ztyinfo['cx_type'] == 9){
					$link = IUrl::creatUrl('wxsite/paotui'); 
				}
				if($ztyinfo['cx_type'] == 10){
					$link = IUrl::creatUrl('wxsite/shopSettled'); 
				}
                //8.7专题页修改
                if($ztyinfo['cx_type'] == 11){
                    $link = IUrl::creatUrl('wxsite/memsharej');
                }
				$this->message('',$link);
			}
		}
		$speciallist = $this->getztyshowlist($ztyinfo['is_custom'],$ztyinfo['showtype'],$ztyinfo['cx_type'],$ztyinfo['listids']);
		
		#print_r($speciallist);
		 
		$data['speciallist'] = $speciallist;
		Mysite::$app->setdata($data);
	}
	
 function specialpagelistdata(){
		$id = intval(IReq::get('id'));
		 $latx  = trim(IReq::get('lat'));
		 $latx  = empty($latx)?0:$latx;
		 $lngx  = trim(IReq::get('lng'));
		 $lngx  = empty($lngx)?0:$lngx;
		 $ctidx  = trim(IReq::get('ctidx'));
		 $ctidx  = empty($ctidx)?0:$ctidx;
		 $page  = intval(IReq::get('page'));
		$ztyinfo = $this->mysql->select_one(" select * from ".Mysite::$app->config['tablepre']."specialpage where id = ".$id."  ");
		$data['ztyinfo'] = $ztyinfo;
		$data['addressname'] = ICookie::get('addressname');
		
        $speciallist = $this->getztyshowlist($ztyinfo['is_custom'],$ztyinfo['showtype'],$ztyinfo['cx_type'],$ztyinfo['listids'],$latx,$lngx,$ctidx,$page);
		 
		 $data['speciallist'] = $speciallist; 
 
	$datas = json_encode($data['speciallist']);
	if($ztyinfo['showtype'] == 0 ){
		echo 'showmorespeciallist('.$datas.')';
	}
	if($ztyinfo['showtype'] == 1 ){
		echo 'showgoodsspeciallist('.$datas.')';
	}
	
		exit; 
	    $this->success($data);
		
/* 	 print_r($backdata);
		exit;  */
		$this->success($backdata);
	}
	
	
        function updatepwdd(){
            $phone = ICookie::get('phone');
            $userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phone."' order by uid desc   ");
            if(empty($userinfo)){
                $this->success('用户不存在');
            }
         $data['userinfo'] =$userinfo;  
	 Mysite::$app->setdata($data);

        }
        //快捷登录加密码
        	function floginaddpwd(){   //手机验证 重新设置密码 
		
		$uid = intval( IFilter::act(IReq::get('uid')) ) ;
                if($uid < 0) $this->message('用户不存在！');

		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$uid."'   ");
		 if(empty($userinfo))
		  {
			$this->message('获取用户信息失败！');
		  }
		$pwd =  IFilter::act(IReq::get('password'))  ;
		 if(!(IValidate::len($pwd,6,20)))
		{
			$this->message('member_pwdlen6to20');
		}  
    

		 $data['password'] = md5($pwd);
	     $data['temp_password'] = '';
		$this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$uid."'");
		$this->success('success');
	}
	
	
	
	
	
	/* 
	专题页管理
	xiaozu_specialpage  专题页活动表
		id
		name	名称
		indeximg	首页显示图片	
		specialimg	专题页头部显示图片	
		color	专题页背景主色调	
***		is_custom 	是否是自定义	默认为1固定的  0为自定义的
***		showtype	针对的是商品还是店铺  默认0为店铺 1为商品
***		cx_type		如果是商品1为折扣  如果是店铺 1为推荐店铺  2为免减商家 3为打折商家 4免配送费  
***		listids		如果是自定义的话 所对应的店铺id集或者商品id集
		ruleintro	规则说明
		is_show		是否展示 默认1展示 0不展示
		orderid		排序
		addtime		添加时间 
	*/
	function getztyshowlist($is_custom,$showtype,$cx_type,$listids,$latx=0,$lngx=0,$ctidx=0,$page=0){

			 $page = $page*10;
			$cxsignlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 1000");
			$cxarray  =  array();
			foreach($cxsignlist as $key=>$value){
			   $cxarray[$value['id']] = $value['imgurl'];
			}
			$weekji = date('w');
			$nowhour = date('H:i:s',time()); 
			 $nowhour = strtotime($nowhour);
			  $templist = array();
			   $cxclass = new sellrule(); 
				
				$where = '';  
	  
				$lng = 0;
				$lat = 0;
		
				$lng = ICookie::get('lng');
				$lat = ICookie::get('lat');
				$lng = empty($lng)?0:$lng;
				$lat =empty($lat)?0:$lat;
				$lat = $latx != 0?$latx:$lat;
				$lng = $lngx != 0?$lngx:$lng;
				$this->CITY_ID = $ctidx != 0?$ctidx:$this->CITY_ID;
	  $where = empty($where)?'   and  SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ': $where.' and SQRT((`lat` -'.$lat.') * (`lat` -'.$lat.' ) + (`lng` -'.$lng.' ) * (`lng` -'.$lng.' )) < (`pradiusa`*0.01094-0.01094) ';
	 

	   $where = empty($where)?'    and (   admin_id='.$this->CITY_ID.'  or  admin_id = 0 )  ': $where.'    and (   admin_id='.$this->CITY_ID.'  or  admin_id = 0 )  ';
					 
		   // print_r($where);      	         
							 
		$lng = trim($lng);
		$lat = trim($lat);
		$lng = empty($lng)?0:$lng;
		$lat =empty($lat)?0:$lat;
			 
	 
		$pageinfo = new page();
		$pageinfo->setpage(intval(IReq::get('page')),1000000);   
		if($showtype == 0){    // 店铺 
			   if($is_custom == 0 ){   //自定义专题页情况下 
					if(!empty($listids)){
						$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where id in (".$listids.") ".$where."  order by sort asc limit {$page},10  ");
					}else{
						$list = array();
					}
			   }
			   if($is_custom == 1 ){  // 系统默认  cx_type 店铺 1为推荐店铺  2为满减商家 3为打折商家 4免配送费     //  private $rulecontrol = array('1'=>'赠送','2'=>'减费用','3'=>'折扣','4'=>免配送费);
				   
				   switch ($cx_type){ 
					case 1:
						//专题页8.7修改
						$list = $this->getdycxshops(1,$page);
		//				$ztywhere =  "  and  is_recom = 1   ";
		//				$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1 ".$ztywhere."  ".$where."   order by sort asc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
						break;
					case 2:
						$list = $this->getdycxshops(2,$page);
						break;
					case 3:
						$list = $this->getdycxshops(3,$page);
						break;
					case 4:
						$list = $this->getdycxshops(4,$page);
						break;
					case 5:
						$list = $this->getdycxshops(1,$page);
						break;
					default : 
						$list = array();
						break; 
					}  
			    }
						  if(is_array($list)){
								 foreach($list as $keys=>$values){
								 if($values['id'] > 0){
								 $templist111 = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype where cattype = ".$values['shoptype']." and  parent_id = 0    order by orderid asc limit 0,1000"); 
								$attra = array();
								 $attra['input'] = 0;
								 $attra['img'] = 0;
								 $attra['checkbox'] = 0; 
								 foreach($templist111 as $key=>$vall){
									  if($vall['type'] == 'input'){
									   $attra['input'] =  $attra['input'] > 0?$attra['input']:$vall['id'];
									  }elseif($vall['type'] == 'img'){
										 $attra['img'] =  $attra['img'] > 0?$attra['img']:$vall['id'];
									  }elseif($vall['type'] == 'checkbox'){
										$attra['checkbox'] =  $attra['checkbox'] > 0?$attra['checkbox']:$vall['id'];
									  }
								 }
												/* 店铺星级计算 */
											$zongpoint = $values['point'];
											$zongpointcount = $values['pointcount'];
											if($zongpointcount != 0 ){
												$values['shopstart'] = intval( round($zongpoint/$zongpointcount) );
											}else{
												$values['shopstart'] = 0;
											}
				 
										if($values['shoptype'] == 1 ){
											$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$values['id']."   ");
										}else{
											$shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$values['id']."   ");
										}
										if(!empty($shopdet)){ 
										$values = array_merge($values,$shopdet);
										}
										$values['shoplogo'] = empty($values['shoplogo'])? Mysite::$app->config['imgserver'].Mysite::$app->config['shoplogo']:Mysite::$app->config['imgserver'].$values['shoplogo'];
										  $checkinfo = $this->shopIsopen($values['is_open'],$values['starttime'],$values['is_orderbefore'],$nowhour); 
										  $values['opentype'] = $checkinfo['opentype'];
										  $values['newstartime']  =  $checkinfo['newstartime'];  
										  $attrdet = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shopattr where  cattype = ".$values['shoptype']." and shopid = ".$values['id']."");
										  $cxclass->setdata($values['id'],1000,$values['shoptype']); 
										  
										  
										  $checkps = 	 $this->pscost($values,$lng,$lat); 
										  $values['pscost'] = $checkps['pscost'];
										  $mi = $this->GetDistance($lat,$lng, $values['lat'],$values['lng'], 1); 
										  $tempmi = $mi;
										  $mi = $mi > 1000? round($mi/1000,2).'km':$mi.'m';
										  $values['juli'] = 		$mi;
										  $values['ordercount']  = $values['virtualsellcounts'] + $values['ordercount'];                                                                                               
										  $d = date("w") ==0?7:date("w");
										  $time = time();
                                          $cxinfo = $this->mysql->getarr("select name,id,signid,imgurl  from ".Mysite::$app->config['tablepre']."rule  where  FIND_IN_SET(".$values['id'].",shopid)  and status = 1 and ( limittype =1 or ( limittype = 2 and  FIND_IN_SET(".$d.",limittime) ) or ( limittype = 3 and endtime > ".$time." and starttime < ".$time.")) order by id desc ");
										  $values['cxlist'] = $cxinfo;
										  
											/* 店铺星级计算 */
										$zongpoint = $values['point'];
										$zongpointcount = $values['pointcount'];
										if($zongpointcount != 0 ){
											$shopstart = intval( round($zongpoint/$zongpointcount) );
										}else{
											$shopstart= 0;
										}
											$values['point'] = 	$shopstart;	
											 $values['attrdet'] = array();
										  foreach($attrdet as $k=>$v){
											   if($v['firstattr'] == $attra['input']){
												$values['attrdet']['input'] = $v['value'];
											   }elseif($v['firstattr'] == $attra['img']){
												$values['attrdet']['img'][] = $v['value'];
											   }elseif($v['firstattr'] == $attra['checkbox']){
													$values['attrdet']['checkbox'][] = $v['value'];
											   } 
										  }
								 
										  $templist[] = $values;
									 }
							   } 
							}
					
			   
			   
		}	


		if($showtype == 1){    // 加载商品

			 if($is_custom == 0 ){   //自定义专题页情况下
					
					if(!empty($listids)){
						$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where  id in (".$listids.")  and  shopid in (select id from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  ".$where."   order by id desc )   and    FIND_IN_SET( ".$weekji." , `weeks` )   order by good_order asc ");
						// print_r($list);
					}else{
						$list = array();
					}

			   }
			   if($is_custom == 1 ){  // 系统默认  cx_type 商品1为折扣
				   switch ($cx_type){ 
					case 1:
						$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods where  shopid in (select id from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  ".$where."   order by id desc )   and    FIND_IN_SET( ".$weekji." , `weeks` )  and is_cx =1  order by good_order asc  ");
						break;
					default : 
						$list = array();
						break; 
					}  
			   }

					if(is_array($list)){

							//limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."
							if(!empty($list)){
								foreach ( $list as $keyq=>$valq ){
									if($valq['is_cx'] == 1){
									//测算促销 重新设置金额
										$cxdata = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goodscx where goodsid=".$valq['id']."  ");
										$newdata = getgoodscx($valq['cost'],$cxdata);
										$valq['zhekou'] = $newdata['zhekou'];
										$valq['is_cx'] = $newdata['is_cx'];
										$valq['cost'] = round($newdata['cost'],2);
										$valq['cxnum'] = $cxdata['cxnum'];
									}else{
                                        $valq['cxnum'] = 0;
                                    }
									
									$vatt = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$valq['shopid']."  ");
									if( $vatt['shoptype'] == 1 ){
										 $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$valq['shopid']."   ");
									}else{
										  $shopdet = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$valq['shopid']."   ");
									}
									$checkinfo = $this->shopIsopen($vatt['is_open'],$vatt['starttime'],$shopdet['is_orderbefore'],$nowhour);
									$valq['opentype'] = $checkinfo['opentype'];
									$valq['shopname'] = $vatt['shopname'];
									$valq['goodattrdefault'] = $vatt['goodattrdefault'];
									$valq['limitcost'] = $shopdet['limitcost'];
                                    $valq['img'] = !empty($valq['img'])? $valq['img']:Mysite::$app->config['goodlogo'];
                                                                       
									if($valq['limitcost'] == null || $valq['limitcost'] == undefined){
										$valq['limitcost'] == 0;
									}
									$valq['cost'] = $this->formatcost($valq['cost'],2);
								//	print_r($valq['is_cx']);
									if($is_custom == 1){
										if($valq['is_cx'] == 1){
											$newtemplist[]  = $valq;
										}
									}else{
										$newtemplist[]  = $valq;
									}
								}
                               #print_r($newtemplist);
								$templist = array();
									//重新分页
								    if($page==0){
										$newpage = ($page+1)*10;
									}else{
										$newpage = $page+10;
									}
									#print_r($newpage);
									foreach($newtemplist as $k=>$v){ //
										if($newpage>$k && $newpage-$k<=10){
											$templist[]=$v;
										}
									}
							 }
				}
			}
//		$templist = array();
		#print_r($templist);exit;
		$data = $templist;
		return $data;
	}
	
	 

function getdycxshops($type,$page){  // 获取对应促销类型的商家

//			    $pageinfo = new page();
//			    $pageinfo->setpage(intval(IReq::get('page')),10);

	  //  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."
				$d = (date("w") ==0) ?7:date("w") ; 
				$cxlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."rule where  status = 1 and controltype = ".$type." and ( limittype = 1 or ( limittype = 2 and FIND_IN_SET(".$d.",limittime))  or ( limittype = 3 and endtime > ".time()." and starttime < ".time().")) ");
				$shopids = array();
				foreach($cxlist as $key=>$value){
					$shopids[] = $value['shopid'];
				}
				$shopids = implode(',',array_unique($shopids));
				if(!empty($shopids)){
					$list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop where is_pass = 1 and  id in (".$shopids.") and endtime > ".time()." order by sort asc limit {$page},10  ");
				}else{
					$list = array();
				} 
				return $list;
}
		
/* 商家入住流程 */
function shopSettled(){
	$urllink = IUrl::creatUrl('wxsite/login');
	$where = "  id > 0 and parent_id = 0   ";
	if($this->member['uid'] == 0)  $this->message('',$urllink); 
	$type =    intval(IReq::get('type'));
	$catparent = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where  type='checkbox' order by cattype asc limit 0,100");
			$catlist = array();
			foreach($catparent as $key=>$value){
				$tempcat   = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where parent_id = '".$value['id']."'  limit 0,100");
				foreach($tempcat as $k=>$v){
					 $catlist[] = $v;
				}
			}
			$data['catarr'] = array('0'=>'外卖','1'=>'超市');
			$data['catlist'] = $catlist;
			$data['citylist']=	$this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where   ".$where."   order by orderid asc ");
			$uid =    $this->member['uid'];
			
			if($uid > 0){
				$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member  where uid = '".$uid."' ");
				$data['shopinfo'] =	$this->mysql->select_one("select id,is_pass from ".Mysite::$app->config['tablepre']."shop  where id = '".$memberinfo['shopid']."' ");
			}else{
				$data['shopinfo'] = array();
			}
        $linkxx = IUrl::creatUrl('wxsite/member');			
		if($data['shopinfo']['is_pass'] == 1 ) $this->message('您已入驻成功，请登录商家端管理店铺 ', $linkxx);
		if($type != 1  && $memberinfo['shopid'] > 0  )	{ 
			//if(!empty($data['shopinfo'])){
				$link = IUrl::creatUrl('wxsite/shangjiaresult/shopid/'.$memberinfo['shopid']);
				$this->message('',$link);
			//}
		}
		#	print_r($data['shopinfo']);
			Mysite::$app->setdata($data);
}
function sjapplyrz(){  
	
	$shopphone	 =    IFilter::act(IReq::get('shopphone'));
	#
	#print_r(strlen($shopphone));
	$shopname    =    IFilter::act(IReq::get('shopname'));
	$shopaddress =    IFilter::act(IReq::get('shopaddress'));
	$shoplicense =    IFilter::act(IReq::get('shoplicense'));
	$shoptype =    IReq::get('shoptype');
	$cityid =    IReq::get('cityid');
	if(empty($shopphone)) $this->message("请填写联系电话");
	if(strlen($shopphone) != 11) $this->message('errphone');
	if(!(IValidate::suremobi($shopphone)))$this->message('errphone');
	$checkphone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member  where phone = ".$shopphone." and `group` = 3 ");
	if(!empty($checkphone)) $this->message("手机号已存在"); 
	if(empty($shopname)) $this->message("请填写店铺名称");
	$checkshopname = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where shopname = '".$shopname."' ");
	if(!empty($checkshopname)) $this->message("店铺名字已存在"); 
	if(empty($shopaddress)) $this->message("请填写店铺地址");
	if(empty($shoplicense)) $this->message("请上传营业执照");
	
	$data['shopphone'] = $shopphone;
	$data['shopname'] = $shopname;
	$data['shopaddress'] = $shopaddress;
	$data['shoptype'] = $shoptype;
	$data['cityid'] = $cityid;
	$data['shoplicense'] = $shoplicense;
	$this->success($data);
	 
	
}	
	function shangjiaapply(){  
	
	$shopphone	 =    IReq::get('shopphone');
	#print_r($shopphone);exit;
	$shopname    =    IFilter::act(IReq::get('shopname'));
	$shopaddress =    IFilter::act(IReq::get('shopaddress'));
	$shoplicense =    IFilter::act(IReq::get('shoplicense'));
	$shoptype =    IReq::get('shoptype');
	$cityid =    IReq::get('cityid');
	$link = IUrl::creatUrl('wxsite/shangjia');
	if(empty($shopphone)) $this->message("请填写联系电话",$link);
	if(strlen($shopphone) != 11) $this->message('errphone');
	if(!(IValidate::phone($shopphone)))$this->message('errphone',$link);
	$checkphone = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member  where phone = ".$shopphone." and `group`=3 ");
	if(!empty($checkphone)) $this->message("手机号已存在",$link); 
	if(empty($shopname)) $this->message("请填写店铺名称",$link);
	$checkshopname = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop  where shopname = '".$shopname."' ");
	if(!empty($checkshopname)) $this->message("店铺名字已存在",$link); 
	if(empty($shopaddress)) $this->message("请填写店铺地址",$link);
	
	$data['shopphone'] = $shopphone;
	$data['shopname'] = $shopname;
	$data['shopaddress'] = $shopaddress;
	$data['shoptype'] = $shoptype;
	$data['cityid'] = $cityid;
	$data['shoplicense'] = $shoplicense;
    $data['xieyiinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."single where  code='shopxieyi'  or title='商家入驻协议'  ");
	 Mysite::$app->setdata($data);
	
}	
function shangjiaresult(){
	$shopid =    intval( IReq::get('shopid') );
	$data['shopinfo'] =	$this->mysql->select_one("select id,is_pass from ".Mysite::$app->config['tablepre']."shop  where id = '".$shopid."' ");
	 
  	Mysite::$app->setdata($data);
	
}



/* 新增网站通知 */
	
function ajaxnoticelist(){
		 
	  $pageinfo = new page();
	  $pageinfo->setpage(intval(IReq::get('page')),100);  
 
	 $list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."information where type=1  and   cityid='".$this->CITY_ID."'    and addtime < ".time()." order by addtime desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");   
//	 $list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."information where type=1  and   cityid='".$this->CITY_ID."'   and addtime < ".time()." order by addtime desc ");   

         $backdata = array();
	 $noticeshuliang = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."information where type=1  and     cityid='".$this->CITY_ID."' ");   
	 $backdata = array();
	foreach($list as $key=>$value){
		$value['addtime'] = date("Y-m-d",$value['addtime']); 
		$backdata[] = $value;
	 }
	 $data['noticeshuliang'] = $noticeshuliang;
	 $data['noticelist'] = $backdata;
	 Mysite::$app->setdata($data);
	}	  
function notice(){
	
	$id =    intval( IReq::get('id') );
	$data['noticeinfo'] =	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."information   where type= 1 and  id = '".$id."' "); 
  	Mysite::$app->setdata($data);
	
}	
/* 新增生活助手 */
function ajaxlifeasslist(){
		 
	  $pageinfo = new page();
	  $pageinfo->setpage(intval(IReq::get('page')),10);  
 
	 $list = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."information where type=2 and    cityid='".$this->CITY_ID."'   order by orderid asc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");   
	 $backdata = array();
	foreach($list as $key=>$value){
            $desLen = strlen($value['describe']);
            if($desLen >102){
                  $value['describe'] = substr($value['describe'],0,102);
            }
		$value['addtime'] = date("Y-m-d",$value['addtime']); 
		$backdata[] = $value;
	 }  
	 $data['noticelist'] = $backdata;
	 Mysite::$app->setdata($data);
	}	  
function lifeass(){
	
	$id =    intval( IReq::get('id') );
	$data['lifeassinfo'] =	$this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."information  where type = 2 and  id = '".$id."' "); 
  	Mysite::$app->setdata($data);
	
}

//保存店铺
	function saveshop()
	{
		#exit;
		$laiyuan = intval(IReq::get('laiyuan')); // 申请来源。1为微信端，主要用于判断微信端用户是否开过店
		$subtype = intval(IReq::get('subtype'));
		$id = intval(IReq::get('uid'));
		if(!in_array($subtype,array(1,2))) $this->message('system_err');

		$admin_id = empty($this->CITY_ID)?0:$this->CITY_ID;
		if($subtype == 1){
			  $username = IReq::get('username');
			  if(empty($username)) $this->message('member_emptyname');
				$testinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where username='".$username."'  ");
			  if(empty($testinfo)) $this->message('member_noexit');
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."usrlimit where  `group`='".$testinfo['group']."' and  name='editshop' ");
			  if(empty($shopinfo)) $this->message('member_noownshop');
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  uid='".$testinfo['uid']."' ");
			  if(!empty($shopinfo)) $this->message('member_isbangshop');
			  $data['shopname'] = IReq::get('shopname');
			  if(empty($data['shopname']))  $this->message('shop_emptyname');
			  $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  shopname='".$data['shopname']."'  ");
			  $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>$admin_id),"uid='".$testinfo['uid']."'");
			  if(!empty($shopinfo)) $this->message('shop_repeatname');
			  $data['uid'] = $testinfo['uid'];
			 
			   $data['admin_id'] = $admin_id;
			  $nowday = 24*60*60*365;
	       $data['endtime'] = time()+$nowday;
 			   
			   
			  
		$shoptype =  IReq::get('shoptype') ; 
	  $temparray = explode('_',$shoptype);
	   
	  $sdata['shoptype']  = $temparray[0];   // 店铺大类型 0为外卖 1为超市
	  $attrid =  $temparray[1];
	   
  
	   $checkshoptype =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id=".$attrid."  ");
	   if(empty($checkshoptype))  $this->message("获取店铺分类失败");
	   
	   
	    $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$data);
	  
	   $shopid = $this->mysql->insertid();
	    
	   $attrdata['shopid'] = $shopid;
	   $attrdata['cattype'] = $checkshoptype['cattype'];
	   $attrdata['firstattr'] = $checkshoptype['parent_id'];
	   $attrdata['attrid'] = $checkshoptype['id'];
	   $attrdata['value'] = $checkshoptype['name']; 
	   
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'shopattr',$attrdata);
	  
			  
			  
			  
			  $this->success('success');
		}elseif($subtype ==  2){
			/*检测*/
			$cityid = IReq::get('cityid');
			#print_r($cityid);exit;
			$adminid = empty($cityid)?$this->CITY_ID:$cityid;
			$data['username'] = IReq::get('username');
		  $data['phone'] = IReq::get('maphone');
      $data['email'] = IReq::get('email');
      $data['password'] = IReq::get('password');
      $sdata['shopname'] = IReq::get('shopname');
      $sdata['address'] = IReq::get('shopaddress');
	  $sdata['shoplicense'] = IReq::get('shoplicense');
       if(empty($sdata['shopname']))  $this->message('shop_emptyname');
		   $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where  shopname='".$sdata['shopname']."'  ");
			 if(!empty($shopinfo)) $this->message('shop_repeatname');
			 $password2 = IReq::get('password2');
		   if($password2 != $data['password']) $this->message('member_twopwdnoequale');
			 $uid = 0;
			 if($this->memberCls->regester($data['email'],$data['username'],$data['password'],$data['phone'],3)){
			 	$uid = $this->memberCls->getuid(); 
			 	$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('admin_id'=>$adminid),"uid='".$uid."'");
			 	
			 }else{
			 	 $this->message($this->memberCls->ero());
			 }
      $sdata['uid'] = $uid;
      $sdata['maphone'] =  $data['phone'];
      $sdata['addtime'] = time();
      $sdata['email'] =  $data['email'];    
      $sdata['admin_id'] = $adminid;
      $nowday = 24*60*60*365;
	     $sdata['endtime'] = time()+$nowday;
  
  
		$shoptype =  IReq::get('shoptype') ; 
	  $temparray = explode('_',$shoptype);
	   
	  $sdata['shoptype']  = $temparray[0];   // 店铺大类型 0为外卖 1为超市
	  $attrid =  $temparray[1];
	   
  
	   $checkshoptype =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shoptype where id=".$attrid."  ");
	   if(empty($checkshoptype))  $this->message("获取店铺分类失败");
	   
	   
	    $this->mysql->insert(Mysite::$app->config['tablepre'].'shop',$sdata);
	  
	   $shopid = $this->mysql->insertid();
	    
	   $attrdata['shopid'] = $shopid;
	   $attrdata['cattype'] = $checkshoptype['cattype'];
	   $attrdata['firstattr'] = $checkshoptype['parent_id'];
	   $attrdata['attrid'] = $checkshoptype['id'];
	   $attrdata['value'] = $checkshoptype['name']; 
	   
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'shopattr',$attrdata);
	 
	 
	   if($laiyuan == 1){
		   $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('shopid'=>$shopid),"uid='".$this->member['uid']."'");
	   }
	   $this->success($shopid);
	 //$this->success('success');
	   
		}else{
		 $this->message('system_err');
		}
	}
		
	/* 举报商家页面 */
	   function shopreport(){
        $this->checkwxweb();
        $shopid = intval(IReq::get(shopid));
		$shopinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id=".$shopid."  ");
		if(empty($shopinfo)) $this->message("获取店铺信息失败！");
        $shopname = $shopinfo['shopname'];
		$typelist = unserialize( Mysite::$app->config['refundreasonlist'] );
		$data['typelist'] = $typelist;
   
        $data['shopname'] = $shopname;
        Mysite::$app->setdata($data);
    } 
    function saveshopreport(){
        $typeidContent = IFilter::act(IReq::get('typeidContent'));
        $shopname = IFilter::act(IReq::get('shopname'));
        $content = IFilter::act(IReq::get('content'));
        $phone= IReq::get('phone');
        if($typeidContent == null || $typeidContent == '')	$this->message('请选择一种投诉类型');
        if(empty($content))	$this->message('详细情况不能为空');
        if(empty($phone))	$this->message('手机号码不能为空');
        $myreg = '/^1[34578]{1}\d{9}$/';
        if(!preg_match($myreg,$phone))$this->message('手机号码格式错误');
        $arr['typeidContent'] = $typeidContent;
        $arr['shopname'] = $shopname;
        $arr['addtime'] = time();
        $arr['content'] = $content;
        $arr['phone'] = $phone;
        $this->mysql->insert(Mysite::$app->config['tablepre'].'shopreport',$arr);
        $this->success('success');
    }


/* 8.3新增  2016-05-31  zem */
	function sharehb(){   //下单分享领取优惠券页面
 		
		/* $wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
 		$data['signPackage'] = $signPackage; */
        
        $juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 4 or name = '下单送优惠券' " );  	   
        $data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 4 or name= '下单送优惠券' order by id asc " ); 
        $data['sendjuanstatus'] = $juansetinfo['status'];
		$shareinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanshowinfo where id=1");		 
		if( empty($shareinfo) ){
			$shareinfo['title'] = Mysite::$app->config['sitename'];
			$shareinfo['img'] = Mysite::$app->config['sitelogo'];
			$shareinfo['describe'] = Mysite::$app->config['sitename'];
		}
		$data['shareinfo'] = $shareinfo;	    
		$orderid = intval(IReq::get('did'));
		
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");
	 
		$where = "  where type=2 and addtime < ".time()." and is_open = 1 and juannum > 0 ";
 		 
		$historyphone =   $_COOKIE['historyphone'];  //  ICookie::get('historyphone');
 		if( !empty($historyphone) ){
			$juanlist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where orderid=".$orderid."  and type = 4 and bangphone = '".$historyphone."' ");
		}else{
			$juanlist = array();
		}
 		 
 		$data['historyphone'] = $historyphone;
		$data['juanlist'] = $juanlist;
		$data['orderinfo'] = $orderinfo;
		Mysite::$app->setdata($data);
		
	}
	function receivejuan(){    //下单成功分享优惠券 根据手机号  领取优惠券
		
		$orderid = intval(IReq::get('orderid'));
		$phone = IFilter::act(IReq::get('phone'));
		$orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id=".$orderid."  ");
		if(empty($orderinfo)) $this->message('获取失败，请尝试刷新页面~');
		if(empty($phone)) $this->message('请填写领取使用的手机号~');
		if(!(IValidate::suremobi($phone)))$this->message('请填写正确的手机号~');
		 
		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 4 or name = '下单送优惠券' " );  	   
        $juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 4 or name= '下单送优惠券' order by id asc " ); 
		if($juansetinfo['status'] == 0 || empty($juaninfo)) $this->message('活动已结束~');
		$checkisrec =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where orderid=".$orderid."  and bangphone = '".$phone."' ");
		if(!empty($checkisrec))  $this->message('您已领取过，不可重复领取噢~');
	 	$where = "  where type=2 and addtime < ".time()." and is_open = 1 and juannum > 0 ";
		$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone = ".$phone."   "); 
        if($memberinfo['uid'] == $orderinfo['buyeruid'] )$this->message('自己不能领取自己发的红包哦~');		
		if( !empty($juaninfo) ){
			foreach($juaninfo as $key=>$val){ 							
				$data['limitcost'] = $val['limitcost'];
				if($juansetinfo['timetype'] == 1){
					$data['creattime'] = time();
					$date = date('Y-m-d',$data['creattime']);
					$endtime = strtotime($date) + ($juansetinfo['days']-1)*24*60*60 + 86399;
					$data['endtime'] = $endtime;
				}else{
					$data['creattime'] = $val['starttime'];
					$data['endtime'] =  $val['endtime'];
			   }
				if($juansetinfo['costtype'] == 1){
					$data['cost'] = $val['cost'];
			   }else{
					$data['cost'] = rand($val['costmin'],$val['costmax']);
			   }
				if( !empty($memberinfo) ){
					$data['uid'] = $memberinfo['uid'];
					$data['username'] = $memberinfo['username'];
					$data['status'] = 1;
				}else{
					$data['status'] = 0;
				}							
				$data['bangphone'] = $phone;
				$data['orderid'] = $orderinfo['id'];
				$data['name'] = "下单送优惠券";
				$data['type'] = 4;
				$data['paytype'] = $val['paytype'];			 
				$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$data);
			}
		 
		}
		 
		$this->success('success');
	}
	function memsharej(){   //会员忠心推广邀请好友分享优惠券页面
		$link = IUrl::creatUrl('wxsite/index'); 
		if($this->member['uid'] == 0)  $this->message('未登录',$link); 
 		$jiamiuidkey = base64_encode($this->member['uid']);  //  base64_decode
	 
		$data['jiamiuidkey'] = $jiamiuidkey;
  		/* $wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
 		$data['signPackage'] = $signPackage; */
		$shareinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanshowinfo where id=2 ");
		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 5 or name = '邀请好友送红包' " );  	   
        
		if( empty($shareinfo) ){
			$shareinfo['title'] = Mysite::$app->config['sitename'];
			$shareinfo['img'] = Mysite::$app->config['sitelogo'];
			$shareinfo['describe'] = Mysite::$app->config['sitename'];
		}
		$data['shareinfo'] = $shareinfo;
		$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$this->member['uid']."  ");
		$historyphone =   $_COOKIE['historyphone'];   
 		if( !empty($historyphone) ){
			$juanlist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where type = 5 and  shareuid=".$this->member['uid']."  and bangphone = '".$historyphone."' ");
		}else{
			$juanlist = array();
		}
 		$data['checkinfosendjuan'] = $juansetinfo['status'];
 		$data['historyphone'] = $historyphone;
		$data['juanlist'] = $juanlist;
		$data['memberinfo'] = $memberinfo;
		 
		Mysite::$app->setdata($data);
		
	}
	
 function memsharehb(){   //会员中心推广分享领取优惠券页面
		$uidkey = trim(IReq::get('key'));
 		$uid = base64_decode($uidkey);          	
		if( is_numeric($uid) ){
			$data['uid'] = $uid;
		}else{
			$data['uid'] = 0;
		} 
  		$sharememberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$data['uid']."'  ");
   		$shareinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanshowinfo where id = 2  ");
		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 5 or name = '邀请好友送红包' " );  
		if( empty($shareinfo) ){
			$shareinfo['title'] = Mysite::$app->config['sitename'];
			$shareinfo['img'] = Mysite::$app->config['sitelogo'];
			$shareinfo['describe'] = Mysite::$app->config['sitename'];
		}
		$data['shareinfo'] = $shareinfo;

		$historyphone =   $_COOKIE['historyphone'];  //  ICookie::get('historyphone');
 		if( !empty($historyphone) ){
			$juanlist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where type = 5 and shareuid='".$sharememberinfo['uid']."'  and bangphone = '".$historyphone."' ");
		}else{
			$juanlist = array();
		}
 		$data['checkinfosendjuan'] = $juansetinfo['status'];
 		$data['historyphone'] = $historyphone;
		$data['juanlist'] = $juanlist;
		$data['sharememberinfo'] = $sharememberinfo;
		Mysite::$app->setdata($data);
		
	}
	
 function memsharelqjuan(){    //会员推广分享优惠券 根据手机号  领取优惠券
		
		$uid = intval(IReq::get('uid'));
		$phone = IFilter::act(IReq::get('phone'));
		$sharememberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$uid."  ");
		if(empty($sharememberinfo)) $this->message('获取失败，请刷新页面尝试~');
		if(empty($phone)) $this->message('请填写领取使用的手机号~');
		if(!(IValidate::suremobi($phone)))$this->message('请填写正确的手机号~');
		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 5 or name = '邀请好友送红包' " ); 
        if($juansetinfo['status'] == 0)	$this->message('活动已结束~');	
		$checkisrec =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where shareuid=".$uid."  and bangphone = '".$phone."' ");
		if(!empty($checkisrec)) $this->message('您已领取过，不可重复领取噢~');
		
		$memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone = ".$phone."   ");
		$juaninfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 5 or name= '邀请好友送红包' order by id asc " );		
		if( !empty($juaninfo) ){
 			foreach($juaninfo as $key=>$val){
				if($juansetinfo['timetype'] == 1){
					$data['creattime'] = time();
					$date = date('Y-m-d',$data['creattime']);
					$endtime = strtotime($date) + ($juansetinfo['days']-1)*24*60*60 + 86399;
					$data['endtime'] = $endtime;
				}else{
					$data['creattime'] = $val['starttime'];
					$data['endtime'] =  $val['endtime'];
			    } 
				if($juansetinfo['costtype'] == 1){
					$data['cost'] = $val['cost'];
			    }else{
					$data['cost'] = rand($val['costmin'],$val['costmax']);
			    }
				$data['limitcost'] = $val['limitcost'];				
				if( !empty($memberinfo) ){
					$data['uid'] = $memberinfo['uid'];
					$data['username'] = $memberinfo['username'];
					$data['status'] = 1;
				}else{
					$data['status'] = 0;
					$data['username'] = '';
				}							
				$data['bangphone'] = $phone;				
				$data['name'] = "邀请好友送红包";
				$data['type'] = 5;
				$data['paytype'] = $val['paytype'];	
				$data['shareuid'] = $sharememberinfo['uid'];		 
				$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$data);  
			}							 
		}		 
		$this->success('success');
	}


    //8.3新增  绑定手机号发送验证码  lzh  2016-6-7
    function bindingphone(){
        $this->checkwxweb();
        $userid=$this->member['uid'];
        $phone = IFilter::act(IReq::get('phone'));
        $is_pass = intval(IReq::get('is_pass'));
        if(empty($phone)){
            echo 'noshow(\'请填写手机号\')';
            exit;
        }
        if(!IValidate::suremobi($phone)){
            echo  'noshow(\'手机格式错误\')';
            exit;
        }
        $makecode =  mt_rand(10000,99999);
        $contents =  '绑定手机号，验证码为：'.$makecode; 
		 $phonecode = new phonecode($this->mysql,8,$phone);
		 $phonecode->sendcode(); 
        ICookie::set('bindingphonecode',$makecode,90);
        ICookie::set('bindingphone',$phone,180);
        $longtime = time()+90;
        ICookie::set('bindingtime',$longtime,180);
        echo 'showsend(\''.$phone.'\',90,\''.$userid.'\')';
        exit;
    }
	
	

    //8.3新增   绑定手机号  lzh  2016-6-7
    function surebinding(){
        $this->checkwxweb();

        $pwdyzm = intval( IFilter::act(IReq::get('pwdyzm')) );
        $phoneyan =  IFilter::act(IReq::get('phone')) ;
		$bindingphone = ICookie::get('bindingphone');
        $is_pass =  intval(IReq::get('is_pass')) ;
        $datauid = $this->member['uid'];
        if(empty($phoneyan)) $this->message('请填写手机号');
		if($phoneyan != $bindingphone) $this->message('请输入获取验证码的手机号');
        if(!IValidate::suremobi($phoneyan)){
            $this->message('手机格式错误');
        }


//        if(Mysite::$app->config['regestercode'] == 1){
            if(empty($pwdyzm)) $this->message('请输入您收到的验证码');
            if(!empty($phoneyan)){
                $phonecode = new phonecode($this->mysql,8,$phone);
				if($phonecode->checkcode($pwdyzm)){
					
				}else{
					$this->message($phonecode->getError());
				}
            }
//        }
        $logintype =  ICookie::get('logintype');
        $userinfoarray = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone='".$phoneyan."' ");
        if(!empty($userinfoarray)){
            $qquser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."oauth where uid='".$userinfoarray['uid']."' ");
            $wxuser = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where uid='".$userinfoarray['uid']."' ");
            if($logintype=='wx'){
                if(!empty($wxuser)){
                    $this->message('该手机号已绑定其他微信账号，请先解绑');
                }else if($is_pass==0){
                    $this->message("y_bd");
                }
            }else if($logintype=='qq'){
                if(!empty($qquser)){
                    $this->message('该手机号已绑定其他qq账号，请先解绑');
                }else if($is_pass==0){
                    $this->message("y_bd");
                }
            }else{
                if(!empty($qquser) || !empty($wxuser)){
                    $this->message('该手机号已绑定其他第三方账号，请先解绑');
                }else if($is_pass==0){
                    $this->message("y_bd");
                }
            }
        }
        if($is_pass==1){
            $udata['phone']=0;
            $this->mysql->update(Mysite::$app->config['tablepre'].'member',$udata,"uid='".$userinfoarray['uid']."'");
        }
        $data['phone']=$phoneyan;
        $this->mysql->update(Mysite::$app->config['tablepre'].'member',$data,"uid='".$this->member['uid']."'");
		/* 更新绑定手机号有关优惠券信息 */
		$memberCls = new memberclass($this->mysql);  
		$memberCls->updatememjuaninfo($phoneyan);
		
        $this->success('success');
    }

    //8.3 个人中心点击头像判断登录
    function myaccount(){
       $this->checkwxweb();
	#	print_r($this->member);
		$userinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxuser where uid = ".$this->member['uid']." ");
		$data['is_bang'] = $userinfo['is_bang'];
		Mysite::$app->setdata($data);
    }
    //8.3 个人中心点击我的收藏判断登录
    function collect(){
        $this->checkwxweb();

    }
    //8.3 个人中心点击绑定手机号判断登录
    function binding(){
        $this->checkwxweb();

    }

    //8.3 闪惠支付页面支付数据处理
    function gotopayhui(){
        $orderid = intval(IReq::get('orderid'));
        $payerrlink = IUrl::creatUrl('wxsite/subpayhui/orderid/'.$orderid);
        $errdata = array('paysure'=>false,'reason'=>'','url'=>'');

        if(empty($orderid)){
            $backurl = IUrl::creatUrl('wxsite/index');
            $errdata['url'] = $backurl;
            $errdata['reason'] = '订单获取失败';
            $errdata['paysure'] = false;
            $this->showpayhtml($errdata);

        }
        $userid = empty($this->member['uid'])?0:$this->member['uid'];
        if($userid == 0){
                $errdata['url'] = $payerrlink;
                $errdata['reason'] = '订单操作无权限';
                $errdata['paysure'] = false;
                $this->showpayhtml($errdata);
        }
        $orderinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophuiorder where id=".$orderid."  ");  //获取主单
        //	print_r($orderinfo);
        if(empty($orderinfo)){
            $errdata['url'] = $payerrlink;
            $errdata['reason'] = '订单数据获取失败';
            $errdata['paysure'] = false;
            $this->showpayhtml($errdata);
        }
        if($userid > 0){
            if($orderinfo['uid'] !=  $userid){
                $errdata['url'] = $payerrlink;
                $errdata['reason'] = '订单不属于您';
                $errdata['paysure'] = false;
                $this->showpayhtml($errdata);
            }
        }
        if($orderinfo['paytype'] == 0){

            $errdata['url'] = $payerrlink;
            $errdata['reason'] = '此订单的支付类型不可操作';
            $errdata['paysure'] = false;
            $this->showpayhtml($errdata);

        }
        if($orderinfo['status']  > 1){

            $errdata['url'] = $payerrlink;
            $errdata['reason'] = '此订单状态不可操作';
            $errdata['paysure'] = false;
            $this->showpayhtml($errdata);

        }
        //
        $paydotype = IFilter::act(IReq::get('paydotype'));


        $paylist = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where  loginname = '".$paydotype."' and (type = 0 or type=2) order by id asc limit 0,50");

        if(empty($paylist)){
            $errdata['url'] = $payerrlink;
            $errdata['reason'] = '不存在的支付类型';
            $errdata['paysure'] = false;
            $this->showpayhtml($errdata);
        }

        if($orderinfo['paystatus'] == 1){
            $errdata['url'] = $payerrlink;
            $errdata['reason'] = '此订单已支付';
            $errdata['paysure'] = false;
            $this->showpayhtml($errdata);

        }
        $paydir = hopedir.'/plug/pay/'.$paydotype;
        if(!file_exists($paydir.'/pay.php'))
        {
            $errdata['url'] = $payerrlink;
            $errdata['reason'] = '支付方式文件不存在';
            $errdata['paysure'] = false;
            $this->showpayhtml($errdata);

        }
        $dopaydata = array('type'=>'yhorder','upid'=>$orderid,'cost'=>$orderinfo['sjcost'],'source'=>2,'paydotype'=>$paydotype);//支付数据
        include_once($paydir.'/pay.php');
        //调用方式  直接调用支付方式
        exit;
    }
//8.3  新增闪惠订单ajax列表
    function huiorderlist(){
		$this->checkwxweb();
        $link = IUrl::creatUrl('wxsite/index');
        if($this->member['uid'] == 0)  $this->message('',$link);
        $pageinfo = new page();
        $pageinfo->setpage(intval(IReq::get('page')),5);
        $datalist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shophuiorder where uid='".$this->member['uid']."' order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
        $backdata = array();
        foreach($datalist as $key=>$value){
            $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$value['shopid']."'");
            $value['shoplogo'] = $shopinfo['shoplogo'];
            $value['addtime'] = date('Y-m-d H:i',$value['addtime']);
            $backdata[] =$value;
        }
        $data['orderlist'] = $backdata;
        Mysite::$app->setdata($data);
    }
    function huiordershow(){
		$this->checkwxweb();
		$orderid = intval(IReq::get('orderid'));
		$orderinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophuiorder where id='".$orderid."'");
		#print_r($orderinfo);
		$data['order'] = $orderinfo;
        Mysite::$app->setdata($data);
	}

	//删除闪惠订单id
	function delshophuiorder(){
		$this->checkwxweb();
		//this->member[];
		$orderid = intval(IReq::get('orderid'));
		if($orderid < 1){
			$this->message('订单不存在');
		}
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shophuiorder  where  id = ".$orderid." ");
		if(empty($checkinfo)){
			$this->message('订单不存在');
		}
		if($checkinfo['paystatus'] == 1){
			$this->message('已支付不能删除,删除请联系平台');
		}
		if($checkinfo['uid'] != $this->member['uid']){
			$this->message('订单不属于您管理');
		}
	    $this->mysql->delete(Mysite::$app->config['tablepre']."shophuiorder"," id='".$orderid."'");
	    $this->success('success');
	}

	//新增微信端积分兑换商品详情页面   2016-6-27  lzh
	function giftinfo(){
		$id = intval(IReq::get('id'));
		$data['list'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."gift where id = ".$id);
		Mysite::$app->setdata($data);
	}
	
	// 8.4新增 关注微信领取优惠券功能  2016-07-15
	function gzwx(){ 
         
 		/* $wxclass = new wx_s();
		$signPackage = $wxclass->getSignPackage();
 		$data['signPackage'] = $signPackage;		 */
		$uid = $this->member['uid'];	
		$shareinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanshowinfo where id = 3 ");		 
		if( empty($shareinfo) ){
			$shareinfo['title'] = Mysite::$app->config['sitename'];
			$shareinfo['img'] = Mysite::$app->config['sitelogo'];
			$shareinfo['describe'] = Mysite::$app->config['sitename'];
		}
		$data['shareinfo'] = $shareinfo;		  
		//检测是否领取过关注送类型的优惠券
		$juancheck =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where type = 1 and uid = '".$uid."' ");
		 
		if(empty($juancheck)){
			$data['canget'] = 1;
		}else{
		    $data['canget'] = 0;
		}	 		
		$where = "  where  type = 1  ";
 		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 1 or name = '关注送优惠券' " );  	   
		$juanlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan ".$where." order by id asc ");			
		$costarr = array();	
		foreach($juanlist as $k=>$v){			
            if($juansetinfo['costtype'] == 2){
				$v['cost'] = rand($v['costmin'],$v['costmax']);				
			}
			$costarr[] = $v['cost'];
            $juaninfo[] = $v;			
		}
		$data['juaninfo'] = $juaninfo;	
		$data['costarr'] = implode('@',$costarr);		       
        $data['status'] = $juansetinfo['status'];			 	
		Mysite::$app->setdata($data);
		
	}
	function receivgzwxjuan(){   
        $coststr = IReq::get('costarr');
		$costarr = explode('@',$coststr); 	
		if(empty($costarr)) $this->message('优惠券信息获取失败');
		$juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 1  " );  	   
		$checkjuaninfo = $this->mysql->select_one("select id from ".Mysite::$app->config['tablepre']."juan where uid = ".$this->member['uid']."  and ( type = 1 or name = '关注送优惠券') " ); 
        if(!empty($checkjuaninfo)) $this->message('您已关注领取过优惠券');		 
		if($juansetinfo['status'] == 0) $this->message('活动已结束');		 
		$where = "  where  type = 1  ";
		$juanlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan ".$where." order by id asc ");
		  
		foreach($juanlist as $key=>$val){ 			
			if($val['timetype'] == 1){
				$data['creattime'] = time();
				$date = date('Y-m-d',$data['creattime']);
				$endtime = strtotime($date) + ($val['days']-1)*24*60*60+86399;
			    $data['endtime'] = $endtime;
			}else{
				$data['creattime'] = $val['starttime'];
			    $data['endtime'] =  $val['endtime'];
			}			 
			$data['cost'] = $costarr[$key];
			$data['limitcost'] = $val['limitcost'];	
			$data['uid'] = $this->member['uid'];
			$data['username'] = $this->member['username'];			
			$data['status'] = 1;													 
			$data['name'] = $val['name'];
			$data['type'] = 1;
			$data['paytype'] = $val['paytype'];					
			$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$data);  
		}	
		$this->success('success');
	}


    //跑腿页面检测两地之间的距离   lzh  2016-8-6
    function getptjuli(){
        $getlng = trim(IReq::get('getlng'));
        $getlat = trim(IReq::get('getlat'));
        $shoulng = trim(IReq::get('shoulng'));
        $shoulat = trim(IReq::get('shoulat'));
        $juli = $this->GetDistance($getlat,$getlng, $shoulat,$shoulng, 1,1);
        $juli = round($juli/1000,1);
        $this->success($juli);
    }

	   //展示店铺地图位置
    function showshopmap(){
	  
	   $shopid = IFilter::act(IReq::get('shopid'));
	   $shopinfo = $this->mysql->select_one("select shopname,address,lng,lat  from  ".Mysite::$app->config['tablepre']."shop  where id = ".$shopid." ");
	   
	   $userlng = ICookie::get('lng');
	   $userlat = ICookie::get('lat');
	    
	   $shopinfo['is_show_juli'] = 0;
	   $shopinfo['juli'] = 0;
	   $shopinfo['userlng'] = $userlng;
	   $shopinfo['userlat'] = $userlat;
	   
	   if( !empty($shopinfo) && !empty($shopinfo['lng']) && !empty($shopinfo['lat'])  && !empty($userlng)  && !empty($userlat)  ){
		   $shopinfo['is_show_juli'] = 1;
		   $juli = $this->GetDistance($shopinfo['lat'],$shopinfo['lng'],$userlat,$userlng,1);
		   if( $juli<1000 ){
			   $shopinfo['juli'] = $juli."m";
		   }else{
			   $shopinfo['juli'] = round($juli/1000,2)."km";
		   }
 	   }
	   
	   $data['shopinfo'] = $shopinfo;
 	   Mysite::$app->setdata($data);
   }
   

 
    function delphone(){
        $phone = intval(IReq::get('phone'));
        $areacodeone =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where phone={$phone} ");
        if(!empty($areacodeone)){
            $this->mysql->delete(Mysite::$app->config['tablepre'].'member',"uid ='".$areacodeone['uid']."'");
            $this->mysql->delete(Mysite::$app->config['tablepre'].'wxuser',"uid ='".$areacodeone['uid']."'");
        }
        echo "删除成功";exit;
    }
	function wxbdphone(){
		if($this->member['uid'] > 0 && $this->member['phone'] != ''){//表示会员已经绑定手机号
			//跳转
		} 
		if($this->member['uid'] < 1){//表示可能是多种登录方式同时使用 
			$bindwxopenid = ICookie::get('bindwxopenid');
			if(empty($bindwxopenid)){//判断绑定微信的OPENID 绑定为
				 $this->wxlogin();
				 $this->setwxlogin(0);
			}
		}else{//自动登录  表示能直接获取$this->member['uid'];
			
			
		} 
		session_start();
		$_SESSION['bindingwxlogin'] =time();
		$weblink = ICookie::get('wx_login_link');
		$defaultlink = IUrl::creatUrl('wxsite/member');
		$data['web_extend_link'] = empty($weblink)? $defaultlink:$weblink;
		Mysite::$app->setdata($data);
	}
    function qqbdphone(){
		session_start();
		$_SESSION['bindingwxlogin'] =time();
        $weblink = ICookie::get('wx_login_link');
        $defaultlink = IUrl::creatUrl('wxsite/member');
        $data['web_extend_link'] = empty($weblink)? $defaultlink:$weblink;
        Mysite::$app->setdata($data);
    }

}