<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "商户登录";
isetcookie("__we7_wmall_store", "", 0);
if ($_W["role"] == "merchanter") {
    isetcookie("__uniacid", "", 0);
    isetcookie("__sid", "", 0);
}
header("location:" . iurl("store/oauth/login"));
exit;

?>