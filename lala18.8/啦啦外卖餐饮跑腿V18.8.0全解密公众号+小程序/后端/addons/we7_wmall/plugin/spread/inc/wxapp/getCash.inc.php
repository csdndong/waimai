<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $condition = " where uniacid = :uniacid and spreadid = :spreadid";
    $params = array(":uniacid" => $_W["uniacid"], ":spreadid" => $_W["member"]["uid"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $id = intval($_GPC["min"]);
    if (0 < $id) {
        $condition .= " and id < :id";
        $params[":id"] = $id;
    }
    $records = pdo_fetchall("select * from " . tablename("tiny_wmall_spread_getcash_log") . $condition . " order by id desc limit 10", $params, "id");
    $min = 0;
    if (!empty($records)) {
        foreach ($records as &$value) {
            $value["addtime"] = date("Y-m-d H:i:s", $value["addtime"]);
        }
        $min = min(array_keys($records));
    }
    $records = array_values($records);
    $respon = array("errno" => 0, "message" => $records, "min" => $min);
    imessage($respon, "", "ajax");
}
if ($op == "application") {
    $config = $_config_plugin["settle"];
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
    $result = array("config" => $config, "member" => $member);
    $status = $_GPC["status"];
    if ($status == 1) {
        $get_fee = floatval($_GPC["fee"]);
        if (empty($get_fee)) {
            imessage(error(-1, "提现金额不能为空"), "", "ajax");
        }
        $channel = trim($_GPC["channel"]);
        if (empty($channel)) {
            imessage(error(-1, "请选择佣金提现渠道"), "", "ajax");
        }
        if (empty($member["openid"]) && empty($member["openid_wxapp"]) || empty($member["realname"])) {
            imessage(error(-1000, "推广员账户信息不完善,无法提现"), "", "ajax");
        }
        $channel_from = "weixin";
        if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
            $channel_from = "wxapp";
            if (empty($member["openid_wxapp"])) {
                imessage(error(-1, "未获取到推广员针对小程序的openid, 你可以尝试进入平台小程序会员中心来解决此问题"), "", "ajax");
            }
        } else {
            $openid = mktTransfers_get_openid($_W["member"]["uid"], $member["openid"], $get_fee, "spread");
            if (is_error($openid)) {
                imessage($openid, "", "ajax");
            }
            if (empty($openid)) {
                imessage(error(-1, "未获取到推广员针对公众号的openid,你可以尝试进入平台公众号会员中心来解决此问题"), "", "ajax");
            }
        }
        if ($get_fee < $config["withdraw"]) {
            imessage(error(-1, "提现金额小于最低提现金额限制"), "", "ajax");
        }
        if ($member["spreadcredit2"] < $get_fee) {
            imessage(error(-1, "提现金额大于账户可用余额"), "", "ajax");
        }
        $take_fee = round($get_fee * $config["withdrawcharge"] / 100, 2);
        $final_fee = $get_fee - $take_fee;
        if ($final_fee < 0) {
            $final_fee = 0;
        }
        $data = array("uniacid" => $_W["uniacid"], "spreadid" => $_W["member"]["uid"], "trade_no" => date("YmdHis") . random(10, true), "get_fee" => $get_fee, "take_fee" => $take_fee, "final_fee" => $final_fee, "channel" => $channel, "account" => iserializer(array("realname" => $member["realname"], "openid" => $openid, "avatar" => $member["avatar"], "nickname" => $member["nickname"], "openid_wxapp" => $member["openid_wxapp"])), "status" => 2, "addtime" => TIMESTAMP, "channel_from" => $channel_from);
        pdo_insert("tiny_wmall_spread_getcash_log", $data);
        $getcash_id = pdo_insertid();
        $remark = date("Y-m-d H:i:s") . "申请佣金提现,提现金额" . $get_fee . "元,手续费" . $take_fee . "元,实际到账" . $final_fee . "元";
        spread_update_credit2($_W["member"]["uid"], 0 - $get_fee, array("trade_type" => 2, "extra" => $getcash_id, "remark" => $remark));
        $data = sys_notice_spread_getcash($_W["member"]["uid"], $getcash_id, "apply");
        imessage(error(0, "申请提现成功"), "", "ajax");
    }
    imessage(error(0, $result), "", "ajax");
}

?>