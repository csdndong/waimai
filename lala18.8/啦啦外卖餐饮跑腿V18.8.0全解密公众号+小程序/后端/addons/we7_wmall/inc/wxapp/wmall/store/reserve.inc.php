<?php
defined("IN_IA") or exit("Access Denied");
define("ORDER_TYPE", "tangshi");
mload()->model("goods");
mload()->model("table");
global $_W;
global $_GPC;
icheckauth();
$sid = intval($_GPC["sid"]);
$store = store_fetch($sid);
if (empty($store)) {
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}
if ($store["is_reserve"] != 1) {
    imessage(error(-1, "预定功能暂未开启"), "", "ajax");
}
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_tables_category") . " where uniacid = :uniacid and sid = :sid", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    if (empty($categorys)) {
        imessage(error(-1, "没有可以预订的桌台类型"), "", "ajax");
    }
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_reserve") . " where uniacid = :uniacid and sid = :sid order by id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    if (empty($data)) {
        imessage(error(-1, "没有可以预订的时间段"), "", "ajax");
    }
    $tables = pdo_fetchall("select count(*) as total_num, cid from " . tablename("tiny_wmall_tables") . " where uniacid = :uniacid and sid = :sid and status = :status group by cid", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":status" => "1"), "cid");
    $condition = " where uniacid = :uniacid and sid = :sid and order_type = :order_type and stat_day > :stat_day and status < :status";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":order_type" => 4, ":stat_day" => date("Ymd", strtotime("-6 day")), ":status" => 5);
    $reserve_orders = pdo_fetchall("select id, table_id, table_cid, reserve_time from " . tablename("tiny_wmall_order") . $condition, $params);
    $tables_info = array();
    $reserves = array();
    $now = TIMESTAMP;
    foreach ($data as $da) {
        $timeout = 1;
        $timestamp = strtotime($da["time"]);
        if ($timestamp < $now) {
            $timeout = 0;
        }
        $reserves[$da["table_cid"]][] = array("time" => $da["time"], "title" => empty($da["title"]) ? $da["time"] : $da["title"], "timeout" => $timeout, "total_num" => intval($tables[$da["table_cid"]]["total_num"]));
    }
    if (!empty($reserve_orders)) {
        foreach ($reserve_orders as $val) {
            $reserve_day = substr($val["reserve_time"], 0, 10);
            $reserve_time = substr($val["reserve_time"], -5, 5);
            $tables_info[$reserve_day][$val["table_cid"]][$reserve_time]++;
        }
    }
    $days = array(array("day" => date("m-d"), "day_cn" => "今天", "week_cn" => date2week(TIMESTAMP)), array("day" => date("m-d", strtotime("+1 day")), "day_cn" => "明天", "week_cn" => date2week(strtotime("+1 day"))), array("day" => date("m-d", strtotime("+2 day")), "day_cn" => "后天", "week_cn" => date2week(strtotime("+2 day"))), array("day" => date("m-d", strtotime("+3 day")), "day_cn" => date("m-d", strtotime("+3 day")), "week_cn" => date2week(strtotime("+3 day"))), array("day" => date("m-d", strtotime("+4 day")), "day_cn" => date("m-d", strtotime("+4 day")), "week_cn" => date2week(strtotime("+4 day"))), array("day" => date("m-d", strtotime("+5 day")), "day_cn" => date("m-d", strtotime("+5 day")), "week_cn" => date2week(strtotime("+5 day"))));
    $result = array("categorys" => $categorys, "reserves" => $reserves, "days" => $days, "year" => date("Y"), "tables_info" => $tables_info);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "post") {
        $condition = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
        $day = trim($condition["day"]);
        $time = trim($condition["time"]);
        $cid = intval($condition["cid"]);
        if (empty($day) || empty($time)) {
            imessage(error(-1, "请先选择预定时间"), "", "ajax");
        }
        $category = pdo_get("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "id" => $cid, "sid" => $sid));
        if (empty($category)) {
            imessage(error(-1, "桌台类型有误"), "", "ajax");
        }
        $tables = table_fetchall(array("sid" => $sid, "cid" => $cid, "status" => "1"));
        if (empty($tables)) {
            imessage(error(-1, "该桌台类型下没有可以预订的桌台"), "", "ajax");
        }
        foreach ($tables as &$table) {
            if (0 < $table["guest_num"]) {
                $table["title"] = (string) $table["title"] . "(" . $table["guest_num"] . "人桌)";
            }
        }
        $where = " where uniacid = :uniacid and sid = :sid and order_type = :order_type and stat_day > :stat_day and status < :status and table_cid = :table_cid and reserve_time = :reserve_time";
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":order_type" => 4, ":stat_day" => date("Ymd", strtotime("-6 day")), ":status" => 5, ":table_cid" => $cid, ":reserve_time" => (string) $day . " " . $time);
        $reserve_orders = pdo_fetchall("select id, table_id, count(*) as total_reserve_num from " . tablename("tiny_wmall_order") . $where, $params, "table_id");
        if (!empty($reserve_orders)) {
            $table_num = count($tables);
            $reserve_order = current($reserve_orders);
            if ($table_num <= $reserve_order["total_reserve_num"]) {
                imessage(error(-1, "该桌台类型下没有可以预订的桌台"), "", "ajax");
            }
            foreach ($tables as &$table) {
                if (!empty($reserve_orders[$table["id"]])) {
                    $table["is_reserved"] = 1;
                    $table["title"] = (string) $table["title"] . "(已经预定)";
                }
            }
        }
        $reserve_type = trim($condition["reserve_type"]);
        if ($reserve_type == "order") {
            $cart = order_fetch_member_cart($sid);
        } else {
            $cart["price"] = $category["reservation_price"];
            $cart["num"] = 0;
        }
        mload()->model("coupon");
        $coupons = coupon_available($sid, $cart["price"]);
        $recordid = intval($condition["coupon_id"]);
        $activityed = order_count_activity($sid, $cart, $recordid, 0, 0, 0, 4);
        $order = array("order_type" => 4, "price" => $cart["price"], "total_fee" => $cart["price"] + $serve_fee, "serve_fee" => $serve_fee, "discount_fee" => $activityed["total"], "activityed" => $activityed, "coupon" => coupon_available_check($sid, $recordid, $cart["price"]), "username" => $_W["member"]["realname"], "mobile" => $_W["member"]["mobile"]);
        $order["final_fee"] = $order["total_fee"] - $order["discount_fee"];
        $result = array("store" => $store, "coupons" => $coupons, "category" => $category, "tables" => $tables, "order" => $order, "cart" => $cart, "columns" => array("1套", "2套", "3套", "4套", "5套", "6套", "7套", "8套", "9套", "10套", "11套", "12套", "13套", "14套", "15套", "16套", "17套", "18套", "19套", "20套"), "islegal" => true);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "goodsIndex") {
            $store["activity"] = store_fetch_activity($sid);
            $store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
            mload()->model("coupon");
            $coupons = coupon_collect_member_available($sid);
            $template = $store["data"]["wxapp"]["template"] ? $store["data"]["wxapp"]["template"] : 1;
            $categorys = array_values(store_fetchall_goods_category($sid, 1, false, "all", "available"));
            $table_cid = intval($_GPC["table_cid"]);
            $table_category = pdo_get("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "id" => $table_cid, "sid" => $sid));
            $result = array("store" => $store, "coupon" => $coupons, "category" => $categorys, "cart" => cart_data_init($sid), "template" => $template, "share" => $_share, "table_category" => $table_category);
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
            if ($ta == "goodsIndex_vue") {
                $store["activity"] = store_fetch_activity($sid);
                $store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
                mload()->model("coupon");
                $coupons = coupon_collect_member_available($sid);
                $template = $store["data"]["wxapp"]["template"] ? $store["data"]["wxapp"]["template"] : 1;
                $table_cid = intval($_GPC["table_cid"]);
                $table_category = pdo_get("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "id" => $table_cid, "sid" => $sid));
                $template_page = $store["data"]["wxapp"]["template_page"]["vue"] ? $store["data"]["wxapp"]["template_page"]["vue"] : 0;
                $result = array("store" => $store, "coupon" => $coupons, "cart" => cart_data_init($sid), "template" => $template, "share" => $_share, "template_page" => $template_page, "table_category" => $table_category, "lazyload_goods" => $_config_mall["lazyload_goods"]);
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
                imessage(error(0, $result), "", "ajax");
            } else {
                if ($ta == "truncate") {
                    $data = pdo_delete("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]));
                    imessage(error(0, ""), "", "ajax");
                } else {
                    if ($ta == "cart") {
                        $goods_id = intval($_GPC["goods_id"]);
                        $option_id = trim($_GPC["option_id"]);
                        $sign = trim($_GPC["sign"]);
                        $cart = cart_data_init($sid, $goods_id, $option_id, $sign);
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
                                $table_cid = intval($_GPC["table_cid"]);
                                $table_category = pdo_get("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "id" => $table_cid, "sid" => $sid));
                                $goods = goods_fetch($id);
                                if (is_error($goods)) {
                                    imessage(error(-1, "商品不存在或已删除"), "", "ajax");
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
                                $result = array("goodsDetail" => $goods, "cart" => cart_data_init($sid), "store" => $store, "table_category" => $table_category);
                                message(error(0, $result), "", "ajax");
                            } else {
                                if ($ta == "submit") {
                                    $condition = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
                                    $day = trim($condition["day"]);
                                    $time = trim($condition["time"]);
                                    $cid = intval($condition["cid"]);
                                    if (empty($day) || empty($time)) {
                                        imessage(error(-1, "请先选择预定时间"), "", "ajax");
                                    }
                                    $category = pdo_get("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "id" => $cid, "sid" => $sid));
                                    if (empty($category)) {
                                        imessage(error(-1, "桌台类型错误"), "", "ajax");
                                    }
                                    $reserve_type = trim($condition["reserve_type"]);
                                    if ($reserve_type == "order") {
                                        $cart = order_fetch_member_cart($sid);
                                    } else {
                                        $cart = array("num" => 0, "price" => $category["reservation_price"]);
                                    }
                                    $recordid = intval($condition["coupon_id"]);
                                    $activityed = order_count_activity($sid, $cart, $recordid, 0, 0, 0, 4);
                                    $invoice_id = intval($condition["invoiceId"]);
                                    if (0 < $invoice_id) {
                                        $invoice = member_invoice($invoice_id);
                                        $invoice = iserializer(array("title" => $invoice["title"], "recognition" => $invoice["recognition"]));
                                    }
                                    $order = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "ordersn" => date("YmdHis") . random(6, true), "serial_sn" => store_order_serial_sn($sid), "code" => random(4, true), "order_type" => 4, "openid" => $_W["openid"], "mobile" => trim($condition["mobile"]), "username" => trim($condition["username"]), "person_num" => intval($condition["person_num"]), "table_cid" => $cid, "table_id" => intval($condition["table_id"]), "reserve_type" => $reserve_type, "reserve_time" => (string) $day . " " . $time, "sex" => "", "address" => "", "location_x" => "", "location_y" => "", "delivery_day" => "", "delivery_time" => "", "delivery_fee" => 0, "pack_fee" => 0, "pay_type" => "", "num" => $cart["num"], "price" => $cart["price"], "total_fee" => $cart["price"], "discount_fee" => $activityed["total"], "store_discount_fee" => $activityed["store_discount_fee"], "plateform_discount_fee" => $activityed["plateform_discount_fee"], "final_fee" => $cart["price"] - $activityed["total"], "status" => 1, "is_comment" => 0, "invoice" => $invoice, "addtime" => TIMESTAMP, "data" => iserializer($cart["data"]), "note" => trim($condition["note"]));
                                    if ($order["final_fee"] < 0) {
                                        $order["final_fee"] = 0;
                                    }
                                    pdo_insert("tiny_wmall_order", $order);
                                    $id = pdo_insertid();
                                    order_update_bill($id);
                                    order_insert_discount($id, $sid, $activityed["list"]);
                                    order_insert_status_log($id, "place_order");
                                    if ($reserve_type == "order") {
                                        order_update_goods_info($id, $sid);
                                        order_del_member_cart($sid);
                                    }
                                    imessage(error(0, $id), "", "ajax");
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
if ($ta == "note") {
    if ($store["invoice_status"] == 1) {
        $invoices = member_invoices();
    }
    $result = array("invoices" => $invoices, "store" => $store);
    imessage(error(0, $result), "", "ajax");
}

?>