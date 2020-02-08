<?php
defined("IN_IA") or exit("Access Denied");
function svip_meal_getall($filter = array())
{
    global $_W;
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $status = intval($filter["status"]);
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    if (empty($filter["haskey"])) {
        $meals = pdo_fetchall("select * from " . tablename("tiny_wmall_svip_meal") . $condition . " order by displayorder desc, id asc", $params);
    } else {
        $meals = pdo_fetchall("select * from " . tablename("tiny_wmall_svip_meal") . $condition . " order by displayorder desc, id asc", $params, "id");
    }
    return $meals;
}
function svip_meal_get($id)
{
    global $_W;
    $meal = pdo_get("tiny_wmall_svip_meal", array("uniacid" => $_W["uniacid"], "id" => $id));
    return $meal;
}
function svip_buy_notice($id, $extra = array())
{
    global $_W;
    $order = pdo_get("tiny_wmall_svip_meal_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    $pay_types = order_pay_types();
    $order["pay_type_cn"] = $pay_types[$order["pay_type"]]["text"];
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $order["uid"]));
    $meal = svip_meal_get($order["meal_id"]);
    $config_wxapp_basic = $_W["we7_wmall"]["config"]["wxapp"]["basic"];
    $order_channel = $order["order_channel"];
    if ($order_channel == "wxapp" && $config_wxapp_basic["wxapp_consumer_notice_channel"] == "wechat") {
        mload()->model("member");
        $order["openid"] = member_wxapp2openid($order["openid"]);
        if (!empty($order["openid"])) {
            $order_channel = "wechat";
        }
    }
    $acc = WeAccount::create($order["acid"], $order_channel);
    if ($order_channel == "wechat") {
        if (!empty($order["openid"])) {
            $title = "恭喜您，成功开通" . $_W["we7_wmall"]["config"]["mall"]["title"] . "超级会员";
            $remark = array("会员套餐: " . $meal["title"], "支付费用: " . $order["final_fee"] . "元", "支付方式: " . $order["pay_type_cn"], "会员期限: " . date("Y-m-d", $order["starttime"]) . "~" . date("Y-m-d", $order["endtime"]));
            $params = array("first" => $title, "OrderSn" => $order["ordersn"], "OrderStatus" => "已生效", "remark" => implode("\n", $remark));
            $url = ivurl("pages/svip/mine", array(), true);
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($order["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url);
        }
    } else {
        if ($order_channel == "wxapp") {
            $send = array("keyword1" => array("value" => "#" . $order["id"], "color" => "#ff510"), "keyword2" => array("value" => (string) $order["order_type_cn"], "color" => "#ff510"), "keyword3" => array("value" => "已生效", "color" => "#ff510"), "keyword4" => array("value" => $member["realname"], "color" => "#ff510"), "keyword5" => array("value" => $member["mobile"], "color" => "#ff510"), "keyword6" => array("value" => $order["final_fee"], "color" => "#ff510"), "keyword7" => array("value" => date("Y-m-d H:i"), "color" => "#ff510"), "keyword8" => array("value" => $order["ordersn"], "color" => "#ff510"));
            $public_tpl = $_W["we7_wmall"]["config"]["wxapp"]["wxtemplate"]["public_tpl"];
            $status = $acc->sendTplNotice($order["openid"], $public_tpl, $send, "svip/pages/svip/mine");
        }
    }
    if (is_error($status)) {
        slog("wxtplNotice", "超级会员购买微信通知购买人-order_id:" . $order["id"], $send, $status["message"]);
    }
    return true;
}
function svip_meal_order_update($id, $type, $extra)
{
    global $_W;
    $order = pdo_get("tiny_wmall_svip_meal_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    if ($type == "pay") {
        if ($order["is_pay"] == 1) {
            return error(-1, "订单已支付");
        }
        $order["data"] = iunserializer($order["data"]);
        $update = array("is_pay" => 1, "order_channel" => $extra["channel"], "pay_type" => $extra["type"], "final_fee" => $extra["card_fee"], "paytime" => TIMESTAMP, "starttime" => TIMESTAMP, "endtime" => TIMESTAMP + $order["data"]["days"] * 86400);
        pdo_update("tiny_wmall_svip_meal_order", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
        $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $order["uid"]), array("svip_status", "svip_starttime", "svip_endtime"));
        $update_member = array("svip_status" => 1, "svip_starttime" => TIMESTAMP, "svip_endtime" => $update["endtime"]);
        if (TIMESTAMP <= $member["svip_endtime"]) {
            $update_member["svip_starttime"] = $member["svip_starttime"];
            $update_member["svip_endtime"] = $member["svip_endtime"] + $order["data"]["days"] * 86400;
        }
        pdo_update("tiny_wmall_members", $update_member, array("uniacid" => $_W["uniacid"], "uid" => $order["uid"]));
        svip_buy_notice($order["id"]);
        return error(0, "购买会员成功");
    }
}
function svip_set_store_redpacket($sid, $params)
{
    global $_W;
    if (empty($params)) {
        return error(-1, "参数错误");
    }
    $redpacket = pdo_get("tiny_wmall_svip_redpacket", array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => 1));
    if (empty($redpacket)) {
        $params["addtime"] = TIMESTAMP;
        $params["data"] = iserializer($params["data"]);
        pdo_insert("tiny_wmall_svip_redpacket", $params);
        $redpacket["id"] = pdo_insertid();
    } else {
        $redpacket["data"] = iunserializer($redpacket["data"]);
        if (!empty($redpacket["data"])) {
            $params["data"] = array_merge($redpacket["data"], $params["data"]);
        }
        $params["data"] = iserializer($params["data"]);
        pdo_update("tiny_wmall_svip_redpacket", $params, array("id" => $redpacket["id"]));
    }
    return $redpacket["id"];
}
function svip_order_getall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $uid = intval($filter["uid"]);
    if (0 < $uid) {
        $condition .= " and a.uid = :uid";
        $params[":uid"] = $uid;
    }
    $meal_id = intval($filter["meal_id"]);
    if (0 < $meal_id) {
        $condition .= " and a.meal_id = :meal_id";
        $params[":meal_id"] = $meal_id;
    }
    $is_pay = isset($filter["is_pay"]) ? intval($filter["is_pay"]) : -1;
    if (-1 < $is_pay) {
        $condition .= " and a.is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (b.mobile like :keyword or b.realname like :keyword)";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    if (!empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " AND a.addtime > :start AND a.addtime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $pindex = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $orders = pdo_fetchall("select a.*, b.realname, b.avatar, b.svip_status, b.svip_starttime, b.svip_endtime from " . tablename("tiny_wmall_svip_meal_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition . " ORDER BY a.id desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($orders)) {
        $pay_types = order_pay_types();
        $meals = svip_meal_getall(array("haskey" => 1));
        foreach ($orders as &$val) {
            $val["meal"] = $meals[$val["meal_id"]];
            $val["pay_type_all"] = $pay_types[$val["pay_type"]];
            $val["pay_type_cn"] = $pay_types[$val["pay_type"]]["text"];
            $val["avatar"] = tomedia($val["avatar"]);
            $val["starttime_cn"] = date("Y-m-d H:i", $val["starttime"]);
            $val["endtime_cn"] = date("Y-m-d H:i", $val["endtime"]);
            $val["paytime_cn"] = date("Y-m-d H:i", $val["paytime"]);
        }
    }
    $result = array("orders" => $orders);
    if (!defined("IN_WXAPP") && !defined("IN_VUE")) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_svip_meal_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $result["pager"] = $pager;
    }
    return $result;
}
function svip_goods_getall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid and svip_status = 1";
    $params = array(":uniacid" => $_W["uniacid"]);
    $sid = intval($filter["sid"]);
    if (0 < $sid) {
        $condition .= " and a.sid = :sid";
        $params[":sid"] = $sid;
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and a.title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $pindex = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $goods = pdo_fetchall("select a.*, b.title as store_title, b.logo as store_logo,b.score as store_score from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id" . $condition . " ORDER BY b.displayorder desc, a.displayorder desc, a.id desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($goods)) {
        foreach ($goods as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
            $val["svip_discount"] = round($val["svip_price"] / $val["price"] * 10, 1);
        }
    }
    $result = array("goods" => $goods);
    if (!defined("IN_WXAPP") && !defined("IN_VUE")) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id" . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $result["pager"] = $pager;
    }
    return $result;
}
function svip_redpacket_fetchall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    } else {
        $filter = array_merge($_GPC, $filter);
    }
    $condition = " where a.uniacid = :uniacid ";
    $params = array(":uniacid" => $_W["uniacid"]);
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and a.status = :status ";
        $params[":status"] = $status;
    }
    $exchange_cost = isset($filter["exchange_cost"]) ? intval($filter["exchange_cost"]) : -1;
    if ($exchange_cost == 0) {
        $condition .= " and a.exchange_cost = :exchange_cost ";
        $params[":exchange_cost"] = $exchange_cost;
    } else {
        if ($exchange_cost == 1) {
            $condition .= " and a.exchange_cost > 0 ";
            $filter["can_exchange"] = 1;
        }
    }
    $can_exchange = isset($filter["can_exchange"]) ? intval($filter["can_exchange"]) : -1;
    if (-1 < $can_exchange) {
        $condition .= " and a.can_exchange = :can_exchange ";
        $params[":can_exchange"] = $can_exchange;
    }
    $sid = isset($filter["sid"]) ? intval($filter["sid"]) : -1;
    if (-1 < $sid) {
        $condition .= " and a.sid = :sid ";
        $params[":sid"] = $sid;
    } else {
        if ($sid == -2) {
            $condition .= " and a.sid > 0";
        }
    }
    if ($status == 1) {
        $condition .= " and a.starttime < :starttime and a.endtime > :endtime ";
        $params[":starttime"] = TIMESTAMP;
        $params[":endtime"] = TIMESTAMP;
    }
    $page = max(1, intval($filter["page"]));
    $psize = 0 < intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $data = array();
    if (!defined("IN_WXAPP") && !defined("IN_VUE")) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_svip_redpacket") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition, $params);
        $pager = pagination($total, $page, $psize);
        $data["pager"] = $pager;
    }
    $redpackets = pdo_fetchall("select a.*, b.title as store_title, b.logo, b.score, b.sailed from " . tablename("tiny_wmall_svip_redpacket") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " order by sid asc, discount desc, status desc,  addtime desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($redpackets)) {
        foreach ($redpackets as &$redpacket) {
            $redpacket["discount"] = floatval($redpacket["discount"]);
            $redpacket["condition"] = floatval($redpacket["condition"]);
            $redpacket["data"] = iunserializer($redpacket["data"]);
            $redpacket["logo"] = tomedia($redpacket["logo"]);
            $redpacket["starttime_cn"] = date("Y-m-d H:i:s", $redpacket["starttime"]);
            $redpacket["endtime_cn"] = date("Y-m-d H:i:s", $redpacket["endtime"]);
            $redpacket["exchange_cost"] = floatval($redpacket["exchange_cost"]);
            if (0 < $redpacket["sid"]) {
                $redpacket["store"] = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $redpacket["sid"]), array("title"));
            }
            if ($filter["get_activity"] == 1 && 0 < $redpacket["sid"]) {
                $redpacket["activity"] = store_fetch_activity($redpacket["sid"], array("discount"));
            }
        }
    }
    $data["redpackets"] = $redpackets;
    return $data;
}
function svip_redpacket_fetch($id, $store_info = false)
{
    global $_W;
    $redpacket = pdo_get("tiny_wmall_svip_redpacket", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (!empty($redpacket)) {
        $redpacket["data"] = iunserializer($redpacket["data"]);
        $redpacket["exchange_cost"] = floatval($redpacket["exchange_cost"]);
        $redpacket["discount"] = floatval($redpacket["discount"]);
        $redpacket["condition"] = floatval($redpacket["condition"]);
        if (0 < $redpacket["sid"] && $store_info) {
            $redpacket["activity"] = store_fetch_activity($redpacket["sid"], array("discount"));
            $hot_goods = pdo_fetchall("select id,title,price,old_price,thumb from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3", array(":uniacid" => $_W["uniacid"], ":sid" => $redpacket["sid"]));
            if (!empty($hot_goods)) {
                foreach ($hot_goods as &$goods) {
                    $goods["thumb"] = tomedia($goods["thumb"]);
                }
            }
            $redpacket["hot_goods"] = $hot_goods;
        }
    }
    return $redpacket;
}
function svip_redpacket_status($type = -1, $key = "all")
{
    $data = array(array("text" => "已结束或已撤销", "css" => "label label-default"), array("text" => "进行中", "css" => "label label-success"), array("text" => "未开始", "css" => "label label-warning"));
    if ($type == -1) {
        return $data;
    }
    if ($key == "all") {
        return $data[$type];
    }
    if ($key == "text") {
        return $data[$type]["text"];
    }
    if ($key == "css") {
        return $data[$type]["css"];
    }
}
/**
 * 红包领取
 * redpacketOrId 待领取的红包或者红包id
 * exchange_cost 是否为奖励金兑换 1是开启奖励金兑换
 **/
function svip_redpacket_exchage($redpacketOrId, $exchange_cost = 0)
{
    global $_W;
    global $_GPC;
    $redpacket = $redpacketOrId;
    if (!is_array($redpacket)) {
        $redpacket = svip_redpacket_fetch($redpacketOrId);
    }
    if (empty($redpacket)) {
        return error(-1, "红包不存在或已删除");
    }
    if ($_W["member"]["svip_status"] != 1) {
        return error(-1, "您还未开通超级会员");
    }
    if ($redpacket["status"] == 0 || $redpacket["endtime"] < TIMESTAMP) {
        return error(-1, "该红包活动已结束或已撤销");
    }
    if ($redpacket["status"] == 2 || TIMESTAMP < $redpacket["starttime"]) {
        return error(-1, "该红包活动还未开始");
    }
    if ($exchange_cost != 1) {
        $num_member = svip_member_exchange_redpacket_num();
        if (is_error($num_member)) {
            return $num_member;
        }
        $config = get_plugin_config("svip.basic");
        $exchange_max = intval($config["exchange_max"]);
        if ($exchange_max <= $num_member) {
            return error(-1, "您本月领取次数已达上限");
        }
    }
    $num_redpacket = svip_redpacket_day_exchange_num($redpacket);
    if ($redpacket["amount"] <= $num_redpacket) {
        return error(-1, "该红包的今日领取次数已达上限");
    }
    $channel = "svip";
    if ($exchange_cost == 1 && 0 < $redpacket["exchange_cost"] && $redpacket["can_exchange"] == 1) {
        $title = "兑换" . $redpacket["discount"] . "元平台红包";
        $remark = "";
        if (0 < $redpacket["sid"]) {
            $title = "兑换" . $redpacket["discount"] . "元商家红包";
            $remark = "商家：" . $redpacket["title"];
        }
        $minus = svip_member_svip_credit1_update($_W["member"]["uid"], 0 - $redpacket["exchange_cost"], $title, $remark);
        if (is_error($minus)) {
            return $minus;
        }
        $channel = "svip_exchange";
    }
    $data = array("title" => $redpacket["title"], "channel" => $channel, "type" => "grant", "discount" => $redpacket["discount"], "days_limit" => $redpacket["use_days_limit"], "condition" => $redpacket["condition"], "uid" => $_W["member"]["uid"], "activity_id" => $redpacket["id"], "sid" => $redpacket["sid"], "discount_bear" => $redpacket["data"]["discount_bear"]);
    mload()->model("redPacket");
    $status = redPacket_grant($data, false);
    if (is_error($status)) {
        return $status;
    }
    return $status;
}
function svip_present_month()
{
    global $_W;
    $member = $_W["member"];
    if ($member["svip_endtime"] < TIMESTAMP) {
        $member["svip_status"] = 0;
    }
    if ($member["svip_status"] != 1) {
        return error(-1, "您还未开通超级会员");
    }
    $svip_starttime = $member["svip_starttime"];
    $svip_endtime = $member["svip_endtime"];
    $svip_month = intval(($svip_endtime - $svip_starttime) / (31 * 24 * 3600));
    $present_month = intval((TIMESTAMP - $svip_starttime) / (31 * 24 * 3600));
    $starttime = $svip_starttime + $present_month * 31 * 24 * 3600;
    if ($svip_month == $present_month) {
        $endtime = $svip_endtime;
    } else {
        if ($present_month < $svip_month) {
            $endtime = $starttime + 31 * 24 * 3600;
        }
    }
    $result = array("starttime" => $starttime, "endtime" => $endtime, "startday" => date("Y-m-d", $starttime), "endday" => date("Y-m-d", $endtime));
    return $result;
}
function svip_member_exchange_redpacket_num()
{
    global $_W;
    $month = svip_present_month();
    if (is_error($month)) {
        return $month;
    }
    $starttime = $month["starttime"];
    $endtime = $month["endtime"];
    $num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and uid = :uid and channel = :channel and granttime >= :starttime and granttime < :endtime", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "svip", ":starttime" => $starttime, ":endtime" => $endtime));
    return $num;
}
function svip_member_exchange_redpackets()
{
    global $_W;
    $month = svip_present_month();
    if (is_error($month)) {
        return $month;
    }
    $starttime = $month["starttime"];
    $endtime = $month["endtime"];
    $condition = " where uniacid = :uniacid and uid = :uid and channel = :channel and granttime >= :starttime and granttime < :endtime ";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "svip", ":starttime" => $starttime, ":endtime" => $endtime);
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_redpacket_record") . " " . $condition . " order by granttime asc", $params);
    if (!empty($data)) {
        foreach ($data as &$da) {
            $da["discount"] = floatval($da["discount"]);
        }
    }
    return $data;
}
function svip_redpacket_day_exchange_num($redpacketOrId)
{
    global $_W;
    $redpacket = $redpacketOrId;
    if (!is_array($redpacket)) {
        $redpacket = svip_redpacket_fetch($redpacketOrId);
    }
    $starttime = strtotime(date("Y-m-d", TIMESTAMP));
    $endtime = $starttime + 86399;
    $num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and activity_id = :activity_id and channel = :channel and granttime >= :starttime and granttime < :endtime", array(":uniacid" => $_W["uniacid"], ":activity_id" => $redpacket["id"], ":channel" => "svip", ":starttime" => $starttime, ":endtime" => $endtime));
    return $num;
}
function svip_member_svip_credit1_update($uid, $value, $title, $remark)
{
    global $_W;
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), array("id", "uid", "svip_credit1"));
    if (empty($member)) {
        return error(-1, "会员不存在");
    }
    $value = floatval($value);
    if (empty($value)) {
        return true;
    }
    $svip_credit1 = $value + $member["svip_credit1"];
    if (0 < $value || 0 <= $svip_credit1) {
        pdo_update("tiny_wmall_members", array("svip_credit1" => $svip_credit1), array("uniacid" => $_W["uniacid"], "uid" => $uid));
        $insert = array("uniacid" => $_W["uniacid"], "uid" => $member["uid"], "title" => $title, "num" => $value, "createtime" => TIMESTAMP, "remark" => $remark);
        pdo_insert("tiny_wmall_member_credit_record", $insert);
    } else {
        return error("-1", "奖励金不足");
    }
}
function svip_redpacket_record_fetchall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    } else {
        $filter = array_merge($_GPC, $filter);
    }
    $condition = " where uniacid = :uniacid and uid = :uid and channel = :channel ";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "svip");
    $page = max(1, intval($filter["page"]));
    $psize = 0 < intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $data = pdo_fetchall("select sid, channel, uid, discount, granttime from " . tablename("tiny_wmall_activity_redpacket_record") . " " . $condition . " order by granttime desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$da) {
            $da["title"] = "会员专享红包";
            if (0 < $da["sid"]) {
                $da["title"] = "兑换专享商家红包";
            }
            $da["granttime_cn"] = date("Y-m-d H:i", $da["granttime"]);
        }
    }
    return $data;
}
function svip_credit1_record_fetchall()
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    } else {
        $filter = array_merge($_GPC, $filter);
    }
    $condition = " where uniacid = :uniacid and uid = :uid ";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $page = max(1, intval($filter["page"]));
    $psize = 0 < intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_member_credit_record") . " " . $condition . " order by createtime desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$da) {
            $da["createtime_cn"] = date("Y-m-d H:i", $da["createtime"]);
        }
    }
    return $data;
}
function svip_task_types($type = "")
{
    $types = array("oneChargeFee" => array("type" => "oneChargeFee", "title" => "单笔充值满额", "css" => "label label-success", "achieve_text" => "去充值", "achieve_link" => "/pages/member/recharge"), "oneOrderFee" => array("type" => "oneOrderFee", "title" => "单笔外卖满额", "css" => "label label-info", "achieve_text" => "去完成", "achieve_link" => "/pages/home/index"), "oneErranderFee" => array("type" => "oneErranderFee", "title" => "单笔跑腿满额", "css" => "label label-warning", "achieve_text" => "去完成", "achieve_link" => "/pages/paotui/guide"));
    if (!empty($type)) {
        return $types[$type];
    }
    return $types;
}
function svip_task_status($status = -1)
{
    $status_all = array(array("css" => "label label-danger", "text" => "已结束"), array("css" => "label label-success", "text" => "进行中"), array("css" => "label label-warning", "text" => "未开始"));
    if (0 <= $status) {
        return $status_all[$status];
    }
    return $status_all;
}
function svip_task_takepart_status($status = 0)
{
    $status_all = array("1" => array("css" => "label label-warning", "text" => "进行中"), "2" => array("css" => "label label-success", "text" => "已完成"), "3" => array("css" => "label label-danger", "text" => "未完成"));
    if (0 < $status) {
        return $status_all[$status];
    }
    return $status_all;
}
function svip_task_getall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $task_type = trim($filter["task_type"]);
    if (!empty($task_type)) {
        $condition .= " and type = :type";
        $params[":type"] = $task_type;
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($filter["page"]));
    $psize = isset($filter["psize"]) ? intval($filter["psize"]) : 15;
    $tasks = pdo_fetchall("select * from " . tablename("tiny_wmall_svip_task") . $condition . " order by displayorder desc, id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    $result = array();
    if (defined("IN_WXAPP") || defined("IN_VUE")) {
        if (!empty($tasks)) {
            $award_types = array("credit1" => "积分", "credit2" => "元", "svip_credit1" => "奖励金");
            $task_types = svip_task_types();
            foreach ($tasks as &$val) {
                $val["data"] = json_decode(base64_decode($val["data"]), true);
                $val["award_cn"] = "";
                if (!empty($val["data"]["award"])) {
                    foreach ($val["data"]["award"] as $key => $award) {
                        if ($key == "redpackets" && !empty($award)) {
                            $award = array_values($award);
                            $val["award_cn"] .= "+" . $award[0]["discount"] . "元红包 ";
                        } else {
                            if (0 < $award) {
                                $val["award_cn"] .= "+" . $award . $award_types[$key] . " ";
                            }
                        }
                    }
                }
                $val["starttime_cn"] = date("Y-m-d H:i", $val["starttime"]);
                $val["endtime_cn"] = date("Y-m-d H:i", $val["endtime"]);
                $val["thumb"] = tomedia($val["data"]["thumb"]);
                $is_going = pdo_get("tiny_wmall_svip_task_records", array("uniacid" => $_W["uniacid"], "task_id" => $val["id"], "uid" => $_W["member"]["uid"], "status" => 1), array("id"));
                if (!empty($is_going)) {
                    $task_type = $task_types[$val["type"]];
                    $val["button"] = array("text" => $task_type["achieve_text"], "link" => $task_type["achieve_link"], "link_type" => 1);
                } else {
                    $val["button"] = array("text" => "领任务", "link_type" => 0);
                }
            }
        }
    } else {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_svip_task") . $condition, $params);
        $pager = pagination($total, $page, $psize);
        $result["pager"] = $pager;
    }
    $result["tasks"] = $tasks;
    return $result;
}
function svip_task_get($id)
{
    global $_W;
    $task = pdo_get("tiny_wmall_svip_task", array("uniacid" => $_W["uniacid"], "id" => $id));
    $task["data"] = json_decode(base64_decode($task["data"]), true);
    return $task;
}
function svip_task_takepart_check($taskOrId)
{
    global $_W;
    if ($_W["member"]["svip_status"] != 1) {
        return error(-1, "您还未开启超级会员，不能领取任务");
    }
    if (!is_array($taskOrId)) {
        $task = svip_task_get($taskOrId);
    }
    if (empty($task)) {
        return error(-1, "任务不存在或已删除");
    }
    if ($task["endtime"] < TIMESTAMP) {
        return error(-1, "任务已结束");
    }
    if (TIMESTAMP < $task["starttime"]) {
        return error(-1, "任务未开始");
    }
    $data = $task["data"];
    $task_takepart_type = $data["task_takepart_type"];
    if (0 < $task_takepart_type) {
        $is_exist = pdo_get("tiny_wmall_svip_task_records", array("uniacid" => $_W["uniacid"], "task_id" => $task["id"], "uid" => $_W["member"]["uid"]), array("id", "status", "endtime", "overtime"));
        if (empty($is_exist)) {
            return $task;
        }
        if ($task_takepart_type == 1) {
            if ($is_exist) {
                return error(-1, "您已参与过该任务，不能再次参加");
            }
        } else {
            if ($task_takepart_type == 2) {
                if (!empty($is_exist)) {
                    if ($is_exist["status"] == 1) {
                        return error(-1, "您已领取该任务，请先完成后才可再次领取");
                    }
                    if ($is_exist["status"] == 2 || $is_exist["status"] == 3) {
                        $last_endtime = $is_exist["status"] == 2 ? $is_exist["endtime"] : $is_exist["overtime"];
                        if (TIMESTAMP - $last_endtime < $data["takepart_repeat_time"] * 3600) {
                            return error(-1, "您已参加过该任务，任务结束后" . $data["takepart_repeat_time"] . "小时才可再次领取");
                        }
                    }
                }
            } else {
                if ($task_takepart_type == 3 && !empty($is_exist)) {
                    if ($is_exist["status"] == 1) {
                        return error(-1, "您已领取该任务，请先完成后才可再次领取");
                    }
                    if ($is_exist["status"] == 2 || $is_exist["status"] == 3) {
                        $last_endtime = $is_exist["status"] == 2 ? $is_exist["endtime"] : $is_exist["overtime"];
                        if ($data["takepart_repeat_time"] == "tomorrow") {
                            $repeat_start_day = date("Ymd", strtotime("+1 day", $last_endtime));
                            $repeat_start_day_cn = "明天";
                        } else {
                            if ($data["takepart_repeat_time"] == "week") {
                                $repeat_start_day = date("Ymd", strtotime("next Monday", $last_endtime));
                                $repeat_start_day_cn = "下周";
                            } else {
                                if ($data["takepart_repeat_time"] == "month") {
                                    $repeat_start_day = date("Ymd", strtotime("first day of next month", $last_endtime));
                                    $repeat_start_day_cn = "下个月";
                                }
                            }
                        }
                        $today = date("Ymd");
                        if ($today < $repeat_start_day) {
                            return error(-1, "您已参加过该任务，" . $repeat_start_day_cn . "才可再次领取");
                        }
                    }
                }
            }
        }
    } else {
        if ($task_takepart_type == 0) {
            $is_exist = pdo_get("tiny_wmall_svip_task_records", array("uniacid" => $_W["uniacid"], "task_id" => $task["id"], "uid" => $_W["member"]["uid"], "status" => 1), array("id"));
            if (!empty($is_exist)) {
                return error(-1, "您已领取该任务，请先完成后才可再次领取");
            }
        }
    }
    return $task;
}
function svip_task_finish_check($uid, $task_type, $order = array())
{
    global $_W;
    $records = pdo_get("tiny_wmall_svip_task_records", array("uniacid" => $_W["uniacid"], "task_type" => $task_type, "uid" => $uid, "status" => 1));
    if (empty($records)) {
        return error(-1, "未参与活动");
    }
    $task = svip_task_get($records["task_id"]);
    if (empty($task)) {
        return error(-1, "活动不存在");
    }
    $task_data = iunserializer($records["data"]);
    if (in_array($task_type, array("oneOrderFee", "oneChargeFee", "oneErranderFee")) && $order["final_fee"] < $task_data["condition"]) {
        return error(-1, "不满足任务条件");
    }
    $task_endtime_type = $task_data["task_endtime_type"];
    if (0 < $task_endtime_type && $records["overtime"] < TIMESTAMP) {
        pdo_update("tiny_wmall_svip_task_records", array("status" => 3), array("uniacid" => $_W["uniacid"], "id" => $records["id"]));
        return error(-1, "完成任务超时");
    }
    $award = $task_data["award"];
    if (!empty($award)) {
        foreach ($award as $key => $val) {
            if ($key == "redpackets") {
                $val = array_values($val);
                $redpacket = $val[0];
                $params = array("title" => $redpacket["name"], "activity_id" => $task["id"], "uid" => $uid, "channel" => "svip_task", "type" => "task", "discount" => $redpacket["discount"], "condition" => $redpacket["condition"], "grant_days_effect" => $redpacket["grant_days_effect"], "days_limit" => $redpacket["use_days_limit"], "is_show" => 1, "scene" => $redpacket["scene"]);
                $times_limit = array();
                if (!empty($redpacket["times"])) {
                    foreach ($redpacket["times"] as $time) {
                        if ($time["start_hour"] && $time["end_hour"]) {
                            $times_limit[] = $time;
                        }
                    }
                }
                if (!empty($times_limit)) {
                    $params["times_limit"] = iserializer($times_limit);
                }
                $category_limit = array();
                if (!empty($redpacket["categorys"])) {
                    foreach ($redpacket["categorys"] as $category) {
                        $category_limit[] = $category["id"];
                    }
                }
                $params["category_limit"] = implode("|", $category_limit);
                mload()->model("redPacket");
                redPacket_grant($params, false);
            } else {
                if (0 < $val) {
                    if ($key == "credit1" || $key == "credit2") {
                        mload()->model("member");
                        member_credit_update($uid, $key, $val, array(), false);
                    } else {
                        svip_member_svip_credit1_update($uid, $val, "会员任务奖励", "任务名称：" . $task["title"]);
                    }
                }
            }
        }
    }
    pdo_update("tiny_wmall_svip_task_records", array("status" => 2, "endtime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "id" => $records["id"]));
    return true;
}
function svip_store_useful_redpacket_get($sid, $discount_max = 0, $extra = array())
{
    global $_W;
    if (empty($sid) || empty($discount_max)) {
        return false;
    }
    if ($_W["member"]["svip_status"] != 1 && empty($extra["is_buysvip"])) {
        return false;
    }
    $config = get_plugin_config("svip.basic");
    if ($extra["is_buysvip"] == 1) {
        $num_taked = 0;
    } else {
        $num_taked = svip_member_exchange_redpacket_num();
    }
    $num_max = $config["exchange_max"];
    $num_taking = $num_max - $num_taked;
    $condition = " where a.uniacid = :uniacid and a.status = :status and a.sid = :sid and a.discount <= :discount and a.condition <= :discount ";
    $params = array(":uniacid" => $_W["uniacid"], ":status" => 1, ":sid" => $sid, ":discount" => $discount_max);
    $store_redpacket = pdo_fetch("select a.*, b.title as store_title, b.logo from " . tablename("tiny_wmall_svip_redpacket") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " ", $params);
    if (!empty($store_redpacket)) {
        $store_redpacket["discount"] = floatval($store_redpacket["discount"]);
        $store_redpacket["logo"] = tomedia($store_redpacket["logo"]);
        $store_redpacket["endtime_cn"] = date("Y-m-d", $store_redpacket["endtime"]);
        if (0 < $num_taking) {
            $plateform_redpacket = svip_plateform_redpacket_get($store_redpacket["discount"]);
            if (!empty($plateform_redpacket)) {
                $plateform_redpacket["store_redpacket"] = $store_redpacket;
                $plateform_redpacket["take_status"] = "exchange";
                $plateform_redpacket["exchange_id"] = 0;
                $plateform_redpacket["desc"] = "您可将此红包升级为¥" . $store_redpacket["discount"] . "红包";
                return $plateform_redpacket;
            }
            $store_redpacket["take_status"] = "take";
            $store_redpacket["take_id"] = $store_redpacket["id"];
            $store_redpacket["desc"] = (string) $store_redpacket["store_title"] . "门店专享";
            return $store_redpacket;
        }
        $member_redpacket = svip_available_plateform_redpacket_get($store_redpacket["discount"]);
        if (!empty($member_redpacket)) {
            $member_redpacket["store_redpacket"] = $store_redpacket;
            $member_redpacket["take_status"] = "exchange";
            $member_redpacket["exchange_id"] = $member_redpacket["id"];
            $member_redpacket["desc"] = "您可将此红包升级为¥" . $store_redpacket["discount"] . "红包";
        }
        return $member_redpacket;
    }
    if (0 < $num_taking) {
        $plateform_redpacket = svip_plateform_redpacket_get($discount_max, "desc");
        if (!empty($plateform_redpacket)) {
            $plateform_redpacket["take_status"] = "take";
            $plateform_redpacket["take_id"] = $plateform_redpacket["id"];
            $plateform_redpacket["desc"] = "平台通用";
        }
        return $plateform_redpacket;
    }
    return false;
}
function svip_store_redpacket_exchange($storeRedpacketId, $plateformRedpacketId)
{
    global $_W;
    $redpacket_store = svip_redpacket_fetch($storeRedpacketId);
    if (empty($redpacket_store)) {
        return error(-1, "门店红包不存在");
    }
    if ($redpacket_store["status"] == 0) {
        return error(-1, "该红包活动已结束或已撤销");
    }
    if ($redpacket_store["status"] == 2) {
        return error(-1, "该红包活动还未开始");
    }
    $num_redpacket = svip_redpacket_day_exchange_num($redpacket_store);
    if ($redpacket_store["amount"] <= $num_redpacket) {
        return error(-1, "该红包的今日领取次数已达上限");
    }
    $redpacket_plateform = pdo_get("tiny_wmall_activity_redpacket_record", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "id" => $plateformRedpacketId));
    if (empty($redpacket_plateform)) {
        return error(-1, "会员专享红包不存在");
    }
    if ($redpacket_plateform["status"] == 2) {
        return error(-1, "该会员专享红包已使用");
    }
    if ($redpacket_plateform["status"] == 3) {
        return error(-1, "该会员专享红包已过期");
    }
    pdo_delete("tiny_wmall_activity_redpacket_record", array("uniacid" => $_W["uniacid"], "id" => $redpacket_plateform["id"]));
    $data = array("title" => $redpacket_store["title"], "channel" => "svip", "type" => "grant", "discount" => $redpacket_store["discount"], "days_limit" => $redpacket_store["use_days_limit"], "condition" => $redpacket_store["condition"], "uid" => $_W["member"]["uid"], "activity_id" => $redpacket_store["id"], "sid" => $redpacket_store["sid"], "discount_bear" => $redpacket_store["data"]["discount_bear"]);
    mload()->model("redPacket");
    $status = redPacket_grant($data, false);
    return $status;
}
function svip_available_plateform_redpacket_get($discount)
{
    global $_W;
    $condition = " where uniacid = :uniacid and uid = :uid and channel = :channel and sid = :sid and status = :status and discount < :discount and `condition` <= :discount ";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "svip", ":sid" => 0, ":status" => 1, ":discount" => $discount);
    $redpacket = pdo_fetch("select * from " . tablename("tiny_wmall_activity_redpacket_record") . " " . $condition . " order by discount asc, endtime asc ", $params);
    if (!empty($redpacket)) {
        $redpacket["discount"] = floatval($redpacket["discount"]);
        $redpacket["endtime_cn"] = date("Y-m-d", $redpacket["endtime"]);
    }
    return $redpacket;
}
function svip_plateform_redpacket_get($discount_max, $discountOrder = "asc")
{
    global $_W;
    $condition = " where uniacid = :uniacid and status = :status and sid = :sid and discount < :discount and `condition` <= :discount ";
    $params = array(":uniacid" => $_W["uniacid"], ":status" => 1, ":sid" => 0, ":discount" => $discount_max);
    $redpacket = pdo_fetch("select * from " . tablename("tiny_wmall_svip_redpacket") . " " . $condition . " order by discount " . $discountOrder . ", endtime desc ", $params);
    if (!empty($redpacket)) {
        $redpacket["discount"] = floatval($redpacket["discount"]);
        $redpacket["endtime_cn"] = date("Y-m-d", $redpacket["endtime"]);
    }
    return $redpacket;
}
function svip_task_takepart_records($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $task_type = trim($filter["task_type"]);
    if (!empty($task_type)) {
        $condition .= " and a.task_type = :task_type";
        $params[":task_type"] = $task_type;
    }
    $uid = intval($filter["uid"]);
    if (0 < $uid) {
        $condition .= " and a.uid = :uid";
        $params[":uid"] = $uid;
    }
    $task_id = intval($filter["task_id"]);
    if (0 < $task_id) {
        $condition .= " and a.task_id = :task_id";
        $params[":task_id"] = $task_id;
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (0 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    if (!empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " AND a.endtime > :start AND a.endtime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $page = max(1, intval($filter["page"]));
    $psize = isset($filter["psize"]) ? intval($filter["psize"]) : 15;
    $records = pdo_fetchall("select a.*, b.realname, b.avatar,b.mobile from " . tablename("tiny_wmall_svip_task_records") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    $result = array();
    if (!empty($records)) {
        $award_types = array("credit1" => "积分", "credit2" => "元", "svip_credit1" => "奖励金");
        $task_types = svip_task_types();
        $takepart_status = svip_task_takepart_status();
        foreach ($records as &$val) {
            $val["data"] = iunserializer($val["data"]);
            $val["award_cn"] = "";
            if (!empty($val["data"]["award"])) {
                foreach ($val["data"]["award"] as $key => $award) {
                    if ($key == "redpackets" && !empty($award)) {
                        $award = array_values($award);
                        $val["award_cn"] .= "+" . $award[0]["discount"] . "元红包 ";
                    } else {
                        if (0 < $award) {
                            $val["award_cn"] .= "+" . $award . $award_types[$key] . " ";
                        }
                    }
                }
            }
            $val["task_type_all"] = $task_types[$val["task_type"]];
            $val["takepart_status_all"] = $takepart_status[$val["status"]];
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            $val["endtime_cn"] = empty($val["endtime"]) ? "" : date("Y-m-d H:i", $val["endtime"]);
            $val["overtime_cn"] = empty($val["overtime"]) ? "无限制" : date("Y-m-d H:i", $val["overtime"]);
            $val["avatar"] = tomedia($val["avatar"]);
        }
    }
    if (!defined("IN_WXAPP") && !defined("IN_VUE")) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_svip_task_records") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition, $params);
        $pager = pagination($total, $page, $psize);
        $result["pager"] = $pager;
    }
    $result["records"] = $records;
    return $result;
}
function svip_member_redpacket_total()
{
    global $_W;
    $total = pdo_fetchcolumn("select sum(discount) from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and uid = :uid and channel = :channel ", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":channel" => "svip"));
    return floatval($total);
}
function svip_before_timeout_notice()
{
    global $_W;
    $svip_config = get_plugin_config("svip.basic");
    $notice_before_overtime = intval($svip_config["notice_before_overtime"]);
    if (empty($notice_before_overtime)) {
        return true;
    }
    $svip_members = pdo_fetchall("select uid, nickname, openid, svip_endtime from" . tablename("tiny_wmall_members") . " where uniacid = :uniacid and svip_status = 1 and svip_notice_times = 0 and svip_endtime < :endtime", array(":uniacid" => $_W["uniacid"], ":endtime" => TIMESTAMP + $notice_before_overtime * 86400));
    if (!empty($svip_members)) {
        $config = $_W["we7_wmall"]["config"];
        foreach ($svip_members as $item) {
            $params = array("first" => (string) $item["nickname"] . "，您的【" . $config["title"] . "】会员即将到期，到期后不再享有会员特权，记得续费哦~", "keyword1" => "会员变动", "keyword2" => "", "keyword3" => date("Y-m-d H:i", TIMESTAMP), "keyword4" => "您的会员即将到期", "remark" => implode("\n", array("到期时间:" . date("Y-m-d", $item["svip_endtime"]), "感谢您对" . $config["title"] . "平台的支持与厚爱,期待您继续成为我们的会员。点击续费>>")));
            $send = sys_wechat_tpl_format($params);
            $acc = WeAccount::create($_W["acid"]);
            $url = ivurl("package/pages/svip/purchase", array(), true);
            $status = $acc->sendTplNotice($item["openid"], $config["notice"]["wechat"]["account_change_tpl"], $send, $url);
            if (is_error($status)) {
                slog("wxtplNotice", "会员到期提醒", $send, $status["message"]);
            }
            pdo_update("tiny_wmall_members", array("svip_notice_times" => 1), array("uniacid" => $_W["uniacid"], "uid" => $item["uid"]));
        }
    }
    return true;
}
function svip_task_cron()
{
    global $_W;
    pdo_query("update " . tablename("tiny_wmall_svip_task") . " set status = 0 where uniacid = :uniacid and status = 1 and endtime <= :endtime", array(":uniacid" => $_W["uniacid"], ":endtime" => TIMESTAMP));
    pdo_query("update " . tablename("tiny_wmall_svip_task") . " set status = 1 where uniacid = :uniacid and status = 2 and starttime >= :starttime", array(":uniacid" => $_W["uniacid"], ":starttime" => TIMESTAMP));
    pdo_query("update " . tablename("tiny_wmall_svip_task_records") . " set status = 3 where uniacid = :uniacid and status = 1 and overtime > 0 and overtime <= :endtime", array(":uniacid" => $_W["uniacid"], ":endtime" => TIMESTAMP));
}
function svip_code_status($type = -1, $key = "all")
{
    $data = array("1" => array("text" => "待兑换", "css" => "label label-danger"), "2" => array("text" => "已兑换", "css" => "label label-success"), "3" => array("text" => "已过期", "css" => "label label-default"));
    if ($type == -1) {
        return $data;
    }
    if ($key == "all") {
        return $data[$type];
    }
    if ($key == "text") {
        return $data[$type]["text"];
    }
    if ($key == "css") {
        return $data[$type]["css"];
    }
}
function svip_code_fetchall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    } else {
        $filter = array_merge($_GPC, $filter);
    }
    $condition = " where a.uniacid = :uniacid ";
    $params = array(":uniacid" => $_W["uniacid"]);
    $uid = intval($filter["uid"]);
    if (0 < $uid) {
        $condition .= " and a.uid = :uid ";
        $params[":uid"] = $uid;
    }
    $status = intval($filter["status"]);
    if (0 < $status) {
        $condition .= " and a.status = :status ";
        $params[":status"] = $status;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (b.mobile like :keyword or b.realname like :keyword) ";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    if ($status == 2 && !empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " and a.exchangetime > :start AND a.exchangetime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $pindex = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $codes = pdo_fetchall("select a.*, b.realname, b.avatar from " . tablename("tiny_wmall_svip_code") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition . " order by status asc, id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($codes)) {
        $all_status = svip_code_status();
        foreach ($codes as &$code) {
            $code["status_cn"] = $all_status[$code["status"]]["text"];
            $code["endtime_cn"] = date("Y-m-d H:i", $code["endtime"]);
            if ($code["status"] == 2) {
                $code["exchangetime_cn"] = date("Y-m-d H:i", $code["exchangetime"]);
            } else {
                if ($code["status"] == 3) {
                    $code["exchangetime_cn"] = "兑换码未被使用，过期了,哈哈";
                } else {
                    $code["exchangetime_cn"] = "未兑换";
                }
            }
        }
    }
    $result = array("codes" => $codes);
    if (!defined("IN_WXAPP") && !defined("IN_VUE")) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_svip_code") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $result["pager"] = $pager;
    }
    return $result;
}
function svip_code_exchange($code, $uid)
{
    global $_W;
    if (empty($code) || empty($uid)) {
        return error(-1, "参数错误");
    }
    $svip_code = pdo_get("tiny_wmall_svip_code", array("uniacid" => $_W["uniacid"], "code" => $code));
    if (empty($svip_code)) {
        return error(-1, "兑换码不存在");
    }
    if ($svip_code["status"] == 2) {
        return error(-1, "兑换码已被使用");
    }
    if ($svip_code["status"] == 3 || $svip_code["endtime"] < TIMESTAMP) {
        return error(-1, "兑换码已过期");
    }
    $update = array("uid" => $uid, "status" => 2, "exchangetime" => TIMESTAMP);
    pdo_update("tiny_wmall_svip_code", $update, array("uniacid" => $_W["uniacid"], "code" => $code));
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), array("svip_status", "svip_starttime", "svip_endtime"));
    if (empty($member)) {
        return error(-1, "会员不存在或已删");
    }
    $update_member = array("svip_status" => 1, "svip_starttime" => TIMESTAMP, "svip_endtime" => TIMESTAMP + $svip_code["days"] * 86400);
    if (TIMESTAMP <= $member["svip_endtime"]) {
        $update_member["svip_starttime"] = $member["svip_starttime"];
        $update_member["svip_endtime"] = $member["svip_endtime"] + $svip_code["days"] * 86400;
    }
    pdo_update("tiny_wmall_members", $update_member, array("uniacid" => $_W["uniacid"], "uid" => $uid));
    return true;
}

?>