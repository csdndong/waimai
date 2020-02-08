<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth();
if ($op == "index") {
    $id = intval($_GPC["id"]);
    $wheel = get_wheel_data($id);
    if (empty($wheel)) {
        imessage(error(-1, "活动不存在或不可用"), "", "ajax");
    }
    $extra = array("order_id" => intval($_GPC["order_id"]));
    if ($_W["ispost"]) {
        $result = get_wheel_winner($wheel, $extra);
        if (is_error($result)) {
            imessage(error(-1, $result["message"]), "", "ajax");
        }
        imessage($result, "", "ajax");
    }
    $result = array("wheelData" => $wheel["data"]);
    $_W["_nav"] = 1;
    imessage(error(0, $result), "", "ajax");
} else {
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
        $page = max(1, intval($_GPC["page"]));
        $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 5;
        $records = pdo_fetchall("select * from " . tablename("tiny_wmall_wheel_record") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
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
        }
        $result = array("records" => $records);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($op == "status") {
            $id = intval($_GPC["id"]);
            $status = pdo_update("tiny_wmall_wheel_record", array("status" => 1), array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0,"奖品已发出"), "", "ajax");
        }
    }
}

?>
