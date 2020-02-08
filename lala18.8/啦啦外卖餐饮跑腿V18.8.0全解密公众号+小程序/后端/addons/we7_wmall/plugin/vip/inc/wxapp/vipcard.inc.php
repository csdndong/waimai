<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if ($op == 'index') {
	$vip_agreement = get_config_text("vip:agreement");
	$result = array(
		'agreement' => $vip_agreement,
		'member' => $_W['member'],
	);
	imessage(error(0, $result), '', 'ajax');
}

elseif ($op == 'post') {
	$is_vip = vip_is_vip();
	if($is_vip) {
		imessage(error(-1000, '您已经是会员'), '', 'ajax');
	}
	$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax');
	$realname = trim($_GPC['username']) ? trim($_GPC['username']) : imessage(error(-1, '请输入昵称'), '', 'ajax');
	$update = array();
	if($mobile != $_W['member']['mobile']) {
		$update['mobile'] = $mobile;
	}
	if($realname != $_W['member']['realname']) {
		$update['realname'] = $realname;
	}
	if(!empty($update)) {
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	}
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['member']['uid'],
		'final_fee' => intval($_GPC['fee']) ? intval($_GPC['fee']) : 30,
		'order_sn' => date('YmdHis') . random(6, true),
		'addtime' => TIMESTAMP,
	);
	pdo_insert('tiny_wmall_vip_order', $insert);
	$order_id = pdo_insertid();
	imessage(error(0, $order_id), '', 'ajax');
}

elseif($op == 'recharge') {
	$config_recharge = $_W['we7_wmall']['config']['recharge'];
	if($config_recharge['status'] != 1) {
		imessage(error(-1, '平台暂未开启充值功能'), '', 'ajax');
	}
	if($_W['ispost']) {
		$price = floatval($_GPC['price']);
		if(!$price || $price < 0) {
			imessage(error(-1, '充值金额必须大于0'), '', 'ajax');
		}
		$tag = array(
			'credit2' => $price,
		);
		$data = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $_W['member']['uid'],
			'openid' => $_W['openid'],
			'order_sn' => date('YmdHis') . random(6, true),
			'type' => 'credit2',
			'fee' => $price,
			'final_fee' => $price,
			'pay_type' => '',
			'is_pay' => 0,
			'tag' => iserializer($tag),
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_member_recharge', $data);
		$id = pdo_insertid();
		imessage(error(0, $id), '', 'ajax');
	}
	imessage(error(0, ''), '', 'ajax');
}