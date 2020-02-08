<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "all";
if ($op == "all") {
    $datas = deliveryer_fetchall();
    $datas = array_values($datas);
    imessage(error(0, $datas), "", "ajax");
}
if ($op == "list") {
    if (isset($_GPC["key"])) {
        $key = trim($_GPC["key"]);
        $type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "is_takeout";
        $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and status = 1 and " . $type . " = 1 and title like :key order by id desc", array(":uniacid" => $_W["uniacid"], ":key" => "%" . $key . "%"), "id");
        if (!empty($data)) {
            foreach ($data as &$value) {
                if ($value["work_status"] == 1) {
                    $value["work_status"] = "接单中";
                } else {
                    $value["work_status"] = "休息中";
                }
                $value["avatar"] = tomedia($value["avatar"]);
            }
        }
        $deliveryers = array_values($data);
        imessage(array("errno" => 0, "message" => $deliveryers, "data" => $data), "", "ajax");
    }
    include itemplate("public/deliveryer");
}
if ($op == "link") {
    $type = empty($_GPC["type"]) ? "deliveryer" : trim($_GPC["type"]);
    $urls = wxapp_urls($type);
    include itemplate("public/plateformLink");
}

?>