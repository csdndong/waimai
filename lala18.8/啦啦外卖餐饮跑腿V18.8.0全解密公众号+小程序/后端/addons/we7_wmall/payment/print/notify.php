<?php
define("IN_MOBILE", true);
global $_GPC;
require "../../../../framework/bootstrap.inc.php";
require "../../../../addons/we7_wmall/payment/__init.php";
require "../../../../addons/we7_wmall/class/TyAccount.class.php";
$orderid = trim($_GPC["orderid"]) ? trim($_GPC["orderid"]) : trim($_GPC["dingdanID"]);
$printer_type = "feie";
if (!empty($_GPC["dingdanID"])) {
    $printer_type = "xixun";
}
if (!empty($_GPC["authkey"])) {
    $printer_type = "lingdian";
    $orderid = trim($_GPC["order_id"]);
}
if (empty($orderid)) {
    islog("printnotify", "打印机回执", array(), "print_sn为空");
    echo "OK";
    exit;
}
if ($printer_type == "lingdian") {
    $order = pdo_get("tiny_wmall_order", array("id" => $orderid), array("id", "uniacid"));
} else {
    $order = pdo_get("tiny_wmall_order", array("print_sn" => $orderid), array("id", "uniacid"));
}
if (empty($order)) {
    $orderid_type = $printer_type == "lingdian" ? "订单id" : "print_sn";
    islog("printnotify", "打印机回调", array(), "订单不存在" . $orderid_type . ":" . $orderid . ",打印机类型" . $printer_type);
    echo "OK";
    exit;
}
$_W["weid"] = $order["uniacid"];
$_W["uniacid"] = $_W["weid"];
$_W["account"] = uni_fetch($_W["uniacid"]);
$_W["uniaccount"] = $_W["account"];
$_W["acid"] = $_W["uniaccount"]["acid"];
$site = WeUtility::createModuleSite("we7_wmall");
if (!is_error($site)) {
    $method = "printResult";
    if (method_exists($site, $method)) {
        $ret = array();
        $ret["uniacid"] = $_W["uniacid"];
        $ret["acid"] = $_W["acid"];
        $ret["result"] = "success";
        $ret["from"] = "notify";
        $ret["order_id"] = $order["id"];
        $ret["order"] = $order;
        $ret["printer_type"] = $printer_type;
        $site->{$method}($ret);
        exit("success");
    }
}

?>
