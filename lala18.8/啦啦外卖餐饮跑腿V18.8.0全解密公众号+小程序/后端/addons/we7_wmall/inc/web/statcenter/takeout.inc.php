<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "外卖订单统计";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"));
    $condition = " WHERE uniacid = :uniacid and order_type <= 2";
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
    $perm_zhunshibao = check_plugin_perm("zhunshibao") ? 1 : 0;
    if ($_W["isajax"]) {
        $stat = array();
        $stat["total_fee"] = floatval(pdo_fetchcolumn("select round(sum(total_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        $stat["total_final_fee"] = floatval(pdo_fetchcolumn("select round(sum(final_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        $stat["total_success_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params);
        $stat["total_cancel_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition . " and status = 6", $params);
        $stat["total_cancel_fee"] = floatval(pdo_fetchcolumn("select round(sum(total_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 6", $params));
        $stat["avg_pre_order"] = floatval(0 < $stat["total_success_order"] ? $stat["total_fee"] / $stat["total_success_order"] : 0);
        $stat["total_serve_fee"] = floatval(pdo_fetchcolumn("select round(sum(plateform_serve_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        $stat["total_delivery_fee"] = floatval(pdo_fetchcolumn("select round(sum(plateform_delivery_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        $stat["total_deliveryer_fee"] = floatval(pdo_fetchcolumn("select round(sum(plateform_deliveryer_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        $stat["plateform_discount_fee"] = floatval(pdo_fetchcolumn("select round(sum(plateform_discount_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        $stat["total_refund_fee"] = floatval(pdo_fetchcolumn("select round(sum(refund_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and refund_status > 0", $params));
        $stat["total_extra_fee"] = floatval(pdo_fetchcolumn("select round(sum(extra_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        if ($_W["is_agent"]) {
            $stat["agent_discount_fee"] = floatval(pdo_fetchcolumn("select round(sum(agent_discount_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
            $stat["total_agent_serve_fee"] = floatval(pdo_fetchcolumn("select round(sum(agent_serve_fee), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1", $params));
        }
        $chart = array("stat" => $stat, "fields" => array("total_success_order", "total_fee", "final_fee", "plateform_delivery_fee", "plateform_serve_fee", "plateform_deliveryer_fee", "plateform_discount_fee", "agent_serve_fee", "agent_discount_fee", "refund_fee", "extra_fee"), "titles" => array("有效订单量", "营业总额", "总入账", "平台配送费收入", "佣金收入", "配送员配送费支出", "平台补贴", "平台佣金","代理补贴", "总退款", "附加费"));
        if ($perm_zhunshibao) {
            $chart["stat"]["total_zhunshibao_price"] = floatval(pdo_fetchcolumn("select round(sum(zhunshibao_price), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and zhunshibao_status > 0", $params));
            $chart["stat"]["total_zhunshibao_compensate"] = floatval(pdo_fetchcolumn("select round(sum(zhunshibao_compensate), 2) from " . tablename("tiny_wmall_order") . $condition . " and status = 5 and zhunshibao_status = 3", $params));
            $chart["fields"][] = "total_zhunshibao_price";
            $chart["fields"][] = "total_zhunshibao_compensate";
            $chart["titles"][] = "准时宝收入";
            $chart["titles"][] = "准时宝赔付";
        }
        $i = $starttime;
        while ($i <= $endtime) {
            $chart["days"][] = $i;
            foreach ($chart["fields"] as $field) {
                $chart[$field][$i] = 0;
            }
            $i = date("Ymd", strtotime($i) + 86400);
        }
        $records = pdo_fetchall("SELECT stat_day,\r\n\t\t\tcount(*) as total_success_order,\r\n\t\t\tround(sum(total_fee), 2) as total_fee,\r\n\t\t\tround(sum(final_fee), 2) as final_fee,\r\n\t\t\tround(sum(plateform_delivery_fee), 2) as plateform_delivery_fee,\r\n\t\t\tround(sum(plateform_deliveryer_fee), 2) as plateform_deliveryer_fee,\r\n\t\t\tround(sum(plateform_serve_fee), 2) as plateform_serve_fee,\r\n\t\t\tround(sum(plateform_discount_fee), 2) as plateform_discount_fee,\r\n\t\t\tround(sum(agent_serve_fee), 2) as agent_serve_fee,\r\n\t\t\tround(sum(agent_discount_fee), 2) as agent_discount_fee,\r\n\t\t\tround(sum(extra_fee), 2) as extra_fee\r\n\t\tFROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1 group by stat_day", $params);
        $refund_records = pdo_fetchall("SELECT stat_day,\r\n\t\t\tround(sum(refund_fee), 2) as refund_fee\r\n\t\tFROM " . tablename("tiny_wmall_order") . $condition . " and refund_status > 0 group by stat_day", $params);
        if (!empty($records)) {
            foreach ($records as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        if (!empty($refund_records)) {
            foreach ($refund_records as $refund_record) {
                if (in_array($refund_record["stat_day"], $chart["days"])) {
                    $chart["refund_fee"][$refund_record["stat_day"]] += $refund_record["refund_fee"];
                }
            }
        }
        $cancel_records = pdo_fetchall("SELECT stat_day, count(*) as total_cancel_order, sum(total_fee) as total_cancel_fee\r\n\t\tFROM " . tablename("tiny_wmall_order") . $condition . " and status = 6 group by stat_day", $params);
        if (!empty($cancel_records)) {
            foreach ($cancel_records as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        if ($perm_zhunshibao) {
            $zhunshibao_price_records = pdo_fetchall("SELECT stat_day, count(*) as total_zhunshibao_price_order, sum(zhunshibao_price) as total_zhunshibao_price\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and zhunshibao_status > 0 group by stat_day", $params);
            $zhunshibao_compensate_records = pdo_fetchall("SELECT stat_day, count(*) as total_zhunshibao_compensate_order, sum(zhunshibao_compensate) as total_zhunshibao_compensate\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and zhunshibao_status = 3 group by stat_day", $params);
            if (!empty($zhunshibao_price_records)) {
                foreach ($zhunshibao_price_records as $record) {
                    if (in_array($record["stat_day"], $chart["days"])) {
                        $chart["total_zhunshibao_price"][$record["stat_day"]] += $record["total_zhunshibao_price"];
                    }
                }
            }
            if (!empty($zhunshibao_compensate_records)) {
                foreach ($zhunshibao_compensate_records as $record) {
                    if (in_array($record["stat_day"], $chart["days"])) {
                        $chart["total_zhunshibao_compensate"][$record["stat_day"]] += $record["total_zhunshibao_compensate"];
                    }
                }
            }
        }
        foreach ($chart["fields"] as $field) {
            $chart[$field] = array_values($chart[$field]);
        }
        message(error(0, $chart), "", "ajax");
    }
    $records_temp = pdo_fetchall("SELECT stat_day,\r\n\t\tcount(*) as total_success_order,\r\n\t\tround(sum(total_fee), 2) as total_fee,\r\n\t\tround(sum(final_fee), 2) as final_fee,\r\n\t\tround(sum(plateform_serve_fee), 2) as plateform_serve_fee,\r\n\t\tround(sum(plateform_delivery_fee), 2) as plateform_delivery_fee,\r\n\t\tround(sum(plateform_deliveryer_fee), 2) as plateform_deliveryer_fee,\r\n\t\tround(sum(plateform_discount_fee), 2) as plateform_discount_fee,\r\n\t\tround(sum(agent_discount_fee), 2) as agent_discount_fee,\r\n\t\tround(sum(extra_fee), 2) as extra_fee\r\n\t FROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and is_pay = 1 group by stat_day", $params, "stat_day");
    $cancel_records = pdo_fetchall("SELECT stat_day, round(sum(refund_fee), 2) as refund_fee\r\n\t FROM " . tablename("tiny_wmall_order") . $condition . " and refund_status > 0 group by stat_day", $params, "stat_day");
    if ($perm_zhunshibao) {
        $zhunshibao_price_records = pdo_fetchall("SELECT stat_day, sum(zhunshibao_price) as zhunshibao_price\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and zhunshibao_status > 0 group by stat_day", $params, "stat_day");
        $zhunshibao_compensate_records = pdo_fetchall("SELECT stat_day, sum(zhunshibao_compensate) as zhunshibao_compensate\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and zhunshibao_status = 3 group by stat_day", $params, "stat_day");
    }
    $records = array();
    $i = $endtime;
    while ($starttime <= $i) {
        if (empty($records_temp[$i])) {
            $records[] = array("stat_day" => $i, "total_success_order" => 0, "total_fee" => 0, "final_fee" => 0, "plateform_serve_fee" => 0, "plateform_deliveryer_fee" => 0, "plateform_delivery_fee" => 0, "plateform_discount_fee" => 0, "agent_discount_fee" => 0, "refund_fee" => 0, "extra_fee" => 0);
        } else {
            $records[] = $records_temp[$i];
        }
        $i = date("Ymd", strtotime($i) - 86400);
    }
}
include itemplate("statcenter/takeout");

?>