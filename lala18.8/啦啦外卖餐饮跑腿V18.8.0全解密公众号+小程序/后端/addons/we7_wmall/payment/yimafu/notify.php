<?php


error_reporting(0);
define("IN_MOBILE", true);
if (!empty($_GET)) {
    require "../../../../framework/bootstrap.inc.php";
    require "../../../../addons/we7_wmall/payment/__init.php";
    require "../../../../addons/we7_wmall/class/TyAccount.class.php";
    $out_trade_no = $_GET["selfOrdernum"];
    $_W["weid"] = intval($_GET["uniacid"]);
    $_W["uniacid"] = $_W["weid"];
    $_W["account"] = uni_fetch($_W["uniacid"]);
    $_W["uniaccount"] = $_W["account"];
    $_W["acid"] = $_W["uniaccount"]["acid"];
    $log = pdo_fetch("SELECT * FROM " . tablename("core_paylog") . " WHERE `uniontid`=:uniontid", array(":uniontid" => $out_trade_no));
    if (!empty($log) && $log["status"] == "0" && $_GET["money"] == $log["card_fee"]) {
        $log["transaction_id"] = $_GET["orderno"];
        $log["type"] = "yimafu";
        $record = array();
        $record["status"] = "1";
        $record["type"] = "yimafu";
        pdo_update("core_paylog", $record, array("plid" => $log["plid"]));
        $site = WeUtility::createModuleSite($log["module"]);
        if (!is_error($site)) {
            $method = "payResult";
            if (method_exists($site, $method)) {
                $ret = array();
                $ret["uniacid"] = $log["uniacid"];
                $ret["acid"] = $log["acid"];
                $ret["result"] = "success";
                $ret["type"] = $log["type"];
                $ret["channel"] = $type;
                $ret["from"] = "notify";
                $ret["tid"] = $log["tid"];
                $ret["uniontid"] = $log["uniontid"];
                $ret["transaction_id"] = $log["transaction_id"];
                $ret["user"] = $log["openid"];
                $ret["fee"] = $log["fee"];
                $ret["is_usecard"] = $log["is_usecard"];
                $ret["card_type"] = $log["card_type"];
                $ret["card_fee"] = $log["card_fee"];
                $ret["card_id"] = $log["card_id"];
                $site->{$method}($ret);
                exit("success");
            }
        }
    }
}
exit("fail");

?>