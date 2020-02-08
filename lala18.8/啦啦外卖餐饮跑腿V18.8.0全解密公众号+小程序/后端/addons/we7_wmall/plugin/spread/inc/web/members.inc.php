<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "推广员";
    $condition = " where uniacid = :uniacid and is_spread = 1";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keywords = trim($_GPC["membername"]);
    if (!empty($keywords)) {
        $condition .= " and (nickname like '%" . $keywords . "%' or realname like '%" . $keywords . "%' or mobile like '%" . $keywords . "%')";
    }
    $spread_status = isset($_GPC["spread_status"]) ? intval($_GPC["spread_status"]) : -1;
    if (-1 < $spread_status) {
        $condition .= " and spread_status = :spread_status";
        $params[":spread_status"] = intval($_GPC["spread_status"]);
    }
    if (!empty($_GPC["spreadtime"]["start"]) && !empty($_GPC["spreadtime"]["end"])) {
        $spreadtime_starttime = strtotime($_GPC["spreadtime"]["start"]);
        $spreadtime_endtime = strtotime($_GPC["spreadtime"]["end"]);
        $condition .= " and spreadtime >= :spreadtime_starttime and spreadtime <= :spreadtime_endtime";
        $params[":spreadtime_starttime"] = $spreadtime_starttime;
        $params[":spreadtime_endtime"] = $spreadtime_endtime;
    }
    $spread_groupid = intval($_GPC["spread_groupid"]);
    if (!empty($spread_groupid)) {
        $condition .= " and spread_groupid = :spread_groupid";
        $params[":spread_groupid"] = intval($_GPC["spread_groupid"]);
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_members") . $condition, $params);
    $members = pdo_fetchall("select * from " . tablename("tiny_wmall_members") . $condition . " LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    foreach ($members as &$val) {
        $level1 = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and spread1 = :spread1", array(":uniacid" => $_W["uniacid"], ":spread1" => $val["uid"]));
        $level2 = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and spread2 = :spread2", array(":uniacid" => $_W["uniacid"], ":spread2" => $val["uid"]));
        $val["level1"] = intval($level1);
        $val["level2"] = intval($level2);
        $val["total"] = $val["level1"] + $val["\$level2"];
    }
    $pager = pagination($total, $pindex, $psize);
    $group = spread_groups();
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_update("tiny_wmall_members", array("is_spread" => 0, "spread_status" => 0, "spread1" => 0, "spread2" => 0), array("id" => $id));
        $spreadid = pdo_get("tiny_wmall_members", array("id" => $id), array("uid"));
        pdo_update("tiny_wmall_members", array("spread1" => 0), array("spread1" => $spreadid["uid"]));
        pdo_update("tiny_wmall_members", array("spread2" => 0), array("spread2" => $spreadid["uid"]));
    }
    imessage(error(0, "删除推广员成功"), "", "ajax");
}
if ($op == "status") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $value = intval($_GPC["value"]);
    if ($value == 0) {
        $type = "fail";
    } else {
        if ($value == 1) {
            $type = "success";
        }
    }
    foreach ($ids as $id) {
        pdo_update("tiny_wmall_members", array("spread_status" => $value), array("id" => $id));
        $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "id" => $id));
        sys_notice_spread_settle($member["uid"], $type);
    }
    imessage(error(0, ""), iurl("spread/members"), "ajax");
}
if ($op == "changes") {
    $id = intval($_GPC["id"]);
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "id" => $id));
    if ($_W["ispost"]) {
        $change_type = intval($_GPC["change_type"]);
        $spreadcredit2 = floatval($_GPC["spreadcredit2"]);
        $remark = trim($_GPC["remark"]);
        $fee = $spreadcredit2 - $member["spreadcredit2"];
        if ($change_type == 1) {
            $fee = "+" . $spreadcredit2;
        } else {
            if ($change_type == 2) {
                $fee = "-" . $spreadcredit2;
                if ($spreadcredit2 < 0) {
                    $fee = "-" . $member["spreadcredit2"];
                }
            }
        }
        $extra = array("trade_type" => 3, "remark" => $remark);
        spread_update_credit2($member["uid"], $fee, $extra);
        imessage(error(0, "更改账户余额成功"), referer(), "ajax");
    }
    include itemplate("membersOp");
    exit;
}
if ($op == "group") {
    if ($_W["ispost"]) {
        $memberId = explode(",", $_GPC["ids"]);
        if (empty($memberId)) {
            imessage(error(-1, "请选择推广员"), "", "ajax");
        }
        $spread_groupid = intval($_GPC["groupid"]);
        foreach ($memberId as $id) {
            pdo_update("tiny_wmall_members", array("spread_groupid" => $spread_groupid, "spread_groupid_change_from" => "manager"), array("uniacid" => $_W["uniacid"], "is_spread" => 1, "id" => $id));
        }
        imessage(error(0, "设置推广员等级成功！"), iurl("spread/members"), "ajax");
    }
    $groups = spread_groups();
    $ids = implode(",", $_GPC["id"]);
    include itemplate("membersOp");
    exit;
} else {
    include itemplate("members");
}

?>