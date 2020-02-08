<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
if (checksubmit('submit')) {
	$timepoint = intval($_GPC['time_point']);
	$timecount = intval($_GPC['time_count']);
	$time = trim($_GPC['time']);
	if (empty($time)) {
		message('请输入起始时间点！', '', 'error');
	}
	if ($timecount <= 0) {
		message('创建数量不能小于0！', '', 'error');
	} else if ($timecount > 15) {
		message('创建数量不能大于15！', '', 'error');
	}
	$t = strtotime($time);
	for ($i = 0; $i < $timecount; $i++) {
		$time = date('H:i', $t);
		$num=$_GPC['num']+$i;
		$ishave = pdo_fetch("SELECT * FROM " . tablename('cjdc_reservation') . " WHERE uniacid = :uniacid AND store_id = :storeid  AND time=:time", array(':uniacid' => $_W['uniacid'], ':storeid' => $storeid, ':time' => $time));
		$data = array(
			'uniacid' => $_W['uniacid'],
			'store_id' => $storeid,
			'time' => $time,
			'dateline' => TIMESTAMP,
			'num'=>$num
			);
		if (empty($ishave)) {
			pdo_insert('cjdc_reservation', $data);
		}
		$t = strtotime($time) + $timepoint * 60;
	}
	message('操作成功！', $this->createWebUrl('reservation', array('op' => 'display', 'storeid' => $storeid)), 'success');
}
include $this->template('web/allreservation');