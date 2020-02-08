<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('plugin');
pload()->model('advertise');
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$recommendHome = get_advertise_info('recommendHome');
if($recommendHome['status'] != 1) {
	imessage(error(-1, '该广告暂未开售'), '', 'ajax');
}
$recommendOther = get_advertise_info('recommendOther');
$amount = $store['account']['amount'];
$_W['page']['title'] = '为您优选推广';
if($ta == 'index') {
	if($_W['isajax']) {
		$type = trim($_GPC['type']);
		if(!$type) {
			imessage(error(-1, '请选择广告类型'), '', 'ajax');
		}
		if($type == 'recommendHome' && !$recommendHome['leave']) {
			imessage(error(-1, '为您优选首页广告位已售空，请选择其他广告位'), '', 'ajax');
		}
		if($type == 'recommendOther' && !$recommendOther['leave']) {
			imessage(error(-1, '为您优选更多页广告位已售空，请选择其他广告位'), '', 'ajax');
		}
		$day = intval($_GPC['day']);
		if(!$day) {
			imessage(error(-1, '请选择购买天数'), '', 'ajax');
		}
		$pay_type = trim($_GPC['pay_type']);
		if(!$pay_type) {
			imessage(error(-1, '请选择支付方式'), '', 'ajax');
		}
		if($type == 'recommendHome'){
			$finalfee = $recommendHome['prices'][$day]['fee'];
		} elseif($type == 'recommendOther') {
			$finalfee = $recommendOther['prices'][$day]['fee'];
		}
		if($pay_type == 'credit' && $amount < $finalfee) {
			imessage(error(-1,'余额不足，请选择其他支付方式'), '', 'ajax');
		}
		$recommendData = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'type' => $type,
			'displayorder' => 0,
			'title' => $type == 'recommendHome' ? "为您优选首页展示{$day}天" : "为您优选更多页展示{$day}天",
			'status' => 0,
			'addtime' => TIMESTAMP,
			'endtime' => TIMESTAMP,
			'final_fee' => $finalfee,
			'pay_type' => $pay_type,
			'days' => $day,
			'is_pay' => 0,
			'order_sn' => date('YmdHis', time()).random(6, true),
			'data' => '',
		);
		pdo_insert('tiny_wmall_advertise_trade', $recommendData);
		$id = pdo_insertid();
		imessage(error(0, array('id' => $id, 'sid' => $sid)), '', 'ajax');
	}

	include itemplate('advertise/recommend');
}





