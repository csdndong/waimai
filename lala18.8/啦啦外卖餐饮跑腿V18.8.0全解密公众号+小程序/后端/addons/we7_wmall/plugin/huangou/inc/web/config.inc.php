<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "basic";
if ($op == "basic") {
    $_W["page"]["title"] = "换购设置";
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1), array("id", "title"));
    if ($_W["ispost"]) {
        $huangou = array("status" => intval($_GPC["status"]));
        $extra_sync = intval($_GPC["extra_sync"]);
        if ($extra_sync == 1) {
            foreach ($stores as $val) {
                store_set_data($val["id"], "huangou", $huangou);
            }
        } else {
            if ($extra_sync == 2) {
                $store_ids = $_GPC["store_ids"];
                foreach ($store_ids as $storeid) {
                    store_set_data($storeid, "huangou", $huangou);
                }
            }
        }
        set_plugin_config("huangou", $huangou);
        imessage(error(0, "设置成功"), referer(), "ajax");
    }
    $config = $_config_plugin;
}
include itemplate("config");

?>
