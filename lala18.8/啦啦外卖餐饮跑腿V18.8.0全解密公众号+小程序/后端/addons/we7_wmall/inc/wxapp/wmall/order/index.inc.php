<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $id = intval($_GPC["min"]);
    $condition = "where a.uniacid = :uniacid and a.uid = :uid and a.is_delete = 0";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    if (0 < $id) {
        $condition .= " and a.id < :id";
        $params[":id"] = $id;
    }
    $orders = pdo_fetchall("select a.id as aid, a.*, b.title, b.logo, b.delivery_mode from " . tablename("tiny_wmall_order") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition . " order by a.id desc limit 20", $params, "aid");
    $min = 0;
    if (!empty($orders)) {
        $share = get_plugin_config("ordergrant.share");
        $order_status = order_status();
        $config_order = $_W["we7_wmall"]["config"]["takeout"]["order"];
        foreach ($orders as &$da) {
            $da["goods"] = pdo_get("tiny_wmall_order_stat", array("oid" => $da["id"]));
            $da["comment_cn"] = "去评价";
            if (0 && $share["status"] == 1 && $da["status"] == 5 && $da["is_comment"] == 0 && time() - $share["share_grant_days_limit"] * 86400 <= $da["endtime"]) {
                $da["comment_cn"] = "评价赢好礼";
            }
            $addtime = $da["addtime"];
            $da["addtime"] = date("Y-m-d H:i:s", $da["addtime"]);
            $da["addtime_hm"] = date("H:i", $addtime);
            $da["status_text"] = $order_status[$da["status"]]["text"];
            $da["logo"] = tomedia($da["logo"]);
            $da["delivery_title"] = $_config_mall["delivery_title"];
            $da["pay_time_limit"] = 0 < $_W["we7_wmall"]["config"]["takeout"]["order"]["pay_time_limit"] ? $_W["we7_wmall"]["config"]["takeout"]["order"]["pay_time_limit"] : 0;
            $da["customer_cancel_status"] = 0;
            if ($da["status"] == 2 && $config_order["customer_cancel_status"] == 1 && 0 < $config_order["customer_cancel_timelimit"] && TIMESTAMP - $da["handletime"] < $config_order["customer_cancel_timelimit"] * 60) {
                $da["customer_cancel_status"] = 1;
            }
        }
        $min = min(array_keys($orders));
    }
    $result = array("errno" => 0, "message" => array_values($orders), "min" => $min, "config_mall" => $_config_mall, "errander_status" => check_plugin_perm("errander") && get_plugin_config("errander.status"));
    imessage($result, "", "ajax");
    return 1;
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = order_fetch($id, true);
        if (empty($order)) {
            imessage(error(-1, "订单不存在或已删除"), "", "ajax");
        }
        $order_status = order_status();
        $store = store_fetch($order["sid"], array("id", "title", "telephone", "pack_price", "logo", "delivery_price", "address", "location_x", "location_y", "data"));
        $goods = order_fetch_goods($order["id"]);
        $log = pdo_fetch("select * from " . tablename("tiny_wmall_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
        $activityed = order_fetch_discount($id);
        $logs = order_fetch_status_log($id);
        if (!empty($logs)) {
            $maxid = max(array_keys($logs));
            $minid = min(array_keys($logs));
            foreach ($logs as &$log) {
                $log["addtime"] = date("H:i", $log["addtime"]);
            }
        }
        if ($order["refund_status"]) {
            $refund = order_refund_fetch($id);
            $refund_logs = order_fetch_refund_log($id);
            if (!empty($refund_logs)) {
                $refundmaxid = max(array_keys($refund_logs));
            }
        }
        $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]));
        if ($order["data"]["yinsihao_status"] == 1) {
            $config_yinsihao = get_plugin_config("yinsihao.basic");
            if ($config_yinsihao["member_call_deliveryer_status"] == 1) {
                $deliveryer["mobile"] = substr_replace($deliveryer["mobile"], "****", 3, 4);
            }
        }
        $share = get_plugin_config("ordergrant.share");
        $comment = pdo_get("tiny_wmall_order_comment", array("oid" => $order["id"]));
        $share_button = 0;
        $order["comment_cn"] = "去评价";
        if ($share["status"] == 1 && !$comment["is_share"] && $order["status"] == 5 && time() - $share["share_grant_days_limit"] * 86400 <= $order["endtime"]) {
            if ($order["is_comment"] == 0) {
                $order["comment_cn"] = "评价赢好礼";
                $share_button = 1;
            } else {
                $share_button = 2;
            }
            $_share = array("title" => $share["share"]["title"], "desc" => $share["share"]["desc"], "imgUrl" => tomedia($share["share"]["imgUrl"]), "link" => imurl("/pages/ordergrant/share/detail", array("id" => $order["id"]), "true"));
        }
        if (check_plugin_perm("superRedpacket")) {
            $superRedpacket = pdo_get("tiny_wmall_superredpacket_grant", array("uniacid" => $_W["uniacid"], "order_id" => $id, "uid" => $_W["member"]["uid"]));
            if (!empty($superRedpacket) && 0 < $superRedpacket["packet_dosage"]) {
                $superRedpacket_share = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "id" => $superRedpacket["activity_id"], "type" => "share", "status" => 1));
                $superRedpacket_share_status = 0;
                if (!empty($superRedpacket_share)) {
                    $share_button = 0;
                    $superRedpacket_share_status = 1;
                    $superRedpacket_share["data"] = json_decode(base64_decode($superRedpacket_share["data"]), true);
                    $_share = array("title" => $superRedpacket_share["data"]["share"]["title"], "desc" => $superRedpacket_share["data"]["share"]["desc"], "imgUrl" => tomedia($superRedpacket_share["data"]["share"]["imgUrl"]), "link" => "/package/pages/superredpacket/share?order_id=" . $id);
                }
            }
        }
        $share["info"] = array("share_button" => $share_button, "superRedpacket_share_status" => $superRedpacket_share_status, "superRedpacket" => $superRedpacket, "sharedata" => $_share);
        $slides = sys_fetch_slide("orderDetail", true);
        if (empty($slides)) {
            $slides = false;
        }
        $show_location = 0;
        if ($order["order_type"] == 1 && $order["status"] < 5 && (check_plugin_perm("deliveryerApp") || check_plugin_perm("wxapp"))) {
            $show_location = 1;
        }
        $config_order = $_W["we7_wmall"]["config"]["takeout"]["order"];
        $can_delete = 0;
        if ($config_order["customer_delete_order"] == 1 && in_array($order["status"], array(5, 6))) {
            $can_delete = 1;
        }
        if ($order["status"] == 2 && $config_order["customer_cancel_status"] == 1 && 0 < $config_order["customer_cancel_timelimit"] && TIMESTAMP - $order["handletime"] < $config_order["customer_cancel_timelimit"] * 60) {
            $order["customer_cancel_status"] = 1;
        }
        $order["update_address_status"] = 0;
        $update_num = isset($order["data"]["order_info_update_num"]["address"]) ? $order["data"]["order_info_update_num"]["address"] : 0;
        $update_address_config = $config_order["order_update"];
        if (check_plugin_perm("svip") && $order["is_pay"] == 1 && $order["status"] < 5 && $order["order_type"] == 1 && !empty($update_address_config) && $update_address_config["status"] == 1 && $update_num < $update_address_config["address_update_num"] && 0 < $update_address_config["newaddress_distance"]) {
            $order["update_address_status"] = 1;
        }
        $order["update_order_info_status"] = check_plugin_perm("svip") ? 1 : 0;
        $result = array("config_mall" => $_config_mall, "goods" => $goods, "store" => $store, "order" => $order, "activityed" => $activityed, "deliveryer" => $deliveryer, "order_status" => $order_status, "delivery_title" => $_config_mall["delivery_title"], "logs" => $logs, "log" => $log, "maxid" => $maxid, "minid" => $minid, "slides" => $slides, "share" => $share["info"], "show_location" => $show_location, "can_delete" => $can_delete, "qrcode" => isurl("pages/order/detail", array("id" => $order["id"], "sid" => $order["sid"], "code" => $order["code"]), true));
        $_W["_nav"] = 1;
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($ta == "new_detail") {
            $id = intval($_GPC["id"]);
            $order = order_fetch($id, true);
            if (empty($order)) {
                imessage("订单不存在或已删除", "", "error");
            }
            $store = store_fetch($order["sid"], array("id", "title", "telephone", "pack_price", "logo", "delivery_price", "address", "location_x", "location_y", "data"));
            $goods = order_fetch_goods($order["id"]);
            $activityed = order_fetch_discount($id);
            $log = pdo_fetch("select * from " . tablename("tiny_wmall_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
            $logs = order_fetch_status_log($id);
            if (!empty($logs)) {
                $maxid = max(array_keys($logs));
                foreach ($logs as &$val) {
                    $val["addtime"] = date("H:i", $val["addtime"]);
                }
            }
            $refund_data = array();
            if ($order["refund_status"]) {
                $refund = order_refund_fetch($id);
                $refund_logs = order_fetch_refund_log($id);
                if (!empty($refund_logs)) {
                    $refundmaxid = max(array_keys($refund_logs));
                }
                $refund_data = array("refund" => $refund, "refund_logs" => $refund_logs, "refundmaxid" => $refundmaxid);
            }
            $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]), array("id", "title", "avatar", "mobile", "location_x", "location_y"));
            if ($order["data"]["yinsihao_status"] == 1) {
                $config_yinsihao = get_plugin_config("yinsihao.basic");
                if ($config_yinsihao["member_call_deliveryer_status"] == 1) {
                    $deliveryer["mobile"] = substr_replace($deliveryer["mobile"], "****", 3, 4);
                }
            }
            $share = get_plugin_config("ordergrant.share");
            $comment = pdo_get("tiny_wmall_order_comment", array("oid" => $order["id"]));
            $share_button = 0;
            $order["comment_cn"] = "去评价";
            if ($share["status"] == 1 && !$comment["is_share"] && $order["status"] == 5 && time() - $share["share_grant_days_limit"] * 86400 <= $order["endtime"]) {
                if ($order["is_comment"] == 0) {
                    $order["comment_cn"] = "评价赢好礼";
                    $share_button = 1;
                } else {
                    $share_button = 2;
                }
                $_W["_share"] = array("autoinit" => 0, "title" => $share["share"]["title"], "desc" => $share["share"]["desc"], "imgUrl" => tomedia($share["share"]["imgUrl"]), "link" => ivurl("/package/pages/ordergrant/detail", array("id" => $order["id"]), "true"));
            }
            if (check_plugin_perm("superRedpacket")) {
                $superRedpacket = pdo_get("tiny_wmall_superredpacket_grant", array("uniacid" => $_W["uniacid"], "order_id" => $id, "uid" => $_W["member"]["uid"]));
                if (!empty($superRedpacket) && 0 < $superRedpacket["packet_dosage"]) {
                    $superRedpacket_share = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "id" => $superRedpacket["activity_id"], "type" => "share", "status" => 1));
                    $superRedpacket_share_status = 0;
                    if (!empty($superRedpacket_share)) {
                        $share_button = 0;
                        $superRedpacket_share_status = 1;
                        $superRedpacket_share["data"] = json_decode(base64_decode($superRedpacket_share["data"]), true);
                        $_W["_share"] = array("title" => $superRedpacket_share["data"]["share"]["title"], "desc" => $superRedpacket_share["data"]["share"]["desc"], "imgUrl" => tomedia($superRedpacket_share["data"]["share"]["imgUrl"]), "link" => ivurl("/pages/superRedpacket/index", array("order_id" => $id), true));
                    }
                }
            }
            $share["info"] = array("share_button" => $share_button, "superRedpacket_share_status" => $superRedpacket_share_status, "superRedpacket" => $superRedpacket);
            $slides = sys_fetch_slide("orderDetail", true, $order["agentid"]);
            if (empty($slides)) {
                $slides = false;
            }
            $show_location = 0;
            if ($order["order_type"] == 1 && $order["status"] < 5 && (check_plugin_perm("deliveryerApp") || check_plugin_perm("wxapp"))) {
                $show_location = 1;
            }
            $config_order = $_W["we7_wmall"]["config"]["takeout"]["order"];
            $can_delete = 0;
            if ($config_order["customer_delete_order"] == 1 && in_array($order["status"], array(5, 6))) {
                $can_delete = 1;
            }
            if ($order["status"] == 2 && $config_order["customer_cancel_status"] == 1 && 0 < $config_order["customer_cancel_timelimit"] && TIMESTAMP - $order["handletime"] < $config_order["customer_cancel_timelimit"] * 60) {
                $order["customer_cancel_status"] = 1;
            }
            $order["update_address_status"] = 0;
            $update_num = isset($order["data"]["order_info_update_num"]["address"]) ? $order["data"]["order_info_update_num"]["address"] : 0;
            $update_address_config = $config_order["order_update"];
            if (check_plugin_perm("svip") && $order["is_pay"] == 1 && $order["status"] < 5 && $order["order_type"] == 1 && !empty($update_address_config) && $update_address_config["status"] == 1 && $update_num < $update_address_config["address_update_num"] && 0 < $update_address_config["newaddress_distance"]) {
                $order["update_address_status"] = 1;
            }
            $order["update_order_info_status"] = check_plugin_perm("svip") ? 1 : 0;
            $result = array("goods" => $goods, "store" => $store, "order" => $order, "activityed" => $activityed, "deliveryer" => $deliveryer, "member" => $_W["member"], "order_log" => array("log" => $log, "logs" => $logs, "maxid" => $maxid), "slides" => $slides, "share" => $share, "refund_data" => $refund_data, "show_location" => $show_location, "can_delete" => $can_delete, "config_mall" => array("call_deliveryer_need_select" => $_config_mall["call_deliveryer_need_select"], "mobile" => $_config_mall["mobile"]), "qrcode" => isurl("pages/order/detail", array("id" => $order["id"], "sid" => $order["sid"], "code" => $order["code"]), true));
            imessage(error(0, $result), "", "ajax");
            return 1;
        } else {
            if ($ta == "remind") {
                $id = intval($_GPC["id"]);
                $order = order_fetch($id);
                if (empty($order)) {
                    imessage(error(-1, "订单不存在或已删除"), "", "ajax");
                }
                $log = pdo_fetch("select * from " . tablename("tiny_wmall_order_status_log") . " where uniacid = :uniacid and oid = :oid and status = 8 order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
                $store = store_fetch($order["sid"], array("remind_time_limit", "remind_time_start"));
                $remind_time_start = intval($store["remind_time_start"]);
                if (!empty($remind_time_start) && time() - $remind_time_start * 60 < $order["addtime"]) {
                    imessage(error(-1, "下单后" . $remind_time_start . "分钟才可以催单"), "", "ajax");
                }
                $remind_time_limit = intval($store["remind_time_limit"]) ? intval($store["remind_time_limit"]) : 10;
                if (time() - $remind_time_limit * 60 <= $log["addtime"]) {
                    imessage(error(-1, "距离上次催单不超过" . $remind_time_limit . "分钟,不能催单"), "", "ajax");
                }
                $result = order_status_update($id, "remind");
                imessage($result, "", "ajax");
            } else {
                if ($ta == "cancel") {
                    $id = intval($_GPC["id"]);
                    $result = order_status_update($id, "cancel");
                    if (is_error($result)) {
                        imessage(error(-1, $result["message"]), "", "ajax");
                    }
                    imessage(error(0, "取消订单成功"), "", "ajax");
                } else {
                    if ($ta == "end") {
                        $id = intval($_GPC["id"]);
                        $result = order_status_update($id, "end");
                        if (is_error($result)) {
                            imessage(error(-1, $result["message"]), "", "ajax");
                        }
                        imessage(error(0, "确认订单完成成功"), "", "ajax");
                    } else {
                        if ($ta == "location") {
                            $orderid = intval($_GPC["id"]);
                            $order = order_fetch($orderid, true);
                            $markers = array();
                            $markers["customer"] = array("iconPath" => "/static/img/shou.png", "id" => 1, "latitude" => $order["location_x"], "longitude" => $order["location_y"], "width" => 40, "height" => 40, "callout" => array("content" => "我", "color" => "#fff", "fontSize" => 12, "borderRadius" => 5, "padding" => 5, "bgColor" => "#ff244b", "display" => "ALWAYS", "textAlign" => "center"));
                            mload()->model("deliveryer");
                            $deliveryer = deliveryer_fetch($order["deliveryer_id"]);
                            $markers["deliveryer"] = array("iconPath" => "/static/img/store1.png", "vue_icon" => $deliveryer["avatar"], "id" => 2, "latitude" => $deliveryer["location_x"], "longitude" => $deliveryer["location_y"], "width" => 40, "height" => 40, "callout" => array("content" => "配送员：" . $deliveryer["title"], "color" => "#fff", "fontSize" => 12, "borderRadius" => 5, "padding" => 5, "bgColor" => "#ff244b", "display" => "ALWAYS", "textAlign" => "center"));
                            $store = store_fetch($order["sid"], array("title", "logo", "location_x", "location_y"));
                            $markers["store"] = array("iconPath" => "/static/img/qu.png", "vue_icon" => $store["logo"], "id" => 3, "latitude" => $store["location_x"], "longitude" => $store["location_y"], "width" => 40, "height" => 40, "callout" => array("content" => "商家：" . $store["title"], "color" => "#fff", "fontSize" => 12, "borderRadius" => 5, "padding" => 5, "bgColor" => "#ff244b", "display" => "ALWAYS", "textAlign" => "center"));
                            $points = array(array("latitude" => $order["location_x"], "longitude" => $order["location_y"]), array("latitude" => $deliveryer["location_x"], "longitude" => $deliveryer["location_y"]), array("latitude" => $store["location_x"], "longitude" => $store["location_y"]));
                            $result = array("markers" => array_values($markers), "points" => $points, "order" => $order);
                            imessage(error(0, $result), "", "ajax");
                        } else {
                            if ($ta == "delete") {
                                $id = intval($_GPC["id"]);
                                if (!$id) {
                                    imessage(error(-1, "删除订单失败"), "", "ajax");
                                }
                                pdo_update("tiny_wmall_order", array("is_delete" => 1), array("uniacid" => $_W["uniacid"], "id" => $id));
                                imessage(error(0, "删除订单成功"), "", "ajax");
                            } else {
                                if ($ta == "refund") {
                                    $id = intval($_GPC["id"]);
                                    $order = order_fetch($id, true);
                                    if (empty($order)) {
                                        imessage("订单不存在或已删除", "", "error");
                                    }
                                    $refunds = order_fetchall_refund($id, array("refund_logs" => 1));
                                    $result = array("refunds" => $refunds);
                                    imessage(error(0, $result), "", "ajax");
                                } else {
                                    if ($ta == "pindan_detail") {
                                        $id = intval($_GPC["id"]);
                                        mload()->model("pindan");
                                        $order = order_fetch_pindan_data($id);
                                        if (is_error($order)) {
                                            imessage($order, "", "ajax");
                                        }
                                        $config_mall = $_W["we7_wmall"]["config"]["mall"];
                                        $result = array("order" => $order, "config" => array("logo" => tomedia($config_mall["logo"]), "title" => $config_mall["title"]));
                                        imessage(error(0, $result), "", "ajax");
                                    } else {
                                        if ($ta == "order_info_update") {
                                            if (!check_plugin_perm("svip")) {
                                                imessage(error(-1, "平台未开启修改订单信息的功能"), "", "ajax");
                                            }
                                            $id = intval($_GPC["id"]);
                                            $order = order_fetch($id, true);
                                            if (empty($order)) {
                                                imessage(error(-1, "订单不存在或已删除"), "", "ajax");
                                            }
                                            if ($order["is_pay"] == 1) {
                                                imessage(error(-1, "订单已支付，无法修改备注等信息"), "", "ajax");
                                            }
                                            if ($order["order_type"] != 1) {
                                                imessage(error(-1, "该订单不是外卖单，无法修改备注等信息"), "", "ajax");
                                            }
                                            $store = store_fetch($order["sid"]);
                                            if ($_W["ispost"]) {
                                                $type = trim($_GPC["type"]);
                                                $tips = array("note" => "备注", "mobile" => "收货电话", "person_num" => "餐具数量");
                                                if (!in_array($type, array_keys($tips))) {
                                                    imessage(error(-1, "修改订单信息的类型错误"), "", "ajax");
                                                }
                                                if ($type == "person_num") {
                                                    $value = intval($_GPC[$type]);
                                                } else {
                                                    $value = trim($_GPC[$type]);
                                                }
                                                pdo_update("tiny_wmall_order", array($type => $value), array("uniacid" => $_W["uniacid"], "id" => $id));
                                                imessage(error(0, "修改订单" . $tips[$type] . "信息成功"), "", "ajax");
                                            }
                                            $result = array("order" => $order, "store" => $store);
                                            imessage(error(0, $result), "", "ajax");
                                        } else {
                                            if ($ta == "update_address") {
                                                $config = $_W["we7_wmall"]["config"]["takeout"]["order"]["order_update"];
                                                if (!check_plugin_perm("svip") || empty($config) || !empty($config) && $config["status"] != 1) {
                                                    imessage(error(-1, "平台未开启修改收货地址功能"), "", "ajax");
                                                }
                                                $id = intval($_GPC["id"]);
                                                $order = order_fetch($id, true);
                                                if (empty($order)) {
                                                    imessage(error(-1, "订单不存在或已删除"), "", "ajax");
                                                }
                                                if ($order["is_pay"] != 1) {
                                                    imessage(error(-1, "订单未支付，无法修改收货地址"), "", "ajax");
                                                }
                                                if ($order["order_type"] != 1) {
                                                    imessage(error(-1, "该订单不是外卖单，无法修改收货地址"), "", "ajax");
                                                }
                                                if ($order["status"] == 5) {
                                                    imessage(error(-1, "订单已完成，无法修改配送地址"), "", "ajax");
                                                }
                                                if ($order["status"] == 6) {
                                                    imessage(error(-1, "订单已取消，无法修改配送地址"), "", "ajax");
                                                }
                                                $address_update_num = isset($order["data"]["order_info_update_num"]["address"]) ? $order["data"]["order_info_update_num"]["address"] : 0;
                                                if ($config["address_update_num"] <= $address_update_num) {
                                                    imessage(error(-1, "订单只能修改" . $config["address_update_num"] . "次收货地址"), "", "ajax");
                                                }
                                                if ($_W["ispost"]) {
                                                    $address_id = intval($_GPC["address_id"]);
                                                    $address = member_takeout_address_check($order["sid"], $address_id);
                                                    if (is_error($address)) {
                                                        imessage(error(-1, $address["message"]), "", "ajax");
                                                    }
                                                    $distance = batch_calculate_distance(array($address["location_y"], $address["location_x"]), array($order["location_y"], $order["location_x"]), 1);
                                                    if (is_error($distance)) {
                                                        imessage($distance, "", "ajax");
                                                    }
                                                    if ($config["newaddress_distance"] * 1000 < $distance[0]["distance"]) {
                                                        imessage(error(-1, "新地址与原地址的距离超出" . $config["newaddress_distance"] . "km, 无法修改"), "", "ajax");
                                                    }
                                                    $data = $order["data"];
                                                    $data["order_info_update_num"]["address"] = $address_update_num + 1;
                                                    $update = array("mobile" => $address["mobile"] ? $address["mobile"] : $_W["member"]["mobile"], "username" => $address["realname"] ? $address["realname"] : $_W["member"]["realname"], "sex" => $address["sex"], "address" => $address["address"] . $address["number"], "location_x" => floatval($address["location_x"]), "location_y" => floatval($address["location_y"]), "distance" => $address["distance"], "data" => iserializer($data));
                                                    pdo_update("tiny_wmall_order", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                                                    imessage(error(0, "修改收货地址成功"), "", "ajax");
                                                }
                                                $addresses = member_fetchall_address_bystore($order["sid"]);
                                                $available = array();
                                                $disavailable = array();
                                                $available_coord = array();
                                                if (!empty($addresses)) {
                                                    foreach ($addresses as $val) {
                                                        if ($val["available"] == 1) {
                                                            $available[] = $val;
                                                            $available_coord[] = array($val["location_y"], $val["location_x"]);
                                                        } else {
                                                            $disavailable[] = $val;
                                                        }
                                                    }
                                                }
                                                $distances = batch_calculate_distance($available_coord, array($order["location_y"], $order["location_x"]), 1);
                                                if (is_error($distances)) {
                                                    imessage($distances, "", "ajax");
                                                }
                                                if (!empty($distances)) {
                                                    foreach ($distances as $key => $value) {
                                                        if ($config["newaddress_distance"] * 1000 < $value["distance"]) {
                                                            $disavailable[] = $available[$key];
                                                            unset($available[$key]);
                                                        }
                                                    }
                                                }
                                                $addresses = array("available" => array_values($available), "dis_available" => array_values($disavailable));
                                                $result = array("order" => $order, "addresses" => $addresses, "config" => $config);
                                                imessage(error(0, $result), "", "ajax");
                                                return 1;
                                            } else {
                                                if ($ta == "zhunshibao") {
                                                    $id = intval($_GPC["id"]);
                                                    $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $id), array("id", "sid", "ordersn", "status", "paytime", "handletime", "delivery_assign_time", "endtime", "final_fee", "zhunshibao_price", "zhunshibao_compensate", "zhunshibao_status"));
                                                    if (empty($order)) {
                                                        imessage(error(-1, "订单不存在"), "", "ajax");
                                                    }
                                                    $config = get_plugin_config("zhunshibao.basic");
                                                    if (empty($order[$config["start_time"]])) {
                                                        if ($config["start_time"] == "paytime") {
                                                            $tip = "请在订单支付后查看";
                                                        } else {
                                                            if ($config["start_time"] == "handletime") {
                                                                $tip = "请在商家接单后查看";
                                                            } else {
                                                                if ($config["start_time"] == "delivery_assign_time") {
                                                                    $tip = "请在配送员接单后查看";
                                                                }
                                                            }
                                                        }
                                                        imessage(error(-1, $tip), "", "ajax");
                                                    }
                                                    $store = store_fetch($order["sid"], array("delivery_time", "data"));
                                                    $zhunshibao_tips = array();
                                                    $prredict_time = $order[$config["start_time"]] + $store["delivery_time"] * 60;
                                                    if (!empty($store["data"]["zhunshibao"]["rule"])) {
                                                        $fee_type = $store["data"]["zhunshibao"]["fee_type"];
                                                        $store["data"]["zhunshibao"]["rule"] = array_sort($store["data"]["zhunshibao"]["rule"], "time");
                                                        $order_final_fee = $order["final_fee"] - floatval($order["zhunshibao_price"]);
                                                        foreach ($store["data"]["zhunshibao"]["rule"] as $val) {
                                                            $compensate_time = $prredict_time + $val["time"] * 60;
                                                            $compensate_time_cn = date("H:i:s", $compensate_time);
                                                            if ($fee_type == 1) {
                                                                $compensate_fee = $val["fee"];
                                                            } else {
                                                                if ($fee_type == 2) {
                                                                    $compensate_fee = round($order_final_fee * $val["fee"] / 100, 2);
                                                                }
                                                            }
                                                            $zhunshibao_tips[] = "晚于" . $compensate_time_cn . "送达，赔付" . $compensate_fee . "元";
                                                        }
                                                    }
                                                    $order["endtime_cn"] = date("Y-m-d H:i:s", $order["endtime"]);
                                                    $order["prredict_time_cn"] = date("Y-m-d H:i:s", $prredict_time);
                                                    $order["zhunshibao_tips"] = $zhunshibao_tips;
                                                    $order["zhunshibao_agreement"] = get_config_text("zhunshibao:agreement");
                                                    $result = array("order" => $order);
                                                    imessage(error(0, $result), "", "ajax");
                                                    return 1;
                                                } else {
                                                    if ($ta == "refresh_map") {
                                                        $id = intval($_GPC["id"]);
                                                        $order = order_fetch($id);
                                                        if (empty($order)) {
                                                            imessage(error(-1, "订单不存在或已删除"), "", "ajax");
                                                        }
                                                        mload()->model("deliveryer");
                                                        $deliveryer = deliveryer_fetch($order["deliveryer_id"]);
                                                        $show_location = 0;
                                                        if ($order["order_type"] == 1 && $order["status"] < 5 && (check_plugin_perm("deliveryerApp") || check_plugin_perm("wxapp"))) {
                                                            $show_location = 1;
                                                        }
                                                        $result = array("order" => $order, "deliveryer" => $deliveryer, "show_location" => $show_location);
                                                        imessage(error(0, $result), "", "ajax");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>