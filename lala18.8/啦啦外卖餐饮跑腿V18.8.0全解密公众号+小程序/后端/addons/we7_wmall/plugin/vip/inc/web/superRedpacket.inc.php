<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op'])? trim($_GPC['op']): 'index';

if($op == 'index') {
	$_W['page']['title'] = '超级红包设置';
	if ($_W['ispost']) {
		$data = $_GPC['data'];
		set_plugin_config('vip.redPacket', $data);
		imessage(error(0, '超级红包设置成功'), iurl('vip/superRedpacket'), 'ajax');
	}
	$data = get_plugin_config('vip.redPacket');
	include itemplate('superRedpacket');
}
?>