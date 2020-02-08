<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $sid = intval($_GPC["sid"]);
    $store = store_fetch($sid, array("title", "logo", "status"));
    $result = array("store" => $store);
    $can_comment = haodian_member_can_comment($sid);
    if (!$can_comment) {
        imessage(error(-1, "您已评论过，请勿重复评论"), "", "ajax");
    } else {
        imessage(error(0, $result), "", "ajax");
    }
} else {
    if ($op == "post") {
        if (!is_array($_GPC["thumbs"])) {
            $_GPC["thumbs"] = json_decode(htmlspecialchars_decode($_GPC["thumbs"]), true);
        }
        $thumbs = array();
        if (!empty($_GPC["thumbs"])) {
            foreach ($_GPC["thumbs"] as $thumb) {
                if (!empty($thumb["filename"])) {
                    $thumbs[] = trim($thumb["filename"]);
                }
            }
        }
        $sid = intval($_GPC["sid"]);
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "uid" => $_W["member"]["uid"], "sid" => $sid, "goods_id" => 0, "username" => $_W["member"]["nickname"], "avatar" => $_W["member"]["avatar"], "store_service" => intval($_GPC["haodianStar"]), "note" => trim($_GPC["note"]), "status" => 0, "thumbs" => empty($thumbs) ? "" : iserializer($thumbs), "addtime" => TIMESTAMP);
        $can_comment = haodian_member_can_comment($sid);
        if (!$can_comment) {
            imessage(error(-1, "您已评论过，请勿重复评论"), "", "ajax");
        }
        pdo_insert("tiny_wmall_gohome_comment", $data);
        $id = pdo_insertid();
        if (0 < $id) {
            haodian_score_update($sid);
            imessage(error(0, "评论成功"), "", "ajax");
            return 1;
        }
        imessage(error(0, "评论失败"), "", "ajax");
    }
}

?>