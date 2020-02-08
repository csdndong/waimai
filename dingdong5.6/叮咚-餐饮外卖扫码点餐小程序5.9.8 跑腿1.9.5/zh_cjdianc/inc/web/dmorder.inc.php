<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$where=" where a.uniacid=:uniacid and a.type=4 and a.dm_state=2";
$data[':uniacid']=$_W['uniacid'];
$type2=isset($_GPC['type2'])?$_GPC['type2']:'today';
if($_GPC['time']){
  $start=$_GPC['time']['start'];
  $end=$_GPC['time']['end'];
  $where.=" and a.time >='{$start}' and a.time<='{$end}'";
}

if(!empty($_GPC['keywords'])){
    $where.=" and (d.name LIKE  concat('%', :name,'%') or a.order_num LIKE  concat('%', :name,'%'))";
    $data[':name']=$_GPC['keywords'];   
}
 if($type2=='today'){
  $time=date("Y-m-d",time());
  $where.="  and a.time LIKE '%{$time}%' ";
}
if($type2=='yesterday'){
  $time=date("Y-m-d",strtotime("-1 day"));
 $where.="  and a.time LIKE '%{$time}%' ";
}
if($type2=='week'){
$time=strtotime(date("Y-m-d",strtotime("-7 day")));

  $where.=" and UNIX_TIMESTAMP(a.time) >".$time;
}
if($type2=='month'){
  $time=date("Y-m");
  $where.="  and a.time LIKE '%{$time}%' ";
}
$sql="SELECT a.*,d.name,b.name as table_name,b.status as t_status,c.name as tablename,d.name,e.dm_poundage as md_poundage,e.poundage FROM ".tablename('cjdc_order'). " a"  . " left join " . tablename("cjdc_table") . " b on a.table_id=b.id  left join " . tablename("cjdc_table_type") ." c on b.type_id=c.id left join " . tablename("cjdc_store") ." d on a.store_id=d.id left join " . tablename("cjdc_storetype") ." e on d.md_type=e.id left join " . tablename("cjdc_storeset") ." f on a.store_id=f.store_id ".$where." ORDER BY a.time DESC";
$total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjdc_order'). " a"  . " left join " . tablename("cjdc_table") . " b on a.table_id=b.id left join " . tablename("cjdc_table_type") ." c on b.type_id=c.id left join " . tablename("cjdc_store") ." d on a.store_id=d.id  left join " . tablename("cjdc_storetype") ." e on d.md_type=e.id left join " . tablename("cjdc_storeset") ." f on a.store_id=f.store_id ".$where." ORDER BY a.time DESC",$data);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
$pager = pagination($total, $pageindex, $pagesize);


if(checksubmit('export_submit', true)) {
  $time=date("Y-m-d");
  $time="'%$time%'";
   $start=$_GPC['time']['start'];
  $end=$_GPC['time']['end'];
  $count = pdo_fetchcolumn("SELECT COUNT(*) FROM". tablename("cjdc_order")." WHERE uniacid={$_W['uniacid']} and type=4 and dm_state=2 and  time >='{$start}' and time<='{$end}'");
  $pagesize = ceil($count/5000);
        //array_unshift( $names,  '活动名称'); 

  $header = array(
    'item'=>'序号',
    'md_name' => '门店名称',
    'order_num' => '订单号', 
    'time' => '下单时间',
    'money' => '金额',
    'pay_type' => '支付方式'
    );

  $keys = array_keys($header);
  $html = "\xEF\xBB\xBF";
  foreach ($header as $li) {
    $html .= $li . "\t ,";
  }
  $html .= "\n";
  for ($j = 1; $j <= $pagesize; $j++) {
    $sql = "select a.*,b.name as md_name from " . tablename("cjdc_order")."  a"  . " inner join " . tablename("cjdc_store")." b on a.store_id=b.id  WHERE a.uniacid={$_W['uniacid']} and a.type=4 and a.time >='{$start}' and a.dm_state=2 and a.time<='{$end}' limit " . ($j - 1) * 5000 . ",5000 ";
    $list = pdo_fetchall($sql);            
  }
  if (!empty($list)) {
    $size = ceil(count($list) / 500);
    for ($i = 0; $i < $size; $i++) {
      $buffer = array_slice($list, $i * 500, 500);
      $user = array();
      foreach ($buffer as $k =>$row) {
        $row['item']= $k+1;

        if($row['pay_type']==1){
          $row['pay_type']='微信支付';
        }elseif($row['pay_type']==2){
          $row['pay_type']='余额支付';
        }elseif($row['pay_type']==3){
          $row['pay_type']='积分支付';
        }
        $good=pdo_getall('cjdc_order_goods',array('order_id'=>$row['id']));
        for($i=0;$i<count($good);$i++){
          $date6='';
          if($good[$i]['spec']){
            $date6 .=$good[$i]['name'].'('.$good[$i]['spec'].')*'.$good[$i]['number']."  ";
          }else{
            $date6 .=$good[$i]['name'].'*'.$good[$i]['number']."  ";
          } 
        }
        $row['goods']=$date6;
        foreach ($keys as $key) {
          $data5[] = $row[$key];
        }
        $user[] = implode("\t ,", $data5) . "\t ,";
        unset($data5);
      }
      $html .= implode("\n", $user) . "\n";
    }
  }
  
  header("Content-type:text/csv");
  header("Content-Disposition:attachment; filename=当面付订单数据.csv");
  echo $html;
  exit();
}

if($_GPC['op']=='dy'){
  $result=$this->qtPrint($_GPC['order_id']);
  message('打印成功！', $this->createWebUrl('dmorder'), 'success');


}

include $this->template('web/dmorder');