<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "log";
if ($op == "log") {
    $_W["page"]["title"] = "账户明细";
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $trade_type = intval($_GPC["trade_type"]);
    if (0 < $trade_type) {
        $condition .= " AND trade_type = :trade_type";
        $params[":trade_type"] = $trade_type;
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]);
    } else {
        $today = strtotime(date("Y-m-d"));
        $starttime = strtotime("-15 day", $today);
        $endtime = $today + 86399;
    }
    $order_type = trim($_GPC["order_type"]);
    if ($trade_type == 1 && !empty($order_type)) {
        $condition .= " and order_type = :order_type";
        $params[":order_type"] = $order_type;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_agent_current_log") . $condition, $params);
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_agent_current_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $order_trade_type = order_trade_type();
    $pager = pagination($total, $pindex, $psize);
}
include itemplate("finance/account");

?>