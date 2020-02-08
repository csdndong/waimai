<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $canBuy = mealRedpacket_can_buy();
    $mealRedpacket = mealRedpacket_available_get();
    $redpackets = array_values($mealRedpacket["data"]["redpackets"]);
    $useful_num = 0;
    if (empty($canBuy)) {
        $redpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and activity_id = :activity_id and channel = :channel and uid = :uid order by status asc, sid asc", array(":uniacid" => $_W["uniacid"], ":activity_id" => $mealRedpacket["id"], ":channel" => "mealRedpacket", ":uid" => $_W["member"]["uid"]));
        if (!empty($redpackets)) {
            foreach ($redpackets as &$redpacket) {
                $redpacket["times"] = iunserializer($redpacket["times_limit"]);
                $redpacket["name"] = $redpacket["title"];
                $redpacket["use_days_limit"] = ($redpacket["endtime"] - $redpacket["starttime"]) / 86400;
                if (0 < $redpacket["sid"]) {
                    $store = store_fetch($redpacket["sid"], array("title"));
                    $redpacket["name"] = $store["title"];
                }
                $redpacket["endtime_cn"] = "有效期至 " . date("Y-m-d", $redpacket["endtime"]);
                if ($redpacket["status"] == 1) {
                    $useful_num++;
                } else {
                    if ($redpacket["status"] == 2) {
                        $redpacket["endtime_cn"] = "已使用";
                    } else {
                        if ($redpacket["status"] == 3) {
                            $redpacket["endtime_cn"] = "已过期";
                        }
                    }
                }
            }
        }
    }
    $exchanges = mealRedpacket_exchanges_getall($mealRedpacket["id"]);
    $result = array("canBuy" => $canBuy, "mealRedpacket" => $mealRedpacket, "redpackets" => $redpackets, "exchanges" => $exchanges, "useful_num" => $useful_num);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "submit") {
    $record = mealRedpacket_can_buy();
    if (!$record) {
        imessage(error(-1, "本月购买次数已用完"), "", "ajax");
    }
    $mealRedpacket_id = intval($_GPC["mealRedpacket_id"]);
    $activity = mealRedpacket_meal_get($mealRedpacket_id);
    $data = array();
    if (!empty($activity) && $activity["status"] == 1) {
        $data["meal"] = $activity["data"];
    } else {
        imessage(error(-1, "套餐红包活动不存在！"), "", "ajax");
    }
    $order = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "sid" => $mealRedpacket_id, "type" => "exchangeRedpacket", "order_sn" => date("YmdHis") . random(6, true), "final_fee" => floatval($activity["data"]["params"]["price"]), "is_pay" => 0, "addtime" => TIMESTAMP, "data" => iserializer($data));
    pdo_insert("tiny_wmall_superredpacket_meal_order", $order);
    $order_id = pdo_insertid();
    imessage(error(0, $order_id), "", "ajax");
}
if ($op == "exchange") {
    $mealRedpacket_id = intval($_GPC["mealRedpacket_id"]);
    $exchanges = mealRedpacket_exchanges_getall($mealRedpacket_id);
    $result = array("exchanges" => $exchanges);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "do_exchange") {
    $redpacket_id = intval($_GPC["redpacket_id"]);
    $sid = intval($_GPC["sid"]);
    $mealRedpacket_id = intval($_GPC["mealRedpacket_id"]);
    $redpacket = pdo_get("tiny_wmall_activity_redpacket_record", array("uniacid" => $_W["uniacid"], "id" => $redpacket_id));
    if (empty($redpacket)) {
        imessage(error(-1, "红包不存在"), "", "ajax");
    }
    if (TIMESTAMP < $redpacket["starttime"] || $redpacket["endtime"] < TIMESTAMP) {
        imessage(error(-1, "红包已过期"), "", "ajax");
    }
    if ($redpacket["status"] != 1) {
        imessage(error(-1, "红包已使用或者已失效"), "", "ajax");
    }
    if ($redpacket["scene"] == "paotui") {
        imessage(error(-1, "该红包为跑腿红包，无法升级为门店红包"), "", "ajax");
    }
    $exchange = pdo_get("tiny_wmall_mealredpacket_exchange", array("uniacid" => $_W["uniacid"], "redpacketid" => $mealRedpacket_id, "sid" => $sid));
    if (empty($exchange)) {
        imessage(error(-1, "无效的门店兑换红包"), "", "ajax");
    }
    $update = array("sid" => $sid, "discount" => $exchange["discount"], "condition" => $exchange["condition"], "starttime" => TIMESTAMP + $exchange["grant_days_effect"] * 86400, "endtime" => TIMESTAMP + $exchange["grant_days_effect"] * 86400 + $exchange["use_days_limit"] * 86400, "times_limit" => "");
    pdo_update("tiny_wmall_activity_redpacket_record", $update, array("id" => $redpacket_id));
    imessage(error(0, "兑换成功"), "", "ajax");
}
if ($op == "mealorder") {
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $condition = " where uniacid = :uniacid and uid = :uid and type = :type and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":type" => "exchangeRedpacket");
    $orders = pdo_fetchall("select * from " . tablename("tiny_wmall_superredpacket_meal_order") . " " . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($orders)) {
        foreach ($orders as &$order) {
            $order["addtime"] = date("Y-m-d H:i", $order["addtime"]);
            $order["data"] = iunserializer($order["data"]);
        }
    }
    $result = array("orders" => $orders);
    imessage(error(0, $result), "", "ajax");
}

?>