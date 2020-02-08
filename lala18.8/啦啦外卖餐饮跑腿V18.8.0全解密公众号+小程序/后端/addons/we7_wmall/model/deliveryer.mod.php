<?php


defined("IN_IA") or exit("Access Denied");
function icheckdeliveryer()
{
    global $_W;
    global $_GPC;
    $_W["deliveryer"] = array();
    if (is_weixin() && !defined("IN_WXAPP")) {
        if (!empty($_W["openid"])) {
            $deliveryer = deliveryer_fetch($_W["openid"], "openid");
        }
    } else {
        if (defined("IN_WXAPP") || defined("IN_VUE")) {
            $token = trim($_GPC["token"]);
            if (!empty($token)) {
                $deliveryer = deliveryer_fetch($token, "token");
                if (!empty($deliveryer) && empty($deliveryer["openid_wxapp_deliveryer"])) {
                    $oauth = pdo_get("tiny_wmall_oauth_fans", array("openid" => $token), array("oauth_openid"));
                    if (!empty($oauth["oauth_openid"])) {
                        pdo_update("tiny_wmall_deliveryer", array("openid_wxapp_deliveryer" => $oauth["oauth_openid"]), array("uniacid" => $_W["uniacid"], "id" => $deliveryer["id"]));
                        $deliveryer["openid_wxapp_deliveryer"] = $oauth["oauth_openid"];
                    }
                }
            }
        }
    }
    if (!empty($deliveryer)) {
        $_W["deliveryer"] = $deliveryer;
    }
    if (empty($_W["deliveryer"])) {
        $key = "we7_wmall_deliveryer_session_" . $_W["uniacid"];
        if (isset($_GPC[$key])) {
            $session = json_decode(base64_decode($_GPC[$key]), true);
            if (is_array($session)) {
                $deliveryer = deliveryer_fetch($session["id"], "id");
                if (is_array($deliveryer) && $session["hash"] == $deliveryer["password"]) {
                    $_W["deliveryer"] = $deliveryer;
                } else {
                    isetcookie($key, false, -100);
                }
            } else {
                isetcookie($key, false, -100);
            }
        }
    }
    if (!empty($_W["deliveryer"])) {
        if (empty($_W["deliveryer"]["openid_wxapp"]) && !empty($_W["deliveryer"]["openid"])) {
            $openid_wxapp = member_openid2wxapp($_W["deliveryer"]["openid"]);
            if (!empty($openid_wxapp)) {
                $_W["deliveryer"]["openid_wxapp"] = $openid_wxapp;
                pdo_update("tiny_wmall_deliveryer", array("openid_wxapp" => $openid_wxapp), array("id" => $_W["deliveryer"]["id"]));
            }
        }
        if (!empty($_W["deliveryer"]["openid_wxapp"]) && empty($_W["deliveryer"]["openid"])) {
            $openid = member_wxapp2openid($_W["deliveryer"]["openid_wxapp"]);
            if (!empty($openid)) {
                $_W["deliveryer"]["openid"] = $openid;
                pdo_update("tiny_wmall_deliveryer", array("openid" => $openid), array("id" => $_W["deliveryer"]["id"]));
            }
        }
        $_W["openid"] = $_W["deliveryer"]["openid"];
        $_W["openid_wxapp"] = $_W["deliveryer"]["openid_wxapp"];
        $sids = pdo_fetchall("select sid from " . tablename("tiny_wmall_store_deliveryer") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and sid > 0", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_W["deliveryer"]["id"]), "sid");
        $_W["deliveryer"]["sids"] = array_unique(array_keys($sids));
        $_W["deliveryer"]["sids_sn"] = implode(",", $_W["deliveryer"]["sids"]);
        $_W["deliveryer"]["perm_takeout"] = $_W["deliveryer"]["is_takeout"];
        if (!empty($_W["deliveryer"]["sids"])) {
            $_W["deliveryer"]["perm_takeout"] = 2;
            if ($_W["deliveryer"]["is_takeout"] == 1) {
                $_W["deliveryer"]["perm_takeout"] = 3;
            }
        } else {
            $_W["deliveryer"]["sids_sn"] = "0";
        }
        $_W["deliveryer"]["perm_errander"] = $_W["deliveryer"]["is_errander"];
        $_W["deliveryer"]["perm_plateform"] = $_W["deliveryer"]["is_errander"] || $_W["deliveryer"]["is_takeout"];
        return true;
    }
    if (defined("IN_WXAPP") || defined("IN_VUE")) {
        imessage(error(41009, "请先登录"), "", "ajax");
    } else {
        if ($_W["ispost"]) {
            imessage(error(-1, "请先登录"), imurl("delivery/auth/login", array("force" => 1)), "ajax");
        }
        header("location: " . imurl("delivery/auth/login", array("force" => 1)), true);
        exit;
    }
}
function deliveryer_all($force_update = false)
{
    global $_W;
    $cache_key = "we7_wmall:deliveryers:" . $_W["uniacid"] . ":" . $_W["agentid"];
    $data = cache_read($cache_key);
    if (!empty($data) && !$force_update) {
        return $data;
    }
    $condition = " where uniacid = :uniacid and status = :status";
    $params = array(":uniacid" => $_W["uniacid"], ":status" => 1);
    if (0 < $_W["agentid"]) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    $deliveryers = pdo_fetchall("select * from " . tablename("tiny_wmall_deliveryer") . $condition, $params, "id");
    cache_write($cache_key, $deliveryers);
    return $deliveryers;
}
function deliveryer_fetch($value, $field = "id")
{
    global $_W;
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "status" => 1, $field => trim($value)));
    if (!empty($deliveryer)) {
        if (empty($deliveryer["token"])) {
            $deliveryer["token"] = random(32);
            pdo_update("tiny_wmall_deliveryer", array("token" => $deliveryer["token"]), array("id" => $deliveryer["id"]));
        }
        if (empty($deliveryer["openid_wxapp"]) && !empty($deliveryer["openid"])) {
            mload()->model("member");
            $openid_wxapp = member_openid2wxapp($deliveryer["openid"]);
            if (!empty($openid_wxapp)) {
                $deliveryer["openid_wxapp"] = $openid_wxapp;
                pdo_update("tiny_wmall_deliveryer", array("openid_wxapp" => $openid_wxapp), array("id" => $deliveryer["id"]));
            }
        }
        if (!empty($deliveryer["openid_wxapp"]) && empty($deliveryer["openid"])) {
            mload()->model("member");
            $openid = member_wxapp2openid($deliveryer["openid_wxapp"]);
            if (!empty($openid)) {
                $deliveryer["openid"] = $openid;
                pdo_update("tiny_wmall_deliveryer", array("openid" => $openid), array("id" => $deliveryer["id"]));
            }
        }
        $deliveryer["extra"] = iunserializer($deliveryer["extra"]);
        $deliveryer["perm_transfer"] = iunserializer($deliveryer["perm_transfer"]);
        $deliveryer["perm_cancel"] = iunserializer($deliveryer["perm_cancel"]);
        $deliveryer["fee_delivery"] = iunserializer($deliveryer["fee_delivery"]);
        $deliveryer["fee_getcash"] = iunserializer($deliveryer["fee_getcash"]);
        $deliveryer["account"] = iunserializer($deliveryer["account"]);
        $tips = array("休息中", "接单中");
        $deliveryer["work_status_cn"] = $tips[$deliveryer["work_status"]];
    }
    return $deliveryer;
}
function deliveryer_order_stat($sid, $deliveryer_id)
{
    global $_W;
    $stat = array();
    $today_starttime = strtotime(date("Y-m-d"));
    $yesterday_starttime = $today_starttime - 86400;
    $month_starttime = strtotime(date("Y-m"));
    $stat["yesterday_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime and addtime <= :endtime", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":deliveryer_id" => $deliveryer_id, ":starttime" => $yesterday_starttime, ":endtime" => $today_starttime)));
    $stat["today_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":deliveryer_id" => $deliveryer_id, ":starttime" => $today_starttime)));
    $stat["month_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5 and addtime >= :starttime", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":deliveryer_id" => $deliveryer_id, ":starttime" => $month_starttime)));
    $stat["total_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and deliveryer_id = :deliveryer_id and delivery_type = 1 and status =5", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":deliveryer_id" => $deliveryer_id)));
    return $stat;
}
function deliveryer_plateform_order_stat($deliveryer_id)
{
    global $_W;
    $stat = array();
    $today_starttime = strtotime(date("Y-m-d"));
    $yesterday_starttime = $today_starttime - 86400;
    $month_starttime = strtotime(date("Y-m"));
    $stat["yesterday_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime and addtime <= :endtime", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id, ":starttime" => $yesterday_starttime, ":endtime" => $today_starttime)));
    $stat["today_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id, ":starttime" => $today_starttime)));
    $stat["month_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5 and addtime >= :starttime", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id, ":starttime" => $month_starttime)));
    $stat["total_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and delivery_type = 2 and status =5", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id)));
    if (check_plugin_perm("errander")) {
        $stat["errander_yesterday_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status =3 and addtime >= :starttime and addtime <= :endtime", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id, ":starttime" => $yesterday_starttime, ":endtime" => $today_starttime)));
        $stat["errander_today_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status =3 and addtime >= :starttime", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id, ":starttime" => $today_starttime)));
        $stat["errander_month_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status =3 and addtime >= :starttime", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id, ":starttime" => $month_starttime)));
        $stat["errander_total_num"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status =3", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id)));
    }
    return $stat;
}
function deliveryer_update_credit2($deliveryer_id, $fee, $trade_type, $extra, $remark = "", $order_type = "order")
{
    global $_W;
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id));
    if (empty($deliveryer)) {
        return error(-1, "账户不存在");
    }
    if ($deliveryer["status"] != 1) {
        return error(-1, "账户已被删除");
    }
    if ($trade_type == 1 && !empty($extra)) {
        $is_exist = pdo_get("tiny_wmall_deliveryer_current_log", array("uniacid" => $_W["uniacid"], "deliveryer_id" => $deliveryer_id, "trade_type" => 1, "extra" => $extra, "order_type" => $order_type), array("id"));
        if ($is_exist) {
            return error(-1, "订单已经入账");
        }
    }
    $hash = md5((string) $_W["uniacid"] . "-" . $deliveryer_id . "-" . $trade_type . "-" . $extra . "-" . $order_type);
    if ($trade_type == 3) {
        $hash = md5((string) $_W["uniacid"] . "-" . $deliveryer_id . "-" . $trade_type . "-" . $fee . TIMESTAMP);
    }
    $now_amount = $deliveryer["credit2"] + $fee;
    $log = array("uniacid" => $_W["uniacid"], "agentid" => $deliveryer["agentid"], "deliveryer_id" => $deliveryer_id, "order_type" => $order_type, "trade_type" => $trade_type, "extra" => $extra, "fee" => $fee, "amount" => $now_amount, "addtime" => TIMESTAMP, "hash" => $hash, "stat_month" => date("Ym", TIMESTAMP), "remark" => $remark);
    pdo_insert("tiny_wmall_deliveryer_current_log", $log);
    $id = pdo_insertid();
    if ($trade_type == 3) {
        mlog(4003, $id);
    }
    if (!empty($id)) {
        $status = pdo_update("tiny_wmall_deliveryer", array("credit2" => $now_amount), array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id));
        if ($status === false) {
            $deliveryer_new = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id));
            slog("deliveryeraccount", "配送员账户变动失败", array(), "配送员id:" . $deliveryer["id"] . ",配送员姓名:" . $deliveryer["title"] . ",变动前金额：" . $deliveryer["credit2"] . ",金额变动:" . $fee . ",变动后金额" . $now_amount . ",实际变动后金额：" . $deliveryer_new["credit2"]);
        }
    }
    return true;
}
function deliveryer_login($mobile, $password)
{
    global $_W;
    global $_GPC;
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "mobile" => $mobile));
    if (empty($deliveryer)) {
        return ierror(-1, "账号不存在");
    }
    if ($deliveryer["status"] != 1) {
        return ierror(-1, "账号已被删除");
    }
    $password = md5(md5($deliveryer["salt"] . $password) . $deliveryer["salt"]);
    if ($password != $deliveryer["password"]) {
        return ierror(-1, "密码错误");
    }
    if (!empty($_GPC["registration_id"]) && $deliveryer["registration_id"] != $_GPC["registration_id"]) {
        pdo_update("tiny_wmall_deliveryer", array("registration_id" => $_GPC["registration_id"]), array("uniacid" => $_W["uniacid"], "id" => $deliveryer["id"]));
    }
    if (empty($deliveryer["token"])) {
        $deliveryer["token"] = random(32);
        $token = $deliveryer["token"];
        pdo_update("tiny_wmall_deliveryer", array("token" => $token), array("uniacid" => $_W["uniacid"], "id" => $deliveryer["id"]));
    }
    return ierror(0, "调用成功", $deliveryer);
}
function deliveryer_order_num_update($deliveryer_id)
{
    global $_W;
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id), array("id", "status"));
    if (empty($deliveryer)) {
        return error(-1, "配送员不存在");
    }
    if ($deliveryer["status"] != 1) {
        return error(-1, "配送员已被删除");
    }
    $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id);
    $takeout_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (delivery_status = 7 or delivery_status = 4 or delivery_status = 8) and status < 5", $params);
    $update = array("order_takeout_num" => intval($takeout_num));
    if (check_plugin_perm("errander")) {
        $errander_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and (delivery_status = 2 or delivery_status = 3) and status < 3", $params);
        $update["order_errander_num"] = intval($errander_num);
    }
    pdo_update("tiny_wmall_deliveryer", $update, array("uniacid" => $_W["uniacid"], "id" => $deliveryer["id"]));
    return true;
}
function sys_notice_deliveryer_settle($deliveryer_id, $note = "")
{
    global $_W;
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id));
    if (empty($deliveryer)) {
        return error(-1, "配送员不存在");
    }
    if ($deliveryer["status"] != 1) {
        return error(-1, "配送员已被删除");
    }
    $maneger = $_W["we7_wmall"]["config"]["manager"];
    if (empty($maneger["openid"])) {
        return error(-1, "平台管理员信息不存在");
    }
    $tips = "尊敬的【" . $maneger["nickname"] . "】，有新的配送员提交了入驻请求。请登录电脑进行权限分配";
    $remark = array("性别 : " . $deliveryer["sex"], "年龄 : " . $deliveryer["age"], "申请人手机号: " . $deliveryer["mobile"], $note);
    $remark = implode("\n", $remark);
    $send = array("first" => array("value" => $tips, "color" => "#ff510"), "keyword1" => array("value" => $deliveryer["title"], "color" => "#ff510"), "keyword2" => array("value" => $deliveryer["title"], "color" => "#ff510"), "keyword3" => array("value" => date("Y-m-d H:i", time()), "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
    $acc = WeAccount::create($_W["acid"]);
    $status = $acc->sendTplNotice($maneger["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["settle_apply_tpl"], $send);
    if (is_error($status)) {
        slog("wxtplNotice", "平台配送员入驻微信通知平台管理员", $send, $status["message"]);
    }
    return $status;
}
function to_workstatus($status, $key = "all")
{
    $data = array("1" => array("css" => "label label-success", "text" => "接单中"), "0" => array("css" => "label label-danger", "text" => "休息中"));
    if ($key == "all") {
        return $data[$status];
    }
    if ($key == "css") {
        return $data[$status]["css"];
    }
    if ($key == "text") {
        return $data[$status]["text"];
    }
}
function deliveryer_work_status_set($deliveryer_id, $status, $jpush = false, $notify = false)
{
    global $_W;
    $tips = array("休息中", "接单中");
    $status = intval($status);
    if (!in_array($status, array_keys($tips))) {
        return error(-1, "工作状态有误");
    }
    pdo_update("tiny_wmall_deliveryer", array("work_status" => $status), array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id));
    $data = array("work_status" => $status, "work_status_cn" => $tips[$status]);
    if ($jpush && !empty($deliveryer["registration_id"])) {
        mload()->model("jpush");
        $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id));
        $original = jpush_get_devices($deliveryer["registration_id"]);
        if (is_error($original)) {
            return $original;
        }
        $relation = deliveryer_push_token($deliveryer);
        $result = jpush_update_devices($deliveryer["registration_id"], $original, $relation);
        if (is_error($result)) {
            return $result;
        }
    }
    if ($notify) {
        deliveryer_notice("work_status_change", $deliveryer_id);
    }
    return $data;
}
function deliveryer_notice($type, $audience = 0, $extra = array())
{
    global $_W;
    $acc = WeAccount::create($_W["uniacid"]);
    if (0 < $audience) {
        $deliveryers[] = $deliveryer = deliveryer_fetch($audience);
    }
    if ($type == "work_status_change") {
        $title = "您好," . $deliveryer["title"] . "。平台管理员已将您的工作状态设置为" . $deliveryer["work_status_cn"] . ",如有疑问,请联系平台管理员";
        $url = imurl("delivery/member/mine", array(), true);
        $send = tpl_format($title, "******", "******", "");
        $status = $acc->sendTplNotice($deliveryer["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url);
        if (is_error($status)) {
            slog("wxtplNotice", "工作状态变动微信通知配送员:" . $deliveryer["title"], $send, $status["message"]);
        }
        if (!empty($deliveryer["token"])) {
            $audience = array("alias" => array($deliveryer["token"]));
            Jpush_deliveryer_send("工作状态变更通知", $title, array("url" => idurl("pages/member/mine", array(), true), "voice_text" => $title, "notify_type" => "work_status_change", "voice_play_nums" => 1, "redirect_type" => "work_status_change", "work_status" => $deliveryer["work_status"]), $audience);
        }
        if (!empty($deliveryer["mobile"]) && $deliveryer["extra"]["accept_voice_notice"] == 1) {
            mload()->model("sms");
            $data = sms_singlecall($deliveryer["mobile"], array("name" => $deliveryer["title"], "work_status_cn" => $deliveryer["work_status_cn"]), "work_status_change");
            if (is_error($data)) {
                slog("alidayuCall", "工作状态变动阿里大鱼语音通知配送员:" . $deliveryer["title"], array("name" => $deliveryer["title"], "work_status_cn" => $deliveryer["work_status_cn"]), $data["message"]);
            }
        }
    }
    return true;
}
function deliveryer_push_token($deliveryerOrid)
{
    global $_W;
    $deliveryer = $deliveryerOrid;
    if (!is_array($deliveryer)) {
        $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $deliveryer));
    }
    if (empty($deliveryer)) {
        return error(-1, "配送员不存在");
    }
    if ($deliveryer["status"] != 1) {
        return error(-1, "配送员已被删除");
    }
    if (empty($deliveryer["token"])) {
        $deliveryer["token"] = random(32);
        pdo_update("tiny_wmall_deliveryer", array("token" => $deliveryer["token"]), array("id" => $deliveryer["id"]));
    }
    $config = $_W["we7_wmall"]["config"]["app"]["deliveryer"];
    $relation = array("alias" => $deliveryer["token"], "tags" => array(), "mobile" => $deliveryer["mobile"]);
    if (!empty($config["serial_sn"])) {
        $relation["tags"][] = $config["serial_sn"];
    }
    if ($deliveryer["work_status"] == 1) {
        $relation["tags"][] = $config["push_tags"]["working"];
    } else {
        $relation["tags"][] = $config["push_tags"]["rest"];
    }
    if ($deliveryer["is_takeout"] == 1 && !empty($config["push_tags"]["waimai"])) {
        $relation["tags"][] = $config["push_tags"]["waimai"];
    }
    if ($deliveryer["is_errander"] == 1 && !empty($config["push_tags"]["paotui"])) {
        $relation["tags"][] = $config["push_tags"]["paotui"];
    }
    if (0 < $deliveryer["agentid"]) {
        $config = get_agent_system_config("app.deliveryer", $deliveryer["agentid"]);
        $relation["tags"][] = $config["push_tags"]["all"];
        if ($deliveryer["work_status"] == 1) {
            $relation["tags"][] = $config["push_tags"]["working"];
        } else {
            $relation["tags"][] = $config["push_tags"]["rest"];
        }
        if ($deliveryer["is_takeout"] == 1 && !empty($config["push_tags"]["waimai"])) {
            $relation["tags"][] = $config["push_tags"]["waimai"];
        }
        if ($deliveryer["is_errander"] == 1 && !empty($config["push_tags"]["paotui"])) {
            $relation["tags"][] = $config["push_tags"]["paotui"];
        }
    }
    $code = md5(iserializer($relation));
    $relation["code"] = $code;
    return $relation;
}
function deliveryer_set_extra($type, $value, $deliveryer_id = 0)
{
    global $_W;
    if (empty($deliveryer_id)) {
        $deliveryer_id = $_W["deliveryer"]["id"];
    }
    $data = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id), array("id", "extra"));
    if (!empty($data)) {
        if (empty($data["extra"])) {
            if ($type == "accept_wechat_notice") {
                $extra[$type] = $value;
                $extra["accept_voice_notice"] = 0;
            }
            if ($type == "accept_voice_notice") {
                $extra["accept_wechat_notice"] = 0;
                $extra[$type] = $value;
            }
        } else {
            $extra = iunserializer($data["extra"]);
            $extra[$type] = $value;
        }
        $update = array("extra" => iserializer($extra));
        pdo_update("tiny_wmall_deliveryer", $update, array("uniacid" => $_W["uniacid"], "id" => $deliveryer_id));
    }
    return true;
}
function deliveryer_getcash_config()
{
    global $_W;
    $config = $_W["we7_wmall"]["config"]["delivery"]["cash"];
    if (empty($config)) {
        $config = array("get_cash_fee_limit" => 1, "get_cash_fee_rate" => 0, "get_cash_fee_min" => 0, "get_cash_fee_max" => 0, "get_cash_period" => 0);
    }
    return $config;
}
function deliveryer_filter($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($filter["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($filter["status"]) ? intval($filter["status"]) : 1;
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $work_status = isset($filter["work_status"]) ? intval($filter["work_status"]) : "-1";
    if (-1 < $work_status) {
        $condition .= " and work_status = :work_status";
        $params[":work_status"] = $work_status;
    }
    $is_takeout = isset($filter["is_takeout"]) ? intval($filter["is_takeout"]) : -1;
    if (-1 < $is_takeout) {
        $condition .= " and is_takeout = :is_takeout";
        $params[":is_takeout"] = $is_takeout;
    }
    $is_errander = isset($filter["is_errander"]) ? intval($filter["is_errander"]) : -1;
    if (-1 < $is_errander) {
        $condition .= " and is_errander = :is_errander";
        $params[":is_errander"] = $is_errander;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (title like '%" . $keyword . "%' or nickname like '%" . $keyword . "%' or mobile like '%" . $keyword . "%')";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 20;
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_deliveryer") . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$row) {
            $row["auth_info"] = iunserializer($row["auth_info"]);
            $row["extra"] = iunserializer($row["extra"]);
            $row["work_status"] = intval($row["work_status"]);
            if (isset($row["extra"]["accept_wechat_notice"])) {
                $row["extra"]["accept_wechat_notice"] = intval($row["extra"]["accept_wechat_notice"]);
            }
            if (isset($row["extra"]["accept_voice_notice"])) {
                $row["extra"]["accept_voice_notice"] = intval($row["extra"]["accept_voice_notice"]);
            }
        }
    }
    return $data;
}
function get_deliveryer_menu()
{
    global $_W;
    $menu = get_plugin_config("deliveryerApp.menu");
    if (empty($menu)) {
        $menu = array("name" => "default", "params" => array("navstyle" => "0"), "css" => array("iconColor" => "#000", "iconColorActive" => "#0EC3B3", "textColor" => "#000", "textColorActive" => "#0EC3B3"), "data" => array("M0123456789101" => array("link" => "/pages/order/takeout", "icon" => "icon-order", "text" => "外卖"), "M0123456789102" => array("link" => "/pages/paotui/index", "icon" => "icon-i-activitymonitoring", "text" => "跑腿"), "M0123456789103" => array("link" => "/pages/member/mine", "icon" => "icon-mine", "text" => "我的")));
    } else {
        $menu = json_decode(base64_decode($menu), true);
        foreach ($menu["data"] as &$val) {
            if (!empty($val["img"])) {
                $val["img"] = tomedia($val["img"]);
            }
        }
    }
    return $menu;
}
function get_deliveryer_paths($deliveryer_id, $stat_day, $force = 0)
{
    global $_W;
    global $_GPC;
    $deliveryer_id = intval($deliveryer_id);
    if (empty($deliveryer_id)) {
        return array();
    }
    if (empty($stat_day)) {
        $stat_day = date("Ymd");
    }
    $stat_day = strtotime($stat_day);
    $stat_day = date("Ymd", $stat_day);
    $deliveryer_paths = pdo_get("tiny_wmall_deliveryer_path", array("uniacid" => $_W["uniacid"], "deliveryer_id" => $deliveryer_id, "stat_day" => $stat_day));
    $result = array();
    if (!empty($deliveryer_paths) && $force == 0) {
        $result = iunserializer($deliveryer_paths["paths"]);
    } else {
        $condition = " where uniacid = :uniacid and deliveryer_id = :deliveryer_id ";
        $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer_id);
        $condition .= " and stat_day = :stat_day ";
        $params[":stat_day"] = $stat_day;
        $data = pdo_fetchall("select location_y, location_x, addtime from " . tablename("tiny_wmall_deliveryer_location_log") . $condition . " order by addtime asc", $params, "addtime");
        if (!empty($data)) {
            $paths = array_values($data);
            if (!empty($paths)) {
                $length = count($paths);
                $temp = 1;
                if (10000 <= $length) {
                    $temp = intval($length / 10000) * 100;
                } else {
                    if (1000 <= $length && $length < 10000) {
                        $temp = intval($length / 1000) * 10;
                    } else {
                        if (100 <= $length && $length < 1000) {
                            $temp = intval($length / 100) * 1;
                        }
                    }
                }
                foreach ($paths as $key => $value) {
                    if ($key % $temp == 0) {
                        $result[] = $value;
                    }
                }
            }
            if (empty($deliveryer_paths)) {
                $insert = array("uniacid" => $_W["uniacid"], "deliveryer_id" => $deliveryer_id, "stat_day" => $stat_day, "addtime" => TIMESTAMP, "paths" => iserializer($result));
                pdo_insert("tiny_wmall_deliveryer_path", $insert);
            } else {
                $update = array("addtime" => TIMESTAMP, "paths" => iserializer($result));
                pdo_update("tiny_wmall_deliveryer_path", $update, array("uniacid" => $_W["uniacid"], "deliveryer_id" => $deliveryer_id, "stat_day" => $stat_day));
            }
        }
    }
    return $result;
}

?>