<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){        
	$data['tx_zdmoney']=$_GPC['tx_zdmoney'];
	$data['tx_sxf']=$_GPC['tx_sxf'];
	$data['tx_notice']=html_entity_decode($_GPC['tx_notice']);
	$data['uniacid']=$_W['uniacid'];          
	if($_GPC['id']==''){                
		$res=pdo_insert('cjpt_system',$data);
		if($res){
			message('添加成功',$this->createWebUrl('txsz',array()),'success');
		}else{
			message('添加失败','','error');
		}
	}else{
		$res = pdo_update('cjpt_system', $data, array('id' => $_GPC['id']));
		if($res){
			message('编辑成功',$this->createWebUrl('txsz',array()),'success');
		}else{
			message('编辑失败','','error');
		}
	}
}
include $this->template('web/txsz');