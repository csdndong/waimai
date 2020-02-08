<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "排行榜设置";
    if ($_W["ispost"]) {
        $num = intval($_GPC["num"]);
        $arr = array("status" => intval($_GPC["status"]), "type" => intval($_GPC["type"]), "num" => 300 < $num ? 300 : $num);
        $item = array();
        if ($arr["type"] == 2) {
            foreach ($_GPC["nickname"] as $k => $v) {
                $item[] = array("avatar" => $_GPC["avatar"][$k], "nickname" => $v, "commission" => $_GPC["commission"][$k]);
            }
            $arr["infomation"] = $item;
        }
        set_plugin_config("rank", $arr);
        imessage(error(0, "排行榜设置成功"), "refresh", "ajax");
    }
    $rank = get_plugin_config("rank");
    $rank["url"] = imurl("spread/rank", array(), true);
}
include itemplate("rank");

?>