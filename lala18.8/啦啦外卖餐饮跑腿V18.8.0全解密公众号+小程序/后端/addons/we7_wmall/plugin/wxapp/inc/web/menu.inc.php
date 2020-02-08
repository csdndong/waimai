<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "post";
if ($op == "post") {
    $_W["page"]["title"] = "编辑导航";
    $router = array("we7_wmall_deliveryer" => "wxapp.deliveryer.menu", "we7_wmall_manager" => "wxapp.manager.menu");
    $type = trim($_GPC["type"]);
    $jsType = "wmall";
    if ($type == "we7_wmall_deliveryer") {
        $jsType = "deliveryer";
    } else {
        if ($type == "we7_wmall_manager") {
            $jsType = "manager";
        }
    }
    if ($_W["ispost"]) {
        $data = $_GPC["menu"];
        $menuType = intval($data["type"]);
        if (empty($menuType)) {
            $urls = wxapp_urls();
            $package = array();
            if (!empty($urls)) {
                foreach ($urls as $value) {
                    if (!empty($value)) {
                        foreach ($value as $items) {
                            if (!empty($items["items"])) {
                                foreach ($items["items"] as $item) {
                                    if (strexists($item["url"], "package") || strexists($item["url"], "gohome") || strexists($item["url"], "plugin")) {
                                        $package[$item["url"]] = array("errMsg" => $item["title"]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $keys = array_keys($package);
            if (!empty($data["data"])) {
                foreach ($data["data"] as $value) {
                    if (strexists($value["pagePath"], "?")) {
                    imessage(error(-1, "底部导航链接不能设置参数"), iurl("wxapp/menu/post"), "ajax");
                    }
                    if (strexists($value["pagePath"], "=")) {
                    imessage(error(-1, "底部导航链接不能设置参数"), iurl("wxapp/menu/post"), "ajax");
                    }
                    if (strexists($value["pagePath"], "pages/store/goods")) {
                        imessage(error(-1, "门店点餐页不能被设置为小程序的底部导航"), iurl("wxapp/menu/post"), "ajax");
                    }
                    if (in_array($value["pagePath"], $keys)) {
                    $errMsg = $package[$value["pagePath"]]["errMsg"] . "不能被设置为小程序的底部导航";
                        imessage(error(-1, $errMsg), "", "ajax");
                    }
                }
            }
        }
        $data = base64_encode(json_encode($data));
        if (!empty($type)) {
            set_plugin_config($router[$type], $data);
        } else {
            set_plugin_config("wxapp.menu", $data);
        }
        imessage(error(0, "保存成功"), referer(), "ajax");
    }
    if (!empty($_GPC["type"])) {
        $menu = get_plugin_config($router[$_GPC["type"]]);
    } else {
        $menu = get_plugin_config("wxapp.menu");
    }
    $menu = json_decode(base64_decode($menu), true);
}
include itemplate("menu");

?>
