<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$time=date("Y-m-d");
$time="'%$time%'";
$wm = "select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$time."  and state not in (5,1,8) and is_yue=2 and type=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$wm = pdo_fetch($wm);//今天的微信支付外卖销售额
$dn ="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$time."  and dn_state not in (3,1) and is_yue=2 and  type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$dn = pdo_fetch($dn);//今天的微信支付店内销售额
$yd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$time."  and state not in (0,6) and is_yue=2 and  uniacid=".$_W['uniacid'];
$yd = pdo_fetch($yd);//今天的微信支付预定销售额
$dmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$time." and state=2 and is_yue=2 and  uniacid=".$_W['uniacid'];
$dmf = pdo_fetch($dmf);//今天的微信支付当面付销售额
$total = $wm['total']+$dn['total']+$yd['total']+$dmf['total'];//今天的微信支付销售额


$wm2="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$time."  and state not in (5,1,8) and is_yue=1 and type=1  and pay_time !='' and uniacid=".$_W['uniacid'];
$wm2=pdo_fetch($wm2);//今天的余额支付外卖销售额
$dn2="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$time."  and dn_state not in (3,1) and is_yue=1 and type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$dn2 = pdo_fetch($dn2);//今天的余额支付店内销售额
$yd2= "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$time."  and is_yue=1 and state not in (0,6) and uniacid=".$_W['uniacid'];
$yd2= pdo_fetch($yd2);//今天的余额支付预定销售额
$dmf2= "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$time." and state=2 and is_yue=1 and uniacid=".$_W['uniacid'];
$dmf2= pdo_fetch($dmf2);//今天的余额支付当面付销售额
$total2 = $wm2['total']+$dn2['total']+$yd2['total']+$dmf2['total'];//今天的余额支付销售额



$wm3= "select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$time."  and state not in (5,1,8) and is_yue=3 and type=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$wm3= pdo_fetch($wm3);//今天的积分支付外卖销售额
$dn3="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$time."  and dn_state not in (3,1) and is_yue=3 and type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$dn3= pdo_fetch($dn3);//今天的积分支付店内销售额
$yd3= "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$time."  and state not in (0,6) and is_yue=3 and uniacid=".$_W['uniacid'];
$yd3= pdo_fetch($yd3);//今天的积分支付预定销售额
$dmf3= "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$time." and state=2 and is_yue=3 and uniacid=".$_W['uniacid'];
$dmf3= pdo_fetch($dmf3);//今天的积分支付当面付销售额
$total3 = $wm3['total']+$dn3['total']+$yd3['total']+$dmf3['total'];//今天的积分支付销售额

include $this->template('web/analysis');