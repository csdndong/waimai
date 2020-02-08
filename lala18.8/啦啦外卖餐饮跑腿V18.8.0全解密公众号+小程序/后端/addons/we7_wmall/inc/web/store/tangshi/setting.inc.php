<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("table");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "店内点餐设置";
    $pay = get_available_payment();
    if ($_W["ispost"]) {
        $payment = array();
        if (!empty($_GPC["payment"])) {
            $payment = $_GPC["payment"];
        }
        $tangshi = array("payment" => $payment, "table" => array("status_not_update" => intval($_GPC["status_not_update"])), "pindan_status" => intval($_GPC["pindan_status"]));
        store_set_data($sid, "tangshi", $tangshi);
        imessage(error(0, "店内点餐设置成功"), "refresh", "ajax");
    }
    $tangshi = store_get_data($sid, "tangshi");
    include itemplate("store/tangshi/setting");
}

?>