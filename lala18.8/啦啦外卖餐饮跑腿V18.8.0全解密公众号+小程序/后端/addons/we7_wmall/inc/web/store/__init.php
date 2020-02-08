<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if (0 < $_W["we7_wmall"]["sid"]) {
    $_W["we7_wmall"]["store"] = store_fetch($_W["we7_wmall"]["sid"]);
}
if ($_W["_action"] == "activity") {
    $op = $_W["_op"];
    $activity_types = store_all_activity();
    if (isset($activity_types["svipRedpacket"])) {
        unset($activity_types["svipRedpacket"]);
    }
    $activity_types = array_keys($activity_types);
    if (in_array($op, $activity_types)) {
        $ta = trim($_GPC["ta"]);
        if (($ta == "post" || $ta != "del" && $_W["ispost"]) && $_W["we7_wmall"]["config"]["store"]["activity"]["perm"][$op]["status"] != 1) {
            if ($_W["ispost"]) {
                imessage(error(-1, "平台没有开启该活动, 详情请咨询平台管理员"), referer(), "ajax");
            }
            imessage("平台没有开启该活动", referer(), "info");
        }
    }
}

?>