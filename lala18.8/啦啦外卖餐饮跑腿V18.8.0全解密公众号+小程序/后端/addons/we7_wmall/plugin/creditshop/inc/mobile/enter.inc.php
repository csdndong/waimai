<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
pload()->func("duiba");
icheckauth();
$url = buildCreditAutoLoginRequest($_config_plugin["appkey"], $_config_plugin["appsecret"], $_W["member"]["uid"], intval($_W["member"]["credit1"]));
header("location:" . $url);
exit;

?>