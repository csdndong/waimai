<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
$config = get_plugin_config("gohome.basic");
if ($config["status"]["seckill"] != 1) {
    imessage("平台已关闭抢购功能，详情请联系平台管理员", "", "info");
}
if ($ta == "post") {
    $_W["page"]["title"] = "添加商品";
    $id = intval($_GPC["id"]);
    if (!empty($id)) {
        $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_seckill_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id = :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id));
        if (empty($item)) {
            imessage("商品不存在或已删除", iurl("store/seckill/goods/list"), "info");
        }
        $item["thumbs"] = iunserializer($item["thumbs"]);
        $item["share"] = iunserializer($item["share"]);
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
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "cid" => intval($_GPC["cid"]), "name" => trim($_GPC["name"]), "price" => floatval($_GPC["price"]), "oldprice" => floatval($_GPC["oldprice"]), "total" => intval($_GPC["total"]), "total_update_type" => intval($_GPC["total_update_type"]), "thumb" => trim($_GPC["thumb"]), "click" => intval($_GPC["click"]), "content" => trim($_GPC["content"]), "falsejoinnum" => intval($_GPC["falsejoinnum"]), "falselooknum" => intval($_GPC["falselooknum"]), "falsesharenum" => intval($_GPC["falsesharenum"]), "description" => htmlspecialchars_decode($_GPC["description"]), "buy_note" => htmlspecialchars_decode($_GPC["buy_note"]), "status" => intval($_GPC["status"]), "starttime" => intval($starttime), "endtime" => intval($endtime), "use_limit_day" => intval($_GPC["use_limit_day"]));
        $data["thumbs"] = array();
        if (!empty($_GPC["thumbs"])) {
            foreach ($_GPC["thumbs"] as $val) {
                if (empty($val)) {
                    continue;
                }
                $data["thumbs"][] = trim($val);
            }
        }
        $data["thumbs"] = iserializer($data["thumbs"]);
        $data["share"] = array();
        $share_thumb = trim($_GPC["share_thumb"]) ? trim($_GPC["share_thumb"]) : trim($_GPC["thumb"]);
        $share_title = trim($_GPC["share_title"]) ? trim($_GPC["share_title"]) : trim($_GPC["title"]);
        $share_detail = trim($_GPC["share_detail"]);
        $data["share"] = array("share_thumb" => $share_thumb, "share_title" => $share_title, "share_detail" => $share_detail);
        $data["share"] = iserializer($data["share"]);
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
                $data = array("name" => trim($_GPC["name"][$k]), "price" => floatval($_GPC["price"][$k]), "oldprice" => floatval($_GPC["old_price"][$k]), "total" => intval($_GPC["total"][$k]), "use_limit_day" => intval($_GPC["use_limit_day"][$k]));
                pdo_update("tiny_wmall_seckill_goods", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("store/seckill/goods/list"), "ajax");
    }
    $condition = " where uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : "-1";
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND (name LIKE '%" . $_GPC["keyword"] . "%')";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_seckill_goods") . $condition, $params);
    $goods = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_seckill_goods") . $condition . " ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $categorys = pdo_fetchall("select id,title from " . tablename("tiny_wmall_seckill_goods_category") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    mload()->model("plugin");
    pload()->model("gohome");
    $goods_status = gohome_goods_status();
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
include itemplate("store/gohome/seckill/goods");

?>