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
                $data = array("name" => trim($_GPC["name"][$k]), "price" => floatval($_GPC["price"][$k]), "old_price" => floatval($_GPC["old_price"][$k]), "total" => intval($_GPC["total"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
                pdo_update("tiny_wmall_seckill_goods", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("seckill/goods/list"), "ajax");
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND (title LIKE '%" . $_GPC["keyword"] . "%')";
    }
    $sid = intval($_GPC["sid"]);
    if (!empty($sid)) {
        $condition .= " AND sid = :sid";
        $params[":sid"] = $sid;
    }
    $cid = intval($_GPC["cid"]);
    if (!empty($cid)) {
        $condition .= " AND cid = :cid";
        $params[":cid"] = $cid;
    }
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : "-1";
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_seckill_goods") . $condition, $params);
    $goods = pdo_fetchall("select * from " . tablename("tiny_wmall_seckill_goods") . $condition . " order by displayorder desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $goods_status = gohome_goods_status();
    $pager = pagination($total, $pindex, $psize);
    $stores = pdo_fetchall("select id,title from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    $categorys = pdo_fetchall("select id,title from " . tablename("tiny_wmall_seckill_goods_category") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
} else {
    if ($op == "del") {
        $id = intval($_GPC["id"]);
        $result = gohome_del_goods($id, "seckill");
        imessage($result, "", "ajax");
    }
}
include itemplate("goods");

?>