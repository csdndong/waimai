<?php
defined("IN_IA") or exit("Access Denied");
include WE7_WMALL_PLUGIN_PATH . "errander/model.php";
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if (empty($_W["deliveryer"]["is_errander"])) {
    imessage("您没有跑腿单的配送权限，请联系管理员授权", ivurl("pages/home/index", array(), true), "error");
}
if ($ta == "list") {
    $_W["page"]["title"] = "订单列表";
    $condition = " where a.uniacid = :uniacid and a.agentid = :agentid and a.is_pay = 1 and a.status != 4";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    $condition .= " and a.delivery_status = :status";
    $params[":status"] = $status;
    $can_collect_order = 1;
    if ($config_errander["dispatch_mode"] != 1 && !$config_errander["can_collect_order"]) {
        $can_collect_order = 0;
    }
    if ($status == 1) {
        $condition .= " and " . $_W["deliveryer"]["work_status"] . " and " . $can_collect_order;
    } else {
        if ($status == 4) {
            $condition .= " and a.deliveryer_id = :deliveryer_id";
        } else {
            $condition .= " and ((a.deliveryer_id = :deliveryer_id and a.transfer_delivery_status = 0) or (a.delivery_collect_type = :delivery_collect_type and a.transfer_deliveryer_id = :transfer_deliveryer_id and a.transfer_delivery_status = 1))";
            $params[":delivery_collect_type"] = 3;
            $params[":transfer_deliveryer_id"] = $_deliveryer["id"];
        }
        $params[":deliveryer_id"] = $_deliveryer["id"];
        $condition .= " order by a.id desc limit 15";
    }
    $orders = pdo_fetchall("SELECT  a.*, b.title FROM" . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_category") . " as b on a.order_cid = b.id " . $condition, $params, "id");
    $min = 0;
    if (!empty($orders)) {
        $types = errander_types();
        $delivery_status = errander_order_delivery_status();
        foreach ($orders as $key => &$row) {
            if ($row["delivery_status"] == 1) {
                $row["deliveryer_fee"] = errander_order_calculate_deliveryer_fee($row, $_deliveryer);
                $row["deliveryer_total_fee"] = $row["deliveryer_fee"] + $row["delivery_tips"];
            }
            $row["order_type_cn"] = $types[$row["order_type"]]["text"];
            $row["order_type_bg"] = $types[$row["order_type"]]["bg"];
            $row["delivery_status_cn"] = $delivery_status[$row["delivery_status"]]["text"];
            $row["delivery_status_color"] = $delivery_status[$row["delivery_status"]]["color"];
            $row["verification_code"] = $config_errander["verification_code"] == 1 ? 1 : 0;
            if (empty($row["buy_address"])) {
                $row["buy_address"] = "用户未指定,您可以自由寻找商户购买";
            }
            if (empty($row["goods_price"])) {
                $row["goods_price"] = "未填写,请联系顾客沟通";
            }
            $row["delivery_collect_type_cn"] = order_collect_type($row);
            $row["transfer_delivery_reason"] = $row["data"]["transfer_delivery_reason"];
            $row["data"] = iunserializer($row["data"]);
        }
        $min = min(array_keys($orders));
    }
    $num1 = pdo_fetch("select  count(*) as num from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_category") . " as b on a.order_cid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.is_pay = 1 and a.status != 4 and a.delivery_status = 1 and " . $_W["deliveryer"]["work_status"] . " and " . $can_collect_order, array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
    $num2 = pdo_fetch("select count(*) as num from " . tablename("tiny_wmall_errander_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and is_pay = 1 and delivery_status = 2 and status != 4", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_deliveryer["id"], ":transfer_deliveryer_id" => $_deliveryer["id"]));
    $num3 = pdo_fetch("select count(*) as num from " . tablename("tiny_wmall_errander_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and is_pay = 1 and delivery_status = 3 and status != 4", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_deliveryer["id"], ":transfer_deliveryer_id" => $_deliveryer["id"]));
    include itemplate("order/erranderList");
}
if ($ta == "more") {
    $id = intval($_GPC["min"]);
    $status = intval($_GPC["status"]);
    $orders = pdo_fetchall("select a.*, b.title from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_category") . " as b on a.order_cid = b.id  where a.uniacid = :uniacid and a.status != 4 and delivery_status = :delivery_status and deliveryer_id = :deliveryer_id and a.id < :id order by id desc limit 15", array(":uniacid" => $_W["uniacid"], ":delivery_status" => $status, ":deliveryer_id" => $_deliveryer["id"], ":id" => $id), "id");
    if (!empty($orders)) {
        $types = errander_types();
        $delivery_status = errander_order_delivery_status();
        foreach ($orders as &$order) {
            $order["addtime"] = date("Y-m-d H:i", $order["addtime"]);
            $order["order_type_cn"] = $types[$order["order_type"]]["text"];
            $order["order_type_bg"] = $types[$order["order_type"]]["bg"];
            $order["delivery_status_cn"] = $delivery_status[$order["delivery_status"]]["text"];
            $order["delivery_status_color"] = $delivery_status[$order["delivery_status"]]["color"];
            $order["data"] = iunserializer($order["data"]);
        }
    }
    $min = 0;
    if (!empty($orders)) {
        $min = min(array_keys($orders));
    }
    $orders = array_values($orders);
    $respon = array("errno" => 0, "message" => $orders, "min" => $min);
    imessage($respon, "", "ajax");
}
if ($ta == "detail") {
    $_W["page"]["title"] = "订单详情";
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        imessage("订单不存在或已删除", "", "error");
    }
    if (!empty($order["deliveryer_id"]) && $order["deliveryer_id"] != $_deliveryer["id"]) {
        imessage("该订单不是由您配送,无权查看订单详情", referer(), "error");
    }
    if ($order["transfer_delivery_status"] == 1) {
        imessage("您已经将该订单转给其他配送员,请等待其他配送员回复", referer(), "info");
    }
    $order["deliveryer_transfer_status"] = 0;
    if ($_deliveryer["perm_transfer"]["status_errander"] == 1 && in_array($order["delivery_status"], array(2, 3))) {
        $order["deliveryer_transfer_status"] = 1;
    }
    $order["deliveryer_cancel_status"] = 0;
    if ($_deliveryer["perm_cancel"]["status_errander"] == 1 && in_array($order["delivery_status"], array(2, 3)) && $order["deliveryer_id"] == $_deliveryer["id"]) {
        $order["deliveryer_cancel_status"] = "1";
    }
    $order["verification_code"] = $config_errander["verification_code"] == 1 ? 1 : 0;
    if ($order["data"]["yinsihao_status"] == 1) {
        $order["accept_mobile"] = substr_replace($order["accept_mobile"], "****", 3, 4);
    }
    include itemplate("order/erranderDetail");
}
if ($ta == "collect") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    $status = errander_order_status_update($id, "delivery_assign", array("deliveryer_id" => $_deliveryer["id"]));
    if (is_error($status)) {
        imessage(error(-1, $status["message"]), "", "ajax");
    }
    imessage(error(0, "抢单成功"), referer(), "ajax");
}
if ($ta == "instore") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    $status = errander_order_status_update($id, "delivery_instore", array("deliveryer_id" => $_deliveryer["id"]));
    if (is_error($status)) {
        imessage(error(-1, $status["message"]), "", "ajax");
    }
    imessage(error(0, "确认取货成功"), referer(), "ajax");
}
if ($ta == "success") {
    $id = intval($_GPC["id"]);
    $status = errander_order_status_update($id, "delivery_success", array("deliveryer_id" => $_deliveryer["id"], "code" => trim($_GPC["code"])));
    if (is_error($status)) {
        imessage(error(-1, $status["message"]), "", "ajax");
    }
    imessage(error(0, "确认送达成功"), referer(), "ajax");
}
if ($ta == "delivery_transfer") {
    $id = intval($_GPC["id"]);
    $result = errander_order_status_update($id, "delivery_transfer", array("deliveryer_id" => $_deliveryer["id"], "reason" => trim($_GPC["reason"])));
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "转单成功"), referer(), "ajax");
}
if ($ta == "op") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    $type = trim($_GPC["type"]);
    if ($type == "transfer") {
        $reasons = $config_errander["deliveryer_transfer_reason"];
    } else {
        if ($type == "cancel") {
            $reasons = $config_errander["deliveryer_cancel_reason"];
        } else {
            if ($type == "direct_transfer") {
                $deliveryers = array_values(deliveryer_fetchall(0, array("order_type" => "is_errander")));
            }
        }
    }
    include itemplate("order/erranderOp");
    exit;
}
if ($ta == "cancel") {
    $id = intval($_GPC["id"]);
    $reason = urldecode($_GPC["reason"]);
    if (empty($reason)) {
        imessage(error(-1, "取消订单原因不能为空"), "", "ajax");
    }
    $extra = array("deliveryer_id" => $deliveryer["id"], "reason" => "other", "note" => $reason);
    $result = errander_order_status_update($id, "cancel", $extra);
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "订单取消成功"), referer(), "ajax");
}
if ($ta == "direct_transfer") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
    }
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if ($deliveryer_id == $deliveryer["id"]) {
        imessage(error(-1, "不能转单给自己"), "", "ajax");
    }
    $result = errander_order_status_update($id, "direct_transfer", array("from_deliveryer_id" => $deliveryer["id"], "to_deliveryer_id" => $deliveryer_id));
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "发起定向转单申请成功，请等待目标配送员回复"), imurl("delivery/order/errander/list", array("status" => $order["delivery_status"])), "ajax");
    return 1;
}
if ($ta == "direct_transfer_reply") {
    $id = intval($_GPC["id"]);
    $result = trim($_GPC["result"]);
    if (empty($result)) {
        imessage(error(-1, "请选择是否同意接受订单"), "", "ajax");
    }
    $extra = array("deliveryer_id" => $deliveryer["id"], "result" => $result);
    $result = errander_order_status_update($id, "direct_transfer_reply", $extra);
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, $result["message"]), "", "ajax");
}

?>