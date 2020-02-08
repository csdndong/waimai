<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "账户余额提现";
    $account = store_account($sid);
    if (empty($account["wechat"]["openid"]) && empty($account["wechat"]["openid_wxapp"]) && empty($account["alipay"]) && empty($account["bank"])) {
        header("location:" . iurl("store/finance/getcash/account"));
        exit;
    }
    $config = $_W["we7_wmall"]["config"]["getcash"];
    $channel = trim($_GPC["channel"]);
    $channel = empty($channel) ? "weixin" : $channel;
    if ($_W["ispost"]) {
        if (!in_array($channel, array("weixin", "alipay", "bank"))) {
            imessage(error(-1, "提现渠道不对"), "", "ajax");
        }
        $get_fee = floatval($_GPC["get_fee"]);
        $getcash_account = array();
        if ($channel == "weixin") {
            if (empty($account["wechat"]["openid"]) && empty($account["wechat"]["openid_wxapp"]) || empty($account["wechat"]["realname"])) {
            imessage(error(-1, "提现账户不完善,提现前请先完善提现账户"), "", "ajax");
        }
            if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
            if (empty($account["wechat"]["openid_wxapp"])) {
                imessage(error(-1, "未获取到商户账户针对小程序的openid,你可以尝试进入平台小程序会员中心并重新设置提现账户来解决此问题,"), "", "ajax");
            }
                $channel = "wxapp";
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
                if ($config["type"]["alipay"] != 1) {
                    imessage(error(-1, "平台未开启提现到支付宝"), "", "ajax");
                }
                if (empty($account["alipay"]) || empty($account["alipay"]["account"]) || empty($account["alipay"]["realname"])) {
                    imessage(error(-1, "支付宝账户信息不完善, 请到电脑端商户管理-财务-提现账户进行完善"), "", "ajax");
                }
                $getcash_account = $account["alipay"];
            } else {
                if ($channel == "bank") {
                    if ($config["type"]["bank"] != 1) {
                        imessage(error(-1, "平台未开启提现到银行卡"), "", "ajax");
                    }
                    if (empty($account["bank"]) || empty($account["bank"]["account"]) || empty($account["bank"]["realname"]) || empty($account["bank"]["id"])) {
                        imessage(error(-1, "银行账户信息不完善，请到电脑端商户管理-财务-提现账户进行完善"), "", "ajax");
                    }
                    $getcash_account = $account["bank"];
                }
            }
        }
        if ($get_fee < $account["fee_limit"]) {
            imessage(error(-1, "提现金额小于最低提现金额限制"), "", "ajax");
        }
        if ($account["amount"] < $get_fee) {
            imessage(error(-1, "提现金额大于账户可用余额"), "", "ajax");
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
        if ($final_fee < 0) {
            $final_fee = 0;
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "trade_no" => date("YmdHis") . random(10, true), "get_fee" => $get_fee, "take_fee" => $take_fee, "final_fee" => $final_fee, "account" => iserializer($getcash_account), "status" => 2, "addtime" => TIMESTAMP, "channel" => $channel);
        pdo_insert("tiny_wmall_store_getcash_log", $data);
        $getcash_id = pdo_insertid();
        store_update_account($sid, 0 - $get_fee, 2, $getcash_id);
        sys_notice_store_getcash($sid, $getcash_id, "apply");
        $getcashperiod = get_system_config("store.serve_fee.get_cash_period");
        if (empty($getcashperiod)) {
            imessage(error(0, "申请提现成功,等待平台管理员审核"), iurl("store/finance/getcash/log"), "ajax");
        } else {
            if ($getcashperiod == 1) {
                mload()->model("store.extra");
                $transfers = store_getcash_update($getcash_id, "transfers");
                imessage($transfers, iurl("store/finance/getcash/log"), "ajax");
            }
        }
    }
}
if ($ta == "account") {
    $_W["page"]["title"] = "设置提现账户";
    mload()->classs("wxpay");
    $wxpay = new wxpay();
    $bank_list = $wxpay->getback();
    $account = store_account($sid);
    if ($_W["ispost"]) {
        $data = array("uniacid" => $_W["uniacid"], "sid" => $sid);
        $wechat = array("openid" => trim($_GPC["wechat"]["openid"]), "openid_wxapp" => trim($_GPC["wechat"]["openid_wxapp"]), "nickname" => trim($_GPC["wechat"]["nickname"]), "avatar" => trim($_GPC["wechat"]["avatar"]), "realname" => trim($_GPC["wechat"]["realname"]) ? trim($_GPC["wechat"]["realname"]) : imessage(error(-1, "微信实名认证姓名不能为空"), "", "ajax"));
        $data["wechat"] = iserializer($wechat);
        $bank = array("id" => intval($_GPC["bank_id"]), "title" => $bank_list[$_GPC["bank_id"]]["title"], "account" => trim($_GPC["bank"]["account"]), "realname" => trim($_GPC["bank"]["realname"]));
        $data["bank"] = iserializer($bank);
        $alipay = array("realname" => trim($_GPC["alipay"]["realname"]), "account" => trim($_GPC["alipay"]["account"]));
        $data["alipay"] = iserializer($alipay);
        if (empty($account)) {
            $data["amount"] = 0;
            pdo_insert("tiny_wmall_store_account", $data);
        } else {
            pdo_update("tiny_wmall_store_account", $data, array("uniacid" => $_W["uniacid"], "sid" => $sid));
        }
        mlog("2011", $sid);
        imessage(error(0, "设置提现账户成功"), iurl("store/finance/getcash/account"), "ajax");
    }
}
if ($ta == "log") {
    $_W["page"]["title"] = "提现记录";
    $condition = " WHERE uniacid = :uniacid AND sid = :sid";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":sid"] = $sid;
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $channel = trim($_GPC["channel"]);
    if (!empty($channel)) {
        if ($channel == "weixin") {
            $condition .= " AND (channel = 'weixin' OR channel = 'wxapp') ";
        } else {
            $condition .= " AND channel = :channel";
            $params[":channel"] = $channel;
        }
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]);
    } else {
        $today = strtotime(date("Y-m-d"));
        $starttime = strtotime("-15 day", $today);
        $endtime = $today + 86399;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_store_getcash_log") . $condition, $params);
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_store_getcash_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$row) {
            $row["account"] = iunserializer($row["account"]);
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $channels = getcash_channels();
}
include itemplate("store/finance/getcash");

?>