<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
$config_mall = $_W["we7_wmall"]["config"]["mall"];
if ($op == "list") {
    $_W["page"]["title"] = "导航图标列表";
    if (checksubmit("submit") && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("title" => trim($_GPC["titles"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]));
            pdo_update("tiny_wmall_store_category", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => intval($v)));
        }
        imessage("编辑成功", iurl("dashboard/nav/list"), "success");
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid and parentid = :parentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":parentid" => 0);
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_store_category") . $condition, $params);
    $navs = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_store_category") . $condition . " ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if (!empty($navs)) {
        foreach ($navs as &$da) {
            $da["thumb"] = tomedia($da["thumb"]);
            $da["is_sys"] = 0;
            if (empty($da["wxapp_link"])) {
                $da["is_sys"] = 1;
                $da["wxapp_link"] = "pages/home/category?cid=" . $da["id"];
                $da["link"] = ivurl("pages/home/category", array("cid" => $da["id"]), true);
            }
            if ($config_mall["store_use_child_category"] == 1) {
                $da["child"] = pdo_fetchall("select * from" . tablename("tiny_wmall_store_category") . " where uniacid = :uniacid and parentid = :parentid order by displayorder desc,id asc", array(":uniacid" => $_W["uniacid"], ":parentid" => $da["id"]));
                if (!empty($da["child"])) {
                    foreach ($da["child"] as &$val) {
                        $val["thumb"] = tomedia($val["thumb"]);
                        $val["is_sys"] = 0;
                        if (empty($val["wxapp_link"])) {
                            $val["is_sys"] = 1;
                            $val["wxapp_link"] = "pages/home/category?cid=" . $val["id"] . "&child_id=" . $val["id"];
                            $val["link"] = ivurl("pages/home/category", array("cid" => $da["id"], "child_id" => $val["id"]), true);
                        }
                    }
                }
            }
        }
    }
    $pager = pagination($total, $pindex, $psize);
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_store_category", array("status" => $status), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_store_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    }
    imessage(error(0, "删除导航图标成功"), referer(), "ajax");
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑导航图标";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $category = pdo_get("tiny_wmall_store_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        if (!empty($category)) {
            $category["nav"] = (array) iunserializer($category["nav"]);
            $category["slide"] = (array) iunserializer($category["slide"]);
            if (!empty($category["slide"])) {
                $category["slide"] = array_sort($category["slide"], "displayorder", SORT_DESC);
            }
        }
    }
    if (empty($category)) {
        $category = array("slide_status" => 0, "slide" => array(), "nav_status" => 0, "nav" => array());
    }
    if ($_W["ispost"]) {
        $title = trim($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "标题不能为空"), "", "ajax");
        $nav = array();
        if (!empty($_GPC["nav_thumb"])) {
            foreach ($_GPC["nav_thumb"] as $k => $v) {
                if (empty($_GPC["nav_title"][$k])) {
                    continue;
                }
                $nav[] = array("title" => trim($_GPC["nav_title"][$k]), "sub_title" => trim($_GPC["nav_sub_title"][$k]), "link" => trim($_GPC["nav_links"][$k]), "thumb" => trim($v));
            }
        }
        $slide = array();
        if (!empty($_GPC["slide_image"])) {
            foreach ($_GPC["slide_image"] as $k => $v) {
                if (empty($v)) {
                    continue;
                }
                $slide[] = array("thumb" => trim($v), "link" => trim($_GPC["slide_links"][$k]), "displayorder" => trim($_GPC["slide_displayorder"][$k]));
            }
        }
        $data = array("uniacid" => $_W["uniacid"], "parentid" => intval($_GPC["parentid"]), "title" => $title, "agentid" => $_W["agentid"], "thumb" => trim($_GPC["thumb"]), "displayorder" => intval($_GPC["displayorder"]), "nav_status" => intval($_GPC["nav_status"]), "slide_status" => intval($_GPC["slide_status"]), "nav" => iserializer($nav), "slide" => iserializer($slide), "wxapp_link" => trim($_GPC["wxapp_link"]));
        if (empty($_GPC["id"])) {
            pdo_insert("tiny_wmall_store_category", $data);
        } else {
            pdo_update("tiny_wmall_store_category", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        }
        imessage(error(0, "编辑导航图标成功"), iurl("dashboard/nav/list"), "ajax");
    }
    if ($_GPC["is_child"] == 1) {
        $parents = pdo_fetchall("select id, title from" . tablename("tiny_wmall_store_category") . " where uniacid = :uniacid and agentid = :agentid and parentid = 0", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
    }
}
include itemplate("dashboard/nav");

?>