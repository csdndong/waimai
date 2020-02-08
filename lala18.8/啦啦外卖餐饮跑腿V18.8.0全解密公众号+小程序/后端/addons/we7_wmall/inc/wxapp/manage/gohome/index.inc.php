<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $gohome = array(array("title" => "砍价", "icon" => "icon-hot_light", "iconColor" => "c-danger", "url" => "/pages/gohome/kanjia/goods/list"), array("title" => "拼团", "icon" => "icon-hot1", "iconColor" => "c-danger", "url" => "/pages/gohome/pintuan/goods/list"), array("title" => "抢购", "icon" => "icon-hot1", "iconColor" => "c-danger", "url" => "/pages/gohome/seckill/goods/list"), array("title" => "订单列表", "icon" => "icon-order", "iconColor" => "c-info", "url" => "/pages/gohome/order/index"), array("title" => "订单统计", "icon" => "icon-rank", "iconColor" => "c-info", "url" => "/pages/gohome/statcenter/list"));
    $result = array("gohome" => $gohome);
    imessage(error(0, $result), "", "ajax");
}

?>