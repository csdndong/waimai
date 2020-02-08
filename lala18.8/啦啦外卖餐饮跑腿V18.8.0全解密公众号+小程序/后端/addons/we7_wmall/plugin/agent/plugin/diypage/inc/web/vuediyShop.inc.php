<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "页面选择";
    mload()->model("diy");
    $pages = array("home" => array("name" => "平台首页", "url" => "pages/home/index", "key" => "home", "save_key" => "use_diy_home", "pages" => get_wxapp_pages(array("type" => 1, "from" => "wechat"), array("id", "name"))));
    if (check_plugin_perm("gohome")) {
        $pages["gohome"] = array("name" => "生活圈首页", "url" => "gohome/pages/home/index", "key" => "gohome", "save_key" => "use_diy_gohome", "pages" => get_wxapp_pages(array("type" => 4, "from" => "wechat"), array("id", "name")));
        $pages["tongcheng"] = array("name" => "同城首页", "url" => "gohome/pages/tongcheng/index", "key" => "tongcheng", "save_key" => "use_diy_tongcheng", "pages" => get_wxapp_pages(array("type" => 4, "from" => "wechat"), array("id", "name")));
        $pages["haodian"] = array("name" => "好店", "url" => "gohome/pages/haodian/index", "key" => "haodian", "save_key" => "use_diy_haodian", "pages" => get_wxapp_pages(array("type" => 5, "from" => "wechat"), array("id", "name")));
    }
    if ($_W["ispost"]) {
        $setting_vue = array("use_diy_home" => intval($_GPC["vue_use_diy_home"]), "use_diy_gohome" => intval($_GPC["vue_use_diy_gohome"]), "use_diy_tongcheng" => intval($_GPC["vue_use_diy_tongcheng"]), "use_diy_haodian" => intval($_GPC["vue_use_diy_haodian"]), "shopPage" => array_map("intval", $_GPC["vue_shopPages"]));
        set_agent_plugin_config("diypage.diy", $setting_vue);
        $setting_wxapp = array("use_diy_home" => intval($_GPC["wxapp_use_diy_home"]), "use_diy_gohome" => intval($_GPC["wxapp_use_diy_gohome"]), "use_diy_tongcheng" => intval($_GPC["wxapp_use_diy_tongcheng"]), "use_diy_haodian" => intval($_GPC["wxapp_use_diy_haodian"]), "shopPage" => array_map("intval", $_GPC["wxapp_shopPages"]));
        set_agent_plugin_config("wxapp.diy", $setting_wxapp);
        imessage(error(0, "编辑成功"), referer(), "ajax");
    }
    $config_diy_vue = get_agent_plugin_config("diypage.diy");
    $config_diy_wxapp = get_agent_plugin_config("wxapp.diy");
}
include itemplate("vue/diyShop");

?>