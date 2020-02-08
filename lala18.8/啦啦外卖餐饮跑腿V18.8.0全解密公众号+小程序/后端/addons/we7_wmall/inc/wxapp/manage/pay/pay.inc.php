<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "统一收银台";
$_config = $_W["we7_wmall"]["config"];
$id = intval($_GPC["id"]);
$type = trim($_GPC["order_type"]);
if (empty($id) || empty($type)) {
    imessage(error(-1, "订单id不能为空"), "", "ajax");
}
$tables = array("advertise" => "tiny_wmall_advertise_trade");
$order = pdo_get($tables[$type], array("uniacid" => $_W["uniacid"], "id" => $id));
if (empty($order)) {
    imessage(error(-1, "订单不存在或已删除"), "", "ajax");
}
if (!empty($order["is_pay"])) {
    imessage(error(-1, "该订单已付款"), "", "ajax");
}
$order_sn = $order["order_sn"];
$record = pdo_get("tiny_wmall_paylog", array("uniacid" => $_W["uniacid"], "order_id" => $id, "order_type" => $type, "order_sn" => $order_sn));
if (empty($record)) {
    $record = array("uniacid" => $_W["uniacid"], "agentid" => $order["agentid"], "uid" => $order["sid"], "order_sn" => $order_sn, "order_id" => $id, "order_type" => $type, "fee" => $order["final_fee"], "status" => 0, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_paylog", $record);
    $record["id"] = pdo_insertid();
} else {
    if ($record["status"] == 1) {
        imessage(error(-1, "该订单已支付,请勿重复支付"), "", "ajax");
    }
}
$logo = $_config["mall"]["logo"];
$routers = array("advertise" => array("title" => (string) $order["title"] . "-" . $record["order_sn"]));
$title = $routers[$type]["title"];
$data = array("title" => $title, "logo" => tomedia($logo), "fee" => $record["fee"]);
pdo_update("tiny_wmall_paylog", array("data" => iserializer($data)), array("id" => $record["id"]));
$params = array("module" => "we7_wmall", "ordersn" => $record["order_sn"], "tid" => $record["order_sn"], "user" => $order["sid"], "openid" => $_W["openid"], "fee" => $record["fee"], "title" => $title, "order_type" => $type, "sid" => $order["sid"]);
$url_pay = $url_detail = "";
$log = pdo_get("core_paylog", array("uniacid" => $_W["uniacid"], "module" => $params["module"], "tid" => $params["tid"]));
if (empty($log)) {
    $log = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "openid" => $params["openid"] ? $params["openid"] : $params["user"], "module" => $params["module"], "uniontid" => date("YmdHis") . random(14, 1), "tid" => $params["tid"], "fee" => $params["fee"], "card_fee" => $params["fee"], "status" => "0", "is_usecard" => "0");
    pdo_insert("core_paylog", $log);
} else {
    if ($log["status"] == 1) {
        imessage(error(-1, "该订单已支付,请勿重复支付"), "", "ajax");
    }
}
if ($order["final_fee"] == 0) {
    imessage(error(-1, "该订单金额有误"), "", "ajax");
}
$payment = array("wechat", "credit", "alipay");
$pay_type = !empty($_GPC["pay_type"]) ? trim($_GPC["pay_type"]) : $order["pay_type"];
if ($pay_type && !$_GPC["type"] && in_array($pay_type, array_keys($payment)) && $pay_type == "credit") {
    if ($_W["we7_wmall"]["store"]["account"]["amount"] < $params["fee"]) {
        imessage(error(-1000, "余额不足以支付, 需要 " . $params["fee"] . ", 当前 " . $_W["store"]["account"]["amount"] . " 元"), "", "ajax");
    }
    $fee = floatval($params["fee"]);
    $result = store_update_account($params["sid"], 0 - $fee, 3, "", $remark = "购买平台广告位");
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    pdo_update("core_paylog", array("status" => "1", "type" => "credit"), array("plid" => $log["plid"]));
    $site = WeUtility::createModuleSite($log["module"]);
    if (!is_error($site)) {
        $site->weid = $_W["weid"];
        $site->uniacid = $_W["uniacid"];
        $site->inMobile = true;
        $method = "payResult";
        if (method_exists($site, $method)) {
            $ret = array();
            $ret["result"] = "success";
            $ret["type"] = "credit";
            $ret["channel"] = "wxapp";
            $ret["from"] = "notify";
            $ret["tid"] = $log["tid"];
            $ret["uniontid"] = $log["uniontid"];
            $ret["user"] = $log["openid"];
            $ret["fee"] = $log["fee"];
            $ret["weid"] = $log["weid"];
            $ret["uniacid"] = $log["uniacid"];
            $ret["acid"] = $log["acid"];
            $ret["is_usecard"] = $log["is_usecard"];
            $ret["card_type"] = $log["card_type"];
            $ret["card_fee"] = $log["card_fee"];
            $ret["card_id"] = $log["card_id"];
            $result = array("message" => array("errno" => 0, "message" => "支付成功"));
            echo json_encode($result);
            $site->{$method}($ret);
        }
    }
}

?>