<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$sid = intval($_GPC["sid"]);
$status = isetcookie("__mg_sid", $sid, 86400 * 7);
file_put_contents(MODULE_ROOT . "/aa.txt", var_export($status, 1));
header("location: " . imurl("manage/shop/index", array("sid" => $sid)));
exit;

?>