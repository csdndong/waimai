<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "兑换码列表";
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " and a.status = :status ";
        $params[":status"] = $status;
    }
    $uid = intval($_GPC["uid"]);
    if (0 < $uid) {
        $condition .= " and a.uid = :uid ";
        $params[":uid"] = $uid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (b.mobile like :keyword or b.realname like :keyword) ";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    if (!empty($_GPC["exchangetime"]["start"]) && !empty($_GPC["exchangetime"]["end"])) {
        $_GPC["starttime"] = strtotime($_GPC["exchangetime"]["start"]);
        $_GPC["endtime"] = strtotime($_GPC["exchangetime"]["end"]);
    }
    if ($status == 2 && !empty($_GPC["starttime"]) && !empty($_GPC["endtime"])) {
        $condition .= " and a.exchangetime > :start AND a.exchangetime < :end";
        $params[":start"] = $_GPC["starttime"];
        $params[":end"] = $_GPC["endtime"];
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_delivery_cards_code") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition, $params);
    $codes = pdo_fetchall("select a.*, b.realname, b.avatar, b.mobile from " . tablename("tiny_wmall_delivery_cards_code") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition . " order by status asc, id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($codes)) {
        foreach ($codes as &$val) {
            $val["endtime_cn"] = date("Y-m-d H:i:s", $val["endtime"]);
            if ($val["status"] == 2) {
                $val["exchangetime_cn"] = date("Y-m-d H:i", $val["exchangetime"]);
            } else {
                if ($val["status"] == 3) {
                    $val["exchangetime_cn"] = "兑换码未被使用，已过期";
                } else {
                    $val["exchangetime_cn"] = "未兑换";
                }
            }
        }
    }
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "批量创建兑换码";
        $meals = pdo_fetchall("select * from " . tablename("tiny_wmall_delivery_cards") . " where uniacid = :uniacid order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"]), "id");
        if ($_W["ispost"]) {
            $meal_id = intval($_GPC["meal_id"]);
            $ids = array_keys($meals);
            if (!in_array($meal_id, $ids)) {
                imessage(error(-1, "请选择有效的套餐类型"), "", "ajax");
            }
            $number = intval($_GPC["number"]);
            if ($number <= 0) {
                imessage(error(-1, "兑换码数量应大于零"), "", "ajax");
            }
            $endtime = trim($_GPC["endtime"]);
            if (empty($endtime)) {
                imessage(error(-1, "兑换码兑换截止期不能为空"), "", "ajax");
            }
            $endtime = strtotime($endtime);
            if ($endtime <= TIMESTAMP) {
                imessage(error(-1, "兑换码兑换截止期不能小于当前时间"), "", "ajax");
            }
            for ($i = 0; $i < $number; $i++) {
                $insert = array("uniacid" => $_W["uniacid"], "deliverycard_id" => $meal_id, "code" => random(16, true), "days" => $meals[$meal_id]["days"], "endtime" => $endtime + 86399, "status" => 1);
                pdo_insert("tiny_wmall_delivery_cards_code", $insert);
            }
            imessage(error(0, "批量创建兑换码成功"), iurl("deliveryCard/code/list"), "ajax");
        }
    } else {
        if ($op == "del") {
            $ids = $_GPC["id"];
            if (!is_array($ids)) {
                $ids = array($ids);
            }
            foreach ($ids as $id) {
                pdo_delete("tiny_wmall_delivery_cards_code", array("uniacid" => $_W["uniacid"], "id" => $id));
            }
            imessage(error(0, "删除兑换码成功"), "", "ajax");
        }
    }
}
include itemplate("code");

?>
