<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "comment";
mload()->model("plugin");
pload()->model("gohome");
if ($ta == "comment") {
    $order_id = intval($_GPC["order_id"]);
    $order = gohome_order_fetch($order_id, true);
    if (empty($order)) {
        imessage(error(-1, "订单不存在"), "", "ajax");
    }
    if ($order["status"] < 5) {
        imessage(error(-1, "请确认核销后再进行评价"), "", "ajax");
    }
    if (5 < $order["status"]) {
        imessage(error(-1, "订单已完成或已取消，无法进行评价"), "", "ajax");
    }
    if ($_W["ispost"]) {
        $thumbs = array();
        if (!empty($_GPC["thumbs"])) {
            foreach ($_GPC["thumbs"] as $thumb) {
                if (!empty($thumb["filename"])) {
                    $thumbs[] = trim($thumb["filename"]);
                }
            }
        }
        $tag_goods = array();
        if (!empty($_GPC["tags"])) {
            foreach ($_GPC["tags"] as $tag) {
                if ($tag["active"] == 1) {
                    $tag_goods[] = intval($tag["id"]);
                }
            }
        }
        $data["tag_goods"] = implode("|", $tag_goods);
        $update = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "oid" => $order["id"], "uid" => $_W["member"]["uid"], "sid" => $order["sid"], "goods_id" => $order["goods_id"], "goods_type" => $order["order_type"], "username" => $order["username"], "mobile" => $order["mobile"], "goods_quality" => intval($_GPC["goods_quality"]), "note" => trim($_GPC["note"]), "thumbs" => empty($thumbs) ? "" : iserializer($thumbs), "status" => 0, "addtime" => TIMESTAMP, "data" => iserializer($data));
        pdo_insert("tiny_wmall_gohome_comment", $update);
        $id = pdo_insertid();
        if (0 < $id) {
            pdo_update("tiny_wmall_gohome_order", array("status" => 6), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $order["id"]));
            imessage(error(0, "评论成功"), "", "ajax");
        } else {
            imessage(error(0, "评论失败"), "", "ajax");
        }
    }
    $goods_tags = gohome_comment_tags("goods");
    $result = array("order" => $order, "goods_tags" => $goods_tags);
    imessage(error(0, $result), "", "ajax");
}

?>