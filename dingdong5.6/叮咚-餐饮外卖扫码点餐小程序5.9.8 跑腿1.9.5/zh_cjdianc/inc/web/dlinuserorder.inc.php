<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$user_id=$_GPC['user_id'];
$type=$_GPC['type']?$_GPC['type']:'wm';
$where=" where user_id={$user_id} and store_id={$storeid}";
if($type=='wm'){
$where .=" and state in (4,5,10)";
}
if($type=='dn'){
$where .=" and dn_state=2";
}
if($type=='yy'){
$where .=" and yy_state=3";
}
if($type=='dm'){
$where .=" and dm_state=2";
}



if($_GPC['time']){
  $start=$_GPC['time']['start'];
  $end=$_GPC['time']['end'];
  $where2=" and time >='{$start}' and time<='{$end}'";
}
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select *  from " . tablename("cjdc_order") ." ".$where.$where2." order by id DESC";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_order") ." ".$where.$where2);
$number=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_order")." ".$where );
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/dlinuserorder');