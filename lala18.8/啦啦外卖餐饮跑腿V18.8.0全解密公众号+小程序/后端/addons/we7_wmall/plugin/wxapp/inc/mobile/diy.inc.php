<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $id = intval($_GPC["id"]);
    if (empty($id)) {
        imessage("页面id不能为空", imurl("wmall/home/index"), "info");
    }
    $page = get_diypage_diy($id, true);
    if (empty($id)) {
        imessage("页面不能为空", imurl("wmall/home/index"), "info");
    }
    $_W["page"]["title"] = $page["data"]["page"]["title"];
    $diypage = $page["data"]["page"];
    $diyitems = $page["data"]["items"];
}
include itemplate("diy");

?>
