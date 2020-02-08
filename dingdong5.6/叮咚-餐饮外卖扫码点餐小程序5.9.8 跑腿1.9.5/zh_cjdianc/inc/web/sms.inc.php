<?php
global $_GPC, $_W;
// $action = 'ad';
// $title = $this->actions_titles[$action];
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
	$data['appkey']=trim($_GPC['appkey']);
	$data['tpl_id']=trim($_GPC['tpl_id']);
	$data['is_dxyz']=$_GPC['is_dxyz'];
	$data['item']=$_GPC['item'];
	$data['appid']=$_GPC['appid'];
	$data['tx_appkey']=$_GPC['tx_appkey'];
	$data['template_id']=$_GPC['template_id'];
	$data['sign']=$_GPC['sign'];
	$data['code']=$_GPC['code'];
	if($_GPC['tpl_id']==''){
		message('短信模板id不能为空!','','error'); 
	}
	$data['uniacid']=trim($_W['uniacid']);
	if($_GPC['id']==''){                
		$res=pdo_insert('cjdc_message',$data);
		if($res){
			message('添加成功',$this->createWebUrl('sms',array()),'success');
		}else{
			message('添加失败','','error');
		}
	}else{
		$res = pdo_update('cjdc_message', $data, array('id' => $_GPC['id']));
		if($res){
			message('编辑成功',$this->createWebUrl('sms',array()),'success');
		}else{
			message('编辑失败','','error');
		}
	}
}
include $this->template('web/sms');