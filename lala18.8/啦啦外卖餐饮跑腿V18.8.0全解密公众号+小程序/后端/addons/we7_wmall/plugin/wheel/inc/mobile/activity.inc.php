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
icheckauth();
if ($op == "index") {
    $_W["page"]["title"] = "幸运抽奖";
    $id = intval($_GPC["id"]);
    $wheel = get_wheel_data($id);
    $wheel_data = $wheel["data"];
    $extra = array("order_id" => intval($_GPC["order_id"]));
    if ($_W["ispost"]) {
        $result = get_wheel_winner($wheel, $extra);
        if (is_error($result)) {
            imessage(error(-1, $result["message"]), "", "ajax");
        }
        imessage($result, "", "ajax");
    }
    include itemplate("activity");
}
if ($op == "record") {
    $_W["page"]["title"] = "中奖纪录";
    $condition = " where uniacid = :uniacid and uid = :uid";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $status = intval($_GPC["status"]);
    if ($status == "1") {
        $condition .= " and status = 1";
    } else {
        if ($status == "0") {
            $condition .= " and status = 0";
        }
    }
    $id = intval($_GPC["min"]);
    if (0 < $id) {
        $condition .= " and id < :id";
        $params[":id"] = $id;
    }
    $records = pdo_fetchall("select * from " . tablename("tiny_wmall_wheel_record") . $condition . " order by id desc limit 5", $params, "id");
    $min = 0;
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["award"] = iunserializer($val["award"]);
            $val["addtime"] = date("Y-m-d H:i:s", $val["addtime"]);
            $val["award_type"] = $val["award"]["data"]["type"];
            $val["type"] = awards_rank($val["type"], true);
            $val["award_type"] = award_type($val["award_type"]);
            if ($val["award_type"]["name"] != "redpacket") {
                $val["award_value"] = $val["award"]["data"]["value"];
            } else {
                foreach ($val["award"]["data"]["value"] as $redpacket) {
                    $val["award_value"][] = "红包：满" . $redpacket["condition"] . "减" . $redpacket["discount"];
                }
            }
        }
        $min = min(array_keys($records));
    }
    if ($_W["ispost"]) {
        $records = array_values($records);
        $respon = array("errno" => 0, "message" => $records, "min" => $min);
        imessage($respon, "", "ajax");
    }
    include itemplate("record");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = pdo_update("tiny_wmall_wheel_record", array("status" => 1), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, "奖品已发出"), imurl("wheel/activity/record"), "ajax");
}

?>