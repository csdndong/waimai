<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "post") {
    $_W["page"]["title"] = "添加商品";
    $id = intval($_GPC["id"]);
    if (!empty($id)) {
        $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_seckill_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id = :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id));
        if (empty($item)) {
            imessage("商品不存在或已删除", iurl("store/seckill/goods/list"), "info");
        }
        $item["slides"] = iunserializer($item["slides"]);
    }
    if ($_W["ispost"]) {
        $starttime = trim($_GPC["starttime"]);
        if (empty($starttime)) {
            imessage(error(-1, "活动开始时间不能为空"), "", "ajax");
        }
        $endtime = trim($_GPC["endtime"]);
        if (empty($endtime)) {
            imessage(error(-1, "活动结束时间不能为空"), "", "ajax");
        }
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        if ($endtime <= $starttime) {
            imessage(error(-1, "活动开始时间不能大于结束时间"), "", "ajax");
        }
        $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => intval($_GPC["cid"]), "title" => trim($_GPC["title"]), "price" => floatval($_GPC["price"]), "old_price" => floatval($_GPC["old_price"]), "total" => intval($_GPC["total"]), "thumb" => trim($_GPC["thumb"]), "click" => intval($_GPC["click"]), "displayorder" => intval($_GPC["displayorder"]), "content" => trim($_GPC["content"]), "description" => htmlspecialchars_decode($_GPC["description"]), "starttime" => intval($starttime), "endtime" => intval($endtime), "use_limit_day" => intval($_GPC["use_limit_day"]));
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
        if (!empty($id)) {
            pdo_update("tiny_wmall_seckill_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_seckill_goods", $data);
        }
        imessage(error(0, "编辑商品成功"), iurl("store/seckill/goods/list"), "ajax");
    }
    $categorys = pdo_fetchall("select id,title from " . tablename("tiny_wmall_seckill_goods_category") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
}
if ($ta == "list") {
    $_W["page"]["title"] = "商品列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["title"][$k]), "price" => floatval($_GPC["price"][$k]), "old_price" => floatval($_GPC["old_price"][$k]), "total" => intval($_GPC["total"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]), "use_limit_day" => intval($_GPC["use_limit_day"][$k]));
                pdo_update("tiny_wmall_seckill_goods", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("store/seckill/goods/list"), "ajax");
    }
    $condition = " where uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND (title LIKE '%" . $_GPC["keyword"] . "%')";
    }
    if (!empty($_GPC["cid"])) {
        $condition .= " AND cid = " . $_GPC["cid"];
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_seckill_goods") . $condition, $params);
    $goods = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_seckill_goods") . $condition . " ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $categorys = pdo_fetchall("select id,title from " . tablename("tiny_wmall_seckill_goods_category") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    $pager = pagination($total, $pindex, $psize);
}
if ($ta == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        $id = intval($id);
        if (0 < $id) {
            pdo_delete("tiny_wmall_seckill_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        }
    }
    imessage(error(0, "删除商品成功"), "", "ajax");
}
include itemplate("store/seckill/goods");

?>