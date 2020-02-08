<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
defined("IN_IA") or exit("Access Denied");
if (!defined("ORDER_TYPE")) {
    define("ORDER_TYPE", "takeout");
}
function bargain_avaliable($sid, $fileds = "id", $filter = array())
{
    global $_W;
    $condition = " where uniacid = :uniacid and sid = :sid and status = :status order by id limit 2";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":status" => 1);
    if (!empty($filter["bargain_id"])) {
        $condition .= " and id = :bargain_id";
        $params[":bargain_id"] = $filter["bargain_id"];
    }
    $bargains = pdo_fetchall("select id, title, content, order_limit, goods_limit from " . tablename("tiny_wmall_activity_bargain") . $condition, $params, "id");
    $bargain_ids = array();
    if ($fileds == "id") {
        if (!empty($bargains)) {
            $bargain_ids = array_keys($bargains);
        }
        return $bargain_ids;
    }
    if ($fileds == "goods") {
        $bargain_ids = implode(",", array_keys($bargains));
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":stat_day" => date("Ymd"), ":uid" => $_W["member"]["uid"]);
        $where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and bargain_id in (" . $bargain_ids . ") group by bargain_id";
        $bargain_order = pdo_fetchall("select count(distinct(oid)) as num, bargain_id from " . tablename("tiny_wmall_order_stat") . $where, $params, "bargain_id");
        foreach ($bargains as &$bargain) {
            $bargain["avaliable_order_limit"] = $bargain["order_limit"];
            if (!empty($bargain_order)) {
                $bargain["avaliable_order_limit"] = $bargain["order_limit"] - intval($bargain_order[$bargain["id"]]["num"]);
            }
            $bargain["hasgoods"] = array();
            array_unshift($categorys, array("id" => "bargain_" . $bargain["id"], "title" => $bargain["title"], "bargain_id" => $bargain["id"]));
        }
        $where = " where uniacid = :uniacid and sid = :sid and (discount_available_total = -1 or discount_available_total > 0) and bargain_id in (" . $bargain_ids . ")";
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
        $bargain_goods = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_bargain_goods") . $where, $params, "goods_id");
        foreach ($bargain_goods as &$goods) {
            $goods = goods_format($goods);
        }
        return $bargain_goods;
    }
}
function goods_format($goods)
{
    global $_W;
    $goods["is_sail_now"] = 1;
    $goods_temp = goods_is_available($goods, "goods");
    if (!empty($goods_temp)) {
        if (!$goods_temp["return_status"]) {
            $goods["is_sail_now"] = 0;
        }
        if (empty($goods["is_showtime"])) {
            $goods["is_showtime"] = $goods_temp["is_showtime"];
            $goods["start_time1"] = $goods_temp["start_time1"];
            $goods["end_time1"] = $goods_temp["end_time1"];
            $goods["week"] = $goods_temp["week"];
        }
    }
    if (isset($goods["discount_price"]) && !empty($goods["discount_price"])) {
        $goods["old_price"] = $goods["old_price"] ? $goods["old_price"] : $goods["price"];
        $goods["price"] = $goods["discount_price"];
        $goods["discount"] = round($goods["price"] / $goods["old_price"], 2) * 10;
    } else {
        if ($goods["config_svip_status"] && $goods["svip_status"] == 1) {
            $old_price = $goods["old_price"] ? $goods["old_price"] : $goods["price"];
            $goods["discount"] = round($goods["svip_price"] / $old_price, 2) * 10;
            $goods["svip_buy_show"] = 1;
            $goods["old_price"] = $old_price;
            $goods["origin_price"] = $goods["price"];
            $goods["price"] = $goods["svip_price"];
            if ($_W["member"]["svip_status"] == 1) {
                $goods["svip_buy_show"] = 0;
            }
            if (ORDER_TYPE == "tangshi") {
                $goods["svip_buy_show"] = 0;
            }
        }
    }
    if ($goods["price"] == $goods["old_price"] || empty($goods["old_price"])) {
        unset($goods["old_price"]);
    }
    $goods["totalnum"] = 0;
    $goods["thumb"] = tomedia($goods["thumb"]);
    $goods["unitname_cn"] = !empty($goods["unitname"]) ? "/" . $goods["unitname"] : "";
    if (1 < $goods["unitnum"]) {
        $unitname_cn = empty($goods["unitname"]) ? "份" : trim($goods["unitname"]);
        $goods["unitnum_multi_cn"] = (string) $goods["unitnum"] . $unitname_cn . "起";
    }
    $goods["options"] = array();
    if ($goods["is_options"] == 1) {
        $goods["options"] = goods_option_fetch($goods["id"]);
        if (empty($goods["options"])) {
            $goods["is_options"] = 0;
        }
    }
    $goods["is_attrs"] = 0;
    $goods["attrs"] = iunserializer($goods["attrs"]);
    if (!empty($goods["attrs"])) {
        $goods["is_attrs"] = 1;
    }
    $goods["options_data"] = goods_build_options($goods);
    $week_cn = array();
    $time_cn = "";
    if ($goods["is_showtime"] == 1) {
        if (!empty($goods["week"])) {
            $weeks = array(0, "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日");
            $week = explode(",", $goods["week"]);
            foreach ($week as $value) {
                foreach ($weeks as $key1 => $value1) {
                    if ($value == $key1) {
                        $week_cn[] = $value1;
                    }
                }
            }
        } else {
            $week_cn = array("星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日");
        }
        if (!empty($goods["start_time1"])) {
            $time_cn = (string) $goods["start_time1"] . "-" . $goods["end_time1"] . " ";
        }
        if (!empty($goods["start_time2"])) {
            $time_cn .= (string) $goods["start_time2"] . "-" . $goods["end_time2"];
        }
    }
    $goods["week_cn"] = implode(",", $week_cn);
    $goods["time_cn"] = $time_cn;
    if (empty($goods["c_status"]) && empty($goods["week_cn"])) {
        $goods["week_cn"] = "商品分组已下架，暂无法购买";
    }
    return $goods;
}
function get_goods_item($goods_id, $option_id = 0, $sign = "+", $cart = array())
{
    global $_W;
    if ($sign == "+") {
        $goods = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $goods_id), array("id", "price", "box_price", "total"));
        if (empty($goods)) {
            return array("price" => 0, "box_price" => 0);
        }
        $goods_info = array("box_price" => $goods["box_price"]);
        if (!empty($option_id)) {
            $option = pdo_get("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "goods_id" => $goods_id, "id" => $option_id), array("id", "price", "total"));
            if (empty($option["total"])) {
                return error(-1, "库存不足");
            }
            $goods_info["price"] = $option["price"];
            return $goods_info;
        }
        $sql = "select a.*,b.id as bid, b. from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join" . tablename("tiny_wmall_activity_bargain") . " as b on a.bargain_id = b.id where a.uniacid = :uniacid and a.goods_id = :goods_id and b.status = 1";
        $bargain_goods = pdo_fetch($sql, array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
        if (empty($bargain_goods["bid"]) || $bargain_goods["discount_available_total"] != -1 && $bargain_goods["discount_available_total"] < 0) {
            if (empty($goods["total"])) {
                return error(-1, "库存不足");
            }
            $goods_info["price"] = $goods["price"];
            return $goods_info;
        }
			//天天特价活动 新用户专享限制
        if ($bargain_goods["poi_user_type"] != "all" && $_W["member"]["is_store_newmember"] == 1) {
            $goods_info["price"] = $goods["price"];
            return $goods_info;
        }
			//天天特价活动 某个商品每单限购几份
        if (!empty($cart) && !empty($cart[$goods_id])) {
            $goods_num = 10;
            if ($bargain_goods["max_buy_limit"] < $goods_num) {
                $goods_info["price"] = $goods["price"];
                return $goods_info;
            }
        }
			//天天特价活动 每个用户每天最多购买几单 限制
        $bargain_id = $bargain_goods["bid"];
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $goods["sid"], ":stat_day" => date("Ymd"), ":uid" => $_W["member"]["uid"], ":bargain_id" => $bargain_id);
        $where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and bargain_id = :bargain_id";
        $bargain_order = pdo_fetch("select count(distinct(oid)) as num, bargain_id from " . tablename("tiny_wmall_order_stat") . $where, $params);
        if ($bargain_goods["order_limit"] <= $bargain_order["num"]) {
            $goods_info["price"] = $goods["price"];
            return $goods_info;
        }
			//天天特价活动 每单可购买特价商品 限制
        if (empty($cart)) {
            $goods_info["price"] = $bargain_goods["discount_price"];
            return $goods_info;
        }
        $goods_ids = pdo_getall("tiny_wmall_activity_bargain_goods", array("uniacid" => $_W["uniacid"], "bargain_id" => $bargain_id), array("goods_id"), "goods_id");
        $goods_ids = array_keys($goods_ids);
        $cart_temp = $cart;
        unset($cart_temp[$goods_id]);
        $cart_goods_ids = array_keys($cart_temp);
        $diff = array_diff($goods_ids, $cart_goods_ids);
        if (empty($diff) || count($diff) < $bargain_goods["goods_limit"]) {
            $goods_info["price"] = $bargain_goods["discount_price"];
            return $goods_info;
        }
        $goods_info["price"] = $goods["price"];
        return $goods_info;
    }
}
function goods_avaliable_fetchall($sid, $cid = 0, $ignore_bargain = false)
{
    global $_W;
    $result = array("goods" => array(), "category" => array());
    $categorys = store_fetchall_goods_category($sid, 1, true, "parent", "available");
    if (empty($categorys)) {
        return $result;
    }
    $condition = " where uniacid = :uniacid and sid = :sid and status = 1 and huangou_type = 1";
    if (ORDER_TYPE == "takeout") {
        $condition .= " and (type = 1 or type = 3)";
    } else {
        if (ORDER_TYPE == "tangshi") {
            $condition .= " and (type = 2 or type = 3)";
        }
    }
    $condition .= " order by displayorder desc, id desc";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $goods = pdo_fetchall("select id, cid, title, price, old_price, ts_price, box_price,svip_status, svip_price, total, thumb, sailed, label, content, is_options, attrs, unitname, unitnum, comment_good, status, is_showtime, start_time1, end_time1, start_time2, end_time2, week from " . tablename("tiny_wmall_goods") . $condition, $params, "id");
    if (empty($goods)) {
        return $result;
    }
    $config_svip_status = svip_status_is_available();
    $options = pdo_fetchall("select * from " . tablename("tiny_wmall_goods_options") . " where uniacid = :uniacid and sid = :sid order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    $goods_options = array();
    foreach ($options as $option) {
        $option["discount_price"] = $option["price"];
        $goods_options[$option["goods_id"]][$option["id"]] = $option;
    }
    unset($options);
    $condition = " where uniacid = :uniacid and sid = :sid and status = :status order by id limit 2";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":status" => 1);
    if (!$ignore_bargain) {
        $bargains = pdo_fetchall("select id, title, content, order_limit, goods_limit from " . tablename("tiny_wmall_activity_bargain") . $condition, $params, "id");
        if (!empty($bargains)) {
            $bargain_ids = implode(",", array_keys($bargains));
            $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":stat_day" => date("Ymd"), ":uid" => $_W["member"]["uid"]);
            $where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and bargain_id in (" . $bargain_ids . ") group by bargain_id";
            $bargain_order = pdo_fetchall("select count(distinct(oid)) as num, bargain_id from " . tablename("tiny_wmall_order_stat") . $where, $params, "bargain_id");
            foreach ($bargains as &$bargain) {
                $bargain["avaliable_order_limit"] = $bargain["order_limit"];
                if (!empty($bargain_order)) {
                    $bargain["avaliable_order_limit"] = $bargain["order_limit"] - intval($bargain_order[$bargain["id"]]["num"]);
                }
                $bargain["hasgoods"] = array();
                array_unshift($categorys, array("id" => "bargain_" . $bargain["id"], "title" => $bargain["title"], "bargain_id" => $bargain["id"]));
            }
            $where = " where uniacid = :uniacid and sid = :sid and (discount_available_total = -1 or discount_available_total > 0) and bargain_id in (" . $bargain_ids . ")";
            $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
            $bargain_goods = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_bargain_goods") . $where, $params, "goods_id");
        }
    }
    $cart = order_fetch_member_cart($sid);
    $cart_goodsids = array();
    if (!empty($cart)) {
        $cart_goodsids = array_keys($cart["data"]);
    }
    $cate_goods = array();
    foreach ($goods as &$good) {
        $goods_category = $categorys[$good["cid"]];
        $good["c_status"] = $goods_category["status"];
        $good["c_is_showtime"] = $goods_category["is_showtime"];
        $good["c_start_time"] = $goods_category["start_time"];
        $good["c_end_time"] = $goods_category["end_time"];
        $good["c_week"] = $goods_category["week"];
        if (empty($good["is_showtime"]) && !empty($good["c_is_showtime"])) {
            $good["is_showtime"] = $good["c_is_showtime"];
            $good["start_time1"] = $good["c_start_time"];
            $good["end_time1"] = $good["c_end_time"];
            $good["week"] = $good["c_week"];
        }
        $good["c_status"] = $goods_category["status"];
        $good["c_status"] = $goods_category["status"];
        $goods["is_showtime"] = $goods["c_is_showtime"];
        $goods["start_time1"] = $goods["c_start_time"];
        $goods["end_time1"] = $goods["c_end_time"];
        $goods["week"] = $goods["c_week"];
        $good["is_sail_now"] = 1;
        if (!goods_is_available($good)) {
            $good["is_sail_now"] = 0;
        }
        $good["totalnum"] = 0;
        $good["thumb"] = tomedia($good["thumb"]);
        $good["unitname_cn"] = !empty($good["unitname"]) ? "/" . $good["unitname"] : "";
        if (1 < $good["unitnum"]) {
            $unitname_cn = empty($good["unitname"]) ? "份" : trim($good["unitname"]);
            $good["unitnum_multi_cn"] = (string) $good["unitnum"] . $unitname_cn . "起";
        }
        $good["options"] = array();
        if ($good["is_options"] == 1) {
            $good["options"] = $goods_options[$good["id"]];
            if (empty($good["options"])) {
                $good["is_options"] = 0;
            }
        }
        $good["is_attrs"] = 0;
        $good["attrs"] = iunserializer($good["attrs"]);
        if (!empty($good["attrs"])) {
            $good["is_attrs"] = 1;
        }
        $good["config_svip_status"] = $config_svip_status;
        $good["from"] = "goods";
        $good["options_data"] = goods_build_options($good);
        if (defined("IN_VUE")) {
            $good["options"] = array_values($goods_options[$good["id"]]);
        }
        $good["show"] = 0;
        if (!empty($cid) && $good["cid"] == $cid) {
            $good["show"] = 1;
        }
        if ($good["is_showtime"] == 1) {
            if (!empty($good["week"])) {
                $week = explode(",", $good["week"]);
                $weeks = array(0, "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日");
                $week_cn = array();
                foreach ($week as $val) {
                    foreach ($weeks as $k => $v) {
                        if ($val == $k) {
                            $week_cn[] = $v;
                        }
                    }
                }
                $good["week_cn"] = implode("，", $week_cn);
            } else {
                $good["week_cn"] = "星期一，星期二，星期三，星期四，星期五，星期六，星期日";
            }
            $time_cn = "";
            if (!empty($good["start_time1"])) {
                $time_cn = (string) $good["start_time1"] . "-" . $good["end_time1"] . " ";
            }
            if (!empty($good["start_time2"])) {
                $time_cn .= (string) $good["start_time2"] . "-" . $good["end_time2"];
            }
            if (empty($good["start_time1"]) && empty($good["start_time2"])) {
                $time_cn = "00:00-23:59";
            }
            $good["time_cn"] = $time_cn;
        }
        if (empty($good["c_status"])) {
            $good["week_cn"] = "商品分组已下架，暂无法购买";
        }
        if ($config_svip_status) {
            if ($good["svip_status"] == 1) {
                $old_price = $good["old_price"] ? $good["old_price"] : $good["price"];
                $good["discount"] = round($good["svip_price"] / $old_price, 2) * 10;
                $good["old_price"] = $old_price;
                $good["svip_buy_show"] = 1;
                $good["origin_price"] = $good["price"];
                $good["price"] = $good["svip_price"];
                if ($_W["member"]["svip_status"] == 1) {
                    $good["svip_buy_show"] = 0;
                }
            }
        } else {
            $good["svip_status"] = 0;
        }
        if (ORDER_TYPE == "tangshi") {
            $good["price"] = $good["ts_price"];
            $good["old_price"] = $good["ts_price"];
            $good["svip_buy_show"] = 0;
        }
        if (in_array($good["id"], $cart_goodsids)) {
            foreach ($cart["data"][$good["id"]] as $key => $cart_option) {
                $good["options_data"][$key]["num"] = $cart_option["num"];
                $good["totalnum"] += $cart_option["num"];
            }
        }
        $good["bargain_id"] = 0;
        if (!empty($bargain_goods) && in_array($good["id"], array_keys($bargain_goods)) && ($good["total"] == -1 || 0 < $good["total"])) {
            $discount_goods = $bargain_goods[$good["id"]];
            $good["bargain_id"] = $discount_goods["bargain_id"];
            $good["old_price"] = $good["old_price"] ? $good["old_price"] : $good["price"];
            $good["discount"] = round($discount_goods["discount_price"] / $good["old_price"], 2) * 10;
            $good["discount_price"] = $discount_goods["discount_price"];
            $good["discount_total"] = $discount_goods["discount_total"];
            $good["max_buy_limit"] = $discount_goods["max_buy_limit"];
            $good["poi_user_type"] = $discount_goods["poi_user_type"];
            $good["svip_buy_show"] = 0;
            if (defined("IN_VUE") || defined("IN_WXAPP")) {
                $good["price"] = $good["discount_price"];
                if ($good["price"] == $good["old_price"] || empty($good["old_price"])) {
                    unset($good["old_price"]);
                }
                $cate_goods["bargain_" . $discount_goods["bargain_id"]][] = $good;
                $cate_goods[$good["cid"]][] = $good;
            } else {
                $cate_goods["bargain_" . $discount_goods["bargain_id"]][] = $good;
            }
        } else {
            if ($good["price"] == $good["old_price"] || empty($good["old_price"])) {
                unset($good["old_price"]);
            }
            $good["discount_price"] = $good["price"];
            $cate_goods[$good["cid"]][] = $good;
        }
    }
    if (!is_array($bargains)) {
        $bargains = array();
    }
    $result = array("goods" => $goods, "cate_goods" => $cate_goods, "category" => $categorys, "bargains" => $bargains);
    if (defined("IN_VUE") || defined("IN_WXAPP")) {
        $cate_has_goods = array();
        if (!empty($categorys)) {
            foreach ($categorys as $key => $value) {
                if (array_key_exists($value["id"], $cate_goods)) {
                    $cate_has_goods[] = array("id" => $value["id"], "title" => $value["title"], "thumb" => $value["thumb"], "category_min_fee" => $value["min_fee"], "total" => $value["total"], "goods" => $cate_goods[$value["id"]]);
                }
            }
        }
        $result["cate_has_goods"] = $cate_has_goods;
    }
    return $result;
}
function goods_option_fetch($id)
{
    global $_W;
    return pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods_options") . " WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC", array(":aid" => $_W["uniacid"], ":goods_id" => $id));
}
function goods_fetch($id)
{
    global $_W;
    $data = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $id));
    $data["options"] = array(array("id" => "0", "title" => "", "price" => $data["price"], "total" => $data["total"]));
    if ($data["is_options"] == 1) {
        $data["options"] = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods_options") . " WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC", array(":aid" => $_W["uniacid"], ":goods_id" => $id));
    }
    $data["is_attrs"] = 0;
    $data["attrs"] = iunserializer($data["attrs"]);
    if (!empty($data["attrs"])) {
        $data["is_attrs"] = 1;
    }
    $options = goods_build_options($data);
    $option = array_slice($options, 0, 1);
    $key = key($option);
    $data["price"] = floatval($option[$key]["price"]);
    $data["option_id"] = $key;
    $data["thumb_"] = tomedia($data["thumb"]);
    if (!$data["comment_total"]) {
        $data["comment_good_percent"] = "0%";
    } else {
        $data["comment_good_percent"] = round($data["comment_good"] / $data["comment_total"] * 100, 2) . "%";
    }
    if (!empty($data["slides"])) {
        $data["slides"] = iunserializer($data["slides"]);
        foreach ($data["slides"] as &$slide) {
            $slide = tomedia($slide);
        }
    } else {
        $data["slides"] = array();
    }
    return $data;
}
function goods_build_options($goods)
{
    global $_W;
    global $_GPC;
    $member_svip_status = 0;
    if ($goods["config_svip_status"]) {
        $buysvip = intval($_GPC["is_buysvip"]);
        if ($_W["member"]["svip_status"] == 1 || $buysvip == 1) {
            $member_svip_status = 1;
        }
    }
    if (!$goods["is_options"] || empty($goods["options"])) {
        $goods["options"] = array(array("id" => "0", "name" => "", "price" => ORDER_TYPE == "takeout" ? $goods["price"] : $goods["ts_price"], "total" => $goods["total"], "svip_status" => $goods["svip_status"], "svip_price" => $goods["svip_price"]));
        if ($goods["config_svip_status"] && $goods["svip_status"]) {
            $goods["options"][0]["origin_price"] = $goods["origin_price"];
        }
    }
    if (!is_array($goods["attrs"])) {
        $goods["attrs"] = iunserializer($goods["attrs"]);
    }
    if (!empty($goods["attrs"]) && is_array($goods["attrs"])) {
        $goods["is_attrs"] = 1;
    }
    if (!$goods["is_attrs"]) {
        $options = array();
        foreach ($goods["options"] as $option) {
            if (0 < $option["svip_price"] && $option["svip_price"] < $option["price"]) {
                $option["origin_price"] = $option["price"];
                if ($goods["from"] == "goods" && ORDER_TYPE == "takeout") {
                    $option["price"] = $option["svip_price"];
                }
                if ($member_svip_status == 1) {
                    $option["svip_status"] = 1;
                    $option["price"] = $option["svip_price"];
                }
            }
            $options[$option["id"]] = $option;
        }
        return $options;
    } else {
        foreach ($goods["attrs"] as $key1 => $value) {
            $labels = array();
            foreach ($value["label"] as $key2 => $label) {
                $labels[(string) $key1 . "s" . $key2] = $label;
            }
            $attrs[] = $labels;
        }
        $attrs = dikaer($attrs, "v");
        $options = array();
        foreach ($goods["options"] as $option) {
            if (0 < $option["svip_price"] && $option["svip_price"] < $option["price"]) {
                $option["origin_price"] = $option["price"];
                if ($goods["from"] == "goods" && ORDER_TYPE == "takeout") {
                    $option["price"] = $option["svip_price"];
                }
                if ($member_svip_status == 1) {
                    $option["svip_status"] = 1;
                    $option["price"] = $option["svip_price"];
                }
            }
            foreach ($attrs as $key => $attr) {
                $index = (string) $option["id"] . "_" . $key;
                $title = $attr;
                if (!empty($option["name"])) {
                    $title = (string) $option["name"] . "+" . $attr;
                }
                $attr = array("name" => $title);
                $options[$index] = array_merge($option, $attr);
            }
        }
        return $options;
    }
}
function tranferOptionid($optionid)
{
    if ($optionid == 0) {
        return 0;
    }
    $params = explode("_", $optionid);
    return $params[0];
}

