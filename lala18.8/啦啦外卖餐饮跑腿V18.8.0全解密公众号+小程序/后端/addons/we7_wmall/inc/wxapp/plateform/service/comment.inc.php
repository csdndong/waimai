<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("deliveryer");
if ($ta == "list") {
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $condition = " WHERE a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (b.uid = :uid or a.mobile like :keyword)";
        $params[":uid"] = $keyword;
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $status = intval($_GPC["status"]);
    if (-1 < $status) {
        $condition .= " AND a.status = :status";
        $params[":status"] = $status;
    }
    $comments = pdo_fetchall("SELECT a.*, b.uid,b.openid FROM " . tablename("tiny_wmall_order_comment") . " AS a LEFT JOIN " . tablename("tiny_wmall_order") . " AS b ON a.oid = b.id " . $condition . " ORDER BY a.addtime DESC LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($comments)) {
        $stores = store_fetchall(array("id", "title"));
        $deliveryers = deliveryer_all(true);
        foreach ($comments as &$row) {
            $row["store"] = (array) $stores[$row["sid"]];
            $row["deliveryer"] = (array) $deliveryers[$row["deliveryer_id"]];
            $row["score"] = ($row["delivery_service"] + $row["goods_quality"]) / 2;
            $row["data"] = iunserializer($row["data"]);
            $row["mobile"] = str_replace(substr($row["mobile"], 4, 4), "****", $row["mobile"]);
            $row["thumbs"] = iunserializer($row["thumbs"]);
            if ($row["thumbs"]) {
                foreach ($row["thumbs"] as &$val) {
                    $val = tomedia($val);
                }
            }
            $row["addtime_cn"] = date("Y-m-d H:i", $row["addtime"]);
            $row["replytime_cn"] = date("Y-m-d H:i", $row["replytime"]);
            $row["goods_quality"] = intval($row["goods_quality"]);
            $row["delivery_service"] = intval($row["delivery_service"]);
        }
    }
    $result = array("records" => $comments);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        $id = intval($_GPC["id"]);
        $comment = pdo_get("tiny_wmall_order_comment", array("uniacid" => $_W["uniacid"], "id" => $id));
        if (empty($comment)) {
            imessage(error(-1, "评论不存在或已删除"), "", "ajax");
        }
        pdo_update("tiny_wmall_order_comment", array("status" => intval($_GPC["status"])), array("uniacid" => $_W["uniacid"], "id" => $id));
        store_comment_stat($comment["sid"]);
        imessage(error(0, ""), "", "ajax");
    } else {
        if ($ta == "reply") {
            $id = intval($_GPC["id"]);
            $comment = pdo_get("tiny_wmall_order_comment", array("uniacid" => $_W["uniacid"], "id" => $id));
            $order = order_fetch($comment["oid"]);
            if (empty($order)) {
                imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
            }
            $update = array("reply" => trim($_GPC["reply"]), "replytime" => TIMESTAMP);
            pdo_update("tiny_wmall_order_comment", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
            store_comment_stat($order["sid"]);
            $reply = $update;
            $reply["replytime_cn"] = date("Y-m-d H:i", $update["replytime"]);
            imessage(error(0, $reply), "", "ajax");
        }
    }
}

?>