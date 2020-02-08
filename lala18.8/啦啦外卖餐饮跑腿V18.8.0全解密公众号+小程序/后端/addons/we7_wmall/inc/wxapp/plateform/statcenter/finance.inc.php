<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    mload()->model("statistics");
    $member = statistics_member();
    $store = statistics_store();
    $deliveryer = statistics_deliveryer();
    $result = array("finance" => array("member" => $member, "store" => $store, "deliveryer" => $deliveryer));
    imessage(error(0, $result), "", "ajax");
}

?>