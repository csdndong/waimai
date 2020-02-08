<?php


defined("IN_IA") or exit("Access Denied");
function is_favorite_store($sid, $uid = 0)
{
    global $_W;
    if (empty($uid)) {
        $uid = $_W["member"]["uid"];
    }
    $is_ok = pdo_get("tiny_wmall_store_favorite", array("sid" => $sid, "uid" => $uid));
    if (!empty($is_ok)) {
        return true;
    }
    return false;
}
function store_set_data($sid, $key, $value)
{
    global $_W;
    $data = store_get_data($sid);
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        $data[$keys[0]] = $value;
    } else {
        if ($counts == 2) {
            if (!is_array($data[$keys[0]])) {
                $data[$keys[0]] = array();
            }
            $data[$keys[0]][$keys[1]] = $value;
        } else {
            if ($counts == 3) {
                if (!is_array($data[$keys[0]])) {
                    $data[$keys[0]] = array();
                } else {
                    if (!is_array($data[$keys[0]][$keys[1]])) {
                        $data[$keys[0]][$keys[1]] = array();
                    }
                }
                $data[$keys[0]][$keys[1]][$keys[2]] = $value;
            }
        }
    }
    pdo_update("tiny_wmall_store", array("data" => iserializer($data)), array("uniacid" => $_W["uniacid"], "id" => $sid));
    return true;
}
function store_get_data($sid, $key = "")
{
    global $_W;
    $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $sid), array("data"));
    $data = iunserializer($store["data"]);
    if (!is_array($data)) {
        $data = array();
    }
    if (empty($key)) {
        return $data;
    }
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        return $data[$key];
    }
    if ($counts == 2) {
        return $data[$keys[0]][$keys[1]];
    }
    if ($counts == 3) {
        return $data[$keys[0]][$keys[1]][$keys[1]];
    }
    return true;
}
function clerk_manage($id)
{
    global $_W;
    $perm = pdo_getall("tiny_wmall_store_clerk", array("uniacid" => $_W["uniacid"], "clerk_id" => $id, "role" => "manager"), array(), "sid");
    if (empty($perm)) {
        return array();
    }
    return array_keys($perm);
}
function store_fetch($id, $field = array())
{
    global $_W;
    if (empty($id)) {
        return false;
    }
    $field_str = "*";
    if (!empty($field)) {
        $field[] = "status";
        if (in_array("cid", $field) && !in_array("cate_parentid1", $field)) {
            $field = array_merge($field, array("cate_parentid1", "cate_childid1", "cate_parentid2", "cate_childid2"));
        }
        $field = array_unique($field);
        $field_str = implode(",", $field);
    }
    $data = pdo_fetch("SELECT " . $field_str . " FROM " . tablename("tiny_wmall_store") . " WHERE uniacid = :uniacid AND id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $id));
    if (empty($data)) {
        return error(-1, "门店不存在或已删除");
    }
    if ($data["status"] == 4) {
        return error(-1, "门店已删除");
    }
    if (empty($data["delivery_mode"])) {
        $data["delivery_mode"] = 2;
    }
    $data["origin_logo"] = $data["logo"];
    $data["logo"] = tomedia($data["logo"]);
    $data["delivery_title"] = $data["delivery_mode"] == 2 && $data["delivery_type"] != 2 ? $_W["we7_wmall"]["config"]["mall"]["delivery_title"] : "";
    $cid = array_filter(explode("|", $data["cid"]));
    $data["category_arr"] = array_values($cid);
    $cid = implode(",", $cid);
    if (!empty($data["cid"]) && !empty($cid)) {
        $category = pdo_fetchall("select id, title from " . tablename("tiny_wmall_store_category") . " where uniacid = :uniacid and id in (" . $cid . ")", array(":uniacid" => $_W["uniacid"]));
        $data["category"] = array();
        if (!empty($category)) {
            $category_cn1 = $category_cn2 = "";
            foreach ($category as $val) {
                if ($val["id"] == $data["cate_parentid1"]) {
                    $category_cn1 .= $val["title"];
                }
                if ($val["id"] == $data["cate_childid1"]) {
                    $category_cn1 .= "-" . $val["title"];
                }
                if ($val["id"] == $data["cate_parentid2"]) {
                    $category_cn2 .= $val["title"];
                }
                if ($val["id"] == $data["cate_childid2"]) {
                    $category_cn2 .= "-" . $val["title"];
                }
                $data["category"][] = $val["title"];
            }
            $data["category"] = implode("、", $data["category"]);
            $data["category_cn1"] = $category_cn1;
            $data["category_cn2"] = $category_cn2;
        }
    }
    $se_fileds = array("thumbs", "delivery_areas", "delivery_areas1", "delivery_extra", "sns", "payment", "business_hours", "remind_reply", "qualification", "comment_reply", "wechat_qrcode", "custom_url", "serve_fee", "order_note", "delivery_times", "data", "haodian_data");
    foreach ($se_fileds as $se_filed) {
        if (isset($data[$se_filed])) {
            if (!in_array($se_filed, array("thumbs", "delivery_areas", "qualification"))) {
                $data[$se_filed] = iunserializer($data[$se_filed]);
            } else {
                $data[$se_filed] = iunserializer($data[$se_filed]);
                if ($se_filed == "thumbs") {
                    foreach ($data[$se_filed] as &$thumb) {
                        $thumb["image"] = tomedia($thumb["image"]);
                    }
                } else {
                    if ($se_filed == "qualification") {
                        foreach ($data[$se_filed] as &$thumb) {
                            $thumb["thumb"] = tomedia($thumb["thumb"]);
                        }
                    }
                }
            }
        }
    }
    $data["address_type"] = 0;
    if (check_plugin_perm("area") && $_W["we7_wmall"]["config"]["mall"]["address_type"] == 1) {
        $data["address_type"] = 1;
    }
    if ($data["auto_handel_order"] == 2 && isset($data["auto_print_order"])) {
        $data["auto_print_order"] = 0;
    }
    if (!empty($data["delivery_areas1"])) {
        foreach ($data["delivery_areas1"] as $key => $value) {
            mload()->model("plugin");
            pload()->model("area");
            $status = area_check_area_status($key);
            if (empty($status)) {
                unset($data["delivery_areas1"][$key]);
            }
        }
        $data["delivery_areas1_ids"] = array_keys($data["delivery_areas1"]);
    }
    $data["is_in_business_hours"] = intval($data["is_in_business"]);
    if (isset($data["business_hours"])) {
        if ($data["is_in_business"] == 1) {
            if ($data["rest_can_order"] == 1) {
                $data["is_in_business_hours"] = true;
            } else {
                $data["is_in_business_hours"] = store_is_in_business_hours($data["business_hours"]);
            }
        }
        if ($data["is_in_business_hours"] && !store_is_in_business_hours($data["business_hours"])) {
            $data["is_rest_reserve"] = 1;
            $rest_order_info = store_rest_start_delivery_time($data);
            if ($rest_order_info["nextday"] == 1) {
                $data["rest_reserve_cn"] = "现在预定，最早明天" . $rest_order_info["delivery_time"] . "开始配餐";
            } else {
                $data["rest_reserve_cn"] = "现在预定，最早明天" . $rest_order_info["delivery_time"] . "开始配餐";
            }
        }
        $hour = array();
        foreach ($data["business_hours"] as $li) {
            if (!is_array($li)) {
                continue;
            }
            $hour[] = (string) $li["s"] . "~" . $li["e"];
        }
        $data["business_hours_cn"] = implode(",", $hour);
    }
    if (isset($data["score"])) {
        $data["score_cn"] = round($data["score"] / 5, 2) * 100;
        $data["score"] = floatval($data["score"]);
    }
    if (isset($data["delivery_fee_mode"])) {
        if ($data["delivery_fee_mode"] == 1) {
            $data["order_address_limit"] = 1;
            if (!$data["not_in_serve_radius"] && 0 < $data["serve_radius"]) {
                $data["order_address_limit"] = 2;
            }
        } else {
            if ($data["delivery_fee_mode"] == 2) {
                $data["delivery_price_extra"] = iunserializer($data["delivery_price"]);
                $data["delivery_price"] = $data["delivery_price_extra"]["start_fee"];
                if (!$data["not_in_serve_radius"] && 0 < $data["serve_radius"]) {
                    $data["order_address_limit"] = 2;
                } else {
                    $data["order_address_limit"] = 3;
                }
            } else {
                if ($data["delivery_fee_mode"] == 3) {
                    $data["order_address_limit"] = 4;
                    $price = store_order_condition($data);
                    $data["delivery_price"] = $price["delivery_price"];
                    $data["send_price"] = $price["send_price"];
                }
            }
        }
    }
    if (isset($data["haodian_score"])) {
        $data["haodian_score"] = floatval($data["haodian_score"]);
    }
    if (isset($data["haodian_cid"]) && 0 < $data["haodian_cid"]) {
        $data["haodian_cid_cn"] = pdo_fetchcolumn("select title from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid and id = :id", array("uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":id" => $data["haodian_cid"]));
        $data["haodian_category_cn"] = $data["haodian_cid_cn"];
    }
    if (isset($data["haodian_child_id"]) && 0 < $data["haodian_child_id"]) {
        $data["haodian_child_id_cn"] = pdo_fetchcolumn("select title from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid and id = :id", array("uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":id" => $data["haodian_child_id"]));
        $data["haodian_category_cn"] .= "-" . $data["haodian_child_id_cn"];
    }
    if (isset($data["data"])) {
        $data["data"] = iunserializer($data["data"]);
        if (!empty($data["data"]["shopSign"])) {
            $data["data"]["shopSign"] = tomedia($data["data"]["shopSign"]);
        }
        $data["service_titles"] = array("takeout" => "点外卖", "tangshi" => "扫码点餐", "assign" => "排号", "reserve" => "预定", "paybill" => "当面付");
        if (!empty($data["data"]["service_titles"])) {
            $data["service_titles"] = array_merge($data["service_titles"], $data["data"]["service_titles"]);
        }
        $data["pindan_status"] = 1;
        if (!empty($data["data"]["pindan"]) && isset($data["data"]["pindan"]["pindan_status"])) {
            $data["pindan_status"] = $data["data"]["pindan"]["pindan_status"];
        }
        if (empty($data["data"]["cn"])) {
            $data["data"]["cn"] = array("box_price" => "餐盒费", "pack_fee" => "包装费");
        }
        $data["cn"] = $data["data"]["cn"];
        if (!empty($data["data"]["zhunshibao"]) && $data["data"]["zhunshibao"]["status"] == 1) {
            if ($data["data"]["zhunshibao"]["fee_type"] == 1) {
                $rule_cn = "";
                if (!empty($data["data"]["zhunshibao"]["rule"])) {
                    foreach ($data["data"]["zhunshibao"]["rule"] as $val) {
                        $rule_cn .= "延误" . $val["time"] . "分钟,赔" . $val["fee"] . "元";
                    }
                }
                if (!empty($rule_cn)) {
                    $rule_cn = rtrim($rule_cn, ",");
                    $rule_cn = "骑手送达" . $rule_cn;
                }
                $data["data"]["zhunshibao"]["rule_cn"] = $rule_cn;
            }
            $data["zhunshibao_agreement"] = get_config_text("zhunshibao:agreement");
        }
        if (empty($data["data"]["order_form"])) {
            $data["data"]["order_form"] = array("person_num" => "1");
        }
    }
    if (isset($data["haodian_data"])) {
        $data["haodian_tags"] = array();
        if (!empty($data["haodian_data"]["tags"])) {
            $data["haodian_tags"] = $data["haodian_data"]["tags"];
        }
    }
    if (isset($data["menu"])) {
        $data["menu"] = json_decode(base64_decode($data["menu"]), true);
        if (!empty($data["menu"]["data"])) {
            foreach ($data["menu"]["data"]["data"] as &$val) {
                if (empty($val["img"])) {
                    continue;
                }
                $val["img"] = tomedia($val["img"]);
            }
        }
    }
    return $data;
}
function store_manager($sid)
{
    global $_W;
    $perm = pdo_get("tiny_wmall_store_clerk", array("uniacid" => $_W["uniacid"], "sid" => $sid, "role" => "manager"));
    $clerk = array();
    if (!empty($perm)) {
        $clerk = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "id" => $perm["clerk_id"]));
    }
    return $clerk;
}
function store_fetchall($field = array())
{
    global $_W;
    $field_str = "*";
    if (!empty($field)) {
        $field_str = implode(",", $field);
    }
    $data = pdo_fetchall("SELECT " . $field_str . " FROM " . tablename("tiny_wmall_store") . " WHERE uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    if (!empty($data)) {
        $se_fileds = array("thumbs", "sns", "mobile_verify", "payment", "business_hours", "thumbs", "remind_reply", "comment_reply", "wechat_qrcode", "custom_url");
        $foreach_fileds = array_merge($se_fileds, array("score"));
        $intersect = array_intersect($field, $foreach_fileds);
        if (!empty($intersect)) {
            foreach ($data as &$row) {
                foreach ($se_fileds as $se_filed) {
                    if (isset($row[$se_filed])) {
                        if ($se_filed != "thumbs") {
                            $row[$se_filed] = (array) iunserializer($row[$se_filed]);
                        } else {
                            $row[$se_filed] = iunserializer($row[$se_filed]);
                        }
                    }
                }
                if (isset($row["business_hours"])) {
                    $row["is_in_business_hours"] = intval($row["is_in_business"]);
                    if ($row["is_in_business"] == 1) {
                        if ($row["rest_can_order"] == 1) {
                            $row["is_in_business_hours"] = true;
                        } else {
                            $row["is_in_business_hours"] = store_is_in_business_hours($row["business_hours"]);
                        }
                    }
                    $hour = array();
                    foreach ($row["business_hours"] as $li) {
                        $hour[] = (string) $li["s"] . "~" . $li["e"];
                    }
                    $row["business_hours_cn"] = implode(",", $hour);
                }
                if (isset($row["score"])) {
                    $row["score_cn"] = round($row["score"] / 5, 2) * 100;
                }
            }
        }
    }
    return $data;
}
function store_fetchall_category($type = "all", $filter = array())
{
    global $_W;
    global $_GPC;
    $condition = " where uniacid = :uniacid and agentid = :agentid and status = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_store_category") . $condition . " order by displayorder desc", $params, "id");
    if (!empty($data)) {
        if ($filter["store_num"] == 1) {
            $stores = pdo_fetchall("select cate_parentid1, cate_childid1, cate_parentid2, cate_childid2 from " . tablename("tiny_wmall_store") . $condition, $params);
        }
        foreach ($data as &$da) {
            $store_num = 0;
            $da["thumb"] = tomedia($da["thumb"]);
            $da["is_sys"] = 0;
            if (empty($da["link"]) && empty($da["wxapp_link"])) {
                $da["is_sys"] = 1;
                $da["link"] = imurl("wmall/home/search", array("cid" => $da["id"], "order" => $_GPC["order"], "dis" => $_GPC["dis"]));
            }
            if (empty($da["wxapp_link"])) {
                $da["wxapp_link"] = "pages/home/category?cid=" . $da["id"];
            }
            if ($filter["is_sys"] == 1 && empty($da["is_sys"])) {
                unset($data[$da["id"]]);
                continue;
            }
            if ($filter["store_num"] == 1) {
                if (!empty($stores)) {
                    foreach ($stores as $val) {
                        if (in_array($da["id"], $val)) {
                            $store_num++;
                        }
                    }
                }
                $da["store_num"] = $store_num;
            }
            if ($type == "parent_child") {
                if (!empty($da["parentid"])) {
                    $config_mall = $_W["we7_wmall"]["config"]["mall"];
                    if ($config_mall["store_use_child_category"] == 1) {
                        $data[$da["parentid"]]["child"][] = $da;
                    }
                    unset($data[$da["id"]]);
                }
            } else {
                if ($type == "parent&child") {
                    $da["name"] = $da["title"];
                    if (empty($da["parentid"])) {
                        $parent[$da["id"]] = $da;
                    } else {
                        $child[$da["parentid"]][$da["id"]] = $da;
                    }
                }
            }
        }
        if ($type == "parent&child") {
            unset($data);
            $data = array("parent" => $parent, "child" => $child);
        }
    }
    return $data;
}
function store_fetch_category()
{
    global $_W;
    global $_GPC;
    $cid = intval($_GPC["cid"]);
    $category = pdo_get("tiny_wmall_store_category", array("uniacid" => $_W["uniacid"], "id" => $cid, "status" => 1));
    if (!empty($category)) {
        $category["thumb"] = tomedia($category["thumb"]);
        if (!empty($category["nav"]) && $category["nav_status"] == 1) {
            $category["nav"] = iunserializer($category["nav"]);
            foreach ($category["nav"] as &$value) {
                $value["thumb"] = tomedia($value["thumb"]);
            }
        }
        if (!empty($category["slide"]) && $category["slide_status"] == 1) {
            $category["slide"] = iunserializer($category["slide"]);
            array_sort($category["slide"], "displayorder", SORT_DESC);
            foreach ($category["slide"] as &$v) {
                $v["thumb"] = tomedia($v["thumb"]);
            }
        }
        $config_mall = $_W["we7_wmall"]["config"]["mall"];
        if ($category["parentid"] == 0 && $config_mall["store_use_child_category"] == 1) {
            $category["child"] = pdo_fetchall("select id, parentid, thumb, title from" . tablename("tiny_wmall_store_category") . " where uniacid = :uniacid and parentid = :parentid order by displayorder desc,id asc", array(":uniacid" => $_W["uniacid"], ":parentid" => $category["id"]), "id");
            if (!empty($category["child"])) {
                foreach ($category["child"] as &$v) {
                    $v["thumb"] = tomedia($v["thumb"]);
                }
            } else {
                unset($category["child"]);
            }
        }
    }
    return $category;
}
function store_fetch_activity($sid, $type = array())
{
    global $_W;
    $condition = " where uniacid = :uniacid and sid = :sid and status = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    if (!empty($type)) {
        $type = implode("','", $type);
        $type = "'" . $type . "'";
        $condition .= " and type in (" . $type . ")";
    }
    $condition .= " order by displayorder desc";
    $data = pdo_fetchall("SELECT title,type,data FROM " . tablename("tiny_wmall_store_activity") . $condition, $params, "type");
    $activity = array("num" => 0, "items" => "", "labels" => "");
    if (!empty($data)) {
        $activity["num"] = count($data);
        $activity["items"] = $data;
        foreach ($data as $da) {
            if ($da["type"] == "discount") {
                $discount = iunserializer($da["data"]);
                foreach ($discount as $dis) {
                    $activity["labels"][] = array("title" => (string) $dis["condition"] . "减" . $dis["back"], "class" => "tag tag-danger");
                }
            } else {
                if ($da["type"] == "grant") {
                    $activity["labels"][] = array("title" => "满赠", "class" => "tag tag-danger");
                } else {
                    if ($da["type"] == "mallNewMember") {
                        $mallNewMember = iunserializer($da["data"]);
                        $activity["labels"][] = array("title" => "首单减" . $mallNewMember["back"], "class" => "tag tag-danger");
                    } else {
                        if ($da["type"] == "newMember") {
                            $newMember = iunserializer($da["data"]);
                            $activity["labels"][] = array("title" => "新客减" . $newMember["back"], "class" => "tag tag-danger");
                        } else {
                            if ($da["type"] == "couponCollect") {
                                $activity["labels"][] = array("title" => "有机会领券", "class" => "tag tag-danger");
                            } else {
                                if ($da["type"] == "couponGrant") {
                                    $couponGrant = iunserializer($da["data"]);
                                    $activity["labels"][] = array("title" => "返" . $couponGrant["discount"] . "元券", "class" => "tag tag-danger");
                                } else {
                                    if ($da["type"] == "bargain") {
                                        $activity["labels"][] = array("title" => $da["title"], "class" => "tag tag-danger");
                                    } else {
                                        if ($da["type"] == "deliveryFeeDiscount") {
                                            $activity["labels"][] = array("title" => "可减配送费", "class" => "tag tag-danger");
                                        } else {
                                            if ($da["type"] == "selfPickup" || $da["type"] == "selfDelivery") {
                                                $activity["labels"][] = array("title" => "自提优惠", "class" => "tag tag-danger");
                                            } else {
                                                if ($da["type"] == "cashGrant") {
                                                    $activity["labels"][] = array("title" => "返余额", "class" => "tag tag-danger");
                                                } else {
                                                    if ($da["type"] == "svipRedpacket") {
                                                        $da["data"] = iunserializer($da["data"]);
                                                        $activity["labels"][] = array("title" => (string) $da["data"]["discount"] . "元无门槛红包", "class" => "tag tag-svip");
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
        $activity["labels_num"] = count($activity["labels"]);
    }
    return $activity;
}
function store_is_in_business_hours($business_hours)
{
    if (!is_array($business_hours)) {
        return true;
    }
    $business_hours_flag = false;
    foreach ($business_hours as $li) {
        if (!is_array($li) || empty($li["s"]) || empty($li["e"])) {
            continue;
        }
        $starttime = strtotime($li["s"]);
        $endtime = strtotime($li["e"]);
        $cross_night = 0;
        if ($endtime <= $starttime) {
            $cross_night = 1;
        }
        $now = TIMESTAMP;
        if (!$cross_night && $starttime <= $now && $now <= $endtime || $cross_night && ($starttime <= $now || $now <= $endtime)) {
            $business_hours_flag = true;
            break;
        }
    }
    return $business_hours_flag;
}
function store_business_hours_init($sid = 0)
{
    global $_W;
    if (0 < $sid) {
        $store = store_fetch($sid, array("business_hours", "is_in_business", "rest_can_order"));
        $is_rest = 1;
        if ($store["is_in_business"]) {
            if ($store["rest_can_order"] == 1) {
                $is_rest = 0;
            } else {
                if (store_is_in_business_hours($store["business_hours"])) {
                    $is_rest = 0;
                }
            }
        }
        pdo_update("tiny_wmall_store", array("is_rest" => $is_rest), array("uniacid" => $_W["uniacid"], "id" => $sid));
        mlog(2012, $sid);
    } else {
        $stores = pdo_fetchall("select id,business_hours,is_in_business,rest_can_order from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
        if (!empty($stores)) {
            foreach ($stores as $row) {
                $row["business_hours"] = iunserializer($row["business_hours"]);
                $is_rest = 1;
                if ($row["is_in_business"]) {
                    if ($row["rest_can_order"] == 1) {
                        $is_rest = 0;
                    } else {
                        if (store_is_in_business_hours($row["business_hours"])) {
                            $is_rest = 0;
                        }
                    }
                }
                pdo_update("tiny_wmall_store", array("is_rest" => $is_rest), array("uniacid" => $_W["uniacid"], "id" => $row["id"]));
                mlog(2012, $row["id"]);
            }
        }
    }
    return true;
}
function store_fetchall_goods_category($store_id, $status = "-1", $ignore_bargain = true, $type = "parent", $category_type = "all")
{
    global $_W;
    $condition = " where uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $store_id);
    if ($type == "parent") {
        $condition .= " and parentid = 0";
    }
    if (0 <= $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_goods_category") . $condition . " order by displayorder desc, id asc", $params, "id");
    if ($type == "parent" && $category_type == "available") {
        foreach ($categorys as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
            if (!empty($val["is_showtime"])) {
                $now_week = date("N", TIMESTAMP);
                $start_time = intval(strtotime($val["start_time"]));
                $end_time = intval(strtotime($val["end_time"]));
                $week = explode(",", $val["week"]);
                if ($end_time <= $start_time) {
                    $end_time = $end_time + 86400;
                }
                if (!empty($val["week"]) && !in_array($now_week, $week) || !empty($start_time) && (TIMESTAMP < $start_time || $end_time < TIMESTAMP)) {
                    unset($categorys[$val["id"]]);
                }
            }
        }
    }
    if ($type == "all") {
        foreach ($categorys as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
            if (!empty($val["parentid"])) {
                $categorys[$val["parentid"]]["child"][] = $val;
                unset($categorys[$val["id"]]);
            } else {
                if ($category_type == "available" && !empty($val["is_showtime"])) {
                    $now_week = date("N", TIMESTAMP);
                    $start_time = intval(strtotime($val["start_time"]));
                    $end_time = intval(strtotime($val["end_time"]));
                    $week = explode(",", $val["week"]);
                    if ($end_time <= $start_time) {
                        $end_time = $end_time + 86400;
                    }
                    if (!empty($val["week"]) && !in_array($now_week, $week) || !empty($start_time) && (TIMESTAMP < $start_time || $end_time < TIMESTAMP)) {
                        unset($categorys[$val["id"]]);
                    }
                }
            }
        }
    } else {
        if ($type == "other") {
            foreach ($categorys as &$value) {
                $value["name"] = $value["title"];
                if (empty($value["parentid"])) {
                    if ($category_type == "available" && !empty($value["is_showtime"])) {
                        $now_week = date("N", TIMESTAMP);
                        $start_time = intval(strtotime($value["start_time"]));
                        $end_time = intval(strtotime($value["end_time"]));
                        $week = explode(",", $value["week"]);
                        if ($end_time <= $start_time) {
                            $end_time = $end_time + 86400;
                        }
                        if (!empty($value["week"]) && !in_array($now_week, $week) || !empty($start_time) && (TIMESTAMP < $start_time || $end_time < TIMESTAMP)) {
                            unset($categorys[$value["id"]]);
                        }
                    }
                    $parent[$value["id"]] = $value;
                } else {
                    $child[$value["parentid"]][$value["id"]] = $value;
                }
            }
            unset($categorys);
            $categorys["parent"] = $parent;
            $categorys["child"] = $child;
        }
    }
    if (!$ignore_bargain) {
        $condition = " where uniacid = :uniacid and sid = :sid and status = :status order by id limit 2";
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $store_id, ":status" => 1);
        $bargains = pdo_fetchall("select id, title, content, thumb from " . tablename("tiny_wmall_activity_bargain") . $condition, $params, "id");
        if (!empty($bargains)) {
            foreach ($bargains as &$bargain) {
                array_unshift($categorys, array("id" => "bargain_" . $bargain["id"], "title" => $bargain["title"], "bargain_id" => $bargain["id"], "decoration" => $bargain["content"], "thumb" => tomedia($bargain["thumb"])));
            }
        }
    }
    foreach ($categorys as &$row) {
        $row["total"] = 0;
        if (!isset($row["child"]) && defined("IN_VUE")) {
            $row["child"] = array();
        }
    }
    return $categorys;
}
function get_goods_child_category($sid, $parentid)
{
    global $_W;
    global $_GPC;
    if (empty($parentid)) {
        $parentid = intval($_GPC["parentid"]);
    }
    $child_category = pdo_fetchall("select id,title from" . tablename("tiny_wmall_goods_category") . "where uniacid = :uniacid and sid = :sid and parentid = :parentid and status = 1 order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":parentid" => $parentid));
    return $child_category;
}
function store_fetch_goods($id, $field = array("basic", "options"))
{
    global $_W;
    $goods = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($goods)) {
        return error(-1, "商品不存在或已删除");
    }
    $goods["data"] = iunserializer($goods["data"]);
    $goods["thumb_"] = tomedia($goods["thumb"]);
    if (in_array("options", $field) && $goods["is_options"]) {
        $goods["options"] = pdo_getall("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "goods_id" => $id), array(), "id");
        $goods["options_haskey"] = $goods["options"];
        $goods["options"] = array_values($goods["options"]);
    }
    return $goods;
}
function store_comment_stat($sid, $update = true)
{
    global $_W;
    $stat = array();
    $stat["goods_quality"] = round(pdo_fetchcolumn("select avg(goods_quality) from " . tablename("tiny_wmall_order_comment") . " where uniacid = :uniacid and sid = :sid and status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid)), 1);
    $stat["delivery_service"] = round(pdo_fetchcolumn("select avg(delivery_service) from " . tablename("tiny_wmall_order_comment") . " where uniacid = :uniacid and sid = :sid and status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid)), 1);
    $stat["score"] = round(($stat["goods_quality"] + $stat["delivery_service"]) / 2, 1);
    if ($update) {
        pdo_update("tiny_wmall_store", array("score" => $stat["score"]), array("uniacid" => $_W["uniacid"], "id" => $sid));
    }
    $stat["goods_quality_star"] = score_format($stat["goods_quality"]);
    $stat["delivery_service_star"] = score_format($stat["delivery_service"]);
    return $stat;
}
function store_status()
{
    $data = array(array("css" => "label label-default", "text" => "隐藏中", "color" => ""), array("css" => "label label-success", "text" => "显示中"), array("css" => "label label-info", "text" => "审核中"), array("css" => "label label-danger", "text" => "审核未通过"), array("css" => "label label-danger", "text" => "回收站"));
    return $data;
}
function store_account($sid, $fileds = array())
{
    global $_W;
    $account = pdo_get("tiny_wmall_store_account", array("uniacid" => $_W["uniacid"], "sid" => $sid), $fileds);
    if (!empty($account)) {
        $se_fileds = array("bank", "wechat", "alipay", "fee_goods", "fee_takeout", "fee_selfDelivery", "fee_instore", "fee_paybill", "fee_eleme", "fee_meituan", "fee_gohome");
        foreach ($se_fileds as $se_filed) {
            if (isset($account[$se_filed])) {
                $account[$se_filed] = (array) iunserializer($account[$se_filed]);
            }
        }
    }
    return $account;
}
function store_update_account($sid, $fee, $trade_type, $extra, $remark = "")
{
    global $_W;
    $account = pdo_get("tiny_wmall_store_account", array("uniacid" => $_W["uniacid"], "sid" => $sid));
    if (empty($account)) {
        return error(-1, "账户不存在");
    }
    if (($trade_type == 1 || $trade_type == 8) && !empty($extra)) {
        $is_exist = pdo_get("tiny_wmall_store_current_log", array("uniacid" => $_W["uniacid"], "sid" => $sid, "trade_type" => $trade_type, "extra" => $extra), array("id"));
        if (!empty($is_exist)) {
            return error(-1, "订单已经入账");
        }
    }
    $hash = md5((string) $_W["uniacid"] . "-" . $sid . "-" . $trade_type . "-" . $extra);
    if ($trade_type == 3 || $trade_type == 7) {
        $hash = md5((string) $_W["uniacid"] . "-" . $sid . "-" . $trade_type . "-" . $fee . TIMESTAMP);
    }
    $now_amount = $account["amount"] + $fee;
    $log = array("uniacid" => $_W["uniacid"], "agentid" => $account["agentid"], "sid" => $sid, "trade_type" => $trade_type, "extra" => $extra, "fee" => $fee, "amount" => $now_amount, "addtime" => TIMESTAMP, "hash" => $hash, "remark" => $remark);
    pdo_insert("tiny_wmall_store_current_log", $log);
    $id = pdo_insertid();
    if (in_array($trade_type, array(3, 5, 7)) && empty($extra)) {
        mlog(2005, $id, $remark);
    }
    if (!empty($id)) {
        $status = pdo_update("tiny_wmall_store_account", array("amount" => $now_amount), array("uniacid" => $_W["uniacid"], "sid" => $sid));
        if ($status === false) {
            $account_new = pdo_get("tiny_wmall_store_account", array("uniacid" => $_W["uniacid"], "sid" => $sid));
            slog("storeaccount", "商户账户变动失败", array(), "商户id:" . $sid . ",变动前金额:" . $account["amount"] . ",变动金额:" . $fee . ",变动后金额" . $now_amount . ",实际变动后金额：" . $account_new["amount"]);
        }
    }
    return true;
}
function store_getcash_status()
{
    $data = array("1" => array("css" => "label label-success", "text" => "提现成功"), "2" => array("css" => "label label-danger", "text" => "申请中"), "3" => array("css" => "label label-default", "text" => "提现失败"));
    return $data;
}
function store_delivery_times($sid, $force_update = false)
{
    global $_W;
    $cache_key = "we7wmall_store_delivery_times|" . $sid . "|" . $_W["uniacid"];
    if (!$force_update && 0) {
        $data = cache_read($cache_key);
        if (!empty($data) && TIMESTAMP < $data["updatetime"]) {
            return $data;
        }
    }
    $store = store_fetch($sid, array("id", "delivery_reserve_days", "delivery_within_days", "delivery_time", "delivery_times", "delivery_fee_mode", "delivery_price"));
    $days = array();
    $totaytime = strtotime(date("Y-m-d"));
    $times = $store["delivery_times"];
    $last_time = $totaytime + 79200;
    if (!empty($times)) {
        $last_time = array_pop($times);
        $last_time = explode(":", $last_time["end"]);
        $last_time = mktime($last_time[0], $last_time[1]);
    }
    $predict_timestamp = TIMESTAMP + 60 * $store["delivery_time"];
    if ($last_time < $predict_timestamp) {
        $totaytime = $totaytime + 86400;
        $nextday = date("m-d", $totaytime);
    }
    if (0 < $store["delivery_reserve_days"]) {
        $days[] = date("m-d", $totaytime + $store["delivery_reserve_days"] * 86400);
    } else {
        if (0 < $store["delivery_within_days"]) {
            for ($i = 0; $i <= $store["delivery_within_days"]; $i++) {
                $days[] = date("m-d", $totaytime + $i * 86400);
            }
        } else {
            $days[] = date("m-d", $totaytime);
        }
    }
    $mktimes = array("month" => date("m", $totaytime), "day" => date("d", $totaytime));
    $times = $store["delivery_times"];
    $timestamp = array();
    if (!empty($times)) {
        foreach ($times as $key => &$row) {
            if (empty($row["status"])) {
                unset($times[$key]);
                continue;
            }
            if ($store["delivery_fee_mode"] == 1) {
                $row["delivery_price"] = $store["delivery_price"] + $row["fee"];
                $row["delivery_price_cn"] = (string) $row["delivery_price"] . "元配送费";
            } else {
                $row["delivery_price"] = $store["delivery_price"] + $row["fee"];
                $row["delivery_price_cn"] = "配送费" . $row["delivery_price"] . "元起";
            }
            $end = explode(":", $row["end"]);
            $row["timestamp"] = mktime($end[0], $end[1], 0, $mktimes["month"], $mktimes["day"]);
            $timestamp[$key] = $row["timestamp"];
        }
    } else {
        $start = mktime(8, 0, 0, $mktimes["month"], $mktimes["day"]);
        $end = mktime(22, 0, 0, $mktimes["month"], $mktimes["day"]);
        $i = $start;
        while ($i < $end) {
            if ($store["delivery_fee_mode"] == 1) {
                $store["delivery_price_cn"] = (string) $store["delivery_price"] . "元配送费";
            } else {
                $store["delivery_price_cn"] = "配送费" . $store["delivery_price"] . "元起";
            }
            $times[] = array("start" => date("H:i", $i), "end" => date("H:i", $i + 1800), "timestamp" => $i + 1800, "fee" => 0, "delivery_price" => $store["delivery_price"], "delivery_price_cn" => $store["delivery_price_cn"]);
            $timestamp[] = $i + 1800;
            $i += 1800;
        }
    }
    $data = array("nextday" => $nextday, "days" => $days, "times" => $times, "timestamp" => $timestamp, "updatetime" => strtotime(date("Y-m-d")) + 86400, "reserve" => 0 < $store["delivery_reserve_days"] ? 1 : 0);
    return $data;
}
function store_rest_start_delivery_time($store)
{
    $delivery_time = "";
    foreach ($store["business_hours"] as $hours) {
        $starthour = strtotime($hours["s"]);
        if (TIMESTAMP < $starthour) {
            $delivery_time = $hours["s"];
            break;
        }
    }
    if (empty($delivery_time)) {
        $delivery_time = $store["business_hours"][0]["s"];
        $delivery_time_cn = "预定明天" . $delivery_time . "开始配送";
        $nextday = 1;
    } else {
        $delivery_time_cn = "预定" . $delivery_time . "开始配送";
    }
    $data = array("delivery_time" => $delivery_time, "delivery_time_cn" => $delivery_time_cn, "nextday" => $nextday);
    return $data;
}
function store_delivery_modes()
{
    $data = array("1" => array("css" => "label label-danger", "text" => "店内配送员", "color" => ""), "2" => array("css" => "label label-success", "text" => "平台配送员"));
    return $data;
}
function store_fetchall_by_condition($type = "hot", $option = array())
{
    global $_W;
    if (empty($option["limit"])) {
        $option["limit"] = 6;
    }
    if (empty($option["extra_type"])) {
        $option["extra_type"] = "all";
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid and status = 1 and is_waimai = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    if (isset($option["is_rest"])) {
        $condition .= " and is_rest = :is_rest";
        $params[":is_rest"] = intval($option["is_rest"]);
    }
    if ($type == "hot") {
        $stores = pdo_fetchall("select id,title,forward_mode,forward_url from " . tablename("tiny_wmall_store") . $condition . " order by click desc, displayorder desc limit 4", $params);
    } else {
        if ($type == "recommend") {
            $condition .= " and is_recommend = 1 and position = 1";
            $stores = pdo_fetchall("select id,title,logo,content,business_hours,delivery_fee_mode,delivery_price,delivery_areas,send_price,delivery_time,forward_mode,forward_url,score,location_y,location_x,sailed,is_rest from " . tablename("tiny_wmall_store") . $condition . " order by is_rest asc, displayorder desc limit " . $option["limit"], $params);
        }
    }
    if (!empty($stores)) {
        foreach ($stores as &$row) {
            $row["logo"] = tomedia($row["logo"]);
            $row["scores_original"] = $row["score"];
            $row["scores"] = score_format($row["score"]);
            $row["url"] = store_forward_url($row["id"], $row["forward_mode"], $row["forward_url"], $_W["channel"]);
            if ($option["extra_type"] == "all") {
                $row["activity"] = store_fetch_activity($row["id"]);
                $row["activity"]["items"] = array_values($row["activity"]["items"]);
                if ($row["delivery_fee_mode"] == 2) {
                    $row["delivery_price"] = iunserializer($row["delivery_price"]);
                    $row["delivery_price"] = $row["delivery_price"]["start_fee"];
                } else {
                    if ($row["delivery_fee_mode"] == 3) {
                        $row["delivery_areas"] = iunserializer($row["delivery_areas"]);
                        if (!is_array($row["delivery_areas"])) {
                            $row["delivery_areas"] = array();
                        }
                        $price = store_order_condition($row);
                        $row["delivery_price"] = $price["delivery_price"];
                        $row["send_price"] = $price["send_price"];
                    }
                }
            }
        }
    }
    return $stores;
}
function store_forward_url($sid, $forward_mode, $forward_url = "", $channel = "")
{
    global $_W;
    if (empty($channel)) {
        $channel = $_W["channel"];
    }
    if ($channel == "wechat") {
        if ($forward_mode == 0) {
            $url = imurl("wmall/store/goods", array("sid" => $sid));
        } else {
            if ($forward_mode == 1) {
                $url = imurl("wmall/store/index", array("sid" => $sid));
            } else {
                if ($forward_mode == 3) {
                    $url = imurl("wmall/store/assign", array("sid" => $sid));
                } else {
                    if ($forward_mode == 4) {
                        $url = imurl("wmall/store/reserve", array("sid" => $sid));
                    } else {
                        if ($forward_mode == 6) {
                            $url = imurl("wmall/store/paybill", array("sid" => $sid));
                        } else {
                            if ($forward_mode == 5) {
                                $url = $forward_url;
                            }
                        }
                    }
                }
            }
        }
    } else {
        $url = "/pages/store/goods?sid=" . $sid;
        if ($forward_mode == 0) {
            $url = "/pages/store/goods?sid=" . $sid;
        } else {
            if ($forward_mode == 1) {
                if (check_plugin_perm("wxapp")) {
                    $url = "/pages/store/home?sid=" . $sid;
                } else {
                    $url = "/pages/store/index?sid=" . $sid;
                }
            } else {
                if ($forward_mode == 4) {
                    $url = "/tangshi/pages/reserve/index?sid=" . $sid;
                } else {
                    if ($forward_mode == 6) {
                        $url = "/pages/store/paybill?sid=" . $sid;
                    }
                }
            }
        }
    }
    return $url;
}
function store_order_serial_sn($store_id)
{
    global $_W;
    $serial_sn = pdo_fetchcolumn("select serial_sn from" . tablename("tiny_wmall_order") . " where uniacid = :uniacid and sid = :sid and order_plateform = :order_plateform and addtime > :addtime order by serial_sn desc", array(":uniacid" => $_W["uniacid"], ":sid" => $store_id, ":order_plateform" => "we7_wmall", ":addtime" => strtotime(date("Y-m-d"))));
    $serial_sn = intval($serial_sn) + 1;
    return $serial_sn;
}
function store_check()
{
    global $_W;
    global $_GPC;
    if (!defined("IN_MOBILE")) {
        if (!empty($_GPC["_sid"])) {
            $sid = intval($_GPC["_sid"]);
            isetcookie("__sid", $sid, 86400);
        } else {
            $sid = intval($_GPC["__sid"]);
        }
    } else {
        $sid = intval($_GPC["sid"]);
    }
    if (!defined("IN_MOBILE") && $_W["role"] != "manager" && empty($_W["isfounder"]) && $_W["we7_wmall"]["store"]["id"] != $sid) {
        message("您没有该门店的管理权限", "", "error");
    }
    $store = pdo_fetch("SELECT id, title, status, pc_notice_status, delivery_mode FROM " . tablename("tiny_wmall_store") . " WHERE uniacid = :aid AND id = :id", array(":aid" => $_W["uniacid"], ":id" => $sid));
    if (empty($store)) {
        if (!defined("IN_MOBILE")) {
            message("门店信息不存在或已删除", "", "error");
        }
        exit;
    }
    $store["manager"] = pdo_get("tiny_wmall_clerk", array("uniacid" => $_W["uniacid"], "sid" => $store["id"], "role" => "manager"));
    $store["account"] = pdo_get("tiny_wmall_store_account", array("uniacid" => $_W["uniacid"], "sid" => $store["id"]));
    $_W["we7_wmall"]["store"] = $store;
    return $store;
}
function store_serve_fee_items()
{
    return array("yes" => array("price" => "商品费用", "box_price" => "餐盒费", "pack_fee" => "包装费", "delivery_fee" => "配送费"), "no" => array("store_discount_fee" => "商户活动补贴"));
}
function is_in_store_radius($sid, $lnglat, $area_id = 0)
{
    global $_W;
    if (is_array($sid)) {
        $store = $sid;
    }
    if (empty($store)) {
        $store = store_fetch($sid, array("location_y", "location_x", "delivery_fee_mode", "delivery_price", "delivery_areas", "delivery_areas1", "serve_radius", "not_in_serve_radius"));
    }
    if (empty($store)) {
        return false;
    }
    $flag = false;
    if ($store["address_type"] == 1) {
        if (!empty($lnglat) && !empty($lnglat["area_id"]) && in_array($lnglat["area_id"], $store["delivery_areas1_ids"])) {
            $flag = true;
        }
    } else {
        if ($store["delivery_fee_mode"] == 1 || $store["delivery_fee_mode"] == 2) {
            if (!$store["not_in_serve_radius"] && 0 < $store["serve_radius"]) {
                if (empty($lnglat[0]) || empty($lnglat[1])) {
                    return false;
                }
                $dist = distanceBetween($lnglat[0], $lnglat[1], $store["location_y"], $store["location_x"]);
                if ($dist <= $store["serve_radius"] * 1000) {
                    $flag = true;
                }
            } else {
                $flag = true;
            }
        } else {
            if ($store["delivery_fee_mode"] == 3) {
                if (empty($lnglat[0]) || empty($lnglat[1])) {
                    return false;
                }
                if (empty($store["delivery_areas"])) {
                    return false;
                }
                if (!empty($area_id)) {
                    $store["delivery_areas"] = array($store["delivery_areas"][$area_id]);
                }
                foreach ($store["delivery_areas"] as $area) {
                    $flag = isPointInPolygon($area["path"], array($lnglat[0], $lnglat[1]));
                    if ($flag) {
                        break;
                    }
                }
            }
        }
    }
    return $flag;
}
function is_in_store_area($storeOrId, $addressOrId, $area_id = 0)
{
    global $_W;
    global $_GPC;
    if (is_array($storeOrId)) {
        $store = $storeOrId;
    } else {
        $store = store_fetch($storeOrId, array("location_y", "location_x", "delivery_fee_mode", "delivery_price", "delivery_areas", "delivery_areas1", "serve_radius", "not_in_serve_radius"));
    }
    if (empty($store)) {
        return false;
    }
    if (is_array($addressOrId)) {
        $address = $addressOrId;
    } else {
        $address = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_address") . " WHERE uniacid = :uniacid AND id = :id AND type = 1", array(":uniacid" => $_W["uniacid"], ":id" => $addressOrId));
    }
    if (empty($address)) {
        return false;
    }
    $flag = false;
    if ($store["address_type"] == 1) {
        $flag = is_in_store_radius($store, array("area_id" => $address["area_id"]));
    } else {
        $flag = is_in_store_radius($store, array($address["location_y"], $address["location_x"]), $area_id);
    }
    return $flag;
}
function store_order_condition($sid, $lnglat = array())
{
    global $_GPC;
    if (is_array($sid)) {
        $store = $sid;
    }
    if (empty($store)) {
        $store = store_fetch($sid, array("location_y", "location_x", "delivery_fee_mode", "delivery_price", "delivery_areas", "delivery_areas1", "delivery_price", "delivery_free_price", "send_price"));
    }
    if (empty($store)) {
        return error(-1, "门店不存在");
    }
    $price = array("send_price" => $store["send_price"], "delivery_price" => $store["delivery_price"], "delivery_free_price" => $store["delivery_free_price"]);
    if (empty($store["address_type"]) && $store["delivery_fee_mode"] == 3) {
        if (empty($lnglat)) {
            if (0 < $_GPC["address_id"]) {
                $address = member_fetch_address($_GPC["address_id"]);
                $lnglat = array($address["location_y"], $address["location_x"]);
            } else {
                $lnglat = array($_GPC["__lng"], $_GPC["__lat"]);
            }
        }
        $delivery_price_arr = array();
        $send_price_arr = array();
        $delivery_free_price_arr = array();
        foreach ($store["delivery_areas"] as $key => $area) {
            $in = isPointInPolygon($area["path"], $lnglat);
            if ($in) {
                if ($_GPC["op"] == "goods") {
                    isetcookie("_guess_area", $key, 300);
                }
                $price["delivery_price"] = $area["delivery_price"];
                $price["send_price"] = $area["send_price"];
                $price["delivery_free_price"] = $area["delivery_free_price"];
                break;
            }
            $delivery_price_arr[] = $area["delivery_price"];
            $send_price_arr[] = $area["send_price"];
            $delivery_free_price_arr[] = $area["delivery_free_price"];
        }
        if (!$in) {
            $price["delivery_price"] = min($delivery_price_arr);
            $price["send_price"] = min($send_price_arr);
            $price["delivery_free_price"] = min($delivery_free_price_arr);
        }
    }
    return $price;
}
function store_notice_stat($clerk_id = 0)
{
    global $_W;
    if (empty($clerk_id)) {
        $clerk_id = $_W["clerk"]["id"];
    }
    $new_id = pdo_fetchcolumn("SELECT notice_id FROM" . tablename("tiny_wmall_notice_read_log") . " WHERE uid = :uid ORDER BY notice_id DESC LIMIT 1", array(":uid" => $clerk_id));
    $new_id = intval($new_id);
    $notices = pdo_fetchall("SELECT id FROM " . tablename("tiny_wmall_notice") . " WHERE status = 1 AND type = :type AND id > :id", array(":type" => "store", ":id" => $new_id));
    if (!empty($notices)) {
        foreach ($notices as &$notice) {
            $insert = array("uid" => $clerk_id, "notice_id" => $notice["id"], "is_new" => 1);
            pdo_insert("tiny_wmall_notice_read_log", $insert);
        }
    }
    $total = intval(pdo_fetchcolumn("SELECT COUNT(*) FROM" . tablename("tiny_wmall_notice_read_log") . " WHERE uid = :uid AND is_new = 1", array(":uid" => $clerk_id)));
    return $total;
}
function store_stat_init($name, $sid = 0, $day = 30)
{
    global $_W;
    $limittime = TIMESTAMP - 86400 * $day;
    $routers = array("sailed" => "count(*) as sailed", "delivery_time" => "avg(delivery_success_time - paytime) as delivery_time, data");
    if (empty($sid)) {
        $orders = pdo_fetchall("select sid, " . $routers[$name] . " from" . tablename("tiny_wmall_order") . "where uniacid = :uniacid and agentid = :agentid and status = 5 and addtime > " . $limittime . " and delivery_success_time > 0 group by sid", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "sid");
        $sids = array();
        if (!empty($orders)) {
            $sids = array_keys($orders);
        }
        $stores = pdo_fetchall("select id,sailed,delivery_time,data from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and agentid = :agentid", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
        foreach ($stores as &$da) {
            if ($name == "delivery_time") {
                $da["data"] = iunserializer($da["data"]);
                if (!empty($da["data"]) && $da["data"]["delivery_time_type"] == 1) {
                    continue;
                }
            }
            $update = array();
            if (in_array($da["id"], $sids)) {
                $value = intval($orders[$da["id"]][$name]);
                if ($name == "delivery_time") {
                    $value = floor($value / 60);
                    $value = min($value, 255);
                }
                $update[$name] = $value;
            } else {
                $update[$name] = 0;
            }
            pdo_update("tiny_wmall_store", $update, array("id" => $da["id"]));
        }
    } else {
        $store = pdo_fetch("select id,sailed,delivery_time,data from" . tablename("tiny_wmall_store") . " where uniacid = :uniacid and agentid = :agentid and id = :id", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":id" => $sid));
        if (empty($store)) {
            return error(-1, "商店不存在");
        }
        if ($name == "delivery_time") {
            $store["data"] = iunserializer($store["data"]);
            if (!empty($store["data"]) && $store["data"]["delivery_time_type"] == 1) {
                return error(-1, "门店预计送达时间计算方式为门店手动设置");
            }
        }
        $orders = pdo_fetch("select sid,{\$routers[\$name]} from" . tablename("tiny_wmall_order") . "where uniacid = :uniacid and agentid = :agentid and status = 5 and addtime > " . $limittime . " and sid = :sid", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":sid" => $sid));
        $update = array();
        if (!empty($orders)) {
            $value = intval($orders[$name]);
            if ($name == "delivery_time") {
                $value = floor($value / 60);
                $value = min($value, 255);
            }
            $update[$name] = $value;
        } else {
            $update[$name] = 0;
        }
        pdo_update("tiny_wmall_store", $update, array("id" => $sid));
    }
    return true;
}

?>