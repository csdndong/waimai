<?php

defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$advertise = get_plugin_config("advertise");
if ($advertise["basic"]["status"] != 1) {
    imessage(error(-1, "广告暂未开售"), "", "ajax");
}
$_W["page"]["title"] = "新建推广";
$sid = intval($_GPC["__mg_sid"]);
include itemplate("advertise/index");

?>