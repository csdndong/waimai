<?php
/*
 * @ 买卖跑腿系统
 * @ APP公众号小程序版
 * @ PHP开源站，遵从PHP开源精神
 * @ 源码仅供学习研究，禁止商业用途
 */

defined("IN_IA") or exit("Access Denied");
function mealRedpacket_available_get()
{
    global $_W;
    $activity = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "type" => "mealRedpacket", "status" => 1));
    if (!empty($activity)) {
        $activity["data"] = json_decode(base64_decode($activity["data"]), true);
        if (!empty($activity["data"]["rules"])) {
            $activity["data"]["rules"] = htmlspecialchars_decode(base64_decode($activity["data"]["rules"]));
        }
        if (!empty($activity["data"]["exchanges"])) {
            foreach ($activity["data"]["exchanges"] as $key => &$val) {
                if (0 < $val["store_id"]) {
                    $val["logo"] = tomedia($val["logo"]);
                    $val["score"] = floatval($val["score"]);
                } else {
                    unset($activity["data"]["exchanges"][$key]);
                }
            }
        }
        if (!empty($activity["data"]["params"]["tips"])) {
            foreach ($activity["data"]["params"]["tips"] as &$tip) {
                $tip["imgurl"] = tomedia($tip["imgurl"]);
            }
        }
        $activity["data"]["params"]["backgroundImage"] = tomedia($activity["data"]["params"]["backgroundImage"]);
    }
    return $activity;
}
function mealRedpacket_meal_get($id)
{
    global $_W;
    $activity = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "type" => "mealRedpacket", "id" => $id));
    if (!empty($activity)) {
        $activity["data"] = json_decode(base64_decode($activity["data"]), true);
        if (!empty($activity["data"]["rules"])) {
            $activity["data"]["rules"] = htmlspecialchars_decode(base64_decode($activity["data"]["rules"]));
        }
        if (!empty($activity["data"]["exchanges"])) {
            foreach ($activity["data"]["exchanges"] as $key => &$val) {
                if (0 < $val["store_id"]) {
                    $val["logo"] = tomedia($val["logo"]);
                    $val["score"] = floatval($val["score"]);
                } else {
                    unset($activity["data"]["exchanges"][$key]);
                }
            }
        }
        if (!empty($activity["data"]["params"]["tips"])) {
            foreach ($activity["data"]["params"]["tips"] as &$tip) {
                $tip["imgurl"] = tomedia($tip["imgurl"]);
            }
        }
        $activity["data"]["params"]["backgroundImage"] = tomedia($activity["data"]["params"]["backgroundImage"]);
    }
    return $activity;
}
function mealRedpacket_can_buy()
{
    global $_W;
    $record = pdo_fetch("select addtime, sid from " . tablename("tiny_wmall_superredpacket_meal_order") . " where uniacid = :uniacid and uid = :uid and  type = :type and is_pay = 1 order by addtime desc", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":type" => "exchangeRedpacket"));
    $mealRedpacket = mealredpacket_meal_get($record["sid"]);
    if (empty($mealRedpacket) || !empty($mealRedpacket) && $mealRedpacket["status"] == 2) {
        return true;
    }
    if (!empty($record)) {
        $time = $record["addtime"] + 31 * 86400;
        if ($time < TIMESTAMP) {
            return true;
        }
        return false;
    }
    return true;
}
function mealRedpacket_order_get($id)
{
    global $_W;
    $data = pdo_get("tiny_wmall_superredpacket_meal_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (!empty($data)) {
        $data["addtime"] = date("Y-m-d H:i", $data["addtime"]);
        $data["data"] = iunserializer($data["data"]);
    }
    return $data;
}
function mealRedpacket_exchanges_getall($id)
{
    global $_W;
    global $_GPC;
    $page = max(1, intval($_GPC["page"]));
    $psize = 0 < intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $exchanges = pdo_fetchall("select a.*, b.title, b.score, b.logo from " . tablename("tiny_wmall_mealredpacket_exchange") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.redpacketid = :redpacketid limit " . ($page - 1) * $psize . "," . $psize, array(":uniacid" => $_W["uniacid"], ":redpacketid" => $id));
    if (!empty($exchanges)) {
        foreach ($exchanges as &$val) {
            $val["store_id"] = $val["sid"];
            $val["discount"] = floatval($val["discount"]);
            $val["score"] = floatval($val["score"]);
            $val["logo"] = tomedia($val["logo"]);
            $activity = store_fetch_activity($val["sid"], array("discount"));
            $val["activity"] = !empty($activity["items"]["discount"]) ? $activity["items"]["discount"]["title"] : "暂无满减优惠";
        }
    }
    return $exchanges;
}
function mealRedpacket_order_update($orderOrId, $type, $extra = array())
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = mealredpacket_order_get($order);
    }
    if (empty($order)) {
        return error(-1, "订单不存在！");
    }
    if ($type == "pay") {
        $update = array("is_pay" => 1, "pay_type" => $extra["type"], "paytime" => TIMESTAMP);
        pdo_update("tiny_wmall_superredpacket_meal_order", $update, array("id" => $order["id"]));
        mload()->model("redPacket");
        $meal_redpackets_data = !empty($order["data"]["meal"]["data"]) ? $order["data"]["meal"]["data"] : $order["data"]["meal"]["redpackets"];
        if (!empty($meal_redpackets_data)) {
            $discount = 0;
            $num = 0;
            foreach ($meal_redpackets_data as $redpacket) {
                $data = array("title" => $redpacket["name"], "channel" => "mealRedpacket", "type" => "grant", "discount" => $redpacket["discount"], "days_limit" => $redpacket["use_days_limit"], "grant_days_effect" => $redpacket["grant_days_effect"], "condition" => $redpacket["condition"], "uid" => $order["uid"], "activity_id" => $order["sid"], "scene" => $redpacket["scene"], "order_type_limit" => $redpacket["order_type_limit"]);
                $times_limit = array();
                if (!empty($redpacket["times"])) {
                    foreach ($redpacket["times"] as $time) {
                        if ($time["start_hour"] && $time["end_hour"]) {
                            $times_limit[] = $time;
                        }
                    }
                }
                if (!empty($times_limit)) {
                    $data["times_limit"] = iserializer($times_limit);
                }
                $category_limit = array();
                if (!empty($redpacket["categorys"])) {
                    foreach ($redpacket["categorys"] as $category) {
                        $category_limit[] = $category["id"];
                    }
                }
                $data["category_limit"] = implode("|", $category_limit);
                if (!empty($extra["pre_mealredpacket_used_key"]) && $extra["pre_mealredpacket_used_key"] == $redpacket["key"]) {
                    $data["status"] = $extra["status"];
                    $data["order_id"] = $extra["order_id"];
                }
                redPacket_grant($data, false);
                $discount += $data["discount"];
                $num++;
            }
            $openid = member_uid2openid($order["uid"]);
            if (!empty($openid)) {
                $config = $_W["we7_wmall"]["config"];
                $params = array("first" => "您在" . $config["mall"]["title"] . "的账户有" . $num . "个代金券到账", "keyword1" => date("Y-m-d H:i", TIMESTAMP), "keyword2" => "代金券到账", "keyword3" => (string) $discount . "元", "remark" => implode("\n", array("感谢您对" . $config["mall"]["title"] . "平台的支持与厚爱。点击查看红包>>")));
                $send = sys_wechat_tpl_format($params);
                load()->func("communication");
                $acc = WeAccount::create($_W["acid"]);
                $url = ivurl("pages/home/index", array(), true);
                $status = $acc->sendTplNotice($openid, $_W["we7_wmall"]["config"]["notice"]["wechat"]["account_change_tpl"], $send, $url);
                if (is_error($status)) {
                    slog("wxtplNotice", "发放红包套餐红包微信通知顾客", $send, $status["message"]);
                }
            }
        }
    } else {
        if ($type == "handle" && $order["status"] == 1) {
            pdo_update("tiny_wmall_superredpacket_meal_order", array("status" => 2), array("id" => $order["id"]));
        }
    }
    return true;
}
function mealRedpacket_build_virtual()
{
    global $_W;
    $can_buy = mealredpacket_can_buy();
    if (!$can_buy) {
        return false;
    }
    $result = mealredpacket_available_get();
    if (!empty($result)) {
        mload()->model("redPacket");
        $meal_redpackets = array("price" => $result["data"]["params"]["price"], "title" => $result["data"]["params"]["title"], "placeholder" => $result["data"]["params"]["placeholder"]);
        $meal_redpackets["data"] = $result["data"]["redpackets"];
        if (empty($meal_redpackets["data"])) {
            return false;
        }
        $total_discount = 0;
        foreach ($meal_redpackets["data"] as $key => $val) {
            $new_key = "pre_mealredpacket_" . $key;
            $meal_redpackets["data"][$key]["key"] = $key;
            $meal_redpackets["data"][$key]["id"] = $new_key;
            $meal_redpackets["data"][$key]["title"] = $val["name"];
            $meal_redpackets["data"][$key]["days_limit"] = $val["use_days_limit"];
            $times_limit = array();
            if (!empty($meal_redpackets["data"][$key]["times"])) {
                foreach ($meal_redpackets["data"][$key]["times"] as $time) {
                    if ($time["start_hour"] && $time["end_hour"]) {
                        $times_limit[] = $time;
                    }
                }
            }
            if (!empty($times_limit)) {
                $meal_redpackets["data"][$key]["times_limit"] = iserializer($times_limit);
            }
            $category_limit = array();
            if (!empty($meal_redpackets["data"][$key]["categorys"])) {
                foreach ($meal_redpackets["data"][$key]["categorys"] as $category) {
                    $category_limit[] = $category["id"];
                }
            }
            $meal_redpackets["data"][$key]["category_limit"] = implode("|", $category_limit);
            $meal_redpackets["data"][$key]["starttime"] = TIMESTAMP;
            $meal_redpackets["data"][$key]["endtime"] = TIMESTAMP + $val["use_days_limit"] * 86400;
            if ($val["grant_days_effect"]) {
                $meal_redpackets["data"][$key]["starttime"] += $val["grant_days_effect"] * 86400;
                $meal_redpackets["data"][$key]["endtime"] += $val["grant_days_effect"] * 86400;
            }
            $meal_redpackets["data"][$key]["day_cn"] = "限" . date("Y-m-d", $meal_redpackets["data"][$key]["starttime"]) . "~" . date("Y-m-d", $meal_redpackets["data"][$key]["endtime"]) . "使用";
            $meal_redpackets["data"][$key]["time_cn"] = totime($meal_redpackets["data"][$key]["times_limit"]);
            if (!empty($meal_redpackets["data"][$key]["time_cn"])) {
                $meal_redpackets["data"][$key]["time_cn"] = "仅限" . $meal_redpackets["data"][$key]["time_cn"] . "时段使用";
            }
            $meal_redpackets["data"][$key]["category_cn"] = tocategory($meal_redpackets["data"][$key]["category_limit"]);
            if (!empty($meal_redpackets["data"][$key]["category_cn"])) {
                $meal_redpackets["data"][$key]["category_cn"] = "仅限" . $meal_redpackets["data"][$key]["category_cn"] . "分类使用";
            }
            $total_discount += $val["discount"];
        }
        $meal_redpackets["superRedpacket_id"] = $result["id"];
        $meal_redpackets["rules"] = $result["data"]["rules"];
        $meal_redpackets["total_discount"] = $total_discount;
        $meal_redpackets["total_num"] = count($meal_redpackets["data"]);
        return $meal_redpackets;
    } else {
        unset($result);
        return false;
    }
}
function mealRedpacket_plus_available_get()
{
    global $_W;
    $activity = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "type" => "meal", "status" => 1));
    if (!empty($activity)) {
        $activity["data"] = json_decode(base64_decode($activity["data"]), true);
        if (!empty($activity["data"]["rules"])) {
            $activity["data"]["rules"] = htmlspecialchars_decode(base64_decode($activity["data"]["rules"]));
        }
    }
    return $activity;
}
function mealRedpacket_plus_get($id)
{
    global $_W;
    $activity = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "type" => "meal", "id" => $id));
    if (!empty($activity)) {
        $activity["data"] = json_decode(base64_decode($activity["data"]), true);
        if (!empty($activity["data"]["rules"])) {
            $activity["data"]["rules"] = htmlspecialchars_decode(base64_decode($activity["data"]["rules"]));
        }
    }
    return $activity;
}
function mealRedpacket_plus_order_get($id)
{
    global $_W;
    $data = pdo_get("tiny_wmall_superredpacket_meal_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (!empty($data)) {
        $data["addtime"] = date("Y-m-d H:i", $data["addtime"]);
        $data["data"] = iunserializer($data["data"]);
    }
    return $data;
}
function mealRedpacket_plus_order_update($orderOrId, $type, $extra = array())
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = mealredpacket_plus_order_get($order);
    }
    if (empty($order)) {
        return error(-1, "商品不存在！");
    }
    if ($type == "pay") {
        $update = array("is_pay" => 1, "pay_type" => $extra["type"], "paytime" => TIMESTAMP);
        pdo_update("tiny_wmall_superredpacket_meal_order", $update, array("id" => $order["id"]));
        mload()->model("redPacket");
        if (!empty($order["data"]["meal"]["data"])) {
            $discount = 0;
            $num = 0;
            foreach ($order["data"]["meal"]["data"] as $redpacket) {
                $data = array("title" => $redpacket["name"], "channel" => "mealRedpacket_plus", "type" => "grant", "discount" => $redpacket["discount"], "days_limit" => $redpacket["use_days_limit"], "grant_days_effect" => $redpacket["grant_days_effect"], "condition" => $redpacket["condition"], "uid" => $order["uid"], "scene" => $redpacket["scene"], "order_type_limit" => $redpacket["order_type_limit"]);
                $times_limit = array();
                if (!empty($redpacket["times"])) {
                    foreach ($redpacket["times"] as $time) {
                        if ($time["start_hour"] && $time["end_hour"]) {
                            $times_limit[] = $time;
                        }
                    }
                }
                if (!empty($times_limit)) {
                    $data["times_limit"] = iserializer($times_limit);
                }
                $category_limit = array();
                if (!empty($redpacket["categorys"])) {
                    foreach ($redpacket["categorys"] as $category) {
                        $category_limit[] = $category["id"];
                    }
                }
                $data["category_limit"] = implode("|", $category_limit);
                if (!empty($extra["pre_mealredpacket_used_key"]) && $extra["pre_mealredpacket_used_key"] == $redpacket["key"]) {
                    $data["status"] = $extra["status"];
                    $data["order_id"] = $extra["order_id"];
                }
                redPacket_grant($data, false);
                $discount += $data["discount"];
                $num++;
            }
            $openid = member_uid2openid($order["uid"]);
            if (!empty($openid)) {
                $config = $_W["we7_wmall"]["config"];
                $params = array("first" => "您在" . $config["mall"]["title"] . "的账户有" . $num . "个代金券到账", "keyword1" => date("Y-m-d H:i", TIMESTAMP), "keyword2" => "代金券到账", "keyword3" => (string) $discount . "元", "remark" => implode("\n", array("感谢您对" . $config["mall"]["title"] . "平台的支持与厚爱。点击查看红包>>")));
                $send = sys_wechat_tpl_format($params);
                load()->func("communication");
                $acc = WeAccount::create($_W["acid"]);
                $url = ivurl("pages/home/index", array(), true);
                $status = $acc->sendTplNotice($openid, $_W["we7_wmall"]["config"]["notice"]["wechat"]["account_change_tpl"], $send, $url);
                if (is_error($status)) {
                    slog("wxtplNotice", "发放红包套餐plus红包微信通知顾客", $send, $status["message"]);
                }
            }
        }
    } else {
        if ($type == "handle" && $order["status"] == 1) {
            pdo_update("tiny_wmall_superredpacket_meal_order", array("status" => 2), array("id" => $order["id"]));
        }
    }
    return true;
}
function mealRedpacket_plus_build_virtual()
{
    global $_W;
    if (check_plugin_exist("mealRedpacket")) {
        $exist_mealredpacket = pdo_get("tiny_wmall_activity_redpacket_record", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "channel" => "mealRedpacket_plus", "status" => 1), array("id"));
        if (empty($exist_mealredpacket)) {
            $result = mealredpacket_plus_available_get();
            if (!empty($result)) {
                mload()->model("redPacket");
                $meal_redpackets = current($result["data"]["redpackets"]);
                if (empty($meal_redpackets["data"])) {
                    return false;
                }
                $total_discount = 0;
                foreach ($meal_redpackets["data"] as $key => $val) {
                    $new_key = "pre_mealredpacket_" . $key;
                    $meal_redpackets["data"][$key]["key"] = $key;
                    $meal_redpackets["data"][$key]["id"] = $new_key;
                    $meal_redpackets["data"][$key]["title"] = $val["name"];
                    $meal_redpackets["data"][$key]["days_limit"] = $val["use_days_limit"];
                    $times_limit = array();
                    if (!empty($meal_redpackets["data"][$key]["times"])) {
                        foreach ($meal_redpackets["data"][$key]["times"] as $time) {
                            if ($time["start_hour"] && $time["end_hour"]) {
                                $times_limit[] = $time;
                            }
                        }
                    }
                    if (!empty($times_limit)) {
                        $meal_redpackets["data"][$key]["times_limit"] = iserializer($times_limit);
                    }
                    $category_limit = array();
                    if (!empty($meal_redpackets["data"][$key]["categorys"])) {
                        foreach ($meal_redpackets["data"][$key]["categorys"] as $category) {
                            $category_limit[] = $category["id"];
                        }
                    }
                    $meal_redpackets["data"][$key]["category_limit"] = implode("|", $category_limit);
                    $meal_redpackets["data"][$key]["starttime"] = TIMESTAMP;
                    $meal_redpackets["data"][$key]["endtime"] = TIMESTAMP + $val["use_days_limit"] * 86400;
                    if ($val["grant_days_effect"]) {
                        $meal_redpackets["data"][$key]["starttime"] += $val["grant_days_effect"] * 86400;
                        $meal_redpackets["data"][$key]["endtime"] += $val["grant_days_effect"] * 86400;
                    }
                    $meal_redpackets["data"][$key]["day_cn"] = "限" . date("Y-m-d", $meal_redpackets["data"][$key]["starttime"]) . "~" . date("Y-m-d", $meal_redpackets["data"][$key]["endtime"]) . "使用";
                    $meal_redpackets["data"][$key]["time_cn"] = totime($meal_redpackets["data"][$key]["times_limit"]);
                    if (!empty($meal_redpackets["data"][$key]["time_cn"])) {
                        $meal_redpackets["data"][$key]["time_cn"] = "仅限" . $meal_redpackets["data"][$key]["time_cn"] . "时段使用";
                    }
                    $meal_redpackets["data"][$key]["category_cn"] = tocategory($meal_redpackets["data"][$key]["category_limit"]);
                    if (!empty($meal_redpackets["data"][$key]["category_cn"])) {
                        $meal_redpackets["data"][$key]["category_cn"] = "仅限" . $meal_redpackets["data"][$key]["category_cn"] . "分类使用";
                    }
                    $total_discount += $val["discount"];
                }
                $meal_ids = array_keys($result["data"]["redpackets"]);
                $meal_redpackets["meal_id"] = $meal_ids[0];
                $meal_redpackets["superRedpacket_id"] = $result["id"];
                $meal_redpackets["rules"] = $result["data"]["rules"];
                $meal_redpackets["total_discount"] = $total_discount;
                $meal_redpackets["total_num"] = count($meal_redpackets["data"]);
                return $meal_redpackets;
            } else {
                unset($result);
            }
        }
    }
    return false;
}
function mealRedpacket_member()
{
    global $_W;
    $status = 0;
    $title = "";
    $mealRedpacket = mealredpacket_available_get();
    if (!empty($mealRedpacket)) {
        $status = 1;
        $title = $mealRedpacket["data"]["params"]["title"];
    }
    $canBuy = mealredpacket_can_buy();
    $total_redpacket = floatval(pdo_fetchcolumn("select sum(discount) from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and uid = :uid and channel = :channel", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "mealRedpacket")));
    return array("status" => $status, "title" => $title, "canBuy" => $canBuy, "total_redpacket" => $total_redpacket);
}

?>