<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
mload()->model("coupon");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $config_supercoupon = get_plugin_config("superCoupon");
    run_creat_system_coupon_group($sid);
    $_W["page"]["title"] = "超级代金券";
    $groups = pdo_fetchall("select id, title, content, is_system, status, code from " . tablename("tiny_wmall_supercoupon_member_group") . " where uniacid = :uniacid and sid = :sid order by is_system desc, code desc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    $system_groups = array();
    $system_scene_groups = array();
    $selfdefiine_groups = array();
    foreach ($groups as $val) {
        if ($val["is_system"] == 1 && $val["status"] == 1) {
            if (20000 < $val["code"]) {
                $system_scene_groups[] = $val;
            } else {
                $system_groups[] = $val;
            }
        } else {
            $selfdefiine_groups[] = $val;
        }
    }
    include itemplate("store/activity/superCoupon");
}
if ($ta == "selfdefine") {
    $_W["page"]["title"] = "自定义人群";
    $id = intval($_GPC["id"]);
    $group = pdo_get("tiny_wmall_supercoupon_member_group", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
    if (!empty($group)) {
        $group["group_condition"] = iunserializer($group["group_condition"]);
        $group["group_condition"]["member_limit"]["uids"] = implode(",", $group["group_condition"]["member_limit"]["uids"]);
        $member_limit = $group["group_condition"]["member_limit"];
    }
    $member_limit["is_comment"] = isset($member_limit["is_comment"]) ? $member_limit["is_comment"] : -1;
    $member_limit["is_favorite"] = isset($member_limit["is_favorite"]) ? $member_limit["is_favorite"] : -1;
    $store_member_type = intval($_GPC["store_member_type"]);
    if ($_W["ispost"]) {
        if ($store_member_type == 1) {
            $member_limit = array("store_new_member_type" => intval($_GPC["store_new_member_type"]));
        } else {
            if ($store_member_type == 3) {
                $uids = trim($_GPC["uids"]);
                if (!empty($uids)) {
                    $uids = str_replace("，", ",", $uids);
                    $uids = explode(",", $uids);
                    $uids = array_filter($uids, trim);
                    $member_limit = array("uids" => $uids);
                }
            } else {
                if ($store_member_type == 0) {
                    $member_limit = array("order_stat_date_type" => intval($_GPC["order_date_type"]), "order_weekend" => intval($_GPC["order_weekend"]), "order_stat_time_type" => intval($_GPC["order_time_type"]), "is_comment" => intval($_GPC["is_comment"]), "is_favorite" => intval($_GPC["is_favorite"]));
                }
                if ($store_member_type == 2) {
                    $member_limit = array("no_order_days" => intval($_GPC["no_order_days"]), "before_leave_days" => intval($_GPC["before_leave_days"]));
                    if ($member_limit["before_leave_days"] <= $member_limit["no_order_days"]) {
                        imessage(error(-1, "统计天数不能小于未下单天数"), "", "ajax");
                    }
                    if (90 < $member_limit["before_leave_days"]) {
                        imessage(error(-1, "统计天数不能超过90天"), "", "ajax");
                    }
                }
                $member_limit["consume_num_type"] = intval($_GPC["consume_num_type"]);
                $member_limit["consume_price_type"] = intval($_GPC["consume_price_type"]);
                if ($member_limit["consume_num_type"] == 1) {
                    $member_limit["over_avg_consume_num"] = intval($_GPC["over_avg_consume_num"]);
                } else {
                    if ($member_limit["consume_num_type"] == 2) {
                        $min_consume_num = intval($_GPC["min_consume_num"]);
                        $max_consume_num = intval($_GPC["max_consume_num"]);
                        if (!empty($min_consume_num) && !empty($max_consume_num) && $max_consume_num <= $min_consume_num) {
                            imessage(error(-1, "最大消费次数不能小于最小消费次数"), "", "ajax");
                        }
                        $member_limit["min_consume_num"] = $min_consume_num;
                        $member_limit["max_consume_num"] = $max_consume_num;
                    }
                }
                if ($member_limit["consume_price_type"] == 1) {
                    $member_limit["over_avg_consume_price"] = intval($_GPC["over_avg_consume_price"]);
                } else {
                    if ($member_limit["consume_price_type"] == 2) {
                        $avg_min_consume_price = intval($_GPC["avg_min_consume_price"]);
                        $avg_max_consume_price = intval($_GPC["avg_max_consume_price"]);
                        if (!empty($avg_min_consume_price) && !empty($avg_max_consume_price) && $avg_max_consume_price <= $avg_min_consume_price) {
                            imessage(error(-1, "最大客单价不能小于最小客单价"), "", "ajax");
                        }
                        $member_limit["avg_min_consume_price"] = $avg_min_consume_price;
                        $member_limit["avg_max_consume_price"] = $avg_max_consume_price;
                    }
                }
                if ($member_limit["order_stat_time_type"] == 1) {
                    $member_limit["order_stat_time"] = $_GPC["order_time"];
                    if (empty($member_limit["order_stat_time"])) {
                        imessage(error(-1, "下单时间段不能为空"), "", "ajax");
                    }
                }
            }
        }
        $group_condition = array("store_member_type" => intval($_GPC["store_member_type"]), "stat_day" => intval($_GPC["date_type"]), "member_limit" => $member_limit);
        if ($group_condition["stat_day"] == -1) {
            $group_condition["stat_day"] = array("starttime" => trim($_GPC["stattime"]["start"]), "endtime" => trim($_GPC["stattime"]["end"]));
            $starttime = strtotime($group_condition["stat_day"]["starttime"]);
            if (86400 * 90 < TIMESTAMP - $starttime) {
                imessage(error(-1, "统计日期不能超过90天之前"), "", "ajax");
            }
            if (TIMESTAMP <= $starttime) {
                imessage(error(-1, "开始时间不能大于当前时间"), "", "ajax");
            }
        }
        $update = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => trim($_GPC["title"]), "content" => trim($_GPC["content"]), "group_condition" => iserializer($group_condition), "addtime" => TIMESTAMP);
        if (!empty($id)) {
            pdo_update("tiny_wmall_supercoupon_member_group", $update, array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        } else {
            pdo_insert("tiny_wmall_supercoupon_member_group", $update);
        }
        imessage(error(0, "编辑自定义人群成功"), iurl("store/activity/superCoupon/index"), "ajax");
    }
    include itemplate("store/activity/superCoupon-selfdefine");
}
if ($ta == "del") {
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_supercoupon_member_group", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
    imessage(error(0, "删除人群成功"), iurl("store/activity/superCoupon/index"), "ajax");
}
if ($ta == "create") {
    $_W["page"]["title"] = "创建优惠券";
    mload()->model("plugin");
    pload()->model("superCoupon");
    $config_supercoupon = get_plugin_config("superCoupon");
    if ($config_supercoupon["timelimit"]["status"] == 1) {
        $can_grant = is_time_in_period($config_supercoupon["timelimit"]["time"]);
        if (!$can_grant) {
            imessage(error(-1, "当前时间不能发放优惠券"), "", "info");
        }
    }
    $record = superCoupon_grant_num($sid);
    if ($record["over_max_limit"] == 1) {
        imessage(error(-1, "本月发券数量达到最大发券数量" . $record["max_limit"] . "，不能继续发券"), "", "info");
    }
    $id = intval($_GPC["id"]);
    $group = superCoupon_get_group_uids($id);
    $everyone_num = 0 < intval($_GPC["everyone_num"]) ? intval($_GPC["everyone_num"]) : 1;
    if ($_W["ispost"]) {
        if (empty($group["uids_total"])) {
            imessage(error(-1, "人群中没有符合条件的顾客"), "", "ajax");
        }
        if (empty($_GPC["discount"])) {
            imessage(error(-1, "优惠不能为空"), "", "ajax");
        }
        if (empty($_GPC["condition"])) {
            imessage(error(-1, "优惠使用条件不能为空"), "", "ajax");
        }
        if (empty($_GPC["use_days_limit"])) {
            imessage(error(-1, "优惠使用结束时间限制不能为空"), "", "ajax");
        }
        $endtime = intval($_GPC["use_days_limit"]);
        $endtime = strtotime("+" . $endtime . " days") + 86399;
        $coupon = array("uniacid" => $_W["uniacid"], "sid" => $group["sid"], "group_id" => $group["id"], "title" => trim($_GPC["title"]), "addtime" => TIMESTAMP, "endtime" => $endtime, "data" => iserializer(array("total_fee" => $group["uids_total"] * intval($_GPC["discount"]) * $everyone_num, "grant_object" => array("uids" => $group["uids"], "total" => $group["uids_total"] * $everyone_num, "unissued_uid" => $group["uids"], "grant_success" => 0, "everyone_num" => $everyone_num), "coupon" => array("discount" => intval($_GPC["discount"]), "condition" => intval($_GPC["condition"]), "use_days_limit" => intval($_GPC["use_days_limit"])))));
        pdo_insert("tiny_wmall_supercoupon_coupon", $coupon);
        $couponid = pdo_insertid();
        imessage(error(0, "设置券优惠成功,准备发放中"), iurl("store/activity/superCoupon/send", array("id" => $couponid)), "ajax");
    }
    include itemplate("store/activity/superCoupon-send");
}
if ($ta == "send") {
    $_W["page"]["title"] = "发送优惠券";
    $id = intval($_GPC["id"]);
    $superCoupon = pdo_get("tiny_wmall_supercoupon_coupon", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
    $data = iunserializer($superCoupon["data"]);
    $grant_object = $data["grant_object"];
    if ($_W["ispost"]) {
        $grant_object["unissued_uid"] = array_values($grant_object["unissued_uid"]);
        $uids = array_slice($grant_object["unissued_uid"], 0, 20);
        $more = 1;
        if (count($uids) < 20 || $grant_object["total"] <= 20) {
            $more = 0;
        }
        $params = array("sid" => $superCoupon["sid"], "coupon_id" => $superCoupon["id"], "type" => "superCoupon", "condition" => $data["coupon"]["condition"], "discount" => $data["coupon"]["discount"], "use_days_limit" => $data["coupon"]["use_days_limit"], "channel" => "superCoupon");
        foreach ($uids as $key => $uid) {
            $params["uid"] = $uid;
            for ($i = 0; $i < $grant_object["everyone_num"]; $i++) {
                $result = coupon_grant($params);
                if (!is_error($result)) {
                    $grant_object["grant_success"]++;
                }
            }
            unset($grant_object["unissued_uid"][$key]);
        }
        $grant_object["unissued_uid"] = array_values($grant_object["unissued_uid"]);
        $data["grant_object"] = $grant_object;
        pdo_update("tiny_wmall_supercoupon_coupon", array("data" => iserializer($data), "endtime" => TIMESTAMP + $data["coupon"]["use_days_limit"] * 86400), array("uniacid" => $_W["uniacid"], "id" => $id, "sid" => $superCoupon["sid"]));
        imessage(error(0, $more), "", "ajax");
    }
    include itemplate("store/activity/superCoupon-send");
}
function run_creat_system_coupon_group($sid)
{
    global $_W;
    $plugins = pdo_getall("tiny_wmall_plugin", array(), array("name"), "name");
    $plugins = array_keys($plugins);
    if (in_array("superCoupon", $plugins)) {
        $system_groups = array(array("id" => "1", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "需一般挽留的流失顾客", "content" => "曾经下单数、单价低于平均，但近30天未下单", "group_condition" => "a:3:{s:17:\"store_member_type\";i:2;s:8:\"stat_day\";i:1;s:12:\"member_limit\";a:6:{s:13:\"no_order_days\";i:30;s:17:\"before_leave_days\";i:90;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:0;s:22:\"over_avg_consume_price\";i:0;}}", "status" => 1, "code" => "10001"), array("id" => "2", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "需一般发展的顾客", "content" => "近30天下单数、单价低于平均", "group_condition" => "a:3:{s:17:\"store_member_type\";i:0;s:8:\"stat_day\";i:30;s:12:\"member_limit\";a:9:{s:20:\"order_stat_date_type\";i:0;s:13:\"order_weekend\";i:0;s:20:\"order_stat_time_type\";i:0;s:10:\"is_comment\";i:-1;s:11:\"is_favorite\";i:-1;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:0;s:22:\"over_avg_consume_price\";i:0;}}", "status" => 1, "code" => "10002"), array("id" => "3", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "需一般关怀的流失熟客", "content" => "曾经下单数高于平均，单价低于平均，但近30天未下单", "group_condition" => "a:3:{s:17:\"store_member_type\";i:2;s:8:\"stat_day\";i:1;s:12:\"member_limit\";a:6:{s:13:\"no_order_days\";i:30;s:17:\"before_leave_days\";i:90;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:1;s:22:\"over_avg_consume_price\";i:0;}}", "status" => 1, "code" => "10003"), array("id" => "4", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "低消费熟客", "content" => "近30天，下单数高于平均，但单价低于平均", "group_condition" => "a:3:{s:17:\"store_member_type\";i:0;s:8:\"stat_day\";i:30;s:12:\"member_limit\";a:9:{s:20:\"order_stat_date_type\";i:0;s:13:\"order_weekend\";i:0;s:20:\"order_stat_time_type\";i:0;s:10:\"is_comment\";i:-1;s:11:\"is_favorite\";i:-1;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:1;s:22:\"over_avg_consume_price\";i:0;}}", "status" => 1, "code" => "10004"), array("id" => "5", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "需重点挽留的流失顾客", "content" => "曾经下单数低于平均，单价高于平均，但近30天未下单", "group_condition" => "a:3:{s:17:\"store_member_type\";i:2;s:8:\"stat_day\";i:1;s:12:\"member_limit\";a:6:{s:13:\"no_order_days\";i:30;s:17:\"before_leave_days\";i:90;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:0;s:22:\"over_avg_consume_price\";i:1;}}", "status" => 1, "code" => "10005"), array("id" => "6", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "需重点发展的潜力顾客", "content" => "近30天，下单数低于平均，单价高于平均", "group_condition" => "a:3:{s:17:\"store_member_type\";i:0;s:8:\"stat_day\";i:30;s:12:\"member_limit\";a:9:{s:20:\"order_stat_date_type\";i:0;s:13:\"order_weekend\";i:0;s:20:\"order_stat_time_type\";i:0;s:10:\"is_comment\";i:-1;s:11:\"is_favorite\";i:-1;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:0;s:22:\"over_avg_consume_price\";i:1;}}", "status" => 1, "code" => "10006"), array("id" => "7", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "需重点关怀的流失熟客", "content" => "曾经下单数、单价高于平均，但近30天未下单", "group_condition" => "a:3:{s:17:\"store_member_type\";i:2;s:8:\"stat_day\";i:1;s:12:\"member_limit\";a:6:{s:13:\"no_order_days\";i:30;s:17:\"before_leave_days\";i:90;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:1;s:22:\"over_avg_consume_price\";i:1;}}", "status" => 1, "code" => "10007"), array("id" => "8", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "高消费熟客", "content" => "近30天下单数、单价高于平均", "group_condition" => "a:3:{s:17:\"store_member_type\";i:0;s:8:\"stat_day\";i:30;s:12:\"member_limit\";a:9:{s:20:\"order_stat_date_type\";i:0;s:13:\"order_weekend\";i:0;s:20:\"order_stat_time_type\";i:0;s:10:\"is_comment\";i:-1;s:11:\"is_favorite\";i:-1;s:16:\"consume_num_type\";i:1;s:18:\"consume_price_type\";i:1;s:20:\"over_avg_consume_num\";i:1;s:22:\"over_avg_consume_price\";i:1;}}", "status" => 1, "code" => "10008"), array("id" => "9", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "粉丝顾客", "content" => "收藏店铺的顾客，并且近90天有订单完成", "group_condition" => "a:3:{s:17:\"store_member_type\";i:0;s:8:\"stat_day\";i:90;s:12:\"member_limit\";a:7:{s:20:\"order_stat_date_type\";i:0;s:13:\"order_weekend\";i:0;s:20:\"order_stat_time_type\";i:0;s:10:\"is_comment\";i:-1;s:11:\"is_favorite\";i:1;s:16:\"consume_num_type\";i:0;s:18:\"consume_price_type\";i:0;}}", "status" => 1, "code" => "20001"), array("id" => "10", "uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => "1", "title" => "昨日进店未下单新客", "content" => "昨天进店，但未下单的新顾客", "group_condition" => "a:3:{s:17:\"store_member_type\";i:1;s:8:\"stat_day\";i:1;s:12:\"member_limit\";a:1:{s:21:\"store_new_member_type\";i:0;}}", "status" => 1, "code" => "20002"));
        $local_groups = pdo_getall("tiny_wmall_supercoupon_member_group", array("uniacid" => $_W["uniacid"], "sid" => $sid, "is_system" => 1), array("code"), "code");
        if (empty($local_groups)) {
            $local_groups = array();
        }
        foreach ($system_groups as &$row) {
            if (in_array($row["code"], array_keys($local_groups))) {
                continue;
            }
            unset($row["id"]);
            pdo_insert("tiny_wmall_supercoupon_member_group", $row);
        }
    }
    return true;
}

?>