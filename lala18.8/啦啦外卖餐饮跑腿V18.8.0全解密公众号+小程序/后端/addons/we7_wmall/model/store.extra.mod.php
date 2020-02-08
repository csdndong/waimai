<?php
defined("IN_IA") or exit("Access Denied");
function store_getcash_update($logOrId, $type, $extra = array())
{
    global $_W;
    $log = $logOrId;
    if (!is_array($log)) {
        $log = pdo_get("tiny_wmall_store_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $log));
    }
    if (empty($log)) {
        return error(-1, "提现记录不存在");
    }
    if ($type == "transfers") {
        if ($log["status"] == 1) {
            return error(-1, "该提现记录已处理");
        }
        mload()->model("store");
        $store = store_fetch($log["sid"], array("title"));
        $log["account"] = iunserializer($log["account"]);
        $params = array();
        if ($log["channel"] == "weixin" || $log["channel"] == "wxapp") {
            mload()->classs("wxpay");
            if ($log["channel"] == "wxapp") {
            //$pay = new WxPay("wxapp");
            if (empty($log["account"]["openid_wxapp"])) {
                return error(-1, "模块版本为小程序版。未获取到提现账户针对小程序的openid,请重新编辑提现账户");
            }
                $pay = new WxPay("wxapp");
        } else {
            $pay = new WxPay();
        }
        $params = array("partner_trade_no" => $log["trade_no"], "openid" => $log["channel"] == "wxapp" ? $log["account"]["openid_wxapp"] : $log["account"]["openid"], "check_name" => "FORCE_CHECK", "re_user_name" => $log["account"]["realname"], "amount" => $log["final_fee"] * 100, "desc" => (string) $store["title"] . date("Y-m-d H:i", $log["addtime"]) . "提现申请");
        $response = $pay->mktTransfers($params);
        } else {
            if ($log["channel"] == "alipay") {
                mload()->classs("alipay");
                $pay = new AliPay();
                $params = array("out_biz_no" => $log["trade_no"], "payee_account" => $log["account"]["account"], "amount" => $log["final_fee"], "payee_real_name" => $log["account"]["realname"], "remark" => (string) $store["title"] . date("Y-m-d H:i", $log["addtime"]) . "鎻愮幇鐢宠");
                $response = $pay->transfer($params);
            } else {
                if ($log["channel"] == "bank") {
                    mload()->classs("wxpay");
                    $pay = new WxPay();
                    $params = array("partner_trade_no" => $log["trade_no"], "enc_bank_no" => $log["account"]["account"], "enc_true_name" => $log["account"]["realname"], "bank_code" => $log["account"]["id"], "amount" => $log["final_fee"] * 100, "desc" => (string) $store["title"] . date("Y-m-d H:i", $log["addtime"]) . "鎻愮幇鐢宠");
                    $response = $pay->mktPayBank($params);
                }
            }
        }
        if (is_error($response)) {
            pdo_update("tiny_wmall_store_getcash_log", array("status" => 2), array("id" => $log["id"]));
            mlog(2007, $log["id"], "打款未成功。详细错误信息：" . $response["message"]);
	    return error(-1, "打款未成功，等待管理员审核。详细错误信息：" . $response["message"]);
        }
        $update = array("status" => 1, "endtime" => TIMESTAMP, "toaccount_status" => 1);
        if ($log["channel"] == "weixin" || $log["channel"] == "wxapp" || $log["channel"] == "alipay") {
            $update["toaccount_status"] = 2;
        }
        pdo_update("tiny_wmall_store_getcash_log", $update, array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_store_getcash($log["sid"], $log["id"], "success");
        mlog(2007, $log["id"], "打款成功");
	return error(0, "打款成功");
    }
    if ($type == "cancel") {
        if ($log["status"] == 1 && $log["toaccount_status"] == 2) {
            return error(-1, "本次提现已成功,无法撤销");
        }
        if ($log["status"] == 3) {
            return error(-1, "本次提现已撤销");
        }
        $remark = trim($extra["remark"]);
        mload()->model("store");
        store_update_account($log["sid"], $log["get_fee"], 3, "", $remark);
        pdo_update("tiny_wmall_store_getcash_log", array("status" => 3, "endtime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_store_getcash($log["sid"], $log["id"], "cancel", $remark);
        mlog(2006, $log["id"], $remark);
	return error(0, "提现撤销成功");
    }
    if ($type == "status") {
        $status = intval($extra["status"]);
        if ($log["status"] == $status) {
            return error(-1, "该提现记录已处理");
        }
        $update = array("status" => $status, "endtime" => TIMESTAMP);
        if ($status == 1) {
            $update["toaccount_status"] = 2;
        }
        pdo_update("tiny_wmall_store_getcash_log", $update, array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_store_getcash($log["sid"], $log["id"], "success");
        mlog(2008, $log["id"]);
	return error(0, "设置提现状态成功");
    }
    if ($type == "query") {
        if ($log["status"] == 2) {
            return error(-1, "该提现正在申请中，请等待管理员审核");
        }
        if ($log["status"] == 3) {
            return error(-1, "该提现申请已撤销");
        }
        if ($log["channel"] != "bank" || $log["toaccount_status"] == 2) {
            return error(0, "该提现已成功到账");
        }
        if ($log["toaccount_status"] == 3) {
            return error(-1, "该提现已失败，请联系商户处理");
        }
        $params = array("partner_trade_no" => $log["trade_no"]);
        mload()->classs("wxpay");
        $pay = new WxPay();
        $response = $pay->mktQueryBank($params);
        if (is_error($response)) {
            return $response;
        }
        $result = $response["message"];
        if (in_array($result["status"], array("SUCCESS", "FAILED", "BANK_FAIL"))) {
            $toaccount_status = $result["toaccount_status"];
            pdo_update("tiny_wmall_store_getcash_log", array("toaccount_status" => $toaccount_status), array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        }
        return error($result["errno"], $result["msg"]);
    }
}

?>