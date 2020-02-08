<?php

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '分类列表';
	if(checksubmit()) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_iservice_category', $data, array('uniacid' => 0, 'id' => intval($v)));
			}
			imessage('编辑分类成功', iurl('iservice/category/list'), 'success');
		}
	}

	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => 0
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_iservice_category') . $condition, $params);
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_category') . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	foreach($categorys as &$da) {
		$da['thumb'] = tomedia($da['thumb']);
	}
	$pager = pagination($total, $pindex, $psize);
	include itemplate('category');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_iservice_category', array('status' => $status), array('uniacid' => 0, 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_iservice_category', array('uniacid' => 0, 'id' => $id));
	imessage(error(0, '删除分类成功'), iurl('iservice/category/list'), 'ajax');
}

if($op == 'post') {
	$_W['page']['title'] = '编辑分类';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$category = pdo_get('tiny_wmall_iservice_category', array('uniacid' => 0, 'id' => $id));
		if(empty($category)) {
			imessage('分类不存在或已删除', referer(), 'error');
		}
	}
	if($_W['ispost']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '标题不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => 0,
			'title' => trim($_GPC['title']),
			'thumb' => trim($_GPC['thumb']),
			'displayorder' => intval($_GPC['displayorder']),
			'status' => intval($_GPC['status']),
			'wxapp_link' => trim($_GPC['wxapp_link'])
		);
		if(empty($_GPC['id'])){
			pdo_insert('tiny_wmall_iservice_category', $data);
		}else{
			pdo_update('tiny_wmall_iservice_category', $data, array('uniacid' => 0, 'id' => $id));
		}
		imessage(error(0, '编辑分类成功'), iurl('iservice/category/list'), 'ajax');
	}
	include itemplate('category');
}