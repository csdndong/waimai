<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$forceLoadAll = intval($_GPC["forceLoadAll"]);
if ($forceLoadAll != 1) {
    icheckauth();
}
$sid = intval($_GPC["sid"]);
store_business_hours_init($sid);
$store = store_fetch($sid);
if (empty($store)) {
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}
if (empty($store["notice"])) {
    $store["notice"] = "本店暂无公告";
}
if ($ta == "index") {
    activity_store_cron($sid);
}
$table_id = intval($_GPC["table_id"]);
if (0 < $table_id) {
    define("ORDER_TYPE", "tangshi");
} else {
    define("ORDER_TYPE", "takeout");
}
mload()->model("goods");
mload()->model("activity");
$price = store_order_condition($store);
$store["send_price"] = $price["send_price"];
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : $store["template"];
if ($ta == "index") {
    $footmark = pdo_get("tiny_wmall_member_footmark", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "sid" => $sid, "stat_day" => date("Ymd")), array("id"));
    if (empty($footmark)) {
        $insert = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "sid" => $sid, "addtime" => TIMESTAMP, "stat_day" => date("Ymd"));
        pdo_insert("tiny_wmall_member_footmark", $insert);
    }
    if ($_GPC["from"] == "search") {
        pdo_query("update " . tablename("tiny_wmall_store") . " set click = click + 1 where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $sid));
    }
    $store["activity"] = store_fetch_activity($sid);
    $store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
    mload()->model("coupon");
    $coupons = coupon_collect_member_available($sid);
    if (!empty($_GPC["order_id"])) {
        order_place_again($sid, $_GPC["order_id"]);
    }
    if (!empty($store["data"]["shopPage"])) {
        foreach ($store["data"]["shopPage"] as &$val) {
            $val["goodsLength"] = count($val["goods"]);
            $val["thumb"] = tomedia($val["thumb"]);
        }
    }
    $template = $store["data"]["wxapp"]["template"] ? $store["data"]["wxapp"]["template"] : 1;
    if (!check_plugin_perm("diypage") && in_array($template, array(4, 5))) {
        $template = 2;
    }
    if ($_W["ochannel"] == "wxapp") {
        $template_page = isset($store["data"]["wxapp"]["template_page"]["wxapp"]) ? $store["data"]["wxapp"]["template_page"]["wxapp"] : 1;
    } else {
        $template_page = $store["data"]["wxapp"]["template_page"]["vue"] ? $store["data"]["wxapp"]["template_page"]["vue"] : 0;
    }
    if (in_array($template, array(4, 5))) {
        $template_page = 1;
    }
    if ($forceLoadAll == 1) {
        $template_page = 0;
    }
    $result = array("store" => $store, "coupon" => $coupons, "cart" => cart_data_init($sid), "template" => $template, "template_page" => $template_page, "lazyload_goods" => $_config_mall["lazyload_goods"], "config_mall" => $_config_mall);
    if ($template_page == 1) {
        $categorys = array_values(store_fetchall_goods_category($sid, 1, false, "all", "available"));
        $result["category"] = $categorys;
        $cid = !empty($_GPC["cid"]) ? trim($_GPC["cid"]) : $categorys[0]["id"];
        $child_id = 0;
        $cindex = 0;
        if (!empty($categorys)) {
            foreach ($categorys as $index => $cate) {
                if ($cate["id"] == $cid) {
                    $cindex = $index;
                    if (!empty($cate["child"]) && 0 < count($cate["child"])) {
                        $child_id = $cate["child"][0]["id"];
                    }
                    break;
                }
            }
        }
        if (!empty($categorys[0]["child"])) {
            $child_id = $categorys[0]["child"][0]["id"];
        }
        $result["goods"] = goods_filter($sid, array("cid" => $cid, "child_id" => $child_id));
        $result["cid"] = $cid;
        $result["child_id"] = $child_id;
        $result["cindex"] = $cindex;
    } else {
        $all = goods_avaliable_fetchall($sid);
        $result["cate_has_goods"] = $all["cate_has_goods"];
        $result["tabActive"] = 0;
        $cid = trim($_GPC["cid"]);
        if (!empty($cid) && !empty($all["cate_has_goods"])) {
            foreach ($all["cate_has_goods"] as $key => $value) {
                if ($value["id"] == $cid) {
                    $result["tabActive"] = $key;
                }
            }
        }
    }
    if ($store["is_rest"]) {
        $result["recommend_stores"] = store_fetchall_by_condition("recommend", array("extra_type" => "all", "is_rest" => 0));
    }
    $_W["_share"] = array("title" => $store["title"], "desc" => $store["content"], "imgUrl" => tomedia($store["logo"]), "link" => ivurl("/pages/store/share", array("sid" => $sid), true));
    imessage(error(0, $result), "", "ajax");
    return 1;
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
            $bargain_goods = pdo_fetch("select a.discount_price,a.max_buy_limit,b.status as bargain_status from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_activity_bargain") . " as b on a.bargain_id = b.id where a.uniacid = :uniacid and a.sid = :sid and a.goods_id = :goods_id and a.status = 1 and b.status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":goods_id" => $id));
            if (!empty($bargain_goods["bargain_status"])) {
                $goods = array_merge($goods, $bargain_goods);
            }
            $cart = order_fetch_member_cart($sid);
            $goods["config_svip_status"] = svip_status_is_available();
            if (!$goods["config_svip_status"]) {
                $goods["svip_status"] = 0;
            }
            $goods["from"] = "goods";
            $goods = goods_format($goods);
            if (!empty($cart["data"][$id])) {
                foreach ($cart["data"][$id] as $key => $cart_option) {
                    $goods["options_data"][$key]["num"] = $cart_option["num"];
                    $goods["totalnum"] += $cart_option["num"];
                }
            }
            $_W["_share"] = array("title" => $goods["title"], "desc" => $goods["content"], "imgUrl" => $goods["thumb"], "link" => ivurl("/pages/store/goodsDetail", array("id" => $goods["id"], "sid" => $goods["sid"]), true));
            $result = array("goodsDetail" => $goods, "cart" => cart_data_init($sid), "store" => $store);
            imessage(error(0, $result), "", "ajax");
            return 1;
        } else {
            if ($ta == "truncate") {
                $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]));
                if ($cart["id"] == $cart["pindan_id"]) {
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
                    $cart = cart_data_init($sid, $goods_id, $option_id, $sign);
                    imessage($cart, "", "ajax");
                } else {
                    if ($ta == "shopPage") {
                        $shopPageKey = trim($_GPC["shopPageKey"]);
                        $goodsids = $store["data"]["shopPage"][$shopPageKey]["goods"];
                        $goods = goods_filter($sid, array("goodsids" => $goodsids, "psize" => 1000));
                        $store["data"]["shopPage"][$shopPageKey]["thumb"] = tomedia($store["data"]["shopPage"][$shopPageKey]["thumb"]);
                        $result = array("goods" => $goods, "store" => $store, "cart" => cart_data_init($sid));
                        imessage(error(0, $result), "", "ajax");
                    } else {
                        if ($ta == "pindan") {
                            if ($store["pindan_status"] != 1) {
                                imessage(error(-1, "本店未开启拼单功能"), "", "ajax");
                            }
                            mload()->model("pindan");
                            $pindan_id = intval($_GPC["pindan_id"]);
                            $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]));
                            $not_takepart = 0;
                            $is_founder = 0;
                            if (empty($pindan_id)) {
                                $update = array();
                                $is_founder = 1;
                                if (empty($cart)) {
                                    $cart = array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "addtime" => TIMESTAMP, "pindan_status" => 1);
                                    pdo_insert("tiny_wmall_order_cart", $cart);
                                    $cart["id"] = pdo_insertid();
                                    $cart["pindan_id"] = $cart["id"];
                                    $pindan_id = $cart["pindan_id"];
                                    $update["pindan_id"] = $pindan_id;
                                } else {
                                    $pindan_id = $cart["id"];
                                    if ($cart["pindan_id"] == 0) {
                                        $update = array("pindan_id" => $cart["id"], "pindan_status" => 1);
                                    } else {
                                        if ($cart["pindan_id"] != $cart["id"]) {
                                            $is_founder = 0;
                                            $pindan_id = $cart["pindan_id"];
                                        }
                                    }
                                }
                            } else {
                                if (empty($cart)) {
                                    $not_takepart = 1;
                                } else {
                                    if ($cart["pindan_id"] == $cart["id"]) {
                                        $is_founder = 1;
                                        $pindan_id = $cart["id"];
                                    } else {
                                        if (empty($cart["pindan_id"])) {
                                            $not_takepart = 1;
                                        } else {
                                            if (0 < $cart["pindan_id"] && $cart["pindan_id"] != $pindan_id) {
                                                imessage(error(-1000, "您正在参与好友拼单，请先取消已参与的拼单"), "", "ajax");
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($update) && !empty($cart)) {
                                pdo_update("tiny_wmall_order_cart", $update, array("uniacid" => $_W["uniacid"], "id" => $cart["id"]));
                            }
                            $pindan = pindan_data_init($sid, $pindan_id);
                            if (is_error($pindan)) {
                                if (0 < $pindan_id) {
                                    $pindan_order = pdo_fetch("select id,price,box_price,data from " . tablename("tiny_wmall_order") . " WHERE uniacid = :uniacid AND pindan_id = :pindan_id", array(":uniacid" => $_W["uniacid"], ":pindan_id" => $pindan_id));
                                    if (!empty($pindan_order)) {
                                        $pindan_order["data"] = iunserializer($pindan_order["data"]);
                                        $pindan = array();
                                        foreach ($pindan_order["data"]["pindan"] as $key => $val) {
                                            if ($val["uid"] == $_W["member"]["uid"]) {
                                                $pindan["mine"] = $val;
                                                unset($pindan_order["data"]["pindan"][$key]);
                                                break;
                                            }
                                        }
                                        $pindan["other"] = array_values($pindan_order["data"]["pindan"]);
                                        unset($pindan_order["data"]);
                                        $pindan["price"] = $pindan_order["price"];
                                        $pindan["box_price"] = $pindan_order["box_price"];
                                        $pindan["total_cart_price"] = $pindan["price"] + $pindan["box_price"];
                                        $pindan["pindan_id"] = $pindan_id;
                                        $pindan["pindan_status"] = 3;
                                        $order_id = $pindan_order["id"];
                                        unset($pindan_order);
                                    }
                                }
                                if (empty($order_id)) {
                                    imessage(error(-1001, $pindan["message"]), "", "ajax");
                                }
                            }
                            $_W["_share"] = array("title" => "来一起点外卖吧", "desc" => "我想点" . $store["title"] . "，快来一起吧", "imgUrl" => tomedia($store["logo"]), "link" => ivurl("/pages/store/pindan", array("sid" => $sid, "pindan_id" => $pindan_id), "true"));
                            $result = array("pindan" => $pindan, "store" => $store, "extra" => array("not_takepart" => $not_takepart, "is_founder" => $is_founder), "cartSendCondition" => $store["send_price"] - $pindan["total_cart_price"], "sharedata" => array("title" => "来一起点外卖吧", "desc" => "我想点" . $store["title"] . "，快来一起吧", "imageUrl" => tomedia($store["logo"]), "path" => "/pages/store/pindan?sid=" . $sid . "&pindan_id=" . $pindan_id));
                            imessage(error(0, $result), "", "ajax");
                            return 1;
                        } else {
                            if ($ta == "giveupPindan") {
                                $cart_id = intval($_GPC["cart_id"]);
                                $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]), array("id", "pindan_id"));
                                if ($cart["id"] == $cart["pindan_id"]) {
                                    if ($cart_id == $cart["id"]) {
                                        pdo_query("update " . tablename("tiny_wmall_order_cart") . " set pindan_status = 0, pindan_id = 0 where uniacid = :uniacid and pindan_id = :pindan_id", array(":uniacid" => $_W["uniacid"], ":pindan_id" => $cart["pindan_id"]));
                                        imessage(error(-1000, "取消拼单成功"), "", "ajax");
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
                                $result = array("pindan" => $pindan, "cartSendCondition" => $store["send_price"] - $pindan["total_cart_price"], "extra" => array("not_takepart" => $not_takepart));
                                imessage(error(0, $result), "", "ajax");
                            } else {
                                if ($ta == "continuePindan") {
                                    $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "pindan_status" => 2), array("id", "pindan_id"));
                                    if ($cart["id"] == $cart["pindan_id"]) {
                                        pdo_query("update " . tablename("tiny_wmall_order_cart") . " set pindan_status = 1 where uniacid = :uniacid and pindan_id = :pindan_id", array(":uniacid" => $_W["uniacid"], ":pindan_id" => $cart["pindan_id"]));
                                        imessage(error(0, ""), "", "ajax");
                                    }
                                    imessage(error(-1, "没有进行中的拼单，请重新发起"), "", "ajax");
                                } else {
                                    if ($ta == "takePartPindan") {
                                        $pindan_id = intval($_GPC["pindan_id"]);
                                        $cart = pdo_get("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]), array("id", "pindan_id"));
                                        if (empty($cart)) {
                                            $cart = array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "addtime" => TIMESTAMP, "pindan_status" => 1, "pindan_id" => $pindan_id);
                                            pdo_insert("tiny_wmall_order_cart", $cart);
                                        } else {
                                            if ($cart["pindan_id"] != $pindan_id) {
                                                if (0 < $cart["pindan_id"]) {
                                                    imessage(error(-1, "您正在参与拼单，请先取消已参与的拼单"), "", "ajax");
                                                }
                                                pdo_update("tiny_wmall_order_cart", array("pindan_status" => 1, "pindan_id" => $pindan_id), array("uniacid" => $_W["uniacid"], "id" => $cart["id"]));
                                            }
                                        }
                                        imessage(error(0, ""), "", "ajax");
                                    } else {
                                        if ($ta == "search") {
                                            $is_search = intval($_GPC["is_search"]);
                                            $table_id = intval($_GPC["table_id"]);
                                            $table = array();
                                            if (0 < $table_id) {
                                                if ($store["is_meal"] != 1) {
                                                    imessage(error(-1000, "店内点餐暂未开启"), "", "ajax");
                                                }
                                                mload()->model("table");
                                                $table = table_fetch($table_id);
                                                if (empty($table)) {
                                                    imessage(error(-1000, "桌号不存在"), "", "ajax");
                                                }
                                            }
                                            if (!$is_search) {
                                                $condition = " where uniacid = :uniacid and sid = :sid and status = :status and is_hot = :is_hot ";
                                                if (0 < $table_id) {
                                                    $condition .= " and (type = 2 or type = 3) ";
                                                } else {
                                                    $condition .= " and (type = 1 or type = 3) ";
                                                }
                                                $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":status" => 1, ":is_hot" => 1);
                                                $hotGoods = pdo_fetchall("select id, title from " . tablename("tiny_wmall_goods") . $condition, $params);
                                                $result = array("hotGoods" => $hotGoods, "store" => $store, "table" => $table);
                                                imessage(error(0, $result), "", "ajax");
                                            }
                                            $title = trim($_GPC["keyword"]);
                                            if (empty($title)) {
                                                imessage(error(-1, "请输入商品名"), "", "ajax");
                                            }
                                            $goods = goods_filter($sid);
                                            $result = array("store" => $store, "goods" => $goods, "table" => $table);
                                            if (!empty($goods)) {
                                                $result["cart"] = cart_data_init($sid);
                                            }
                                            imessage(error(0, $result), "", "ajax");
                                        } else {
                                            if ($ta == "updateCart") {
                                                $result = array("cart" => cart_data_init($sid));
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

?>
