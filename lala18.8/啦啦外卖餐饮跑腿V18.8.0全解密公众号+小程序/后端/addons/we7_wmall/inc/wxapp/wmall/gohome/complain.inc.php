<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $type = trim($_GPC["type"]);
    $link = trim($_GPC["link"]);
    $insert = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "type" => $type, "link" => $link, "status" => 0, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_complain", $insert);
    gohome_complain_notice($insert);
    imessage(error(0, "投诉成功"), "", "ajax");
}

?>