<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$info=pdo_get('cjdc_call',array('store_id'=>$storeid));
if(checksubmit('submit')){
	$data['store_id']=$storeid;
	$data['is_open']=$_GPC['is_open'];
	$data['appid']=$_GPC['appid'];
	$data['apikey']=$_GPC['apikey'];
	$data['uniacid']=$_W['uniacid'];

	if($_GPC['id']){
		$res = pdo_update('cjdc_call', $data, array('store_id' => $storeid));
	}else{
		$res=pdo_insert('cjdc_call', $data);
	}

	if($res){
		message('编辑成功',$this->createWebUrl('call',array()),'success');
	}else{
		message('编辑失败','','error');
	}
}
if($_GPC['op']=='ok'){
	$res=pdo_update('cjdc_call',array('src'=>''),array('store_id'=>$storeid));
	if($res){
		message('操作成功',$this->createWebUrl('call',array()),'success');
	}else{
		message('操作失败','','error');
	}

}
include $this->template('web/call');