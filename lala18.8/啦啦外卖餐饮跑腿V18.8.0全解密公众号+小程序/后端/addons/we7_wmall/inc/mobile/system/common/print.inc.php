<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("cron");
global $_W;
global $_GPC;
$orderid = intval($_GPC["id"]);
$sid = intval($_GPC["id"]);
if (empty($orderid)) {
    return error(-1, "订单不存在");
}
if (empty($sid)) {
    return error(-1, "商家不存在");
}
$printsn = pdo_get("tiny_wmall_order", array("id" => $orderid, "uniacid" => $_W["uniacid"]), array("print_sn"));
file_put_contents(MODULE_ROOT . "/dd1.txt", var_export($_GPC, 1));
if ($printsn["print_sn"] == $orderindex) {
    $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => intval($_GPC["sid"])), array("auto_handel_order"));
    if ($store["auto_handel_order"] == 2) {
        order_status_update($_GPC["id"], "handle");
    }
}

?>