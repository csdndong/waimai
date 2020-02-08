<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('member');
$_W['page']['title'] = '顾客充值记录';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$condition = ' where a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$keywords = trim($_GPC['keyword']);
	if(!empty($keywords)) {
		$condition .= " and (b.nickname like '%{$keywords}%' or b.realname like '%{$keywords}%' or b.mobile like '%{$keywords}%')";
	}
	if(!empty($_GPC['addtime']['start']) && !empty($_GPC['addtime']['end'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']);
		$condition .= ' and a.addtime >= :starttime and a.addtime <= :endtime';
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	}
	$type = trim($_GPC['type']);
	if(!empty($type)) {
		$condition .= ' and pay_type = :pay_type';
		$params[':pay_type'] = $type;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_vip_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid ' . $condition, $params);
	$trade = pdo_fetchall('select a.*, b.avatar,b.nickname,b.realname,b.mobile from ' . tablename('tiny_wmall_vip_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid ' . $condition . ' order by a.id desc LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}
include itemplate('trade');

?>