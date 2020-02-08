<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$table_id=$_GPC['table_id'];
$type=isset($_GPC['type'])?$_GPC['type']:'all';
$where=" where uniacid={$_W['uniacid']} and type=2 and store_id={$storeid} and table_id={$table_id}";
$data=array();
if($_GPC['time']){
  $start=strtotime($_GPC['time']['start']);
  $end=strtotime($_GPC['time']['end']);
  $where.=" and UNIX_TIMESTAMP(time) >={$start} and UNIX_TIMESTAMP(time)<={$end}";
}
if(!empty($_GPC['keywords'])){
  $where.=" and order_num LIKE  concat('%', :name,'%')";
  $data[':name']=$_GPC['keywords'];   
}
if(!$_GPC['time'] and !$_GPC['keywords']){
  $type2=isset($_GPC['type2'])?$_GPC['type2']:'today';
}



if($type=='wait'){
  $where.=" and dn_state=1";
}
if($type=='complete'){
  $where.=" and dn_state=2";
}
if($type=='close'){
  $where.=" and dn_state=3";
}
if($type2=='today'){
  $time=date("Y-m-d",time());
  $where.="  and time LIKE '%{$time}%' ";
}
if($type2=='yesterday'){
  $time=date("Y-m-d",strtotime("-1 day"));
  $where.="  and time LIKE '%{$time}%' ";
}
if($type2=='week'){
  $time=strtotime(date("Y-m-d",strtotime("-7 day")));
  $where.=" and UNIX_TIMESTAMP(time) >".$time;
}
if($type2=='month'){
  $time=date("Y-m");
  $where.="  and time LIKE '%{$time}%' ";
}
//var_dump($data);die;
$sql=" select id,order_num,time,money,discount,ps_money,box_money from ".tablename('cjdc_order').$where." order by id desc";
//var_dump($sql);die;
$total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjdc_order').$where,$data);
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
        'number'=>$res2[$k]['number'],
        'img'=>$res2[$k]['img'],
        'money'=>$res2[$k]['money'],
        'dishes_id'=>$res2[$k]['dishes_id']
        );
    }
  }

  $data3[]=array(
    'order'=> $list[$i],
    'goods'=>$data4
    );
}
//var_dump($data3);die;
$pager = pagination($total, $pageindex, $pagesize);
include $this->template('web/dltableorder');