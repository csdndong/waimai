<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "数据初始化";
    set_time_limit(0);
    if ($_W["ispost"]) {
        $tables = array("tiny_wmall_activity_bargain", "tiny_wmall_activity_bargain_goods", "tiny_wmall_cube", "tiny_wmall_deliveryer", "tiny_wmall_deliveryer_current_log", "tiny_wmall_deliveryer_getcash_log", "tiny_wmall_errander_category", "tiny_wmall_errander_order", "tiny_wmall_news", "tiny_wmall_news_category", "tiny_wmall_notice", "tiny_wmall_order", "tiny_wmall_order_comment", "tiny_wmall_order_stat", "tiny_wmall_paybill_order", "tiny_wmall_paylog", "tiny_wmall_report", "tiny_wmall_slide", "tiny_wmall_store", "tiny_wmall_store_account", "tiny_wmall_store_category", "tiny_wmall_store_current_log", "tiny_wmall_store_deliveryer", "tiny_wmall_store_getcash_log", "tiny_wmall_store_activity", "tiny_wmall_text");
        if (check_plugin_perm(base64_decode("Z29ob21l"))) {
            $tables_gohome = array("tiny_wmall_tongcheng_category", "tiny_wmall_tongcheng_comment", "tiny_wmall_tongcheng_information", "tiny_wmall_tongcheng_order", "tiny_wmall_haodian_category", "tiny_wmall_haodian_order", "tiny_wmall_seckill_goods", "tiny_wmall_seckill_goods_category", "tiny_wmall_pintuan_category", "tiny_wmall_pintuan_goods", "tiny_wmall_kanjia", "tiny_wmall_kanjia_category", "tiny_wmall_kanjia_helprecord", "tiny_wmall_kanjia_userlist", "tiny_wmall_gohome_order", "tiny_wmall_gohome_slide", "tiny_wmall_gohome_notice", "tiny_wmall_gohome_comment", "tiny_wmall_gohome_category");
            $tables = array_merge($tables, $tables_gohome);
        }
        $agentid = isset($_GPC["agentid"]) ? intval($_GPC["agentid"]) : 0;
        foreach ($tables as $table) {
            if (pdo_fieldexists($table, "agentid")) {
                pdo_update($table, array("agentid" => $agentid), array("uniacid" => $_W["uniacid"]));
            }
        }
        cache_clean("we7_wmall:deliveryers:");
        imessage(error(0, "数据初始化成功"), "referer", "ajax");
    }
}
include itemplate("initialize");

?>