<?php
global $_GPC, $_W;
$system=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
$time=time()-($system['day']*24*60*60);
pdo_update('cjdc_order',array('state'=>4),array('state'=>3,'time <='=>$time));
$GLOBALS['frames'] = $this->getMainMenu();
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$type=isset($_GPC['type'])?$_GPC['type']:'now';
$type2=isset($_GPC['type2'])?$_GPC['type2']:'today';
$user_id=$_GPC['user_id'];
if($user_id){
 $where=" where id in (select order_id from ".tablename('cjdc_earnings')." where son_id in (select fx_user from ".tablename('cjdc_fxuser')." where user_id={$user_id})) and uniacid=:uniacid";
}else{
   $where=" where id in (select order_id from ".tablename('cjdc_earnings').") and uniacid=:uniacid";
}
$data[':uniacid']=$_W['uniacid']; 
if($_GPC['type']&&$_GPC['type']!='all'){
  $where.=" and type={$_GPC['type']}";

}
if($_GPC['keywords']){
  $op=$_GPC['keywords'];
  $where.=" and order_num LIKE  '%{$op}%'";

}
$sql="select xx.*,b.name as yh_name,c.name as table_name from (SELECT * FROM ".tablename('cjdc_order').$where." ORDER BY id DESC) xx left join ".tablename('cjdc_user')." b on xx.user_id=b.id left join ".tablename('cjdc_table')." c on xx.table_id=c.id";
$total=pdo_fetchcolumn(" select count(*) from (SELECT * FROM ".tablename('cjdc_order').$where." ORDER BY id DESC) xx left join ".tablename('cjdc_user')." b on xx.user_id=b.id",$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$res2=pdo_getall('cjdc_order_goods');
$data3=array();
for($i=0;$i<count($list);$i++){
  $data4=array();
  for($k=0;$k<count($res2);$k++){
    if($list[$i]['id']==$res2[$k]['order_id']){
      $data4[]=array(
        'name'=>$res2[$k]['name'],
        'num'=>$res2[$k]['number'],
        'img'=>$res2[$k]['img'],
        'money'=>$res2[$k]['money'],
        'dishes_id'=>$res2[$k]['dishes_id'],
        'spec'=>$res2[$k]['spec'],
        );
    } 
  }
  $sql1="select a.money,a.note,b.user_name,b.user_tel,c.name from ".tablename('cjdc_earnings')." a left join ".tablename('cjdc_retail')." b on a.user_id=b.user_id left join".tablename('cjdc_user')." c on a.user_id=c.id where a.order_id={$list[$i]['id']}";
  $yjinfo=pdo_fetchall($sql1);
  $data3[]=array(
    'order'=> $list[$i],
    'goods'=>$data4,
    'yjinfo'=>$yjinfo
    );
}
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/fxorder');