<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "group";
if ($op == "group") {
    $_W["page"]["title"] = "顾客设置";
    if ($_W["ispost"]) {
        $group_update_mode = trim($_GPC["group_update_mode"]);
        if (empty($group_update_mode)) {
            imessage(error(-1, "请选择顾客等级升级依据"));
        }
        set_system_config("member.group_update_mode", $group_update_mode);
        imessage(error(0, "设置顾客等级升级依据成功"), referer(), "ajax");
    }
    $group_update_mode = get_system_config("member.group_update_mode");
} else {
    if ($op == "address") {
        $_W["page"]["title"] = "顾客收货地址设置";
        if ($_W["ispost"]) {
            $use_weixin_address = intval($_GPC["use_weixin_address"]);
            set_system_config("member.use_weixin_address", $use_weixin_address);
            imessage(error(0, "设置顾客收货地址模式"), referer(), "ajax");
        }
        $use_weixin_address = get_system_config("member.use_weixin_address");
    }
}
include itemplate("config/member");

?>