<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "应用购买";
} else {
    if ($op == "detail") {
        $_W["page"]["title"] = "应用详情";
    }
}
include itemplate("plugin/buy);

?>