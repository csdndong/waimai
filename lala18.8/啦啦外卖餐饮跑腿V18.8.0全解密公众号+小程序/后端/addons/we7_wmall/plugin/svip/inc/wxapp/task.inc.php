<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth();
if ($_config_plugin["basic"]["status"] != 1) {
    imessage(error(-1, "超级会员功能未开启"), "", "ajax");
}
if ($op == "index") {
    $filter = $_GPC;
    $filter["status"] = 1;
    $data = svip_task_getall($filter);
    $result = array("tasks" => $data["tasks"], "stat" => array("total_finish" => intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_svip_task_records") . " where uniacid = :uniacid and uid = :uid and status = 2", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"])))), "agreementMission" => get_config_text("agreement_mission_svip"));
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "takepart") {
        $id = intval($_GPC["id"]);
        $task = svip_task_takepart_check($id);
        if (is_error($task)) {
            imessage($task, "", "ajax");
        }
        $update = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "task_type" => $task["type"], "task_id" => $task["id"], "status" => 1, "addtime" => TIMESTAMP);
        $data = $task["data"];
        if ($data["task_endtime_type"] == 1) {
            $overtime = TIMESTAMP + intval($data["task_endtime"]) * 3600;
        } else {
            if ($data["task_endtime_type"] == 2) {
                $overtime = strtotime($data["task_endtime"]);
            } else {
                if ($data["task_endtime_type"] == 3) {
                    if ($data["task_endtime"] == "today") {
                        $overtime = strtotime(date("ymd")) + 86399;
                    } else {
                        if ($data["task_endtime"] == "week") {
                            $overtime = strtotime("next monday 00:00:00") - 1;
                        } else {
                            if ($data["task_endtime"] == "month") {
                                $overtime = strtotime("first day of next month 00:00:00") - 1;
                            }
                        }
                    }
                }
            }
        }
        $update["data"] = array("title" => $task["title"], "task_endtime_type" => $data["task_endtime_type"], "condition" => $data["condition"], "award" => $data["award"]);
        $update["data"] = iserializer($update["data"]);
        if (!empty($overtime)) {
            $update["overtime"] = $overtime;
        }
        pdo_insert("tiny_wmall_svip_task_records", $update);
        imessage(error(0, "领取任务成功，请尽快完成任务获得奖励"), "", "ajax");
    } else {
        if ($op == "takepartlist") {
            $filter = $_GPC;
            $filter["uid"] = $_W["member"]["uid"];
            $data = svip_task_takepart_records($filter);
            $result = array("records" => $data["records"]);
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>