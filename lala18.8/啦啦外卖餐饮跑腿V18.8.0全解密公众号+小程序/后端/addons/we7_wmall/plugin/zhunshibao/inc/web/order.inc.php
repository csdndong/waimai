<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "赔付订单";
    $condition = " where uniacid = :uniacid and order_type = 1 and status = 5";
    $zhunshibao_status = intval($_GPC["zhunshibao_status"]);
    if (0 < $zhunshibao_status) {
        $condition .= " AND zhunshibao_status = :zhunshibao_status";
        $params[":zhunshibao_status"] = $zhunshibao_status;
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
    } else {
        $starttime = strtotime("-7 day");
        $endtime = TIMESTAMP;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_order") . $condition, $params);
    $orders = pdo_fetchall("SELECT id, uid, username, sid, address, distance, deliveryer_id, addtime, note, data FROM " . tablename("tiny_wmall_order") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
}
include itemplate("order");

?>
