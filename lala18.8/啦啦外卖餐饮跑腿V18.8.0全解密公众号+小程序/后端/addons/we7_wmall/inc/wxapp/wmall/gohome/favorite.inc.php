<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("plugin");
pload()->model("gohome");
if ($ta == "list") {
    $filter = $_GPC;
    $filter["uid"] = $_W["member"]["uid"];
    $records = gohome_favor_fetchall($filter);
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "favorite") {
        $goods_id = intval($_GPC["goods_id"]);
        $type = trim($_GPC["type"]);
        $result = gohome_goods_favorite($goods_id, $type);
        imessage($result, "", "ajax");
    }
}

?>