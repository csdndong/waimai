<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "商户统计详情";
if ($_W["ispost"]) {
    $filter = array("start" => trim($_GPC["start"]), "end" => trim($_GPC["end"]));
}
$result = store_finance_stat($sid, $filter);
imessage(error(0, $result), "", "ajax");

?>