<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("plateform");
$_W["we7_wmall"]["config"]["app"]["plateform"] = get_plugin_config("plateformApp");
if ($_W["_action"] != "auth") {
    icheckplateformer();
    $plateformer = $_W["plateformer"];
}
if (!empty($_GPC["filter"])) {
    $_GPC["filter"] = json_decode(htmlspecialchars_decode($_GPC["filter"]), true);
    foreach ($_GPC["filter"] as $key => $val) {
        $_GPC[$key] = $val;
    }
}
$config_takeout = $_W["we7_wmall"]["config"]["takeout"];
$config_delivery = $_W["we7_wmall"]["config"]["delivery"];

?>