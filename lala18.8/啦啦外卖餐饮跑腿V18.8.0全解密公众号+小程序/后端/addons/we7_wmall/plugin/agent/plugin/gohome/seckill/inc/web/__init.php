<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
function seckill_all_times() {
	$data = array();
	for($i = 0; $i < 24; $i++) {
		$data[] = $i;
	}
	return $data;
}
?>