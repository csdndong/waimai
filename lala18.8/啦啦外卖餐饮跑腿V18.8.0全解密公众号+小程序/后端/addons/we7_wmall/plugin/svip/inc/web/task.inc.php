<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
$task_types = svip_task_types();
if ($op == "list") {
    $_W["page"]["title"] = "任务中心";
    if ($_W["ispost"] && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $title = trim($_GPC["title"][$k]);
            if (empty($title)) {
                continue;
            }
            $data = array("title" => $title, "displayorder" => intval($_GPC["displayorder"][$k]));
            pdo_update("tiny_wmall_svip_task", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
        }
        imessage(error(0, "修改成功"), iurl("svip/task/list"), "ajax");
    }
    $task_type = trim($_GPC["task_type"]);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
    $status_all = svip_task_status();
    $data = svip_task_getall();
    $pager = $data["pager"];
    $tasks = $data["tasks"];
    include itemplate("task");
    return 1;
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑任务";
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $data = $_GPC["data"];
            $starttime = strtotime($data["activity_starttime"]);
            $endtime = strtotime($data["activity_endtime"]);
            if (empty($starttime)) {
                imessage(error(-1, "请设置任务开始时间"), "", "ajax");
            }
            if ($endtime <= $starttime) {
                imessage(error(-1, "开始时间不能小于结束时间"), "", "ajax");
            }
            $update = array("uniacid" => $_W["uniacid"], "type" => $data["activity_type"], "title" => $data["title"], "content" => $data["content"], "displayorder" => $data["displayorder"], "status" => 1, "starttime" => $starttime, "endtime" => $endtime, "data" => base64_encode(json_encode($data)));
            if (TIMESTAMP < $starttime) {
                $update["status"] = 2;
            }
            if (!empty($id)) {
                pdo_update("tiny_wmall_svip_task", $update, array("id" => $id, "uniacid" => $_W["uniacid"]));
            } else {
                $update["addtime"] = TIMESTAMP;
                pdo_insert("tiny_wmall_svip_task", $update);
                $id = pdo_insertid();
            }
            imessage(error(0, "设置会员任务成功"), iurl("svip/task/list"), "ajax");
        }
        if (!empty($id)) {
            $task = pdo_fetch("select * from " . tablename("tiny_wmall_svip_task") . " where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $id));
            if (!empty($task)) {
                $data = json_decode(base64_decode($task["data"]), true);
            }
        }
        $data["activity_types"] = $task_types;
        include itemplate("task");
    } else {
        if ($op == "del") {
            $id = intval($_GPC["id"]);
            pdo_delete("tiny_wmall_svip_task", array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "删除任务成功"), "", "ajax");
        } else {
            if ($op == "takepartlist") {
                $_W["page"]["title"] = "任务参与记录";
                $task_type = trim($_GPC["task_type"]);
                $task_id = intval($_GPC["task_id"]);
                $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
                if (!empty($_GPC["endtime"]["start"]) && !empty($_GPC["endtime"]["end"])) {
                    $_GPC["starttime"] = strtotime($_GPC["endtime"]["start"]);
                    $_GPC["endtime"] = strtotime($_GPC["endtime"]["end"]);
                }
                $data = svip_task_takepart_records();
                $tasks = pdo_getall("tiny_wmall_svip_task", array("uniacid" => $_W["uniacid"]), array("id", "title"));
                $pager = $data["pager"];
                $records = $data["records"];
                include itemplate("takepartlist");
            } else {
                if ($op == "del_takepartlist") {
                    $id = intval($_GPC["id"]);
                    pdo_delete("tiny_wmall_svip_task_records", array("uniacid" => $_W["uniacid"], "id" => $id));
                    imessage(error(0, "删除任务记录成功"), "", "ajax");
                }
            }
        }
    }
}

?>