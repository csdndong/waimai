<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $type = intval($_GPC["type"]);
    if ($type == 1) {
        $condition .= " and score >= 8";
    } else {
        if ($type == 2) {
            $condition .= " and score >= 4 and score <= 7";
        } else {
            if ($type == 3) {
                $condition .= " and score <= 3";
            }
        }
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $comments = pdo_fetchall("select * from " . tablename("tiny_wmall_order_comment") . $condition . " order by id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($comments)) {
        $comment_status = order_comment_status();
        foreach ($comments as &$row) {
            $row["data"] = iunserializer($row["data"]);
            $row["score"] = ($row["delivery_service"] + $row["goods_quality"]) * 10;
            $row["addtime"] = date("Y-m-d H:i", $row["addtime"]);
            $row["mobile"] = str_replace(substr($row["mobile"], 3, 6), "******", $row["mobile"]);
            $row["avatar"] = tomedia($row["avatar"]) ? tomedia($row["avatar"]) : "/static/img/avatar.png";
            $row["thumbs"] = iunserializer($row["thumbs"]);
            if (!empty($row["thumbs"])) {
                foreach ($row["thumbs"] as &$item) {
                    $item = tomedia($item);
                }
            }
            $row["scores"] = score_format($row["score"] / 20);
            $row["status_cn"] = $comment_status[$row["status"]]["text"];
            $row["status_css"] = $comment_status[$row["status"]]["css"];
            $row["self_audit_comment"] = $store["self_audit_comment"];
        }
    }
    $result = array("comments" => $comments, "store" => array("comment_reply" => $store["comment_reply"], "self_audit_comment" => $store["self_audit_comment"]));
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "status") {
        if (empty($store["self_audit_comment"])) {
            imessage(error(-1, "店铺不能自己审核评论"), "", "ajax");
        }
        $id = intval($_GPC["id"]);
        $comment = pdo_get("tiny_wmall_order_comment", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        if (empty($comment)) {
            imessage(error(-1, "评论不存在或已删除"), "", "ajax");
        }
        $status = intval($_GPC["status"]);
        if (2 < $status) {
            imessage(error(-1, "非法访问"), "", "ajax");
        }
        pdo_update("tiny_wmall_order_comment", array("status" => $status), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        store_comment_stat($comment["sid"]);
        imessage(error(0, "更新状态成功"), referer(), "ajax");
    } else {
        if ($ta == "reply") {
            $id = intval($_GPC["id"]);
            $comment = pdo_get("tiny_wmall_order_comment", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            if (empty($comment)) {
                imessage(error(-1, "评论不存在或已删除"), "", "ajax");
            }
            $reply = trim($_GPC["reply"]);
            $update = array("reply" => $reply, "replytime" => TIMESTAMP);
            if ($store["self_audit_comment"] == 1) {
                $update["status"] = 1;
            }
            pdo_update("tiny_wmall_order_comment", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
            store_comment_stat($comment["sid"]);
            imessage(error(0, $update), "", "ajax");
        } else {
            if ($ta == "comment_status") {
                $id = intval($_GPC["id"]);
                $comment = pdo_get("tiny_wmall_order_comment", array("uniacid" => $_W["uniacid"], "id" => $id));
                if (empty($comment)) {
                    imessage(error(-1, "评论不存在或已删除"), "", "ajax");
                }
                pdo_update("tiny_wmall_order_comment", array("status" => intval($_GPC["status"])), array("uniacid" => $_W["uniacid"], "id" => $id));
                store_comment_stat($comment["sid"]);
                imessage(error(0, ""), "", "ajax");
            }
        }
    }
}

?>