<?php
global $_GPC, $_W;
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getMainMenu2();
if(checksubmit('submit')){
	$op=$_GPC['keywords'];
	$where="%$op%";	

}else{
	$where='%%';
}
if(!$_GPC['page']){
	$_GPC['page']=1;
}
$user=pdo_getall('wpdc_user',array('uniacid'=>$_W['uniacid']));
for($i=0;$i<count($user);$i++){
    $wm = "select sum(money) as total from " . tablename("wpdc_order")." WHERE  user_id=".$user[$i]['id']." and seller_id=".$storeid." and state not in (5,1,8) and type=1 and pay_time !=''";
    $wm = pdo_fetch($wm);//外卖销售额
    $dn = "select sum(money) as total from " . tablename("wpdc_order")." WHERE user_id=".$user[$i]['id']." and seller_id=".$storeid." and dn_state not in (3,1) and type=2 and pay_time !=''";
    $dn = pdo_fetch($dn);//店内销售额
    $yd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE user_id=".$user[$i]['id']." and store_id=".$storeid." and state not in (0,6)";
    $yd = pdo_fetch($yd);//预定销售额
    $dmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE user_id=".$user[$i]['id']." and store_id=".$storeid." and state=2";
    $dmf = pdo_fetch($dmf);//当面付销售额
    $total = $wm['total']+$dn['total']+$yd['total']+$dmf['total'];//销售额
    $wm2 = "select * from " . tablename("wpdc_order")." WHERE user_id=".$user[$i]['id']." and seller_id=".$storeid." and state not in (5,1,8) and type=1 and pay_time !=''";
    $wm2 = count(pdo_fetchall($wm2));//外卖销售量
    $dn2 = "select * from " . tablename("wpdc_order")." WHERE user_id=".$user[$i]['id']." and seller_id=".$storeid." and dn_state not in (3,1) and type=2 and pay_time !=''";
    $dn2 = count(pdo_fetchall($dn2));//店内销售量
    $yd2 = "select * from " . tablename("wpdc_ydorder")." WHERE user_id=".$user[$i]['id']." and store_id=".$storeid." and state not in (0,6)";
    $yd2 = count(pdo_fetchall($yd2));//预定销售量
    $dmf2 = "select * from " . tablename("wpdc_dmorder")." WHERE user_id=".$user[$i]['id']." and store_id=".$storeid." and state=2";
    $dmf2 = count(pdo_fetchall($dmf2));//当面付销售量
    $number=$wm2+$dn2+$yd2+$dmf2;//销售量
    $user[$i]['money']=$total;
    $user[$i]['number']=$number;
    pdo_update('wpdc_user',array('order_money'=>$total),array('id'=>$user[$i]['id']));
    pdo_update('wpdc_user',array('order_number'=>$number),array('id'=>$user[$i]['id']));
}
    $pageindex = max(1, intval($_GPC['page']));
    $pagesize=10;
    $sql="select *  from " . tablename("wpdc_user") ." WHERE  name LIKE :name  and uniacid=:uniacid order by order_money DESC";
    $select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
    $list = pdo_fetchall($select_sql,array(':uniacid'=>$_W['uniacid'],':name'=>$where));       
    $total=pdo_fetchcolumn("select count(*) from " . tablename("wpdc_user") ." WHERE  name LIKE :name  and uniacid=:uniacid",array(':uniacid'=>$_W['uniacid'],':name'=>$where));
    $pager = pagination($total, $pageindex, $pagesize);

include $this->template('web/ygranking');