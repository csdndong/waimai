<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $sid_str = implode(", ", array_unique(array_keys($sids)));
    $stores = pdo_fetchall("select id, title, logo from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id in (" . $sid_str . ")", array(":uniacid" => $_W["uniacid"]));
    $result = array("stores" => $stores);
    imessage(error(0, $result), "", "ajax");
}

?>