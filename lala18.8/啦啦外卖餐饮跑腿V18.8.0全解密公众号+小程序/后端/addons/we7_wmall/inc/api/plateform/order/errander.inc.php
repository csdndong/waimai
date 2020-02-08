<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("deliveryer");
mload()->model("plugin");
pload()->model("errander");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
$config_errander = get_plugin_config("errander");
if ($ta == "list") {
    $condition = " WHERE uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]) ? intval($_GPC["agentid"]) : $_W["agentid"];
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        if ($status < 5) {
            $condition .= " AND status = :status";
            $params[":status"] = $status;
        } else {
            if ($status = 5) {
                $condition .= " AND (refund_status = 1 or refund_status = 2)";
            }
        }
    }
    $re_status = intval($_GPC["refund_status"]);
    if (0 < $re_status) {
        $condition .= " AND refund_status = :refund_status";
        $params[":refund_status"] = $re_status;
    }
    $is_pay = isset($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : -1;
    if (0 <= $is_pay) {
        $condition .= " AND is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }
    $pay_type = trim($_GPC["pay_type"]);
    if (!empty($pay_type)) {
        $condition .= " AND is_pay = 1 AND pay_type = :pay_type";
        $params[":pay_type"] = $pay_type;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND (accept_username LIKE '%" . $keyword . "%' OR accept_mobile LIKE '%" . $keyword . "%' OR order_sn LIKE '%" . $keyword . "%')";
    }
    if (!empty($_GPC["addtime_start"])) {
        $starttime = strtotime($_GPC["addtime_start"]);
        $endtime = strtotime($_GPC["addtime_end"]) + 86399;
    } else {
        $starttime = strtotime("-7 day");
        $endtime = TIMESTAMP;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_errander_order") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($orders)) {
        $errander_types = errander_types();
        $deliveryers = deliveryer_all();
        foreach ($orders as &$da) {
            $da["order_type_cn"] = $errander_type[$da["order_type"]]["text"];
            $da["data"] = iunserializer($row["data"]);
            if ($row["data"]["version"] == "version_diy") {
                $category = pdo_get("tiny_wmall_errander_page", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $da["order_cid"]), array("name"));
                $da["title"] = $category["name"];
            } else {
                $category = pdo_get("tiny_wmall_errander_category", array("uniacid" => $_W["uniacid"], "id" => $da["order_cid"]), array("title"));
                $da["title"] = $category["title"];
            }
            $da["tags"] = array();
            if (!empty($da["order_type_cn"])) {
                $da["tags"][] = array("title" => $da["order_type_cn"], "color" => "#ff9988");
            }
            $da["tags"][] = array("title" => $da["title"], "color" => "#ff9900");
            if ($da["order_type"] == "buy") {
                $da["buy_address"] = $da["buy_address"] ? $da["buy_address"] : "用户未指定，可自由选择";
            }
            $da["deliveryer"] = (object) array();
            if (0 < $da["deliveryer_id"]) {
                $deliveryer = $deliveryers[$da["deliveryer_id"]];
                $da["deliveryer"] = array("id" => $deliveryer["id"], "title" => $deliveryer["title"], "mobile" => $deliveryer["mobile"]);
            }
            $da["addtime_cn"] = date("Y-m-d H:i", $da["addtime"]);
            $da["deliverytime_cn"] = (string) $da["delivery_time"];
        }
    }
    $result = array("order" => $orders);
    message(ierror(0, "", $result), "", "ajax");
    return 1;
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = errander_order_fetch($id);
        if (empty($order)) {
            message(ierror(-1, "订单不存在或已删除"), "", "ajax");
        }
        $pay_types = order_pay_types();
        $order_types = errander_types();
        $order_status = errander_order_status();
        $order["category"] = (object) $order["category"];
        if (empty($order["data"])) {
            $order["data"] = array("channel" => "wap");
        }
        if (!empty($order["data"]["order"]["partData"])) {
            foreach ($order["data"]["order"]["partData"] as &$item) {
                if ($item["type"] == "multipleChoices") {
                    $item["value"] = implode(",", $item["value"]);
                }
            }
        }
        $order["buy_address"] = $order["buy_address"] ? $order["buy_address"] : "用户未指定，可自由选择";
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
        mload()->model("deliveryer");
        $order["deliveryer"] = (object) array();
        if (0 < $order["deliveryer_id"]) {
            $deliveryer = deliveryer_fetch($order["deliveryer_id"]);
            $order["deliveryer"] = (object) array("title" => $deliveryer["title"], "mobile" => $deliveryer["mobile"], "age" => $deliveryer["age"], "sex" => $deliveryer["sex"], "location_x" => $deliveryer["location_x"], "location_y" => $deliveryer["location_y"], "order_takeout_num" => $deliveryer["order_takeout_num"], "order_errander_num" => $deliveryer["order_errander_num"]);
        }
        message(ierror(0, "", $order), "", "ajax");
        return 1;
    } else {
        if ($ta == "status") {
            $id = intval($_GPC["id"]);
            $type = trim($_GPC["type"]);
            if (empty($type)) {
                message(ierror(-1, "订单状态错误"), "", "ajax");
            }
            if (in_array($type, array("end"))) {
                $extra = array();
                $result = errander_order_status_update($id, $type);
                if (is_error($result)) {
                    message(ierror(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
                }
                message(ierror(0, "订单完成成功"), "", "ajax");
                return 1;
            }
            if ($type == "cancel") {
                $reason = trim($_GPC["reason"]);
                $extra = array("note" => $reason);
                $result = errander_order_status_update($id, $type, $extra);
                if (is_error($result)) {
                    message(ierror(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
                }
                if ($result["message"]["is_refund"]) {
                    $refund = errander_order_begin_payrefund($id);
                    if (is_error($refund)) {
                        message(ierror(-1, $refund["message"]), "", "ajax");
                    }
                    message(ierror(0, "取消订单成功," . $refund["message"]), "", "ajax");
                    return 1;
                }
                message(ierror(0, "取消订单成功"), "", "ajax");
                return 1;
            }
            if ($type == "refund_handle") {
                $result = errander_order_begin_payrefund($id);
                if (!is_error($result)) {
                    $query = errander_order_query_payrefund($id);
                    if (is_error($query)) {
                        message(ierror(-1, "发起退款成功, 获取退款状态失败"), "", "ajax");
                        return 1;
                    }
                    message(ierror(0, "发起退款成功, 退款状态已更新"), "", "ajax");
                    return 1;
                }
                message(ierror(-1, $result["message"]), "", "ajax");
                return 1;
            }
            if ($type == "refund_status") {
                pdo_update("tiny_wmall_errander_order", array("refund_status" => 3), array("uniacid" => $_W["uniacid"], "id" => $id));
                errander_order_insert_refund_log($id, "success");
                message(ierror(0, "设置为已退款成功"), referer(), "ajax");
                return 1;
            }
            if ($ta == "refund_query") {
                $query = errander_order_query_payrefund($id);
                if (is_error($query)) {
                    message(ierror(-1, "获取退款状态失败,失败原因：" . $query["message"]), "", "ajax");
                }
                message(ierror(0, "获取退款状态成功"), "", "ajax");
                return 1;
            }
        } else {
            if ($ta == "analyse") {
                $id = intval($_GPC["id"]);
                $result = errander_order_analyse($id, array("channel" => "plateform_dispatch"));
                if (is_error($result)) {
                    message(ierror(-1, $result["message"]), "", "ajax");
                }
                $result = array_elements(array("id", "buy_location_x", "buy_location_y", "accept_location_x", "accept_location_y", "deliveryers"), $result);
                message(ierror(0, "", $result), "", "ajax");
                return 1;
            }
            if ($ta == "dispatch") {
                $id = intval($_GPC["id"]);
                $deliveryer_id = intval($_GPC["deliveryer_id"]);
                $status = errander_order_assign_deliveryer($id, $deliveryer_id, true);
                if (is_error($status)) {
                    message(ierror(-1, $status["message"]), "", "ajax");
                }
                message(ierror(0, "分配订单成功"), "", "ajax");
                return 1;
            }
            if ($ta == "cancel_reason") {
                $reasons = $config_errander["deliveryer_cancel_reason"];
                if (empty($reasons)) {
                    $reasons = array("其他原因");
                }
                $result = array("reasons" => $reasons);
                message(ierror(0, "", $result), "", "ajax");
            }
        }
    }
}

?>