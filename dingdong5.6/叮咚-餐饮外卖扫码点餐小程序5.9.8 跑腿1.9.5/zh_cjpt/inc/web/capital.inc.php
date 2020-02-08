<?php
global $_GPC, $_W;
load()->func('tpl');
$GLOBALS['frames'] = $this->getMainMenu($storeid,$action);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sys=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']),'yc_money');
$type=isset($_GPC['type'])?$_GPC['type']:'all';
$where=" where xx.uniacid=:uniacid and xx.qs_id>0 ";
$data[':uniacid']=$_W['uniacid'];
if($_GPC['time']){
    $start=strtotime($_GPC['time']['start']);
    $end=strtotime($_GPC['time']['end']);
    $where.=" and UNIX_TIMESTAMP(xx.days) >={$start} and UNIX_TIMESTAMP(xx.days)<={$end}";
}
if(!empty($_GPC['keywords'])){
    $where.=" and (b.name LIKE  concat('%', :name,'%') || b. tel LIKE  concat('%', :name,'%'))";
    $data[':name']=$_GPC['keywords'];   
}
if(!$_GPC['time'] and !$_GPC['keywords']){
$type2=isset($_GPC['type2'])?$_GPC['type2']:'today';
}

$sql=" select xx.*,b.name,tel from (SELECT FROM_UNIXTIME(jd_time,'%Y-%m-%d') days, qs_id,uniacid,state FROM ".tablename('cjpt_dispatch')." GROUP BY qs_id,days) xx left join".tablename('cjpt_rider')." b on xx.qs_id=b.id".$where." order by xx.days desc";

$total=pdo_fetchcolumn("select count(*) from (SELECT FROM_UNIXTIME(jd_time,'%Y%m%d') days, qs_id,uniacid,state FROM ".tablename('cjpt_dispatch')." GROUP BY qs_id,days) xx left join".tablename('cjpt_rider')." b on xx.qs_id=b.id".$where,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);

foreach ($list as $key => $value) {
$sql2="select sum( case when state=2 then ps_money else 0 end) as jdmoney, sum( case when state=3 then ps_money else 0 end) as dsdmoney,sum( case when state=4 then ps_money else 0 end) as wcmoney from  ".tablename('cjpt_dispatch')." where qs_id={$value['qs_id']} and FROM_UNIXTIME(jd_time,'%Y-%m-%d') like'%{$value['days']}%'";
$rst=pdo_fetch($sql2);
$sql3=" select count(id) as count from ".tablename('cjpt_dispatch')." where qs_id={$value['qs_id']} and FROM_UNIXTIME(jd_time,'%Y-%m-%d') like'%{$value['days']}%' and state=5 and item=2";
$rst2=pdo_fetch($sql3);
$list[$key]['jdmoney']=$rst['jdmoney'];
$list[$key]['wcmoney']=$rst['wcmoney'];
$list[$key]['dsdmoney']=$rst['dsdmoney'];
$list[$key]['count']=$rst2['count'];
}
//var_dump($list);die;

$pager = pagination($total, $pageindex, $pagesize);

include $this->template('web/capital');