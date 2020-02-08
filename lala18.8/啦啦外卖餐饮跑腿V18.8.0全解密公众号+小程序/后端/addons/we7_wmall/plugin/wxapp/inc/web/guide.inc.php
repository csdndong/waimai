<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "小程序启动图";
    if ($_W["ispost"]) {
        $data = $_GPC["guide"];
        $data = base64_encode(json_encode($data));
        set_plugin_config("wxapp.guide", $data);
        imessage(error(0, "保存成功"), iurl("wxapp/guide/index"), "ajax");
    }
    $guide = get_plugin_config("wxapp.guide");
    $guide = json_decode(base64_decode($guide), true);
}
include itemplate("guide");

?>
