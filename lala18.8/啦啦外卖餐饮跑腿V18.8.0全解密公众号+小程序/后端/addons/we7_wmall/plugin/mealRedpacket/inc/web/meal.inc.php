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
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "套餐红包列表";
    $condition = " where uniacid = :uniacid and type = :type";
    $params = array(":uniacid" => $_W["uniacid"], ":type" => "mealRedpacket");
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and name like '%" . $keyword . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) FROM " . tablename("tiny_wmall_superredpacket") . $condition, $params);
    $mealRedpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_superredpacket") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    include itemplate("mealList");
}
if ($op == "post") {
    $_W["page"]["title"] = "套餐红包设置";
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $condition = " where uniacid = :uniacid";
        $params = array(":uniacid" => $_W["uniacid"]);
        $data = array("uniacid" => $_W["uniacid"], "name" => $_GPC["data"]["name"], "type" => "mealRedpacket", "data" => base64_encode(json_encode($_GPC["data"])));
        if (!empty($id)) {
            pdo_delete("tiny_wmall_mealredpacket_exchange", array("uniacid" => $_W["uniacid"], "redpacketid" => $id));
            pdo_update("tiny_wmall_superredpacket", $data, array("id" => $id, "uniacid" => $_W["uniacid"]));
        } else {
            $data["addtime"] = TIMESTAMP;
            $data["status"] = 1;
            pdo_insert("tiny_wmall_superredpacket", $data);
            $id = pdo_insertid();
        }
        $exchanges = $_GPC["data"]["exchanges"];
        if (!empty($exchanges) && $_GPC["data"]["params"]["exchangeStatus"] == 1) {
            foreach ($exchanges as $exchange) {
                if (0 < $exchange["store_id"]) {
                    $item = array("uniacid" => $_W["uniacid"], "redpacketid" => $id, "sid" => $exchange["store_id"], "discount" => $exchange["discount"], "condition" => $exchange["condition"], "grant_days_effect" => $exchange["grant_days_effect"], "use_days_limit" => $exchange["use_days_limit"]);
                    pdo_insert("tiny_wmall_mealredpacket_exchange", $item);
                }
            }
        }
        imessage(error(0, "套餐红包设置成功"), iurl("mealRedpacket/meal/post", array("id" => $id)), "ajax");
    }
    if (!empty($id)) {
        $mealRedpacket = pdo_fetch("select * from " . tablename("tiny_wmall_superredpacket") . " where uniacid = :uniacid and id = :id", array(":id" => $id, ":uniacid" => $_W["uniacid"]));
        if (!empty($mealRedpacket)) {
            $mealRedpacket["data"] = json_decode(base64_decode($mealRedpacket["data"]), true);
        }
    } else {
        $mealRedpacket_yes = pdo_fetch("select id from " . tablename("tiny_wmall_superredpacket") . " where uniacid = :uniacid and type = :type and status = 1", array(":uniacid" => $_W["uniacid"], ":type" => "mealRedpacket"));
        if (!empty($mealRedpacket_yes)) {
            imessage("已有套餐红包活动, 如需重新添加套餐红包活动，请先撤销其他活动", referer(), "info");
        }
    }
    include itemplate("mealPost");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    pdo_update("tiny_wmall_superredpacket", array("status" => 2), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, "撤销成功"), referer(), "ajax");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    imessage(error(0, "删除成功"), referer(), "ajax");
}

?>