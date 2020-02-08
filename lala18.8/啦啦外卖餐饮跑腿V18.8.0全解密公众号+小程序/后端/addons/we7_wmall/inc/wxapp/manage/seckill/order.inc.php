<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $pay_types = order_pay_types();
    $condition = " where a.uniacid = :uniacid and a.sid = :sid and a.is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $orders = pdo_fetchall("select a.*, b.title as goods_title, b.use_limit_day from " . tablename("tiny_wmall_seckill_order") . " as a left join " . tablename("tiny_wmall_seckill_goods") . " as b on a.goods_id = b.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($orders)) {
        foreach ($orders as &$value) {
            $value["addtime_cn"] = date("m-d H:i:s", $value["addtime"]);
            if ($value["status"] == 1) {
                $value["status_cn"] = "待核销";
                $value["status_color"] = "color-default";
            } else {
                if ($value["status"] == 2) {
                    $value["status_cn"] = "已核销";
                    $value["status_color"] = "color-success";
                } else {
                    if ($value["status"] == 3) {
                        $value["status_cn"] = "已取消";
                        $value["status_color"] = "color-danger";
                    }
                }
            }
            $value["pay_type_cn"] = $value["pay_type"] ? $pay_types[$value["pay_type"]]["text"] : "未支付";
        }
    }
    $result = array("orders" => $orders);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
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
}

?>