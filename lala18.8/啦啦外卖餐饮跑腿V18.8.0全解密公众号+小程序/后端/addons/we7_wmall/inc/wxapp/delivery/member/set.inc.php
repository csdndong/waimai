<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "location") {
    $location_x = floatval($_GPC["location_x"]);
    $location_y = floatval($_GPC["location_y"]);
    if (empty($location_x) || empty($location_y)) {
        imessage(error(-1, "地理位置不完善"), "", "ajax");
    }
    pdo_update("tiny_wmall_deliveryer", array("location_x" => $location_x, "location_y" => $location_y), array("uniacid" => $_W["uniacid"], "id" => $_W["deliveryer"]["id"]));
    $data = array("uniacid" => $_W["uniacid"], "deliveryer_id" => $deliveryer["id"], "location_x" => $location_x, "location_y" => $location_y, "from" => !empty($_GPC["from"]) ? $_GPC["from"] : "app", "addtime" => TIMESTAMP, "addtime_cn" => date("Y-m-d H:i:s"));
    pdo_insert("tiny_wmall_deliveryer_location_log", $data);
    imessage(error(0, ""), "", "ajax");
} else {
    if ($ta == "slog") {
        $type = trim($_GPC["type"]);
        $title = trim($_GPC["title"]);
        $message = trim($_GPC["message"]);
        if (!empty($type) && !empty($message)) {
            slog($type, (string) $title . ":" . $deliveryer["title"], "", $message);
        }
        imessage(error(0, ""), "", "ajax");
    }
}

?>