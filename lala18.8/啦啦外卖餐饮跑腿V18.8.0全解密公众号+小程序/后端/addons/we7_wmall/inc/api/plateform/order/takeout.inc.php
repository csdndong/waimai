<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE uniacid = :uniacid and order_type < 3";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]) ? intval($_GPC["agentid"]) : $_W["agentid"];
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $uid = intval($_GPC["uid"]);
    if (0 < $uid) {
        $condition .= " AND uid = :uid";
        $params[":uid"] = $uid;
    }
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " AND sid = :sid";
        $params[":sid"] = $sid;
    }
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }
    $re_status = intval($_GPC["refund_status"]);
    if (0 < $re_status) {
        $condition .= " AND refund_status = :refund_status";
        $params[":refund_status"] = $re_status;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        if ($status < 7) {
            $condition .= " AND status = :status";
            $params[":status"] = $status;
        } else {
            if ($status = 7) {
                $condition .= " AND (refund_status = 1 or refund_status = 2)";
            } else {
                if ($status = 8) {
                    $condition .= " AND refund_status = 3";
                }
            }
        }
    }
    $is_remind = intval($_GPC["is_remind"]);
    if (0 < $is_remind) {
        $condition .= " AND is_remind = :is_remind";
        $params[":is_remind"] = $is_remind;
    }
    $is_pay = isset($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : 1;
    if (-1 < $is_pay) {
        $condition .= " AND is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }
    $pay_type = trim($_GPC["pay_type"]);
    if (!empty($pay_type)) {
        $condition .= " AND is_pay = 1 AND pay_type = :pay_type";
        $params[":pay_type"] = $pay_type;
    }
    $order_plateform = trim($_GPC["order_plateform"]);
    if (!empty($order_plateform)) {
        $condition .= " AND order_plateform = :order_plateform";
        $params[":order_plateform"] = $order_plateform;
    }
    $order_channel = trim($_GPC["order_channel"]);
    if (!empty($order_channel)) {
        $condition .= " AND order_channel = :order_channel";
        $params[":order_channel"] = $order_channel;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND (username LIKE '%" . $keyword . "%' OR mobile LIKE '%" . $keyword . "%' OR ordersn LIKE '%" . $keyword . "%')";
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
    $fileds = "id,uniacid,agentid,sid,serial_sn,username,mobile,address,note,delivery_day,delivery_time,status,final_fee,addtime,deliveryer_id";
    $orders = pdo_fetchall("SELECT " . $fileds . " FROM" . tablename("tiny_wmall_order") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($orders)) {
        mload()->model("deliveryer");
        $deliveryers = deliveryer_all();
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title", "address"), "id");
        foreach ($orders as &$da) {
            $da["serial_sn"] = (string) $da["id"] . "-" . $da["serial_sn"];
            $da["deliveryer"] = (object) array();
            if (0 < $da["deliveryer_id"]) {
                $deliveryer = $deliveryers[$da["deliveryer_id"]];
                $da["deliveryer"] = array("id" => $deliveryer["id"], "title" => $deliveryer["title"], "mobile" => $deliveryer["mobile"]);
            }
            $da["store"] = $stores[$da["sid"]];
            $da["addtime_cn"] = date("Y-m-d H:i", $da["addtime"]);
            $da["deliverytime_cn"] = (string) $da["delivery_day"] . " " . $da["delivery_time"];
        }
    }
    $result = array("order" => $orders);
    message(ierror(0, "", $result), "", "ajax");
    return 1;
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = order_fetch($id);
        if (empty($order)) {
            message(ierror(-1, "订单不存在或已删除"), "", "ajax");
        }
        unset($order["data"]);
        unset($order["invoice"]);
        $order["deliverytime_cn"] = (string) $order["delivery_day"] . " " . $order["delivery_time"];
        $order["addtime_cn"] = date("Y-m-d H:i", $order["addtime"]);
        $order["paytime_cn"] = date("Y-m-d H:i", $order["paytime"]);
        $order["deliveryingtime_cn"] = date("Y-m-d H:i", $order["delivery_assign_time"]);
        $order["deliveryinstoretime_cn"] = date("Y-m-d H:i", $order["delivery_instore_time"]);
        $order["deliverysuccesstime_cn"] = date("Y-m-d H:i", $order["delivery_success_time"]);
        $order["delivery_takegoods_time_cn"] = array("day" => "未知", "time" => "未知");
        $order["delivery_success_time_cn"] = $order["delivery_takegoods_time_cn"];
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
        if (!empty($order["delivery_takegoods_time"])) {
            $order["delivery_takegoods_time_cn"] = array("day" => date("m-d", $order["delivery_takegoods_time"]), "time" => date("H:i", $order["delivery_takegoods_time"]));
        }
        $store = store_fetch($order["sid"], array("id", "title", "address", "telephone", "logo", "location_x", "location_y"));
        $order["store"] = array("title" => $store["title"], "address" => $store["address"], "telephone" => $store["telephone"], "location_x" => $store["location_x"], "location_y" => $store["location_y"]);
        mload()->model("deliveryer");
        $order["deliveryer"] = (object) array();
        if (0 < $order["deliveryer_id"]) {
            $deliveryer = deliveryer_fetch($order["deliveryer_id"]);
            $order["deliveryer"] = array("id" => $deliveryer["id"], "title" => $deliveryer["title"], "mobile" => $deliveryer["mobile"], "location_x" => $deliveryer["location_x"], "location_y" => $deliveryer["location_y"], "order_takeout_num" => $deliveryer["order_takeout_num"], "order_errander_num" => $deliveryer["order_errander_num"]);
        }
        $goods = order_fetch_goods($order["id"]);
        $order["goods"] = $goods;
        if (0 < $order["discount_fee"]) {
            $activityed = order_fetch_discount($id);
        }
        $order["activityed"] = $activityed;
        $order_types = order_types();
        $pay_types = order_pay_types();
        $order_status = order_status();
        message(ierror(0, "", $order), "", "ajax");
    } else {
        if ($ta == "analyse") {
            $id = intval($_GPC["id"]);
            $result = order_dispatch_analyse($id, array("channel" => "plateform_dispatch"));
            unset($result["invoice"]);
            if (is_error($result)) {
                message(ierror(-1, $result["message"]), "", "ajax");
            }
            message(ierror(0, "", $result), "", "ajax");
        } else {
            if ($ta == "dispatch") {
                $id = intval($_GPC["id"]);
                $deliveryer_id = intval($_GPC["deliveryer_id"]);
                $status = order_assign_deliveryer($id, $deliveryer_id, true, "本订单由平台管理员调度分配,请尽快处理");
                if (is_error($status)) {
                    message(ierror(-1, $status["message"]), "", "ajax");
                }
                message(ierror(0, "分配订单成功"), "", "ajax");
            } else {
                if ($ta == "order_search") {
                    $deliveryers = pdo_getall("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]), array("id", "title"));
                    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "status" => 1), array("id", "title"));
                    $result = array("deliveryers" => $deliveryers, "stores" => $stores, "agents" => array());
                    if ($_W["is_agent"]) {
                        $result["agents"] = pdo_getall("tiny_wmall_agent", array("uniacid" => $_W["uniacid"]), array("id", "title"));
                    }
                    message(ierror(0, "", $result), "", "ajax");
                } else {
                    if ($ta == "status") {
                        $id = intval($_GPC["id"]);
                        $type = trim($_GPC["type"]);
                        if (empty($type)) {
                            message(ierror(-1, "订单状态错误"), "", "ajax");
                        }
                        if (in_array($type, array("notify_deliveryer_collect", "re_notify_deliveryer_collect", "handle", "notify_clerk_handle", "end"))) {
                            $extra = array();
                            if ($type == "notify_deliveryer_collect") {
                                $extra["force"] = 1;
                            } else {
                                if ($type == "re_notify_deliveryer_collect") {
                                    $extra["force"] = 1;
                                    $extra["channel"] = "re_notify_deliveryer_collect";
                                    $type = "notify_deliveryer_collect";
                                }
                            }
                            $result = order_status_update($id, $type, $extra);
                            if (is_error($result)) {
                                message(ierror(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
                            }
                            message(ierror(0, $result["message"]), "", "ajax");
                            return 1;
                        }
                        if ($type == "cancel") {
                            $reason = trim($_GPC["reason"]);
                            if (empty($reason)) {
                                message(ierror(-1, "请选择退款理由"), "", "ajax");
                            }
                            $remark = trim($_GPC["remark"]);
                            $result = order_status_update($id, "cancel", array("force_cancel" => 1, "reason" => $reason, "remark" => $remark, "note" => (string) $reason . " " . $remark));
                            if (is_error($result)) {
                                message(ierror(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
                            }
                            if ($result["message"]["is_refund"]) {
                                $refund = order_begin_payrefund($id);
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
                            $refund = order_begin_payrefund($id);
                            if (is_error($refund)) {
                                message(ierror(-1, $refund["message"]), "", "ajax");
                            }
                            message(ierror(0, "取消订单成功," . $refund["message"]), "", "ajax");
                            return 1;
                        }
                        if ($type == "refund_status") {
                            $refund = pdo_get("tiny_wmall_order_refund", array("uniacid" => $_W["uniacid"], "order_id" => $id));
                            if (empty($refund)) {
                                message(ierror(-1, "退款申请不存在或已删除"), referer(), "ajax");
                            }
                            pdo_update("tiny_wmall_order_refund", array("status" => 3), array("uniacid" => $_W["uniacid"], "id" => $refund["id"]));
                            pdo_update("tiny_wmall_order", array("refund_status" => 3), array("uniacid" => $_W["uniacid"], "id" => $id));
                            order_insert_refund_log($id, "success");
                            message(ierror(0, "设置为已退款成功"), referer(), "ajax");
                            return 1;
                        }
                        if ($ta == "refund_query") {
                            $query = order_query_payrefund($id);
                            if (is_error($query)) {
                                message(ierror(-1, $query["message"]), "", "ajax");
                            }
                            message(ierror(0, $query["message"]), "", "ajax");
                            return 1;
                        }
                    } else {
                        if ($ta == "cancel_reason") {
                            $reasons = order_cancel_types("manager");
                            $reasons = array_values($reasons);
                            $reasons = array("reasons" => $reasons);
                            message(ierror(0, "", $reasons), "", "ajax");
                        }
                    }
                }
            }
        }
    }
}

?>