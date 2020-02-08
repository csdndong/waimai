<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    $condition = " WHERE uniacid = :uniacid and order_type <= 2";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $sid = intval($_GPC["sid"]);
    $store = array();
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
    $orderby = trim($_GPC["orderby"]) ? trim($_GPC["orderby"]) : "final_fee";
    $plateform = pdo_fetch("SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee FROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params);
    $plateform["total_cancel_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition . " and status = 6", $params);
    $records = pdo_fetchall("SELECT count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, sid FROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1 group by sid order by " . $orderby . " desc", $params);
    $records_cancel = pdo_fetchall("select count(*) as total_cancel_order, sid from " . tablename("tiny_wmall_order") . $condition . " and status = 6 group by sid order by total_cancel_order desc", $params, "sid");
    if (!empty($records)) {
        foreach ($records as &$row) {
            $row["total_success_order"] = $row["total_success_order"] ? $row["total_success_order"] : 0;
            $row["store_final_fee"] = $row["store_final_fee"] ? $row["store_final_fee"] : 0;
            $row["final_fee"] = $row["final_fee"] ? $row["final_fee"] : 0;
            $row["total_cancel_order"] = $records_cancel[$row["sid"]]["total_cancel_order"] ? $records_cancel[$row["sid"]]["total_cancel_order"] : 0;
            $row["pre_final_fee"] = round($row["final_fee"] / $plateform["final_fee"], 4) * 100;
            $row["pre_success_order"] = round($row["total_success_order"] / $plateform["total_success_order"], 4) * 100;
            $row["pre_store_final_fee"] = round($row["store_final_fee"] / $plateform["store_final_fee"], 4) * 100;
            $row["pre_cancel_order"] = round($row["total_cancel_order"] / $plateform["total_cancel_order"], 4) * 100;
            $row["store_name"] = $stores[$row["sid"]]["title"];
        }
    }
    $result = array("stat" => $records, "total" => $plateform, "store" => $store);
    message(error(0, $result), "", "ajax");
}

?>