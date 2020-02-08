<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "操作日志";
    $role = trim($_GPC["role"]);
    $logs_all_type = mlog_types($role);
    $type = trim($_GPC["type"]);
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : -2;
    $todaytime = strtotime(date("Y-m-d"));
    $starttime = $todaytime;
    $endtime = $starttime + 86399;
    $_GPC["endtime"] = $endtime;
    if (-2 < $days) {
        if ($days == -1) {
            $starttime = strtotime($_GPC["addtime"]["start"]);
            $endtime = strtotime($_GPC["addtime"]["end"]);
            $_GPC["starttime"] = $starttime;
            $_GPC["endtime"] = $endtime;
        } else {
            $starttime = strtotime("-" . $days . " days", $todaytime);
            $_GPC["starttime"] = $starttime;
        }
    }
    $data = mlog_fetch_all();
    $logs = $data["logs"];
    $pager = $data["pager"];
}
include itemplate(base64_decode("perm/operatelog"));

?>
