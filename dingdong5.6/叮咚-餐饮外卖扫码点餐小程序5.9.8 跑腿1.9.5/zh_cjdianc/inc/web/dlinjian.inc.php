<?php
global $_GPC, $_W;
$action = 'start';
//$GLOBALS['frames'] = $this->getMainMenu2();
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$list=pdo_getall('cjdc_reduction',array('store_id'=>$storeid));
if($_GPC['id']){
	$res=pdo_delete('cjdc_reduction',array('id'=>$_GPC['id']));
	if($res){
		 message('删除成功！', $this->createWebUrl2('dlinjian'), 'success');
		}else{
			  message('删除失败！','','error');
		}
}
include $this->template('web/dlinjian');