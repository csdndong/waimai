<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$type=isset($_GPC['type'])?$_GPC['type']:'all';
$where=" where a.uniacid=:uniacid  and a.state=5 and a.item=2";
$data[':uniacid']=$_W['uniacid']; 
if(isset($_GPC['keywords'])){
  $where.=" and (a.order_id LIKE  concat('%', :name,'%') || b.tel LIKE  concat('%', :name,'%') || b.name LIKE  concat('%', :name,'%') || a.ps_num LIKE  concat('%', :name,'%'))";
  $data[':name']=$_GPC['keywords']; 
  $type='all';  
}
if($_GPC['time']){
  $start=$_GPC['time']['start'];
  $end=$_GPC['time']['end'];
  $where.=" and a.time >='{$start}' and a.time<='{$end}'";
  $type='all';
}
 if($_GPC['state']){
  $where.=" and a.state={$_GPC['state']}";
}
$sql="SELECT a.*,b.name,b.tel FROM ".tablename('cjpt_dispatch'). " a left join".tablename('cjpt_rider')." b on a.qs_id=b.id"  .$where." ORDER BY a.id DESC";
$total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjpt_dispatch'). " a left join".tablename('cjpt_rider')." b on a.qs_id=b.id"  .$where,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/abnormal');