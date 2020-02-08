<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $filter = $_GPC;
    $filter["uid"] = $_W["member"]["uid"];
    $data = gohome_order_fetchall($filter);
    $result = array("records" => $data["orders"]);
    $_W["_nav"] = 1;
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "detail") {
        $id = intval($_GPC["id"]);
        $order = gohome_order_fetch($id, true);
        $qrcode = isurl("pages/gohome/order/detail", array("id" => $order["id"], "code" => $order["code"]), true);
        $result = array("order" => $order, "qrcode" => $qrcode);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($op == "cancel") {
            $id = intval($_GPC["id"]);
            $result = gohome_order_update($id, "cancel");
            imessage($result, "", "ajax");
        }
    }
}

?>