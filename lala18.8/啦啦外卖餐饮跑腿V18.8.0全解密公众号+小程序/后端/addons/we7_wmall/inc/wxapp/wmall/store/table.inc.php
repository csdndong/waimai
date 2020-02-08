<?php
defined("IN_IA") or exit("Access Denied");
define("ORDER_TYPE", "tangshi");
mload()->model("goods");
mload()->model("table");
global $_W;
global $_GPC;
icheckauth();
$_W["page"]["title"] = "商品列表";
$sid = intval($_GPC["sid"]);
$store = store_fetch($sid);
if (empty($store)) {
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}
if ($store["is_meal"] != 1) {
    imessage(error(-1000, "店内点餐暂未开启"), "", "ajax");
}
$_share = array("title" => $store["title"], "desc" => $store["content"], "imgUrl" => tomedia($store["logo"]));
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $table_id = intval($_GPC["table_id"]);
    $table = table_fetch($table_id);
    if (empty($table)) {
        imessage(error(-1000, "桌号不存在"), "", "ajax");
    }
    $store["activity"] = store_fetch_activity($sid);
    $store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
    mload()->model("coupon");
    $coupons = coupon_collect_member_available($sid);
    $template = $store["data"]["wxapp"]["template"] ? $store["data"]["wxapp"]["template"] : 1;
    $categorys = array_values(store_fetchall_goods_category($sid, 1, false, "all", "available"));
    $store["table_id"] = $table["id"];
    $result = array("store" => $store, "coupon" => $coupons, "category" => $categorys, "cart" => cart_data_init($sid), "template" => $template, "share" => $_share, "table" => $table);
    if ($store["data"]["tangshi"]["pindan_status"] == 1) {
        $cart_id = $result["cart"]["message"]["cart"]["id"];
        if (empty($table["cart_id"]) && $table["status"] == 1) {
            pdo_update("tiny_wmall_tables", array("cart_id" => $cart_id), array("uniacid" => $_W["uniacid"], "id" => $table["id"]));
        } else {
            if (0 < $table["cart_id"] && $table["status"] == 1) {
                $first_cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "id" => $table["cart_id"]), array("id", "pindan_id", "pindan_status"));
                if ($first_cart["pindan_id"] == $first_cart["id"] && $first_cart["pindan_status"] == 1) {
                    $pindan_id = $first_cart["id"];
                    if ($first_cart["id"] != $cart_id) {
                        pdo_update("tiny_wmall_order_cart", array("pindan_id" => $first_cart["id"], "pindan_status" => 1), array("uniacid" => $_W["uniacid"], "id" => $cart_id));
                    }
                } else {
                    if (empty($first_cart["pindan_id"]) && $cart_id != $first_cart["id"]) {
                        $pindan_id = $first_cart["id"];
                        pdo_update("tiny_wmall_order_cart", array("pindan_id" => $first_cart["id"], "pindan_status" => 1), array("uniacid" => $_W["uniacid"], "id" => $first_cart["id"]));
                        pdo_update("tiny_wmall_order_cart", array("pindan_id" => $first_cart["id"], "pindan_status" => 1), array("uniacid" => $_W["uniacid"], "id" => $cart_id));
                    }
                }
            }
        }
        if (!empty($pindan_id)) {
            $result["cart"]["message"]["cart"]["pindan_id"] = $pindan_id;
            $result["cart"]["message"]["cart"]["pindan_status"] = 1;
        }
    }
    $cid = intval($_GPC["cid"]) ? intval($_GPC["cid"]) : $categorys[0]["id"];
    $child_id = 0;
    if (!empty($categorys[0]["child"])) {
        $child_id = $categorys[0]["child"][0]["id"];
    }
    $result["goods"] = goods_filter($sid, array("cid" => $cid, "child_id" => $child_id));
    $result["cid"] = $cid;
    $result["child_id"] = $child_id;
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "index_vue") {
        $table_id = intval($_GPC["table_id"]);
        $table = table_fetch($table_id);
        if (empty($table)) {
            imessage(error(-1000, "桌号不存在"), "", "ajax");
        }
        $store["activity"] = store_fetch_activity($sid);
        $store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
        mload()->model("coupon");
        $coupons = coupon_collect_member_available($sid);
        $template = $store["data"]["wxapp"]["template"] ? $store["data"]["wxapp"]["template"] : 1;
        $template_page = $store["data"]["wxapp"]["template_page"]["vue"] ? $store["data"]["wxapp"]["template_page"]["vue"] : 0;
        $store["table_id"] = $table["id"];
        $result = array("store" => $store, "coupon" => $coupons, "cart" => cart_data_init($sid), "template" => $template, "table" => $table, "template_page" => $template_page);
        if ($store["data"]["tangshi"]["pindan_status"] == 1) {
            $cart_id = $result["cart"]["message"]["cart"]["id"];
            if (empty($table["cart_id"]) && $table["status"] == 1) {
                pdo_update("tiny_wmall_tables", array("cart_id" => $cart_id), array("uniacid" => $_W["uniacid"], "id" => $table["id"]));
            } else {
                if (0 < $table["cart_id"] && $table["status"] == 1) {
                    $first_cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "id" => $table["cart_id"]), array("id", "pindan_id", "pindan_status"));
                    if ($first_cart["pindan_id"] == $first_cart["id"] && $first_cart["pindan_status"] == 1) {
                        $pindan_id = $first_cart["id"];
                        if ($first_cart["id"] != $cart_id) {
                            pdo_update("tiny_wmall_order_cart", array("pindan_id" => $first_cart["id"], "pindan_status" => 1), array("uniacid" => $_W["uniacid"], "id" => $cart_id));
                        }
                    } else {
                        if (empty($first_cart["pindan_id"]) && $cart_id != $first_cart["id"]) {
                            $pindan_id = $first_cart["id"];
                            pdo_update("tiny_wmall_order_cart", array("pindan_id" => $first_cart["id"], "pindan_status" => 1), array("uniacid" => $_W["uniacid"], "id" => $first_cart["id"]));
                            pdo_update("tiny_wmall_order_cart", array("pindan_id" => $first_cart["id"], "pindan_status" => 1), array("uniacid" => $_W["uniacid"], "id" => $cart_id));
                        }
                    }
                }
            }
            if (!empty($pindan_id)) {
                $result["cart"]["message"]["cart"]["pindan_id"] = $pindan_id;
                $result["cart"]["message"]["cart"]["pindan_status"] = 1;
            }
        }
        if ($template_page == 1) {
            $categorys = array_values(store_fetchall_goods_category($sid, 1, false, "all", "available"));
            $result["category"] = $categorys;
            $cid = intval($_GPC["cid"]) ? intval($_GPC["cid"]) : $categorys[0]["id"];
            $child_id = 0;
            if (!empty($categorys[0]["child"])) {
                $child_id = $categorys[0]["child"][0]["id"];
            }
            $result["goods"] = goods_filter($sid, array("cid" => $cid, "child_id" => $child_id));
            $result["cid"] = $cid;
            $result["child_id"] = $child_id;
        } else {
            $all = goods_avaliable_fetchall($sid);
            $result["cate_has_goods"] = $all["cate_has_goods"];
        }
        $_W["_share"] = $_share;
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "truncate") {
            $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]));
            if (0 < $cart["pindan_id"]) {
                pdo_update("tiny_wmall_order_cart", array("num" => 0, "box_price" => 0, "price" => 0, "original_price" => 0, "data" => "", "original_data" => "", "bargain_use_limit" => 0, "is_buysvip" => 0), array("uniacid" => $_W["uniacid"], "id" => $cart["id"]));
            } else {
                $data = pdo_delete("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]));
            }
            imessage(error(0, ""), "", "ajax");
        } else {
            if ($ta == "cart") {
                $goods_id = intval($_GPC["goods_id"]);
                $option_id = trim($_GPC["option_id"]);
                $sign = trim($_GPC["sign"]);
                $order_id = intval($_GPC["order_id"]);
                $ignore_bargain = 0 < $order_id ? true : false;
                $cart = cart_data_init($sid, $goods_id, $option_id, $sign, $ignore_bargain);
                imessage($cart, "", "ajax");
            } else {
                if ($ta == "goods") {
                    $goods = goods_filter($sid);
                    $result = array("goods" => $goods);
                    imessage(error(0, $result), "", "ajax");
                } else {
                    if ($ta == "detail") {
                        $sid = intval($_GPC["sid"]);
                        $id = intval($_GPC["id"]);
                        $goods = goods_fetch($id);
                        if (is_error($goods)) {
                            imessage(error(-1, "商品不存在或已删除"), "", "ajax");
                        }
                        $table_id = intval($_GPC["table_id"]);
                        $table = table_fetch($table_id);
                        if (empty($table)) {
                            imessage(error(-1000, "桌号不存在"), "", "ajax");
                        }
                        $bargain_goods = pdo_fetch("select a.discount_price,a.max_buy_limit,b.status as bargain_status from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_activity_bargain") . " as b on a.bargain_id = b.id where a.uniacid = :uniacid and a.sid = :sid and a.goods_id = :goods_id and a.status = 1 and b.status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":goods_id" => $id));
                        if (!empty($bargain_goods["bargain_status"])) {
                            $goods = array_merge($goods, $bargain_goods);
                        }
                        $cart = order_fetch_member_cart($sid);
                        $goods["old_price"] = $goods["ts_price"];
                        $goods["price"] = $goods["old_price"];
                        $goods = goods_format($goods);
                        if (!empty($cart["data"][$id])) {
                            foreach ($cart["data"][$id] as $key => $cart_option) {
                                $goods["options_data"][$key]["num"] = $cart_option["num"];
                                $goods["totalnum"] += $cart_option["num"];
                            }
                        }
                        $result = array("goodsDetail" => $goods, "cart" => cart_data_init($sid), "store" => $store, "table" => $table);
                        message(error(0, $result), "", "ajax");
                        return 1;
                    } else {
                        if ($ta == "create") {
                            $is_pindan = intval($_GPC["is_pindan"]);
                            if ($is_pindan == 1) {
                                mload()->model("pindan");
                                $pindan_id = intval($_GPC["pindan_id"]);
                                $cart = pindan_data_init($sid, $pindan_id, false, array("is_submit" => 1));
                            } else {
                                $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]), array("id", "pindan_id"));
                                if (0 < $cart["pindan_id"]) {
                                    imessage(error(-1000, array("message" => "有其他朋友与你一起点餐了, 请确认后提交", "pindan_id" => $cart["pindan_id"])), "", "ajax");
                                }
                                $cart = cart_data_init($sid);
                                $cart = $cart["message"]["cart"];
                            }
                            if (is_error($cart)) {
                                imessage($cart, "", "ajax");
                            }
                            if (empty($cart["data"])) {
                                imessage(error(1000, "购物车数据错误"), "", "ajax");
                            }
                            $pay_types = order_pay_types();
                            $condition = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
                            $coupon_id = intval($condition["coupon_id"]);
                            $redpacket_id = intval($condition["redpacket_id"]);
                            $message = error(0, "");
                            $activityed = order_count_activity($sid, $cart, $coupon_id, $redpacket_id, 0, 0, 3);
                            $serve_fee = 0;
                            if (0 < $store["serve_fee"]["type"] && 0 < $store["serve_fee"]["fee"]) {
                                $serve_fee = $store["serve_fee"]["fee"];
                                if ($store["serve_fee"]["type"] == 2) {
                                    $serve_fee = round($store["serve_fee"]["fee"] * $cart["price"] / 100, 2);
                                }
                            }
                            $table_id = intval($_GPC["table_id"]);
                            table_order_update($table_id, 0, 2);
                            $box_price_tangshi = floatval($store["data"]["box_price_tangshi"]);
                            $person_num = intval($condition["person_num"]);
                            $box_price = round($box_price_tangshi * $person_num, 2);
                            mload()->model("coupon");
                            mload()->model("redPacket");
                            $order = array("order_type" => 3, "price" => $cart["price"], "total_fee" => $cart["price"] + $serve_fee + $box_price, "serve_fee" => $serve_fee, "discount_fee" => $activityed["total"], "activityed" => $activityed, "note" => trim($condition["note"]), "box_price" => $box_price, "box_price_tangshi" => $box_price_tangshi, "person_num" => $person_num, "invoiceId" => intval($condition["invoiceId"]), "coupon" => coupon_available_check($sid, $coupon_id, $cart["price"]), "redpacket" => redpacket_available_check($redpacket_id, $cart["price"], explode("|", $store["cid"]), array("scene" => "waimai", "sid" => $sid)));
                            $order["final_fee"] = $order["total_fee"] - $activityed["total"];
                            $result = array("store" => $store, "cart" => $cart, "coupons" => coupon_available($sid, $cart["price"]), "redPackets" => redPacket_available($cart["price"], explode("|", $store["cid"]), array("scene" => "waimai", "sid" => $sid)), "order" => $order, "message" => $message, "islegal" => 1);
                            imessage(error(0, $result), "", "ajax");
                        } else {
                            if ($ta == "submit") {
                                if (!$store["is_in_business_hours"]) {
                                    imessage(error(-1, "商户休息中"), "", "ajax");
                                }
                                $condition = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
                                if (empty($condition)) {
                                    imessage(error(-1, "参数错误"), "", "ajax");
                                }
                                $is_pindan = intval($_GPC["is_pindan"]);
                                if ($is_pindan == 1) {
                                    mload()->model("pindan");
                                    $pindan_id = intval($_GPC["pindan_id"]);
                                    $cart = pindan_data_init($sid, $pindan_id, false, array("is_submit" => 1, "all_goods" => 1));
                                } else {
                                    $cart = cart_data_init($sid);
                                    $cart = $cart["message"]["cart"];
                                    $cart["original_data"] = $cart["original_data1"];
                                }
                                if (is_error($cart)) {
                                    imessage($cart, "", "ajax");
                                }
                                if (empty($cart["data"])) {
                                    imessage(error(1000, "购物车数据错误"), "", "ajax");
                                }
                                $coupon_id = intval($condition["coupon_id"]);
                                $redpacket_id = intval($condition["redpacket_id"]);
                                $activityed = order_count_activity($sid, $cart, $coupon_id, $redpacket_id, 0, 0, 3);
                                $serve_fee = 0;
                                if (0 < $store["serve_fee"]["type"] && 0 < $store["serve_fee"]["fee"]) {
                                    $serve_fee = $store["serve_fee"]["fee"];
                                    if ($store["serve_fee"]["type"] == 2) {
                                        $serve_fee = round($store["serve_fee"]["fee"] * $cart["price"] / 100, 2);
                                    }
                                }
                                $invoice_id = intval($condition["invoice_id"]);
                                if (0 < $invoice_id) {
                                    $invoice = member_invoice($invoice_id);
                                    $invoice = iserializer(array("title" => $invoice["title"], "recognition" => $invoice["recognition"]));
                                }
                                $table_id = intval($_GPC["table_id"]);
                                $box_price_tangshi = floatval($store["data"]["box_price_tangshi"]);
                                $person_num = intval($condition["person_num"]);
                                $box_price = round($box_price_tangshi * $person_num, 2);
                                $order = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "ordersn" => date("YmdHis") . random(6, true), "serial_sn" => store_order_serial_sn($sid), "code" => random(4, true), "order_type" => 3, "openid" => $_W["openid"], "mobile" => "", "username" => trim($condition["username"]), "person_num" => $person_num, "table_id" => $table_id, "sex" => "", "address" => "", "location_x" => "", "location_y" => "", "delivery_day" => "", "delivery_time" => "", "delivery_fee" => 0, "pack_fee" => 0, "pay_type" => "", "num" => $cart["num"], "price" => $cart["price"], "serve_fee" => $serve_fee, "box_price" => $box_price, "total_fee" => $cart["price"] + $serve_fee + $box_price, "discount_fee" => $activityed["total"], "store_discount_fee" => $activityed["store_discount_fee"], "plateform_discount_fee" => $activityed["plateform_discount_fee"], "final_fee" => $cart["price"] + $serve_fee + $box_price - $activityed["total"], "status" => 1, "is_comment" => 0, "invoice" => $invoice, "addtime" => TIMESTAMP, "data" => array("cart" => $cart["original_data"]), "note" => trim($condition["note"]));
                                if ($order["final_fee"] < 0) {
                                    $order["final_fee"] = 0;
                                }
                                if (0 < $pindan_id) {
                                    $order["pindan_id"] = $pindan_id;
                                    $cart["other"][] = $cart["mine"];
                                    $order["data"]["pindan"] = $cart["other"];
                                }
                                $order["data"] = iserializer($order["data"]);
                                pdo_insert("tiny_wmall_order", $order);
                                $order_id = pdo_insertid();
                                $order_id = intval($order_id);
                                if (empty($order_id)) {
                                    imessage(error(-1, "订单信息有误，请重新下单"), "", "ajax");
                                }
                                order_update_bill($order_id);
                                table_order_update($table_id, $order_id, 3);
                                order_insert_discount($order_id, $sid, $activityed["list"]);
                                order_insert_status_log($order_id, "place_order");
                                if (0 < $pindan_id) {
                                    order_update_goods_info($order_id, $sid, $cart);
                                } else {
                                    order_update_goods_info($order_id, $sid);
                                }
                                order_del_member_cart($sid, $pindan_id);
                                imessage(error(0, $order_id), "", "ajax");
                            } else {
                                if ($ta == "jiacai") {
                                    $order_id = intval($_GPC["order_id"]);
                                    $result = table_order_jiacai($sid, $order_id);
                                    if (!is_error($result)) {
                                        order_del_member_cart($sid);
                                    }
                                    imessage($result, "", "ajax");
                                } else {
                                    if ($ta == "note") {
                                        if ($store["invoice_status"] == 1) {
                                            $invoices = member_invoices();
                                        }
                                        $result = array("invoices" => $invoices, "store" => $store);
                                        imessage(error(0, $result), "", "ajax");
                                    } else {
                                        if ($ta == "pindan") {
                                            if ($store["pindan_status"] != 1) {
                                                imessage(error(-1, "本店未开启拼单功能"), "", "ajax");
                                            }
                                            $table_id = intval($_GPC["table_id"]);
                                            $table = table_fetch($table_id);
                                            if (empty($table)) {
                                                imessage(error(-1, "桌号不存在"), "", "ajax");
                                            }
                                            $pindan_id = intval($_GPC["pindan_id"]);
                                            $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]));
                                            $is_founder = 0;
                                            if ($cart["pindan_id"] == $cart["id"]) {
                                                $is_founder = 1;
                                                $pindan_id = $cart["id"];
                                            }
                                            mload()->model("pindan");
                                            $pindan = pindan_data_init($sid, $pindan_id);
                                            if (is_error($pindan)) {
                                                imessage(error(-1001, $pindan["message"]), "", "ajax");
                                            }
                                            $result = array("pindan" => $pindan, "store" => $store, "extra" => array("not_takepart" => 0, "is_founder" => $is_founder), "table" => $table);
                                            imessage(error(0, $result), "", "ajax");
                                        } else {
                                            if ($ta == "giveupPindan") {
                                                $cart_id = intval($_GPC["cart_id"]);
                                                $table_id = intval($_GPC["table_id"]);
                                                $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]), array("id", "pindan_id"));
                                                if ($cart["id"] == $cart["pindan_id"]) {
                                                    if ($cart_id == $cart["id"]) {
                                                        pdo_query("update " . tablename("tiny_wmall_order_cart") . " set pindan_status = 0, pindan_id = 0 where uniacid = :uniacid and pindan_id = :pindan_id", array(":uniacid" => $_W["uniacid"], ":pindan_id" => $cart["pindan_id"]));
                                                        pdo_query("update " . tablename("tiny_wmall_tables") . " set cart_id = 0 where uniacid = :uniacid and id = :id and cart_id = :cart_id", array(":uniacid" => $_W["uniacid"], ":id" => $table_id, ":cart_id" => $cart_id));
                                                        imessage(error(-1000, "取消共同点餐成功"), "", "ajax");
                                                    } else {
                                                        pdo_update("tiny_wmall_order_cart", array("pindan_status" => 0, "pindan_id" => 0), array("uniacid" => $_W["uniacid"], "id" => $cart_id));
                                                    }
                                                    $not_takepart = 0;
                                                } else {
                                                    $not_takepart = 1;
                                                    pdo_update("tiny_wmall_order_cart", array("pindan_status" => 0, "pindan_id" => 0), array("uniacid" => $_W["uniacid"], "id" => $cart["id"]));
                                                }
                                                mload()->model("pindan");
                                                $pindan = pindan_data_init($sid, $cart["pindan_id"]);
                                                $result = array("pindan" => $pindan, "extra" => array("not_takepart" => $not_takepart));
                                                imessage(error(0, $result), "", "ajax");
                                            } else {
                                                if ($ta == "continuePindan") {
                                                    $table_id = intval($_GPC["table_id"]);
                                                    $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "pindan_status" => 2), array("id", "pindan_id"));
                                                    if ($cart["id"] == $cart["pindan_id"]) {
                                                        pdo_query("update " . tablename("tiny_wmall_order_cart") . " set pindan_status = 1 where uniacid = :uniacid and pindan_id = :pindan_id", array(":uniacid" => $_W["uniacid"], ":pindan_id" => $cart["pindan_id"]));
                                                        pdo_query("update " . tablename("tiny_wmall_tables") . " set status = 1 where uniacid = :uniacid and id = :id and cart_id = :cart_id", array(":uniacid" => $_W["uniacid"], ":id" => $table_id, ":cart_id" => $cart["id"]));
                                                        imessage(error(0, ""), "", "ajax");
                                                    }
                                                    imessage(error(-1, "没有进行中的拼单，请重新发起"), "", "ajax");
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