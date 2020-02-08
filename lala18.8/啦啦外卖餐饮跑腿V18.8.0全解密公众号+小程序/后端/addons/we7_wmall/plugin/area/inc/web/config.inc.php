<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    $config = $_W["we7_wmall"]["config"]["mall"];
    if ($_W["ispost"]) {
        $config["address_type"] = intval($_GPC["address_type"]);
        set_system_config("mall", $config);
        imessage(error(0, "配送地址基础设置设置成功"), "refresh", "ajax");
    }
}
include itemplate("config");

?>
