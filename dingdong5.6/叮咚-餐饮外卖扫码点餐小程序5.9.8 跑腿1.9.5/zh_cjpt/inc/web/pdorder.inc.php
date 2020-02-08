<?php
global $_GPC, $_W;
load()->func('tpl');
$GLOBALS['frames'] = $this->getMainMenu($storeid,$action);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$type=isset($_GPC['type'])?$_GPC['type']:'all';
$where=" where xx.uniacid=:uniacid and xx.qs_id>0";
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
$list=pdo_fetchall($sql,$data);
foreach ($list as $key => $value) {
$sql2="select sum( case when state=2 then 1 else 0 end) as jd, sum( case when state=3 then 1 else 0 end) as dsd,sum( case when state=4 then 1 else 0 end) as wc from  ".tablename('cjpt_dispatch')." where qs_id={$value['qs_id']} and FROM_UNIXTIME(jd_time,'%Y-%m-%d') like '%{$value['days']}%'";
$rst=pdo_fetch($sql2);
$list[$key]['jd']=$rst['jd'];
$list[$key]['wc']=$rst['wc'];
$list[$key]['dsd']=$rst['dsd'];
}
$pager = pagination($total, $pageindex, $pagesize);

if(checksubmit('export_submit', true)) {
  $time=date("Y-m-d");
  $time="'%$time%'";
  $count = pdo_fetchcolumn("SELECT COUNT(*) FROM". tablename("cjdc_order")." WHERE type=1 and time LIKE ".$time);
  $pagesize = ceil($count/5000);
        //array_unshift( $names,  '活动名称'); 

  $header = array(
    'item'=>'序号',
    'name' => '骑手',
    'tel' => '电话', 
    'jd' => '接单数量', 
    'tel' => '完成数量',
    'days' => '日期',
    );

  $keys = array_keys($header);
  $html = "\xEF\xBB\xBF";
  foreach ($header as $li) {
    $html .= $li . "\t ,";
  }
  $html .= "\n";
  for ($j = 1; $j <= $pagesize; $j++) {
    $sql = "select a.*,b.name as md_name from " . tablename("cjdc_order")."  a"  . " inner join " . tablename("cjdc_store")." b on a.store_id=b.id  WHERE a.type=1 and a.time LIKE ".$time."  limit " . ($j - 1) * 5000 . ",5000 ";
    $list = pdo_fetchall($sql);            
  }
  if (!empty($list)) {
    $size = ceil(count($list) / 500);
    for ($i = 0; $i < $size; $i++) {
      $buffer = array_slice($list, $i * 500, 500);
      $user = array();
      foreach ($buffer as $k =>$row) {
        $row['item']= $k+1;
        if($row['state']==1){
          $row['state']='待付款';
        }elseif($row['state']==2){
          $row['state']='等待接单';
        }elseif($row['state']==3){
          $row['state']='等待送达';
        }elseif($row['state']==4){
          $row['state']='完成';
        }elseif($row['state']==5){
          $row['state']='已评价';
        }elseif($row['state']==6){
          $row['state']='已取消';
        }elseif($row['state']==7){
          $row['state']='已拒绝';
        }elseif($row['state']==8){
          $row['state']='退款中';
        }elseif($row['state']==9){
          $row['state']='退款成功';
        }elseif($row['state']==10){
          $row['state']='退款失败';
        }
        if($row['pay_type']==1){
          $row['pay_type']='微信支付';
        }elseif($row['pay_type']==2){
          $row['pay_type']='余额支付';
        }elseif($row['pay_type']==3){
          $row['pay_type']='积分支付';
        }elseif($row['pay_type']==4){
          $row['pay_type']='货到付款';
        }
        if($row['order_type']==1){
          $row['order_type']='外卖配送';
        }elseif($row['order_type']==2){
          $row['order_type']='到店自提';
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
  header("Content-Disposition:attachment; filename=派单记录.csv");
  echo $html;
  exit();
}


include $this->template('web/pdorder');