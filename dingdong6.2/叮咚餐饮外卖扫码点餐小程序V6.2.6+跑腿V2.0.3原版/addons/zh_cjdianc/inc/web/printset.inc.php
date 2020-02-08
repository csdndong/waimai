<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$info=pdo_get('cjdc_storeset',array('store_id'=>$storeid));
if(checksubmit('submit')){
	$data['print_type']=$_GPC['print_type'];
	$data['print_mode']=$_GPC['print_mode'];
	$data['store_id']=$storeid;	
	$res = pdo_update('cjdc_storeset', $data, array('store_id' => $storeid));
	if($res){
		message('编辑成功',$this->createWebUrl('printset',array()),'success');
	}else{
		message('编辑失败','','error');
	}
}
include $this->template('web/printset');