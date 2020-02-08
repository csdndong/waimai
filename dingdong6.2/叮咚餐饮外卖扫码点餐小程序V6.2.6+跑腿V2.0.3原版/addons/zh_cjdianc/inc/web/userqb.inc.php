<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$uniacid=$_W['uniacid'];
$user_id=$_GPC['user_id'];
$where=" where   user_id={$user_id}";
if($_GPC['time']){
  $start=$_GPC['time']['start'];
  $end=$_GPC['time']['end'];
  $where.=" and time >='{$start}' and time<='{$end}'";
}
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select *  from " . tablename("cjdc_qbmx") ." ".$where." order by id DESC";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_qbmx") ." ".$where);
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/userqb');