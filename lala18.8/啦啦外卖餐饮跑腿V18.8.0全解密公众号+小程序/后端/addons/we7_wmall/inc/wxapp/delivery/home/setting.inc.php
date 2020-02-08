<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $result = array("deliveryer" => $_W["deliveryer"]);
    imessage(error(0, $result), "", "ajax");
}

?>
