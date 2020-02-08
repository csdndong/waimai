<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
icheckauth();
if ($_config_plugin["basic"]["status"] != 1) {
    imessage(error(-1, "超级会员功能未开启"), "", "ajax");
}
$config = $_config_plugin["basic"];
if ($op == "list") {
    $month = svip_present_month();
    $member = $_W["member"];
    $member_redpackets = svip_member_exchange_redpackets();
    if (is_error($member_redpackets)) {
        imessage($member_redpackets, "", "ajax");
    }
    $exchange_max = intval($config["exchange_max"]);
    $can_exchange = $exchange_max;
    if (!empty($member_redpackets)) {
        $can_exchange = $exchange_max - count($member_redpackets);
        if ($can_exchange < 0) {
            $can_exchange = 0;
        }
    }
    $filter = array("status" => 1, "get_activity" => 1);
    $redpackets = svip_redpacket_fetchall($filter);
    $result = array("month" => $month, "redpackets" => $redpackets["redpackets"], "member_redpackets" => $member_redpackets, "member" => $member, "can_exchange" => $can_exchange, "exchange_max" => $exchange_max);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "more") {
        $filter = array("status" => 1, "get_activity" => 1);
        $redpackets = svip_redpacket_fetchall($filter);
        $result = array("redpackets" => $redpackets["redpackets"]);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($op == "redpacket") {
            $id = intval($_GPC["id"]);
            $redpacket = svip_redpacket_fetch($id, true);
            if (empty($redpacket)) {
                imessage(error(-1, "红包不存在或已删除"), "", "ajax");
            }
            $result = array("redpacket" => $redpacket);
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
                $num = svip_member_exchange_redpacket_num();
                $exchange_max = intval($config["exchange_max"]);
                $member_redpackets = svip_member_exchange_redpackets();
                $svip_credit1 = floatval($_W["member"]["svip_credit1"]);
                if ($exchange_cost == 1) {
                    $svip_credit1 = floatval($_W["member"]["svip_credit1"] - $redpacket["exchange_cost"]);
                }
                $result = array("member_redpackets" => $member_redpackets, "can_exchange" => $exchange_max - $num, "svip_credit1" => $svip_credit1);
                imessage(error(0, $result), "", "ajax");
            }
        }
    }
}

?>