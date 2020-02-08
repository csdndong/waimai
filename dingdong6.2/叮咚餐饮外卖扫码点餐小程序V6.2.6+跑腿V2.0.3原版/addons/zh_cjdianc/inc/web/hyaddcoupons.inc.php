<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$list = pdo_get('cjdc_coupons',array('id'=>$_GPC['id']));
if(checksubmit('submit')){
		$data['name']=$_GPC['name'];
		$data['full']=$_GPC['full'];
		$data['reduce']=$_GPC['reduce'];
		$data['day']=$_GPC['day'];
		$data['is_hy']=1;
		$data['uniacid']=$_W['uniacid'];
		$data['instruction']=$_GPC['instruction'];
	if($_GPC['id']==''){
		$res=pdo_insert('cjdc_coupons',$data);
		if($res){
			message('添加成功',$this->createWebUrl('hycoupons',array()),'success');
		}else{
			message('添加失败','','error');
		}
	}else{
		$res = pdo_update('cjdc_coupons', $data, array('id' => $_GPC['id']));
		if($res){
			message('编辑成功',$this->createWebUrl('hycoupons',array()),'success');
		}else{
			message('编辑失败','','error');
		}
	}
}
include $this->template('web/hyaddcoupons');