<?php

defined('IN_IA') or exit('Access Denied');

function vip_trade_update($id, $type = 'pay', $extra = array()) {
	global $_W;
	$trade = pdo_get('tiny_wmall_vip_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($trade)) {
		return error(-1, '订单不存在');
	}
	if($type == 'pay') {
		if($trade['is_pay']) {
			return error(-1, '订单已支付');
		}
		$data = array(
			'is_pay' => 1,
			'pay_type' => $extra['type'],
			'final_fee' => $extra['card_fee'],
			//'paytime' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_vip_order', $data, array('id' => $id));
		return true;
	}
}

function vip_is_vip($order = array()) {
	global $_W;
	$uid = $_W['member']['uid'];
	if(!empty($order)) {
		$uid = $order['uid'];
	}
	$is_vip = pdo_get('tiny_wmall_vip_order', array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'is_pay' => 1), array('id'));
	if(!empty($is_vip)) {
		return true;
	}
	return false;
}

function vip_recharge_status_update($order_id, $type, $params) {
	global $_W;
	$order = pdo_get('tiny_wmall_member_recharge', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if(empty($order)) {
		return error(-1, '充值订单不存在');
	}
	if($type == 'pay') {
		if($order['is_pay'] == 1) {
			return error(-1, '订单已支付');
		}
		$update = array(
			'is_pay' => 1,
			'pay_type' => $params['type'],
			'paytime' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_member_recharge', $update, array('uniacid' => $_W['uniacid'], 'id' => $order_id));
		$tag = iunserializer($order['tag']);
		if($tag['credit2'] > 0) {
			$log = array(
				$order['uid'],
				"会员用户充值{$tag['credit2']}元"
			);
			mload()->model('member');
			member_credit_update($order['uid'], 'credit2', $tag['credit2'], $log);
		}
		if($order['final_fee'] < 50) {
			return error(-1, '充值超过50元才可赠送红包');
		}
		$is_vip = vip_is_vip($order);
		if($is_vip) {
			$data = get_plugin_config('vip.redPacket');
			$redpackets = $data['redpackets'];
			mload()->model('redPacket');
			if(!empty($redpackets)) {
				foreach($redpackets as $redpacket) {
					$params = array(
						'uniacid' => $_W['uniacid'],
						//'activity_id' => $activity['id'],
						'title' => $redpacket['name'],
						'channel' => 'vip_redpacket',
						'type' => 'vip_recharge',
						'uid' => $order['uid'],
						'discount' => $redpacket['discount'],
						'condition' => $redpacket['condition'],
						'days_limit' => $redpacket['use_days_limit'],
						'grant_days_effect' => $redpacket['grant_days_effect'],
						'category_limit' => $redpacket['category_limit'],
						'times_limit' => $redpacket['times_limit'],
						'is_show' => 1,
						'scene' => $redpacket['scene'],
					);
					redPacket_grant($params, false);
				}
			}
		}
		return true;
	}
	return true;
}


?>