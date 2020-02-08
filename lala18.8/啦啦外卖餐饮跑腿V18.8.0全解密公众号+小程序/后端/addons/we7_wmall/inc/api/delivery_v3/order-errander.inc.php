<?php
defined("IN_IA") or exit("Access Denied");
include WE7_WMALL_PLUGIN_PATH . "errander/model.php";
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
$deliveryer = $_W["we7_wmall"]["deliveryer"]["user"];
if (empty($deliveryer["is_errander"])) {
    message(ierror(-1, "您没有平台跑腿单的配送权限，请联系管理员授权"), "", "ajax");
}
if ($op == "list") {
    $condition = " WHERE a.uniacid = :uniacid and a.agentid = :agentid and a.is_pay = 1 and a.status != 4";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    $can_collect_order = $deliveryer["work_status"];
    $can_collect_order_cn = "";
    if (empty($can_collect_order)) {
        $can_collect_order_cn = "您已停工，开工后再进行接单！";
    }
    if ($config_errander["dispatch_mode"] != 1 && !$config_errander["can_collect_order"]) {
        $can_collect_order = 0;
        $can_collect_order_cn = "当前调度模式不允许抢单,请等待管理员/系统派单";
    }
    $condition .= " and a.delivery_status = :status";
    $params[":status"] = $status;
    if ($status == 1) {
        $condition .= " and " . $_W["deliveryer"]["work_status"] . " and " . $can_collect_order;
    } else {
        if ($status == 4) {
            $condition .= " and a.deliveryer_id = :deliveryer_id";
        } else {
            $condition .= " and ((a.deliveryer_id = :deliveryer_id and a.transfer_delivery_status = 0) or (a.delivery_collect_type = :delivery_collect_type and a.transfer_deliveryer_id = :transfer_deliveryer_id and a.transfer_delivery_status = 1))";
            $params[":delivery_collect_type"] = 3;
            $params[":transfer_deliveryer_id"] = $deliveryer["id"];
        }
        $params[":deliveryer_id"] = $deliveryer["id"];
    }
    $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "load";
    $id = intval($_GPC["id"]);
    if ($type == "load") {
        if (0 < $id) {
            $condition .= " and a.id < :id";
            $params[":id"] = $id;
        }
    } else {
        $condition .= " and a.id > :id";
        $params[":id"] = $id;
    }
    $min_id = intval(pdo_fetchcolumn("SELECT min(id) as min_id FROM " . tablename("tiny_wmall_errander_order") . " as a " . $condition, $params));
    $orders = pdo_fetchall("SELECT a.*,b.title FROM " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_category") . " as b on a.order_cid = b.id " . $condition . " order by a.id desc limit 15", $params, "id");
    $min = $max = 0;
    if (!empty($orders)) {
        $errander_type = errander_types();
        foreach ($orders as &$da) {
            $da["data"] = iunserializer($da["data"]);
            if ($da["delivery_status"] == 1) {
                $da["deliveryer_fee"] = errander_order_calculate_deliveryer_fee($da, $deliveryer);
                $da["deliveryer_total_fee"] = $da["deliveryer_fee"] + $da["delivery_tips"];
            }
            $da["deliveryer_fee"] = floatval($da["deliveryer_fee"]);
            $da["deliveryer_total_fee"] = floatval($da["deliveryer_total_fee"]);
            $da["order_type_cn"] = $errander_type[$da["order_type"]]["text"];
            $da["addtime_cn"] = date("m-d H:i", $da["addtime"]);
            if ($da["order_type"] == "buy") {
                $da["buy_address_title"] = "买";
                $da["accept_address_title"] = "送";
                $da["buy_address"] = $da["buy_address"] ? $da["buy_address"] : "用户未指定，可自由选择";
            } else {
                $da["buy_address_title"] = "取";
                $da["accept_address_title"] = "送";
            }
            $da["store2user_distance"] = $da["distance"] ? strval($da["distance"]) : "未知";
            $da["store2deliveryer_distance"] = "未知";
            if (!empty($da["buy_location_x"]) && !empty($da["buy_location_y"]) && !empty($deliveryer["location_x"]) && !empty($deliveryer["location_y"])) {
                $da["store2deliveryer_distance"] = distanceBetween($da["buy_location_y"], $da["buy_location_x"], $deliveryer["location_y"], $deliveryer["location_x"]);
                $da["store2deliveryer_distance"] = strval(round($da["store2deliveryer_distance"] / 1000, 2));
                $da["store2deliveryer_distance"] = strval($da["store2deliveryer_distance"]);
            }
            $da["verification_code"] = $config_errander["verification_code"] == 1 ? 1 : 0;
            $da["delivery_collect_type_cn"] = order_collect_type($da);
            $da["transfer_delivery_reason"] = $da["data"]["transfer_delivery_reason"];
        }
        $more = 1;
        $min = min(array_keys($orders));
        $max = max(array_keys($orders));
        if ($min <= $min_id) {
            $more = 0;
        }
    }
    $orders = array_values($orders);
    $data = array("list" => $orders, "max_id" => $max, "min_id" => $min, "more" => $more, "can_collect_order" => $can_collect_order, "can_collect_order_cn" => $can_collect_order_cn);
    $delivery_status = order_delivery_status();
    $respon = array("resultCode" => 0, "resultMessage" => "调用成功", "data" => $data);
    message($respon, "", "ajax");
}
if ($op == "detail") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        message(ierror(-1, "订单不存在或已删除"), "", "ajax");
    }
    $order["category"] = (object) $order["category"];
    if (empty($order["data"])) {
        $order["data"] = array("channel" => "wap", "order" => array());
    }
    if (empty($order["data"]["order"])) {
        $order["data"]["order"] = array();
    }
    if (empty($order["data"]["order"]["partData"])) {
        $order["data"]["order"]["partData"] = array();
    }
    if (empty($order["data"]["order"]["extra_fee"])) {
        $order["data"]["order"]["extra_fee"] = array();
    }
    if (!empty($order["data"]["order"]["partData"])) {
        foreach ($order["data"]["order"]["partData"] as &$item) {
            if ($item["type"] == "multipleChoices") {
                $item["value"] = implode(",", $item["value"]);
            }
        }
    }
    unset($order["agent_serve"]);
    unset($order["plateform_serve"]);
    unset($order["category"]);
    $errander_type = errander_types();
    $pay_types = order_pay_types();
    $order_status = errander_order_status();
    $order["deliveryer_transfer_status"] = 0;
    if ($deliveryer["perm_transfer"]["status_errander"] == 1 && in_array($order["delivery_status"], array(2, 3))) {
        $order["deliveryer_transfer_status"] = "1";
    }
    $order["deliveryer_transfer_reason"] = $config_errander["deliveryer_transfer_reason"];
    $order["deliveryer_cancel_status"] = 0;
    if ($deliveryer["perm_cancel"]["status_errander"] == 1 && in_array($order["delivery_status"], array(2, 3)) && $order["deliveryer_id"] == $deliveryer["id"]) {
        $order["deliveryer_cancel_status"] = "1";
    }
    $order["deliveryer_cancel_reason"] = $config_errander["deliveryer_cancel_reason"];
    $order["order_type_cn"] = $errander_type[$order["order_type"]]["text"];
    $order["pay_type_cn"] = $pay_types[$order["pay_type"]]["text"];
    $order["addtime_cn"] = date("m-d H:i", $order["addtime"]);
    if ($order["order_type"] == "buy") {
        $order["buy_address_title"] = "买";
        $order["accept_address_title"] = "送";
        $order["buy_address"] = $order["buy_address"] ? $order["buy_address"] : "用户未指定，可自由选择";
    } else {
        $order["buy_address_title"] = "取";
        $order["accept_address_title"] = "送";
    }
    $order["addtime_cn"] = date("Y-m-d H:i", $order["addtime"]);
    $order["paytime_cn"] = date("Y-m-d H:i", $order["paytime"]);
    $order["deliveryingtime_cn"] = date("Y-m-d H:i", $order["delivery_assign_time"]);
    $order["deliveryinstoretime_cn"] = date("Y-m-d H:i", $order["delivery_instore_time"]);
    $order["deliverysuccesstime_cn"] = date("Y-m-d H:i", $order["delivery_success_time"]);
    $order["delivery_success_time_cn"] = array("day" => "未知", "time" => "未知");
    $order["delivery_instore_time_cn"] = $order["delivery_success_time_cn"];
    $order["delivery_assign_time_cn"] = $order["delivery_instore_time_cn"];
    if (!empty($order["delivery_assign_time"])) {
        $order["delivery_assign_time_cn"] = array("day" => date("m-d", $order["delivery_assign_time"]), "time" => date("H:i", $order["delivery_assign_time"]));
    }
    if (!empty($order["delivery_instore_time"])) {
        $order["delivery_instore_time_cn"] = array("day" => date("m-d", $order["delivery_instore_time"]), "time" => date("H:i", $order["delivery_instore_time"]));
    }
    if (!empty($order["delivery_success_time"])) {
        $order["delivery_success_time_cn"] = array("day" => date("m-d", $order["delivery_success_time"]), "time" => date("H:i", $order["delivery_success_time"]));
    }
    $deliveryer = deliveryer_fetch($deliveryer["id"]);
    $order["deliveryer"] = (object) array("title" => $deliveryer["title"], "mobile" => $deliveryer["mobile"], "age" => $deliveryer["age"], "sex" => $deliveryer["sex"], "location_x" => $deliveryer["location_x"], "location_y" => $deliveryer["location_y"]);
    $order["store2user_distance"] = $order["distance"] ? $order["distance"] : "未知";
    $order["store2deliveryer_distance"] = "未知";
    if (!empty($order["buy_location_x"]) && !empty($order["buy_location_y"]) && !empty($deliveryer["location_x"]) && !empty($deliveryer["location_y"])) {
        $order["store2deliveryer_distance"] = distanceBetween($order["buy_location_y"], $order["buy_location_x"], $deliveryer["location_y"], $deliveryer["location_x"]);
        $order["store2deliveryer_distance"] = round($order["store2deliveryer_distance"] / 1000, 2);
        $order["store2deliveryer_distance"] = strval($order["store2deliveryer_distance"]);
    }
    $order["deliveryer_fee"] = floatval($order["deliveryer_fee"]);
    $order["deliveryer_total_fee"] = floatval($order["deliveryer_total_fee"]);
    $order["verification_code"] = $config_errander["verification_code"] == 1 ? 1 : 0;
    message(ierror(0, "", $order), "", "ajax");
}
if ($op == "collect") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        message(ierror(-1, "订单不存在或已删除"), "", "ajax");
    }
    $status = errander_order_status_update($id, "delivery_assign", array("deliveryer_id" => $deliveryer["id"]));
    if (is_error($status)) {
        message(ierror(-1, $status["message"]), "", "ajax");
    }
    message(ierror(0, "抢单成功"), "", "ajax");
}
if ($op == "instore") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        message(ierror(-1, "订单不存在或已删除"), "", "ajax");
    }
    $status = errander_order_status_update($id, "delivery_instore", array("deliveryer_id" => $deliveryer["id"], "delivery_handle_type" => "app"));
    if (is_error($status)) {
        message(ierror(-1, $status["message"]), "", "ajax");
    }
    message(ierror(0, "确认取货成功"), "", "ajax");
}
if ($op == "success") {
    $id = intval($_GPC["id"]);
    $status = errander_order_status_update($id, "delivery_success", array("deliveryer_id" => $deliveryer["id"], "code" => trim($_GPC["code"])));
    if (is_error($status)) {
        message(ierror(-1, $status["message"]), "", "ajax");
    }
    message(ierror(0, "确认送达成功"), "", "ajax");
}
if ($op == "transfer_reason") {
    if (empty($config_errander["deliveryer_transfer_reason"])) {
        $config_errander["deliveryer_transfer_reason"] = array("其它");
    }
    message(ierror(0, $config_errander["deliveryer_transfer_reason"]), "", "ajax");
}
if ($op == "transfer") {
    $id = intval($_GPC["id"]);
    $reason = urldecode($_GPC["reason"]);
    $result = errander_order_status_update($id, "delivery_transfer", array("deliveryer_id" => $deliveryer["id"], "reason" => $reason));
    if (is_error($result)) {
        message(ierror(-1, $result["message"]), "", "ajax");
    }
    message(ierror(0, "转单成功"), "", "ajax");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    $reason = urldecode($_GPC["reason"]);
    if (empty($reason)) {
        message(ierror(0, "取消订单原因不能为空"), "", "ajax");
    }
    $extra = array("deliveryer_id" => $deliveryer["id"], "reason" => "other", "note" => $reason);
    $result = errander_order_status_update($id, "cancel", $extra);
    if (is_error($result)) {
        message(ierror(-1, $result["message"]), "", "ajax");
    }
    message(ierror(0, "订单取消成功"), "", "ajax");
}
if ($op == "direct_transfer") {
    $deliveryers = array_values(deliveryer_fetchall(0, array("order_type" => "is_errander")));
    $data = array("verify_reason" => 1, "tips" => "注意：您不能随意转单啊！否则扣你工资奖金绩效各种。", "deliveryers" => $deliveryers);
    message(ierror(0, "", $data), "", "ajax");
}
if ($op == "direct_transfer_begin") {
    $reason = urldecode($_GPC["reason"]);
    if (empty($reason)) {
        message(ierror(0, "转单原因不能为空"), "", "ajax");
    }
    $id = intval($_GPC["id"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    $extra = array("from_deliveryer_id" => $deliveryer["id"], "to_deliveryer_id" => $deliveryer_id, "note" => $reason);
    $result = errander_order_status_update($id, "direct_transfer", $extra);
    if (is_error($result)) {
        message(ierror(-1, $result["message"]), "", "ajax");
    }
    message(ierror(0, "发起定向转单申请成功，请等待目标配送员回复"), "", "ajax");
    return 1;
}
if ($op == "direct_transfer_reply") {
    $id = intval($_GPC["id"]);
    $result = trim($_GPC["result"]);
    if (empty($result)) {
        message(ierror(-1, "请选择是否同意接受订单"), "", "ajax");
    }
    $extra = array("deliveryer_id" => $deliveryer["id"], "result" => $result);
    $result = errander_order_status_update($id, "direct_transfer_reply", $extra);
    if (is_error($result)) {
        message(ierror(-1, $result["message"]), "", "ajax");
    }
    message(ierror(0, $result["message"]), "", "ajax");
}

?>