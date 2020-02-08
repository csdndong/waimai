<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
$_W["_process"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and order_type < 3 and status >= 1 and status <= 4", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
$_W["_remind"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and order_type < 3 and status >= 1 and status <= 4 and is_remind = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
$_W["_refund"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and order_type < 3 and refund_status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
$config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
if ($ta == "list") {
    mload()->model("member");
    $_W["page"]["title"] = "外卖订单";
    if ($_W["isajax"]) {
        $type = trim($_GPC["type"]);
        $status = intval($_GPC["value"]);
        isetcookie("_" . $type, $status, 1000000);
    }
    $condition = " where uniacid = :uniacid and sid = :sid and status = 5 and order_type < 3 and stat_day = :stat_day";
    $stat = pdo_fetch("select count(*) as total_num, sum(store_final_fee) as store_final_fee from " . tablename("tiny_wmall_order") . $condition, array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":stat_day" => date("Ymd")));
    $filter_type = trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process";
    $condition = " WHERE uniacid = :uniacid and sid = :sid and order_type < 3";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    if ($filter_type == "process") {
        $condition .= " AND (status != 5 and status != 6)";
    }
    $uid = intval($_GPC["uid"]);
    if (0 < $uid) {
        $condition .= " AND uid = :uid";
        $params[":uid"] = $uid;
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
        $condition .= " AND refund_status = :refund_status";
        $params[":refund_status"] = $re_status;
    }
    $is_pay = intval($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : -1;
    if (-1 < $is_pay) {
        $condition .= " AND is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }
    $pay_type = trim($_GPC["pay_type"]);
    if (!empty($pay_type)) {
        $condition .= " AND is_pay = 1 AND pay_type = :pay_type";
        $params[":pay_type"] = $pay_type;
    }
    $is_reserve = isset($_GPC["is_reserve"]) ? intval($_GPC["is_reserve"]) : -1;
    if (-1 < $is_reserve) {
        $condition .= " AND is_reserve = :is_reserve";
        $params[":is_reserve"] = $is_reserve;
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
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_order") . $condition, $params);
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " ORDER BY addtime DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if (!empty($orders)) {
        $order_ids = implode(",", array_keys($orders));
        $goods_temp = pdo_fetchall("select * from " . tablename("tiny_wmall_order_stat") . " where uniacid = :uniacid and oid in (" . $order_ids . ")", array(":uniacid" => $_W["uniacid"]));
        $goods_all = array();
        foreach ($goods_temp as $row) {
            $goods_all[$row["oid"]][] = $row;
        }
        foreach ($orders as &$da) {
            $da["pay_type_class"] = "";
            if ($da["is_pay"] == 1) {
                $da["pay_type_class"] = "have-pay";
                if ($da["pay_type"] == "delivery") {
                    $da["pay_type_class"] = "delivery-pay";
                }
            }
            if ($da["status"] == "6") {
                $da["cancel_reason"] = order_cancel_reason($da["id"]);
            }
            $da["data"] = iunserializer($da["data"]);
            $da["customer_cancel_endtime"] = 0;
            if ($da["status"] == 2 && $config_takeout["customer_cancel_status"] == 1 && 0 < $config_takeout["customer_cancel_timelimit"]) {
                $customer_cancel_endtime = $da["handletime"] + $config_takeout["customer_cancel_timelimit"] * 60;
                if (TIMESTAMP < $customer_cancel_endtime) {
                    $da["customer_cancel_endtime"] = $customer_cancel_endtime;
                }
            }
            if (0 < $da["refund_status"]) {
                $da["refund_data"] = order_fetchall_refund($da["id"], array("refund_logs" => 1));
            }
            $da["clerk_endorder_when_zipeisong"] = $config_takeout["clerk_endorder_when_zipeisong"];
            $da["mobile_protect"] = $da["mobile"];
            if ($da["data"]["yinsihao_status"] == 1) {
                $da["mobile_protect"] = substr_replace($da["mobile"], "****", 3, 4);
            }
            $da["order_num"] = member_store_order_num($sid, $da["uid"]);
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $refund_status = order_refund_status();
    $deliveryers = deliveryer_all();
    $order_channels = order_channel();
    include itemplate("store/order/takeoutList");
}
if ($ta == "detail") {
    $_W["page"]["title"] = "订单详情";
    $id = intval($_GPC["id"]);
    $order = order_fetch($id);
    if (empty($order)) {
        imessage("订单不存在或已经删除", iurl("order/takeout/list"), "error");
    }
    $order["goods"] = order_fetch_goods($order["id"]);
    if ($order["is_comment"] == 1) {
        $comment = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_order_comment") . " WHERE uniacid = :aid AND oid = :oid", array(":aid" => $_W["uniacid"], ":oid" => $id));
        if (!empty($comment)) {
            $comment["data"] = iunserializer($comment["data"]);
            $comment["thumbs"] = iunserializer($comment["thumbs"]);
        }
    }
    if (0 < $order["discount_fee"]) {
        $discount = order_fetch_discount($id);
    }
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $order_channel = order_channel($order["order_channel"], true);
    $logs = order_fetch_status_log($id);
    include itemplate("store/order/takeoutDetail");
}
if ($ta == "status") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $type = trim($_GPC["type"]);
    if (empty($type)) {
        imessage(error(-1, "订单状态错误"), "", "ajax");
    }
    foreach ($ids as $id) {
        $id = intval($id);
        if ($id <= 0) {
            continue;
        }
        $result = order_status_update($id, $type);
        if (is_error($result)) {
            imessage(error(-1, "处理编号为:" . $id . "的订单失败，具体原因：" . $result["message"]), "", "ajax");
        }
    }
    imessage(error(0, "更新订状态成功"), "", "ajax");
}
if ($ta == "cancel") {
    $id = intval($_GPC["id"]);
    $reasons = order_cancel_types("manager");
    if ($_W["ispost"]) {
        $reason = $_GPC["reason"];
        if (empty($reason)) {
            imessage(error(-1, "请选择退款理由"), "", "ajax");
        }
        $remark = trim($_GPC["remark"]);
        $result = order_status_update($id, "cancel", array("reason" => $reason, "remark" => $remark, "note" => (string) $reasons[$reason] . " " . $remark));
        if (is_error($result)) {
            imessage(error(-1, $result["message"]), "", "ajax");
        }
        if ($result["message"]["is_refund"]) {
            imessage(error(0, "取消订单成功, 退款会在1-3个工作日打到客户账户"), iurl("store/order/takeout/list"), "ajax");
        } else {
            imessage(error(0, "取消订单成功"), iurl("store/order/takeout/list"), "ajax");
        }
    }
    include itemplate("store/order/takeoutOp");
}
if ($ta == "remind") {
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $reply = trim($_GPC["reply"]);
        $result = order_status_update($id, "reply", array("reply" => $reply));
        if (is_error($result)) {
            imessage(error(-1, "回复催单失败:" . $result["message"]), referer(), "ajax");
        }
        imessage(error(0, "回复催单成功"), referer(), "ajax");
    }
    include itemplate("store/order/takeoutOp");
}
if ($ta == "print") {
    $id = intval($_GPC["id"]);
    $status = order_print($id);
    if (is_error($status)) {
        imessage(error(-1, $status["message"]), "", "ajax");
    }
    imessage(error(0, "发送打印指定成功"), "", "ajax");
}
if ($ta == "select_deliveryer") {
    $id = intval($_GPC["id"]);
    if ($_W["we7_wmall"]["store"]["delivery_mode"] == 2) {
        imessage(error(-1, "当前配送模式为平台配送模式, 不能指定配送员"), "", "ajax");
    }
    $deliveryers = deliveryer_fetchall($sid);
    if (empty($deliveryers)) {
        imessage(error(-1, "您的店铺还没有添加配送员,请先添加配送员后再进行订单指派"), "", "ajax");
    }
    include itemplate("store/order/takeoutOp");
}
if ($ta == "set_deliveryer") {
    if ($_W["we7_wmall"]["store"]["delivery_mode"] == 2) {
        imessage(error(-1, "当前配送模式为平台配送模式, 不能指定配送员"), "", "ajax");
    }
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    $id = intval($_GPC["id"]);
    $result = order_assign_deliveryer($id, $deliveryer_id);
    if (is_error($result)) {
        imessage(error(-1, "ID为" . $id . "的订单分配配送员失败:" . $result["message"]), "", "ajax");
    }
    imessage(error(0, ""), "", "ajax");
}
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
}
if ($ta == "push_uupaotui") {
    $id = intval($_GPC["id"]);
    $push = intval($_GPC["push"]) ? true : false;
    if ($_W["ispost"]) {
        $status = order_push_uupaotui($id, $push);
        if (is_error($status)) {
            imessage($status, referer(), "ajax");
        }
        if (!$push) {
            $result = array("tips" => "UU跑腿需要配送费" . $status["need_paymoney"] . "元，确定推送到UU跑腿吗？", "id" => $id);
        } else {
            $result = "订单推送到UU跑腿成功";
        }
        imessage(error(0, $result), referer(), "ajax");
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
        imessage(error(0, $result), referer(), "ajax");
    }
}
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
        imessage(error(-1, "未接单，不能部分退订"), "", "ajax");
    }
    if ($order["status"] == 6) {
        imessage(error(-1, "订单已取消，不能部分退订"), "", "ajax");
    }
    $reasons = order_cancel_types("clerker");
    if ($_W["ispost"]) {
        $refund_data = $_GPC["refund_data"];
        if (empty($refund_data["refund_part"])) {
            imessage(error(-1, "请选择退货商品"), "", "ajax");
        }
        if (empty($refund_data["refund_total_fee"])) {
            imessage(error(-1, "请选择退货商品"), "", "ajax");
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
        imessage(error(0, "部分商品退款成功"), "", "ajax");
    }
    $order["goods"] = order_fetch_goods($id);
    include itemplate("store/order/takeoutOp");
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
}
if ($ta == "yinsihao") {
    $order_id = intval($_GPC["order_id"]);
    $ordersn = trim($_GPC["ordersn"]);
    mload()->model("plugin");
    pload()->model("yinsihao");
    $data = yinsihao_bind($order_id, "member", $ordersn);
    if (is_error($data)) {
        slog("yinsihao", "隐私号绑定错误", array("order_id" => $order_id), "生成顾客隐私号错误" . $data["message"]);
        imessage($data, "", "ajax");
    }
    include itemplate("store/order/takeoutOp");
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
        imessage(error(0, $result), referer(), "ajax");
    }
}

?>