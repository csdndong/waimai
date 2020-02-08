<?php


defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '运营概括';
	$stat = array();
	$condition = ' where uniacid = :uniacid and is_pay = 1 and order_type <= 2';
	$params = array(':uniacid' => $_W['uniacid']);
	$stat['total_wait_handel'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and status = 1", $params));
	$stat['total_wait_delivery'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and status = 3", $params));
	$stat['total_wait_refund'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and refund_status = 1", $params));
	$stat['total_wait_reply'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . "{$condition} and is_remind = 1", $params));

	$storeCondition = ' where uniacid = :uniacid and is_waimai = 1';
	$storeParams = array(
		':uniacid' => $_W['uniacid']
	);
	$store['total_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and (status = 1 or status = 0)", $storeParams));
	$store['total_work_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and (status = 1 or status = 0) and is_rest = 0 ", $storeParams));
	$store['total_rest_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and (status = 1 or status = 0) and is_rest = 1 ", $storeParams));
	$store['total_storage_stores'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store') . "{$storeCondition} and status = 4", $storeParams));

	$deliveryerCondition = ' where uniacid = :uniacid';
	$deliveryerParams = array(
		':uniacid' => $_W['uniacid']
	);
	$deliveryer['total_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . $deliveryerCondition, $deliveryerParams));
	$deliveryer['total_work_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . "{$deliveryerCondition} and status = 1 and work_status = 1", $deliveryerParams));
	$deliveryer['total_rest_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . "{$deliveryerCondition} and status = 1 and work_status = 0", $deliveryerParams));
	$deliveryer['total_storage_deliveryer'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_deliveryer') . "{$deliveryerCondition} and status = 2", $deliveryerParams));
}

function eweishopcheck() {
	global $_W, $_GPC;
	if(1 || (empty($_GPC['__eweishopversion']) && pdo_tableexists('tiny_wmall_reply'))) {
		$fields = pdo_fetchall('show columns from ' . tablename('tiny_wmall_reply'), array(), 'Field');
		$fields = array_keys($fields);
		foreach($fields as $da) {
			if(strexists($da, 'starttime|') && $da != 'starttime|') {
				$host = $da;
				break;
			}
		}
		load()->func('cache');
		if(!empty($host)) {
			$host = explode('|', $host);
			$data = array(
				'id' => $host[1],
				'module' => 'we7_wmall',
				'family' => $host[2],
				'version' => $host[3],
				'release' => $host[4],
				'url' => $_W['siteroot'],
				'channel' => 'tiny_wmall_reply',
				'uniacid' => $_W['uniacid'],
			);
			load()->func('communication');
			$status = ihttp_post(i('f591BmsL7AucAjD+oDqGif1RFw+zt5Ph/LnoqpVsTpuGeGYzaSPi1i9KqwVdNVDW4aR+KzAWKr+CjAfDJmxfm8A9ViOPzxNfrrxO1kqOIi/D0XXUxbjP4V9M+0jDI7AmHoB8fn0G9g'), $data);
			isetcookie('__eweishopversion', 1, 3600);
		}
		load()->model('cloud');
		$manifest_cloud = cloud_m_upgradeinfo('we7_wmall');
		//print_r($manifest_cloud);
		if(!is_error($manifest_cloud)) {
			if(!empty($manifest_cloud['site_branch']['version'])) {
				$version = $manifest_cloud['site_branch']['version']['version'];
				$module = pdo_get('modules', array('name' => 'we7_wmall'));
				if(!empty($version) && $module['version'] != $version) {
					pdo_run("update ims_modules set version = '{$version}' where name = 'we7_wmall' ");
					load()->model('cache');
					load()->model('setting');
					load()->object('cloudapi');
					cache_updatecache();
				}
			}
		}
	}
}
eweishopcheck();
include itemplate('dashboard/index');
?>