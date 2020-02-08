<?php
/*
 * @ 买卖跑腿系统
 * @ APP公众号小程序版
 * @ PHP开源站，遵从PHP开源精神
 * @ 源码仅供学习研究，禁止商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "店铺绑定";
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " and a.sid = :sid";
        $params[":sid"] = $sid;
    }
    $bd_id = intval($_GPC["bd_id"]);
    if (0 < $bd_id) {
        $condition .= " and a.bd_id = :bd_id";
        $params[":bd_id"] = $bd_id;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_storebd_store") . " as a" . $condition, $params);
    $data = pdo_fetchall("SELECT a.*, b.uid, b.status FROM " . tablename("tiny_wmall_storebd_store") . " as a left join" . tablename("tiny_wmall_storebd_user") . " as b on a.bd_id = b.id" . $condition . " ORDER BY a.id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$val) {
            $val["fee_takeout"] = iunserializer($val["fee_takeout"]);
            $val["fee_instore"] = iunserializer($val["fee_instore"]);
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            $val["member"] = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $val["uid"]), array("avatar", "realname"));
        }
    }
    $store_spreads = storebd_user_fetchall();
    $stores = store_fetchall(array("id", "title"));
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "绑定关系";
        $id = intval($_GPC["id"]);
        if (!empty($id)) {
            $data = pdo_get("tiny_wmall_storebd_store", array("uniacid" => $_W["uniacid"], "id" => $id));
            if (!empty($data)) {
                $data["fee_takeout"] = iunserializer($data["fee_takeout"]);
                $data["fee_instore"] = iunserializer($data["fee_instore"]);
            }
        }
    if ($_W["ispost"]) {
            $storebd_id = intval($_GPC["bd_id"]);
            $sid = intval($_GPC["sid"]);
        if (empty($storebd_id)) {
            imessage(error(-1, "请选择推广员"), "", "ajax");
        }
        if (empty($sid)) {
            imessage(error(-1, "请选择店铺"), "", "ajax");
        }
        $fee_takeout = array();
        $takeout_GPC = $_GPC["fee_takeout"];
        $fee_takeout["type"] = intval($takeout_GPC["type"]) ? intval($takeout_GPC["type"]) : 1;
        if ($fee_takeout["type"] == 2) {
            $fee_takeout["fee"] = floatval($takeout_GPC["fee"]);
        } else {
            $fee_takeout["fee_rate"] = floatval($takeout_GPC["fee_rate"]);
            $fee_takeout["fee_min"] = floatval($takeout_GPC["fee_min"]);
        }
        $instore_GPC = $_GPC["fee_instore"];
        $fee_instore["type"] = intval($instore_GPC["type"]) ? intval($instore_GPC["type"]) : 1;
        if ($fee_instore["type"] == 2) {
            $fee_instore["fee"] = floatval($instore_GPC["fee"]);
        } else {
            $fee_instore["fee_rate"] = floatval($instore_GPC["fee_rate"]);
            $fee_instore["fee_min"] = floatval($instore_GPC["fee_min"]);
        }
        $update = array("uniacid" => $_W["uniacid"], "sid" => $sid, "bd_id" => $storebd_id, "fee_takeout" => iserializer($fee_takeout), "fee_instore" => iserializer($fee_instore), "addtime" => TIMESTAMP);
            $storebd_store = pdo_get("tiny_wmall_storebd_store", array("uniacid" => $_W["uniacid"], "sid" => $sid));
            if (!empty($storebd_store)) {
                pdo_update("tiny_wmall_storebd_store", $update, array("uniacid" => $_W["uniacid"], "id" => $storebd_store["id"]));
            } else {
                if (empty($id)) {
            pdo_insert("tiny_wmall_storebd_store", $update);
        } else {
                    pdo_update("tiny_wmall_storebd_store", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
        }
            }
            imessage(error(0, "设置绑定成功"), iurl("storebd/bind/index"), "ajax");
    }
    $store_spreads = pdo_fetchall("select a.id, b.realname as title from" . tablename("tiny_wmall_storebd_user") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid where a.uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1), array("id", "title"));
    } else {
        if ($op == "del") {
            $id = intval($_GPC["id"]);
            pdo_delete("tiny_wmall_storebd_store", array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "解除店铺绑定成功"), "", "ajax");
        }
    }
}
include itemplate("bind");

?>