<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select a.* ,b.name,b.img as  user_img from " . tablename("cjdc_jfrecord") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id WHERE a.good_id =".$_GPC['good_id']." order by id DESC";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_jfrecord") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id WHERE a.good_id =".$_GPC['good_id']."");
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=='upd'){
	$res=pdo_update('cjdc_jfrecord',array('state'=>2),array('id'=>$_GPC['id']));
	if($res){
		message('修改成功',$this->createWebUrl('receivelist',array('good_id' => $_GPC['good_id'],'type'=>$_GPC['type'])),'success');
	}else{
		message('修改失败',$this->createWebUrl('receivelist',array('good_id' => $_GPC['good_id'],'type'=>$_GPC['type'])),'error');
	}
}
include $this->template('web/receivelist');