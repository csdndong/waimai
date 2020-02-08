<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
if($_GPC['time']){
	$time=strtotime($_GPC['time']['start']);
    $mttime=strtotime($_GPC['time']['end']);
}else{
	$time=strtotime(date("Y-m-d"));
	$mttime=strtotime(date("Y-m-d",strtotime("+1 day")));
}
$zttime=date("Y-m-d",strtotime("-1 day"));
$zttime="'%$zttime%'";

//今日平台总销售额
$wm = "select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and state not in (5,1,8) and type=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$wm = pdo_fetch($wm);//今天的外卖销售额
$dn ="select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and dn_state not in (3,1) and type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$dn = pdo_fetch($dn);//今天的店内销售额
$yd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE time2>=".$time." and time2<".$mttime." and state not in (0,6) and uniacid=".$_W['uniacid'];
$yd = pdo_fetch($yd);//今天的预定销售额
$dmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time2>=".$time." and time2<".$mttime." and state=2 and uniacid=".$_W['uniacid'];
$dmf = pdo_fetch($dmf);//今天的当面付销售额
$total = $wm['total']+$dn['total']+$yd['total']+$dmf['total'];//今天的销售额
//今日平台总销售额




//今日平台微信总销售额
$wxwm = "select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and state not in (5,1,8) and type=1 and is_yue=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$wxwm = pdo_fetch($wxwm);//今天的外卖销售额
$wxdn ="select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and dn_state not in (3,1) and type=2 and is_yue=2 and  pay_time !='' and uniacid=".$_W['uniacid'];
$wxdn = pdo_fetch($wxdn);//今天的店内销售额
$wxyd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE time2>=".$time." and time2<".$mttime." and state not in (0,6) and is_yue=2 and  uniacid=".$_W['uniacid'];
$wxyd = pdo_fetch($wxyd);//今天的预定销售额
$wxdmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time2>=".$time." and time2<".$mttime." and state=2 and  is_yue=2 and uniacid=".$_W['uniacid'];
$wxdmf = pdo_fetch($wxdmf);//今天的当面付销售额
$wxtotal = $wxwm['total']+$wxdn['total']+$wxyd['total']+$wxdmf['total'];//今天的销售额
//今日平台微信总销售额


//今日平台余额总销售额
$yuewm = "select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and state not in (5,1,8) and type=1 and is_yue=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$yuewm = pdo_fetch($yuewm);//今天的外卖销售额
$yuedn ="select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and dn_state not in (3,1) and type=2 and is_yue=1 and  pay_time !='' and uniacid=".$_W['uniacid'];
$yuedn = pdo_fetch($yuedn);//今天的店内销售额
$yueyd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE time2>=".$time." and time2<".$mttime." and state not in (0,6) and is_yue=1 and  uniacid=".$_W['uniacid'];
$yueyd = pdo_fetch($yueyd);//今天的预定销售额
$yuedmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time2>=".$time." and time2<".$mttime." and state=2 and  is_yue=1 and uniacid=".$_W['uniacid'];
$yuedmf = pdo_fetch($yuedmf);//今天的当面付销售额
$yuetotal = $yuewm['total']+$yuedn['total']+$yueyd['total']+$yuedmf['total'];//今天的销售额
//今日平台余额总销售额


//今日平台积分总销售额
$jfwm = "select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and state not in (5,1,8) and type=1 and is_yue=3 and pay_time !='' and uniacid=".$_W['uniacid'];
$jfwm = pdo_fetch($jfwm);//今天的外卖销售额
$jfdn ="select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and dn_state not in (3,1) and type=2 and is_yue=3 and  pay_time !='' and uniacid=".$_W['uniacid'];
$jfdn = pdo_fetch($jfdn);//今天的店内销售额
$jfyd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE time2>=".$time." and time2<".$mttime." and state not in (0,6) and is_yue=3 and  uniacid=".$_W['uniacid'];
$jfyd = pdo_fetch($jfyd);//今天的预定销售额
$jfdmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time2>=".$time." and time2<".$mttime." and state=2 and  is_yue=3 and uniacid=".$_W['uniacid'];
$jfdmf = pdo_fetch($jfdmf);//今天的当面付销售额
$jftotal = $jfwm['total']+$jfdn['total']+$jfyd['total']+$jfdmf['total'];//今天的销售额
//今日平台积分总销售额
















$ztwm="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and state not in (5,1,8) and type=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$ztwm =pdo_fetch($ztwm);//昨天的外卖销售额
$ztdn="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and dn_state not in (3,1) and type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$ztdn = pdo_fetch($ztdn);//昨天的店内销售额
$ztyd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$zttime." and state not in (0,6) and uniacid=".$_W['uniacid'];
$ztyd = pdo_fetch($ztyd);//昨天的预定销售额
$ztdmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$zttime." and state=2 and uniacid=".$_W['uniacid'];
$ztdmf = pdo_fetch($ztdmf);//昨天的当面付销售额
$zttotal = $ztwm['total']+$ztdn['total']+$ztyd['total']+$ztdmf['total'];//昨天的销售额




