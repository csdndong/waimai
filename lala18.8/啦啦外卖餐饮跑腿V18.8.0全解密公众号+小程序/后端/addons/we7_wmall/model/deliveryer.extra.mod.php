<?php


defined("IN_IA") or exit("Access Denied");
function deliveryer_getcash_update($logOrId, $type, $extra = array())
{
    global $_W;
    $log = $logOrId;
    if (!is_array($log)) {
        $log = pdo_get("tiny_wmall_deliveryer_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $log));
    }
    if (empty($log)) {
        return error(-1, "提现记录不存在");
    }
    if ($type == "transfers") {
        $log["account"] = iunserializer($log["account"]);
        if (!is_array($log["account"])) {
            $log["account"] = array();
        }
        if ($log["status"] == 1) {
            return error(-1, "该提现记录已处理");
        }
        $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $log["deliveryer_id"]));
        if ($log["channel"] == "weixin" || $log["channel"] == "wxapp") {
            mload()->classs("wxpay");
            if ($log["channel"] == "wxapp") {
                if (empty($log["account"]["openid_wxapp"])) {
                    return error(-1, "模块版本为小程序版。未获取到配送员针对小程序的openid");
                }
                $pay = new WxPay("wxapp");
            } else {
                $pay = new WxPay();
            }
            $params = array("partner_trade_no" => $log["trade_no"], "openid" => $log["channel"] == "wxapp" ? $log["account"]["openid_wxapp"] : $log["account"]["openid"], "check_name" => "FORCE_CHECK", "re_user_name" => $log["account"]["realname"], "amount" => $log["final_fee"] * 100, "desc" => (string) $deliveryer["title"] . date("Y-m-d H:i", $log["addtime"]) . "提现申请");
            $response = $pay->mktTransfers($params);
        } else {
            if ($log["channel"] == "alipay") {
                mload()->classs("alipay");
                $pay = new AliPay();
                $params = array("out_biz_no" => $log["trade_no"], "payee_account" => $log["account"]["account"], "amount" => $log["final_fee"], "payee_real_name" => $log["account"]["realname"], "remark" => (string) $deliveryer["title"] . date("Y-m-d H:i", $log["addtime"]) . "提现申请");
                $response = $pay->transfer($params);
            } else {
                if ($log["channel"] == "bank") {
                    mload()->classs("wxpay");
                    $pay = new WxPay();
                    $params = array("partner_trade_no" => $log["trade_no"], "enc_bank_no" => $log["account"]["account"], "enc_true_name" => $log["account"]["realname"], "bank_code" => $log["account"]["id"], "amount" => $log["final_fee"] * 100, "desc" => (string) $deliveryer["title"] . date("Y-m-d H:i", $log["addtime"]) . "提现申请");
                    $response = $pay->mktPayBank($params);
                }
            }
        }
        if (is_error($response)) {
            mlog(4005, $log["id"], "打款未成功，详细错误信息:" . $response["message"]);
            return error(-1, "打款未成功，等待管理员处理。详细错误信息：" . $response["message"]);
        }
        $update = array("status" => 1, "endtime" => TIMESTAMP, "toaccount_status" => 1);
        if ($log["channel"] == "weixin" || $log["channel"] == "wxapp" || $log["channel"] == "alipay") {
            $update["toaccount_status"] = 2;
        }
        pdo_update("tiny_wmall_deliveryer_getcash_log", $update, array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_deliveryer_getcash($log["deliveryer_id"], $log["id"], "success");
        mlog(4005, $log["id"], "打款成功");
        return error(0, "打款成功");
    }
    if ($type == "cancel") {
        if ($log["status"] == 1 && $log["toaccount_status"] == 2) {
            return error(-1, "本次提现已成功,无法撤销");
        }
        if ($log["status"] == 3) {
            return error(-1, "本次提现已撤销");
        }
        $remark = trim($extra["remark"]);
        mload()->model("deliveryer");
        deliveryer_update_credit2($log["deliveryer_id"], $log["get_fee"], 3, "", $remark, "");
        pdo_update("tiny_wmall_deliveryer_getcash_log", array("status" => 3, "endtime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_deliveryer_getcash($log["deliveryer_id"], $log["id"], "cancel", $remark);
        mlog(4004, $log["id"], $remark);
        return error(0, "提现撤销成功");
    }
    if ($type == "status") {
        $status = intval($extra["status"]);
        if ($log["status"] == $status) {
            return error(-1, "该提现记录已处理");
        }
        $update = array("status" => $status, "endtime" => TIMESTAMP);
        if ($status == 1) {
            $update["toaccount_status"] = 2;
        }
        pdo_update("tiny_wmall_deliveryer_getcash_log", $update, array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_deliveryer_getcash($log["deliveryer_id"], $log["id"], "success");
        mlog(4006, $log["id"]);
        return error(0, "设置提现状态成功");
    }
    if ($type == "query") {
        if ($log["status"] == 2) {
            return error(-1, "该提现正在申请中，请等待管理员审核");
        }
        if ($log["status"] == 3) {
            return error(-1, "该提现申请已撤销");
        }
        if ($log["channel"] != "bank" || $log["toaccount_status"] == 2) {
        return error(0, "该提现已成功到账");
        }
        if ($log["toaccount_status"] == 3) {
            return error(-1, "该提现已失败，请联系管理员处理");
        }
        $params = array("partner_trade_no" => $log["trade_no"]);
        mload()->classs("wxpay");
        $pay = new WxPay();
        $response = $pay->mktQueryBank($params);
        if (is_error($response)) {
            return $response;
        }
        $result = $response["message"];
        if (in_array($result["status"], array("SUCCESS", "FAILED", "BANK_FAIL"))) {
            pdo_update("tiny_wmall_deliveryer_getcash_log", array("toaccount_status" => $result["toaccount_status"]), array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        }
        return error($result["errno"], $result["msg"]);
    }
}
function deliveryer_stat_order1($type = "takeout")
{
    global $_W;
    global $_GPC;
    $condition = " where uniacid = :uniacid and deliveryer_id = :deliveryer_id";
    $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_W["deliveryer"]["id"]);
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : 0;
    if ($days == -1) {
        $stat_day = json_decode(htmlspecialchars_decode($_GPC["stat_day"]), true);
        if (empty($stat_day["start"]) || empty($stat_day["end"])) {
            return error(-1, "请选择日期");
        }
        $starttime = str_replace("-", "", trim($stat_day["start"]));
        $endtime = str_replace("-", "", trim($stat_day["end"]));
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
    $init_stat = array("total_fee" => 0, "success_order" => 0);
    $stat = array();
    if ($type == "takeout") {
        $init_stat["total_paytype_delivery"] = 0;
        $condition_takeout = (string) $condition . " and delivery_type = 2 and status = 5";
        $data = pdo_fetchall("select plateform_deliveryer_fee, final_fee, pay_type from " . tablename("tiny_wmall_order") . $condition_takeout, $params);
        $stat["takeout"] = $init_stat;
    } else {
        if ($type == "errander") {
            $data = pdo_fetchall("select deliveryer_total_fee from " . tablename("tiny_wmall_errander_order") . $condition . " and status = 3", $params);
            $stat["errander"] = $init_stat;
        }
    }
    if (!empty($data)) {
        foreach ($data as $val) {
            $stat[$type]["success_order"]++;
            $stat[$type]["total_fee"] += $type == "takeout" ? $val["plateform_deliveryer_fee"] : $val["deliveryer_total_fee"];
            if ($val["pay_type"] == "delivery") {
                $stat[$type]["total_paytype_delivery"] += $val["final_fee"];
            }
        }
    }
    return $stat;
}
function deliveryer_stat_detail($type = "takeout")
{
    global $_W;
    global $_GPC;
    $condition = " where uniacid = :uniacid and deliveryer_id = :deliveryer_id";
    $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_W["deliveryer"]["id"]);
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : 0;
    if ($days == -1) {
        $stat_day = json_decode(htmlspecialchars_decode($_GPC["stat_day"]), true);
        if (empty($stat_day["start"]) || empty($stat_day["end"])) {
            return error(-1, "请选择日期");
        }
        $starttime = str_replace("-", "", trim($stat_day["start"]));
        $endtime = str_replace("-", "", trim($stat_day["end"]));
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
    if ($type == "takeout") {
        $records_sql = pdo_fetchall("SELECT stat_day, plateform_deliveryer_fee, final_fee, pay_type FROM " . tablename("tiny_wmall_order") . $condition . " and status = 5 and delivery_type = 2 ", $params);
        $records_temp = array();
        if (!empty($records_sql)) {
            foreach ($records_sql as $val) {
                if (empty($records_temp[$val["stat_day"]])) {
                    $records_temp[$val["stat_day"]] = array("stat_day" => $val["stat_day"], "total_fee" => 0, "total_success_order" => 0, "total_paytype_delivery" => 0);
                }
                $records_temp[$val["stat_day"]]["total_fee"] += $val["plateform_deliveryer_fee"];
                $records_temp[$val["stat_day"]]["total_success_order"]++;
                if ($val["pay_type"] == "delivery") {
                    $records_temp[$val["stat_day"]]["total_paytype_delivery"] += $val["final_fee"];
                }
            }
        }
    } else {
        if ($type == "errander") {
            $records_temp = pdo_fetchall("SELECT stat_day, count(*) as total_success_order, round(sum(deliveryer_total_fee), 2) as total_fee FROM " . tablename("tiny_wmall_errander_order") . $condition . " and status = 3 group by stat_day", $params, "stat_day");
        }
    }
    $records = array();
    $i = $endtime;
    while ($starttime <= $i) {
        if (empty($records_temp[$i])) {
            $records[] = array("stat_day" => $i, "total_fee" => 0, "total_success_order" => 0, "total_paytype_delivery" => 0);
        } else {
            $records[] = $records_temp[$i];
        }
        $i = date("Ymd", strtotime($i) - 86400);
    }
    return $records;
}
function deliveryer_stat_order()
{
    global $_W;
    global $_GPC;
    $type = trim($_GPC["type"]);
    if (empty($type)) {
        return error(-1, "请选择统计日期");
    }
    $condition = " where uniacid = :uniacid and deliveryer_id = :deliveryer_id";
    $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_W["deliveryer"]["id"]);
    if ($type == "today") {
        $condition .= " and stat_day = :stat_day";
        $params[":stat_day"] = date("Ymd");
        $starttime = date("Y-m-d 00:00");
        $endtime = date("Y-m-d 23:59");
    } else {
        if ($type == "yesterday") {
            $condition .= " and stat_day = :stat_day";
            $params[":stat_day"] = date("Ymd", strtotime("-1 day"));
            $starttime = date("Y-m-d 00:00", strtotime("-1 day"));
            $endtime = date("Y-m-d 23:59", strtotime("-1 day"));
        } else {
            if ($type == "month") {
                $condition .= " and stat_month = :stat_month";
                $params[":stat_month"] = date("Ym");
                $starttime = date("Y-m-d H:i", mktime(0, 0, 0, date("m"), 1, date("Y")));
                $endtime = date("Y-m-d H:i", mktime(23, 59, 59, date("m"), date("t"), date("Y")));
            } else {
                if ($type == "last_month") {
                    $condition .= " and stat_month = :stat_month";
                    $params[":stat_month"] = date("Ym", strtotime("last month"));
                    $starttime = date("Y-m-01 00:00", strtotime("-1 month"));
                    $endtime = date("Y-m-d 23:59", strtotime(0 - date("d") . "day"));
                } else {
                    if ($type == "week") {
                        $condition = " stat_day >= :start_day and stat_day <= :end_day";
                        $params[":start_day"] = date("Y") . date("m") . (date("d") - date("w"));
                        $params[":end_day"] = date("Y") . date("m") . (date("d") - date("w") + 6);
                        $starttime = date("Y-m-d 00:00", strtotime($params[":start_day"]));
                        $endtime = date("Y-m-d 23:59", strtotime($params[":end_day"]));
                    } else {
                        if ($type == "custom") {
                            $start = trim($_GPC["start"]);
                            $end = trim($_GPC["end"]);
                            if (empty($start) || empty($end)) {
                                return error(-1, "请选择日期");
                            }
                            $starttime = strtotime($start);
                            $endtime = strtotime($end) + 86399;
                            $condition = " stat_day >= :start_day and stat_day <= :end_day";
                            $params[":start_day"] = date("Ymd", $starttime);
                            $params[":end_day"] = date("Ymd", $endtime);
                            $starttime = date("Y-m-d 00:00", strtotime($start));
                            $endtime = date("Y-m-d 23:59", strtotime($end));
                        }
                    }
                }
            }
        }
    }
    $condition_takeout = (string) $condition . " and delivery_type = 2";
    $takeout = pdo_fetchall("select plateform_deliveryer_fee,status from " . tablename("tiny_wmall_order") . $condition_takeout, $params);
    $errander = pdo_fetchall("select deliveryer_total_fee, status from " . tablename("tiny_wmall_errander_order") . $condition, $params);
    $stat = array("takeout" => array("total_num" => 0, "fee" => 0, "num" => 0, "cancel_num" => 0), "errander" => array("total_num" => 0, "fee" => 0, "num" => 0, "cancel_num" => 0), "total" => array("num" => 0, "fee" => 0), "time" => array("start" => $starttime, "end" => $endtime));
    foreach ($takeout as $val) {
        $stat["takeout"]["total_num"]++;
        if ($val["status"] == 5) {
            $stat["takeout"]["num"]++;
            $stat["takeout"]["fee"] += $val["plateform_deliveryer_fee"];
        } else {
            if ($val["status"] == 6) {
                $stat["takeout"]["cancel_num"]++;
            }
        }
    }
    foreach ($errander as $val) {
        $stat["errander"]["total_num"]++;
        if ($val["status"] == 3) {
            $stat["errander"]["num"]++;
            $stat["errander"]["fee"] += $val["deliveryer_total_fee"];
        } else {
            if ($val["status"] == 4) {
                $stat["errander"]["cancel_num"]++;
            }
        }
    }
    return $stat;
}
function deliveryer_takeout_rank($extra)
{
    global $_W;
    global $_GPC;
    if (!is_array($extra) || empty($extra["type"])) {
        return error(-1, "请选择统计时间");
    }
    $config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
    $config_takeout["delivery_timeout_limit"] = intval($config_takeout["delivery_timeout_limit"]);
    if (empty($config_takeout["delivery_timeout_limit"])) {
        $config_takeout["delivery_timeout_limit"] = 45;
    }
    $type = trim($extra["type"]);
    $deliveryer_id = intval($extra["deliveryer_id"]);
    $condition = " where uniacid = :uniacid and agentid = :agentid and status = 5 and delivery_type = 2 and deliveryer_id != 0 and order_type <= 2";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    if ($type == "today") {
        $condition .= " and stat_day = :today";
        $params[":today"] = date("Ymd");
    } else {
        if ($type == "yesterday") {
            $condition .= " and stat_day = :yesterday";
            $params[":yesterday"] = date("Ymd", strtotime("-1 day"));
        } else {
            if ($type == "week") {
                $condition = " stat_day >= :start_day and stat_day <= :end_day";
                $params[":start_day"] = date("Y") . date("m") . (date("d") - date("w"));
                $params[":end_day"] = date("Y") . date("m") . (date("d") - date("w") + 6);
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
        if ($i == $deliveryer_id) {
            $records_mine = $data;
        }
        $records[] = $data;
    }
    $sort_type = $extra["sort_type"];
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
        if ($val["deliveryer_id"] == $deliveryer_id) {
            $result["mine"]["ranking"] = $rank;
        }
    }
    return $result;
}
function deliveryer_errander_rank($extra)
{
    global $_W;
    global $_GPC;
    if (!is_array($extra) || empty($extra["type"])) {
        return error(-1, "请选择统计时间");
    }
    $config_errander = get_plugin_config("errander");
    $config_errander["delivery_timeout_limit"] = intval($config_errander["delivery_timeout_limit"]);
    if (empty($config_errander["delivery_timeout_limit"])) {
        $config_errander["delivery_timeout_limit"] = 45;
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid and status = 3 and deliveryer_id != 0";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $deliveryer_id = $extra["deliveryer_id"];
    $type = trim($extra["type"]);
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
        if ($i == $deliveryer_id) {
            $records_mine = $data;
        }
        $records[] = $data;
    }
    $sort_type = trim($extra["sort_type"]);
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
        if ($val["deliveryer_id"] == $deliveryer_id) {
            $result["mine"]["ranking"] = $rank;
        }
    }
    return $result;
}
function deliveryer_get_location($filter = array())
{
    global $_W;
    $condition = " where uniacid = :uniacid and status = :status";
    $params = array(":uniacid" => $_W["uniacid"], ":status" => 1);
    $ids = $filter["ids"];
    if (isset($filter["agentid"]) && empty($ids)) {
        $condition .= " and agentid = :agentid ";
        $params[":agentid"] = intval($filter["agentid"]);
    }
    if ($filter["order_type"]) {
        $condition .= " and " . $filter["order_type"] . " = 1";
    }
    if (!isset($filter["work_status"])) {
        $filter["work_status"] = 1;
    } else {
        if ($filter["work_status"] == -1) {
            unset($filter["work_status"]);
        }
    }
    if (isset($filter["work_status"])) {
        $condition .= " and work_status = :work_status";
        $params[":work_status"] = $filter["work_status"];
    }
    if (!empty($ids)) {
        $ids_str = implode(",", $ids);
        $condition .= " and id in (" . $ids_str . ")";
    }
    $deliveryers = pdo_fetchall("select id,mobile,title,location_x,location_y,order_takeout_num,order_errander_num from " . tablename("tiny_wmall_deliveryer") . $condition, $params);
    $stat_day = date("Ymd");
    $def_starttime = strtotime("00:00");
    $def_endtime = strtotime("05:00");
    if ($def_starttime <= TIMESTAMP && TIMESTAMP <= $def_endtime) {
        $stat_day = date("Ymd", strtotime("-1 day"));
    }
    if (!empty($deliveryers)) {
        foreach ($deliveryers as &$row) {
            $row["wait_pickup"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and (delivery_status = 7 or delivery_status = 8)", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $row["id"], ":stat_day" => $stat_day));
            $row["wait_delivery"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and delivery_status = 4", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $row["id"], ":stat_day" => $stat_day));
            $row["work_status"] = 1;
            $addtime = pdo_fetchcolumn("select addtime from " . tablename("tiny_wmall_deliveryer_location_log") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id order by id desc limit 1", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $row["id"]));
            if (300 < TIMESTAMP - $addtime) {
                $row["work_status"] = 2;
            }
            $row["css"] = "";
            if ($row["work_status"] == 2) {
                $row["css"] = "off-line";
            } else {
                if (($filter["order_type"] == "is_takeout" || empty($filter["order_type"])) && $row["work_status"] == 1 && empty($row["wait_pickup"]) && empty($row["wait_delivery"])) {
                    $row["css"] = "active";
                } else {
                    if ($filter["order_type"] == "is_errander" && empty($row["order_takeout_num"]) && empty($row["order_errander_num"])) {
                        $row["css"] = "active";
                    }
                }
            }
        }
    }
    return $deliveryers;
}
function deliveryer_vue_tabs($type = "takeout")
{
    global $_W;
    global $_GPC;
    if (empty($_W["deliveryer"])) {
        return array();
    }
    $can_collect_order = 1;
    if ($type == "takeout") {
        $config_takeout = $_W["we7_wmall"]["config"]["takeout"];
        if ($config_takeout["order"]["dispatch_mode"] != 1 && !$config_takeout["order"]["can_collect_order"]) {
            $can_collect_order = 0;
        }
        $num3_condition = " WHERE uniacid = :uniacid and agentid = :agentid and " . $_W["deliveryer"]["work_status"] . " and delivery_status = :status";
        if ($_W["deliveryer"]["perm_takeout"] == 1) {
            $num3_condition .= " and " . $can_collect_order . " and delivery_type = 2";
        } else {
            if ($_W["deliveryer"]["perm_takeout"] == 2) {
                $num3_condition .= " and delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")";
            } else {
                $num3_condition .= " and (delivery_type = 2 or (delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")))";
            }
        }
        $num3 = pdo_fetchcolumn("select count(*) as num from " . tablename("tiny_wmall_order") . $num3_condition, array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":status" => 3));
        $num4 = pdo_fetchcolumn("select count(*) as num from " . tablename("tiny_wmall_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and delivery_status = 4", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_W["deliveryer"]["id"], ":transfer_deliveryer_id" => $_W["deliveryer"]["id"]));
        $num7 = pdo_fetchcolumn("select count(*) as num from " . tablename("tiny_wmall_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and (delivery_status = 7 or delivery_status = 8)", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_W["deliveryer"]["id"], ":transfer_deliveryer_id" => $_W["deliveryer"]["id"]));
        return $tabs = array(array("status" => 3, "status_cn" => "待抢", "num" => $num3), array("status" => 7, "status_cn" => "待取餐", "num" => $num7), array("status" => 4, "status_cn" => "配送中", "num" => $num4), array("status" => 5, "status_cn" => "已完成", "num" => 0));
    }
    if ($type == "errander") {
        $config_errander = get_plugin_config("errander");
        if ($config_errander["dispatch_mode"] != 1 && !$config_errander["can_collect_order"]) {
            $can_collect_order = 0;
        }
        $num1 = pdo_fetchcolumn("select  count(*) as num from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_category") . " as b on a.order_cid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.is_pay = 1 and a.status != 4 and a.delivery_status = 1 and " . $_W["deliveryer"]["work_status"] . " and " . $can_collect_order, array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
        $num2 = pdo_fetchcolumn("select count(*) as num from " . tablename("tiny_wmall_errander_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and is_pay = 1 and delivery_status = 2 and status != 4", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_W["deliveryer"]["id"], ":transfer_deliveryer_id" => $_W["deliveryer"]["id"]));
        $num3 = pdo_fetchcolumn("select count(*) as num from " . tablename("tiny_wmall_errander_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and is_pay = 1 and delivery_status = 3 and status != 4", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_W["deliveryer"]["id"], ":transfer_deliveryer_id" => $_W["deliveryer"]["id"]));
        return $tabs = array(array("status" => 1, "status_cn" => "待抢", "num" => $num1), array("status" => 2, "status_cn" => "待取餐", "num" => $num2), array("status" => 3, "status_cn" => "配送中", "num" => $num3), array("status" => 4, "status_cn" => "已完成", "num" => 0));
    }
}
function deliveryer_notice_stat($delivery_id = 0)
{
    global $_W;
    if (empty($delivery_id)) {
        $delivery_id = $_W["deliveryer"]["id"];
    }
    $new_id = pdo_fetchcolumn("SELECT notice_id FROM" . tablename("tiny_wmall_notice_read_log") . " WHERE uid = :uid ORDER BY notice_id DESC LIMIT 1", array(":uid" => $delivery_id));
    $new_id = intval($new_id);
    $notices = pdo_fetchall("SELECT id FROM " . tablename("tiny_wmall_notice") . " WHERE status = 1 AND type = :type AND id > :id", array(":type" => "delivery", ":id" => $new_id));
    if (!empty($notices)) {
        foreach ($notices as &$notice) {
            $insert = array("uid" => $delivery_id, "notice_id" => $notice["id"], "is_new" => 1);
            pdo_insert("tiny_wmall_notice_read_log", $insert);
        }
    }
    $total = intval(pdo_fetchcolumn("SELECT COUNT(*) FROM" . tablename("tiny_wmall_notice_read_log") . " WHERE uid = :uid AND is_new = 1", array(":uid" => $delivery_id)));
    return $total;
}

?>