<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "菜单";
    if (checksubmit()) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
                pdo_update("tiny_wmall_cloudgoods_menu_category", $data, array("id" => intval($v)));
            }
        }
        imessage(error(0, "编辑类型成功"), iurl("cloudGoods/menuCategory/index"), "ajax");
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 10;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_cloudgoods_menu_category"), array());
    $store = pdo_fetchall("select * from" . tablename("tiny_wmall_cloudgoods_menu_category") . " order by displayorder desc,id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    include itemplate("menuCategory");
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑菜单";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $storecate = pdo_get("tiny_wmall_cloudgoods_menu_category", array("id" => $id));
    }
    if ($_W["ispost"]) {
        $data = array("title" => trim($_GPC["title"]), "displayorder" => intval($_GPC["displayorder"]), "status" => intval($_GPC["status"]));
        if (!empty($storecate)) {
            pdo_update("tiny_wmall_cloudgoods_menu_category", $data, array("id" => $id));
        } else {
            $data["uniacid"] = $_W["uniacid"];
            pdo_insert("tiny_wmall_cloudgoods_menu_category", $data);
        }
        imessage(error(0, "编辑菜单成功"), iurl("cloudGoods/menuCategory/index"), "ajax");
    }
    include itemplate("menuCategory");
}
if ($op == "del") {
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_cloudgoods_menu_category", array("id" => $id));
    imessage(error(0, "删除菜单成功"), "", "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_cloudgoods_menu_category", array("status" => $status), array("id" => $id));
}

?>