<?php
/*



 * @ 请勿传播
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "导航图标列表";
    if (checksubmit() && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
            pdo_update("tiny_wmall_gohome_category", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
        }
        imessage("编辑导航图标成功", iurl("gohome/nav/list"), "success");
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 10;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_gohome_category") . $condition, $params);
    $navs = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_gohome_category") . $condition . " ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    foreach ($navs as &$da) {
        $da["thumb"] = tomedia($da["thumb"]);
    }
    $pager = pagination($total, $pindex, $psize);
    include itemplate("nav");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_gohome_category", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "del") {
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_gohome_category", array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, "删除导航图标成功"), iurl("gohome/nav/list"), "ajax");
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑导航图标";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $category = pdo_get("tiny_wmall_gohome_category", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    if ($_W["ispost"]) {
        $title = trim($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "标题不能为空"), "", "ajax");
        $data = array("uniacid" => $_W["uniacid"], "title" => $title, "thumb" => trim($_GPC["thumb"]), "displayorder" => intval($_GPC["displayorder"]), "status" => intval($_GPC["status"]), "wxapp_link" => trim($_GPC["wxapp_link"]));
        if (!empty($id)) {
            pdo_update("tiny_wmall_gohome_category", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_gohome_category", $data);
        }
        imessage(error(0, "编辑导航图标成功"), iurl("gohome/nav/list"), "ajax");
    }
    include itemplate("nav");
}
if ($op == "navagent") {
    if ($_W["is_agent"]) {
        $agents = get_agents();
    }
    $ids = $_GPC["id"];
    $ids = implode(",", $ids);
    if ($_W["ispost"] && $_GPC["set"] == 1) {
        $navid = explode(",", $_GPC["id"]);
        $agentid = intval($_GPC["agentid"]);
        if (0 < $agentid) {
            foreach ($navid as $value) {
                pdo_update("tiny_wmall_gohome_category", array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], "id" => $value));
            }
        }
        imessage(error(0, "批量操作修改成功"), iurl("gohome/nav/list"), "ajax");
    }
    include itemplate("op");
}

?>