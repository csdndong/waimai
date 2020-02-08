<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where uniacid = :uniacid and agentid = :agentid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":sid" => $sid);
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $record = pdo_fetchall("select * from " . tablename("tiny_wmall_store_getcash_log") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($record)) {
        foreach ($record as &$val) {
            $val["addtime"] = date("Y-m-d H:i", $val["addtime"]);
            if ($val["status"] == "1") {
                $val["status_cn"] = "提现成功";
            } else {
                if ($val["status"] == "2") {
                    $val["status_cn"] = "申请中";
                } else {
                    $val["status_cn"] = "已撤销";
                }
            }
        }
    }
    $result = array("record" => $record);
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "detail") {
    $id = intval($_GPC["id"]);
    $getcashDetail = pdo_get("tiny_wmall_store_getcash_log", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    if (empty($getcashDetail)) {
        imessage(error(-1, "交易记录不存在"), "", "ajax");
    }
    if ($getcashDetail["status"] == "1") {
        $getcashDetail["status_cn"] = "提现成功";
    } else {
        if ($getcashDetail["status"] == "2") {
            $getcashDetail["status_cn"] = "申请中";
        } else {
            $getcashDetail["status_cn"] = "已撤销";
        }
    }
    $getcashDetail["addtime"] = date("Y-m-d H:i", $getcashDetail["addtime"]);
    $result = array("getcashDetail" => $getcashDetail);
    imessage(error(0, $result), "", "ajax");
}

?>