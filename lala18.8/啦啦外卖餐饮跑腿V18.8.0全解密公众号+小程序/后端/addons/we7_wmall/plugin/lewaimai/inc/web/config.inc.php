<?php
/*
 * @ 买卖跑腿系统
 * @ APP公众号小程序版
 * @ PHP开源站，遵从PHP开源精神
 * @ 源码仅供学习研究，禁止商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "基础设置";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    if ($_W["ispost"]) {
        $admin_id = trim($_GPC["admin_id"]);
        if (empty($admin_id)) {
            imessage(error(-1, "admin_id不能为空"), "", "ajax");
        }
        $divpage_id = trim($_GPC["divpage_id"]);
        if (empty($divpage_id)) {
            imessage(error(-1, "divpage_id不能为空"), "", "ajax");
        }
        $data = array("admin_id" => $admin_id, "divpage_id" => $divpage_id, "lwm_appid" => "dh129ahsd9898123gjhjfamnxoo1", "latitude" => trim($_GPC["latitude"]), "longitude" => trim($_GPC["longitude"]));
        set_plugin_config("lewaimai.config", $data);
        imessage(error(0, "参数保存成功"), iurl("lewaimai/config/index"), "ajax");
    }
    $config = get_plugin_config("lewaimai.config");
}
if ($op == "afresh") {
    cache_clean("lewaimai");
    $table = array("tiny_wmall_goods", "tiny_wmall_goods_category", "tiny_wmall_store", "tiny_wmall_store_account", "tiny_wmall_store_category", "tiny_wmall_lewaimai_log");
    foreach ($table as $value) {
        pdo_delete($value, array("uniacid" => $_W["uniacid"]));
    }
    imessage("数据清空成功,即将重新采集,请勿关闭浏览器", iurl("lewaimai/batch/batch"), "success");
}
include itemplate("config");

?>