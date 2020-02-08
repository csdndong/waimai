<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
icheckauth(true);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $filter = array("uid" => $_W["member"]["uid"], "status" => intval($_GPC["status"]));
    $records = gohoem_kanjia_record_fetchall($filter);
    $result = array( "records" => $records );
    imessage(error(0, $result), "", "ajax");
}

?>
