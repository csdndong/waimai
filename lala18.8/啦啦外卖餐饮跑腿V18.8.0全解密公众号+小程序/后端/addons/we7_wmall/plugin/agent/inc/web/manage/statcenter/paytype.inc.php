<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "支付方式统计";
    $paytypes = array("delivery" => "货到付款", "credit" => "余额支付", "wechat" => "微信支付", "alipay" => "支付宝支付");
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]), array("id", "title"));
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid and order_type <= 2 and status = 5 and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
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
        $chart = array("fields" => array("total_success_order", "order_delivery", "order_credit", "order_wechat", "order_alipay"), "titles" => array("有效订单量", "货到付款订单", "余额支付订单", "微信支付订单", "支付宝支付订单"));
        $i = $starttime;
        while ($i <= $endtime) {
            $chart["days"][] = $i;
            foreach ($chart["fields"] as $field) {
                $chart[$field][$i] = 0;
            }
            $i = date("Ymd", strtotime($i) + 86400);
        }
        $total_success_order = pdo_fetchall("SELECT stat_day, count(*) as total_success_order FROM " . tablename("tiny_wmall_order") . $condition . " group by stat_day", $params);
        if (!empty($total_success_order)) {
            foreach ($total_success_order as $value) {
                if (in_array($value["stat_day"], $chart["days"])) {
                    $chart["total_success_order"][$value["stat_day"]] += $value["total_success_order"];
                }
            }
        }
        $stat = array();
        $stat["total_success_order"] = pdo_fetchcolumn("select count(*) as total_success_order from " . tablename("tiny_wmall_order") . $condition, $params);
        foreach ($paytypes as $key => $value) {
            $params[":pay_type"] = $key;
            $total_order = "total_" . $key . "_order";
            $stat[$total_order] = pdo_fetchcolumn("select count(*) as " . $total_order . " from " . tablename("tiny_wmall_order") . (string) $condition . " and pay_type = :pay_type", $params);
            $per_total_order = "per_total_" . $key . "_order";
            if ($stat["total_success_order"]) {
                $stat[$per_total_order] = round($stat[$total_order] / $stat["total_success_order"], 4) * 100;
            } else {
                $stat[$per_total_order] = 0;
            }
            $order = "order_" . $key;
            $one_order = pdo_fetchall("select stat_day, count(*) as " . $order . " from " . tablename("tiny_wmall_order") . (string) $condition . " and pay_type = :pay_type group by stat_day", $params);
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
    foreach ($paytypes as $key => $value) {
        $name = "order_" . $key;
        $params[":pay_type"] = $key;
        $records_all[$name] = pdo_fetchall("select stat_day, count(*) as " . $name . " from " . tablename("tiny_wmall_order") . (string) $condition . " and pay_type = :pay_type group by stat_day", $params, "stat_day");
        $sum = "sum_" . $key;
        $records_all[$sum] = pdo_fetchall("select stat_day, sum(final_fee) as " . $sum . " from " . tablename("tiny_wmall_order") . (string) $condition . " and pay_type = :pay_type group by stat_day", $params, "stat_day");
    }
    $basic = array("stat_day" => 0, "total_success_order" => 0, "order_delivery" => 0, "order_credit" => 0, "order_wechat" => 0, "order_alipay" => 0, "sum_delivery" => 0, "sum_credit" => 0, "sum_wechat" => 0, "sum_alipay" => 0);
    $i = $endtime;
    while ($starttime <= $i) {
        $basic["stat_day"] = $i;
        foreach ($paytypes as $key => $value) {
            $name = "order_" . $key;
            $records_all[$name][$i] = empty($records_all[$name][$i]) ? array() : $records_all[$name][$i];
            $sum = "sum_" . $key;
            $records_all[$sum][$i] = empty($records_all[$sum][$i]) ? array() : $records_all[$sum][$i];
        }
        $records_all["total_success_order"][$i] = empty($records_all["total_success_order"][$i]) ? array() : $records_all["total_success_order"][$i];
        $data = array_merge($basic, $records_all["total_success_order"][$i], $records_all["order_delivery"][$i], $records_all["order_credit"][$i], $records_all["order_wechat"][$i], $records_all["order_alipay"][$i], $records_all["sum_delivery"][$i], $records_all["sum_credit"][$i], $records_all["sum_wechat"][$i], $records_all["sum_alipay"][$i]);
        if (!empty($data["total_success_order"])) {
            foreach ($paytypes as $key => $value) {
                $pre_order = "pre_order_" . $key;
                $name = "order_" . $key;
                $data[$pre_order] = round($data[$name] / $data["total_success_order"], 4) * 100;
            }
        } else {
            foreach ($paytypes as $key => $value) {
                $pre_order = "pre_order_" . $key;
                $data[$pre_order] = 0;
            }
        }
        $records[] = $data;
        $i = date("Ymd", strtotime($i) - 86400);
    }
}
include itemplate("statcenter/paytype");

?>