<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
mload()->model("agent");
$agent = get_agent($_W["agentid"], array("amount", "account", "fee"));
$account = $agent["account"];
$account["amount"] = $agent["amount"];
if ($op == "index") {
    $_W["page"]["title"] = "账户余额提现";
    if (empty($account["wechat"]["openid"])) {
        header("location:" . iurl("finance/getcash/account"));
        exit;
    }
    $config = $_W["we7_wmall"]["config"]["getcash"];
    $channel = trim($_GPC["channel"]);
    $channel = empty($channel) ? "weixin" : $channel;
    if ($_W["ispost"]) {
        $channel = trim($_GPC["channel"]);
        if (!in_array($channel, array("weixin", "alipay", "bank"))) {
            imessage(error(-1, "提现前请先完善提现账户"), "", "ajax");
        }
        $get_fee = floatval($_GPC["get_fee"]);
        if ($account["amount"] < $get_fee) {
            imessage(error(-1, "提现金额大于账户可用余额"), "", "ajax");
        }
        $getcash_account = array();
        if ($channel == "weixin") {
            if (empty($account["wechat"]["openid"]) && empty($account["wechat"]["openid_wxapp"]) || empty($account["wechat"]["realname"])) {
                imessage(error(-1, "微信信息不完善，提现前请先完善提现账户"), "", "ajax");
            }
            if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
            if (empty($account["wechat"]["openid_wxapp"])) {
                imessage(error(-1, "未获取到代理商针对小程序的openid,你可以尝试进入平台小程序会员中心并重新设置提现账户来解决此问题"), "", "ajax");
            }
                $channel = "wxapp";
            } else {
                $openid = mktTransfers_get_openid($_W["agentid"], $account["wechat"]["openid"], $_GPC["get_fee"], "agent");
                if (is_error($openid)) {
                    imessage($openid, "", "ajax");
                }
                if (empty($openid)) {
                imessage(error(-1, "未获取到代理商针对公众号的openid,你可以尝试进入平台公众号会员中心并重新设置提现账户来解决此问题"), "", "ajax");
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
                    imessage(error(-1, "支付宝账户信息不完善，提现前请先完善提现账户"), "", "ajax");
                }
                $getcash_account = $account["alipay"];
            } else {
                if ($channel == "bank") {
                    if ($config["type"]["bank"] != 1) {
                        imessage(error(-1, "平台未开启提现到银行卡"), "", "ajax");
                    }
                    if (empty($account["bank"]) || empty($account["bank"]["account"]) || empty($account["bank"]["realname"]) || empty($account["bank"]["id"])) {
                        imessage(error(-1, "银行账户信息不完善，提现前请先完善提现账户"), "", "ajax");
                    }
                    $getcash_account = $account["bank"];
                }
            }
        }
        $fee_period = $agent["fee"]["fee_period"] * 24 * 3600;
        if (0 < $fee_period) {
            $getcash_log = pdo_fetch("select addtime from " . tablename("tiny_wmall_agent_getcash_log") . " where uniacid = :uniacid and agentid = :agentid order by addtime desc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
            $last_getcash = $getcash_log["addtime"];
            if (TIMESTAMP < $last_getcash + $fee_period) {
                imessage(error(-1, "距上次提现时间小于提现周期"), "", "ajax");
            }
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "trade_no" => date("YmdHis") . random(10, true), "get_fee" => $get_fee, "take_fee" => 0, "final_fee" => $get_fee, "account" => iserializer($getcash_account), "status" => 2, "addtime" => TIMESTAMP, "channel" => $channel);
        pdo_insert("tiny_wmall_agent_getcash_log", $data);
        $getcash_id = pdo_insertid();
        agent_update_account($_W["agentid"], 0 - $get_fee, 2, "");
        sys_notice_agent_getcash($_W["agentid"], $getcash_id, "apply");
        imessage(error(0, "申请提现成功,等待平台管理员审核"), iurl("finance/getcash/log"), "ajax");
    }
}
if ($op == "account") {
    $_W["page"]["title"] = "设置提现账户";
    mload()->classs("wxpay");
    $wxpay = new wxpay();
    $bank_list = $wxpay->getback();
    if ($_W["ispost"]) {
        $wechat = array();
        $wechat["openid"] = trim($_GPC["wechat"]["openid"]);
        $wechat["openid_wxapp"] = trim($_GPC["wechat"]["openid_wxapp"]);
        $wechat["nickname"] = trim($_GPC["wechat"]["nickname"]);
        $wechat["avatar"] = trim($_GPC["wechat"]["avatar"]);
        $wechat["realname"] = trim($_GPC["wechat"]["realname"]) ? trim($_GPC["wechat"]["realname"]) : imessage(error(-1, "微信实名认证姓名不能为空"), "", "ajax");
        $update["wechat"] = $wechat;
        $bank = array();
        $bank["id"] = intval($_GPC["bank_id"]);
        $bank["title"] = $bank_list[$_GPC["bank_id"]]["title"];
        $bank["account"] = trim($_GPC["bank"]["account"]);
        $bank["realname"] = trim($_GPC["bank"]["realname"]);
        $update["bank"] = $bank;
        $alipay = array();
        $alipay["realname"] = trim($_GPC["alipay"]["realname"]);
        $alipay["account"] = trim($_GPC["alipay"]["account"]);
        $update["alipay"] = $alipay;
        pdo_update("tiny_wmall_agent", array("account" => iserializer($update)), array("uniacid" => $_W["uniacid"], "id" => $_W["agentid"]));
        mlog("5007", $_W["agentid"]);
        imessage(error(0, "设置提现账户成功"), iurl("finance/getcash/account"), "ajax");
    }
}
if ($op == "log") {
    $_W["page"]["title"] = "提现记录";
    $condition = " WHERE uniacid = :uniacid AND agentid = :agentid";
    $params[":uniacid"] = $_W["uniacid"];
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
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
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_agent_getcash_log") . $condition, $params);
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_agent_getcash_log") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$row) {
            $row["account"] = iunserializer($row["account"]);
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $channels = getcash_channels();
}
include itemplate("finance/getcash");

?>