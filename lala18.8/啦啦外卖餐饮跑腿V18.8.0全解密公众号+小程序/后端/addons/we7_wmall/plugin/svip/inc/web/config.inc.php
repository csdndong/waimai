<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $exchange_max = intval($_GPC["exchange_max"]);
        if ($exchange_max <= 0) {
            imessage(error(-1, "会员每月最多领取红包个数必须大于零"), "", "ajax");
        }
        $store_redpacket_min = floatval($_GPC["store_redpacket_min"]);
        if ($store_redpacket_min < 0) {
            imessage(error(-1, "商家红包最低金额不能小于零"), "", "ajax");
        }
        $svip = array("status" => intval($_GPC["status"]), "exchange_max" => $exchange_max, "store_redpacket_min" => $store_redpacket_min, "notice_before_overtime" => intval($_GPC["notice_before_overtime"]));
        set_plugin_config("svip.basic", $svip);
        set_config_text("超级会员权益说明", "agreement_svip", htmlspecialchars_decode($_GPC["agreement_svip"]));
        set_config_text("超级会员任务说明", "agreement_mission_svip", htmlspecialchars_decode($_GPC["agreement_mission_svip"]));
        imessage(error(0, "保存成功"), "refresh", "ajax");
    }
    $config = get_plugin_config("svip.basic");
    $agreement_svip = get_config_text("agreement_svip");
    $agreement_mission_svip = get_config_text("agreement_mission_svip");
}
include itemplate("config");

?>