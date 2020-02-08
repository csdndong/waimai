<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckAuth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $title = $_GPC["title"];
    if (empty($title)) {
        imessage(error(-1, "发票抬头不能为空"), "", "ajax");
    }
    $recognition = $_GPC["recognition"];
    if (empty($recognition)) {
        imessage(error(-1, "纳税人识别号不能为空"), "", "ajax");
    }
    $insert = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "title" => $title, "recognition" => $recognition, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_member_invoice", $insert);
    $id = pdo_insertid();
    imessage(error(0, $id), "", "ajax");
}

?>