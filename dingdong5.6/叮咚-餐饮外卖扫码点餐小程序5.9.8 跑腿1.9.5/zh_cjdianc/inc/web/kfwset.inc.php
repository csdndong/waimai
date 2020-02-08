<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();

$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$info=pdo_get('cjdc_kfwset',array('store_id'=>$storeid,'uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
	$data['store_id']=$storeid;
	$data['user_id']=$_GPC['user_id'];	
	$data['uniacid']=$_W['uniacid'];
	if($_GPC['id']==''){
		$res = pdo_insert('cjdc_kfwset', $data);
	}else{
		$res = pdo_update('cjdc_kfwset', $data, array('store_id' => $storeid));
	}	
	if($res){
		message('编辑成功',$this->createWebUrl('kfwset',array()),'success');
	}else{
		message('编辑失败','','error');
	}
}



include $this->template('web/kfwset');