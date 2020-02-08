<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "post") {
    $_W["page"]["title"] = "编辑幻灯片";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $slide = pdo_get("tiny_wmall_slide", array("uniacid" => $_W["uniacid"], "id" => $id));
        if (empty($slide)) {
            imessage("幻灯片不存在或已删除", referer(), "error");
        }
    }
    if ($_W["ispost"]) {
        $title = trim($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "标题不能为空"), "", "ajax");
        $data = array("uniacid" => $_W["uniacid"], "title" => $title, "thumb" => trim($_GPC["thumb"]), "link" => trim($_GPC["link"]), "displayorder" => intval($_GPC["displayorder"]), "type" => trim($_GPC["type"]), "status" => intval($_GPC["status"]), "wxapp_link" => trim($_GPC["wxapp_link"]));
        if (!empty($slide)) {
            pdo_update("tiny_wmall_slide", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_slide", $data);
        }
        imessage(error(0, "编辑幻灯片成功"), iurl("dashboard/slide/list"), "ajax");
    }
    include itemplate("dashboard/slide");
}
if ($op == "list") {
    $_W["page"]["title"] = "幻灯片列表";
    if (checksubmit()) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["titles"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]));
                pdo_update("tiny_wmall_slide", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage("编辑幻灯片成功", iurl("dashboard/slide/list"), "success");
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $type = isset($_GPC["type"]) ? trim($_GPC["type"]) : "";
    if (!empty($type)) {
        $condition .= " and type = :type";
        $params[":type"] = $type;
    } else {
        $condition .= " and type != :type";
        $params[":type"] = "startpage";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_slide") . $condition, $params);
    $slides = pdo_fetchall("select * from" . tablename("tiny_wmall_slide") . $condition . " order by displayorder desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    include itemplate("dashboard/slide");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_slide", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    imessage(error(0, "删除幻灯片成功"), referer(), "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_slide", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "displayorder") {
    $id = intval($_GPC["id"]);
    $displayorder = intval($_GPC["displayorder"]);
    pdo_update("tiny_wmall_slide", array("displayorder" => $displayorder), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "slideagent") {
    if ($_W["is_agent"]) {
        $agents = get_agents();
    }
    $ids = $_GPC["id"];
    $ids = implode(",", $ids);
    if ($_W["ispost"] && $_GPC["set"] == 1) {
        $slideid = explode(",", $_GPC["id"]);
        $agentid = intval($_GPC["agentid"]);
        if (0 < $agentid) {
            foreach ($slideid as $value) {
                pdo_update("tiny_wmall_slide", array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], "id" => $value));
            }
        }
        imessage(error(0, "批量操作修改成功"), iurl("dashboard/slide/list"), "ajax");
    }
    include itemplate("dashboard/op");
}

?>