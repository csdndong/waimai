<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$sid = intval($_GPC["__mg_sid"]);
$account = $store["account"];
if ($ta == "index") {
    $result = array("account" => $account, "config" => $_W["we7_wmall"]["config"]["getcash"]);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "getcash") {
        $channel = empty($_GPC["channel"]) ? "weixin" : trim($_GPC["channel"]);
        if (!in_array($channel, array("weixin", "alipay", "bank"))) {
            imessage(error(-1, "提现渠道不对"), "", "ajax");
        }
        $getcash_account = array();
        $get_fee = floatval($_GPC["fee"]);
        if ($channel == "weixin") {
        if (empty($account["wechat"]["openid"]) && empty($account["wechat"]["openid_wxapp"]) || empty($account["wechat"]["realname"])) {
            imessage(error(-1, "提现账户不完善, 请到电脑端商户管理-财务-提现账户进行完善"), "", "ajax");
        }
            if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
                $channel = "wxapp";
            if (empty($account["wechat"]["openid_wxapp"])) {
                imessage(error(-1, "未获取到商户账户针对小程序的openid,你可以尝试进入平台小程序会员中心并重新设置提现账户来解决此问题"), "", "ajax");
            }
        } else {
            $openid = mktTransfers_get_openid($sid, $account["wechat"]["openid"], $get_fee);
            if (is_error($openid)) {
                imessage($openid, "", "ajax");
            }
            if (empty($openid)) {
                imessage(error(-1, "未获取到商户账户针对公众号的openid,你可以尝试进入平台公众号会员中心并重新设置提现账户来解决此问题"), "", "ajax");
            }
            $account["wechat"]["openid"] = $openid;
            }
            $getcash_account = $account["wechat"];
        } else {
            if ($channel == "alipay") {
                if ($_W["we7_wmall"]["config"]["getcash"]["type"]["alipay"] != 1) {
                    imessage(error(-1, "平台未开启提现到支付宝"), "", "ajax");
                }
                if (empty($account["alipay"]) || empty($account["alipay"]["account"]) || empty($account["alipay"]["realname"])) {
                    imessage(error(-1, "支付宝账户信息不完善, 请到电脑端商户管理-财务-提现账户进行完善"), "", "ajax");
                }
                $getcash_account = $account["alipay"];
            } else {
                if ($channel == "bank") {
                    if ($_W["we7_wmall"]["config"]["getcash"]["type"]["bank"] != 1) {
                        imessage(error(-1, "平台未开启提现到银行卡"), "", "ajax");
                    }
                    if (empty($account["bank"]) || empty($account["bank"]["account"]) || empty($account["bank"]["realname"]) || empty($account["bank"]["id"])) {
                        imessage(error(-1, "银行账户信息不完善， 请到电脑端商户管理-财务-提现账户进行完善"), "", "ajax");
                    }
                    $getcash_account = $account["bank"];
                }
            }
        }
        if (!$get_fee) {
            imessage(error(-1, "提现金额有误"), "", "ajax");
        }
        if ($get_fee < $account["fee_limit"]) {
            imessage(error(-1, "提现金额不能小于最低提现金额限制"), "", "ajax");
        }
        if ($account["amount"] < $get_fee) {
            imessage(error(-1, "提现金额不能大于账户可用余额"), "", "ajax");
        }
        $fee_period = $account["fee_period"] * 24 * 3600;
        if (0 < $fee_period) {
            $getcash_log = pdo_fetch("select addtime from " . tablename("tiny_wmall_store_getcash_log") . " where uniacid = :uniacid and sid = :sid order by addtime desc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
            $last_getcash = $getcash_log["addtime"];
            if (TIMESTAMP < $last_getcash + $fee_period) {
                imessage(error(-1, "距上次提现时间小于提现周期"), "", "ajax");
            }
        }
        $take_fee = round($get_fee * $account["fee_rate"] / 100, 2);
        $take_fee = max($take_fee, $account["fee_min"]);
        if (0 < $account["fee_max"]) {
            $take_fee = min($take_fee, $account["fee_max"]);
        }
        $final_fee = $get_fee - $take_fee;
        if ($final_fee <= 0) {
            imessage(error(-1, "实际到账金额小于0元"), "", "ajax");
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "trade_no" => date("YmdHis") . random(10, true), "get_fee" => $get_fee, "take_fee" => $take_fee, "final_fee" => $final_fee, "account" => iserializer($getcash_account), "status" => 2, "addtime" => TIMESTAMP, "channel" => $channel);
        pdo_insert("tiny_wmall_store_getcash_log", $data);
        $getcash_id = pdo_insertid();
        store_update_account($sid, 0 - $get_fee, 2, $getcash_id);
        sys_notice_store_getcash($sid, $getcash_id, "apply");
        $getcashperiod = get_system_config("store.serve_fee.get_cash_period");
        if (empty($getcashperiod)) {
            imessage(error(0, "申请提现成功,等待平台管理员处理"), "", "ajax");
            return 1;
        }
        if ($getcashperiod == 1) {
            mload()->model("store.extra");
            $transfers = store_getcash_update($getcash_id, "transfers");
            imessage($transfers, "", "ajax");
        }
    }
}

?>