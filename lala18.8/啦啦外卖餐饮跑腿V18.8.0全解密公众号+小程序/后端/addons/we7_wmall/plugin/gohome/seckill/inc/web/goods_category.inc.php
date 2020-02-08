<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "post") {
    $_W["page"]["title"] = "编辑分类";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $item = pdo_get("tiny_wmall_seckill_goods_category", array("id" => $id, "uniacid" => $_W["uniacid"]));
    }
    if ($_W["ispost"]) {
        $data = array("uniacid" => intval($_W["uniacid"]), "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "link" => trim($_GPC["link"]), "status" => intval($_GPC["status"]), "displayorder" => intval($_GPC["displayorder"]));
        if (0 < $id) {
            pdo_update("tiny_wmall_seckill_goods_category", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_seckill_goods_category", $data);
        }
        imessage(error(0, "编辑商品分类成功"), iurl("seckill/goods_category/list"), "ajax");
    }
    include itemplate("goods_category");
} else {
    if ($op == "list") {
        $_W["page"]["title"] = "商品分类";
        if (checksubmit() && !empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
                pdo_update("tiny_wmall_seckill_goods_category", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
            imessage(error(0, "编辑商品分类成功"), iurl("seckill/goods_category/list"), "ajax");
        }
        if ($_W["uniacid"]) {
            $condition = " where uniacid = :uniacid";
            $params[":uniacid"] = $_W["uniacid"];
        }
        $agentid = intval($_GPC["agentid"]);
        if (0 < $agentid) {
            $condition .= " and agentid = :agentid";
            $params[":agentid"] = $agentid;
        }
        $pindex = max(1, intval($_GPC["page"]));
        $psize = 10;
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_seckill_goods_category") . $condition, $params);
        $category = pdo_fetchall("select * from" . tablename("tiny_wmall_seckill_goods_category") . $condition . " ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
        $pager = pagination($total, $pindex, $psize);
        include itemplate("goods_category");
        return 1;
    } else {
        if ($op == "del") {
            $id = intval($_GPC["id"]);
            pdo_delete("tiny_wmall_seckill_goods_category", array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "删除商品成功"), iurl("seckill/goods_category/list"), "ajax");
        } else {
            if ($op == "status") {
                $id = intval($_GPC["id"]);
                $status = intval($_GPC["status"]);
                pdo_update("tiny_wmall_seckill_goods_category", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
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
                                pdo_update("tiny_wmall_seckill_goods_category", array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], "id" => $value));
                            }
                        }
                        imessage(error(0, "批量操作修改成功"), iurl("seckill/goods_category/list"), "ajax");
                    }
                    include itemplate("op");
                }
            }
        }
    }
}

?>
