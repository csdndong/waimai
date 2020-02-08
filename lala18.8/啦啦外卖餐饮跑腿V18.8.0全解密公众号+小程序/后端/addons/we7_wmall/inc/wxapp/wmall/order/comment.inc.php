<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->func("tpl.app");
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$id = intval($_GPC["id"]);
$order = order_fetch($id);
if ($ta == "index") {
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    $goods = order_fetch_goods($order["id"]);
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]));
    $store = store_fetch($order["sid"]);
    $delivery = array(1 => array("title" => "很差", "value" => 1, "tags" => array()), 2 => array("title" => "一般", "value" => 2, "tags" => array()), 3 => array("title" => "满意", "value" => 3, "tags" => array()), 4 => array("title" => "非常满意", "value" => 4, "tags" => array()), 5 => array("title" => "无可挑剔", "value" => 5, "tags" => array()));
    $labels = pdo_fetchall("select * from " . tablename("tiny_wmall_category") . " where uniacid = :uniacid and type = \"TY_delivery_label\"", array(":uniacid" => $_W["uniacid"]));
    foreach ($labels as $row) {
        $delivery[$row["score"]]["tags"][] = array("name" => $row["title"], "id" => $row["id"], "selected" => 0);
    }
    $result = array("goods" => $goods, "deliveryer" => $deliveryer, "order" => $order, "store" => $store, "delivery" => $delivery);
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "post") {
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    if ($order["is_comment"] == 1) {
        imessage(error(-1, "订单已评价"), "", "ajax");
    }
    $activeDelivery = $_GPC["activeDelivery"];
    $store = store_fetch($order["sid"], array("comment_status"));
    $delivery_tags = $_GPC["delivery_tags"];
    $delivery_tags = implode(",", json_decode(htmlspecialchars_decode($delivery_tags)));
    $insert = array("uniacid" => $_W["uniacid"], "agentid" => $order["agentid"], "uid" => $_W["member"]["uid"], "username" => $order["username"], "avatar" => $_W["member"]["avatar"], "mobile" => $order["mobile"], "oid" => $id, "sid" => $order["sid"], "deliveryer_id" => $order["deliveryer_id"], "goods_quality" => intval($_GPC["goods_quality"]) ? intval($_GPC["goods_quality"]) : 5, "delivery_service" => intval($_GPC["deliverScore"]) ? intval($_GPC["deliverScore"]) : 5, "note" => trim($_GPC["note"]), "status" => $store["comment_status"], "data" => "", "addtime" => TIMESTAMP, "taste_score" => intval($_GPC["tasteScore"]), "package_score" => intval($_GPC["packageScore"]), "deliveryer_tag" => $delivery_tags);
    $thumbs = $_GPC["thumbs"];
    if (!is_array($thumbs)) {
        $thumbs = json_decode(htmlspecialchars_decode($thumbs), true);
    }
    foreach ($thumbs as $v) {
        $thumb[] = $v["filename"];
    }
    $insert["thumbs"] = iserializer($thumb);
    $goods = order_fetch_goods($order["id"]);
    $goodsList = $_GPC["goods"];
    if (!is_array($goodsList)) {
        $goodsList = json_decode(htmlspecialchars_decode($goodsList), true);
    }
    $newGoods = array();
    foreach ($goodsList as $row) {
        $newGoods[$row["id"]] = $row;
    }
    foreach ($goods as $good) {
        $value = intval($newGoods[$good["id"]]["activity"]);
        if (!$value) {
            continue;
        }
        $update = " set comment_total = comment_total + 1";
        if ($value == 1) {
            $update .= " , comment_good = comment_good + 1";
            $insert["data"]["good"][] = $good["goods_title"];
        } else {
            $insert["data"]["bad"][] = $good["goods_title"];
        }
        pdo_query("update " . tablename("tiny_wmall_goods") . $update . " where id = :id", array(":id" => $good["goods_id"]));
    }
    $insert["score"] = $insert["goods_quality"] + $insert["delivery_service"];
    $insert["data"] = iserializer($insert["data"]);
    pdo_insert("tiny_wmall_order_comment", $insert);
    pdo_update("tiny_wmall_order", array("is_comment" => 1), array("id" => $id));
    if ($store["comment_status"] == 1) {
        store_comment_stat($order["sid"]);
    }
    if (check_plugin_perm("spread")) {
        mload()->model("plugin");
        pload()->model("spread");
        member_spread_confirm($order["id"]);
        spread_order_balance($order["id"]);
    }
    imessage(error(0, "评价成功"), "", "ajax");
}

?>