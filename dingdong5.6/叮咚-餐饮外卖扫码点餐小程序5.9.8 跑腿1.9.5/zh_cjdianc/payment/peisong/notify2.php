<?php
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
global $_W, $_GPC;
$input = file_get_contents('php://input');
$isxml = true;
$data = json_decode($input, true);
pdo_update('cjdc_order',array('kfw_info'=>$input),array('ship_id'=>$data['ship_id']));
 if($data['order_status']==4){
	pdo_update('cjdc_order',array('state'=>4,'complete_time'=>date('Y-m-d H:i:s',time())),array('ship_id'=>$data['ship_id']));
	return 'success';
}

load()->web('common');
load()->model('mc');
load()->func('communication');
$_W['uniacid'] = $_W['weid'] = intval($get['attach']);

$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
$paySetting = uni_setting($_W['uniacid'], array('payment'));








