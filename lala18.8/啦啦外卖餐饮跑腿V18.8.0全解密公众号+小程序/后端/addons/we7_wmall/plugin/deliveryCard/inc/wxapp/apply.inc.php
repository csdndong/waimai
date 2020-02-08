<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $payment = get_available_payment("deliveryCard");
    $pay_types = order_pay_types();
    $endtime = strtotime(date("Y-m-d"));
    if (0 < $_W["member"]["setmeal_endtime"]) {
        $setmeal_endtime = $_W["member"]["setmeal_endtime"];
        if ($endtime < $setmeal_endtime) {
            $endtime = $setmeal_endtime;
        }
    }
    $cards = pdo_fetchall("select * from " . tablename("tiny_wmall_delivery_cards") . " where uniacid = :uniacid and status = 1 order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"]));
    if (empty($cards)) {
        imessage(error(-1, "平台未设置配送会员卡套餐"), "", "ajax");
    }
    foreach ($cards as &$row) {
        $row["starttime"] = date("Y-m-d", $endtime);
        $row["endtime"] = date("Y-m-d", strtotime((string) $row["days"] . "days", $endtime));
    }
    imessage(error(0, $cards), "", "ajax");
}
if ($op == "pay") {
    $id = intval($_GPC["setmeal_id"]);
    $card = pdo_get("tiny_wmall_delivery_cards", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($card)) {
        imessage(error(-1, "会员卡套餐不存在"), "", "ajax");
    }
    $pay_type = trim($_GPC["pay_type"]);
    if (!in_array($pay_type, array("alipay", "wechat", "credit"))) {
        imessage(error(-1, "支付方式错误"), "", "ajax");
    }
    $order = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "ordersn" => date("YmdHis") . random(6, true), "card_id" => $card["id"], "final_fee" => $card["price"], "pay_type" => $pay_type, "is_pay" => 0, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_delivery_cards_order", $order);
    $id = pdo_insertid();
    imessage(error(0, $id), "", "ajax");
}
if ($op == "pay1") {
    $id = intval($_GPC["setmeal_id"]);
    $card = pdo_get("tiny_wmall_delivery_cards", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($card)) {
        imessage(error(-1, "会员卡套餐不存在"), "", "ajax");
    }
    $order = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "ordersn" => date("YmdHis") . random(6, true), "card_id" => $card["id"], "final_fee" => $card["price"], "pay_type" => "", "is_pay" => 0, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_delivery_cards_order", $order);
    $id = pdo_insertid();
    imessage(error(0, $id), "", "ajax");
}

?>