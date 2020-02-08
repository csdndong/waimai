<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("gohome");
if ($ta == "list") {
    $records = gohome_order_fetchall();
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = gohome_order_fetch($id);
        $result = array("order" => $order);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "cancel") {
            $id = intval($_GPC["id"]);
            $result = gohome_order_update($id, "cancel");
            imessage($result, "", "ajax");
        } else {
            if ($ta == "favorite") {
                $goods_id = intval($_GPC["goods_id"]);
                $sid = intval($_GPC["sid"]);
                $type = trim($_GPC["type"]);
                $result = gohome_goods_favorite($goods_id, $sid, $type);
                imessage($result, "", "ajax");
            }
        }
    }
}

?>