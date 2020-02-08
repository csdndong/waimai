<?php

defined("IN_IA") or exit("Access Denied");
mload()->model("page");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$config_mall = $_W["we7_wmall"]["config"]["mall"];
if (defined("IN_WXAPP")) {
    icheckauth();
    $_config_wxapp_basic = $_config_wxapp["basic"];
    $_config_wxapp_basic["new_audit_status"] = 0;
    if ($_W["we7_wxapp"]["config"]["basic"]["release_version"] < $_W["we7_wxapp"]["config"]["basic"]["request_version"] && $_config_wxapp_basic["audit_status"] == 1) {
        $_config_wxapp_basic["new_audit_status"] = 1;
    }
    if ($_config_wxapp_basic["new_audit_status"] == 1) {
        $config_mall["version"] = 2;
        $config_mall["default_sid"] = $_config_wxapp_basic["default_sid"];
    }
    $config_mall["store_url"] = $_config_wxapp_basic["store_url"];
    $_W["we7_wmall"]["config_mall"] = $config_mall;
} else {
    if (!empty($_GPC["code"])) {
        icheckauth();
    } else {
        icheckauth(false);
    }
    if ($config_mall["version"] == 2) {
        $store = store_fetch($config_mall["default_sid"]);
        if (empty($store)) {
            imessage(error(-1, "当前平台模式为单店铺模式,请设置默认门店"), "", "ajax");
        }
        $page = store_forward_url($store["id"], $store["forward_mode"], $store["forward_url"]);
        $url = ivurl($page, array(), true);
        imessage(error(41100, array("url" => $url)), "", "ajax");
    }
    if ($config_mall["is_to_nearest_store"] == 1) {
        $lat = trim($_GPC["lat"]);
        $lng = trim($_GPC["lng"]);
        if (empty($lat) || empty($lng)) {
            imessage(error(-1, "获取位置失败"), "", "ajax");
        }
        $stores = pdo_fetchall("select id,location_x,location_y,serve_radius from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and status = 1 and is_waimai = 1", array(":uniacid" => $_W["uniacid"]));
        if (empty($stores)) {
            imessage(error(-1, "还没有门店哦"), "", "ajax");
        }
        $distance = array();
        if (!empty($lat) && !empty($lng)) {
            foreach ($stores as $key => &$row) {
                $row["distance"] = distanceBetween($row["location_y"], $row["location_x"], $lng, $lat);
                $row["distance"] = round($row["distance"] / 1000, 2);
                $distance[$row["id"]] = $row["distance"];
            }
        }
        $sid = 0;
        $min_distance = min($distance);
        $sid = array_search($min_distance, $distance);
        if (0 < $sid) {
            $store = store_fetch($sid);
            $page = store_forward_url($store["id"], $store["forward_mode"], $store["forward_url"]);
            $url = ivurl($page, array(), true);
            imessage(error(41100, array("url" => $url)), "", "ajax");
        }
    }
}
if ($ta == "index") {
    $config_app_customer = $_W["we7_wmall"]["config"]["app"]["customer"];
    if (is_h5app() && is_ios() && !empty($config_app_customer) && empty($config_app_customer["iosstatus"]) && !empty($config_app_customer["iosurl"])) {
        $result = array("url" => $config_app_customer["iosurl"]);
        imessage(error(41100, $result), "", "ajax");
    }
    mload()->model("plugin");
    if ($_W["ochannel"] == "wxapp" && $config_mall["version"] == 2) {
        $sid = $config_mall["default_sid"];
        if (empty($sid)) {
            imessage(error(-1, "当前平台模式为单店铺模式,请设置默认门店"), "", "ajax");
        }
        if ($_W["we7_wxapp"]["config"]["basic"]["store_url"] == "home") {
            mload()->model("page");
            $homepage = store_page_get($sid);
            if (empty($homepage)) {
                imessage(error(-1, "您使用的是单店版本，并且设置的打开首页直接跳转到“门店自定义页面”，你需要登陆默认店铺后台-装修-门店首页 设置自定义首页后才能显示出来"), "", "ajax");
            }
        }
        imessage(error(0, array("homepage" => $homepage["data"], "store_id" => $sid, "config" => $config_mall)), "", "ajax");
    }
    mload()->model("diy");
    if ($_config_wxapp["diy"]["use_diy_home"] != 1) {
        $pageOrid = get_wxapp_defaultpage();
        $pagetype = "default";
    } else {
        $pageOrid = $_config_wxapp["diy"]["shopPage"]["home"];
        if (empty($pageOrid)) {
            imessage(error(-1, "未设置首页DIY页面"), "", "ajax");
        }
    }
    $page = get_wxapp_diy($pageOrid, true, array("pagetype" => $pagetype, "pagepath" => "home"));
    if (empty($page)) {
        imessage(error(-1, "页面不能为空"), "", "ajax");
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
    $_GPC["cid"] = intval($page["cid"]);
    $result = array("is_use_diy" => 1, "config" => $config_mall, "config_wxapp" => $_config_wxapp, "diy" => $page, "stores" => store_filter(), "cart_sum" => $page["is_show_cart"] == 1 ? get_member_cartnum() : 0, "default_location" => $default_location);
    $result["superRedpacketData"] = array();
    if (check_plugin_perm("superRedpacket")) {
        pload()->model("superRedpacket");
        $result["superRedpacketData"] = superRedpacket_grant_show();
    }
    if (check_plugin_perm("spread")) {
        mload()->model("plugin");
        pload()->model("spread");
        $spread = member_spread_bind();
        if (!is_error($spread)) {
            $spread = error(0, $spread);
        }
        $result["spread"] = $spread;
    }
    $guide = json_decode(base64_decode($_config_wxapp["guide"]), true);
    if (!empty($guide["data"])) {
        foreach ($guide["data"] as &$gvalue) {
            $gvalue["imgUrl"] = tomedia($gvalue["imgUrl"]);
        }
    }
    $result["guide"] = $guide;
    $_W["_share"] = get_mall_share();
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "store") {
        $result = store_filter();
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "spread") {
            if (check_plugin_perm("spread")) {
                mload()->model("plugin");
                pload()->model("spread");
                $spread = member_spread_bind();
                if (!is_error($spread)) {
                    $spread = error(0, $spread);
                }
                imessage($spread, "", "ajax");
                return 1;
            }
        } else {
            if ($ta == "cart") {
                $result = array("cart_sum" => get_member_cartnum());
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>
