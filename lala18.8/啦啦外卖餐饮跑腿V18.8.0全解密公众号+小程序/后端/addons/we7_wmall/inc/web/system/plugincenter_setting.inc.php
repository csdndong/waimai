<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('plugin');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'setting';

if($op == 'setting') {
	$_W['page']['title'] = '应用设置';
	if($_W['ispost']) {
		$power = array(
			'basic' => array(
				'status' => intval($_GPC['basic']['status']),
				'thumb' => trim($_GPC['basic']['thumb']),
				'pluginname' => array_map("trim", $_GPC['basic']['pluginname'])
			),
			'pay_type' => array(
				'wechat' => array(
					'appid' => trim($_GPC['app']['wechat']['appid']),
					'mchid' => trim($_GPC['app']['wechat']['mchid']),
					'apikey' => trim($_GPC['app']['wechat']['apikey']),
				),
				'alipay' => array(
					'account' => trim($_GPC['app']['alipay']['account']),
					'partner' => trim($_GPC['app']['alipay']['partner']),
					'secret' => trim($_GPC['app']['alipay']['secret']),
				)
			),
			'contact' => array(
				'customer' => trim($_GPC['contact']['customer']),
				'servertime' => trim($_GPC['contact']['servertime'])
			)
		);
		if(!empty($_GPC['app_type'])) {
			foreach($_GPC['app_type'] as $key => $row) {
				if($row == 1) {
					$power['app'][] = $key;
				}
			}
		}
		if(!empty($_GPC['meal'])) {
			$meal = array();
			foreach ($_GPC['meal']['tel'] as $key => $val) {
				if (empty($val)) {
					continue;
				}
				$note = $_GPC['meal']['note'][$key];
				if (empty($note)) {
					continue;
				}
				$meal[] = array(
					'tel' => $val,
					'note' => $note
				);
			}
		}
		$power['contact']['meal'] = $meal;
		set_global_config('plugincenter', $power);
		imessage(error(0, '应用设置设置成功'), referer(), 'ajax');
	}
	$plugins = pdo_fetchall('select id,title,name from' . tablename('tiny_wmall_plugin'), array(), 'id');
	$power = get_global_config('plugincenter');
}

include itemplate('system/plugincenter_setting');