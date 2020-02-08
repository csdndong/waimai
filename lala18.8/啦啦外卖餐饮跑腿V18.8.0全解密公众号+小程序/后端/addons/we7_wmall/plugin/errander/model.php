<?php

defined("IN_IA") or exit("Access Denied");
function set_errander_order_data($id, $key, $value)
{
    global $_W;
    $data = get_errander_order_data($id);
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        $data[$keys[0]] = $value;
    } else {
        if ($counts == 2) {
            $data[$keys[0]][$keys[1]] = $value;
        } else {
            if ($counts == 3) {
                $data[$keys[0]][$keys[1]][$keys[2]] = $value;
            }
        }
    }
    pdo_update("tiny_wmall_errander_order", array("data" => iserializer($data)), array("uniacid" => $_W["uniacid"], "id" => $id));
    return true;
}
function get_errander_order_data($idOrorder, $key = "")
{
    global $_W;
    $order = $idOrorder;
    if (!is_array($order) || empty($order["data"])) {
        $order = pdo_get("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "id" => $order), array("data", "id"));
    }
    if (empty($order["data"])) {
        return array();
    }
    $data = iunserializer($order["data"]);
    if (!is_array($data)) {
        $data = array();
    }
    if (empty($key)) {
        return $data;
    }
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        return $data[$key];
    }
    if ($counts == 2) {
        return $data[$keys[0]][$keys[1]];
    }
    if ($counts == 3) {
        return $data[$keys[0]][$keys[1]][$keys[2]];
    }
}
function errander_types()
{
    $data = array("buy" => array("css" => "label label-success", "text" => "随意购", "bg" => "bg-danger"), "delivery" => array("css" => "label label-warning", "text" => "快速送", "bg" => "bg-success"), "pickup" => array("css" => "label label-danger", "text" => "快速取", "bg" => "bg-primary"), "multiaddress" => array("css" => "label label-primary", "text" => "多地址购物", "bg" => "bg-primary"));
    return $data;
}
function errander_order_status()
{
    $data = array(array("css" => "", "text" => "所有", "color" => ""), array("css" => "label label-default", "text" => "待接单", "color" => ""), array("css" => "label label-info", "text" => "正在进行中", "color" => "color-info"), array("css" => "label label-success", "text" => "已完成", "color" => "color-success"), array("css" => "label label-danger", "text" => "已取消", "color" => "color-danger"));
    return $data;
}
function errander_order_delivery_status()
{
    $data = array("1" => array("css" => "label label-default", "text" => "待接单", "color" => ""), "2" => array("css" => "label label-info", "text" => "待取货", "color" => "color-info"), "3" => array("css" => "label label-warning", "text" => "配送中", "color" => "color-warning"), "4" => array("css" => "label label-success", "text" => "已完成", "color" => "color-success"));
    return $data;
}
function get_errander_share()
{
    global $_W;
    $config_errander = $_W["_plugin"]["config"];
    $share = $config_errander["share"];
    $default_link = ivurl("pages/paotui/guide", array(), true);
    $_share = array("title" => $share["title"], "desc" => $share["desc"], "imgUrl" => tomedia($share["imgUrl"]), "link" => empty($share["link"]) ? $default_link : $share["link"]);
    return $_share;
}
function errander_category_fetch($id)
{
    global $_W;
    $category = pdo_get("tiny_wmall_errander_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    if (!empty($category)) {
        $category["tip_min"] = $category["tip_min"] ? $category["tip_min"] : 0;
        $category["tip_max"] = $category["tip_max"] ? $category["tip_max"] : 200;
        $category["label"] = iunserializer($category["label"]);
        $category["delivery_times"] = iunserializer($category["delivery_times"]);
        if (!empty($category["weight_fee"])) {
            $category["weight_fee"] = iunserializer($category["weight_fee"]);
            ksort($category["weight_fee"]);
        }
        $category["multiaddress"] = iunserializer($category["multiaddress"]);
        $category["group_discount"] = iunserializer($category["group_discount"]);
        $category["labels"] = iunserializer($category["labels"]);
        $category["notice"] = iunserializer($category["notice"]);
        if ($_W["is_agent"]) {
            $category["agent"] = get_agent($category["agentid"], array("id", "area"));
        }
    }
    return $category;
}
function errander_delivery_times($idOrCategory)
{
    global $_W;
    if (is_array($idOrCategory)) {
        $category = $idOrCategory;
    } else {
        $category = errander_category_fetch($idOrCategory);
    }
    $days = array();
    $totaytime = strtotime(date("Y-m-d"));
    if (0 < $category["delivery_within_days"]) {
        for ($i = 0; $i <= $category["delivery_within_days"]; $i++) {
            $days[] = date("m-d", $totaytime + $i * 86400);
        }
    } else {
        $days[] = date("m-d");
    }
    $times = $category["delivery_times"];
    $timestamp = array();
    if (!empty($times)) {
        foreach ($times as $key => &$row) {
            if (empty($row["status"])) {
                unset($times[$key]);
                continue;
            }
            $row["delivery_price"] = $category["start_fee"] + $row["fee"];
            $row["delivery_price_cn"] = "配送费" . $row["delivery_price"] . "元起";
            $end = explode(":", $row["end"]);
            $row["timestamp"] = mktime($end[0], $end[1]);
            $timestamp[$key] = $row["timestamp"];
        }
    } else {
        $start = mktime(8, 0);
        $end = mktime(22, 0);
        $i = $start;
        while ($i < $end) {
            $category["delivery_price_cn"] = "配送费" . $category["start_fee"] . "元起";
            $times[] = array("start" => date("H:i", $i), "end" => date("H:i", $i + 1800), "timestamp" => $i + 1800, "fee" => 0, "delivery_price" => $category["start_fee"], "delivery_price_cn" => $category["delivery_price_cn"]);
            $timestamp[] = $i + 1800;
            $i += 1800;
        }
    }
    $data = array("days" => $days, "times" => $times, "timestamp" => $timestamp, "updatetime" => strtotime(date("Y-m-d")) + 86400);
    return $data;
}
function errander_order_fetch($id)
{
    global $_W;
    $id = intval($id);
    $order = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_errander_order") . " WHERE uniacid = :aid AND id = :id", array(":aid" => $_W["uniacid"], ":id" => $id));
    if (empty($order)) {
        return false;
    }
    if ($order["delivery_status"] == 1 && 0 < $_W["deliveryer"]["id"]) {
        $order["deliveryer_fee"] = errander_order_calculate_deliveryer_fee($order, $_W["deliveryer"]);
        $order["deliveryer_total_fee"] = $order["deliveryer_fee"] + $order["delivery_tips"];
    }
    $delivery_status = errander_order_delivery_status();
    $order_status = errander_order_status();
    $pay_types = order_pay_types();
    $order_types = errander_types();
    $order["order_type_cn"] = $order_types[$order["order_type"]]["text"];
    $order["status_cn"] = $order_status[$order["status"]]["text"];
    $order["delivery_status_cn"] = $delivery_status[$order["delivery_status"]]["text"];
    if (empty($order["is_pay"])) {
        $order["pay_type_cn"] = "未支付";
    } else {
        $order["pay_type_cn"] = !empty($pay_types[$order["pay_type"]]["text"]) ? $pay_types[$order["pay_type"]]["text"] : "其他支付方式";
    }
    if (empty($order["delivery_time"])) {
        $order["delivery_time"] = "立即送达";
    }
    if (!empty($order["thumbs"])) {
        $order["thumbs"] = iunserializer($order["thumbs"]);
        foreach ($order["thumbs"] as &$row) {
            $row = tomedia($row);
        }
    }
    if (!is_array($order["thumbs"])) {
        $order["thumbs"] = array();
    }
    $order["category"] = pdo_get("tiny_wmall_errander_page", array("uniacid" => $_W["uniacid"], "id" => $order["order_cid"]), array("id", "name", "thumb"));
    if (empty($order["category"])) {
        $order["category"]["name"] = "该场景已删除";
    } else {
        $order["category"]["thumb"] = tomedia($order["category"]["thumb"]);
    }
    if ($order["order_type"] == "buy") {
        $order["buy_address"] = !empty($order["buy_address"]) ? $order["buy_address"] : "用户未指定,您可自由寻找商户购买商品";
        $order["goods_price"] = !empty($order["goods_price"]) ? $order["goods_price"] : "未填写,请联系顾客沟通";
    }
    if (0 < $order["refund_status"]) {
        $refund_channel = order_refund_channel();
        $refund_status = order_refund_status();
        $order["refund_status_cn"] = $refund_status[$order["refund_status"]]["text"];
        $order["refund_channel_cn"] = $refund_channel[$order["refund_channel"]]["text"];
    }
    $order["agent_serve"] = iunserializer($order["agent_serve"]);
    $order["plateform_serve"] = iunserializer($order["plateform_serve"]);
    $order["data"] = iunserializer($order["data"]);
    if (!empty($order["data"]["order"]["thumbs"])) {
        foreach ($order["data"]["order"]["thumbs"] as &$val) {
            foreach ($val["value"] as &$item) {
                $item = tomedia($item);
            }
        }
    }
    $order["addtime_cn"] = date("Y-m-d H:i", $order["addtime"]);
    return $order;
}
function errander_order_fetch_status_log($id)
{
    global $_W;
    $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_errander_order_status_log") . " WHERE uniacid = :uniacid and oid = :oid order by id asc", array(":uniacid" => $_W["uniacid"], ":oid" => $id), "id");
    return $data;
}
function errander_order_insert_status_log($id, $type, $note = "", $extra = array())
{
    global $_W;
    if (empty($type)) {
        return false;
    }
    $config = $_W["_plugin"]["config"];
    mload()->model("store");
    $order = errander_order_fetch($id);
    $notes = array("place_order" => array("status" => 1, "title" => "订单提交成功", "note" => "单号:" . $order["order_sn"], "ext" => array(array("key" => "pay_time_limit", "title" => "待支付", "note" => "请在订单提交后" . $config["pay_time_limit"] . "分钟内完成支付"))), "pay" => array("status" => 2, "title" => "订单已支付", "note" => "支付成功.付款时间:" . date("Y-m-d H:i:s", $order["paytime"]), "ext" => array(array("key" => "handle_time_limit", "title" => "待接单", "note" => "超出" . $config["handle_time_limit"] . "分钟未接单，平台将自动取消订单"))), "delivery_assign" => array("status" => 3, "title" => "已接单", "note" => ""), "delivery_instore" => array("status" => 4, "title" => "已取货", "note" => ""), "end" => array("status" => 5, "title" => "订单已完成", "note" => "任何意见和吐槽,都欢迎联系我们"), "cancel" => array("status" => 6, "title" => "订单已取消", "note" => ""), "delivery_transfer" => array("status" => 7, "title" => "配送员申请转单", "note" => ""), "direct_transfer" => array("status" => 8, "title" => "配送员发起定向转单申请", "note" => ""), "direct_transfer_agree" => array("status" => 9, "title" => "配送员同意接受转单", "note" => ""), "direct_transfer_refuse" => array("status" => 10, "title" => "配送员拒绝接受转单", "note" => ""));
    $title = $notes[$type]["title"];
    $note = $note ? $note : $notes[$type]["note"];
    $role = !empty($extra["role"]) ? $extra["role"] : $_W["role"];
    $role_cn = !empty($extra["role_cn"]) ? $extra["role_cn"] : $_W["role_cn"];
    $data = array("uniacid" => $_W["uniacid"], "oid" => $id, "status" => $notes[$type]["status"], "role" => $role, "role_cn" => $role_cn, "type" => $type, "title" => $title, "note" => $note, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_errander_order_status_log", $data);
    if (!empty($notes[$type]["ext"])) {
        foreach ($notes[$type]["ext"] as $val) {
            if ($val["key"] == "pay_time_limit" && !$config["pay_time_limit"]) {
                unset($val["note"]);
            }
            if ($val["key"] == "handle_time_limit" && !$config["handle_time_limit"]) {
                unset($val["note"]);
            }
            $data = array("uniacid" => $_W["uniacid"], "oid" => $id, "title" => $val["title"], "note" => $val["note"], "addtime" => TIMESTAMP);
            pdo_insert("tiny_wmall_errander_order_status_log", $data);
        }
    }
    return true;
}
function errander_order_status_notice($id, $status, $note = "")
{
    global $_W;
    $status_arr = array("pay", "delivery_assign", "delivery_instore", "end", "cancel", "delivery_notice");
    if (!in_array($status, $status_arr)) {
        return false;
    }
    $type = $status;
    $order = errander_order_fetch($id);
    if (!empty($order["openid"])) {
        $config_wxapp_basic = $_W["we7_wmall"]["config"]["wxapp"]["basic"];
        $order_channel = $order["order_channel"];
        if ($order_channel == "wxapp" && ($config_wxapp_basic["wxapp_consumer_notice_channel"] == "wechat" || in_array($order["pay_type"], array("credit", "delivery") && empty($order["data"]["formId"])) || empty($order["data"]["formId"]) && $order["data"]["prepay_times"] <= 0)) {
            mload()->model("member");
            $openid = member_wxapp2openid($order["openid"]);
            if (!empty($openid)) {
                $order_channel = "wap";
                $order["openid"] = $openid;
            }
        }
        $acc = TyAccount::create($order["acid"], $order_channel);
        $channel_notice = "wechat";
        if ($order_channel == "wap") {
            if ($type == "pay") {
                $title = "您的跑腿订单已付款,等待平台接单";
                $remark = array("订单类型: " . $order["order_type_cn"], "商品信息: " . $order["goods_name"], "总金　额: " . $order["total_fee"] . "元", "支付方式: " . $order["pay_type_cn"], "支付时间: " . date("Y-m-d H: i", $order["paytime"]));
            } else {
                if ($type == "delivery_assign") {
                    $title = "平台已接受您的跑腿订单， 订单正在处理中";
                    $remark = array("订单类型: " . $order["order_type_cn"], "商品信息: " . $order["goods_name"], "总金　额: " . $order["total_fee"], "接单时间: " . date("Y-m-d H:i:s", $order["delivery_assign_time"]));
                    $end_remark = "";
                } else {
                    if ($type == "delivery_instore") {
                        $title = "配送员已取货，正在配送中";
                        $remark = array("订单类型: " . $order["order_type_cn"], "商品信息: " . $order["goods_name"], "总金　额: " . $order["total_fee"], "收货　码: " . $order["code"]);
                        $end_remark = "配送员已取货，正在为您配送中。请您收到商品后将收货码: " . $order["code"] . " 给配送员";
                    } else {
                        if ($type == "end") {
                            $title = "订单处理完成";
                            $remark = array("订单类型: " . $order["order_type_cn"], "商品信息: " . $order["goods_name"], "总金　额: " . $order["total_fee"], "完成时间: " . date("Y-m-d H: i", time()));
                            $end_remark = "您的订单已处理完成, 如对商品有不满意或投诉请联系客服:" . $_W["we7_wmall"]["config"]["mall"]["mobile"] . ",欢迎您下次光临.戳这里记得给我们的服务评价.";
                        } else {
                            if ($type == "cancel") {
                                $title = "订单已取消";
                                $remark = array("订单类型: " . $order["order_type_cn"], "商品信息: " . $order["goods_name"], "总金　额: " . $order["total_fee"], "取消时间: " . date("Y-m-d H: i", time()));
                            }
                        }
                    }
                }
            }
            if (!empty($note)) {
                if (!is_array($note)) {
                    $remark[] = $note;
                } else {
                    $remark[] = implode("\n", $note);
                }
            }
            if (!empty($end_remark)) {
                $remark[] = $end_remark;
            }
            $remark = implode("\n", $remark);
            $send = tpl_format($title, $order["order_sn"], $order["status_cn"], $remark);
            $url = ivurl("pages/paotui/detail", array("id" => $order["id"]), true);
            $miniprogram = "";
            if ($config_wxapp_basic["tpl_consumer_url"] == "wxapp") {
                $miniprogram = array("appid" => $config_wxapp_basic["key"], "pagepath" => "pages/paotui/detail?id=" . $order["id"]);
            }
            $status = $acc->sendTplNotice($order["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url, $miniprogram);
        } else {
            $channel_notice = "wxapp";
            $send = array("keyword1" => array("value" => "跑腿单", "color" => "#ff510"), "keyword2" => array("value" => $order["order_type_cn"], "color" => "#ff510"), "keyword3" => array("value" => $order["status_cn"], "color" => "#ff510"), "keyword4" => array("value" => $order["accept_username"], "color" => "#ff510"), "keyword5" => array("value" => $order["accept_mobile"], "color" => "#ff510"), "keyword6" => array("value" => date("Y-m-d H:i"), "color" => "#ff510"), "keyword7" => array("value" => $order["final_fee"], "color" => "#ff510"), "keyword8" => array("value" => $order["order_sn"], "color" => "#ff510"));
            $public_tpl = $_W["we7_wmall"]["config"]["wxapp"]["wxtemplate"]["public_tpl"];
            $form_id = $order["data"]["formId"];
            $form_type = "formId";
            if (empty($form_id) && 0 < $order["data"]["prepay_times"]) {
                $form_type = "prepayId";
                $form_id = $order["data"]["prepay_id"];
            }
            if (!empty($form_id)) {
                if ($form_type == "formId") {
                    set_errander_order_data($order["id"], "formId", "");
                } else {
                    $prepay_times = $order["data"]["prepay_times"] - 1;
                    set_errander_order_data($order["id"], "prepay_times", $prepay_times);
                }
                $status = $acc->sendTplNotice($order["openid"], $public_tpl, $send, "pages/paotui/detail?id=" . $order["id"], $form_id);
            }
        }
        if (is_error($status)) {
            slog("wxtplNotice", "跑腿订单状态改变微信通知顾客-order_id:" . $order["id"] . ", 渠道:" . $channel_notice, $send, $status["message"]);
        }
    }
    return true;
}
function errander_order_insert_refund_log($id, $type, $note = "")
{
    global $_W;
    if (empty($type)) {
        return false;
    }
    $notes = array("apply" => array("status" => 1, "title" => "提交退款申请", "note" => ""), "handel" => array("status" => 2, "title" => (string) $_W["we7_wmall"]["config"]["mall"]["title"] . "接受退款申请", "note" => ""), "success" => array("status" => 3, "title" => "退款成功", "note" => ""), "fail" => array("status" => 4, "title" => "退款失败", "note" => ""));
    $title = $notes[$type]["title"];
    $note = $note ? $note : $notes[$type]["note"];
    $data = array("uniacid" => $_W["uniacid"], "order_type" => "errander", "sid" => 0, "oid" => $id, "status" => $notes[$type]["status"], "type" => $type, "title" => $title, "note" => $note, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_order_refund_log", $data);
    return true;
}
function errander_order_fetch_refund_status_log($id)
{
    global $_W;
    $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order_refund_log") . " WHERE uniacid = :uniacid and oid = :oid and order_type = :order_type order by id asc", array(":uniacid" => $_W["uniacid"], ":oid" => $id, ":order_type" => "errander"), "id");
    return $data;
}
function errander_order_deliveryer_notice($id, $type, $deliveryer_id = 0, $note = "")
{
    global $_W;
    $order = errander_order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    $_W["agentid"] = $order["agentid"];
    mload()->model("deliveryer");
    if (empty($deliveryer_id)) {
        $filter = array("order_type" => "is_errander", "over_max_collect_show" => 0);
        $deliveryers = deliveryer_fetchall(0, $filter);
        if (empty($deliveryers)) {
            errander_order_manager_notice($order["id"], "no_working_deliveryer");
            return false;
        }
    } else {
        $deliveryer = deliveryer_fetch($deliveryer_id);
    }
    $account = $order["acid"];
    $channel_notice = "wechat";
    $config_wxapp_deliveryer = $_W["we7_wmall"]["config"]["wxapp"]["deliveryer"];
    if (MODULE_FAMILY == "wxapp" && $config_wxapp_deliveryer["wxapp_deliveryer_notice_channel"] == "wxapp") {
        $channel_notice = "wxapp";
        $account = $config_wxapp_deliveryer;
    }
    $acc = TyAccount::create($account, $channel_notice);
    if ($type == "new_delivery") {
        $total_fee = $order["deliveryer_fee"] + $order["delivery_tips"];
        $title = "您有新的跑腿订单,配送地址为" . $order["accept_address"] . ",本单可收入" . $total_fee . "元";
        $remark = array("下单时间: " . date("Y-m-d H:i", $order["addtime"]), "配送　费: " . $order["deliveryer_fee"] . "元", "小　　费: " . $order["delivery_tips"] . "元", "本单收入: " . ($order["deliveryer_fee"] + $order["delivery_tips"]) . "元", "收货　人: " . $order["accept_username"], "送货地址: " . $order["accept_address"]);
        $remark = implode("\n", $remark);
        $url = imurl("delivery/order/errander", array(), true);
    } else {
        if ($type == "delivery_wait") {
            $title = "平台有新的跑腿订单, 配送地址为" . $order["accept_address"] . ", 快去抢单吧";
            $remark = array("订单类型: " . $order["order_type_cn"], "下单时间: " . date("Y-m-d H:i", $order["addtime"]));
            if ($order["order_type"] == "buy") {
                $remark[] = "购买商品: " . $order["goods_name"];
                if (!empty($order["goods_price"])) {
                    $remark[] = "预期价格: " . $order["goods_price"] . "元";
                }
                $remark[] = "购买地址: " . $order["buy_address"];
            } else {
                if ($order["order_type"] == "delivery") {
                    $remark[] = "物品信息: " . $order["goods_name"];
                    if (!empty($order["goods_price"])) {
                        $remark[] = "物品价值: " . $order["goods_price"];
                    }
                    $remark[] = "发货地址: " . $order["buy_address"];
                    $remark[] = "联系　人: " . $order["buy_username"];
                } else {
                    $remark[] = "物品信息: " . $order["goods_name"];
                    if (!empty($order["goods_price"])) {
                        $remark[] = "物品价值: " . $order["goods_price"];
                    }
                    $remark[] = "取货地址: " . $order["buy_address"];
                    $remark[] = "联系　人: " . $order["buy_username"];
                }
            }
            $remark[] = "收货　人: " . $order["accept_username"]  . "\n送货地址: " . $order["accept_address"];
            $remark = implode("\n", $remark);
            $url = imurl("delivery/order/errander/list", array(), true);
        } else {
            if ($type == "cancel") {
                $title = "收货地址为" . $order["accept_address"] . ", 收货人为" . $order["accept_username"] . "的" . $order["order_type_cn"] . "订单已取消,请及时调整配送顺序";
                $remark = array("订单类型: " . $order["order_type_cn"], "收货人: " . $order["accept_username"], "收货地址: " . $order["accept_address"]);
                $remark = implode("\n", $remark);
                $url = imurl("delivery/order/errander/detail", array("id" => $order["id"]), true);
            } else {
                if ($type == "direct_transfer") {
                    $from_deliveryer = $note["from_deliveryer"];
                    $title = (string) $from_deliveryer["title"] . "向您发起跑腿单转单申请，收货地址为" . $order["accept_address"] . ",请及时做出回复";
                    $remark = array("下单时间: " . date("Y-m-d H:i", $order["addtime"]), "转单时间: " . date("Y-m-d H:i", TIMESTAMP), "小　　费: " . $order["delivery_tips"] . "元", "收货　人: " . $order["accept_username"], "送货地址: " . $order["accept_address"], "订单类型: " . $order["order_type_cn"]);
                    $remark = implode("\n", $remark);
                    $url = imurl("delivery/order/errander/list", array("status" => $order["delivery_status"]), true);
                } else {
                    if ($type == "direct_transfer_refuse") {
                        $from_deliveryer = $note["from_deliveryer"];
                        $to_deliveryer = $note["to_deliveryer"];
                        $title = (string) $to_deliveryer["title"] . "拒绝了收获地址为" . $order["accept_address"] . "的跑腿单定向转单申请,此订单将由您继续配送";
                        $remark = array("下单时间: " . date("Y-m-d H:i", $order["addtime"]), "收货　人: " . $order["accept_username"], "送货地址: " . $order["accept_address"], "订单类型: " . $order["order_type_cn"]);
                        $remark = implode("\n", $remark);
                        $url = imurl("delivery/order/errander/list", array("status" => $order["delivery_status"]), true);
                    }
                }
            }
        }
    }
    if ($channel_notice == "wechat") {
        $miniprogram = "";
        if ($config_wxapp_deliveryer["tpl_deliveryer_url"] == "wxapp") {
            $miniprogram = array("appid" => $config_wxapp_deliveryer["key"], "pagepath" => "pages/errander/list");
        }
        $send = tpl_format($title, $order["order_sn"], $order["status_cn"], $remark);
    } else {
        $data = array($title, "******", "#" . $order["serial_cn"], "跑腿单", date("Y-m-d H:i", $order["addtime"]), $order["note"], "￥" . $order["final_fee"]);
        $send = format_wxapp_tpl($data);
        $url = "pages/errander/list";
    }
    $durl = idurl("pages/paotui/index", array(), true);
    if (in_array($type, array("new_delivery", "direct_transfer", "direct_transfer_refuse"))) {
        if (empty($deliveryer)) {
            return error(-1, "配送员不存在");
        }
        if ($deliveryer["extra"]["accept_wechat_notice"] == 1) {
            if ($channel_notice == "wechat") {
                $status = $acc->sendTplNotice($deliveryer["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url, $miniprogram);
            } else {
                $status = $acc->sendTplNotice($deliveryer["openid_wxapp_deliveryer"], $config_wxapp_deliveryer["wxtemplate"]["status_tpl"], $send, $url);
            }
            if (is_error($status)) {
                slog("wxtplNotice", "跑腿订单通知配送员抢单:" . $deliveryer["title"], $send, $status["message"]);
            }
        }
        if (!empty($deliveryer["mobile"]) && $deliveryer["extra"]["accept_voice_notice"] == 1 && !in_array($type, array("direct_transfer", "direct_transfer_refuse"))) {
            mload()->model("sms");
            $data = sms_singlecall($deliveryer["mobile"], array("name" => $deliveryer["title"], "deliveryer_fee" => $order["deliveryer_total_fee"]), "errander_deliveryer");
            if (is_error($data)) {
                slog("alidayuCall", "跑腿订单动阿里大鱼语音通知配送员抢单:" . $deliveryer["title"], array(), $data["message"]);
            }
        }
        if (!empty($deliveryer["token"])) {
            $audience = array("alias" => array($deliveryer["token"]));
            if ($type == "new_delivery") {
                Jpush_deliveryer_send("您有新的跑腿配送订单", $title, array("url" => $durl, "id" => $order["id"], "voice_text" => $title, "notify_type" => "orderassign", "redirect_type" => "errander", "redirect_extra" => 2), $audience);
            } else {
                if ($type == "direct_transfer") {
                    Jpush_deliveryer_send((string) $from_deliveryer["title"] . "向您发起转单申请", $title, array("url" => $durl, "id" => $order["id"], "voice_text" => $title, "notify_type" => "orderDirectTransfer", "redirect_type" => "errander", "redirect_extra" => $order["delivery_status"]), $audience);
                } else {
                    if ($type == "direct_transfer_refuse") {
                        Jpush_deliveryer_send((string) $to_deliveryer["title"] . "拒绝了收获地址为" . $order["address"] . "的定向转单申请", $title, array("url" => $durl, "id" => $order["id"], "voice_text" => $title, "notify_type" => "orderDirectTransferRefuse", "redirect_type" => "errander", "redirect_extra" => $order["delivery_status"]), $audience);
                    }
                }
            }
        }
    } else {
        if ($type == "delivery_wait") {
            mload()->model("sms");
            $url = imurl("delivery/order/errander", array(), true);
            foreach ($deliveryers as $deliveryer) {
                if (!empty($deliveryer["mobile"]) && $deliveryer["extra"]["accept_voice_notice"] == 1) {
                    $order["deliveryer_fee"] = errander_order_calculate_deliveryer_fee($order, $deliveryer);
                    $order["deliveryer_total_fee"] = $order["deliveryer_fee"] + $order["delivery_tips"];
                    $data = sms_singlecall($deliveryer["mobile"], array("name" => $deliveryer["title"], "deliveryer_fee" => $order["deliveryer_total_fee"]), "errander_deliveryer");
                    if (is_error($data)) {
                        slog("alidayuCall", "跑腿订单动阿里大鱼语音通知配送员抢单:" . $deliveryer["title"], array(), $data["message"]);
                    }
                }
                if ($deliveryer["extra"]["accept_wechat_notice"] == 1) {
                    if ($channel_notice == "wechat") {
                        $status = $acc->sendTplNotice($deliveryer["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url, $miniprogram);
                    } else {
                        $status = $acc->sendTplNotice($deliveryer["openid_wxapp_deliveryer"], $config_wxapp_deliveryer["wxtemplate"]["status_tpl"], $send, $url);
                    }
                    if (is_error($status)) {
                        slog("wxtplNotice", "跑腿订单通知配送员抢单:" . $deliveryer["title"], $send, $status["message"]);
                    }
                }
            }
            Jpush_deliveryer_send("您有新的跑腿待抢订单", $title, array("url" => $durl, "id" => $order["id"], "voice_text" => $title, "notify_type" => "ordernew", "redirect_type" => "errander", "redirect_extra" => 1));
        } else {
            if ($type == "cancel") {
                $deliveryer = $deliveryers[$deliveryer_id];
                if (empty($deliveryer)) {
                    return error(-1, "配送员不存在");
                }
                if ($deliveryer["extra"]["accept_wechat_notice"] == 1) {
                    if ($channel_notice == "wechat") {
                        $status = $acc->sendTplNotice($deliveryer["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, $url, $miniprogram);
                    } else {
                        $status = $acc->sendTplNotice($deliveryer["openid_wxapp_deliveryer"], $config_wxapp_deliveryer["wxtemplate"]["status_tpl"], $send, $url);
                    }
                    if (is_error($status)) {
                        slog("wxtplNotice", "跑腿订单通知配送员顾客已取消订单-" . $deliveryer["title"], $send, $status["message"]);
                    }
                }
                if (!empty($deliveryer["token"])) {
                    $audience = array("alias" => array($deliveryer["token"]));
                    Jpush_deliveryer_send("订单取消通知", $title, array("url" => $durl, "id" => $order["id"], "voice_text" => $title, "notify_type" => "ordercancel", "redirect_type" => "errander", "redirect_extra" => 2), $audience);
                }
            }
        }
    }
    return true;
}
function errander_order_analyse($id, $extra = array())
{
    global $_W;
    $order = errander_order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    $_W["agentid"] = $order["agentid"];
    $filter = array("order_type" => "is_errander", "over_max_collect_show" => 0);
    if ($extra["channel"] == "plateform_dispatch") {
        $filter["over_max_collect_show"] = 1;
    }
    $deliveryers = deliveryer_fetchall(0, $filter);
    if (!empty($deliveryers)) {
        foreach ($deliveryers as &$deliveryer) {
            $deliveryer["order_id"] = $id;
            if (empty($order["buy_location_x"]) || empty($order["buy_location_y"]) || empty($deliveryer["location_y"]) || empty($deliveryer["location_x"])) {
                $deliveryer["store2deliveryer_distance"] = "未知";
                $deliveryer["store2user_distance"] = "未知";
            } else {
                $deliveryer["store2deliveryer_distance"] = distanceBetween($order["buy_location_y"], $order["buy_location_x"], $deliveryer["location_y"], $deliveryer["location_x"]);
                $deliveryer["store2deliveryer_distance"] = round($deliveryer["store2deliveryer_distance"] / 1000, 2) . "km";
                $deliveryer["store2user_distance"] = $order["distance"] . "km";
                $deliveryer["user2deliveryer_distance"] = distanceBetween($order["accept_location_y"], $order["accept_location_x"], $deliveryer["location_y"], $deliveryer["location_x"]);
                $deliveryer["user2deliveryer_distance"] = round($deliveryer["user2deliveryer_distance"] / 1000, 2) . "km";
            }
        }
        if (empty($order["buy_location_x"]) || empty($order["buy_location_y"])) {
            $deliveryers = array_sort($deliveryers, "user2deliveryer_distance");
        } else {
            $deliveryers = array_sort($deliveryers, "store2deliveryer_distance");
        }
        $order["deliveryers"] = $deliveryers;
        return $order;
    } else {
        return error(-1, "没有平台配送员，无法进行自动调度");
    }
}
function errander_order_assign_deliveryer($order_id, $deliveryer_id, $update_deliveryer = false)
{
    global $_W;
    $order = errander_order_fetch($order_id);
    if (empty($order)) {
        return error(-1, "订单不存在或已经删除");
    }
    if ($order["status"] == 3) {
        return error(-1, "订单已处理完成, 不能指定配送员");
    }
    if ($order["status"] == 4) {
        return error(-1, "订单已取消, 不能指定配送员");
    }
    if ($order["status"] == 2 && !$update_deliveryer) {
        return error(-1, "该订单已经分配给其他配送员，不能重新指定配送员");
    }
    $_W["agentid"] = $order["agentid"];
    mload()->model("deliveryer");
    $deliveryer = deliveryer_fetch($deliveryer_id);
    if (empty($deliveryer)) {
        return error(-1, "配送员不存在或已经删除,请指定其他配送员配送");
    }
    if (empty($deliveryer["is_errander"])) {
        return error(-1, "配送员没有配送平台跑腿单的权限");
    }
    if (0 < $deliveryer["collect_max_errander"] && $deliveryer["collect_max_errander"] < $deliveryer["order_errander_num"] && !$update_deliveryer) {
        return error(-1, "每人最多可抢" . $deliveryer["collect_max_errander"] . "个跑腿单");
    }
    $update = array("status" => 2, "deliveryer_id" => $deliveryer_id, "delivery_assign_time" => TIMESTAMP, "delivery_collect_type" => $update_deliveryer ? 2 : 1, "delivery_status" => 2);
    $update["deliveryer_fee"] = errander_order_calculate_deliveryer_fee($order, $deliveryer);
    $update["deliveryer_total_fee"] = $update["deliveryer_fee"] + $order["delivery_tips"];
    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order_id));
    if (0 < $order["deliveryer_id"]) {
        deliveryer_order_num_update($order["deliveryer_id"]);
    }
    errander_order_update_bill($order["id"]);
    deliveryer_order_num_update($deliveryer_id);
    $note = "配送员：" . $deliveryer["title"] . ", 手机号：" . $deliveryer["mobile"];
    errander_order_insert_status_log($order_id, "delivery_assign", $note);
    $remark = array("配送员：" . $deliveryer["title"], "手机号：" . $deliveryer["mobile"]);
    errander_order_status_notice($order_id, "delivery_assign", $remark);
    errander_order_deliveryer_notice($order_id, "new_delivery", $deliveryer_id);
    return error(0, "订单分派配送员成功");
}
function errander_order_manager_notice($order_id, $type, $note = "")
{
    global $_W;
    $maneger = $_W["we7_wmall"]["config"]["manager"];
    if (empty($maneger)) {
        return error(-1, "管理员信息不完善");
    }
    $order = errander_order_fetch($order_id);
    if (empty($order)) {
        return error(-1, "订单不存在或已经删除");
    }
    $acc = WeAccount::create($order["acid"]);
    if ($type == "new_delivery") {
        $title = "平台有新的跑腿订单，请尽快调度处理";
        $remark = array("订单类型: " . $order["order_type_cn"], "商品信息: " . $order["goods_name"], "总金　额: " . $order["total_fee"], "支付方式: " . $order["pay_type_cn"], "支付时间: " . date("Y-m-d H: i", $order["paytime"]));
    } else {
        if ($type == "dispatch_error") {
            $title = "平台有新的跑腿订单，系统自动调度失败，请登录后台人工调度";
            $remark = array("订单类型: " . $order["order_type_cn"], "商品信息: " . $order["goods_name"], "总金　额: " . $order["total_fee"]);
        } else {
            if ($type == "no_working_deliveryer") {
                $title = "平台有新的待配送跑腿订单,但没有接单中的配送员,请尽快协调";
                $remark = array("订单类型: 跑腿订单");
            }
        }
    }
    if (!empty($note)) {
        if (!is_array($note)) {
            $remark[] = $note;
        } else {
            $remark[] = implode("\n", $note);
        }
    }
    if (!empty($end_remark)) {
        $remark[] = $end_remark;
    }
    $remark = implode("\n", $remark);
    $send = tpl_format($title, $order["order_sn"], $order["status_cn"], $remark);
    $status = $acc->sendTplNotice($maneger["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send);
    if (is_error($status)) {
        slog("wxtplNotice", "跑腿订单通知平台管理员抢单", $send, $status["message"]);
    }
    return $status;
}
function errander_order_status_update($id, $type, $extra = array())
{
    global $_W;
    $order = errander_order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    $_W["agentid"] = $order["agentid"];
    $config = get_plugin_config("errander");
    if ($type == "dispatch") {
        if (empty($order["is_pay"])) {
            return error(-1, "订单尚未支付，支付后才能进行调度派单");
        }
        if ($config["dispatch_mode"] == 1) {
            errander_order_deliveryer_notice($id, "delivery_wait");
        } else {
            if ($config["dispatch_mode"] == 2) {
            } else {
                $order = errander_order_analyse($id);
                if (is_error($order)) {
                    errander_order_manager_notice($id, "dispatch_error", "失败原因：" . $order["message"]);
                }
                $deliveryer = array_shift($order["deliveryers"]);
                $status = errander_order_assign_deliveryer($id, $deliveryer["id"]);
            }
        }
    } else {
        if ($type == "pay") {
            errander_order_insert_status_log($id, "pay");
            errander_order_status_notice($id, "pay");
            errander_order_manager_notice($id, "new_delivery");
        } else {
            if ($type == "cancel") {
                if ($order["status"] == 3) {
                    return error(-1, "系统已完成， 不能取消订单");
                }
                if ($order["status"] == 4) {
                    return error(-1, "系统已取消， 不能取消订单");
                }
                if (2 <= $order["delivery_status"] && $_W["role"] == "consumer") {
                    return error(-1, "配送员已接单， 不能取消订单");
                }
                if ($_W["role"] == "deliveryer") {
                    if (empty($extra["note"])) {
                        return error(-1, "订单取消原因不能为空");
                    }
                    if (empty($extra["deliveryer_id"])) {
                        return error(-1, "配送员不存在");
                    }
                    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $extra["deliveryer_id"]), array("id", "perm_cancel", "status"));
                    if (empty($deliveryer)) {
                        return error(-1, "配送员不存在");
                    }
                    if ($deliveryer["status"] != 1) {
                        return error(-1, "配送员已被删除");
                    }
                    $deliveryer["perm_cancel"] = iunserializer($deliveryer["perm_cancel"]);
                    if (!$deliveryer["perm_cancel"]["status_errander"]) {
                        return error(-1, "您没有取消订单的权限");
                    }
                    if ($order["deliveryer_id"] != $deliveryer["id"]) {
                        return error(-1, "该订单不是您配送，不能取消");
                    }
                }
                mload()->model("deliveryer");
                deliveryer_order_num_update($order["deliveryer_id"]);
                $config_activity = $_W["we7_wmall"]["config"]["activity"];
                $return_redpacket_status = intval($config_activity["return_redpacket_status"]);
                if ($return_redpacket_status == 1 && 0 < $order["discount_fee"]) {
                    pdo_update("tiny_wmall_activity_redpacket_record", array("status" => 1, "usetime" => 0, "order_id" => 0), array("uniacid" => $_W["uniacid"], "uid" => $order["uid"], "order_id" => $order["id"], "scene" => "paotui"));
                }
                if (!$order["is_pay"] || $order["final_fee"] <= 0) {
                    pdo_update("tiny_wmall_errander_order", array("status" => 4), array("uniacid" => $_W["uniacid"], "id" => $id));
                    errander_order_insert_status_log($id, "cancel", $extra["note"]);
                    errander_order_status_notice($id, "cancel", $extra["note"]);
                } else {
                    if (0 < $order["refund_status"]) {
                        return error(-1, "退款申请处理中, 请勿重复发起");
                    }
                    $update = array("status" => 4, "refund_status" => 1, "refund_out_no" => date("YmdHis") . random(10, true), "refund_apply_time" => TIMESTAMP);
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    errander_order_insert_status_log($id, "cancel", $extra["note"]);
                    errander_order_insert_refund_log($id, "apply");
                    $extra["note"] = $extra["note"] ? $extra["note"] : "未知";
                    $note = array("取消原因: " . $extra["note"], "退款金额: " . $order["final_fee"] . "元", "已付款项会在1-3工作日内返回您的账号");
                    errander_order_status_notice($id, "cancel", $note);
                    errander_order_refund_notice($id, "apply");
                    if (0 < $order["deliveryer_id"] && $type != "delivery_cancel") {
                        errander_order_deliveryer_notice($id, "cancel", $order["deliveryer_id"]);
                    }
                    return error(0, array("is_refund" => 1));
                }
            } else {
                if ($type == "end") {
                    if ($order["status"] == 3) {
                        return error(-1, "系统已完成， 请勿重复操作");
                    }
                    if ($order["status"] == 4) {
                        return error(-1, "系统已取消， 不能在进行其他操作");
                    }
                    $update = array("status" => 3, "delivery_status" => 4, "delivery_success_time" => TIMESTAMP);
                    if (0 < $order["deliveryer_id"]) {
                        $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]));
                        if (!empty($deliveryer)) {
                            mload()->model("deliveryer");
                            deliveryer_order_num_update($deliveryer["id"]);
                            $update["delivery_success_location_x"] = $deliveryer["location_x"];
                            $update["delivery_success_location_y"] = $deliveryer["location_y"];
                        }
                    }
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    $total_deliveryer_fee = $order["deliveryer_fee"] + $order["delivery_tips"];
                    if (0 < $total_deliveryer_fee) {
                        mload()->model("deliveryer");
                        deliveryer_update_credit2($order["deliveryer_id"], $total_deliveryer_fee, 1, $id, "", "errander");
                    }
                    if (0 < $order["agentid"]) {
                        $remark = "跑腿订单,id:" . $order["id"];
                        agent_update_account($order["agentid"], $order["agent_final_fee"], 1, $order["id"], $remark, "errander");
                    }
                    if (check_plugin_perm("svip")) {
                        pload()->model("svip");
                        svip_task_finish_check($order["uid"], "oneErranderFee", $order);
                    }
                    if (check_plugin_perm("spread")) {
                        pload()->model("spread");
                        spread_order_balance($id, "paotui");
                    }
                    $credit1_config = $config["credit"]["credit1"];
                    if (!empty($credit1_config) && $credit1_config["status"] == 1 && 0 < $credit1_config["grant_num"] && 0 < $order["uid"]) {
                        $credit1 = $credit1_config["grant_num"];
                        if ($credit1_config["grant_type"] == 2) {
                            $credit1 = round($order["final_fee"] * $credit1_config["grant_num"], 2);
                        }
                        if (0 < $credit1) {
                            mload()->model("member");
                            $result = member_credit_update($order["uid"], "credit1", $credit1, array(0, "跑腿订单完成, 赠送:" . $credit1 . "积分"));
                            if (is_error($result)) {
                                slog("credit1Update", "跑腿下单送积分-order_id:" . $order["id"], array("order_id" => $order["id"], "uid" => $order["uid"], "credit_type" => "credit1"), $result["message"]);
                            }
                        }
                    }
                    errander_order_insert_status_log($id, "end", $extra["note"]);
                    errander_order_status_notice($id, "end", $extra["note"]);
                    return error(0, "订单完成成功");
                }
                if ($type == "delivery_assign") {
                    if ($order["status"] == 3) {
                        return error(-1, "系统已完成， 不能抢单或分配订单");
                    }
                    if ($order["status"] == 4) {
                        return error(-1, "系统已取消， 不能抢单或分配订单");
                    }
                    if (0 < $order["deliveryer_id"]) {
                        return error(-1, "来迟了, 该订单已被别人接单");
                    }
                    if (empty($extra["deliveryer_id"])) {
                        return error(-1, "配送员id不存在");
                    }
                    mload()->model("deliveryer");
                    $deliveryer = deliveryer_fetch($extra["deliveryer_id"]);
                    if (empty($deliveryer)) {
                        return error(-1, "配送员不存在");
                    }
                    if (0 < $deliveryer["collect_max_errander"]) {
                        $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $deliveryer["id"]);
                        $num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_errander_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and status = 2", $params);
                        $num = intval($num);
                        if ($deliveryer["collect_max_errander"] <= $num) {
                            return error(-1, "每人最多可抢" . $deliveryer["collect_max_errander"] . "个跑腿单");
                        }
                    }
                    $update = array("status" => 2, "delivery_status" => 2, "deliveryer_id" => $extra["deliveryer_id"], "delivery_handle_type" => !empty($extra["delivery_handle_type"]) ? $extra["delivery_handle_type"] : "wechat", "delivery_assign_time" => TIMESTAMP);
                    $update["deliveryer_fee"] = errander_order_calculate_deliveryer_fee($order, $deliveryer);
                    $update["deliveryer_total_fee"] = $update["deliveryer_fee"] + $order["delivery_tips"];
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    errander_order_update_bill($order["id"]);
                    mload()->model("deliveryer");
                    deliveryer_order_num_update($deliveryer["id"]);
                    $note = "配送员：" . $deliveryer["title"] . ", 手机号：" . $deliveryer["mobile"];
                    if ($order["type"] == "buy") {
                        $note .= ",正在为您购买商品";
                    }
                    errander_order_insert_status_log($id, "delivery_assign", $note);
                    $remark = array("配送员：" . $deliveryer["title"], "手机号：" . $deliveryer["mobile"]);
                    errander_order_status_notice($id, "delivery_assign", $remark);
                    return error(0, "抢单成功");
                }
                if ($type == "delivery_instore") {
                    if ($order["status"] == 3) {
                        return error(-1, "系统已完成， 不能变更状态");
                    }
                    if ($order["status"] == 4) {
                        return error(-1, "系统已取消， 不能变更状态");
                    }
                    if (empty($extra["deliveryer_id"])) {
                        return error(-1, "配送员不存在");
                    }
                    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $extra["deliveryer_id"]));
                    if (empty($deliveryer)) {
                        return error(-1, "配送员不存在");
                    }
                    if ($deliveryer["status"] != 1) {
                        return error(-1, "配送员已被删除");
                    }
                    if ($order["deliveryer_id"] != $deliveryer["id"]) {
                        return error(-1, "该订单不是您配送，不能确认取货");
                    }
                    $update = array("status" => 2, "delivery_status" => 3, "delivery_instore_time" => TIMESTAMP, "delivery_handle_type" => !empty($extra["delivery_handle_type"]) ? $extra["delivery_handle_type"] : "wechat");
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    $note = "配送员：" . $deliveryer["title"] . ", 手机号：" . $deliveryer["mobile"] . ", 请在收到商品后收货码: <span class='color-danger'>" . $order["code"] . "</span> 给配送员";
                    errander_order_insert_status_log($id, "delivery_instore", $note);
                    errander_order_status_notice($id, "delivery_instore");
                    return error(0, "确认取货成功");
                }
                if ($type == "delivery_success") {
                    if ($order["status"] == 3) {
                        return error(-1, "系统已完成， 不能变更状态");
                    }
                    if ($order["status"] == 4) {
                        return error(-1, "系统已取消， 不能变更状态");
                    }
                    if (empty($extra["deliveryer_id"])) {
                        return error(-1, "配送员不存在");
                    }
                    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $extra["deliveryer_id"]));
                    if (empty($deliveryer)) {
                        return error(-1, "配送员不存在");
                    }
                    if ($deliveryer["status"] != 1) {
                        return error(-1, "配送员已被删除");
                    }
                    if ($order["deliveryer_id"] != $deliveryer["id"]) {
                        return error(-1, "该订单不是您配送，不能确认完成");
                    }
                    if ($config["verification_code"] == 1) {
                        if (empty($extra["code"])) {
                            return error(-1, "收货码不能为空");
                        }
                        if ($extra["code"] != $order["code"]) {
                            return error(-1, "收货码有误");
                        }
                    }
                    $update = array("status" => 3, "delivery_status" => 4, "delivery_success_time" => TIMESTAMP, "delivery_success_location_x" => $deliveryer["location_x"], "delivery_success_location_y" => $deliveryer["location_y"]);
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    mload()->model("deliveryer");
                    deliveryer_order_num_update($deliveryer["id"]);
                    $total_deliveryer_fee = $order["deliveryer_fee"] + $order["delivery_tips"];
                    if (0 < $total_deliveryer_fee) {
                        mload()->model("deliveryer");
                        deliveryer_update_credit2($order["deliveryer_id"], $total_deliveryer_fee, 1, $id, "", "errander");
                    }
                    if (0 < $order["agentid"]) {
                        $remark = "跑腿订单,id:" . $order["id"];
                        agent_update_account($order["agentid"], $order["agent_final_fee"], 1, $order["id"], $remark, "errander");
                    }
                    errander_order_insert_status_log($id, "end");
                    errander_order_status_notice($id, "end");
                    return error(0, "确认送达成功");
                }
                if ($type == "delivery_transfer") {
                    if ($order["status"] == 3) {
                        return error(-1, "系统已完成， 不能申请转单");
                    }
                    if ($order["status"] == 4) {
                        return error(-1, "系统已取消， 不能申请转单");
                    }
                    if (empty($extra["reason"])) {
                        return error(-1, "转单理由不能为空");
                    }
                    if (empty($extra["deliveryer_id"])) {
                        return error(-1, "配送员不存在");
                    }
                    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $extra["deliveryer_id"]), array("id", "perm_transfer", "status"));
                    if (empty($deliveryer)) {
                        return error(-1, "配送员不存在");
                    }
                    if ($deliveryer["status"] != 1) {
                        return error(-1, "配送员已被删除");
                    }
                    if ($order["deliveryer_id"] != $deliveryer["id"]) {
                        return error(-1, "该订单不是您配送，不能申请转单");
                    }
                    $deliveryer["perm_transfer"] = iunserializer($deliveryer["perm_transfer"]);
                    if (!$deliveryer["perm_transfer"]["status_errander"]) {
                        return error(-1, "您没有转单权限，请联系平台管理员");
                    }
                    $transfer_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer_transfer_log") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and order_type = :order_type and stat_day = :stat_day", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $extra["deliveryer_id"], ":order_type" => "errander", ":stat_day" => date("Ymd")));
                    if (0 < $deliveryer["perm_transfer"]["max_errander"] && $deliveryer["perm_transfer"]["max_errander"] <= $transfer_num) {
                        return error(-1, "每天最多可以转单" . $deliveryer["perm_transfer"]["max_errander"] . "次,您已超过限定次数");
                    }
                    $transfer_log = array("uniacid" => $_W["uniacid"], "deliveryer_id" => $extra["deliveryer_id"], "order_type" => "errander", "order_id" => $order["id"], "reason" => $extra["reason"], "addtime" => TIMESTAMP, "stat_year" => date("Y"), "stat_month" => date("Ym"), "stat_day" => date("Ymd"));
                    pdo_insert("tiny_wmall_deliveryer_transfer_log", $transfer_log);
                    $update = array("delivery_status" => 1, "deliveryer_id" => 0, "delivery_handle_type" => "wechat");
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
                    errander_order_insert_status_log($order["id"], "delivery_transfer", "转单理由:" . $extra["reason"] . ",等待其他配送员接单");
                    errander_order_deliveryer_notice($order["id"], "delivery_wait");
                    mload()->model("deliveryer");
                    deliveryer_order_num_update($deliveryer["id"]);
                    return error(0, "转单成功");
                }
                if ($type == "direct_transfer") {
                    if (empty($extra["from_deliveryer_id"])) {
                        return error(-1, "配送员不存在");
                    }
                    if (empty($extra["to_deliveryer_id"])) {
                        return error(-1, "转单目标配送员不存在");
                    }
                    $to_deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $extra["to_deliveryer_id"], "is_errander" => 1, "work_status" => 1), array("id", "title", "status"));
                    if (empty($to_deliveryer)) {
                        return error(-1, "转单目标配送员不存在");
                    }
                    if ($to_deliveryer["status"] != 1) {
                        return error(-1, "转单目标配送员已被删除");
                    }
                    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $extra["from_deliveryer_id"]), array("id", "title", "perm_transfer", "status"));
                    if (empty($deliveryer)) {
                        return error(-1, "配送员不存在");
                    }
                    if ($deliveryer["status"] != 1) {
                        return error(-1, "配送员已被删除");
                    }
                    $deliveryer["perm_transfer"] = iunserializer($deliveryer["perm_transfer"]);
                    if (!$deliveryer["perm_transfer"]["status_errander"]) {
                        return error(-1, "您没有转单权限");
                    }
                    if ($order["deliveryer_id"] != $deliveryer["id"]) {
                        return error(-1, "该订单不是您配送，不能转单");
                    }
                    $transfer_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer_transfer_log") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and order_type = :order_type and stat_day = :stat_day", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $extra["deliveryer_id"], ":order_type" => "errander", ":stat_day" => date("Ymd")));
                    if (0 < $deliveryer["perm_transfer"]["max_errander"] && $deliveryer["perm_transfer"]["max_errander"] <= $transfer_num) {
                        return error(-1, "每天最多可以转单" . $deliveryer["perm_transfer"]["max_errander"] . "次,您已超过限定次数");
                    }
                    $order["data"]["transfer_delivery_reason"] = $extra["note"];
                    $order["data"]["original_delivery_collect_type"] = $order["delivery_collect_type"];
                    $update = array("delivery_collect_type" => 3, "transfer_deliveryer_id" => $extra["to_deliveryer_id"], "transfer_delivery_status" => 1, "data" => iserializer($order["data"]));
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
                    errander_order_insert_status_log($order["id"], "direct_transfer", "目标配送员:" . $to_deliveryer["title"] . ",转单理由:" . $extra["reason"] . ",等待其他配送员回复");
                    $extra["from_deliveryer"] = $deliveryer;
                    errander_order_deliveryer_notice($order["id"], "direct_transfer", $extra["to_deliveryer_id"], $extra);
                    return error(0, "发起定向转单申请成功，请等待目标配送员回复");
                }
                if ($type == "direct_transfer_reply") {
                    if (empty($extra["deliveryer_id"])) {
                        return error(-1, "目标配送员不存在");
                    }
                    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $extra["deliveryer_id"], "is_errander" => 1), array("id", "title", "mobile", "status"));
                    if (empty($deliveryer)) {
                        return error(-1, "配送员不存在");
                    }
                    if ($deliveryer["status"] != 1) {
                        return error(-1, "配送员已被删除");
                    }
                    if ($order["transfer_deliveryer_id"] != $deliveryer["id"]) {
                        return error(-1, "您没有转单回复的权限");
                    }
                    $from_deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"], "is_errander" => 1), array("id", "title", "status"));
                    if (empty($from_deliveryer)) {
                        return error(-1, "转单配送员不存在");
                    }
                    if ($from_deliveryer["status"] != 1) {
                        return error(-1, "转单配送员已被删除");
                    }
                    if ($extra["result"] == "agree") {
                        $update = array("delivery_collect_type" => 3, "deliveryer_id" => $extra["deliveryer_id"], "transfer_deliveryer_id" => $order["deliveryer_id"], "transfer_delivery_status" => 0);
                        pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
                        errander_order_insert_status_log($order["id"], "direct_transfer_agree", (string) $deliveryer["title"] . "接受来自" . $from_deliveryer["title"] . "的转单,此订单由" . $deliveryer["title"] . "为您配送，手机号：<a href='tel:" . $deliveryer["mobile"] . "'>" . $deliveryer["mobile"] . "</a>");
                        mload()->model("deliveryer");
                        deliveryer_order_num_update($deliveryer["id"]);
                        deliveryer_order_num_update($order["deliveryer_id"]);
                        return error(0, "接受转单成功");
                    }
                    $update = array("delivery_collect_type" => $order["data"]["original_delivery_collect_type"], "transfer_deliveryer_id" => 0, "transfer_delivery_status" => 0);
                    pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
                    errander_order_insert_status_log($order["id"], "direct_transfer_refuse", (string) $deliveryer["title"] . "拒绝来自" . $from_deliveryer["title"] . "的转单");
                    $extra = array("from_deliveryer" => $from_deliveryer, "to_deliveryer" => $deliveryer);
                    errander_order_deliveryer_notice($order["id"], "direct_transfer_refuse", $order["deliveryer_id"], $extra);
                    return error(0, "已拒绝转单");
                }
            }
        }
    }
    return true;
}
function errander_order_refund_notice($order_id, $type, $note = "")
{
    global $_W;
    $order = errander_order_fetch($order_id);
    if (empty($order)) {
        return error(-1, "订单不存在或已经删除");
    }
    $acc = WeAccount::create($order["acid"]);
    if ($type == "apply") {
        if (0 < $order["agentid"]) {
            $_W["agentid"] = 0;
            $_W["we7_wmall"]["config"] = get_system_config();
        }
        $maneger = $_W["we7_wmall"]["config"]["manager"];
        if (!empty($maneger["openid"])) {
            $tips = "您的平台有新的【退款申请】, 单号【" . $order["refund_out_no"] . "】,请尽快处理";
            $remark = array("订单类型: 跑腿订单-" . $order["order_type_cn"], "退款单号: " . $order["refund_out_no"], "支付方式: " . $order["pay_type_cn"], "用户姓名: " . $order["accept_username"], "联系方式: " . $order["accept_mobile"], $note);
            $params = array("first" => $tips, "reason" => "订单取消, 发起退款流程", "refund" => $order["final_fee"], "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($maneger["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["refund_tpl"], $send);
        }
        if (!empty($order["openid"])) {
            $tips = "您发起取消订单流程,已付款项会在1-3工作日内返回到用户的账号, 如有疑问, 请联系平台管理员";
            $remark = array("订单类型: 跑腿订单-" . $order["order_type_cn"], "订单　号: " . $order["order_sn"], "退款单号: " . $order["refund_out_no"], "支付方式: " . $order["pay_type_cn"], $note);
            $params = array("first" => $tips, "reason" => "订单取消, 发起退款流程", "refund" => $order["final_fee"], "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($order["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["refund_tpl"], $send);
        }
    } else {
        if ($type == "success" && !empty($order["openid"])) {
            $tips = "您的订单已退款成功，如有疑问, 请联系平台管理员 ";
            $remark = array("订单　号: " . $order["order_sn"], "退款单号: " . $order["refund_out_no"], "支付方式: " . $order["pay_type_cn"], "退款渠道: " . $order["refund_channel_cn"], "退款账户: " . $order["refund_account"], "如有疑问, 请联系平台管理员");
            $params = array("first" => $tips, "reason" => "订单取消, 发起退款流程", "refund" => $order["final_fee"], "remark" => implode("\n", $remark));
            $send = sys_wechat_tpl_format($params);
            $status = $acc->sendTplNotice($order["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["refund_tpl"], $send);
        }
    }
    return true;
}
function errander_order_begin_payrefund($id)
{
    global $_W;
    $order = errander_order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    if ($order["refund_status"] == 2) {
        return error(-1, "退款进行中， 请勿重复操作");
    }
    if ($order["refund_status"] == 3) {
        return error(-1, "退款已成功, 不能发起退款");
    }
    errander_order_insert_refund_log($order["id"], "handel");
    if ($order["pay_type"] == "credit") {
        if (0 < $order["uid"]) {
            $log = array($order["uid"], "外送模块订单退款, 订单号:" . $order["id"] . ", 退款金额:" . $order["final_fee"] . "元", "we7_wmall");
            mload()->model("member");
            member_credit_update($order["uid"], "credit2", $order["final_fee"], $log);
            $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
            pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            errander_order_insert_refund_log($order["id"], "success");
            errander_order_refund_notice($order["id"], "success");
        }
        return error(0, "退款成功,支付金额已退款至顾客的平台余额");
    }
    if ($order["pay_type"] == "wechat") {
        mload()->classs("wxpay");
        $pay = new WxPay($order["order_channel"]);
        $params = array("total_fee" => $order["final_fee"] * 100, "refund_fee" => $order["final_fee"] * 100, "out_trade_no" => $order["out_trade_no"], "out_refund_no" => $order["refund_out_no"]);
        $response = $pay->payRefund_build($params);
        if (is_error($response)) {
            return error(-1, $response["message"]);
        }
        $update = array("refund_status" => 2);
        pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        return true;
    }
    if ($order["pay_type"] == "alipay") {
        mload()->classs("alipay");
        $pay = new AliPay($order["order_channel"]);
        $params = array("refund_fee" => $order["final_fee"], "out_trade_no" => $order["out_trade_no"]);
        $response = $pay->payRefund_build($params);
        if (is_error($response)) {
            return error(-1, $response["message"]);
        }
        $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
        pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        errander_order_insert_refund_log($order["id"], "success");
        errander_order_refund_notice($order["id"], "success");
        return error(0, "退款成功,支付金额已退款至顾客的支付宝账户:" . $response["buyer_logon_id"]);
    }
    if ($order["pay_type"] == "yimafu") {
        $orderno = number_format($order["transaction_id"], 0, "", "");
        mload()->classs("yimafu");
        $pay = new YiMaFu();
        $response = $pay->payRefund_build($orderno);
        if (is_error($response)) {
            return error(-1, "退款失败");
        }
        $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
        pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        errander_order_insert_refund_log($order["id"], "success");
        errander_order_refund_notice($order["id"], "success");
        return error(0, "退款成功,支付金额已退款至顾客一码付账户");
    }
    if ($order["pay_type"] == "qianfan") {
        $member = pdo_get("tiny_wmall_members", array("uid" => $order["uid"]));
        if (empty($member["uid_qianfan"])) {
            return error(-1, "获取用户uid失败");
        }
        mload()->model("plugin");
        pload()->model("qianfanApp");
        $status = qianfan_user_credit_add($member["uid_qianfan"], $order["final_fee"]);
        if (is_error($status)) {
            return error(-1, "退款失败:" . $status["message"]);
        }
        $update = array("refund_status" => 3, "refund_success_time" => TIMESTAMP, "refund_account" => "支付用户的平台余额", "refund_channel" => "ORIGINAL");
        pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        errander_order_insert_refund_log($order["id"], "success");
        errander_order_refund_notice($order["id"], "success");
        return error(0, "退款成功,支付金额已退款至顾客的APP账户余额");
    }
}
function errander_order_query_payrefund($id)
{
    global $_W;
    $order = errander_order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    if ($order["refund_status"] != 2) {
        return true;
    }
    if ($order["refund_status"] == 3) {
        return error(-1, "退款已成功, 不能发起退款");
    }
    if ($order["pay_type"] == "wechat") {
        mload()->classs("wxpay");
        $pay = new WxPay($order["order_channel"]);
        $response = $pay->payRefund_query(array("out_refund_no" => $order["refund_out_no"]));
        if (is_error($response)) {
            return $response;
        }
        $wechat_status = $pay->payRefund_status();
        $update = array("refund_status" => $wechat_status[$response["refund_status_0"]]["value"]);
        if ($response["refund_status_0"] == "SUCCESS") {
            $update["refund_channel"] = $response["refund_channel_0"];
            $update["refund_account"] = $response["refund_recv_accout_0"];
            $update["refund_success_time"] = TIMESTAMP;
            pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            errander_order_insert_refund_log($order["id"], "success");
            errander_order_refund_notice($order["id"], "success");
        } else {
            pdo_update("tiny_wmall_errander_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        }
        return true;
    }
    return true;
}
function errander_order_delivery_fee($idOrCategory, $extra)
{
    global $_W;
    if (is_array($idOrCategory)) {
        $category = $idOrCategory;
    } else {
        $category = errander_category_fetch($idOrCategory);
    }
    if (empty($category)) {
        return error(-1, "跑腿类型不存在");
    }
    if (empty($category["status"])) {
        return error(-1, "该跑腿类型已关闭");
    }
    $tip = floatval($extra["delivery_tips"]);
    $start_address = $extra["start_address"];
    $end_address = $extra["end_address"];
    $goods_weight = floatval($extra["goods_weight"]);
    $predict_index = intval($extra["predict_index"]);
    $start_address_num = intval($extra["start_address_num"]);
    if ($tip < $category["tip_min"] || 0 < $category["tip_max"] && $category["tip_max"] < $tip) {
        $tip = $category["tip_min"];
    }
    $delivery_time = errander_delivery_times($category);
    $delivery_fee_predict_time = $delivery_time["times"][$predict_index]["fee"];
    $fees = array();
    if ($category["type"] == "multiaddress") {
        if (!$start_address_num) {
            return error(-1, "购买地址不能为空");
        }
        $multiaddress = $category["multiaddress"];
        if ($multiaddress["max"] < $start_address_num) {
            return error(-1, "购买地址最多只能设置" . $multiaddress["max"] . "个");
        }
        $delivery_fee = $delivery_fee_predict_time;
        $message = array();
        for ($i = 0; $i < $start_address_num; $i++) {
            $delivery_fee += $multiaddress["fee"][$i];
            $num = $i + 1;
            $message[] = "第" . $num . "个购买地址收取" . $multiaddress["fee"][$i] . "元配送费";
        }
        if (0 < $delivery_fee_predict_time) {
            $message[] = "特殊时间额外配送费" . $delivery_time["times"][$predict_index]["fee"] . "元";
        }
        $message = implode("<br>", $message);
    } else {
        $delivery_fee = $category["start_fee"] + $delivery_fee_predict_time;
        $fees["basic"] = array("title" => "起步价", "note" => "含" . $category["start_km"] . "公里", "fee" => (string) $category["start_fee"], "fee_cn" => "￥" . $category["start_fee"]);
        $distance = 0;
        if (!empty($start_address["location_y"]) && !empty($start_address["location_x"]) && !empty($end_address["location_y"]) && !empty($end_address["location_x"])) {
            $origins = array($start_address["location_y"], $start_address["location_x"]);
            $destination = array($end_address["location_y"], $end_address["location_x"]);
            $distance_calculate_type = $category["distance_calculate_type"];
            $distance = calculate_distance($origins, $destination, $distance_calculate_type);
            if (is_error($distance)) {
                return error(-1, $distance["message"]);
            }
        }
        if ($category["start_km"] < $distance && 0 < $category["pre_km"]) {
            $distance_over = round($distance - $category["start_km"], 2);
            $distance_over_fee = round($category["pre_km_fee"] * ceil($distance_over / $category["pre_km"]), 2);
            $delivery_fee += $distance_over_fee;
            $fees[] = array("title" => "里程费", "note" => (string) $distance_over . "公里", "fee" => (string) $distance_over_fee, "fee_cn" => "￥" . $distance_over_fee);
        }
        $message = (string) $category["start_km"] . "千米内";
        if ($category["weight_fee_status"] == 1) {
            $message .= "，" . $category["weight_fee"]["start_weight"] . "千克内";
        }
        $message .= "收取" . $category["start_fee"] . "元<br>";
        if (0 < $category["pre_km"]) {
            $message .= (string) $category["start_km"] . "千米以上，每增加" . $category["pre_km"] . "千米多收取" . $category["pre_km_fee"] . "元<br>";
        }
        if ($category["weight_fee_status"] == 1) {
            $fees["basic"]["note"] .= "," . $category["weight_fee"]["start_weight"] . "公斤";
            if ($category["weight_fee"]["start_weight"] < $goods_weight) {
                $weight_fee = array_compare($goods_weight, $category["weight_fee"]["weight"]);
                $index = array_search($weight_fee, $category["weight_fee"]["weight"]);
                $goods_weight_over = round($goods_weight - $category["weight_fee"]["start_weight"], 2);
                $goods_weight_over_fee = round($goods_weight_over * $weight_fee, 2);
                $delivery_fee += $goods_weight_over_fee;
                $message .= (string) $index . "千克以上，每千克多收取" . $weight_fee . "元<br>";
                $fees[] = array("title" => "重量费", "note" => (string) $goods_weight_over . "公斤", "fee" => (string) $goods_weight_over_fee, "fee_cn" => "￥" . $goods_weight_over_fee);
            }
        }
        if (0 < $delivery_fee_predict_time) {
            $message .= "特殊时间额外配送费" . $delivery_fee_predict_time . "元";
            $fees[] = array("title" => "特殊时间额外配送费", "note" => "", "fee" => (string) $delivery_fee_predict_time, "fee_cn" => "￥" . $delivery_fee_predict_time);
        }
    }
    if (0 < $tip) {
        $fees[] = array("title" => "小费", "note" => "", "fee" => (string) $tip, "fee_cn" => "￥" . $tip);
    }
    $activityed = array();
    $discount_fee = 0;
    if (0 < $_W["member"]["groupid"]) {
        $groupid = $_W["member"]["groupid"];
        if ($category["group_discount"]["type"] == 1) {
            $group_discount = $category["group_discount"]["data"][$groupid];
            if ($group_discount["condition"] <= $delivery_fee) {
                $discount_fee = $group_discount["back"];
            }
        } else {
            if ($category["group_discount"]["type"] == 2) {
                $group_discount = $category["group_discount"]["data"][$groupid];
                if ($group_discount["condition"] <= $delivery_fee) {
                    $discount_fee = round($delivery_fee * (10 - $group_discount["back"]) / 10, 2);
                }
            }
        }
        if (0 < $discount_fee) {
            $activityed[] = array("title" => (string) $_W["member"]["groupname"], "note" => $category["group_discount"]["type"] == 1 ? "返" . $discount_fee . "元" : "打" . $group_discount["back"] . "折", "fee" => (string) $discount_fee, "fee_cn" => "-￥" . $discount_fee);
        }
    }
    $total_fee = $delivery_fee + $tip;
    $data = array("goods_weight" => $goods_weight, "delivery_fee" => $delivery_fee, "delivery_extra_fee" => $delivery_fee_predict_time, "tip" => $tip, "total_fee" => $total_fee, "discount_fee" => $discount_fee, "final_fee" => $total_fee - $discount_fee, "distance" => $distance, "activityed" => $activityed, "fees" => array_values($fees), "message" => $message);
    return $data;
}
function errander_order_calculate_delivery_fee($idOrCategory, $extra, $is_calculate = 0)
{
    global $_W;
    if (is_array($idOrCategory)) {
        $category = $idOrCategory;
    } else {
        $category = get_errander_diypage($idOrCategory);
    }
    if (empty($category)) {
        return error(-1, "跑腿类型不存在");
    }
    $diypage = $category["diypage"];
    $basic = $category["basic"]["params"];
    $rule_fee = $diypage["data"]["fees"];
    $rule_fee_type = $diypage["data"]["fees"]["fee_type"];
    $rule_fee_distance = $diypage["data"]["fees"]["fee_data"];
    $distance_fee = $rule_fee_distance["fee"];
    $fees = array();
    $fees["basic"] = array("title" => "基础配送费", "note" => "固定金额", "fee" => $distance_fee, "fee_cn" => "￥" . $distance_fee);
    if ($rule_fee_type != "fee") {
        $rule_fee_wrap = $rule_fee_distance[$rule_fee_type];
        if (!empty($rule_fee_wrap["data"])) {
            foreach ($rule_fee_wrap["data"] as $row) {
                if (!strexists($row["start_hour"], ":")) {
                    $row["start_hour"] = (string) $row["start_hour"] . ":00";
                }
                if (!strexists($row["end_hour"], ":")) {
                    $row["end_hour"] = (string) $row["end_hour"] . ":00";
                }
                if (strtotime($row["start_hour"]) < TIMESTAMP && TIMESTAMP < strtotime($row["end_hour"])) {
                    $rule_fee_item = $row;
                    break;
                }
            }
        }
        if (!empty($extra["acceptaddress"]["location_x"]) && !empty($extra["buyaddress"]["location_y"])) {
            $origins = array($extra["buyaddress"]["location_y"], $extra["buyaddress"]["location_x"]);
            $destination = array($extra["acceptaddress"]["location_y"], $extra["acceptaddress"]["location_x"]);
            $distance_type = array("riding" => 2, "driving" => 1, "line" => 0, "walking" => 3);
            $distance = calculate_distance($origins, $destination, $distance_type[$rule_fee_wrap["route_mode"]]);
        }
        if ($rule_fee_type == "distance") {
            $distance_fee = $rule_fee_item["start_fee"];
            $calculate_distance_type = $rule_fee_distance["distance"]["calculate_distance_type"];
            if ($calculate_distance_type == 1) {
                $distance = ceil($distance);
            } else {
                if ($calculate_distance_type == 2) {
                    $distance = floor($distance);
                }
            }
            if ($rule_fee_item["start_km"] < $distance && 0 < $rule_fee_item["pre_km"]) {
                if ($rule_fee_item["over_km"] < $distance && 0 < $rule_fee_item["over_pre_km"]) {
                    $distance_over = round($distance - $rule_fee_item["start_km"], 2);
                    $distance_over_fee = round($rule_fee_item["over_pre_km_fee"] * round($distance_over / $rule_fee_item["over_pre_km"], 2), 2);
                } else {
                    $distance_over = round($distance - $rule_fee_item["start_km"], 2);
                    $distance_over_fee = round($rule_fee_item["pre_km_fee"] * round($distance_over / $rule_fee_item["pre_km"], 2), 2);
                }
                $distance_fee += $distance_over_fee;
            }
            $fees["basic"] = array("title" => "基础配送费", "note" => "里程计价", "fee" => $rule_fee_item["start_fee"], "fee_cn" => "￥" . $rule_fee_item["start_fee"]);
            if (0 < $distance_over_fee) {
                $fees[] = array("title" => "距离附加费", "note" => "", "fee" => $distance_over_fee, "fee_cn" => "￥" . $distance_over_fee);
            }
        } else {
            if ($rule_fee_type == "section") {
                $rule_fee_item = $rule_fee_item["rules"]["data"];
                if (empty($distance)) {
                    $first = reset($rule_fee_item);
                    $distance_fee = $first["fee"];
                }
                foreach ($rule_fee_item as $row) {
                    if ($row["start_km"] < $distance && $distance < $row["end_km"]) {
                        $distance_fee = $row["fee"];
                        break;
                    }
                }
                $fees["basic"] = array("title" => "基础配送费", "note" => "区间计价", "fee" => $distance_fee, "fee_cn" => "￥" . $distance_fee);
            }
        }
    }
    $weight_fee = 0;
    $rule_fee_weight = $rule_fee["weight_data"];
    if ($rule_fee["weight_status"] == 1) {
        $over_weight = $extra["goods_weight"] - $rule_fee_weight["basic"];
        if (0 < $over_weight) {
            foreach ($rule_fee_weight["data"] as $row) {
                if ($row["over_kgs"] < $extra["goods_weight"]) {
                    $weight_fee = $over_weight * $row["pre_kg_fees"];
                }
            }
        }
        if (0 < $weight_fee) {
            $fees[] = array("title" => "重量附加费", "note" => "", "fee" => $weight_fee, "fee_cn" => "￥" . $weight_fee);
        }
    }
    $deliveryinfo = array();
    for ($i = 0; $i <= intval($rule_fee["fee_day_limit"]); $i++) {
        $day = date("m-d", strtotime("+" . $i . " day"));
        $deliveryinfo[$day] = array("day" => $day, "times" => array());
        $j = strtotime("00:00");
        while ($j <= strtotime("23:59")) {
            if ($day == date("m-d") && $j < TIMESTAMP) {
                $j = TIMESTAMP + 1200;
                continue;
            }
            $deliveryinfo[$day]["times"][] = date("H:i", $j);
            $j += 1200;
        }
    }
    $days = array_keys($deliveryinfo);
    $delivery_nowday = $days[0];
    $delivery_nowtime = $deliveryinfo[$delivery_nowday]["times"][0];
    $delivery_day = trim($extra["delivery_day"]);
    if (!in_array($delivery_day, $days)) {
        $delivery_day = $days[0];
    }
    $delivery_time = trim($extra["delivery_time"]);
    $times = $deliveryinfo[$delivery_day]["times"];
    if (!in_array($delivery_time, $times)) {
        $delivery_time = $times[0];
    }
    $delivery_time_cn = date("Y-") . (string) $delivery_day . " " . ($delivery_nowtime == $delivery_time ? "立即送达" : $delivery_time);
    $special_time_fee = 0;
    if ($rule_fee["extra_fee_time_status"] == 1) {
        foreach ($rule_fee["extra_fee_time_data"]["data"] as $val) {
            if (!empty($val["start_hour"]) && !empty($val["end_hour"])) {
                $starttime = strtotime($val["start_hour"]);
                $endtime = strtotime($val["end_hour"]);
                $deliverytime = strtotime($delivery_time);
                if ($endtime <= $starttime && ($starttime <= $deliverytime || $deliverytime <= $endtime)) {
                    $special_time_fee = $val["fee"];
                    break;
                }
                if ($starttime < $endtime && $starttime <= $deliverytime && $deliverytime <= $endtime) {
                    $special_time_fee = $val["fee"];
                    break;
                }
            }
        }
        if (0 < $special_time_fee) {
            $fees[] = array("title" => "特殊时段附加费", "note" => "", "fee" => $special_time_fee, "fee_cn" => "￥" . $special_time_fee);
        }
    }
    $delivery_tips = 0;
    if ($basic["showtips"] == 1) {
        $delivery_tips = floatval($extra["delivery_tips"]);
        if ($delivery_tips < $basic["minfee"] || 0 < $basic["maxfee"] && $basic["maxfee"] < $delivery_tips) {
            $delivery_tips = $basic["minfee"];
        }
        if (0 < $delivery_tips) {
            $fees[] = array("title" => "小费", "note" => "", "fee" => $delivery_tips, "fee_cn" => "￥" . $delivery_tips);
        }
    }
    $extra_fee_data = errander_order_get_extra_fee($diypage, $extra, $is_calculate);
    if (is_error($extra_fee_data)) {
        return $extra_fee_data;
    }
    $fees = array_merge($fees, $extra_fee_data["fees"]);
    $extra_fees = $extra_fee_data["extra_fees"];
    $extra["extra_fee"] = $extra_fee_data["extra_fee"];
    $pre_goods_price = 0;
    if ($basic["estimate"] == 1) {
        $pre_goods_price = $extra["goods_price"];
        if (500 < $pre_goods_price) {
            $pre_goods_price = 500;
        } else {
            if ($pre_goods_price < 0) {
                $pre_goods_price = 0;
            }
        }
    }
    $delivery_fee = $distance_fee + $special_time_fee + $weight_fee + $extra_fees;
    $discount_fee = 0;
    $activityed = errander_order_count_activity($delivery_fee, array($diypage["id"]), array("redpacket_id" => $extra["redpacket_id"]));
    if (!empty($activityed["redPacket"])) {
        $redpacket = $activityed["redPacket"];
        $fees[] = array("title" => "红包", "note" => "", "fee" => 0 - $activityed["redPacket"]["discount"], "fee_cn" => "-￥" . $activityed["redPacket"]["discount"]);
        $discount_fee += $activityed["total"];
    } else {
        unset($extra["redpacket_id"]);
    }
    $yinsihao_status = $extra["yinsihao_status"];
    if (!empty($yinsihao_status)) {
        if (check_plugin_perm("yinsihao")) {
            $yinsihao = get_plugin_config("yinsihao.basic");
            if (!empty($yinsihao) && $yinsihao["status"] != 1) {
                $yinsihao_status = false;
            }
        } else {
            $yinsihao_status = false;
        }
    }
    $data = array("delivery_fee" => $delivery_fee, "delivery_extra_fee" => $special_time_fee + $weight_fee + $extra_fees, "delivery_tips" => $delivery_tips, "total_fee" => $delivery_fee + $delivery_tips, "discount_fee" => $discount_fee, "final_fee" => $delivery_fee + $delivery_tips - $discount_fee, "distance" => $distance, "fees" => array_values($fees), "activityed" => $activityed, "redpacket" => $redpacket, "redpacket_id" => $redpacket["id"], "delivery_info" => $deliveryinfo, "delivery_day" => $delivery_day, "delivery_time" => $delivery_time, "delivery_nowtime" => $delivery_nowtime, "delivery_time_cn" => $delivery_time_cn, "goods_weight" => 0 < $extra["goods_weight"] ? $extra["goods_weight"] : $rule_fee_weight["basic"], "goods_category" => $extra["goods_category"], "goods_price" => $pre_goods_price, "extra_fee" => $extra["extra_fee"], "note" => trim($extra["note"]), "yinsihao_status" => $yinsihao_status, "data" => array("fees" => array_values($fees)));
    return $data;
}
function errander_order_get_extra_fee($diypage, $extra, $is_calculate = 0)
{
    global $_GPC;
    $rule_fee = $diypage["data"]["fees"];
    $extra_fees = 0;
    $fees = array();
    if ($is_calculate == 1) {
        if (!empty($extra["extra_fee"])) {
            $current_cindex = $extra["extra_fee"]["current"]["cindex"];
            unset($extra["extra_fee"]["current"]);
            foreach ($extra["extra_fee"] as $key => $val) {
                $extra_fee = 0;
                if (empty($val)) {
                    unset($extra["extra_fee"][$key]);
                }
                foreach ($val as $v) {
                    $selected_num = count($val);
                    if ($rule_fee["extra_fee"][$key]["max"] < $selected_num) {
                        unset($extra["extra_fee"][$key][$current_cindex]);
                        return error(-1000, (string) $rule_fee["extra_fee"][$key]["title"] . "最多选择" . $rule_fee["extra_fee"][$key]["max"] . "项");
                    }
                    $extra_item_fee = $rule_fee["extra_fee"][$key]["data"][$v]["fee"];
                    $extra_fee_name = $rule_fee["extra_fee"][$key]["data"][$v]["fee_name"];
                    $extra_fee += $extra_item_fee;
                    $fees[] = array("title" => (string) $rule_fee["extra_fee"][$key]["title"] . "-" . $extra_fee_name, "note" => $extra_fee_name, "fee" => $extra_item_fee, "fee_cn" => "￥" . $extra_item_fee);
                }
                $extra_fees += $extra_fee;
            }
        }
    } else {
        $extra_fee_store = $extra["extra_fee"];
        if (empty($diypage["data"]["fees"]["extra_fee"])) {
            unset($extra_fee_store);
        } else {
            foreach ($diypage["data"]["fees"]["extra_fee"] as $pindex => $item) {
                if ($item["status"] == 1) {
                    $selected_num = count($extra_fee_store[$pindex]);
                    $extra_fee = 0;
                    if ($item["max"] < $selected_num) {
                        $delete_num = $selected_num - $item["max"];
                        array_splice($extra_fee_store[$pindex], 0 - $delete_num, $delete_num);
                    }
                    foreach ($extra_fee_store[$pindex] as $val) {
                        $extra_item_fee = $rule_fee["extra_fee"][$pindex]["data"][$val]["fee"];
                        $extra_fee_name = $rule_fee["extra_fee"][$pindex]["data"][$val]["fee_name"];
                        $extra_fee += $extra_item_fee;
                        $fees[] = array("title" => (string) $rule_fee["extra_fee"][$pindex]["title"] . "-" . $extra_fee_name, "note" => $extra_fee_name, "fee" => $extra_item_fee, "fee_cn" => "￥" . $extra_item_fee);
                    }
                    $extra_fees += $extra_fee;
                } else {
                    unset($extra_fee_store[$pindex]);
                }
            }
        }
        $extra["extra_fee"] = $extra_fee_store;
    }
    return array("extra_fees" => $extra_fees, "fees" => $fees, "extra_fee" => $extra["extra_fee"]);
}
function errander_order_calculate_deliveryer_fee($order, $deliveryerOrid = 0)
{
    global $_W;
    $deliveryer = $deliveryerOrid;
    if (!is_array($deliveryer) || !is_array($deliveryer["fee_delivery"])) {
        mload()->model("deliveryer");
        $deliveryer = deliveryer_fetch($deliveryerOrid);
    }
    if (empty($deliveryer)) {
        return 0;
    }
    $config_errander = get_deliveryer_feerate($deliveryer, "errander");
    $plateform_errander_fee = floatval($config_errander["deliveryer_fee"]);
    if ($config_errander["deliveryer_fee_type"] == 2) {
        $plateform_errander_fee = round($order["delivery_fee"] * $config_errander["deliveryer_fee"] / 100, 2);
    } else {
        if ($config_errander["deliveryer_fee_type"] == 3) {
            $config_errander_fee = $config_errander["deliveryer_fee"];
            $plateform_errander_fee = floatval($config_errander_fee["start_fee"]);
            $over_km = $order["distance"] - $config_errander_fee["start_km"];
            if (0 < $over_km) {
                $over_fee = round($over_km * $config_errander_fee["pre_km"], 2);
            }
            $plateform_errander_fee += $over_fee;
            if (0 < $config_errander_fee["max_fee"]) {
                $plateform_errander_fee = min($plateform_errander_fee, $config_errander_fee["max_fee"]);
            }
        }
    }
    return floatval($plateform_errander_fee);
}
function errander_order_delivery_info($idOrCategory, $predict_index = -1, $condition = array())
{
    $delivery_time = errander_delivery_times($idOrCategory);
    foreach ($delivery_time["times"] as &$time) {
        $time["time_cn"] = (string) $time["start"] . "~" . $time["end"];
    }
    $delivery_times = array();
    foreach ($delivery_time["days"] as &$days) {
        $delivery_times["days"][] = $days;
        $times = $delivery_time["times"];
        if ($days == date("m-d")) {
            foreach ($times as $key => $time) {
                if ($time["timestamp"] <= TIMESTAMP) {
                    unset($times[$key]);
                }
            }
        }
        $delivery_times["times"][$days] = array("days" => $days, "times" => $times);
    }
    $sys_predict_index = 0;
    $data = array_order(TIMESTAMP, $delivery_time["timestamp"]);
    if (!empty($data)) {
        $sys_predict_index = array_search($data, $delivery_time["timestamp"]);
    }
    $predict_day = $condition["predict_day_cn"];
    if (!empty($predict_day)) {
        if (strtotime(date("Y-") . $predict_day) < strtotime(date("Y-m-d"))) {
            $predict_index = -1;
        }
        if ($sys_predict_index !== false && $predict_index != -1 && $condition["predict_index"] < $sys_predict_index) {
            $predict_index = -1;
        }
    }
    if ($predict_index == -1) {
        if ($sys_predict_index !== false) {
            $predict_day = $delivery_time["days"][0];
            $predict_index = $sys_predict_index;
            $predict_time = "立即送出";
        } else {
            $predict_index = 0;
            $predict_day = $delivery_time["days"][1];
            $predict_times = array_shift($delivery_time["times"]);
            $predict_time = (string) $predict_times["start"] . "~" . $predict_times["end"];
        }
    } else {
        $predict_day = $condition["predict_day_cn"];
        $predict_time = $condition["predict_time_cn"];
    }
    $delivery_times["predict_index"] = $predict_index;
    $delivery_times["predict_day"] = $predict_day;
    $delivery_times["predict_day_cn"] = $predict_day;
    $delivery_times["predict_time_cn"] = $predict_time;
    return $delivery_times;
}
function errander_order_calculate($idOrCategory, $condition = array())
{
    if (!isset($condition["predict_index"])) {
        $condition["predict_index"] = -1;
    }
    $delivery_info = errander_order_delivery_info($idOrCategory, $condition["predict_index"], $condition);
    $delivery_fee_info = errander_order_delivery_fee($idOrCategory, $condition);
    if (is_error($delivery_fee_info)) {
        return $delivery_fee_info;
    }
    $order = array("delivery_fee_info" => $delivery_fee_info, "delivery_times" => $delivery_info, "note" => trim($condition["note"]), "goods_name" => trim($condition["goods_name"]) ? trim($condition["goods_name"]) : $idOrCategory["labels"][0]["name"]);
    return $order;
}
function is_in_errander_radius($lnglat)
{
    global $_W;
    $config = $_W["_plugin"]["config"];
    if (empty($config)) {
        $config = get_plugin_config("errander");
    }
    if (0 < $config["serve_radius"]) {
        if (empty($lnglat[0]) || empty($lnglat[1])) {
            return false;
        }
        $dist = distanceBetween($lnglat[0], $lnglat[1], $config["map"]["location_y"], $config["map"]["location_x"]);
        if ($dist <= $config["serve_radius"] * 1000) {
            return true;
        }
        return false;
    }
    return true;
}
function member_errander_address_check($idOrAddress)
{
    $address = $idOrAddress;
    if (!is_array($address)) {
        $address = member_fetch_address($idOrAddress);
    }
    if (empty($address)) {
        return error(-1, "地址不存在");
    }
    $is_ok = is_in_errander_radius(array($address["location_y"], $address["location_x"]));
    if (!$is_ok) {
        return error(-1, "该收货地址超过门店配送范围,请选择其他收货地址");
    }
    return $address;
}
function errander_category_deliveryer_reset($deliveryer_id)
{
    global $_W;
    if (!check_plugin_perm("errander")) {
        return false;
    }
    $is_errander = pdo_get("tiny_wmall_deliveryer", array(":uniacid" => $_W["uniacid"], ":id" => $deliveryer_id), array("is_errander"));
    if ($is_errander == 1) {
        return false;
    }
    $erranders = pdo_fetchall("select deliveryers,id from" . tablename("tiny_wmall_errander_category") . "where uniacid = :uniacid and FIND_IN_SET(" . $deliveryer_id . ", deliveryers)", array(":uniacid" => $_W["uniacid"]));
    foreach ($erranders as $val) {
        if (empty($val["deliveryers"])) {
            continue;
        }
        $errander_ids = explode(",", $val["deliveryers"]);
        foreach ($errander_ids as $k => $v) {
            if ($v == $deliveryer_id) {
                unset($errander_ids[$k]);
            }
        }
        if (!empty($errander_ids)) {
            $update_deliveryers = implode(",", $errander_ids);
        } else {
            $update_deliveryers = "";
        }
        pdo_update("tiny_wmall_errander_category", array("deliveryers" => $update_deliveryers), array("id" => $val["id"], "uniacid" => $_W["uniacid"]));
    }
    return true;
}
function get_errander_diypage($id)
{
    global $_W;
    $diypage = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_errander_page") . " WHERE id = :id and uniacid = :uniacid and agentid = :agentid", array(":id" => $id, ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
    if (!empty($diypage)) {
        $start_hour = strtotime($diypage["start_hour"]);
        $end_hour = strtotime($diypage["end_hour"]);
        $diypage["is_rest"] = 0;
        if (!empty($start_hour) && !empty($end_hour)) {
            if ($end_hour <= $start_hour && $end_hour <= TIMESTAMP && TIMESTAMP <= $start_hour) {
                $diypage["is_rest"] = 1;
            } else {
                if ($start_hour < $end_hour && ($end_hour <= TIMESTAMP || TIMESTAMP <= $start_hour)) {
                    $diypage["is_rest"] = 1;
                }
            }
        }
        $diypage["data"] = json_decode(base64_decode($diypage["data"]), true);
        if (empty($diypage["data"]["page"]["activecolor"])) {
            $diypage["data"]["page"]["activecolor"] = "#ffffff";
        }
        if (empty($diypage["data"]["page"]["activebackground"])) {
            $diypage["data"]["page"]["activebackground"] = "#ff2d4b";
        }
        foreach ($diypage["data"]["items"] as &$item) {
            if ($item["id"] == "banner" || $item["id"] == "picture") {
                foreach ($item["data"] as &$v) {
                    $v["imgurl"] = tomedia($v["imgurl"]);
                }
            } else {
                if ($item["id"] == "basic") {
                    $item["params"]["minfee"] = intval($item["params"]["minfee"]);
                    $item["params"]["maxfee"] = intval($item["params"]["maxfee"]);
                    $basic = $item;
                }
            }
        }
    }
    $diypage["data"]["fees"]["weight_data"]["basic"] = intval($diypage["data"]["fees"]["weight_data"]["basic"]);
    $result = array("diypage" => $diypage, "basic" => $basic);
    return $result;
}
function get_errander_rule_fee($diypageorId)
{
    $diypage = $diypageorId;
    if (!is_array($diypage)) {
        $diypage = get_errander_diypage($diypage);
        $diypage = $diypage["diypage"];
    }
    $fees = array();
    $rule_fee = $diypage["data"]["fees"];
    $rule_fee_type = $rule_fee["fee_type"];
    $rule_fee_distance = $rule_fee["fee_data"];
    $distance_fee = $rule_fee_distance["fee"];
    $fees["basic"] = array("title" => "基础配送费", "name" => "basic", "items" => array(array("note" => "每单", "fee" => $distance_fee, "fee_cn" => (string) $distance_fee . "元")));
    if ($rule_fee_type != "fee") {
        $rule_fee_wrap = $rule_fee_distance[$rule_fee_type];
        if (!empty($rule_fee_wrap["data"])) {
            foreach ($rule_fee_wrap["data"] as $row) {
                if (strtotime($row["start_hour"]) < TIMESTAMP && TIMESTAMP < strtotime($row["end_hour"])) {
                    $rule_fee_item = $row;
                    break;
                }
            }
        }
        if ($rule_fee_type == "distance") {
            $fees["basic"] = array("title" => "基础配送费", "name" => "basic", "items" => array(array("note" => (string) $rule_fee_item["start_km"] . "公里内", "fee" => $rule_fee_item["start_fee"], "fee_cn" => (string) $rule_fee_item["start_fee"] . "元")));
            if (empty($rule_fee_item["over_km"]) || $rule_fee_item["over_km"] <= $rule_fee_item["start_km"]) {
                $fees["distance"] = array("title" => "距离附加费", "name" => "distance", "items" => array(array("note" => "超过" . $rule_fee_item["start_km"] . "公里", "fee" => $rule_fee_item["pre_km_fee"], "fee_cn" => 1 < $rule_fee_item["pre_km"] ? "每" . $rule_fee_item["pre_km"] . "公里+" . $rule_fee_item["pre_km_fee"] . "元" : "每公里+" . $rule_fee_item["pre_km_fee"] . "元")));
            } else {
                $fees["distance"] = array("title" => "距离附加费", "name" => "distance", "items" => array(array("note" => (string) $rule_fee_item["start_km"] . "-" . $rule_fee_item["over_km"] . "公里", "fee" => $rule_fee_item["pre_km_fee"], "fee_cn" => 1 < $rule_fee_item["pre_km"] ? "每" . $rule_fee_item["pre_km"] . "公里+" . $rule_fee_item["pre_km_fee"] . "元" : "每公里+" . $rule_fee_item["pre_km_fee"] . "元"), array("note" => "超过" . $rule_fee_item["over_km"] . "公里", "fee" => $rule_fee_item["pre_km_fee"], "fee_cn" => 1 < $rule_fee_item["over_pre_km"] ? "每" . $rule_fee_item["over_pre_km"] . "公里+" . $rule_fee_item["over_pre_km_fee"] . "元" : "每公里+" . $rule_fee_item["over_pre_km_fee"] . "元")));
            }
        } else {
            if ($rule_fee_type == "section") {
                $rule_fee_item = $rule_fee_item["rules"]["data"];
                $fees["distance"]["title"] = "距离附加费";
                $fees["distance"]["name"] = "section";
                foreach ($rule_fee_item as $row) {
                    $fees["distance"]["items"][] = array("note" => (string) $row["start_km"] . "-" . $row["end_km"] . "公里", "fee" => $row["fee"], "fee_cn" => (string) $row["fee"] . "元");
                }
                $fees["basic"] = array("title" => "基础配送费", "name" => "basic", "items" => array(array("note" => $fees["distance"]["items"][0]["note"], "fee" => $fees["distance"]["items"][0]["fee"], "fee_cn" => $fees["distance"]["items"][0]["fee_cn"])));
                unset($fees["distance"]["items"][0]);
            }
        }
    }
    $rule_fee_weight = $rule_fee["weight_data"];
    if ($rule_fee["weight_status"] == 1 && !empty($rule_fee_weight["data"])) {
        $fees["weight"]["title"] = "重量附加费";
        $fees["weight"]["name"] = "weight";
        foreach ($rule_fee_weight["data"] as $row) {
            $fees["weight"]["items"][] = array("note" => "超过" . $row["over_kgs"] . "公斤", "fee" => $row["pre_kg_fees"], "fee_cn" => "每公斤+" . $row["pre_kg_fees"] . "元");
        }
    }
    if ($rule_fee["extra_fee_time_status"] == 1) {
        $fees["special_time"]["title"] = "特殊时段附加费";
        $fees["special_time"]["name"] = "special_time";
        foreach ($rule_fee["extra_fee_time_data"]["data"] as $val) {
            $fees["special_time"]["items"][] = array("note" => (string) $val["start_hour"] . "-" . $val["end_hour"], "fee" => $val["fee"], "fee_cn" => "+" . $val["fee"] . "元");
        }
    }
    $fees["extra_fee"]["title"] = "选择附加费";
    $fees["extra_fee"]["name"] = "extra_fee";
    foreach ($rule_fee["extra_fee"] as $item) {
        if ($item["status"] == 1) {
            foreach ($item["data"] as $val) {
                $fees["extra_fee"]["items"][] = array("note" => (string) $item["title"] . "-" . $val["fee_name"], "fee" => $val["fee"], "fee_cn" => "+" . $val["fee"] . "元");
            }
        }
    }
    if (empty($fees["extra_fee"]["items"])) {
        unset($fees["extra_fee"]);
    }
    return $fees;
}
function errander_order_check_required($diypage, $extra)
{
    $order_data = array();
    foreach ($diypage["data"]["items"] as $key => $item) {
        if (in_array($item["id"], array("multipleChoices", "oneChoice", "text", "uploadImg"))) {
            foreach ($item["data"] as $k => $v) {
                $option_key = (string) $key . "_" . $k;
                if ($v["is_required"] == 1) {
                    if ($item["id"] == "uploadImg") {
                        if (empty($extra["thumbs"][$option_key])) {
                            return error(-1, (string) $v["title"] . ",此项不能为空");
                        }
                    } else {
                        if (empty($extra["partData"][$option_key])) {
                            if ($item["id"] == "text") {
                                return error(-1, (string) $v["title"] . "不能为空");
                            }
                            if ($item["id"] == "oneChoice" || $item["id"] == "multipleChoices") {
                                return error(-1, "请选择" . $v["title"] . ",此项不能为空");
                            }
                        }
                    }
                }
                if (!empty($extra["partData"][$option_key])) {
                    if ($item["id"] == "oneChoice" || $item["id"] == "text") {
                        $order_data["partData"][] = array("title" => $v["title"], "value" => $extra["partData"][$option_key], "type" => $item["id"]);
                    } else {
                        $order_data["partData"][] = array("title" => $v["title"], "value" => array_values($extra["partData"][$option_key]), "type" => "multipleChoices");
                    }
                }
                if ($item["id"] == "uploadImg" && !empty($extra["thumbs"][$option_key])) {
                    $urls = array();
                    foreach ($extra["thumbs"][$option_key] as $thumbItem) {
                        $urls[] = $thumbItem["filename"];
                    }
                    $order_data["thumbs"][] = array("title" => $v["title"], "value" => $urls, "type" => $item["id"]);
                }
            }
        }
    }
    foreach ($diypage["data"]["fees"]["extra_fee"] as $k => $item) {
        if ($item["status"] == 1) {
            $selected_num = count($extra["extra_fee"][$k]);
            if ($selected_num < $item["min"]) {
                return error(-1, (string) $item["title"] . "最少选择" . $item["min"] . "项");
            }
            if ($item["max"] < $selected_num) {
                return error(-1, (string) $item["title"] . "最多选择" . $item["max"] . "项");
            }
            if (empty($extra["extra_fee"][$k])) {
                continue;
            }
            $extra_fees["title"] = $item["title"];
            foreach ($extra["extra_fee"][$k] as $val) {
                $extra_fees["value"][] = array("name" => $item["data"][$val]["fee_name"], "fee" => $item["data"][$val]["fee"]);
            }
            $order_data["extra_fee"][] = $extra_fees;
            unset($extra_fees);
        }
    }
    return $order_data;
}
function errander_order_insert_discount($id, $discount_data)
{
    global $_W;
    if (empty($discount_data)) {
        return false;
    }
    if (!empty($discount_data["redPacket"])) {
        pdo_update("tiny_wmall_activity_redpacket_record", array("status" => 2, "usetime" => TIMESTAMP, "order_id" => $id), array("uniacid" => $_W["uniacid"], "id" => $discount_data["redPacket"]["redPacket_id"]));
    }
    foreach ($discount_data as $data) {
        $insert = array("uniacid" => $_W["uniacid"], "oid" => $id, "type" => $data["type"], "name" => $data["name"], "icon" => $data["icon"], "note" => $data["text"], "fee" => $data["value"], "store_discount_fee" => floatval($data["store_discount_fee"]), "agent_discount_fee" => floatval($data["agent_discount_fee"]), "plateform_discount_fee" => floatval($data["plateform_discount_fee"]));
        pdo_insert("tiny_wmall_errander_order_discount", $insert);
    }
    return true;
}
function errander_order_count_activity($delivery_fee = 0, $errander_category = array(), $extra = array())
{
    $activityed = array("list" => "", "total" => 0, "activity" => 0, "token" => 0, "store_discount_fee" => 0, "agent_discount_fee" => 0, "plateform_discount_fee" => 0);
    if (!empty($extra["redpacket_id"])) {
        mload()->model("redPacket");
        $redpacket = redpacket_available_check($extra["redpacket_id"], $delivery_fee, $errander_category, array("scene" => "paotui"));
        if (!is_error($redpacket)) {
            $activityed["list"]["redPacket"] = array("text" => "-￥" . $redpacket["discount"], "value" => $redpacket["discount"], "type" => "redPacket", "name" => "平台红包优惠", "icon" => "redPacket_b.png", "redPacket_id" => $redpacket["id"], "plateform_discount_fee" => $redpacket["discount"], "agent_discount_fee" => 0, "store_discount_fee" => 0);
            $activityed["redPacket"] = $redpacket;
            $activityed["total"] += $redpacket["discount"];
            $activityed["activity"] += $redpacket["discount"];
            $activityed["store_discount_fee"] += 0;
            $activityed["agent_discount_fee"] += 0;
            $activityed["plateform_discount_fee"] += $redpacket["discount"];
        }
    }
    return $activityed;
}
function errander_order_cancel_reason($id)
{
    $log = pdo_fetch("select * from " . tablename("tiny_wmall_errander_order_status_log") . " where oid = :id and status = 6 order by id desc", array(":id" => $id));
    if (empty($log)) {
        return "未知";
    }
    $reason = "未知";
    if (!empty($log["note"])) {
        $reason = (string) $log["note"];
    }
    if (!empty($log["role_cn"])) {
        $reason = (string) $reason . "。操作人:" . $log["role_cn"];
    }
    return $reason;
}
function errander_order_update_bill($id, $extra = array())
{
    global $_W;
    $order = pdo_get("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    $deliveryer_fee = $order["deliveryer_fee"];
    if (0 < $extra["deliveryer_id"]) {
        $deliveryer_fee = errander_order_calculate_deliveryer_fee($order, $extra["deliveryer_id"]);
    }
    $deliveryer_total_fee = $deliveryer_fee + $order["delivery_tips"];
    $plateform_serve_fee = $order["delivery_fee"] + $order["delivery_tips"] - $order["discount_fee"];
    $plateform_serve = array("fee" => $plateform_serve_fee, "note" => "订单配送费 ￥" . $order["delivery_fee"] . " + 订单小费 ￥" . $order["delivery_tips"] . " - 使用红包 ￥" . $order["discount_fee"]);
    if ($_W["is_agent"]) {
        mload()->model("agent");
        $account_agent = get_agent($order["agentid"], "fee");
        $agent_fee_config = $account_agent["fee"]["fee_errander"];
        if ($agent_fee_config["type"] == 2) {
            $agent_serve_fee = floatval($agent_fee_config["fee"]);
            $agent_serve = array("fee_type" => 2, "fee_rate" => 0, "fee" => $agent_serve_fee, "note" => "每单固定" . $agent_serve_fee . "元");
        } else {
            if ($agent_fee_config["type"] == 3) {
                $agent_serve_rate = floatval($agent_fee_config["fee_rate"]);
                $agent_serve_fee = round(($plateform_serve_fee - $deliveryer_total_fee) * $agent_serve_rate / 100, 2);
                $text = "(本单代理佣金" . $plateform_serve_fee . " - 代理商支付给配送员配送费含订单小费 ￥" . $deliveryer_total_fee . ") x " . $agent_serve_rate . "%";
                if (0 < $agent_fee_config["fee_min"] && $agent_serve_fee < $agent_fee_config["fee_min"]) {
                    $agent_serve_fee = $agent_fee_config["fee_min"];
                    $text .= "平台佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                }
                if ($agent_serve_fee < 0) {
                    $agent_serve_fee = 0;
                }
                $agent_serve = array("fee_type" => 3, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
            } else {
                $basic = 0;
                $note = array("yes" => array(), "no" => array());
                $fee_items = agent_serve_fee_items();
                if (!empty($agent_fee_config["items_yes"])) {
                    foreach ($agent_fee_config["items_yes"] as $item) {
                        $basic += $order[$item];
                    $note["yes"][] = (string) $fee_items["yes"][$item] . " ￥" . $order[$item];
                    }
                }
                if (!empty($agent_fee_config["items_no"])) {
                    foreach ($agent_fee_config["items_no"] as $item) {
                        $basic -= $order[$item];
                    $note["no"][] = (string) $fee_items["no"][$item] . " ￥" . $order[$item];
                    }
                }
                if ($basic < 0) {
                    $basic = 0;
                }
                $agent_serve_rate = floatval($agent_fee_config["fee_rate"]);
                $agent_serve_fee = round($basic * $agent_serve_rate / 100, 2);
                $text = "(" . implode(" + ", $note["yes"]);
                if (!empty($note["no"])) {
                    $text .= " - " . implode(" - ", $note["no"]);
                }
                $text .= ") x " . $agent_serve_rate . "%";
                if (0 < $agent_fee_config["fee_min"] && $agent_serve_fee < $agent_fee_config["fee_min"]) {
                    $agent_serve_fee = $agent_fee_config["fee_min"];
                    $text .= " 佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                }
                $agent_serve = array("fee_type" => 1, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
            }
        }
        $agent_serve["final"] = "(代理商抽取佣金 ￥" . $plateform_serve_fee . " - 平台服务佣金 ￥" . $agent_serve_fee . " - 代理商补贴 ￥" . $order["agent_discount_fee"] . " - 代理商支付给配送员配送费含订单小费 ￥" . $deliveryer_total_fee . ")";
        $agent_final_fee = $plateform_serve_fee - $agent_serve_fee - $order["agent_discount_fee"] - $deliveryer_total_fee;
    } else {
        $agent_final_fee = $plateform_serve_fee - $order["agent_discount_fee"] - $deliveryer_total_fee;
        $plateform_serve_fee = $plateform_serve_fee - $deliveryer_total_fee;
        $plateform_serve["fee"] = $plateform_serve_fee;
        $plateform_serve["note"] .= " - 支付给配送员配送费含订单小费 ￥" . $deliveryer_total_fee;
    }
    $data = array("plateform_serve" => iserializer($plateform_serve), "plateform_serve_fee" => $plateform_serve_fee, "agent_serve" => iserializer($agent_serve), "agent_serve_fee" => $agent_serve_fee, "agent_final_fee" => $agent_final_fee, "deliveryer_total_fee" => $deliveryer_total_fee, "deliveryer_fee" => $deliveryer_fee);
    pdo_update("tiny_wmall_errander_order", $data, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
    return true;
}

?>
