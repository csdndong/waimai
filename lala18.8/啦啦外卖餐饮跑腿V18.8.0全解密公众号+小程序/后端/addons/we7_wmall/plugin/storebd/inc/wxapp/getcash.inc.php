<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $condition = " where uniacid = :uniacid and bd_id = :bd_id";
    $params = array(":uniacid" => $_W["uniacid"], ":bd_id" => $_W["storebd_user"]["id"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $records = pdo_fetchall("select * from " . tablename("tiny_wmall_storebd_getcash_log") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$value) {
            $value["addtime_cn"] = date("Y-m-d H:i", $value["addtime"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($op == "application") {
        $config = get_plugin_config("storebd.basic");
        $storebd_user = storebd_user_fetch($_W["storebd_user"]["id"], "id");
        if ($_W["ispost"]) {
            $get_fee = floatval($_GPC["fee"]);
            if (empty($get_fee)) {
                imessage(error(-1, "提现金额不能为空"), "", "ajax");
            }
            if (empty($storebd_user["openid"]) && empty($storebd_user["openid_wxapp"]) || empty($storebd_user["title"])) {
                imessage(error(-1000, "店铺推广员账户信息不完善,无法提现"), "", "ajax");
            }
            $channel = "weixin";
            if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
                $channel = "wxapp";
                if (empty($storebd_user["openid_wxapp"])) {
                    imessage(error(-1, "未获取到店铺推广员针对公众号的openid, 你可以尝试进入平台公众号会员中心来解决此问题"), "", "ajax");
                }
            } else {
                $openid = mktTransfers_get_openid($storebd_user["id"], $storebd_user["openid"], $get_fee, "storebd");
                if (is_error($openid)) {
                    imessage($openid, "", "ajax");
                }
                if (empty($openid)) {
                    imessage(error(-1, "未获取到店铺推广员针对公众号的openid, 你可以尝试进入平台公众号会员中心来解决此问题"), "", "ajax");
                }
            }
            if ($get_fee < $config["fee_getcash"]["get_cash_fee_min"]) {
                imessage(error(-1, "提现金额小于最低提现金额限制"), "", "ajax");
            }
            if ($storebd_user["credit2"] < $get_fee) {
                imessage(error(-1, "提现金额大于账户可用余额"), "", "ajax");
            }
            $take_fee = round($get_fee * $config["fee_getcash"]["get_cash_fee_rate"] / 100, 2);
            $take_fee = max($take_fee, $config["fee_getcash"]["get_cash_fee_min"]);
            if (0 < $config["fee_getcash"]["get_cash_fee_max"]) {
                $take_fee = min($take_fee, $config["fee_getcash"]["get_cash_fee_max"]);
            }
            $final_fee = $get_fee - $take_fee;
            if ($final_fee < 0) {
                $final_fee = 0;
            }
            $data = array("uniacid" => $_W["uniacid"], "bd_id" => $storebd_user["id"], "trade_no" => date("YmdHis") . random(10, true), "get_fee" => $get_fee, "take_fee" => $take_fee, "final_fee" => $final_fee, "channel" => $channel, "account" => iserializer(array("realname" => $storebd_user["realname"], "openid" => $openid, "avatar" => $storebd_user["avatar"], "nickname" => $storebd_user["nickname"], "openid_wxapp" => $storebd_user["openid_wxapp"])), "status" => 2, "addtime" => TIMESTAMP);
            pdo_insert("tiny_wmall_storebd_getcash_log", $data);
            $getcash_id = pdo_insertid();
            $remark = date("Y-m-d H:i:s") . "申请佣金提现,提现金额" . $get_fee . "元,手续费" . $take_fee . "元,实际到账" . $final_fee . "元";
            $params = array("bd_id" => $storebd_user["id"], "trade_type" => 2, "fee" => 0 - $get_fee, "remark" => $remark, "extra" => $getcash_id);
            storebd_user_credit_update($params);
            $data = sys_notice_storebd_user_getcash($storebd_user["id"], $getcash_id, "apply");
            if (empty($config["fee_getcash"]["get_cash_period"])) {
                imessage(error(0, "申请提现成功,等待平台管理员审核"), "", "ajax");
            } else {
                if ($config["fee_getcash"]["get_cash_period"] == 1) {
                    $transfers = storebd_user_getcash_transfers($getcash_id);
                    imessage($transfers, "", "ajax");
                }
            }
            imessage(error(0, "申请提现成功"), "", "ajax");
        }
        $result = array("config" => $config, "storebd_user" => $storebd_user);
        imessage(error(0, $result), "", "ajax");
    }
}

?>