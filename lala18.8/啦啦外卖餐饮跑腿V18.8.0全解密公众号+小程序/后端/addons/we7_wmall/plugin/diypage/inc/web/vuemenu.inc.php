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
    $_W["page"]["title"] = "菜单列表";
    $condition = " where uniacid = :uniacid and version = 2";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and name like '%" . $keyword . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_diypage_menu") . $condition, $params);
    $menus = pdo_fetchall("select * from " . tablename("tiny_wmall_diypage_menu") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
} else {
if ($op == "post") {
    $_W["page"]["title"] = "新建自定义菜单";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
        $_W["page"]["title"] = "编辑菜单";
        }
        if ($_W["ispost"]) {
            $data = $_GPC["menu"];
            $menudata = array("uniacid" => $_W["uniacid"], "name" => $data["name"], "data" => base64_encode(json_encode($data)), "updatetime" => TIMESTAMP, "version" => 2);
            if (!empty($id)) {
                pdo_update("tiny_wmall_diypage_menu", $menudata, array("id" => $id, "uniacid" => $_W["uniacid"]));
            } else {
                $menudata["addtime"] = TIMESTAMP;
            pdo_insert("tiny_wmall_diypage_menu", $menudata);
            $id = pdo_insertid();
        }
        imessage(error(0, "添加成功"), iurl("diypage/vuemenu/post", array("id" => $id)), "ajax");
    }
    if (!empty($id)) {
        $menu = diypage_menu($id);
    }
    } else {
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_delete("tiny_wmall_diypage_menu", array("uniacid" => $_W["uniacid"], "id" => $id));
    }
    imessage(error(0, "删除菜单成功"), referer(), "ajax");
        } else {
            if ($op == "copy") {
                $id = intval($_GPC["id"]);
                $menu = pdo_get("tiny_wmall_diypage_menu", array("uniacid" => $_W["uniacid"], "id" => $id));
                if (empty($menu)) {
                    imessage(error(-1, "菜单不存在或已删"), "", "ajax");
                }
                $menu["name"] = $menu["name"] . "-复制";
                unset($menu["id"]);
                pdo_insert("tiny_wmall_diypage_menu", $menu);
                imessage(error(0, "复制菜单成功"), "", "ajax");
            }
        }
    }
}
include itemplate("vue/menu");

?>