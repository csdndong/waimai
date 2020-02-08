<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
$config_takeout["delivery_timeout_limit"] = intval($config_takeout["delivery_timeout_limit"]);
if (empty($config_takeout["delivery_timeout_limit"])) {
    $config_takeout["delivery_timeout_limit"] = 45;
}
$config_takeout["delivery_before_limit"] = intval($config_takeout["delivery_before_limit"]);
if (empty($config_takeout["delivery_before_limit"])) {
    $config_takeout["delivery_before_limit"] = 30;
}
$config_takegoods_timeout = $config_takeout["delivery_status_7"]["timeout_limit"] ? $config_takeout["delivery_status_7"]["timeout_limit"] * 60 : 15 * 60;
$config_service_timeout = $config_takeout["delivery_status_4"]["timeout_limit"] ? $config_takeout["delivery_status_4"]["timeout_limit"] * 60 : 15 * 60;
if ($op == "index") {
    $_W["page"]["title"] = "配送员统计";
    $condition = " as a left join " . tablename("tiny_wmall_order_comment") . " as b on a.id = b.oid where a.uniacid = :uniacid and a.status = 5 and a.delivery_type = 2 and a.order_type <= 2";
    $params = array(":uniacid" => $_W["uniacid"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " and a.deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : 0;
    if ($days == -1) {
        $starttime = str_replace("-", "", trim($_GPC["stat_day"]["start"]));
        $endtime = str_replace("-", "", trim($_GPC["stat_day"]["end"]));
        $condition .= " and a.stat_day >= :start_day and a.stat_day <= :end_day";
        $params[":start_day"] = $starttime;
        $params[":end_day"] = $endtime;
    } else {
        $todaytime = strtotime(date("Y-m-d"));
        $starttime = date("Ymd", strtotime("-" . $days . " days", $todaytime));
        $endtime = date("Ymd", $todaytime + 86399);
        $condition .= " and a.stat_day >= :stat_day";
        $params[":stat_day"] = $starttime;
    }
    $condition .= " and a.deliveryer_id != 0";
    $condition_normal = (string) $condition . " and (a.endtime - a.clerk_notify_collect_time < " . $config_takeout["delivery_timeout_limit"] . " * 60)";
    $condition_timeout = (string) $condition . " and (a.endtime - a.clerk_notify_collect_time > " . $config_takeout["delivery_timeout_limit"] . " * 60)";
    $condition_before = (string) $condition . " and (a.endtime - a.clerk_notify_collect_time <= " . $config_takeout["delivery_before_limit"] . " * 60)";
    $condition_comment = (string) $condition;
    $condition_takegoods_timeout = (string) $condition . " and (a.delivery_takegoods_time - a.delivery_assign_time) > " . $config_takegoods_timeout;
    $condition_service_timeout = (string) $condition . " and (a.delivery_success_time - a.delivery_takegoods_time) > " . $config_service_timeout;
    if ($_W["isajax"]) {
        $stat = array();
        $stat["total_success_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition, $params);
        $stat["total_normal_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition_normal, $params);
        $stat["total_timeout_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " " . $condition_timeout, $params);
        $stat["avg_normal_delivery_time"] = floatval(pdo_fetchcolumn("select round(avg(endtime - clerk_notify_collect_time) / 60, 2) from " . tablename("tiny_wmall_order") . " " . $condition_normal, $params));
        $stat["total_before_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " " . $condition_before, $params);
        $stat["total_comment_order"] = pdo_fetchcolumn("select count(b.id) from " . tablename("tiny_wmall_order") . " " . $condition_comment, $params);
        if (!$stat["total_success_order"]) {
            $stat["percent_normal"] = 0;
            $stat["percent_timeout"] = 0;
        } else {
            $stat["percent_normal"] = round(($stat["total_success_order"] - $stat["total_timeout_order"]) / $stat["total_success_order"], 2) * 100;
            $stat["percent_timeout"] = round($stat["total_timeout_order"] / $stat["total_success_order"], 2) * 100;
        }
        $chart = array("stat" => $stat, "fields" => array("total_success_order", "total_normal_order", "total_timeout_order", "total_before_order", "avg_normal_delivery_time", "total_takegoods_timeout_order", "total_service_timeout_order"), "titles" => array("总配送", "正常送达订单", "超时订单", "提前订单", "普通单平均配送时长"));
        $i = $starttime;
        while ($i <= $endtime) {
            $chart["days"][] = $i;
            foreach ($chart["fields"] as $field) {
                $chart[$field][$i] = 0;
            }
            $i = date("Ymd", strtotime($i) + 86400);
        }
        $records = pdo_fetchall("SELECT\r\n\t\t\tstat_day,\r\n\t\t\tcount(*) as total_success_order\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition . " group by stat_day", $params, "stat_day");
        if (!empty($records)) {
            foreach ($records as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        $records_normal = pdo_fetchall("SELECT\r\n\t\t\tstat_day,\r\n\t\t\tcount(*) as total_normal_order,\r\n\t\t\tround(avg(endtime - clerk_notify_collect_time) / 60, 2) as avg_normal_delivery_time\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition_normal . " group by stat_day", $params, "stat_day");
        if (!empty($records_normal)) {
            foreach ($records_normal as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        $records_timeouts = pdo_fetchall("SELECT\r\n\t\t\tstat_day,\r\n\t\t\tcount(*) as total_timeout_order\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition_timeout . " group by stat_day", $params, "stat_day");
        if (!empty($records_timeouts)) {
            foreach ($records_timeouts as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        $records_befores = pdo_fetchall("SELECT\r\n\t\t\tstat_day,\r\n\t\t\tcount(*) as total_before_order\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition_before . " group by stat_day", $params, "stat_day");
        if (!empty($records_befores)) {
            foreach ($records_befores as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        $records_takegoods_timeout_order = pdo_fetchall("SELECT\r\n\t\t\tstat_day,\r\n\t\t\tcount(*) as total_takegoods_timeout_order\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition_takegoods_timeout . " group by stat_day", $params, "stat_day");
        if (!empty($records_takegoods_timeout_order)) {
            foreach ($records_takegoods_timeout_order as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        $records_service_timeout_order = pdo_fetchall("SELECT\r\n\t\t\tstat_day,\r\n\t\t\tcount(*) as total_service_timeout_order\r\n\t\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition_service_timeout . " group by stat_day", $params, "stat_day");
        if (!empty($records_service_timeout_order)) {
            foreach ($records_service_timeout_order as $record) {
                if (in_array($record["stat_day"], $chart["days"])) {
                    foreach ($chart["fields"] as $field) {
                        $chart[$field][$record["stat_day"]] += $record[$field];
                    }
                }
            }
        }
        foreach ($chart["fields"] as $field) {
            $chart[$field] = array_values($chart[$field]);
        }
        message(error(0, $chart), "", "ajax");
    }
    $deliveryers = deliveryer_fetchall(0, array("work_status" => -1, "agentid" => -1));
    $records_temp = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(*) as total_success_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition . " group by stat_day", $params, "stat_day");
    $records_normal = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(*) as total_normal_order,\r\n\t\tround(avg(endtime - clerk_notify_collect_time) / 60, 2) as avg_normal_delivery_time,\r\n\t\tround(avg(delivery_assign_time - clerk_notify_collect_time) / 60, 2) as avg_delivery_notify_time,\r\n\t\tround(avg(delivery_takegoods_time - delivery_assign_time) / 60, 2) as avg_delivery_takegoods_time,\r\n\t\tround(avg(delivery_success_time - delivery_instore_time) / 60, 2) as avg_delivery_success_time\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_normal . " group by stat_day", $params, "stat_day");
    $records_timeout = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(*) as total_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_timeout . " group by stat_day", $params, "stat_day");
    $records_before = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(*) as total_before_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_before . " group by stat_day", $params, "stat_day");
    $records_takegoods_timeout_order = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(*) as total_takegoods_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_takegoods_timeout . " group by stat_day", $params, "stat_day");
    $records_service_timeout_order = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(*) as total_service_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_service_timeout . " group by stat_day", $params, "stat_day");
    $records_comment_temp = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(b.id) as commment_num,\r\n\t\tdelivery_service\r\n\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition_comment . " group by stat_day,delivery_service", $params);
    $records_comment = array();
    if (!empty($records_comment_temp)) {
        foreach ($records_comment_temp as $row) {
            if (!isset($records_comment[$row["stat_day"]])) {
                $records_comment[$row["stat_day"]] = array("total_comment_order" => 0, "total_comment_1" => 0, "total_comment_2" => 0, "total_comment_3" => 0, "total_comment_4" => 0, "total_comment_5" => 0);
            }
            $records_comment[$row["stat_day"]]["total_comment_order"] += $row["commment_num"];
            if (0 < $row["commment_num"]) {
                $key = "total_comment_" . $row["delivery_service"];
                $records_comment[$row["stat_day"]][$key] += $row["commment_num"];
            }
        }
    }
    $records = array();
    $basic = array("stat_day" => 0, "total_success_order" => 0, "total_timeout_order" => 0, "total_before_order" => 0, "avg_delivery_time" => 0, "avg_normal_delivery_time" => 0, "total_comment_order" => 0, "percent_normal" => 0, "percent_timeout" => 0, "avg_delivery_notify_time" => 0, "avg_delivery_takegoods_time" => 0, "avg_delivery_success_time" => 0, "total_takegoods_timeout_order" => 0, "total_service_timeout_order" => 0);
    $i = $endtime;
    while ($starttime <= $i) {
        $basic["stat_day"] = $i;
        $records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
        $records_normal[$i] = empty($records_normal[$i]) ? array() : $records_normal[$i];
        $records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
        $records_before[$i] = empty($records_before[$i]) ? array() : $records_before[$i];
        $records_takegoods_timeout_order[$i] = empty($records_takegoods_timeout_order[$i]) ? array() : $records_takegoods_timeout_order[$i];
        $records_service_timeout_order[$i] = empty($records_service_timeout_order[$i]) ? array() : $records_service_timeout_order[$i];
        $records_comment[$i] = empty($records_comment[$i]) ? array() : $records_comment[$i];
        $data = array_merge($basic, $records_temp[$i], $records_normal[$i], $records_timeout[$i], $records_before[$i], $records_comment[$i], $records_takegoods_timeout_order[$i], $records_service_timeout_order[$i]);
        if (!empty($data["total_success_order"])) {
            $data["percent_normal"] = round(($data["total_success_order"] - $data["total_timeout_order"]) / $data["total_success_order"], 2) * 100;
            $data["percent_timeout"] = round($data["total_timeout_order"] / $data["total_success_order"], 2) * 100;
        }
        $records[] = $data;
        $i = date("Ymd", strtotime($i) - 86400);
    }
    include itemplate("statcenter/deliveryIndex");
    return 1;
} else {
    if ($op == "day") {
        $_W["page"]["title"] = "配送订单统计";
        $condition = " as a left join " . tablename("tiny_wmall_order_comment") . " as b on a.id = b.oid where a.uniacid = :uniacid and a.status = 5 and a.delivery_type = 2 and a.order_type <= 2";
        $params = array(":uniacid" => $_W["uniacid"]);
        $agentid = intval($_GPC["agentid"]);
        if (0 < $agentid) {
            $condition .= " and a.agentid = :agentid";
            $params[":agentid"] = $agentid;
        }
        $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : 0;
        if ($days == -1) {
            if (is_array($_GPC["stat_day"])) {
                $starttime = str_replace("-", "", trim($_GPC["stat_day"]["start"]));
                $endtime = str_replace("-", "", trim($_GPC["stat_day"]["end"]));
            } else {
                $stat_day = intval($_GPC["stat_day"]);
                $starttime = str_replace("-", "", $stat_day);
                $endtime = str_replace("-", "", date("Ymd", strtotime($stat_day) + 86399));
            }
            $condition .= " and a.stat_day >= :start_day and a.stat_day <= :end_day";
            $params[":start_day"] = $starttime;
            $params[":end_day"] = $endtime;
        } else {
            $todaytime = strtotime(date("Y-m-d"));
            $starttime = date("Ymd", strtotime("-" . $days . " days", $todaytime));
            $endtime = date("Ymd", $todaytime + 86399);
            $condition .= " and a.stat_day >= :stat_day";
            $params[":stat_day"] = $starttime;
        }
        $condition .= " and a.deliveryer_id != 0";
        $condition_normal = (string) $condition . " and (a.endtime - a.clerk_notify_collect_time < " . $config_takeout["delivery_timeout_limit"] . " * 60)";
        $condition_timeout = (string) $condition . " and (a.endtime - a.clerk_notify_collect_time > " . $config_takeout["delivery_timeout_limit"] . " * 60)";
        $condition_before = (string) $condition . " and (a.endtime - a.clerk_notify_collect_time <= " . $config_takeout["delivery_before_limit"] . " * 60)";
        $condition_comment = (string) $condition;
        $condition_takegoods_timeout = (string) $condition . " and (a.delivery_takegoods_time - a.delivery_assign_time) > " . $config_takegoods_timeout;
        $condition_service_timeout = (string) $condition . " and (a.delivery_success_time - a.delivery_takegoods_time) > " . $config_service_timeout;
        $stat = array();
        $stat["total_success_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition, $params);
        $stat["total_timeout_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " " . $condition_timeout, $params);
        $stat["total_normal_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " " . $condition_normal, $params);
        $stat["avg_normal_delivery_time"] = floatval(pdo_fetchcolumn("select round(avg(endtime - clerk_notify_collect_time) / 60, 2) from " . tablename("tiny_wmall_order") . " " . $condition_normal, $params));
        $stat["total_takegoods_timeout_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition_takegoods_timeout, $params);
        $stat["total_service_timeout_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition_service_timeout, $params);
        $stat["total_before_order"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " " . $condition_before, $params);
        $stat["total_comment_order"] = pdo_fetchcolumn("select count(b.id) from " . tablename("tiny_wmall_order") . " " . $condition_comment, $params);
        $stat["total_paytype_delivery"] = floatval(pdo_fetchcolumn("select sum(a.final_fee) from " . tablename("tiny_wmall_order") . " " . $condition . " and a.pay_type = 'delivery'", $params));
        if (!$stat["total_success_order"]) {
            $stat["percent_normal"] = 0;
            $stat["percent_timeout"] = 0;
        } else {
            $stat["percent_normal"] = round(($stat["total_success_order"] - $stat["total_timeout_order"]) / $stat["total_success_order"], 2) * 100;
            $stat["percent_timeout"] = round($stat["total_timeout_order"] / $stat["total_success_order"], 2) * 100;
        }
        $records_temp = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\ta.deliveryer_id,\r\n\t\tcount(*) as total_success_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition . " group by a.deliveryer_id", $params, "deliveryer_id");
        $records_normal = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\ta.deliveryer_id,\r\n\t\tcount(*) as total_normal_order,\r\n\t\tround(avg(endtime - clerk_notify_collect_time) / 60, 2) as avg_normal_delivery_time,\r\n\t\tround(avg(delivery_assign_time - clerk_notify_collect_time) / 60, 2) as avg_delivery_notify_time,\r\n\t\tround(avg(delivery_takegoods_time - delivery_assign_time) / 60, 2) as avg_delivery_takegoods_time,\r\n\t\tround(avg(delivery_success_time - delivery_instore_time) / 60, 2) as avg_delivery_success_time\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_normal . " group by a.deliveryer_id", $params, "deliveryer_id");
        $records_timeout = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\ta.deliveryer_id,\r\n\t\tcount(*) as total_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_timeout . " group by a.deliveryer_id", $params, "deliveryer_id");
        $records_before = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\ta.deliveryer_id,\r\n\t\tcount(*) as total_before_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_before . " group by a.deliveryer_id", $params, "deliveryer_id");
        $records_takegoods_timeout_order = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\ta.deliveryer_id,\r\n\t\tcount(*) as total_takegoods_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_takegoods_timeout . " group by a.deliveryer_id", $params, "deliveryer_id");
        $records_service_timeout_order = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\ta.deliveryer_id,\r\n\t\tcount(*) as total_service_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_service_timeout . " group by a.deliveryer_id", $params, "deliveryer_id");
        $records_comment_temp = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tcount(b.id) as commment_num,\r\n\t\tdelivery_service,\r\n\t\ta.deliveryer_id\r\n\t\tFROM " . tablename("tiny_wmall_order") . " " . $condition_comment . " group by a.deliveryer_id,delivery_service", $params);
        $records_comment = array();
        if (!empty($records_comment_temp)) {
            foreach ($records_comment_temp as $row) {
                if (!isset($records_comment[$row["deliveryer_id"]])) {
                    $records_comment[$row["deliveryer_id"]] = array("total_comment_order" => 0, "total_comment_1" => 0, "total_comment_2" => 0, "total_comment_3" => 0, "total_comment_4" => 0, "total_comment_5" => 0);
                }
                $records_comment[$row["deliveryer_id"]]["total_comment_order"] += $row["commment_num"];
                if (0 < $row["commment_num"]) {
                    $key = "total_comment_" . $row["delivery_service"];
                    $records_comment[$row["deliveryer_id"]][$key] += $row["commment_num"];
                }
            }
        }
        $condition_paytype_delivery = (string) $condition . " and a.pay_type = :pay_type";
        $params[":pay_type"] = "delivery";
        $records_paytype_delivery = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\ta.deliveryer_id,\r\n\t\tsum(final_fee) as total_paytype_delivery\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_paytype_delivery . " group by a.deliveryer_id", $params, "deliveryer_id");
        $records = array();
        $basic = array("id" => 0, "total_success_order" => 0, "total_timeout_order" => 0, "total_normal_order" => 0, "total_before_order" => 0, "avg_delivery_time" => 0, "avg_normal_delivery_time" => 0, "total_comment_order" => 0, "percent_normal" => 0, "percent_timeout" => 0, "avg_delivery_notify_time" => 0, "avg_delivery_takegoods_time" => 0, "avg_delivery_success_time" => 0, "total_takegoods_timeout_order" => 0, "total_service_timeout_order" => 0, "total_paytype_delivery" => 0);
        $records = array();
        $deliveryers = deliveryer_fetchall(0, array("work_status" => -1, "agentid" => 0 < $agentid ? $agentid : -1));
        foreach ($deliveryers as $deliveryer) {
            $i = $deliveryer["id"];
            $records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
            $records_normal[$i] = empty($records_normal[$i]) ? array() : $records_normal[$i];
            $records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
            $records_before[$i] = empty($records_before[$i]) ? array() : $records_before[$i];
            $records_takegoods_timeout_order[$i] = empty($records_takegoods_timeout_order[$i]) ? array() : $records_takegoods_timeout_order[$i];
            $records_service_timeout_order[$i] = empty($records_service_timeout_order[$i]) ? array() : $records_service_timeout_order[$i];
            $records_comment[$i] = empty($records_comment[$i]) ? array() : $records_comment[$i];
            $records_paytype_delivery[$i] = empty($records_paytype_delivery[$i]) ? array() : $records_paytype_delivery[$i];
            $data = array_merge($basic, $records_temp[$i], $records_normal[$i], $records_timeout[$i], $records_before[$i], $records_comment[$i], $records_takegoods_timeout_order[$i], $records_service_timeout_order[$i], $records_paytype_delivery[$i]);
            if (!empty($data["total_success_order"])) {
                $data["percent_normal"] = round(($data["total_success_order"] - $data["total_timeout_order"]) / $data["total_success_order"], 2) * 100;
                $data["percent_timeout"] = round($data["total_timeout_order"] / $data["total_success_order"], 2) * 100;
                $data["total_percent_timeout"] = round($data["total_timeout_order"] / $stat["total_timeout_order"], 4) * 100;
            }
            $data["title"] = $deliveryer["title"];
            $records[] = $data;
        }
        $records = array_sort($records, "total_success_order", SORT_DESC);
        unset($deliveryers);
        include itemplate("statcenter/deliveryDay");
    }
}

?>