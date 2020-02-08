<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$_W["page"]["title"] = "新建活动";
$sid = intval($_GPC["__mg_sid"]);
$activity = activity_getall($sid, -1);
$perm = $_W["we7_wmall"]["config"]["store"]["activity"]["perm"];
include itemplate("activity/index");

?>