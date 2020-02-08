<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$freelunch = freelunch_record_init();
if (is_error($freelunch)) {
    imessage(error(-1, $freelunch["message"]), "", "ajax");
}
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "common";
    $serial_sn = $type == "common" ? $freelunch["serial_sn"] : $freelunch["plus_serial_sn"];
    $record = freelunch_record_get($serial_sn, $type);
    if ($record["status"] == 2) {
        imessage(error(-1, "活动已开奖,现在去参加新一期活动"), "", "ajax");
    }
    $partake_status = freelunch_partaker_partake_status($_W["member"]["uid"], $record["id"], $type);
    $member_partaker = freelunch_member_partaker($record["id"]);
    $luckiers = pdo_fetchall("select a.*,b.nickname,b.mobile,b.avatar from " . tablename("tiny_wmall_freelunch_record") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.reward_uid= b.uid where a.uniacid = :uniacid and a.status = 2 and a.serial_sn < :serial_sn and a.type = :type and a.freelunch_id = :freelunch_id order by a.id desc limit 15", array(":uniacid" => $_W["uniacid"], ":serial_sn" => $freelunch["serial_sn"], ":type" => $type, ":freelunch_id" => $freelunch["id"]));
    if (!empty($luckiers)) {
        foreach ($luckiers as &$row) {
            $row["avatar"] = tomedia($row["avatar"]);
            $row["time"] = sub_time($row["endtime"]);
        }
    }
    $result = array("freelunch" => $freelunch, "record" => $record, "partake_status" => $partake_status, "member_partaker" => $member_partaker, "luckiers" => $luckiers);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "partakers") {
    $min_id = intval($_GPC["min"]);
    $record_id = intval($_GPC["record_id"]);
    $partakers = freelunch_record_partaker($record_id, $min_id);
    imessage($partakers, "", "ajax");
}
if ($op == "partake") {
    $time = TIMESTAMP - 600;
    pdo_query("delete from " . tablename("tiny_wmall_freelunch_partaker") . " where uniacid = :uniacid and addtime < :time and is_pay = 0", array(":uniacid" => $_W["uniacid"], ":time" => $time));
    $record = pdo_get("tiny_wmall_freelunch_record", array("uniacid" => $_W["uniacid"], "id" => intval($_GPC["record_id"])));
    if (empty($record)) {
        imessage(error(-1, "活动不存在"), "", "ajax");
    }
    if ($record["status"] == 2) {
        imessage(error(-1, "活动已开奖,现在去参加新一期活动"), "", "ajax");
    }
    $partake_status = freelunch_partaker_partake_status($_W["member"]["uid"], $record["id"], $record["type"]);
    if ($partake_status["errno"] == -1) {
        imessage($partake_status["message"], referer(), "info");
    }
    $insert = array("uniacid" => $_W["uniacid"], "freelunch_id" => $freelunch["id"], "record_id" => $record["id"], "serial_sn" => $record["serial_sn"], "uid" => $_W["member"]["uid"], "number" => "", "addtime" => TIMESTAMP, "final_fee" => $record["partaker_fee"], "order_sn" => date("YmdHis") . random(6, true), "is_pay" => 0);
    pdo_insert("tiny_wmall_freelunch_partaker", $insert);
    $partaker_id = pdo_insertid();
    $result = array("errno" => 0, "message" => $partaker_id);
    imessage($result, "", "ajax");
}
if ($op == "partake_success") {
    $partaker = pdo_fetch("select * from " . tablename("tiny_wmall_freelunch_partaker") . " where uniacid = :uniacid and uid = :uid and is_pay = 1 order by id desc", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]));
    if (!empty($partaker)) {
        $partaker["final_fee"] = floatval($partaker["final_fee"]);
    }
    $num = $partaker["number"] - 10000;
    $result = array("freelunch" => $freelunch, "partaker" => $partaker, "num" => $num);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "detail") {
    $id = intval($_GPC["record_id"]);
    $record = pdo_get("tiny_wmall_freelunch_record", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (!empty($record)) {
        $record["startime_cn"] = date("Y-m-d H:i:s", $record["startime"]);
        $record["endtime_cn"] = date("Y-m-d H:i:s", $record["endtime"]);
    }
    $record["percent"] = round(1 - $record["partaker_dosage"] / $record["partaker_total"], 2) * 100;
    if (0 < $record["reward_number"]) {
        $record["reward_number"] = str_split($record["reward_number"]);
        $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $record["reward_uid"]), array("avatar", "nickname"));
    }
    $mine_partaker = freelunch_member_partaker($record["id"]);
    $partake_status = freelunch_partaker_partake_status($_W["member"]["uid"], $record["id"], $record["type"]);
    $winner_partaker = freelunch_member_partaker($record["id"], $record["reward_uid"]);
    $result = array("freelunch" => $freelunch, "record" => $record, "mine_partaker" => $mine_partaker, "partake_status" => $partake_status, "winner_partaker" => $winner_partaker, "member" => $member, "uid" => $_W["member"]["uid"]);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "luckier") {
    $condition = " where a.uniacid = :uniacid and a.status = 2 and a.freelunch_id = :freelunch_id";
    $params = array(":uniacid" => $_W["uniacid"], ":freelunch_id" => $freelunch["id"]);
    $id = intval($_GPC["min"]);
    if (0 < $id) {
        $condition .= " and a.id < :id";
        $params[":id"] = $id;
    }
    $luckiers = pdo_fetchall("select a.*,b.nickname,b.mobile,b.avatar from " . tablename("tiny_wmall_freelunch_record") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.reward_uid= b.uid " . $condition . " order by a.id desc limit 15", $params, "id");
    $min = 0;
    if (!empty($luckiers)) {
        foreach ($luckiers as &$value) {
            $value["avatar"] = tomedia($value["avatar"]);
            $value["endtime"] = date("Y-m-d H:i:s", $value["endtime"]);
            $value["total"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_freelunch_partaker") . " where uniacid = :uniacid and record_id = :record_id and uid = :uid and is_pay = 1", array(":uniacid" => $_W["uniacid"], ":record_id" => $value["id"], ":uid" => $value["reward_uid"]));
        }
        $min = min(array_keys($luckiers));
    }
    $result = array("errno" => 0, "message" => array_values($luckiers), "min" => $min);
    imessage($result, "", "ajax");
}
if ($op == "rule") {
    imessage(error(0, $freelunch), "", "ajax");
}

?>