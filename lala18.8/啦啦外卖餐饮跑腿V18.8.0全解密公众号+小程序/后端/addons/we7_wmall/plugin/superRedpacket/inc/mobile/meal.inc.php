<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(false);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "mealindex";
if ($op == "mealindex") {
    $_W["page"]["title"] = "套餐红包购买";
    $activity = superRedpacket_available_meal_get();
    include itemplate("mealindex");
}
if ($op == "submit") {
    $sid = intval($_GPC["sid"]);
    $meal_id = trim($_GPC["meal_id"]);
    $activity = superRedpacket_meal_get(intval($_GPC["sid"]));
    $data = array();
    if (!empty($activity) && $activity["status"] == 1) {
        if (!empty($activity["data"]["redpackets"][$meal_id]) && !empty($activity["data"]["redpackets"][$meal_id]["data"])) {
            $data["meal"] = $activity["data"]["redpackets"][$meal_id];
        } else {
            imessage(error(-1, "套餐不存在！"), "", "ajax");
        }
    } else {
        imessage(error(-1, "套餐红包活动不存在！"), "", "ajax");
    }
    $order = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "sid" => $sid, "meal_id" => $meal_id, "order_sn" => date("YmdHis") . random(6, true), "final_fee" => intval($_GPC["final_fee"]), "is_pay" => 0, "addtime" => TIMESTAMP, "data" => iserializer($data));
    pdo_insert("tiny_wmall_superredpacket_meal_order", $order);
    $order_id = pdo_insertid();
    imessage(error(0, $order_id), "", "ajax");
}
if ($op == "mealorder") {
    $_W["page"]["title"] = "套餐红包购买记录";
    $id = intval($_GPC["min"]);
    $condition = " where uniacid = :uniacid and uid = :uid and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    if (0 < $id) {
        $condition .= " AND id < :id";
        $params[":id"] = $id;
    }
    $datas = pdo_fetchall("select * from " . tablename("tiny_wmall_superredpacket_meal_order") . " " . $condition . " order by id desc limit 15", $params, "id");
    if (!empty($datas)) {
        foreach ($datas as &$data) {
            $data["addtime"] = date("Y-m-d H:i:s", $data["addtime"]);
            $data["data"] = iunserializer($data["data"]);
        }
    }
    $min = 0;
    if (!empty($datas)) {
        $min = min(array_keys($datas));
    }
    if ($_W["ispost"]) {
        $datas = array_values($datas);
        $respon = array("errno" => 0, "message" => $datas, "min" => $min);
        imessage($respon, "", "ajax");
    }
    include itemplate("mealorder");
}

?>