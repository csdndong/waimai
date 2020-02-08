<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND (b.title like :keyword or b.area like :keyword)";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " AND a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND a.status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("SELECT a.*,b.title,b.area FROM " . tablename("tiny_wmall_agent_getcash_log") . " as a left join " . tablename("tiny_wmall_agent") . " as b on a.agentid = b.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        $toaccount_status_arr = getcash_toaccount_status("", "all", true);
        foreach ($records as &$row) {
            $row["account"] = iunserializer($row["account"]);
            $row["avatar"] = tomedia($row["account"]["avatar"]);
            $row["addtime_cn"] = date("Y-m-d H:i", $row["addtime"]);
            $row["endtime_cn"] = date("Y-m-d H:i", $row["endtime"]);
            $row["toaccount_status_cn"] = $toaccount_status_arr[$row["toaccount_status"]]["text"];
            $row["toaccount_status_css"] = $toaccount_status_arr[$row["toaccount_status"]]["css"];
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        $id = intval($_GPC["id"]);
        $type = trim($_GPC["type"]);
        $extra = array();
        if ($type == "status") {
            $extra["status"] = intval($_GPC["status"]);
        } else {
            if ($type == "cancel") {
                $extra["remark"] = trim($_GPC["remark"]);
            }
        }
        mload()->model("agent");
        $result = agent_getcash_update($id, $type, $extra);
        imessage($result, "", "ajax");
    } else {
        if ($ta == "query") {
            $id = intval($_GPC["id"]);
            mload()->model("agent");
            $result = agent_getcash_update($id, "query");
            imessage($result, "", "ajax");
        }
    }
}

?>