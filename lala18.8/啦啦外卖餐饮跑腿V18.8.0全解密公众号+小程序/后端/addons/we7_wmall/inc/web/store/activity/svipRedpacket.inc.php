<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    if (!check_plugin_perm("svip")) {
        imessage(error(-1, "平台暂无超级会员插件的使用权"), "", "error");
    }
    $config = get_plugin_config("svip.basic");
    if ($config["status"] != 1) {
        imessage(error(-1, "平台暂未开启超级会员"), "", "error");
    }
    $_W["page"]["title"] = "超级会员门店红包";
    if ($_W["ispost"]) {
        $starttime = trim($_GPC["starttime"]);
        if (empty($starttime)) {
            imessage(error(-1, "活动开始时间不能为空"), "", "ajax");
        }
        $endtime = trim($_GPC["endtime"]);
        if (empty($endtime)) {
            imessage(error(-1, "活动结束时间不能为空"), "", "ajax");
        }
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        if ($endtime <= $starttime) {
            imessage(error(-1, "活动开始时间不能大于结束时间"), "", "ajax");
        }
        $discount = floatval($_GPC["discount"]);
        $config_svip = get_plugin_config("svip");
        $redpacket_min = floatval($config_svip["basic"]["store_redpacket_min"]);
        if ($discount < $redpacket_min) {
            imessage(error(-1, "红包金额不能小于" . $redpacket_min . "元"), "", "ajax");
        }
        $condition = floatval($_GPC["condition"]);
        if ($condition < 0) {
            $condition = 0;
        }
        $use_days_limit = intval($_GPC["use_days_limit"]);
        if ($use_days_limit <= 0) {
            imessage(error(-1, "红包有效期必须大于或等于零"), "", "ajax");
        }
        $amount = floatval($_GPC["amount"]);
        if ($amount <= 0) {
            imessage(error(-1, "每日限领红包数量必须大于零"), "", "ajax");
        }
        $plateform_charge = 0;
        $agent_charge = 0;
        $store_charge = $discount;
        if (!empty($_W["ismanager"])) {
            $plateform_charge = floatval($_GPC["plateform_charge"]);
            $agent_charge = floatval($_GPC["agent_charge"]);
            if ($discount < $agent_charge) {
                $agent_charge = $discount;
                $plateform_charge = 0;
                $store_charge = 0;
            } else {
                if ($discount < $plateform_charge) {
                    $plateform_charge = $discount;
                    $agent_charge = 0;
                    $store_charge = 0;
                } else {
                    if ($discount < $plateform_charge + $agent_charge) {
                        $plateform_charge = $discount - $agent_charge;
                        $store_charge = 0;
                    } else {
                        $store_charge = round($discount - $agent_charge - $plateform_charge, 2);
                    }
                }
            }
            if ($store_charge < 0) {
                $store_charge = 0;
            }
        } else {
            if (!empty($_W["isagenter"])) {
                $agent_charge = floatval($_GPC["agent_charge"]);
                if ($discount < $agent_charge) {
                    $agent_charge = $discount;
                    $plateform_charge = 0;
                    $store_charge = 0;
                } else {
                    $store_charge = round($discount - $agent_charge, 2);
                }
                if ($store_charge < 0) {
                    $store_charge = 0;
                }
            }
        }
        $store = store_fetch($sid, array("id", "title", "agentid"));
        $activity = array("uniacid" => $_W["uniacid"], "agentid" => $store["agentid"], "sid" => $sid, "title" => (string) $discount . "元会员红包", "starttime" => $starttime, "endtime" => $endtime, "type" => "svipRedpacket", "status" => 1, "data" => array("discount" => $discount, "condition" => $condition, "use_days_limit" => $use_days_limit, "amount" => $amount, "discount_bear" => array("plateform_charge" => $plateform_charge, "agent_charge" => $agent_charge, "store_charge" => $store_charge)));
        $activity["data"] = iserializer($activity["data"]);
        $status = activity_set($sid, $activity);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        mload()->model("plugin");
        pload()->model("svip");
        $redpacket = array("uniacid" => $_W["uniacid"], "agentid" => $store["agentid"], "sid" => $sid, "title" => $store["title"], "discount" => $discount, "condition" => $condition, "use_days_limit" => $use_days_limit, "amount" => $amount, "starttime" => $starttime, "endtime" => $endtime, "data" => array("discount_bear" => array("plateform_charge" => $plateform_charge, "agent_charge" => $agent_charge, "store_charge" => $store_charge)));
        $status = svip_set_store_redpacket($sid, $redpacket);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        imessage(error(0, "设置超级会员门店红包成功"), "refresh", "ajax");
    }
    $activity = activity_get($sid, "svipRedpacket");
}
if ($ta == "del") {
    $status = activity_del($sid, "svipRedpacket");
    if (is_error($status)) {
        imessage($status, referer(), "ajax");
    }
    imessage(error(0, "撤销活动成功"), referer(), "ajax");
}
include itemplate("store/activity/svipRedpacket");

?>