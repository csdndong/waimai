<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    mload()->classs("wxpay");
    $wxpay = new wxpay();
    $bank_list = $wxpay->getback();
    $bank_list = array_values($bank_list);
    $account = $_W["member"]["account"];
    if ($_W["ispost"]) {
        $params = json_decode(htmlspecialchars_decode($_GPC["params"]), true);
        $bank = array("id" => intval($params["bank"]["id"]), "title" => trim($params["bank"]["title"]), "account" => trim($params["bank"]["account"]), "realname" => trim($params["bank"]["realname"]));
        $alipay = array("realname" => trim($params["alipay"]["realname"]), "account" => trim($params["alipay"]["account"]));
        $account["bank"] = $bank;
        $account["alipay"] = $alipay;
        $data = array("account" => iserializer($account));
        pdo_update("tiny_wmall_members", $data, array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
        imessage(error(0, "提现账户设置成功"), "", "ajax");
    }
    $result = array("bank_list" => $bank_list, "bank" => $account["bank"], "alipay" => $account["alipay"]);
    imessage(error(0, $result), "", "ajax");
}

?>
