<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $data = array("appname" => trim($_GPC["appname"]), "hostname" => trim($_GPC["hostname"]), "appsecret" => trim($_GPC["appsecret"]));
        set_plugin_config("majiaApp", $data);
        imessage(error(0, "设置马甲App成功"), "refresh", "ajax");
    }
    $majia = get_plugin_config("majiaApp");
}
include itemplate("config");

?>