<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if (empty($_W["deliveryer"]["perm_takeout"])) {
    imessage(error(-1, "您没有外卖单的配送权限，请联系管理员授权"), "", "ajax");
}
if ($ta == "list") {
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
                if ($can_collect_order == 1) {
                    $condition .= " and (delivery_type = 2 or (delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")))";
                } else {
                    $condition .= " and delivery_type = 1 and sid in (" . $_W["deliveryer"]["sids_sn"] . ")";
                }
            }
        }
        if ($config_takeout["order"]["deliverynoassign_sort_type"] == "desc") {
            $order_by = " order by id desc";
        } else {
            $order_by = " order by id asc";
        }
        if (0 < $config_takeout["order"]["max_dispatching"]) {
            $limit .= " limit " . $config_takeout["order"]["max_dispatching"];
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
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    if (!isset($order_by)) {
        $order_by = " ORDER BY id DESC";
    }
    if (!isset($limit)) {
        $limit = " LIMIT " . ($pindex - 1) * $psize . "," . $psize;
    }
    $orders = pdo_fetchall("SELECT id,order_plateform,ordersn,serial_sn, addtime, is_pay, pay_type,order_type, status, username, mobile, address,distance, delivery_status,deliveryer_id,plateform_deliveryer_fee, delivery_type, delivery_fee, delivery_day, delivery_time,delivery_assign_time, sid, num, final_fee, delivery_collect_type, transfer_delivery_status,location_x,location_y,note, is_reserve, zhunshibao_status, data FROM " . tablename("tiny_wmall_order") . $condition . $order_by . $limit, $params);
    if (!empty($orders)) {
        $stores_id = array();
        foreach ($orders as &$da) {
            $stores_id[] = $da["sid"];
        }
        $stores_str = implode(",", array_unique($stores_id));
        $stores = pdo_fetchall("select id, title, address, location_x, location_y, telephone from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id in (" . $stores_str . ")", array(":uniacid" => $_W["uniacid"]), "id");
        foreach ($orders as &$da) {
            if ($da["status"] == 3) {
                $da["plateform_deliveryer_fee"] = order_calculate_deliveryer_fee($da, $_deliveryer);
                if (!$config_takeout["order"]["show_acceptaddress_when_firstdelivery"] && !$_deliveryer["order_takeout_num"]) {
                    $da["address"] = "接单后可见收货地址";
                }
            }
            $da["addtime_cn"] = date("m-d H:i", $da["addtime"]);
            $da["delivery_collect_type_cn"] = order_collect_type($da);
            $da["store"] = array("title" => $stores[$da["sid"]]["title"], "telephone" => $stores[$da["sid"]]["telephone"], "address" => $stores[$da["sid"]]["address"], "location_x" => $stores[$da["sid"]]["location_x"], "location_y" => $stores[$da["sid"]]["location_y"]);
            $da["store2deliveryer_distance"] = "未知";
            $da["store2user_distance"] = $da["store2deliveryer_distance"];
            if (!empty($da["location_x"]) && !empty($da["location_y"])) {
                if (!empty($da["store"]["location_x"]) && !empty($da["store"]["location_y"])) {
                    $da["store2user_distance"] = distanceBetween($da["location_y"], $da["location_x"], $da["store"]["location_y"], $da["store"]["location_x"]);
                    $da["store2user_distance"] = round($da["store2user_distance"] / 1000, 2);
                }
                if (!empty($deliveryer["location_x"]) && !empty($deliveryer["location_y"])) {
                    $da["store2deliveryer_distance"] = distanceBetween($da["store"]["location_y"], $da["store"]["location_x"], $deliveryer["location_y"], $deliveryer["location_x"]);
                    $da["store2deliveryer_distance"] = round($da["store2deliveryer_distance"] / 1000, 2);
                }
            }
            if ($da["status"] == 4 && 0 < $config_takeout["order"]["delivery_timeout_limit"]) {
                $delivery_overtime = $da["delivery_assign_time"] + $config_takeout["order"]["delivery_timeout_limit"] * 60;
                if ($delivery_overtime < TIMESTAMP) {
                    $da["delivery_overtime_start"] = $delivery_overtime;
                } else {
                    $da["delivery_overtime"] = $delivery_overtime;
                }
            }
            $da["data"] = iunserializer($da["data"]);
            $da["mobile_protect"] = $da["mobile"];
            if (!empty($da["data"]) && $da["data"]["yinsihao_status"] == 1) {
                $da["mobile_protect"] = substr_replace($da["mobile"], "****", 3, 4);
            }
        }
    }
    $delivery_status = order_delivery_status();
    $result = array("orders" => $orders, "max_dispatching" => $config_takeout["order"]["max_dispatching"], "can_collect_order" => $can_collect_order, "deliveryer" => $_W["deliveryer"], "config" => array("auto_refresh" => $config_takeout["order"]["auto_refresh"]));
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        $id = intval($_GPC["id"]);
        $type = trim($_GPC["type"]);
        if (empty($id)) {
            imessage(error(-1, "请选择订单"), "", "ajax");
        }
        $types = array("delivery_assign", "delivery_instore", "delivery_takegoods", "delivery_success", "direct_transfer_reply", "delivery_transfer", "delivery_cancel", "direct_transfer");
        if (!in_array($type, $types)) {
            imessage(error(-1, "订单操作有误"), "", "ajax");
        }
        $extra = array("deliveryer_id" => $deliveryer["id"], "delivery_handle_type" => "wxapp");
        if ($type == "direct_transfer_reply") {
            $extra["result"] = trim($_GPC["reply"]);
        } else {
            if ($type == "delivery_transfer") {
                $extra["reason"] = trim($_GPC["reason"]);
            } else {
                if ($type == "delivery_cancel") {
                    $extra["reason"] = "other";
                    $extra["note"] = trim($_GPC["reason"]);
                } else {
                    if ($type == "direct_transfer") {
                        $extra["from_deliveryer_id"] = $deliveryer["id"];
                        $extra["to_deliveryer_id"] = intval($_GPC["deliveryer_id"]);
                        if ($extra["to_deliveryer_id"] == $deliveryer["id"]) {
                            imessage(error(-1, "不能转单给自己"), "", "ajax");
                        }
                    }
                }
            }
        }
        $result = order_deliveryer_update_status($id, $type, $extra);
        if (is_error($result)) {
            imessage($result, "", "ajax");
        }
        imessage(error(0, $result["message"]), "", "ajax");
    } else {
        if ($ta == "detail") {
            $id = intval($_GPC["id"]);
            $order = order_fetch($id);
            if (empty($order)) {
                imessage(error(-1, "订单不存在或已删除"), "", "ajax");
            }
            if (!empty($order["deliveryer_id"]) && $order["deliveryer_id"] != $_deliveryer["id"] && !$order["transfer_delivery_status"] || $order["transfer_delivery_status"] == 1 && $order["transfer_deliveryer_id"] != $_deliveryer["id"]) {
                imessage(error(-1, "该订单不是由您配送,无权查看订单详情"), "", "ajax");
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
            $order["goods"] = $goods;
            $store = store_fetch($order["sid"], array("id", "title", "address", "telephone", "logo", "location_x", "location_y"));
            $order["store"] = $store;
            $pay_types = order_pay_types();
            $order["paytype_cn"] = $pay_types[$order["pay_type"]]["text"];
            $order["addtime_cn"] = date("Y-m-d H:i", $order["addtime"]);
            if ($order["status"] == 4 && 0 < $config_takeout["order"]["delivery_timeout_limit"]) {
                $delivery_overtime = $order["delivery_assign_time"] + $config_takeout["order"]["delivery_timeout_limit"] * 60;
                if ($delivery_overtime < TIMESTAMP) {
                    $order["delivery_overtime_start"] = $delivery_overtime;
                } else {
                    $order["delivery_overtime"] = $delivery_overtime;
                }
            }
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
            $result = array("order" => $order, "deliveryer" => $_deliveryer, "config" => $config_takeout["range"]);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "op") {
                $id = intval($_GPC["id"]);
                $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $id), array("id"));
                if (empty($order)) {
                    imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
                }
                $type = trim($_GPC["type"]);
                if ($type == "delivery_transfer") {
                    $result = $config_takeout["order"]["deliveryer_transfer_reason"];
                } else {
                    if ($type == "delivery_cancel") {
                        $result = $config_takeout["order"]["deliveryer_cancel_reason"];
                    } else {
                        if ($type == "direct_transfer") {
                            $result = array_values(deliveryer_fetchall(0));
                        }
                    }
                }
                imessage(error(0, $result), "", "ajax");
            } else {
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
                    imessage(error(0, "通知成功"), "", "ajax");
                }
            }
        }
    }
}

?>