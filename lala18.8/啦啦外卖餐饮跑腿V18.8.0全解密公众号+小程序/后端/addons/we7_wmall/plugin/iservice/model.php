<?php
defined('IN_IA') or exit('Access Denied');

function iservice_category_fetchall($filter = array()) {
	global $_W, $_GPC;
	if(empty($filter)) {
		$filter = $_GPC;
	} else {
		$filter = array_merge($_GPC, $filter);
	}
	$condition = ' where uniacid = :uniacid ';
	$params = array(
		':uniacid' => 0
	);
	$status = isset($filter['status']) ? intval($filter['status']) : -1;
	if($status > -1) {
		$condition .= ' and status = :status ';
		$params[':status'] = $status;
	}
	$limit = '';
	$result = array();
	if(!defined('IN_WXAPP') && !defined('IN_VUE')) {
		$pindex = max(1, intval($filter['page']));
		$psize = intval($filter['psize']) > 0 ? intval($filter['psize']) : 15;
		$limit = ' limit ' . ($pindex - 1) * $psize . ',' . $psize;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_iservice_category') . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		$result['pager'] = $pager;
	}
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_iservice_category') . $condition . ' order by displayorder desc,id asc ' . $limit, $params, 'id');
	if(!empty($categorys)) {
		foreach($categorys as &$cate) {
			$cate['thumb'] = tomedia($cate['thumb']);
		}
	}
	$result['categorys'] = $categorys;
	return $result;
}

function iservice_service_fetchall() {
	global $_W, $_GPC;
	if(empty($filter)) {
		$filter = $_GPC;
	} else {
		$filter = array_merge($_GPC, $filter);
	}
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => 0
	);
	$pid = intval($filter['pid']);
	if($pid > 0) {
		$condition .= ' and pid = :pid';
		$params[':pid'] = $pid;
	}
	$cid = intval($filter['cid']);
	if($cid > 0) {
		$condition .= ' and cid = :cid';
		$params[':cid'] = $cid;
	}
	$keyword = trim($filter['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (title like :keyword or id = :keyword)';
		$params[':keyword'] = $keyword;
	}
	$order_by_type = trim($filter['order_by_type'])? trim($filter['order_by_type']): 'displayorder';
	$order_by = " ORDER BY {$order_by_type} DESC, id desc";
	$pindex = max(1, intval($filter['page']));
	$psize = intval($filter['psize']) > 0 ? intval($filter['psize']) : 15;
	$result = array();
	if(!defined('IN_WXAPP') && !defined('IN_VUE')) {
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_iservice_service') . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		$result['pager'] = $pager;
	}
	if($order_by_type == 'total') {
		$services = pdo_fetchall('SELECT *, CASE total WHEN -1 THEN 10000000 ELSE total END AS order_by_total FROM ' . tablename('tiny_wmall_iservice_service') . " {$condition} ORDER BY order_by_total ASC, id desc LIMIT " . ($pindex - 1) * $psize.','.$psize, $params);
	} else {
		$services = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_service') . " {$condition} {$order_by} LIMIT " . ($pindex - 1) * $psize.','.$psize, $params);
	}
	if(!empty($services)) {
		foreach($services as &$svalue) {
			$svalue['thumb'] = tomedia($svalue['thumb']);
			$svalue['label'] = iunserializer($svalue['label']);
		}
	}
	$result['services'] = $services;
	return $result;
}

function iservice_service_fetch($id, $extra = array()) {
	global $_W, $_GPC;
	$service = pdo_get('tiny_wmall_iservice_service', array('uniacid' => 0, 'id' => $id));
	if(!empty($service)) {
		$service['attrs'] = iunserializer($service['attrs']);
		if(!empty($service['attrs'])) {
			foreach($service['attrs'] as &$aval) {
				$aval['label'] = implode(',', $aval['label']);
			}
		}
		$service['label'] = iunserializer($service['label']);
		$service['thumbs'] = iunserializer($service['thumbs']);
		$service['thumb'] = tomedia($service['thumb']);

		if(!empty($extra['get_option']) && $service['is_options']) {
			$service['options'] = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_service_options') . ' WHERE uniacid = :uniacid AND service_id = :service_id ORDER BY displayorder DESC, id ASC', array(':uniacid' => 0, ':service_id' => $id));
			if(empty($service['options'])) {
				$service['is_options'] = 0;
			} else {
				$service['price'] = $service['options'][0]['price'];
			}
		}
		if(!empty($extra['get_provider'])) {
			$service['provider'] = pdo_get('tiny_wmall_iservice_provider', array('uniacid' => 0, 'id' => $service['pid']));
		}
	}
	return $service;
}

