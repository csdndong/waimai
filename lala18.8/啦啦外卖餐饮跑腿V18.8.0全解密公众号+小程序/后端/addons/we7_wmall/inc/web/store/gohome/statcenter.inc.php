<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $_W["page"]["title"] = "订单统计";
    $condition = " WHERE uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $order_type = trim($_GPC["order_type"]);
    if (!empty($order_type)) {
        $condition .= " and order_type = :order_type";
        $params[":order_type"] = $order_type;
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
        $stat["total_fee"] = floatval(pdo_fetchcolumn("select round(sum(price), 2) from " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5)", $params));
        $stat["store_final_fee"] = floatval(pdo_fetchcolumn("select round(sum(store_final_fee), 2) from " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5)", $params));
        $stat["total_success_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5)", $params);
        $stat["total_cancel_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7", $params);
        $stat["total_cancel_fee"] = floatval(pdo_fetchcolumn("select round(sum(price), 2) from " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7", $params));
        $stat["avg_pre_order"] = floatval(0 < $stat["total_success_order"] ? $stat["total_fee"] / $stat["total_success_order"] : 0);
        $chart = array("stat" => $stat, "fields" => array("total_success_order", "total_fee", "store_final_fee", "store_discount_fee", "plateform_discount_fee", "agent_discount_fee", "plateform_serve_fee", "total_cancel_order", "total_cancel_fee"), "titles" => array("有效订单", "营业总额", "总收入", "商家补贴", "平台补贴", "代理补贴", "平台服务费", "无效订单", "损失营业额"));
        $i = $starttime;
        while ($i <= $endtime) {
            $chart["days"][] = $i;
            foreach ($chart["fields"] as $field) {
                $chart[$field][$i] = 0;
            }
            $i = date("Ymd", strtotime($i) + 86400);
        }
        $records = pdo_fetchall("SELECT stat_day, count(*) as total_success_order,round(sum(store_final_fee), 2) as store_final_fee, round(sum(price), 2) as total_fee, round(sum(plateform_discount_fee), 2) as plateform_discount_fee, round(sum(store_discount_fee), 2) as store_discount_fee, round(sum(agent_discount_fee), 2) as agent_discount_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee\r\n\t\tFROM " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5) group by stat_day", $params);
        if (!empty($records)) {
            foreach ($records as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        $cancel_records = pdo_fetchall("SELECT stat_day, count(*) as total_cancel_order, round(sum(price), 2) as total_cancel_fee FROM " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7 group by stat_day", $params);
        if (!empty($cancel_records)) {
            foreach ($cancel_records as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    $chart["total_cancel_order"][$record["stat_day"]] += $record["total_cancel_order"];
                    $chart["total_cancel_fee"][$record["stat_day"]] += $record["total_cancel_fee"];
                }
            }
        }
        foreach ($chart["fields"] as $field) {
            $chart[$field] = array_values($chart[$field]);
        }
        imessage(error(0, $chart), "", "ajax");
    }
    $records_temp = pdo_fetchall("SELECT stat_day, count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(store_final_fee), 2) as store_final_fee, round(sum(plateform_discount_fee), 2) as plateform_discount_fee, round(sum(agent_discount_fee), 2) as agent_discount_fee, round(sum(store_discount_fee), 2) as store_discount_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee\r\n\t FROM " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5) group by stat_day", $params, "stat_day");
    $cancel_records = pdo_fetchall("SELECT stat_day, count(*) as total_cancel_order\r\n\t FROM " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7 group by stat_day", $params, "stat_day");
    $records = array();
    $i = $endtime;
    while ($starttime <= $i) {
        if (empty($records_temp[$i])) {
            $records[] = array("stat_day" => $i, "total_success_order" => 0, "final_fee" => 0, "store_final_fee" => 0, "plateform_discount_fee" => 0, "agent_discount_fee" => 0, "store_discount_fee" => 0, "plateform_serve_fee" => 0);
        } else {
            $records[] = $records_temp[$i];
        }
        $i = date("Ymd", strtotime($i) - 86400);
    }
}
include itemplate("store/gohome/statcenter");

?>