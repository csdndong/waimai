<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "check";
if ($ta == "check") {
    $id = intval($_GPC["id"]);
    $order_from = trim($_GPC["order_from"]);
    if (empty($order_from)) {
        $order_from = "takeout";
    }
    $routers = array("takeout" => array("table" => "tiny_wmall_order", "stop_status" => 1), "gohome" => array("table" => "tiny_wmall_gohome_order", "stop_status" => 1));
    $router = $routers[$order_from];
    $order = pdo_get($router["table"], array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        imessage(error(-1, "订单不存在"), "", "ajax");
    }
    if ($router["stop_status"] < $order["status"]) {
        imessage(error(-1, "订单已经接单"), "", "ajax");
    }
    imessage(error(0, ""), "", "ajax");
}

?>