<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$list = pdo_get('wpdc_spec',array('id'=>$_GPC['id']));
			$data['name']=$_GPC['name'];
			$data['cost']=$_GPC['cost'];
			$data['goods_id']=$_GPC['dishes_id'];
		if(checksubmit('submit')){
			if($_GPC['id']==''){
				$res=pdo_insert('wpdc_spec',$data);
				if($res){
					message('添加成功',$this->createWebUrl('spec',array('dishes_id'=>$_GPC['dishes_id'])),'success');
				}else{
					message('添加失败','','error');
				}
			}else{
				$res = pdo_update('wpdc_spec', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl('spec',array('dishes_id'=>$_GPC['dishes_id'])),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/addspec');