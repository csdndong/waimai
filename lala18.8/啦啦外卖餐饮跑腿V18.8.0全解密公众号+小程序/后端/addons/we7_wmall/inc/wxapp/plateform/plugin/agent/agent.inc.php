<?php
/*

 
 
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " AND id = :id";
        $params[":id"] = $agentid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (title like :keyword or area like :keyword)";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("select * from " . tablename("tiny_wmall_agent") . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "del") {
        $id = intval($_GPC["id"]);
        pdo_delete("tiny_wmall_agent", array("id" => $id, "uniacid" => $_W["uniacid"]));
        imessage(error(0, ""), "", "ajax");
    } else {
        if ($ta == "status") {
            $id = intval($_GPC["id"]);
            $status = intval($_GPC["status"]);
            pdo_update("tiny_wmall_agent", array("status" => $status), array("id" => $id, "uniacid" => $_W["uniacid"]));
            imessage(error(0, ""), "", "ajax");
        }
    }
}

?>