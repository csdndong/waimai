<?php 
/**
 * @class distribution
 * @brief 分销类
 */
class distribution{ 
	private $orderid ='';  
	private $txdata = array();
	private $err = ''; 
	 
	//构造函数
	function __construct(){	
		$this->mysql = new mysql_class(); 		
	}
	
	//订单完成后调用该函数，进行相应的返佣操作，请在平台开启分销并且订单状态status更新为3后调用。
	public function operateorder($orderid){		
		$is_open_distribution = Mysite::$app->config['is_open_distribution'];//获取后台是否开启分销		
		if($is_open_distribution == 0){
			$this->err = '平台未开启分销';
			return false;
		} 
		if($orderid == '' || $orderid < 1){
			$this->err = '订单id获取失败';
			return false;
		} 	
		
        $orderinfo = $this->mysql->select_one("select id,dno,status,buyeruid,buyername,allcost  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
		if(empty($orderinfo)){
			$this->err = '订单不存在';
			return false;
		}
		if($orderinfo['status'] != 3){
			$this->err = '订单尚未完成不可进行返佣操作';
			return false;
		}
		if($orderinfo['is_reback'] == 1 || $orderinfo['is_reback'] == 2 || $orderinfo['is_reback'] == 4 ){
			$this->err = '订单涉及退款不可进行返佣操作';
			return false;
		}
		$buyerinfo = $this->mysql->select_one("select uid,username,fxpid from ".Mysite::$app->config['tablepre']."member  where uid= '".$orderinfo['buyeruid']."'   ");
		if(empty($buyerinfo)){
			$this->err = '下单用户不存在';
			return false;
		} 
		$grade = Mysite::$app->config['distribution_grade'];//获取后台开启的分销等级数		
		if($grade > 0){
			//分销记录公共数据
			$data['buyeruid'] = $orderinfo['buyeruid'];
			$data['buyername'] = $buyerinfo['username'];
			$data['orderid'] = $orderinfo['id'];
			$data['dno'] = $orderinfo['dno'];
			$data['addtime'] = time();
			$data['ordercost'] = $orderinfo['allcost'];
			//获取下单用户的一级分销会员信息			
			$member1 = $this->mysql->select_one("select uid,fxpid,fxcost from ".Mysite::$app->config['tablepre']."member  where uid= '".$buyerinfo['fxpid']."'   ");
			if(!empty($member1)){
				/************一级返佣开始************/
				$yj1 = Mysite::$app->config['distribution_yj1'];
				$yjcost1 = $orderinfo['allcost'] * $yj1 * 0.01;
				$fcost = $member1['fxcost'] + $yjcost1;
				$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('fxcost'=>$fcost),"uid='".$member1['uid']."'");
				//生成分销收入记录
				$data['uid'] = $member1['uid'];
				$data['grade'] = 1;
				$data['yjb'] = $yj1;
				$data['yjbcost'] = $yjcost1;	 
				$this->mysql->insert(Mysite::$app->config['tablepre'].'fxincomelog',$data);
				$id = $this->mysql->insertid(); 
				$this->notice($id);
				logwrite('通知id'.$id);
				/************一级返佣结束************/
				if($grade > 1){
					//获取下单用户的二级分销会员信息			
					$member2 = $this->mysql->select_one("select uid,fxpid,fxcost from ".Mysite::$app->config['tablepre']."member  where uid= '".$member1['fxpid']."' ");
					if(!empty($member2)){
						/************二级返佣开始************/
						$yj2 = Mysite::$app->config['distribution_yj2'];
						$yjcost2 = $orderinfo['allcost'] * $yj2 * 0.01;
						$fcost = $member2['fxcost'] + $yjcost2;
						$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('fxcost'=>$fcost),"uid='".$member2['uid']."'");
						//生成分销收入记录
						$data['uid'] = $member2['uid'];
						$data['grade'] = 2;
						$data['yjb'] = $yj2;
						$data['yjbcost'] = $yjcost2;
						$this->mysql->insert(Mysite::$app->config['tablepre'].'fxincomelog',$data);
						$id = $this->mysql->insertid(); 
						$this->notice($id);
						/************二级返佣结束************/	
						if($grade > 2){
							//获取下单用户的三级分销会员信息			
					        $member3 = $this->mysql->select_one("select uid,fxpid,fxcost from ".Mysite::$app->config['tablepre']."member  where uid= '".$member2['fxpid']."' ");
							if(!empty($member3)){
								/************三级返佣开始************/
								$yj3 = Mysite::$app->config['distribution_yj3'];
								$yjcost3 = $orderinfo['allcost'] * $yj3 * 0.01;
								$fcost = $member3['fxcost'] + $yjcost3;
								$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('fxcost'=>$fcost),"uid='".$member3['uid']."'");
								//生成分销收入记录
								$data['uid'] = $member3['uid'];
								$data['grade'] = 3;
								$data['yjb'] = $yj3;
								$data['yjbcost'] = $yjcost3;
								$this->mysql->insert(Mysite::$app->config['tablepre'].'fxincomelog',$data);
								$id = $this->mysql->insertid(); 
								$this->notice($id);
								/************三级返佣结束************/	
							}
						}	
					}	
				}	
			}
		}
		return true;
	} 
	//分销佣金提现函数
	//需要传过来以下数据
	/*$txdata = array(
		'uid'=>'用户uid',
		'txtype'=>'提现方式 1账户余额 2支付宝  3银行卡',
		'txcost'=>'提现金额',
		'zfbaccount'=>'支付宝账号',
		'zfbusername'=>'支付宝姓名',
		'cardusername'=>'持卡人姓名',
		'cardnumber'=>'银行卡号',
		'bankname'=>'银行名称',
	);*/
	public function tixian($txdata){		
		if(!is_numeric($txdata['txcost']) || $txdata['txcost'] <= 0 ){
			$this->err = "提现金额请输入大于0的数字";
			return false;
		}
		$minfxtxcost = Mysite::$app->config['minfxtxcost'];
		if($minfxtxcost > 0 && $txdata['txcost'] < $minfxtxcost ){
			$this->err = "提现金额不能少于".$minfxtxcost.'元';
			return false;
		}
		$memberinfo = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."member where uid=".$txdata['uid']." ");
		if(empty($memberinfo)){
			$this->err = "用户信息获取失败";
			return false;
		}else{
			if($txdata['txcost'] > $memberinfo['fxcost']){
				$this->err = "提现金额大于账户余额";
			    return false;
			}
		}
		if(empty($txdata['txtype'])){
			$this->err = "请选择提现方式";
			return false;
		}
		if($txdata['txtype'] == 2){
			if(empty($txdata['zfbaccount'])){
				$this->err = "请输入支付宝账户";
				return false;
			}
			if(empty($txdata['zfbusername'])){
				$this->err = "请输入支付宝姓名";
				return false;
			}
		}
		if($txdata['txtype'] == 3){
			if(empty($txdata['cardusername'])){
				$this->err = "请输入持卡人姓名";
				return false;
			}
			if(empty($txdata['cardnumber'])){
				$this->err = "请输入银行卡号";
				return false;
			}
			if(empty($txdata['bankname'])){
				$this->err = "请输入开户银行名称";
				return false;
			}
		}
		$feelv = Mysite::$app->config['fxfeelv'];
		$fee = $feelv > 0? $txdata['txcost']*$feelv *0.01:0;
		if($txdata['txtype'] == 1){
			$fee = 0;
		}
		$reallycost = $txdata['txcost'] - $fee;
		$yue = $memberinfo['fxcost'] - $txdata['txcost'];		 
		$logdata = array(
		    'uid'=>$txdata['uid'],
			'cost'=>$txdata['txcost'],
			'fee'=>$fee,
			'feelv'=>$feelv,
			'reallycost'=>$reallycost,
			'yue'=>$yue,
			'status'=>0,			 
			'type'=>$txdata['txtype'],				
			'zfbusername'=>$txdata['zfbusername'],
			'zfbaccount'=>$txdata['zfbaccount'],
			'cardusername'=>$txdata['cardusername'],
			'cardnumber'=>$txdata['cardnumber'],
			'bankname'=>$txdata['bankname'],
			'addtime'=>time(),
		);
		$this->mysql->insert(Mysite::$app->config['tablepre'].'distributiontxlog',$logdata);
		$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('fxcost'=>$yue),"uid='".$txdata['uid']."'");
		return true;
	}
	//获取用户的提现记录
	public function gettxloglist($uid,$page){
		$pageinfo = new page();
		$pageinfo->setpage($page,10);  
		$loglist = array();
		$statusarr = array('0'=>'处理中','1'=>'提现成功','2'=>' 提现失败');
		$list = $this->mysql->getarr("select id,cost,status,addtime from ".Mysite::$app->config['tablepre']."distributiontxlog where uid=".$uid."  order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." "); 
		foreach($list as $k=>$v){
			$v['addtime'] = date('Y-m-d H:i',$v['addtime']);
			$v['status'] = isset($statusarr[$v['status']])?$statusarr[$v['status']]:'未定义';
			$loglist[] = $v;
		}
		return $loglist;
	}
	//获取提现记录单条
	public function gettxlogdet($id){
		$statusarr = array('0'=>'处理中','1'=>'提现成功','2'=>' 提现失败');
		$typearr = array('1'=>'账户余额','2'=>'支付宝','3'=>' 银行卡');
		$txdet = $this->mysql->select_one("select id,cost,status,type,addtime from ".Mysite::$app->config['tablepre']."distributiontxlog where id=".$id." "); 
 		$txdet['content'] = $txdet['status'] == 2?'平台驳回提现申请':'无';
		$txdet['addtime'] = date('Y-m-d H:i',$txdet['addtime']);
		$txdet['status'] = isset($statusarr[$txdet['status']])?$statusarr[$txdet['status']]:'未定义';
		$txdet['type'] = isset($typearr[$txdet['type']])?$typearr[$txdet['type']]:'未定义';
		return $txdet;
	}
	//佣金记录
	public function getyjloglist($uid,$page,$searchvalue){				
		$pageinfo = new page();
		$pageinfo->setpage($page,5);		 
		$gradearr = array('1'=>'（一级下线）','2'=>'（二级下线）','3'=>'（三级下线）');
        $where = empty($searchvalue)?' and id > 0 ':' and ( dno = "'.$searchvalue.'" or buyername = "'.$searchvalue.'"  ) ';
		$loglist = array();	
		$list = $this->mysql->getarr("select id,yjbcost,dno,addtime,buyername,grade,ordercost from ".Mysite::$app->config['tablepre']."fxincomelog where uid=".$uid."   ".$where."order by id desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." "); 
		foreach($list as $k=>$v){
			$v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);			 
			$v['grade'] = isset($gradearr[$v['grade']])?$gradearr[$v['grade']]:'未定义';
			$loglist[] = $v;
		}		 
		return $loglist;	
	}
	//佣金排行榜
	public function yjranking($uid){
		$list = $this->mysql->getarr("select sum(a.yjbcost) as yjbcost,a.uid,b.logo,b.username from ".Mysite::$app->config['tablepre']."fxincomelog as a left join  ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.uid > 0  group by a.uid  order by sum(a.yjbcost) desc limit 50 "); 
		 
		$selfranking = 0;
		$returnlist = array();
		foreach($list as $k=>$v){
			$v['logo'] = empty($v['logo'])?Mysite::$app->config['userlogo']:$v['logo'];
			$v['logo'] = getImgQuanDir($v['logo']);
			$v['ranking'] = $k + 1;
			if($v['uid'] == $uid){
				$selfranking = $k + 1;//获取自身排名
			}
			$returnlist[] = $v;
		} 
		$data['list'] = $returnlist;		
		$data['selfinfo'] = array();
		if($selfranking > 0 && $selfranking < 51){
			$data['is_showself'] = 0;
		}else{
			$menberinfo = $this->mysql->select_one("select uid,logo,username from ".Mysite::$app->config['tablepre']."member where uid = ".$uid." "); 
			$data['is_showself'] = 1;
			$logo = empty($menberinfo['logo'])?Mysite::$app->config['userlogo']:$menberinfo['logo'];
			if($selfranking == 0){				
				$data['selfinfo'] = array('yjbcost'=>'0.00','uid'=>$uid,'logo'=>$logo,'username'=>$menberinfo['username'],'ranking'=>'--');
			}else{
				$data['selfinfo'] = array('yjbcost'=>'0.00','uid'=>$uid,'logo'=>$logo,'username'=>$menberinfo['username'],'ranking'=>'50+');	
			}
		}
		$data['selfranking'] = $selfranking;
	    return $data;	
	} 
	//获取分销商列表
	public function getmemberlist($uid,$page,$grade){
		$pageinfo = new page();
		$pageinfo->setpage($page,10);  
		$junior1ids = '-1';//一级分销商会员id集
		$junior2ids = '-1';//二级分销商会员id集
		$junior3ids = '-1';//三级分销商会员id集 
		$datalist1 = $this->mysql->getarr("select uid from ".Mysite::$app->config['tablepre']."member where fxpid=".$uid."  order by uid desc "); 
		foreach($datalist1 as $k1=>$v1){
			$junior1ids .=','.$v1['uid'];
		} 
		$datalist2 = $this->mysql->getarr("select uid from ".Mysite::$app->config['tablepre']."member where fxpid in (".$junior1ids.")  order by uid desc "); 
		foreach($datalist2 as $k2=>$v2){
			$junior2ids .=','.$v2['uid'];
		}
		$datalist3 = $this->mysql->getarr("select uid from ".Mysite::$app->config['tablepre']."member where fxpid in (".$junior2ids.")  order by uid desc "); 
		foreach($datalist3 as $k3=>$v3){
			$junior3ids .=','.$v3['uid'];
		}
        $ids = array('1'=>$junior1ids,'2'=>$junior2ids,'3'=>$junior3ids);
		$ids[$grade] = isset($ids[$grade])?$ids[$grade]:'0';
		$members = $this->mysql->getarr("select uid,username,logo,befxtime from ".Mysite::$app->config['tablepre']."member where uid in (".$ids[$grade].")  order by uid desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
		$memberlist = array();
		foreach($members as $key=>$val){
			$val['befxtime'] = date('Y-m-d H:i',$val['befxtime']);
			$val['logo'] = empty($val['logo'])?Mysite::$app->config['userlogo']:$val['logo'];
			$val['logo'] = getImgQuanDir($val['logo']);
			$order =  $this->mysql->select_one("select count(id) as ordershu , sum(yjbcost) as yjbcost from  ".Mysite::$app->config['tablepre']."fxincomelog where buyeruid = ".$val['uid']." and uid = ".$uid." ");
			$val['ordershu'] = $order['ordershu']>0?$order['ordershu']:0;
			$val['yjbcost'] = $order['yjbcost']>0?$order['yjbcost']:0.00;
			$grade = Mysite::$app->config['distribution_grade'];//获取后台开启的分销等级数		
			//一级
			$juniors1 = $this->mysql->getarr("select uid  from  ".Mysite::$app->config['tablepre']."member where fxpid = ".$val['uid']." ");
			$juniorcount = 0;
			$count1 = count($juniors1);//一级分销商个数
			$juniorcount = $juniorcount + $count1;			
			$juniorx1ids = '-1';
			foreach($juniors1 as $kx1=>$vx1){
			    $juniorx1ids .=','.$vx1['uid'];//一级分销商uid集合
		    }		
			if($grade>1){
				$juniorx1ids = empty($juniorx1ids)?'0':$juniorx1ids;			
				$juniors2 = $this->mysql->getarr("select uid  from  ".Mysite::$app->config['tablepre']."member where fxpid in (".$juniorx1ids.")");
				$count2 = count($juniors2);//二级分销商个数
				$juniorx2ids = '-1';
				foreach($juniors2 as $kx2=>$vx2){
			        $juniorx2ids .=','.$vx2['uid'];//二级分销商uid集合
		        }
				$juniorcount = $juniorcount + $count2;
			}
			if($grade>2){			 
				$juniors3 = $this->mysql->select_one("select count(uid) as count  from  ".Mysite::$app->config['tablepre']."member where fxpid in (".$juniorx2ids.")");
				$count3 = $juniors3['count'];//三级分销商个数
				$juniorcount = $juniorcount + $count3;
			}
			$val['juniorcount'] = $juniorcount;
			$memberlist[] = $val;
		}
		return $memberlist;
	}
	//通知函数 用于返佣后通知受益者
	public function notice($id){
		//获取收益记录
		$fxincomelog = $this->mysql->select_one("select * from  ".Mysite::$app->config['tablepre']."fxincomelog where id = ".$id." ");
		/**
		   写入通知操作业务代码
		*/
		return true;
	}
	public function Error(){
		return $this->err;
	}
	  
}
?>