<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$list=pdo_getall('cjdc_coupons',array('uniacid' => $_W['uniacid'],'store_id'=>$storeid));
if($_GPC['id']){
	$result = pdo_delete('cjdc_coupons', array('id'=>$_GPC['id']));
	if($result){
		message('删除成功',$this->createWebUrl2('dlincoupons',array()),'success');
	}else{
		message('删除失败','','error');
	}
}

include $this->template('web/dlincoupons');