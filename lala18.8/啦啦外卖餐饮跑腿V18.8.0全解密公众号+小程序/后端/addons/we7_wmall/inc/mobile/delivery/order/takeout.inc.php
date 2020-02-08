<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if (empty($_W["deliveryer"]["perm_takeout"])) {
    imessage("您没有外卖单的配送权限，请联系管理员授权", ivurl("pages/home/index", array(), true), "error");
}
if ($ta == "list") {
    $_W["page"]["title"] = "订单列表";
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 3;
    $can_collect_order = 1;
    if ($config_takeout["order"]["dispatch_mode"] != 1 && !$config_takeout["order"]["can_collect_order"]) {
        $can_collect_order = 0;
    }
    if ($status == 3) {
        $condition .= " and " . $_W["deliveryer"]["work_status"] . " and delivery_status = :status";
        $params[":status"] = $status;
        if ($_W["deliveryer"]["perm_takeout"] == 1) {
            $condition .= " and " . $can_collect_order . " and delivery_type = 2";
        } else {
            if ($_W["deliveryer"]["perm_takeout"] == 2) {
                $condition .= " and delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")";
            } else {
                $condition .= " and (delivery_type = 2 or (delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")))";
            }
        }
        if ($config_takeout["order"]["deliverynoassign_sort_type"] == "desc") {
            $condition .= " order by id desc";
        } else {
            $condition .= " order by id asc";
        }
        if (0 < $config_takeout["order"]["max_dispatching"]) {
            $condition .= " limit " . $config_takeout["order"]["max_dispatching"];
        }
    } else {
        if ($status == 5) {
            $condition .= " and deliveryer_id = :deliveryer_id and delivery_status = 5";
        } else {
            if ($status == 7) {
                $condition .= " and (delivery_status = 7 or delivery_status = 8)";
            } else {
                if ($status == 4) {
                    $condition .= " and delivery_status = 4";
                }
            }
            $condition .= " and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = :delivery_collect_type and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1))";
            $params[":delivery_collect_type"] = 3;
            $params[":transfer_deliveryer_id"] = $_deliveryer["id"];
        }
        $params[":deliveryer_id"] = $_deliveryer["id"];
        $condition .= " order by id desc limit 15";
    }
    $orders = pdo_fetchall("SELECT id,order_plateform,serial_sn, addtime, is_pay, pay_type,order_type, status, username, mobile, address,distance, delivery_status,deliveryer_id,plateform_deliveryer_fee, delivery_type, delivery_fee, delivery_time,sid, num, final_fee, delivery_collect_type, transfer_delivery_status, ordersn, data FROM " . tablename("tiny_wmall_order") . $condition, $params, "id");
    $min = 0;
    if (!empty($orders)) {
        $stores_id = array();
        foreach ($orders as &$da) {
            if ($da["status"] == 3) {
                $da["plateform_deliveryer_fee"] = order_calculate_deliveryer_fee($da, $_deliveryer);
                if (!$config_takeout["order"]["show_acceptaddress_when_firstdelivery"] && !$_deliveryer["order_takeout_num"]) {
                    $da["address"] = "<span class=\"color-primary\">接单后可见收货地址</span>";
                }
            }
            $da["pay_type_class"] = "";
            if ($da["is_pay"] == 1) {
                $da["pay_type_class"] = "have-pay";
                if ($da["pay_type"] == "delivery") {
                    $da["pay_type_class"] = "delivery-pay";
                }
            }
            $stores_id[] = $da["sid"];
            $da["remind"] = order_timeout_remind($da["id"]);
            $da["delivery_collect_type_cn"] = order_collect_type($da);
            $da["transfer_delivery_reason"] = $da["data"]["transfer_delivery_reason"];
            $da["data"] = iunserializer($da["data"]);
        }
        $stores_str = implode(",", array_unique($stores_id));
        $stores = pdo_fetchall("select id, title, address, telephone from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id in (" . $stores_str . ")", array(":uniacid" => $_W["uniacid"]), "id");
        $min = min(array_keys($orders));
    }
    $num3_condition = " WHERE uniacid = :uniacid and agentid = :agentid and " . $_W["deliveryer"]["work_status"] . " and delivery_status = :status";
    if ($_W["deliveryer"]["perm_takeout"] == 1) {
        $num3_condition .= " and " . $can_collect_order . " and delivery_type = 2";
    } else {
        if ($_W["deliveryer"]["perm_takeout"] == 2) {
            $num3_condition .= " and delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")";
        } else {
            $num3_condition .= " and (delivery_type = 2 or (delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")))";
        }
    }
    $num3 = pdo_fetch("select count(*) as num from " . tablename("tiny_wmall_order") . $num3_condition, array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":status" => 3));
    $num4 = pdo_fetch("select count(*) as num from " . tablename("tiny_wmall_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and delivery_status = 4", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_deliveryer["id"], ":transfer_deliveryer_id" => $_deliveryer["id"]));
    $num7 = pdo_fetch("select count(*) as num from " . tablename("tiny_wmall_order") . " WHERE uniacid = :uniacid and agentid = :agentid and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = 3 and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1)) and (delivery_status = 7 or delivery_status = 8)", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":deliveryer_id" => $_deliveryer["id"], ":transfer_deliveryer_id" => $_deliveryer["id"]));
    $delivery_status = order_delivery_status();
    include itemplate("order/takeoutList");
}
if ($ta == "more") {
    $id = intval($_GPC["min"]);
    $status = intval($_GPC["status"]);
    $orders = pdo_fetchall("select * from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and delivery_status = :delivery_status and deliveryer_id = :deliveryer_id and id < :id order by id desc limit 15", array(":uniacid" => $_W["uniacid"], ":delivery_status" => $status, ":deliveryer_id" => $_deliveryer["id"], ":id" => $id), "id");
    $min = 0;
    if (!empty($orders)) {
        $delivery_status = order_delivery_status();
        foreach ($orders as &$row) {
            $row["addtime_cn"] = date("Y-m-d H:i:s", $row["addtime"]);
            $row["status_color"] = $delivery_status[$row["delivery_status"]]["color"];
            $row["status_cn"] = $delivery_status[$row["delivery_status"]]["text"];
            $row["store"] = pdo_fetch("select address, telephone from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id = :sid", array(":uniacid" => $_W["uniacid"], ":sid" => $row["sid"]));
            $label = order_timeout_remind($row["id"]);
            $row["label"] = $label;
            $row["data"] = iunserializer($row["data"]);
        }
        $min = min(array_keys($orders));
    }
    $orders = array_values($orders);
    $respon = array("errno" => 0, "message" => $orders, "min" => $min);
    imessage($respon, "", "ajax");
}
if ($ta == "detail") {
    $_W["page"]["title"] = "订单详情";
    $id = intval($_GPC["id"]);
    $order = order_fetch($id);
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
    if ($_deliveryer["perm_transfer"]["status_takeout"] == 1 && in_array($order["delivery_status"], array(4, 7, 8))) {
        $order["deliveryer_transfer_status"] = 1;
    }
    $order["deliveryer_cancel_status"] = 0;
    if ($_deliveryer["perm_cancel"]["status_takeout"] == 1 && in_array($order["status"], array(2, 3, 4)) && $order["deliveryer_id"] == $_deliveryer["id"]) {
        $order["deliveryer_cancel_status"] = "1";
    }
    $order["delivery_collect_type_cn"] = order_collect_type($order);
    $goods = order_fetch_goods($order["id"]);
    $activityed = order_fetch_discount($id);
    $log = pdo_fetch("select * from " . tablename("tiny_wmall_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
    $store = store_fetch($order["sid"], array("id", "title", "address", "telephone", "logo", "location_x", "location_y"));
    $order_types = order_types();
    $pay_types = order_pay_types();
    $order_status = order_status();
    include itemplate("order/takeoutDetail");
}
if ($ta == "collect") {
    $id = intval($_GPC["id"]);
    $result = order_deliveryer_update_status($id, "delivery_assign", array("deliveryer_id" => $_deliveryer["id"]));
    if (is_error($result)) {
        imessage($result, "", "ajax");
    }
    imessage(error(0, "抢单成功"), referer(), "ajax");
}
if ($ta == "success") {
    $id = intval($_GPC["id"]);
    $result = order_deliveryer_update_status($id, "delivery_success", array("deliveryer_id" => $_deliveryer["id"]));
    if (is_error($result)) {
        imessage($result, "", "ajax");
    }
    imessage(error(0, "确认送达成功"), referer(), "ajax");
}
if ($ta == "notice") {
    $id = intval($_GPC["id"]);
    $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
    }
    if (0 < $order["delivery_id"] && $order["delivery_id"] != $_deliveryer["id"]) {
        imessage(error(-1, "该订单不是由您配送,不能进行微信通知"), "", "ajax");
    }
    $content = array("title" => $_deliveryer["title"], "mobile" => $_deliveryer["mobile"]);
    order_status_notice($id, "delivery_notice", $content);
    imessage(error(0, "通知成功"), referer(), "ajax");
}
if ($ta == "instore") {
    $id = intval($_GPC["id"]);
    $result = order_deliveryer_update_status($id, "delivery_instore", array("deliveryer_id" => $deliveryer["id"], "delivery_handle_type" => "wechat"));
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "确认到店成功"), referer(), "ajax");
}
if ($ta == "takegoods") {
    $id = intval($_GPC["id"]);
    $result = order_deliveryer_update_status($id, "delivery_takegoods", array("deliveryer_id" => $deliveryer["id"], "delivery_handle_type" => "wechat"));
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "确认取货成功"), referer(), "ajax");
}
if ($ta == "delivery_transfer") {
    $id = intval($_GPC["id"]);
    $result = order_deliveryer_update_status($id, "delivery_transfer", array("deliveryer_id" => $deliveryer["id"], "reason" => trim($_GPC["reason"])));
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "转单成功"), referer(), "ajax");
}
if ($ta == "op") {
    $id = intval($_GPC["id"]);
    $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
    }
    $type = trim($_GPC["type"]);
    if ($type == "transfer") {
        $reasons = $config_takeout["order"]["deliveryer_transfer_reason"];
    } else {
        if ($type == "cancel") {
            $reasons = $config_takeout["order"]["deliveryer_cancel_reason"];
        } else {
            if ($type == "direct_transfer") {
                $deliveryers = array_values(deliveryer_fetchall(0));
            }
        }
    }
    include itemplate("order/takeoutOp");
    exit;
}
if ($ta == "cancel") {
    $id = intval($_GPC["id"]);
    $reason = trim($_GPC["reason"]);
    if (empty($reason)) {
        imessage(error(-1, "取消订单原因不能为空"), "", "ajax");
    }
    $extra = array("deliveryer_id" => $deliveryer["id"], "reason" => "other", "note" => $reason);
    $result = order_deliveryer_update_status($id, "delivery_cancel", $extra);
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "订单取消成功"), referer(), "ajax");
}
if ($ta == "direct_transfer") {
    $id = intval($_GPC["id"]);
    $order = order_fetch($id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
    }
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if ($deliveryer_id == $deliveryer["id"]) {
        imessage(error(-1, "不能转单给自己"), "", "ajax");
    }
    $result = order_deliveryer_update_status($id, "direct_transfer", array("from_deliveryer_id" => $deliveryer["id"], "to_deliveryer_id" => $deliveryer_id));
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    if ($order["delivery_status"] == 8) {
        $order["delivery_status"] = 7;
    }
    imessage(error(0, "发起定向转单申请成功，请等待目标配送员回复"), imurl("delivery/order/takeout/list", array("status" => $order["delivery_status"])), "ajax");
    return 1;
}
if ($ta == "direct_transfer_reply") {
    $id = intval($_GPC["id"]);
    $result = trim($_GPC["result"]);
    if (empty($result)) {
        imessage(error(-1, "请选择是否同意接受订单"), "", "ajax");
    }
    $extra = array("deliveryer_id" => $deliveryer["id"], "result" => $result);
    $result = order_deliveryer_update_status($id, "direct_transfer_reply", $extra);
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, $result["message"]), "", "ajax");
}

?>