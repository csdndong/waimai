<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $result = plateform_login($_POST);
    if (is_error($result)) {
        message(ierror(-1, $result["message"]), "", "ajax");
    }
    message(ierror(0, "", $result), "", "ajax");
}

?>