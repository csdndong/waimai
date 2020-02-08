<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "买单";
    $config = $_W["we7_wmall"]["config"];
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("note" => trim($_GPC["note"][$k]));
                pdo_update("tiny_wmall_paybill_order", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "编辑备注成功"), iurl("paycenter/paybill/index"), "success");
    }
    $condition = " WHERE a.uniacid = :uniacid and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $pay_type = trim($_GPC["pay_type"]);
    if (!empty($_GPC["pay_type"])) {
        $condition .= " and a.pay_type = :pay_type";
        $params[":pay_type"] = $pay_type;
    }
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " AND a.sid = :sid";
        $params[":sid"] = $sid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND (b.nickname LIKE '%" . $keyword . "%' OR b.mobile LIKE '%" . $keyword . "%' OR a.order_sn LIKE '%" . $keyword . "%')";
    }
    $uid = intval($_GPC["uid"]);
    if (0 < $uid) {
        $condition .= " AND a.uid = :uid";
        $params[":uid"] = $uid;
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]);
    } else {
        $starttime = strtotime("-7 day");
        $endtime = TIMESTAMP;
    }
    $condition .= " AND a.addtime > :start AND a.addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_paybill_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition, $params);
    $orders = pdo_fetchall("SELECT a.*,b.nickname,b.mobile,b.avatar FROM " . tablename("tiny_wmall_paybill_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition . " ORDER BY addtime DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    $pager = pagination($total, $pindex, $psize);
    include itemplate("paycenter/paybill");
    return 1;
} else {
    if ($op == "change_note") {
        $id = intval($_GPC["id"]);
        $data = pdo_get("tiny_wmall_paybill_order", array("uniacid" => $_W["uniacid"], "id" => $id));
        if ($_W["ispost"]) {
            if (empty($id)) {
                imessage(error(-1, "顾客不存在"), referer(), "ajax");
            }
            $note = trim($_GPC["note"]);
            pdo_update("tiny_wmall_paybill_order", array("note" => $note), array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "备注修改成功"), referer(), "ajax");
        }
        include itemplate("paycenter/paybillOp");
        exit;
    }
}

?>