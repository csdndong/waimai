<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $stat_day = trim($_GPC["date"]) ? str_replace("-", "", $_GPC["date"]) : date("Ymd");
    $condition = " where a.uniacid = :uniacid and a.sid = :sid and a.is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    $starttime = strtotime($stat_day);
    $endtime = $starttime + 86399;
    $condition .= " and a.addtime > " . $starttime . " and a.addtime <= " . $endtime . " order by id desc limit 5";
    $orders = pdo_fetchall("select a.*, b.title as goods_title, b.use_limit_day from " . tablename("tiny_wmall_seckill_order") . " as a left join " . tablename("tiny_wmall_seckill_goods") . " as b on a.goods_id = b.id " . $condition, $params, "id");
    $min = 0;
    if (!empty($orders)) {
        foreach ($orders as &$value) {
            $value["addtime_cn"] = date("m-d H:i:s", $value["addtime"]);
        }
        $min = min(array_keys($orders));
    }
    $pay_types = order_pay_types();
    $order_status = array("1" => array("css" => "label label-default", "text" => "待核销", "color" => ""), "2" => array("css" => "label label-success", "text" => "已核销", "color" => "color-success"), "3" => array("css" => "label label-danger", "text" => "已取消", "color" => "color-danger"));
    include itemplate("seckill/orderList");
}
if ($ta == "more") {
    $id = intval($_GPC["min"]);
    $condition = " where a.uniacid = :uniacid and a.sid = :sid and a.is_pay = 1 and a.id < :id";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id);
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    $stat_day = trim($_GPC["date"]) ? str_replace("-", "", $_GPC["date"]) : date("Ymd");
    $starttime = strtotime($stat_day);
    $endtime = $starttime + 86399;
    $condition .= " and a.addtime > " . $starttime . " and a.addtime <= " . $endtime . " order by id desc limit 5";
    $orders = pdo_fetchall("select a.*, b.title as goods_title, b.use_limit_day from " . tablename("tiny_wmall_seckill_order") . " as a left join " . tablename("tiny_wmall_seckill_goods") . " as b on a.goods_id = b.id " . $condition, $params, "id");
    $min = 0;
    if (!empty($orders)) {
        $pay_types = order_pay_types();
        $order_status = array("1" => array("css" => "label label-default", "text" => "待核销", "color" => ""), "2" => array("css" => "label label-success", "text" => "已核销", "color" => "color-success"), "3" => array("css" => "label label-danger", "text" => "已取消", "color" => "color-danger"));
        foreach ($orders as &$value) {
            $value["status_cn"] = $order_status[$value["status"]]["text"];
            $value["addtime_cn"] = date("m-d H:i:s", $value["addtime"]);
            if ($value["is_pay"]) {
                $value["pay_type_cn"] = $pay_types[$value["pay_type"]]["text"];
            } else {
                $value["pay_type_cn"] = $pay_types[$value["pay_type"]];
            }
        }
        $min = min(array_keys($orders));
    }
    $orders = array_values($orders);
    $respon = array("errno" => 0, "message" => $orders, "min" => $min);
    imessage($respon, "", "ajax");
}
if ($ta == "status") {
    $id = intval($_GPC["id"]);
    mload()->model("plugin");
    pload()->model("seckill");
    $order = seckill_order_get($id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    if ($order["status"] != 1) {
        imessage(error(-1, "该订单已核销或已取消"), "", "ajax");
    }
    $type = trim($_GPC["type"]);
    if ($type == "status") {
        $code = intval($_GPC["code"]);
        if ($code != $order["code"]) {
            imessage(error(-1, "兑换码不正确"), "", "ajax");
        }
        seckill_order_update($order, "status");
        imessage(error(0, "核销成功"), "", "ajax");
        return 1;
    }
    if ($type == "cancel") {
        $res = seckill_order_update($order, "cancel");
        imessage(error(0, "取消成功"), "", "ajax");
    }
}

?>