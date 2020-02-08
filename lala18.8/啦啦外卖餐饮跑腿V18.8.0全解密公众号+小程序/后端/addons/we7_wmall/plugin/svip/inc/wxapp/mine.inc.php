<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth();
if ($_config_plugin["basic"]["status"] != 1) {
    imessage(error(-1, "超级会员功能未开启"), "", "ajax");
}
if ($op == "index") {
    $member = $_W["member"];
    if ($member["svip_status"] != 1) {
        imessage(error(-2, "您还未开通会员"), "", "ajax");
    }
    $member["svip_endtime_cn"] = date("Y-m-d", $member["svip_endtime"]);
    $config = get_plugin_config("svip.basic");
    $member["exchange_max"] = intval($config["exchange_max"]);
    $num_taked = svip_member_exchange_redpacket_num();
    $member["num_taked"] = $num_taked;
    $member["total_discount"] = svip_member_redpacket_total();
    $filter = array("status" => 1, "psize" => 10);
    $redpackets = svip_redpacket_fetchall($filter);
    $goods = svip_goods_getall($filter);
    $tasks = svip_task_getall(array("status" => 1, "psize" => 3));
    $result = array("member" => $member, "redpackets" => $redpackets["redpackets"], "goods" => $goods["goods"], "tasks" => $tasks["tasks"], "agreement" => get_config_text("agreement_svip"));
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "exchange") {
        $id = intval($_GPC["id"]);
        $exchange_cost = intval($_GPC["exchange_cost"]);
        $redpacket = svip_redpacket_fetch($id);
        $status = svip_redpacket_exchage($redpacket, $exchange_cost);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        $num_taked = svip_member_exchange_redpacket_num();
        $total_discount = svip_member_redpacket_total();
        $result = array("num_taked" => svip_member_exchange_redpacket_num(), "total_discount" => svip_member_redpacket_total());
        imessage(error(0, $result), "", "ajax");
    }
}

?>