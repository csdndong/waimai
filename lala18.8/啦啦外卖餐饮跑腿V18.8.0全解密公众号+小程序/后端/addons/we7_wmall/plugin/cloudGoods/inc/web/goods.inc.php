<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "post") {
    $_W["page"]["title"] = "编辑商品";
    $id = intval($_GPC["id"]);
    if ($id) {
        $good = pdo_get("tiny_wmall_cloudgoods_goods", array("id" => $id));
        if ($good["is_options"]) {
            $good["options"] = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_cloudgoods_goods_options") . " WHERE goods_id = :goods_id ORDER BY displayorder DESC, id ASC", array(":goods_id" => $id));
        }
        $good["attrs"] = iunserializer($good["attrs"]);
        if (!empty($good["attrs"])) {
            foreach ($good["attrs"] as &$val) {
                $val["label"] = implode(",", $val["label"]);
            }
        }
        $good["slides"] = iunserializer($good["slides"]);
    } else {
        $good["total"] = -1;
        $good["unitname"] = "份";
    }
    if ($_W["ispost"]) {
        $data = array("category_id" => intval($_GPC["category_id"]), "menu_id" => intval($_GPC["menu_id"]), "type" => intval($_GPC["type"]), "title" => trim($_GPC["title"]), "number" => trim($_GPC["number"]), "price" => floatval($_GPC["price"]), "old_price" => floatval($_GPC["old_price"]), "box_price" => floatval($_GPC["box_price"]), "ts_price" => floatval($_GPC["ts_price"]), "is_options" => intval($_GPC["is_options"]), "unitname" => trim($_GPC["unitname"]), "total" => intval($_GPC["total"]), "status" => intval($_GPC["status"]), "is_hot" => intval($_GPC["is_hot"]), "thumb" => trim($_GPC["thumb"]), "label" => trim($_GPC["label"]), "displayorder" => intval($_GPC["displayorder"]), "content" => trim($_GPC["content"]), "description" => trim($_GPC["description"]));
        $menuid = $data["category_id"];
        if (!empty($menuid)) {
            $getmenu = pdo_get("tiny_wmall_cloudgoods_goods_category", array("id" => $menuid));
            $data["menu_id"] = $getmenu["menu_id"];
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
        if ($data["is_options"] == 1) {
            $options = array();
            foreach ($_GPC["options"]["name"] as $key => $val) {
                $val = trim($val);
                $price = floatval($_GPC["options"]["price"][$key]);
                if (empty($val) || empty($price)) {
                    continue;
                }
                $options[] = array("id" => intval($_GPC["options"]["id"][$key]), "name" => $val, "price" => $price, "total" => intval($_GPC["options"]["total"][$key]), "displayorder" => intval($_GPC["options"]["displayorder"][$key]));
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
            pdo_update("tiny_wmall_cloudgoods_goods", $data, array("id" => $id));
        } else {
            $data["uniacid"] = $_W["uniacid"];
            pdo_insert("tiny_wmall_cloudgoods_goods", $data);
            $id = pdo_insertid();
        }
        $ids = array(0);
        if (!empty($options)) {
            foreach ($options as $val) {
                $option_id = $val["id"];
                if (0 < $option_id) {
                    pdo_update("tiny_wmall_cloudgoods_goods_options", $val, array("id" => $option_id, "goods_id" => $id));
                } else {
                    $val["uniacid"] = $_W["uniacid"];
                    $val["goods_id"] = $id;
                    pdo_insert("tiny_wmall_cloudgoods_goods_options", $val);
                    $option_id = pdo_insertid();
                }
                $ids[] = $option_id;
            }
        }
        $ids = implode(",", $ids);
        imessage(error(0, "编辑商品成功"), iurl("cloudGoods/goods/index"), "ajax");
    }
    $condition = " where status = 1 ";
    $goodscategory = pdo_fetchall("select * from " . tablename("tiny_wmall_cloudgoods_goods_category") . $condition, array());
    include itemplate("goods");
}
if ($op == "index") {
    $_W["page"]["title"] = "商品列表";
    if ($_W["ispost"] && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("title" => trim($_GPC["titles"][$k]), "price" => floatval($_GPC["prices"][$k]), "box_price" => floatval($_GPC["box_prices"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]), "total" => intval($_GPC["totals"][$k]));
            pdo_update("tiny_wmall_cloudgoods_goods", $data, array("id" => intval($v)));
        }
        imessage(error(0, "修改成功"), iurl("cloudGoods/goods/index/list"), "ajax");
    }
    $params = array();
    if (!empty($_GPC["keyword"])) {
        if (isset($condition)) {
            $condition .= " and (title like '%" . $_GPC["keyword"] . "%' or number like '%" . $_GPC["keyword"] . "%')";
        } else {
            $condition = " where (title LIKE '%" . $_GPC["keyword"] . "%' or number like '%" . $_GPC["keyword"] . "%')";
        }
    }
    if (!empty($_GPC["category_id"])) {
        if (isset($condition)) {
            $condition .= " and category_id = :category_id";
        } else {
            $condition = " where category_id = :category_id";
        }
        $params["category_id"] = intval($_GPC["category_id"]);
    }
    $type = intval($_GPC["type"]);
    if (!empty($type)) {
        if (isset($condition)) {
            $condition .= " and type = :type";
        } else {
            $condition = " where type = :type";
        }
        $params[":type"] = $type;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_cloudgoods_goods") . $condition, $params);
    $goodsinfo = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_cloudgoods_goods") . (string) $condition . " order by displayorder desc,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    $goodscategory = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_cloudgoods_goods_category"), array(), "id");
    include itemplate("goods");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_cloudgoods_goods", array("status" => $status), array("id" => $id));
    imessage(error(0, "修改状态成功"), "", "ajax");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        $id = intval($id);
        if (0 < $id) {
            pdo_delete("tiny_wmall_cloudgoods_goods", array("id" => $id));
            pdo_delete("tiny_wmall_cloudgoods_goods_options", array("goods_id" => $id));
        }
    }
    imessage(error(0, "删除商品成功"), "", "ajax");
}
if ($op == "copy") {
    $id = intval($_GPC["id"]);
    $goods = pdo_get("tiny_wmall_cloudgoods_goods", array("id" => $id));
    if (empty($goods)) {
        imessage(error(-1, "商品不存在或已删除"), "", "ajax");
    }
    if ($goods["is_options"]) {
        $options = pdo_getall("tiny_wmall_cloudgoods_goods_options", array("goods_id" => $id));
    }
    unset($goods["id"]);
    $goods["title"] = $goods["title"] . "-复制";
    $goods["uniacid"] = $_W["uniacid"];
    pdo_insert("tiny_wmall_cloudgoods_goods", $goods);
    $goods_id = pdo_insertid();
    if (!empty($options) && $goods_id) {
        foreach ($options as $option) {
            unset($option["id"]);
            $option["goods_id"] = $goods_id;
            $option["uniacid"] = $_W["uniacid"];
            pdo_insert("tiny_wmall_cloudgoods_goods_options", $option);
        }
    }
    imessage(error(0, "复制商品成功, 现在进入编辑页"), iurl("cloudGoods/goods/post", array("id" => $goods_id)), "ajax");
}

?>