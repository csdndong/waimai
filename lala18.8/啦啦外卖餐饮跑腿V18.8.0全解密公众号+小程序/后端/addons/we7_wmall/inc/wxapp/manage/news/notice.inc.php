<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
$sid = intval($_GPC["__mg_sid"]);
if ($ta == "list") {
    $condition = " as b on a.id = b.notice_id where b.uid = :uid and a.uniacid = :uniacid and a.agentid = :agentid and a.type = :type and a.status = 1";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["manager"]["id"], ":type" => "store", ":agentid" => $store["agentid"]);
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $data = pdo_fetchall("select a.*,b.uid,b.is_new from " . tablename("tiny_wmall_notice") . " as a left join" . tablename("tiny_wmall_notice_read_log") . $condition . " order by id desc, displayorder desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$val) {
            $val["addtime"] = date("Y-m-d H:i:s", $val["addtime"]);
        }
    }
    $result = array("notice" => $data);
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "detail") {
    $notice = pdo_get("tiny_wmall_notice", array("id" => $_GPC["id"], "uniacid" => $_W["uniacid"], "status" => 1, "type" => "store"));
    if (empty($notice)) {
        imessage(error(0, "该消息不存在或已删除"), "", "ajax");
    }
    pdo_update("tiny_wmall_notice_read_log", array("is_new" => 0), array("notice_id" => $_GPC["id"], "uid" => $_W["manager"]["id"]));
    $result = array("notice" => $notice);
    imessage(error(0, $result), "", "ajax");
}

?>