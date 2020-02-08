<?php

defined("IN_IA") or exit("Access Denied");
function huangou_get_store_goods($sid)
{
    global $_W;
    $activity = pdo_get("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => 1, "type" => "huangou"), array("id", "data"));
    if (empty($activity)) {
        return error(-1, "门店没有可用的换购活动");
    }
    $activity["data"] = iunserializer($activity["data"]);
    $huangou_bargain = pdo_get("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "sid" => $sid, "type" => "huangou"), array("id", "title", "content"));
    $huangou_id = "bargain_" . $huangou_bargain["id"];
    $huangou_goods = goods_filter($sid, array("cid" => $huangou_id));
    return array("activity" => $huangou_bargain, "huangou_goods" => $huangou_goods, "price_limit" => floatval($activity["data"]["price_limit"]));
}

?>
