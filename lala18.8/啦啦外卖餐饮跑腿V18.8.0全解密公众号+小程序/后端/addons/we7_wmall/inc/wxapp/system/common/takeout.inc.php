<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "check";
if ($ta == "check") {
    $id = intval($_GPC["id"]);
    $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        imessage(error(-1, "订单不存在"), "", "ajax");
    }
    if (1 < $order["status"]) {
        imessage(error(-1, "订单已接单"), "", "ajax");
    }
    imessage(error(0, ""), "", "ajax");
}
if ($ta == "location") {
    $location_x = floatval($_GPC["location_x"]);
    $location_y = floatval($_GPC["location_y"]);
    if (empty($location_x) || empty($location_y)) {
        message(ierror(-1, "地理位置不完善"), "", "ajax");
    }
    $token = trim($_GPC["token"]);
    $deliveryer = deliveryer_fetch($token, "token");
    file_put_contents(MODULE_ROOT . "/aa.txt", var_export($deliveryer, 1));
    pdo_query("delete from " . tablename("tiny_wmall_deliveryer_location_log") . " where addtime <= :addtime", array(":addtime" => TIMESTAMP - 10 * 86400));
    pdo_update("tiny_wmall_deliveryer", array("location_x" => $location_x, "location_y" => $location_y), array("uniacid" => $_W["uniacid"], "id" => $deliveryer["id"]));
    $data = array("uniacid" => $_W["uniacid"], "deliveryer_id" => $deliveryer["id"], "location_x" => $location_x, "location_y" => $location_y, "addtime" => TIMESTAMP, "addtime_cn" => date("Y-m-d H:i:s"));
    pdo_insert("tiny_wmall_deliveryer_location_log", $data);
    message(ierror(0, ""), "", "ajax");
}

?>