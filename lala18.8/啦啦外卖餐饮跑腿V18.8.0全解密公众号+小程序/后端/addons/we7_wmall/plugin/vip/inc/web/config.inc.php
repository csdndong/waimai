<?php

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$dataProtocol = $_GPC['protocol'];
		set_config_text('会员卡协议', "vip:agreement", htmlspecialchars_decode($dataProtocol));
		$protocol = get_config_text("vip:agreement");
		imessage(error(0, '会员卡协议设置成功'), 'refresh', 'ajax');
	}	
}
include itemplate('config');
?>