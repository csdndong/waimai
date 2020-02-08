<?php

defined("IN_IA") or exit("Access Denied");

function pindan_data_init($sid, $pindan_id, $ignore_bargain = false, $extra = array())
{
    global $_W;
    $pindan_id = intval($pindan_id);
    if ($pindan_id <= 0) {
        return error(-1, "拼单不存在");
    }
    $groups = pdo_fetchall("select * from " . tablename("tiny_wmall_order_cart") . " where uniacid = :uniacid and sid = :sid and pindan_id = :pindan_id and pindan_status > 0", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":pindan_id" => $pindan_id), "id");
    if (empty($groups)) {
        return error(-1, "拼单不存在或已结束");
    }
    $goods_ids = array();
    $takepart_num = 0;
    foreach ($groups as &$da) {
        $takepart_num++;
        $da["data"] = iunserializer($da["data"]);
        foreach ($da["data"] as $key => $row) {
            $goods_ids[] = $key;
        }
    }
    if (empty($goods_ids)) {
        $pindan_data = array("mine" => array("member" => $_W["member"]), "price" => 0, "box_price" => 0, "total_cart_price" => 0, "pindan_id" => $pindan_id, "pindan_status" => $groups[$pindan_id]["pindan_status"]);
        return $pindan_data;
    }
    $goods_ids_str = implode(",", $goods_ids);
    $goods_info = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id IN (" . $goods_ids_str . ")", array(":uniacid" => $_W["uniacid"], ":sid" => $sid), "id");
    foreach ($goods_info as $key => &$value) {
        if (ORDER_TYPE == "tangshi") {
            $value["price"] = $value["ts_price"];
        }
        if (ORDER_TYPE == "takeout" && $value["type"] == 2 || ORDER_TYPE == "tangshi" && $value["type"] == 1) {
            unset($goods_info[$key]);
        }
    }
    $options = pdo_fetchall("select * from " . tablename("tiny_wmall_goods_options") . " where uniacid = :uniacid and sid = :sid and goods_id in (" . $goods_ids_str . ") ", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    foreach ($options as $option) {
        $goods_info[$option["goods_id"]]["options"][$option["id"]] = $option;
    }
    $bargain_goods_ids = array();
    if (!$ignore_bargain) {
        mload()->model("activity");
        activity_store_cron($sid);
        $bargains = pdo_getall("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => "1"), array(), "id");
        if (!empty($bargains)) {
            $bargain_ids = implode(",", array_keys($bargains));
            $bargain_goods = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_bargain_goods") . " where uniacid = :uniacid and sid = :sid and bargain_id in (" . $bargain_ids . ")", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
            $bargain_goods_group = array();
            if (!empty($bargain_goods)) {
                foreach ($bargain_goods as &$row) {
                    $bargain_goods_ids[$row["goods_id"]] = $row["bargain_id"];
                    $row["available_buy_limit"] = $row["max_buy_limit"];
                    $bargain_goods_group[$row["bargain_id"]][$row["goods_id"]] = $row;
                }
            }
            $where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and bargain_id in (" . $bargain_ids . ") group by bargain_id";
            $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":stat_day" => date("Ymd"), ":uid" => $_W["member"]["uid"]);
            $bargain_order = pdo_fetchall("select count(distinct(oid)) as num, bargain_id from " . tablename("tiny_wmall_order_stat") . $where, $params, "bargain_id");
            foreach ($bargains as &$row) {
                $row["available_goods_limit"] = $row["goods_limit"];
                $row["goods"] = $bargain_goods_group[$row["id"]];
                $row["avaliable_order_limit"] = $row["order_limit"];
                if (!empty($bargain_order)) {
                    $row["avaliable_order_limit"] = $row["order_limit"] - intval($bargain_order[$row["id"]]["num"]);
                }
                $row["hasgoods"] = array();
            }
        } else {
            $bargains = array();
        }
    }
    $pindan_bargain_use_limit = $groups[$pindan_id]["bargain_use_limit"];
    $cart_bargain = array();
    $bargain_has_goods = array();
    $pindan_data = array("box_price" => 0, "price" => 0, "original_price" => 0, "num" => 0);
    $pindan_goods = array();
    $member_svip_status = 0;
    mload()->model("goods");
    $config_svip_status = svip_status_is_available();
    foreach ($groups as &$cart) {
        unset($cart["original_data"]);
        $total_num = 0;
        $total_original_price = 0;
        $total_price = 0;
        $total_box_price = 0;
        $cart_goods = array();
        $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $cart["uid"]), array("realname", "nickname", "avatar", "svip_status"));
        $member["avatar"] = tomedia($member["avatar"]);
        $cart["member"] = $member;
        $_W["member"]["svip_status"] = $member["svip_status"];
        if ($config_svip_status && $_W["member"]["svip_status"] == 1) {
            $member_svip_status = 1;
        }
        foreach ($cart["data"] as $k => $v) {
            $k = intval($k);
            $goods = $goods_info[$k];
            if ($member_svip_status == 1 && $goods["svip_status"] == 1) {
                $goods["price"] = $goods["svip_price"];
            }
            $goods["config_svip_status"] = $config_svip_status;
            if (empty($goods) || $k == "88888") {
                continue;
            }
            if (!goods_is_available($goods)) {
                unset($cart["data"][$k]);
                continue;
            }
            $goods_box_price = $goods["box_price"];
            if (!$goods["is_options"]) {
                $discount_num = 0;
                foreach ($v as $key => $val) {
                    $goods["options_data"] = goods_build_options($goods);
                    $key = trim($key);
                    $option = $goods["options_data"][$key];
                    if (empty($option) || empty($option["total"])) {
                        continue;
                    }
                    $num = intval($val["num"]);
                    if ($option["total"] != -1 && $option["total"] <= $num) {
                        $num = $option["total"];
                    }
                    if ($num <= 0) {
                        continue;
                    }
                    if ($goods["total"] != -1) {
                        $goods["total"] -= $num;
                        $goods["total"] = max($goods["total"], 0);
                    }
                    $title = $goods_info[$k]["title"];
                    if (!empty($key)) {
                        $title = (string) $title . "(" . $option["name"] . ")";
                    }
                    $cart_item = array("cid" => $goods["cid"], "child_id" => $goods["child_id"], "goods_id" => $k, "thumb" => tomedia($goods["thumb"]), "title" => $title, "option_title" => $option["name"], "num" => $num, "price" => $goods["price"], "discount_price" => $goods["price"], "discount_num" => 0, "price_num" => $num, "total_price" => round($goods["price"] * $num, 2), "total_discount_price" => round($goods["price"] * $num, 2), "bargain_id" => 0);
                    if (in_array($k, array_keys($bargain_goods_ids))) {
                        $goods_bargain_id = $bargain_goods_ids[$k];
                        $bargain = $bargains[$goods_bargain_id];
                        $bargain_goods = $bargain["goods"][$k];
                        $val["discount_num"] = min($bargain_goods["max_buy_limit"], $num);
                        if (0 < $bargain["avaliable_order_limit"] && 0 < $bargain["available_goods_limit"] && 0 < $bargain_goods["available_buy_limit"]) {
                            $i = 0;
                            while ($i < $val["discount_num"]) {
                                if ($bargain_goods["poi_user_type"] == "new" && empty($_W["member"]["is_store_newmember"])) {
                                    break;
                                }
                                if (($bargain_goods["discount_available_total"] == -1 || 0 < $bargain_goods["discount_available_total"]) && 0 < $bargain_goods["available_buy_limit"]) {
                                    $cart_item["discount_price"] = $bargain_goods["discount_price"];
                                    $cart_item["discount_num"]++;
                                    $cart_item["bargain_id"] = $bargain["id"];
                                    $cart_bargain[] = $bargain["use_limit"];
                                    if (0 < $cart_item["price_num"]) {
                                        $cart_item["price_num"]--;
                                    }
                                    if (0 < $bargain_goods["discount_available_total"]) {
                                        $bargain_goods["discount_available_total"]--;
                                        $bargains[$goods_bargain_id]["goods"][$k]["discount_available_total"]--;
                                    }
                                    $bargain_goods["available_buy_limit"]--;
                                    $bargains[$goods_bargain_id]["goods"][$k]["available_buy_limit"]--;
                                    $discount_num++;
                                    $bargain_has_goods[] = $k;
                                    $i++;
                                } else {
                                    break;
                                }
                            }
                            $cart_item["total_discount_price"] = $cart_item["discount_num"] * $bargain_goods["discount_price"] + $cart_item["price_num"] * $goods["price"];
                            $cart_item["total_discount_price"] = round($cart_item["total_discount_price"], 2);
                        }
                    }
                    $total_num += $num;
                    $total_price += $cart_item["total_discount_price"];
                    $total_original_price += $cart_item["total_price"];
                    $total_box_price += $goods_box_price * $num;
                    $cart_goods[$k][$key] = $cart_item;
                    if ($extra["all_goods"] == 1) {
                        if (!empty($pindan_goods[$k][$key])) {
                            $pindan_goods[$k][$key]["num"] += $cart_item["num"];
                            $pindan_goods[$k][$key]["total_price"] += $cart_item["total_price"];
                            $pindan_goods[$k][$key]["total_discount_price"] += $cart_item["total_discount_price"];
                            $pindan_goods[$k][$key]["discount_num"] += $cart_item["discount_num"];
                            $pindan_goods[$k][$key]["price_num"] += $cart_item["price_num"];
                        } else {
                            $pindan_goods[$k][$key] = $cart_item;
                        }
                    }
                }
                if (0 < $discount_num) {
                    $bargain["available_goods_limit"]--;
                    $bargains[$goods_bargain_id]["available_goods_limit"]--;
                    $bargains[$goods_bargain_id]["goods"][$k]["available_buy_limit"] -= $discount_num;
                }
                $totalnum = get_cart_goodsnum($k, -1, "num", $cart_goods);
                if ($goods_info[$k]["total"] != -1) {
                    $goods_info[$k]["total"] -= $totalnum;
                    $goods_info[$k]["total"] = max($goods_info[$k]["total"], 0);
                }
            } else {
                foreach ($v as $key => $val) {
                    $goods["options_data"] = goods_build_options($goods);
                    $option_id = tranferOptionid($key);
                    $key = trim($key);
                    $option = $goods["options_data"][$key];
                    if (empty($option) || empty($option["total"])) {
                        continue;
                    }
                    $num = intval($val["num"]);
                    if ($option["total"] != -1 && $option["total"] <= $num) {
                        $num = $option["total"];
                    }
                    if ($num <= 0) {
                        continue;
                    }
                    if ($goods["options"][$option_id]["total"] != -1) {
                        $goods["options"][$option_id]["total"] -= $num;
                        $goods["options"][$option_id]["total"] = max($goods["options"][$option_id]["total"], 0);
                    }
                    $title = $goods_info[$k]["title"];
                    if (!empty($key)) {
                        $title = (string) $title . "(" . $option["name"] . ")";
                    }
                    $cart_goods[$k][$key] = array("cid" => $goods_info[$k]["cid"], "goods_id" => $k, "thumb" => tomedia($goods_info[$k]["thumb"]), "title" => $title, "option_title" => $option["name"], "num" => $num, "price" => $option["price"], "discount_price" => $option["price"], "discount_num" => 0, "price_num" => $num, "total_price" => round($option["price"] * $num, 2), "total_discount_price" => round($option["price"] * $num, 2), "bargain_id" => 0);
                    $total_num += $num;
                    $total_price += $option["price"] * $num;
                    $total_original_price += $option["price"] * $num;
                    $total_box_price += $goods_box_price * $num;
                    if ($goods_info[$k]["options"][$option_id]["total"] != -1) {
                        $goods_info[$k]["options"][$option_id]["total"] -= $num;
                        $goods_info[$k]["options"][$option_id]["total"] = max($goods_info[$k]["options"][$option_id]["total"], 0);
                    }
                    if ($extra["all_goods"] == 1) {
                        if (!empty($pindan_goods[$k][$key])) {
                            $pindan_goods[$k][$key]["num"] += $cart_goods[$k][$key]["num"];
                            $pindan_goods[$k][$key]["total_price"] += $cart_goods[$k][$key]["total_price"];
                            $pindan_goods[$k][$key]["total_discount_price"] += $cart_goods[$k][$key]["total_discount_price"];
                            $pindan_goods[$k][$key]["price_num"] += $cart_goods[$k][$key]["price_num"];
                        } else {
                            $pindan_goods[$k][$key] = $cart_goods[$k][$key];
                        }
                    }
                }
            }
        }
        $cart["data"] = $cart_goods;
        $cart["num"] = $total_num;
        $cart["price"] = round($total_price, 2);
        $cart["box_price"] = round($total_box_price, 2);
        if (!empty($cart_bargain)) {
            $cart_bargain = array_unique($cart_bargain);
            if (in_array(1, $cart_bargain)) {
                $cart["bargain_use_limit"] = 1;
            }
            if (in_array(2, $cart_bargain)) {
                $cart["bargain_use_limit"] = 2;
            }
        }
        if ($pindan_bargain_use_limit != 2) {
            $pindan_bargain_use_limit = $cart["bargain_use_limit"];
        }
        if ($cart["uid"] == $_W["member"]["uid"]) {
            $pindan_data["mine"] = $cart;
        } else {
            $pindan_data["other"][] = $cart;
        }
        if ($extra["is_submit"]) {
            $pindan_data["data"][] = $cart_goods;
        }
        $pindan_data["price"] += $total_price;
        $pindan_data["box_price"] += $total_box_price;
        $pindan_data["original_price"] += $total_original_price;
        $pindan_data["num"] += $total_num;
        if (0 < $cart["is_buysvip"]) {
            pdo_update("tiny_wmall_order_cart", array("is_buysvip" => 0), array("uniacid" => $_W["uniacid"], "id" => $cart["id"]));
        }
    }
    if ($extra["is_submit"]) {
        if (empty($pindan_data["mine"])) {
            return error(-1, "拼单发起人不存在");
        }
        if ($pindan_data["mine"]["id"] != $pindan_id) {
            return error(-1, "你不是拼单发起人，不能提交订单");
        }
        $cart_goods_original = array();
        foreach ($pindan_goods as $key => $row) {
            $cart_goods_original[$key] = array("title" => $goods_info[$key]["title"], "goods_id" => $key, "options" => $row);
        }
        $pindan_data["original_data"] = $cart_goods_original;
        $pindan_data["pindan_data"] = $pindan_goods;
    }
    pdo_update("tiny_wmall_order_cart", array("bargain_use_limit" => $pindan_bargain_use_limit), array("uniacid" => $_W["uniacid"], "id" => $pindan_id));
    if ($extra["is_submit"]) {
        pdo_query("update " . tablename("tiny_wmall_order_cart") . " set pindan_status = 2 where uniacid = :uniacid and pindan_id = :pindan_id", array(":uniacid" => $_W["uniacid"], ":pindan_id" => $pindan_id));
    }
    $pindan_data["takepart_num"] = $takepart_num;
    $pindan_data["price"] = round($pindan_data["price"], 2);
    $pindan_data["box_price"] = round($pindan_data["box_price"], 2);
    $pindan_data["original_price"] = round($pindan_data["original_price"], 2);
    $pindan_data["cart_price"] = $pindan_data["price"] + $pindan_data["box_price"];
    $pindan_data["total_cart_price"] = $pindan_data["cart_price"];
    $pindan_data["pindan_id"] = $pindan_id;
    $pindan_data["bargain_use_limit"] = $pindan_bargain_use_limit;
    $pindan_data["pindan_status"] = $groups[$pindan_id]["pindan_status"];
    unset($cart);
    return $pindan_data;
}
function order_fetch_pindan_data($id)
{
    global $_W;
    $condition = "WHERE a.uniacid = :uniacid AND a.id = :id";
    $params = array(":uniacid" => $_W["uniacid"], ":id" => $id);
    $order = pdo_fetch("select a.id, a.sid, a.order_type, a.price, a.serve_fee, a.delivery_fee, a.pack_fee, a.discount_fee, a.extra_fee, a.final_fee, a.data, b.logo as store_logo, b.title as store_title from " . tablename("tiny_wmall_order") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition, $params);
    $order["data"] = iunserializer($order["data"]);
    if (empty($order["data"]["pindan"])) {
        return error(-1, "该订单不是拼单订单");
    }
    $order["store_logo"] = tomedia($order["store_logo"]);
    $pindan_num = count($order["data"]["pindan"]);
    $avg_delivery_fee = $avg_pack_fee = $avg_extra_fee = $avg_discount_fee = $avg_serve_fee = 0;
    if ($order["order_type"] <= 2) {
        $avg_delivery_fee = round($order["delivery_fee"] / $pindan_num, 2);
        $avg_pack_fee = round($order["pack_fee"] / $pindan_num, 2);
        $avg_extra_fee = round($order["extra_fee"] / $pindan_num, 2);
    } else {
        $avg_serve_fee = round($order["serve_fee"] / $pindan_num, 2);
    }
    $avg_discount_fee = round($order["discount_fee"] / $pindan_num, 2);
    $avg_total = $avg_delivery_fee + $avg_pack_fee + $avg_extra_fee - $avg_discount_fee + $avg_serve_fee;
    foreach ($order["data"]["pindan"] as &$cart) {
        $cart["avg_discount_fee"] = $avg_discount_fee;
        if ($order["order_type"] <= 2) {
            $cart["pindan_fee"] = $cart["price"] + $cart["box_price"] + $avg_total;
            $cart["avg_delivery_fee"] = $avg_delivery_fee;
            $cart["avg_pack_fee"] = $avg_pack_fee;
            $cart["avg_extra_fee"] = $avg_extra_fee;
        } else {
            $cart["pindan_fee"] = $cart["price"] + $avg_total;
            $cart["avg_serve_fee"] = $avg_serve_fee;
        }
    }
    return $order;
}

?>