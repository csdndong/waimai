<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$info=pdo_get('cjdc_storeset',array('store_id'=>$storeid));
if(checksubmit('submit')){
		$data['is_chzf']=$_GPC['is_chzf'];
		$data['is_wxzf']=$_GPC['is_wxzf'];
		$data['is_czztpd']=$_GPC['is_czztpd'];
		$data['is_ydtime']=$_GPC['is_ydtime'];
		$data['is_yyzw']=$_GPC['is_yyzw'];
		$res = pdo_update('cjdc_storeset', $data, array('store_id' => $storeid));
		if($res){
			message('编辑成功',$this->createWebUrl2('dlintabelset',array()),'success');
		}else{
			message('编辑失败','','error');
		}
}
include $this->template('web/dlintabelset');