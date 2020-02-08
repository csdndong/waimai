<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "create") {
    $id = intval($_GPC["id"]);
    $category = errander_category_fetch($id);
    if (empty($category)) {
        imessage(error(-1, "跑腿类型不存在"), "", "ajax");
    }
    $goods_name = trim($_GPC["goods_name"]);
    if (empty($goods_name)) {
        imessage(error(-1, "商品名称不能为空"), "", "ajax");
    }
    $start_address_id = intval($_GPC["start_address_id"]);
    if ($category["type"] == "buy") {
        $start_address = member_fetch_serve_address($start_address_id);
    } else {
        if ($category["type"] == "multiaddress") {
            $start_address_num = count($_GPC["multiaddress"]);
            if (!$start_address_num) {
                imessage(error(-1, "购买地址不能为空"), "", "ajax");
            }
            $address = implode(",", $_GPC["multiaddress"]);
            $start_address = array("address" => $address);
        } else {
            $start_address = member_fetch_address($start_address_id);
            if (empty($start_address)) {
                imessage(error(-1, "取货地址不存在"), "", "ajax");
            }
        }
    }
    $end_address_id = intval($_GPC["end_address_id"]);
    $end_address = member_fetch_address($end_address_id);
    if (empty($end_address)) {
        imessage(error(-1, "收货地址不存在"), "", "ajax");
    }
    $extra = array("start_address_num" => $start_address_num, "start_address" => $start_address, "end_address" => $end_address, "goods_weight" => floatval($_GPC["goods_weight"]), "predict_index" => intval($_GPC["predict_index"]), "delivery_tips" => floatval($_GPC["delivery_tips"]));
    $fee = errander_order_delivery_fee($id, $extra);
    if (is_error($fee)) {
        imessage(error(-1, $fee["message"]), "", "ajax");
    }
    $pay_type = trim($_GPC["pay_type"]);
    $payment = get_available_payment("errander");
    if (!in_array($pay_type, $payment)) {
        imessage(error(-1, "支付方式有误"), "", "ajax");
    }
    if (!is_array($_GPC["thumbs"])) {
        $_GPC["thumbs"] = array();
    }
    $delivery_day = trim($_GPC["delivery_day"]) ? date("Y") . "-" . trim($_GPC["delivery_day"]) : date("Y-m-d");
    $delivery_time = trim($_GPC["delivery_time"]) ? trim($_GPC["delivery_time"]) : "尽快送出";
    $order = array("uniacid" => $_W["uniacid"], "agentid" => $category["agentid"], "acid" => $_W["acid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "code" => random(4, true), "order_sn" => date("YmdHis") . random(6, true), "order_type" => $category["type"], "order_cid" => $category["id"], "buy_username" => $start_address["realname"], "buy_mobile" => $start_address["mobile"], "buy_sex" => $start_address["sex"], "buy_address" => $start_address["address"] . $start_address["number"], "buy_location_x" => $start_address["location_x"], "buy_location_y" => $start_address["location_y"], "accept_mobile" => $end_address["mobile"], "accept_username" => $end_address["realname"], "accept_sex" => $end_address["sex"], "accept_address" => $end_address["address"] . $end_address["number"], "accept_location_x" => $end_address["location_x"], "accept_location_y" => $end_address["location_y"], "distance" => $fee["distance"], "delivery_time" => (string) $delivery_day . " " . $delivery_time, "goods_name" => $goods_name, "goods_price" => in_array($category["type"], array("buy", "multiaddress")) ? trim($_GPC["goods_price"]) : trim($_GPC["goods_price_cn"]), "goods_weight" => trim($_GPC["goods_weight"]), "thumbs" => iserializer(array_filter($_GPC["thumbs"], trim)), "note" => trim($_GPC["note"]), "delivery_fee" => $fee["delivery_fee"], "delivery_tips" => floatval($fee["tip"]), "total_fee" => $fee["total_fee"], "discount_fee" => $fee["discount_fee"], "final_fee" => $fee["final_fee"], "deliveryer_fee" => 0, "deliveryer_total_fee" => 0, "is_anonymous" => intval($_GPC["is_anonymous"]), "is_pay" => 0, "pay_type" => $pay_type, "note" => trim($_GPC["note"]), "status" => 1, "delivery_status" => 1, "addtime" => TIMESTAMP, "stat_year" => date("Y", TIMESTAMP), "stat_month" => date("Ym", TIMESTAMP), "stat_day" => date("Ymd", TIMESTAMP), "agent_discount_fee" => 0);
    $name = $order["accept_username"];
    if ($order["is_anonymous"] == 1) {
        if (!empty($_config_plugin["anonymous"])) {
            $index = array_rand($_config_plugin["anonymous"]);
            $name = $_config_plugin["anonymous"][$index];
        } else {
            $name = cutstr($order["accept_username"], 1) . "**";
        }
    }
    $order["anonymous_username"] = $name;
    $order["plateform_serve_fee"] = $order["delivery_fee"] + $order["delivery_tips"] - $order["discount_fee"];
    $order["plateform_serve"] = iserializer(array("fee" => $order["plateform_serve_see"], "note" => "订单配送费 ￥" . $order["delivery_fee"] . " + 订单小费 ￥" . $order["delivery_tips"] . " - 使用红包" . $order["discount_fee"]));
    pdo_insert("tiny_wmall_errander_order", $order);
    $id = pdo_insertid();
    errander_order_insert_status_log($id, "place_order");
    isetcookie("errander_order", "", -1000);
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
    $_W["page"]["title"] = "跑腿订单";
    $total_user = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $orders = pdo_fetchall("select * from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and uid = :uid order by id desc limit 15", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]), "id");
    $min = 0;
    if (!empty($orders)) {
        $order_status = errander_order_status();
        $min = min(array_keys($orders));
        foreach ($orders as &$row) {
            $row["data"] = iunserializer($row["data"]);
            if ($row["data"]["version"] == "version_diy") {
                $category = pdo_get("tiny_wmall_errander_page", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $row["order_cid"]), array("name"));
                $row["title"] = $category["name"];
            } else {
                $category = pdo_get("tiny_wmall_errander_category", array("uniacid" => $_W["uniacid"], "id" => $row["order_cid"]), array("title", "thumb"));
                $row["title"] = $category["title"];
                $row["thumb"] = $category["thumb"];
            }
            if (0 < $row["deliveryer_id"]) {
                $row["deliveryer"] = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $row["deliveryer_id"]));
            }
        }
    } else {
        $others = pdo_fetchall("select a.*,b.title,b.thumb from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_category") . " as b on a.order_cid = b.id where a.uniacid = :uniacid order by a.id desc limit 5", array(":uniacid" => $_W["uniacid"]), "id");
    }
    include itemplate("orderList");
}
if ($op == "more") {
    $id = intval($_GPC["min"]);
    $orders = pdo_fetchall("select a.*,b.title,b.thumb from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_category") . " as b on a.order_cid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 15", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":id" => $id), "id");
    $min = 0;
    if (!empty($orders)) {
        $order_status = errander_order_status();
        foreach ($orders as &$order) {
            $order["addtime_cn"] = date("Y-m-d H:i:s", $order["addtime"]);
            $order["time_cn"] = date("H:i", $order["addtime"]);
            $order["status_cn"] = $order_status[$order["status"]]["text"];
            $order["thumb"] = tomedia($order["thumb"]);
            $order["deliveryer"] = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]));
        }
        $min = min(array_keys($orders));
    }
    $orders = array_values($orders);
    $respon = array("errno" => 0, "message" => $orders, "min" => $min);
    imessage($respon, "", "ajax");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    $status = errander_order_status_update($id, "cancel");
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    imessage(error(0, "订单取消成功"), referer(), "ajax");
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
        imessage("订单不存在或已删除", "", "error");
    }
    $log = pdo_fetch("select * from " . tablename("tiny_wmall_errander_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
    $logs = errander_order_fetch_status_log($id);
    if (!empty($logs)) {
        $maxid = max(array_keys($logs));
    }
    if (0 < $order["refund_status"]) {
        $refund_logs = errander_order_fetch_refund_status_log($id);
        if (!empty($refund_logs)) {
            $refundmaxid = max(array_keys($refund_logs));
        }
    }
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]));
    $order_types = errander_types();
    $pay_types = order_pay_types();
    $order_status = errander_order_status();
    include itemplate("orderDetail");
}

?>