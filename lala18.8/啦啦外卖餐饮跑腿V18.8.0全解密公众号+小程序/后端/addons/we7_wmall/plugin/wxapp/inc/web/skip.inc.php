<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "跳转小程序设置";
    if ($_W["ispost"] && !empty($_GPC["wxapp_skip"])) {
        $wxapp_skip = array();
        foreach ($_GPC["wxapp_skip"]["title"] as $key => $val) {
            $val = trim($val);
            if (empty($val)) {
                continue;
            }
            $appid = $_GPC["wxapp_skip"]["appid"][$key];
            if (empty($appid)) {
                continue;
            }
            $wxapp_skip[] = array("title" => $val, "appid" => $appid);
        }
        set_plugin_config("wxapp.miniProgramAppIdList", $wxapp_skip);
        imessage(error(0, "编辑跳转小程序成功"), "refresh", "ajax");
    }
    $wxapp_skip = get_plugin_config("wxapp.miniProgramAppIdList");
}
include itemplate("skip");

?>
