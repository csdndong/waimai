<?php


defined("IN_IA") or exit("Access Denied");

function icheckstorebduser($force = true)
{
    global $_W;
    global $_GPC;
    icheckauth();
    $storebd_user = storebd_user_fetch($_W["member"]["uid"], "uid");
    $_W["storebd_user"] = array();
    if (!empty($storebd_user)) {
        $_W["storebd_user"] = $storebd_user;
    }
    if (0 < $_W["storebd_user"]["id"]) {
        $bd_id = $_W["storebd_user"]["id"];
        return true;
    }
    if ($force) {
        if (defined("IN_VUE")) {
            $result = array("errno" => 41010, "message" => "您不是店铺推广员，请联系平台进行添加", "sessionid" => $_W["session_id"], "oauthurl" => imurl("system/common/vuesession/userinfo", array("state" => "we7sid-" . $_W["session_id"], "from" => "vue"), true));
            imessage($result, "", "ajax");
        }
        exit;
    }
}
function storebd_user_fetch($value, $field = "id")
{
    global $_W;
    $condition = " WHERE a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    if (in_array($field, array("openid", "mobile", "openid_wxapp", "realname", "token", "nickname"))) {
        $condition .= " and b." . $field . " = :field";
        $params[":field"] = $value;
    } else {
        $condition .= " and a." . $field . " = :field";
        $params[":field"] = $value;
    }
    $storebd_user = pdo_fetch("SELECT a.*, b.nickname,b.avatar, b.realname, b.mobile, b.openid,b.openid_wxapp,b.token,b.salt,b.password FROM " . tablename("tiny_wmall_storebd_user") . " as a left join" . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition, $params);
    if (!empty($storebd_user["realname"])) {
        $storebd_user["title"] = $storebd_user["realname"];
    }
    return $storebd_user;
}
function storebd_user_credit_update($params)
{
    global $_W;
    if (empty($params) || empty($params["bd_id"]) || empty($params["fee"])) {
        return error(-1, "参数有误");
    }
    $storebd_user = pdo_get("tiny_wmall_storebd_user", array("uniacid" => $_W["uniacid"], "id" => $params["bd_id"]), array("id", "credit2"));
    if (empty($storebd_user)) {
        return error(-1, "账户不存在");
    }
    if ($params["trade_type"] == 1 && !empty($extra)) {
        $is_exist = pdo_get("tiny_wmall_storebd_current_log", array("uniacid" => $_W["uniacid"], "bd_id" => $params["bd_id"], "trade_type" => 1, "extra" => $extra), array("id"));
        if ($is_exist) {
            return error(-1, "订单已经入账");
        }
    }
    $now_amount = $storebd_user["credit2"] + $params["fee"];
    pdo_update("tiny_wmall_storebd_user", array("credit2" => $now_amount), array("uniacid" => $_W["uniacid"], "id" => $params["bd_id"]));
    $log = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "bd_id" => $params["bd_id"], "sid" => $params["sid"], "trade_type" => $params["trade_type"], "extra" => $params["extra"], "fee" => $params["fee"], "amount" => $now_amount, "addtime" => TIMESTAMP, "remark" => $params["remark"]);
    pdo_insert("tiny_wmall_storebd_current_log", $log);
    return true;
}
function storebd_user_order_commision($order)
{
    global $_W;
    $storebd = pdo_get("tiny_wmall_storebd_store", array("uniacid" => $_W["uniacid"], "sid" => $order["sid"]), array("bd_id", "fee_takeout"));
    if (empty($storebd["bd_id"])) {
        return error(-1, "门店没有绑定推广员");
    }
    if (2 < $order["order_type"]) {
        $config_commision = iunserializer($storebd["fee_instore"]);
    } else {
        $config_commision = iunserializer($storebd["fee_takeout"]);
    }
    $spread_credit = 0;
    if ($config_commision["type"] == 1) {
        $spread_credit = round($order["plateform_serve_fee"] * $config_commision["fee_rate"] / 100, 2);
        $spread_credit = max($spread_credit, $config_commision["fee_min"]);
    } else {
        if ($config_commision["type"] == 2) {
            $spread_credit = $config_commision["fee"];
        }
    }
    $params = array("bd_id" => $storebd["bd_id"], "sid" => $order["sid"], "trade_type" => 1, "extra" => $order["id"], "fee" => $spread_credit);
    return $params;
}
function storebd_user_commission_stat($bd_id)
{
    global $_W;
    $commission_total = pdo_fetchcolumn("select sum(fee) from" . tablename("tiny_wmall_storebd_current_log") . "where uniacid = :uniacid and bd_id = :bd_id and trade_type = 1", array(":uniacid" => $_W["uniacid"], ":bd_id" => $bd_id));
    $commission_total = round($commission_total, 2);
    $commission_getcash_apply = pdo_fetchcolumn("select sum(get_fee) from" . tablename("tiny_wmall_storebd_getcash_log") . "where uniacid = :uniacid and status = 2 and bd_id = :bd_id", array(":uniacid" => $_W["uniacid"], ":bd_id" => $bd_id));
    $commission_getcash_apply = round($commission_getcash_apply, 2);
    $commission_getcash_success = pdo_fetchcolumn("select sum(get_fee) from" . tablename("tiny_wmall_storebd_getcash_log") . "where uniacid = :uniacid and status = 1 and bd_id = :bd_id", array(":uniacid" => $_W["uniacid"], ":bd_id" => $bd_id));
    $commission_getcash_success = round($commission_getcash_success, 2);
    return array("total" => $commission_total, "getcash_apply" => $commission_getcash_apply, "getcash_success" => $commission_getcash_success);
}
function storebd_trade_types()
{
    $data = array("1" => array("css" => "label label-success", "text" => "订单入账"), "2" => array("css" => "label label-danger", "text" => "申请提现"), "3" => array("css" => "label label-default", "text" => "其他变动"));
    return $data;
}
function storebd_user_getcash_transfers($id)
{
    global $_W;
    $id = intval($id);
    $log = pdo_get("tiny_wmall_storebd_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($log)) {
        return error(-1, "提现记录不存在");
    }
    $log["account"] = iunserializer($log["account"]);
    if (!is_array($log["account"])) {
        $log["account"] = array();
    }
    if ($log["status"] == 1) {
        return error(-1, "该提现记录已处理");
    }
    $storebd_user = storebd_user_fetch($log["bd_id"], "id");
    if (empty($storebd_user) || empty($storebd_user["title"])) {
        return error(-1, "店铺推广员微信信息不完善,无法进行微信付款");
    }
    $params = array("partner_trade_no" => $log["trade_no"], "openid" => $log["account"]["openid"], "check_name" => "FORCE_CHECK", "re_user_name" => $storebd_user["title"], "amount" => $log["final_fee"] * 100, "desc" => (string) $storebd_user["title"] . date("Y-m-d H:i", $log["addtime"]) . "搴楅摵鎺ㄥ箍浣ｉ噾鎻愮幇鐢宠");
    mload()->classs("wxpay");
    if ($log["channel"] == "wxapp") {
        $params["openid"] = $log["account"]["openid_wxapp"];
        if (empty($params["openid"])) {
            return error(-1, "模块版本为小程序版。未获取到店铺推广员针对小程序的openid");
        }
        $pay = new WxPay("wxapp");
    } else {
        if (empty($params["openid"])) {
            return error(-1, "模块版本为公众号版。未获取到店铺推广员针对公众号的openid");
        }
        $pay = new WxPay();
    }
    $response = $pay->mktTransfers($params);
    if (is_error($response)) {
        return error(-1, "打款未成功，等待管理员处理。详细错误信息：" . $response["message"]);
    }
    pdo_update("tiny_wmall_storebd_getcash_log", array("status" => 1, "endtime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "id" => $id));
    sys_notice_storebd_user_getcash($log["bd_id"], $id, "success");
    return error(0, "打款成功");
}
function sys_notice_storebd_user_getcash($bd_id, $getcash_log_id, $type = "apply", $note = "")
{
    global $_W;
    $storebd_user = storebd_user_fetch($bd_id, "id");
    if (empty($storebd_user)) {
        return error(-1, "店铺推广员不存在");
    }
    if ($type != "borrow_openid") {
        $log = pdo_get("tiny_wmall_storebd_getcash_log", array("uniacid" => $_W["uniacid"], "bd_id" => $bd_id, "id" => $getcash_log_id));
        if (empty($log)) {
            return error(-1, "提现记录不存在");
        }
    }
    $acc = WeAccount::create($_W["acid"]);
    if ($type == "apply") {
        if (!empty($storebd_user["openid"])) {
            $tips = "您好,【" . $storebd_user["title"] . "】, 您的账户余额提现申请已提交,请等待管理员审核";
            $remark = array("申请　人: " . $storebd_user["title"], "手机　号: " . $storebd_user["mobile"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"], $note);
            $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($storebd_user["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "店铺推广员申请提现微信通知申请人:" . $storebd_user["title"], $send, $status["message"]);
            }
        }
        $maneger = $_W["we7_wmall"]["config"]["manager"];
        if (!empty($maneger["openid"])) {
            $tips = "您好,【" . $maneger["nickname"] . "】,店铺推广员【" . $storebd_user["title"] . "】申请提现,请尽快处理";
            $remark = array("申请　人: " . $storebd_user["title"], "手机　号: " . $storebd_user["mobile"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"], $note);
            $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($maneger["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "店铺推广员申请提现微信通知平台管理员", $send, $status["message"]);
            }
        }
    } else {
        if ($type == "success") {
            if (empty($storebd_user["openid"])) {
                return error(-1, "店铺推广员信息不完善");
            }
            $tips = "您好,【" . $storebd_user["title"] . "】,您的账户余额提现已处理";
            $remark = array("处理时间: " . date("Y-m-d H:i", $log["endtime"]), "真实姓名: " . $storebd_user["title"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"], "如有疑问请及时联系平台管理人员");
            $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", $log["addtime"]), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($storebd_user["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_success_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "店铺推广员申请提现成功微信通知申请人:" . $storebd_user["title"], $send, $status["message"]);
            }
        } else {
            if ($type == "fail") {
                if (empty($storebd_user["openid"])) {
                    return error(-1, "店铺推广员信息不完善");
                }
                $tips = "您好,【" . $storebd_user["title"] . "】, 您的账户余额提现已处理, 提现未成功";
                $remark = array("处理时间: " . date("Y-m-d H:i", $log["endtime"]), "真实姓名: " . $storebd_user["title"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"], "如有疑问请及时联系平台管理人员");
                $params = array("first" => $tips, "money" => $log["get_fee"], "time" => date("Y-m-d H:i", $log["addtime"]), "remark" => implode("\n", $remark));
                $send = sys_wechat_tpl_format($params);
                $status = $acc->sendTplNotice($storebd_user["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_fail_tpl"], $send);
                if (is_error($status)) {
                    slog("wxtplNotice", "店铺推广员申请提现失败微信通知申请人:" . $storebd_user["title"], $send, $status["message"]);
                }
            } else {
                if ($type == "borrow_openid") {
                    if (empty($storebd_user["openid"])) {
                        return error(-1, "店铺推广员信息不完善");
                    }
                    $tips = "您好,【" . $storebd_user["title"] . "】, 您正在进行提现申请.平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
                    $remark = array("申请　人: " . $storebd_user["title"], "手机　号: " . $storebd_user["mobile"], "请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员");
                    $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
                    $send = sys_wechat_tpl_format($params);
                    $payment_wechat = $_W["we7_wmall"]["config"]["payment"]["wechat"];
                    $url = imurl("wmall/auth/oauth", array("params" => base64_encode(json_encode($payment_wechat[$payment_wechat["type"]]))), true);
                    $status = $acc->sendTplNotice($storebd_user["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send, $url);
                    if (is_error($status)) {
                        slog("wxtplNotice", "微信端店铺推广员申请提现授权微信通知申请人:" . $storebd_user["title"], $send, $status["message"]);
                    }
                } else {
                    if ($type == "cancel") {
                        if (empty($storebd_user["openid"])) {
                            return error(-1, "店铺推广员信息不完善");
                        }
                        $addtime = date("Y-m-d H:i", $log["addtime"]);
                        $tips = "您好,【" . $storebd_user["title"] . "】,您在" . $addtime . "的申请提现已被平台管理员撤销";
                        $remark = array("订单　号: " . $log["trade_no"], "撤销时间: " . date("Y-m-d H:i", $log["endtime"]), "撤销原因: " . $note, "如有疑问请及时联系平台管理人员");
                        $params = array("first" => $tips, "money" => $log["get_fee"], "time" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
                        $send = sys_wechat_tpl_format($params);
                        $status = $acc->sendTplNotice($storebd_user["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_fail_tpl"], $send);
                        if (is_error($status)) {
                            slog("wxtplNotice", "店铺推广员申请提现被平台管理员取消微信通知申请人:" . $storebd_user["title"], $send, $status["message"]);
                        }
                    }
                }
            }
        }
    }
    return $status;
}
function storebd_user_fetchall()
{
    global $_W;
    $bd_user = pdo_fetchall("select a.id, b.avatar, b.nickname, b.realname as title from" . tablename("tiny_wmall_storebd_user") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid where a.uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    foreach ($bd_user as &$val) {
        if (empty($val["title"])) {
            $val["title"] = $val["nickname"];
        }
        $val["avatar"] = tomedia($val["avatar"]);
    }
    return $bd_user;
}

?>