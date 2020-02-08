<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $stores = pdo_fetchall("select id, title from" . tablename("tiny_wmall_store") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $condition = " WHERE uniacid = :uniacid and order_plateform = :order_plateform";
    $params = array(":uniacid" => $_W["uniacid"], ":order_plateform" => "we7_wmall");
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 5;
    if (0 < $status) {
        $condition .= " and status = :status";
        $params["status"] = $status;
    }
    $store = array();
    $sid = isset($_GPC["sid"]) ? intval($_GPC["sid"]) : 0;
    if (0 < $sid) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
        $store = $stores[$sid];
    }
    $days = isset($_GPC["stat_day"]) ? -1 : 1;
    if ($days == -1) {
        $starttime = str_replace("-", "", trim($_GPC["stat_day"]["start"]));
        $endtime = str_replace("-", "", trim($_GPC["stat_day"]["end"]));
        $condition .= " and stat_day >= :start_day and stat_day <= :end_day";
        $params[":start_day"] = $starttime;
        $params[":end_day"] = $endtime;
    } else {
        $starttime = $endtime = date("Ymd");
        $condition .= " and stat_day = :stat_day";
        $params[":stat_day"] = $starttime;
    }
    $orderby = trim($_GPC["orderby"]) ? trim($_GPC["orderby"]) : "total_goods_price";
    $stat = array();
    $stat["total_goods_num"] = intval(pdo_fetchcolumn("select sum(goods_num) from " . tablename("tiny_wmall_order_stat") . $condition, $params));
    $stat["total_goods_price"] = floatval(pdo_fetchcolumn("select round(sum(goods_price), 2) from " . tablename("tiny_wmall_order_stat") . $condition, $params));
    $records = pdo_fetchall("select sid, stat_day, goods_id, goods_title, sum(goods_price) as total_goods_price, sum(goods_num) as total_goods_num from " . tablename("tiny_wmall_order_stat") . $condition . " group by goods_id order by " . $orderby . " desc limit 100", $params);
    if (!empty($records)) {
        foreach ($records as &$row) {
            $row["pre_goods_price"] = round($row["total_goods_price"] / $stat["total_goods_price"], 2) * 100 . "%";
            $row["pre_goods_num"] = round($row["total_goods_num"] / $stat["total_goods_num"], 2) * 100 . "%";
            $row["goods"] = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $row["goods_id"]), array("title", "number", "is_options", "unitname"));
            $row["store_name"] = $stores[$row["sid"]]["title"];
        }
    }
    $result = array("stat" => $records, "total" => $stat, "store" => $store);
    message(error(0, $result), "", "ajax");
}

?>