<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '活动列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'name' => trim($_GPC['name'][$k]),
					'oldprice' => trim($_GPC['oldprice'][$k]),
					'total' => trim($_GPC['total'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_pintuan_goods', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => intval($v)));
			}
			imessage(error(0, '编辑活动商品成功'), iurl('pintuan/activity/list'), 'ajax');
		}
	}

	$condition = ' where uniacid = :uniacid and agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and name like :keyword';
		$params[':keyword'] = "%{$keyword}%";
	}
	$sid = intval($_GPC['sid']);
	if(!empty($sid)) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}
	$cateid = intval($_GPC['cateid']);
	if(!empty($cateid)) {
		$condition .= ' and cateid = :cateid';
		$params[':cateid'] = $cateid;
	}
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : '-1';
	if($status > -1) {
		$condition .= " and status = :status";
		$params[':status'] = $status;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_pintuan_goods') . $condition, $params);
	$goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_pintuan_goods') . $condition . ' order by displayorder desc,id asc LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($goods)) {
		foreach($goods as &$da) {
			$da['thumb'] = tomedia($da['thumb']);
		}
	}
	$goods_status = gohome_goods_status();
	$stores = pdo_fetchall('select id,title from ' . tablename('tiny_wmall_store') . ' where uniacid = :uniacid and agentid = :agentid', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
	$categorys = pdo_fetchall('select id,title from ' . tablename('tiny_wmall_pintuan_category') . ' where uniacid = :uniacid and agentid = :agentid', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']), 'id');
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	$result = gohome_del_goods($id, 'pintuan');
	imessage($result, '',  'ajax');
}

include itemplate('activity');
?>