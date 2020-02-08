<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $data = array("sourceid" => trim($_GPC["sourceid"]), "appkey" => trim($_GPC["appkey"]), "appsecret" => trim($_GPC["appsecret"]), "status" => intval($_GPC["status"]) ? intval($_GPC["status"]) : 0);
        set_plugin_config("dada", $data);
        imessage(error(0, "设置达达配送成功"), "refresh", "ajax");
    }
    $dada = get_plugin_config("dada");
}
include itemplate("config");

?>