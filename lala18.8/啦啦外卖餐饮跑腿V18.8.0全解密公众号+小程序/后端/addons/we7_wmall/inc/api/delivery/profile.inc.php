<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $profile = $_W["deliveryer"];
    $profile["relation"] = deliveryer_push_token($_W["deliveryer"]["id"]);
    message(ierror(0, "", $profile), "", "ajax");
}

?>