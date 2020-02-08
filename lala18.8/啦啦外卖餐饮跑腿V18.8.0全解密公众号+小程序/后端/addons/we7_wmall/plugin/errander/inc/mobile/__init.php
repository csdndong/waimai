<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if (empty($_config_plugin["map"]["location_x"]) || empty($_config_plugin["map"]["location_y"])) {
    $_W["_plugin"]["config"]["map"] = array("location_x" => "39.90923", "location_y" => "116.397428");
    $_config_plugin["map"] = $_W["_plugin"]["config"]["map"];
}
if (!$_config_plugin["status"]) {
    $_config_plugin["close_reason"] = $_config_plugin["close_reason"] ? $_config_plugin["close_reason"] : "平台已关闭跑腿功能";
    imessage($_config_plugin["close_reason"], "", "info");
}

?>