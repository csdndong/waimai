<?php
error_reporting(0);
define(base64_decode("SU5fTU9CSUxF"), true);
if (!empty($_POST)) {
    require "../../../../framework/bootstrap.inc.php";
    require "../../../../addons/we7_wmall/payment/__init.php";
    require "../../../../addons/we7_wmall/class/TyAccount.class.php";
    $out_trade_no = $_POST["out_trade_no"];
    $body = explode(":", $_POST["body"]);
    $_W["weid"] = intval($body[0]);
    $_W["uniacid"] = $_W["weid"];
    $_W["account"] = uni_fetch($_W["uniacid"]);
    $_W["uniaccount"] = $_W["account"];
    $_W["acid"] = $_W["uniaccount"]["acid"];
    $type = trim($body[1]) ? trim($body[1]) : "wap";
    $payment_from = trim($body[2]);
    if ($payment_from == "plugincenter") {
        $config_payment = get_plugin_config("plugincenter.pay_type");
    } else {
        $config_payment = get_system_config("payment");
    }
    if ($type == "wap") {
        $config_alipay = $config_payment["alipay"];
        if (empty($config_alipay)) {
            exit("fail");
        }
        $prepares = array();
        foreach ($_POST as $key => $value) {
            if ($key != "sign" && $key != "sign_type") {
                $prepares[] = (string) $key . "=" . $value;
            }
        }
        sort($prepares);
        $string = implode($prepares, "&");
        $string .= $config_alipay["secret"];
        $sign = md5($string);
    } else {
        if ($_POST["trade_status"] != "TRADE_SUCCESS") {
            exit("fail");
        }
        $config_alipay = $config_payment["app_alipay"];
        if (empty($config_alipay)) {
            exit("fail");
        }
        $public_key = file_get_contents(IA_ROOT . "/addons/we7_wmall/cert/" . $config_alipay["public_key"] . "/public_key.pem");
        $public_key = openssl_pkey_get_public($public_key);
        if (empty($public_key)) {
            exit("fail");
        }
        $return_sign = $_POST["sign"];
        ksort($_POST);
        $prepares = array();
        foreach ($_POST as $key => $value) {
            if ($key != "sign" && $key != "sign_type") {
                $prepares[] = (string) $key . "=" . $value;
            }
        }
        $string = implode($prepares, "&");
        $return_sign = str_replace(" ", "+", $_POST["sign"]);
        $return_sign = base64_decode($return_sign);
        if ($config_alipay["rsa_type"] == "" || $config_alipay["rsa_type"] == "RSA") {
            $rsaverify = openssl_verify($string, $return_sign, $public_key);
        } else {
            $rsaverify = openssl_verify($string, $return_sign, $public_key, OPENSSL_ALGO_SHA256);
        }
    }
    if ($sign == $_POST["sign"] || $rsaverify) {
        $_POST["query_type"] = "notify";
        $log = pdo_fetch("SELECT * FROM " . tablename("core_paylog") . " WHERE `uniontid`=:uniontid", array(":uniontid" => $out_trade_no));
        if (!empty($log) && $log["status"] == "0" && ($_POST["total_fee"] == $log["card_fee"] || $_POST["total_amount"] == $log["card_fee"])) {
            $log["transaction_id"] = $_POST["trade_no"];
            $log["type"] = "alipay";
            $record = array();
            $record["status"] = "1";
            $record["type"] = "alipay";
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
}
exit("fail");

?>