<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$time=date('Y-m-d');
//获取订单总数据
$orders="select count( case when state=1 then 1 end) as djd, count( case when state=2 then 1 end) as yjd,count( case when state=3 then 1 end) as psz,count( case when state=4 then 1 end) as ywc from  ".tablename('cjpt_dispatch')." where uniacid={$_W['uniacid']}  ";
$orders=pdo_fetch($orders);
//今日订单数据
$jrorders="select count( case when state=1 then 1 end) as djdl, count( case when state in (2,3,4) then 1 end) as jdl,count( case when state=4 then 1 end) as ywcl,count( case when state=5 then 1 end) as qx from  ".tablename('cjpt_dispatch')." where uniacid={$_W['uniacid']} and  FROM_UNIXTIME(time) like '%{$time}%' ";
$jrorders=pdo_fetch($jrorders);

//获取骑手信息
$qs="select count( case when state=2 then 1 end) as total, count( case when status=1 and state=2 then 1 end) as zg,count( case when status=2 and state=2 then 1 end) as xx from  ".tablename('cjpt_rider')." where uniacid={$_W['uniacid']} ";
$qs=pdo_fetch($qs);

include $this->template('web/index');
