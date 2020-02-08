<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'post') {
	$_W['page']['title'] = '编辑分类';
	$id = intval($_GPC['id']);
	if ($id > 0) {
		$item = pdo_get('tiny_wmall_seckill_goods_category', array('id' => $id, 'uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid']));
	}
	if ($_W['ispost']) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'title' => trim($_GPC['title']),
			'thumb' => trim($_GPC['thumb']),
			'link' => trim($_GPC['link']),
			'status' => intval($_GPC['status']),
			'displayorder' => intval($_GPC['displayorder'])
		);
		if (!empty($id)) {
			pdo_update('tiny_wmall_seckill_goods_category', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_seckill_goods_category', $data);
		}
		imessage(error(0, '编辑商品分类成功'), iurl('seckill/goods_category/list'), 'ajax');
	}
}

elseif($op == 'list') {
	$_W['page']['title'] = '商品分类';
	if ($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
				);
				pdo_update('tiny_wmall_seckill_goods_category', $data, array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => intval($v)));
			}
			imessage(error(0, '编辑商品分类成功'), iurl('seckill/goods_category/list'), 'ajax');
		}
	}
	$condition = ' where uniacid = :uniacid and agentid = :agentid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':agentid' => $_W['agentid']
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_seckill_goods_category') . $condition, $params);
	$category = pdo_fetchall('select * from' . tablename('tiny_wmall_seckill_goods_category') . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_seckill_goods_category', array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, '删除商品成功'), iurl('seckill/goods_category/list'), 'ajax');
}

elseif ($op == 'status'){
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_seckill_goods_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'agentid' => $_W['agentid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}
include itemplate('goods_category');
?>