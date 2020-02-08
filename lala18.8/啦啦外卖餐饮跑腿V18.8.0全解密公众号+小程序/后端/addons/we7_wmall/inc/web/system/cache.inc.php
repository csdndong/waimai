<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "更新缓存";
if ($_W["ispost"]) {
    pdo_run("delete from ims_core_sessions where uniacid = '" . $_W["uniacid"] . "'");
    imessage(error(0, "更新缓存成功"), referer(), "ajax");
}
include itemplate("system/cache");

?>