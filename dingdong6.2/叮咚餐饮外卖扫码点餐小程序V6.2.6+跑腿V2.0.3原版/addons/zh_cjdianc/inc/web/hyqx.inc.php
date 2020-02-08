<?php

global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$list = pdo_getall('cjdc_hyqx',array('uniacid' => $_W['uniacid']),array(),'','num asc');
if($_GPC['id']){
    $res=pdo_delete('cjdc_hyqx',array('id'=>$_GPC['id']));
    if($res){
        message('删除成功',$this->createWebUrl('hyqx',array()),'success');
    }else{
        message('删除失败','','error');
    }
}
include $this->template('web/hyqx');