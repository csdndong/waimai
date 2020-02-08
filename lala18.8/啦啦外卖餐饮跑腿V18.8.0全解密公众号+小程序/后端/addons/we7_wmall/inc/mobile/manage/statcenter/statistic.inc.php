<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "商户统计详情";
if ($_W["ispost"]) {
    $filter = array("start" => trim($_GPC["start"]), "end" => trim($_GPC["end"]));
}
$stat = store_finance_stat($sid, $filter);
include itemplate("statcenter/statistic");

?>