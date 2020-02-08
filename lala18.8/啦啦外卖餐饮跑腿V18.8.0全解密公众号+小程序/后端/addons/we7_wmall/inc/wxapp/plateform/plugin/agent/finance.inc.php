<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $agent = $_W["agent"];
    $result = array("agent" => $agent);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "getcash") {
        $agent = $_W["agent"];
        if (empty($agent["account"]["wechat"]["openid"]) && empty($agent["account"]["wechat"]["openid_wxapp"]) || empty($agent["account"]["wechat"]["realname"])) {
            imessage(error(-1, "提现前请先完善提现账户"), "", "ajax");
        }
        $get_fee = floatval($_GPC["get_fee"]);
        if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
            if (empty($agent["account"]["wechat"]["openid_wxapp"])) {
                imessage(error(-1, "未获取到代理商针对小程序的openid,你可以尝试进入平台小程序会员中心并重新设置提现账户来解决此问题"), "", "ajax");
            }
        } else {
            $openid = mktTransfers_get_openid($agent["id"], $agent["account"]["wechat"]["openid"], $get_fee, "agent");
            if (is_error($openid)) {
                imessage($openid, "", "ajax");
            }
            if (empty($openid)) {
                imessage(error(-1, "未获取到代理商针对公众号的openid,你可以尝试进入平台公众号会员中心并重新设置提现账户来解决此问题"), "", "ajax");
            }
            $agent["account"]["wechat"]["openid"] = $openid;
        }
        $fee_period = $agent["fee"]["fee_period"] * 24 * 3600;
        if (0 < $fee_period) {
            $getcash_log = pdo_fetch("select addtime from " . tablename("tiny_wmall_agent_getcash_log") . " where uniacid = :uniacid and agentid = :agentid order by addtime desc", array(":uniacid" => $_W["uniacid"], ":agentid" => $agent["id"]));
            $last_getcash = $getcash_log["addtime"];
            if (TIMESTAMP < $last_getcash + $fee_period) {
                imessage(error(-1, "距上次提现时间小于提现周期"), "", "ajax");
            }
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $agent["id"], "trade_no" => date("YmdHis") . random(10, true), "get_fee" => $get_fee, "take_fee" => 0, "final_fee" => $get_fee, "account" => iserializer($agent["account"]["wechat"]), "status" => 2, "addtime" => TIMESTAMP, "channel" => MODULE_FAMILY == "wxapp" ? "wxapp" : "weixin");
        pdo_insert("tiny_wmall_agent_getcash_log", $data);
        $getcash_id = pdo_insertid();
        agent_update_account($agent["id"], 0 - $get_fee, 2, "");
        sys_notice_agent_getcash($agent["id"], $getcash_id, "apply");
        imessage(error(0, "申请提现成功,等待平台管理员审核"), "", "ajax");
    }
}

?>