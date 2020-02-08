<?php

global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$list = pdo_getall('cjdc_rzqx',array('uniacid' => $_W['uniacid']),array(),'','num asc');
if($_GPC['id']){
    $res=pdo_delete('cjdc_rzqx',array('id'=>$_GPC['id']));
    if($res){
        message('删除成功',$this->createWebUrl('rzqx',array()),'success');
    }else{
        message('删除失败','','error');
    }
}
include $this->template('web/rzqx');