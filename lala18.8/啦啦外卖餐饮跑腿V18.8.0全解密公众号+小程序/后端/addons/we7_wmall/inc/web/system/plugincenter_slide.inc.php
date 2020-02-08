<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'post') {
	$_W['page']['title'] = '编辑幻灯片';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$slide = pdo_get('tiny_wmall_plugincenter_slide', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(empty($slide)) {
			imessage('幻灯片不存在或已删除', referer(), 'error');
		}
	}
	if($_W['ispost']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '标题不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'thumb' => trim($_GPC['thumb']),
			'displayorder' => intval($_GPC['displayorder']),
			'status' => intval($_GPC['status']),
			'link' => trim($_GPC['link']),
		);
		if(!empty($id)) {
			pdo_update('tiny_wmall_plugincenter_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_plugincenter_slide', $data);
		}
		imessage(error(0, '编辑幻灯片成功'), iurl('system/plugincenter_slide/list'), 'ajax');
	}
	include itemplate('system/plugincenter_slide');
}

elseif($op == 'list') {
	$_W['page']['title'] = '幻灯片列表';
	if(checksubmit()) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
				);
				pdo_update('tiny_wmall_plugincenter_slide', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
		}
		imessage('编辑幻灯片成功', iurl('system/plugincenter_slide/list'), 'success');
	}
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => $_W['uniacid']
	);
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$slides = pdo_fetchall('select * from' . tablename('tiny_wmall_plugincenter_slide') . $condition . ' order by displayorder desc', $params);
	include itemplate('system/plugincenter_slide');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_plugincenter_slide', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除幻灯片成功'), '', 'ajax');
}

elseif($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_plugincenter_slide', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'slideagent'){
	if($_W['is_agent']) {
		$agents = get_agents();
	}
	$ids = $_GPC['id'];
	$ids = implode(',', $ids);
	if($_W['ispost'] && $_GPC['set'] == 1){
		$slideid = explode(',', $_GPC['id']);
		$agentid = intval($_GPC['agentid']);
		if($agentid > 0){
			foreach ($slideid as $value) {
				pdo_update('tiny_wmall_plugincenter_slide', array('agentid' => $agentid), array('uniacid' => $_W['uniacid'], 'id' => $value));
			}
		}
		imessage(error(0, '批量操作修改成功'), iurl('system/plugincenter_slide/list'), 'ajax');
	}
	include itemplate('system/op');
}
?>