<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where uniacid = :uniacid and addtype = 2";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND title like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_store") . $condition . " ORDER BY id DESC LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["user"] = store_manager($val["id"]);
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            if ($val["status"] == 1) {
                $val["status_cn"] = "审核通过";
            } else {
                if ($val["status"] == 2) {
                    $val["status_cn"] = "待审核";
                } else {
                    if ($val["status"] == 3) {
                        $val["status_cn"] = "审核未通过";
                    } else {
                        if ($val["status"] == 4) {
                            $val["status_cn"] = "回收站";
                        }
                    }
                }
            }
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "audit") {
        $id = intval($_GPC["id"]);
        $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $id));
        if (empty($store)) {
            imessage(error(-1, "门店不存在或已删除"), "", "ajax");
        }
        $clerk = store_manager($store["id"]);
        if (empty($clerk)) {
            imessage(error(-1, "获取门店申请人失败"), "", "ajax");
        }
        $status = intval($_GPC["status"]);
        pdo_update("tiny_wmall_store", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
        sys_notice_settle($store["id"], "clerk");
        imessage(error(0, ""), "", "ajax");
    }
}

?>