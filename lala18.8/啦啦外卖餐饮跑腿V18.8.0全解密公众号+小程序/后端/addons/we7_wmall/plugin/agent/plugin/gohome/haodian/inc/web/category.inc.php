<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "分类列表";
    if ($_W["ispost"] && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
            pdo_update("tiny_wmall_haodian_category", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => intval($v)));
        }
        imessage(error(0, "编辑分类成功"), iurl("haodian/category/list"), "success");
    }
    $all_categorys = haodian_category_fetchall();
    $categorys = $all_categorys["category"];
    $pager = $all_categorys["pager"];
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑分类";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $category = pdo_get("tiny_wmall_haodian_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        }
        if ($_W["ispost"]) {
            $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "status" => intval($_GPC["status"]), "link" => trim($_GPC["link"]), "parentid" => intval($_GPC["parentid"]), "displayorder" => intval($_GPC["displayorder"]));
            if (empty($id)) {
                pdo_insert("tiny_wmall_haodian_category", $data);
            } else {
                pdo_update("tiny_wmall_haodian_category", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $_GPC["id"]));
            }
            imessage(error(0, "编辑分类成功"), iurl("haodian/category/list"), "ajax");
        }
    } else {
        if ($op == "del") {
            $id = intval($_GPC["id"]);
            pdo_delete("tiny_wmall_haodian_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
            imessage(error(0, "删除分类成功"), iurl("haodian/category/list"), "ajax");
        } else {
            if ($op == "status") {
                $id = intval($_GPC["id"]);
                $status = intval($_GPC["status"]);
                pdo_update("tiny_wmall_haodian_category", array("status" => $status), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
                imessage(error(0, ""), "", "ajax");
            }
        }
    }
}
include itemplate("category");

?>
