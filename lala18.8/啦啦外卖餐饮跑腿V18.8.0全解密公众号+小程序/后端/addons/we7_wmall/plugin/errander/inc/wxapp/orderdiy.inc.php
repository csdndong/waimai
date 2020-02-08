<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "create";
if ($op == "create") {
    $id = intval($_GPC["id"]);
    $category = get_errander_diypage($id);
    if (empty($category)) {
        imessage(error(-1000, "跑腿类型不存在"), imurl("errander/index"), "ajax");
    }
    $diypage = $category["diypage"];
    $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
    if (empty($params)) {
        imessage(error(-1, "参数错误"), "", "ajax");
    }
    if (empty($params["note"])) {
        imessage(error(-1, "请填写物品信息"), "", "ajax");
    }
    if (empty($params["goods_weight"]) && $diypage["data"]["fees"]["weight_status"] == 1) {
        imessage(error(-1, "请选择物品重量"), "", "ajax");
    }
    if ($diypage["data"]["page"]["scene"] != "buy") {
        $status = member_errander_address_check($params["buyaddress_id"]);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        $buyaddress = $status;
    } else {
        $buyaddress = $params["buyaddress"];
        if (empty($params["buyaddress"])) {
            $buyaddress = array("address" => "就近购买");
        }
    }
    $params["buyaddress"] = $buyaddress;
    $status = member_errander_address_check($params["acceptaddress_id"]);
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    $acceptaddress = $status;
    $params["acceptaddress"] = $acceptaddress;
    $required = errander_order_check_required($diypage, $params);
    if (is_error($required)) {
        imessage($required, "", "ajax");
    }
    $order = errander_order_calculate_delivery_fee($id, $params);
    $delivery_info = $order["deliveryInfo"];
    $data = array("uniacid" => $_W["uniacid"], "agentid" => $category["diypage"]["agentid"], "acid" => $_W["acid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "code" => random(4, true), "order_sn" => date("YmdHis") . random(6, true), "order_type" => $diypage["data"]["page"]["scene"], "order_cid" => $id, "buy_username" => $buyaddress["realname"], "buy_mobile" => $buyaddress["mobile"], "buy_sex" => $buyaddress["sex"], "buy_address" => $buyaddress["address"] . $buyaddress["number"], "buy_location_x" => $buyaddress["location_x"], "buy_location_y" => $buyaddress["location_y"], "accept_mobile" => $acceptaddress["mobile"], "accept_username" => $acceptaddress["realname"], "accept_sex" => $acceptaddress["sex"], "accept_address" => $acceptaddress["address"] . $acceptaddress["number"], "accept_location_x" => $acceptaddress["location_x"], "accept_location_y" => $acceptaddress["location_y"], "distance" => $order["distance"], "delivery_time" => $order["delivery_time_cn"], "goods_name" => $order["note"], "goods_price" => $order["goods_price"], "goods_weight" => trim($order["goods_weight"]), "note" => $order["note"], "data" => array("order" => $required, "fees" => $order["data"]["fees"], "version" => "version_diy", "yinsihao_status" => intval($order["yinsihao_status"])), "delivery_fee" => $order["delivery_fee"], "delivery_tips" => floatval($order["delivery_tips"]), "total_fee" => $order["total_fee"], "discount_fee" => $order["discount_fee"], "final_fee" => $order["final_fee"], "deliveryer_fee" => 0, "deliveryer_total_fee" => 0, "order_channel" => "wxapp", "is_anonymous" => intval($_GPC["is_anonymous"]), "is_pay" => 0, "pay_type" => "", "status" => 1, "delivery_status" => 1, "addtime" => TIMESTAMP, "stat_year" => date("Y", TIMESTAMP), "stat_month" => date("Ym", TIMESTAMP), "stat_day" => date("Ymd", TIMESTAMP), "agent_discount_fee" => 0);
    $data["plateform_serve_fee"] = $data["delivery_fee"] + $data["delivery_tips"] - $data["discount_fee"];
    $data["plateform_serve"] = iserializer(array("fee" => $data["plateform_serve_fee"], "note" => "订单配送费 ￥" . $data["delivery_fee"] . " + 订单小费 ￥" . $data["delivery_tips"] . " - 使用红包 ￥" . $data["discount_fee"]));
    $data["spreadbalance"] = 1;
    if (check_plugin_perm("spread")) {
        pload()->model("spread");
        $data = order_spread_commission_calculate("paotui", $data);
    }
    $data["data"] = iserializer($data["data"]);
    pdo_insert("tiny_wmall_errander_order", $data);
    $orderid = pdo_insertid();
    if ($order["yinsihao_status"] == 1) {
        mload()->model("plugin");
        pload()->model("yinsihao");
        $bind_status = yinsihao_bind($orderid, "errander", $data["order_sn"], "errander");
        if (is_error($bind_status)) {
            slog("yinsihao", "隐私号绑定错误", array("order_id" => $order_id), "生成顾客跑腿隐私号错误" . $bind_status["message"]);
        }
    }
    errander_order_insert_discount($orderid, $order["activityed"]["list"]);
    errander_order_insert_status_log($orderid, "place_order");
    imessage(error(0, $orderid), "", "ajax");
}

?>