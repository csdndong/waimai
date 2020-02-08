<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "财务统计";
    mload()->model("statistics");
    $store = statistics_store();
    $deliveryer = statistics_deliveryer();
    $member = statistics_member();
}
include itemplate("statcenter/finance");

?>