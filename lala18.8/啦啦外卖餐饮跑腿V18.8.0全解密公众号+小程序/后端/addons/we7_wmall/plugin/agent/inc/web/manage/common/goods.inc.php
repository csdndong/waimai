<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if ($op == "list") {
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $sid = intval($_GPC["store_id"]);
    if (0 < $sid) {
        $condition .= " and a.sid = :sid";
        $params[":sid"] = $sid;
    }
    if (isset($_GPC["is_options"])) {
        $is_options = intval($_GPC["is_options"]);
        $condition .= " and a.is_options = :is_options";
        $params[":is_options"] = $is_options;
    }
    $key = trim($_GPC["key"]);
    if (!empty($key)) {
        $condition .= " and a.title like :key";
        $params[":key"] = "%" . $key . "%";
    }
    $condition .= " and b.status = :status and b.agentid = :agentid";
    $params[":status"] = 1;
    $params[":agentid"] = $_W["agentid"];
    $data = pdo_fetchall("select a.id, a.sid, a.title, a.thumb, a.price, a.total, b.status as store_status from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id" . $condition, $params, "id");
    if (!empty($data)) {
        foreach ($data as &$row) {
            $row["thumb"] = tomedia($row["thumb"]);
            if ($row["total"] == -1) {
                $row["total"] = "无限";
            }
        }
        $goods = array_values($data);
    }
    message(array("errno" => 0, "message" => $goods, "data" => $data), "", "ajax");
}

?>