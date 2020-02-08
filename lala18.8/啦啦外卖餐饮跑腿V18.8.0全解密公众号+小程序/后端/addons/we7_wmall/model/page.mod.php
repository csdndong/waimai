<?php

defined("IN_IA") or exit("Access Denied");
function store_filter($filter = array(), $orderby = "")
{
    global $_W;
    global $_GPC;
    $condition = "  where uniacid = :uniacid and agentid = :agentid and status = 1 and is_waimai = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    if (empty($filter)) {
        $filter = $_GPC;
    }
    if (0 < $filter["child_id"]) {
        $condition .= " and (cate_childid1 = :child_id or cate_childid2 = :child_id)";
        $params[":child_id"] = $filter["child_id"];
    } else {
        if (0 < $filter["cid"]) {
            $condition .= " and (cate_parentid1 = :parent_id or cate_parentid2 = :parent_id)";
            $params[":parent_id"] = $filter["cid"];
        }
    }
    $delivery_type = intval($filter["delivery_type"]);
    if ($delivery_type == 2) {
        $condition .= " and delivery_type > 1 ";
    }
    if (!empty($filter["ids"])) {
        $condition .= " and id in (" . $filter["ids"] . ")";
    }
    if (defined("IN_WXAPP") || defined("IN_VUE")) {
        $temp = $_GPC["condition"];
        $temp = json_decode(htmlspecialchars_decode($temp), true);
    }
    if (!empty($temp)) {
        $dis = trim($temp["dis"]);
        if (!empty($dis)) {
            if ($dis == "invoice_status") {
                $condition .= " and invoice_status = 1";
            } else {
                if ($dis == "delivery_price") {
                    $condition .= " and (delivery_price = '0' or delivery_free_price > 0)";
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
        $mode = intval($temp["mode"]);
        if (!empty($mode)) {
            $condition .= " and delivery_mode = " . $mode;
        }
    }
    $config_mall = $_W["we7_wmall"]["config"]["mall"];
    $lat = trim($_GPC["lat"]) ? trim($_GPC["lat"]) : "37.80081";
    $lng = trim($_GPC["lng"]) ? trim($_GPC["lng"]) : "112.57543";
    $order_by_base = " order by is_rest asc, is_stick desc";
    $order_by = trim($temp["order"]) ? trim($temp["order"]) : $config_mall["store_orderby_type"];
    if (in_array($order_by, array("sailed", "score", "displayorder", "click"))) {
        $order_by_base .= ", " . $order_by . " desc";
    } else {
        if ($order_by == "displayorderAndDistance") {
            $order_by_base .= ", displayorder desc, distance asc";
        } else {
            $order_by_base .= ", " . $order_by . " asc";
        }
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 20;
    $limit = " limit " . ($pindex - 1) * $psize . "," . $psize;
    $stores = pdo_fetchall("select id,agentid,cate_parentid1,cate_childid1,cate_parentid2,cate_childid2,score,title,logo,content,sailed,score,label,delivery_type,serve_radius,not_in_serve_radius,delivery_areas,business_hours,is_in_business,is_rest,is_stick,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click,\r\n ROUND(\r\n        6378.138 * 2 * ASIN(\r\n            SQRT(\r\n                POW(\r\n                    SIN(\r\n                        (\r\n                            " . $lat . " * 3.141592654 / 180 - location_x * 3.141592654 / 180\r\n                        ) / 2\r\n                    ),\r\n                    2\r\n                ) + COS(" . $lat . " * 3.141592654 / 180) * COS(location_x * 3.141592654 / 180) * POW(\r\n                    SIN(\r\n                        (\r\n                           " . $lng . "  * 3.141592654 / 180 - location_y * 3.141592654 / 180\r\n                        ) / 2\r\n                    ),\r\n                    2\r\n                )\r\n            )\r\n        ) * 1000) as distance from " . tablename("tiny_wmall_store") . " " . $condition . " " . $order_by_base . " " . $limit, $params, "id");
    $total = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . $condition, $params));
    $pagetotal = ceil($total / $psize);
    if (!empty($stores)) {
        $store_keys = implode(",", array_keys($stores));
        $cart_nums = pdo_fetchall("select sid, num as cart_num from " . tablename("tiny_wmall_order_cart") . " where uniacid = :uniacid and uid = :uid and sid in (" . $store_keys . ")", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]), "sid");
        $store_label = category_store_label();
        foreach ($stores as $key => &$da) {
            $da["cart_num"] = intval($cart_nums[$key]["cart_num"]);
            $da["logo"] = tomedia($da["logo"]);
            if ($da["delivery_mode"] == 2 && $da["delivery_type"] != 2) {
                $da["delivery_title"] = $config_mall["delivery_title"];
            }
            $da["scores"] = score_format($da["score"]);
            $da["score"] = floatval($da["score"]);
            $da["url"] = store_forward_url($da["id"], $da["forward_mode"], $da["forward_url"]);
            $da["hot_goods"] = array();
            $hot_goods = pdo_fetchall("select id,title,price,old_price,thumb,svip_status,svip_price from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3", array(":uniacid" => $_W["uniacid"], ":sid" => $da["id"]));
            if (!empty($hot_goods)) {
                foreach ($hot_goods as &$goods) {
                    $goods["thumb"] = tomedia($goods["thumb"]);
                    if (0 < $goods["old_price"] && $goods["price"] < $goods["old_price"]) {
                        $old_price = $goods["old_price"];
                        $goods["discount"] = round($goods["price"] / $goods["old_price"] * 10, 1);
                    } else {
                        $old_price = $goods["price"];
                        $goods["old_price"] = 0;
                        $goods["discount"] = 0;
                    }
                    if ($goods["svip_status"] == 1) {
                        $goods["price"] = $goods["svip_price"];
                        $goods["old_price"] = $old_price;
                        $goods["discount"] = round($goods["price"] / $old_price * 10, 1);
                    }
                    $da["hot_goods"][] = $goods;
                }
                $da["hot_goods_num"] = count($da["hot_goods"]);
                unset($hot_goods);
            }
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
                $da["activity"]["labels"][] = array("title" => "可免配送费", "class" => "tag tag-success");
                $da["activity"]["labels_num"] += 1;
            }
            if (2 <= $da["delivery_type"]) {
                $da["activity"]["labels"][] = array("title" => "支持自取", "class" => "tag tag-success");
                $da["activity"]["labels_num"] += 1;
            }
            if (!empty($da["activity"]["items"]["zhunshibao"])) {
                $da["zhunshibao_cn"] = "准时宝";
                unset($da["activity"]["items"]["zhunshibao"]);
            }
            $da["activity"]["items"] = array_values($da["activity"]["items"]);
            $da["activity"]["is_show_all"] = 0;
            $da["distance"] = round($da["distance"] / 1000, 1);
            if (!empty($lng) && !empty($lat)) {
                $in = is_in_store_radius($da, array($lng, $lat));
                if ($config_mall["store_overradius_display"] == 2 && !$in) {
                    unset($stores[$key]);
                }
            }
            unset($da["delivery_areas"]);
            $da["business_hours"] = iunserializer($da["business_hours"]);
            if (!$da["is_rest"] && !store_is_in_business_hours($da["business_hours"])) {
                $da["is_rest_reserve"] = 1;
                $rest_order_info = store_rest_start_delivery_time($da);
                $da["rest_reserve_cn"] = $rest_order_info["delivery_time_cn"];
            }
        }
    }
    $result = array("stores" => array_values($stores), "total" => $total, "pagetotal" => $pagetotal);
    return $result;
}
function store_page_get($sid, $id = 0, $mobile = true)
{
    global $_W;
    $condition = " WHERE uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    if (empty($id)) {
        $condition .= " and type = :type";
        $params[":type"] = "home";
    } else {
        $condition .= " and id = :id";
        $params[":id"] = $id;
    }
    $page = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_store_page") . $condition, $params);
    if (!empty($page)) {
        $page["data"] = json_decode(base64_decode($page["data"]), true);
        foreach ($page["data"]["items"] as $itemid => &$item) {
            if (in_array($item["id"], array("picture", "banner"))) {
                foreach ($item["data"] as &$val) {
                    $val["imgurl"] = tomedia($val["imgurl"]);
                }
                if ($item["id"] == "picture" && !isset($item["params"]["picturedata"])) {
                    $item["params"]["picturedata"] = 0;
                }
            } else {
                if (in_array($item["id"], array("copyright", "img_card"))) {
                    $item["params"]["imgurl"] = tomedia($item["params"]["imgurl"]);
                } else {
                    if ($item["id"] == "richtext" && $mobile) {
                        $item["params"]["content"] = base64_decode($item["params"]["content"]);
                    } else {
                        if ($item["id"] == "info" && $mobile) {
                            $store = store_fetch($sid, array("id", "title", "logo", "business_hours", "send_price", "delivery_price", "telephone", "address", "is_rest", "location_x", "location_y", "consume_per_person"));
                            $item["data"] = $store;
                        } else {
                            if ($item["id"] == "operation") {
                                if (empty($item["params"])) {
                                    $item["params"] = array("rownum" => 4, "pagenum" => 8, "navsdata" => 0, "navsnum" => 4, "showtype" => 0, "showdot" => 0);
                                }
                                $item["params"]["has_diypage"] = 0;
                                if (check_plugin_perm("diypage")) {
                                    $item["params"]["has_diypage"] = 1;
                                } else {
                                    $item["params"]["navsdata"] = 0;
                                    $item["params"]["showtype"] = 0;
                                }
                                if (!isset($item["style"]["dotbackground"])) {
                                    $item["style"]["dotbackground"] = "#ff2d4b";
                                }
                                if ($item["params"]["navsdata"] == 1) {
                                    $categorys = store_fetchall_goods_category($sid, 1, false, "parent", "available");
                                    $categorys = array_slice($categorys, 0, $item["params"]["navsnum"]);
                                    $item["data"] = array();
                                    if (!empty($categorys)) {
                                        foreach ($categorys as $cate) {
                                            $childid = rand(1000000000, 9999999999.0);
                                            $childid = "C" . $childid;
                                            $item["data"][$childid] = array("text" => $cate["title"], "decoration" => empty($cate["description"]) ? $cate["content"] : $cate["description"], "imgurl" => tomedia($cate["thumb"]), "linkurl" => "/pages/store/goods?sid=" . $sid . "&cid=" . $cate["id"], "color" => "#333333", "dec_color" => "#a0a0a0");
                                        }
                                    }
                                    $item["data_num"] = count($item["data"]);
                                    $item["row"] = ceil($item["params"]["pagenum"] / $item["params"]["rownum"]);
                                    if ($mobile && $item["params"]["showtype"] == 1 && $item["params"]["pagenum"] < $item["data_num"]) {
                                        $item["data"] = array_chunk($item["data"], $item["params"]["pagenum"]);
                                    }
                                } else {
                                    foreach ($item["data"] as &$val) {
                                        $val["imgurl"] = tomedia($val["imgurl"]);
                                    }
                                }
                            } else {
                                if ($item["id"] == "coupon" && $mobile) {
                                    $item["sid"] = $sid;
                                    mload()->model("coupon");
                                    $coupon = coupon_collect_member_available($sid);
                                    if (!empty($coupon)) {
                                        $coupon["can_collect"] = 1;
                                        $coupon["endtime_cn"] = date("Y-m-d", $coupon["endtime"]);
                                        $coupon["collect_percent"] = round($coupon["dosage"] / $coupon["amount"], 2) * 100;
                                    }
                                    $records = pdo_fetchall("select a.id,a.discount,a.condition,a.endtime,a.sid,b.title from" . tablename("tiny_wmall_activity_coupon_record") . " as a left join " . tablename("tiny_wmall_activity_coupon") . " as b on a.couponid = b.id where a.uniacid = :uniacid and a.status = 1 and a.sid = :sid and a.uid = :uid", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":uid" => $_W["member"]["uid"]));
                                    if (!empty($records)) {
                                        foreach ($records as &$record) {
                                            $record["endtime_cn"] = date("Y-m-d", $record["endtime"]);
                                        }
                                        $coupon["record"] = $records;
                                    }
                                    $item["data"] = $coupon;
                                } else {
                                    if ($item["id"] == "onsale" && $mobile) {
                                        $item["sid"] = $sid;
                                        if ($item["params"]["goodsdata"] == "0") {
                                            if (!empty($item["data"]) && is_array($item["data"])) {
                                                $goodsids = array();
                                                foreach ($item["data"] as $data) {
                                                    if (!empty($data["goods_id"])) {
                                                        $goodsids[] = $data["goods_id"];
                                                    }
                                                }
                                                if (!empty($goodsids)) {
                                                    $item["data"] = array();
                                                    $goodsids_str = implode(",", $goodsids);
                                                    $goods = pdo_fetchall("select a.*, b.title as store_title from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.sid = :sid and a.status = 1 and a.id in (" . $goodsids_str . ") order by a.displayorder desc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
                                                }
                                            }
                                        } else {
                                            if ($item["params"]["goodsdata"] == "1") {
                                                $item["data"] = array();
                                                $condition = " where a.uniacid = :uniacid and a.sid = :sid and a.status= 1";
                                                $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
                                                $limit = intval($item["params"]["goodsnum"]);
                                                $limit = $limit ? $limit : 4;
                                                $goods = pdo_fetchall("select a.discount_price,a.goods_id,a.discount_available_total,b.* from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id " . $condition . " order by a.mall_displayorder desc limit " . $limit, $params);
                                                if (!empty($goods)) {
                                                    $stores = pdo_fetchall("select distinct(a.sid),b.title as store_title,b.is_rest from " . tablename("tiny_wmall_activity_bargain") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.status = 1", array(":uniacid" => $_W["uniacid"]), "sid");
                                                }
                                            } else {
                                                if ($item["params"]["goodsdata"] == "2") {
                                                    $item["data"] = array();
                                                    $limit = intval($item["params"]["goodsnum"]);
                                                    $limit = $limit ? $limit : 4;
                                                    $goods = pdo_fetchall("select a.*, b.title as store_title from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.sid = :sid and a.status = 1 and a.is_hot = 1 order by a.displayorder desc limit " . $limit, array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
                                                }
                                            }
                                        }
                                        if (!empty($goods)) {
                                            foreach ($goods as $good) {
                                                $childid = rand(1000000000, 9999999999.0);
                                                $childid = "C" . $childid;
                                                $item["data"][$childid] = array("goods_id" => $good["id"], "sid" => $good["sid"], "store_title" => $item["params"]["goodsdata"] == "1" ? $stores[$good["sid"]]["store_title"] : $good["store_title"], "thumb" => tomedia($good["thumb"]), "title" => $good["title"], "price" => $good["price"], "old_price" => $good["old_price"] ? $good["old_price"] : $good["price"], "sailed" => $good["sailed"], "unitname" => empty($good["unitname"]) ? "份" : $good["unitname"], "total" => $good["total"] != -1 ? $good["total"] : "无限", "discount" => $good["old_price"] == 0 ? 0 : round($good["price"] / $good["old_price"] * 10, 1), "comment_good_percent" => $good["comment_total"] == 0 ? 0 : round($good["comment_good"] / $good["comment_total"] * 100, 2) . "%");
                                                if ($item["params"]["goodsdata"] == "1") {
                                                    $item["data"][$childid]["price"] = $good["discount_price"];
                                                    $item["data"][$childid]["old_price"] = $good["price"];
                                                    $item["data"][$childid]["discount"] = round($good["discount_price"] / $good["price"] * 10, 1);
                                                } else {
                                                    if ($good["svip_status"] == 1) {
                                                        $item["data"][$childid]["svip_status"] = $good["svip_status"];
                                                        $item["data"][$childid]["svip_price"] = $good["svip_price"];
                                                        $item["data"][$childid]["price"] = $good["svip_price"];
                                                        $item["data"][$childid]["discount"] = round($good["svip_price"] / $item["data"][$childid]["old_price"] * 10, 1);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if ($item["id"] == "evaluate" && $mobile) {
                                            $item["sid"] = $sid;
                                            $condition = " where uniacid = :uniacid and sid = :sid and status= 1 order by score desc limit 8";
                                            $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
                                            $item["data"] = array();
                                            $comments = pdo_fetchall("select * from " . tablename("tiny_wmall_order_comment") . $condition, $params);
                                            if (!empty($comments)) {
                                                foreach ($comments as $comment) {
                                                    if (!empty($comment["thumbs"])) {
                                                        $comment["thumbs"] = iunserializer($comment["thumbs"]);
                                                        foreach ($comment["thumbs"] as &$val) {
                                                            $val = tomedia($val);
                                                        }
                                                    }
                                                    $comment["data"] = iunserializer($comment["data"]);
                                                    $comment["goods_title"] = array_merge($comment["data"]["good"], $comment["data"]["bad"]);
                                                    $comment["avatar"] = tomedia($comment["avatar"]);
                                                    $childid = rand(1000000000, 9999999999.0);
                                                    $childid = "C" . $childid;
                                                    $item["data"][$childid] = array("note" => $comment["note"], "thumbs" => $comment["thumbs"], "goods_title" => $comment["goods_title"], "goods_title_str" => implode(" ", $comment["goods_title"]), "mobile" => str_replace(substr($comment["mobile"], 3, 6), "******", $comment["mobile"]), "avatar" => $comment["avatar"], "reply" => $comment["reply"], "score_original" => $comment["score"], "score" => score_format($comment["score"] / 2), "replytime" => $comment["replytime"], "replytime_cn" => date("Y-m-d H:i", $comment["replytime"]), "addtime" => $comment["addtime"], "addtime_cn" => date("Y-m-d H:i", $comment["addtime"]));
                                                }
                                            }
                                        } else {
                                            if ($item["id"] == "picturew" && !empty($item["data"])) {
                                                foreach ($item["data"] as &$v) {
                                                    $v["imgurl"] = tomedia($v["imgurl"]);
                                                }
                                                $item["data_num"] = count($item["data"]);
                                                if ($item["params"]["row"] == 1) {
                                                    $item["data"] = array_values($item["data"]);
                                                } else {
                                                    if ($item["params"]["showtype"] == 1 && $item["params"]["pagenum"] < count($item["data"])) {
                                                        $item["data"] = array_chunk($item["data"], $item["params"]["pagenum"]);
                                                        $item["style"]["rows_num"] = ceil($item["params"]["pagenum"] / $item["params"]["row"]);
                                                        $row_base_height = array("2" => 122, "3" => 85, "4" => 65);
                                                        $item["style"]["base_height"] = $row_base_height[$item["params"]["row"]];
                                                    }
                                                }
                                            } else {
                                                if ($item["id"] == "gohomeActivity" && $mobile) {
                                                    mload()->model("diy");
                                                    $item["data"] = get_wxapp_gohome_goods($item, $mobile);
                                                    if (empty($item["data"])) {
                                                        unset($page["data"]["items"][$itemid]);
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
    return $page;
}

?>