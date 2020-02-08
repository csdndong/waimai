<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "店铺订单统计统计";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    $condition = " WHERE uniacid = :uniacid and order_type <= 2";
    $params = array(":uniacid" => $_W["uniacid"]);
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
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
    if ($_W["isajax"]) {
        $stat = array();
        $stat["final_fee"] = $plateform["final_fee"];
        $stat["total_success_order"] = $plateform["total_success_order"];
        $stat["store_final_fee"] = $plateform["store_final_fee"];
        $stat["total_cancel_order"] = $plateform["total_cancel_order"];
        if ($orderby == "total_success_order") {
            $title = "有效订单量";
        } else {
            if ($orderby == "final_fee") {
                $title = "营业额";
            } else {
                if ($orderby == "store_final_fee") {
                    $title = "总收入";
                }
            }
        }
        $stat["title"] = $title;
        $i = 0;
        foreach ($records as $value) {
            if ($i == 10) {
                break;
            }
            $stat["sid"][] = $value["store_name"];
            if ($orderby == "total_success_order") {
                $stat["value"][] = $value["total_success_order"];
            } else {
                if ($orderby == "final_fee") {
                    $stat["value"][] = $value["final_fee"];
                } else {
                    if ($orderby == "store_final_fee") {
                        $stat["value"][] = $value["store_final_fee"];
                    }
                }
            }
            $i++;
        }
        message(error(0, $stat), "", "ajax");
    }
}
include itemplate("statcenter/takeoutOrder");

?>