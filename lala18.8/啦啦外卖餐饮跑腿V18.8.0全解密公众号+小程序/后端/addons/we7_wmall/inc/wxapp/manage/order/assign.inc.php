<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("table");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "排号管理";
    $queues = pdo_fetchall("select * from " . tablename("tiny_wmall_assign_queue") . " where uniacid = :uniacid and sid = :sid order by guest_num asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    if (!empty($queues)) {
        foreach ($queues as &$val) {
            $val["total"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $val["id"]));
            $val["now_number"] = pdo_fetchcolumn("select number from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id order by id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $val["id"]));
        }
    }
    $result = array("queues" => $queues);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        $_W["page"]["title"] = "排号管理";
        $status = intval($_GPC["status"]);
        $queue_id = intval($_GPC["id"]);
        $now_number = trim($_GPC["number"]);
        $board = pdo_fetch("select id,number from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id order by id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $queue_id));
        if (empty($now_number)) {
            imessage(error(-1, "当前队列没有在排队号码"), "", "ajax");
        }
        if ($now_number != $board["number"]) {
            imessage(error(-1, "处理排号顺序有误刷新之后重试"), "", "ajax");
        }
        pdo_update("tiny_wmall_assign_board", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $board["id"]));
        $status = assign_notice($sid, $board["id"], $status);
        if (!is_error($status)) {
            pdo_update("tiny_wmall_assign_board", array("is_notify" => 1), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $board["id"]));
        }
        assign_notice_queue($board["id"], $queue_id);
        imessage(error(0, "改变状态成功"), "", "ajax");
    } else {
        if ($ta == "notice") {
            $queue_id = intval($_GPC["id"]);
            $now_number = trim($_GPC["number"]);
            $board = pdo_fetch("select id,number from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id order by id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $queue_id));
            if (empty($now_number)) {
                imessage(error(-1, "当前队列没有在排队号码"), "", "ajax");
            }
            if ($now_number != $board["number"]) {
                imessage(error(-1, "处理排号顺序有误刷新之后重试"), "", "ajax");
            }
            $status = assign_notice($sid, $board["id"], 2);
            if (!is_error($status)) {
                pdo_update("tiny_wmall_assign_board", array("is_notify" => 1), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $board["id"]));
                imessage(error(0, "通知成功"), "", "ajax");
            }
            imessage(error(-1, "通知失败:" . $status["message"]), "", "ajax");
        } else {
            if ($ta == "record") {
                $_W["page"]["title"] = "队列详情";
                $queue_id = intval($_GPC["id"]);
                $queue = pdo_get("tiny_wmall_assign_queue", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $queue_id));
                if (empty($queue)) {
                    imessage("队列不存在或已删除", "", "error");
                }
                $condition = " where uniacid = :uniacid and sid = :sid and queue_id = :queue_id";
                $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $queue_id);
                $page = max(1, intval($_GPC["page"]));
                $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
                $status = assign_board_status();
                $data = pdo_fetchall("select * from " . tablename("tiny_wmall_assign_board") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
                foreach ($data as &$val) {
                    if ($val["status"] == 1) {
                        $val["before_num"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and queue_id = :queue_id and id < :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $queue_id, ":id" => $val["id"]));
                    }
                }
                $result = array("queue_title" => $queue["title"], "records" => $data);
                imessage(error(0, $result), "", "ajax");
                return 1;
            } else {
                if ($ta == "del") {
                    $id = intval($_GPC["id"]);
                    pdo_delete("tiny_wmall_assign_board", array("uniacid" => $_W["uniacid"], "id" => $id));
                    imessage(error(0, "删除成功"), "", "ajax");
                } else {
                    if ($ta == "queue_post" && $_W["ispost"]) {
                        $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => trim($_GPC["title"]), "guest_num" => trim($_GPC["guest_num"]), "notify_num" => trim($_GPC["notify_num"]), "starttime" => trim($_GPC["starttime"]), "endtime" => trim($_GPC["endtime"]), "prefix" => trim($_GPC["prefix"]));
                        pdo_insert("tiny_wmall_assign_queue", $data);
                        imessage(error(0, "添加队列成功"), "", "ajax");
                    }
                }
            }
        }
    }
}

?>