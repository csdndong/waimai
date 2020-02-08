<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $condition = " where a.uniacid = :uniacid and a.bd_id = :bd_id";
    $params = array(":uniacid" => $_W["uniacid"], ":bd_id" => $_W["storebd_user"]["id"]);
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $stores = pdo_fetchall("select a.*, b.title, b.logo from " . tablename("tiny_wmall_storebd_store") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id" . $condition . " order by a.id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($stores)) {
        foreach ($stores as &$value) {
            $value["fee_takeout"] = iunserializer($value["fee_takeout"]);
            $value["fee_instore"] = iunserializer($value["fee_instore"]);
            $value["addtime_cn"] = date("Y-m-d H:i", $value["addtime"]);
            $value["logo"] = tomedia($value["logo"]);
        }
    }
    $result = array("stores" => $stores);
    imessage(error(0, $result), "", "ajax");
}

?>