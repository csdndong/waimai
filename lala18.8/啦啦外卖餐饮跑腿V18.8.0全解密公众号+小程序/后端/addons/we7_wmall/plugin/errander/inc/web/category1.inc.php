<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $fees = array("id" => "fees", "params" => array(), "data" => array());
}
include itemplate("category1");
exit;

?>