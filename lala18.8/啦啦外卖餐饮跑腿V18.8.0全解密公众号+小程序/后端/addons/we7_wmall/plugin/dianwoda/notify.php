<?php
define("IN_MOBILE", true);
require "../../../../framework/bootstrap.inc.php";
include IA_ROOT . "/addons/we7_wmall/version.php";
include IA_ROOT . "/addons/we7_wmall/defines.php";
include IA_ROOT . "/addons/we7_wmall/model.php";
require IA_ROOT . "/addons/we7_wmall/class/TyAccount.class.php";
$originBody = file_get_contents("php://input");
$body = json_decode($originBody, true);
flog("点我达回调", $body, "dianwoda");
if (!empty($body)) {
    $ordersn = $body["content"]["order_original_id"];
    if (strexists($ordersn, "_")) {
        $snArr = explode("_", $ordersn);
        $ordersn = $snArr[1];
    }
    $order = pdo_get("tiny_wmall_order", array("ordersn" => $ordersn), array("id", "uniacid"));
    if (!empty($order)) {
        $_W["uniacid"] = $order["uniacid"];
        $_W["account"] = uni_fetch($_W["uniacid"]);
        $_W["uniaccount"] = $_W["account"];
        $_W["acid"] = $_W["uniaccount"]["acid"];
        $_W["_plugin"] = array("name" => "dianwoda");
        mload()->model("plugin");
        pload()->classs("subscribe");
        $subscribe = new subscribe();
        $subscribe->start($_GET, $body, $originBody);
    }
    exit(json_encode(array("code" => "success")));
}

?>
