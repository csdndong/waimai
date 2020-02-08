<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$list=pdo_getall('wpdc_voucher',array('uniacid' => $_W['uniacid'],'store_id'=>$storeid));
if($_GPC['id']){
	$result = pdo_delete('wpdc_voucher', array('id'=>$_GPC['id']));
		if($result){
			message('删除成功',$this->createWebUrl('voucher',array()),'success');
		}else{
			message('删除失败','','error');
		}
}
include $this->template('web/voucher');