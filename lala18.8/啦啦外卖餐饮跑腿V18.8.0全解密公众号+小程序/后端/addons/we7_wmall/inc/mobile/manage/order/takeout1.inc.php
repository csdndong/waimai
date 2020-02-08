<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("deliveryer");
$_W["page"]["title"] = "订单管理";
$config = get_system_config("takeout.order");
if ($ta == "list") {
    $stat_day = trim($_GPC["date"]) ? str_replace("-", "", $_GPC["date"]) : date("Ymd");
    $condition = " WHERE uniacid = :uniacid AND sid = :sid AND stat_day = :stat_day AND (order_type = 1 or order_type = 2)";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":sid"] = $sid;
    $params[":stat_day"] = $stat_day;
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    if ($status == 1 && $config["show_no_pay"] == 1) {
        $condition .= " AND is_pay = 1";
    }
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " order by id desc limit 5", $params, "id");
    $min = 0;
    if (!empty($orders)) {
        foreach ($orders as &$da) {
            $da["pay_type_class"] = "";
            if ($da["is_pay"] == 1) {
                $da["pay_type_class"] = "have-pay";
                if ($da["pay_type"] == "delivery") {
                    $da["pay_type_class"] = "delivery-pay";
                }
            }
            $da["goods"] = order_fetch_goods($da["id"]);
            if ($da["status"] == "6") {
                $da["cancel_reason"] = order_cancel_reason($da["id"]);
            }
            $da["favorite_store"] = is_favorite_store($da["sid"], $da["uid"]);
            $da["data"] = iunserializer($da["data"]);
            if (!empty($da["data"]) && $da["data"]["yinsihao_status"] == 1) {
                $da["mobile"] = substr_replace($da["mobile"], "****", 3, 4);
            }
        }
        $min = min(array_keys($orders));
    }
    $order_status = order_status();
    $pay_types = order_pay_types();
    $deliveryers = deliveryer_fetchall($sid);
    include itemplate("order/takeoutList1");
}
if ($ta == "more") {
    $id = intval($_GPC["min"]);
    $stat_day = trim($_GPC["date"]) ? str_replace("-", "", $_GPC["date"]) : date("Ymd");
    $condition = " WHERE uniacid = :uniacid AND sid = :sid and stat_day = :stat_day and id < :id AND (order_type = 1 or order_type = 2)";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id, ":stat_day" => $stat_day);
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " order by id desc limit 5", $params, "id");
    if (!empty($orders)) {
        $pay_types = order_pay_types();
        $order_status = order_status();
        foreach ($orders as &$row) {
            $row["goods"] = order_fetch_goods($row["id"]);
            $row["addtime_cn"] = date("Y-m-d H:i:s", $row["addtime"]);
            $row["status_color"] = $order_status[$row["status"]]["color"];
            $row["status_cn"] = $order_status[$row["status"]]["text"];
            $row["delivery_mode"] = $store["delivery_mode"];
            $row["pay_type_class"] = "";
            if ($row["is_pay"] == 1) {
                $row["pay_type_class"] = "have-pay";
                if ($row["pay_type"] == "delivery") {
                    $row["pay_type_class"] = "delivery-pay";
                }
            }
            if ($row["status"] == "6") {
                $row["cancel_reason"] = order_cancel_reason($row["id"]);
            }
            $row["favorite_store"] = is_favorite_store($row["sid"], $row["uid"]);
        }
        $min = min(array_keys($orders));
    }
    $orders = array_values($orders);
    $respon = array("errno" => 0, "message" => $orders, "min" => $min);
    imessage($respon, "", "ajax");
}
if ($ta == "detail" || $ta == "consume") {
    $id = intval($_GPC["id"]);
    $order = order_fetch($id);
    if (empty($order)) {
        imessage("订单不存在或已删除", "", "error");
    }
    $goods = order_fetch_goods($order["id"]);
    $log = pdo_fetch("select * from " . tablename("tiny_wmall_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
    $activityed = order_fetch_discount($id);
    $logs = order_fetch_status_log($id);
    if (!empty($logs)) {
        $maxid = max(array_keys($logs));
    }
    if ($order["refund_status"]) {
        $refund = order_refund_fetch($id);
        $refund_logs = order_fetch_refund_log($id);
        if (!empty($refund_logs)) {
            $refundmaxid = max(array_keys($refund_logs));
        }
    }
    if ($order["data"] && $order["data"]["yinsihao_status"] == 1) {
        $order["mobile"] = substr_replace($order["mobile"], "****", 3, 4);
    }
    $order_types = order_types();
    $pay_types = order_pay_types();
    $order_status = order_status();
    $deliveryers = deliveryer_fetchall($sid);
    include itemplate("order/takeoutDetail");
}
if ($ta == "print") {
    $id = intval($_GPC["id"]);
    $status = order_print($id);
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    imessage(error(0, ""), "", "ajax");
}
if ($ta == "status") {
    $id = $_GPC["id"];
    $type = trim($_GPC["type"]);
    $result = order_status_update($id, $type);
    if (is_error($result)) {
        imessage(error(-1, "处理订单失败:" . $result["message"]), "", "ajax");
    }
    imessage(error(0, $result["message"]), referer(), "ajax");
}
if ($ta == "cancel") {
    $id = $_GPC["id"];
    $reasons = order_cancel_types("clerker");
    if ($_W["ispost"]) {
        $reason = $_GPC["reason"];
        if (empty($reason)) {
            imessage(error(-1, "请选择退款理由"), "", "ajax");
        }
        $remark = trim($_GPC["remark"]);
        $result = order_status_update($id, "cancel", array("reason" => $reason, "remark" => $remark, "note" => (string) $reasons[$reason] . " " . $remark));
        if (is_error($result)) {
            imessage(error(-1, $result["message"]), "", "ajax");
        }
        if ($result["message"]["is_refund"]) {
            imessage(error(0, "取消订单成功, 退款会在1-3个工作日打到客户账户"), imurl("manage/order/takeout/list"), "ajax");
        } else {
            imessage(error(0, "取消订单成功"), imurl("manage/order/takeout/list"), "ajax");
        }
    }
    include itemplate("order/takeoutOp");
}
if ($ta == "deliveryer") {
    $id = $_GPC["id"];
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    $result = order_assign_deliveryer($id, $deliveryer_id);
    if (is_error($result)) {
        imessage(error(-1, "ID为" . $id . "的订单分配配送员失败"), "", "ajax");
    }
    imessage(error(0, ""), "", "ajax");
}
if ($ta == "consume_post") {
    $id = intval($_GPC["id"]);
    $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
    }
    $result = order_status_update($id, "end");
    if (is_error($result)) {
        imessage(error(-1, "核销订单失败:" . $result["message"]), "", "ajax");
    }
    imessage(error(0, "核销订单成功"), referer(), "ajax");
}
if ($ta == "reply") {
    $id = intval($_GPC["id"]);
    $reply = trim($_GPC["reply"]);
    $result = order_status_update($id, "reply", array("reply" => $reply));
    imessage(error(0, ""), "", "ajax");
}
if ($ta == "refund") {
    $id = intval($_GPC["id"]);
    $order = order_fetch($id);
    if (empty($order)) {
        imessage("订单不存在或已删除", imurl("manage/order/takeout/list"), "error");
    }
    if (2 < $order["order_type"]) {
        imessage("外卖订单才可以部分商品退款", imurl("manage/order/takeout/list"), "error");
    }
    if (in_array($order["status"], array(4, 5, 6))) {
        imessage("不能部分退订商品", imurl("manage/order/takeout/list"), "error");
    }
    $is_refund = pdo_get("tiny_wmall_order_refund", array("uniacid" => $_W["uniacid"], "sid" => $sid, "order_id" => $id), array("id"));
    if (!empty($is_refund)) {
        imessage("已经在退款中，不能再次发起退款", imurl("manage/order/takeout/list"), "error");
    }
    $reasons = order_cancel_types("clerker");
    $goods = order_fetch_goods($id);
    if ($_W["ispost"]) {
        $refund_data = $_GPC["refund_data"];
        $refund_part = $refund_data["refund_part"];
        if (empty($refund_part)) {
            imessage(error(-1, "请选择退货商品"), "", "ajax");
        }
        if (empty($refund_data["refund_total_fee"])) {
            imessage(error(-1, "请选择退货商品"), "", "ajax");
        }
        $reason_key = trim($_GPC["reason"]);
        $reason = $reasons[$reason_key];
        if (empty($reason)) {
            imessage(error(-1, "原因不能为空"), "", "ajax");
        }
        $calculate_refund_fee = 0;
        $update_stat = array();
        foreach ($goods as $val) {
            if (!empty($refund_part[$val["id"]])) {
                $refund_item = $refund_part[$val["id"]];
                $normal_num = $val["goods_num"] - $val["goods_discount_num"];
                $refund_discount_num = 0;
                if ($refund_item["total_num"] <= $normal_num) {
                    $refund_item_fee = ($val["goods_unit_price"] + $val["box_price"]) / ($order["box_price"] + $order["price"]) * ($order["final_fee"] - $order["delivery_fee"] - $order["extra_fee"]) * $refund_item["total_num"];
                } else {
                    if ($normal_num < $refund_item["total_num"] && $refund_item["total_num"] <= $val["goods_num"]) {
                        $refund_item_fee = ($val["goods_unit_price"] + $val["box_price"]) / ($order["box_price"] + $order["price"]) * ($order["final_fee"] - $order["delivery_fee"] - $order["extra_fee"]) * $normal_num;
                        $discount_price = $val["goods_unit_price"] - ($val["goods_original_price"] - $val["goods_price"]) / $val["goods_discount_num"];
                        $refund_discount_num = $refund_item["total_num"] - $normal_num;
                        $refund_item_fee += ($discount_price + $val["box_price"]) / ($order["box_price"] + $order["price"]) * ($order["final_fee"] - $order["delivery_fee"] - $order["extra_fee"]) * $refund_discount_num;
                    }
                }
                $update_stat[$val["id"]] = array("goods_num" => $val["goods_num"] - $refund_item["total_num"], "goods_discount_num" => $val["goods_discount_num"] - $refund_discount_num, "goods_price" => $val["goods_price"] - round($refund_item_fee, 2), "goods_original_price" => $val["goods_price"] - round($refund_item_fee, 2));
                $refund_part[$val["id"]]["discount_num"] = $refund_discount_num;
                $refund_part[$val["id"]]["goods_unit_discount_price"] = $discount_price;
                $refund_part[$val["id"]]["fee"] = round($refund_item_fee, 2);
                $refund_part[$val["id"]]["goods_id"] = $val["goods_id"];
                $refund_part[$val["id"]]["goods_title"] = $val["goods_title"];
                $refund_part[$val["id"]]["goods_unit_price"] = $val["goods_unit_price"];
                $refund_part[$val["id"]]["option_id"] = $val["option_id"];
                $calculate_refund_fee += $refund_item_fee;
            } else {
                unset($refund_part[$val["id"]]);
            }
        }
        $calculate_refund_fee = round($calculate_refund_fee, 2);
        if ($calculate_refund_fee != $refund_data["refund_total_fee"]) {
            imessage(error(-1, "退款金额有误"), "", "ajax");
        }
        $insert = array("uniacid" => $order["uniacid"], "acid" => $order["acid"], "sid" => $order["sid"], "uid" => $order["uid"], "order_id" => $order["id"], "order_sn" => $order["ordersn"], "order_channel" => $order["order_channel"], "pay_type" => $order["pay_type"], "fee" => $calculate_refund_fee, "status" => 1, "out_trade_no" => $order["out_trade_no"], "out_refund_no" => date("YmdHis") . random(10, true), "apply_time" => TIMESTAMP, "reason" => "部分商品退款 " . $reason, "data" => iserializer($refund_part), "type" => "goods");
        pdo_insert("tiny_wmall_order_refund", $insert);
        $refund_id = pdo_insertid();
        order_insert_refund_log($order["id"], $refund_id, "apply", "部分商品退款");
        if ($order["is_pay"] && $order["pay_type"] != "delivery") {
            order_refund_status_update($order["id"], $refund_id, "handle");
        }
        foreach ($update_stat as $key => $val) {
            pdo_update("tiny_wmall_order_stat", $val, array("uniacid" => $_W["uniacid"], "id" => $key));
        }
        $update_order = array("final_fee" => $order["final_fee"] - $calculate_refund_fee);
        pdo_update("tiny_wmall_order", $update_order, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        imessage(error(0, "部分商品退订成功"), "", "ajax");
    }
    $store = store_fetch($sid, array("id", "title", "logo", "delivery_mode"));
    include itemplate("order/refund");
}
if ($ta == "refund_calculate") {
    $id = intval($_GPC["id"]);
    $stat_id = intval($_GPC["stat_id"]);
    $sign = trim($_GPC["sign"]);
    $refund_data = $_GPC["refund_data"];
    $refund_data = order_goods_cancel_calculate($id, $stat_id, $sign, $refund_data);
    if (is_error($refund_data)) {
        imessage($refund_data, "", "ajax");
    }
    $result = array("refund_data" => $refund_data);
    imessage(error(0, $result), "", "ajax");
}

?>