<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "跑腿设置";
    $config_errander = get_agent_plugin_config("errander");
    if ($_W["ispost"]) {
        $errander = array("status" => intval($_GPC["status"]), "map" => array("location_x" => trim($_GPC["map"]["lat"]), "location_y" => trim($_GPC["map"]["lng"])), "city" => trim($_GPC["city"]), "serve_radius" => floatval($_GPC["serve_radius"]), "mobile" => trim($_GPC["mobile"]), "dispatch_mode" => intval($_GPC["dispatch_mode"]), "can_collect_order" => intval($_GPC["can_collect_order"]), "deliveryer_fee_type" => intval($_GPC["deliveryer_fee_type"]), "deliveryer_collect_max" => intval($_GPC["deliveryer_collect_max"]), "over_collect_max_notify" => intval($_GPC["over_collect_max_notify"]), "deliveryer_transfer_status" => intval($_GPC["deliveryer_transfer_status"]), "deliveryer_transfer_max" => intval($_GPC["deliveryer_transfer_max"]), "deliveryer_transfer_reason" => explode("\n", trim($_GPC["deliveryer_transfer_reason"])), "deliveryer_cancel_reason" => explode("\n", trim($_GPC["deliveryer_cancel_reason"])));
        $order["deliveryer_transfer_reason"] = array_filter($order["deliveryer_transfer_reason"], trim);
        $order["deliveryer_cancel_reason"] = array_filter($order["deliveryer_cencel_reason"], trim);
        $errander["deliveryer_fee"] = $errander["deliveryer_fee_type"] == 1 ? trim($_GPC["deliveryer_fee_1"]) : trim($_GPC["deliveryer_fee_2"]);
        $errander = array_merge($config_errander, $errander);
        set_agent_plugin_config("errander", $errander);
        imessage(error(0, "设置跑腿服务参数成功"), "refresh", "ajax");
    }
    $config_errander["map"]["lat"] = $config_errander["map"]["location_x"];
    $config_errander["map"]["lng"] = $config_errander["map"]["location_y"];
    if (!empty($config_errander["deliveryer_transfer_reason"])) {
        $config_errander["deliveryer_transfer_reason"] = implode("\n", $config_errander["deliveryer_transfer_reason"]);
    }
    if (!empty($config_errander["deliveryer_cancel_reason"])) {
        $config_errander["deliveryer_cancel_reason"] = implode("\n", $config_errander["deliveryer_cancel_reason"]);
    }
    $agreement_errander = get_config_text("agreement_errander");
}
include itemplate("config");

?>