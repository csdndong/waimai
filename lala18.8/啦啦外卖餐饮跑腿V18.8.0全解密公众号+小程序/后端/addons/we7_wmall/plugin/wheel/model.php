<?php

defined("IN_IA") or exit("Access Denied");
function get_wheel_data($id)
{
    global $_W;
    $wheel = pdo_fetch("select * from " . tablename("tiny_wmall_wheel") . " where uniacid = :uniacid and id = :id and status = 1", array(":id" => $id, ":uniacid" => $_W["uniacid"]));
    if (!empty($wheel)) {
        $wheel["data"] = json_decode(base64_decode($wheel["data"]), true);
        foreach ($wheel["data"]["data"] as &$val) {
            $val["imgurl"] = tomedia($val["imgurl"]);
        }
    }
    return $wheel;
}
function get_wheel_winner($wheel, $extra = array())
{
    global $_W;
    $wheel_params = $wheel["data"]["params"];
    $wheel_data = $wheel["data"]["data"];
    $endtime = strtotime($wheel_params["endtime"]);
    if ($endtime < TIMESTAMP) {
        return error(-1, "");
    }
    $starttime = strtotime($wheel_params["starttime"]);
    if (TIMESTAMP < $starttime) {
        return error(-1, "活动未开始");
    }
    if ($wheel_params["memberlimit"] == 1 && empty($extra["order_id"])) {
        return error(-1, "下单用户才能参与抽奖");
    }
    $condition = " where uniacid = :uniacid and uid = :uid and activity_id = :activity_id";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":activity_id" => $wheel["id"]);
    if (0 < $wheel_params["takeparttotal"]) {
        if ($wheel_params["memberlimit"] == 1) {
            $condition .= " and order_id = :order_id";
            $params[":order_id"] = $extra["order_id"];
        }
        $takeparttotal = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_wheel_record") . $condition, $params);
        if ($wheel_params["takeparttotal"] <= $takeparttotal) {
            return error(-1, "抽奖次数达到上限，不能参与抽奖");
        }
    }
    if (0 < $wheel_params["per_day"]) {
        $condition .= " and stat_day = :stat_day";
        $params[":stat_day"] = date("Ymd", TIMESTAMP);
        $takeparday = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_wheel_record") . $condition, $params);
        if ($wheel_params["per_day"] <= $takeparday) {
            return error(-1, "当日抽奖次数达到上限，不能参与抽奖");
        }
    }
    $consume = intval($wheel_params["consume"]);
    if (0 < $consume) {
        if ($_W["member"]["credit1"] < $consume) {
            return error(-1, "您的积分不足，不能抽奖");
        }
        $log = array($_W["member"]["uid"], "幸运抽奖消耗积分, 消耗积分:" . $consume, "we7_wmall");
        member_credit_update($_W["member"]["uid"], "credit1", 0 - $consume, $log);
    }
    $one_total = intval($wheel_data["one"]["awardtotal"]);
    $two_total = intval($wheel_data["two"]["awardtotal"]);
    $three_total = intval($wheel_data["three"]["awardtotal"]);
    $total = $one_total + $two_total + $three_total;
    $proArr = array("one" => $one_total, "two" => $two_total, "three" => $three_total);
    $type = get_rand($proArr);
    $selected = 0;
    if (0 < $total) {
        $selected = 1;
    }
    $can_get_total = intval($wheel_data[$type]["can_get_total"]);
    if (0 < $can_get_total) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
        $type_select_total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_wheel_record") . $condition, $params);
        if ($can_get_total <= $type_select_total) {
            $selected = 0;
        }
    }
    $can_get_day = intval($wheel_data[$type]["can_get_day"]);
    if (0 < $can_get_day) {
        $condition .= " and type = :type and stat_day = :stat_day";
        $params[":type"] = $type;
        $params[":stat_day"] = date("Ymd", TIMESTAMP);
        $type_select_day = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_wheel_record") . $condition, $params);
        if ($can_get_day <= $type_select_day) {
            $selected = 0;
        }
    }
    if ($selected == 1) {
        $chance = $wheel_data[$type]["get_chance"];
        $rand_chance_num = mt_rand(1, 100);
        if ($chance < $rand_chance_num) {
            $selected = 0;
        }
    }
    $note = "";
    if ($selected == 1) {
        $award_type = trim($wheel_data[$type]["award"]);
        if ($award_type == "credit1" || $award_type == "credit2") {
            $award_value = floatval($wheel_data[$type]["creditnum"]);
            if ($wheel_data[$type]["credittype"] == 1) {
                $award_value = randFloat(floatval($wheel_data[$type]["creditmin"]), floatval($wheel_data[$type]["creditmax"]));
            }
            $award_value = array("value" => $award_value, "type" => $award_type);
            $note = $award_type == "credit1" ? "恭喜您！获奖啦！奖品：获得" . $award_value["value"] . "积分，已存入您的积分账户" : "恭喜您！获奖啦！奖品：获得余额" . $award_value["value"] . "元，已加入您的余额账户";
            mload()->model("member");
            $log = array($_W["member"]["uid"], "幸运抽奖" . $note, "we7_wmall");
            member_credit_update($_W["member"]["uid"], $award_type, $award_value["value"], $log);
        } else {
            if ($award_type == "gift") {
                $award_value = array("value" => trim($wheel_data[$type]["giftcontent"]), "type" => "gift");
                $note = "恭喜您！获奖啦！奖品：" . $award_value["value"] . "，请联系商家领取";
            } else {
                if ($award_type == "redpacket") {
                    $award_value = array("value" => array_values($wheel_data[$type]["redpackets"]), "type" => "redpacket");
                    mload()->model("redPacket");
                    $total_discount = 0;
                    foreach ($award_value["value"] as $redpacket) {
                        $redpacket_params = array("title" => $redpacket["name"], "activity_id" => $wheel["id"], "uid" => $_W["member"]["uid"], "channel" => "wheel", "type" => "wheel", "discount" => $redpacket["discount"], "condition" => $redpacket["condition"], "grant_days_effect" => $redpacket["grant_days_effect"], "days_limit" => $redpacket["use_days_limit"], "is_show" => 0, "scene" => $redpacket["scene"]);
                        $total_discount += $redpacket["discount"];
                        redPacket_grant($redpacket_params, false);
                    }
                    $note = "恭喜您！获奖啦！奖品：总计" . $total_discount . "元红包，已发放至您的账户中，快去使用吧";
                } else {
                    if ($award_type == "order_discount") {
                        if (empty($wheel_params["memberlimit"]) || empty($extra["order_id"])) {
                            $selected = 0;
                        }
                        $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $extra["order_id"], "uid" => $_W["member"]["uid"]), array("final_fee"));
                        $order_free_max = intval($wheel_data[$type]["order_free_max"]);
                        if ($order_free_max < $order["final_fee"]) {
                            $selected = 0;
                        }
                        if ($selected == 1) {
                            $award_value = array("value" => $order["final_fee"], "type" => "free");
                            $note = "恭喜您！获奖啦！奖品：本单免单，奖品价值" . $order["final_fee"] . "元，订单金额将会返还到您的账户余额中";
                            mload()->model("member");
                            $log = array($_W["member"]["uid"], "幸运抽奖" . $note, "we7_wmall");
                            member_credit_update($_W["member"]["uid"], "credit2", $order["final_fee"], $log);
                        }
                    }
                }
            }
        }
    }
    $back = floatval($wheel_params["takepartback"]);
    $backstatus = intval($wheel_params["backstatus"]);
    if (0 < $backstatus && 0 < $back && ($wheel_params["backlimit"] == 1 && $selected == 0 || $wheel_params["backlimit"] == 0)) {
        mload()->model("member");
        $back_type = $backstatus == 1 ? "credit1" : "credit2";
        $back_type_cn = $back_type == "credit1" ? (string) $back . "积分" : "余额" . $back . "元";
        $log = array($_W["member"]["uid"], "幸运抽奖参与奖励" . $back_type_cn, "we7_wmall");
        $note .= "幸运抽奖参与奖励" . $back_type_cn;
        $award_value["takepartback"] = array("value" => $back, "type" => $back_type);
        member_credit_update($_W["member"]["uid"], $back_type, $back, $log);
    }
    $award = array("type" => $selected == 1 ? $type : "noaward", "data" => $award_value, "note" => $note);
    $update = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "activity_id" => $wheel["id"], "order_id" => 0 < $extra["order_id"] ? $extra["order_id"] : 0, "type" => $award["type"], "award_type" => $award_type, "code" => random(6, true), "award" => iserializer($award), "status" => $award_type == "gift" ? 0 : 1, "addtime" => TIMESTAMP, "handletime" => "", "stat_day" => date("Ymd", TIMESTAMP));
    pdo_insert("tiny_wmall_wheel_record", $update);
    $id = pdo_insertid();
    if ($selected == 1) {
        $wheel["data"]["data"][$type]["awardtotal"] = intval($wheel["data"]["data"][$type]["awardtotal"]) - 1;
        $wheel["data"]["data"][$type]["awardtotal"] = $wheel["data"]["data"][$type]["awardtotal"] < 0 ? 0 : $wheel["data"]["data"][$type]["awardtotal"];
        $total = $total - 1 < 0 ? 0 : $total - 1;
        pdo_update("tiny_wmall_wheel", array("total" => $total, "data" => base64_encode(json_encode($wheel["data"]))), array("uniacid" => $_W["uniacid"], "id" => $wheel["id"]));
        $lucknum_award = array("one" => "1", "two" => "4", "three" => "7");
        return array("errno" => 0, "message" => (string) $note, "id" => $id, "award" => $award, "luckyNum" => $lucknum_award[$type]);
    }
    $lucknum_noaward = array("2", "3", "5", "6", "8");
    $rand_num = rand(0, 4);
    return array("errno" => 0, "message" => (string) $wheel_params["noawardtips"] . " " . $note, "luckyNum" => $lucknum_noaward[$rand_num]);
}
function awards_rank($key, $all = false)
{
    $rank = array("one" => array("css" => "label label-success", "text" => "一等奖", "color" => ""), "two" => array("css" => "label label-primary", "text" => "2等奖", "color" => ""), "three" => array("css" => "label label-warning", "text" => "3等奖", "color" => ""));
    if (empty($all)) {
        return $rank[$key]["text"];
    }
    return $rank[$key];
}
function award_type($key)
{
    $type = array("credit1" => array("css" => "label label-danger", "text" => "积分", "name" => "credit1"), "credit2" => array("css" => "label label-primary", "text" => "余额", "name" => "credit2"), "redpacket" => array("css" => "label label-success", "text" => "红包", "name" => "redpacket"), "gift" => array("css" => "label label-success", "text" => "赠品", "name" => "gift"));
    return $type[$key];
}
function get_wheel_url($extra = array(), $type = "pay")
{
    global $_W;
    $order_id = 0;
    if ($type == "pay") {
        $order_id = $extra["order_id"];
    }
    $url = "";
    $_wheel_scene = get_plugin_config("wheel.scene");
    if (!empty($_wheel_scene[$type]) && !empty($_wheel_scene[$type]["wheel_id"])) {
        $wheel = pdo_fetch("select starttime, endtime from " . tablename("tiny_wmall_wheel") . " where uniacid = :uniacid and id = :id and status = 1", array(":id" => $_wheel_scene[$type]["wheel_id"], ":uniacid" => $_W["uniacid"]));
        if ($wheel["starttime"] <= TIMESTAMP && TIMESTAMP < $wheel["endtime"]) {
            if ($extra["ochannel"] == "wxapp") {
                $url = "plugin/pages/wheel/index?id=" . $_wheel_scene[$type]["wheel_id"] . "&order_id=" . $order_id;
            } else {
                if ($extra["ochannel"] == "wap") {
                    $url = ivurl("/plugin/pages/wheel/index", array("id" => $_wheel_scene[$type]["wheel_id"], "order_id" => $order_id));
                } else {
                    if ($extra["ochannel"] == "h5app") {
                        $url = ivurl("/plugin/pages/wheel/index", array("id" => $_wheel_scene[$type]["wheel_id"], "order_id" => $order_id), true);
                    } else {
                        if ($extra["ochannel"] == "owap") {
                            $url = ivurl("/plugin/pages/wheel/index", array("id" => $_wheel_scene[$type]["wheel_id"], "order_id" => $order_id), true);
                        } else {
                            $url = imurl("wheel/activity/index", array("id" => $_wheel_scene[$type]["wheel_id"], "order_id" => $order_id), true);
                        }
                    }
                }
            }
        }
    }
    return $url;
}

?>
