<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"]; 
$cur_store = $this->getStoreById($storeid); 

$info=pdo_get('cjdc_goods',array('id'=>$_GPC['id']));

$type=pdo_getall('cjdc_type',array('uniacid'=>$_W['uniacid'],'store_id'=>$storeid),array(),'','order_by asc');
if(!$type){
	message('请先添加分类',$this->createWebUrl('adddishestype',array()),'error');
}
$dytag=pdo_getall('cjdc_dytag',array('uniacid'=>$_W['uniacid'],'store_id'=>$storeid),array(),'','sort asc');

include $this->template('web/adddishes');