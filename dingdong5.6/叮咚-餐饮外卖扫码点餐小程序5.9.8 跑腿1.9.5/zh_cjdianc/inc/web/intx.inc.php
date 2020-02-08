<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$system=pdo_get('wpdc_system',array('uniacid'=>$_W['uniacid']));
$store=pdo_get('wpdc_store',array('id'=>$storeid));
if($store['poundage']){
  $poundage=$store['poundage'];
}else{
  $storetype=pdo_get('wpdc_storetype',array('id'=>$store['md_type']));
  $poundage=$storetype['poundage'];
}
$sql = "select sum(money) as total from " . tablename("wpdc_order")." WHERE  seller_id=".$storeid." and state  in (4,6,9) and type=1 and pay_time !=''";
$total = pdo_fetch($sql);//可提现金额外卖
$sql3 = "select sum(money) as total from " . tablename("wpdc_order")." WHERE  seller_id=".$storeid." and dn_state  in (2,4) and type=2 and pay_time !=''";
$total3 = pdo_fetch($sql3);//可提现金额店内
$sql4 = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE  store_id=".$storeid." and state  in (7,2)";
$total4 = pdo_fetch($sql4);//可提现金额预定
$sql5 = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE state=2 and store_id=".$storeid;
$total5 = pdo_fetch($sql5);//可提现金额预定

$total6=$total['total']+$total3['total']+$total4['total']+$total5['total'];
$total6=number_format($total6-($total6*($poundage/100)),2, ".", "");
//echo $total6;die;
$sql2 = "select sum(sj_cost) as total from " . tablename("wpdc_withdrawal")." WHERE  store_id=".$storeid." and state in (1,2)";
$total2 = pdo_fetch($sql2);//已提现金额
$ktxcost=$total6-$total2['total'];
// $ktxcost=number_format($ktxcost-($ktxcost*($poundage/100)),2, ".", "");
if(checksubmit('submit')){
  if($_GPC['tx_cost']>$ktxcost){
message('提现金额不能超过账户可提现金额','','error');
  }
  if($_GPC['tx_cost']<$system['tx_money']){
message('提现金额小于最低提现金额','','error');
  }
  $data['sj_cost']=$_GPC['tx_cost'];
  $data['store_id']=$storeid;
  $data['name']=$_GPC['name'];
  $data['username']=$_GPC['username'];
  $data['type']=$_GPC['type'];
  $data['state']=1;
  $data['tx_cost']=$_GPC['tx_cost'];
  $data['uniacid']=$_W['uniacid'];
  $data['time']=date("Y-m-d H:i:s");
  $res=pdo_insert('wpdc_withdrawal',$data);
 if($res){
        message('提交成功',$this->createWebUrl('intx',array()),'success');
    }else{
        message('提交失败','','error');
    }


}
include $this->template('web/intx');