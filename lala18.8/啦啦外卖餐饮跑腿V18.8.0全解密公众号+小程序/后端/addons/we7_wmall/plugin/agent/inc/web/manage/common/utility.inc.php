<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$do = "utility";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "build_time";
if ($op == "build_time" && $_W["isajax"]) {
    $pre_minute = intval($_GPC["pre_minute"]);
    $starttime = strtotime(date("Y-m-d"));
    $endtime = $starttime + 86399;
    $times = array();
    $start = $starttime;
    while ($start < $endtime) {
        $end = $start + $pre_minute * 60;
        if ($endtime <= $end) {
            $end = $starttime + 86340;
        }
        $times[] = array("start" => date("H:i", $start), "end" => date("H:i", $end), "fee" => 0, "status" => 1);
        $start += $pre_minute * 60;
    }
    message(error(0, $times), "", "ajax");
}

?>