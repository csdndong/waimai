<?php
* 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "套餐红包列表";
    $condition = " where uniacid = :uniacid and type = :type";
    $params = array(":uniacid" => $_W["uniacid"], ":type" => "meal");
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and name like '%" . $keyword . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) FROM " . tablename("tiny_wmall_superredpacket") . $condition, $params);
    $superRedpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_superredpacket") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    include itemplate("plusList");
}
if ($op == "post") {
    $_W["page"]["title"] = "套餐红包设置";
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $condition = " where uniacid = :uniacid";
        $params = array(":uniacid" => $_W["uniacid"]);
        $menudata = array("uniacid" => $_W["uniacid"], "name" => $_GPC["data"]["name"], "type" => "meal", "data" => base64_encode(json_encode($_GPC["data"])));
        if (!empty($id)) {
            pdo_update("tiny_wmall_superredpacket", $menudata, array("id" => $id, "uniacid" => $_W["uniacid"]));
        } else {
            $menudata["addtime"] = TIMESTAMP;
            $menudata["status"] = 1;
            pdo_insert("tiny_wmall_superredpacket", $menudata);
            $id = pdo_insertid();
        }
        imessage(error(0, "套餐红包设置成功"), iurl("mealRedpacket/plus/post", array("id" => $id)), "ajax");
    }
    if (!empty($id)) {
        $superRedpacket = pdo_fetch("select * from " . tablename("tiny_wmall_superredpacket") . " where uniacid = :uniacid and id = :id", array(":id" => $id, ":uniacid" => $_W["uniacid"]));
        if (!empty($superRedpacket)) {
            $superRedpacket["data"] = json_decode(base64_decode($superRedpacket["data"]), true);
        }
    } else {
        $superRedpacket_yes = pdo_fetch("select id from " . tablename("tiny_wmall_superredpacket") . " where uniacid = :uniacid and type = :type and status = 1", array(":uniacid" => $_W["uniacid"], ":type" => "meal"));
        if (!empty($superRedpacket_yes)) {
            imessage("已有套餐红包活动, 如需重新添加套餐红包活动，请先撤销其他活动", referer(), "info");
        }
    }
    include itemplate("plusPost");
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
