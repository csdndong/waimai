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
    $data = gohome_order_fetchall($filter);
    $result = array("records" => $data["orders"]);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = gohome_order_fetch($id, true);
        $qrcode = iaurl("manage/gohome/order/confirm", array("code" => $order["code"]), true);
        $result = array("order" => $order, "qrcode" => $qrcode);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($ta == "cancel") {
            $id = intval($_GPC["id"]);
            $result = gohome_order_update($id, "cancel");
            imessage($result, "", "ajax");
        }
    }
}

?>