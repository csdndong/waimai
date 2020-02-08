<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "外卖订单";
    if ($_W["isajax"]) {
        $type = trim($_GPC["type"]);
        $status = intval($_GPC["value"]);
        isetcookie("_" . $type, $status, 1000000);
    }
    $filter_type = trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process";
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
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
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
        $condition .= " AND (id = " . $keyword . " OR username LIKE '%" . $keyword . "%' OR mobile LIKE '%" . $keyword . "%' OR ordersn LIKE '%" . $keyword . "%')";
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
    $psize = 30;
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
            $da["remind"] = order_timeout_remind($da);
            $da["data"] = iunserializer($da["data"]);
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $refund_status = order_refund_status();
    $deliveryers = deliveryer_all();
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    load()->model("mc");
    $fields = mc_acccount_fields();
    include itemplate("order/takeoutNewList");
}
if ($op == "detail") {
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
    $logs = order_fetch_status_log($id);
    include itemplate("order/takeoutDetail");
}
if ($op == "remind") {
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $reply = trim($_GPC["reply"]);
        $result = order_status_update($id, "reply", array("reply" => $reply));
        if (is_error($result)) {
            imessage(error(-1, "回复催单失败:" . $result["message"]), referer(), "ajax");
        }
        imessage(error(0, "回复催单成功"), referer(), "ajax");
    }
    include itemplate("order/takeoutOp");
}
if ($op == "status" && $_W["ispost"]) {
    $ids = $_GPC["id"];
    if (empty($ids)) {
        imessage(error(-1, "请选择要操作的订单"), "", "ajax");
    }
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $type = trim($_GPC["type"]);
    if (empty($type)) {
        imessage(error(-1, "订单状态错误"), "", "ajax");
    }
    $extra = array();
    if ($type == "notify_deliveryer_collect") {
        $extra["force"] = 1;
    }
    foreach ($ids as $id) {
        $result = order_status_update($id, $type, $extra);
        if (is_error($result)) {
            imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
        }
    }
    imessage(error(0, "操作成功"), "referer", "ajax");
}
if ($op == "groupcancel") {
    $ids = $_GPC["id"];
    $ids = implode(",", $ids);
    $reasons = order_cancel_types("manager");
    if ($_W["ispost"]) {
        $reason = $_GPC["reason"];
        if (empty($reason)) {
            imessage(error(-1, "请选择退款理由"), "", "ajax");
        }
        $remark = trim($_GPC["remark"]);
        $ids = $_GPC["id"];
        $ids = explode(",", $ids);
        foreach ($ids as $id) {
            $result = order_status_update($id, "cancel", array("force_cancel" => 1, "reason" => $reason, "remark" => $remark, "note" => (string) $reasons[$reason] . " " . $remark));
            if (is_error($result)) {
                imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
            }
            if ($result["message"]["is_refund"]) {
                $refund = order_begin_payrefund($id);
                if (is_error($refund)) {
                    imessage(error(-1, $refund["message"]), "", "ajax");
                }
            }
        }
        imessage(error(0, "取消订单成功"), "referer", "ajax");
    }
    include itemplate("order/takeoutOp");
}
if ($op == "refund_update") {
    $order_id = intval($_GPC["id"]);
    $refund_id = intval($_GPC["refund_id"]);
    $type = trim($_GPC["type"]);
    $result = order_refund_status_update($order_id, $refund_id, $type);
    imessage($result, referer(), "ajax");
}
if ($op == "analyse") {
    $id = intval($_GPC["id"]);
    $deliveryers = order_dispatch_analyse($id, array("channel" => "plateform_dispatch", "sort" => $_W["we7_wmall"]["config"]["takeout"]["order"]["dispatch_sort"]));
    if (is_error($deliveryers)) {
        imessage($deliveryers, "", "ajax");
    }
    imessage(error(0, $deliveryers), "", "ajax");
}
if ($op == "dispatch") {
    $order_id = intval($_GPC["order_id"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    $status = order_assign_deliveryer($order_id, $deliveryer_id, true, "本订单由平台管理员调度分配,请尽快处理");
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    imessage(error(0, "分配订单成功"), "", "ajax");
}
if ($op == "print") {
    $order_ids = intval($_GPC["id"]);
    if (!is_array($order_ids)) {
        $order_ids = array($order_ids);
    }
    foreach ($order_ids as $order_id) {
        $result = order_print($order_id);
        if (is_error($result)) {
            imessage(error(-1, "打印编号为" . $order_id . "的订单失败，具体原因：" . $result["message"]), "", "ajax");
        }
    }
    imessage(error(0, "订单打印成功"), "", "ajax");
}
if ($op == "changeOrder") {
    $id = intval($_GPC["id"]);
    $order = pdo_get("tiny_wmall_order", array("id" => $id), array("username", "mobile", "address", "serial_sn", "note"));
    if ($_W["ispost"] && intval($_GPC["set"]) == 1) {
        $id = intval($_GPC["id"]);
        pdo_update("tiny_wmall_order", array("username" => trim($_GPC["username"]), "mobile" => trim($_GPC["mobile"]), "address" => trim($_GPC["address"]), "note" => trim($_GPC["note"])), array("id" => $id));
        imessage(error(0, "修改成功"), referer(), "ajax");
    }
    include itemplate("order/takeoutOp");
}

?>