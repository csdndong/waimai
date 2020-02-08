<?php
defined("IN_IA") or exit("Access Denied");
define("ORDER_TYPE", "takeout");
global $_W;
global $_GPC;
mload()->model("member");
icheckAuth();
$_W["page"]["title"] = "提交订单";
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "goods";
$sid = intval($_GPC["sid"]);
store_business_hours_init($sid);
$store = store_fetch($sid, array("agentid", "id", "cid", "is_rest", "title", "logo", "location_x", "location_y", "invoice_status", "delivery_type", "delivery_mode", "delivery_price", "delivery_fee_mode", "delivery_areas", "delivery_time", "delivery_free_price", "pack_price", "delivery_within_days", "delivery_reserve_days", "order_note", "data", "not_in_serve_radius", "serve_radius"));
if (is_error($store)) {
    imessage("门店不存在", "", "error");
}
if ($store["is_rest"] == 1) {
    imessage(array("title" => "门店已打烊,换个店铺下单哇！", "btn_text" => "看看其他店铺"), imurl("wmall/home/index"), "info");
}
if (empty($store["location_x"]) || empty($store["location_y"])) {
    imessage("门店未设置经纬度，需要完善经纬度后才能下单", imurl("wmall/home/index"), "info");
}
$store["payment"] = get_available_payment("takeout", $sid);
if ($ta == "goods") {
    $cart = order_insert_member_cart($sid);
    if (is_error($cart)) {
        header("location:" . imurl("wmall/store/goods", array("sid" => $sid)));
        exit;
    }
    header("location:" . imurl("wmall/order/create/index", array("sid" => $sid)));
    exit;
}
if ($ta == "index") {
    $cart = order_fetch_member_cart($sid);
    if (empty($cart)) {
        if (!is_h5app()) {
            header("location:" . imurl("wmall/store/goods", array("sid" => $sid)));
        }
        exit;
    }
    $pay_types = order_pay_types();
    if (empty($store["payment"])) {
        imessage("店铺没有设置有效的支付方式", referer(), "error");
    }
    $config_order = $_W["we7_wmall"]["config"]["takeout"]["order"];
    if ($_GPC["address_id"] || $config_order["use_default_accept_address"] != 2) {
        $address = member_fetch_available_address($store);
        $address_id = $address["id"];
    }
    $delivery_time = store_delivery_times($sid);
    $time_flag = 0;
    $predict_index = 0;
    $predict_timestamp = TIMESTAMP + 60 * $store["delivery_time"];
    if (!$delivery_time["reserve"]) {
        $data = array_order(TIMESTAMP + 60 * $store["delivery_time"], $delivery_time["timestamp"]);
        if (!empty($data)) {
            $time_flag = 1;
            $predict_index = array_search($data, $delivery_time["timestamp"]);
            $predict_day = $delivery_time["days"][0];
            $predict_time_cn = (string) $delivery_time["times"][$predict_index]["start"] . "~" . $delivery_time["times"][$predict_index]["end"];
            $text_time = $predict_time = "尽快送达";
            $predict_extra_price = $delivery_time["times"][$predict_index]["fee"];
        } else {
            $predict_day = $delivery_time["days"][1];
            $predict_times = array_shift($delivery_time["times"]);
            $predict_time = (string) $predict_times["start"] . "~" . $predict_times["end"];
            $text_time = (string) $predict_day . " " . $predict_time;
        }
        $predict_delivery_price = $store["delivery_price"] + $delivery_time["times"][$predict_index]["fee"];
        if ($store["delivery_fee_mode"] == 1) {
            $predict_delivery_price = (string) $predict_delivery_price . "元配送费";
        } else {
            $predict_delivery_price = "配送费" . $predict_delivery_price . "元起";
        }
    } else {
        $predict_day = $delivery_time["days"][0];
        $predict_time = (string) $delivery_time["times"][0]["start"] . "~" . $delivery_time["times"][0]["end"];
        $text_time = (string) $predict_day . " " . $predict_time;
    }
    $delivery_price = 0;
    if ($store["delivery_type"] != 2) {
        if ($store["delivery_fee_mode"] == 1) {
            $delivery_price_basic = $store["delivery_price"];
            $delivery_price = $store["delivery_price"] + $delivery_time["times"][$predict_index]["fee"];
        } else {
            if ($store["delivery_fee_mode"] == 2) {
                $delivery_price = $delivery_price_basic = $store["delivery_price_extra"]["start_fee"];
                $distance = $address["distance"];
                if (!empty($address) && 0 < $distance) {
                    if ($store["delivery_price_extra"]["over_km"] < $distance && 0 < $store["delivery_price_extra"]["over_pre_km_fee"]) {
                        $delivery_price += ($distance - $store["delivery_price_extra"]["start_km"]) * $store["delivery_price_extra"]["over_pre_km_fee"];
                    } else {
                        if ($store["delivery_price_extra"]["start_km"] < $distance && 0 < $store["delivery_price_extra"]["pre_km_fee"]) {
                            $delivery_price += ($distance - $store["delivery_price_extra"]["start_km"]) * $store["delivery_price_extra"]["pre_km_fee"];
                        }
                    }
                    $delivery_price = $delivery_price_basic = round($delivery_price, 2);
                    if (0 < $store["delivery_price_extra"]["max_fee"] && $store["delivery_price_extra"]["max_fee"] < $delivery_price) {
                        $delivery_price = $store["delivery_price_extra"]["max_fee"];
                    }
                    $delivery_price += $delivery_time["times"][$predict_index]["fee"];
                }
                $_SESSION["delivery_price"] = $delivery_price;
            } else {
                if ($store["delivery_fee_mode"] == 3 && !empty($address)) {
                    $area_index = 0;
                    foreach ($store["delivery_areas"] as $key => $row) {
                        $is_ok = isPointInPolygon($row["path"], array($address["location_y"], $address["location_x"]));
                        if ($is_ok) {
                            $area_index = $key;
                            break;
                        }
                    }
                    if (!empty($area_index)) {
                        $area = $store["delivery_areas"][$area_index];
                        $delivery_price = $delivery_price_basic = round($area["delivery_price"], 2);
                        $send_price = $area["send_price"];
                        $delivery_free_price = $area["delivery_free_price"];
                        $delivery_price += $delivery_time["times"][$predict_index]["fee"];
                    }
                }
            }
        }
    }
    $cookie_price_original = array();
    if (!empty($_GPC["_cookie_price"])) {
        $cookie_price_original = iunserializer(base64_decode($_GPC["_cookie_price"]));
    }
    $cookie_price = array("delivery_price" => $delivery_price, "delivery_free_price" => $delivery_free_price);
    isetcookie("_cookie_price", base64_encode(iserializer($cookie_price)), 180);
    $send_diff = 0;
    if ($cart["price"] + $cart["box_price"] < $send_price) {
        $send_diff = round($send_price - $cart["price"] - $cart["box_price"], 2);
    } else {
        if (!empty($address_id)) {
            isetcookie("__aid", $address["id"], 300);
        }
    }
    $coupon_text = "无可用代金券";
    mload()->model("coupon");
    $coupons = coupon_available($sid, $cart["price"]);
    if (!empty($coupons)) {
        $coupon_text = count($coupons) . "张可用代金券";
    }
    $redPacket_text = "无可用红包";
    mload()->model("redPacket");
    $redPackets = redPacket_available($cart["price"], explode("|", $store["cid"]), array("scene" => "waimai", "sid" => $sid));
    if (!empty($redPackets)) {
        $redPacket_text = count($redPackets) . "个可用红包";
    }
    $recordid = intval($_GPC["recordid"]);
    $redPacket_id = intval($_GPC["redPacket_id"]);
    $activityed = order_count_activity($sid, $cart, $recordid, $redPacket_id, $delivery_price, $delivery_free_price);
    if (!empty($activityed["list"]["token"])) {
        $coupon_text = (string) $activityed["list"]["token"]["value"] . "元券";
    }
    if (!empty($activityed["list"]["redPacket"])) {
        $redPacket_text = "-￥" . $activityed["list"]["redPacket"]["value"];
        $redPacket = $activityed["redPacket"];
    }
    $activity_price = $activity_notSelfDelivery_price = $activityed["total"];
    $delivery_activity_price = 0;
    if (!empty($activityed) && (!empty($activityed["list"]["delivery"]) || !empty($activityed["list"]["deliveryFeeDiscount"]))) {
        $delivery_activity_price = floatval($activityed["list"]["delivery"]["value"] + $activityed["list"]["deliveryFeeDiscount"]["value"]);
    }
    $self_delivery_activity_price = 0;
    if (!empty($activityed) && !empty($activityed["list"]["selfDelivery"]) && empty($activityed["list"]["selfPickup"])) {
        $self_delivery_activity_price = $activityed["list"]["selfDelivery"]["value"];
    }
    $extra_fee = 0;
    if (!empty($store["data"]["extra_fee"])) {
        foreach ($store["data"]["extra_fee"] as $key => $item) {
            $item_fee = floatval($item["fee"]);
            if ($item["status"] == 1 && 0 < $item_fee) {
                $extra_fee += $item_fee;
            } else {
                unset($store["data"]["extra_fee"][$key]);
            }
        }
    }
    $waitprice = $cart["price"] + $cart["box_price"] + $delivery_price + $store["pack_price"] - $activityed["total"] + $self_delivery_activity_price + $extra_fee;
    $activity_price -= $self_delivery_activity_price;
    $activity_notSelfDelivery_price -= $self_delivery_activity_price;
    if ($store["delivery_type"] == 2) {
        $waitprice -= $self_delivery_activity_price;
        $activity_price += $self_delivery_activity_price;
        $activity_notSelfDelivery_price += $self_delivery_activity_price;
    }
    $waitprice = 0 < $waitprice ? $waitprice : 0;
}
if ($ta == "submit") {
    if (!$_W["isajax"]) {
        imessage(error(-1, "非法访问"), "", "ajax");
    }
    $cart = order_check_member_cart($sid);
    if (is_error($cart)) {
        imessage($cart, "", "ajax");
    }
    if ($_GPC["order_type"] == 1) {
        $address = member_takeout_address_check($store, $_GPC["address_id"]);
        if (is_error($address)) {
            imessage(error(-1, $address["message"]), "", "ajax");
        }
        $delivery_time = store_delivery_times($sid);
        $predict_index = intval($_GPC["delivery_index"]);
        $delivery_price = 0;
        if ($store["delivery_type"] != 2) {
            if ($store["delivery_fee_mode"] == 1) {
                $delivery_price = $store["delivery_price"] + $delivery_time["times"][$predict_index]["fee"];
            } else {
                if ($store["delivery_fee_mode"] == 2) {
                    $distance = $address["distance"];
                    $delivery_price = $store["delivery_price_extra"]["start_fee"];
                    if (0 < $distance) {
                        if ($store["delivery_price_extra"]["over_km"] < $distance && 0 < $store["delivery_price_extra"]["over_pre_km_fee"]) {
                            $delivery_price += ($distance - $store["delivery_price_extra"]["start_km"]) * $store["delivery_price_extra"]["over_pre_km_fee"];
                        } else {
                            if ($store["delivery_price_extra"]["start_km"] < $distance && 0 < $store["delivery_price_extra"]["pre_km_fee"]) {
                                $delivery_price += ($distance - $store["delivery_price_extra"]["start_km"]) * $store["delivery_price_extra"]["pre_km_fee"];
                            }
                        }
                        $delivery_price = round($delivery_price, 2);
                        if (0 < $store["delivery_price_extra"]["max_fee"] && $store["delivery_price_extra"]["max_fee"] < $delivery_price) {
                            $delivery_price = $store["delivery_price_extra"]["max_fee"];
                        }
                        $delivery_price += $delivery_time["times"][$predict_index]["fee"];
                    }
                    if (!empty($_SESSION["delivery_price"])) {
                        $delivery_price = $_SESSION["delivery_price"];
                    }
                } else {
                    if ($store["delivery_fee_mode"] == 3) {
                        $price = store_order_condition($store, array($address["location_y"], $address["location_x"]));
                        $send_price = $price["send_price"];
                        if ($cart["price"] + $cart["box_price"] < $send_price) {
                            imessage(error(-1, "当前商品不满起送价"), "", "ajax");
                        }
                        $delivery_price = round($price["delivery_price"], 2);
                        $delivery_free_price = $price["delivery_free_price"];
                        $delivery_price += $delivery_time["times"][$predict_index]["fee"];
                    }
                }
            }
        }
    } else {
        if ($_GPC["order_type"] == 2) {
            $address = array("realname" => trim($_GPC["username"]), "mobile" => trim($_GPC["mobile"]));
        }
    }
    isetcookie("_cookie_price", "", -100);
    $order_type = intval($_GPC["order_type"]) ? intval($_GPC["order_type"]) : 1;
    $recordid = intval($_GPC["record_id"]);
    $redPacket_id = intval($_GPC["redPacket_id"]);
    $activityed = order_count_activity($sid, $cart, $recordid, $redPacket_id, $delivery_price, $delivery_free_price, $order_type);
    $extra_fee_note = array();
    $extra_fee = 0;
    if (!empty($store["data"]["extra_fee"])) {
        foreach ($store["data"]["extra_fee"] as $item) {
            $item_fee = floatval($item["fee"]);
            if ($item["status"] == 1 && 0 < $item_fee) {
                $extra_fee += $item_fee;
                $extra_fee_note[] = $item;
            }
        }
    }
    $total_fee = $cart["price"] + $cart["box_price"] + $store["pack_price"] + $delivery_price + $extra_fee;
    $order = array("uniacid" => $_W["uniacid"], "agentid" => $store["agentid"], "acid" => $_W["acid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "mall_first_order" => $_W["member"]["is_mall_newmember"], "ordersn" => date("YmdHis") . random(6, true), "serial_sn" => store_order_serial_sn($sid), "code" => random(4, true), "order_type" => $order_type, "openid" => $_W["openid"], "mobile" => $address["mobile"], "username" => $address["realname"], "sex" => $address["sex"], "address" => $address["address"] . $address["number"], "location_x" => floatval($address["location_x"]), "location_y" => floatval($address["location_y"]), "delivery_day" => trim($_GPC["delivery_day"]) ? date("Y") . "-" . trim($_GPC["delivery_day"]) : date("Y-m-d"), "delivery_time" => trim($_GPC["delivery_time"]) ? trim($_GPC["delivery_time"]) : "尽快送出", "delivery_fee" => $delivery_price, "pack_fee" => $store["pack_price"], "pay_type" => trim($_GPC["pay_type"]), "num" => $cart["num"], "distance" => $distance, "box_price" => $cart["box_price"], "price" => $cart["price"], "extra_fee" => $extra_fee, "total_fee" => $total_fee, "discount_fee" => $activityed["total"], "store_discount_fee" => $activityed["store_discount_fee"], "plateform_discount_fee" => $activityed["plateform_discount_fee"], "agent_discount_fee" => $activityed["agent_discount_fee"], "final_fee" => $total_fee - $activityed["total"], "vip_free_delivery_fee" => !empty($activityed["list"]["vip_delivery"]) ? 1 : 0, "delivery_type" => $store["delivery_mode"], "status" => 1, "is_comment" => 0, "invoice" => trim($_GPC["invoice"]), "addtime" => TIMESTAMP, "data" => array("extra_fee" => $extra_fee_note, "cart" => iunserializer($cart["original_data"]), "commission" => array("spread1_rate" => "0%", "spread1" => 0, "spread2_rate" => "0%", "spread2" => 0), "store" => array("location_x" => $store["location_x"], "location_y" => $store["location_y"])), "note" => trim($_GPC["note"]));
    if ($order["delivery_time"] != "尽快送出") {
        $predict_start_time = substr($order["delivery_time"], 0, 5);
        $predict_time_cn = $order["delivery_day"] . " " . $predict_start_time;
        $order["deliverytime"] = strtotime($predict_time_cn);
    }
    if ($order["final_fee"] < 0) {
        $order["final_fee"] = 0;
    }
    $order["spreadbalance"] = 1;
    if (check_plugin_perm("spread") && !empty($_W["member"]["spread1"]) && $_W["member"]["spreadfixed"] == 1) {
        mload()->model("plugin");
        $_W["plugin"] = array("name" => "spread");
        pload()->model("spread");
        $config_spread = get_plugin_config("spread");
        $order["spread1"] = $_W["member"]["spread1"];
        if ($config_spread["basic"]["level"] == 2) {
            $order["spread2"] = $_W["member"]["spread2"];
        }
        $spreads = pdo_fetchall("select uid,spread_groupid from " . tablename("tiny_wmall_members") . " where uid = :uid1 or uid = :uid2", array(":uid1" => $order["spread1"], ":uid2" => $order["spread2"]), "uid");
        if (!empty($spreads)) {
            $order["spreadbalance"] = 0;
            $groups = spread_groups();
            $group1 = $groups[$spreads[$order["spread1"]]["spread_groupid"]];
            $commission1_type = $group1["commission_type"];
            if ($commission1_type == "ratio") {
                $spread1_rate = $group1["commission1"] / 100;
                $commission_spread1 = round($spread1_rate * $order["final_fee"], 2);
                $spread1_rate = $spread1_rate * 100;
            } else {
                if ($commission1_type == "fixed") {
                    $commission_spread1 = $group1["commission1"];
                }
            }
            if (!empty($order["spread2"])) {
                $group2 = $groups[$spreads[$order["spread2"]]["spread_groupid"]];
                $commission2_type = $group2["commission_type"];
                if ($commission2_type == "ratio") {
                    $spread2_rate = $group2["commission2"] / 100;
                    $commission_spread2 = round($spread2_rate * $order["final_fee"], 2);
                    $spread2_rate = $spread2_rate * 100;
                } else {
                    if ($commission1_type == "fixed") {
                        $commission_spread2 = $group2["commission2"];
                    }
                }
            }
            $order["data"]["spread"] = array("commission" => array("commission1_type" => $commission1_type, "spread1_rate" => (string) $spread1_rate . "%", "spread1" => $commission_spread1, "commission2_type" => $commission2_type, "spread2_rate" => (string) $spread2_rate . "%", "spread2" => $commission_spread2, "from_spread" => $_SESSION["from_spread_id"]));
        }
    }
    $order["data"] = iserializer($order["data"]);
    unset($_SESSION["from_spread_id"]);
    pdo_insert("tiny_wmall_order", $order);
    $order_id = pdo_insertid();
    order_update_bill($order_id, array("activity" => $activityed));
    order_insert_discount($order_id, $sid, $activityed["list"]);
    order_insert_status_log($order_id, "place_order");
    order_update_goods_info($order_id, $sid);
    order_del_member_cart($sid);
    imessage(error(0, $order_id), "", "ajax");
}
include itemplate("order/create");

?>