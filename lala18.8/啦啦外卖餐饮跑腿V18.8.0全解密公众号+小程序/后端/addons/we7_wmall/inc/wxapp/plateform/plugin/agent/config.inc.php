<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "order";
$_config = get_agent_system_config("", $_W["agentid"]);
if ($ta == "order") {
    if ($_W["ispost"]) {
        $_GPC["clerk"] = $_GPC["notify_rule_clerk"];
        $_GPC["deliveryer"] = $_GPC["notify_rule_deliveryer"];
        $order = array("notify_rule_clerk" => array("notify_delay" => intval($_GPC["clerk"]["notify_delay"]), "notify_frequency" => intval($_GPC["clerk"]["notify_frequency"]), "notify_total" => intval($_GPC["clerk"]["notify_total"]), "notify_phonecall_time" => intval($_GPC["clerk"]["notify_phonecall_time"])), "notify_rule_deliveryer" => array("notify_delay" => intval($_GPC["deliveryer"]["notify_delay"]), "notify_frequency" => intval($_GPC["deliveryer"]["notify_frequency"]), "notify_total" => intval($_GPC["deliveryer"]["notify_total"]), "notify_phonecall_time" => intval($_GPC["deliveryer"]["notify_phonecall_time"])), "show_no_pay" => intval($_GPC["show_no_pay"]), "auto_refresh" => intval($_GPC["auto_refresh"]), "deliveryer_collect_notify_clerk" => intval($_GPC["deliveryer_collect_notify_clerk"]), "timeout_limit" => intval($_GPC["timeout_limit"]), "delivery_timeout_limit" => intval($_GPC["delivery_timeout_limit"]), "delivery_before_limit" => intval($_GPC["delivery_before_limit"]), "dispatch_mode" => intval($_GPC["dispatch_mode"]), "can_collect_order" => intval($_GPC["can_collect_order"]), "deliveryer_collect_max" => intval($_GPC["deliveryer_collect_max"]), "over_collect_max_notify" => intval($_GPC["over_collect_max_notify"]), "deliveryer_transfer_status" => intval($_GPC["deliveryer_transfer_status"]), "deliveryer_transfer_max" => intval($_GPC["deliveryer_transfer_max"]), "deliveryer_transfer_reason" => explode("\n", trim($_GPC["deliveryer_transfer_reason"])), "deliveryer_cancel_reason" => explode("\n", trim($_GPC["deliveryer_cancel_reason"])), "dispatch_sort" => trim($_GPC["dispatch_sort"]), "max_dispatching" => intval($_GPC["max_dispatching"]), "show_acceptaddress_when_firstdelivery" => intval($_GPC["show_acceptaddress_when_firstdelivery"]));
        $order["deliveryer_transfer_reason"] = array_filter($order["deliveryer_transfer_reason"], trim);
        $order["deliveryer_cancel_reason"] = array_filter($order["deliveryer_cancel_reason"], trim);
        set_agent_system_config("takeout.order", $order, $_W["agentid"]);
        imessage(error(0, ""), "", "ajax");
    }
    $order = $_config["takeout"]["order"];
    if (!empty($order["deliveryer_transfer_reason"])) {
        $order["deliveryer_transfer_reason"] = implode("\n", $order["deliveryer_transfer_reason"]);
    }
    if (!empty($order["deliveryer_cancel_reason"])) {
        $order["deliveryer_cancel_reason"] = implode("\n", $order["deliveryer_cancel_reason"]);
    }
    $result = array("order" => $order);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "mall") {
        if ($_W["ispost"]) {
            $mall = array("version" => 1, "store_orderby_type" => trim($_GPC["store_orderby_type"]), "store_overradius_display" => intval($_GPC["store_overradius_display"]));
            set_agent_system_config("mall", $mall, $_W["agentid"]);
            imessage(error(0, ""), "", "ajax");
        }
        $mall = $_config["mall"];
        $result = array("mall" => $mall);
        imessage(error(0, $result), "", "ajax");
    }
}

?>