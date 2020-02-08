<?php

defined("IN_IA") or exit("Access Denied");
function is_open_order($order)
{
    if (!is_array($order) || empty($order["order_plateform"])) {
        $id = is_array($order) ? $order["id"] : $order;
        $order = pdo_get("tiny_wmall_order", array("id" => $id), array("order_plateform"));
    }
    return $order["order_plateform"] != "we7_wmall";
}
function order_fetch($id, $oauth = false)
{
    global $_W;
    $id = intval($id);
    $condition = " where uniacid = :uniacid and id = :id";
    $params = array(":uniacid" => $_W["uniacid"], ":id" => $id);
    if ($oauth) {
        $condition .= " and uid = :uid";
        $params[":uid"] = $_W["member"]["uid"];
    }
    $order = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_order") . $condition, $params);
    if (empty($order)) {
        return false;
    }
    if (!empty($order["number"])) {
        $order["address"] = (string) $order["address"] . "-" . $order["number"];
    }
    $order["addtime_cn"] = date("Y-m-d H:i", $order["addtime"]);
    if ($order["status"] == 3 && 0 < $_W["deliveryer"]["id"]) {
        $order["plateform_deliveryer_fee"] = order_calculate_deliveryer_fee($order, $_W["deliveryer"]);
    }
    $order["invoice"] = iunserializer($order["invoice"]);
    if (!empty($order["invoice"]) && !is_array($order["invoice"])) {
        $order["invoice"] = array("title" => $order["invoice"]["title"], "recognition" => $order["invoice"]["recognition"]);
    }
    $order["data"] = iunserializer($order["data"]);
    if (defined("IN_DELIVERYAPP")) {
        $order["data"] = "";
        $order["invoice"] = $order["invoice"]["title"];
    }
    $order["delivery_title"] = $order["delivery_type"] == 2 ? $_W["we7_wmall"]["config"]["mall"]["delivery_title"] : "";
    $order_status = order_status();
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order["order_type_cn"] = $order_types[$order["order_type"]]["text"];
    $order["status_cn"] = $order_status[$order["status"]]["text"];
    if (!empty($order["plateform_serve"])) {
        $order["plateform_serve"] = iunserializer($order["plateform_serve"]);
    }
    if (!empty($order["agent_serve"])) {
        $order["agent_serve"] = iunserializer($order["agent_serve"]);
    }
    if (empty($order["is_pay"])) {
        $order["pay_type_cn"] = "未支付";
    } else {
        $order["pay_type_cn"] = !empty($pay_types[$order["pay_type"]]["text"]) ? $pay_types[$order["pay_type"]]["text"] : "鍏朵粬鏀粯鏂瑰紡";
    }
    if (empty($order["delivery_time"])) {
        $order["delivery_time"] = "尽快送出";
    }
    if ($order["order_type"] == 3) {
		//扫码点餐
        $table = pdo_get("tiny_wmall_tables", array("uniacid" => $_W["uniacid"], "id" => $order["table_id"]));
        $order["table"] = $table;
    } else {
        if ($order["order_type"] == 4) {
		//预定
            $reserve_type = order_reserve_type();
            $order["reserve_type_cn"] = $reserve_type[$order["reserve_type"]]["text"];
            $category = pdo_get("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "id" => $order["table_cid"]));
            $order["table_category"] = $category;
            if (0 < $order["table_id"]) {
                $table = pdo_get("tiny_wmall_tables", array("uniacid" => $_W["uniacid"], "id" => $order["table_id"]), array("title"));
                $order["table"] = $table;
            }
        }
    }
    $order["pay_type_class"] = "";
    if ($order["is_pay"] == 1) {
        $order["pay_type_class"] = "have-pay";
        if ($order["pay_type"] == "delivery") {
            $order["pay_type_class"] = "delivery-pay";
        }
    }
    $order["mobile_protect"] = $order["mobile"];
    if ($order["data"]["yinsihao_status"] == 1) {
        $order["mobile_protect"] = substr_replace($order["mobile"], "****", 3, 4);
    }
    return $order;
}
function order_fetch_goods($oid, $print_lable = "", $goods_type = "normal", $extra = array())
{
    global $_W;
    $oid = intval($oid);
    $condition = "WHERE a.uniacid = :uniacid AND a.oid = :oid";
    $params = array(":uniacid" => $_W["uniacid"], ":oid" => $oid);
    if ($goods_type == "jiacai") {
        $condition .= " AND a.goods_type = :goods_type and a.id in (" . $extra["jiacai_ids"] . ")";
        $params["goods_type"] = "jiacai";
    }
    if (!empty($print_lable)) {
        $condition .= " AND a.print_label in (" . $print_lable . ")";
    }
    $data = pdo_fetchall("select a.*,b.thumb, b.box_price from " . tablename("tiny_wmall_order_stat") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id " . $condition, $params);
    foreach ($data as &$item) {
        $item["thumb"] = tomedia($item["thumb"]);
        $item["activity"] = 0;
        if ($item["goods_type"] == "jiacai") {
            $item["goods_title"] .= "(鍔犺彍)";
        } else {
            if ($item["goods_type"] == "huangou") {
                if ($item["goods_discount_num"] < $item["goods_num"]) {
                    $item["goods_title"] .= "(" . $item["goods_discount_num"] . "浠芥崲璐?";
                } else {
                    $item["goods_title"] .= "(鎹㈣喘)";
                }
            }
        }
        $item["can_refund_num"] = $item["goods_num"];
        $item["can_refund_discount_num"] = $item["goods_discount_num"];
        if (!empty($item["data"])) {
            $item["data"] = iunserializer($item["data"]);
            $item["can_refund_num"] = $item["goods_num"] - intval($item["data"]["refund_total_num"]);
            $item["can_refund_discount_num"] = $item["goods_discount_num"] - intval($item["data"]["refund_total_discount_num"]);
        }
    }
    return $data;
}
function order_fetch_discount($id, $type = "")
{
    global $_W;
    if (empty($type)) {
        $data = pdo_getall("tiny_wmall_order_discount", array("uniacid" => $_W["uniacid"], "oid" => $id));
    } else {
        $data = pdo_get("tiny_wmall_order_discount", array("uniacid" => $_W["uniacid"], "oid" => $id, "type" => $type));
    }
    return $data;
}
function order_place_again($sid, $order_id)
{
    global $_W;
    $order = order_fetch($order_id);
    if (empty($order)) {
        return false;
    }
    $order["data"] = iunserializer($order["data"]);
    $isexist = pdo_fetchcolumn("SELECT id FROM " . tablename("tiny_wmall_order_cart") . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(":aid" => $_W["uniacid"], ":sid" => $sid, ":uid" => $_W["member"]["uid"]));
    $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "groupid" => $_W["member"]["groupid"], "num" => $order["num"], "price" => $order["price"], "box_price" => $order["box_price"], "original_data" => $order["data"]["cart"] ? $order["data"]["cart"] : $order["data"], "addtime" => TIMESTAMP);
    $cart_data = array();
    if (!empty($data["original_data"])) {
        foreach ($data["original_data"] as $key => $row) {
            if ($key == 88888) {
                continue;
            }
            $cart_data[$key] = $row["options"];
        }
        $data["data"] = iserializer($cart_data);
    }
    $original_data = $data["original_data"];
    $data["original_data"] = iserializer($original_data);
    if (empty($isexist)) {
        pdo_insert("tiny_wmall_order_cart", $data);
    } else {
        pdo_update("tiny_wmall_order_cart", $data, array("uniacid" => $_W["uniacid"], "id" => $isexist, "uid" => $_W["member"]["uid"]));
    }
    $data["original_data"] = $original_data;
    $data["data"] = $cart_data;
    return $data;
}
//order_insert_discount
function order_insert_discount($id, $sid, $discount_data)
{
    global $_W;
    if (empty($discount_data)) {
        return false;
    }
    if (!empty($discount_data["token"])) {
        pdo_update("tiny_wmall_activity_coupon_record", array("status" => 2, "usetime" => TIMESTAMP, "order_id" => $id), array("uniacid" => $_W["uniacid"], "id" => $discount_data["token"]["recordid"]));
    }
    if (!empty($discount_data["redPacket"])) {
        pdo_update("tiny_wmall_activity_redpacket_record", array("status" => 2, "usetime" => TIMESTAMP, "order_id" => $id), array("uniacid" => $_W["uniacid"], "id" => $discount_data["redPacket"]["redPacket_id"]));
    }
    foreach ($discount_data as $data) {
        $insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "oid" => $id, "type" => $data["type"], "name" => $data["name"], "icon" => $data["icon"], "note" => $data["text"], "fee" => $data["value"], "store_discount_fee" => floatval($data["store_discount_fee"]), "agent_discount_fee" => floatval($data["agent_discount_fee"]), "plateform_discount_fee" => floatval($data["plateform_discount_fee"]));
        pdo_insert("tiny_wmall_order_discount", $insert);
    }
    return true;
}
function get_cart_goodsnum($goods_id, $option_key = 0, $type = "num", $cart = array())
{
    $cart_goods_item = $cart[$goods_id];
    if (!$cart_goods_item) {
        return 0;
    }
    if ($option_key != -1) {
        $option = $cart_goods_item[$option_key];
        if (!$option) {
            return 0;
        }
        return $option[$type];
    }
    $num = 0;
    foreach ($cart_goods_item as $option) {
        if ($option[$type]) {
            $num += $option[$type];
        }
    }
    return $num;
}
function cart_data_init($sid, $goods_id = 0, $option_key = 0, $sign = "", $ignore_bargain = false)
{
    global $_W;
    global $_GPC;
    $option_key = trim($option_key);
    if (empty($option_key)) {
        $option_key = 0;
    }
    mload()->model("goods");
    $cart = order_fetch_member_cart($sid, false);
    $svip_buy_show = 1;
    $goods_ids = array();
    if (!empty($cart)) {
        $goods_ids = array_keys($cart["data"]);
        $svip_buy_show = 2;
    }
    if (0 < $goods_id) {
        $svip_buy_show = 3;
    }
    $goods_ids[] = $goods_id;
    $goods_ids_str = implode(",", $goods_ids);
    $buy_huangou_goods = intval($_GPC["buy_huangou_goods"]);
    if ($buy_huangou_goods == 1) {
        $goods_info = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id IN (" . $goods_ids_str . ")", array(":uniacid" => $_W["uniacid"], ":sid" => $sid), "id");
    } else {
        $goods_info = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND sid = :sid and huangou_type = :huangou_type AND id IN (" . $goods_ids_str . ")", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":huangou_type" => 1), "id");
    }
    if (!empty($goods_info)) {
        $goods_categorys = pdo_fetchall("select id, is_showtime, start_time, end_time, week, status from " . tablename("tiny_wmall_goods_category") . "where uniacid = :uniacid and sid = :sid", array(":uniacid" => $_W["uniacid"], ":sid" => $sid), "id");
        $config_svip_status = svip_status_is_available();
        $member_svip_status = 0;
        $buysvip = 0;
        if ($config_svip_status) {
            $buysvip = intval($_GPC["is_buysvip"]);
            if ($_W["member"]["svip_status"] == 1 || $buysvip == 1) {
                $member_svip_status = 1;
            }
        }
        foreach ($goods_info as $key => &$value) {
            if ($member_svip_status == 1 && $value["svip_status"] == 1) {
                $value["origin_price"] = $value["price"];
                $value["price"] = $value["svip_price"];
            }
            if ($config_svip_status) {
                $value["config_svip_status"] = $config_svip_status;
            }
            if (ORDER_TYPE == "tangshi") {
                $value["price"] = $value["ts_price"];
            }
            if (ORDER_TYPE == "takeout" && $value["type"] == 2 || ORDER_TYPE == "tangshi" && $value["type"] == 1) {
                unset($goods_info[$key]);
            }
            $goods_category = $goods_categorys[$value["cid"]];
            $value["c_status"] = $goods_category["status"];
            $value["c_is_showtime"] = $goods_category["is_showtime"];
            $value["c_start_time"] = $goods_category["start_time"];
            $value["c_end_time"] = $goods_category["end_time"];
            $value["c_week"] = $goods_category["week"];
            if (empty($value["is_showtime"]) && !empty($value["c_is_showtime"])) {
                $value["is_showtime"] = $value["c_is_showtime"];
                $value["start_time1"] = $value["c_start_time"];
                $value["end_time1"] = $value["c_end_time"];
                $value["week"] = $value["c_week"];
            }
        }
    }
    $options = pdo_fetchall("select * from " . tablename("tiny_wmall_goods_options") . " where uniacid = :uniacid and sid = :sid and goods_id in (" . $goods_ids_str . ") ", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    if (!empty($options)) {
        foreach ($options as $option) {
            $goods_info[$option["goods_id"]]["options"][$option["id"]] = $option;
        }
    }
    $bargain_goods_ids = array();
    if (!$ignore_bargain) {
        mload()->model("activity");
        activity_store_cron($sid);
        $bargains = pdo_getall("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => "1"), array(), "id");
        if ($buy_huangou_goods == 1) {
            $bargains_huangou = pdo_get("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "sid" => $sid, "type" => "huangou"), array());
            if (!empty($bargains_huangou)) {
                $bargains[$bargains_huangou["id"]] = $bargains_huangou;
            }
        }
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
            $where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and status < :status and bargain_id in (" . $bargain_ids . ") group by bargain_id";
            $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":stat_day" => date("Ymd"), ":uid" => $_W["member"]["uid"], ":status" => 6);
            $bargain_order = pdo_fetchall("select count(distinct(oid)) as num, bargain_id from " . tablename("tiny_wmall_order_stat") . $where, $params, "bargain_id");
            foreach ($bargains as &$row) {
                $row["available_goods_limit"] = $row["goods_limit"];
                $row["goods"] = $bargain_goods_group[$row["id"]];
                $row["avaliable_order_limit"] = $row["order_limit"];
                if (!empty($bargain_order) && $row["type"] != "huangou") {
                    $row["avaliable_order_limit"] = $row["order_limit"] - intval($bargain_order[$row["id"]]["num"]);
                }
                $row["hasgoods"] = array();
            }
        } else {
            $bargains = array();
        }
    }
    $total_num = 0;
    $total_original_price = 0;
    $total_price = 0;
    $total_box_price = 0;
    $cart_bargain = array();
    $bargain_has_goods = array();
    $total_huangou_price = 0;
    if (!empty($cart)) {
        foreach ($cart["data"] as $k => $v) {
            $k = intval($k);
            $goods = $goods_info[$k];
            if (empty($goods) || $k == "88888") {
                continue;
            }
            if (!goods_is_available($goods)) {
                unset($cart["data"][$k]);
                unset($cart["original_data"][$k]);
                continue;
            }
            if ($goods["is_options"] == 1 && empty($goods["options"])) {
                $goods["is_options"] = 0;
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
                    if ($num < $goods["unitnum"]) {
                        continue;
                    }
                    if (0 < $option["total"] && $option["total"] < $goods["unitnum"]) {
                        continue;
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
                    $cart_item = array("cid" => $goods_info[$k]["cid"], "child_id" => $goods_info[$k]["child_id"], "goods_id" => $k, "thumb" => tomedia($goods_info[$k]["thumb"]), "title" => $title, "option_title" => $option["name"], "num" => $num, "price" => $goods_info[$k]["price"], "discount_price" => $goods_info[$k]["price"], "discount_num" => 0, "price_num" => $num, "total_price" => round($goods_info[$k]["price"] * $num, 2), "total_discount_price" => round($goods_info[$k]["price"] * $num, 2), "bargain_id" => 0, "buy_svip_status" => intval($option["svip_status"]) && $buysvip, "is_svip_price" => $member_svip_status, "total_price_show" => 0, "total_discount_price_show" => 0, "total_huangou_price" => 0, "is_show" => 1);
                    $cart_item["total_price_show"] = $cart_item["total_price"];
                    $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"];
                    if ($svip_buy_show != 2 && $option["svip_status"] == 1 && empty($buysvip)) {
                        $svip_buy_show = 0;
                    }
                    if (in_array($k, array_keys($bargain_goods_ids))) {
                        $goods_bargain_id = $bargain_goods_ids[$k];
                        $bargain = $bargains[$goods_bargain_id];
                        $bargain_goods = $bargain["goods"][$k];
                        $option_discount_num_cart = $val["discount_num"];
                        $val["discount_num"] = min($bargain_goods["max_buy_limit"], $num);
                        if ($bargain["type"] == "huangou") {
                            $val["discount_num"] = min($bargain_goods["max_buy_limit"], $option_discount_num_cart);
                        }
                        if ($bargain["type"] == "bargain" && 0 < $bargain["avaliable_order_limit"] && 0 < $bargain["available_goods_limit"] && 0 < $bargain_goods["available_buy_limit"] || $bargain["type"] == "huangou" && 0 < $val["discount_num"] && 0 < $bargain["available_goods_limit"] && 0 < $bargain_goods["available_buy_limit"]) {
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
                                    $cart_item["bargain_type"] = $bargain["type"];
                                    $i++;
                                } else {
                                    break;
                                }
                            }
                            $cart_item["total_discount_price"] = $cart_item["discount_num"] * $bargain_goods["discount_price"] + $cart_item["price_num"] * $goods_info[$k]["price"];
                            $cart_item["total_discount_price"] = round($cart_item["total_discount_price"], 2);
                            $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"];
                            if ($bargain["type"] == "huangou") {
                                $cart_item["total_huangou_price"] = round($cart_item["discount_num"] * $bargain_goods["discount_price"], 2);
                                $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"] - $cart_item["total_huangou_price"];
                                $cart_item["total_price_show"] = round($goods_info[$k]["price"] * ($num - $cart_item["discount_num"]), 2);
                                if ($cart_item["discount_num"] == $cart_item["num"]) {
                                    $cart_item["is_show"] = 0;
                                }
                            }
                        }
                    } else {
                        if ($goods["svip_status"] == 1 && $member_svip_status == 1) {
                            $cart_item["total_price"] = round($goods["origin_price"] * $num, 2);
                        }
                    }
                    $total_num += $num;
                    $total_price += $cart_item["total_discount_price"];
                    $total_original_price += $cart_item["total_price"];
                    $total_box_price += $goods_box_price * $num;
                    $cart_goods[$k][$key] = $cart_item;
                    $total_huangou_price += $cart_item["total_huangou_price"];
                }
                if (0 < $discount_num) {
                    $bargain["available_goods_limit"]--;
                    $bargains[$goods_bargain_id]["available_goods_limit"]--;
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
                    if ($num < $goods["unitnum"]) {
                        continue;
                    }
                    if (0 < $option["total"] && $option["total"] < $goods["unitnum"]) {
                        continue;
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
                    $cart_goods[$k][$key] = array("cid" => $goods_info[$k]["cid"], "child_id" => $goods_info[$k]["child_id"], "goods_id" => $k, "thumb" => tomedia($goods_info[$k]["thumb"]), "title" => $title, "option_title" => $option["name"], "num" => $num, "price" => $option["price"], "discount_price" => $option["price"], "discount_num" => 0, "price_num" => $num, "total_price" => round($option["price"] * $num, 2), "total_discount_price" => round($option["price"] * $num, 2), "bargain_id" => 0, "buy_svip_status" => intval($option["svip_status"]) && $buysvip, "is_svip_price" => $member_svip_status, "is_show" => 1);
                    if ($svip_buy_show != 2 && 0 < $option["svip_price"] && $option["svip_price"] < $option["price"] && empty($buysvip)) {
                        $svip_buy_show = 0;
                    }
                    if ($option["svip_status"] == 1) {
                        $cart_goods[$k][$key]["total_price"] = round($option["origin_price"] * $num, 2);
                    }
                    $cart_goods[$k][$key]["total_price_show"] = $cart_goods[$k][$key]["total_price"];
                    $cart_goods[$k][$key]["total_discount_price_show"] = $cart_goods[$k][$key]["total_discount_price"];
                    $total_num += $num;
                    $total_price += $option["price"] * $num;
                    $total_original_price += $cart_goods[$k][$key]["total_price"];
                    $total_box_price += $goods_box_price * $num;
                    if ($goods_info[$k]["options"][$option_id]["total"] != -1) {
                        $goods_info[$k]["options"][$option_id]["total"] -= $num;
                        $goods_info[$k]["options"][$option_id]["total"] = max($goods_info[$k]["options"][$option_id]["total"], 0);
                    }
                }
            }
        }
    }
    $goods_item = $goods_info[$goods_id];
    $goods_item["options_data"] = goods_build_options($goods_item);
    $cart_item = $cart_goods[$goods_id][$option_key];
    if ($sign == "+") {
        if (!goods_is_available($goods_info[$goods_id])) {
            return error(-1, "当前商品不在可售时间范围内");
        }
        $option = $goods_item["options_data"][$option_key];
        if (empty($option["total"]) || $option["total"] < -1) {
            return error(-1, "库存不足");
        }
        if (empty($cart_item)) {
            if (1 < $goods_item["unitnum"] && 0 < $option["total"] && $option["total"] < $goods_item["unitnum"]) {
                return error(-1, "库存不足");
            }
            $title = $goods_item["title"];
            if (!empty($option_key)) {
                $title = (string) $title . "(" . $option["name"] . ")";
            }
            $cart_item = array("cid" => $goods_info[$goods_id]["cid"], "child_id" => $goods_info[$goods_id]["child_id"], "goods_id" => $goods_id, "thumb" => tomedia($goods_info[$goods_id]["thumb"]), "title" => $title, "option_title" => $option["name"], "num" => 0, "price" => $option["price"], "discount_price" => $option["price"], "discount_num" => 0, "price_num" => 0, "total_price" => 0, "total_discount_price" => 0, "bargain_id" => 0, "buy_svip_status" => intval($option["svip_status"]) && $buysvip, "is_svip_price" => $member_svip_status, "total_price_show" => 0, "total_discount_price_show" => 0, "total_huangou_price" => 0, "is_show" => 1);
            if ($svip_buy_show != 2) {
                if ($goods_info[$goods_id]["is_options"]) {
                    if (0 < $option["svip_price"] && $option["svip_price"] < $option["price"] && empty($buysvip)) {
                        $svip_buy_show = 0;
                    }
                } else {
                    if ($option["svip_status"] == 1 && empty($buysvip)) {
                        $svip_buy_show = 0;
                    }
                }
            }
        }
        if ($cart_item["num"] == 0) {
            for ($i = 0; $i < $goods_item["unitnum"]; $i++) {
                $price_change = 0;
                $price = $option["price"];
                if (in_array($goods_id, array_keys($bargain_goods_ids))) {
                    $goods_bargain_id = $bargain_goods_ids[$goods_id];
                    $bargain = $bargains[$goods_bargain_id];
                    $bargain_goods = $bargain["goods"][$goods_id];
                    if ($bargain["type"] == "huangou") {
                        if ($bargain["available_goods_limit"] <= 0 && !in_array($goods_id, $bargain_has_goods)) {
                            return error(-1, "换购:" . $bargain["goods_limit"] . "种商品");
                        }
                        if ($bargain_goods["discount_available_total"] != -1 && $bargain_goods["discount_available_total"] == 0) {
                            return error(-1, "换购商品库存不足");
                        }
                    }
                    $msg = "";
                    $pricenum = get_cart_goodsnum($goods_id, "-1", "price_num", $cart_goods);
                    if ($bargain_goods["poi_user_type"] == "new" && !$_W["member"]["is_store_newmember"]) {
                        if (!$pricenum) {
                    $msg = "仅限门店新用户优惠";
                        }
                        $price_change = 1;
                        $price = $option["price"];
                    }
                    if (!$price_change && $bargain["avaliable_order_limit"] <= 0) {
                        if (!$pricenum) {
                    $msg = (string) $bargain["title"] . "活动每天限购一单,超出后恢复原价";
                        }
                        $price_change = 1;
                        $price = $option["price"];
                    }
                    if (!$price_change && $bargain["available_goods_limit"] <= 0 && !in_array($goods_id, $bargain_has_goods)) {
                        if (!$pricenum) {
                    $msg = (string) $bargain["title"] . "每单特价商品限购" . $bargain["goods_limit"] . "种,超出后恢复原价";
                        }
                        $price_change = 1;
                        $price = $option["price"];
                    }
                    if (!$price_change) {
                        if (!$pricenum && get_cart_goodsnum($goods_id, "-1", "discount_num", $cart_goods) == $bargain_goods["max_buy_limit"]) {
                    $msg = (string) $bargain["title"] . "每单特价商品限购" . $bargain_goods["max_buy_limit"] . "份,超出后恢复原价";
                }
                        if ($bargain_goods["available_buy_limit"] == 0) {
                            $price_change = 1;
                            $price = $option["price"];
                        }
                        if ($bargain_goods["discount_available_total"] != -1 && $bargain_goods["discount_available_total"] == 0) {
                            if (!$pricenum) {
                        $msg = "活动库存不足,恢复原价购买";
                            }
                            $price_change = 1;
                            $price = $option["price"];
                        }
                    }
                    if (!$price_change) {
                        $price_change = 2;
                        $price = $bargain_goods["discount_price"];
                        $cart_bargain[] = $bargain["use_limit"];
                    }
                }
                if ($price_change == 2) {
                    $cart_item["discount_num"]++;
                    $cart_item["bargain_id"] = $bargain["id"];
                    $cart_item["bargain_type"] = $bargain["type"];
                    $cart_item["discount_price"] = $bargain_goods["discount_price"];
                    $bargains[$goods_bargain_id]["goods"][$goods_id]["available_buy_limit"]--;
                    if (0 < $bargains[$goods_bargain_id]["goods"][$goods_id]["discount_available_total"]) {
                        $bargains[$goods_bargain_id]["goods"][$goods_id]["discount_available_total"]--;
                    }
                } else {
                    $cart_item["price_num"]++;
                }
                $cart_item["num"]++;
                $cart_item["total_discount_price"] = $cart_item["discount_num"] * $bargain_goods["discount_price"] + $cart_item["price_num"] * $option["price"];
                $cart_item["total_discount_price"] = round($cart_item["total_discount_price"], 2);
                $cart_item["total_price"] = round($cart_item["num"] * $option["price"], 2);
                $cart_item["total_price_show"] = $cart_item["total_price"];
                $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"];
                if ($bargain["type"] == "huangou") {
                    $cart_item["total_huangou_price"] = round($cart_item["discount_num"] * $bargain_goods["discount_price"], 2);
                    $total_huangou_price = $total_huangou_price + $bargain_goods["discount_price"];
                    $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"] - $cart_item["total_huangou_price"];
                    if ($cart_item["discount_num"] == $cart_item["num"]) {
                        $cart_item["is_show"] = 0;
                    }
                }
                $total_num++;
                $total_box_price = $total_box_price + $goods_item["box_price"];
                $total_price = $total_price + $price;
                $cart_goods[$goods_id][$option_key] = $cart_item;
            }
        } else {
            $price_change = 0;
            $price = $option["price"];
            if (in_array($goods_id, array_keys($bargain_goods_ids))) {
                $goods_bargain_id = $bargain_goods_ids[$goods_id];
                $bargain = $bargains[$goods_bargain_id];
                $bargain_goods = $bargain["goods"][$goods_id];
                if ($bargain["type"] == "huangou") {
                    if ($bargain["available_goods_limit"] <= 0 && !in_array($goods_id, $bargain_has_goods)) {
                        return error(-1, "换购：" . $bargain["goods_limit"] . "种商品");
                    }
                    if ($bargain_goods["available_buy_limit"] <= 0) {
                        return error(-1, "换购商品限购" . $bargain_goods["max_buy_limit"] . "件");
                    }
                    if ($bargain_goods["discount_available_total"] != -1 && $bargain_goods["discount_available_total"] == 0) {
                        return error(-1, "换购商品库存不足");
                    }
                }
                $msg = "";
                $pricenum = get_cart_goodsnum($goods_id, "-1", "price_num", $cart_goods);
                if ($bargain_goods["poi_user_type"] == "new" && !$_W["member"]["is_store_newmember"]) {
                    if (!$pricenum) {
                        $msg = "仅限门店新用户优惠";
                    }
                    $price_change = 1;
                    $price = $option["price"];
                }
                if (!$price_change && $bargain["avaliable_order_limit"] <= 0) {
                    if (!$pricenum) {
                        $msg = (string) $bargain["title"] . "活动每天限购一次超出后恢复原价";
                    }
                    $price_change = 1;
                    $price = $option["price"];
                }
                if (!$price_change && $bargain["available_goods_limit"] <= 0 && !in_array($goods_id, $bargain_has_goods)) {
                    if (!$pricenum) {
                        $msg = (string) $bargain["title"] . "每单特价商品限购" . $bargain["goods_limit"] . "次，超出后恢复原价";
                    }
                    $price_change = 1;
                    $price = $option["price"];
                }
                if (!$price_change) {
                    if (!$pricenum && get_cart_goodsnum($goods_id, "-1", "discount_num", $cart_goods) == $bargain_goods["max_buy_limit"]) {
                        $msg = (string) $bargain["title"] . "每单特价商品限购" . $bargain_goods["max_buy_limit"] . "次，超出后恢复原价";
                    }
                    if ($bargain_goods["available_buy_limit"] <= 0) {
                        $price_change = 1;
                        $price = $option["price"];
                    }
                    if ($bargain_goods["discount_available_total"] != -1 && $bargain_goods["discount_available_total"] == 0) {
                        if (!$pricenum) {
                            $msg = "活动库存不足,恢复原价购买";
                        }
                        $price_change = 1;
                        $price = $option["price"];
                    }
                }
                if (!$price_change) {
                    $price_change = 2;
                    $price = $bargain_goods["discount_price"];
                    $cart_bargain[] = $bargain["use_limit"];
                }
            }
            if ($price_change == 2) {
                $cart_item["discount_num"]++;
                $cart_item["bargain_id"] = $bargain["id"];
                $cart_item["bargain_type"] = $bargain["type"];
                $cart_item["discount_price"] = $bargain_goods["discount_price"];
            } else {
                $cart_item["price_num"]++;
            }
            $cart_item["num"]++;
            $cart_item["total_discount_price"] = $cart_item["discount_num"] * $bargain_goods["discount_price"] + $cart_item["price_num"] * $option["price"];
            $cart_item["total_discount_price"] = round($cart_item["total_discount_price"], 2);
            $cart_item["total_price"] = round($cart_item["num"] * $option["price"], 2);
            $total_num++;
            $total_box_price = $total_box_price + $goods_item["box_price"];
            $total_price = $total_price + $price;
            $cart_item["total_price_show"] = $cart_item["total_price"];
            $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"];
            if ($bargain["type"] == "huangou") {
                $cart_item["total_huangou_price"] = round($cart_item["discount_num"] * $bargain_goods["discount_price"], 2);
                $total_huangou_price = $total_huangou_price + $bargain_goods["discount_price"];
                $cart_item["total_price_show"] = round($option["price"] * ($cart_item["num"] - $cart_item["discount_num"]), 2);
                $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"] - $cart_item["total_huangou_price"];
                if ($cart_item["discount_num"] == $cart_item["num"]) {
                    $cart_item["is_show"] = 0;
                }
            }
        }
    } else {
        if (!empty($cart_item) && 0 < $cart_item["num"]) {
            if ($cart_item["num"] <= $goods_item["unitnum"]) {
                if ($buy_huangou_goods == 1 && $cart_item["discount_num"] <= 0) {
                    return error(-1, "已经没有换购商品");
                }
                $total_num = $total_num - $cart_item["num"];
                $total_box_price = $total_box_price - $goods_item["box_price"] * $cart_item["num"];
                $total_price = $total_price - $cart_item["total_discount_price"];
                $cart_item["num"] = 0;
                $total_huangou_price = 0;
            } else {
                $cart_item["num"]--;
                $price = $cart_item["price"];
                if (empty($buy_huangou_goods)) {
                    if (0 < $cart_item["price_num"]) {
                        $cart_item["price_num"]--;
                    } else {
                        if (0 < $cart_item["discount_num"]) {
                            $price = $cart_item["discount_price"];
                            $cart_item["discount_num"]--;
                            if ($cart_item["discount_num"] <= 0) {
                                $cart_item["bargain_id"] = 0;
                            }
                        }
                    }
                } else {
                    if ($cart_item["discount_num"] <= 0) {
                        return error(-1, "已经没有换购商品");
                    }
                    $price = $cart_item["discount_price"];
                    $cart_item["discount_num"]--;
                    $cart_item["total_huangou_price"] = round($cart_item["discount_num"] * $cart_item["discount_price"], 2);
                    $total_huangou_price = $total_huangou_price - $cart_item["discount_price"];
                    if ($cart_item["discount_num"] <= 0) {
                        $cart_item["bargain_id"] = 0;
                    }
                }
                $cart_item["total_price"] = round($cart_item["num"] * $cart_item["price"], 2);
                $cart_item["total_discount_price"] = $cart_item["discount_num"] * $cart_item["discount_price"] + $cart_item["price_num"] * $cart_item["price"];
                $cart_item["total_discount_price"] = round($cart_item["total_discount_price"], 2);
                $total_num--;
                $total_box_price = $total_box_price - $goods_item["box_price"];
                $total_price = $total_price - $price;
                $cart_item["total_price_show"] = $cart_item["total_price"];
                $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"];
                if ($buy_huangou_goods == 1) {
                    $cart_item["total_price_show"] = round($cart_item["price_num"] * $cart_item["price"], 2);
                    $cart_item["total_discount_price_show"] = $cart_item["total_discount_price"] - $cart_item["total_huangou_price"];
                    if ($cart_item["discount_num"] == $cart_item["num"]) {
                        $cart_item["is_show"] = 0;
                    }
                }
            }
        }
    }
    $box_price_cn = store_get_data($sid, "cn.box_price");
    if (0 < $total_box_price && ORDER_TYPE != "tangshi") {
        $cart_goods["88888"] = array(array("num" => 0, "title" => empty($box_price_cn) ? "餐盒费" : $box_price_cn, "goods_id" => "88888", "discount_num" => 0, "price_num" => 0, "price_total" => $total_box_price, "total_discount_price" => $total_box_price));
    }
    if ($sign) {
        $cart_goods[$goods_id][$option_key] = $cart_item;
        if ($sign == "-") {
            $buysvip = 0;
            foreach ($cart_goods[$goods_id] as $key => &$item) {
                if (!$item["num"]) {
                    unset($cart_goods[$goods_id][$key]);
                }
            }
            foreach ($cart_goods as $val) {
                foreach ($val as $v) {
                    if ($v["buy_svip_status"]) {
                        $buysvip = 1;
                        break 2;
                    }
                }
            }
            $item_total_num = get_cart_goodsnum($goods_id, -1, "num", $cart_goods);
            if (!$item_total_num) {
                unset($cart_goods[$goods_id]);
            }
        }
    }
    $isexist = pdo_fetchcolumn("SELECT id FROM " . tablename("tiny_wmall_order_cart") . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(":aid" => $_W["uniacid"], ":sid" => $sid, ":uid" => $_W["member"]["uid"]));
    $cart_goods_original = array();
    foreach ($cart_goods as $key => $row) {
        $cart_goods_original[$key] = array("title" => $goods_info[$key]["title"], "goods_id" => $key, "options" => $row);
    }
    $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "groupid" => $_W["member"]["groupid"], "num" => $total_num, "price" => round($total_price, 2), "box_price" => round($total_box_price, 2), "data" => iserializer($cart_goods), "original_data" => iserializer($cart_goods_original), "addtime" => TIMESTAMP, "bargain_use_limit" => 0);
    if (!empty($cart_bargain)) {
        $cart_bargain = array_unique($cart_bargain);
        if (in_array(1, $cart_bargain)) {
            $data["bargain_use_limit"] = 1;
        }
        if (in_array(2, $cart_bargain)) {
            $data["bargain_use_limit"] = 2;
        }
    }
    $data["is_buysvip"] = $buysvip;
    if ($_W["member"]["svip_status"] == 1) {
        $data["is_buysvip"] = 0;
    }
    if (empty($isexist)) {
        pdo_insert("tiny_wmall_order_cart", $data);
        $data["id"] = pdo_insertid();
    } else {
        pdo_update("tiny_wmall_order_cart", $data, array("uniacid" => $_W["uniacid"], "id" => $isexist, "uid" => $_W["member"]["uid"]));
        $data["id"] = $cart["id"];
    }
    if (empty($bargain_has_goods)) {
        $discount_notice = array();
        $store_discount = pdo_get("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "sid" => $sid, "type" => "discount", "status" => 1), array("title", "data"));
        if (!empty($store_discount)) {
            $discount_notice["note"] = $store_discount["title"];
            if (0 < $data["price"]) {
                $discount_condition = iunserializer($store_discount["data"]);
                $apply_price = array_keys($discount_condition);
                sort($apply_price);
                foreach ($apply_price as $key => $val) {
                    if ($val < $data["price"] && $apply_price[$key + 1]) {
                        continue;
                    }
                    $dvalue = $val - $data["price"];
                    if ($dvalue <= 5 || 0 < $key) {
                        $discount_notice["leave_price"] = $dvalue;
                        $discount_notice["back_price"] = $discount_condition[$val]["back"];
                        $discount_notice["note"] = 0 < $dvalue ? "再买" . $dvalue . "元, 可减" . $discount_notice["back_price"] . " 元 " : "下单减" . $discount_notice["back_price"] . " 元 ";
                        if (0 < $key && 0 < $dvalue) {
                            $discount_notice["note"] = "下单减" . $discount_condition[$apply_price[$key - 1]]["back"] . " 元 " . $discount_notice["note"];
                        }
                        if ($data["price"] < $apply_price[$key + 1]) {
                            if ($dvalue <= 0) {
                                $furdiscount = $apply_price[$key + 1] - $data["price"];
                                $discount_notice["note"] .= ", 再买 " . $furdiscount . "元可减" . $discount_condition[$apply_price[$key + 1]]["back"];
                            }
                            break;
                        }
                    }
                    if (5 < $dvalue) {
                        break;
                    }
                }
            }
        }
    }
    $data["discount_notice"] = $discount_notice;
    $data["data"] = array_values($cart_goods);
    $data["data1"] = $cart_goods;
    $data["original_data1"] = $cart_goods_original;
    $data["original_data"] = array_values($cart_goods_original);
    $data["total_huangou_price"] = round($total_huangou_price, 2);
    $data["price"] = $data["price"] - $total_huangou_price;
    $data["cart_price"] = round($data["price"] + $data["box_price"], 2);
    $category_limit = order_goods_category_limit_check($sid, $data);
    $data["is_category_limit"] = 0;
    if (is_error($category_limit)) {
        $data["is_category_limit"] = 1;
        $data["category_limit_cn"] = $category_limit["message"];
    }
    $data["svip_buy_show"] = intval($svip_buy_show);
    $data["pindan_id"] = intval($cart["pindan_id"]);
    $result = array("cart" => $data, "msg" => $msg);
    return error(0, $result);
}
function order_insert_member_cart($sid, $ignore_bargain = false)
{
    global $_W;
    global $_GPC;
    if (!empty($_GPC["goods"])) {
		//修复&nbsp;在utf8编码下被转换成黑块的坑
        $_GPC["goods"] = str_replace("&nbsp;", "#nbsp;", $_GPC["goods"]);
        $_GPC["goods"] = json_decode(str_replace("#nbsp;", "&nbsp;", html_entity_decode(urldecode($_GPC["goods"]))), true);
        if (empty($_GPC["goods"])) {
            return array();
        }
        mload()->model("goods");
        $ids_str = implode(",", array_keys($_GPC["goods"]));
        $goods_info = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id IN (" . $ids_str . ")", array(":uniacid" => $_W["uniacid"], ":sid" => $sid), "id");
        $options = pdo_fetchall("select * from " . tablename("tiny_wmall_goods_options") . " where uniacid = :uniacid and sid = :sid and goods_id in (" . $ids_str . ") ", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
        foreach ($options as $option) {
            $goods_info[$option["goods_id"]]["options"][$option["id"]] = $option;
        }
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
                        $row["available_buy_limit"] = $row["max_buy_limit"];
                        $bargain_goods_group[$row["bargain_id"]][$row["goods_id"]] = $row;
                    }
                }
                foreach ($bargains as &$row) {
                    $row["available_goods_limit"] = $row["goods_limit"];
                    $row["goods"] = $bargain_goods_group[$row["id"]];
                }
            } else {
                $bargains = array();
            }
        }
        $total_num = 0;
        $total_original_price = 0;
        $total_price = 0;
        $total_box_price = 0;
        $cart_bargain = array();
        foreach ($_GPC["goods"] as $k => $v) {
            $k = intval($k);
            if (ORDER_TYPE == "tangshi") {
                $goods_info[$k]["price"] = $goods_info[$k]["ts_price"];
            }
            $goods = $goods_info[$k];
            if (empty($goods) || $k == "88888") {
                continue;
            }
            $goods["options_data"] = goods_build_options($goods);
            $goods_box_price = $goods["box_price"];
            if (!$goods["is_options"]) {
                $discount_num = 0;
                foreach ($v["options"] as $key => $val) {
                    $key = trim($key);
                    $option = $goods["options_data"][$key];
                    if (empty($option)) {
                        continue;
                    }
                    $num = intval($val["num"]);
                    if ($num <= 0) {
                        continue;
                    }
                    $title = $goods_info[$k]["title"];
                    if (!empty($key)) {
                        $title = (string) $title . "(" . $option["name"] . ")";
                    }
                    $cart_item = array("title" => $title, "option_title" => $option["name"], "num" => $num, "price" => $goods_info[$k]["price"], "discount_price" => $goods_info[$k]["price"], "discount_num" => 0, "price_num" => $num, "total_price" => round($goods_info[$k]["price"] * $num, 2), "total_discount_price" => round($goods_info[$k]["price"] * $num, 2), "bargain_id" => 0);
                    $bargain = $bargains[$val["bargain_id"]];
                    $bargain_goods = $bargain["goods"][$k];
                    if (0 < $val["bargain_id"] && 0 < $val["discount_num"]) {
                        if ($bargain_goods["max_buy_limit"] < $val["discount_num"]) {
                            $val["discount_num"] = $bargain_goods["max_buy_limit"];
                        }
                        $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":stat_day" => date("Ymd"), ":bargain_id" => $bargain["id"]);
                        $numed = pdo_fetchcolumn("select count(distinct(oid))  from " . tablename("tiny_wmall_order_stat") . " where uniacid = :uniacid and uid = :uid and bargain_id = :bargain_id and stat_day = :stat_day", $params);
                        $numed = intval($numed);
						//available_goods_limit:每单限购几种折扣商品，available_buy_limit：没单限购折扣商品几份，discount_available_total：折扣商品的库存
                        if ($numed < $bargain["order_limit"] && 0 < $bargain["available_goods_limit"] && 0 < $bargain_goods["available_buy_limit"]) {
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
                                        $bargains[$val["bargain_id"]]["goods"][$k]["discount_available_total"]--;
                                    }
                                    $bargain_goods["available_buy_limit"]--;
                                    $bargains[$val["bargain_id"]]["goods"][$k]["available_buy_limit"]--;
                                    $discount_num++;
                                    $i++;
                                } else {
                                    break;
                                }
                            }
                            $cart_item["total_discount_price"] = $cart_item["discount_num"] * $bargain_goods["discount_price"] + $cart_item["price_num"] * $goods_info[$k]["price"];
                            $cart_item["total_discount_price"] = round($cart_item["total_discount_price"], 2);
                        }
                    }
                    $total_num += $num;
                    $total_price += $cart_item["total_discount_price"];
                    $total_original_price += $cart_item["total_price"];
                    $total_box_price += $goods_box_price * $num;
                    $cart_goods[$k][$key] = $cart_item;
                }
                if (0 < $discount_num) {
                    $bargain["available_goods_limit"]--;
                    $bargains[$val["bargain_id"]]["goods"][$k]["available_goods_limit"]--;
                }
            } else {
                foreach ($v["options"] as $key => $val) {
                    $key = trim($key);
                    $option = $goods["options_data"][$key];
                    if (empty($option)) {
                        continue;
                    }
                    $title = $goods_info[$k]["title"];
                    if (!empty($key)) {
                        $title = (string) $title . "(" . $option["name"] . ")";
                    }
                    $cart_goods[$k][$key] = array("title" => $title, "option_title" => $option["name"], "num" => $val["num"], "price" => $option["price"], "discount_price" => $option["price"], "discount_num" => 0, "price_num" => $num, "total_price" => round($option["price"] * $val["num"], 2), "total_discount_price" => round($option["price"] * $val["num"], 2), "bargain_id" => 0);
                    $total_num += $val["num"];
                    $total_price += $option["price"] * $val["num"];
                    $total_original_price += $option["price"] * $val["num"];
                    $total_box_price += $goods_box_price * $val["num"];
                }
            }
        }
        $isexist = pdo_fetchcolumn("SELECT id FROM " . tablename("tiny_wmall_order_cart") . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(":aid" => $_W["uniacid"], ":sid" => $sid, ":uid" => $_W["member"]["uid"]));
        $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "groupid" => $_W["member"]["groupid"], "num" => $total_num, "price" => round($total_price, 2), "box_price" => round($total_box_price, 2), "data" => iserializer($cart_goods), "original_data" => iserializer($_GPC["goods"]), "addtime" => TIMESTAMP, "bargain_use_limit" => 0);
        if (!empty($cart_bargain)) {
            $cart_bargain = array_unique($cart_bargain);
            if (in_array(1, $cart_bargain)) {
                $data["bargain_use_limit"] = 1;
            }
            if (in_array(2, $cart_bargain)) {
                $data["bargain_use_limit"] = 2;
            }
        }
        if (empty($isexist)) {
            pdo_insert("tiny_wmall_order_cart", $data);
        } else {
            pdo_update("tiny_wmall_order_cart", $data, array("uniacid" => $_W["uniacid"], "id" => $isexist, "uid" => $_W["member"]["uid"]));
        }
        $data["data"] = $cart_goods;
        $data["original_data"] = $_GPC["goods"];
        return $data;
    } else {
        return error(-1, "商品信息错误");
    }
}
function order_dispatch_analyse($id, $extra = array())
{
    global $_W;
    $order = order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    $_W["agentid"] = $order["agentid"];
    $store = pdo_get("tiny_wmall_store", array("id" => $order["sid"]), array("location_x", "location_y"));
    $order["store"] = $store;
    $filter = array("over_max_collect_show" => 0);
    if ($extra["channel"] == "plateform_dispatch") {
        $filter = array("over_max_collect_show" => 1);
    }
    $deliveryers = deliveryer_fetchall(0, $filter);
    if (empty($deliveryers)) {
        return error(-1, "没有平台配送员，无法进行自动调度");
    }
    foreach ($deliveryers as &$deliveryer) {
        $deliveryer["order_id"] = $id;
        if (empty($order["location_x"]) || empty($order["location_y"]) || empty($deliveryer["location_y"]) || empty($deliveryer["location_x"])) {
            $deliveryer["store2deliveryer_distance"] = "未知";
            $deliveryer["store2user_distance"] = "未知";
        } else {
            $deliveryer["store2user_distance"] = distanceBetween($order["location_y"], $order["location_x"], $store["location_y"], $store["location_x"]);
            $deliveryer["store2user_distance"] = round($deliveryer["store2user_distance"] / 1000, 2) . "km";
            $deliveryer["store2deliveryer_distance"] = distanceBetween($store["location_y"], $store["location_x"], $deliveryer["location_y"], $deliveryer["location_x"]);
            $deliveryer["store2deliveryer_distance"] = round($deliveryer["store2deliveryer_distance"] / 1000, 2) . "km";
        }
    }
    if (empty($extra["sort"])) {
        $extra["sort"] = "store2deliveryer_distance";
    }
    if ($extra["sort"] == "store2deliveryer_distance") {
        $deliveryers = array_sort($deliveryers, $extra["sort"]);
    } else {
        $deliveryers = array_sort($deliveryers, $extra["sort"], SORT_DESC);
    }
    $order["deliveryers"] = $deliveryers;
    return $order;
}
function order_dispatch_analyse1($id, $extra = array())
{
    global $_W;
    mload()->func("zuobiao");
    $order = order_fetch($id);
    if (empty($order)) {
        return error(-1, "订单不存在或已删除");
    }
    if (3 < $order["status"]) {
        return error(-1, "配送员已接单");
    }
    $_W["agentid"] = $order["agentid"];
    $filter = array("over_max_collect_show" => 0);
    if ($extra["channel"] == "plateform_dispatch") {
        $filter = array("over_max_collect_show" => 1);
    }
    $deliveryers = deliveryer_fetchall(0, $filter);
    if (empty($deliveryers)) {
        return error(-1, "平台没有可用的配送员");
    }
    $limits = array("max_takeout_num" => 5, "same_store_paytime_diff" => 600, "same_store_accept_distance_diff" => 1000, "order_paytime_before" => 600, "accept_distance_diff" => 3000);
	//相同店铺的订单,并且未取餐
    $same_store_orders = pdo_fetchall("select id, location_x, location_y, delivery_status, deliveryer_id, paytime from" . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and delivery_status = 7 group by deliveryer_id", array(":uniacid" => $_W["uniacid"], ":sid" => $order["sid"]));
    if (!empty($same_store_orders)) {
        foreach ($same_store_orders as $val) {
            $deliveryer = $deliveryers[$val["deliveryer_id"]];
            if (empty($deliveryer) || $limits["max_takeout_num"] <= $deliveryer["order_takeout_num"]) {
                continue;
            }
			//订单支付时间间隔
            $between_time = $order["paytime"] - $val["paytime"];
            if ($between_time < $limits["same_store_paytime_diff"]) {
                $distance = distanceBetween($val["location_y"], $val["location_x"], $order["location_y"], $order["location_x"]);
                if ($distance < $limits["same_store_accept_distance_diff"]) {
                    $status = order_assign_deliveryer($id, $val["deliveryer_id"]);
                    if (!is_error($status)) {
                        return error(0, "已分配配送员");
                    }
                } else {
                    slog("takeoutdispatcherror", "系统分配订单失败,订单id:" . $order["id"], array(), "失败原因：同一店铺订单，收货地址距离超过" . $limits["same_store_accept_distance_diff"] . "米");
                }
            } else {
                slog("takeoutdispatcherror", "系统分配订单失败,订单id:" . $order["id"], array(), "失败原因：同一店铺订单，支付时间相差超过" . $limits["same_store_paytime_diff"] . "分钟");
            }
        }
    }
	//手中单少的配送员；同配送方向
    $paytime_limit = TIMESTAMP - $limits["order_paytime_before"];
    $delivery_orders = pdo_fetchall("select sid, deliveryer_id, status, location_x, location_y, delivery_status, addtime, paytime, data from" . tablename("tiny_wmall_order") . " where uniacid = :uniacid and status = 4 and delivery_type = 2 and paytime > :paytime order by delivery_assign_time desc limit 100", array(":uniacid" => $_W["uniacid"], ":paytime" => $paytime_limit));
    if (!empty($delivery_orders)) {
        $deliveryers = array_sort($deliveryers, "order_takeout_num");
        $delivery_orders_sort = array();
		//处理订单少的配送员排序
        foreach ($deliveryers as $deliveryer) {
            foreach ($delivery_orders as $val) {
                if ($val["deliveryer_id"] == $deliveryer["id"] && empty($delivery_orders_sort[$deliveryer["id"]])) {
                    $val["deliveryer"] = $deliveryer;
                    $delivery_orders_sort[$deliveryer["id"]] = $val;
                }
            }
        }
        foreach ($delivery_orders_sort as $val) {
            $val["data"] = iunserializer($val["data"]);
            $deliveryer = $val["deliveryer"];
            if (empty($deliveryer) || $limits["max_takeout_num"] <= $deliveryer["order_takeout_num"]) {
                slog("takeoutdispatcherror", "分配配送员" . $deliveryer["title"] . "失败, 订单id:" . $order["id"], array(), "失败原因：" . $deliveryer["title"] . "手中订单超过" . $limits["max_takeout_num"] . "单");
                continue;
            }
			//判断同方向订单
            $judged_vector = array("destination" => array($order["location_y"], $order["location_x"]), "origin" => array($order["data"]["store"]["location_y"], $order["data"]["store"]["location_x"]));
			//同方向基础上限制新订单收货地址
			//收货距离间距限制条件优化
            $distance_accept = distanceBetween($val["location_y"], $val["location_x"], $order["location_y"], $order["location_x"]);
            if ($distance_accept < $limits["accept_distance_diff"]) {
                if ($val["delivery_status"] == 7) {
                    $reference_vector = array("destination" => array($val["location_y"], $val["location_x"]), "origin" => array($val["data"]["store"]["location_y"], $val["data"]["store"]["location_x"]));
                    $in_identical_direction = is_in_identical_direction($reference_vector, $judged_vector);
                    if ($in_identical_direction) {
                        $status = order_assign_deliveryer($id, $val["deliveryer_id"]);
                        if (!is_error($status)) {
                            return error(0, "已分配配送员");
                        }
                    } else {
                        slog("takeoutdispatcherror", "已分配配送员" . $deliveryer["title"] . "失败, 订单id:" . $order["id"], array(), "失败原因：配送员赶往上一单门店取餐， 与新订单不是同方向");
                    }
                } else {
                    if ($val["delivery_status"] == 8) {
                        $reference_vector = array("destination" => array($val["location_y"], $val["location_x"]), "origin" => array($deliveryer["location_y"], $deliveryer["location_x"]));
                        $delivery_identical_direction = is_in_identical_direction($reference_vector, $judged_vector);
					//配送中判断配送员位置距离取餐位置，出餐时间
					//配送中判断配送员位置距离取餐位置,顾客1的收货距离
                        if ($delivery_identical_direction) {
                            $status = order_assign_deliveryer($id, $val["deliveryer_id"]);
                            if (!is_error($status)) {
                                return error(0, "已分配配送员");
                            }
                        } else {
                            slog("takeoutdispatcherror", "分配配送员" . $deliveryer["title"] . "失败, 订单id:" . $order["id"], array(), "失败原因：配送员上一单已取餐，配送中， 与新订单不是同方向");
                        }
                    }
                }
            } else {
                slog("takeoutdispatcherror", "分配配送员" . $deliveryer["title"] . "失败, 订单id:" . $order["id"], array(), "失败原因：新订单距离配送员手中订单收货地址超过" . $limits["accept_distance_diff"] . "米");
            }
        }
    }
	//空闲配送员
    foreach ($deliveryers as $deliveryer) {
        if ($deliveryer["order_takeout_num"] == 0) {
            $store2deliveryer_distance = distanceBetween($deliveryer["location_y"], $deliveryer["location_x"], $order["data"]["store"]["location_y"], $order["data"]["store"]["location_x"]);
            if (1 || $store2deliveryer_distance <= 1000) {
                $status = order_assign_deliveryer($id, $deliveryer["id"]);
                if (!is_error($status)) {
                    return error(0, "已分配配送员");
                }
            }
        } else {
            slog("takeoutdispatcherror", "分配配送员失败, 订单id:" . $order["id"], array(), "失败原因：平台没有空闲配送员");
        }
    }
	//不满足同方向；空闲配送员
	return error(-1, '经过一圈计算后,平台没有可分配的配送员');
}
function deliveryer_fetchall($sid = 0, $filter = array())
{
    global $_W;
    global $_GPC;
    if (!isset($filter["over_max_collect_show"])) {
        $filter["over_max_collect_show"] = 1;
    }
    $where = "where uniacid = :uniacid ";
    $params = array(":uniacid" => $_W["uniacid"]);
    if (!isset($filter["agentid"]) || $filter["agentid"] != -1) {
        $where .= " and agentid = :agentid";
        if (0 < $_GPC["agentid"]) {
            $_W["agentid"] = $_GPC["agentid"];
        }
        $params[":agentid"] = $_W["agentid"];
    }
    $status = intval($filter["status"]) ? intval($filter["status"]) : 1;
    if (0 < $status) {
        $where .= " and status = :status ";
        $params[":status"] = 1;
    }
    if (!isset($filter["work_status"])) {
        $filter["work_status"] = 1;
    } else {
        if ($filter["work_status"] == -1) {
            unset($filter["work_status"]);
        }
    }
    if (isset($filter["work_status"])) {
        $where .= " and work_status = :work_status";
        $params[":work_status"] = $filter["work_status"];
    }
    if (0 < $sid) {
        $condition = " where uniacid = :uniacid and sid = :sid";
        $params_store = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
        $data = pdo_fetchall("SELECT id,sid,deliveryer_id FROM " . tablename("tiny_wmall_store_deliveryer") . $condition, $params_store, "deliveryer_id");
        if (empty($data)) {
            return array();
        }
        $filter["deliveryer_ids"] = implode(",", array_keys($data));
    } else {
        if (empty($filter["order_type"])) {
            $filter["order_type"] = "is_takeout";
        }
        $where .= " and " . $filter["order_type"] . " = 1";
    }
    if (!empty($filter["deliveryer_ids"])) {
        $where .= " and id in (" . $filter["deliveryer_ids"] . ")";
    }
    $deliveryers = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer") . $where, $params, "id");
    if (!empty($deliveryers)) {
        foreach ($deliveryers as &$da) {
            if ($filter["over_max_collect_show"] == 0) {
                if ($filter["order_type"] == "is_takeout") {
                    if (0 < $da["collect_max_takeout"] && $da["collect_max_takeout"] <= $da["order_takeout_num"]) {
                        unset($deliveryers[$da["id"]]);
                        continue;
                    }
                } else {
                    if ($filter["order_type"] == "is_errander" && 0 < $da["collect_max_errander"] && $da["collect_max_errander"] <= $da["order_errander_num"]) {
                        unset($deliveryers[$da["id"]]);
                        continue;
                    }
                }
            }
            $da["extra"] = iunserializer($da["extra"]);
            if (empty($da["extra"])) {
                $da["extra"] = array("accept_wechat_notice" => 1, "accept_voice_notice" => 1);
            }
            $da["fee_delivery"] = iunserializer($da["fee_delivery"]);
            $da["avatar"] = tomedia($da["avatar"]);
        }
    }
    return $deliveryers;
}
function activity_getall($sid, $status = -1)
{
    global $_W;
    activity_store_cron($sid);
    $params = array("uniacid" => $_W["uniacid"], "sid" => $sid);
    if (0 <= $status) {
        $params["status"] = $status;
    }
    $activity = pdo_getall("tiny_wmall_store_activity", $params, array(), "type");
    if (!empty($activity)) {
        foreach ($activity as &$row) {
            $row["data"] = iunserializer($row["data"]);
        }
    }
    return $activity;
}
function order_is_reach_storesendprice($sendprice, $cartprice)
{
    if ($cartprice < $sendprice) {
        return false;
    }
    return true;
}

?>
