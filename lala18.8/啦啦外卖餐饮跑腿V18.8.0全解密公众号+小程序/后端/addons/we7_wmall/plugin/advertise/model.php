<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;

function advertise_trade_update($id, $type = "pay")
{
    $trade = pdo_get("tiny_wmall_advertise_trade", array("id" => $id));
    if (empty($trade)) {
        return error(-1, "订单不存在");
    }
    if ($type == "pay") {
        if ($trade["is_pay"]) {
            return error(-1, "订单已支付");
        }
        if ($trade["status"]) {
            return error(-1, "订单已变更");
        }
        $preTrade = pdo_fetch("select id,endtime from " . tablename("tiny_wmall_advertise_trade") . " where uniacid = :uniacid and sid = :sid and type = :type and is_pay = 1 and status = 0 order by id desc", array(":uniacid" => $trade["uniacid"], ":sid" => $trade["sid"], ":type" => $trade["type"]));
        if (empty($preTrade)) {
            $preTrade = pdo_fetch("select id,endtime from " . tablename("tiny_wmall_advertise_trade") . " where uniacid = :uniacid and sid = :sid and type = :type and status = 1 order by id desc", array(":uniacid" => $trade["uniacid"], ":sid" => $trade["sid"], ":type" => $trade["type"]));
        }
        $data = array("is_pay" => 1, "status" => 1, "starttime" => $preTrade["endtime"] ? $preTrade["endtime"] : TIMESTAMP, "endtime" => $preTrade["endtime"] ? $preTrade["endtime"] + $trade["days"] * 86400 : TIMESTAMP + $trade["days"] * 86400);
        pdo_update("tiny_wmall_advertise_trade", $data, array("id" => $id));
        advertise_cron($id);
        advertise_clerk_notice($id);
        advertise_manager_notice($id);
        return true;
    }
}
function advertise_clerk_notice($idOrTrade, $type = "pay", $extra = array())
{
    global $_W;
    if (!is_array($idOrTrade)) {
        $trade = pdo_get("tiny_wmall_advertise_trade", array("id" => $idOrTrade));
    } else {
        $trade = $idOrTrade;
    }
    if (empty($trade)) {
        return error(-1, "订单不存在");
    }
    $notices = iunserializer($trade["data"]);
    if ($type == "expire" && $notices["notify_clerk_expire"] == 1) {
        return error(0, "已发送过一次模板消息");
    }
    if ($type == "timeout" && $notices["notify_clerk_timeout"] == 1) {
        return error(0, "已发送过一次模板消息");
    }
    $store_manager = store_manager($trade["sid"]);
    if (empty($store_manager)) {
        return error(-1, "没有设置店铺管理员");
    }
    $store = store_fetch($trade["sid"], array("title", "id"));
    if ($type == "pay") {
        $title = "恭喜您成功购买平台广告位【" . $trade["title"] . "】";
    } else {
        if ($type == "expire") {
            $notify_clerk_expire = 1;
            $title = "您购买的平台广告位【" . $trade["title"] . "】，还有" . $extra["notify_before_hours"] . "小时即将到期";
        } else {
            if ($type == "timeout") {
                $notify_clerk_timeout = 1;
                $title = "您购买的平台广告位【" . $trade["title"] . "】，已经到期";
            }
        }
    }
    $status_trade = advertise_get_status();
    $status_sn = $status_trade[$trade["status"]]["text"];
    $remark = array("店铺名称： " . $store["title"], "支付金额： " . $trade["final_fee"], "开始时间：" . date("Y-m-d H:i", $trade["starttime"]), "结束时间：" . date("Y-m-d H:i", $trade["endtime"]), "如有疑问请联系平台管理员");
    if (!empty($extra)) {
        if (!is_array($extra["remark"])) {
            $remark[] = $extra["remark"];
        } else {
            $remark = array_merge($remark, $extra["remark"]);
        }
    }
    $remark = implode("\n", $remark);
    $url = imurl("manage/advertise/list", array("sid" => $trade["sid"]), true);
    $send = tpl_format($title, $trade["order_sn"], $status_sn, $remark);
    $acc = WeAccount::create($trade["uniacid"]);
    $status = $acc->sendTplNotice($store_manager["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url);
    if (is_error($status)) {
        slog("wxtplNotice", "广告状态更新微信通知商家管理员", $send, $status["message"]);
    } else {
        if (empty($notices)) {
            $notices = array();
        }
        $notices["notify_clerk_timeout"] = $notify_clerk_timeout ? $notify_clerk_timeout : 0;
        $notices["notify_clerk_expire"] = $notify_clerk_expire ? $notify_clerk_expire : 0;
        pdo_update("tiny_wmall_advertise_trade", array("data" => iserializer($notices)), array("id" => $trade["id"]));
    }
}
function advertise_manager_notice($idOrTrade, $type = "pay", $extra = array())
{
    global $_W;
    if (!is_array($idOrTrade)) {
        $trade = pdo_get("tiny_wmall_advertise_trade", array("id" => $idOrTrade));
    } else {
        $trade = $idOrTrade;
    }
    if (empty($trade)) {
        return error(-1, "订单不存在");
    }
    $notices = iunserializer($trade["data"]);
    if ($type == "expire" && $notices["notify_manage_expire"] == 1) {
        return error(0, "已发送过一次模板消息");
    }
    if ($type == "timeout" && $notices["notify_manage_timeout"] == 1) {
        return error(0, "已发送过一次模板消息");
    }
    $mall_manager = $_W["we7_wmall"]["config"]["manager"];
    if (empty($mall_manager)) {
        return error(-1, "没有获取到平台管理员信息");
    }
    $store = store_fetch($trade["sid"], array("title", "id"));
    if ($type == "pay") {
        $title = "店铺【" . $store["title"] . "】，购买了平台广告位【" . $trade["title"] . "】，请您及时处理。";
    } else {
        if ($type == "expire") {
            $notify_manage_expire = 1;
            $title = "店铺【" . $store["title"] . "】的广告位【" . $trade["title"] . "】，还有" . $extra["notify_before_hours"] . "小时即将到期";
        } else {
            if ($type == "timeout") {
                $notify_manage_timeout = 1;
                $title = "店铺【" . $store["title"] . "】的广告位【" . $trade["title"] . "】，已经到期，请您及时处理";
            }
        }
    }
    $status_trade = advertise_get_status();
    $status_sn = $status_trade[$trade["status"]]["text"];
    $remark = array("店铺名称： " . $store["title"], "支付金额： " . $trade["final_fee"], "开始时间：" . date("Y-m-d H:i", $trade["starttime"]), "结束时间：" . date("Y-m-d H:i", $trade["endtime"]));
    if (!empty($extra)) {
        if (!is_array($extra["remark"])) {
            $remark[] = $extra["remark"];
        } else {
            $remark = array_merge($remark, $extra["remark"]);
        }
    }
    $remark = implode("\n", $remark);
    $send = tpl_format($title, $trade["order_sn"], $status_sn, $remark);
    $acc = WeAccount::create($trade["uniacid"]);
    $status = $acc->sendTplNotice($mall_manager["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, "");
    if (is_error($status)) {
        slog("wxtplNotice", "广告状态更新微信通知平台管理员", $send, $status["message"]);
    } else {
        if (empty($notices)) {
            $notices = array();
        }
        $notices["notify_manage_timeout"] = $notify_manage_timeout ? $notify_manage_timeout : 0;
        $notices["notify_manage_expire"] = $notify_manage_expire ? $notify_manage_expire : 0;
        pdo_update("tiny_wmall_advertise_trade", array("data" => iserializer($notices)), array("id" => $trade["id"]));
    }
}
function advertise_get_status($status = "")
{
    $data = array(array("text" => "待生效"), array("text" => "已生效"), array("text" => "已失效"));
    if (empty($status)) {
        return $data;
    }
    return $data[$status];
}
function advertise_get_types()
{
    $advertise_types = array("stick" => array("text" => "商家列表置顶", "value" => "stick"), "recommendHome" => array("text" => "为您优选首页", "value" => "recommendHome"), "recommendOther" => array("text" => "为您优选更多页", "value" => "recommendOther"), "slideHomeTop" => array("text" => "平台首页幻灯片", "value" => "slideHomeTop"), "slideOrderDetail" => array("text" => "订单详情页幻灯片", "value" => "slideOrderDetail"), "slidePaycenter" => array("text" => "收银台页幻灯片", "value" => "slidePaycenter"), "slideMember" => array("text" => "会员中心幻灯片", "value" => "slideMember"));
    return $advertise_types;
}
function get_advertise_info($type)
{
    global $_W;
    $types = array("recommendHome", "recommendOther", "slideHomeTop", "slideMember", "slidePaycenter", "slideOrderDetail");
    if ($type != "stick" && !in_array($type, $types)) {
        return false;
    }
    $advertise = get_plugin_config("advertise");
    if ($type == "recommendHome" || $type == "recommendOther") {
        $config_type = $advertise["type"]["recommend"][$type];
        $status = $advertise["type"]["recommend"]["status"];
    } else {
        $config_type = $advertise["type"][$type];
        $status = $config_type["status"];
    }
    if (in_array($type, $types)) {
        $sailed = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_advertise_trade") . " where uniacid = :uniacid and agentid = :agentid and type = :type and status = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":type" => $type));
        $leave = $config_type["num"] - $sailed[0];
        $leave = 0 < $leave ? $leave : 0;
        $prices = $config_type["prices"];
    } else {
        if ($type == "stick") {
            $sailed = pdo_fetchall("select * from " . tablename("tiny_wmall_advertise_trade") . " where uniacid = :uniacid and agentid = :agentid and type = :type and status = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":type" => "stick"), "displayorder");
            $prices = $config_type["displayorder_fees"];
            $leave = array_diff_key($prices, $sailed);
            $sailed = array_keys($sailed);
        }
    }
    $data = array("status" => $status, "total" => $config_type["num"], "sailed" => $sailed, "leave" => $leave, "prices" => $prices);
    return $data;
}
function advertise_cron($trade_id = 0)
{
    global $_W;
    if (empty($trade_id)) {
        $trades = pdo_fetchall("select * from" . tablename("tiny_wmall_advertise_trade") . "where uniacid = :uniacid and is_pay = 1 and status < 2", array(":uniacid" => $_W["uniacid"]));
    } else {
        $trades = pdo_fetchall("select * from" . tablename("tiny_wmall_advertise_trade") . "where uniacid = :uniacid and is_pay = 1 and id = :id and status < 2", array(":uniacid" => $_W["uniacid"], ":id" => $trade_id));
    }
    if (!empty($trades)) {
        $routers = array("recommendHome" => "is_recommend", "recommendOther" => "is_recommend", "stick" => "is_stick");
        foreach ($trades as $trade) {
            $update_store = array();
            $update_trade = array();
            if ($trade["status"] == 1) {
                if ($trade["endtime"] <= TIMESTAMP) {
                    $update_store[$routers[$trade["type"]]] = 0;
                    $update_trade["status"] = 2;
                    advertise_clerk_notice($trade, "timeout");
                    advertise_manager_notice($trade, "timeout");
                } else {
                    $update_store[$routers[$trade["type"]]] = 1;
                    $update_trade["status"] = 1;
                    if (TIMESTAMP < $trade["starttime"]) {
                        $update_trade["status"] = 0;
                    }
                    $config_advertise = get_plugin_config("advertise.basic");
                    $config_hours = $config_advertise["notify_before_hours"];
                    $hours = round(($trade["endtime"] - TIMESTAMP) / 3600);
                    if ($hours <= $config_hours) {
                        advertise_clerk_notice($trade, "expire", array("notify_before_hours" => $hours));
                        advertise_manager_notice($trade, "expire", array("notify_before_hours" => $hours));
                    }
                }
            } else {
                if ($trade["status"] == 0 && $trade["strattime"] <= TIMESTAMP) {
                    $update_trade["status"] = 1;
                    $update_store[$routers[$trade["type"]]] = 1;
                }
            }
            pdo_update("tiny_wmall_advertise_trade", $update_trade, array("id" => $trade["id"]));
            if (in_array($trade["type"], array_keys($routers))) {
                if ($trade["type"] == "stick") {
                    if (empty($update_store["is_stick"])) {
                        $trade_data = iunserializer($trade["type"]["data"]);
                        $update_store["displayorder"] = !empty($trade_data["displayorder"]) ? $trade_data["displayorder"] : 0;
                    } else {
                        if ($update_store["is_stick"] == 1) {
                            $update_store["displayorder"] = 256 - $trade["displayorder"];
                        }
                    }
                } else {
                    if ($trade["type"] == "recommendHome") {
                        $update_store["position"] = 1;
                    } else {
                        $update_store["position"] = 2;
                    }
                }
                pdo_update("tiny_wmall_store", $update_store, array("id" => $trade["sid"]));
            }
        }
    }
    return true;
}

?>