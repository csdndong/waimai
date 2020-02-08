<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "搜索";
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$config = $_W["we7_wmall"]["config"]["mall"];
$config_takeout = $_W["we7_wmall"]["config"]["takeout"];
$carousel = store_fetch_category();
if ($ta == "list") {
    $lat = trim($_GPC["lat"]);
    $lng = trim($_GPC["lng"]);
    $condition = " where uniacid = :uniacid and agentid = :agentid and status = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $cid = intval($_GPC["cid"]);
    if (0 < $cid) {
        $condition .= " and cid like :cid";
        $params[":cid"] = "%|" . $cid . "|%";
    }
    $dis = trim($_GPC["dis"]);
    if (!empty($dis)) {
        if (in_array($dis, array("invoice_status"))) {
            $condition .= " and invoice_status = 1";
        } else {
            if ($dis == "delivery_price") {
                $condition .= " and (delivery_price = 0 or delivery_free_price > 0)";
            } else {
                $sids = pdo_getall("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "type" => $dis, "status" => 1), array("sid"), "sid");
                if (empty($sids)) {
                    $sids = array(0);
                }
                $sids = implode(",", array_keys($sids));
                $condition .= " and id in (" . $sids . ")";
            }
        }
    }
    $order_by_type = trim($_GPC["order"]) ? trim($_GPC["order"]) : $config["store_orderby_type"];
    $order_by = " order by is_rest asc, is_stick desc";
    if ($order_by_type != "distance") {
        if (in_array($order_by_type, array("sailed", "score", "displayorder", "click"))) {
            $order_by .= ", " . $order_by_type . " desc";
        } else {
            if ($order_by_type == "displayorderAndDistance") {
                $order_by .= ", displayorder desc";
            } else {
                $order_by .= ", " . $order_by_type . " asc";
            }
        }
    }
    $stores = pdo_fetchall("select id,title,logo,content,label,serve_radius,not_in_serve_radius,delivery_areas,sailed,score,business_hours,is_in_business,is_rest,is_stick,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url from " . tablename("tiny_wmall_store") . (string) $condition . " " . $order_by, $params);
    $min = 0;
    if (!empty($stores)) {
        $store_label = category_store_label();
        foreach ($stores as $key => &$row) {
            $row["logo"] = tomedia($row["logo"]);
            $row["score_cn"] = round($row["score"] / 5, 2) * 100;
            $row["url"] = store_forward_url($row["id"], $row["forward_mode"], $row["forward_url"]);
            if (0 < $row["label"]) {
                $row["label_color"] = $store_label[$row["label"]]["color"];
                $row["label_cn"] = $store_label[$row["label"]]["title"];
            }
            if ($row["delivery_fee_mode"] == 2) {
                $row["delivery_price"] = iunserializer($row["delivery_price"]);
                $row["delivery_price"] = $row["delivery_price"]["start_fee"];
            } else {
                if ($row["delivery_fee_mode"] == 3) {
                    $row["delivery_areas"] = iunserializer($row["delivery_areas"]);
                    if (!is_array($row["delivery_areas"])) {
                        $row["delivery_areas"] = array();
                    }
                    $price = store_order_condition($row, array($lng, $lat));
                    $row["delivery_price"] = $price["delivery_price"];
                    $row["send_price"] = $price["send_price"];
                    $row["delivery_free_price"] = $price["delivery_free_price"];
                }
            }
            $row["activity"] = store_fetch_activity($row["id"]);
            if (0 < $row["delivery_free_price"]) {
                $row["activity"]["items"]["delivery"] = array("title" => "满" . $row["delivery_free_price"] . "免配送费", "type" => "delivery");
                $row["activity"]["num"] += 1;
            }
            if (!empty($lng) && !empty($lat)) {
                $row["distance"] = distanceBetween($row["location_y"], $row["location_x"], $lng, $lat);
                $row["distance"] = round($row["distance"] / 1000, 2);
                $in = is_in_store_radius($row, array($lng, $lat));
                if ($config["store_overradius_display"] == 2 && !$in) {
                    unset($stores[$key]);
                }
            } else {
                $row["distance"] = 0;
            }
            $row["distance_order"] = $row["distance"] + $row["distance"] * ($row["is_rest"] == 0 ? 0 : 10000000);
            $row["distance_order"] = $row["distance_order"] + $row["distance_order"] * ($row["is_stick"] == 1 ? 0 : 10000);
            if ($order_by_type == "displayorderAndDistance" && $row["is_rest"] == 0) {
                if ($row["is_stick"] == 1) {
                    $row["distance_order"] = $row["distance_order"] / 10000 + 255 - $row["displayorder"];
                } else {
                    $row["distance_order"] = $row["distance_order"] / 10000 + (256 - $row["displayorder"]) * 10000;
                }
            }
        }
        if (!empty($stores)) {
            if ($order_by_type == "distance") {
                $stores = array_sort($stores, (string) $order_by_type . "_order", SORT_ASC);
            }
            $stores = array_values($stores);
        }
    }
    $categorys = store_fetchall_category();
    $discounts = store_discounts();
    $orderbys = store_orderbys();
    $result = array("filter" => array("cid" => $cid, "dis" => $dis, "order" => $order_by_type), "stores" => $stores, "categorys" => $categorys, "discounts" => $discounts, "orderbys" => $orderbys, "carousel" => $carousel, "categoryTitle" => !empty($carousel["title"]) ? $carousel["title"] : "商家分类", "discountTitle" => !empty($discounts[$_GPC["dis"]]["title"]) ? $discounts[$_GPC["dis"]]["title"] : "优惠活动", "orderTitle" => !empty($orderbys[$_GPC["order"]]["title"]) ? $orderbys[$_GPC["order"]]["title"] : "智能排序", "lat" => $lat, "lng" => $lng);
    $respon = array("error" => 0, "message" => $result, "min" => $min);
    message($respon, "", "ajax");
}
include itemplate("home/search");

?>