function iservice_get_diypage($pageOrid, $mobile = false, $extra = array()) {
	global $_W;
	if(is_array($pageOrid)) {
		$page = $pageOrid;
	} else {
		$id = intval($pageOrid);
		if(empty($id)) {
			return false;
		}
		$page = pdo_get('tiny_wmall_iservice_diypage', array('uniacid' => 0, 'id' => $id));
	}
	if(empty($page)) {
		return false;
	}
	$page['data'] = base64_decode($page['data']);
	$page['data'] = json_decode($page['data'], true);
	$page['parts'] = array();

	if(!$mobile) {
		if(!empty($page['data']['items']) && is_array($page['data']['items'])) {
			foreach($page['data']['items'] as $itemid => &$item) {
				if($item['id'] == 'navs') {
					$result = iservice_get_navs($item);
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					$item['row'] = $result['row'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'richtext') {
					$item['params']['content'] = htmlspecialchars_decode($item['params']['content']);
				}  elseif($item['id'] == 'activity') {
					$result = iservice_get_cubes($item);
					$item['data'] = $result['data'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'picture') {
					if(empty($item['style'])) {
						$item['style'] = array(
							'background' => '#ffffff',
							'paddingtop' => '0',
							'paddingleft' => '0'
						);
					}
					if(empty($item['params'])) {
						$item['params'] = array(
							'picturedata' => 0,
						);
					}
					$result = iservice_get_slides($item);
					$item['data'] = $result['data'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} else {
					if($item['id'] == 'picturew') {
						if(empty($item['style'])) {
							$item['style'] = array(
								'background' => '#ffffff',
								'paddingtop' => '0',
								'paddingleft' => '0'
							);
						}
					} elseif(empty($item['id'])) {
						unset($page['data']['items'][$itemid]);
					}
				}
			}
			unset($item);
			pdo_update('tiny_wmall_iservice_diypage', array('data' => base64_encode(json_encode($page['data']))), array('uniacid' => 0, 'id' => $id));
		}
	} else {
		if(!empty($page['data']['items']) && is_array($page['data']['items'])) {
			foreach($page['data']['items'] as $itemid => &$item) {
				if($item['id'] == 'richtext') {
					$item['params']['content'] = base64_decode($item['params']['content']);
				} elseif(in_array($item['id'], array('copyright', 'img_card'))) {
					$item['params']['imgurl'] = tomedia($item['params']['imgurl']);
					if($item['id'] == 'notice') {
						$item['data'] = get_wxapp_notice($item, true);
						if(empty($item['data'])) {
							unset($page['data']['items'][$itemid]);
						}
					}
				} elseif(in_array($item['id'], array('banner')) && !empty($item['data'])) {
					foreach($item['data'] as &$v) {
						$v['imgurl'] = tomedia($v['imgurl']);
					}
				} elseif($item['id'] == 'picturew' && !empty($item['data'])) {
					foreach($item['data'] as &$v) {
						$v['imgurl'] = tomedia($v['imgurl']);
					}
					$item['data_num'] = count($item['data']);
					if(in_array($item['params']['row'], array('1','5','6'))) {
						$item['data'] = array_values($item['data']);
					} else {
						if($item['params']['showtype'] == 1 && count($item['data']) > $item['params']['pagenum']) {
							$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
							$item['style']['rows_num'] = ceil($item['params']['pagenum']/$item['params']['row']);
							$row_base_height = array(
								'2' => 122,
								'3' => 85,
								'4' => 65,
							);
							$item['style']['base_height'] = $row_base_height[$item['params']['row']];
						}
					}
				} elseif($item['id'] == 'navs' && !empty($item['data'])) {
					$result = iservice_get_navs($item, true);
					$item['data'] = $result['data'];
					$item['data_num'] = $result['data_num'];
					$item['row'] = $result['row'];
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'activity') {
					$result = iservice_get_cubes($item, true);
					$item['data'] = array_values($result['data']);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				} elseif($item['id'] == 'picture') {
					$result = get_wxapp_slides($item, true);
					$item['data'] = array_values($result['data']);
					if(empty($item['data'])) {
						unset($page['data']['items'][$itemid]);
					}
				}
			}
			unset($item);
		}
	}
	return $page;
}

function iservice_get_slides($item, $mobile = false) {
	global $_W;
	if(empty($item['params']['picturedata'])) {
		if(!empty($item['data'])) {
			foreach($item['data'] as &$val) {
				$val['imgurl'] = tomedia($val['imgurl']);
			}
		}
	} else {
		if($item['params']['picturedata'] == 1) {
			$condition = ' where uniacid = :uniacid and type = :type and status = 1 ';
			$params = array(
				':uniacid' => 0,
				':type' => 'service'
			);
			$slides = pdo_fetchall("select * from " . tablename('tiny_wmall_iservice_slide') . $condition . ' order by displayorder desc', $params);
		}

		$item['data'] = array();
		if(!empty($slides)){
			foreach($slides as $val) {
				$childid = rand(1000000000, 9999999999);
				$childid = "C{$childid}";
				$item['data'][$childid] = array(
					'linkurl' => empty($val['link'])? $val['wxapp_link']:$val['link'],
					'imgurl' => tomedia($val['thumb']),
				);
			}
		}
	}
	$result = array(
		'data' => $item['data']
	);
	return $result;
}

function iservice_get_navs($item, $mobile = false) {
	global $_W;
	if($item['params']['navsdata'] == 0) {
		if(!empty($item['data'])) {
			foreach($item['data'] as &$val) {
				$val['imgurl'] = tomedia($val['imgurl']);
			}
		}
	} else {
		if($item['params']['navsdata'] == 1) {
			$empty_link = 'package/pages/iservice/category?cid=';
			$condition = ' where uniacid = :uniacid';
			$params = array(
				':uniacid' => 0
			);
			$limit = intval($item['params']['navsnum']) ? intval($item['params']['navsnum']) : 4;
			$navs = pdo_fetchall("select * from " .tablename('tiny_wmall_iservice_category') . $condition . ' and status = 1 order by displayorder desc limit ' . $limit, $params);
		}
		$item['data'] = array();
		if(!empty($navs)){
			foreach($navs as $val) {
				$childid = rand(1000000000, 9999999999);
				$childid = "C{$childid}";
				if(in_array($item['params']['navsdata'], array(3, 4))) {
					$val['wxapp_link'] = $val['link'];
				}
				$item['data'][$childid] = array(
					'linkurl' => empty($val['wxapp_link']) ? (empty($empty_link) ? '' : "{$empty_link}{$val['id']}") : $val['wxapp_link'],
					'text' => $val['title'],
					'imgurl' => tomedia($val['thumb']),
				);
			}
		}
	}
	$item['data_num'] = count($item['data']);
	if($mobile && $item['params']['showtype'] == 1 && $item['data_num'] > $item['params']['pagenum']) {
		$item['data'] = array_chunk($item['data'], $item['params']['pagenum']);
	}

	$result = array(
		'data' => $item['data'],
		'data_num' => $item['data_num'],
		'row' => ceil($item['params']['pagenum']/$item['params']['rownum']),
	);
	return $result;
}

function iservice_get_cubes($item, $mobile = false) {
	global $_W;
	if(empty($item['params']['activitydata'])) {
		if(!empty($item['data'])) {
			foreach($item['data'] as &$val) {
				$val['imgurl'] = tomedia($val['imgurl']);
			}
		}
	} else {
		
	}
	$result = array(
		'data' => $item['data']
	);
	return $result;
}

function iservice_get_defaultpage($type = 'home', $from = '') {
	global $_W, $_GPC;
	if(empty($from)) {
		$from = $_GPC['from'];
	}
	$type = "{$from}$type";
	$pages = array(
		'vuehome' => array (
			'uniacid' => 0,
			'name' => '自定义公众号服务市场首页',
			'type' => '1',
			'data' => '',
			'updatetime' => 1531985983,
		),
		'wxapphome' => array (
			'uniacid' => 0,
			'name' => '自定义小程序服务市场首页',
			'type' => '1',
			'data' => '',
			'updatetime' => 1531985983,
		)
	);
	return $pages[$type];
}

function iservice_get_pages($filter = array(), $search = array('*')) {
	global $_W;
	$condition = ' where uniacid = :uniacid';
	$params = array(
		':uniacid' => 0,
	);
	$table = 'tiny_wmall_iservice_diypage';
	if(!empty($filter) && !empty($filter['type'])) {
		$condition .= ' and type = :type';
		$params[':type'] = intval($filter['type']);
	}
	if(!empty($search)) {
		$search = implode(',', $search);
	}
	$pages = pdo_fetchall("select {$search} from " . tablename($table) . $condition, $params);
	return $pages;
}






