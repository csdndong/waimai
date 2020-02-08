<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("coupon");
mload()->model("redPacket");
icheckAuth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$sid = intval($_GPC["sid"]);
$store = store_fetch($sid);
if (is_error($store)) {
    imessage(error(-1, "门店不存在"), "", "ajax");
}
if (empty($store["location_x"]) || empty($store["location_y"])) {
    imessage(error(-1, "门店未设置经纬度，需要完善经纬度后才能下单"), "", "ajax");
}
$status = check_mall_status($store["agentid"]);
if (is_error($status)) {
    imessage(error(-3000, $status["message"]), "", "ajax");
}
if ($ta == "index") {
    $is_pindan = intval($_GPC["is_pindan"]);
    if ($is_pindan == 1) {
        mload()->model("pindan");
        $pindan_id = intval($_GPC["pindan_id"]);
        $cart = pindan_data_init($sid, $pindan_id, false, array("is_submit" => 1));
    } else {
        $_GPC["buy_huangou_goods"] = 1;
        $goods_id = intval($_GPC["goods_id"]);
        $message_huangou = error(0, "");
        if (0 < $goods_id) {
            $sign = trim($_GPC["sign"]);
            $cart = cart_data_init($sid, $goods_id, 0, $sign);
            if (is_error($cart)) {
                $message_huangou = $cart;
                $cart = cart_data_init($sid);
            } else {
                $huangou_cart_message = $cart["message"]["msg"];
            }
        } else {
            $cart = cart_data_init($sid);
        }
        $cart = $cart["message"]["cart"];
    }
    if (is_error($cart)) {
        imessage($cart, "", "ajax");
    }
    if (empty($cart["data"])) {
        imessage(error(1000, "购物车数据错误"), "", "ajax");
    }
    $pay_types = order_pay_types();
    $default_order_type = 1;
    if ($store["delivery_type"] == 2) {
        $default_order_type = 2;
    }
    $condition = array("order_type" => $default_order_type, "address" => array());
    $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
    if (!empty($params)) {
        $address_id = intval($params["address_id"]);
        if (0 < $address_id) {
            $address = member_takeout_address_check($store, $address_id);
            if (!is_error($address)) {
                $condition["address"] = $address;
            }
        }
        $condition = array_merge($condition, $params);
    }
    if ($default_order_type == 2) {
        $address = array("address" => $store["address"]);
    }
    $config_order = $_W["we7_wmall"]["config"]["takeout"]["order"];
    if (empty($address) && $config_order["use_default_accept_address"] != 2) {
        $address = member_fetch_available_address($store);
        if (!is_error($address)) {
            $condition["address"] = $address;
            $guess_address = 1;
        }
    }
    $message = error(0, "");
    $price = store_order_condition($store, array($condition["address"]["location_y"], $condition["address"]["location_x"]));
    $reach_sendprice = order_is_reach_storesendprice($price["send_price"], $cart["cart_price"]);
    if (!$reach_sendprice) {
        if ($guess_address) {
            $condition["address"] = array();
        } else {
            $message = error("noReachSendPrice", "由于配送地址发生变化,起送价发生了变化");
        }
    }
    $condition["meal_redpackets"] = false;
    if (check_plugin_exist("mealRedpacket")) {
        mload()->model("plugin");
        pload()->model("mealRedpacket");
        $meal_redpackets = mealRedpacket_build_virtual();
        $condition["meal_redpackets"] = $meal_redpackets;
        unset($meal_redpackets);
    }
    $svip_redpacket = array();
    $show_buy_svip = 0;
    $config_svip_status = svip_status_is_available();
    if ($config_svip_status) {
        if ($_W["member"]["svip_status"] != 1) {
            $show_buy_svip = 1;
        }
        mload()->model("plugin");
        pload()->model("svip");
        if ($cart["is_buysvip"] == 1) {
            $_W["member"]["svip_status"] = 1;
        }
        $svip_redpacket = svip_store_useful_redpacket_get($sid, $cart["price"] + $cart["box_price"], array("is_buysvip" => $cart["is_buysvip"]));
        if (!is_error($svip_redpacket) && $cart["is_buysvip"] == 1) {
            $svip_redpacket_virtual = $svip_redpacket;
            if (!empty($svip_redpacket["store_redpacket"])) {
                $svip_redpacket_virtual = $svip_redpacket["store_redpacket"];
            }
            $svip_redpacket_virtual["original_id"] = $svip_redpacket["id"];
            $svip_redpacket_virtual["id"] = "svip_" . $svip_redpacket["id"];
            $svip_redpacket_virtual["scene"] = "waimai";
            $condition["svip_redpacket"] = $svip_redpacket_virtual;
        }
        $store_has_selfPickup = store_fetch_activity($sid, array("selfPickup"));
        $store["selfPickup_status"] = $store_has_selfPickup["num"];
        if (0 < $pindan_id) {
            $show_buy_svip = 0;
        }
    }
    $order = order_calculate($store, $cart, $condition);
    $order["show_buy_svip"] = $show_buy_svip;
    if (!empty($store["data"]["zhunshibao"]) && $store["data"]["zhunshibao"]["status"] == 1) {
        $order_meal_redpackets = $condition["buy_mealredpacket"] == 1 ? $order["meal_redpackets"]["price"] : 0;
        $order_zhunshibao_price = $condition["buy_zhunshibao"] == 1 ? $order["zhunshibao_price"] : 0;
        $order_final_fee = $order["final_fee"] - floatval($order["svip_meal"]["price"]) - $order_meal_redpackets - $order_zhunshibao_price;
        if ($store["data"]["zhunshibao"]["fee_type"] == 2) {
            $rule_cn = "";
            if (!empty($store["data"]["zhunshibao"]["rule"])) {
                foreach ($store["data"]["zhunshibao"]["rule"] as $val) {
                    $compensate_fee = round($order_final_fee * $val["fee"] / 100, 2);
                    $rule_cn .= "延误" . $val["time"] . "分钟,减" . $compensate_fee . "元";
                }
            }
            if (!empty($rule_cn)) {
                $rule_cn = rtrim($rule_cn, ",");
                $rule_cn = "骑手送达" . $rule_cn;
            }
            $store["data"]["zhunshibao"]["rule_cn"] = $rule_cn;
        }
    }
    $order["yinsihao"] = array("status" => 0, "agreement" => "");
    if (check_plugin_perm("yinsihao")) {
        $yinsihao = get_plugin_config("yinsihao.basic");
        if (!empty($yinsihao) && $yinsihao["status"] == 1) {
            $order["yinsihao"]["status"] = 1;
            $order["yinsihao"]["agreement"] = get_config_text("yinsihao:agreement");
        }
    }
    $result = array("store" => $store, "address" => $address ? $address : array(), "cart" => $cart, "coupons" => coupon_available($sid, $cart["price"]), "redPackets" => redPacket_available($cart["price"], explode("|", $store["cid"]), array("scene" => "waimai", "sid" => $sid, "order_type" => $condition["order_type"])), "addresses" => member_fetchall_address_bystore($sid), "order" => $order, "message" => $message, "islegal" => 0, "mobile" => $_W["member"]["mobile"], "config_takeout" => array("audit_accept_address" => $_W["we7_wmall"]["config"]["takeout"]["order"]["audit_accept_address"]));
    if (check_plugin_perm("huangou")) {
        $huangou_goods = array();
        mload()->model("plugin");
        pload()->model("huangou");
        if ($store["data"]["huangou"]["status"] == 1) {
            $huangou = huangou_get_store_goods($sid);
            if (!is_error($huangou)) {
                $huangou_goods = $huangou["huangou_goods"];
            }
            $result["huangou"] = array("activity" => $huangou["activity"], "goods" => $huangou_goods, "message" => $message_huangou, "cart_message" => $huangou_cart_message, "price_limit" => $huangou["price_limit"], "can_buy_huangou_goods" => $huangou["price_limit"] <= $order["total_fee_show"] ? 1 : 0);
        }
    }
    if ($condition["buy_mealredpacket"] == 1 && !is_error($order["meal_redpackets"])) {
        $result["redPackets"] = array_merge($result["redPackets"], $order["meal_redpackets"]["data"]);
    }
    if ($cart["is_buysvip"] == 1) {
        if (!empty($svip_redpacket) && !is_error($svip_redpacket)) {
            if (!empty($svip_redpacket["store_redpacket"])) {
                $result["svip_redpacket"] = $svip_redpacket;
            } else {
                $result["svip_redpacket"] = $svip_redpacket_virtual;
            }
        }
    } else {
        $result["svip_redpacket"] = $svip_redpacket;
    }
    $result["islegal"] = $store["is_in_business_hours"] && (!empty($address) || $order["order_type"] == 2) && empty($message["errno"]);
    $result["islegal"] = intval($result["islegal"]);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "submit") {
        $is_pindan = intval($_GPC["is_pindan"]);
        if ($is_pindan == 1) {
            mload()->model("pindan");
            $pindan_id = intval($_GPC["pindan_id"]);
            $cart = pindan_data_init($sid, $pindan_id, false, array("is_submit" => 1, "all_goods" => 1));
        } else {
            $_GPC["buy_huangou_goods"] = 1;
            $cart_is_buysvip = pdo_fetchcolumn("select is_buysvip from" . tablename("tiny_wmall_order_cart") . " where uniacid = :uniacid and uid = :uid and sid = :sid", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":sid" => $sid));
            $_GPC["is_buysvip"] = $cart_is_buysvip;
            $cart = cart_data_init($sid);
            $cart = $cart["message"]["cart"];
            $cart["is_buysvip"] = $cart_is_buysvip;
            $cart["original_data"] = $cart["original_data1"];
        }
        if (is_error($cart)) {
            imessage($cart, "", "ajax");
        }
        if (empty($cart["data"])) {
            imessage(error(1000, "购物车数据错误"), "", "ajax");
        }
        if (!$store["is_in_business_hours"]) {
            imessage(error(-1, "商户休息中"), "", "ajax");
        }
        $default_order_type = 1;
        if ($store["delivery_type"] == 2) {
            $default_order_type = 2;
        }
        $condition = array("order_type" => $default_order_type);
        $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
        if (empty($params)) {
            imessage(error(-1, "参数错误"), "", "ajax");
        }
        if ($params["order_type"] != 2) {
            $address_id = intval($params["address_id"]);
            $address = member_takeout_address_check($store, $address_id);
            if (is_error($address)) {
                imessage(error(-1, $address["message"]), "", "ajax");
            }
            $condition["address"] = $address;
            $price = store_order_condition($store, array($condition["address"]["location_y"], $condition["address"]["location_x"]));
            $reach_sendprice = order_is_reach_storesendprice($price["send_price"], $cart["cart_price"]);
            if (!$reach_sendprice) {
                imessage(error(1000, "参数错误"), "", "ajax");
            }
        }
        $condition = array_merge($condition, $params);
        $condition["meal_redpackets"] = false;
        if (check_plugin_exist("mealRedpacket")) {
            mload()->model("plugin");
            pload()->model("mealRedpacket");
            $meal_redpackets = mealRedpacket_build_virtual();
            $condition["meal_redpackets"] = $meal_redpackets;
            unset($meal_redpackets);
        }
        mload()->model("goods");
        $config_svip_status = svip_status_is_available();
        if ($config_svip_status) {
            mload()->model("plugin");
            pload()->model("svip");
            if ($cart["is_buysvip"] == 1) {
                $_W["member"]["svip_status"] = 1;
            }
            $svip_redpacket = svip_store_useful_redpacket_get($sid, $cart["price"] + $cart["box_price"], array("is_buysvip" => $cart["is_buysvip"]));
            if (!is_error($svip_redpacket) && $cart["is_buysvip"] == 1) {
                $svip_redpacket_virtual = $svip_redpacket;
                if (!empty($svip_redpacket["store_redpacket"])) {
                    $svip_redpacket_virtual = $svip_redpacket["store_redpacket"];
                }
                $svip_redpacket_virtual["original_id"] = $svip_redpacket["id"];
                $svip_redpacket_virtual["id"] = "svip_" . $svip_redpacket["id"];
                $svip_redpacket_virtual["scene"] = "waimai";
                $condition["svip_redpacket"] = $svip_redpacket_virtual;
            }
        }
        $calculate = order_calculate($store, $cart, $condition);
        $invoice_id = intval($condition["invoiceId"]);
        if (0 < $invoice_id) {
            $invoice = member_invoice($invoice_id);
            $invoice = iserializer(array("title" => $invoice["title"], "recognition" => $invoice["recognition"]));
        }
        $order = array("uniacid" => $_W["uniacid"], "agentid" => $store["agentid"], "acid" => $_W["acid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "mall_first_order" => $_W["member"]["is_mall_newmember"], "ordersn" => date("YmdHis") . random(6, true), "serial_sn" => store_order_serial_sn($sid), "code" => random(4, true), "order_type" => $calculate["order_type"], "openid" => $_W["openid"], "mobile" => $address["mobile"] ? $address["mobile"] : $_W["member"]["mobile"], "username" => $address["realname"] ? $address["realname"] : $_W["member"]["realname"], "sex" => $address["sex"], "address" => $address["address"] . $address["number"], "location_x" => floatval($address["location_x"]), "location_y" => floatval($address["location_y"]), "delivery_day" => date("Y") . "-" . $calculate["deliveryTimes"]["predict_day_cn"], "delivery_time" => $calculate["deliveryTimes"]["predict_time_cn"], "delivery_fee" => $calculate["delivery_fee"], "pack_fee" => $calculate["pack_fee"], "pay_type" => trim($_GPC["pay_type"]), "num" => $cart["num"], "distance" => $address["distance"], "box_price" => $calculate["box_price"], "price" => $calculate["price"], "extra_fee" => $calculate["extra_fee"], "total_fee" => $calculate["total_fee"], "discount_fee" => $calculate["discount_fee"], "store_discount_fee" => $calculate["activityed"]["store_discount_fee"], "plateform_discount_fee" => $calculate["activityed"]["plateform_discount_fee"], "agent_discount_fee" => $calculate["activityed"]["agent_discount_fee"], "final_fee" => $calculate["final_fee"], "vip_free_delivery_fee" => !empty($calculate["activityed"]["list"]["vip_delivery"]) ? 1 : 0, "delivery_type" => $store["delivery_mode"], "status" => 1, "is_comment" => 0, "invoice" => $invoice, "addtime" => TIMESTAMP, "person_num" => intval($store["data"]["order_form"]["person_num"]) == 1 ? intval($condition["person_num"]) : 0, "data" => array("formId" => $condition["formId"], "extra_fee" => $calculate["extra_fee_detail"], "cart" => iunserializer($cart["original_data"]), "commission" => array("spread1_rate" => "0%", "spread1" => 0, "spread2_rate" => "0%", "spread2" => 0), "store" => array("location_x" => $store["location_x"], "location_y" => $store["location_y"])), "note" => trim($calculate["note"]));
        if (0 < $pindan_id) {
            $order["pindan_id"] = $pindan_id;
            $cart["other"][] = $cart["mine"];
            $order["data"]["pindan"] = $cart["other"];
        }
        if ($order["delivery_time"] != "立即送出") {
            $predict_start_time = substr($order["delivery_time"], 0, 5);
            $predict_time_cn = $order["delivery_day"] . " " . $predict_start_time;
            $order["deliverytime"] = strtotime($predict_time_cn);
        }
        if ($order["order_type"] == 2) {
            $order["mobile"] = $condition["mobile"];
        }
        if ($order["final_fee"] < 0) {
            $order["final_fee"] = 0;
        }
        $order["data"]["final_fee_pay"] = $order["final_fee"];
        $order["spreadbalance"] = 1;
        if (check_plugin_perm("spread")) {
            mload()->model("plugin");
            pload()->model("spread");
            $order = order_spread_commission_calculate("takeout", $order);
        }
        if ($condition["buy_mealredpacket"] == 1) {
            $meal_redpacket_order = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "sid" => $calculate["meal_redpackets"]["superRedpacket_id"], "meal_id" => "", "order_sn" => date("YmdHis") . random(6, true), "final_fee" => floatval($calculate["meal_redpackets"]["price"]), "is_pay" => 0, "addtime" => TIMESTAMP, "type" => "exchangeRedpacket", "data" => iserializer(array("meal" => $calculate["meal_redpackets"])));
            pdo_insert("tiny_wmall_superredpacket_meal_order", $meal_redpacket_order);
            $meal_redpacket_order_id = pdo_insertid();
            $order["data"]["meal_redpacket"] = array("fee" => $meal_redpacket_order["final_fee"], "meal_order_id" => $meal_redpacket_order_id, "pre_mealredpacket_used_key" => !empty($calculate["meal_redpackets"]["pre_mealredpacket_used_key"]) ? $calculate["meal_redpackets"]["pre_mealredpacket_used_key"] : 0);
            $order["final_fee"] = $order["final_fee"] - $calculate["meal_redpackets"]["price"];
        }
        if (!empty($calculate["svip_meal"])) {
            $svip_order = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "ordersn" => date("YmdHis") . random(6, true), "meal_id" => $calculate["svip_meal"]["id"], "final_fee" => $calculate["svip_meal"]["price"], "is_pay" => 0, "order_channel" => $_W["ochannel"] == "wxapp" ? "wxapp" : "wechat", "addtime" => TIMESTAMP, "data" => iserializer(array("days" => $calculate["svip_meal"]["days"])));
            pdo_insert("tiny_wmall_svip_meal_order", $svip_order);
            $svip_order_id = pdo_insertid();
            $order["data"]["svip"] = array("fee" => $svip_order["final_fee"], "svip_order_id" => $svip_order_id, "pre_svip_redpacket_id" => intval($calculate["pre_svip_redpacket_id"]));
            $order["final_fee"] = $order["final_fee"] - $calculate["svip_meal"]["price"];
        }
        if ($calculate["buy_zhunshibao"] == 1) {
            $order["zhunshibao_price"] = $calculate["zhunshibao_price"];
            $order["zhunshibao_status"] = 1;
        }
        if ($condition["yinsihao_status"] == 1) {
            $order["data"]["yinsihao_status"] = 1;
        }
        $order["data"] = iserializer($order["data"]);
        pdo_insert("tiny_wmall_order", $order);
        $order_id = pdo_insertid();
        $order_id = intval($order_id);
        if (empty($order_id)) {
            imessage(error(-1, "订单信息有误，请重新下单"), "", "ajax");
        }
        if ($condition["yinsihao_status"] == 1) {
            mload()->model("plugin");
            pload()->model("yinsihao");
            $bind_status = yinsihao_bind($order_id, "member", $order["ordersn"]);
            if (is_error($bind_status)) {
                slog("yinsihao", "隐私号绑定错误", array("order_id" => $order_id), "生成顾客隐私号错误" . $bind_status["message"]);
            }
        }
        order_update_bill($order_id, array("activity" => $calculate["activityed"]));
        order_insert_discount($order_id, $sid, $calculate["activityed"]["list"]);
        order_insert_status_log($order_id, "place_order");
        if (0 < $pindan_id) {
            order_update_goods_info($order_id, $sid, $cart);
        } else {
            order_update_goods_info($order_id, $sid);
        }
        order_del_member_cart($sid, $pindan_id);
        imessage(error(0, $order_id), "", "ajax");
    } else {
        if ($ta == "note") {
            $type = trim($_GPC["type"]);
            $invoices = array();
            if ($type == "invoice" && $store["invoice_status"] == 1) {
                $invoices = member_invoices();
            }
            $result = array("invoices" => $invoices, "store" => $store);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "exchange") {
                $cart = order_fetch_member_cart($sid);
                $id = intval($_GPC["id"]);
                $oldid = intval($_GPC["oldid"]);
                $discount = floatval($_GPC["discount"]);
                if ($cart["is_buysvip"] == 1) {
                    $_W["member"]["svip_status"] = 1;
                }
                mload()->model("plugin");
                pload()->model("svip");
                if (0 < $oldid) {
                    $redpacket_id = svip_store_redpacket_exchange($id, $oldid);
                } else {
                    $redpacket_id = svip_redpacket_exchage($id);
                }
                if (is_error($redpacket_id)) {
                    imessage($redpacket_id, "", "ajax");
                }
                $result = array("redPackets" => redPacket_available($cart["price"], explode("|", $store["cid"]), array("scene" => "waimai", "sid" => $sid)), "svip_redpacket" => svip_store_useful_redpacket_get($sid, $discount), "redpacket_id" => $redpacket_id);
                imessage(error(0, $result), "", "ajax");
            } else {
                if ($ta == "destroyed") {
                    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]), array("svip_status"));
                    $_W["member"]["svip_status"] = $member["svip_status"];
                }
            }
        }
    }
}

?>
