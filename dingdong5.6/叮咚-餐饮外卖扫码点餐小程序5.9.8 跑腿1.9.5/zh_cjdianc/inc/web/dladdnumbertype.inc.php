<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);

$list = pdo_get('cjdc_numbertype',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
if(checksubmit('submit')){
	$data['typename']=$_GPC['typename'];
	$data['store_id']=$storeid;
	$data['sort']=$_GPC['sort'];
	$data['time']=time();
	$data['uniacid']=$_W['uniacid'];
	if($_GPC['id']==''){	
		$res=pdo_insert('cjdc_numbertype',$data);
		if($res){
			message('添加成功',$this->createWebUrl2('dlnumbertype',array()),'success');
		}else{
			message('添加失败','','error');
		}
	}else{		
		$res = pdo_update('cjdc_numbertype', $data, array('id' => $_GPC['id']));
		if($res){
			message('编辑成功',$this->createWebUrl2('dlnumbertype',array()),'success');
		}else{
			message('编辑失败','','error');
		}
	}
}
include $this->template('web/dladdnumbertype');