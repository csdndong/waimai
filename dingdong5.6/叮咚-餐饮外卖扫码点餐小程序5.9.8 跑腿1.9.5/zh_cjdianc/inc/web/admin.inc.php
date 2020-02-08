<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
 $sql="select a.* ,b.md_name from " . tablename("wpdc_seller") . " a"  . " left join " . tablename("wpdc_store") . " b on b.id=a.store_id   WHERE a.uniacid=:uniacid";
$list=pdo_fetchall($sql,array(':uniacid'=>$_W['uniacid']));
if($_GPC['op']=='delete'){
	$res=pdo_delete('wpdc_seller',array('id'=>$_GPC['id']));
	if($res){
		 message('删除成功！', $this->createWebUrl('admin'), 'success');
		}else{
			  message('删除失败！','','error');
		}
}
if($_GPC['state']){
	$data['state']=$_GPC['state'];
	$res=pdo_update('wpdc_seller',$data,array('id'=>$_GPC['id']));
	if($res){
		 message('编辑成功！', $this->createWebUrl('admin'), 'success');
		}else{
			  message('编辑失败！','','error');
		}
}
include $this->template('web/admin');