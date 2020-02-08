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
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $basic = array("status" => intval($_GPC["status"]), "fee_getcash" => $_GPC["fee_getcash"], "setting_meta_title" => trim($_GPC["setting_meta_title"]));
        set_plugin_config("storebd.basic", $basic);
        imessage(error(0, "设置成功"), referer(), "ajax");
    }
    $basic = get_plugin_config("storebd.basic");
} else {
    if ($op == "cover") {
        $_W["page"]["title"] = "店铺推广员入口";
        $urls = array("index" => ivurl("/package/pages/storebd/index", array(), true));
    }
}
include itemplate("config");

?>