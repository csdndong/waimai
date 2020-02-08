<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "basic") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $dataBasic = array("level" => intval($_GPC["level"]), "show_in_mine" => intval($_GPC["show_in_mine"]), "menu_name" => trim($_GPC["menu_name"]), "commission_from" => intval($_GPC["commission_from"]));
        if (check_plugin_perm(base64_decode("ZXJyYW5kZXI="))) {
            $dataBasic["paotui_status"] = intval($_GPC["paotui_status"]);
        }
        if (check_plugin_perm("gohome")) {
            $dataBasic["gohome_status"] = intval($_GPC["gohome_status"]);
        }
        $dataRelate = array("become" => intval($_GPC["become"]), "open_protocol" => intval($_GPC["open_protocol"]), "become_ordercount" => intval($_GPC["become_ordercount"]), "become_moneycount" => intval($_GPC["become_moneycount"]), "become_check" => intval($_GPC["become_check"]), "become_reg" => intval($_GPC["become_reg"]), "group_update_mode" => trim($_GPC["group_update_mode"]), "admin_update_rules" => trim($_GPC["admin_update_rules"]));
        $dataSettle = array("withdraw" => floatval($_GPC["withdraw"]), "withdrawcharge" => floatval($_GPC["withdrawcharge"]), "balance_condition" => intval($_GPC["balance_condition"]));
        $cashcredit = array_filter($_GPC["cashcredit"], trim);
        if (empty($cashcredit)) {
            imessage(error(-1, "至少选择一种提现方式"), "", "ajax");
        }
        $dataSettle["cashcredit"] = $cashcredit;
        $dataTemplate = array("avatar" => trim($_GPC["avatar"]), "spread" => trim($_GPC["spread"]), "shop" => trim($_GPC["shop"]), "myshop" => trim($_GPC["myshop"]));
        $spread_config = array("basic" => $dataBasic, "relate" => $dataRelate, "settle" => $dataSettle, "template" => $dataTemplate, "poster" => $_config_plugin["poster"], "rank" => $_config_plugin["rank"]);
        set_plugin_config("spread", $spread_config);
        $dataProtocol = $_GPC["protocol"];
        set_config_text("推广员申请协议", "spread:agreement", htmlspecialchars_decode($dataProtocol));
        $upgrade_explain = $_GPC["upgrade_explain"];
        set_config_text("推广员升级说明", "spread:upgrade_explain", htmlspecialchars_decode($upgrade_explain));
        imessage(error(0, "推广员基础设置成功"), "refresh", "ajax");
    }
    $basic = $_config_plugin["basic"];
    $relate = $_config_plugin["relate"];
    $settle = $_config_plugin["settle"];
    $template = $_config_plugin["template"];
    $protocol = get_config_text("spread:agreement");
    $upgrade_explain = get_config_text("spread:upgrade_explain");
}
include itemplate("config");

?>