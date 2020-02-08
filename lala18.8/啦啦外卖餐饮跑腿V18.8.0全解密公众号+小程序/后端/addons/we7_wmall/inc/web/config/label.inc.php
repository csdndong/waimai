<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("build");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "TY_store_label";
if ($op == "TY_store_label") {
    $_W["page"]["title"] = "商户标签";
    if ($_W["ispost"]) {
        $ids = array(0);
        if (!empty($_GPC["add_title"])) {
            foreach ($_GPC["add_title"] as $k => $v) {
                $title = trim($v);
                $color = trim($_GPC["add_color"][$k]);
                if (empty($title) || empty($color)) {
                    continue;
                }
                $insert = array("uniacid" => $_W["uniacid"], "title" => $title, "color" => $color, "displayorder" => intval($_GPC["add_displayorder"][$k]), "is_system" => 0, "type" => "TY_store_label");
                pdo_insert("tiny_wmall_category", $insert);
                $ids[] = pdo_insertid();
            }
        }
        if (!empty($_GPC["id"])) {
            foreach ($_GPC["id"] as $k => $v) {
                $id = intval($v);
                $title = trim($_GPC["title"][$k]);
                $color = trim($_GPC["color"][$k]);
                if (0 < $id && empty($title) || empty($color)) {
                    $ids[] = $id;
                    continue;
                }
                $update = array("title" => $title, "color" => $color, "displayorder" => intval($_GPC["displayorder"][$k]));
                pdo_update("tiny_wmall_category", $update, array("uniacid" => $_W["uniacid"], "type" => "TY_store_label", "id" => $id));
                $ids[] = $id;
            }
        }
        $ids = implode(",", $ids);
        pdo_query("delete from " . tablename("tiny_wmall_category") . " where uniacid = " . $_W["uniacid"] . " and type = 'TY_store_label' and is_system = 0 and id not in (" . $ids . ")");
        imessage(error(0, "保存成功"), iurl("config/label/TY_store_label"), "ajax");
    }
    build_category("TY_store_label");
    $labels = category_store_label();
}
if ($op == "TY_delivery_label") {
    $_W["page"]["title"] = "配送评分标签";
    $condition = " where uniacid = :uniacid and type = 'TY_delivery_label' ";
    $params[":uniacid"] = $_W["uniacid"];
    $keywords = trim($_GPC["keyword"]);
    if (!empty($keywords)) {
        $condition .= " and title like '%" . $keywords . "%'";
    }
    $score = isset($_GPC["score"]) ? intval($_GPC["score"]) : 0;
    if (0 < $score) {
        $condition .= " and score = :score";
        $params[":score"] = intval($_GPC["score"]);
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_category") . $condition, $params);
    $labels = pdo_fetchall("select * from " . tablename("tiny_wmall_category") . $condition . " order by displayorder desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
}
if ($op == "post") {
    $_W["page"]["title"] = "添加配送评分标签";
    $id = $_GPC["id"];
    if (0 < $id) {
        $label = pdo_get("tiny_wmall_category", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    if ($_W["ispost"]) {
        $title = $_GPC["title"];
        if (empty($title)) {
            imessage(error(-1, "标签名称不能为空"), "", "ajax");
        }
        $score = $_GPC["score"];
        if (empty($score)) {
            imessage(error(-1, "等级不能为空"), "", "ajax");
        }
        $displayorder = $_GPC["displayorder"];
        if (empty($displayorder)) {
            imessage(error(-1, "排序不能为空"), "", "ajax");
        }
        $data = array("uniacid" => $_W["uniacid"], "title" => $title, "score" => $score, "type" => "TY_delivery_label", "displayorder" => $displayorder, "is_system" => 0);
        if (!empty($label["id"])) {
            pdo_update("tiny_wmall_category", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_category", $data);
        }
        imessage(error(0, "保存成功"), iurl("config/label/TY_delivery_label"), "ajax");
    }
}
if ($op == "del") {
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_category", array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, "标签删除成功"), iurl("config/label/TY_delivery_label"), "ajax");
}
include itemplate("config/label");

?>