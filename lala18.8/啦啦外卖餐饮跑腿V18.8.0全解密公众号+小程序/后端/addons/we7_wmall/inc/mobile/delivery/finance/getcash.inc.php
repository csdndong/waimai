<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "申请提现";
$config_getcash = $_deliveryer["fee_getcash"];
$config = $_W["we7_wmall"]["config"]["getcash"];
if ($_W["isajax"]) {
    if (empty($_W["deliveryer"]["perm_plateform"])) {
        imessage(error(-1, "你不是平台配送员，不能进行提现操作"), "", "ajax");
    }
    $channel = empty($_GPC["channel"]) ? "weixin" : trim($_GPC["channel"]);
    if (!in_array($channel, array("weixin", "alipay", "bank"))) {
        imessage(error(-1, "提现渠道有误"), "", "ajax");
    }
    $get_fee = floatval($_GPC["fee"]);
    $getcash_account = array();
    if ($channel == "weixin") {
    if (empty($_deliveryer["openid"]) && empty($_deliveryer["openid_wxapp"]) || empty($_deliveryer["title"])) {
        imessage(error(-1, "配送员账户不完善, 无法提现"), "", "ajax");
    }
        if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
        if (empty($_deliveryer["openid_wxapp"])) {
            imessage(error(-1, "未获取到配送员针对小程序的openid, 你可以尝试进入平台小程序会员中心来解决此问题"), "", "ajax");
        }
            $channel = "wxapp";
    } else {
        $openid = mktTransfers_get_openid($_deliveryer["id"], $_deliveryer["openid"], $get_fee, "deliveryer");
        if (is_error($openid)) {
            imessage($openid, "", "ajax");
            }
        }
        $getcash_account = array("nickname" => $_deliveryer["nickname"], "openid" => $openid, "openid_wxapp" => $_deliveryer["openid_wxapp"], "avatar" => $_deliveryer["avatar"], "realname" => $_deliveryer["title"]);
    } else {
        if ($channel == "alipay") {
            if ($_W["we7_wmall"]["config"]["getcash"]["type"]["alipay"] != 1) {
                imessage(error(-1, "平台未开启提现到支付宝"), "", "ajax");
            }
            if (empty($_deliveryer["account"]["alipay"]["account"]) || empty($_deliveryer["account"]["alipay"]["realname"])) {
                imessage(error(-1, "支付宝账户信息不完善, 请完善支付宝账户信息后再进行提现操作"), "", "ajax");
            }
            $getcash_account = $_deliveryer["account"]["alipay"];
        } else {
            if ($channel == "bank") {
                if ($_W["we7_wmall"]["config"]["getcash"]["type"]["bank"] != 1) {
                    imessage(error(-1, "平台未开启提现到银行卡"), "", "ajax");
                }
                if (empty($_deliveryer["account"]["bank"]["id"]) || empty($_deliveryer["account"]["bank"]["account"]) || empty($_deliveryer["account"]["bank"]["realname"])) {
                    imessage(error(-1, "银行账户信息不完善, 请完善账户信息后再进行提现操作"), "", "ajax");
                }
                $getcash_account = $_deliveryer["account"]["bank"];
            }
        }
    }
    if (!$get_fee) {
        imessage(error(-1, "提现金额有误"), "", "ajax");
    }
    if ($get_fee < $config_getcash["get_cash_fee_limit"]) {
        imessage(error(-1, "提现金额小于最低提现金额限制"), "", "ajax");
    }
    if ($_deliveryer["credit2"] < $get_fee) {
        imessage(error(-1, "提现金额大于账户可用余额"), "", "ajax");
    }
    $take_fee = round($get_fee * $config_getcash["get_cash_fee_rate"] / 100, 2);
    $take_fee = max($take_fee, $config_getcash["get_cash_fee_min"]);
    if (0 < $config_getcash["get_cash_fee_max"]) {
        $take_fee = min($take_fee, $config_getcash["get_cash_fee_max"]);
    }
    $final_fee = $get_fee - $take_fee;
    if ($final_fee < 0) {
        $final_fee = 0;
    }
    $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "deliveryer_id" => $_deliveryer["id"], "trade_no" => date("YmdHis") . random(10, true), "get_fee" => $get_fee, "take_fee" => $take_fee, "final_fee" => $final_fee, "account" => iserializer($getcash_account), "status" => 2, "addtime" => TIMESTAMP, "channel" => $channel);
    pdo_insert("tiny_wmall_deliveryer_getcash_log", $data);
    $getcash_id = pdo_insertid();
    $remark = date("Y-m-d H:i:s") . "申请提现,提现金额" . $get_fee . "元, 手续费" . $take_fee . "元, 实际到账" . $final_fee . "元";
    deliveryer_update_credit2($_deliveryer["id"], 0 - $get_fee, 2, $getcash_id, $remark);
    sys_notice_deliveryer_getcash($_deliveryer["id"], $getcash_id, "apply");
    $getcashperiod = $config_getcash["get_cash_period"];
    if (empty($getcashperiod)) {
        imessage(error(0, "申请提现成功,等待平台管理员审核"), iurl("delivery/finance/getcash/log"), "ajax");
    } else {
        if ($getcashperiod == 1) {
            $transfers = deliveryer_getcash_update($getcash_id, "transfers");
            imessage($transfers, "", "ajax");
        }
    }
}
include itemplate("finance/getcash");

?>