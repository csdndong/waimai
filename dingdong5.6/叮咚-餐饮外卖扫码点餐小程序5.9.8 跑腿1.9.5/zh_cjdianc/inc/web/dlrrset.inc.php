<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
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
		message('编辑成功',$this->createWebUrl2('dlrrset',array()),'success');
	}else{
		message('编辑失败','','error');
	}
}



include $this->template('web/dlrrset');