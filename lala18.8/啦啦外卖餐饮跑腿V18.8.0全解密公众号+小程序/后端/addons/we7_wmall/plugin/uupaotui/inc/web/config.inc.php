<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $data = array("appid" => trim($_GPC["appid"]), "appkey" => trim($_GPC["appkey"]), "openid" => trim($_GPC["openid"]), "type" => trim($_GPC["type"]), "city" => trim($_GPC["city"]), "status" => intval($_GPC["status"]) ? intval($_GPC["status"]) : 0);
        set_plugin_config("uupaotui", $data);
        imessage(error(0, "设置UU跑腿配送成功"), "refresh", "ajax");
    }
    $uupaotui = get_plugin_config("uupaotui");
}
include itemplate("config");

?>