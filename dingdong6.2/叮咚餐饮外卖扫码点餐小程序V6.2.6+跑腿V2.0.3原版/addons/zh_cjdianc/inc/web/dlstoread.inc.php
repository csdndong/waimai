<?php
global $_GPC, $_W;
$action = 'start';
//$GLOBALS['frames'] = $this->getMainMenu2();
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$list=pdo_getall('cjdc_storead',array('store_id'=>$storeid),array(),'','orderby ASC');
if($_GPC['op']=='delete'){
	$res=pdo_delete('cjdc_storead',array('id'=>$_GPC['id']));
	if($res){
		 message('删除成功！', $this->createWebUrl2('dlstoread'), 'success');
		}else{
			  message('删除失败！','','error');
		}
}
if($_GPC['status']){
	$data['status']=$_GPC['status'];
	$res=pdo_update('cjdc_storead',$data,array('id'=>$_GPC['id']));
	if($res){
		 message('编辑成功！', $this->createWebUrl2('dlstoread'), 'success');
		}else{
			  message('编辑失败！','','error');
		}
}
include $this->template('web/dlstoread');