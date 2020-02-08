<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "create") {
    $goods_id = intval($_GPC["goods_id"]);
    $good = creditshop_goods_get($goods_id);
    imessage(error(0, $good), "", "ajax");
}
if ($op == "submit") {
    $goods_id = intval($_GPC["goods_id"]);
    $good = creditshop_goods_get($goods_id);
    $can = creditshop_can_exchange_goods($goods_id);
    if ($can["errno"] == -2) {
        imessage(error(-2, "兑换已达最大次数"), "", "ajax");
    }
    $data = array();
    if (!empty($good)) {
        if ($good["type"] == "redpacket") {
            $data["redpacket"] = $good["redpacket"];
        } else {
            if ($good["type"] == "credit2") {
                $credit2 = $good["credit2"];
                $data["credit2"] = $credit2;
            }
        }
    }
    if ($_W["member"]["credit1"] < $good["use_credit1"]) {
        return error(-1, "积分不足，暂时无法兑换");
    }
    $order = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "agentid" => $_W["agentid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "channel" => $_W["ochannel"], "goods_id" => $goods_id, "goods_type" => $good["type"], "order_sn" => date("YmdHis") . random(6, true), "is_pay" => 0, "code" => random(6, true), "addtime" => TIMESTAMP, "use_credit1" => $good["use_credit1"], "use_credit2" => $good["use_credit2"], "data" => iserializer($data), "username" => trim($_GPC["username"]), "mobile" => trim($_GPC["mobile"]));
    pdo_insert("tiny_wmall_creditshop_order_new", $order);
    $orderid = pdo_insertid();
    $order_info = array("order_id" => $orderid, "redirect" => "pay");
    if (0 < $good["use_credit1"] && $good["use_credit2"] <= 0) {
        $order_info["redirect"] = "detail";
        $status = member_credit_update($_W["member"]["uid"], "credit1", 0 - $good["use_credit1"]);
        if (is_error($status)) {
            imessage(-1, $status["message"], "", "ajax");
        }
        $status = creditshop_order_update($order_info["order_id"], "pay");
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
    }
    imessage(error(0, $order_info), "", "ajax");
}
if ($op == "detail") {
    $order_id = intval($_GPC["order_id"]);
    $order = creditshop_order_get($order_id);
    imessage(error(0, $order), "", "ajax");
}
if ($op == "list") {
    $orders = creditshop_orderall_get();
    imessage(error(0, $orders), "", "ajax");
}
if ($op == "cancel") {
    $order_id = intval($_GPC["order_id"]);
    creditshop_order_update($order_id, "cancel");
    imessage(error(0, "取消成功"), "", "ajax");
}

?>