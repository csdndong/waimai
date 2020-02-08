<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=20;
$where=" WHERE a.uniacid=:uniacid and a.store_id=:store_id";
$data[':uniacid']=$_W['uniacid'];
$data[':store_id']=$storeid;
$sql="select a.id,a.time,a.state, b.name,b.tag,c.name as type_name from " . tablename("cjdc_calllog") . " a"  . " left join " . tablename("cjdc_table") . " b on b.id=a.table_id left join " . tablename("cjdc_table_type") . " c on b.type_id=c.id".$where." order by a.id desc";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,$data);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_calllog") . " a"  . " left join " . tablename("cjdc_table") . " b on b.id=a.table_id left join " . tablename("cjdc_table_type") . " c on b.type_id=c.id".$where,$data);
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=="delete"){
	$result = pdo_delete('cjdc_calllog', array('id'=>$_GPC['id']));
		if($result){
			message('删除成功',$this->createWebUrl2('dlcalllog',array()),'success');
		}else{
			message('删除失败','','error');
		}
}
if($_GPC['op']=='ok'){
	$res=pdo_update('cjdc_call',array('src'=>''),array('store_id'=>$storeid));
	if($res){
		message('操作成功',$this->createWebUrl2('dlcalllog',array()),'success');
	}else{
		message('操作失败','','error');
	}

}
if($_GPC['op']=='fw'){
	$res=pdo_update('cjdc_calllog',array('state'=>2),array('id'=>$_GPC['id']));
	if($res){
		message('操作成功',$this->createWebUrl2('dlcalllog',array()),'success');
	}else{
		message('操作失败','','error');
	}

}
include $this->template('web/dlcalllog');