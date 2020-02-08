<?php
global $_GPC, $_W;
load()->func('tpl');
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getMainMenu2($storeid,$action);
if(checksubmit('submit')){
	$op=$_GPC['keywords'];
	$where="%$op%";
}else{
	$where='%%';
}
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select * from ". tablename("wpdc_assess").  "WHERE uniacid=:uniacid and seller_id=".$storeid." and content LIKE :name";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,array(':uniacid'=>$_W['uniacid'],':name'=>$where));	   
$total = pdo_fetchcolumn("select count(*) from ".tablename("wpdc_assess").  "WHERE uniacid=:uniacid and seller_id=".$storeid." and content LIKE :name",array(':uniacid'=>$_W['uniacid'],':name'=>$where));	
$pager = pagination($total, $pageindex, $pagesize);
if(checksubmit('submit2')){
	$result = pdo_update('wpdc_assess', array('reply' => $_GPC['reply'],'status'=>2,'reply_time'=>date("Y-m-d H:i:s")), array('id' => $_GPC['id']));
	if($result){
			message('回复成功',$this->createWebUrl('assess2',array()),'success');
		}else{
			message('回复失败','','error');
		}
}
if($_GPC['op']=='delete'){
	$result = pdo_delete('wpdc_assess', array('id'=>$_GPC['id']));
	if($result){
		message('删除成功',$this->createWebUrl('assess2',array()),'success');
	}else{
		message('删除失败','','error');
	}
}



include $this->template('web/assess2');