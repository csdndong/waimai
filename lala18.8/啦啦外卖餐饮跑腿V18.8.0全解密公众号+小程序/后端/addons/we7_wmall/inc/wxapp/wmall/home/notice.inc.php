<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$id = intval($_GPC["id"]);
$notice = pdo_get("tiny_wmall_notice", array("id" => $id, "uniacid" => $_W["uniacid"]));
imessage(error(0, $notice), "", "ajax");

?>