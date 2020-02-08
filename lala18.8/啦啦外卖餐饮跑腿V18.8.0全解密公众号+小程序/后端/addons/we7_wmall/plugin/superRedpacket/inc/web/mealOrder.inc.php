<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "套餐红包发放记录";
    $condition = " where a.uniacid = :uniacid and a.is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"]);
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]);
    } else {
        $today = strtotime(date("Y-m-d"));
        $starttime = strtotime("-15 day", $today);
        $endtime = $today + 86399;
    }
    $condition .= " and a.addtime >= :starttime and a.addtime <= :endtime";
    $params[":starttime"] = $starttime;
    $params[":endtime"] = $endtime;
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and b.nickname like '%" . $keyword . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_superredpacket_meal_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition, $params);
    $meal_orders = pdo_fetchall("select a.*,b.avatar,b.nickname from " . tablename("tiny_wmall_superredpacket_meal_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition . " order by a.id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($meal_orders)) {
        foreach ($meal_orders as &$order) {
            $order["data"] = iunserializer($order["data"]);
        }
    }
    $pay_types = order_pay_types();
    $pager = pagination($total, $pindex, $psize);
}
if ($op == "detail") {
    $_W["page"]["title"] = "订单详情";
    $id = $_GPC["id"];
    $order = pdo_fetch("select a.*,b.nickname from " . tablename("tiny_wmall_superredpacket_meal_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid where a.uniacid = :uniacid and a.id = :id order by a.id desc ", array(":uniacid" => $_W["uniacid"], ":id" => $id));
    if (!empty($order)) {
        $order["addtime"] = date("Y-m-d H:i", $order["addtime"]);
        $order["data"] = iunserializer($order["data"]);
    }
    $pay_types = order_pay_types();
}
include itemplate("mealOrder");

?>