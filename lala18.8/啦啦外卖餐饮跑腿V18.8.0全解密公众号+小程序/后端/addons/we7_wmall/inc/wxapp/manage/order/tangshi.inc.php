<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("table");
if ($ta == "list") {
    $condition = " WHERE uniacid = :uniacid AND sid = :sid AND order_type > 2";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":sid"] = $sid;
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($orders)) {
        $pay_types = order_pay_types();
        $order_status = order_status();
        $order_reserve_types = order_reserve_type();
        foreach ($orders as &$da) {
            $table = table_fetch($da["table_id"]);
            $da["table_sn"] = $table["title"];
            $da["table_category_title"] = $table["category"]["title"];
            $da["goods"] = order_fetch_goods($da["id"]);
            $da["pay_type_class"] = "";
            if ($da["is_pay"] == 1) {
                $da["pay_type_class"] = "have-pay";
                if ($da["pay_type"] == "delivery") {
                    $da["pay_type_class"] = "delivery-pay";
                }
            }
            $da["addtime_cn"] = date("Y-m-d H:i", $da["addtime"]);
            $da["order_status_cn"] = $order_status[$da["status"]]["text"];
            $da["pay_type_cn"] = $pay_types[$da["pay_type"]]["text"];
            $da["reserve_type_cn"] = $order_reserve_types[$da["reserve_type"]]["text"];
        }
    }
    $result = array("orders" => $orders);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "detail") {
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
        $order_types = order_types();
        $pay_types = order_pay_types();
        $order_status = order_status();
        $table_categorys = table_category_fetchall($sid);
        include itemplate("order/tangshiDetail");
    } else {
        if ($ta == "print") {
            $id = intval($_GPC["id"]);
            $status = order_print($id);
            if (is_error($status)) {
                imessage($status, "", "ajax");
            }
            imessage(error(0, ""), "", "ajax");
        } else {
            if ($ta == "status") {
                $id = $_GPC["id"];
                $type = trim($_GPC["type"]);
                $result = order_status_update($id, $type);
                if (is_error($result)) {
                    imessage(error(-1, "处理订单失败:" . $result["message"]), "", "ajax");
                }
                imessage(error(0, $result["message"]), "", "ajax");
            } else {
                if ($ta == "cancel") {
                    $id = $_GPC["id"];
                    $result = order_status_update($id, "cancel");
                    if (is_error($result)) {
                        imessage(error(-1, $result["message"]), "", "ajax");
                    }
                    if ($result["message"]["is_refund"]) {
                        imessage(error(0, "取消订单成功, 退款会在1-3个工作日打到客户账户"), "", "ajax");
                    } else {
                        imessage(error(0, "取消订单成功"), "", "ajax");
                    }
                }
            }
        }
    }
}
if ($ta == "pay_status") {
    $id = intval($_GPC["id"]);
    $result = order_status_update($id, "pay");
    if (is_error($result)) {
        imessage($result["message"], "", "ajax");
    }
    imessage(error(0, "设置订单支付成功"), "", "ajax");
}

?>