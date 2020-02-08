<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if (is_weixin() && check_plugin_perm("spread")) {
    icheckauth();
}
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$config = $_W["we7_wmall"]["config"]["mall"];
$config_takeout = $_W["we7_wmall"]["config"]["takeout"];
$_W["page"]["title"] = $config["title"];
if ($ta == "index") {
    if ($config["version"] == 2) {
        $url = imurl("wmall/store/goods", array("sid" => $config["default_sid"]));
        header("location:" . $url);
        exit;
    }
    if (!empty($_GPC["__address"])) {
        $_GPC["__address"] = urldecode($_GPC["__address"]);
    }
    $address_id = intval($_GPC["aid"]);
    if (0 < $address_id) {
        isetcookie("__aid", $address_id, 1800);
    }
    $_share = get_mall_share();
    include itemplate("home/index");
} else {
    if ($ta == "data") {
        $spread = error(-1, "");
        if (check_plugin_perm("spread")) {
            mload()->model("plugin");
            pload()->model("spread");
            $spread = member_spread_bind();
            if (!is_error($spread)) {
                $spread = error(0, $spread);
            }
        }
        $orderbys = store_orderbys();
        $discounts = store_discounts();
        $slides = sys_fetch_slide("homeTop", true);
        $categorys = store_fetchall_category();
        $categorys_chunk = array_chunk($categorys, 8);
        $notices = pdo_fetchall("select id,title,link,wxapp_link,displayorder,status from" . tablename("tiny_wmall_notice") . " where uniacid = :uniacid and agentid = :agentid and type = :type and status = 1 order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":type" => "member"));
        $recommends = store_fetchall_by_condition("recommend", array("extra_type" => "base"));
        $cubes = pdo_fetchall("select * from " . tablename("tiny_wmall_cube") . " where uniacid = :uniacid and agentid = :agentid order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
        if (check_plugin_perm("bargain")) {
            $_config_bargain = get_plugin_config("bargain");
            if ($_config_bargain["status"] == 1 && $_config_bargain["is_home_display"] == 1) {
                $bargains = pdo_fetchall("select a.discount_price,a.goods_id,b.title,b.thumb,b.price,b.sid,c.is_rest from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id left join " . tablename("tiny_wmall_store") . "as c on b.sid = c.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1 order by c.is_rest asc, a.mall_displayorder desc limit 8", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
                foreach ($bargains as &$val) {
                    $val["discount"] = round($val["discount_price"] / $val["price"] * 10, 1);
                }
            }
        }
        $lat = trim($_GPC["lat"]);
        $lng = trim($_GPC["lng"]);
        $order_by_type = $config["store_orderby_type"] ? $config["store_orderby_type"] : "distance";
        $order_by = " order by is_rest asc, is_stick desc";
        if (!empty($order_by_type) && $order_by_type != "distance") {
            if ($order_by_type == "displayorderAndDistance") {
                $order_by .= ", displayorder desc";
            } else {
                $order_by .= ", " . $order_by_type . " desc";
            }
        }
        $stores = pdo_fetchall("select id,agentid,score,title,logo,content,sailed,score,label,serve_radius,not_in_serve_radius,delivery_areas,business_hours,is_in_business,is_rest,is_stick,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and agentid = :agentid and status = 1 " . $order_by, array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
        $min = 0;
        if (!empty($stores)) {
            $store_label = category_store_label();
            foreach ($stores as $key => &$da) {
                $da["logo"] = tomedia($da["logo"]);
                $da["score_cn"] = round($da["score"] / 5, 2) * 100;
                $da["url"] = store_forward_url($da["id"], $da["forward_mode"], $da["forward_url"]);
                if (0 < $da["label"]) {
                    $da["label_color"] = $store_label[$da["label"]]["color"];
                    $da["label_cn"] = $store_label[$da["label"]]["title"];
                }
                if ($da["delivery_fee_mode"] == 2) {
                    $da["delivery_price"] = iunserializer($da["delivery_price"]);
                    $da["delivery_price"] = $da["delivery_price"]["start_fee"];
                } else {
                    if ($da["delivery_fee_mode"] == 3) {
                        $da["delivery_areas"] = iunserializer($da["delivery_areas"]);
                        if (!is_array($da["delivery_areas"])) {
                            $da["delivery_areas"] = array();
                        }
                        $price = store_order_condition($da, array($lng, $lat));
                        $da["delivery_price"] = $price["delivery_price"];
                        $da["send_price"] = $price["send_price"];
                        $da["delivery_free_price"] = $price["delivery_free_price"];
                    }
                }
                $da["activity"] = store_fetch_activity($da["id"]);
                if (0 < $da["delivery_free_price"]) {
                    $da["activity"]["items"]["delivery"] = array("title" => "满" . $da["delivery_free_price"] . "免配送费", "type" => "delivery");
                    $da["activity"]["num"] += 1;
                }
                if (!empty($lng) && !empty($lat)) {
                    $da["distance"] = distanceBetween($da["location_y"], $da["location_x"], $lng, $lat);
                    $da["distance"] = round($da["distance"] / 1000, 2);
                    $in = is_in_store_radius($da, array($lng, $lat));
                    if ($config["store_overradius_display"] == 2 && !$in) {
                        unset($stores[$key]);
                    }
                    $da["distance_order"] = $da["distance"] + $da["distance"] * ($da["is_rest"] == 0 ? 0 : 10000000);
                    $da["distance_order"] = $da["distance_order"] + $da["distance_order"] * ($da["is_stick"] == 1 ? 0 : 10000);
                    if ($order_by_type == "displayorderAndDistance" && $da["is_rest"] == 0) {
                        if ($da["is_stick"] == 1) {
                            $da["distance_order"] = $da["distance_order"] / 10000 + 255 - $da["displayorder"];
                        } else {
                            $da["distance_order"] = $da["distance_order"] / 10000 + (256 - $da["displayorder"]) * 10000;
                        }
                    }
                } else {
                    $da["distance"] = 0;
                }
            }
            if (!empty($stores)) {
                $min = min(array_keys($stores));
                if (($order_by_type == "distance" || $order_by_type == "displayorderAndDistance") && !empty($lng)) {
                    $stores = array_sort($stores, "distance_order", SORT_ASC);
                }
                $stores = array_values($stores);
            }
        }
        $result = array("config" => $config, "slides" => $slides, "categorys" => $categorys, "categorys_chunk" => $categorys_chunk, "notices" => $notices, "recommends" => $recommends, "cubes" => $cubes, "bargains" => $bargains, "orderbys" => $orderbys, "discounts" => $discounts, "stores" => $stores, "spread" => $spread);
        $respon = array("error" => 0, "message" => $result, "min" => $min);
        message($respon, "", "ajax");
    }
}

?>