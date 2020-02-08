<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("plugin");
pload()->model("mealRedpacket");
if ($ta == "list") {
    $id = intval($_GPC["id"]);
    $order = mealRedpacket_order_get($id);
    if ($order["type"] == "exchangeRedpacket") {
        $order["data"]["meal"]["data"] = array_values($order["data"]["meal"]["redpackets"]);
    } else {
        if ($order["type"] == "mealRedpacket") {
            $order["data"]["meal"]["data"] = array_values($order["data"]["meal"]["data"]);
        }
    }
    $result = array("records" => $order);
    imessage(error(0, $result), "", "ajax");
}

?>