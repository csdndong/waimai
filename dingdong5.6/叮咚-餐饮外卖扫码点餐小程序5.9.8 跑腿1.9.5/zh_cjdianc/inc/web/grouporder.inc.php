<?php
global $_GPC, $_W;
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
pdo_update('cjdc_order',array('state'=>4),array('state'=>3,'time <='=>$time));
$GLOBALS['frames'] = $this->getMainMenu2();
$pageindex = max(1, intval($_GPC['page']));
$pagesize=8;
$type=isset($_GPC['type'])?$_GPC['type']:'all';
$type2=isset($_GPC['type2'])?$_GPC['type2']:'today';
$where=" where a.uniacid=:uniacid and a.store_id=:store_id";
$data[':uniacid']=$_W['uniacid']; 
$data[':store_id']=$storeid; 
if(isset($_GPC['keywords'])){
  $where.=" and ( a.order_num LIKE  concat('%', :name,'%') || b.name LIKE  concat('%', :name,'%'))";
  $data[':name']=$_GPC['keywords']; 
  $type='all';  
}
if($_GPC['time']){
  $start=strtotime($_GPC['time']['start']);
  $end=strtotime($_GPC['time']['end']);
  $where.=" and a.time >='{$start}' and a.time<='{$end}'";
  $type='all';
}else{
 if($type=='wait'){
  $where.=" and a.state=1";
}
if($type=='pay'){
  $where.=" and a.state=2";
}

if($type=='complete'){
  $where.=" and a.state=3";
}
if($type=='close'){
  $where.=" and a.state=4";
}
if($type=='invalid'){
  $where.=" and a.state=5";
} 

}
$sql="SELECT a.*,b.name as nick_name FROM ".tablename('cjdc_grouporder'). " a"  . " left join " . tablename("cjdc_user") . " b on a.user_id=b.id " .$where." ORDER BY a.id DESC";
$total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjdc_grouporder'). " a"  . " left join " . tablename("cjdc_user") . " b on a.user_id=b.id  " .$where,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;

$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);

if($_GPC['op']=='delete'){
  $res=pdo_delete('cjdc_grouporder',array('id'=>$_GPC['id']));
  if($res){
   message('删除成功！', $this->createWebUrl('grouporder'), 'success');
 }else{
  message('删除失败！','','error');
}
}
include $this->template('web/grouporder');