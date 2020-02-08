<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=15;
$where=" WHERE uniacid={$_W['uniacid']} and store_id={$storeid}";
$sql=" select * from" . tablename("cjdc_reservation") .$where." order by num asc";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_reservation").$where);
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=='delete'){
	$result = pdo_delete('cjdc_reservation', array('id'=>$_GPC['id']));
	if($result){
		message('删除成功',$this->createWebUrl('reservation',array()),'success');
	}else{
		message('删除失败','','error');
	}
}
include $this->template('web/reservation');