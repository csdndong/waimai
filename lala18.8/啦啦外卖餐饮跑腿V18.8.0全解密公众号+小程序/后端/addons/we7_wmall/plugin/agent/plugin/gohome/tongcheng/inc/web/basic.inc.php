<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$audit = array(
			'new' => intval($_GPC['new']),
			'edit' => intval($_GPC['edit']),
		);
		$limit_num = array(
			'total_num' => intval($_GPC['total_num']),
			'day_num' => intval($_GPC['day_num'])
		);
		$data = array(
			'audit' => $audit,
			'limit_num' => $limit_num,
			'stick_num' => intval($_GPC['stick_num']),
			'pay_time_limit' => intval($_GPC['pay_time_limit']),
			'falselooknum' => intval($_GPC['falselooknum']),
			'falsefabunum' => intval($_GPC['falsefabunum']),
			'falselikenum' => intval($_GPC['falselikenum']),
			'minup' => intval($_GPC['minup']),
			'maxup' => intval($_GPC['maxup']),
		);
		set_agent_plugin_config('gohome.tongcheng', $data);
		imessage(error(0, '基础设置成功'), iurl('tongcheng/basic/index'), 'ajax');
	}
	$tongcheng = get_agent_plugin_config('gohome.tongcheng');
}

include itemplate('basic');
?>