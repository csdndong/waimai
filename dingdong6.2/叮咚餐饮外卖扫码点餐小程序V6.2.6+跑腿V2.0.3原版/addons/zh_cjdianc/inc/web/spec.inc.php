<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$list = pdo_getall('wpdc_spec',array('goods_id' => $_GPC['dishes_id']));
if($_GPC['id']){
		$result = pdo_delete('wpdc_spec', array('id'=>$_GPC['id']));
		if($result){
			message('删除成功',$this->createWebUrl('spec',array('dishes_id'=>$_GPC['dishes_id'])),'success');
		}else{
		message('删除失败','','error');
		}
	
}
include $this->template('web/spec');