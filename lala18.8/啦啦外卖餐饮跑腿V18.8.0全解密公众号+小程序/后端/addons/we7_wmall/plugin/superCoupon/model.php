<?php
defined("IN_IA") or exit("Access Denied");
function superCoupon_get_group_uids($id)
{
    global $_W;
    $group = pdo_get("tiny_wmall_supercoupon_member_group", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($group)) {
        return error(-1, "人群不存在");
    }
    $group["group_condition"] = iunserializer($group["group_condition"]);
    $member_limit = $group["group_condition"]["member_limit"];
    $condition = " where uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $group["sid"]);
    if ($group["group_condition"]["store_member_type"] < 2) {
        if ($group["group_condition"]["stat_day"] == -1) {
            $condition .= " and stat_day >= :start_day and stat_day <= :end_day";
            $params[":start_day"] = date("Ymd", strtotime($group["group_condition"]["stat_day"]["starttime"]));
            $params[":end_day"] = date("Ymd", strtotime($group["group_condition"]["stat_day"]["endtime"]));
        } else {
            $condition .= " and stat_day >= :start_day and stat_day <= :end_day";
            $params[":start_day"] = date("Ymd", strtotime("-" . $group["group_condition"]["stat_day"] . " days"));
            $params[":end_day"] = date("Ymd", strtotime("-1 days"));
        }
    } else {
        if ($group["group_condition"]["store_member_type"] == 2) {
            $condition .= " and stat_day >= :start_day and stat_day <= :end_day";
            $params[":start_day"] = date("Ymd", strtotime("-" . $member_limit["before_leave_days"] . " days"));
            $params[":end_day"] = date("Ymd", strtotime("-" . $member_limit["no_order_days"] . " days"));
        }
    }
    if ($group["group_condition"]["store_member_type"] == 1) {
        if ($member_limit["store_new_member_type"] == 1) {
            $condition .= " and (status = 6 or status = 5)";
            $orders = pdo_fetchall("select uid,status from " . tablename("tiny_wmall_order") . $condition, $params);
            $has_order = $cancel_order = array();
            foreach ($orders as $order) {
                if ($order["status"] == 5) {
                    $has_order[$order["uid"]] = $order["uid"];
                } else {
                    $cancel_order[$order["uid"]] = $order["uid"];
                }
            }
            $uids = array_diff($cancel_order, $has_order);
        } else {
            $footmark = pdo_fetchall("select uid from " . tablename("tiny_wmall_member_footmark") . $condition, $params, "uid");
            $orders = pdo_fetchall("select uid from " . tablename("tiny_wmall_order") . $condition, $params, "uid");
            $has_order_uids = array_keys($orders);
            $store_uids = array_keys($footmark);
            $uids = array_diff($store_uids, $has_order_uids);
        }
    } else {
        if ($group["group_condition"]["store_member_type"] == 3) {
            $uids = $member_limit["uids"];
        } else {
            $condition .= " and status = 5";
            if ($group["group_condition"]["store_member_type"] == 0) {
                if ($member_limit["is_comment"] != -1) {
                    $condition .= " and is_comment = " . $member_limit["is_comment"];
                }
                if ($member_limit["order_stat_date_type"] == 1) {
                    if ($member_limit["order_weekend"] == 1) {
                        $condition .= " and (stat_week = 0 or stat_week = 6)";
                    } else {
                        $condition .= " and stat_week >= 1 and stat_week <= 5";
                    }
                }
                if ($member_limit["order_stat_time_type"] == 1) {
                    $meals_cn_condition = "";
                    foreach ($member_limit["order_stat_time"] as $val) {
                        $meals_cn_condition .= "meals_cn = '" . $val . "' or ";
                    }
                    $meals_cn_condition = substr($meals_cn_condition, 0, strlen($meals_cn_condition) - 4);
                    $condition .= " and (" . $meals_cn_condition . ")";
                }
            }
            $select_fields = "uid, count(*) as total_num, sum(final_fee) as total_final_fee";
            $condition .= " group by uid";
            $orders = pdo_fetchall("select " . $select_fields . " from " . tablename("tiny_wmall_order") . $condition, $params, "uid");
            $uids_num = count($orders);
            if (!empty($member_limit["consume_num_type"]) || !empty($member_limit["consume_price_type"])) {
                $num_all = 0;
                $price_all = 0;
                foreach ($orders as $order) {
                    $num_all += $order["total_num"];
                    $price_all += $order["total_final_fee"];
                }
                $avg_price_all = round($price_all / $num_all, 2);
                $avg_consume_num_all = round($num_all / $uids_num, 2);
                foreach ($orders as $key => $order) {
                    if ($member_limit["consume_num_type"] == 1) {
                        if ($member_limit["over_avg_consume_num"] == 1) {
                            if ($order["total_num"] < $avg_consume_num_all) {
                                unset($orders[$key]);
                            }
                        } else {
                            if ($avg_consume_num_all < $order["total_num"]) {
                                unset($orders[$key]);
                            }
                        }
                    } else {
                        if ($member_limit["consume_num_type"] == 2) {
                            if (0 < $member_limit["min_consume_num"] && $order["total_num"] < $member_limit["min_consume_num"]) {
                                unset($orders[$key]);
                            }
                            if (0 < $member_limit["max_consume_num"] && $member_limit["min_consume_num"] < $member_limit["max_consume_num"] && $member_limit["max_consume_num"] < $order["total_num"]) {
                                unset($orders[$key]);
                            }
                        }
                    }
                    $avg_price = round($order["total_final_fee"] / $order["total_num"], 2);
                    if ($member_limit["consume_price_type"] == 1) {
                        if ($member_limit["over_avg_consume_price"] == 1) {
                            if ($avg_price < $avg_price_all) {
                                unset($orders[$key]);
                            }
                        } else {
                            if ($avg_price_all < $avg_price) {
                                unset($orders[$key]);
                            }
                        }
                    } else {
                        if ($member_limit["consume_price_type"] == 2) {
                            if (0 < $member_limit["avg_min_consume_price"] && $avg_price < $member_limit["avg_min_consume_price"]) {
                                unset($orders[$key]);
                            }
                            if (0 < $member_limit["avg_max_consume_price"] && $member_limit["avg_min_consume_price"] < $member_limit["avg_max_consume_price"] && $member_limit["avg_max_consume_price"] < $avg_price) {
                                unset($orders[$key]);
                            }
                        }
                    }
                }
            }
            $uids = array_keys($orders);
            if ($member_limit["is_favorite"] != -1 && $group["group_condition"]["store_member_type"] == 0) {
                $store_favorite = pdo_getall("tiny_wmall_store_favorite", array("uniacid" => $_W["uniacid"], "sid" => $group["sid"]), array("uid"), "uid");
                $is_favorite = array_keys($store_favorite);
                if ($member_limit["is_favorite"] == 0) {
                    $uids = array_diff($uids, $is_favorite);
                } else {
                    if ($member_limit["is_favorite"] == 1) {
                        $uids = array_intersect($is_favorite, $uids);
                    }
                }
            }
            if ($group["group_condition"]["store_member_type"] == 2) {
                $orders_in_limit_days = pdo_fetchall("select uid from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and status = 5 and stat_day > :stat_day", array(":uniacid" => $_W["uniacid"], ":sid" => $group["sid"], ":stat_day" => $params[":end_day"]), "uid");
                $uids_in_limit_days = array_keys($orders_in_limit_days);
                $uids = array_diff($uids, $uids_in_limit_days);
            }
        }
    }
    $group["uids"] = $uids;
    $group["uids_total"] = count($group["uids"]);
    return $group;
}
function superCoupon_grant_num($sid)
{
    global $_W;
    $starttime = mktime(0, 0, 0, date("m"), 1, date("Y"));
    $endtime = mktime(23, 59, 59, date("m"), date("t"), date("Y"));
    $num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and sid = :sid and type = :type and granttime > :starttime and granttime < :endtime", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":type" => "superCoupon", ":starttime" => $starttime, ":endtime" => $endtime));
    $max_limit = $_W["we7_wmall"]["store"]["data"]["superCoupon"]["max_limit"];
    if (empty($max_limit)) {
        $max_limit = get_plugin_config("superCoupon.store_coupon_max");
    }
    $over_max_limit = 0;
    if ($max_limit <= $num && 0 < $max_limit) {
        $over_max_limit = 1;
    }
    return array("max_limit" => $max_limit, "grant_num" => $num, "over_max_limit" => $over_max_limit);
}

?>