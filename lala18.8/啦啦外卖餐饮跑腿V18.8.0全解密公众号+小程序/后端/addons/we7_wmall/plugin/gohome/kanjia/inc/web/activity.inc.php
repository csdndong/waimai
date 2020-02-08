<?php 
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "活动列表";
    if ($_W["ispost"] && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $data = array("name" => trim($_GPC["name"][$k]), "oldprice" => trim($_GPC["oldprice"][$k]), "total" => trim($_GPC["total"][$k]), "malldisplayorder" => intval($_GPC["malldisplayorder"][$k]));
            pdo_update("tiny_wmall_kanjia", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
        }
        imessage(error(0, "编辑活动商品成功"), iurl("kanjia/activity/list"), "ajax");
    }
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and name like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $sid = intval($_GPC["sid"]);
    if (!empty($sid)) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }
    $cateid = intval($_GPC["cateid"]);
    if (!empty($cateid)) {
        $condition .= " and cateid = :cateid";
        $params[":cateid"] = $cateid;
    }
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : "-1";
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 10;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_kanjia") . $condition, $params);
    $goods = pdo_fetchall("select * from " . tablename("tiny_wmall_kanjia") . $condition . " order by displayorder desc,id asc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($goods)) {
        foreach ($goods as &$da) {
            $da["thumb"] = tomedia($da["thumb"]);
        }
    }
    $goods_status = gohome_goods_status();
    $stores = store_fetchall(array("id", "title"));
    $categorys = pdo_fetchall("select id,title from " . tablename("tiny_wmall_kanjia_category") . " where uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "del") {
        $id = intval($_GPC["id"]);
        $result = gohome_del_goods($id, "kanjia");
        imessage($result, "", "ajax");
    }
}
include itemplate("activity");

?>