<?php

/**
 * 本破解程序由资源邦提供
 * 资源邦www.wazyb.com
 * QQ:993424780  承接网站建设、公众号搭建、小程序建设、企业网站
 */
defined("IN_IA") or exit("Access Denied");

function mload()
{
    static $mloader = NULL;
    if (empty($mloader)) {
        $mloader = new Mloader();
    }
    return $mloader;
}
function pload()
{
    static $ploader = NULL;
    if (empty($ploader)) {
        $ploader = new Ploader();
    }
    return $ploader;
}
function check_plugin_perm($name)
{
    global $_W;
    static $_plugins = array();
    if (isset($_plugins[$name])) {
        return $_plugins[$name];
    }
    $dir = WE7_WMALL_PLUGIN_PATH . $name . "/inc";
    if (!is_dir($dir)) {
        $_plugins[$name] = false;
        return $_plugins[$name];
    }
    $plugin = pdo_get("tiny_wmall_plugin", array("name" => $name), array("id", "name"));
    if (empty($plugin)) {
        $_plugins[$name] = false;
        return $_plugins[$name];
    }
    mload()->model("common");
    $perms = get_account_perm();
    if (empty($perms) || in_array($name, $perms["plugins"])) {
        $_plugins[$name] = true;
    } else {
        $_plugins[$name] = false;
    }
    return $_plugins[$name];
}
function check_plugin_exist($name)
{
    global $_W;
    static $_plugins_exist = array();
    if (isset($_plugins_exist[$name])) {
        return $_plugins_exist[$name];
    }
    if (!empty($_W["_plugins"])) {
        $_plugins_exist[$name] = false;
        if (in_array($name, array_keys($_W["_plugins"]))) {
            $_plugins_exist[$name] = true;
        }
    } else {
        $plugin = pdo_get("tiny_wmall_plugin", array("name" => $name), array("id", "name"));
        if (empty($plugin)) {
            $_plugins_exist[$name] = false;
            return $_plugins_exist[$name];
        }
        $_plugins_exist[$name] = true;
    }
    return $_plugins_exist[$name];
}
function fans_info_query($openid)
{
    global $_W;
    load()->func("communication");
    static $account_api = NULL;
    if (empty($account_api)) {
        $account_api = WeAccount::create();
    }
    $fan = $account_api->fansQueryInfo($openid, true);
    if (!is_error($fan) && $fan["subscribe"] == 1) {
        $fan["nickname"] = stripcslashes($fan["nickname"]);
        $fan["remark"] = !empty($fan["remark"]) ? stripslashes($fan["remark"]) : "";
    } else {
        $fan = array();
    }
    return $fan;
}

?>
