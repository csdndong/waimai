<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "basic";
if ($op == "basic") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $mall = array("title" => trim($_GPC["title"]), "logo" => trim($_GPC["logo"]), "mobile" => trim($_GPC["mobile"]), "version" => intval($_GPC["version"]), "is_to_nearest_store" => intval($_GPC["is_to_nearest_store"]), "default_sid" => intval($_GPC["default_sid"]), "template_mobile" => trim($_GPC["template"]) ? trim($_GPC["template"]) : "default", "store_orderby_type" => trim($_GPC["store_orderby_type"]), "store_overradius_display" => intval($_GPC["store_overradius_display"]), "delivery_title" => trim($_GPC["delivery_title"]), "lazyload_store" => trim($_GPC["lazyload_store"]), "lazyload_goods" => trim($_GPC["lazyload_goods"]), "copyright" => htmlspecialchars_decode($_GPC["copyright"]), "seniverse" => htmlspecialchars_decode($_GPC["seniverse"]), "meiqia" => htmlspecialchars_decode(str_replace(array("&#039;"), array("&quot;"), $_GPC["meiqia"])), "store_use_child_category" => intval($_GPC["store_use_child_category"]));
        set_system_config("mall", $mall);
        $manager = $_GPC["manager"];
        set_system_config("manager", $manager);
        imessage(error(0, "基础设置成功"), referer(), "ajax");
    }
    $config = $_config["mall"];
    $config["manager"] = $_config["manager"];
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]));
    $templates = array();
    $dir = IA_ROOT . "/addons/we7_wmall/template/mobile/wmall";
    if ($handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false) {
            if ($file != ".." && $file != "." && is_dir($dir . "/" . $file)) {
                $templates[] = $file;
            }
        }
        closedir($handle);
    }
    include itemplate("config/basic");
}
if ($op == "follow") {
    $_W["page"]["title"] = "分享及关注";
    if ($_W["ispost"]) {
        $share = array("title" => trim($_GPC["title"]), "imgUrl" => trim($_GPC["imgUrl"]), "desc" => trim($_GPC["desc"]), "link" => trim($_GPC["link"]));
        set_system_config("share", $share);
        $follow = array("guide_status" => intval($_GPC["guide_status"]), "link" => trim($_GPC["followurl"]), "qrcode" => trim($_GPC["qrcode"]));
        set_system_config("follow", $follow);
        imessage(error(0, "分享关注设置成功"), referer(), "ajax");
    }
    $share = $_config["share"];
    $follow = $_config["follow"];
    include itemplate("config/follow");
}
if ($op == "close") {
    $_W["page"]["title"] = "平台状态";
    if ($_W["ispost"]) {
        $close = array("status" => intval($_GPC["status"]) ? intval($_GPC["status"]) : 1, "url" => trim($_GPC["url"]), "tips" => trim($_GPC["tips"]));
        set_system_config("close", $close);
        imessage(error(0, "平台状态设置成功"), referer(), "ajax");
    }
    $close = $_config["close"];
    include itemplate("config/close");
}
if ($op == "oauth") {
    $_W["page"]["title"] = "公众平台oAuth设置";
    if ($_W["ispost"]) {
        $oauth_host = trim($_GPC["oauth_host"]);
        if (!empty($oauth_host) && !strexists($oauth_host, "https://") && !strexists($oauth_host, "http://")) {
            imessage(error(-1, "域名必须以https://或者https//开头"), "", "ajax");
        }
        $oauth = array("oauth_host" => trim($_GPC["oauth_host"]));
        set_system_config("oauth", $oauth);
        imessage(error(0, "平台状态设置成功"), referer(), "ajax");
    }
    $oauth = $_config["oauth"];
    include itemplate("config/oauth");
}

?>