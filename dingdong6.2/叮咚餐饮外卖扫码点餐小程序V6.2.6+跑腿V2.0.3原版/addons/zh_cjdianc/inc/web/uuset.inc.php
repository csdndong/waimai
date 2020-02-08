<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();

$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$info=pdo_get('wpdc_uuset',array('store_id'=>$storeid,'uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
	$data['store_id']=$storeid;
	$data['appid']=$_GPC['appid'];
	$data['appkey']=$_GPC['appkey'];
	$data['account']=$_GPC['account'];
	$data['OpenId']=$_GPC['OpenId'];
	//$data['is_open']=$_GPC['is_open'];
	$data['is_check']=$_GPC['is_check'];		
	$data['uniacid']=$_W['uniacid'];
	if($_GPC['id']==''){
		$res = pdo_insert('wpdc_uuset', $data);
	}else{
		$res = pdo_update('wpdc_uuset', $data, array('store_id' => $storeid));
	}	
	if($res){
		message('编辑成功',$this->createWebUrl('uuset',array()),'success');
	}else{
		message('编辑失败','','error');
	}
}



include $this->template('web/uuset');