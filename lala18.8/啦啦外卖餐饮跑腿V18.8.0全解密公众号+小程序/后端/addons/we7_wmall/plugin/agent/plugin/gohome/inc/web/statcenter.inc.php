<?php
defined('IN_IA') or exit('Access Denied');
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "订单统计";
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $sid = intval($_GPC["sid"]);
    if (!empty($sid)) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
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
        $stat["total_final_fee"] = floatval(pdo_fetchcolumn("select round(sum(final_fee), 2) from " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5)", $params));
        $stat["total_success_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5)", $params);
        $stat["total_serve_fee"] = floatval(pdo_fetchcolumn("select round(sum(plateform_serve_fee), 2) from " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5)", $params));
        $stat["total_cancel_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7", $params);
        $stat["total_refund_fee"] = floatval(pdo_fetchcolumn("select round(sum(final_fee), 2) from " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7 and refund_status > 0", $params));
        $stat["total_agent_serve_fee"] = floatval(pdo_fetchcolumn("select round(sum(agent_serve_fee), 2) from " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5)", $params));
        $chart = array("stat" => $stat, "fields" => array("total_success_order", "total_fee", "final_fee", "plateform_serve_fee", "agent_serve_fee", "refund_fee"), "titles" => array("有效订单数", "营业总额", "总入帐", "代理佣金收入", "平台服务费", "总退款"));
        $i = $starttime;
        while ($i <= $endtime) {
            $chart["days"][] = $i;
            foreach ($chart["fields"] as $field) {
                $chart[$field][$i] = 0;
            }
            $i = date("Ymd", strtotime($i) + 86400);
        }
        $records = pdo_fetchall("SELECT stat_day, count(*) as total_success_order,round(sum(final_fee), 2) as final_fee, round(sum(price), 2) as total_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee, round(sum(agent_serve_fee), 2) as agent_serve_fee\r\n\t\tFROM " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5) group by stat_day", $params);
        if (!empty($records)) {
            foreach ($records as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        $cancel_records = pdo_fetchall("SELECT stat_day, sum(final_fee) as refund_fee FROM " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7 and refund_status > 0 group by stat_day", $params);
        if (!empty($cancel_records)) {
            foreach ($cancel_records as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    $chart["refund_fee"][$record["stat_day"]] += $record["refund_fee"];
                }
            }
        }
        foreach ($chart["fields"] as $field) {
            $chart[$field] = array_values($chart[$field]);
        }
        imessage(error(0, $chart), "", "ajax");
    }
    $records_temp = pdo_fetchall("SELECT stat_day, count(*) as total_success_order, round(sum(final_fee), 2) as final_fee, round(sum(price), 2) as total_fee, round(sum(plateform_serve_fee), 2) as plateform_serve_fee, round(sum(agent_serve_fee), 2) as agent_serve_fee\r\n\t FROM " . tablename("tiny_wmall_gohome_order") . $condition . " and (status = 6 or status = 5) group by stat_day", $params, "stat_day");
    $cancel_records = pdo_fetchall("SELECT stat_day, round(sum(final_fee), 2) as refund_fee\r\n\t FROM " . tablename("tiny_wmall_gohome_order") . $condition . " and status = 7 and refund_status > 0 group by stat_day", $params, "stat_day");
    $records = array();
    $i = $endtime;
    while ($starttime <= $i) {
        if (empty($records_temp[$i])) {
            $records[] = array("stat_day" => $i, "total_success_order" => 0, "total_fee" => 0, "final_fee" => 0, "plateform_serve_fee" => 0, "agent_serve_fee" => 0);
        } else {
            $records[] = $records_temp[$i];
        }
        $i = date("Ymd", strtotime($i) - 86400);
    }
    $stores = store_fetchall(array("id", "title"));
}
include itemplate("statcenter");

?>
