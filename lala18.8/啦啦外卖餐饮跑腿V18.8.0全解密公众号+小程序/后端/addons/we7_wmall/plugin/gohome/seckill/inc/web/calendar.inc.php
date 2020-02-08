<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "config";
if ($op == "config") {
    $_W["page"]["title"] = "任务设置";
}
include itemplate("calendar");

?>