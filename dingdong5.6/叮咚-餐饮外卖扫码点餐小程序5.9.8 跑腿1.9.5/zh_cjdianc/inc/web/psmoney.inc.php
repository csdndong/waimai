<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$store_id=$_GPC['id'];

$list=pdo_getall('cjdc_distribution',array('store_id'=>$store_id));
if($_GPC['op']=='delete'){
	$res=pdo_delete('cjdc_distribution',array('id'=>$_GPC['id']));
	if($res){
		 message('删除成功！', $this->createWebUrl('psmoney',array('id'=>$_GPC['store_id'])), 'success');
		}else{
			  message('删除失败！','','error');
		}
}
include $this->template('web/psmoney');