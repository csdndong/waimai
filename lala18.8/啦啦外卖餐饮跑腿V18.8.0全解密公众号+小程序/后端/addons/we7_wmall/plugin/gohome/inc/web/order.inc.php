<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "订单列表";
    $stores = store_fetchall(array("id", "title"));
    $order_status = gohome_order_status();
    $refund_status = intval($_GPC["refund_status"]);
    if (0 < $refund_status) {
        $_GPC["status"] = 7;
    }
    if (!empty($_GPC["addtime"]["start"]) && !empty($_GPC["addtime"]["end"])) {
        $_GPC["starttime"] = strtotime($_GPC["addtime"]["start"]);
        $_GPC["endtime"] = strtotime($_GPC["addtime"]["end"]);
    }
    $agentid = intval($_GPC["agentid"]);
    $filter = $_GPC;
    $data = gohome_order_fetchall($filter);
    $orders = $data["orders"];
    $pager = $data["pager"];
    include itemplate("order");
} else {
    if ($op == "remark") {
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $remark = trim($_GPC["remark"]);
            pdo_update("tiny_wmall_gohome_order", array("remark" => $remark), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
            imessage(error(0, "订单成功添加备注"), referer(), "ajax");
        }
        $order = gohome_order_fetch($id);
        include itemplate("orderOp");
    } else {
        if ($op == "cancel") {
            $id = intval($_GPC["id"]);
            $team_cancel = intval($_GPC["team_cancel"]);
            $extra = array();
            if ($team_cancel == 1) {
                $extra = array("team_cancel" => 1);
            }
            $result = gohome_order_update($id, "cancel", $extra);
            if (is_array($result["message"]) && $result["message"]["is_refund"]) {
                imessage(error(0, "取消订单成功," . $result["message"]["refund_message"]), referer(), "ajax");
            } else {
                imessage(error(0, "取消订单成功"), referer(), "ajax");
            }
        } else {
            if ($op == "detail") {
                $id = intval($_GPC["id"]);
                $_W["page"]["title"] = "订单详情";
                $order = gohome_order_fetch($id);
                $order_status = gohome_order_status();
                include itemplate("order");
            } else {
                if ($op == "status") {
                    $id = intval($_GPC["id"]);
                    $type = trim($_GPC["type"]);
                    $result = gohome_order_update($id, $type);
                    imessage($result, referer(), "ajax");
                } else {
                    if ($op == "refund_handle") {
                        $id = intval($_GPC["id"]);
                        mload()->model("plugin");
                        pload()->model("gohome");
                        $refund = gohome_order_begin_refund($id);
                        if (is_error($refund)) {
                            imessage(error(-1, $refund["message"]), "", "ajax");
                        }
                        imessage(error(0, "取消订单成功, " . $refund["message"]), "", "ajax");
                    } else {
                        if ($op == "refund_status") {
                            $id = intval($_GPC["id"]);
                            mload()->model("plugin");
                            pload()->model("gohome");
                            $order = gohome_order_fetch($id);
                            if (empty($order)) {
                                imessage(error(-1, "订单不存在"), referer(), "ajax");
                            }
                            if ($order["refund_status"] == 0) {
                                imessage(error(-1, "退款申请不存"), referer(), "ajax");
                            }
                            if ($order["refund_status"] == 3) {
                                imessage(error(-1, "已退款成功"), referer(), "ajax");
                            }
                            pdo_update("tiny_wmall_gohome_order", array("refund_status" => 3), array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
                            imessage(error(0, "设置为已退款成功"), referer(), "ajax");
                        } else {
                            if ($op == "print") {
                                $order_id = intval($_GPC["id"]);
                                $result = gohome_order_print($order_id);
                                if (is_error($result)) {
                                    imessage(error(-1, $result["message"]), "", "ajax");
                                }
                                imessage(error(0, "订单打印成功"), "", "ajax");
                            }
                        }
                    }
                }
            }
        }
    }
}

?>