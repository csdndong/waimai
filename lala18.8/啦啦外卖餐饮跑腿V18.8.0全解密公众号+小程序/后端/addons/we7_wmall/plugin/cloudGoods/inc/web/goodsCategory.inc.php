<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "商品类型";
    if (checksubmit()) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
                pdo_update("tiny_wmall_cloudgoods_goods_category", $data, array("id" => intval($v)));
            }
        }
        imessage(error(0, "编辑商品类型成功"), iurl("cloudGoods/goodsCategory/index"), "ajax");
    }
    $params = array();
    $menu_id = intval($_GPC["menu_id"]);
    if (!empty($menu_id)) {
        if (isset($condition)) {
            $condition .= " and menu_id = :menu_id";
        } else {
            $condition = " where menu_id = :menu_id";
        }
        $params[":menu_id"] = $menu_id;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_cloudgoods_goods_category") . $condition, $params);
    $goods = pdo_fetchall("select * from" . tablename("tiny_wmall_cloudgoods_goods_category") . $condition . " order by displayorder desc,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $menus = pdo_fetchall("select * from" . tablename("tiny_wmall_cloudgoods_menu_category"), array(), "id");
    $pager = pagination($total, $pindex, $psize);
    include itemplate("goodsCategory");
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑商品类型";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $goodscate = pdo_get("tiny_wmall_cloudgoods_goods_category", array("id" => $id));
    }
    if ($_W["ispost"]) {
        $data = array("menu_id" => intval($_GPC["menu_id"]), "title" => trim($_GPC["title"]), "displayorder" => intval($_GPC["displayorder"]), "status" => intval($_GPC["status"]));
        if (!empty($goodscate)) {
            pdo_update("tiny_wmall_cloudgoods_goods_category", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            $data["uniacid"] = $_W["uniacid"];
            pdo_insert("tiny_wmall_cloudgoods_goods_category", $data);
        }
        imessage(error(0, "编辑商品类型成功"), iurl("cloudGoods/goodsCategory/index"), "ajax");
    }
    $menus = pdo_fetchall("select * from" . tablename("tiny_wmall_cloudgoods_menu_category"), array(), "id");
    include itemplate("goodsCategory");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_cloudgoods_goods_category", array("status" => $status), array("id" => $id));
}
if ($op == "del") {
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_cloudgoods_goods_category", array("id" => $id));
    imessage(error(0, "删除商品类型成功"), "", "ajax");
}

?>