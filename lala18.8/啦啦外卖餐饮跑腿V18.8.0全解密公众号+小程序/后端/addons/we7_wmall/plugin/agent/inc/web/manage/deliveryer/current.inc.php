<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "配送员账户明细";
    $condition = " WHERE uniacid = :uniacid AND agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }
    $trade_type = intval($_GPC["trade_type"]);
    if (0 < $trade_type) {
        $condition .= " AND trade_type = :trade_type";
        $params[":trade_type"] = $trade_type;
    }
    $order_type = trim($_GPC["order_type"]);
    if (!empty($order_type)) {
        $condition .= " AND order_type = :order_type";
        $params[":order_type"] = $order_type;
    }
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : -2;
    $todaytime = strtotime(date("Y-m-d"));
    $starttime = $todaytime;
    $endtime = $starttime + 86399;
    if (-2 < $days) {
        if ($days == -1) {
            $starttime = strtotime($_GPC["addtime"]["start"]);
            $endtime = strtotime($_GPC["addtime"]["end"]);
            $condition .= " AND addtime > :start AND addtime < :end";
            $params[":start"] = $starttime;
            $params[":end"] = $endtime;
        } else {
            $starttime = strtotime("-" . $days . " days", $todaytime);
            $condition .= " and addtime >= :start";
            $params[":start"] = $starttime;
        }
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_deliveryer_current_log") . $condition, $params);
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer_current_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $order_trade_type = order_trade_type();
    $pager = pagination($total, $pindex, $psize);
    $deliveryers = deliveryer_all(true);
}
include itemplate("deliveryer/current");

?>