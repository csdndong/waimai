<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $result = array("data" => spread_commission_stat(), "settle" => $_config_plugin["settle"]);
    imessage(error(0, $result), "", "ajax");
}

?>