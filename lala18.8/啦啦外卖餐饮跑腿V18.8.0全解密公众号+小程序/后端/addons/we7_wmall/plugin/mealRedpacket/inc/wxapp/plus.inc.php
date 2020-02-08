<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $result = mealRedpacket_plus_available_get();
    imessage(error(0, $result), "", "ajax");
}
if ($op == "submit") {
    $sid = intval($_GPC["sid"]);
    $meal_id = trim($_GPC["meal_id"]);
    $activity = mealRedpacket_plus_get(intval($_GPC["sid"]));
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
    $order = array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "sid" => $sid, "meal_id" => $meal_id, "order_sn" => date("YmdHis") . random(6, true), "final_fee" => intval($activity["data"]["redpackets"][$meal_id]["price"]), "is_pay" => 0, "addtime" => TIMESTAMP, "data" => iserializer($data));
    pdo_insert("tiny_wmall_superredpacket_meal_order", $order);
    $order_id = pdo_insertid();
    imessage(error(0, $order_id), "", "ajax");
}
if ($op == "mealorder") {
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $condition = " where uniacid = :uniacid and uid = :uid and type = :type and is_pay = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":type" => "mealRedpacket");
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_superredpacket_meal_order") . " " . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$order) {
            $order["addtime"] = date("Y-m-d H:i", $order["addtime"]);
            $order["data"] = iunserializer($order["data"]);
        }
    }
    imessage(error(0, $data), "", "ajax");
}

?>