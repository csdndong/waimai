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
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "买单";
    $condition = " WHERE a.uniacid = :uniacid and a.agentid = :agentid and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
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
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"]), array("id", "title"), "id");
    $pager = pagination($total, $pindex, $psize);
    include itemplate("paycenter/paybill");
}

?>