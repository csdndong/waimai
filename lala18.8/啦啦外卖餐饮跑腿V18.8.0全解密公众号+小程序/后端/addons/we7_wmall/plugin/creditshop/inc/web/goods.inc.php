<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "商品列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["titles"][$k]), "old_price" => floatval($_GPC["old_prices"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]));
                pdo_update("tiny_wmall_creditshop_goods", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("creditshop/goods/list"), "ajax");
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $type = trim($_GPC["type"]);
    if (!empty($type)) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
    }
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND title LIKE '%" . $_GPC["keyword"] . "%'";
    }
    $lists = pdo_fetchall("select * from" . tablename("tiny_wmall_creditshop_goods") . $condition . " order by displayorder desc", $params);
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑商品";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $item = creditshop_goods_get($id);
        $item["redpacket"] = iunserializer($item["redpacket"]);
    }
    $categorys = creditshop_category_get();
    if ($_W["ispost"]) {
        $type = trim($_GPC["type"]);
        $data = array("uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "category_id" => intval($_GPC["creditshop_category_id"]), "type" => $type, "credit2" => trim($_GPC["credit2"]), "old_price" => trim($_GPC["old_price"]), "status" => intval($_GPC["status"]), "thumb" => trim($_GPC["thumb"]), "chance" => intval($_GPC["chance"]), "use_credit1" => trim($_GPC["use_credit1"]), "use_credit2" => trim($_GPC["use_credit2"]), "description" => htmlspecialchars_decode($_GPC["description"]), "displayorder" => intval($_GPC["displayorder"]));
        $hour = array();
        if (!empty($_GPC["start_hour"])) {
            $hour = array();
            foreach ($_GPC["start_hour"] as $k => $v) {
                $v = str_replace("：", ":", trim($v));
                if (!strexists($v, ":")) {
                    $v .= ":00";
                }
                $end = str_replace("：", ":", trim($_GPC["end_hour"][$k]));
                if (!strexists($end, ":")) {
                    $end .= ":00";
                }
                $hour[] = array("s" => $v, "e" => $end);
            }
        }
        $category = array();
        if (!empty($_GPC["category_id"])) {
            foreach ($_GPC["category_id"] as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $category[] = array("id" => intval($value), "title" => trim($_GPC["category_title"][$key]), "src" => trim($_GPC["category_src"][$key]));
            }
        }
        $use_days_limit = intval($_GPC["use_days_limit"]);
        if ($type == "redpacket" && $use_days_limit <= 0) {
            imessage(error(-1, "红包的有效期应大于零"), "", "ajax");
        }
        $redpacket = array("name" => trim($_GPC["name"]), "discount" => trim($_GPC["discount"]), "condition" => trim($_GPC["condition"]), "grant_days_effect" => intval($_GPC["grant_days_effect"]), "use_days_limit" => $use_days_limit, "hour" => $hour, "category" => $category);
        $data["redpacket"] = iserializer($redpacket);
        if (0 < $id) {
            pdo_update("tiny_wmall_creditshop_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_creditshop_goods", $data);
        }
        imessage(error(0, "编辑商品成功"), iurl("creditshop/goods/list"), "ajax");
    }
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    $state = pdo_update("tiny_wmall_creditshop_goods", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
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
    foreach ($ids as $v) {
        pdo_delete("tiny_wmall_creditshop_goods", array("uniacid" => $_W["uniacid"], "id" => $v));
    }
    imessage(error(0, "删除商品成功"), "", "ajax");
}
include itemplate("goods");

?>