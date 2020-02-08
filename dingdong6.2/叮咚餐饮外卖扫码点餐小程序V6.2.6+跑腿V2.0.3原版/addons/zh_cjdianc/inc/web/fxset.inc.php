<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item = pdo_get('cjdc_fxset', array('uniacid' => $_W['uniacid']));
if (checksubmit('submit')) {
    $data['tx_rate'] = $_GPC['tx_rate'];
    $data['fx_name'] = $_GPC['fx_name'];
    $data['is_open'] = $_GPC['is_open'];
    $data['is_type'] = $_GPC['is_type'];
    $data['type'] = $_GPC['type'];
    $data['is_check'] = $_GPC['is_check'];
    $data['is_ej'] = $_GPC['is_ej'];
    $data['tx_money'] = $_GPC['tx_money'];
    $data['dn_yj'] = $_GPC['dn_yj'];
    $data['dn_ej'] = $_GPC['dn_ej'];
    $data['wm_yj'] = $_GPC['wm_yj'];
    $data['wm_ej'] = $_GPC['wm_ej'];
    $data['img'] = $_GPC['img'];
    $data['img2'] = $_GPC['img2'];
    $data['xx_name'] = $_GPC['xx_name'];
    $data['uniacid'] = $_W['uniacid'];
    $data['tx_details'] = html_entity_decode($_GPC['tx_details']);
    $data['fx_details'] = html_entity_decode($_GPC['fx_details']);
    $data['instructions'] = html_entity_decode($_GPC['instructions']);
    if ($_GPC['id'] == '') {
        $res = pdo_insert('cjdc_fxset', $data);
        if ($res) {
            message('添加成功', $this->createWebUrl('fxset', array()), 'success');
        } else {
            message('添加失败', '', 'error');
        }
    } else {
        $res = pdo_update('cjdc_fxset', $data, array('id' => $_GPC['id']));
        if ($res) {
            message('编辑成功', $this->createWebUrl('fxset', array()), 'success');
        } else {
            message('编辑失败', '', 'error');
        }
    }
}
include $this->template('web/fxset');