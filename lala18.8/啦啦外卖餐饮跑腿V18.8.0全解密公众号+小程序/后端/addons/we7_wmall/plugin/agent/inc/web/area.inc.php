<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
mload()->classs("pinyin");
$pinyin = new pinyin();
if ($op == "list") {
    $_W["page"]["title"] = "区域列表";
    if ($_W["ispost"] && !empty($_GPC["hids"])) {
        foreach ($_GPC["hids"] as $k => $v) {
            $spell = $pinyin->getAllPY($_GPC["title"][$k]);
            $initial = $pinyin->getFirstPY($_GPC["title"][$k]);
            $initial = strtoupper(substr($initial, 0, 1));
            $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]), "spell" => $spell, "initial" => $initial);
            pdo_update("tiny_wmall_agent_area", $data, array("id" => $v, "uniacid" => $_W["uniacid"]));
        }
        imessage(error(0, "区域设置成功"), referer(), "ajax");
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_agent_area") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]));
    $areas = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_agent_area") . " where uniacid = :uniacid ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, array(":uniacid" => $_W["uniacid"]));
    $pager = pagination($total, $pindex, $psize);
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑区域";
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $title = trim($_GPC["title"]);
        $spell = $pinyin->getAllPY($title);
        $initial = $pinyin->getFirstPY($title);
        $initial = strtoupper(substr($initial, 0, 1));
        $data = array("uniacid" => $_W["uniacid"], "title" => $title, "displayorder" => intval($_GPC["displayorder"]), "spell" => $spell, "initial" => $initial, "status" => 1);
        if (0 < $id) {
            pdo_update("tiny_wmall_agent_area", $data, array("id" => $id, "uniacid" => $_W["uniacid"]));
        } else {
            pdo_insert("tiny_wmall_agent_area", $data);
        }
        imessage(error(0, "编辑区域成功"), iurl("agent/area/list"), "ajax");
    }
    if (0 < $id) {
        $area = pdo_get("tiny_wmall_agent_area", array("id" => $id, "uniacid" => $_W["uniacid"]));
    }
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_agent_area", array("id" => $id, "uniacid" => $_W["uniacid"]));
    }
    imessage(error(0, "删除区域成功"), "", "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_agent_area", array("status" => $status), array("id" => $id, "uniacid" => $_W["uniacid"]));
    imessage(error(0, ""), "", "ajax");
}
include itemplate("area");

?>