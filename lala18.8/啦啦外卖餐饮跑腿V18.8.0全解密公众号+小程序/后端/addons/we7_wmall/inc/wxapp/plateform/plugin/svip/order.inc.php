<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("plugin");
pload()->model("svip);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $meals = svip_meal_getall();
    $data = svip_order_getall();
    $result = array("records" => $data["orders"], "meals" => $meals);
    imessage(error(0, $result), "", "ajax");
}

?>