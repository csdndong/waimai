<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $data = array("status" => intval($_GPC["status"]), "key" => trim($_GPC["key"]), "secret" => trim($_GPC["secret"]));
        set_plugin_config("eleme", $data);
        $description = htmlspecialchars_decode($_GPC["description"]);
        set_config_text("饿了么平台对接说明", "eleme:description", htmlspecialchars_decode($_GPC["description"]));
        imessage(error(0, "设置饿了么平台对接成功"), "refresh", "ajax");
    }
    $eleme = get_plugin_config("eleme");
    $eleme["description"] = get_config_text("eleme:description");
    $urls = array("callback" => imurl("eleme/index", array(), true), "subscribe" => imurl("eleme/api", array(), true));
}
include itemplate("config");

?>