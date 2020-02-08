<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "create") {
    if (empty($_config_plugin["status"])) {
        $_config_plugin["close_reason"] = !empty($_config_plugin["close_reason"]) ? $_config_plugin["close_reason"] : "跑腿功能已关闭,请稍后";
        imessage(error(-1000, $_config_plugin["close_reason"]), imurl("errander/index"), "ajax");
    }
    $id = intval($_GPC["id"]);
    $category = errander_category_fetch($id);
    if (empty($category)) {
        imessage(error(-1000, "跑腿类型不存在"), imurl("errander/index"), "ajax");
    }
    if (empty($category["status"])) {
        imessage(error(-1001, "该跑腿类型已关闭"), imurl("errander/index"), "ajax");
    }
    $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
    if (empty($params)) {
        imessage(error(-1, "参数错误"), "", "ajax");
    }
    $address_buy = $params["address"]["buy"];
    if (!empty($address_buy["location_x"])) {
        $status = member_errander_address_check($address_buy);
        if (is_error($status)) {
            imessage(error(-1, "取货地址超出跑腿服务范围"), "", "ajax");
        }
    }
    $address_accept = $params["address"]["accept"];
    if (!empty($address_accept)) {
        $status = member_errander_address_check($address_accept);
        if (is_error($status)) {
            imessage(error(-1, "收货地址超出跑腿服务范围"), "", "ajax");
        }
    }
    $params["start_address"] = $address_buy;
    $params["end_address"] = $address_accept;
    $order = errander_order_calculate($category, $params);
    $fee = $order["delivery_fee_info"];
    $delivery_time_info = $order["delivery_times"];
    $order = array("uniacid" => $_W["uniacid"], "agentid" => $category["agentid"], "acid" => $_W["acid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "code" => random(4, true), "order_sn" => date("YmdHis") . random(6, true), "order_type" => $category["type"], "order_cid" => $category["id"], "buy_username" => $address_buy["realname"], "buy_mobile" => $address_buy["mobile"], "buy_sex" => $address_buy["sex"], "buy_address" => $address_buy["address"] . $address_buy["number"], "buy_location_x" => $address_buy["location_x"], "buy_location_y" => $address_buy["location_y"], "accept_mobile" => $address_accept["mobile"], "accept_username" => $address_accept["realname"], "accept_sex" => $address_accept["sex"], "accept_address" => $address_accept["address"] . $address_accept["number"], "accept_location_x" => $address_accept["location_x"], "accept_location_y" => $address_accept["location_y"], "distance" => $fee["distance"], "delivery_time" => (string) $delivery_time_info["predict_day_cn"] . " " . $delivery_time_info["predict_time_cn"], "goods_name" => trim($order["goods_name"]), "goods_price" => in_array($category["type"], array("buy", "multiaddress")) ? trim($_GPC["goods_price"]) : trim($_GPC["goods_price_cn"]), "goods_weight" => trim($fee["goods_weight"]), "note" => trim($order["note"]), "delivery_fee" => $fee["delivery_fee"], "delivery_tips" => floatval($fee["tip"]), "total_fee" => $fee["total_fee"], "discount_fee" => $fee["discount_fee"], "final_fee" => $fee["final_fee"], "deliveryer_fee" => 0, "deliveryer_total_fee" => 0, "order_channel" => $_W["ochannel"] == "wxapp" ? "wxapp" : "wechat", "is_anonymous" => intval($_GPC["is_anonymous"]), "is_pay" => 0, "pay_type" => "", "status" => 1, "delivery_status" => 1, "addtime" => TIMESTAMP, "stat_year" => date("Y", TIMESTAMP), "stat_month" => date("Ym", TIMESTAMP), "stat_day" => date("Ymd", TIMESTAMP), "agent_discount_fee" => 0);
    pdo_insert("tiny_wmall_errander_order", $order);
    $id = pdo_insertid();
    errander_order_insert_status_log($id, "place_order");
    if (!empty($address_buy)) {
        member_errander_address_add($address_buy, "history");
    }
    if (!empty($address_accept)) {
        member_errander_address_add($address_accept, "history");
    }
    imessage(error(0, $id), "", "ajax");
}
if ($op == "delivery_fee" && $_W["ispost"]) {
    $id = intval($_GPC["id"]);
    $extra = array("start_address_num" => intval($_GPC["start_address_num"]), "start_address" => $_GPC["start_address"], "end_address" => $_GPC["end_address"], "goods_weight" => $_GPC["goods_weight"], "predict_index" => intval($_GPC["predict_index"]), "delivery_tips" => floatval($_GPC["delivery_tips"]));
    $fee = errander_order_delivery_fee($id, $extra);
    if (is_error($fee)) {
        imessage(error(-1, $fee["message"]), "", "ajax");
    }
    imessage(error(0, $fee), "", "ajax");
}
if ($op == "list") {
    $total_user = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $condition = " where a.uniacid = :uniacid and a.uid = :uid ";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $id = intval($_GPC["min"]);
    if (0 < $id) {
        $condition .= " and a.id < :id";
        $params[":id"] = $id;
    }
    $orders = pdo_fetchall("select a.*, b.id as scene_id, b.name as title from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_page") . " as b on a.order_cid = b.id " . $condition . " order by id desc limit 7", $params, "id");
    $min = 0;
    if (!empty($orders)) {
        $order_status = errander_order_status();
        $min = min(array_keys($orders));
        foreach ($orders as &$row) {
            $row["data"] = iunserializer($row["data"]);
            if (empty($row["scene_id"])) {
                $row["title"] = "该场景已删除";
            }
            if (0 < $row["deliveryer_id"]) {
                $row["deliveryer"] = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $row["deliveryer_id"]));
            }
            $row["order_status"] = $order_status[$row["status"]]["text"];
            $row["addtime"] = date("Y-m-d H:i", $row["addtime"]);
        }
    }
    $respon = array("errno" => 0, "message" => array_values($orders), "min" => $min, "order_status" => $order_status);
    imessage($respon, "", "ajax");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    $status = errander_order_status_update($id, "cancel");
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    if ($status["message"]["is_refund"]) {
        $refund = errander_order_begin_payrefund($id);
        if (is_error($refund)) {
            imessage(error(-1, $refund["message"]), "", "ajax");
        }
        imessage(error(0, "取消订单成功," . $refund["message"]), "", "ajax");
    } else {
        imessage(error(0, "取消订单成功"), "", "ajax");
    }
}
if ($op == "end") {
    $id = intval($_GPC["id"]);
    $status = errander_order_status_update($id, "end");
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    imessage(error(0, "确认收货成功"), referer(), "ajax");
}
if ($op == "detail") {
    $_W["page"]["title"] = "订单详情";
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    $log = pdo_fetch("select * from " . tablename("tiny_wmall_errander_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
    $logs = errander_order_fetch_status_log($id);
    if (!empty($logs)) {
        $maxid = max(array_keys($logs));
        $minid = min(array_keys($logs));
        foreach ($logs as &$log) {
            $log["addtime"] = date("H:i", $log["addtime"]);
        }
    }
    if (0 < $order["refund_status"]) {
        $refund_logs = errander_order_fetch_refund_status_log($id);
        if (!empty($refund_logs)) {
            $refundmaxid = max(array_keys($refund_logs));
            foreach ($refund_logs as &$val) {
                $val["addtime"] = date("H:i", $val["addtime"]);
            }
        }
    }
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]));
    if ($order["data"]["yinsihao_status"] == 1) {
        $deliveryer["mobile"] = substr_replace($deliveryer["mobile"], "****", 3, 4);
    }
    $order_types = errander_types();
    $pay_types = order_pay_types();
    $order_status = errander_order_status();
    $order["order_status"] = $order_status[$order["status"]]["text"];
    $show_location = 0;
    if ($order["status"] == 2 && in_array($order["delivery_handle_type"], array("app", "wxapp"))) {
        $show_location = 1;
    }
    $result = array("order" => $order, "deliveryer" => $deliveryer, "log" => $log, "logs" => $logs, "maxid" => $maxid, "minid" => $minid, "refund_logs" => $refund_logs, "refundmaxid" => $refundmaxid, "show_location" => $show_location, "config_mall" => array("mobile" => $_config_mall["mobile"]));
    imessage(error(0, $result), "", "ajax");
}
if ($op == "vi_list") {
    $total_user = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $condition = " where a.uniacid = :uniacid and a.uid = :uid";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 7;
    $orders = pdo_fetchall("select a.*, b.id as scene_id, b.name as title, b.thumb from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_page") . " as b on a.order_cid = b.id " . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($orders)) {
        $order_status = errander_order_status();
        foreach ($orders as &$row) {
            $row["data"] = iunserializer($row["data"]);
            if (empty($row["scene_id"])) {
                $row["title"] = "该场景已删除";
            }
            $row["thumb"] = tomedia($row["thumb"]);
            if (0 < $row["deliveryer_id"]) {
                $row["deliveryer"] = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $row["deliveryer_id"]));
            }
            $row["order_status"] = $order_status[$row["status"]]["text"];
            $row["addtime"] = date("Y-m-d H:i", $row["addtime"]);
        }
    } else {
        $others = pdo_fetchall("select a.*,b.name as title,b.thumb from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_page") . " as b on a.order_cid = b.id where a.uniacid = :uniacid order by a.id desc limit 5", array(":uniacid" => $_W["uniacid"]));
        if (!empty($others)) {
            foreach ($others as &$val) {
                $val["addtime"] = date("Y-m-d H:i", $val["addtime"]);
                $val["thumb"] = tomedia($val["thumb"]);
            }
        }
    }
    $result = array("orders" => $orders, "total_user" => $total_user, "config" => $_config_plugin, "others" => $others);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($op == "location") {
        $orderid = intval($_GPC["id"]);
        $order = errander_order_fetch($orderid);
        mload()->model("deliveryer");
        $deliveryer = deliveryer_fetch($order["deliveryer_id"]);
        $points = array(array("latitude" => $deliveryer["location_x"], "longitude" => $deliveryer["location_y"]), array("latitude" => $order["accept_location_x"], "longitude" => $order["accept_location_y"]));
        $markers = array();
        $markers["deliveryer"] = array("iconPath" => "/static/img/store1.png", "vue_icon" => $deliveryer["avatar"], "id" => 1, "latitude" => $deliveryer["location_x"], "longitude" => $deliveryer["location_y"], "width" => 40, "height" => 40, "callout" => array("content" => "配送员：" . $deliveryer["title"], "color" => "#fff", "fontSize" => 12, "borderRadius" => 5, "padding" => 5, "bgColor" => "#ff244b", "display" => "ALWAYS", "textAlign" => "center"));
        $markers["accept"] = array("iconPath" => "/static/img/shou.png", "id" => 2, "latitude" => $order["accept_location_x"], "longitude" => $order["accept_location_y"], "width" => 40, "height" => 40, "callout" => array("content" => "收货", "color" => "#fff", "fontSize" => 12, "borderRadius" => 5, "padding" => 5, "bgColor" => "#ff244b", "display" => "ALWAYS", "textAlign" => "center"));
        if (!empty($order["buy_location_x"]) && !empty($order["buy_location_y"])) {
            $markers["buy"] = array("iconPath" => "/static/img/qu.png", "id" => 3, "latitude" => $order["buy_location_x"], "longitude" => $order["buy_location_y"], "width" => 40, "height" => 40, "callout" => array("content" => "取货", "color" => "#fff", "fontSize" => 12, "borderRadius" => 5, "padding" => 5, "bgColor" => "#ff244b", "display" => "ALWAYS", "textAlign" => "center"));
            $points[] = array("latitude" => $order["buy_location_x"], "longitude" => $order["buy_location_y"]);
        }
        $result = array("order" => $order, "deliveryer" => $deliveryer, "markers" => array_values($markers), "points" => $points);
        imessage(error(0, $result), "", "ajax");
    }
}

?>