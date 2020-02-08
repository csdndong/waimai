<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "分类列表";
    if (checksubmit() && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
            pdo_update("tiny_wmall_kanjia_category", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
        }
        imessage("编辑分类成功", iurl("kanjia/category/list"), "success");
    }
    $condition = " where uniacid = :uniacid";
    $params = array( ":uniacid" => $_W["uniacid"] );
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 10;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_kanjia_category") . $condition, $params);
    $category = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_kanjia_category") . $condition . " ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if (!empty($category)) {
        foreach ($category as &$da) {
            $da["thumb"] = tomedia($da["thumb"]);
        }
    }
    $pager = pagination($total, $pindex, $psize);
    include itemplate("category");
    return 1;
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑分类";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $category = pdo_get("tiny_wmall_kanjia_category", array("uniacid" => $_W["uniacid"], "id" => $id));
        }
        if ($_W["ispost"]) {
            $data = array("uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "link" => trim($_GPC["link"]), "status" => trim($_GPC["status"]), "displayorder" => intval($_GPC["displayorder"]));
            if (empty($_GPC["id"])) {
                pdo_insert("tiny_wmall_kanjia_category", $data);
            } else {
                pdo_update("tiny_wmall_kanjia_category", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            }
            imessage(error(0, "编辑分类成功"), iurl("kanjia/category/list"), "ajax");
        }
        include itemplate("category");
    } else {
        if ($op == "del") {
            $id = intval($_GPC["id"]);
            pdo_delete("tiny_wmall_kanjia_category", array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "删除分类成功"), iurl("kanjia/category/list"), "ajax");
        } else {
            if ($op == "status") {
                $id = intval($_GPC["id"]);
                $status = intval($_GPC["status"]);
                pdo_update("tiny_wmall_kanjia_category", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
                imessage(error(0, ""), "", "ajax");
            } else {
                if ($op == "categoryagent") {
                    if ($_W["is_agent"]) {
                        $agents = get_agents();
                    }
                    $ids = $_GPC["id"];
                    $ids = implode(",", $ids);
                    if ($_W["ispost"] && $_GPC["set"] == 1) {
                        $categoryid = explode(",", $_GPC["id"]);
                        $agentid = intval($_GPC["agentid"]);
                        if (0 < $agentid) {
                            foreach ($categoryid as $value) {
                                pdo_update("tiny_wmall_kanjia_category", array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], "id" => $value));
                            }
                        }
                        imessage(error(0, "批量操作修改成功"), iurl("kanjia/category/list"), "ajax");
                    }
                    include itemplate("op");
                }
            }
        }
    }
}

?>
