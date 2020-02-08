<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("deliveryer");
$config = $_W["we7_wmall"]["config"]["takeout"]["order"];
if ($ta == "list") {
    mload()->model("member");
    $condition = " WHERE uniacid = :uniacid AND sid = :sid AND (order_type = 1 or order_type = 2)";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":sid"] = $sid;
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    if ($status == 1 && $config["show_no_pay"] == 1) {
        $condition .= " AND is_pay = 1";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($orders)) {
        $order_status = order_status();
        $pay_types = order_pay_types();
        foreach ($orders as &$da) {
            $da["pay_type_class"] = "";
            if ($da["is_pay"] == 1) {
                $da["pay_type_class"] = "have-pay";
                if ($da["pay_type"] == "delivery") {
                    $da["pay_type_class"] = "delivery-pay";
                }
            }
            $da["addtime_cn"] = date("Y-m-d H:i", $da["addtime"]);
            $da["goods"] = order_fetch_goods($da["id"]);
            if ($da["status"] == "6") {
                $da["cancel_reason"] = order_cancel_reason($da["id"]);
            }
            $da["favorite_store"] = is_favorite_store($da["sid"], $da["uid"]);
            $da["order_status_cn"] = $order_status[$da["status"]]["text"];
            $da["pay_type_cn"] = $pay_types[$da["pay_type"]]["text"];
            $da["customer_cancel_endtime"] = 0;
            if (2 <= $da["status"] && $config["customer_cancel_status"] == 1 && 0 < $config["customer_cancel_timelimit"]) {
                $customer_cancel_endtime = $da["handletime"] + $config["customer_cancel_timelimit"] * 60;
                if (TIMESTAMP < $customer_cancel_endtime) {
                    $da["customer_cancel_endtime"] = $customer_cancel_endtime;
                }
            }
            if ($da["is_reserve"] == 1 && $da["status"] == 1) {
                $time_difference = $da["deliverytime"] - TIMESTAMP;
                $time_difference = $time_difference - $time_difference % 60;
                $time_difference = transform_time($time_difference);
                $da["handle_tip"] = "本单属于预订单，顾客选择送达时间" . date("Y-m-d H:i", $da["deliverytime"]) . "当前距离送达时间还有" . $time_difference . "现在接单吗？";
            }
            if (0 < $da["refund_status"]) {
                $da["refund_data"] = order_fetchall_refund($da["id"], array("refund_logs" => 1));
            }
            $da["clerk_endorder_when_zipeisong"] = intval($config["clerk_endorder_when_zipeisong"]);
            $da["cancel_after_handle"] = intval($config["cancel_after_handle"]);
            $da["data"] = iunserializer($da["data"]);
            $da["mobile_protect"] = $da["mobile"];
            if (!empty($da["data"]) && $da["data"]["yinsihao_status"] == 1) {
                $da["mobile_protect"] = substr_replace($da["mobile"], "****", 3, 4);
            }
            $da["order_num"] = member_store_order_num($sid, $da["uid"]);
        }
    }
    $result = array("orders" => $orders, "openid_wxapp_manager" => $_W["manager"]["openid_wxapp_manager"], "store" => array("data" => $_W["we7_wmall"]["store"]["data"], "cn" => $_W["we7_wmall"]["store"]["cn"]));
    if (empty($result["store"]["data"]["uupaotui"])) {
        $result["store"]["data"]["uupaotui"] = array();
    }
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = order_fetch($id);
        if (empty($order)) {
            imessage("订单不存在或已删除", "", "error");
        }
        $order["goods"] = order_fetch_goods($order["id"]);
        $order["store"] = store_fetch($order["sid"], array("title", "logo", "telephone", "delivery_mode", "data"));
        $order["log"] = pdo_fetch("select * from " . tablename("tiny_wmall_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
        $order["activityed"] = order_fetch_discount($id);
        $logs = order_fetch_status_log($id);
        if (!empty($logs)) {
            $order["logs"] = $logs;
            $order["maxid"] = max(array_keys($logs));
            $order["minid"] = min(array_keys($logs));
        }
        $order["deliveryer"] = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]));
        if ($order["is_reserve"] == 1 && $order["status"] == 1) {
            $time_difference = $order["deliverytime"] - TIMESTAMP;
            $time_difference = $time_difference - $time_difference % 60;
            $time_difference = transform_time($time_difference);
            $order["handle_tip"] = "本单属于预订单，顾客选择送达时间为" . date("Y-m-d H:i", $order["deliverytime"]) . "当前距离送达时间还有" . $time_difference . "，现在接单么？";
        }
        $result = array("order" => $order, "config" => $_W["we7_wmall"]["config"]["takeout"]["order"]);
        if (empty($result["order"]["store"]["data"]["uupaotui"])) {
            $result["order"]["store"]["data"]["uupaotui"] = array();
        }
        if (empty($result["order"]["store"]["data"]["dada"])) {
            $result["order"]["store"]["data"]["dada"] = array();
        }
        imessage(error(0, $result), "", "ajax");
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
                    $reasons = order_cancel_types("clerker");
                    if ($_W["ispost"]) {
                        if (empty($order_type)) {
                            $reason = $_GPC["reason"];
                            if (empty($reason)) {
                                imessage(error(-1, "请选择取消订单理由"), "", "ajax");
                            }
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
                } else {
                    if ($ta == "deliveryer") {
                        $id = $_GPC["id"];
                        $deliveryer_id = intval($_GPC["deliveryer_id"]);
                        $result = order_assign_deliveryer($id, $deliveryer_id);
                        if (is_error($result)) {
                            imessage(error(-1, "ID为" . $id . "的订单分配配送员失败"), "", "ajax");
                        }
                        imessage(error(0, ""), "", "ajax");
                    } else {
                        if ($ta == "op") {
                            $type = $_GPC["type"];
                            if ($type == "cancel") {
                                $data = order_cancel_types("clerker");
                            } else {
                                if ($type == "direct_deliveryer") {
                                    $data = deliveryer_fetchall($sid);
                                } else {
                                    if ($type == "reply") {
                                        $data = $store["remind_reply"];
                                    }
                                }
                            }
                            $result = array("data" => $data);
                            imessage(error(0, $result), "", "ajax");
                        } else {
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
                            } else {
                                if ($ta == "reply") {
                                    $id = intval($_GPC["id"]);
                                    $reply = trim($_GPC["reply"]);
                                    $result = order_status_update($id, "reply", array("reply" => $reply));
                                    if (is_error($result)) {
                                        imessage(error(-1, "回复催单失败:" . $result["message"]), "", "ajax");
                                    }
                                    imessage(error(0, ""), "", "ajax");
                                } else {
                                    if ($ta == "push_dada") {
                                        $id = intval($_GPC["id"]);
                                        if ($_W["we7_wmall"]["store"]["delivery_mode"] == 2) {
        imessage(error(-1, "当前配送模式为平台配送模式, 不能指定配送员"), "", "ajax");
                                        }
                                        $result = order_push_dada($id);
                                        if (is_error($result)) {
                                            imessage(error(-1, $result["message"]), "", "ajax");
                                        }
                                        imessage(error(0, "推送订单成功"), "", "ajax");
                                    } else {
                                        if ($ta == "push_uupaotui") {
                                            $id = intval($_GPC["id"]);
                                            $push = intval($_GPC["push"]) ? true : false;
                                            if ($_W["ispost"]) {
                                                $status = order_push_uupaotui($id, $push);
                                                if (is_error($status)) {
                                                    imessage($status, "", "ajax");
                                                }
                                                if (!$push) {
                                                    $result = array("tips" => "UU跑腿需要配送费" . $status["need_paymoney"] . "元，确定推送到UU跑腿吗？", "id" => $id);
                                                } else {
                                                    $result = "订单推送到UU跑腿成功";
                                                }
                                                imessage(error(0, $result), "", "ajax");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
if ($ta == "push_shansong") {
    $id = intval($_GPC["id"]);
    $push = intval($_GPC["push"]) ? true : false;
    if ($_W["ispost"]) {
        $status = order_push_shansong($id, $push);
        if (is_error($status)) {
            imessage($status, referer(), "ajax");
        }
        if (!$push) {
            $result = array("tips" => "闪送配送需要配送费" . $status["amount"] . "元，确定推送闪送吗?", "id" => $id);
        } else {
            $result = "订单推送到闪送成功";
        }
        imessage(error(0, $result), "", "ajax");
        return 1;
    }
} else {
    if ($ta == "refund") {
        $id = intval($_GPC["id"]);
        $order = order_fetch($id);
        if (empty($order)) {
                                imessage(error(-1, "订单不存在或已删除"), "", "ajax");
        }
        if (2 < $order["order_type"]) {
                                imessage(error(-1, "外卖订单才可以部分商品退款"), "", "ajax");
        }
        if ($order["status"] == 1) {
                                imessage(error(-1, "未接单，不能部分退款"), "", "ajax");
        }
        if ($order["status"] == 6) {
                                imessage(error(-1, "订单已取消，不能部分退款"), "", "ajax");
        }
        $reasons = order_cancel_types("clerker");
        if ($_W["ispost"]) {
            $refund_data = $_GPC["refund_data"];
            if (empty($refund_data["refund_part"])) {
                                    imessage(error(-1, "请选择退款商品"), "", "ajax");
                                }
                                if (empty($refund_data["refund_total_fee"])) {
                                    imessage(error(-1, "请选择退款商品"), "", "ajax");
            }
            $reason_key = trim($_GPC["reason"]);
            $reason = $reasons[$reason_key];
            if (empty($reason)) {
                imessage(error(-1, "原因不能为空"), "", "ajax");
            }
            $calculate = order_goods_cancel_calculate($order, 0, "", $refund_data, array("is_submit" => 1));
            if (is_error($calculate)) {
                imessage($calculate, "", "ajax");
            }
            $insert = array("uniacid" => $order["uniacid"], "acid" => $order["acid"], "sid" => $order["sid"], "uid" => $order["uid"], "order_id" => $order["id"], "order_sn" => $order["ordersn"], "order_channel" => $order["order_channel"], "pay_type" => $order["pay_type"], "fee" => $calculate["refund_total_fee"], "total_fee" => $order["data"]["final_fee_pay"], "status" => 1, "out_trade_no" => $order["out_trade_no"], "out_refund_no" => date("YmdHis") . random(10, true), "apply_time" => TIMESTAMP, "reason" => "部分商品退款" . $reason, "data" => iserializer(array("refund_info" => array("title" => "商户发起部分退款", "reason" => $reason), "refund_goods" => $calculate["refund_part"])), "type" => "goods");
            pdo_insert("tiny_wmall_order_refund", $insert);
            $refund_id = pdo_insertid();
            order_part_refund_handle($order, $refund_id, array("refund_fee" => $calculate["refund_total_fee"]));
            mlog(1001, $refund_id);
            imessage(error(0, "部分商品退订成功"), "", "ajax");
        }
        $store = store_fetch($sid, array("id", "title", "logo", "delivery_mode"));
        $order["goods"] = order_fetch_goods($id);
        $result = array("store" => $store, "reasons" => $reasons, "order" => $order);
        imessage(error(0, $result), "", "ajax");
        return 1;
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
        return 1;
    }
    if ($ta == "handleGoodsRefund") {
        $refund_id = intval($_GPC["refund_id"]);
        $refund = pdo_get("tiny_wmall_order_refund", array("uniacid" => $_W["uniacid"], "id" => $refund_id), array("id", "data"));
        if (empty($refund)) {
                                imessage(error(-1, "部分商品退款不存在"), "", "ajax");
        }
        if ($refund["status"] != 1) {
         imessage(error(-1, "退款已处理，不能再次处理"), "", "ajax");
        }
        $order_id = intval($_GPC["order_id"]);
        $order = order_fetch($order_id);
        if (empty($order)) {
                                imessage(error(-1, "订单不存在或已删"), "", "ajax");
        }
        if (2 < $order["order_type"]) {
                                imessage(error(-1, "外卖订单才可以部分商品退款"), "", "ajax");
        }
        if ($order["status"] == 1) {
                                imessage(error(-1, "未接单，不能部分退款"), "", "ajax");
        }
        if ($order["status"] == 6) {
                                imessage(error(-1, "订单已取消，不能部分退款"), "", "ajax");
        }
        $type = trim($_GPC["type"]);
        if ($type == "agree") {
            $refund["data"] = iunserializer($refund["data"]);
            $calculate = order_goods_cancel_calculate($order_id, 0, "", array("refund_part" => $refund["data"]["refund_goods"]), array("is_submit" => 1));
            $config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
            if ($config_takeout["auto_refund_cancel_order"] == 1) {
                $result = order_refund_status_update($order_id, $refund_id, "handle");
                if (is_error($result)) {
                    imessage($result, "", "ajax");
                }
            }
            $data = $order["data"];
            if (empty($data["part_refund"])) {
                $data["part_refund"] = array("refund_total_fee" => 0);
            }
            $data["part_refund"]["refund_total_fee"] += $calculate["refund_total_fee"];
            $update_order = array("store_final_fee" => $order["store_final_fee"] - $calculate["refund_total_fee"], "data" => iserializer($data), "refund_fee" => $order["refund_fee"] + $calculate["refund_total_fee"]);
            pdo_update("tiny_wmall_order", $update_order, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            imessage($result, "", "ajax");
            return 1;
        }
        pdo_update("tiny_wmall_order_refund", array("status" => 6), array("uniacid" => $_W["uniacid"], "id" => $refund_id));
                            order_insert_refund_log($order_id, $refund_id, "rejected", "商户拒绝部分商品退款");
        imessage(error(0, "拒绝退订部分商品成功"), "", "ajax");
        return 1;
    }
    if ($ta == "confirm") {
        $code = intval($_GPC["code"]);
        $sid_temp = intval($_GPC["sid"]);
        if (!empty($sid_temp)) {
            $sid = $sid_temp;
        }
        $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "sid" => $sid, "code" => $code));
        if (empty($order)) {
            imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
        }
        $result = order_status_update($order["id"], "end");
        if (is_error($result)) {
            imessage(error(-1, "核销订单失败:" . $result["message"]), "", "ajax");
        }
        imessage(error(0, "核销订单成功"), referer(), "ajax");
        return 1;
    }
    if ($ta == "push_dianwoda") {
        $id = intval($_GPC["id"]);
        $push = intval($_GPC["push"]) ? true : false;
        if ($_W["ispost"]) {
            $status = order_push_dianwoda($id, $push);
            if (is_error($status)) {
                imessage($status, referer(), "ajax");
            }
            $fee = floatval($status["total_price"] / 100);
            if (!$push) {
                $result = array("tips" => "点我达配送需要配送费" . $fee . "元，确定推送点我达吗？", "id" => $id);
            } else {
                $result = "订单成功推送到点我达";
            }
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>