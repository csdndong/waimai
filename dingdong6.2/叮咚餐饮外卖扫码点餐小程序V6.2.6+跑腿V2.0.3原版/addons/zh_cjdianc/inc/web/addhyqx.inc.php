<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
	$info = pdo_get('cjdc_hyqx',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
		if(checksubmit('submit')){
			$data['days']=$_GPC['days'];
			$data['money']=$_GPC['money'];
			$data['num']=$_GPC['num'];
			$data['uniacid']=$_W['uniacid'];
			if($_GPC['days']<=0){
				message('天数必须大于0!','','error');
			}
			if($_GPC['id']==''){				
				$res=pdo_insert('cjdc_hyqx',$data);
				if($res){
					message('添加成功',$this->createWebUrl('hyqx',array()),'success');
				}else{
					message('添加失败','','error');
				}
			}else{
				$res = pdo_update('cjdc_hyqx', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl('hyqx',array()),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/addhyqx');