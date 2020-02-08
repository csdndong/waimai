<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "basic";
if ($op == "basic") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $mall = array("version" => 1, "store_orderby_type" => trim($_GPC["store_orderby_type"]), "store_overradius_display" => intval($_GPC["store_overradius_display"]));
        set_agent_system_config("mall", $mall);
        $manager = $_GPC["manager"];
        set_agent_system_config("manager", $manager);
        imessage(error(0, "基础设置成功"), referer(), "ajax");
    }
    $_config = get_agent_system_config();
    $config = $_config["mall"];
    $config["manager"] = $_config["manager"];
    include itemplate("config/basic");
} else {
    if ($op == "close") {
        $_W["page"]["title"] = "代理状态";
        if ($_W["ispost"]) {
            $close = array("status" => intval($_GPC["status"]) ? intval($_GPC["status"]) : 1, "url" => trim($_GPC["url"]), "tips" => trim($_GPC["tips"]));
            set_agent_system_config("close", $close);
            imessage(error(0, "平台状态设置成功"), referer(), "ajax");
        }
        $close = get_agent_system_config("close");
        include itemplate("config/close");
    }
}

?>