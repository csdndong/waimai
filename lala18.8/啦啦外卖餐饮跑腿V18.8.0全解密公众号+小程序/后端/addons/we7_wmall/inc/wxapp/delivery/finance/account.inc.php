<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    mload()->classs("wxpay");
    $wxpay = new wxpay();
    $bank_list = $wxpay->getback();
    $bank_list = array_values($bank_list);
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $_deliveryer["id"]));
    if (empty($deliveryer)) {
        imessage(error(-1, "配送员不存在"), "", "ajax");
    }
    if ($deliveryer["status"] != 1) {
        imessage(error(-1, "配送员已被删除"), "", "ajax");
    }
    $account = iunserializer($deliveryer["account"]);
    if ($_W["ispost"]) {
        $params = $_GPC["params"];
        $bank = array("id" => intval($params["bank"]["id"]), "title" => trim($params["bank"]["title"]), "account" => trim($params["bank"]["account"]), "realname" => trim($params["bank"]["realname"]));
        $alipay = array("realname" => trim($params["alipay"]["realname"]), "account" => trim($params["alipay"]["account"]));
        $account["bank"] = $bank;
        $account["alipay"] = $alipay;
        $data = array("account" => iserializer($account));
        pdo_update("tiny_wmall_deliveryer", $data, array("uniacid" => $_W["uniacid"], "id" => $_deliveryer["id"]));
    }
    $result = array("bank_list" => $bank_list, "bank" => $account["bank"], "alipay" => $account["alipay"]);
    imessage(error(0, $result), "", "ajax");
}

?>