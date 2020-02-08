<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$config_close = $_W["we7_wmall"]["config"]["close"];
if ($config_close["status"] == 2 && $_W["_controller"] != "common") {
    if (!empty($config_close["url"])) {
        header("location:" . $config_close["url"]);
        exit;
    }
    $tips = !empty($config_close["tips"]) ? $config_close["tips"] : "亲,平台休息中。。。";
    imessage($tips, "close", "info");
}
$_W["role"] = "consumer";
$_W["role_cn"] = "下单顾客";

?>