//商品是否在可售时间检测函数
function goods_is_available($goodsOrId, $return_type = "status")
{
    global $_W;
    $goods = $goodsOrId;
    if (!is_array($goods) || empty($goods["is_showtime"]) || empty($goods["c_status"])) {
        $id = $goods;
        if (is_array($goods)) {
            $id = $goods["id"];
        }
        $goods = pdo_fetch("select a.status, a.is_showtime, a.start_time1, a.end_time1, a.start_time2, a.end_time2, a.week, b.is_showtime as c_is_showtime, b.start_time as c_start_time, b.end_time as c_end_time, b.week as c_week, b.status as c_status from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_goods_category") . " as b on a.cid = b.id where a.uniacid = :uniacid and a.id = :id", array("uniacid" => $_W["uniacid"], "id" => $id));
        if (empty($goods["is_showtime"]) && !empty($goods["c_is_showtime"])) {
            $goods["is_showtime"] = $goods["c_is_showtime"];
            $goods["start_time1"] = $goods["c_start_time"];
            $goods["end_time1"] = $goods["c_end_time"];
            $goods["week"] = $goods["c_week"];
        }
    }
    $goods["return_status"] = false;
    if ($goods["status"] == 2 || $goods["status"] == 0) {
        return false;
    }
    if ($goods["c_status"] == 1) {
        if (empty($goods["is_showtime"])) {
            $goods["return_status"] = true;
        } else {
            $now_week = date("N", TIMESTAMP);
            $start_time1 = intval(strtotime($goods["start_time1"]));
            $end_time1 = intval(strtotime($goods["end_time1"]));
            $start_time2 = intval(strtotime($goods["start_time2"]));
            $end_time2 = intval(strtotime($goods["end_time2"]));
            $week = array();
            if (!empty($goods["week"])) {
                $week = explode(",", $goods["week"]);
            }
            if ((empty($week) || in_array($now_week, $week)) && (!empty($start_time1) && $start_time1 < TIMESTAMP && TIMESTAMP < $end_time1 || !empty($start_time2) && $start_time2 < TIMESTAMP && TIMESTAMP < $end_time2)) {
                $goods["return_status"] = true;
            }
        }
    }
    if ($return_type == "status") {
        return $goods["return_status"];
    }
    return $goods;
}
function goods_filter($sid, $filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        if (!empty($_GPC["cid"])) {
            $filter["cid"] = trim($_GPC["cid"]);
        }
        if (!empty($_GPC["child_id"])) {
            $filter["child_id"] = intval($_GPC["child_id"]);
        }
        if (!empty($_GPC["type"])) {
            $filter["type"] = trim($_GPC["type"]);
        }
        if (!empty($_GPC["value"])) {
            $filter["value"] = trim($_GPC["value"]);
        }
        if (!empty($_GPC["page"])) {
            $filter["page"] = max(1, intval($_GPC["page"]));
        }
        if (!empty($_GPC["psize"])) {
            $filter["psize"] = intval($_GPC["psize"]);
        }
        if (!empty($_GPC["keyword"])) {
            $filter["keyword"] = trim($_GPC["keyword"]);
        }
    }
    $goods_categorys = pdo_fetchall("select id, is_showtime, start_time, end_time, week from " . tablename("tiny_wmall_goods_category") . "where uniacid = :uniacid and sid = :sid", array(":uniacid" => $_W["uniacid"], ":sid" => $sid), "id");
    if (strexists($filter["cid"], "bargain_")) {
        $bargain_id = str_replace("bargain_", "", $filter["cid"]);
        $where = " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id where a.uniacid = :uniacid and a.sid = :sid and b.status = 1 and (a.discount_available_total = -1 or a.discount_available_total > 0) and a.bargain_id = :bargain_id";
        if (ORDER_TYPE == "takeout") {
            $where .= " and (b.type = 1 or b.type = 3)";
        } else {
            if (ORDER_TYPE == "tangshi") {
                $where .= " and (b.type = 2 or b.type = 3)";
            }
        }
        $where .= " order by b.displayorder desc, b.id desc";
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":bargain_id" => $bargain_id);
        $bargain_goods = pdo_fetchall("select a.*, b.* from " . tablename("tiny_wmall_activity_bargain_goods") . $where, $params);
        if (!empty($bargain_goods)) {
            $cart = order_fetch_member_cart($sid);
            $cart_goodsids = array();
            if (!empty($cart)) {
                $cart_goodsids = array_keys($cart["data"]);
            }
            foreach ($bargain_goods as &$goods) {
                $goods_category = $goods_categorys[$goods["cid"]];
                $goods["c_status"] = $goods_category["status"];
                $goods["c_is_showtime"] = $goods_category["is_showtime"];
                $goods["c_start_time"] = $goods_category["start_time"];
                $goods["c_end_time"] = $goods_category["end_time"];
                $goods["c_week"] = $goods_category["week"];
                if (empty($goods["is_showtime"]) && !empty($goods["c_is_showtime"])) {
                    $goods["is_showtime"] = $goods["c_is_showtime"];
                    $goods["start_time1"] = $goods["c_start_time"];
                    $goods["end_time1"] = $goods["c_end_time"];
                    $goods["week"] = $goods["c_week"];
                }
                if (ORDER_TYPE == "tangshi") {
                    $goods["price"] = $goods["ts_price"];
                    $goods["old_price"] = $goods["ts_price"];
                }
                $goods = goods_format($goods);
                if (in_array($goods["id"], $cart_goodsids)) {
                    foreach ($cart["data"][$goods["id"]]["options"] as $key => $cart_option) {
                        $goods["options_data"][$key]["num"] = $cart_option["num"];
                        $goods["totalnum"] += $cart_option["num"];
                    }
                }
                if (!$goods["comment_total"]) {
                    $goods["comment_good_percent"] = "0%";
                } else {
                    $goods["comment_good_percent"] = round($goods["comment_good"] / $goods["comment_total"] * 100, 2) . "%";
                }
            }
        }
        return $bargain_goods;
    } else {
        $condition = " where uniacid = :uniacid and sid = :sid and status = 1 and huangou_type = 1";
        if (ORDER_TYPE == "takeout") {
            $condition .= " and (type = 1 or type = 3)";
        } else {
            if (ORDER_TYPE == "tangshi") {
                $condition .= " and (type = 2 or type = 3)";
            }
        }
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
        if (!empty($filter["cid"])) {
            $condition .= " and cid = :cid";
            $params[":cid"] = $filter["cid"];
        } else {
            if (!empty($filter["goodsids"]) && is_array($filter["goodsids"])) {
                $filter["goodsids"] = implode(",", $filter["goodsids"]);
                $condition .= " and id in (" . $filter["goodsids"] . ")";
            }
        }
        if (!empty($filter["child_id"])) {
            $condition .= " and child_id = :child_id";
            $params[":child_id"] = $filter["child_id"];
        }
        if (!empty($filter["keyword"])) {
            $condition .= " and title like :keyword";
            $params[":keyword"] = "%" . $filter["keyword"] . "%";
        }
        $orderby = " order by displayorder desc, id desc";
        if (!empty($filter["type"]) && !empty($filter["value"])) {
            $orderby = " order by CONVERT(" . $filter["type"] . ",SIGNED) " . $filter["value"] . ", displayorder desc, id desc";
        }
        $pindex = max(1, intval($filter["page"]));
        $psize = !isset($filter["psize"]) ? 30 : $filter["psize"];
        $condition .= $orderby . " LIMIT " . ($pindex - 1) * $psize . " , " . $psize;
        $goods = pdo_fetchall("select id, cid, child_id, title, price, ts_price, svip_price, old_price, box_price,svip_status, total, thumb, sailed, label, content, is_options, attrs, unitname, unitnum, comment_good, status, is_showtime, start_time1, end_time1, start_time2, end_time2, week from " . tablename("tiny_wmall_goods") . $condition, $params);
        if (!empty($goods)) {
            $config_svip_status = svip_status_is_available();
            $cart = order_fetch_member_cart($sid);
            $cart_goodsids = array();
            if (!empty($cart)) {
                $cart_goodsids = array_keys($cart["data"]);
            }
            foreach ($goods as $gkey => &$good) {
                $bargain_goods = pdo_fetch("select a.discount_price,a.max_buy_limit,b.status as bargain_status from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_activity_bargain") . " as b on a.bargain_id = b.id where a.uniacid = :uniacid and a.sid = :sid and a.goods_id = :goods_id and a.status = 1 and b.status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":goods_id" => $good["id"]));
                if (!empty($bargain_goods["bargain_status"])) {
                    $good = array_merge($good, $bargain_goods);
                }
                $good_category = $goods_categorys[$good["cid"]];
                $good["c_status"] = $good_category["status"];
                $good["c_is_showtime"] = $good_category["is_showtime"];
                $good["c_start_time"] = $good_category["start_time"];
                $good["c_end_time"] = $good_category["end_time"];
                $good["c_week"] = $good_category["week"];
                if (empty($good["is_showtime"]) && !empty($good["c_is_showtime"])) {
                    $good["is_showtime"] = $good["c_is_showtime"];
                    $good["start_time1"] = $good["c_start_time"];
                    $good["end_time1"] = $good["c_end_time"];
                    $good["week"] = $good["c_week"];
                }
                $good["unitname_cn"] = !empty($good["unitname"]) ? "/" . $good["unitname"] : "";
                if (1 < $good["unitnum"]) {
                    $unitname_cn = empty($good["unitname"]) ? "份" : trim($good["unitname"]);
                    $good["unitnum_multi_cn"] = (string) $good["unitnum"] . $unitname_cn . "起";
                }
                if (!$config_svip_status) {
                    $good["svip_status"] = 0;
                }
                $good["config_svip_status"] = $config_svip_status;
                $good["from"] = "goods";
                if (ORDER_TYPE == "tangshi") {
                    $good["price"] = $good["ts_price"];
                    $good["old_price"] = $good["ts_price"];
                }
                $good = goods_format($good);
                if (in_array($good["id"], $cart_goodsids)) {
                    foreach ($cart["data"][$good["id"]] as $key => $cart_option) {
                        $good["options_data"][$key]["num"] = $cart_option["num"];
                        $good["totalnum"] += $cart_option["num"];
                    }
                }
                if (!$good["comment_total"]) {
                    $good["comment_good_percent"] = "0%";
                } else {
                    $good["comment_good_percent"] = round($good["comment_good"] / $good["comment_total"] * 100, 2) . "%";
                }
            }
        }
        return $goods;
    }
}
function svip_status_is_available()
{
    if (!check_plugin_perm("svip")) {
        return false;
    }
    $config = get_plugin_config("svip.basic");
    if ($config["status"] == 1 && ORDER_TYPE == "takeout") {
        return true;
    }
    return false;
}
function goods_change_price_check($now_price, $old_price, $config_goods, $data)
{
    global $_W;
    $change_price = $now_price - $old_price;
    if (0 < $change_price && $config_goods["rule_price"]["audit_status"] == 1 && ($_W["role"] == "clerker" || $_W["role"] == "merchanter")) {
        $change_range = $change_price / $old_price * 100;
        if ($config_goods["rule_price"]["increase_range"] < $change_range && 0 < $config_goods["rule_price"]["increase_range"]) {
            return error(-1, "商品价格涨幅不可超过" . $config_goods["rule_price"]["increase_range"] . "%，如有特殊情况需修改请联系平台管理员");
        }
        if (!empty($data["price_updatetime"]) && 0 < $config_goods["rule_price"]["time_interval"]) {
            $can_change_time = $data["price_updatetime"] + $config_goods["rule_price"]["time_interval"] * 86400;
            if (TIMESTAMP < $can_change_time) {
                $can_change_time_cn = date("Y-m-d H:i", $can_change_time);
                return error(-1, "商品价格每" . $config_goods["rule_price"]["time_interval"] . "天可更改一次，请于" . $can_change_time_cn . "后尝试，如有特殊情况需修改请联系平台管理员");
            }
        }
        return error(0, 1);
    }
    return error(0, 0);
}

?>
