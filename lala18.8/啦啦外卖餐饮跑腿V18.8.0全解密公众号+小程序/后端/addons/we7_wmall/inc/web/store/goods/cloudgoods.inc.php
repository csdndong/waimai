<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("plugin");
pload()->model("cloudGoods");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "商品极速录入";
    if ($_W["isajax"]) {
        $keywords = trim($_GPC["keywords"]);
        $menus = cloudgoods_getall_menus(array("keywords" => $keywords));
        $goods = cloudgoods_getall_goods(array("keywords" => $keywords));
        $result = array("menus" => array_values($menus), "goods" => array_values($goods));
        imessage(error(0, $result), "", "ajax");
    }
    $categorys = store_fetchall_goods_category($sid, -1, true, "other");
}
if ($ta == "goods") {
    $keywords = trim($_GPC["keywords"]);
    $goods_categoryid = intval($_GPC["goods_categoryid"]);
    $goods = cloudgoods_getall_goods(array("keywords" => $keywords, "goods_categoryid" => $goods_categoryid));
    $result = array("goods" => array_values($goods));
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "menu_detail") {
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $data = cloudgoods_menu_fetch($id);
        $result = array("menu" => $data["menu"], "categorys" => $data["goods_categorys"], "goods" => array_values(cloudgoods_getall_goods(array("goods_categoryid" => $data["goods_categorys"][0]["id"]))));
    }
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "goods_writed") {
    $id = intval($_GPC["goods_id"]);
    $goods = pdo_get("tiny_wmall_cloudgoods_goods", array("id" => $id));
    if (empty($goods)) {
        imessage(error(-1, "商品不存在或已删除"), "", "ajax");
    }
    $goods["title"] = trim($_GPC["title"]);
    $goods["sid"] = $sid;
    $goods["price"] = floatval($_GPC["price"]) ? floatval($_GPC["price"]) : $goods["price"];
    if (!empty($_GPC["category_parentid"])) {
        $goods["cid"] = intval($_GPC["category_parentid"]);
        if (0 < $_GPC["category_childid"]) {
            $goods["child_id"] = intval($_GPC["category_childid"]);
        }
    } else {
        $goods_category = pdo_get("tiny_wmall_cloudgoods_goods_category", array("id" => $goods["category_id"]), array("title", "status", "displayorder"));
        $old_category = pdo_get("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "title" => $goods_category["title"], "parentid" => 0), array("id"));
        if (!empty($old_category)) {
            if (0 < $old_category["parentid"]) {
                $goods["cid"] = $old_category["parentid"];
                $goods["child_id"] = $old_category["id"];
            } else {
                $goods["cid"] = $old_category["id"];
            }
        } else {
            $goods_category["sid"] = $sid;
            $goods_category["uniacid"] = $_W["uniacid"];
            pdo_insert("tiny_wmall_goods_category", $goods_category);
            $goods["cid"] = pdo_insertid();
        }
    }
    unset($goods["category_id"]);
    unset($goods["menu_id"]);
    unset($goods["id"]);
    pdo_insert("tiny_wmall_goods", $goods);
    $goods_id = pdo_insertid();
    $options_new = $_GPC["options"] ? $_GPC["options"] : array();
    if ($goods["is_options"]) {
        $options = pdo_getall("tiny_wmall_cloudgoods_goods_options", array("goods_id" => $id));
    }
    if (!empty($options) && $goods_id) {
        foreach ($options as $option) {
            $option["goods_id"] = $goods_id;
            $option["sid"] = $sid;
            if (!empty($options_new)) {
                foreach ($options_new as $value) {
                    if (intval($value["id"]) == $option["id"]) {
                        $option["price"] = floatval($value["price"]);
                    }
                }
            }
            unset($option["id"]);
            $option["uniacid"] = $_W["uniacid"];
            pdo_insert("tiny_wmall_goods_options", $option);
        }
    }
    imessage(error(0, "商品录入成功"), referer(), "ajax");
}
include itemplate("store/goods/cloudgoods");

?>