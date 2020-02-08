<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "basic";
if ($ta == "basic") {
    $mall = $_W["we7_wmall"]["config"]["mall"];
    if ($_W["ispost"]) {
        $mall = array("title" => trim($_GPC["title"]), "logo" => trim($_GPC["logo"]), "mobile" => trim($_GPC["mobile"]), "version" => intval($_GPC["version"]), "is_to_nearest_store" => intval($_GPC["is_to_nearest_store"]), "default_sid" => intval($_GPC["default_sid"]), "template_mobile" => trim($_GPC["template"]) ? trim($_GPC["template"]) : "default", "store_orderby_type" => trim($_GPC["store_orderby_type"]), "store_overradius_display" => intval($_GPC["store_overradius_display"]), "delivery_title" => trim($_GPC["delivery_title"]), "lazyload_store" => trim($_GPC["lazyload_store"]), "lazyload_goods" => trim($_GPC["lazyload_goods"]), "copyright" => htmlspecialchars_decode($_GPC["copyright"]), "seniverse" => htmlspecialchars_decode($_GPC["seniverse"]), "meiqia" => htmlspecialchars_decode(str_replace(array("&#039;"), array("&quot;"), $_GPC["meiqia"])));
        set_system_config("mall", $mall);
        imessage(error(0, ""), "", "ajax");
    }
    $result = array("mall" => $mall);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "close") {
        $close = $_W["we7_wmall"]["config"]["close"];
        if ($_W["ispost"]) {
            $close = array("status" => intval($_GPC["status"]), "url" => trim($_GPC["url"]), "tips" => trim($_GPC["tips"]));
            set_system_config("close", $close);
            imessage(error(0, "平台状态设置成功"), referer(), "ajax");
        }
        $result = array("close" => $close);
        imessage(error(0, $result), "", "ajax");
    }
}

?>