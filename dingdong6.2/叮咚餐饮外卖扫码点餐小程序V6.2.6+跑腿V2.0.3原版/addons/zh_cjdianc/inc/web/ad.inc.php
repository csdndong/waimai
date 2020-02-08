<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$list=pdo_getall('cjdc_ad',array('uniacid'=>$_W['uniacid']),array(),'','orderby ASC');
if($_GPC['op']=='delete'){
	$res=pdo_delete('cjdc_ad',array('id'=>$_GPC['id']));
	if($res){
		 message('删除成功！', $this->createWebUrl('ad'), 'success');
		}else{
			  message('删除失败！','','error');
		}
}
if($_GPC['status']){
	$data['status']=$_GPC['status'];
	$res=pdo_update('cjdc_ad',$data,array('id'=>$_GPC['id']));
	if($res){
		 message('编辑成功！', $this->createWebUrl('ad'), 'success');
		}else{
			  message('编辑失败！','','error');
		}
}
include $this->template('web/ad');