$wxztwm="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and state not in (5,1,8) and is_yue=2 and type=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$wxztwm =pdo_fetch($wxztwm);//昨天的微信外卖销售额
$wxztdn="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and dn_state not in (3,1) and is_yue=2 and  type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$wxztdn = pdo_fetch($wxztdn);//昨天的微信店内销售额
$wxztyd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$zttime." and is_yue=2 and  state not in (0,6) and uniacid=".$_W['uniacid'];
$wxztyd = pdo_fetch($wxztyd);//昨天的微信预定销售额
$wxztdmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$zttime." and state=2 and  is_yue=2 and uniacid=".$_W['uniacid'];
$wxztdmf = pdo_fetch($wxztdmf);//昨天的微信当面付销售额
$wxzttotal = $wxztwm['total']+$wxztdn['total']+$wxztyd['total']+$wxztdmf['total'];//昨天的微信销售额





$yueztwm="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and state not in (5,1,8) and is_yue=1 and type=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$yueztwm =pdo_fetch($yueztwm);//昨天的微信外卖销售额
$yueztdn="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and dn_state not in (3,1) and is_yue=1 and  type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$yueztdn = pdo_fetch($yueztdn);//昨天的微信店内销售额
$yueztyd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$zttime." and is_yue=1 and  state not in (0,6) and uniacid=".$_W['uniacid'];
$yueztyd = pdo_fetch($yueztyd);//昨天的微信预定销售额
$yueztdmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$zttime." and state=2 and  is_yue=1 and uniacid=".$_W['uniacid'];
$yueztdmf = pdo_fetch($yueztdmf);//昨天的微信当面付销售额
$yuezttotal = $yueztwm['total']+$yueztdn['total']+$yueztyd['total']+$yueztdmf['total'];//昨天的微信销售额







$jfztwm="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and state not in (5,1,8) and is_yue=3 and type=1 and pay_time !='' and uniacid=".$_W['uniacid'];
$jfztwm =pdo_fetch($jfztwm);//昨天的微信外卖销售额
$jfztdn="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime." and dn_state not in (3,1) and is_yue=3 and  type=2 and pay_time !='' and uniacid=".$_W['uniacid'];
$jfztdn = pdo_fetch($jfztdn);//昨天的微信店内销售额
$jfztyd = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$zttime." and is_yue=3 and  state not in (0,6) and uniacid=".$_W['uniacid'];
$jfztyd = pdo_fetch($jfztyd);//昨天的微信预定销售额
$jfztdmf = "select sum(money) as total from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$zttime." and state=2 and  is_yue=3 and uniacid=".$_W['uniacid'];
$jfztdmf = pdo_fetch($jfztdmf);//昨天的微信当面付销售额
$jfzttotal = $jfztwm['total']+$jfztdn['total']+$jfztyd['total']+$jfztdmf['total'];//昨天的微信销售额






$wm2 = "select * from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and state in (4,6,9) and type=1 and uniacid=".$_W['uniacid'];
$wm2 = pdo_fetchall($wm2);
$dn2 = "select * from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime." and dn_state=2 and type=2 and uniacid=".$_W['uniacid'];
$dn2 = pdo_fetchall($dn2);
$yd2 = "select * from " . tablename("wpdc_ydorder")." WHERE time2>=".$time." and time2<".$mttime." and state in (2,7) and uniacid=".$_W['uniacid'];
$yd2 = pdo_fetchall($yd2);
$dm2 = "select * from " . tablename("wpdc_dmorder")." WHERE time2>=".$time." and time2<".$mttime." and state=2 and uniacid=".$_W['uniacid'];
$dm2= pdo_fetchall($dm2);
$total2=(count($wm2)+count($dn2)+count($yd2)+count($dm2));


$ztwm2 = "select * from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime."   and state in (4,6,9) and type=1 and uniacid=".$_W['uniacid'];
$ztwm2 = pdo_fetchall($ztwm2);
$ztdn2 = "select * from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime."   and  dn_state=2 and type=2 and uniacid=".$_W['uniacid'];
$ztdn2 = pdo_fetchall($ztdn2);
$ztyd2 = "select * from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$zttime."   and state in (2,7) and uniacid=".$_W['uniacid'];
$ztyd2 = pdo_fetchall($ztyd2);
$ztdm2 = "select * from " . tablename("wpdc_dmorder")." WHERE time LIKE ".$zttime." and state=2 and uniacid=".$_W['uniacid'];
$ztdm2= pdo_fetchall($ztdm2);
$zttotal2=(count($ztwm2)+count($ztdn2)+count($ztyd2)+count($ztdm2));



