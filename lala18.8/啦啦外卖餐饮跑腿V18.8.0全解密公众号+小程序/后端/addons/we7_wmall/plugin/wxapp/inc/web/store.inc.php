<?php

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'basic';
if($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$data = array(
			'status' => intval($_GPC['status']),
			'audit_status' => intval($_GPC['audit_status']),
			'key' => trim($_GPC['key']),
			'secret' => trim($_GPC['secret']),
			'wxapp_consumer_notice_channel' => trim($_GPC['wxapp_consumer_notice_channel']),
			'tpl_consumer_url' => trim($_GPC['tpl_consumer_url']),
			'tpl_store_url' => trim($_GPC['tpl_store_url']),
			'test' => array(
				'username' => trim($_GPC['test']['username']),
				'password' => trim($_GPC['test']['password']),
			),
		);
		set_plugin_config('wxapp.store', $data);
		imessage(error(0, '基础设置成功'), 'refresh', 'ajax');
	}
	$wxapp = get_plugin_config('wxapp.store');
	include itemplate('config/store');
}

?>
