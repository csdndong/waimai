<?php

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'index';
if($op == 'index') {
	$_W['page']['title'] = '页面选择';
	$pages = array(
		'home' => array(
			'name' => '首页',
			'url' => 'package/pages/iservice/index',
			'key' => 'home',
			'save_key' => 'use_diy_home',
			'pages' => iservice_get_pages(array('type' => 1), array('id', 'name')),
		),
	);
	if($_W['ispost']) {
		$setting = array(
			'vue' => array(
				'use_diy_home' => intval($_GPC['vue_use_diy_home']),
				'shopPage' => array_map('intval', $_GPC['vue_shopPages'])
			),
			'wxapp' => array(
				'use_diy_home' => intval($_GPC['wxapp_use_diy_home']),
				'shopPage' => array_map('intval', $_GPC['wxapp_shopPages'])
			)
		);
		set_plugin_config("iservice.diypage", $setting);
		imessage(error(0, '设置成功'), referer(), 'ajax');
	}
	$config = get_plugin_config('iservice.diypage');
	$config_diy_vue = $config['vue'];
	$config_diy_wxapp = $config['wxapp'];
}
include itemplate('diyshop');