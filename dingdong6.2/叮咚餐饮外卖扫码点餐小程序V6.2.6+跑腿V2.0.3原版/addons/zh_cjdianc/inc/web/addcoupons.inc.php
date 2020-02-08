<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$list = pdo_get('cjdc_coupons',array('id'=>$_GPC['id']));
$type = pdo_getall('cjdc_storetype',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
		$data['name']=$_GPC['name'];
		$data['full']=$_GPC['full'];
		$data['reduce']=$_GPC['reduce'];
		$data['number']=$_GPC['number'];
		$data['start_time']=$_GPC['time']['start'];
		$data['end_time']=$_GPC['time']['end'];
		$data['uniacid']=$_W['uniacid'];
		$data['type']=$_GPC['type'];
		$data['type_id']=$_GPC['type_id'];
		$data['instruction']=$_GPC['instruction'];
	if($_GPC['id']==''){
		$data['stock']=$_GPC['number'];
		$res=pdo_insert('cjdc_coupons',$data);
		if($res){
			message('添加成功',$this->createWebUrl('coupons',array()),'success');
		}else{
			message('添加失败','','error');
		}
	}else{
		$data['stock']=$_GPC['stock'];
		$res = pdo_update('cjdc_coupons', $data, array('id' => $_GPC['id']));
		if($res){
			message('编辑成功',$this->createWebUrl('coupons',array()),'success');
		}else{
			message('编辑失败','','error');
		}
	}
}
include $this->template('web/addcoupons');