<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "幻灯片列表";
    if ($_W["ispost"] && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("displayorder" => intval($_GPC["displayorder"][$k]));
            pdo_update("tiny_wmall_seckill_slide", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
        }
        imessage(error(0, "排序成功"), referer(), "ajax");
    }
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $taskid = intval($_GPC["taskid"]);
    if (0 < $taskid) {
        $condition .= " and a.taskid = :taskid";
        $params[":taskid"] = $taskid;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_seckill_slide") . " as a left join " . tablename("tiny_wmall_seckill_task") . " as b on a.taskid = b.id " . $condition, $params);
    $slides = pdo_fetchall("select a.*,b.title as tasktitle from " . tablename("tiny_wmall_seckill_slide") . " as a left join " . tablename("tiny_wmall_seckill_task") . " as b on a.taskid = b.id " . $condition . " order by a.displayorder desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    $tasks = pdo_fetchall("select id,title from " . tablename("tiny_wmall_seckill_task") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
}
if ($op == "post") {
    $_W["page"]["title"] = "幻灯片设置";
    $tasks = pdo_getall("tiny_wmall_seckill_task", array( "uniacid" => $_W["uniacid"] ), array( "id", "title" ));
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $slide = pdo_get("tiny_wmall_seckill_slide", array("id" => $id, "uniacid" => $_W["uniacid"]));
    }
    if ($_W["ispost"]) {
        $thumb = trim($_GPC["thumb"]) ? trim($_GPC["thumb"]) : imessage(error(-1, "请选择幻灯片"), referer(), "ajax");
        $insert = array("title" => trim($_GPC["title"]), "uniacid" => $_W["uniacid"], "displayorder" => intval($_GPC["displayorder"]), "thumb" => $thumb, "taskid" => $taskid, "link" => trim($_GPC["link"]), "status" => intval($_GPC["status"]));
        if (0 < $id) {
            pdo_update("tiny_wmall_seckill_slide", $insert, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_seckill_slide", $insert);
        }

        imessage(error(0, "编辑幻灯片成功"), iurl("seckill/slide/list"), "ajax");
    }
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_seckill_slide", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    imessage(error(0, "删除幻灯片成功"), "", "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_seckill_slide", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
include itemplate("slide");

?>