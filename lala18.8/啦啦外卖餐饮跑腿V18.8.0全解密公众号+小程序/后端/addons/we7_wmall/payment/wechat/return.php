<?php


error_reporting(0);
define("IN_MOBILE", true);
if (empty($_GET["outtradeno"])) {
    exit("fail");
}
require "../../../../framework/bootstrap.inc.php";
require "../../../../addons/we7_wmall/payment/__init.php";
$out_trade_no = $_GET["outtradeno"];
$body = explode(":", $_GET["attach"]);
$_W["weid"] = intval($body[0]);
$_W["uniacid"] = $_W["weid"];
$_W["account"] = uni_fetch($_W["uniacid"]);
$_W["uniaccount"] = $_W["account"];
$_W["acid"] = $_W["uniaccount"]["acid"];
$type = trim($body[1]) ? trim($body[1]) : "wap";
$log = pdo_fetch("SELECT * FROM " . tablename("core_paylog") . " WHERE `uniontid`=:uniontid", array(":uniontid" => $out_trade_no));
if (!empty($log)) {
    $site = WeUtility::createModuleSite($log["module"]);
    $method = "payResult";
    if (!is_error($site)) {
        $ret["uniacid"] = $log["uniacid"];
        $ret["tid"] = $log["tid"];
        $ret["result"] = "success";
        $ret["from"] = "return";
        $ret["type"] = $log["type"];
        $ret["channel"] = $type;
        $site->{$method}($ret);
        exit;
    }
}
exit("fail");

?>