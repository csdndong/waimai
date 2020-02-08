<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
isetcookie("__we7_wmall_agent", false, -1000);
isetcookie("__sid", 0, -1000);
isetcookie("__agent_id", 0, -1000);
header("location:" . iurl("oauth/login"));
exit;

?>