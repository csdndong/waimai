<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "post") {
    $_W["page"]["title"] = "商品编辑";
    load()->func("tpl");
    $id = intval($_GPC["id"]);
    if ($id) {
        $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $id));
        if (empty($item)) {
            imessage("商品不存在或已删除", iurl("cloudGoods/storeGoods/index"), "info");
        }
        if ($item["is_options"]) {
            $item["options"] = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods_options") . " WHERE uniacid = :aid AND goods_id = :goods_id ORDER BY displayorder DESC, id ASC", array(":aid" => $_W["uniacid"], ":goods_id" => $id));
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
    if ($_W["ispost"]) {
        $data = array("sid" => intval($_GPC["sid"]), "uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "number" => trim($_GPC["number"]), "type" => intval($_GPC["type"]), "price" => floatval($_GPC["price"]), "old_price" => floatval($_GPC["old_price"]), "unitname" => trim($_GPC["unitname"]), "total" => intval($_GPC["total"]), "total_warning" => intval($_GPC["total_warning"]), "total_update_type" => intval($_GPC["total_update_type"]), "sailed" => intval($_GPC["sailed"]), "status" => intval($_GPC["status"]), "cid" => intval($_GPC["category"]["parentid"]), "child_id" => intval($_GPC["category"]["childid"]), "box_price" => floatval($_GPC["box_price"]), "thumb" => trim($_GPC["thumb"]), "label" => trim($_GPC["label"]), "displayorder" => intval($_GPC["displayorder"]), "content" => trim($_GPC["content"]), "description" => htmlspecialchars_decode($_GPC["description"]), "is_options" => intval($_GPC["is_options"]), "is_hot" => intval($_GPC["is_hot"]), "print_label" => intval($_GPC["print_label"]), "is_showtime" => intval($_GPC["is_showtime"]));
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
                $options[] = array("id" => intval($_GPC["options"]["id"][$key]), "name" => $val, "price" => $price, "total" => intval($_GPC["options"]["total"][$key]), "total_warning" => intval($_GPC["options"]["total_warning"][$key]), "displayorder" => intval($_GPC["options"]["displayorder"][$key]));
            }
            if (empty($options)) {
                imessage(error(-1, "没有设置有效的规格项"), "", "ajax");
            }
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
        imessage(error(0, "编辑商品成功"), iurl("cloudGoods/storeGoods/index"), "ajax");
    }
    $print_labels = pdo_fetchall("select * from " . tablename("tiny_wmall_printer_label") . " where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    $wmstore = pdo_fetchall("select id,title from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"]), "id");
}
if ($op == "index") {
    $_W["page"]["title"] = "商品列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["titles"][$k]), "price" => floatval($_GPC["prices"][$k]), "box_price" => floatval($_GPC["box_prices"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]), "total" => intval($_GPC["totals"][$k]));
                if ($_W["store"]["data"]["custom_goods_sailed_status"] == 1) {
                    $data["sailed"] = intval($_GPC["sailed"][$k]);
                }
                pdo_update("tiny_wmall_goods", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("cloudGoods/storeGoods/index"), "ajax");
    }
    $condition = " where uniacid = :uniacid";
    $params[":uniacid"] = $_W["uniacid"];
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND title LIKE '%" . $_GPC["keyword"] . "%' ";
    }
    if (!empty($_GPC["sid"])) {
        $condition .= " AND sid = :sid";
        $params[":sid"] = intval($_GPC["sid"]);
    }
    $type = intval($_GPC["type"]);
    if (!empty($type)) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_goods") . $condition, $params);
    $lists = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods") . (string) $condition . " ORDER BY id desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $wmStore = pdo_fetchall("select id,title from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"]), "id");
    $pager = pagination($total, $pindex, $psize);
    $categorys = store_fetchall_goods_category($sid, -1, true, "other");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    $state = pdo_update("tiny_wmall_goods", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
    if ($state === false) {
        imessage(error(-1, "操作失败"), "", "ajax");
    }
    imessage(error(0, "操作成功"), "", "ajax");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        $id = intval($id);
        if (0 < $id) {
            pdo_delete("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $id));
            pdo_delete("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "goods_id" => $id));
        }
    }
    imessage(error(0, "删除菜品成功"), "", "ajax");
}
if ($op == "synchronization") {
    if ($_W["ispost"] && $_GPC["set"] == 1) {
        $ids = explode(",", $_GPC["ids"]);
        $category_ids = $_GPC["category_id"];
        if (!is_array($category_ids)) {
            $category_ids = array($category_ids);
        }
        if (!empty($category_ids)) {
            foreach ($category_ids as $cid) {
                $mid = pdo_get("tiny_wmall_cloudgoods_goods_category", array("uniacid" => $_W["uniacid"], "id" => $cid), array("menu_id"));
                $goods = pdo_getall("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $ids));
                if (empty($goods)) {
                    imessage(error(-1, "商品不存在或已删除"), "", "ajax");
                }
                if ($goods["is_options"]) {
                    $options = pdo_getall("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "goods_id" => $id));
                }
                foreach ($goods as $value) {
                    $useful_keys = array("uniacid", "title", "number", "price", "old_price", "box_price", "is_options", "unitname", "total", "status", "is_hot", "thumb", "slides", "label", "displayorder", "content", "description", "attrs", "type");
                    $value = array_elements($useful_keys, $value);
                    $value["menu_id"] = $mid["menu_id"];
                    $value["category_id"] = $cid;
                    pdo_insert("tiny_wmall_cloudgoods_goods", $value);
                }
                $goods_id = pdo_insertid();
                if (!empty($options) && $goods_id) {
                    foreach ($options as $v) {
                        unset($v["id"]);
                        unset($v["sid"]);
                        unset($v["total_warning"]);
                        $v["goods_id"] = $goods_id;
                        pdo_insert("tiny_wmall_cloudgoods_goods_options", $v);
                    }
                }
            }
        }
        imessage(error(0, "同步商品成功"), iurl("cloudGoods/storeGoods/index"), "success");
    }
    $categorys = pdo_fetchall("select id, title from" . tablename("tiny_wmall_cloudgoods_goods_category") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $ids = implode(",", $_GPC["id"]);
    include itemplate("storeGoodsOp");
    exit;
} else {
    include itemplate("storeGoods");
}

?>