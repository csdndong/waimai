<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "购买记录";
    $adtypes = advertise_get_types();
    $types = array_keys($adtypes);
    $condition = " WHERE uniacid = :uniacid and is_pay = 1";
    $params[":uniacid"] = $_W["uniacid"];
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
    if (0 <= $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " AND sid = :sid";
        $params[":sid"] = $sid;
    }
    $type = isset($_GPC["type"]) ? trim($_GPC["type"]) : -1;
    if (!empty($type) && $type != -1) {
        $condition .= " AND type = :type";
        $params[":type"] = $type;
    }
    $days = isset($_GPC["days"]) ? intval($_GPC["days"]) : -2;
    $todaytime = strtotime(date("Y-m-d"));
    $starttime = $todaytime;
    $endtime = $starttime + 86399;
    if (-2 < $days) {
        if ($days == -1) {
            $starttime = strtotime($_GPC["addtime"]["start"]);
            $endtime = strtotime($_GPC["addtime"]["end"]);
            $condition .= " AND addtime > :start AND addtime < :end";
            $params[":start"] = $starttime;
            $params[":end"] = $endtime;
        } else {
            $starttime = strtotime("-" . $days . " days", $todaytime);
            $condition .= " and addtime >= :start";
            $params[":start"] = $starttime;
        }
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_advertise_trade") . $condition, $params);
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_advertise_trade") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $stores = store_fetchall(array("id", "logo", "title"));
    $pager = pagination($total, $pindex, $psize);
    include itemplate("trade");
}
if ($op == "setting") {
    $id = intval($_GPC["id"]);
    $trade = pdo_get("tiny_wmall_advertise_trade", array("id" => $id, "uniacid" => $_W["uniacid"]), array("sid", "title", "starttime", "endtime", "status", "type"));
    if ($_W["ispost"]) {
        $id = intval($_GPC["id"]);
        $status = intval($_GPC["change_status"]);
        if (!$status) {
            imessage(error(0, "操作成功"), referer(), "ajax");
        } else {
            if ($status == 2) {
                $update = array("status" => 2, "endtime" => TIMESTAMP);
            } else {
                $endtime = strtotime($_GPC["endtime"]);
                if ($trade["status"] == 2 && TIMESTAMP < $endtime && $trade["endtime"] < $endtime) {
                    $update = array("status" => 1);
                }
                $update["endtime"] = $endtime;
            }
        }
        pdo_update("tiny_wmall_advertise_trade", $update, array("id" => $id));
        if ($update["status"] == 2) {
            if (in_array($trade["type"], array("stick", "recommendHome", "recommendOther"))) {
                if ($trade["type"] == "stick") {
                    $update_store = array("is_stick" => 0);
                } else {
                    $update_store = array("is_recommend" => 0);
                }
                pdo_update("tiny_wmall_store", $update_store, array("id" => $trade["sid"]));
            }
        } else {
            advertise_cron($id);
        }
        imessage(error(0, "设置成功"), referer(), "ajax");
    }
    include itemplate("tradeOp");
}

?>