$ztcz = "select sum(money) as total from " . tablename("wpdc_czorder")." WHERE time LIKE ".$zttime."  and state=2 and uniacid=".$_W['uniacid'];
$ztcz = pdo_fetch($ztcz);//昨天充值
$ztcz=$ztcz['total'];

$cz = "select sum(money) as total from " . tablename("wpdc_czorder")." WHERE unix_timestamp(time)>=".$time." and unix_timestamp(time)<".$mttime."  and state=2 and uniacid=".$_W['uniacid'];
$cz = pdo_fetch($cz);//今天充值
$cz=$cz['total'];


$wmtk="select sum(money) as total from " . tablename("wpdc_order")." WHERE time2>=".$time." and time2<".$mttime."  and state=8 and uniacid=".$_W['uniacid'];
$wmtk = pdo_fetch($wmtk);//今天的外卖退款
$ydtk = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE time2>=".$time." and time2<".$mttime."  and state=6 and uniacid=".$_W['uniacid'];
$ydtk = pdo_fetch($ydtk);//今天的预定退款
$tk=$wmtk['total']+$ydtk['total'];

$ztwmtk="select sum(money) as total from " . tablename("wpdc_order")." WHERE time LIKE ".$zttime."  and state=8 and uniacid=".$_W['uniacid'];
$ztwmtk = pdo_fetch($ztwmtk);//昨天的外卖退款
$ztydtk = "select sum(pay_money) as total from " . tablename("wpdc_ydorder")." WHERE created_time LIKE ".$zttime."  and state=6 and uniacid=".$_W['uniacid'];
$ztydtk = pdo_fetch($ztydtk);//昨天的预定退款
$zttk=$ztwmtk['total']+$ztydtk['total'];









$time2=date("Y-m-d");
$time3=date("Y-m-d",strtotime("-1 day"));
$time4=date("Y-m");
//会员总数
$totalhy=pdo_get('wpdc_user', array('uniacid'=>$_W['uniacid']), array('count(id) as count'));
//今日新增会员
$sql=" select a.* from (select  id,FROM_UNIXTIME(join_time) as time  from".tablename('wpdc_user')." where uniacid={$_W['uniacid']}) a where time like '%{$time2}%' ";
$jr=count(pdo_fetchall($sql));
//昨日新增
$sql2=" select a.* from (select  id,FROM_UNIXTIME(join_time) as time  from".tablename('wpdc_user')." where uniacid={$_W['uniacid']}) a where time like '%{$time3}%' ";
$zuor=count(pdo_fetchall($sql2));
//本月新增
$sql3=" select a.* from (select  id,FROM_UNIXTIME(join_time) as time  from".tablename('wpdc_user')." where uniacid={$_W['uniacid']}) a where time like '%{$time4}%' ";
$beny=count(pdo_fetchall($sql3));


//待提现
$tx=pdo_getall('wpdc_withdrawal',array('uniacid'=>$_W['uniacid'],'state'=>1));
$tx2=pdo_getall('wpdc_commission_withdrawal',array('uniacid'=>$_W['uniacid'],'state'=>1));
$dtx=count($tx)+count($tx2);
//已提现
$tx3=pdo_getall('wpdc_withdrawal',array('uniacid'=>$_W['uniacid'],'state'=>2));
$tx4=pdo_getall('wpdc_commission_withdrawal',array('uniacid'=>$_W['uniacid'],'state'=>2));
$ytx=count($tx3)+count($tx4);





$wm3 = "select * from " . tablename("wpdc_order")." WHERE  state!=1 and dn_state!=3 and uniacid=".$_W['uniacid'];
$wm3 = pdo_fetchall($wm3);
$yd3 = "select * from " . tablename("wpdc_ydorder")." WHERE  state!=0 and uniacid=".$_W['uniacid'];
$yd3 = pdo_fetchall($yd3);
$dm3 = "select * from " . tablename("wpdc_dmorder")." WHERE  state!=1 and uniacid=".$_W['uniacid'];
$dm3= pdo_fetchall($dm3);
$total3= (count($wm3)+count($yd3)+count($dm3));



$wm4 = "select * from " . tablename("wpdc_order")." WHERE  state=1 || dn_state=4 and uniacid=".$_W['uniacid'];
$wm4 = pdo_fetchall($wm4);
$yd4 = "select * from " . tablename("wpdc_ydorder")." WHERE  state=0 and uniacid=".$_W['uniacid'];
$yd4 = pdo_fetchall($yd4);
$dm4 = "select * from " . tablename("wpdc_dmorder")." WHERE  state=1 and uniacid=".$_W['uniacid'];
$dm4= pdo_fetchall($dm4);
$total4= (count($wm4)+count($yd4)+count($dm4));
include $this->template('web/gaikuangdata');