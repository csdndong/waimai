<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$list=pdo_getall('cjdc_dyj',array('store_id'=>$storeid,'uniacid'=>$_W['uniacid']));
if($_GPC['id']){
	$result = pdo_delete('cjdc_dyj', array('id'=>$_GPC['id']));
		if($result){
			message('删除成功',$this->createWebUrl('print',array()),'success');
		}else{
			message('删除失败','','error');
		}
}
include $this->template('web/print');