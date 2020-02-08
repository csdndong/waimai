<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu($storeid,$action);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$days=$_GPC['days'];
$name=$_GPC['name'];
$qs_id=$_GPC['qs_id'];
$where="where xx.uniacid=:uniacid and xx.days like '%{$days}%' and xx.qs_id={$qs_id} and ( xx.state!=5 or (xx.state=5 and xx.item=2))";
$data[':uniacid']=$_W['uniacid'];
if($_GPC['time']){
    $start=strtotime($_GPC['time']['start']);
    $end=strtotime($_GPC['time']['end']);
    $where=" where  UNIX_TIMESTAMP(xx.days) >={$start} and UNIX_TIMESTAMP(xx.days)<={$end} and xx.qs_id={$qs_id} and ( xx.state!=5 or (xx.state=5 and xx.item=2))";
}

$sql=" select xx.* from (SELECT FROM_UNIXTIME(jd_time,'%Y-%m-%d') days,id, qs_id,uniacid,state,ps_money,order_id,ps_num,item FROM ".tablename('cjpt_dispatch').") xx ".$where." order by xx.id desc";
$total=pdo_fetchcolumn("select count(*) from (SELECT FROM_UNIXTIME(jd_time,'%Y-%m-%d') days, qs_id,uniacid,state,item FROM ".tablename('cjpt_dispatch').") xx ".$where,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/capitallist');

