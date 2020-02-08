<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "post") {
    $_W["page"]["title"] = "创建换购活动";
    $id = intval($_GPC["id"]);
    if (empty($id)) {
        $activity = activity_get($sid, "huangou");
        if (!empty($activity)) {
            imessage("门店已有换购活动, 如需重新添加换购活动，请先撤销其他换购活动", "", "info");
        }
    }
    if ($_W["ispost"]) {
        $title = !empty($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "活动主题不能为空"), referer(), "ajax");
        $goods = array();
        if (!empty($_GPC["goods_id"])) {
            foreach ($_GPC["goods_id"] as $key => $goods_id) {
                $temp = pdo_fetch("select a.id, a.price, a.attrs, b.bargain_id from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_activity_bargain_goods") . " as b on a.id = b.goods_id where a.uniacid = :uniacid and a.id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $goods_id));
                if (!empty($temp["attrs"])) {
                    $temp["attrs"] = iunserializer($temp["attrs"]);
                }
                if (empty($temp) || 0 < $temp["bargain_id"] && $temp["bargain_id"] != $id || !empty($temp["attrs"])) {
                    continue;
                }
                $row = array("goods_id" => $goods_id, "discount_price" => floatval($_GPC["discount_price"][$key]), "max_buy_limit" => intval($_GPC["max_buy_limit"][$key]), "poi_user_type" => "all", "discount_total" => intval($_GPC["discount_total"][$key]), "discount_available_total" => intval($_GPC["discount_available_total"][$key]));
                $goods[$goods_id] = $row;
            }
        }
        if (empty($goods)) {
            imessage(error(-1, "请选择参与活动的商品"), referer(), "ajax");
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "title" => $title, "content" => trim($_GPC["content"]), "order_limit" => 1, "goods_limit" => intval($_GPC["goods_limit"]), "starttime" => strtotime($_GPC["time"]["start"]), "endtime" => strtotime($_GPC["time"]["end"]) + 86399, "use_limit" => "1", "addtime" => TIMESTAMP, "type" => "huangou", "total_updatetime" => strtotime(date("Y-m-d")) + 86400);
        $activity = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $title, "starttime" => strtotime($_GPC["time"]["start"]), "endtime" => strtotime($_GPC["time"]["end"]) + 86399, "type" => "huangou", "status" => 1, "data" => iserializer(array("price_limit" => floatval($_GPC["price_limit"]))));
        $status = activity_set($sid, $activity);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        if (0 < $id) {
            pdo_update("tiny_wmall_activity_bargain", $data, array("uniacid" => $_W["uniacid"], "sid" => $store["id"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_activity_bargain", $data);
            $id = pdo_insertid();
        }
        foreach ($goods as $row) {
            $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "bargain_id" => $id, "sid" => $store["id"], "goods_id" => $row["goods_id"], "discount_price" => $row["discount_price"], "max_buy_limit" => $row["max_buy_limit"], "poi_user_type" => "all", "discount_total" => $row["discount_total"], "discount_available_total" => $row["discount_available_total"]);
            $is_exist = pdo_get("tiny_wmall_activity_bargain_goods", array("bargain_id" => $id, "goods_id" => $row["goods_id"]));
            if (empty($is_exist)) {
                pdo_insert("tiny_wmall_activity_bargain_goods", $data);
            } else {
                pdo_update("tiny_wmall_activity_bargain_goods", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "bargain_id" => $id, "goods_id" => $row["goods_id"]));
            }
        }
        $goods_ids = implode(",", array_keys($goods));
        pdo_query("delete from " . tablename("tiny_wmall_activity_bargain_goods") . " where uniacid = :uniacid and sid = :sid and bargain_id = :bargain_id and goods_id not in (" . $goods_ids . ")", array(":uniacid" => $_W["uniacid"], ":sid" => $store["id"], ":bargain_id" => $id));
        activity_cron();
        imessage(error(0, "编辑换购活动成功"), iurl("store/activity/huangou/list"), "ajax");
    }
    if (0 < $id) {
        $bargain = pdo_get("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $store["id"], "id" => $id));
        if (empty($bargain)) {
            imessage("换购活动不存在或已删除", referer(), "error");
        }
        $activity = activity_get($sid, "huangou");
        $row = pdo_fetchall("select a.*,b.title,b.price,b.thumb from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id where bargain_id = :bargain_id order by a.displayorder desc", array(":bargain_id" => $bargain["id"]), "goods_id");
        $bargain["goods"] = $row;
        $bargain["price_limit"] = $activity["data"]["price_limit"];
    }
    if (empty($bargain)) {
        $bargain = array("starttime" => TIMESTAMP, "endtime" => TIMESTAMP + 86400 * 15, "goods_limit" => 1, "goods" => array());
    }
}
if ($ta == "list") {
    $_W["page"]["title"] = "换购活动";
    $huangou = pdo_fetch("select a.*, b.status from" . tablename("tiny_wmall_activity_bargain") . " as a left join " . tablename("tiny_wmall_store_activity") . " as b on a.sid = b.sid where a.uniacid = :uniacid and a.agentid = :agentid and a.sid = :sid and a.type = :type and b.type = :type", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":sid" => $store["id"], ":type" => "huangou"));
    if (!empty($huangou)) {
        $bargain_status = activity_bargain_status();
    }
}
if ($ta == "del") {
    $ids = $_GPC["id"];
    if ($_W["ispost"]) {
        $status = activity_del($sid, "huangou");
        if (is_error($status)) {
            imessage($status, referer(), "ajax");
        }
        pdo_delete("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id, "sid" => $sid));
        pdo_delete("tiny_wmall_activity_bargain_goods", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "bargain_id" => $id, "sid" => $sid));
        imessage(error(0, "删除活动成功"), "", "ajax");
    }
}
include itemplate("store/activity/huangou");

?>
