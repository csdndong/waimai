<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();

$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$info=pdo_get('wpdc_rrset',array('store_id'=>$storeid,'uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
	$data['store_id']=$storeid;
	$data['username']=$_GPC['username'];
	$data['appkey']=$_GPC['appkey'];	
	//$data['is_open']=$_GPC['is_open'];	
	$data['uniacid']=$_W['uniacid'];
	if($_GPC['id']==''){
		$res = pdo_insert('wpdc_rrset', $data);
	}else{
		$res = pdo_update('wpdc_rrset', $data, array('store_id' => $storeid));
	}	
	if($res){
		message('编辑成功',$this->createWebUrl('rrset',array()),'success');
	}else{
		message('编辑失败','','error');
	}
}



include $this->template('web/rrset');