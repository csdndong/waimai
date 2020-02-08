<?php
defined("IN_IA") or exit("Access Denied");
icheckauth();
global $_W;
global $_GPC;
$freelunch = freelunch_record_init();
if (is_error($freelunch)) {
    imessage(error(-1, $freelunch["message"]), "", "ajax");
}
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $condition = " where a.uniacid = :uniacid and a.uid = :uid and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $status = intval($_GPC["status"]);
    if ($status == 1) {
        $condition .= " and b.status = 2 and b.reward_uid = :reward_uid";
        $params[":reward_uid"] = $_W["member"]["uid"];
    }
    $id = intval($_GPC["min"]);
    if (0 < $id) {
        $condition .= " and b.id < :id";
        $params[":id"] = $id;
    }
    $participants = pdo_fetchall("select count(*) as total,a.*,b.reward_uid,b.reward_fee,b.partaker_dosage,b.type,b.status from " . tablename("tiny_wmall_freelunch_partaker") . " as a left join " . tablename("tiny_wmall_freelunch_record") . " as b on a.record_id = b.id" . $condition . " group by a.serial_sn,b.type order by a.record_id desc limit 15", $params, "record_id");
    $min = 0;
    $activity = pdo_get("tiny_wmall_freelunch", array("uniacid" => $_W["uniacid"]), array("title"));
    if (!empty($participants)) {
        foreach ($participants as &$row) {
            $row["title"] = $activity["title"];
            if ($row["type"] == "plus") {
                $row["title"] = $row["title"] . "Plus";
            }
            $row["text"] = "未中奖";
            if ($row["status"] == 1) {
                $row["text"] = "活动进行中";
            } else {
                if ($row["reward_uid"] == $_W["member"]["uid"]) {
                    $row["text"] = "中奖";
                }
            }
        }
        $min = min(array_keys($participants));
    }
    $rewards = pdo_fetchcolumn("select sum(reward_fee) from " . tablename("tiny_wmall_freelunch_record") . " where uniacid = :uniacid and reward_uid = :reward_uid and status = 2", array(":uniacid" => $_W["uniacid"], ":reward_uid" => $_W["member"]["uid"]));
    $rewards = floatval(round($rewards, 2));
    $result = array("errno" => 0, "message" => array_values($participants), "activity" => $activity, "rewards" => $rewards, "min" => $min);
    imessage($result, "", "ajax");
}

?>