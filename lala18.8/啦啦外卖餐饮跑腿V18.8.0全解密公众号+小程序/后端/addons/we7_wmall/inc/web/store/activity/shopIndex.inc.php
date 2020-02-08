<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "店铺活动类型";
    $activitys = activity_getall($sid, 1);
    $stats = activity_stat();
    $perm = $_W["we7_wmall"]["config"]["store"]["activity"]["perm"];
}
include itemplate("store/activity/index");

?>