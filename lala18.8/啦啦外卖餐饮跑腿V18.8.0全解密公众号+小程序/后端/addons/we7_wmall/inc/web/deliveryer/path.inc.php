<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    mload()->model("deliveryer");
    $_W["page"]["title"] = "轨迹回放";
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if ($_W["ispost"]) {
        $paths = get_deliveryer_paths($deliveryer_id, "20190125", 1);
        imessage(error(0, ("更新成功"), referer(), "ajax");
    }
    $paths = get_deliveryer_paths($deliveryer_id, "20190125");
}
include itemplate("deliveryer/path");

?>