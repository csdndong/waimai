<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$key = trim($_GPC["key"]);
$agreement = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "name" => $key));
$result = array("agreement" => $agreement["value"], "title" => $agreement["title"]);
imessage(error(0, $result), "", "ajax");

?>