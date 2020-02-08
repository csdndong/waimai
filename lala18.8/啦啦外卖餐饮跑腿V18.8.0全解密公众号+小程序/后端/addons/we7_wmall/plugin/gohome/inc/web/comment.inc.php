<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "订单评论";
    $condition = "where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 10;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_gohome_comment") . $condition, $params);
    $comments = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_gohome_comment") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($comments)) {
        $tag_goods = gohome_comment_tags("goods");
        foreach ($comments as &$val) {
            $val["thumbs"] = iunserializer($val["thumbs"]);
            if (!empty($val["thumbs"])) {
                foreach ($val["thumbs"] as &$thumb) {
                    $thumb = tomedia($thumb);
                }
            }
            $val["goods"] = gohome_order_goods($val["goods_id"], $val["goods_type"]);
            $val["data"] = iunserializer($val["data"]);
            if (!empty($val["data"]["tag_goods"])) {
                $tags = array();
                $tags_keys = explode("|", $val["data"]["tag_goods"]);
                if (!empty($tags_keys)) {
                    foreach ($tags_keys as $keys) {
                        $tags[] = $tag_goods[$val["goods_quality"]]["tags"][$keys];
                    }
                }
            }
            $val["tag_goods"] = $tags;
        }
    }
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "status") {
        $id = intval($_GPC["comment_id"]);
        if (!empty($id)) {
            $comment = pdo_get("tiny_wmall_gohome_comment", array("uniacid" => $_W["uniacid"], "id" => $id));
            if (empty($comment)) {
                imessage(error(-1, "评论不存在或已删除"), "", "ajax");
            }
            pdo_update("tiny_wmall_gohome_comment", array("status" => intval($_GPC["status"])), array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            $ids = $_GPC["id"];
            if (!empty($ids)) {
                foreach ($ids as $value) {
                    $comment = pdo_get("tiny_wmall_gohome_comment", array("uniacid" => $_W["uniacid"], "id" => $value));
                    if (empty($comment)) {
                        imessage(error(-1, "评论不存在或已删除"), "", "ajax");
                    }
                    pdo_update("tiny_wmall_gohome_comment", array("status" => intval($_GPC["status"])), array("uniacid" => $_W["uniacid"], "id" => $value));
                }
            }
        }
        imessage(error(0, "设置评论状态成功"), referer(), "ajax");
    } else {
        if ($op == "reply") {
            if (!$_W["isajax"]) {
                return false;
            }
            $id = intval($_GPC["id"]);
            $reply = trim($_GPC["reply"]);
            pdo_update("tiny_wmall_gohome_comment", array("reply" => $reply, "replytime" => TIMESTAMP, "status" => 1), array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, ""), "", "ajax");
        }
    }
}
include itemplate("comment");

?>
