<?php
defined("IN_IA") or exit("Access Denied");

function gohome_goods_sync($type = "kanjia")
{
    global $_W;
    $routers = array("kanjia" => "tiny_wmall_kanjia", "pintuan" => "tiny_wmall_pintuan_goods", "seckill" => "tiny_wmall_seckill_goods");
    $table = $routers[$type];
    pdo_query("update " . tablename($table) . " set status = 1 where uniacid = :uniacid and status > 0 and starttime < :starttime and endtime > :endtime", array(":uniacid" => $_W["uniacid"], ":starttime" => TIMESTAMP, ":endtime" => TIMESTAMP));
    pdo_query("update " . tablename($table) . " set status = 2 where uniacid = :uniacid and status > 0 and starttime > :starttime", array(":uniacid" => $_W["uniacid"], ":starttime" => TIMESTAMP));
    pdo_query("update " . tablename($table) . " set status = 3 where uniacid = :uniacid and status > 0 and endtime < :endtime", array(":uniacid" => $_W["uniacid"], ":endtime" => TIMESTAMP));
    return true;
}
function gohome_goods_total_update($order, $scene)
{
    $routers = array("kanjia" => "tiny_wmall_kanjia", "pintuan" => "tiny_wmall_pintuan_goods", "seckill" => "tiny_wmall_seckill_goods");
    $table = $routers[$order["order_type"]];
    pdo_query("update " . tablename($table) . " set total = total - " . $order["num"] . " where uniacid = :uniacid and id = :id and total > 0 and total_update_type = :total_update_type", array(":uniacid" => $order["uniacid"], ":id" => $order["goods_id"], ":total_update_type" => $scene));
    return true;
}
function gohome_order_fetch($id, $oauth = false)
{
    global $_W;
    $id = intval($id);
    $condition = " where uniacid = :uniacid and id = :id";
    $params = array(":uniacid" => $_W["uniacid"], ":id" => $id);
    if ($oauth) {
        $condition .= " and uid = :uid";
        $params[":uid"] = $_W["member"]["uid"];
    }
    $order = pdo_fetch("select * from " . tablename("tiny_wmall_gohome_order") . $condition, $params);
    if (empty($order)) {
        return false;
    }
    $order["addtime_cn"] = date("Y-m-d H:i:s", $order["addtime"]);
    $order["paytime_cn"] = date("Y-m-d H:i:s", $order["paytime"]);
    $pay_types = order_pay_types();
    if (empty($order["is_pay"])) {
        $order["pay_type_cn"] = "未支付";
    } else {
        $order["pay_type_cn"] = !empty($pay_types[$order["pay_type"]]["text"]) ? $pay_types[$order["pay_type"]]["text"] : "其他支付方式";
    }
    $order["order_type_all"] = gohome_order_types($order["order_type"], "all");
    $order["order_type_cn"] = $order["order_type_all"]["text"];
    $order["status_cn"] = gohome_order_status($order["status"], "text");
    $order["goods"] = gohome_order_goods($order["goods_id"], $order["order_type"]);
    $order["store"] = store_fetch($order["sid"], array("title", "logo", "telephone", "address", "push_token"));
    $order["plateform_serve"] = iunserializer($order["plateform_serve"]);
    $order["agent_serve"] = iunserializer($order["agent_serve"]);
    return $order;
}
function gohome_order_goods($goods_id, $order_type)
{
    global $_W;
    if (!in_array($order_type, array("kanjia", "pintuan", "seckill"))) {
        return false;
    }
    $goods_id = intval($goods_id);
    $table = array("kanjia" => "tiny_wmall_kanjia", "pintuan" => "tiny_wmall_pintuan_goods", "seckill" => "tiny_wmall_seckill_goods");
    $tablename = $table[$order_type];
    $goods = pdo_get($tablename, array("uniacid" => $_W["uniacid"], "id" => $goods_id));
    if (!empty($goods)) {
        $goods["thumb"] = tomedia($goods["thumb"]);
        $serializer = array("thumbs", "rules", "share");
        foreach ($serializer as $ser) {
            if (!empty($goods[$ser])) {
                $goods[$ser] = iunserializer($goods[$ser]);
            }
        }
        $float = array("oldprice", "price", "vipprice", "submitmoneylimit", "alongprice", "grouptime");
        foreach ($float as $flo) {
            if (!empty($goods[$flo])) {
                $goods[$flo] = floatval($goods[$flo]);
            }
        }
        $goods["starttime_cn"] = date("Y-m-d H:i:s", $goods["starttime"]);
        $goods["endtime_cn"] = date("Y-m-d H:i:s", $goods["endtime"]);
    }
    return $goods;
}
function gohome_goods_fetchall()
{
    global $_W;
    $goods_all = array();
    $table = array("kanjia" => "tiny_wmall_kanjia", "pintuan" => "tiny_wmall_pintuan_goods", "seckill" => "tiny_wmall_seckill_goods");
    $serializer = array("thumbs", "rules", "share");
    $float = array("oldprice", "price", "vipprice", "submitmoneylimit", "alongprice", "grouptime");
    $condition = " where uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    foreach ($table as $key => $value) {
        $data = pdo_fetchall("select * from " . tablename($value) . $condition, $params, "id");
        if (!empty($data)) {
            foreach ($data as &$goods) {
                $goods["thumb"] = tomedia($goods["thumb"]);
                foreach ($serializer as $ser) {
                    if (!empty($goods[$ser])) {
                        $goods[$ser] = iunserializer($goods[$ser]);
                    }
                }
                foreach ($float as $flo) {
                    if (!empty($goods[$flo])) {
                        $goods[$flo] = floatval($goods[$flo]);
                    }
                }
                $goods["starttime_cn"] = date("Y-m-d H:i:s", $goods["starttime"]);
                $goods["endtime_cn"] = date("Y-m-d H:i:s", $goods["endtime"]);
            }
        }
        $goods_all[$key] = $data;
    }
    return $goods_all;
}
function gohome_order_fetchall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($filter["agentid"]) ? intval($filter["agentid"]) : $_W["agentid"];
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $sid = intval($filter["sid"]);
    if (0 < $sid) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }
    $uid = intval($filter["uid"]);
    if (0 < $uid) {
        $condition .= " and uid = :uid";
        $params[":uid"] = $uid;
    }
    $order_type = trim($filter["order_type"]);
    if (in_array($order_type, array("kanjia", "pintuan", "seckill"))) {
        $condition .= " and order_type = :order_type";
        $params[":order_type"] = $order_type;
    }
    $is_pay = isset($filter["is_pay"]) ? intval($filter["is_pay"]) : -1;
    if (-1 < $is_pay) {
        $condition .= " and is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }
    $pay_type = trim($filter["pay_type"]);
    if (!empty($pay_type)) {
        $condition .= " and pay_type = :pay_type";
        $params[":pay_type"] = $pay_type;
    }
    $refund_status = intval($filter["refund_status"]);
    if (0 < $refund_status) {
        $condition .= " and refund_status = :refund_status";
        $params[":refund_status"] = $refund_status;
        $filter["status"] = 7;
    }
    $status = intval($filter["status"]);
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    if (!empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " AND addtime > :start AND addtime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (mobile like :keyword or username like :keyword or ordersn like :keyword or code like :keyword)";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_order") . $condition, $params);
    $orders = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_order") . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($orders)) {
        $stores = store_fetchall(array("id", "title", "logo", "telephone", "address"));
        foreach ($orders as &$order) {
            $order["addtime_cn"] = date("Y-m-d H:i:s", $order["addtime"]);
            if (0 < $order["paytime"]) {
                $order["paytime_cn"] = date("Y-m-d H:i:s", $order["paytime"]);
            }
            if ($order["applytime"]) {
                $order["applytime_cn"] = date("Y-m-d H:i:s", $order["applytime"]);
            }
            if (empty($order["is_pay"])) {
                $order["pay_type_all"] = array("text" => "未支付", "css" => "label label-default");
            } else {
                $order["pay_type_all"] = to_paytype($order["pay_type"], "all");
            }
            $order["order_type_all"] = gohome_order_types($order["order_type"], "all");
            $order["status_all"] = gohome_order_status($order["status"], "all");
            $order["goods"] = gohome_order_goods($order["goods_id"], $order["order_type"]);
            $order["store"] = $stores[$order["sid"]];
        }
    }
    $pager = pagination($total, $page, $psize);
    return array("orders" => $orders, "total" => $total, "pager" => $pager);
}
function gohome_order_types($type, $key = "all")
{
    $data = array("kanjia" => array("text" => "砍价", "css" => "label label-danger"), "pintuan" => array("text" => "拼团", "css" => "label label-info"), "seckill" => array("text" => "抢购", "css" => "label label-warning"));
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
function gohome_order_status($type = "", $key = "all")
{
    $data = array("1" => array("text" => "待付款", "css" => "label label-warning"), "2" => array("text" => "待生效", "css" => "label label-warning"), "3" => array("text" => "待使用", "css" => "label label-danger"), "4" => array("text" => "待收货", "css" => "label label-danger"), "5" => array("text" => "待评价", "css" => "label label-danger"), "6" => array("text" => "已完成", "css" => "label label-success"), "7" => array("text" => "已取消", "css" => "label label-default"));
    if (empty($type)) {
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
function gohome_goods_status($type = -1, $key = "all")
{
    $data = array(array("text" => "下架中", "css" => "label label-info"), array("text" => "进行中", "css" => "label label-success"), array("text" => "未开始", "css" => "label label-warning"), array("text" => "已结束", "css" => "label label-danger"));
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
function gohome_order_update($orderOrId, $type, $extra = array())
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = gohome_order_fetch($order);
    }
    if (empty($order)) {
        return error(-1, "订单不存在！");
    }
    if ($order["order_type"] == "kanjia") {
        mload()->model("plugin");
        pload()->model("kanjia");
        return kanjia_order_update($order, $type, $extra);
    }
    if ($order["order_type"] == "pintuan") {
        mload()->model("plugin");
        pload()->model("pintuan");
        return pintuan_order_update($order, $type, $extra);
    }
    if ($order["order_type"] == "seckill") {
        mload()->model("plugin");
        pload()->model("seckill");
        return seckill_order_update($order, $type, $extra);
    }
}
function gohome_order_update_bill($orderOrId)
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = gohome_order_fetch($order);
    }
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    $account = store_account($order["sid"], array("fee_gohome"));
    $account = $account["fee_gohome"];
    if (empty($account[$order["order_type"]])) {
        $account = get_plugin_config("gohome.serve_fee");
    }
    $fee_config = $account[$order["order_type"]];
    if ($fee_config["type"] == 2) {
        $plateform_serve_rate = 0;
        $platform_serve_fee = floatval($fee_config["fee"]);
        $plateform_serve = array("fee_type" => 2, "fee_rate" => 0, "fee" => $platform_serve_fee, "note" => "每单固定" . $platform_serve_fee . "元");
    } else {
        $basic = 0;
        $note = array("yes" => array(), "no" => array());
        $fee_items = store_serve_fee_items();
        if (!empty($fee_config["items_yes"])) {
            $fee_config["items_yes"] = array_unique($fee_config["items_yes"]);
            foreach ($fee_config["items_yes"] as $item) {
                $basic += $order[$item];
                $note["yes"][] = (string) $fee_items["yes"][$item] . " ￥" . $order[$item];
            }
        }
        if (!empty($fee_config["items_no"])) {
            $fee_config["items_no"] = array_unique($fee_config["items_no"]);
            foreach ($fee_config["items_no"] as $item) {
                $basic -= $order[$item];
                $note["no"][] = (string) $fee_items["no"][$item] . " ￥" . $order[$item];
            }
        }
        if ($basic < 0) {
            $basic = 0;
        }
        $plateform_serve_rate = $fee_config["fee_rate"];
        $platform_serve_fee = round($basic * $plateform_serve_rate / 100, 2);
        $text = "(" . implode(" + ", $note["yes"]);
        if (!empty($note["no"])) {
            $text .= " - " . implode(" - ", $note["no"]);
        }
        $text .= ") x " . $plateform_serve_rate . "%";
        if (0 < $fee_config["fee_min"] && $platform_serve_fee < $fee_config["fee_min"]) {
            $platform_serve_fee = $fee_config["fee_min"];
            $text .= " 佣金小于平台设置最少抽佣金额，以最少抽佣金额计";
        }
        $plateform_serve = array("fee_type" => 1, "fee_rate" => $plateform_serve_rate, "fee" => $platform_serve_fee, "note" => $text);
    }
    $store_final_fee = $order["price"] - $order["discount_fee"] - $platform_serve_fee + $order["plateform_discount_fee"] + $order["agent_discount_fee"];
    if (0 < $order["agentid"]) {
        mload()->model("agent");
        $account_agent = get_agent($order["agentid"], "fee");
        $agent_fee_config = $account_agent["fee"]["fee_gohome"];
        if (empty($agent_fee_config[$order["order_type"]])) {
            $account_agent = get_plugin_config("agent.serve_fee");
            $agent_fee_config = $account_agent["fee_gohome"];
        }
        $agent_fee_config = $agent_fee_config[$order["order_type"]];
        if ($agent_fee_config["type"] == 2) {
            $agent_serve_fee = floatval($agent_fee_config["fee"]);
            $agent_serve = array("fee_type" => 2, "fee_rate" => 0, "fee" => $agent_serve_fee, "note" => "每单固定" . $agent_serve_fee . "元");
        } else {
            if ($agent_fee_config["type"] == 1) {
                $basic = 0;
                $note = array("yes" => array(), "no" => array());
                $fee_items = agent_serve_fee_items();
                if (!empty($agent_fee_config["items_yes"])) {
                    foreach ($agent_fee_config["items_yes"] as $item) {
                        $basic += $order[$item];
                        $note["yes"][] = (string) $fee_items["yes"][$item] . " ￥" . $order[$item];
                    }
                }
                if (!empty($agent_fee_config["items_no"])) {
                    foreach ($agent_fee_config["items_no"] as $item) {
                        $basic -= $order[$item];
                        $note["no"][] = (string) $fee_items["no"][$item] . " ￥" . $order[$item];
                    }
                }
                if ($basic < 0) {
                    $basic = 0;
                }
                $agent_serve_rate = floatval($agent_fee_config["fee_rate"]);
                $agent_serve_fee = round($basic * $agent_serve_rate / 100, 2);
                $text = "(" . implode(" + ", $note["yes"]);
                if (!empty($note["no"])) {
                    $text .= " - " . implode(" - ", $note["no"]);
                }
                $text .= ") x " . $agent_serve_rate . "%";
                if (0 < $agent_fee_config["fee_min"] && $agent_serve_fee < $agent_fee_config["fee_min"]) {
                    $agent_serve_fee = $agent_fee_config["fee_min"];
                    $text .= " 佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                }
                $agent_serve = array("fee_type" => 1, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
            } else {
                if ($agent_fee_config["type"] == 3) {
                    $agent_serve_rate = floatval($agent_fee_config["fee_rate"]);
                    $agent_serve_fee = round($platform_serve_fee * $agent_serve_rate / 100, 2);
                    $text = "本单代理佣金:" . $platform_serve_fee . " x " . $agent_serve_rate . "%";
                    if (0 < $agent_fee_config["fee_min"] && $agent_serve_fee < $agent_fee_config["fee_min"]) {
                        $agent_serve_fee = $agent_fee_config["fee_min"];
                        $text .= " 佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                    }
                    $agent_serve = array("fee_type" => 3, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
                }
            }
        }
    }
    $agent_final_fee = $platform_serve_fee - $agent_serve_fee - $order["agent_discount_fee"];
    $agent_serve["final"] = "(代理商抽取佣金 ￥" . $platform_serve_fee . " - 平台服务佣金 ￥" . $agent_serve_fee . " - 代理商补贴 ￥" . $order["agent_discount_fee"] . ")";
    $data = array("agent_final_fee" => $agent_final_fee, "agent_serve" => iserializer($agent_serve), "agent_serve_fee" => $agent_serve_fee);
    $data["plateform_serve_fee"] = $platform_serve_fee;
    $data["plateform_serve"] = iserializer($plateform_serve);
    $data["store_final_fee"] = $store_final_fee;
    pdo_update("tiny_wmall_gohome_order", $data, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
    return true;
}
function gohome_update_activity_flow($activity_type, $goods_id, $type)
{
    global $_W;
    if (!in_array($type, array("looknum", "sharenum"))) {
        return false;
    }
    $routers = array("pintuan" => array("table" => "tiny_wmall_pintuan_goods"), "kanjia" => array("table" => "tiny_wmall_kanjia"), "seckill" => array("table" => "tiny_wmall_seckill_goods"), "tongcheng" => array("table" => "tiny_wmall_tongcheng_information"));
    $router = $routers[$activity_type];
    pdo_query("UPDATE " . tablename($router["table"]) . " set " . $type . " = " . $type . " + 1 WHERE uniacid = :uniacid AND id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $goods_id));
    return true;
}
function gohome_goods_favorite($goods_id, $type)
{
    global $_W;
    if (empty($goods_id) || !in_array($type, array("kanjia", "pintuan", "seckill"))) {
        return error(-1, "参数错误");
    }
    mload()->model("plugin");
    pload()->model($type);
    $goods = array();
    if ($type == "kanjia") {
        $goods = kanjia_get_activity($goods_id);
    } else {
        if ($type == "pintuan") {
            $goods = pintuan_get_activity($goods_id);
        } else {
            if ($type == "seckill") {
                $goods = seckill_goods($goods_id);
            }
        }
    }
    if (empty($goods)) {
        return error(-1, "商品不存在");
    }
    $is_favor = gohome_goods_favorite_check($goods_id, $type);
    if (empty($is_favor)) {
        $insert = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "sid" => $goods["sid"], "goods_id" => $goods_id, "type" => $type, "addtime" => TIMESTAMP);
        pdo_insert("tiny_wmall_gohome_favorite", $insert);
        return error(0, "收藏成功");
    }
    pdo_delete("tiny_wmall_gohome_favorite", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "goods_id" => $goods_id, "type" => $type));
    return error(0, "已取消收藏");
}
function gohome_goods_favorite_check($goods_id, $type)
{
    global $_W;
    $favor = pdo_get("tiny_wmall_gohome_favorite", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "goods_id" => $goods_id, "type" => $type));
    if (!empty($favor)) {
        return true;
    }
    return false;
}
function gohome_favor_fetchall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $uid = intval($filter["uid"]);
    if (0 < $uid) {
        $condition .= " and uid = :uid";
        $params[":uid"] = $uid;
    }
    $sid = intval($filter["sid"]);
    if (0 < $sid) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }
    $type = trim($filter["type"]);
    if (in_array($type, array("kanjia", "pintuan", "seckill"))) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $favors = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_favorite") . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($favors)) {
        $stores = store_fetchall(array("id", "title", "logo", "telephone", "address"));
        foreach ($favors as &$favor) {
            $favor["addtime_cn"] = date("Y-m-d H:i:s", $favor["addtime"]);
            $favor["goods"] = gohome_order_goods($favor["goods_id"], $favor["type"]);
            $favor["type_all"] = gohome_order_types($favor["type"], "all");
            $favor["store"] = $stores[$favor["sid"]];
        }
    }
    return $favors;
}
function gohome_order_begin_refund($orderOrid)
{
    global $_W;
    $order = $orderOrid;
    if (!is_array($order)) {
        $order = gohome_order_fetch($order);
    }
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    if ($order["refund_status"] == 2) {
        return error(-1, "退款进行中， 请勿重复操作");
    }
    if ($order["refund_status"] == 3) {
        return error(-1, "退款已成功, 不能发起退款");
    }
    if ($order["pay_type"] == "credit") {
        if (0 < $order["uid"]) {
            $log = array($order["uid"], (string) $order["order_type_cn"] . "订单退款, 订单号:" . $order["id"] . ", 退款金额:" . $order["final_fee"] . "元", "we7_wmall");
            mload()->model("member");
            member_credit_update($order["uid"], "credit2", $order["final_fee"], $log);
            $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
            pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        }
        return error(0, "退款成功,支付金额已退款至顾客的平台余额");
    }
    if ($order["pay_type"] == "wechat") {
        mload()->classs("wxpay");
        $pay = new WxPay($order["order_channel"]);
        $params = array("total_fee" => $order["final_fee"] * 100, "refund_fee" => $order["final_fee"] * 100, "out_trade_no" => $order["out_trade_no"], "out_refund_no" => $order["refund_out_no"]);
        $response = $pay->payRefund_build($params);
        if (is_error($response)) {
            return error(-1, $response["message"]);
        }
        $update = array("refund_status" => 2);
        pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        $query = gohome_order_query_payrefund($order["id"]);
        return $query;
    }
    if ($order["pay_type"] == "alipay") {
        mload()->classs("alipay");
        $pay = new AliPay($order["order_channel"]);
        $params = array("refund_fee" => $order["final_fee"], "out_trade_no" => $order["out_trade_no"]);
        $response = $pay->payRefund_build($params);
        if (is_error($response)) {
            return error(-1, $response["message"]);
        }
        $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
        pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        return error(0, "退款成功,支付金额已退款至顾客的支付宝账户:" . $response["buyer_logon_id"]);
    }
    if ($order["pay_type"] == "yimafu") {
        $orderno = number_format($order["transaction_id"], 0, "", "");
        mload()->classs("yimafu");
        $pay = new YiMaFu();
        $response = $pay->payRefund_build($orderno);
        if (is_error($response)) {
            return error(-1, "退款失败");
        }
        $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
        pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        return error(0, "退款成功,支付金额已退款至顾客一码付账户");
    }
    if ($order["pay_type"] == "qianfan") {
        $member = pdo_get("tiny_wmall_members", array("uid" => $order["uid"]));
        if (empty($member["uid_qianfan"])) {
            return error(-1, "获取用户uid失败");
        }
        mload()->model("plugin");
        pload()->model("qianfanApp");
        $status = qianfan_user_credit_add($member["uid_qianfan"], $order["final_fee"]);
        if (is_error($status)) {
            return error(-1, "退款失败:" . $status["message"]);
        }
        $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
        pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        return error(0, "退款成功,支付金额已退款至顾客的APP账户余额");
    }
}
function gohome_order_query_payrefund($orderId)
{
    global $_W;
    $order = gohome_order_fetch($orderId);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    if ($order["refund_status"] != 2) {
        return error(-1, "退款已处理");
    }
    if ($order["pay_type"] == "wechat") {
        mload()->classs("wxpay");
        $pay = new WxPay($order["order_channel"]);
        $response = $pay->payRefund_query(array("out_refund_no" => $order["refund_out_no"]));
        if (is_error($response)) {
            return $response;
        }
        $wechat_status = $pay->payRefund_status();
        $update = array("refund_status" => $wechat_status[$response["refund_status_0"]]["value"]);
        if ($response["refund_status_0"] == "SUCCESS") {
            $update["refund_channel"] = $response["refund_channel_0"];
            $update["refund_account"] = $response["refund_recv_accout_0"];
            $update["refund_success_time"] = TIMESTAMP;
            pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            return error(0, "退款成功,支付金额已退款至顾客的微信账号:" . $response["refund_recv_accout_0"]);
        }
        pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        return error(0, "退款进行中, 请耐心等待。微信官方说明：退款有一定延时，用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。");
    }
    return true;
}
function gohome_order_status_notice($orderOrId, $type, $extra = array())
{
    global $_W;
    $types = array("pay", "cancel", "confirm", "team_success");
    if (!in_array($type, $types)) {
        return error(-1, "参数错误");
    }
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = gohome_order_fetch($order);
    }
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    $store = $order["store"];
    $goods = $order["goods"];
    $config_wxapp_basic = $_W["we7_wmall"]["config"]["wxapp"]["basic"];
    $order_channel = $order["order_channel"];
    if ($order_channel == "wxapp" && $config_wxapp_basic["wxapp_consumer_notice_channel"] == "wechat") {
        mload()->model("member");
        $order["openid"] = member_wxapp2openid($order["openid"]);
        if (!empty($order["openid"])) {
            $order_channel = "wechat";
        }
    }
    $acc = TyAccount::create($order["uniacid"], $order_channel);
    if ($order_channel == "wechat") {
        if ($type == "pay") {
            $title = "您的订单已付款";
            $remark = array("门店名称: " . $store["title"], "支付方式: " . $order["pay_type_cn"], "支付时间: " . $order["paytime_cn"], "购买商品: " . $goods["name"] . " X " . $order["num"]);
            $order["status_cn"] = "待使用";
            $end_remark = "您已下单成功，及时到店核销";
        } else {
            if ($type == "confirm") {
                $title = "订单已核销";
                $remark = array("门店名称: " . $store["title"], "订单类型: " . $order["order_type_cn"] . "订单", "完成时间: " . date("Y-m-d H:i", $order["applytime"]));
                $order["status_cn"] = "已核销";
                $end_remark = "您的订单已核销, 如对商品有不满意或投诉请联系客服:" . $_W["we7_wmall"]["config"]["mobile"] . ",欢迎您下次光临.戳这里记得给我们的服务评价.";
            } else {
                if ($type == "cancel") {
                    $title = "订单已取消";
                    $remark = array("门店名称: " . $store["title"], "订单类型: " . $order["order_type_cn"] . "订单", "取消时间: " . date("Y-m-d H:i", TIMESTAMP));
                    $order["status_cn"] = "已取消";
                } else {
                    if ($type == "team_success") {
                        $title = "【" . $goods["name"] . "】拼团成功了";
                        $remark = array("门店名称: " . $store["title"], "订单类型: " . $order["order_type_cn"] . "订单", "商品名称: " . $goods["name"]);
                        $order["status_cn"] = "拼团成功";
                    }
                }
            }
        }
        $note = trim($extra["note"]);
        if (!empty($note)) {
            $remark[] = implode("\n", $extra["note"]);
        }
        if (!empty($end_remark)) {
            $remark[] = $end_remark;
        }
        $remark = implode("\n", $remark);
        $url = ivurl("gohome/pages/order/detail", array("id" => $order["id"]), true);
        $miniprogram = "";
        if ($config_wxapp_basic["tpl_consumer_url"] == "wxapp") {
            $miniprogram = array("appid" => $config_wxapp_basic["key"], "pagepath" => "gohome/pages/order/detail?id=" . $order["id"]);
        }
        $send = tpl_format($title, $order["ordersn"], $order["status_cn"], $remark);
        $status = $acc->sendTplNotice($order["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url, $miniprogram);
    } else {
        if ($order["order_channel"] == "wxapp") {
            $send = array("keyword1" => array("value" => "#" . $order["serial_sn"], "color" => "#ff510"), "keyword2" => array("value" => (string) $order["order_type_cn"], "color" => "#ff510"), "keyword3" => array("value" => $order["status_cn"], "color" => "#ff510"), "keyword4" => array("value" => $order["username"], "color" => "#ff510"), "keyword5" => array("value" => $order["mobile"], "color" => "#ff510"), "keyword6" => array("value" => $order["final_fee"], "color" => "#ff510"), "keyword7" => array("value" => date("Y-m-d H:i"), "color" => "#ff510"), "keyword8" => array("value" => $order["ordersn"], "color" => "#ff510"));
            $public_tpl = $_W["we7_wmall"]["config"]["wxapp"]["wxtemplate"]["public_tpl"];
            $status = $acc->sendTplNotice($order["openid"], $public_tpl, $send, "pages/order/detail?id=" . $order["id"]);
        }
    }
    if (is_error($status)) {
        slog("wxtplNotice", "gohome订单状态改变微信通知顾客-order_id:" . $order["id"], $send, $status["message"]);
    }
    return true;
}
function gohome_comment_tags($type)
{
    $data = array("goods" => array("1" => array("title" => "很差", "tags" => array(array("id" => 0, "title" => "味道差", "active" => 0), array("id" => 1, "title" => "服务不好", "active" => 0), array("id" => 2, "title" => "质量差", "active" => 0), array("id" => 3, "title" => "包装差", "active" => 0), array("id" => 4, "title" => "环境差", "active" => 0), array("id" => 5, "title" => "卫生差", "active" => 0))), "2" => array("title" => "一般", "tags" => array(array("id" => 0, "title" => "味道一般", "active" => 0), array("id" => 1, "title" => "服务一般", "active" => 0), array("id" => 2, "title" => "质量一般", "active" => 0), array("id" => 3, "title" => "包装一般", "active" => 0), array("id" => 4, "title" => "环境一般", "active" => 0), array("id" => 5, "title" => "卫生一般", "active" => 0))), "3" => array("title" => "满意", "tags" => array(array("id" => 0, "title" => "味道还行", "active" => 0), array("id" => 1, "title" => "服务还行", "active" => 0), array("id" => 2, "title" => "质量还行", "active" => 0), array("id" => 3, "title" => "包装还行", "active" => 0), array("id" => 4, "title" => "环境还行", "active" => 0), array("id" => 5, "title" => "卫生还行", "active" => 0))), "4" => array("title" => "非常满意", "tags" => array(array("id" => 0, "title" => "味道很好", "active" => 0), array("id" => 1, "title" => "服务很好", "active" => 0), array("id" => 2, "title" => "质量很好", "active" => 0), array("id" => 3, "title" => "包装很好", "active" => 0), array("id" => 4, "title" => "环境很好", "active" => 0), array("id" => 5, "title" => "卫生很好", "active" => 0))), "5" => array("title" => "无可挑剔", "tags" => array(array("id" => 0, "title" => "菜品美味", "active" => 0), array("id" => 1, "title" => "服务周到", "active" => 0), array("id" => 2, "title" => "干净卫生", "active" => 0), array("id" => 3, "title" => "态度很好", "active" => 0), array("id" => 4, "title" => "价格便宜", "active" => 0), array("id" => 5, "title" => "货品完好", "active" => 0)))), "deliveryer" => array());
    if (!empty($type)) {
        return $data[$type];
    }
    return $data;
}
function gohome_get_goods_comment($goods_id, $goods_type)
{
    global $_W;
    global $_GPC;
    if (empty($goods_id) || empty($goods_type)) {
        return false;
    }
    $condition = " where a.uniacid = :uniacid and a.agentid = :agentid and a.goods_id = :goods_id and a.goods_type = :goods_type and a.status = 0";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":goods_id" => $goods_id, ":goods_type" => $goods_type);
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_comment") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition, $params);
    $data = pdo_fetchall("select a.*, b.nickname, b.avatar from " . tablename("tiny_wmall_gohome_comment") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        $tag_goods = gohome_comment_tags("goods");
        foreach ($data as &$value) {
            $value["addtime_cn"] = date("Y-m-d H:i", $value["addtime"]);
            $value["goods_quality"] = intval($value["goods_quality"]);
            $value["thumbs"] = iunserializer($value["thumbs"]);
            if (!empty($value["thumbs"])) {
                foreach ($value["thumbs"] as &$thumb) {
                    $thumb = tomedia($thumb);
                }
            }
            $value["data"] = iunserializer($value["data"]);
            if (!empty($value["data"]["tag_goods"])) {
                $tags = array();
                $tags_keys = explode("|", $value["data"]["tag_goods"]);
                if (!empty($tags_keys)) {
                    foreach ($tags_keys as $keys) {
                        $tags[] = $tag_goods[$value["goods_quality"]]["tags"][$keys];
                    }
                }
            }
            $value["tag_goods"] = $tags;
        }
    }
    $pager = pagination($total, $page, $psize);
    return array("comment" => $data, "total" => $total, "pager" => $pager);
}
function gohome_get_danmu($goods_id = 0, $type = "")
{
    global $_W;
    $config = get_plugin_config("gohome.basic.danmu_status");
    $status = isset($config[$type]) ? $config[$type] : 0;
    if ($status != 1) {
        return false;
    }
    $condition = " as b on a.uid = b.uid where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    if (0 < $goods_id) {
        $condition .= " and a.goods_id = :goods_id";
        $params[":goods_id"] = $goods_id;
    }
    $order_types = array("pintuan", "kanjia", "seckill");
    if (in_array($type, $order_types)) {
        $condition .= " and a.order_type = :order_type";
        $params[":order_type"] = $type;
    }
    $condition .= " and b.nickname != '' and b.avatar != '' order by a.id desc limit 10";
    $members = pdo_fetchall("select b.nickname, b.avatar from " . tablename("tiny_wmall_gohome_order") . " as a left join " . tablename("tiny_wmall_members") . $condition, $params);
    if (empty($members)) {
        $members = pdo_fetchall("select nickname, avatar from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and nickname != '' and avatar != '' order by id desc limit 10;", array(":uniacid" => $_W["uniacid"]));
    }
    if (!empty($members)) {
        foreach ($members as &$val) {
            $val["avatar"] = tomedia($val["avatar"]);
            $val["time"] = "刚刚";
        }
    }
    return $members;
}
function gohome_complain_notice($idOrComplain)
{
    global $_W;
    $maneger = $_W["we7_wmall"]["config"]["manager"];
    if (empty($maneger["openid"])) {
        return error(-1, "未获取到平台管理员的openid");
    }
    $complain = $idOrComplain;
    if (!is_array($complain)) {
        $complain = pdo_get("tiny_wmall_complain", array("uniacid" => $_W["uniacid"], "id" => $complain));
    }
    if (!empty($complain)) {
        $complain_types = array("cheat" => array("type" => "cheat", "text" => "网页包含欺诈信息（如：假红包）"), "eroticism" => array("type" => "eroticism", "text" => "网页包含欺色情信息"), "violence" => array("type" => "violence", "text" => "网页包含欺暴力恐怖信息"), "politics" => array("type" => "politics", "text" => "网页包含欺政治敏感信息"), "privacy" => array("type" => "privacy", "text" => "网页在收集个人隐私信息（如：钓鱼链接）"), "induce" => array("type" => "induce", "text" => "网页包含诱导分享/关注性质的内容"), "rumor" => array("type" => "rumor", "text" => "网页可能包含谣言信息"), "gamble" => array("type" => "gamble", "text" => "网页包含赌博信息"));
        $acc = TyAccount::create($complain["uniacid"]);
        $title = "有顾客投诉了，请处理";
        $remark = array("投诉　人: " . $_W["member"]["nickname"], "顾客编号: " . $_W["member"]["uid"], "投诉原因: " . $complain_types[$complain["type"]]["text"], "投诉时间: " . date("Y-m-d H:i", $complain["addtime"]));
        $end_remark = "点击详情查看顾客投诉页面";
        $remark[] = $end_remark;
        $remark = implode("\n", $remark);
        $url = $complain["link"];
        $send = tpl_format($title, "", "", $remark);
        $status = $acc->sendTplNotice($maneger["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url);
    }
    if (is_error($status)) {
        slog("wxtplNotice", "gohome订单顾客投诉通知平台管理员-UID:" . $_W["member"]["uid"], $send, $status["message"]);
    }
    return true;
}
function gohome_order_refund_notice($orderOrid, $type, $note = "")
{
    global $_W;
    $order = $orderOrid;
    if (!is_array($order)) {
        $order = gohome_order_fetch($orderOrid);
    }
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    $store = store_fetch($order["sid"], array("title", "id"));
    $acc = WeAccount::create($order["uniacid"]);
    mload()->model("clerk");
    $clerks = clerk_fetchall($order["sid"]);
    if ($type == "success") {
        gohome_order_clerk_notice($order["id"], "cancel");
    } else {
        if ($type == "fail") {
            if (0 < $order["agentid"]) {
                $_W["agentid"] = 0;
                $_W["we7_wmall"]["config"] = get_system_config();
            }
            $maneger = $_W["we7_wmall"]["config"]["manager"];
            if (!empty($maneger["openid"])) {
                $tips = "您的平台的退款订单【退款失败】,退款单号【" . $order["refund_out_no"] . "】,请尽快处理";
                $remark = array("申请门店: " . $store["title"], "退款单号: " . $order["refund_out_no"], "支付方式: " . $order["pay_type_cn"], "用户姓名: " . $order["username"], "联系方式: " . $order["mobile"], $note);
                $params = array("first" => $tips, "reason" => "生活圈订单取消, 发起退款失败", "refund" => $order["final_fee"], "remark" => implode("\n", $remark));
                $send = sys_wechat_tpl_format($params);
                $status = $acc->sendTplNotice($maneger["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["refund_tpl"], $send);
                if (is_error($status)) {
                    slog("wxtplNotice", "发起退款失败微信通知平台管理员", $send, $status["message"]);
                }
            }
        }
    }
    return true;
}
function gohome_order_clerk_notice($id, $type, $note = "")
{
    global $_W;
    $order = gohome_order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    mload()->model("clerk");
    $clerks = clerk_fetchall($order["sid"]);
    if (empty($clerks)) {
        return false;
    }
    $store = $order["store"];
    $account = $order["uniacid"];
    $channel_notice = "wechat";
    $acc = TyAccount::create($account, $channel_notice);
    if ($type == "pay") {
        $title = "您的店铺有新的" . $order["order_type_cn"] . "订单,订单号为 #" . $order["id"] . " ,订单金额:" . $order["final_fee"] . "元,请尽快处理";
        $Jpush_title = "您的店铺有新的生活圈订单";
        $remark = array("门店名称： " . $store["title"], "订单备注：" . $order["buyremark"], "商品信息： " . $order["goods"]["name"] . " x " . $order["num"], "下单时间： " . $order["addtime_cn"], "总金　额： " . $order["final_fee"], "支付状态： " . $order["pay_type_cn"], "订单类型: " . $order["order_type_cn"]);
        if ($order["order_type"] == "pintuan") {
            $pintuan_type = $order["is_team"] == 1 ? "团购" : "单独购买";
            $remark[] = "购买类型： " . $pintuan_type;
        }
    } else {
        if ($type == "confirm") {
            $title = "订单号为 #" . $order["id"] . " 的" . $order["order_type_cn"] . "订单, 已经核销";
            $remark = array("门店名称: " . $store["title"], "订单类型: " . $order["order_type_cn"], "商品信息： " . $order["goods"]["name"] . " x " . $order["num"], "核销时间: " . date("Y-m-d H:i", $order["applytime"]));
        } else {
            if ($type == "team_success") {
                $title = "订单号为：" . $order["id"] . "的订单组团成功，请留意处理订单核销";
                $Jpush_title = "您的店铺有新的拼团订单组团成功了";
                $remark = array("门店名称: " . $store["title"], "拼团商品: " . $order["goods"]["name"], "参团人数: " . $order["takepart_num"]);
            } else {
                if ($type == "cancel") {
                    $tips = "您店铺单号为【" . $order["refund_out_no"] . "】的退款已退款成功";
                    $Jpush_title = "您店铺有生活圈订单取消";
                    $remark = array("申请门店: " . $store["title"], "支付方式: " . $order["pay_type_cn"], "用户姓名: " . $order["username"], "联系方式: " . $order["mobile"], "退款渠道: " . $order["pay_type_cn"], "如有疑问, 请联系平台管理员");
                }
            }
        }
    }
    if ($channel_notice == "wechat") {
        if (!empty($note)) {
            if (!is_array($note)) {
                $remark[] = $note;
            } else {
                $remark[] = implode("\n", $note);
            }
        }
        $remark[] = "请尽快登录商户电脑端或者商户APP进行生活圈订单处理";
        $url = "";
        $remark = implode("\n", $remark);
        $miniprogram = "";
        $send = tpl_format($title, $order["ordersn"], $order["status_cn"], $remark);
        $public_tpl = $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"];
    }
    mload()->model("sms");
    foreach ($clerks as $clerk) {
        if ($clerk["extra"]["accept_wechat_notice"] == 1) {
            if ($channel_notice == "wechat") {
                $status = $acc->sendTplNotice($clerk["openid"], $public_tpl, $send, $url, $miniprogram);
            } else {
                $status = $acc->sendTplNotice($clerk["openid_wxapp_manager"], $public_tpl, $send, $url);
            }
            if (is_error($status)) {
                slog("wxtplNotice", "生活圈订单状态变动微信通知商户-" . $store["title"] . ":" . $clerk["title"] . ",渠道:" . $channel_notice, $send, $status["message"]);
            }
        } else {
            slog("wxtplNotice", "生活圈订单状态变动微信通知商户-" . $store["title"] . ":" . $clerk["title"], $send, "商户设置不接收微信模板消息");
        }
    }
    if (in_array($type, array("pay", "cancel", "team_success"))) {
        $audience = array("tag" => array($store["push_token"]));
        $type = $type == "pay" || $type == "team_success" ? "place_order" : $type;
        $data = Jpush_clerk_send($Jpush_title, $title, array("voice_text" => $title, "url" => $url, "notify_type" => $type, "order_from" => "gohome", "id" => $order["id"]), $audience);
    }
    return true;
}
function gohome_get_menu($type = "")
{
    global $_W;
    if (empty($type)) {
        $type = $_W["_controller"];
    }
    if (empty($type)) {
        $type = "pintuan";
    }
    $menus = array("gohome" => array("name" => "gohome", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "/gohome/pages/home/index", "icon" => "icon-home", "text" => "首页"), "M0123456789102" => array("link" => "/gohome/pages/member/favorite", "icon" => "icon-likefill", "text" => "收藏"), "M0123456789103" => array("link" => "/gohome/pages/order/index", "icon" => "icon-order", "text" => "订单"), "M0123456789104" => array("link" => "/gohome/pages/tongcheng/index", "icon" => "icon-community_fill_light", "text" => "同城"), "M0123456789105" => array("link" => "/pages/member/mine", "icon" => "icon-mine", "text" => "我的"))), "pintuan" => array("name" => "pintuan", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "/gohome/pages/pintuan/index", "icon" => "icon-home", "text" => "首页"), "M0123456789103" => array("link" => "/gohome/pages/member/favorite", "icon" => "icon-likefill", "text" => "收藏"), "M0123456789102" => array("link" => "/gohome/pages/order/index", "icon" => "icon-order", "text" => "订单"), "M0123456789104" => array("link" => "/gohome/pages/home/index", "icon" => "icon-skip", "text" => "返回生活圈"))), "kanjia" => array("name" => "kanjia", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "/gohome/pages/kanjia/index", "icon" => "icon-home", "text" => "首页"), "M0123456789103" => array("link" => "/gohome/pages/kanjia/record", "icon" => "icon-friend", "text" => "砍价"), "M0123456789104" => array("link" => "/gohome/pages/member/favorite", "icon" => "icon-likefill", "text" => "收藏"), "M0123456789102" => array("link" => "/gohome/pages/order/index", "icon" => "icon-order", "text" => "订单"), "M0123456789105" => array("link" => "/gohome/pages/home/index", "icon" => "icon-skip", "text" => "返回生活圈"))), "seckill" => array("name" => "seckill", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "/gohome/pages/seckill/index", "icon" => "icon-home", "text" => "首页"), "M0123456789102" => array("link" => "/gohome/pages/order/index", "icon" => "icon-order", "text" => "订单"), "M0123456789103" => array("link" => "/gohome/pages/member/favorite", "icon" => "icon-likefill", "text" => "收藏"), "M0123456789104" => array("link" => "/gohome/pages/home/index", "icon" => "icon-skip", "text" => "返回生活圈"))), "tongcheng" => array("name" => "tongcheng", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "/gohome/pages/tongcheng/index", "icon" => "icon-home", "text" => "首页"), "M0123456789102" => array("link" => "/gohome/pages/tongcheng/publish/index", "icon" => "icon-edit", "text" => "发布"), "M0123456789103" => array("link" => "/gohome/pages/tongcheng/publish/list", "icon" => "icon-message", "text" => "我的发布"), "M0123456789104" => array("link" => "/gohome/pages/home/index", "icon" => "icon-skip", "text" => "返回生活圈"))), "haodian" => array("name" => "haodian", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#163636", "iconColorActive" => "#ff2d4b", "textColor" => "#929292", "textColorActive" => "#ff2d4b"), "data" => array("M0123456789101" => array("link" => "/gohome/pages/haodian/index", "icon" => "icon-home", "text" => "首页"), "M0123456789103" => array("link" => "/gohome/pages/member/favorite", "icon" => "icon-likefill", "text" => "收藏"), "M0123456789104" => array("link" => "/gohome/pages/home/index", "icon" => "icon-skip", "text" => "返回生活圈"))));
    return $menus[$type];
}
function gohome_del_goods($id, $type)
{
    global $_W;
    $routers = array("kanjia" => "tiny_wmall_kanjia", "pintuan" => "tiny_wmall_pintuan_goods", "seckill" => "tiny_wmall_seckill_goods");
    pdo_delete($routers[$type], array("uniacid" => $_W["uniacid"], "id" => $id));
    return error(0, "删除商品成功");
}
function gohome_get_notice($status = "-1")
{
    global $_W;
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($_W["is_agent"]) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $notice = pdo_fetchall("select * from" . tablename("tiny_wmall_gohome_notice") . $condition . " order by displayorder desc", $params);
    return $notice;
}
function gohome_get_slider($status = "-1")
{
    global $_W;
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($_W["is_agent"]) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $sliders = pdo_fetchall("select * from" . tablename("tiny_wmall_gohome_slide") . $condition . " order by displayorder desc", $params);
    if (!empty($sliders)) {
        foreach ($sliders as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
        }
    }
    return $sliders;
}
function gohome_order_print($id)
{
    global $_W;
    $order = gohome_order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    $sid = intval($order["sid"]);
    $store = store_fetch($order["sid"], array("title"));
    $prints = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_printer") . " WHERE uniacid = :aid AND sid = :sid AND status = 1", array(":aid" => $_W["uniacid"], ":sid" => $sid));
    if (empty($prints)) {
        return error(-1, "没有有效的打印机");
    }
    mload()->model("print");
    $num = 0;
    foreach ($prints as $li) {
        if (!empty($li["print_no"])) {
            $content = array("title" => "<CB>" . $_W["we7_wmall"]["config"]["mall"]["title"] . "</CB>", "orderfrom" => "<CB>" . $order["order_type_cn"] . "订单</CB>", "store" => "<C>*" . $store["title"] . "*</C>");
            if (!empty($li["print_header"])) {
                $content["print_header"] = "<C>" . $li["print_header"] . "</C>";
                if ($li["type"] == "365") {
                    $content["print_header"] = (string) $li["print_header"];
                }
            }
            if ($order["is_pay"] == 1) {
                $content["pay"] = "<CB>--" . $order["pay_type_cn"] . "--</CB>";
            }
            if ($li["type"] == "365") {
                $content["store"] = "*" . $store["title"] . "*";
                $content["pay"] = "--" . $order["pay_type_cn"] . "--";
            }
            if (!empty($order["buyremark"])) {
                $content["buyremark"] = "<L>备注:" . $order["buyremark"] . "</L>";
                if ($li["type"] == "365") {
                    $content["buyremark"] = "<C>备注:" . $order["buyremark"] . "</C>";
                }
            }
            $content[] = "--------------------------------";
            $content[] = "下单时间:" . $order["addtime_cn"];
            $content[] = "订单编号:" . $order["ordersn"];
            $content[] = "--------------------------------";
            if (!empty($order["goods"])) {
                $content["goods_header"] = "名称　　　　　　　数量　　　金额";
                $content[] = "********************************";
                $title = $order["goods"]["name"];
                $title = iconv("utf-8", "GBK//IGNORE", $title);
                $length = strlen($title);
                if ($li["type"] == "xixun") {
                    if (16 < $length) {
                        $content["goods_title"] = "<1D2101><1B6100>" . $order["goods"]["name"];
                        $str = "";
                        $str .= "　　　　　　　　　<1D2101><1B6100>X" . str_pad($order["num"], "3", " ", STR_PAD_RIGHT);
                        $str .= str_pad($order["final_fee"], "10", " ", STR_PAD_LEFT);
                        $content["goods_price"] = $str;
                    } else {
                        $title = str_pad($title, "16", " ", STR_PAD_RIGHT);
                        $title = iconv("GBK", "utf-8", $title);
                        $str = "<1D2101><1B6100>" . $title;
                        $str .= "　<1D2101><1B6100>X" . str_pad($order["num"], "3", " ", STR_PAD_RIGHT);
                        $str .= str_pad($order["final_fee"], "10", " ", STR_PAD_LEFT);
                        $content["goods_item"] = $str;
                    }
                } else {
                    if ($li["type"] == "365") {
                        if (16 < $length) {
                            $content[] = "<C>" . $order["goods"]["name"] . "</C>";
                            $str = "<C>";
                            $str .= "　　　　　　　　　X" . str_pad($order["num"], "3", " ", STR_PAD_RIGHT);
                            $str .= str_pad($order["final_fee"], "10", " ", STR_PAD_LEFT) . "</C>";
                            $content[] = $str;
                        } else {
                            $title = str_pad($title, "16", " ", STR_PAD_RIGHT);
                            $title = iconv("GBK", "utf-8", $title);
                            $str = "<C>" . $title . "</C>";
                            $str .= "　X" . str_pad($order["num"], "3", " ", STR_PAD_RIGHT);
                            $str .= str_pad($order["final_fee"], "10", " ", STR_PAD_LEFT);
                            $content[] = $str;
                        }
                    } else {
                        if (16 < $length) {
                            $content[] = "<L>" . $order["goods"]["name"] . "</L>";
                            $str = "";
                            $str .= "　　　　　　　　　<L>X" . str_pad($order["num"], "3", " ", STR_PAD_RIGHT) . "</L>";
                            $str .= "<L>" . str_pad($order["final_fee"], "10", " ", STR_PAD_LEFT) . "</L>";
                            $content[] = $str;
                        } else {
                            $title = str_pad($title, "16", " ", STR_PAD_RIGHT);
                            $title = iconv("GBK", "utf-8", $title);
                            $str = "<L>" . $title . "</L>";
                            $str .= "　<L>X" . str_pad($order["num"], "3", " ", STR_PAD_RIGHT) . "</L>";
                            $str .= "<L>" . str_pad($order["final_fee"], "10", " ", STR_PAD_LEFT) . "</L>";
                            $content[] = $str;
                        }
                    }
                }
                $content[] = "********************************";
                if ($order["order_type"] == "kanjia") {
                    $bargain = $order["goods"]["oldprice"] - $order["final_fee"];
                    $content[] = "好友累计帮砍:" . $bargain . "元";
                } else {
                    if ($order["order_type"] == "pintuan") {
                        $team_cn = empty($order["is_team"]) ? "单独购买" : $order["team_num"] . "人团";
                        $content[] = "拼团类型:" . $team_cn;
                    }
                }
                $content["final_fee"] = "实际支付:" . $order["final_fee"] . "元";
                if ($li["type"] == "365") {
                    $content["username"] = "<C>" . $order["username"] . "</C>";
                    $content["mobile"] = "<C>" . $order["mobile"] . "</C>";
                } else {
                    $content["username"] = "<L>" . $order["username"] . "</L>";
                    $content["mobile"] = "<L>" . $order["mobile"] . "</L>";
                }
                if (!empty($li["print_footer"])) {
                    $content["print_footer"] = "<C>" . $li["print_footer"] . "</C>";
                    if ($li["type"] == "365") {
                        $content["print_footer"] = (string) $li["print_footer"];
                    }
                }
                if ($li["type"] == "feie") {
                    $content[] = implode("", array("\33", "d", "\1", "\33", "p", "0", "\36", "x"));
                } else {
                    if ($li["type"] == "qiyun" && 0 < $li["print_nums"]) {
                        $content[] = "<N>" . $li["print_nums"] . "</N>";
                    }
                }
                $content["end"] = "<CB>*****团****</CB>";
                $li["deviceno"] = $li["print_no"];
                $li["content"] = $content;
                $li["times"] = $li["print_nums"];
                $li["orderindex"] = $order["ordersn"] . random(10, true);
                if (($li["type"] == "feiyin" || $li["type"] == "AiPrint") && 0 < $li["print_nums"]) {
                    for ($i = 0; $i < $li["print_nums"]; $i++) {
                        $li["orderindex"] = $order["ordersn"] . random(10, true);
                        $status = print_add_order($li, $order);
                        if (!is_error($status)) {
                            $num++;
                        }
                    }
                } else {
                    $status = print_add_order($li, $order);
                    if (!is_error($status)) {
                        $num += $li["print_nums"];
                    }
                }
            }
        }
        if (0 < $num) {
            pdo_query("UPDATE " . tablename("tiny_wmall_gohome_order") . " SET print_nums = print_nums + " . $num . ", print_status = 1 WHERE uniacid = " . $_W["uniacid"] . " AND id = " . $order["id"]);
            return true;
        }
        if ($order["print_status"] != 0) {
            pdo_update("tiny_wmall_gohome_order", array("print_status" => 0), array("id" => $order["id"]));
        }
        slog("orderprint", "请求打印接口失败", "", "生活圈订单号:" . $order["id"] . ";错误信息:" . $status["message"]);
        return error(-1, "请求打印接口失败:" . $status["message"]);
    }
    return true;
}

?>