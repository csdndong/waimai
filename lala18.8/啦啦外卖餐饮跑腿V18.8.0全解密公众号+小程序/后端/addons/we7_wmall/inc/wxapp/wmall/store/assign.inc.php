<?php
defined("IN_IA") or exit("Access Denied");
define("ORDER_TYPE", "tangshi");
mload()->model("table");
mload()->model("goods");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
icheckauth();
$sid = intval($_GPC["sid"]);
$store = store_fetch($sid);
if (empty($store)) {
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}
if (!$store["is_assign"]) {
    imessage(error(-1000, "商家已经关闭排号功能"), "", "ajax");
}
if ($ta == "index") {
    if (!$_W["ispost"]) {
        $mine = pdo_get("tiny_wmall_assign_board", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "status" => 1));
        if (!empty($mine)) {
            imessage(error(1000, ""), "", "ajax");
        }
    }
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_assign_queue") . " where uniacid = :uniacid and sid = :sid and status = 1 order by guest_num asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid), "id");
    $queue_ids = array();
    if (!empty($data)) {
        foreach ($data as $key => &$row) {
            if (TIMESTAMP < strtotime($row["starttime"]) || strtotime($row["endtime"]) < TIMESTAMP) {
                unset($data[$key]);
                continue;
            }
            $wait = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, "queue_id" => $row["id"]));
            $row["wait"] = 0 < $wait ? $wait : 0;
        }
        $queue_ids = array_keys($data);
    }
    if ($_W["ispost"]) {
        $queue_id = intval($_GPC["queue_id"]) ? intval($_GPC["queue_id"]) : imessage(error(-1, "队列错误"), "", "ajax");
        if (!in_array($queue_id, $queue_ids)) {
            imessage(error(-1, "不合法的队列"), "", "ajax");
        }
        $queue = $data[$queue_id];
        $today = strtotime(date("Y-m-d"));
        if ($queue["updatetime"] < $today) {
            pdo_update("tiny_wmall_assign_queue", array("position" => 1, "updatetime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $queue_id));
            $queue["position"] = 1;
        }
        $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "queue_id" => $queue_id, "openid" => $_W["openid"], "mobile" => $_W["member"]["mobile"], "position" => $queue["position"], "number" => $queue["prefix"] . str_pad($queue["position"], 3, "0", STR_PAD_LEFT), "status" => 1, "is_notify" => 0, "createtime" => TIMESTAMP);
        pdo_insert("tiny_wmall_assign_board", $data);
        $board_id = pdo_insertid();
        pdo_update("tiny_wmall_assign_queue", array("position" => $queue["position"] + 1, "updatetime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $queue_id));
        assign_notice($sid, $board_id, 1);
        assign_notice_clerk($sid, $board_id);
        imessage(error(0, "排号成功"), "ajax");
    }
    if (empty($queue_ids)) {
        imessage(error(-1000, "门店未添加排号队列,无法取号"), "", "ajax");
    }
    $result = array("queues" => $data, "store" => $store, "queueid_select" => $queue_ids[0]);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "mine") {
        $mine = pdo_fetch("select a.id,a.number,a.status, a.queue_id,a.createtime, b.title from " . tablename("tiny_wmall_assign_board") . " as a left join " . tablename("tiny_wmall_assign_queue") . " as b on a.queue_id = b.id where a.uniacid = :uniacid and a.sid = :sid and a.uid = :uid and a.status = 1", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":uid" => $_W["member"]["uid"]));
        if (empty($mine)) {
            imessage(error(-1000, "请先取号"), "", "ajax");
        }
        $before_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id and id < :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $mine["queue_id"], ":id" => $mine["id"]));
        $mine["\$before_num"] = 0 < $before_num ? $before_num : 0;
        $mine["createtime_cn"] = date("Y-m-d H:i:s", $mine["createtime"]);
        $result = array("store" => $store, "mine" => $mine);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "cancel") {
            $id = intval($_GPC["id"]);
            $board = assign_board_fetch($id);
            if (empty($board)) {
                imessage(error(-1, "排队不存在"), "", "ajax");
            }
            pdo_update("tiny_wmall_assign_board", array("status" => 4), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            assign_notice($sid, $id, 4);
            assign_notice_queue($board["id"], $board["queue_id"]);
            imessage(error(0, "取消排号成功"), "", "ajax");
        }
    }
}

?>