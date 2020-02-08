<?php

defined("IN_IA") or exit("Access Denied");
function zhunshibao_check($order)
{
    if ($order["order_type"] != 1) {
        return error(-1, "不是外卖单");
    }
    if ($order["delivery_time"] != "立即送出") {
        return error(-1, "非及时送达订单");
    }
    if ($order["zhunshibao_status"] != 1) {
        return error(-1, "未购买准时宝");
    }
    $config = get_plugin_config("zhunshibao");
    $store = store_fetch($order["sid"], array("delivery_time", "data"));
    if (is_error($store)) {
        return error(-1, "门店信息有误");
    }
    if (empty($store["delivery_time"])) {
        return error(-1, "闂ㄥ簵鏈缃璁￠€佽揪鏃堕");
    }
    $overtime = $order["endtime"] - $order[$config["basic"]["start_time"]] - $store["delivery_time"] * 60;
    if (0 < $overtime) {
        $over_min = ceil($overtime / 60);
        $rule = array();
        if (!empty($store["data"]["zhunshibao"]["rule"])) {
            $store["data"]["zhunshibao"]["rule"] = array_sort($store["data"]["zhunshibao"]["rule"], "time");
            foreach ($store["data"]["zhunshibao"]["rule"] as $val) {
                if ($over_min < $val["time"]) {
                    $rule = $val;
                    break;
                }
            }
            if (empty($rule)) {
                $rule = array_pop($store["data"]["zhunshibao"]["rule"]);
            }
        }
        $update = array("zhunshibao_status" => 2, "zhunshibao_compensate" => 0);
        if (!empty($rule)) {
            if ($config["setting"]["fee_type"] == 1) {
                $update["zhunshibao_compensate"] = $rule["fee"];
            } else {
                if ($config["setting"]["fee_type"] == 2) {
                    $update["zhunshibao_compensate"] = round($order["final_fee"] * $rule["fee"] / 100, 2);
                }
            }
        }
    }
    if (!empty($update)) {
        pdo_update("tiny_wmall_order", $update, array("id" => $order["id"]));
    }
    return true;
}
function zhunshibao_update_status($orderOrId, $status)
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $order), array("id", "agentid", "uid", "ordersn", "zhunshibao_compensate", "zhunshibao_status"));
    }
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    if ($order["zhunshibao_status"] == 0 || $order["zhunshibao_status"] == 1) {
        return error(-1, "不需要进行赔付");
    }
    if ($order["zhunshibao_status"] != 2) {
        return error(-1, "订单超时赔付已处理");
    }
    $update = array("zhunshibao_status" => $status);
    if ($status == 3) {
        $log = array($order["uid"], "准时宝赔付!订单号:" . $order["ordersn"] . ", 赔付金额:" . $order["zhunshibao_compensate"] . "元", "we7_wmall");
        mload()->model("member");
        member_credit_update($order["uid"], "credit2", $order["zhunshibao_compensate"], $log);
        if (0 < $order["agentid"]) {
            mload()->model("agent");
            $remark = "准时宝赔付:" . $order["zhunshibao_compensate"] . "订单id：" . $order["id"];
            agent_update_account($order["agentid"], 0 - $order["zhunshibao_compensate"], 3, $order["id"], $remark);
        }
    }
    pdo_update("tiny_wmall_order", $update, array("id" => $order["id"]));
    return error(0, "订单超时处理成功");
}

?>
