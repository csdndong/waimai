<?php
/*

 
 
 * 源码仅供研究学习，请勿用于商业用途
 */
defined('IN_IA') or exit('Access Denied');
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "分类列表";
    if ($_W["ispost"] && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
            pdo_update("tiny_wmall_tongcheng_category", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => intval($v)));
        }
        imessage(error(0, "编辑分类成功"), iurl("tongcheng/category/list"), "success");
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid and parentid = 0";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 10;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_tongcheng_category") . $condition, $params);
    $category = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_tongcheng_category") . $condition . " ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if (!empty($category)) {
        foreach ($category as $key => &$val) {
            $val["child"] = pdo_fetchall("select * from" . tablename("tiny_wmall_tongcheng_category") . "where uniacid = :uniacid and parentid = :parentid and agentid = :agentid order by displayorder desc,id asc", array(":uniacid" => $_W["uniacid"], ":parentid" => $key, ":agentid" => $_W["agentid"]));
        }
    }
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑分类";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $category = pdo_get("tiny_wmall_tongcheng_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
            $category["tags"] = iunserializer($category["tags"]);
            $category["tags"] = implode("\n", $category["tags"]);
            $category["config"] = iunserializer($category["config"]);
        }
        if ($_W["ispost"]) {
            $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "displayorder" => intval($_GPC["displayorder"]), "title" => trim($_GPC["title"]), "content" => trim($_GPC["content"]), "thumb" => trim($_GPC["thumb"]), "price" => floatval($_GPC["price"]), "status" => intval($_GPC["status"]), "is_hot" => intval($_GPC["is_hot"]), "link" => trim($_GPC["link"]), "tags" => explode("\n", trim($_GPC["tags"])));
            $data["tags"] = array_filter($data["tags"], trim);
            $data["tags"] = iserializer($data["tags"]);
            $data["config"] = array();
            if (!empty($_GPC["config"]["orderby"])) {
                $data["config"] = array("orderby" => trim($_GPC["config"]["orderby"]));
            }
            if (!empty($_GPC["config"]["stick_price"])) {
                foreach ($_GPC["config"]["stick_price"]["day"] as $key => $val) {
                    $val = trim($val);
                    if (empty($val)) {
                        continue;
                    }
                    $price = $_GPC["config"]["stick_price"]["price"][$key];
                    if (empty($price)) {
                        continue;
                    }
                    $stick_price[$val] = array("day" => $val, "price" => $price);
                }
                $data["config"]["stick_price"] = $stick_price;
            }
            $data["config"] = iserializer($data["config"]);
            if (empty($id)) {
                if (!empty($_GPC["parentid"])) {
                    $data["parentid"] = intval($_GPC["parentid"]);
                    pdo_insert("tiny_wmall_tongcheng_category", $data);
                } else {
                    pdo_insert("tiny_wmall_tongcheng_category", $data);
                }
            } else {
                pdo_update("tiny_wmall_tongcheng_category", $data, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $_GPC["id"]));
            }
            imessage(error(0, "编辑分类成功"), iurl("tongcheng/category/list"), "ajax");
        }
    } else {
        if ($op == "del") {
            $id = intval($_GPC["id"]);
            pdo_delete("tiny_wmall_tongcheng_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
            imessage(error(0, "删除分类成功"), iurl("tongcheng/category/list"), "ajax");
        } else {
            if ($op == "status") {
                $id = intval($_GPC["id"]);
                $type = trim($_GPC["type"]);
                if (empty($type)) {
                    $type = "status";
                }
                $value = intval($_GPC[$type]);
                pdo_update("tiny_wmall_tongcheng_category", array($type => $value), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
                imessage(error(0, ""), "", "ajax");
            }
        }
    }
}
include itemplate("category");

?>
