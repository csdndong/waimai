<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "basic";
if ($ta == "basic") {
    $config_errander = get_plugin_config("errander");
    if ($_W["ispost"]) {
        $errander = array("status" => intval($_GPC["status"]), "close_reason" => trim($_GPC["close_reason"]), "map" => array("location_x" => trim($_GPC["map"]["location_x"]), "location_y" => trim($_GPC["map"]["location_y"])), "city" => trim($_GPC["city"]), "serve_radius" => floatval($_GPC["serve_radius"]), "mobile" => trim($_GPC["mobile"]), "pay_time_limit" => intval($_GPC["pay_time_limit"]), "handle_time_limit" => intval($_GPC["handle_time_limit"]), "auto_success_hours" => intval($_GPC["auto_success_hours"]), "delivery_before_limit" => intval($_GPC["delivery_before_limit"]), "delivery_timeout_limit" => intval($_GPC["delivery_timeout_limit"]), "auto_refresh" => intval($_GPC["auto_refresh"]), "verification_code" => intval($_GPC["verification_code"]), "dispatch_mode" => intval($_GPC["dispatch_mode"]), "can_collect_order" => intval($_GPC["can_collect_order"]), "deliveryer_fee_type" => intval($_GPC["deliveryer_fee_type"]), "deliveryer_collect_max" => intval($_GPC["deliveryer_collect_max"]), "over_collect_max_notify" => intval($_GPC["over_collect_max_notify"]), "deliveryer_transfer_status" => intval($_GPC["deliveryer_transfer_status"]), "deliveryer_transfer_max" => intval($_GPC["deliveryer_transfer_max"]), "deliveryer_transfer_reason" => $_GPC["deliveryer_transfer_reason"], "deliveryer_cancel_reason" => $_GPC["deliveryer_cancel_reason"]);
        $errander["deliveryer_transfer_reason"] = array_filter($errander["deliveryer_transfer_reason"], trim);
        $errander["deliveryer_cancel_reason"] = array_filter($errander["deliveryer_cancel_reason"], trim);
        if ($errander["deliveryer_fee_type"] == 1) {
            $errander["deliveryer_fee"] = trim($_GPC["deliveryer_fee"]);
        } else {
            if ($errander["deliveryer_fee_type"] == 2) {
                $errander["deliveryer_fee"] = trim($_GPC["deliveryer_fee"]);
            } else {
                if ($errander["deliveryer_fee_type"] == 3) {
                    $errander["deliveryer_fee"] = array("start_fee" => floatval($_GPC["deliveryer_fee"]["start_fee"]), "start_km" => floatval($_GPC["deliveryer_fee"]["start_km"]), "pre_km" => floatval($_GPC["deliveryer_fee"]["pre_km"]), "max_fee" => floatval($_GPC["deliveryer_fee"]["max_fee"]));
                }
            }
        }
        $errander["anonymous"] = array();
        if (!empty($_GPC["anonymous"])) {
            foreach ($_GPC["anonymous"] as $anonymous) {
                if (empty($anonymous)) {
                    continue;
                }
                $errander["anonymous"][] = $anonymous;
            }
        }
        if (0 < $_W["agentid"]) {
            set_agent_plugin_config("errander", $errander);
        } else {
            set_plugin_config(base64_decode("ZXJyYW5kZXI="), $errander);
        }
        imessage(error(0, ""), "", "ajax");
    }
    $result = array("mall" => $config_errander);
    imessage(error(0, $result), "", "ajax");
}

?>