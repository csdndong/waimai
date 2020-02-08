<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "link";
if ($ta == "link") {
    $type = empty($_GPC["type"]) ? "manager" : trim($_GPC["type"]);
    $urls = wxapp_urls($type);
    include itemplate("public/plateformLink");
}


?>