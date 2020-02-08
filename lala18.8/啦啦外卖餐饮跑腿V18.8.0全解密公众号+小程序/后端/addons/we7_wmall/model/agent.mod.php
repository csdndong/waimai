<?php

defined("IN_IA") or exit("Access Denied");

function get_agents($status = -1)
{
    global $_W;
    $condition = array("uniacid" => $_W["uniacid"]);
    if (-1 < $status) {
        $condition["status"] = $status;
    }
    $agents = pdo_getall("tiny_wmall_agent", $condition, array("id", "title", "area"), "id");
    return $agents;
}
function get_agent($id = 0, $fields = "*")
{
    global $_W;
    if (is_array($fields)) {
        $fields = implode(",", $fields);
    }
    if (empty($id)) {
        $id = $_W["agentid"];
    }
    $agent = pdo_fetch("select " . $fields . " from " . tablename("tiny_wmall_agent") . " where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $id));
    if (empty($agent)) {
        return array();
    }
    foreach ($agent as $key => $val) {
        if (in_array($key, array("fee", "account", "pluginset", "sysset", "geofence"))) {
            $agent[$key] = iunserializer($val);
        }
    }
    return $agent;
}
function toagent($id)
{
    global $_W;
    $agents = $_W["agents"];
    if (empty($agents)) {
        $agents = get_agents();
    }
    if (empty($agents[$id])) {
        return "未知";
    }
    return $agents[$id]["area"];
}
function agent_serve_fee_items()
{
    return array("yes" => array("price" => "商品费用", "box_price" => "餐盒费", "pack_fee" => "包装费", "delivery_fee" => "配送费"), "no" => array("store_discount_fee" => "商户活动补贴", "agent_discount_fee" => "代理商活动补贴"));
}
function agent_update_account($id, $fee, $trade_type, $extra, $remark = "", $order_type = "takeout")
{
    global $_W;
    $agent = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $id), array("id", "amount"));
    if (empty($agent)) {
        return error(-1, "账户不存在");
    }
    $hash = md5((string) $_W["uniacid"] . "-" . $id . "-" . $trade_type . "-" . $extra . "-" . $order_type);
    if ($trade_type == 2 || $trade_type == 3) {
        $hash = md5((string) $_W["uniacid"] . "-" . $id . "-" . $trade_type . "-" . $fee . TIMESTAMP);
    }
    $now_amount = $agent["amount"] + $fee;
    $log = array("uniacid" => $_W["uniacid"], "agentid" => $id, "trade_type" => $trade_type, "order_type" => $order_type, "extra" => $extra, "fee" => $fee, "amount" => $now_amount, "addtime" => TIMESTAMP, "hash" => $hash, "remark" => $remark);
    pdo_insert("tiny_wmall_agent_current_log", $log);
    $id = pdo_insertid();
    if ($trade_type == 3) {
        mlog(5002, $id, $remark);
    }
    if (!empty($id)) {
        $status = pdo_update("tiny_wmall_agent", array("amount" => $now_amount), array("uniacid" => $_W["uniacid"], "id" => $agent["id"]));
        if ($status === false) {
            $agent_new = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $id), array("id", "amount"));
            slog("agentaccount", "代理账户变动失败", array(), "代理id:" . $agent["id"] . ",变动前金额:" . $agent["amount"] . ",金额变动:" . $fee . ",变动后金额:" . $now_amount . ",实际变动后金额:" . $agent_new["amount"]);
        }
    }
    return true;
}
function get_location_agent($location_x, $location_y)
{
    global $_W;
    $agentid = -1;
    $agents = pdo_getall("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "status" => 1), array("id", "geofence"));
    if (!empty($agents)) {
        foreach ($agents as $agent) {
            if (0 < $agentid) {
                break;
            }
            if (empty($agent)) {
                continue;
            }
            $agent["geofence"] = iunserializer($agent["geofence"]);
            if (!is_array($agent["geofence"]) || !is_array($agent["geofence"]["areas"])) {
                continue;
            }
            foreach ($agent["geofence"]["areas"] as $area) {
                if (!is_array($area["path"])) {
                    continue;
                }
                $flag = isPointInPolygon($area["path"], array($location_y, $location_x));
                if ($flag) {
                    $agentid = $agent["id"];
                    break;
                }
            }
        }
    }
    return $agentid;
}
function agent_area()
{
    global $_W;
    $initials = pdo_fetchall("select distinct(initial) from " . tablename("tiny_wmall_agent") . " where uniacid = :uniacid and status = 1 order by initial", array(":uniacid" => $_W["uniacid"]));
    $agents = pdo_fetchall("select id,title,area,initial from " . tablename("tiny_wmall_agent") . " where uniacid = :uniacid and status = 1 order by displayorder desc", array(":uniacid" => $_W["uniacid"]));
    if (!empty($initials)) {
        foreach ($initials as &$row) {
            foreach ($agents as $val) {
                if ($row["initial"] == $val["initial"]) {
                    $row["agent"][] = $val;
                }
            }
        }
    }
    return $initials;
}
function agent_getcash_update($logOrId, $type, $extra = array())
{
    global $_W;
    $log = $logOrId;
    if (!is_array($log)) {
        $log = pdo_get("tiny_wmall_agent_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $log));
    }
    if (empty($log)) {
        return error(-1, "提现记录不存在");
    }
    if ($type == "transfers") {
        if ($log["status"] == 1) {
            return error(-1, "该提现记录已处理");
        }
        if ($log["status"] == 3) {
            return error(-1, "本次提现已撤销");
        }
        $agent = pdo_get("tiny_wmall_agent", array("uniacid" => $_W["uniacid"], "id" => $log["agentid"]), array("title"));
        $log["account"] = iunserializer($log["account"]);
        if ($log["channel"] == "weixin" || $log["channel"] == "wxapp") {
        mload()->classs("wxpay");
        if ($log["channel"] == "wxapp") {
            $params["openid"] = $log["account"]["openid_wxapp"];
                if (empty($log["account"]["openid_wxapp"])) {
                    return error(-1, "模块版本为小程序版。未获取到代理针对小程序的openid");
                }
                $pay = new WxPay("wxapp");
            } else {
                $pay = new WxPay();
            }
            $params = array("partner_trade_no" => $log["trade_no"], "openid" => $log["channel"] == "wxapp" ? $log["account"]["openid_wxapp"] : $log["account"]["openid"], "check_name" => "FORCE_CHECK", "re_user_name" => $log["account"]["realname"], "amount" => $log["final_fee"] * 100, "desc" => (string) $agent["title"] . date("Y-m-d H:i", $log["addtime"]) . "提现申请");
            $response = $pay->mktTransfers($params);
        } else {
            if ($log["channel"] == "alipay") {
                mload()->classs("alipay");
                $pay = new AliPay();
                $params = array("out_biz_no" => $log["trade_no"], "payee_account" => $log["account"]["account"], "amount" => $log["final_fee"], "payee_real_name" => $log["account"]["realname"], "remark" => (string) $agent["title"] . date("Y-m-d H:i", $log["addtime"]) . "提现申请");
                $response = $pay->transfer($params);
            } else {
                if ($log["channel"] == "bank") {
                    mload()->classs("wxpay");
                    $pay = new WxPay();
                    $params = array("partner_trade_no" => $log["trade_no"], "enc_bank_no" => $log["account"]["account"], "enc_true_name" => $log["account"]["realname"], "bank_code" => $log["account"]["id"], "amount" => $log["final_fee"] * 100, "desc" => (string) $agent["title"] . date("Y-m-d H:i", $log["addtime"]) . "提现申请");
                    $response = $pay->mktPayBank($params);
                }
            }
        }
        if (is_error($response)) {
            pdo_update("tiny_wmall_agent_getcash_log", array("status" => 2), array("id" => $log["id"]));
            mlog(5004, $log["id"], "打款失败，错误详情：" . $response["message"]);
            return error(-1, "打款失败，错误详情：" . $response["message"]);
        }
        $update = array("status" => 1, "endtime" => TIMESTAMP, "toaccount_status" => 1);
        if ($log["channel"] == "weixin" || $log["channel"] == "wxapp" || $log["channel"] == "alipay") {
            $update["toaccount_status"] = 2;
        }
        pdo_update("tiny_wmall_agent_getcash_log", $update, array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_agent_getcash($log["agentid"], $log["id"], "success");
        mlog(5004, $log["id"], "打款成功");
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
        pdo_update("tiny_wmall_agent_getcash_log", array("status" => 3, "endtime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        agent_update_account($log["agentid"], $log["get_fee"], 3, "", $remark);
        sys_notice_agent_getcash($log["agentid"], $log["id"], "cancel", $remark);
        mlog(5003, $log["id"], $remark);
	return error(0, "提现撤销成功");
    }
    if ($type == "status") {
        if ($log["status"] == 1) {
            return error(-1, "该提现记录已处理");
        }
        $status = intval($extra["status"]);
        $update = array("status" => $status, "endtime" => TIMESTAMP);
        if ($status == 1) {
            $update["toaccount_status"] = 2;
        }
        pdo_update("tiny_wmall_agent_getcash_log", $update, array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        sys_notice_agent_getcash($log["agentid"], $log["id"], "success");
        mlog(5005, $log["id"]);
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
            return error(-1, "该提现已失败，请联系管理员处理");
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
            pdo_update("tiny_wmall_agent_getcash_log", array("toaccount_status" => $result["toaccount_status"]), array("uniacid" => $_W["uniacid"], "id" => $log["id"]));
        }
        return error($result["errno"], $result["msg"]);
    }
}
function sys_notice_agent_getcash($agentid, $getcash_log_id, $type = "apply", $note = "")
{
    global $_W;
    $agent = get_agent($agentid, array("id", "title", "account"));
    if (empty($agent)) {
        return error(-1, "代理不存在");
    }
    if ($type != "borrow_openid") {
        $log = pdo_get("tiny_wmall_agent_getcash_log", array("uniacid" => $_W["uniacid"], "agentid" => $agentid, "id" => $getcash_log_id));
        if (empty($log)) {
            return error(-1, "提现记录不存在");
        }
    }
    $acc = WeAccount::create($_W["acid"]);
    if ($type == "apply") {
        mlog(5006, $getcash_log_id);
        if (!empty($agent["account"]) && !empty($agent["account"]["openid"])) {
            $tips = "您好,【" . $agent["account"]["nickname"] . "】,【" . $agent["title"] . "】账户余额提现申请已提交,请等待管理员审核";
            $remark = array("申请代理: " . $agent["title"], "账户类型: 微信", "真实姓名: " . $agent["account"]["realname"], $note);
            $params = array("first" => $tips, "money" => $log["final_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($agent["account"]["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "代理申请提现微信通知申请人", $send, $status["message"]);
            }
        }
        $maneger = $_W["we7_wmall"]["config"]["manager_plateform"];
        if (!empty($maneger["openid"])) {
            $tips = "您好,【" . $maneger["nickname"] . "】,代理【" . $agent["title"] . "】申请提现,请尽快处理";
            $remark = array("申请代理: " . $agent["title"], "账户类型: 微信", "真实姓名: " . $agent["account"]["realname"], "提现总金额: " . $log["get_fee"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"], $note);
            $params = array("first" => $tips, "money" => $log["final_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($maneger["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "代理申请提现微信通知平台管理员", $send, $status["message"]);
            }
        }
    } else {
        if ($type == "success") {
            if (empty($agent["account"]) || empty($agent["account"]["openid"])) {
                return error(-1, "代理提现账户信息不完善");
            }
            $tips = "您好,【" . $agent["account"]["nickname"] . "】,【" . $agent["title"] . "】账户余额提现已处理";
            $remark = array("处理时间: " . date("Y-m-d H:i", $log["endtime"]), "申请代理: " . $agent["title"], "账户类型: 微信", "真实姓名: " . $agent["account"]["realname"], "如有疑问请及时联系平台管理人员");
            $params = array("first" => $tips, "money" => $log["final_fee"], "timet" => date("Y-m-d H:i", $log["addtime"]), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($agent["account"]["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_success_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "代理申请提现成功微信通知申请人", $send, $status["message"]);
            }
        } else {
            if ($type == "fail") {
                if (empty($agent["account"]) || empty($agent["account"]["openid"])) {
                    return error(-1, "代理提现账户信息不完善");
                }
                $tips = "您好,【" . $agent["account"]["nickname"] . "】, 【" . $agent["title"] . "】账户余额提现已处理, 提现未成功";
                $remark = array("处理时间: " . date("Y-m-d H:i", $log["endtime"]), "申请代理: " . $agent["title"], "账户类型: 微信", "真实姓名: " . $agent["account"]["realname"], "如有疑问请及时联系平台管理人员");
                $params = array("first" => $tips, "money" => $log["final_fee"], "time" => date("Y-m-d H:i", $log["addtime"]), "remark" => implode("\n", $remark));
                $send = sys_wechat_tpl_format($params);
                $status = $acc->sendTplNotice($agent["account"]["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_fail_tpl"], $send);
                if (is_error($status)) {
                    slog("wxtplNotice", "代理申请提现失败微信通知申请人", $send, $status["message"]);
                }
            } else {
                if ($type == "borrow_openid") {
                    $agent["account"] = $agent["account"]["wechat"];
                    if (empty($agent["account"]) || empty($agent["account"]["openid"])) {
                        return error(-1, "代理提现账户信息不完善");
                    }
                    $tips = "您好,【" . $agent["account"]["nickname"] . "】, 您正在进行代理【" . $agent["title"] . "】的提现申请。平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
                    $remark = array("申请代理: " . $agent["title"], "账户类型: 微信", "请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员");
                    $params = array("first" => $tips, "money" => $getcash_log_id, "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
                    $send = sys_wechat_tpl_format($params);
                    $payment_wechat = $_W["we7_wmall"]["config"]["payment"]["wechat"];
                    $url = imurl("wmall/auth/oauth", array("params" => base64_encode(json_encode($payment_wechat[$payment_wechat["type"]]))), true);
                    $status = $acc->sendTplNotice($agent["account"]["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send, $url);
                    if (is_error($status)) {
                        slog("wxtplNotice", "微信端代理申请提现授权微信通知申请人", $send, $status["message"]);
                    }
                } else {
                    if ($type == "cancel") {
                        if (empty($agent["account"]) || empty($agent["account"]["openid"])) {
                            return error(-1, "代理提现账户信息不完善");
                        }
                        $addtime = date("Y-m-d H:i", $log["addtime"]);
                        $tips = "您好,【" . $agent["account"]["nickname"] . "】,【" . $agent["title"] . "】在" . $addtime . "的申请提现已被平台管理员撤销";
                        $remark = array("订单　号: " . $log["trade_no"], "申请代理: " . $agent["title"], "撤销时间: " . date("Y-m-d H:i", $log["endtime"]), "撤销原因: " . $note, "如有疑问请及时联系平台管理人员");
                        $params = array("first" => $tips, "money" => $log["get_fee"], "time" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
                        $send = sys_wechat_tpl_format($params);
                        $status = $acc->sendTplNotice($agent["account"]["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_fail_tpl"], $send);
                        if (is_error($status)) {
                            slog("wxtplNotice", "代理申请提现被平台管理员取消微信通知申请人", $send, $status["message"]);
                        }
                    }
                }
            }
        }
    }
    return $status;
}
function update_store_agent($storeid, $agentid)
{
    global $_W;
    $tables = array("tiny_wmall_store_current_log", "tiny_wmall_store_account", "tiny_wmall_store_getcash_log", "tiny_wmall_activity_bargain", "tiny_wmall_activity_bargain_goods");
    $tables[] = "tiny_wmall_store";
    $key = "sid";
    foreach ($tables as $table) {
        if ($table == "tiny_wmall_store") {
            $key = "id";
        }
        pdo_update($table, array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], $key => $storeid));
    }
    return true;
}
function update_deliveryer_agent($deliveryerid, $agentid)
{
    global $_W;
    $tables = array("tiny_wmall_deliveryer_current_log", "tiny_wmall_deliveryer_getcash_log", "tiny_wmall_store_deliveryer");
    $tables[] = "tiny_wmall_deliveryer";
    $key = "deliveryer_id";
    foreach ($tables as $table) {
        if ($table == "tiny_wmall_deliveryer") {
            $key = "id";
        }
        pdo_update($table, array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], $key => $deliveryerid));
    }
    return true;
}
function get_agent_perms($justkey = true, $from = "app")
{
    $all_perms = array("dashboard" => array("title" => "概括", "perms" => array("dashboard.index" => "运营概况", "dashboard.ad" => "全屏引导页", "dashboard.slide" => "幻灯片", "dashboard.nav" => "导航图标", "dashboard.notice" => "公告", "dashboard.cube" => "图片魔方")), "order" => array("title" => "订单", "perms" => array("order.takeout" => "外卖", "order.takeoutNew" => "未完成", "order.distribute" => "订单分布", "order.neworder" => "代客下单", "order.dispatch" => "调度中心-待指派", "order.records" => "调度中心-接单统计/接单记录", "order.tangshi" => "店内")), "paycenter" => array("title" => "当面付", "perms" => array("paycenter.paybill" => "买单订单")), "statcenter" => array("title" => "数据", "perms" => array("statcenter.takeout" => "外卖统计", "statcenter.paytype" => "支付方式统计", "statcenter.takeoutOrder" => "店铺订单统计", "statcenter.takeoutOrderChannel" => "订单来源统计", "statcenter.delivery" => "配送统计/配送详情", "statcenter.hot" => "热门商品", "statcenter.finance" => "财务统计")), "merchant" => array("title" => "商户", "perms" => array("merchant.store" => "商户列表", "merchant.account" => "商户账户", "merchant.activity" => "商户活动/活动展示", "merchant.getcash" => "申请提现", "merchant.current" => "账户明细", "merchant.settle" => "入驻", "merchant.storage" => "商家回收站", "merchant.newsCategory" => "资讯分类", "merchant.news" => "资讯列表", "merchant.ad" => "广告", "merchant.notice" => "公告列表", "merchant.report" => "投诉列表")), "service" => array("title" => "售后", "perms" => array("service.comment" => "用户评价")), "deliveryer" => array("title" => "配送员", "perms" => array("deliveryer.plateform" => "配送员", "deliveryer.getcash" => "提现申请", "deliveryer.current" => "账户明细", "deliveryer.comment" => "配送评价", "deliveryer.storage" => "配送员回收站", "deliveryer.cover" => "注册&登录")), "clerk" => array("title" => "店员", "perms" => array("clerk.account" => "店员列表", "clerk.cover" => "注册&登录")));
    if (check_plugin_perm("errander")) {
        $all_perms["errander"] = array("title" => "跑腿", "perms" => array("errander.order" => "订单", "errander.statcenter" => "跑腿统计", "errander.statDelivery" => "配送统计/配送详情", "errander.diypage" => "首页设置/跑腿首页跑腿场景", "errander.config" => "跑腿设置", "errander.cover" => "入口设置"));
    }
    if (check_plugin_perm("diypage")) {
        $all_perms["diypage"] = array("title" => "平台装修", "perms" => array("diypage.diyPage" => "自定义DIY", "diypage.vuediyShop" => "页面设置", "diypage.template" => "模板管理"));
    }
    if (check_plugin_perm("gohome")) {
        $all_perms["gohome"] = array("title" => "啦啦生活圈", "perms" => array("gohome.slide" => "生活圈幻灯片", "gohome.nav" => "生活圈导航图标", "gohome.notice" => "生活圈公告", "gohome.order" => "订单列表", "gohome.statcenter" => "数据统计", "kanjia.category" => "砍价活动分类", "kanjia.activity" => "砍价活动列表", "pintuan.category" => "拼团活动分类", "pintuan.activity" => "拼团活动列表", "seckill.goods_category" => "抢购活动分类", "seckill.goods" => "抢购活动列表", "gohome.comment" => "订单评论", "gohome.complain" => "投诉列表", "gohome.memberBlack" => "黑名单", "gohome.config" => "活动设置/费率设置", "gohome.poster" => "活动海报", "gohome.cover" => "活动入口", "tongcheng.category" => "同城分类列表", "tongcheng.basic" => "同城设置", "tongcheng.slide" => "同城幻灯", "tongcheng.information" => "同城帖子", "tongcheng.comment" => "同城评论列表", "haodian.settle" => "好店入驻设置", "haodian.slide" => "好店幻灯", "haodian.category" => "好店商户分类", "haodian.store" => "好店商户列表"));
    }
    if (check_plugin_perm("zhunshibao")) {
        $all_perms["zhunshibao"] = array("title" => "准时宝", "perms" => array("zhunshibao.config" => "准时宝设置"));
    }
    if ($from == "app") {
        $all_perms["agent.config"] = array("title" => "设置", "perms" => array("agent.config.mall" => "基础设置/分享及关注/平台状态/oAuth设置", "agent.config.takeout" => "服务范围/订单相关", "agent.config.store" => "配送模式/服务费率/商户入驻/其他批量操作", "agent.config.deliveryer" => "配送员申请/提成及提现"));
        $all_perms["agent.finance"] = array("title" => "财务", "perms" => array("agent.finance.getcash" => "提现"));
        $all_perms["agent"] = array("title" => "代理", "perms" => array("agent.agent" => "代理", "agent.getcash" => "申请提现", "agent.current" => "账户明细"));
    } else {
        if ($from == "web") {
            $all_perms["config"] = array("title" => "设置", "perms" => array("config.mall" => "基础设置/分享及关注/平台状态/oAuth设置", "config.takeout" => "服务范围/订单相关", "config.store" => "配送模式/服务费率/商户入驻/其他批量操作", "config.deliveryer" => "配送员申请/提成及提现"));
            $all_perms["finance"] = array("title" => "财务", "perms" => array("finance.getcash" => "提现/提现账户", "finance.account" => "账户明细", "finance.order" => "订单入账"));
        }
    }
    if ($justkey) {
        $perms = array();
        foreach ($all_perms as $key => $item) {
            $perms[] = $key;
            if (!empty($item["perms"])) {
                foreach ($item["perms"] as $key1 => $item1) {
                    $perms[] = $key1;
                }
            }
        }
        return $perms;
    } else {
        return $all_perms;
    }
}

?>