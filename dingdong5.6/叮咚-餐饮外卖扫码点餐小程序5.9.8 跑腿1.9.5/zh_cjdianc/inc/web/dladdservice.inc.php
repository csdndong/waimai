<?php
global $_GPC, $_W;
$action = 'start';
$storeid=$_COOKIE["storeid"];
$uid=$_COOKIE["uid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$id=$_GPC['id'];
$item = pdo_get('cjdc_service',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
if (checksubmit('submit')) {
    $data = array(
        'uniacid' =>$_W['uniacid'],
        'store_id' => $storeid,
        'time' => trim($_GPC['time']),
        'dateline' => TIMESTAMP,
        'num'=>$_GPC['num']
        );

    if (empty($id)) {
        pdo_insert('cjdc_service', $data);
    } else {
        unset($data['dateline']);
        pdo_update('cjdc_service', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
    }
    message('操作成功！', $this->createWebUrl2('dlservice', array('op' => 'display', 'storeid' => $storeid)), 'success');
}

include $this->template('web/dladdservice');