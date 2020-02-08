<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "统一收银台";
$_config = $_W["we7_wmall"]["config"];
$id = intval($_GPC["id"]);
$type = trim($_GPC["order_type"]);
if (empty($id) || empty($type)) {
    imessage("订单id不能为空", "", "error");
}
$tables = array("advertise" => "tiny_wmall_advertise_trade");
$order = pdo_get($tables[$type], array("uniacid" => $_W["uniacid"], "id" => $id));
if (empty($order)) {
    imessage("订单不存在或已删除", "", "error");
}
if (!empty($order["is_pay"])) {
    imessage("该订单已付款", "", "info");
}
$order_sn = $order["order_sn"];
$record = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "order_id" => $id, "order_type" => $type, "order_sn" => $order_sn));
if (empty($record)) {
    $record = array("uniacid" => $_W["uniacid"], "agentid" => $order["agentid"], "uid" => $order["sid"], "order_sn" => $order_sn, "order_id" => $id, "order_type" => $type, "fee" => $order["final_fee"], "status" => 0, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_paylog", $record);
    $record["id"] = pdo_insertid();
} else {
    if ($record["status"] == 1) {
        imessage("该订单已支付,请勿重复支付", "", "error");
    }
}
$logo = $_config["mall"]["logo"];
$routers = array("advertise" => array("title" => (string) $order["title"] . "-" . $record["order_sn"], "url_pay" => imurl("manage/pay/pay", array("id" => $order["id"], "order_type" => "advertise"), true), "url_detail" => imurl("manage/advertise/" . $order["type"], array("id" => $order["id"]), true)));
$title = $routers[$type]["title"];
$data = array("title" => $title, "logo" => $logo, "fee" => $record["fee"]);
pdo_update("tiny_wmall_paylog", array("data" => iserializer($data)), array("id" => $record["id"]));
$params = array("module" => "we7_wmall", "ordersn" => $record["order_sn"], "tid" => $record["order_sn"], "user" => $order["sid"], "openid" => $_W["openid"], "fee" => $record["fee"], "title" => $title, "order_type" => $type, "sid" => $order["sid"], "url_detail" => $routers[$type]["url_detail"]);
$url_pay = $url_detail = "";
$log = pdo_get("core_paylog", array("uniacid" => $_W["uniacid"], "module" => $params["module"], "tid" => $params["tid"]));
if (empty($log)) {
    $log = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "openid" => $params["openid"] ? $params["openid"] : $params["user"], "module" => $params["module"], "uniontid" => date("YmdHis") . random(14, 1), "tid" => $params["tid"], "fee" => $params["fee"], "card_fee" => $params["fee"], "status" => "0", "is_usecard" => "0");
    pdo_insert("core_paylog", $log);
} else {
    if ($log["status"] == 1) {
        imessage("该订单已支付,请勿重复支付", "", "error");
    }
}
if ($order["final_fee"] == 0) {
    $params = base64_encode(json_encode($params));
    header("location:" . imurl("manage/pay/cash/credit", array("params" => $params)));
    exit;
}
$payment = array("wechat", "credit", "alipay");
if (empty($payment)) {
    imessage("没有有效的支付方式, 请联系网站管理员.", "", "error");
}
$pay_type = !empty($_GPC["pay_type"]) ? trim($_GPC["pay_type"]) : $order["pay_type"];
if ($pay_type && in_array($pay_type, $payment)) {
    $params = base64_encode(json_encode($params));
    header("location:" . imurl("manage/pay/cash/" . $pay_type, array("params" => $params)));
    exit;
}

?>