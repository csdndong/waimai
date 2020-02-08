<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "订单来源统计";
    $channels = array("eleme" => "饿了么", "meituan" => "美团", "wxapp" => "小程序", "wap" => "H5", "h5app" => "顾客APP", "plateformCreate" => "后台创建");
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"));
    $condition = " WHERE uniacid = :uniacid and order_type <= 2 and status = 5 and is_pay = 1";
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
    if ($_W["isajax"]) {
        $chart = array("fields" => array("total_success_order", "order_eleme", "order_meituan", "order_wxapp", "order_wap", "order_h5app", "order_plateformCreate"), "titles" => array("有效订单量", "饿了么订单", "美团订单", "小程序订单", "H5订单", "顾客APP订单", "后台创建订单"));
        $i = $starttime;
        while ($i <= $endtime) {
            $chart["days"][] = $i;
            foreach ($chart["fields"] as $field) {
                $chart[$field][$i] = 0;
            }
            $i = date("Ymd", strtotime($i) + 86400);
        }
        $total_success_order = pdo_fetchall("select stat_day, count(*) as total_success_order from " . tablename("tiny_wmall_order") . $condition . " group by stat_day", $params);
        if (!empty($total_success_order)) {
            foreach ($total_success_order as $value) {
                if (in_array($value["stat_day"], $chart["days"])) {
                    $chart["total_success_order"][$value["stat_day"]] += $value["total_success_order"];
                }
            }
        }
        $stat = array();
        $stat["total_success_order"] = pdo_fetchcolumn("select count(*) as total_success_order from " . tablename("tiny_wmall_order") . $condition, $params);
        foreach ($channels as $key => $value) {
            $params[":order_channel"] = $key;
            $total_order = "total_" . $key . "_order";
            $stat[$total_order] = pdo_fetchcolumn("select count(*) as " . $total_order . " from " . tablename("tiny_wmall_order") . (string) $condition . " and order_channel = :order_channel", $params);
            $per_total_order = "per_total_" . $key . "_order";
            if ($stat["total_success_order"]) {
                $stat[$per_total_order] = round($stat[$total_order] / $stat["total_success_order"], 4) * 100;
            } else {
                $stat[$per_total_order] = 0;
            }
            $order = "order_" . $key;
            $one_order = pdo_fetchall("select stat_day, count(*) as " . $order . " from " . tablename("tiny_wmall_order") . (string) $condition . " and order_channel = :order_channel group by stat_day", $params);
            if (!empty($one_order)) {
                foreach ($one_order as $row) {
                    if (in_array($row["stat_day"], $chart["days"])) {
                        foreach ($chart["fields"] as $field) {
                            $chart[$field][$row["stat_day"]] += $row[$field];
                        }
                    }
                }
            }
        }
        $chart["stat"] = $stat;
        foreach ($chart["fields"] as $field) {
            $chart[$field] = array_values($chart[$field]);
        }
        message(error(0, $chart), "", "ajax");
    }
    $records_all = array();
    $records_all["total_success_order"] = pdo_fetchall("select stat_day,count(*) as total_success_order from " . tablename("tiny_wmall_order") . " " . $condition . " group by stat_day", $params, "stat_day");
    foreach ($channels as $key => $value) {
        $name = "order_" . $key;
        $params[":order_channel"] = $key;
        $records_all[$name] = pdo_fetchall("select stat_day,count(*) as " . $name . " from " . tablename("tiny_wmall_order") . $condition . " and order_channel = :order_channel group by stat_day", $params, "stat_day");
    }
    $basic = array("stat_day" => 0, "total_success_order" => 0, "order_eleme" => 0, "order_meituan" => 0, "order_wxapp" => 0, "order_wap" => 0, "order_h5app" => 0, "order_plateformCreate" => 0);
    $i = $endtime;
    while ($starttime <= $i) {
        $basic["stat_day"] = $i;
        foreach ($channels as $key => $value) {
            $name = "order_" . $key;
            $records_all[$name][$i] = empty($records_all[$name][$i]) ? array() : $records_all[$name][$i];
            $records_all["total_success_order"][$i] = empty($records_all["total_success_order"][$i]) ? array() : $records_all["total_success_order"][$i];
        }
        $data = array_merge($basic, $records_all["total_success_order"][$i], $records_all["order_eleme"][$i], $records_all["order_meituan"][$i], $records_all["order_wxapp"][$i], $records_all["order_wap"][$i], $records_all["order_h5app"][$i], $records_all["order_plateformCreate"][$i]);
        if (!empty($data["total_success_order"])) {
            foreach ($channels as $key => $value) {
                $pre_order = "pre_order_" . $key;
                $name = "order_" . $key;
                $data[$pre_order] = round($data[$name] / $data["total_success_order"], 4) * 100;
            }
        } else {
            foreach ($channels as $key => $value) {
                $pre_order = "pre_order_" . $key;
                $data[$pre_order] = 0;
            }
        }
        $records[] = $data;
        $i = date("Ymd", strtotime($i) - 86400);
    }
}
include itemplate("statcenter/takeoutOrderChannel");

?>