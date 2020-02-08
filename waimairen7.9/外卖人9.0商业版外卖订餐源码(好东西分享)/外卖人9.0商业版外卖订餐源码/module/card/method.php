<?php
class method   extends baseclass
{

	function exchangjuan(){
		$this->checkmemberlogin();
		$card = trim(IFilter::act(IReq::get('card')));
		$password = trim(IFilter::act(IReq::get('password')));
		if(empty($card)) $this->message('card_emptyjuancard');
		if(empty($password)) $this->message('card_emptyjuanpwd');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juan where card ='".$card."'  and card_password = '".$password."' and (endtime > ".time()." or endtime = 0) and status = 0");
		if(empty($checkinfo)) $this->message('card_emptyjuan');
		if($checkinfo['uid'] > 0) $this->message('card_juanisuse');

		$arr['uid'] = $this->member['uid'];
		$arr['status'] =  1;
		$arr['username'] = $this->member['username'];
		if($checkinfo['timetype'] == 1){
			$arr['creattime'] =  time();
			$arr['endtime'] =  time() + $checkinfo['days'] * 24 *60 *60;
		}
		$this->mysql->update(Mysite::$app->config['tablepre'].'juan',$arr,"card='".$card."'  and card_password = '".$password."' and ( endtime > ".time()." or endtime = 0)  and status = 0 and uid = 0");
		$mess['userid'] = $this->member['uid'];
	  $mess['username']  ='';
	  $mess['content'] = '绑定优惠劵'.$checkinfo['card'];
	  $mess['addtime'] = time();
//	  print_r($mess);
    //$this->mysql->insert(Mysite::$app->config['tablepre'].'message',$mess);  //写消息表
		$this->success('success');

	}
	function exchangcard(){
		$this->checkmemberlogin();
		$card = trim(IFilter::act(IReq::get('card')));
		$password = trim(IFilter::act(IReq::get('password')));
		if(empty($card)) $this->message('card_emptycard');
		if(empty($password)) $this->message('card_emptycardpwd');
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."card where card ='".$card."'  and card_password = '".$password."' and uid =0 and status = 0");
		if(empty($checkinfo)) $this->message('card_cardiuser');
		$arr['uid'] = $this->member['uid'];
		$arr['status'] =  1;
		$arr['username'] = $this->member['username'];
		$this->mysql->update(Mysite::$app->config['tablepre'].'card',$arr,"card ='".$card."'  and card_password = '".$password."' and uid =0 and status = 0");
		//`$key`
		$this->mysql->update(Mysite::$app->config['tablepre'].'member','`cost`=`cost`+'.$checkinfo['cost'],"uid ='".$this->member['uid']."' ");
    $allcost = $this->member['cost']+$checkinfo['cost'];
    $this->memberCls->addlog($this->member['uid'],2,1,$checkinfo['cost'],'充值卡充值','使用充值卡'.$checkinfo['card'].'充值'.$checkinfo['cost'].'元',$allcost);
	 $this->memberCls->addmemcostlog( $this->member['uid'],$this->member['username'],$this->member['cost'],1,$checkinfo['cost'],$allcost,"使用充值卡充值",ICookie::get('adminuid'),ICookie::get('adminname') );
		$this->success('success');
	}

}



?>