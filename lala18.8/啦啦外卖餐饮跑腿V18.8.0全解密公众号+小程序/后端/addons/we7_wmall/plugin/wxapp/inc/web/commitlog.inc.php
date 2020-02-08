<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$wxapp_type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "we7_wmall";
mload()->model("cloud");
if ($op == "index") {
    $_W["page"]["title"] = "代码上传记录";
    $logs = cloud_w_wxapp_get_commit_log($wxapp_type);
    if (is_error($logs)) {
        imessage($logs, "", "info");
    }
}
include itemplate("commitlog");

?>
