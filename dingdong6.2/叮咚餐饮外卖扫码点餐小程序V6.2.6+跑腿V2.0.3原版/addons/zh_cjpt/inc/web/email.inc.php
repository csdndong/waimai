<?php

global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$info = pdo_get('cjpt_mail',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){              
	$data2['username']=trim($_GPC['username']);
	$data2['password']=trim($_GPC['password']);
	$data2['type']=$_GPC['type'];
	$data2['sender']=trim($_GPC['sender']);
	$data2['uniacid']=trim($_W['uniacid']);
	$data2['is_email']=trim($_GPC['is_email']);
	$data2['signature']=trim($_GPC['signature']);
	if($_GPC['id']==''){        
		$res=pdo_insert('cjpt_mail',$data2);
		if($res){
			message('添加成功',$this->createWebUrl('email',array()),'success');
		}else{
			message('添加失败','','error');
		}
	}else{
		$res = pdo_update('cjpt_mail', $data2, array('id' => $_GPC['id']));
		if($res){
			message('编辑成功',$this->createWebUrl('email',array()),'success');
		}else{
			message('编辑失败','','error');
		}

	}

}

include $this->template('web/email');



