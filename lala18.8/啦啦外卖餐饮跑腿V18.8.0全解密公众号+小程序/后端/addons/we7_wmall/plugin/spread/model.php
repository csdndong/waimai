<?php
/*

 * @开源学习用
 * @Popping
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
function spread_groups()
{
    global $_W;
    $groups = pdo_fetchall("select * from " . tablename("tiny_wmall_spread_groups") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    foreach ($groups as &$val) {
        if (!empty($val["data"])) {
            $val["data"] = iunserializer($val["data"]);
        }
    }
    return $groups;
}
function to_spreadgroup($groupid, $key = "all")
{
    global $_W;
    $data = array();
    $groups = spread_groups();
    foreach ($groups as $val) {
        $data[$val["id"]] = array("title" => $val["title"]);
    }
    if ($key == "all") {
        return $data;
    }
    if ($key == "title") {
        return $data[$groupid]["title"];
    }
}
function spread_status($status, $key = "all")
{
    $data = array("1" => array("css" => "label label-success", "text" => "正常"), "2" => array("css" => "label label-default", "text" => "黑名单"), "0" => array("css" => "label label-warning", "text" => "待审核"));
    if ($key == "all") {
        return $data;
    }
    if ($key == "text") {
        return $data[$status]["text"];
    }
    if ($key == "css") {
        return $data[$status]["css"];
    }
}
function spread_getcash_type($channel, $key = "all")
{
    $data = array("weixin" => array("css" => "label label-success", "text" => "微信-公众号"), "wxapp" => array("css" => "label label-success", "text" => "微信-小程序"), "credit" => array("css" => "label label-warning", "text" => "账户余额"), "bank" => array("css" => "label label-info", "text" => "银行卡"), "alipay" => array("css" => "label label-default", "text" => "支付宝"));
    if ($key == "all") {
        return $data[$channel];
    }
    if ($key == "text") {
        return $data[$channel]["text"];
    }
    if ($key == "css") {
        return $data[$channel]["css"];
    }
}
function get_spread($spread_id = 0)
{
    global $_W;
    if ($spread_id == 0) {
        $spread_id = $_W["member"]["uid"];
    }
    $fields = array("uid", "mobile", "avatar", "nickname", "is_spread", "spreadcredit2", "spread1", "spread2", "spread_groupid", "spread_groupid_change_from", "spread_status", "spreadtime");
    $spread = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $spread_id), $fields);
    $group = pdo_get("tiny_wmall_spread_groups", array("uniacid" => $_W["uniacid"], "id" => $spread["spread_groupid"]));
    $spread["groupname"] = $group["title"];
    $spread["group"] = $group;
    return $spread;
}
function spread_commission_stat($spread_id = 0)
{
    global $_W;
    if ($spread_id == 0) {
        $spread_id = $_W["member"]["uid"];
    }
    $commission_grandtotal = pdo_fetchcolumn("select sum(fee) from" . tablename("tiny_wmall_spread_current_log") . "where uniacid = :uniacid and spreadid = :spreadid and trade_type = 1", array(":uniacid" => $_W["uniacid"], ":spreadid" => $spread_id));
    $commission_grandtotal = round($commission_grandtotal, 2);
    $commission_getcash_apply = pdo_fetchcolumn("select sum(get_fee) from" . tablename("tiny_wmall_spread_getcash_log") . "where uniacid = :uniacid and status = 2 and spreadid = :spreadid", array(":uniacid" => $_W["uniacid"], ":spreadid" => $spread_id));
    $commission_getcash_apply = round($commission_getcash_apply, 2);
    $commission_getcash_success = pdo_fetchcolumn("select sum(get_fee) from" . tablename("tiny_wmall_spread_getcash_log") . "where uniacid = :uniacid and status = 1 and spreadid = :spreadid", array(":uniacid" => $_W["uniacid"], ":spreadid" => $spread_id));
    $commission_getcash_success = round($commission_getcash_success, 2);
    $member = pdo_fetch("select spreadcredit2 from" . tablename("tiny_wmall_members") . "where uniacid = :uniacid and uid = :spreadid", array(":uniacid" => $_W["uniacid"], ":spreadid" => $spread_id));
    $spreadcredit2 = round($member["spreadcredit2"], 2);
    $data = array("commission_getcash_apply" => $commission_getcash_apply, "commission_getcash_success" => $commission_getcash_success, "spreadcredit2" => $spreadcredit2, "commission_grandtotal" => $commission_grandtotal);
    return $data;
}
function spread_group_update($spread_id = 0, $wx_tpl = false)
{
    global $_W;
    $spread_id = intval($spread_id);
    if (empty($spread_id)) {
        $spread_id = $_W["member"]["uid"];
    }
    $spread_info = get_spread($spread_id);
    if ($spread_info["group"]["admin_update_rules"] == "keep") {
        return true;
    }
    $config = get_plugin_config("spread");
    $relate = $config["relate"];
    $config_basic = $config["basic"];
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $spread_paotui = $spread_gohome = 0;
    if ($relate["group_update_mode"] == "order_money") {
        $condition .= " and is_pay = 1 and status = 5 and (spread1 = :spread or spread2 = :spread)";
        $params[":spread"] = $spread_id;
        $result = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_order") . $condition, $params);
        $result = round($result, 2);
        if ($config_basic["paotui_status"] == 1) {
            $spread_paotui = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and status = 4 and (spread1 = :spread or spread2 = :spread)", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
        }
        if ($config_basic["gohome_status"] == 1) {
            $spread_gohome = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and (status = 5 or status = 6) and (spread1 = :spread or spread2 = :spread)", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
        }
        $result = $result + $spread_paotui + $spread_gohome;
    } else {
        if ($relate["group_update_mode"] == "order_money_1") {
            $condition .= " and is_pay = 1 and status = 5 and spread1 = :spread";
            $params[":spread"] = $spread_id;
            $result = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_order") . $condition, $params);
            $result = round($result, 2);
            if ($config_basic["paotui_status"] == 1) {
                $spread_paotui = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and status = 4 and spread1 = :spread", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
            }
            if ($config_basic["gohome_status"] == 1) {
                $spread_gohome = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and (status = 5 or status = 6) and spread1 = :spread", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
            }
            $result = $result + $spread_paotui + $spread_gohome;
        } else {
            if ($relate["group_update_mode"] == "order_count") {
                $condition .= " and is_pay = 1 and status = 5 and (spread1 = :spread or spread2 = :spread)";
                $params[":spread"] = $spread_id;
                $result = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_order") . $condition, $params);
                if ($config_basic["paotui_status"] == 1) {
                    $spread_paotui = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and status = 4 and (spread1 = :spread or spread2 = :spread)", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
                }
                if ($config_basic["gohome_status"] == 1) {
                    $spread_gohome = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and (status = 5 or status = 6) and (spread1 = :spread or spread2 = :spread)", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
                }
                $result = $result + $spread_paotui + $spread_gohome;
            } else {
                if ($relate["group_update_mode"] == "order_count_1") {
                    $condition .= " and is_pay = 1 and status = 5 and spread1 = :spread";
                    $params[":spread"] = $spread_id;
                    $result = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_order") . $condition, $params);
                    if ($config_basic["paotui_status"] == 1) {
                        $spread_paotui = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and status = 4 and spread1 = :spread", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
                    }
                    if ($config_basic["gohome_status"] == 1) {
                        $spread_gohome = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and (status = 5 or status = 6) and spread1 = :spread", array(":uniacid" => $_W["uniacid"], ":spread" => $spread_id));
                    }
                    $result = $result + $spread_paotui + $spread_gohome;
                } else {
                    if ($relate["group_update_mode"] == "self_order_money") {
                        $condition .= " and is_pay = 1 and status = 5 and uid = :uid";
                        $params[":uid"] = $spread_id;
                        $result = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_order") . $condition, $params);
                        $result = round($result, 2);
                        if ($config_basic["paotui_status"] == 1) {
                            $spread_paotui = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and uid = :uid and status = 4", array(":uniacid" => $_W["uniacid"], ":uid" => $spread_id));
                        }
                        if ($config_basic["gohome_status"] == 1) {
                            $spread_gohome = pdo_fetchcolumn("select sum(final_fee) from" . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and uid = :uid and (status = 5 or status = 6)", array(":uniacid" => $_W["uniacid"], ":uid" => $spread_id));
                        }
                        $result = $result + $spread_paotui + $spread_gohome;
                    } else {
                        if ($relate["group_update_mode"] == "self_order_count") {
                            $condition .= " and is_pay = 1 and status =5 and uid = :uid";
                            $params[":uid"] = $spread_id;
                            $result = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_order") . $condition, $params);
                            $spread_paotui = $spread_gohome = 0;
                            if ($config_basic["paotui_status"] == 1) {
                                $spread_paotui = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and uid = :uid and status = 4", array(":uniacid" => $_W["uniacid"], ":uid" => $spread_id));
                            }
                            if ($config_basic["gohome_status"] == 1) {
                                $spread_gohome = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and uid = :uid and (status = 5 or status = 6)", array(":uniacid" => $_W["uniacid"], ":uid" => $spread_id));
                            }
                            $result = $result + $spread_paotui + $spread_gohome;
                        } else {
                            if ($relate["group_update_mode"] == "down_count") {
                                $condition .= " and (spread1 = :spread or spread2 = :spread)";
                                $params[":spread"] = $spread_id;
                                $result = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_members") . $condition, $params);
                            } else {
                                if ($relate["group_update_mode"] == "down_count_1") {
                                    $condition .= " and spread1 = :spread";
                                    $params[":spread"] = $spread_id;
                                    $result = pdo_fetchcolumn("select count(*) from" . tablename("tiny_wmall_members") . $condition, $params);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $groups = pdo_fetchall("select * from" . tablename("tiny_wmall_spread_groups") . " where uniacid = :uniacid order by group_condition asc", array(":uniacid" => $_W["uniacid"]), "id");
    foreach ($groups as $group) {
        if ($group["group_condition"] <= $result) {
            $group_id = $group["id"];
        }
        if ($group["id"] == $spread_info["spread_groupid"]) {
            $group_condition_now = $group["group_condition"];
        }
    }
    if ($spread_info["spread_groupid_change_from"] == "manager") {
        if ($relate["admin_update_rules"] == "keep") {
            $group_id = $spread_info["spread_groupid"];
        } else {
            if ($result < $group_condition_now) {
                $group_id = $spread_info["spread_groupid"];
            }
        }
    }
    if ($spread_info["admin_update_rules"] == "only_up" && $result < $group_condition_now) {
        $group_id = $spread_info["spread_groupid"];
    }
    pdo_update("tiny_wmall_members", array("spread_groupid" => $group_id), array("uniacid" => $_W["uniacid"], "uid" => $spread_id));
    return true;
}
function sys_notice_spread_down($uid, $type, $extra = array())
{
    global $_W;
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), array("uid", "nickname", "openid", "spread1", "spread2", "spreadfixed", "addtime"));
    if (empty($member)) {
        return error(-1, "用户不存在");
    }
    $spreads = member_spread($uid);
    if (empty($spreads)) {
        return error(-1, "推广上线不存在");
    }
    $spread1 = $spreads["spread1"];
    $config_mall = $_W["we7_wmall"]["config"]["mall"];
    $config_spread = get_plugin_config("spread");
    $acc = WeAccount::create($_W["acid"]);
    if ($type == "pseudo_down") {
        if (!empty($spread1["openid"])) {
            $tips = "您好,【" . $spread1["nickname"] . "】," . $member["nickname"] . "通过您分享的二维码登录了" . $config_mall["title"] . "。";
            $remark = array("顾客昵称: " . $member["nickname"]);
            if (empty($member["spreadfixed"])) {
                if ($config_spread["relate"]["become_child"] == 1) {
                    $remark[] = "注意:您并不是" . $member["nickname"] . "的固定推广员,在" . $member["nickname"] . "下单确认收货之前,他的推广员都有可能发生变化";
                } else {
                    if ($config_spread["relate"]["become_child"] == 2) {
                        $remark[] = "注意:您并不是" . $member["nickname"] . "的固定推广员,在" . $member["nickname"] . "下单确认收货并进行评价之前,他的推广员都有可能发生变化";
                    }
                }
            }
        }
    } else {
        if ($type == "new_down") {
            $tips = "您好,【" . $spread1["nickname"] . "】";
            if ($extra["channel"] == "qrcode") {
                $tips .= "," . $member["nickname"] . "通过您分享的二维码登录了" . $config_mall["title"] . ",您已成功升级为" . $member["nickname"] . "的推广员";
            } else {
                if ($extra["channel"] == "order_end") {
                    $tips .= "您成功推荐" . $member["nickname"] . "在" . $config_mall["title"] . "下单并已确认收货,您已成功升级为" . $member["nickname"] . "的推广员";
                } else {
                    if ($extra["channel"] == "order_comment") {
                        $tips .= "您成功推荐" . $member["nickname"] . "在" . $config_mall["title"] . "下单,确认收货并已完成评价,您已成功升级为" . $member["nickname"] . "的推广员";
                    }
                }
            }
            $remark = array("顾客昵称: " . $member["nickname"], (string) $member["nickname"] . "今后在" . $config_mall["title"] . "下单,您将会获得平台的返佣");
        }
    }
    $remark = implode("\n", $remark);
    $send = array("first" => array("value" => $tips, "color" => "#ff510"), "keyword1" => array("value" => $member["uid"], "color" => "#ff510"), "keyword2" => array("value" => date("Y-m-d H:i", $member["addtime"]), "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
    $status = $acc->sendTplNotice($spread1["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["join_tpl"], $send);
    if (is_error($status)) {
        slog("wxtplNotice", "新的推广下线通知", $send, $status["message"]);
    }
    return $status;
}
function sys_notice_spread_settle($spread_id, $type, $extra = array())
{
    global $_W;
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $spread_id));
    if (empty($member)) {
        return error(-1, "用户不存在");
    }
    $acc = WeAccount::create($_W["acid"]);
    if ($type == "apply") {
        if (!empty($member["openid"])) {
            $tips = "您好,【" . $member["realname"] . "】,您的推广员入驻申请已经提交,请等待管理员审核";
            $remark = array("申请　人: " . $member["realname"], "手机　号: " . $member["mobile"]);
            $remark = implode("\n", $remark);
            $send = array("first" => array("value" => $tips, "color" => "#ff510"), "keyword1" => array("value" => $member["realname"], "color" => "#ff510"), "keyword2" => array("value" => $member["realname"], "color" => "#ff510"), "keyword3" => array("value" => date("Y-m-d H:i", time()), "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
            $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["settle_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "推广员入驻申请提交", $send, $status["message"]);
            }
        }
        $manager = $_W["we7_wmall"]["config"]["manager"];
        if (!empty($manager["openid"])) {
            $tips = "尊敬的【" . $manager["nickname"] . "】，有新的推广员提交了入驻请求。请登录电脑进行权限分配";
            $remark = array("申请　人: " . $member["realname"], "手机　号: " . $member["mobile"]);
            $remark = implode("\n", $remark);
            $send = array("first" => array("value" => $tips, "color" => "#ff510"), "keyword1" => array("value" => $member["realname"], "color" => "#ff510"), "keyword2" => array("value" => $member["realname"], "color" => "#ff510"), "keyword3" => array("value" => date("Y-m-d H:i", time()), "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
            $status = $acc->sendTplNotice($manager["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["settle_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "推广员入驻微信通知平台管理员", $send, $status["message"]);
            }
        }
    } else {
        if ($type == "success") {
            if (empty($member["openid"])) {
                return error(-1, "推广员信息不完善");
            }
            $tips = "您好,【" . $member["realname"] . "】,您的推广员入驻申请已经通过审核";
            $remark = array("如有疑问请及时联系平台管理人员");
            $remark = implode("\n", $remark);
            $send = array("first" => array("value" => $tips, "color" => "#ff510"), "keyword1" => array("value" => $member["realname"], "color" => "#ff510"), "keyword2" => array("value" => $member["realname"], "color" => "#ff510"), "keyword3" => array("value" => date("Y-m-d H:i", time()), "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
            $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["settle_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "推广员入驻成功", $send, $status["message"]);
            }
        } else {
            if ($type == "fail") {
                if (empty($member["openid"])) {
                    return error(-1, "推广员信息不完善");
                }
                $tips = "您好,【" . $member["realname"] . "】,您的推广员入驻申请失败";
                $remark = array("如有疑问请及时联系平台管理人员");
                $remark = implode("\n", $remark);
                $send = array("first" => array("value" => $tips, "color" => "#ff510"), "keyword1" => array("value" => $member["realname"], "color" => "#ff510"), "keyword2" => array("value" => $member["realname"], "color" => "#ff510"), "keyword3" => array("value" => date("Y-m-d H:i", time()), "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
                $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["settle_apply_tpl"], $send);
                if (is_error($status)) {
                    slog("wxtplNotice", "推广员入驻申请失败", $send, $status["message"]);
                }
            }
        }
    }
    return $status;
}
function sys_notice_spread_getcash($getcash_log_id, $type = "apply", $note = array())
{
    global $_W;
    if ($type == "borrow_openid") {
        $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $getcash_log_id));
    } else {
        $log = pdo_get("tiny_wmall_spread_getcash_log", array("uniacid" => $_W["uniacid"], "id" => $getcash_log_id));
        if (empty($log)) {
            return error(-1, "提现记录不存在");
        }
        $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $log["spreadid"]));
    }
    if (empty($member)) {
        return error(-1, "推广员不存在");
    }
    $acc = WeAccount::create($_W["acid"]);
    if ($type == "apply") {
        if (!empty($member["openid"])) {
            $tips = "您好,【" . $member["realname"] . "】, 您的推广佣金提现申请已提交,请等待管理员审核";
            $remark = array("申请　人: " . $member["realname"], "手机　号: " . $member["mobile"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"]);
            if (!empty($note)) {
                $remark[] = implode("\n", $note);
            }
            $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "推广员申请佣金提现微信通知申请人", $send, $status["message"]);
            }
        }
        $manager = $_W["we7_wmall"]["config"]["manager"];
        if (!empty($manager["openid"])) {
            $tips = "您好,【" . $manager["nickname"] . "】,推广员【" . $member["realname"] . "】申请佣金提现,请尽快处理";
            $remark = array("申请　人: " . $member["realname"], "手机　号: " . $member["mobile"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"]);
            if (!empty($note)) {
                $remark[] = implode("\n", $note);
            }
            $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($manager["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "推广员申请佣金提现微信通知平台管理员", $send, $status["message"]);
            }
        }
    } else {
        if ($type == "success") {
            if (empty($member["openid"])) {
                return error(-1, "推广员信息不完善");
            }
            $tips = "您好,【" . $member["realname"] . "】,您的推广佣金提现已处理";
            $remark = array("处理时间: " . date("Y-m-d H:i", $log["endtime"]), "真实姓名: " . $member["realname"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"], "如有疑问请及时联系平台管理人员");
            $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", $log["addtime"]), "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_success_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "推广员申请佣金提现成功微信通知申请人", $send, $status["message"]);
            }
        } else {
            if ($type == "fail") {
                if (empty($member["openid"])) {
                    return error(-1, "推广员信息不完善");
                }
                $tips = "您好,【" . $member["realname"] . "】, 您的推广佣金提现已处理, 提现未成功";
                $remark = array("处理时间: " . date("Y-m-d H:i", $log["endtime"]), "真实姓名: " . $member["realname"], "手续　费: " . $log["take_fee"], "实际到账: " . $log["final_fee"], "如有疑问请及时联系平台管理人员");
                $params = array("first" => $tips, "money" => $log["get_fee"], "time" => date("Y-m-d H:i", $log["addtime"]), "remark" => implode("\n", $remark));
                $send = sys_wechat_tpl_format($params);
                $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_fail_tpl"], $send);
                if (is_error($status)) {
                    slog("wxtplNotice", "推广员申请佣金提现失败微信通知申请人", $send, $status["message"]);
                }
            } else {
                if ($type == "borrow_openid") {
                    if (empty($member["openid"])) {
                        return error(-1, "推广员信息不完善");
                    }
                    $tips = "您好,【" . $member["realname"] . "】, 您正在进行推广佣金提现申请.平台需要获取您的微信身份信息,您可以点击该消息进行授权。";
                    $remark = array("申请　人: " . $member["realname"], "手机　号: " . $member["mobile"], "请点击该消息进行授权,否则无法进行提现。如果疑问，请联系平台管理员");
                    $params = array("first" => $tips, "money" => $log["get_fee"], "timet" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
                    $send = sys_wechat_tpl_format($params);
                    $payment_wechat = $_W["we7_wmall"]["config"]["payment"]["wechat"];
                    $url = imurl("wmall/auth/oauth", array("params" => base64_encode(json_encode($payment_wechat[$payment_wechat["type"]]))), true);
                    $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_apply_tpl"], $send, $url);
                    if (is_error($status)) {
                        slog("wxtplNotice", "微信端推广员申请佣金提现授权微信通知申请人", $send, $status["message"]);
                    }
                } else {
                    if ($type == "cancel") {
                        if (empty($member["openid"])) {
                            return error(-1, "推广员信息不完善");
                        }
                        $addtime = date("Y-m-d H:i", $log["addtime"]);
                        $tips = "您好,【" . $member["realname"] . "】,您在" . $addtime . "的申请佣金提现已被平台管理员撤销,您可以重新发起提现申请";
                        $remark = array("订单　号: " . $log["trade_no"], "撤销时间: " . date("Y-m-d H:i", $log["endtime"]), "如有疑问请及时联系平台管理人员");
                        if (!empty($note)) {
                            $remark["撤销原因"] = implode("\n", $note);
                        }
                        $params = array("first" => $tips, "money" => $log["get_fee"], "time" => date("Y-m-d H:i", TIMESTAMP), "remark" => implode("\n", $remark));
                        $send = sys_wechat_tpl_format($params);
                        $status = $acc->sendTplNotice($member["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["getcash_fail_tpl"], $send);
                        if (is_error($status)) {
                            slog("wxtplNotice", "推广员申请佣金提现被平台管理员取消微信通知申请人", $send, $status["message"]);
                        }
                    }
                }
            }
        }
    }
    return $status;
}
function spread_update_credit2($spread_id, $fee, $extra = array())
{
    global $_W;
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $spread_id), array("spreadcredit2", "is_spread", "spread_status"));
    if (empty($member)) {
        return error(-1, "账户不存在");
    }
    if (empty($member["is_spread"]) || empty($member["spread_status"])) {
        return error(-1, "该推广员已取消资格");
    }
    $now_amount = $member["spreadcredit2"] + $fee;
    pdo_update("tiny_wmall_members", array("spreadcredit2" => $now_amount), array("uniacid" => $_W["uniacid"], "uid" => $spread_id));
    $log = array("uniacid" => $_W["uniacid"], "spreadid" => $spread_id, "trade_type" => $extra["trade_type"], "order_type" => $extra["order_type"], "extra" => $extra["extra"], "fee" => $fee, "amount" => $now_amount, "addtime" => TIMESTAMP, "remark" => $extra["remark"]);
    pdo_insert("tiny_wmall_spread_current_log", $log);
    return true;
}
function spread_order_balance($order_id, $type = "takeout")
{
    global $_W;
    global $_GPC;
    $routers = array("takeout" => array("table" => "tiny_wmall_order", "end_status" => array(5), "order_type_cn" => "外卖单"), "paotui" => array("table" => "tiny_wmall_errander_order", "end_status" => array(3), "order_type_cn" => "跑腿单"), "gohome" => array("table" => "tiny_wmall_gohome_order", "end_status" => array(5, 6), "order_type_cn" => "生活圈订单"));
    $router = $routers[$type];
    if ($type == "takeout") {
        $order = pdo_fetch("select a.id,a.uid,a.spread1,a.spread2,a.spreadbalance,a.data,a.endtime,a.status,a.plateform_serve_fee,b.oid from " . tablename("tiny_wmall_order") . " as a left join " . tablename("tiny_wmall_order_comment") . " as b on a.id = b.oid where a.uniacid = :uniacid and a.id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $order_id));
    } else {
        $order = pdo_get($router["table"], array("uniacid" => $_W["uniacid"], "id" => $order_id), array("id", "uid", "spread1", "spread2", "spreadbalance", "data", "status", "plateform_serve_fee"));
    }
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    if ($order["spreadbalance"] == 1) {
        return error(-1, "订单已经结算");
    }
    if (!in_array($order["status"], $router["end_status"])) {
        return error(-1, "订单未完成");
    }
    $data = iunserializer($order["data"]);
    $commission = $data["spread"]["commission"];
    $spread = pdo_fetch("select a.uid, b.valid_period from " . tablename("tiny_wmall_members") . " as a left join " . tablename("tiny_wmall_spread_groups") . " as b on a.spread_groupid = b.id where a.uniacid = :uniacid and a.uid = :uid", array(":uniacid" => $_W["uniacid"], ":uid" => $order["spread1"]));
    if ($spread["valid_period"] == "once") {
        pdo_update("tiny_wmall_members", array("spread1" => 0, "spread2" => 0), array("uniacid" => $_W["uniacid"], "uid" => $order["uid"]));
        if (empty($commission["from_spread"]) || $commission["from_spread"] != $spread["uid"]) {
            pdo_update("tiny_wmall_order", array("spreadbalance" => 1, "spread1" => 0, "spread2" => 0), array("id" => $order["id"]));
            return error(-1, "扫码下单推广员才能获得佣金");
        }
    }
    $balance = 1;
    $config_spread = get_plugin_config("spread");
    $config_settle = $config_spread["settle"];
    if ($config_settle["balance_condition"] == 2) {
        if ($type == "takeout" && empty($order["oid"])) {
            $balance = 0;
        } else {
            if ($type == "gohome" && $order["status"] != 6) {
                $balance = 0;
            }
        }
    }
    if ($balance == 1) {
        if (!empty($commission)) {
            if ($config_spread["basic"]["commission_from"] == 1) {
                if (0 < $order["plateform_serve_fee"]) {
                    if ($commission["commission1_type"] == "ratio") {
                        $commission["spread1"] = round(floatval($commission["spread1_rate"]) * $order["plateform_serve_fee"] / 100, 2);
                    }
                    if ($commission["commission2_type"] == "ratio") {
                        $commission["spread2"] = round(floatval($commission["spread2_rate"]) * $order["plateform_serve_fee"] / 100, 2);
                    }
                } else {
                    $commission["spread2"] = 0;
                    $commission["spread1"] = $commission["spread2"];
                }
                $update["data"] = $data;
                $update["data"]["spread"]["commission"] = $commission;
                $update["data"] = iserializer($update["data"]);
            }
            $fee_spread1 = $commission["spread1"];
            if (0 <= $fee_spread1) {
                $extra = array("trade_type" => 1, "extra" => $order["id"], "order_type" => $type);
                if ($commission["commission1_type"] == "ratio") {
                    $extra["remark"] = (string) $router["order_type_cn"] . " 订单号:" . $order["id"] . ", 一级下线佣金费率" . $commission["spread1_rate"] . ", 佣金:" . $fee_spread1 . "元";
                } else {
                    if ($commission["commission1_type"] == "fixed") {
                        $extra["remark"] = (string) $router["order_type_cn"] . " 订单号:" . $order["id"] . ", 一级下线佣金" . $fee_spread1 . "元";
                    }
                }
                spread_update_credit2($order["spread1"], $fee_spread1, $extra);
            }
            $fee_spread2 = $commission["spread2"];
            if (0 <= $fee_spread2) {
                $extra = array("trade_type" => 1, "extra" => $order["id"], "order_type" => $type);
                if ($commission["commission2_type"] == "ratio") {
                    $extra["remark"] = (string) $router["order_type_cn"] . " 订单号:" . $order["id"] . ", 二级下线佣金费率:" . $commission["spread2_rate"] . ", 佣金:" . $fee_spread2 . "元";
                } else {
                    if ($commission["commission2_type"] == "fixed") {
                        $extra["remark"] = (string) $router["order_type_cn"] . " 订单号：" . $order["id"] . ", 二级下线佣金:" . $fee_spread2 . "元";
                    }
                }
                spread_update_credit2($order["spread2"], $fee_spread2, $extra);
            }
        }
        $update["spreadbalance"] = 1;
        pdo_update($router["table"], $update, array("id" => $order["id"]));
    }
    return true;
}
function spread_trade_type($status, $key = "all")
{
    $data = array("1" => array("css" => "label label-success", "text" => "推广佣金入账"), "2" => array("css" => "label label-danger", "text" => "申请提现"), "3" => array("css" => "label label-default", "text" => "其他变动"));
    if ($key == "all") {
        return $data[$status];
    }
    if ($key == "text") {
        return $data[$status]["text"];
    }
    if ($key == "css") {
        return $data[$status]["css"];
    }
}
function member_spread($uid = 0)
{
    global $_W;
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), array("spread1", "spread2"));
    if (empty($member["spread1"])) {
        return false;
    }
    $uids = implode(",", array($member["spread1"], $member["spread2"]));
    $fields = implode(",", array("uid", "nickname", "mobile", "realname", "openid", "avatar"));
    $members = pdo_fetchall("select " . $fields . " from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and uid in (" . $uids . ")", array(":uniacid" => $_W["uniacid"]), "uid");
    $data = array("spread1" => $members[$member["spread1"]], "spread2" => $members[$member["spread2"]]);
    return $data;
}
function member_spread_confirm($order_id)
{
    global $_W;
    $config_spread = get_plugin_config("spread");
    $config_spread_basic = $config_spread["basic"];
    if (empty($config_spread_basic["level"])) {
        return true;
    }
    $order = pdo_fetch("select a.id,a.uid,b.oid from " . tablename("tiny_wmall_order") . " as a left join " . tablename("tiny_wmall_order_comment") . " as b on a.id = b.oid where a.uniacid = :uniacid and a.id = :id and a.status = 5", array(":uniacid" => $_W["uniacid"], ":id" => $order_id));
    if (empty($order)) {
        return true;
    }
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $order["uid"]), array("uid", "spreadfixed", "spread1"));
    if (!empty($member["spreadfixed"])) {
        return true;
    }
    if (empty($member["spread1"])) {
        return true;
    }
    $update = array("spreadfixed" => 0);
    $config_spread_relate = $config_spread["relate"];
    if ($config_spread_relate["become_child"] == 1) {
        $update["spreadfixed"] = 1;
        $channel = "order_end";
    } else {
        if ($config_spread_relate["become_child"] == 2 && !empty($order["oid"])) {
            $update["spreadfixed"] = 1;
            $channel = "order_comment";
        }
    }
    if ($update["spreadfixed"] == 1) {
        pdo_update("tiny_wmall_members", $update, array("uniacid" => $_W["uniacid"], "uid" => $member["uid"]));
        sys_notice_spread_down($member["uid"], "new_down", array("channel" => $channel));
    }
    return true;
}
function member_spread_bind()
{
    global $_W;
    global $_GPC;
    $uid = intval($_GPC["code"]);
    if (empty($uid)) {
        return error(-1, "没有推广人");
    }
    if ($_W["member"]["uid"] == $uid) {
        return error(-1, "自己不能成为自己的推广员");
    }
    $config_spread = get_plugin_config("spread");
    $config_spread_basic = $config_spread["basic"];
    if (empty($config_spread_basic["level"])) {
        return error(-1, "未开启推广层级");
    }
    $spread = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), array("uid", "nickname", "is_spread", "spread_groupid", "spread1", "spread2"));
    if (empty($spread) || !$spread["is_spread"]) {
        return error(-1, "推广人不存在或没有推广权限");
    }
    if ($_W["member"]["spread1"] == $spread["uid"] || $_W["member"]["spread2"] == $spread["uid"]) {
        return error(-1, "您已经是该推广员下线");
    }
    if ($spread["spread1"] == $_W["member"]["uid"]) {
        return error(-1, "该推广员已经是您的一级下线,您不能成为该推广员的下线");
    }
    if ($spread["spread2"] == $_W["member"]["uid"]) {
        return error(-1, "该推广员已经是您的二级下线,您不能成为该推广员的下线");
    }
    $spread_group = pdo_get("tiny_wmall_spread_groups", array("uniacid" => $_W["uniacid"], "id" => $spread["spread_groupid"]));
    if (empty($spread_group) || !isset($spread_group["become_child_limit"])) {
        return error(-1, "推广员等级不能为空");
    }
    $group_become_child_limit = $spread_group["become_child_limit"];
    if ($group_become_child_limit == 1) {
        if (!empty($_W["member"]["spread1"]) || !empty($_W["member"]["spread2"])) {
            return error(-1, "您已经有推广上线，不能被推广");
        }
        if (empty($_W["member"]["is_mall_newmember"])) {
            return error(-1, "您不是新顾客，不能被推广");
        }
        if ($config_spread_basic["paotui_status"] == 1) {
            $paotui_order = pdo_get("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]), array("id"));
            if (!empty($paotui_order)) {
                return error(-1, "您已经下过跑腿单，不能被推广");
            }
        }
        if ($config_spread_basic["gohome_status"] == 1) {
            $gohome_order = pdo_get("tiny_wmall_gohome_order", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]), array("id"));
            if (!empty($gohome_order)) {
                return error(-1, "您已经下过生活圈订单，不能被推广");
            }
        }
    } else {
        if ($group_become_child_limit == 2) {
            if (empty($_W["member"]["is_mall_newmember"])) {
                return error(-1, "您不是新顾客，不能被推广");
            }
            if ($config_spread_basic["paotui_status"] == 1) {
                $paotui_order = pdo_get("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]), array("id"));
                if (!empty($paotui_order)) {
                    return error(-1, "您已经下过跑腿单，不能被推广");
                }
            }
            if ($config_spread_basic["gohome_status"] == 1) {
                $gohome_order = pdo_get("tiny_wmall_gohome_order", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]), array("id"));
                if (!empty($gohome_order)) {
                    return error(-1, "您已经下过生活圈订单，不能被推广");
                }
            }
        } else {
            if ($group_become_child_limit == 3 && (!empty($_W["member"]["spread1"]) || !empty($_W["member"]["spread2"]))) {
                return error(-1, "您已经有推广上线，不能被推广");
            }
        }
    }
    if ($spread_group["valid_period"] == "once") {
        $_SESSION["from_spread_id"] = $spread["uid"];
    }
    $update = array("spread1" => $spread["uid"], "spread2" => $config_spread_basic["level"] == 2 ? $spread["spread1"] : 0, "spreadfixed" => 1);
    pdo_update("tiny_wmall_members", $update, array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
    sys_notice_spread_down($_W["member"]["uid"], "new_down", array("channel" => "qrcode"));
    return $spread;
}
function order_spread_commission_calculate($type, $data)
{
    global $_W;
    if (!empty($_W["member"]["spread1"]) && $_W["member"]["spreadfixed"] == 1) {
        $spread_status = (string) $type . "_status";
        $config_spread = get_plugin_config("spread");
        $config_spread["basic"]["takeout_status"] = 1;
        if ($config_spread["basic"][$spread_status] != 1) {
            return $data;
        }
        $spread1 = $_W["member"]["spread1"];
        if ($config_spread["basic"]["level"] == 2) {
            $spread2 = $_W["member"]["spread2"];
        }
        $spreads = pdo_fetchall("select uid,spread_groupid from " . tablename("tiny_wmall_members") . " where uid = :uid1 or uid = :uid2", array(":uid1" => $spread1, ":uid2" => $spread2), "uid");
        if (!empty($spreads)) {
            $groups = spread_groups();
            $group1 = $groups[$spreads[$spread1]["spread_groupid"]];
            if (!empty($group1)) {
                $data["spread1"] = $spread1;
                $commission1_type = $group1["commission_type"];
                $commission1_value = $group1["commission1"];
                if ($type == "paotui" || $type == "gohome") {
                    $commission1_type = $group1["data"][$type]["commission_type"];
                    $commission1_value = $group1["data"][$type]["commission1"];
                }
                if ($commission1_type == "ratio") {
                    $spread1_rate = $commission1_value / 100;
                    $commission_spread1 = round($spread1_rate * $data["final_fee"], 2);
                    $spread1_rate = $spread1_rate * 100;
                } else {
                    if ($commission1_type == "fixed") {
                        $commission_spread1 = $commission1_value;
                    }
                }
            }
            if (!empty($spread2)) {
                $group2 = $groups[$spreads[$spread2]["spread_groupid"]];
                if (!empty($group2)) {
                    $data["spread2"] = $spread2;
                    $commission2_type = $group2["commission_type"];
                    $commission2_value = $group2["commission2"];
                    if ($type == "paotui" || $type == "gohome") {
                        $commission2_type = $group2["data"][$type]["commission_type"];
                        $commission2_value = $group2["data"][$type]["commission2"];
                    }
                    if ($commission2_type == "ratio") {
                        $spread2_rate = $commission2_value / 100;
                        $commission_spread2 = round($spread2_rate * $data["final_fee"], 2);
                        $spread2_rate = $spread2_rate * 100;
                    } else {
                        if ($commission2_type == "fixed") {
                            $commission_spread2 = $commission2_value;
                        }
                    }
                }
            }
            if (0 < $commission_spread1 || 0 < $commission_spread2) {
                $data["spreadbalance"] = 0;
                $data["data"]["spread"] = array("commission" => array("commission1_type" => $commission1_type, "spread1_rate" => (string) $spread1_rate . "%", "spread1" => floatval($commission_spread1), "commission2_type" => $commission2_type, "spread2_rate" => (string) $spread2_rate . "%", "spread2" => floatval($commission_spread2), "from_spread" => $_SESSION["from_spread_id"]));
            }
        }
    }
    unset($_SESSION["from_spread_id"]);
    return $data;
}

?>