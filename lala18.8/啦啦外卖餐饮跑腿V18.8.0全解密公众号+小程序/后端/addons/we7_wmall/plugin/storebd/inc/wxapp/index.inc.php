<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$bd_id = $_W["storebd_user"]["id"];
if ($op == "index") {
    $condition = " where uniacid = :uniacid and bd_id = :bd_id";
    $params = array(":uniacid" => $_W["uniacid"], ":bd_id" => $bd_id);
    $stores = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_storebd_store") . $condition, $params);
    $getcash = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_storebd_getcash_log") . $condition, $params);
    $current = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_storebd_current_log") . $condition, $params);
    $commission = storebd_user_commission_stat($bd_id);
    $result = array("storebd_user" => $_W["storebd_user"], "commission" => $commission, "stores" => intval($stores), "getcash" => intval($getcash), "current" => intval($current), "config" => array("setting_meta_title" => $_config_plugin["basic"]["setting_meta_title"]));
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "commission") {
        $config_basic = get_plugin_config("storebd.basic");
        $commission = storebd_user_commission_stat($bd_id);
        $result = array("config" => $config_basic, "storebd_user" => $_W["storebd_user"], "commission" => $commission);
        imessage(error(0, $result), "", "ajax");
    }
}

?>