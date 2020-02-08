<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
mload()->model("deliveryer.extra");
if ($_W["_action"] != "auth") {
    icheckdeliveryer();
    $_deliveryer = $deliveryer = $_W["deliveryer"];
    $relation = deliveryer_push_token($_W["deliveryer"]);
    $_W["wxapp"]["jpush_relation"] = $relation;
    $_W["is_agent"] = is_agent();
    $_W["agentid"] = 0;
    if ($_W["is_agent"]) {
        $_W["agentid"] = $_W["deliveryer"]["agentid"];
        if (empty($_W["agentid"])) {
            imessage(error(-1, "未找到配送员所属的代理区域,请先给配送员分配所属的代理"), "", "ajax");
        }
        $_W["we7_wmall"]["config"] = get_system_config();
    }
    collect_wxapp_formid();
}
$config_takeout = $_W["we7_wmall"]["config"]["takeout"];
$config_delivery = $_W["we7_wmall"]["config"]["delivery"];
$errander_perm = check_plugin_perm("errander");
if ($errander_perm) {
    $config_errander = get_plugin_config("errander");
}
$_W["role"] = "deliveryer";
$_W["role_cn"] = "配送员:" . $_W["deliveryer"]["title"];
if (!empty($_GPC["filter"])) {
    $_GPC["filter"] = json_decode(htmlspecialchars_decode($_GPC["filter"]), true);
    foreach ($_GPC["filter"] as $key => $val) {
        $_GPC[$key] = $val;
    }
}

?>