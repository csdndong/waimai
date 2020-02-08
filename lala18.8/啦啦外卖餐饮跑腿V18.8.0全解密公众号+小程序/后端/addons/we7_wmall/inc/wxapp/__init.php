<?php
/*



 * @ 请勿传播
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("common");
mload()->func("wxapp");
mload()->model("member");
mload()->model("store");
mload()->model("order");
$_W["we7_wmall"]["global"] = get_global_config();
if ($_W["we7_wmall"]["global"]["development"] == 1) {
    ini_set("display_errors", "1");
    error_reporting(30719 ^ 8);
}
$_W["is_agent"] = is_agent();
$_W["agentid"] = 0;
if ($_W["is_agent"]) {
    mload()->model("agent");
    if (in_array($_GPC["ctrl"], array("wmall")) && in_array($_GPC["ac"], array("home", "channel")) || in_array($_GPC["ctrl"], array("errander", "bargain", "diypage")) || defined("IN_GOHOME")) {
        if (!empty($_GPC["lat"]) && empty($_GPC["__lat"])) {
            $_GPC["__lat"] = $_GPC["lat"];
        }
        if (!empty($_GPC["lng"]) && empty($_GPC["__lng"])) {
            $_GPC["__lng"] = $_GPC["lng"];
        }
        if ($_GPC["ac"] == "home" && !empty($_GPC["lat"])) {
            $location = array($_GPC["lat"], $_GPC["lng"]);
            $_W["agentid"] = get_location_agent($location[0], $location[1]);
        }
        if ($_W["agentid"] <= 0) {
            $location = array($_GPC["__lat"], $_GPC["__lng"]);
            $_W["agentid"] = get_location_agent($location[0], $location[1]);
        }
        $_W["agent"] = get_agent($_W["agentid"], array("id", "area", "amount", "amount_min", "status"));
    }
}
$_W["we7_wmall"]["config"] = get_system_config();
$_W["we7_wmall"]["config"]["mall"]["lazyload_goods"] = tomedia($_W["we7_wmall"]["config"]["mall"]["lazyload_goods"]);
$_W["we7_wmall"]["config"]["mall"]["lazyload_store"] = tomedia($_W["we7_wmall"]["config"]["mall"]["lazyload_store"]);
$_config_mall = $_W["we7_wmall"]["config"]["mall"];
if (empty($_config_mall["delivery_title"])) {
    $_config_mall["delivery_title"] = "平台专送";
}
$config_close = $_W["we7_wmall"]["config"]["close"];
if ($_W["ochannel"] == "wxapp") {
    $_W["we7_wxapp"]["config"] = get_plugin_config("wxapp");
    if (empty($_W["we7_wxapp"]["config"]["basic"]["release_version"])) {
        $_W["we7_wxapp"]["config"]["basic"]["release_version"] = "8.0";
    }
    $_W["we7_wxapp"]["config"]["basic"]["request_version"] = !empty($_GPC["v"]) ? trim($_GPC["v"]) : "8.0";
} else {
    $_W["we7_wxapp"]["config"] = array("basic" => array("status" => empty($config_close["status"]) ? 1 : 2));
    $_W["we7_wxapp"]["config"]["diy"] = array();
    if (check_plugin_perm("diypage")) {
        $config_diypage = get_plugin_config("diypage");
        $_W["we7_wxapp"]["config"]["diy"] = $config_diypage["diy"];
        if (!is_array($_W["we7_wxapp"]["config"]["diy"])) {
            $_W["we7_wxapp"]["config"]["diy"] = array();
        }
    }
}
$config_wxapp = $_config_wxapp = $_W["we7_wxapp"]["config"];
if ($_W["_controller"] == "wmall") {
    if ($config_close["status"] == 2 || !$config_wxapp["basic"]["status"]) {
    $config_close["tips"] = !empty($config_close["tips"]) ? $config_close["tips"] : "亲,平台休息中。。。";
    imessage(error(-3000, $config_close["tips"]), "close", "ajax");
    }
    if (0 < $_W["agentid"] && $_W["agent"]["amount_min"] != 0 && $_W["agent"]["amount"] < $_W["agent"]["amount_min"]) {
        imessage(error(-3000, "代理异常"), "", "ajax");
    }
}
$_W["role"] = "consumer";
$_W["role_cn"] = "下单顾客";

?>