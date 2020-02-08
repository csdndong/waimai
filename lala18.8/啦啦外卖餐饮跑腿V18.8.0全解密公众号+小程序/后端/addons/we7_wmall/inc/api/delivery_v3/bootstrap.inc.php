<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
global $_POST;
mload()->classs("TyAccount");
mload()->func("common");
mload()->model("common");
mload()->model("store");
mload()->model("order");
mload()->model("deliveryer");
mload()->model("deliveryer.extra");
define("IN_DELIVERYAPP", 1);
if (in_array($do, array("login"))) {
    $result = api_check_sign($_POST, $_POST["sign"]);
    if (!$result) {
    }
} else {
    $token = trim($_POST["token"]);
    if (empty($token)) {
        message(ierror(-1, "身份验证失败, 请重新登录"), "", "ajax");
    }
    $deliveryer = deliveryer_fetch($token, "token");
    if (empty($deliveryer)) {
        message(ierror(-1, "身份验证失败, 请重新登录"), "", "ajax");
    }
    if (empty($deliveryer["is_errander"]) && empty($deliveryer["is_takeout"])) {
        message(ierror(-1, "您没有抢单的权限, 请联系平台管理员分配接单权限"), "", "ajax");
    }
    $_W["we7_wmall"]["deliveryer"]["user"] = $deliveryer;
    $_W["deliveryer"] = $_W["we7_wmall"]["deliveryer"]["user"];
    $_W["is_agent"] = is_agent();
    $_W["agentid"] = 0;
    if ($_W["is_agent"]) {
        mload()->model("agent");
        $_W["agentid"] = $_W["deliveryer"]["agentid"];
        if (empty($_W["agentid"])) {
            message(ierror(-1, "未找到配送员所属的代理区域,请先给配送员分配所属的代理"), "", "ajax");
        }
    }
}
$_W["we7_wmall"]["config"] = get_system_config();
$config_takeout = $_W["we7_wmall"]["config"]["takeout"];
$config_delivery = $_W["we7_wmall"]["config"]["delivery"];
$config_errander = get_plugin_config("errander");
$_W["role"] = "deliveryer";
$_W["role_cn"] = "配送员:" . $_W["deliveryer"]["title"];

?>