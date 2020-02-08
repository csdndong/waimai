<?php
global $_GPC, $_W;
$action = 'start';
//$GLOBALS['frames'] = $this->getMainMenu2();
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$type=empty($_GPC['type']) ? 'all' :$_GPC['type'];
$state=$_GPC['state'];
$where=' WHERE  uniacid=:uniacid and store_id=:store_id';
$data[':uniacid']=$_W['uniacid'];
$data[':store_id']=$storeid;
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
if($type=='all'){    
  $sql="SELECT * FROM ".tablename('wpdc_withdrawal') .  $where." ORDER BY time DESC";
  $total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('wpdc_withdrawal') .  $where." ORDER BY time DESC",$data);
}else{
    $where.= "  and state=$state";
    $sql="SELECT * FROM ".tablename('wpdc_withdrawal') .  $where." ORDER BY time DESC";
    $total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('wpdc_withdrawal') . $where." ORDER BY time DESC",$data);    
}
$list=pdo_fetchall( $sql,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/dlintxlist');