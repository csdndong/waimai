<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info = pdo_get('cjdc_address',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
		if(checksubmit('submit')){
			$data['name']=$_GPC['name'];
			$data['num']=$_GPC['num'];
			$data['state']=$_GPC['state'];
			if($_GPC['level']){
				$data['level']=$_GPC['level'];
			}			
			$data['uniacid']=$_W['uniacid'];
			if($_GPC['fid']>0){
				$data['fid']=$_GPC['fid'];
			}
			if($_GPC['id']==''){				
				$res=pdo_insert('cjdc_address',$data);
				if($res){
					message('添加成功',$this->createWebUrl('address',array()),'success');
				}else{
					message('添加失败','','error');
				}
			}else{
				$res = pdo_update('cjdc_address', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl('address',array()),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/addaddress');