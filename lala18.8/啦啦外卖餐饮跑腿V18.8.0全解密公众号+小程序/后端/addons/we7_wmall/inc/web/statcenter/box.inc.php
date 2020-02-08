<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "餐盒费统计";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    $condition = " WHERE uniacid = :uniacid and status = 5 and order_type <= 2";
    $params = array(":uniacid" => $_W["uniacid"]);
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : 0;
    if ($days == -1) {
        $starttime = str_replace("-", "", trim($_GPC["stat_day"]["start"]));
        $endtime = str_replace("-", "", trim($_GPC["stat_day"]["end"]));
        $condition .= " and stat_day >= :start_day and stat_day <= :end_day";
        $params[":start_day"] = $starttime;
        $params[":end_day"] = $endtime;
    } else {
        $todaytime = strtotime(date("Y-m-d"));
        $starttime = date("Ymd", strtotime("-" . $days . " days", $todaytime));
        $endtime = date("Ymd", $todaytime + 86399);
        $condition .= " and stat_day >= :stat_day";
        $params[":stat_day"] = $starttime;
    }
    if ($_W["isajax"]) {
        $stat = array();
        $stat["total_fee"] = floatval(pdo_fetchcolumn("select round(sum(box_price), 2) from " . tablename("tiny_wmall_order") . $condition, $params));
        message(error(0, $stat), "", "ajax");
    }
    $records_temp = pdo_fetchall("SELECT sid, round(sum(box_price), 2) as total_box_price\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition . " group by sid", $params, "sid");
    if (empty($sid)) {
        $records = array();
        foreach ($stores as $store) {
            $records[] = array("sid" => $store["id"], "title" => $store["title"], "total_box_price" => $records_temp["total_box_price"]);
        }
    } else {
        $records = $records_temp;
    }
    if (!empty($records)) {
        $records = array_sort($records, "total_box_price", SORT_DESC);
    }
}
include itemplate("statcenter/box");

?>