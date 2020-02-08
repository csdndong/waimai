<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$deliveryer = $_W["we7_wmall"]["deliveryer"]["user"];
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
}

?>