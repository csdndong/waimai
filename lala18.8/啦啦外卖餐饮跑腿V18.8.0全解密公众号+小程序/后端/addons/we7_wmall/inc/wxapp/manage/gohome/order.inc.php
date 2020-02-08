<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("plugin");
pload()->model("gohome");
if ($ta == "list") {
    $records = gohome_order_fetchall();
    $result = array("records" => $records["orders"]);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "confirm") {
        $code = trim($_GPC["code"]);
        $order = pdo_get("tiny_wmall_gohome_order", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "code" => $code));
        if (empty($order)) {
            imessage(error(-1, "订单不存在"), "", "ajax");
        }
        $result = gohome_order_update($order, "confirm");
        imessage($result, "", "ajax");
    } else {
        if ($ta == "detail") {
            $id = intval($_GPC["id"]);
            $order = gohome_order_fetch($id);
            $result = array("order" => $order);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "remark") {
                $id = intval($_GPC["id"]);
                if ($_W["ispost"]) {
                    $remark = trim($_GPC["remark"]);
                    pdo_update("tiny_wmall_gohome_order", array("remark" => $remark), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
                    imessage(error(0, "订单成功添加备注"), "", "ajax");
                }
                $order = gohome_order_fetch($id);
            } else {
                if ($ta == "cancel") {
                    $id = intval($_GPC["id"]);
                    $team_cancel = intval($_GPC["team_cancel"]);
                    $extra = array();
                    if ($team_cancel == 1) {
                        $extra = array("team_cancel" => 1);
                    }
                    $result = gohome_order_update($id, "cancel", $extra);
                    if (!empty($result["message"]["is_refund"])) {
                        imessage(error(0, "取消订单成功," . $result["message"]["refund_message"]), "", "ajax");
                    } else {
                        imessage(error(0, "取消订单成功"), "", "ajax");
                    }
                } else {
                    if ($ta == "status") {
                        $id = intval($_GPC["id"]);
                        $type = trim($_GPC["type"]);
                        $result = gohome_order_update($id, $type);
                        imessage($result, "", "ajax");
                    } else {
                        if ($ta == "print") {
                            $order_id = intval($_GPC["id"]);
                            $result = gohome_order_print($order_id);
                            if (is_error($result)) {
                                imessage(error(-1, $result["message"]), "", "ajax");
                            }
                            imessage(error(0, base64_decode("6K6i5Y2V5omT5Y2w5oiQ5Yqf")), "", "ajax");
                        }
                    }
                }
            }
        }
    }
}

?>