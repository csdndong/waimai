<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
icheckauth();
if ($_config_plugin["basic"]["status"] != 1) {
    imessage(error(-1, "超级会员功能未开启"), "", "ajax");
}
if ($op == "list") {
    $month = svip_present_month();
    if (is_error($month)) {
        imessage($month, "", "ajax");
    }
    $next = $month["endtime"] + 86400;
    $next = date("Y-m-d", $next);
    $config = get_plugin_config("svip.basic");
    $exchange_max = intval($config["exchange_max"]);
    $type = trim($_GPC["type"]);
    $records = array();
    if ($type == "redpacket") {
        $records = svip_redpacket_record_fetchall();
    } else {
        if ($type == "credit") {
            $records = svip_credit1_record_fetchall();
        }
    }
    $result = array("records" => $records, "next" => $next, "exchange_max" => $exchange_max);
    imessage(error(0, $result), "", "ajax");
}

?>