<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
if (empty($_W["deliveryer"]["perm_takeout"]) && empty($_W["deliveryer"]["is_errander"])) {
    imessage("您没有配送权限，请联系管理员授权", imurl("wmall/member/mine"), "error");
}
$url = "delivery/order/takeout/list";
if (empty($_W["deliveryer"]["perm_takeout"])) {
    $url = "delivery/order/errander/list";
}
header("location:" . imurl($url));
exit;

?>