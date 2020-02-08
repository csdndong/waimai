<?php 
defined("IN_IA") or exit("Access Denied");
mload()->model("page");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth(false);
if ($op == "index") {
    if ($_W["ochannel"] == "wxapp" && $_W["is_agent"] && $_W["agentid"] == -1) {
        imessage(error(-2, "您所在的区域暂未获取到同城信信，建议您手动搜索地址或切换到此前常用的地址再试"), "", "ajax");
    }
    mload()->model("diy");
    if ($_config_wxapp["diy"]["use_diy_gohome"] != 1) {
        $pageOrid = get_wxapp_defaultpage("gohome");
        $config_share = $_config_plugin["share"];
        $share = array("title" => $config_share["title"], "desc" => $config_share["detail"], "link" => empty($config_share["link"]) ? ivurl("gohome/pages/home/index", array(), true) : $config_share["link"], "imgUrl" => tomedia($config_share["thumb"]));
    } else {
        $pageOrid = $_config_wxapp["diy"]["shopPage"]["gohome"];
        if (empty($pageOrid)) {
            imessage(error(-1, "未设置生活圈DIY页面"), "", "ajax");
        }
    }
    $page = get_wxapp_diy($pageOrid, true);
    if (empty($page)) {
        imessage(error(-1, "页面不能为空"), "", "ajax");
    }
    $_W["_share"] = array("title" => $page["data"]["page"]["title"], "desc" => $page["data"]["page"]["desc"], "link" => ivurl("gohome/pages/home/index", array(), true), "imgUrl" => tomedia($page["data"]["page"]["thumb"]));
    if ($_config_wxapp["diy"]["use_diy_tongcheng"] != 1) {
        $_W["_share"] = $share;
    }
    $default_location = array();
    if (empty($_GPC["lat"]) || empty($_GPC["lng"])) {
        $config_takeout = $_W["we7_wmall"]["config"]["takeout"]["range"];
        if (!empty($config_takeout["map"]["location_x"]) && !empty($config_takeout["map"]["location_y"])) {
            $_GPC["lat"] = $config_takeout["map"]["location_x"];
            $_GPC["lng"] = $config_takeout["map"]["location_y"];
            $default_location = array("location_x" => $config_takeout["map"]["location_x"], "location_y" => $config_takeout["map"]["location_y"], "address" => $config_takeout["city"]);
        }
    }
    if (empty($result)) {
        $result = array("diy" => $page, "config" => $_W["we7_wmall"]["config"]["mall"], "cart_sum" => $page["is_show_cart"] == 1 ? get_member_cartnum() : 0);
        $result["config"]["default_location"] = $default_location;
    }
    $_W["_nav"] = 1;
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "information") {
        $filter = $_GPC;
        $filter["status"] = 3;
        pload()->model("tongcheng");
        $informations = tongcheng_get_informations($filter);
        $result = array("informations" => $informations["informations"]);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($op == "cart") {
            $result = array("cart_sum" => get_member_cartnum());
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>
