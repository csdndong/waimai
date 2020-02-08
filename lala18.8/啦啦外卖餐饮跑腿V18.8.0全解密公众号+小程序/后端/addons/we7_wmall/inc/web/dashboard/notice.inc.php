<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "公告列表";
    if (checksubmit()) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["titles"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]));
                pdo_update("tiny_wmall_notice", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage("编辑公告成功", iurl("dashboard/notice/list"), "success");
    }
    $condition = " where uniacid = :uniacid and type = :type";
    $params = array(":uniacid" => $_W["uniacid"], ":type" => "member");
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_notice") . $condition, $params);
    $notices = pdo_fetchall("select * from" . tablename("tiny_wmall_notice") . $condition . " order by displayorder desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    include itemplate("dashboard/notice");
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑公告";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $notice = pdo_get("tiny_wmall_notice", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    if (empty($notice)) {
        $notice = array("status" => 1);
    }
    if ($_W["ispost"]) {
        $data = array("uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "description" => trim($_GPC["description"]), "content" => htmlspecialchars_decode($_GPC["content"]), "link" => trim($_GPC["link"]), "displayorder" => intval($_GPC["displayorder"]), "status" => intval($_GPC["status"]), "addtime" => TIMESTAMP, "type" => "member", "wxapp_link" => trim($_GPC["wxapp_link"]));
        if (!empty($notice["id"])) {
            pdo_update("tiny_wmall_notice", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_notice", $data);
        }
        imessage(error(0, "更新公告成功"), iurl("dashboard/notice/list"), "ajax");
    }
    include itemplate("dashboard/notice");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_notice", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    imessage(error(0, "删除公告成功"), referer(), "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $data = array("status" => intval($_GPC["status"]));
    pdo_update("tiny_wmall_notice", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "noticeagent") {
    if ($_W["is_agent"]) {
        $agents = get_agents();
    }
    $ids = $_GPC["id"];
    $ids = implode(",", $ids);
    if ($_W["ispost"] && $_GPC["set"] == 1) {
        $noticeid = explode(",", $_GPC["id"]);
        $agentid = intval($_GPC["agentid"]);
        if (0 < $agentid) {
            foreach ($noticeid as $value) {
                pdo_update("tiny_wmall_notice", array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], "id" => $value));
            }
        }
        imessage(error(0, "批量操作修改成功"), iurl("dashboard/notice/list"), "ajax");
    }
    include itemplate("dashboard/op");
}

?>