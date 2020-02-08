<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "页面配色设置";
    if ($_W["ispost"]) {
        $data = array("header" => $_GPC["header"], "store" => array("discount_style" => intval($_GPC["discountstyle"]), "showhotgoods" => intval($_GPC["showhotgoods"])), "loading" => $_GPC["loading"]);
        set_plugin_config("diypage.diyTheme", $data);
        imessage(error(0, "设置成功"), referer(), "ajax");
    }
    $diycolor = get_plugin_config("diypage.diyTheme");
    if (isset($diycolor["loading"]["img"])) {
        $diycolor["loading"]["img"] = tomedia($diycolor["loading"]["img"]);
    }
}
include itemplate("vue/diyColor");

?>