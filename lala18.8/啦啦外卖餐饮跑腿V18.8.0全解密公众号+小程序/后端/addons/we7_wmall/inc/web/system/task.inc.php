<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "计划任务";
$url = (string) $_W["siteroot"] . "app/api.php?&c=entry&ctrl=system&ac=common&op=task&do=mobile&m=we7_wmall&no_i=1";
$url_business = (string) $_W["siteroot"] . "app/api.php?&c=entry&ctrl=system&ac=common&op=itask&do=mobile&m=we7_wmall&no_i=1";
include itemplate("system/task");

?>