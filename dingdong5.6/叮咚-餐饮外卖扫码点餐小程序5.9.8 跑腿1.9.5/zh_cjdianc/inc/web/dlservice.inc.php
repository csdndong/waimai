<?php
global $_GPC, $_W;
$action = 'start';
$storeid=$_COOKIE["storeid"];
$uid=$_COOKIE["uid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=15;
$where=" WHERE uniacid={$_W['uniacid']} and store_id={$storeid} and pid=0";
$sql=" select * from" . tablename("cjdc_service") .$where." order by num asc";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);	   
foreach($list as $key => $value){
	$data=pdo_getall('cjdc_service',array('pid'=>$value['id'],'uniacid'=>$_W['uniacid']),array(),'','order by num asc');
	$list[$key]['ej']=$data;
	
}
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_service").$where);
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=='delete'){
	$rst=pdo_get('cjdc_service',array('pid'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
	if(!$rst){
	$result = pdo_delete('cjdc_service', array('id'=>$_GPC['id']));
	if($result){
		message('删除成功',$this->createWebUrl2('dlservice',array()),'success');
	}else{
		message('删除失败','','error');
	}
}else{
	message('改时间点存在时间段无法删除！','','error');
}
}
include $this->template('web/dlservice');