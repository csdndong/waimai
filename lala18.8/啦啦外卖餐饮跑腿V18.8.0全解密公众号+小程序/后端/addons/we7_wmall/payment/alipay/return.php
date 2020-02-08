<?php
error_reporting(0);
define("IN_MOBILE", true);
if (empty($_GET["out_trade_no"])) {
    if (!empty($_GET["alipayresult"])) {
        $alipayresult = json_decode($_GET["alipayresult"], true);
        $alipayresult = $alipayresult["alipay_trade_app_pay_response"];
        $_GET["out_trade_no"] = $alipayresult["out_trade_no"];
        $_GET["body"] = "0:h5app";
    }
    if (empty($_GET["out_trade_no"])) {
        exit("fail");
    }
}
require "../../../../framework/bootstrap.inc.php";
require "../../../../addons/we7_wmall/payment/__init.php";
require "../../../../addons/we7_wmall/class/TyAccount.class.php";
$out_trade_no = $_GET["out_trade_no"];
$body = explode(":", $_GET["body"]);
$_W["weid"] = intval($body[0]);
$_W["uniacid"] = $_W["weid"];
$_W["account"] = uni_fetch($_W["uniacid"]);
$_W["uniaccount"] = $_W["account"];
$_W["acid"] = $_W["uniaccount"]["acid"];
//注意：支付宝的参数有个坑
$type = trim($body[1], "\"") ? trim($body[1], "\"") : "wap";
if ($type == "wap") {
    $payment_from = trim($body[2], "\"");
    if ($payment_from == "plugincenter") {
        $config_payment = get_plugin_config("plugincenter.pay_type");
    } else {
        $config_payment = get_system_config("payment");
    }
    $config_alipay = $config_payment["alipay"];
    if (empty($config_alipay)) {
        exit("fail");
    }
    $prepares = array();
    foreach ($_GET as $key => $value) {
        if ($key != "sign" && $key != "sign_type") {
            $prepares[] = (string) $key . "=" . $value;
        }
    }
    sort($prepares);
    $string = implode($prepares, "&");
    $string .= $config_alipay["secret"];
    $sign = md5($string);
    $result = $sign == $_GET["sign"] && $_GET["is_success"] == "T" && ($_GET["trade_status"] == "TRADE_FINISHED" || $_GET["trade_status"] == "TRADE_SUCCESS");
}
if (!empty($result) || $type == "h5app") {
    $_GET["query_type"] = "return";
    $out_trade_no = trim($out_trade_no, "\"");
    $log = pdo_fetch("SELECT * FROM " . tablename("core_paylog") . " WHERE `uniontid`=:uniontid", array(":uniontid" => $out_trade_no));
    if (!empty($log)) {
        $site = WeUtility::createModuleSite($log["module"]);
        $method = "payResult";
        if (!is_error($site)) {
            $ret["uniacid"] = $log["uniacid"];
            $ret["acid"] = $log["acid"];
            $ret["tid"] = $log["tid"];
            $ret["result"] = "success";
            $ret["from"] = "return";
            $ret["type"] = $log["type"];
            $ret["channel"] = $type;
            $site->{$method}($ret);
            exit;
        }
    }
}
exit("fail");

?>
