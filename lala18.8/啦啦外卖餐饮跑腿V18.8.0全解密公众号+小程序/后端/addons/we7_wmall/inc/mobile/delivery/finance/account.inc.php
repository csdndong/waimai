<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "提现账户";
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "bank";
    mload()->classs("wxpay");
    $wxpay = new wxpay();
    $bank_list = $wxpay->getback();
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $_deliveryer["id"]));
    $account = iunserializer($deliveryer["account"]);
    if ($_W["isajax"]) {
        $params = $_GPC["params"];
        $bank_id = intval($params["bank"]["id"]);
        $bank = array("id" => intval($params["bank"]["id"]), "title" => $bank_list[$bank_id]["title"], "account" => trim($params["bank"]["account"]), "realname" => trim($params["bank"]["realname"]));
        $alipay = array("realname" => trim($params["alipay"]["realname"]), "account" => trim($params["alipay"]["account"]));
        $account["bank"] = $bank;
        $account["alipay"] = $alipay;
        $data = array("account" => iserializer($account));
        pdo_update("tiny_wmall_deliveryer", $data, array("uniacid" => $_W["uniacid"], "id" => $_deliveryer["id"]));
        imessage(error(0, ""), "", "ajax");
    }
}
include itemplate("finance/account");

?>