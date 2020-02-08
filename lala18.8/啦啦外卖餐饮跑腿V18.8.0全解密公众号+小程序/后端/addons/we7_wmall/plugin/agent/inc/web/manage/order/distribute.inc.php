<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "订单分布";
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_W["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $stat_type = $_GPC["stat_type"] ? trim($_GPC["stat_type"]) : "today";
    if ($stat_type == "today") {
        $condition .= " and stat_day = :stat_day";
        $params[":stat_day"] = date("Ymd");
    } else {
        if ($stat_type == "month") {
            $condition .= " and stat_month = :stat_month";
            $params[":stat_month"] = date("Ym");
        } else {
            if ($stat_type == "last_month") {
                $condition .= " and stat_month = :stat_month";
                $params[":stat_month"] = date("Ym", strtotime("-1 month"));
            }
        }
    }
    $orders = pdo_fetchall("select location_x, location_y from " . tablename("tiny_wmall_order") . $condition, $params);
}
include itemplate("order/distribute");

?>