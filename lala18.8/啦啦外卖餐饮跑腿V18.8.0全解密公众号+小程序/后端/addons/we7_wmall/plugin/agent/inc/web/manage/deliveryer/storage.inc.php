<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "配送员垃圾桶";
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid and status = 2";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (title like '%" . $keyword . "%' or nickname like '%" . $keyword . "%' or mobile like '%" . $keyword . "%')";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_deliveryer") . $condition, $params);
    $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "delete") {
        $id = intval($_GPC["id"]);
        if (!$id) {
            imessage(error(-1, "配送员不存在或已被删除"), "", "ajax");
        }
        pdo_delete("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        pdo_delete("tiny_wmall_store_deliveryer", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "deliveryer_id" => $id));
        pdo_delete("tiny_wmall_deliveryer_current_log", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "deliveryer_id" => $id));
        pdo_delete("tiny_wmall_deliveryer_getcash_log", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "deliveryer_id" => $id));
        mlog(4002, $id, "代理删除配送员");
        deliveryer_all(true);
        imessage(error(0, "删除配送员成功"), "", "ajax");
    } else {
        if ($op == "recover") {
            $id = intval($_GPC["id"]);
            if (!$id) {
                imessage(error(-1, "配送员不存在或已被删除"), "", "ajax");
            }
            pdo_update("tiny_wmall_deliveryer", array("status" => 1, "deltime" => ""), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
            mlog(4008, $id, "代理将配送员从回收站中恢复");
            deliveryer_all(true);
            imessage(error(0, "恢复配送员成功"), "", "ajax");
        }
    }
}
include itemplate("deliveryer/storage");

?>
