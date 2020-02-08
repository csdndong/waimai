<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '活动设置';
	if($_W['ispost']){
		$invite_speck = $_GPC['invite_speck'];
		set_agent_plugin_config('pintuan.basic', $invite_speck);
		imessage(error(0, '编辑活动设置成功'), iurl('pintuan/basic/list'), 'ajax');
	}
	$invite_speck = get_agent_plugin_config('pintuan.basic');
}

include itemplate('basic');
