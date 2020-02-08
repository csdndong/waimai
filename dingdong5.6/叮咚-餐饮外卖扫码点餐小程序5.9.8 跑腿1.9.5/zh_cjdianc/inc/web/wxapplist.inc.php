<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
if($_GPC['keywords']){
	$op=$_GPC['keywords'];
	$where="%$op%";	
}else{
	$where='%%';
}
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select a.*  from " . tablename("account_wxapp") . " a"  . " left join " . tablename("wxapp_versions") . " b on b.uniacid=a.uniacid WHERE b.modules LIKE '%{$_GPC['m']}%'  and a.name LIKE :name";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,array(':name'=>$where));	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("account_wxapp") . " a"  . " left join " . tablename("wxapp_versions") . " b on b.uniacid=a.uniacid WHERE b.modules LIKE '%{$_GPC['m']}%' and  name LIKE :name",array(':name'=>$where));
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/wxapplist');