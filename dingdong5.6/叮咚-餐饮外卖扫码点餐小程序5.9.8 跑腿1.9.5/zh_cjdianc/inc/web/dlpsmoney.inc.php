<?php
global $_GPC, $_W;
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$list=pdo_getall('cjdc_distribution',array('store_id'=>$storeid));
if($_GPC['id']){
	$res=pdo_delete('cjdc_distribution',array('id'=>$_GPC['id']));
	if($res){
		 message('删除成功！', $this->createWebUrl2('dlpsmoney'), 'success');
		}else{
			  message('删除失败！','','error');
		}
}
include $this->template('web/dlpsmoney');