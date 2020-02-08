<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "menu";
if ($op == "menu") {
    $_W["page"]["title"] = "菜单设置";
    if ($_W["ispost"]) {
        $menu = array_map("intval", $_GPC["menu"]);
        set_plugin_config("diypage.vuemenu", $menu);
        $wxappmenu = array_map("intval", $_GPC["wxappmenu"]);
        set_plugin_config("wxapp.wxappmenu", $wxappmenu);
        imessage(error(0, "菜单设置成功"), referer(), "ajax");
    }
    $config_menu = get_plugin_config("diypage.vuemenu");
    $config_wxappmenu = get_plugin_config("wxapp.wxappmenu");
    $menus = pdo_getall("tiny_wmall_diypage_menu", array("uniacid" => $_W["uniacid"], "version" => 2), array("id", "name"));
}
include itemplate("vue/mall");

?>