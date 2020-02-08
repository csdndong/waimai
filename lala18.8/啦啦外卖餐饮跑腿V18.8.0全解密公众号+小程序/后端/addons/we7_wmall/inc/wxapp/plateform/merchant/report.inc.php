<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND b.title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $status = intval($_GPC["status"]);
    if (-1 < $status) {
        $condition .= " AND a.status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("SELECT a.*,b.title as store_title FROM " . tablename("tiny_wmall_report") . "as a left join" . tablename("tiny_wmall_store") . "as b on a.sid = b.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$row) {
            $row["thumbs"] = iunserializer($row["thumbs"]);
            $row["addtime_cn"] = date("Y-m-d H:i", $row["addtime"]);
            if ($row["thumbs"]) {
                foreach ($row["thumbs"] as &$val) {
                    if (is_array($val)) {
                        $val = tomedia($val["image"]);
                    } else {
                        $val = tomedia($val);
                    }
                }
            }
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        $id = intval($_GPC["id"]);
        $status = intval($_GPC["status"]);
        pdo_update("tiny_wmall_report", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
        imessage(error(0, ""), "", "ajax");
    }
}

?>