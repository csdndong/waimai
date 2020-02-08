<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE uniacid = :uniacid and order_type < 3";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
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
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $is_remind = intval($_GPC["is_remind"]);
    if (0 < $is_remind) {
        $condition .= " AND is_remind = :is_remind";
        $params[":is_remind"] = $is_remind;
    }
    $re_status = intval($_GPC["refund_status"]);
    if (0 < $re_status) {
        $condition .= " AND refund_status >= :refund_status";
        $params[":refund_status"] = $re_status;
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
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
    } else {
        $starttime = strtotime("-7 day");
        $endtime = TIMESTAMP;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $orders = pdo_fetchall("SELECT id,sid,serial_sn,order_type,username,mobile,address,note,is_pay,pay_type,final_fee,deliveryer_id,delivery_status,delivery_type,delivery_day,delivery_time,deliverytime,is_reserve,addtime,status,refund_status,refund_fee,delivery_assign_time,delivery_instore_time,delivery_takegoods_time,delivery_success_time FROM " . tablename("tiny_wmall_order") . $condition . " ORDER BY is_reserve asc, addtime DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title", "telephone", "address"), "id");
    if (!empty($orders)) {
        $deliveryers = deliveryer_all();
        $uupaotui_status = check_plugin_exist("uupaotui") && get_plugin_config("uupaotui.status") == 1;
        $shansong_status = check_plugin_exist("shansong") && get_plugin_config("shansong.status") == 1;
        $dianwoda_status = check_plugin_exist("dianwoda") && get_plugin_config("dianwoda.status") == 1;
        foreach ($orders as &$da) {
            $da["addtime_cn"] = date("Y-m-d H:i", $da["addtime"]);
            $da["store"] = $stores[$da["sid"]];
            $da["uupaotui_status"] = $uupaotui_status;
            $da["shansong_status"] = $shansong_status;
            $da["dianwoda_status"] = $dianwoda_status;
            if (0 < $da["deliveryer_id"]) {
                $da["deliveryer"] = $deliveryers[$da["deliveryer_id"]];
                if (!empty($da["delivery_assign_time"])) {
                    $da["delivery_assign_time_cn"] = date("Y-m-d H:i", $da["delivery_assign_time"]);
                }
                if (!empty($da["delivery_instore_time"])) {
                    $da["delivery_instore_time_cn"] = date("Y-m-d H:i", $da["delivery_instore_time"]);
                }
                if (!empty($da["delivery_takegoods_time"])) {
                    $da["delivery_takegoods_time_cn"] = date("Y-m-d H:i", $da["delivery_takegoods_time"]);
                }
                if (!empty($da["delivery_success_time"])) {
                    $da["delivery_success_time_cn"] = date("Y-m-d H:i", $da["delivery_success_time"]);
                }
            }
            if ($da["status"] == "6") {
                $da["cancel_reason"] = order_cancel_reason($da["id"]);
            }
            if ($da["is_reserve"] == 1 && $da["status"] == 1) {
                $time_difference = $da["deliverytime"] - TIMESTAMP;
                $time_difference = $time_difference - $time_difference % 60;
                $time_difference = transform_time($time_difference);
                $da["handle_tip"] = "本单属于预订单，顾客选择送达时间" . date("Y-m-d H:i", $da["deliverytime"]) . "当前距离送达时间还有" . $time_difference . "现在接单吗？";
            }
        }
    }
    $result = array("order" => $orders);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = order_fetch($id);
        if (empty($order)) {
            imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
        }
        $order["goods"] = order_fetch_goods($order["id"]);
        $order["store"] = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $order["sid"]), array("title", "address", "telephone"));
        if (0 < $order["discount_fee"]) {
            $discount = order_fetch_discount($id);
        }
        $order["order_channel_cn"] = order_channel($order["order_channel"]);
        if ($order["deliveryer_id"]) {
            $order["delivery_collect_type_cn"] = order_collect_type($order);
            $order["deliveryer"] = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]), array("id", "title", "avatar", "mobile", "location_x", "location_y"));
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
        if (!empty($order["delivery_takegoods_time"])) {
            $order["delivery_takegoods_time_cn"] = array("day" => date("m-d", $order["delivery_takegoods_time"]), "time" => date("H:i", $order["delivery_takegoods_time"]));
        }
        if (!empty($order["delivery_success_time"])) {
            $order["delivery_success_time_cn"] = array("day" => date("m-d", $order["delivery_success_time"]), "time" => date("H:i", $order["delivery_success_time"]));
        }
        $order["log_current"] = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_order_status_log") . " WHERE uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
        $order["log_current"]["addtime_cn"] = date("Y-m-d H:i", $order["log_current"]["addtime"]);
        $order["uupaotui_status"] = check_plugin_exist("uupaotui") && get_plugin_config("uupaotui.status") == 1;
        $order["shansong_status"] = check_plugin_exist("shansong") && get_plugin_config("shansong.status") == 1;
        $order["dianwoda_status"] = check_plugin_exist("dianwoda") && get_plugin_config("dianwoda.status") == 1;
        if ($order["is_reserve"] == 1 && $order["status"] == 1) {
            $time_difference = $order["deliverytime"] - TIMESTAMP;
            $time_difference = $time_difference - $time_difference % 60;
            $time_difference = transform_time($time_difference);
            $order["handle_tip"] = "本单属于预订单，顾客选择送达时间" . date("Y-m-d H:i", $order["deliverytime"]) . "当前距离送达时间还有" . $time_difference . ", 现在接单吗？";
        }
        $result = array("order" => $order);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "logs") {
            $id = intval($_GPC["id"]);
            $logs = order_fetch_status_log($id);
            $result = array("logs" => array_values($logs));
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "status") {
                $id = intval($_GPC["id"]);
                $type = trim($_GPC["type"]);
                if (empty($type)) {
                    imessage(error(-1, "订单状态错误"), "", "ajax");
                }
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
                    imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
                }
                imessage(error(0, $result["message"]), "", "ajax");
            } else {
                if ($ta == "cancel_reason") {
                    $reasons_org = order_cancel_types("manager");
                    $reasons = array();
                    foreach ($reasons_org as $key => $val) {
                        $reasons[] = array("type" => $key, "text" => $val);
                    }
                    $result = array("reasons" => $reasons);
                    imessage(error(0, $result), "", "ajax");
                } else {
                    if ($ta == "cancel") {
                        $id = intval($_GPC["id"]);
                        $reasons = order_cancel_types("manager");
                        $reason = $_GPC["reason"];
                        if (empty($reason)) {
                            imessage(error(-1, "请选择退款理由"), "", "ajax");
                        }
                        $remark = trim($_GPC["remark"]);
                        $result = order_status_update($id, "cancel", array("force_refund" => 1, "force_cancel" => 1, "reason" => $reason, "remark" => $remark, "note" => (string) $reasons[$reason] . " " . $remark));
                        if (is_error($result)) {
                            imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
                        }
                        if ($result["message"]["is_refund"]) {
                            imessage(error(0, "取消订单成功," . $result["message"]["refund_message"]), iurl("order/takeout/list"), "ajax");
                        } else {
                            imessage(error(0, "取消订单成功"), iurl("order/takeout/list"), "ajax");
                        }
                    } 
                }
            }
        }
    }
}
if ($op == "refund_update") {
    $order_id = intval($_GPC["id"]);
    $refund_id = intval($_GPC["refund_id"]);
    $type = trim($_GPC["type"]);
    $result = order_refund_status_update($order_id, $refund_id, $type);
    imessage($result, referer(), "ajax");
    return 1;
}
if ($ta == "search") {
     $params = array("input" => array("uid" => "用户UID", "keyword" => "姓名/手机号"), "time" => array("name" => "addtime", "start" => "下单开始时间", "end" => "下单截止时间"), "extra" => array("deliveryer_id" => "1", "store" => "1"));
    if (0 < $_W["agentid"]) {
        $params["extra"]["agent"] = "1";
    }
    $filter = get_filter_params($params);
    $result = array("filter" => $filter);
    imessage(error(0, $result), "", "ajax");
    return 1;
}
if ($ta == "dispatch") {
    $id = intval($_GPC["id"]);
    $dispatch = intval($_GPC["dispatch"]);
    if ($dispatch) {
        $force = $_GPC["force"] ? true : false;
        $deliveryer_id = intval($_GPC["deliveryer_id"]);
        $status = order_assign_deliveryer($id, $deliveryer_id, $force, "本订单由平台管理员调度分配,请尽快处理");
        imessage($status, "", "ajax");
    }
    $order = order_fetch($id);
    if (empty($order)) {
    imessage(error(-1, "订单不存在"), "", "ajax");
    }
    $store = store_fetch($order["sid"], array("title", "logo", "location_x", "location_y"));
    mload()->model("deliveryer.extra");
    $deliveryer = deliveryer_get_location(array("order_type" => "is_takeout", "agentid" => $order["agentid"]));
    $result = array("order" => $order, "store" => $store, "deliveryer" => $deliveryer);
    imessage(error(0, $result), "", "ajax");
    return 1;
}
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
        return 1;
    }
} else {
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
                    $result = "订单成功推送到点我�?;
                }
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>