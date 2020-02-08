<?php

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'list';
if($op == 'list') {
	$_W['page']['title'] = '自定义页面';
	$condition = ' where uniacid = :uniacid ';
	$params = array(
		':uniacid' => 0
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and name like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_iservice_diypage') .  $condition, $params);
	$pages = pdo_fetchall('select * from ' . tablename('tiny_wmall_iservice_diypage') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$_W['page']['title'] = '新建自定义页面';
	$id = intval($_GPC['id']);
	$type = intval($_GPC['type']);
	if($id > 0) {
		$_W['page']['title'] = '编辑自定义页面';
		$page = iservice_get_diypage($id, false, array('from' => 'wap'));
	}
	if($_W['ispost']) {
		$data = $_GPC['data'];
		$diydata = array(
			'uniacid' => 0,
			'name' =>  $data['page']['name'],
			'type' => $data['page']['type'],
			'data' => base64_encode(json_encode($data)),
			'updatetime' => TIMESTAMP,
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_iservice_diypage', $diydata, array('id' => $id, 'uniacid' => 0));
		} else {
			$diydata['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_iservice_diypage', $diydata);
			$id = pdo_insertid();
		}
		imessage(error(0, '编辑成功'), iurl('iservice/diypage/post', array('id' => $id)), 'ajax');
	}
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_iservice_diypage', array('uniacid' => 0, 'id' => $id));
	}
	imessage(error(0, '删除自定义页面成功'), referer(), 'ajax');
}

include itemplate('diypage');

