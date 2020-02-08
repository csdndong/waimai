<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$deliveryer = $_W["we7_wmall"]["deliveryer"]["user"];
$config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
$config_takeout["delivery_timeout_limit"] = intval($config_takeout["delivery_timeout_limit"]);
if (empty($config_takeout["delivery_timeout_limit"])) {
    $config_takeout["delivery_timeout_limit"] = 45;
}
if ($op == "index") {
    $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer["id"]);
    $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "today";
    if ($type == "today") {
        $condition = " stat_day = :stat_day";
        $params[":stat_day"] = date("Ymd");
        $starttime = strtotime(date("Y-m-d"));
        $endtime = $starttime + 86399;
    } else {
        if ($type == "week") {
            $condition = " stat_day >= :start_day and stat_day <= :end_day";
            $params[":start_day"] = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - date("w"), date("Y")));
            $params[":end_day"] = date("Ymd", mktime(23, 59, 59, date("m"), date("d") - date("w") + 6, date("Y")));
            $starttime = strtotime($params[":start_day"]);
            $endtime = strtotime($params[":end_day"]) + 86399;
        } else {
            if ($type == "month") {
                $condition = " stat_month = :stat_month";
                $params[":stat_month"] = date("Y") . date("m");
                $starttime = mktime(0, 0, 0, date("m"), 1, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("t"), date("Y"));
            } else {
                if ($type == "custom") {
                    $start = trim($_GPC["start"]);
                    $end = trim($_GPC["end"]);
                    if (empty($start) || empty($end)) {
                        message(ierror(-1, "请选择日期"), "", "ajax");
                    }
                    $starttime = strtotime($start);
                    $endtime = strtotime($end) + 86399;
                    $condition = " stat_day >= :start_day and stat_day <= :end_day";
                    $params[":start_day"] = date("Ymd", $starttime);
                    $params[":end_day"] = date("Ymd", $endtime);
                }
            }
        }
    }
    $stat = array("takeout" => array("num" => 0, "fee" => 0), "errander" => array("num" => 0, "fee" => 0), "total" => array("num" => 0, "fee" => 0), "time" => array("start" => date("Y-m-d H:i", $starttime), "end" => date("Y-m-d H:i", $endtime)));
    $stat["takeout"]["num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status = 5 and " . $condition, $params));
    $stat["takeout"]["fee"] = floatval(pdo_fetchcolumn("select sum(plateform_deliveryer_fee) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status = 5 and " . $condition, $params));
    $stat["errander"]["num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status = 3 and " . $condition, $params));
    $stat["errander"]["fee"] = floatval(pdo_fetchcolumn("select sum(deliveryer_total_fee) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status = 3 and " . $condition, $params));
    message(ierror(0, "", $stat), "", "ajax");
} else {
    if ($op == "rank_takeout") {
        $condition = " where uniacid = :uniacid and agentid = :agentid and status = 5 and delivery_type = 2 and deliveryer_id != 0 and order_type <= 2";
        $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
        $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "today";
        if ($type == "today") {
            $condition .= " and stat_day = :today";
            $params[":today"] = date("Ymd");
        } else {
            if ($type == "yesterday") {
                $condition .= " and stat_day = :yesterday";
                $params[":yesterday"] = date("Ymd", strtotime("-1 day"));
            } else {
                if ($type == "week") {
                    $condition .= " and stat_week = :week";
                    $params[":week"] = date("w");
                } else {
                    if ($type == "month") {
                        $condition .= " and stat_month = :month";
                        $params[":month"] = date("Ym");
                    } else {
                        if ($type == "last_month") {
                            $condition .= " and stat_month = :last_month";
                            $params[":last_month"] = date("Ym", strtotime("last month"));
                        } else {
                            if ($type == "custom") {
                                $start = trim($_GPC["start"]);
                                $end = trim($_GPC["end"]);
                                if (empty($start) || empty($end)) {
                                    message(ierror(-1, "请选择日期"), "", "ajax");
                                }
                                $starttime = strtotime($start);
                                $endtime = strtotime($end) + 86399;
                                $condition .= " and endtime >= :starttime and endtime <= :endtime";
                                $params[":starttime"] = $starttime;
                                $params[":endtime"] = $endtime;
                            }
                        }
                    }
                }
            }
        }
        $records_temp = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tdeliveryer_id,\r\n\t\tcount(*) as total_success_order,\r\n\t\tround(avg(delivery_success_time - clerk_notify_collect_time) / 60, 2) as avg_delivery_success_time\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition . " group by deliveryer_id", $params, "deliveryer_id");
        $condition_timeout = (string) $condition . " and (endtime - clerk_notify_collect_time > " . $config_takeout["delivery_timeout_limit"] . " * 60)";
        $records_timeout = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tdeliveryer_id,\r\n\t\tcount(*) as total_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_timeout . " group by deliveryer_id", $params, "deliveryer_id");
        $basic = array("total_success_order" => "0", "total_timeout_order" => "0", "percent_timeout" => "0", "percent_timeout_cn" => "0%", "percent_normal" => "0", "percent_normal_cn" => "0%", "avg_delivery_success_time" => "0");
        $records = array();
        $deliveryers = deliveryer_fetchall(0, array("work_status" => -1, "over_max_collect_show" => 1));
        foreach ($deliveryers as $item) {
            $i = $item["id"];
            $records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
            $records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
            $data = array_merge($basic, $records_timeout[$i], $records_temp[$i]);
            if (!empty($data["total_success_order"])) {
                $data["percent_timeout"] = round($data["total_timeout_order"] / $data["total_success_order"], 2) * 100;
                $data["percent_timeout"] = (string) $data["percent_timeout"];
                $data["percent_timeout_cn"] = (string) $data["percent_timeout"] . "%";
                $data["percent_normal"] = round(($data["total_success_order"] - $data["total_timeout_order"]) / $data["total_success_order"], 2) * 100;
                $data["percent_normal"] = (string) $data["percent_normal"];
                $data["percent_normal_cn"] = (string) $data["percent_normal"] . "%";
            }
            $data["title"] = $item["title"];
            $data["avatar"] = tomedia($item["avatar"]);
            if ($i == $deliveryer["id"]) {
                $records_mine = $data;
            }
            $records[] = $data;
        }
        $sort_type = trim($_GPC["sort_type"]) ? trim($_GPC["sort_type"]) : "total_success_order";
        $orderby = SORT_DESC;
        if ($sort_type == "avg_delivery_success_time") {
            $orderby = SORT_ASC;
            foreach ($records as $key => $item) {
                if (empty($item["avg_delivery_success_time"])) {
                    $record_no_success_time[] = $item;
                    unset($records[$key]);
                }
            }
        }
        $records = array_sort($records, $sort_type, $orderby);
        if ($sort_type == "avg_delivery_success_time") {
            $records = array_merge($records, $record_no_success_time);
        }
        $result["mine"] = $records_mine;
        $result["rank"] = $records;
        $rank = 0;
        $result["mine"]["ranking"] = $rank;
        foreach ($result["rank"] as $val) {
            $rank++;
            if ($val["deliveryer_id"] == $deliveryer["id"]) {
                $result["mine"]["ranking"] = $rank;
            }
        }
        message(ierror(0, "", $result), "", "ajax");
        return 1;
    } else {
        if ($op == "rank_errander") {
            $config_errander["delivery_timeout_limit"] = intval($config_errander["delivery_timeout_limit"]);
            if (empty($config_errander["delivery_timeout_limit"])) {
                $config_errander["delivery_timeout_limit"] = 45;
            }
            $condition = " where uniacid = :uniacid and agentid = :agentid and status = 3 and deliveryer_id != 0";
            $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
            $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "today";
            if ($type == "today") {
                $condition .= " and stat_day = :today";
                $params[":today"] = date("Ymd");
            } else {
                if ($type == "yesterday") {
                    $condition .= " and stat_day = :yesterday";
                    $params[":yesterday"] = date("Ymd", strtotime("-1 day"));
                } else {
                    if ($type == "week") {
                        $condition .= " and stat_week = :week";
                        $params[":week"] = date("w");
                    } else {
                        if ($type == "month") {
                            $condition .= " and stat_month = :month";
                            $params[":month"] = date("Ym");
                        } else {
                            if ($type == "last_month") {
                                $condition .= " and stat_month = :last_month";
                                $params[":last_month"] = date("Ym", strtotime("last month"));
                            } else {
                                if ($type == "custom") {
                                    $start = trim($_GPC["start"]);
                                    $end = trim($_GPC["end"]);
                                    if (empty($start) || empty($end)) {
                                        message(ierror(-1, "请选择日期"), "", "ajax");
                                    }
                                    $starttime = strtotime($start);
                                    $endtime = strtotime($end) + 86399;
                                    $condition .= " and endtime >= :starttime and endtime <= :endtime";
                                    $params[":starttime"] = $starttime;
                                    $params[":endtime"] = $endtime;
                                }
                            }
                        }
                    }
                }
            }
            $records_temp = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tdeliveryer_id,\r\n\t\tcount(*) as total_success_order,\r\n\t\tround(avg(delivery_success_time - delivery_assign_time) / 60, 2) as avg_delivery_success_time\r\n\t FROM " . tablename("tiny_wmall_errander_order") . " " . $condition . " group by deliveryer_id", $params, "deliveryer_id");
            $condition_timeout = (string) $condition . " and (delivery_success_time - delivery_assign_time > " . $config_errander["delivery_timeout_limit"] . " * 60)";
            $records_timeout = pdo_fetchall("SELECT\r\n\t\tstat_day,\r\n\t\tdeliveryer_id,\r\n\t\tcount(*) as total_timeout_order\r\n\t FROM " . tablename("tiny_wmall_errander_order") . " " . $condition_timeout . " group by deliveryer_id", $params, "deliveryer_id");
            $basic = array("total_success_order" => "0", "total_timeout_order" => "0", "percent_timeout" => "0", "percent_timeout_cn" => "0%", "percent_normal" => "0", "percent_normal_cn" => "0%", "avg_delivery_success_time" => "0");
            $records = array();
            $deliveryers = deliveryer_fetchall(0, array("work_status" => -1, "order_type" => "is_errander", "over_max_collect_show" => 1));
            foreach ($deliveryers as $item) {
                $i = $item["id"];
                $records_temp[$i] = empty($records_temp[$i]) ? array() : $records_temp[$i];
                $records_timeout[$i] = empty($records_timeout[$i]) ? array() : $records_timeout[$i];
                $data = array_merge($basic, $records_timeout[$i], $records_temp[$i]);
                if (!empty($data["total_success_order"])) {
                    $data["percent_timeout"] = round($data["total_timeout_order"] / $data["total_success_order"], 2) * 100;
                    $data["percent_timeout"] = (string) $data["percent_timeout"];
                    $data["percent_timeout_cn"] = (string) $data["percent_timeout"] . "%";
                    $data["percent_normal"] = round(($data["total_success_order"] - $data["total_timeout_order"]) / $data["total_success_order"], 2) * 100;
                    $data["percent_normal"] = (string) $data["percent_normal"];
                    $data["percent_normal_cn"] = (string) $data["percent_normal"] . "%";
                }
                $data["title"] = $item["title"];
                $data["avatar"] = tomedia($item["avatar"]);
                if ($i == $deliveryer["id"]) {
                    $records_mine = $data;
                }
                $records[] = $data;
            }
            $sort_type = trim($_GPC["sort_type"]) ? trim($_GPC["sort_type"]) : "total_success_order";
            $orderby = SORT_DESC;
            if ($sort_type == "avg_delivery_success_time") {
                $orderby = SORT_ASC;
                foreach ($records as $key => $item) {
                    if (empty($item["avg_delivery_success_time"])) {
                        $record_no_success_time[] = $item;
                        unset($records[$key]);
                    }
                }
            }
            $records = array_sort($records, $sort_type, $orderby);
            if ($sort_type == "avg_delivery_success_time") {
                $records = array_merge($records, $record_no_success_time);
            }
            $result["mine"] = $records_mine;
            $result["rank"] = $records;
            $rank = 0;
            $result["mine"]["ranking"] = $rank;
            foreach ($result["rank"] as $val) {
                $rank++;
                if ($val["deliveryer_id"] == $deliveryer["id"]) {
                    $result["mine"]["ranking"] = $rank;
                }
            }
            message(ierror(0, "", $result), "", "ajax");
            return 1;
        } else {
            if ($op == "mine") {
                $condition = " where uniacid = :uniacid and agentid = :agentid and status = 5 and delivery_type = 2 and deliveryer_id != 0 and order_type <= 2";
                $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
                $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "yesterday";
                if ($type == "yesterday") {
                    $condition .= " and stat_day = :yesterday";
                    $params[":yesterday"] = date("Ymd", strtotime("-1 day"));
                } else {
                    if ($type == "month") {
                        $condition .= " and stat_month = :month";
                        $params[":month"] = date("Ym");
                    } else {
                        if ($type == "last_month") {
                            $condition .= " and stat_month = :month";
                            $params[":month"] = date("Ym", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
                        }
                    }
                }
                $records = array();
                $records_temp = pdo_fetchall("SELECT\r\n\t\tdeliveryer_id,\r\n\t\tcount(*) as total_success_order,\r\n\t\tround(avg(delivery_success_time - clerk_notify_collect_time) / 60, 2) as avg_delivery_success_time\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition . " group by deliveryer_id", $params, "deliveryer_id");
                $records["avg_delivery_success_time"]["value"] = empty($records_temp[$deliveryer["id"]]["avg_delivery_success_time"]) ? "0" : $records_temp[$deliveryer["id"]]["avg_delivery_success_time"];
                $records["total_success_order"]["value"] = empty($records_temp[$deliveryer["id"]]["total_success_order"]) ? "0" : $records_temp[$deliveryer["id"]]["total_success_order"];
                $records_temp = array_sort($records_temp, "total_success_order", SORT_DESC);
                $rank = 0;
                $records["total_success_order"]["ranking"] = 0;
                foreach ($records_temp as $val) {
                    $rank++;
                    if ($val["deliveryer_id"] == $deliveryer["id"]) {
                        $records["total_success_order"]["ranking"] = $rank;
                    }
                }
                $records_temp = array_sort($records_temp, "avg_delivery_success_time", SORT_ASC);
                $rank = 0;
                $records["avg_delivery_success_time"]["ranking"] = 0;
                foreach ($records_temp as $val) {
                    $rank++;
                    if ($val["deliveryer_id"] == $deliveryer["id"]) {
                        $records["avg_delivery_success_time"]["ranking"] = $rank;
                    }
                }
                $condition_timeout = (string) $condition . " and (endtime - clerk_notify_collect_time > " . $config_takeout["delivery_timeout_limit"] . " * 60)";
                $records_timeout = pdo_fetchall("SELECT\r\n\t\tdeliveryer_id,\r\n\t\tcount(*) as total_timeout_order\r\n\t FROM " . tablename("tiny_wmall_order") . " " . $condition_timeout . " group by deliveryer_id", $params, "deliveryer_id");
                $records["total_timeout_order"]["value"] = empty($records_timeout["total_timeout_order"]) ? 0 : $records_timeout["total_timeout_order"];
                foreach ($records_timeout as $val) {
                    $val["percent_timeout"] = round($val["total_timeout_order"] / $records_temp[$val["deliveryer_id"]]["total_success_order"], 2) * 100;
                    $val["percent_timeout"] = (string) $val["percent_timeout"] . "%";
                    $val["percent_normal"] = round($records_temp[$val["deliveryer_id"]]["total_success_order"] - $val["total_timeout_order"] / $records_temp[$val["deliveryer_id"]]["total_success_order"], 2) * 100;
                    $val["percent_normal"] = (string) $val["percent_normal"] . "%";
                    $records_percent[$val["deliveryer_id"]] = array("percent_timeout" => $val["percent_timeout"], "percent_normal" => $val["percent_normal"]);
                }
                $records["percent_timeout"]["value"] = empty($records_percent[$deliveryer["id"]]["percent_timeout"]) ? 0 : $records_percent[$deliveryer["id"]]["percent_timeout"];
                $records["percent_normal"]["value"] = empty($records_percent[$deliveryer["id"]]["percent_normal"]) ? 0 : $records_percent[$deliveryer["id"]]["percent_normal"];
                $records["percent_normal"]["ranking"] = 0;
                if (!empty($records["total_success_order"]["value"]) && empty($records["total_timeout_order"]["value"])) {
                    $records["percent_normal"]["value"] = 100;
                    $records["percent_normal"]["ranking"] = 1;
                }
                if ($records["percent_normal"]["ranking"] == 0) {
                    $records_percent = array_sort($records_percent, "percent_normal", SORT_DESC);
                    $rank = 0;
                    foreach ($records_percent as $val) {
                        $rank++;
                        if ($val["deliveryer_id"] == $deliveryer["id"]) {
                            $records["percent_normal"]["ranking"] = $rank;
                        }
                    }
                }
                message(ierror(0, "", $records), "", "ajax");
            }
        }
    }
}

?>