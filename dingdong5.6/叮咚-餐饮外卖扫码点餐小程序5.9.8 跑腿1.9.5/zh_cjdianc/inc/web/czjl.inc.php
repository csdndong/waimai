<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$pageindex = max(1, intval($_GPC['page']));
$pagesize=15;
$start=$_GPC['time']['start'];
$end=$_GPC['time']['end'];
if($start || $end){
$sql="select a.* ,b.name,b.img from " . tablename("cjdc_qbmx") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id"." where ( a.note='充值赠送' || a.note='后台充值' || a.note='在线充值' ) and b.uniacid = :uniacid and a.time >='{$start}' and a.time<='{$end}' order by a.id DESC";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,array(':uniacid'=>$_W['uniacid']));     
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_qbmx") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id where ( a.note='充值赠送' || a.note='后台充值' || a.note='在线充值' ) and b.uniacid = :uniacid and a.time >='{$start}' and a.time<='{$end}'",array(':uniacid'=>$_W['uniacid']));
}else{
  $sql="select a.* ,b.name,b.img from " . tablename("cjdc_qbmx") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id"." where ( a.note='充值赠送' || a.note='后台充值' || a.note='在线充值' ) and b.uniacid = :uniacid order by a.id DESC";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,array(':uniacid'=>$_W['uniacid']));     
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_qbmx") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id where ( a.note='充值赠送' || a.note='后台充值' || a.note='在线充值' ) and b.uniacid = :uniacid",array(':uniacid'=>$_W['uniacid']));
}

$pager = pagination($total, $pageindex, $pagesize);


if(checksubmit('export_submit', true)) {
  $time=date("Y-m-d");
  $time="'%$time%'";
   $start=$_GPC['time']['start'];
  $end=$_GPC['time']['end'];
  $count = pdo_fetchcolumn("SELECT COUNT(*) FROM". tablename("cjdc_qbmx") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id"." where ( a.note='充值赠送' || a.note='后台充值' || a.note='在线充值' ) and b.uniacid = {$_W['uniacid']} and a.time >='{$start}' and a.time<='{$end}'");
  $pagesize = ceil($count/5000);
  $header = array(
    'item'=>'序号',
    'name' => '充值用户',
    'money' => '金额', 
    'time' => '时间', 
    'note' => '备注',

    );

  $keys = array_keys($header);
  $html = "\xEF\xBB\xBF";
  foreach ($header as $li) {
    $html .= $li . "\t ,";
  }
  $html .= "\n";
  for ($j = 1; $j <= $pagesize; $j++) {
    $sql = "select a.* ,b.name,b.img from " . tablename("cjdc_qbmx") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id"." where ( a.note='充值赠送' || a.note='后台充值' || a.note='在线充值' ) and b.uniacid =  {$_W['uniacid']} and a.time >='{$start}' and a.time<='{$end}' order by a.id DESC limit " . ($j - 1) * 5000 . ",5000 ";
    $list = pdo_fetchall($sql);            
  }
  if (!empty($list)) {
    $size = ceil(count($list) / 500);
    for ($i = 0; $i < $size; $i++) {
      $buffer = array_slice($list, $i * 500, 500);
      $user = array();
      foreach ($buffer as $k =>$row) {
        $row['item']= $k+1;
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
  header("Content-Disposition:attachment; filename=外卖订单数据.csv");
  echo $html;
  exit();
}









include $this->template('web/czjl');