<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('plugincenter');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '订单列表';
	$plugins = pdo_fetchall('select id, title, name from ' . tablename('tiny_wmall_plugin') . ' where 1', array());
	if(!empty($_GPC['addtime']['start']) && !empty($_GPC['addtime']['end'])) {
		$_GPC['starttime'] = strtotime($_GPC['addtime']['start']);
		$_GPC['endtime'] = strtotime($_GPC['addtime']['end']);
	}
	$data = getall_plugincenter_order();
	$orders = $data['orders'];
	$pager = $data['pager'];
}

include itemplate('system/plugincenter_order');

?>