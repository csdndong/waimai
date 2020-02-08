<?php 
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "clear";
$_W["page"]["title"] = "活动海报";
if ($op == "clear") {
    load()->func("file");
    @rmdirs(MODULE_ROOT . "/resource/poster/qrcode/" . $_W["uniacid"]);
    @rmdirs(MODULE_ROOT . "/resource/poster/gohome/" . $_W["uniacid"]);
    imessage(error(0, "清除海报缓存成功"), iurl("gohome/poster/index"), "ajax");
}
include itemplate("poster");

?>