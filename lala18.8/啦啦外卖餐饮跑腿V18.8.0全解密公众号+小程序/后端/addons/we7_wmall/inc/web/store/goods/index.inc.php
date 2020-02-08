<?php
defined("IN_IA") or exit("Access Denied");
load()->func("communication");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
set_time_limit(0);
if ($ta == "post") {
    $_W["page"]["title"] = "商品编辑";
    load()->func("tpl");
    $id = intval($_GPC["id"]);
    if ($id) {
        $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id = :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id));
        $item["data"] = iunserializer($item["data"]);
        if (empty($item)) {
            imessage("商品不存在或已删除", iurl("store/goods/index/list"), "info");
        }
        if ($item["is_options"]) {
            $item["options"] = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods_options") . " WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC", array(":aid" => $_W["uniacid"], ":goods_id" => $id), "id");
        }
        $item["attrs"] = iunserializer($item["attrs"]);
        if (!empty($item["attrs"])) {
            foreach ($item["attrs"] as &$val) {
                $val["label"] = implode(",", $val["label"]);
            }
        }
        $item["slides"] = iunserializer($item["slides"]);
        if (!empty($item["week"])) {
            $item["week"] = explode(",", $item["week"]);
        }
    } else {
        $item["total"] = -1;
        $item["unitname"] = "份";
    }
    $store_config = $_W["we7_wmall"]["config"]["store"]["settle"];
    $svip_perm = check_plugin_perm("svip");
    $config_goods = $_W["we7_wmall"]["store"]["data"]["goods"];
    if ($_W["ispost"]) {
        $price = floatval($_GPC["price"]);
        if (0 < $id && !$item["is_options"]) {
            mload()->model("goods");
            $result_pricechange = goods_change_price_check($price, $item["price"], $config_goods, $item["data"]);
            if (is_error($result_pricechange)) {
                imessage($result_pricechange, "", "ajax");
            } else {
                $change_price_success = $result_pricechange["message"];
            }
        }
        $data = array("sid" => $sid, "uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "number" => trim($_GPC["number"]), "type" => intval($_GPC["type"]), "price" => floatval($_GPC["price"]), "old_price" => floatval($_GPC["old_price"]), "ts_price" => floatval($_GPC["ts_price"]), "unitname" => trim($_GPC["unitname"]), "unitnum" => 1 < intval($_GPC["unitnum"]) ? intval($_GPC["unitnum"]) : 1, "total" => intval($_GPC["total"]), "total_warning" => intval($_GPC["total_warning"]), "total_update_type" => intval($_GPC["total_update_type"]), "sailed" => intval($_GPC["sailed"]), "status" => intval($_GPC["status"]), "cid" => intval($_GPC["category"]["parentid"]), "child_id" => intval($_GPC["category"]["childid"]), "box_price" => floatval($_GPC["box_price"]), "thumb" => trim($_GPC["thumb"]), "label" => trim($_GPC["label"]), "displayorder" => intval($_GPC["displayorder"]), "content" => trim($_GPC["content"]), "description" => htmlspecialchars_decode($_GPC["description"]), "is_options" => intval($_GPC["is_options"]), "is_hot" => intval($_GPC["is_hot"]), "print_label" => intval($_GPC["print_label"]), "is_showtime" => intval($_GPC["is_showtime"]));
        $data["svip_status"] = 0;
        if ($svip_perm) {
            $data["svip_price"] = floatval($_GPC["svip_price"]);
            if (!empty($data["svip_price"]) && $data["svip_price"] < $data["price"]) {
                $data["svip_status"] = 1;
            }
        }
        if (check_plugin_perm("huangou")) {
            $data["huangou_type"] = intval($_GPC["huangou_type"]);
        }
        $getcategory = $_GPC["category"];
        $data["cid"] = intval($getcategory["parentid"]);
        $data["child_id"] = intval($getcategory["childid"]);
        if (!empty($_GPC["is_showtime"])) {
            if (empty($_GPC["start_time1"]) && empty($_GPC["end_time1"]) && empty($_GPC["start_time2"]) && empty($_GPC["end_time2"]) && empty($_GPC["week"])) {
                imessage(error(-1, "请完善可售时间段信息"), "", "ajax");
            }
            if (!empty($_GPC["start_time1"]) && empty($_GPC["end_time1"]) || !empty($_GPC["start_time2"]) && empty($_GPC["end_time2"]) || empty($_GPC["start_time2"]) && !empty($_GPC["end_time2"]) || empty($_GPC["start_time1"]) && !empty($_GPC["end_time1"])) {
                imessage(error(-1, "请完整填写分类显示时段"), "", "ajax");
            }
            if (!empty($_GPC["start_time1"]) && !empty($_GPC["end_time1"]) && strtotime($_GPC["end_time1"]) <= strtotime($_GPC["start_time1"])) {
                imessage(error(-1, "分类显示时段 起始时间需小于结束时间，请重新设置"), "", "ajax");
            }
            if (!empty($_GPC["start_time2"]) && !empty($_GPC["end_time2"]) && strtotime($_GPC["end_time2"]) <= strtotime($_GPC["start_time2"])) {
                imessage(error(-1, "分类显示时段 起始时间需小于结束时间，请重新设置"), "", "ajax");
            }
            if (!empty($_GPC["end_time1"]) && !empty($_GPC["start_time2"]) && strtotime($_GPC["start_time2"]) < strtotime($_GPC["end_time1"])) {
                imessage(error(-1, "第二个时间段的开始时间必须大于第一个时间段的结束时间"), "", "ajax");
            }
            $data["end_time2"] = "";
            $data["start_time2"] = $data["end_time2"];
            $data["end_time1"] = $data["start_time2"];
            $data["start_time2"] = $data["end_time1"];
            $data["start_time1"] = $data["start_time2"];
            if (!empty($_GPC["start_time1"])) {
                $data["start_time1"] = date("H:i", strtotime($_GPC["start_time1"]));
            }
            if (!empty($_GPC["end_time1"])) {
                $data["end_time1"] = date("H:i", strtotime($_GPC["end_time1"]));
            }
            if (!empty($_GPC["start_time2"])) {
                $data["start_time2"] = date("H:i", strtotime($_GPC["start_time2"]));
            } else {
                $data["start_time2"] = "";
            }
            if (!empty($_GPC["end_time2"])) {
                $data["end_time2"] = date("H:i", strtotime($_GPC["end_time2"]));
            } else {
                $data["start_time2"] = "";
            }
            $week = implode(",", $_GPC["week"]);
            $data["week"] = $week;
        }
        $data["slides"] = array();
        if (!empty($_GPC["slides"])) {
            foreach ($_GPC["slides"] as $slides) {
                if (empty($slides)) {
                    continue;
                }
                $data["slides"][] = $slides;
            }
        }
        $data["slides"] = iserializer($data["slides"]);
        if (!$_W["store"]["data"]["custom_goods_sailed_status"]) {
            unset($data["sailed"]);
        }
        if ($data["is_options"] == 1) {
            $options = array();
            foreach ($_GPC["options"]["name"] as $key => $val) {
                $val = trim($val);
                $price = floatval($_GPC["options"]["price"][$key]);
                if (empty($val) || empty($price)) {
                    continue;
                }
                $options[] = array("id" => intval($_GPC["options"]["id"][$key]), "name" => $val, "price" => $price, "svip_price" => floatval($_GPC["options"]["svip_price"][$key]), "total" => intval($_GPC["options"]["total"][$key]), "total_warning" => intval($_GPC["options"]["total_warning"][$key]), "displayorder" => intval($_GPC["options"]["displayorder"][$key]));
                if (0 < $_GPC["options"]["id"][$key]) {
                    mload()->model("goods");
                    $result_pricechange = goods_change_price_check($price, $item["options"][$_GPC["options"]["id"][$key]]["price"], $config_goods, $item["data"]);
                    if (is_error($result_pricechange)) {
                        imessage($result_pricechange, "", "ajax");
                    } else {
                        if (!$change_price_success) {
                            $change_price_success = $result_pricechange["message"];
                        }
                    }
                }
            }
            if (empty($options)) {
                imessage(error(-1, "没有设置有效的规格项"), "", "ajax");
            }
        }
        if ($change_price_success) {
            $data["data"]["price_updatetime"] = TIMESTAMP;
        }
        $data["attrs"] = array();
        if (!empty($_GPC["attrs"])) {
            foreach ($_GPC["attrs"]["name"] as $key => $row) {
                $row = trim($row);
                if (empty($row)) {
                    continue;
                }
                $labels = $_GPC["attrs"]["label"][$key];
                $labels = array_filter(explode(",", str_replace("，", ",", $labels)), trim);
                if (empty($labels)) {
                    continue;
                }
                $data["attrs"][] = array("name" => $row, "label" => $labels);
            }
        }
        $data["attrs"] = iserializer($data["attrs"]);
        if (!empty($data["data"])) {
            $data["data"] = iserializer($data["data"]);
        }
        if ($id) {
            pdo_update("tiny_wmall_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_goods", $data);
            $id = pdo_insertid();
        }
        $ids = array(0);
        if (!empty($options)) {
            foreach ($options as $val) {
                $option_id = $val["id"];
                if (0 < $option_id) {
                    pdo_update("tiny_wmall_goods_options", $val, array("uniacid" => $_W["uniacid"], "id" => $option_id, "goods_id" => $id));
                } else {
                    $val["uniacid"] = $_W["uniacid"];
                    $val["sid"] = $sid;
                    $val["goods_id"] = $id;
                    pdo_insert("tiny_wmall_goods_options", $val);
                    $option_id = pdo_insertid();
                }
                $ids[] = $option_id;
            }
        }
        $ids = implode(",", $ids);
        pdo_query("delete from " . tablename("tiny_wmall_goods_options") . " WHERE uniacid = :aid AND goods_id = :goods_id and id not in (" . $ids . ")", array(":aid" => $_W["uniacid"], ":goods_id" => $id));
        imessage(error(0, "编辑商品成功"), iurl("store/goods/index/list"), "ajax");
    }
    $print_labels = pdo_fetchall("select * from " . tablename("tiny_wmall_printer_label") . " where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    $categorys = store_fetchall_goods_category($sid, -1, true, "other");
}
if ($ta == "list") {
    $_W["page"]["title"] = "商品列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["titles"][$k]), "price" => floatval($_GPC["prices"][$k]), "svip_price" => floatval($_GPC["svip_prices"][$k]), "box_price" => floatval($_GPC["box_prices"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]), "total" => intval($_GPC["totals"][$k]));
                $data["svip_status"] = 0;
                if (0 < $data["svip_price"]) {
                    $data["svip_status"] = $data["svip_price"] < $data["price"] ? 1 : 0;
                }
                if ($_W["store"]["data"]["custom_goods_sailed_status"] == 1) {
                    $data["sailed"] = intval($_GPC["sailed"][$k]);
                }
                pdo_update("tiny_wmall_goods", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("store/goods/index/list"), "ajax");
    }
    $condition = " where uniacid = :uniacid AND sid = :sid";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":sid"] = $sid;
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND (title LIKE '%" . $_GPC["keyword"] . "%' OR number LIKE '%" . $_GPC["keyword"] . "%')";
    }
    if (!empty($_GPC["cid"])) {
        $cid = $_GPC["cid"];
        $condition .= " AND cid = :cid";
        if (strexists($cid, ":")) {
            $condition .= " AND child_id = :child_id";
            $cid = explode(":", $cid);
            $params[":child_id"] = intval($cid[1]);
            $cid = $cid[0];
        }
        $params[":cid"] = intval($cid);
    }
    $type = intval($_GPC["type"]);
    if (!empty($type)) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
    }
    $status = intval($_GPC["status"]);
    if (!empty($status)) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $total_status = intval(pdo_fetchcolumn("SELECT count(*) FROM " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid AND sid = :sid", array(":uniacid" => $_W["uniacid"], ":sid" => $sid)));
    $total_status_1 = intval(pdo_fetchcolumn("SELECT count(*) FROM " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid AND sid = :sid and status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid)));
    $total_status_2 = intval(pdo_fetchcolumn("SELECT count(*) FROM " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid AND sid = :sid and status = 2", array(":uniacid" => $_W["uniacid"], ":sid" => $sid)));
    $order_by_type = trim($_GPC["order_by_type"]) ? trim($_GPC["order_by_type"]) : "displayorder";
    $order_by = " ORDER BY " . $order_by_type . " DESC, id desc";
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_goods") . $condition, $params);
    if ($order_by_type == "total") {
        $lists = pdo_fetchall("SELECT *, CASE total WHEN -1 THEN 10000000 ELSE total END AS order_by_total FROM " . tablename("tiny_wmall_goods") . (string) $condition . " ORDER BY order_by_total ASC, id desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    } else {
        $lists = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods") . (string) $condition . $order_by . " LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    }
    $pager = pagination($total, $pindex, $psize);
    $categorys = pdo_fetchall("select id,title,parentid from " . tablename("tiny_wmall_goods_category") . " where uniacid = :uniacid and sid = :sid  order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid), "id");
    foreach ($categorys as &$val) {
        if (!empty($val["parentid"])) {
            $categorys[$val["parentid"]]["child"][$val["id"]] = $val;
            unset($categorys[$val["id"]]);
        }
    }
}
if ($ta == "status") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $status = intval($_GPC["status"]);
    foreach ($ids as $id) {
        $id = intval($id);
        $state = pdo_update("tiny_wmall_goods", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
        if ($state === false) {
            imessage(error(-1, "操作失败"), "", "ajax");
        }
    }
    $batch = intval($_GPC["batch"]);
    if (empty($batch)) {
        imessage(error(0, "操作成功"), "", "ajax");
    }
    imessage(error(0, "操作成功"), referer(), "ajax");
}
if ($ta == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        $id = intval($id);
        if (0 < $id) {
            pdo_delete("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            pdo_delete("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $id));
            pdo_delete("tiny_wmall_activity_bargain_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $id));
        }
    }
    imessage(error(0, "删除菜品成功"), "", "ajax");
}
if ($ta == "export") {
    $_W["page"]["title"] = "批量导入商品";
    if ($_W["ispost"]) {
        $file = upload_file($_FILES["file"], "excel");
        if (is_error($file)) {
            imessage(error(-1, $file["message"]), "", "ajax");
        }
        $data = read_excel($file);
        if (is_error($data)) {
            imessage(error(-1, $data["message"]), "", "ajax");
        }
        unset($data[1]);
        if (empty($data)) {
            imessage(error(-1, "没有要导入的数据"), "", "ajax");
        }
        foreach ($data as $da) {
            if (empty($da["0"]) || empty($da["1"])) {
                continue;
            }
            $title = trim($da[1]);
            $category = pdo_fetch("select id, parentid from " . tablename("tiny_wmall_goods_category") . " where uniacid = :uniacid and sid = :sid and title = :title", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":title" => $title));
            $insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => trim($da[0]), "cid" => 0 < $category["parentid"] ? intval($category["parentid"]) : intval($category["id"]), "child_id" => 0 < $category["parentid"] ? intval($category["id"]) : 0, "unitname" => trim($da[2]), "price" => floatval($da[3]), "box_price" => floatval($da[4]), "label" => trim($da[5]), "total" => intval($da[6]), "sailed" => trim($da[7]), "thumb" => trim($da[8]), "displayorder" => intval($da[9]), "description" => trim($da[10]), "number" => intval($da[13]), "type" => intval($da[14]) ? intval($da[14]) : 3, "ts_price" => floatval($da[15]) ? floatval($da[15]) : floatval($da[3]));
            if (!empty($da[12])) {
                $attrs = str_replace("，", ",", $da[12]);
                $attrs = explode(",", $attrs);
                $new_attrs = array();
                if (!empty($attrs)) {
                    foreach ($attrs as $attr) {
                        $attr = array_filter(explode("|", $attr));
                        $name = $attr[0];
                        array_shift($attr);
                        if (empty($name) || empty($attr)) {
                            continue;
                        }
                        $new_attrs[] = array("name" => $name, "label" => $attr);
                    }
                }
                $insert["attrs"] = iserializer($new_attrs);
            }
            pdo_insert("tiny_wmall_goods", $insert);
            $goods_id = pdo_insertid();
            if (!empty($da[11])) {
                $options = str_replace("，", ",", $da[11]);
                $options = explode(",", $options);
                if (!empty($options)) {
                    foreach ($options as $option) {
                        $option = explode("|", $option);
                        if (count($option) == 4) {
                            $insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $goods_id, "name" => trim($option[0]), "price" => floatval($option[1]), "total" => intval($option[2]), "displayorder" => intval($option[3]));
                            pdo_insert("tiny_wmall_goods_options", $insert);
                            $i++;
                        }
                    }
                    if (0 < $i) {
                        pdo_update("tiny_wmall_goods", array("is_options" => 1), array("id" => $goods_id));
                    }
                }
            }
        }
        imessage(error(0, "导入商品成功"), iurl("store/goods/index/list"), "ajax");
    }
}
if ($ta == "copy") {
    $id = intval($_GPC["id"]);
    $goods = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
    if (empty($goods)) {
        imessage(error(-1, "商品不存在或已删除"), "", "ajax");
    }
    if ($goods["is_options"]) {
        $options = pdo_getall("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $id));
    }
    unset($goods["id"]);
    $goods["title"] = $goods["title"] . "-复制";
    pdo_insert("tiny_wmall_goods", $goods);
    $goods_id = pdo_insertid();
    if (!empty($options) && $goods_id) {
        foreach ($options as $option) {
            unset($option["id"]);
            $option["goods_id"] = $goods_id;
            pdo_insert("tiny_wmall_goods_options", $option);
        }
    }
    imessage(error(0, "复制商品成功, 现在进入编辑页"), iurl("store/goods/index/post", array("id" => $goods_id)), "ajax");
}
if ($ta == "eleme_category") {
    $_W["page"]["title"] = "从饿了么导入";
    if ($_W["ispost"]) {
        mload()->model("plugin");
        $_W["_plugin"] = array("name" => "eleme");
        pload()->classs("product");
        $product = new product($sid);
        $results = $product->getShopCategoriesWithChildren();
        if (is_error($results)) {
            imessage(error(-1, $results["message"]), "", "ajax");
        }
        if (!empty($results)) {
            $insert = 0;
            $update = 0;
            $childs2parent = array();
            foreach ($results as &$result) {
                $category = pdo_get("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "elemeId" => $result["id"]));
                if (empty($category)) {
                    $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => removeEmoji($result["name"]), "status" => $result["isValid"], "elemeId" => $result["id"]);
                    pdo_insert("tiny_wmall_goods_category", $data);
                    $parentid = pdo_insertid();
                    $insert++;
                    if (!empty($result["children"])) {
                        foreach ($result["children"] as $val) {
                            $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => removeEmoji($val["name"]), "status" => $val["isValid"], "elemeId" => $val["id"], "parentid" => $parentid);
                            pdo_insert("tiny_wmall_goods_category", $data);
                            $insert++;
                            $childs2parent[] = $val;
                        }
                    }
                } else {
                    $data = array("title" => removeEmoji($result["name"]), "status" => $result["isValid"]);
                    pdo_update("tiny_wmall_goods_category", $data, array("uniacid" => $_W["uniacid"], "sid" => $sid, "elemeId" => $category["elemeId"]));
                    $update++;
                    if (!empty($result["children"])) {
                        foreach ($result["children"] as $val) {
                            $child_category = pdo_get("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "elemeId" => $val["id"]));
                            if (empty($child_category)) {
                                $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => removeEmoji($val["name"]), "status" => $val["isValid"], "elemeId" => $val["id"], "parentid" => $category["id"]);
                                pdo_insert("tiny_wmall_goods_category", $data);
                                $insert++;
                            } else {
                                $data = array("title" => removeEmoji($val["name"]), "status" => $val["isValid"], "parentid" => $category["id"]);
                                pdo_update("tiny_wmall_goods_category", $data, array("uniacid" => $_W["uniacid"], "sid" => $sid, "elemeId" => $val["elemeId"]));
                                $update++;
                            }
                            $childs2parent[] = $val;
                        }
                    }
                }
                unset($result["description"]);
                unset($result["children"]);
            }
            cache_write("we7wmall:eleme:" . $_W["uniacid"] . ":" . $sid, array_merge($results, $childs2parent));
            imessage(error(0, "导入分类成功,本次操作导入" . $insert . "条数据,更新" . $update . "条数据"), iurl("store/goods/index/eleme"), "ajax");
        } else {
            imessage(error(-1, "饿了么暂无分类"), "", "ajax");
        }
    }
}
if ($ta == "eleme") {
    $_W["page"]["title"] = "从饿了么导入";
    mload()->model("plugin");
    $_W["_plugin"] = array("name" => "eleme");
    pload()->classs("product");
    $product = new product($sid);
    $category = cache_read("we7wmall:eleme:" . $_W["uniacid"] . ":" . $sid);
    if ($_W["ispost"]) {
        $categoryId = $_GPC["__input"]["category"]["id"];
        $classId = pdo_fetch("select id, parentid from" . tablename("tiny_wmall_goods_category") . "where uniacid = :uniacid and sid = :sid and elemeId = :elemeId", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":elemeId" => $categoryId));
        $goods = $product->getItemsByCategoryId($categoryId);
        if (is_error($goods)) {
            imessage(error(-1, $goods["message"]), "", "ajax");
        }
        foreach ($goods as $good) {
            $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => empty($classId["parentid"]) ? $classId["id"] : $classId["parentid"], "child_id" => empty($classId["parentid"]) ? 0 : $classId["id"], "title" => removeEmoji($good["name"]), "unitname" => $good["unit"], "thumb" => $good["imageUrl"], "status" => $good["isValid"], "price" => $good["specs"][0]["price"], "total" => -1, "elemeId" => $good["id"]);
            if (!empty($good["imageUrl"])) {
                $img = ihttp_get($good["imageUrl"]);
                if (!is_error($img)) {
                    $content = $img["content"];
                    $name = ifile_write($content, "", true);
                    if (!is_error($name)) {
                        $data["thumb"] = $name;
                    }
                }
            }
            if (!empty($good["attributes"])) {
                foreach ($good["attributes"] as $attr) {
                    $data["attrs"][] = array("name" => $attr["name"], "label" => $attr["details"]);
                }
                $data["attrs"] = iserializer($data["attrs"]);
            }
            $commodity = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "elemeId" => $good["id"]));
            pdo_delete("tiny_wmall_goods_options", array("sid" => $sid, "goods_id" => $commodity["id"]));
            if (empty($commodity)) {
                pdo_insert("tiny_wmall_goods", $data);
                $goods_id = pdo_insertid();
            } else {
                pdo_update("tiny_wmall_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $commodity["id"]));
                $goods_id = $commodity["id"];
            }
            if (!empty($good["specs"]) && count($good["specs"]) != 1) {
                foreach ($good["specs"] as $option) {
                    $options = array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $goods_id, "name" => $option["name"], "price" => $option["price"], "total" => -1);
                    pdo_insert("tiny_wmall_goods_options", $options);
                }
                pdo_update("tiny_wmall_goods", array("is_options" => 1), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $goods_id));
            }
        }
        $key = array_search($categoryId, $category);
        unset($category[$key]);
        $category = array_values($category);
        cache_write("we7wmall:eleme:" . $_W["uniacid"] . ":" . $sid, $category);
        imessage(error(0, $category), "", "ajax");
    }
}
if ($ta == "meituan_category") {
    $_W["page"]["title"] = "从美团导入";
    if ($_W["ispost"]) {
        mload()->model("plugin");
        $_W["_plugin"] = array("name" => "meituan");
        pload()->classs("product");
        $product = new product($sid);
        $results = $product->queryCatList();
        if (is_error($results)) {
            imessage(error(-1, $results["message"]), "", "ajax");
        }
        if (!empty($results)) {
            $insert = 0;
            $update = 0;
            foreach ($results as $result) {
                $result["name"] = removeEmoji($result["name"]);
                $category = pdo_get("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $result["name"]));
                if (empty($category)) {
                    $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $result["name"], "status" => 1);
                    pdo_insert("tiny_wmall_goods_category", $data);
                    $insert++;
                } else {
                    $data = array("title" => $result["name"]);
                    pdo_update("tiny_wmall_goods_category", $data, array("uniacid" => $_W["uniacid"], "id" => $category["id"], "sid" => $sid));
                    $update++;
                }
            }
            $basic = $product->queryBaseListByEPoiId($_W["store"]["meituanShopId"]);
            if (is_error($basic)) {
                imessage(error(-1, $basic["message"]), "", "ajax");
            }
            if (!empty($basic)) {
                $goods = array();
                foreach ($basic as $item) {
                    $item["categoryName"] = removeEmoji($item["categoryName"]);
                    $item["dishName"] = removeEmoji($item["dishName"]);
                    $categoryId = pdo_fetch("select id from" . tablename("tiny_wmall_goods_category") . "where uniacid = :uniacid and sid = :sid and title = :title", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":title" => $item["categoryName"]));
                    $basicGood = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $categoryId["id"], "meituanId" => $item["dishId"]));
                    if (!empty($categoryId)) {
                        $record = array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $categoryId["id"], "title" => $item["dishName"], "openplateformCode" => $item["eDishCode"], "meituanId" => $item["dishId"]);
                        if (empty($basicGood)) {
                            pdo_insert("tiny_wmall_goods", $record);
                        } else {
                            pdo_update("tiny_wmall_goods", $record, array("uniacid" => $_W["uniacid"], "id" => $basicGood["id"], "sid" => $sid));
                        }
                    }
                }
            }
            $goods = pdo_fetchall("select * from" . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid and meituanId > 0", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
            cache_write("we7wmall:meituan:" . $_W["uniacid"] . ":" . $sid, $goods);
            imessage(error(0, "导入分类成功,本次操作导入" . $insert . "条数据,更新" . $update . "条数据"), iurl("store/goods/index/meituan"), "ajax");
        } else {
            imessage(error(-1, "美团暂无分类"), "", "ajax");
        }
    }
}
if ($ta == "meituan") {
    $_W["page"]["title"] = "从美团导入";
    mload()->model("plugin");
    $_W["_plugin"] = array("name" => "meituan");
    pload()->classs("product");
    $product = new product($sid);
    $goods = cache_read("we7wmall:meituan:" . $_W["uniacid"] . ":" . $sid);
    if ($_W["ispost"]) {
        $good_id = $_GPC["__input"]["good"]["id"];
        $good = $product->queryListByEdishCodes($good_id, $_W["store"]["meituanShopId"]);
        if (is_error($good)) {
            imessage(error(-1, $good["message"]), "", "ajax");
        }
        $good["dishName"] = removeEmoji($good["dishName"]);
        $data = array("description" => $good["description"], "title" => $good["dishName"], "box_price" => $good["boxPrice"], "unitname" => $good["unit"], "thumb" => $good["picture"], "price" => $good["price"], "total" => -1);
        if (!empty($good["picture"])) {
            $img = ihttp_get($good["picture"]);
            if (!is_error($img)) {
                $content = $img["content"];
                $name = ifile_write($content, "", true);
                if (!is_error($name)) {
                    $data["thumb"] = $name;
                }
            }
        }
        if (!empty($good["attrs"])) {
            foreach ($good["attrs"] as $attr) {
                $data["attrs"][] = array("name" => $attr["propertyName"], "label" => $attr["values"]);
            }
            $data["attrs"] = iserializer($data["attrs"]);
        }
        pdo_update("tiny_wmall_goods", $data, array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $good_id));
        pdo_delete("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $good_id));
        if (count($good["skus"]) != 1) {
            foreach ($good["skus"] as $v) {
                $options = array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $good_id, "name" => $v["spec"], "total" => -1, "price" => $v["price"]);
                pdo_insert("tiny_wmall_goods_options", $options);
            }
            pdo_update("tiny_wmall_goods", array("is_options" => 1), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $good_id));
        }
        $key = array_search($good_id, $goods);
        unset($goods[$key]);
        $goods = array_values($goods);
        cache_write("we7wmall:meituan:" . $_W["uniacid"] . ":" . $sid, $goods);
        imessage(error(0, $goods), "", "ajax");
    }
}
if ($ta == "lewaimai") {
    $_W["page"]["title"] = "从乐外卖";
    $store = store_fetch($sid);
    $data = $store["data"];
    if ($_W["ispost"]) {
        if (empty($_GPC["admin_id"])) {
            imessage(error(-1, "admin_id不能为空"), "", "ajax");
        }
        if (empty($_GPC["shop_id"])) {
            imessage(error(-1, "shop_id不能为空"), "", "ajax");
        }
        $config = array("admin_id" => trim($_GPC["admin_id"]), "shop_id" => trim($_GPC["shop_id"]));
        store_set_data($sid, "lewaimai", $config);
        imessage(error(0, "开始导入"), iurl("store/goods/index/lewaimai_goods"), "ajax");
    }
}
if ($ta == "lewaimai_again") {
    $_W["page"]["title"] = "从乐外卖重新采集";
    cache_delete("lewaimai:" . $sid . "goods_category:" . $_W["uniacid"]);
    cache_delete("lewaimai:" . $sid . "goods_list:" . $_W["uniacid"]);
    imessage("重新导入商品", iurl("store/goods/index/lewaimai_goods"), "success");
}
if ($ta == "lewaimai_goods") {
    $store = store_fetch($sid);
    $data = $store["data"]["lewaimai"];
    if (empty($data["admin_id"]) || empty($data["shop_id"])) {
        imessage("参数不完整,请先填写", iurl("store/goods/index/lewaimai"), "error");
    }
    $goodsUrl = "https://api.lewaimai.com/customer/common/page/food/choose?ver=v2";
    $goodsArr = array("lwm_appid" => "dh129ahsd9898123gjhjfamnxoo1", "admin_id" => $data["admin_id"], "shop_id" => $data["shop_id"], "from_type" => 1);
    $results = ihttp_post($goodsUrl, $goodsArr);
    if (is_error($results)) {
        imessage("拉取失败", iurl("store/goods/index/list"), "error");
    }
    $results = @json_decode($results["content"], true);
    if ($results["error_code"] != 0) {
        imessage($results["error_msg"], iurl("store/goods/index/list"), "error");
    }
    $results = $results["data"];
    $category = $results["foodtype"];
    $cache_goods_category = cache_read("lewaimai:" . $sid . "goods_category:" . $_W["uniacid"]);
    if ($results["is_total"] == 1) {
        foreach ($category as $value) {
            if (!empty($cache_goods_category) && in_array($value["id"], array_keys($cache_goods_category))) {
                continue;
            }
            $goods_category_insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $value["name"], "status" => $value["is_show"]);
            pdo_insert("tiny_wmall_goods_category", $goods_category_insert);
            $goods_category_id = pdo_insertid();
            $cache_goods_category[$value["id"]] = $goods_category_id;
        }
        cache_write("lewaimai:" . $sid . "goods_category:" . $_W["uniacid"], $cache_goods_category);
        $goods = $results["foodlist"];
        $cache_goods_list = cache_read("lewaimai:" . $sid . "goods_list:" . $_W["uniacid"]);
        foreach ($goods as $good) {
            if (!empty($cache_goods_list) && in_array($good["id"], array_keys($cache_goods_list))) {
                continue;
            }
            $goods_insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $cache_goods_category[$good["type_id"]], "title" => $good["name"], "price" => $good["price"], "unitname" => $good["unit"], "status" => 1, "total" => -1, "box_price" => $good["dabao_money"], "content" => $good["descript"], "old_price" => $good["formerprice"]);
            if ($good["is_nature"] == 1) {
                foreach ($good["nature"] as $v) {
                    $labels = array();
                    foreach ($v["data"] as $attr) {
                        $labels[] = $attr["naturevalue"];
                    }
                    $goods_insert["attrs"][] = array("name" => $v["naturename"], "label" => $labels);
                }
                $goods_insert["attrs"] = iserializer($goods_insert["attrs"]);
            }
            pdo_insert("tiny_wmall_goods", $goods_insert);
            $goodsList_id = pdo_insertid();
            $cache_goods_list[$good["id"]] = $goodsList_id;
            if (!empty($good["img"])) {
                $cache_goods_img[$goodsList_id] = $good["img"];
            }
        }
        cache_write("lewaimai:" . $sid . "goods_list:" . $_W["uniacid"], $cache_goods_list);
    } else {
        if ($results["is_total"] == 0) {
            foreach ($category as $value) {
                if (!empty($cache_goods_category) && in_array($value["id"], array_keys($cache_goods_category))) {
                    continue;
                }
                $goods_category_insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $value["name"], "status" => $value["is_show"]);
                pdo_insert("tiny_wmall_goods_category", $goods_category_insert);
                $goods_category_id = pdo_insertid();
                $cache_goods_category[$value["id"]] = $goods_category_id;
                $page = 1;
                $goodsArr["type_id"] = $value["id"];
                $goodsArr["page"] = $page;
                $goodsUrl2 = "https://api.lewaimai.com/customer/common/page/food/getFoodByPage?ver=v2";
                $results2 = ihttp_post($goodsUrl2, $goodsArr);
                if (is_error($results2)) {
                    imessage("拉取失败", iurl("store/goods/index/list"), "error");
                }
                $results2 = @json_decode($results2["content"], true);
                if ($results2["error_code"] != 0) {
                    imessage($results2["error_msg"], iurl("store/goods/index/list"), "error");
                }
                $results2 = $results2["data"];
                $goods = $results2["foodlist"];
                $page++;
                if (empty($goods)) {
                    continue;
                }
                $cache_goods_list = cache_read("lewaimai:" . $sid . "goods_list:" . $_W["uniacid"]);
                foreach ($goods as $good) {
                    if (!empty($cache_goods_list) && in_array($good["id"], array_keys($cache_goods_list))) {
                        continue;
                    }
                    $goods_insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $cache_goods_category[$good["type_id"]], "title" => $good["name"], "price" => $good["price"], "unitname" => $good["unit"], "status" => 1, "total" => -1, "box_price" => $good["dabao_money"], "content" => $good["descript"], "old_price" => $good["formerprice"]);
                    if ($good["is_nature"] == 1) {
                        foreach ($good["nature"] as $v) {
                            $labels = array();
                            foreach ($v["data"] as $attr) {
                                $labels[] = $attr["naturevalue"];
                            }
                            $goods_insert["attrs"][] = array("name" => $v["naturename"], "label" => $labels);
                        }
                        $goods_insert["attrs"] = iserializer($goods_insert["attrs"]);
                    }
                    pdo_insert("tiny_wmall_goods", $goods_insert);
                    $goodsList_id = pdo_insertid();
                    $cache_goods_list[$good["id"]] = $goodsList_id;
                    if (!empty($good["img"])) {
                        $cache_goods_img[$goodsList_id] = $good["img"];
                    }
                }
                cache_write("lewaimai:" . $sid . "goods_list:" . $_W["uniacid"], $cache_goods_list);
                if ($page > $results2["pageTotal"]) {
                }
            }
            cache_write("lewaimai:" . $sid . "goods_category:" . $_W["uniacid"], $cache_goods_category);
        }
    }
    cache_write("lewaimai:" . $sid . "goodsImg:" . $_W["uniacid"], $cache_goods_img);
    imessage("即将拉取商品图片,请勿关闭浏览器", iurl("store/goods/index/goodsImg"), "success");
}
if ($ta == "goodsImg") {
    $goods_img = cache_read("lewaimai:" . $sid . "goodsImg:" . $_W["uniacid"]);
    $page = intval($_GPC["page"]) ? intval($_GPC["page"]) : 0;
    $pindex = 40;
    if (!empty($goods_img)) {
        $image = array_slice($goods_img, 0, $pindex, true);
        foreach ($image as $key => $val) {
            $img = ihttp_get($val);
            if (is_error($img)) {
                continue;
            }
            $content = $img["content"];
            $name = ifile_write($content, "", true);
            if (is_error($name)) {
                continue;
            }
            unset($goods_img[$key]);
            pdo_update("tiny_wmall_goods", array("thumb" => $name), array("uniacid" => $_W["uniacid"], "id" => $key));
        }
        cache_write("lewaimai:" . $sid . "goodsImg:" . $_W["uniacid"], $goods_img);
    }
    if (count($image) == 40) {
        $page++;
        imessage("即将拉取第" . $page . "页图片,请勿关闭浏览器", iurl("store/goods/index/goodsImg", array("page" => $page)), "success");
    } else {
        cache_delete("lewaimai:" . $sid . "goodsImg:" . $_W["uniacid"]);
        imessage("拉取成功", iurl("store/goods/index/list"), "success");
    }
} else {
    if ($ta == "svip_price") {
        if ($_W["ispost"]) {
            $type = intval($_GPC["type"]);
            if (!in_array($type, array(1, 2))) {
                imessage(error(-1, "请选择商品会员价格计算方式"), referer(), "error");
            }
            $number = floatval($_GPC["number"]);
            if ($type == 1 && (10 < $number || $number < 0)) {
                imessage(error(-1, "请设定1-10之间的折扣数"), referer(), "error");
            }
            if ($type == 2 && $number <= 0) {
                imessage(error(-1, "固定金额值不能小于零"), referer(), "error");
            }
            $ids = trim($_GPC["ids"]);
            $goods = pdo_fetchall("select id, price, is_options from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and id in (" . $ids . ")", array(":uniacid" => $_W["uniacid"]), "id");
            if (empty($goods)) {
                imessage(error(-1, "请选择需要设置的商品"), referer(), "error");
            }
            $svip_price = 0;
            if (!empty($goods)) {
                foreach ($goods as $id => $val) {
                    if ($type == 1) {
                        $svip_price = floatval(round($val["price"] * $number / 10, 2));
                    } else {
                        if ($type == 2) {
                            $svip_price = floatval(round($val["price"] - $number, 2));
                        }
                    }
                    if ($svip_price < 0) {
                        $svip_price = 0;
                    }
                    $update = array("svip_price" => $svip_price, "svip_status" => 1);
                    pdo_update("tiny_wmall_goods", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                    if ($val["is_options"] == "1") {
                        if ($type == 1) {
                            $price = "ROUND(price * " . $number . " / 10, 2) ";
                        } else {
                            if ($type == 2) {
                                $price = "ROUND(price - " . $number . ", 2) ";
                            }
                        }
                        pdo_query("UPDATE" . tablename("tiny_wmall_goods_options") . " SET svip_price = " . $price . " WHERE uniacid = :uniacid AND goods_id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $id));
                    }
                }
            }
            imessage(error(0, "批量设置会员价格成功"), iurl("store/goods/index"), "success");
        }
        $ids = $_GPC["id"];
        if (empty($ids)) {
            imessage(error(-1, "请选择需要设置的商品"), referer(), "info");
        }
        $ids = implode(",", $ids);
        include itemplate("store/goods/listOp");
        exit;
    }
}
include itemplate("store/goods/index");

?>