<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$id=$_GPC['id'];
$item = pdo_get('cjdc_reservation',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
if (checksubmit('submit')) {
    $data = array(
        'uniacid' =>$_W['uniacid'],
        'store_id' => $storeid,
        'time' => trim($_GPC['time']),
        'label' => trim($_GPC['label']),
        'dateline' => TIMESTAMP,
        'num'=>$_GPC['num']
        );

    if (empty($id)) {
        pdo_insert('cjdc_reservation', $data);
    } else {
        unset($data['dateline']);
        pdo_update('cjdc_reservation', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
    }
    message('操作成功！', $this->createWebUrl2('dlreservation', array('op' => 'display', 'storeid' => $storeid)), 'success');
}

include $this->template('web/dladdreservation');