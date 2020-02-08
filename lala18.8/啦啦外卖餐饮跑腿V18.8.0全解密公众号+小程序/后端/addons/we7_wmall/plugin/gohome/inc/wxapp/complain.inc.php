<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]);
if ($op == "post") {
    $type = trim($_GPC["type"]);
    $link = trim($_GPC["link"]);
    $update = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "type" => $type, "link" => $link, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_complain", $update);
    imessage(error(0, ""), "", "ajax");
}

?>