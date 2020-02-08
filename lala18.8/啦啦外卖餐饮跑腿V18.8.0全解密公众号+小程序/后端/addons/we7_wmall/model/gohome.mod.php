<?php

defined("IN_IA") or exit("Access Denied");

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
    $order["order_type_cn"] = gohome_order_types($order["order_type"], "text");
    $order["order_type_css"] = gohome_order_types($order["order_type"], "css");
    $order["status_cn"] = gohome_order_status($order["status"], "text");
    $order["goods"] = gohome_order_goods($order["goods_id"], $order["order_type"]);
    $order["store"] = store_fetch($order["sid"], array("title", "logo", "telephone", "address"));
    return $order;
}
function gohome_order_goods($goods_id, $order_type)
{
    global $_W;
    if (!in_array($order_type, array("kanjia", "pintuan", "qianggou"))) {
        return false;
    }
    $goods_id = intval($goods_id);
    $table = array("kanjia" => "tiny_wmall_kanjia", "pintuan" => "tiny_wmall_pintuan_goods", "qianggou" => "");
    $tablename = $table[$order_type];
    $goods = pdo_get($tablename, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $goods_id));
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
    $table = array("kanjia" => "tiny_wmall_kanjia", "pintuan" => "tiny_wmall_pintuan_goods");
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
    $condition = " where uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
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
    if (in_array($order_type, array("kanjia", "pintuan", "qianggou"))) {
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
        $condition .= " and (mobile like :keyword or username like :keyword or ordersn like :keyword)";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_order") . $condition, $params);
    $orders = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_order") . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($orders)) {
        $pay_types = order_pay_types();
        $stores = store_fetchall(array("id", "title", "logo", "telephone", "address"));
        foreach ($orders as &$order) {
            $order["addtime_cn"] = date("Y-m-d H:i:s", $order["addtime"]);
            $order["paytime_cn"] = date("Y-m-d H:i:s", $order["paytime"]);
            if (empty($order["is_pay"])) {
                $order["pay_type_cn"] = "未支付";
                $order["pay_type_css"] = "label label-default";
            } else {
                $order["pay_type_cn"] = !empty($pay_types[$order["pay_type"]]["text"]) ? $pay_types[$order["pay_type"]]["text"] : "其他支付方式";
                $order["pay_type_css"] = !empty($pay_types[$order["pay_type"]]["css"]) ? $pay_types[$order["pay_type"]]["css"] : "label label-info";
            }
            $order["order_type_cn"] = gohome_order_types($order["order_type"], "text");
            $order["order_type_css"] = gohome_order_types($order["order_type"], "css");
            $order["status_cn"] = gohome_order_status($order["status"], "text");
            $order["status_css"] = gohome_order_status($order["status"], "css");
            $order["goods"] = gohome_order_goods($order["goods_id"], $order["order_type"]);
            $order["store"] = $stores[$order["sid"]];
        }
    }
    $pager = pagination($total, $page, $psize);
    return array("orders" => $orders, "total" => $total, "pager" => $pager);
}
function gohome_order_types($type, $key = "all")
{
    $data = array("kanjia" => array("text" => "砍价", "css" => "label label-danger"), "pintuan" => array("text" => "拼团", "css" => "label label-info"), "qianggou" => array("text" => "抢购", "css" => "label label-warning"));
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
function gohome_order_status($type, $key = "all")
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
        pintuan_order_update($order, $type, $extra);
    } else {
        if ($order["order_type"] == "qianggou") {
        }
    }
}
function gohome_update_activity_flow($activity_type, $goods_id, $type)
{
    global $_W;
    if (!in_array($type, array("looknum", "sharenum"))) {
        return false;
    }
    $routers = array("pintuan" => array("table" => "tiny_wmall_pintuan_goods"), "kanjia" => array("table" => "tiny_wmall_kanjia"), "qianggou" => array("table" => ""));
    $router = $routers[$activity_type];
    pdo_query("UPDATE " . tablename($router["table"]) . " set " . $type . " = " . $type . " + 1 WHERE uniacid = :uniacid AND id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $goods_id));
    return true;
}
function gohome_goods_favorite($goods_id, $sid, $type)
{
    global $_W;
    if (empty($goods_id) || empty($sid) || !in_array($type, array("kanjia", "pintuan", "qianggou"))) {
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
            if ($type == "qianggou") {
            }
        }
    }
    if (empty($goods)) {
        return error(-1, "商品不存在");
    }
    $is_favor = gohome_goods_favorite_check($goods_id, $type);
    if (empty($is_favor)) {
        $insert = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "sid" => $sid, "goods_id" => $goods_id, "type" => $type, "addtime" => TIMESTAMP);
        pdo_insert("tiny_wmall_gohome_favorite", $insert);
        return error(0, "收藏成功");
    }
    pdo_delete("tiny_wmall_gohome_favorite", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "sid" => $sid, "goods_id" => $goods_id, "type" => $type));
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
        $params["uid"] = $uid;
    }
    $sid = intval($filter["sid"]);
    if (0 < $sid) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }
    $type = trim($filter["type"]);
    if (in_array($type, array("kanjia", "pintuan", "qianggou"))) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $favors = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_favorite") . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($favors)) {
        $stores = store_fetchall(array("id", "title", "logo", "telephone", "address"));
        foreach ($favors as &$favor) {
            $favor["type_cn"] = gohome_order_types($favor["type"], "text");
            $favor["addtime_cn"] = date("Y-m-d H:i:s", $favor["addtime"]);
            $favor["goods"] = gohome_order_goods($favor["goods_id"], $favor["type"]);
            $favor["store"] = $stores[$favor["sid"]];
            $favor["order_type_css"] = gohome_order_types($favor["type"], "css");
        }
    }
    return $favors;
}

?>