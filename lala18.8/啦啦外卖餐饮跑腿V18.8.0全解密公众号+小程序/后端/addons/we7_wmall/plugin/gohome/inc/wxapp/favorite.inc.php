<?php 
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $filter = $_GPC;
    $filter["uid"] = $_W["member"]["uid"];
    $records = gohome_favor_fetchall($filter);
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "favorite") {
        $goods_id = intval($_GPC["goods_id"]);
        $type = trim($_GPC["type"]);
        $result = gohome_goods_favorite($goods_id, $type);
        imessage($result, "", "ajax");
    }
}

?>
