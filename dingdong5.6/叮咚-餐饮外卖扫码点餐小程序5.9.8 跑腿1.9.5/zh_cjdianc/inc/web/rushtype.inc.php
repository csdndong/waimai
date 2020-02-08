<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$list = pdo_getall('cjdc_qgtype',array('uniacid' => $_W['uniacid']),array(),'','num ASC');
if($_GPC['op']=='del'){
    $res=pdo_delete('cjdc_qgtype',array('id'=>$_GPC['id']));
    if($res){
        message('删除成功',$this->createWebUrl('rushtype',array()),'success');
    }else{
        message('删除失败','','error');
    }
}
if($_GPC['op']=='upd'){
	$res=pdo_update('cjdc_qgtype',array('state'=>$_GPC['state']),array('id'=>$_GPC['id']));
	if($res){
			message('修改成功',$this->createWebUrl('rushtype',array()),'success');
		}else{
		message('修改失败','','error');
		}
}
include $this->template('web/rushtype');