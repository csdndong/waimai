<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'post') {
	$_W['page']['title'] = '编辑幻灯片';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$slide = pdo_get('tiny_wmall_iservice_slide', array('uniacid' => 0, 'id' => $id));
		if(empty($slide)) {
			imessage('幻灯片不存在或已删除', referer(), 'error');
		}
	}
	if($_W['ispost']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '标题不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => 0,
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'displayorder' => intval($_GPC['displayorder']),
			'type' => 'service',
			'status' => intval($_GPC['status']),
			'wxapp_link' => trim($_GPC['wxapp_link']),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_iservice_slide', $data, array('uniacid' => 0, 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_iservice_slide', $data);
		}
		imessage(error(0, '编辑幻灯片成功'), iurl('iservice/slide/list'), 'ajax');
	}
	include itemplate('slide');
}

if($op == 'list') {
	$_W['page']['title'] = '幻灯片列表';
	if(checksubmit()) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_iservice_slide', $data, array('uniacid' => 0, 'id' => intval($v)));
			}
		}
		imessage('编辑幻灯片成功', iurl('iservice/slide/list'), 'success');
	}
	$condition = ' where uniacid = :uniacid and type = :type';
	$params = array(
		':uniacid' => 0,
		':type' => 'service'
	);
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_iservice_slide') . $condition . ' order by displayorder desc', $params);
	include itemplate('slide');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_iservice_slide', array('uniacid' => 0, 'id' => $id));
	imessage(error(0, '删除幻灯片成功'), '', 'ajax');
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_iservice_slide', array('status' => $status), array('uniacid' => 0, 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}