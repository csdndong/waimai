<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();

$list=pdo_getall('cjdc_coupons',array('uniacid' => $_W['uniacid'],'is_hy'=>1));
if($_GPC['id']){
	$result = pdo_delete('cjdc_coupons', array('id'=>$_GPC['id']));
	pdo_delete('cjdc_usercoupons',array('coupon_id'=>$_GPC['id']));
	if($result){
		message('删除成功',$this->createWebUrl('coupons',array()),'success');
	}else{
		message('删除失败','','error');
	}
}
include $this->template('web/hycoupons');