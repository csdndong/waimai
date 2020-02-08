<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "location";
if ($ta == "location") {
    $id = intval($_GPC["id"]);
    $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($deliveryer)) {
        imessage(error(-1, "配送员不存在"), "", "ajax");
    }
    if ($deliveryer["status"] != 1) {
        imessage(error(-1, "配送员已被删除"), "", "ajax");
    }
    $deliveryer["avatar"] = tomedia($deliveryer["avatar"]);
    imessage(error(0, $deliveryer), "", "ajax");
}

?>