<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '服务列表';
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['titles'][$k]),
					'price' => floatval($_GPC['prices'][$k]),
					'displayorder' => intval($_GPC['displayorders'][$k]),
					'total' => intval($_GPC['totals'][$k]),
					'sailed' => intval($_GPC['sailed'][$k]),
				);
				pdo_update('tiny_wmall_iservice_service', $data, array('uniacid' => 0, 'id' => intval($v)));
			}
		}
		imessage(error(0, '修改成功'), iurl('iservice/service/list'), 'ajax');
	}

	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => 0
	);
	$cid = intval($_GPC['cid']);
	if(!empty($cid)) {
		$condition .= ' and cid = :cid';
		$params[':cid'] = $cid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (title like :keyword or id = :keyword)';
		$params[':keyword'] = $keyword;
	}
	$order_by_type = trim($_GPC['order_by_type'])? trim($_GPC['order_by_type']): 'displayorder';
	$order_by = " ORDER BY {$order_by_type} DESC, id desc";

	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_iservice_service') . $condition, $params);
	if($order_by_type == 'total') {
		$service = pdo_fetchall('SELECT *, CASE total WHEN -1 THEN 10000000 ELSE total END AS order_by_total FROM ' . tablename('tiny_wmall_iservice_service') . " {$condition} ORDER BY order_by_total ASC, id desc LIMIT " . ($pindex - 1) * $psize.','.$psize, $params);
	} else {
		$service = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_service') . " {$condition}{$order_by} LIMIT " . ($pindex - 1) * $psize.','.$psize, $params);
	}
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_category') . " where uniacid = :uniacid" ,array(':uniacid' => 0), 'id');
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'post') {
	$_W['page']['title'] = '编辑服务';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$service = pdo_get('tiny_wmall_iservice_service', array('uniacid' => 0, 'id' => $id));
		if(!empty($service)) {
			if($service['is_options']) {
				$service['options'] = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_service_options') . ' WHERE uniacid = :uniacid AND service_id = :service_id ORDER BY displayorder DESC, id ASC', array(':uniacid' => 0, ':service_id' => $id));
			}
			$service['attrs'] = iunserializer($service['attrs']);
			if(!empty($service['attrs'])) {
				foreach($service['attrs'] as &$aval) {
					$aval['label'] = implode(',', $aval['label']);
				}
			}
			$service['label'] = iunserializer($service['label']);
			$service['thumbs'] = iunserializer($service['thumbs']);
		}
	}
	if($_W['ispost']) {
		$pid = intval($_GPC['pid']);
		$data = array(
			'uniacid' => 0,
			'pid' => $pid,
			'cid' => intval($_GPC['cid']),
			'title' => trim($_GPC['title']),
			'mobile' => trim($_GPC['mobile']),
			'price' => floatval($_GPC['price']),
			'old_price' => floatval($_GPC['old_price']),
			'is_options' => intval($_GPC['is_options']),
			'total' => intval($_GPC['total']),
			'total_warning' => intval($_GPC['total_warning']),
			'total_update_type' => intval($_GPC['total_update_type']),
			'sailed' => intval($_GPC['sailed']),
			'status' => intval($_GPC['status']),
			'is_hot' => intval($_GPC['is_hot']),
			'thumb' => trim($_GPC['thumb']),
			'displayorder' => intval($_GPC['displayorder']),
			'content' => trim($_GPC['content']),
			'description' => htmlspecialchars_decode($_GPC['description'])
		);
		$data['thumbs'] = array();
		if(!empty($_GPC['thumbs'])) {
			foreach($_GPC['thumbs'] as $thumbs) {
				if(empty($thumbs)) continue;
				$data['thumbs'][] = $thumbs;
			}
		}
		$data['thumbs'] = iserializer($data['thumbs']);
		if(!empty($_GPC['label'])) {
			foreach($_GPC['label'] as $val) {
				if(empty($val)){
					continue;
				}
				$data['label'][] = trim($val);
			}
			$data['label'] = iserializer($data['label']);
		}
		if($data['is_options'] == 1) {
			$options = array();
			foreach($_GPC['options']['name'] as $key => $val) {
				$val = trim($val);
				$price = floatval($_GPC['options']['price'][$key]);
				if(empty($val) || empty($price)) {
					continue;
				}
				$options[] = array(
					'id' => intval($_GPC['options']['id'][$key]),
					'name' => $val,
					'price' => $price,
					'total' => intval($_GPC['options']['total'][$key]),
					'total_warning' => intval($_GPC['options']['total_warning'][$key]),
					'displayorder' => intval($_GPC['options']['displayorder'][$key]),
				);
			}
			if(empty($options)) {
				imessage(error(-1, '没有设置有效的规格项'), '', 'ajax');
			}
		}
		$data['attrs'] = array();
		if(!empty($_GPC['attrs'])) {
			foreach($_GPC['attrs']['name'] as $key => $row) {
				$row = trim($row);
				if(empty($row)) {
					continue;
				}
				$labels = $_GPC['attrs']['label'][$key];
				$labels = array_filter(explode(',', str_replace('，', ',', $labels)), trim);
				if(empty($labels)) {
					continue;
				}
				$data['attrs'][] = array(
					'name' => $row,
					'label' => $labels
				);
			}
		}
		$data['attrs'] = iserializer($data['attrs']);
		if(!empty($id)) {
			pdo_update('tiny_wmall_iservice_service', $data, array('uniacid' => 0, 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_iservice_service', $data);
			$id = pdo_insertid();
		}
		$ids = array(0);
		if(!empty($options)) {
			foreach($options as $val) {
				$option_id = $val['id'];
				if($option_id > 0) {
					pdo_update('tiny_wmall_iservice_service_options', $val, array('uniacid' => 0, 'id' => $option_id, 'service_id' => $id));
				} else {
					$val['uniacid'] = 0;
					$val['provider_id'] = $pid;
					$val['service_id'] = $id;
					pdo_insert('tiny_wmall_iservice_service_options', $val);
					$option_id = pdo_insertid();
				}
				$ids[] = $option_id;
			}
		}
		$ids = implode(',', $ids);
		pdo_query('delete from ' . tablename('tiny_wmall_iservice_service_options') . " WHERE uniacid = :uniacid AND service_id = :service_id and id not in ({$ids})", array(':uniacid' => 0, ':service_id' => $id));
		imessage(error(0, '编辑服务成功'), iurl('iservice/service/list'), 'ajax');
	}
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_category') . " where uniacid = :uniacid" ,array(':uniacid' => 0));
	$providers = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_provider') . " where uniacid = :uniacid" ,array(':uniacid' => 0));
}

elseif($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_iservice_service', array('status' => $status), array('uniacid' => 0, 'id' => $id));
	imessage(error(0, ''), '', 'ajax');
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_iservice_service', array('uniacid' => 0, 'id' => $id));
	imessage(error(0, '删除服务成功'), iurl('iservice/service/list'), 'ajax');
}
include itemplate('service');