<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
$deliveryer = $_W["we7_wmall"]["deliveryer"]["user"];
if (empty($deliveryer["is_takeout"])) {
    message(ierror(-1, "您没有平台外卖单的配送权限，请联系管理员授权"), "", "ajax");
}
if ($op == "list") {
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 3;
    $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "load";
    $id = intval($_GPC["id"]);
    if ($type == "load") {
        if (0 < $id) {
            $condition .= " and id < :id";
            $params[":id"] = $id;
        }
    } else {
        $condition .= " and id > :id";
        $params[":id"] = $id;
    }
    $can_collect_order = $deliveryer["work_status"];
    $can_collect_order_cn = "";
    if (empty($can_collect_order)) {
        $can_collect_order_cn = "您已停工，开工后再进行接单！";
    }
    if ($config_takeout["order"]["dispatch_mode"] != 1 && !$config_takeout["order"]["can_collect_order"]) {
        $can_collect_order = 0;
        $can_collect_order_cn = "当前调度模式不允许抢单,请等待管理员/系统派单";
    }
    if ($status == 3) {
        $condition .= " and delivery_status = :status and delivery_type = 2 and " . $can_collect_order;
        $params[":status"] = $status;
        if ($config_takeout["order"]["deliverynoassign_sort_type"] == "desc") {
            $condition .= " order by id desc";
        } else {
            $condition .= " order by id asc";
        }
        if (0 < $config_takeout["order"]["max_dispatching"]) {
            $condition .= " limit " . $config_takeout["order"]["max_dispatching"];
        }
    } else {
        $condition .= " and delivery_type = 2";
        if ($status == 7) {
            $condition .= " and (delivery_status = 7 or delivery_status = 8) and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = :delivery_collect_type and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1))";
            $params[":deliveryer_id"] = $deliveryer["id"];
            $params[":delivery_collect_type"] = 3;
            $params[":transfer_deliveryer_id"] = $deliveryer["id"];
        } else {
            $condition .= " and delivery_status = :delivery_status and ((deliveryer_id = :deliveryer_id and transfer_delivery_status = 0) or (delivery_collect_type = :delivery_collect_type and transfer_deliveryer_id = :transfer_deliveryer_id and transfer_delivery_status = 1))";
            $params[":delivery_status"] = $status;
            $params[":deliveryer_id"] = $deliveryer["id"];
            $params[":delivery_collect_type"] = 3;
            $params[":transfer_deliveryer_id"] = $deliveryer["id"];
        }
        $condition .= " order by id desc limit 15";
    }
    $min_id = intval(pdo_fetchcolumn("SELECT min(id) as min_id FROM " . tablename("tiny_wmall_order") . $condition, $params));
    $orders = pdo_fetchall("SELECT id,serial_sn,delivery_collect_type,transfer_deliveryer_id,transfer_delivery_status,ordersn,order_type,order_plateform,distance,addtime, status, username, mobile, address, location_x, location_y, delivery_status, delivery_type, delivery_fee,plateform_deliveryer_fee,delivery_time,sid, num, final_fee, note,data FROM " . tablename("tiny_wmall_order") . $condition, $params, "id");
    $min = $max = 0;
    if (!empty($orders)) {
        $stores_id = array();
        foreach ($orders as &$da) {
            $stores_id[] = $da["sid"];
        }
        $stores_str = implode(",", array_unique($stores_id));
        $stores = pdo_fetchall("select id, title, address, location_x, location_y, telephone from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id in (" . $stores_str . ")", array(":uniacid" => $_W["uniacid"]), "id");
        foreach ($orders as &$da) {
            $da["data"] = iunserializer($da["data"]);
            if ($da["status"] == 3) {
                $da["plateform_deliveryer_fee"] = order_calculate_deliveryer_fee($da, $deliveryer);
                if (!$config_takeout["order"]["show_acceptaddress_when_firstdelivery"] && !$deliveryer["order_takeout_num"]) {
                    $da["address"] = "接单后可见收货地址";
                }
            }
            $da["delivery_collect_type_cn"] = order_collect_type($da);
            $da["transfer_delivery_reason"] = $da["data"]["transfer_delivery_reason"];
            $da["plateform_deliveryer_fee"] = floatval($da["plateform_deliveryer_fee"]);
            $da["deliveryer_fee"] = floatval($da["plateform_deliveryer_fee"]);
            $da["addtime_cn"] = date("m-d H:i", $da["addtime"]);
            $da["store"] = array("title" => $stores[$da["sid"]]["title"], "telephone" => $stores[$da["sid"]]["telephone"], "address" => $stores[$da["sid"]]["address"], "location_x" => $stores[$da["sid"]]["location_x"], "location_y" => $stores[$da["sid"]]["location_y"]);
            $da["store2deliveryer_distance"] = "未知";
            $da["store2user_distance"] = $da["store2deliveryer_distance"];
            if (!empty($da["location_x"]) && !empty($da["location_y"])) {
                if (!empty($da["store"]["location_x"]) && !empty($da["store"]["location_y"])) {
                    $da["store2user_distance"] = distanceBetween($da["location_y"], $da["location_x"], $da["store"]["location_y"], $da["store"]["location_x"]);
                    $da["store2user_distance"] = round($da["store2user_distance"] / 1000, 2);
                    $da["store2user_distance"] = strval($da["store2user_distance"]);
                }
                if (!empty($deliveryer["location_x"]) && !empty($deliveryer["location_y"])) {
                    $da["store2deliveryer_distance"] = distanceBetween($da["store"]["location_y"], $da["store"]["location_x"], $deliveryer["location_y"], $deliveryer["location_x"]);
                    $da["store2deliveryer_distance"] = round($da["store2deliveryer_distance"] / 1000, 2);
                    $da["store2deliveryer_distance"] = strval($da["store2deliveryer_distance"]);
                }
            }
            $stores_id[] = $da["sid"];
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
    $respon = array("resultCode" => 0, "resultMessage" => "调用成功", "data" => $data);
    message($respon, "", "ajax");
    return 1;
} else {
    if ($op == "detail") {
        $id = intval($_GPC["id"]);
        $order = order_fetch($id);
        if (empty($order)) {
            message(ierror(-1, "订单不存在或已删除"), "", "ajax");
        }
        $order["plateform_deliveryer_fee"] = floatval($order["plateform_deliveryer_fee"]);
        $order["deliveryer_fee"] = $order["plateform_deliveryer_fee"];
        $order["deliveryer_transfer_status"] = 0;
        if ($deliveryer["perm_transfer"]["status_takeout"] == 1 && in_array($order["delivery_status"], array(4, 7, 8)) && $order["transfer_delivery_status"] != 1) {
            $order["deliveryer_transfer_status"] = "1";
        }
        $order["deliveryer_transfer_reason"] = $config_takeout["order"]["deliveryer_transfer_reason"];
        $order["deliveryer_cancel_status"] = 0;
        if ($deliveryer["perm_cancel"]["status_takeout"] == 1 && in_array($order["status"], array(2, 3, 4)) && $order["deliveryer_id"] == $deliveryer["id"]) {
            $order["deliveryer_cancel_status"] = "1";
        }
        $order["deliveryer_cancel_reason"] = $config_takeout["order"]["deliveryer_cancel_reason"];
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
        $deliveryer = deliveryer_fetch($deliveryer["id"]);
        $order["deliveryer"] = array("title" => $deliveryer["title"], "mobile" => $deliveryer["mobile"], "age" => $deliveryer["age"], "sex" => $deliveryer["sex"], "location_x" => $deliveryer["location_x"], "location_y" => $deliveryer["location_y"]);
        $order["store2deliveryer_distance"] = "未知";
        $order["store2user_distance"] = $order["store2deliveryer_distance"];
        if (!empty($order["location_x"]) && !empty($order["location_y"])) {
            if (!empty($order["store"]["location_x"]) && !empty($order["store"]["location_y"])) {
                $order["store2user_distance"] = distanceBetween($order["location_y"], $order["location_x"], $order["store"]["location_y"], $order["store"]["location_x"]);
                $order["store2user_distance"] = strval(round($order["store2user_distance"] / 1000, 2));
                $order["store2user_distance"] = strval($order["store2user_distance"]);
            }
            if (!empty($order["deliveryer"]["location_x"]) && !empty($order["deliveryer"]["location_y"])) {
                $order["store2deliveryer_distance"] = distanceBetween($order["store"]["location_y"], $order["store"]["location_x"], $order["deliveryer"]["location_y"], $order["deliveryer"]["location_x"]);
                $order["store2deliveryer_distance"] = strval(round($order["store2deliveryer_distance"] / 1000, 2));
                $order["store2deliveryer_distance"] = strval($order["store2deliveryer_distance"]);
            }
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
        if ($op == "collect") {
            $id = intval($_GPC["id"]);
            $result = order_deliveryer_update_status($id, "delivery_assign", array("deliveryer_id" => $deliveryer["id"], "delivery_handle_type" => "app"));
            if (is_error($result)) {
                message(ierror(-1, $result["message"]), "", "ajax");
            }
            message(ierror(0, "抢单成功"), "", "ajax");
        } else {
            if ($op == "instore") {
                $id = intval($_GPC["id"]);
                $result = order_deliveryer_update_status($id, "delivery_instore", array("deliveryer_id" => $deliveryer["id"], "delivery_handle_type" => "app"));
                if (is_error($result)) {
                    message(error(-1, $result["message"]), "", "ajax");
                }
                message(ierror(0, "确认到店成功"), referer(), "ajax");
            } else {
                if ($op == "takegoods") {
                    $id = intval($_GPC["id"]);
                    $result = order_deliveryer_update_status($id, "delivery_takegoods", array("deliveryer_id" => $deliveryer["id"], "delivery_handle_type" => "app"));
                    if (is_error($result)) {
                        message(ierror(-1, $result["message"]), "", "ajax");
                    }
                    message(ierror(0, "确认取货成功"), referer(), "ajax");
                } else {
                    if ($op == "success") {
                        $id = intval($_GPC["id"]);
                        $result = order_deliveryer_update_status($id, "delivery_success", array("deliveryer_id" => $deliveryer["id"], "delivery_success_location_x" => $deliveryer["location_x"], "delivery_success_location_y" => $deliveryer["location_y"]));
                        if (is_error($result)) {
                            message(ierror(-1, $result["message"]), "", "ajax");
                        }
                        message(ierror(0, "确认送达成功"), "", "ajax");
                    } else {
                        if ($op == "transfer_reason") {
                            if (empty($config_takeout["order"]["deliveryer_transfer_reason"])) {
                                $config_takeout["order"]["deliveryer_transfer_reason"] = array("其它");
                            }
                            message(ierror(0, $config_takeout["order"]["deliveryer_transfer_reason"]), "", "ajax");
                        } else {
                            if ($op == "transfer") {
                                $id = intval($_GPC["id"]);
                                $reason = urldecode($_GPC["reason"]);
                                $result = order_deliveryer_update_status($id, "delivery_transfer", array("deliveryer_id" => $deliveryer["id"], "reason" => $reason));
                                if (is_error($result)) {
                                    message(ierror(-1, $result["message"]), "", "ajax");
                                }
                                message(ierror(0, "转单成功"), "", "ajax");
                            } else {
                                if ($op == "cancel") {
                                    $id = intval($_GPC["id"]);
                                    $reason = urldecode($_GPC["reason"]);
                                    if (empty($reason)) {
                                        message(ierror(0, "取消订单原因不能为空"), "", "ajax");
                                    }
                                    $extra = array("deliveryer_id" => $deliveryer["id"], "reason" => "other", "note" => $reason, "deliveryer_id" => $deliveryer["id"]);
                                    $result = order_deliveryer_update_status($id, "delivery_cancel", $extra);
                                    if (is_error($result)) {
                                        message(ierror(-1, $result["message"]), "", "ajax");
                                    }
                                    message(ierror(0, "订单取消成功"), "", "ajax");
                                } else {
                                    if ($op == "direct_transfer") {
                                        $deliveryers = array_values(deliveryer_fetchall(0));
                                        $data = array("verify_reason" => 1, "tips" => "注意：您不能随意转单啊！否则扣你工资奖金绩效各种。", "deliveryers" => $deliveryers);
                                        message(ierror(0, "", $data), "", "ajax");
                                    } else {
                                        if ($op == "direct_transfer_begin") {
                                            $reason = urldecode($_GPC["reason"]);
                                            if (empty($reason)) {
                                                message(ierror(0, "转单原因不能为空"), "", "ajax");
                                            }
                                            $id = intval($_GPC["id"]);
                                            $deliveryer_id = intval($_GPC["deliveryer_id"]);
                                            $extra = array("from_deliveryer_id" => $deliveryer["id"], "to_deliveryer_id" => $deliveryer_id, "note" => $reason);
                                            $result = order_deliveryer_update_status($id, "direct_transfer", $extra);
                                            if (is_error($result)) {
                                                message(ierror(-1, $result["message"]), "", "ajax");
                                            }
                                            message(ierror(0, "发起定向转单申请成功，请等待目标配送员回复"), "", "ajax");
                                        } else {
                                            if ($op == "direct_transfer_reply") {
                                                $id = intval($_GPC["id"]);
                                                $result = trim($_GPC["result"]);
                                                if (empty($result)) {
                                                    message(ierror(-1, "请选择是否同意接受订单"), "", "ajax");
                                                }
                                                $extra = array("deliveryer_id" => $deliveryer["id"], "result" => $result);
                                                $result = order_deliveryer_update_status($id, "direct_transfer_reply", $extra);
                                                if (is_error($result)) {
                                                    message(ierror(-1, $result["message"]), "", "ajax");
                                                }
                                                message(ierror(0, $result["message"]), "", "ajax");
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

?>