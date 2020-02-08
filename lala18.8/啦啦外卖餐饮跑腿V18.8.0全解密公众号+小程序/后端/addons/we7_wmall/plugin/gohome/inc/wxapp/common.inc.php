<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]);
if ($op == "comment") {
    $id = intval($_GPC["id"]);
    $type = trim($_GPC["type"]);
    $comment = gohome_get_goods_comment($id, $type);
    $result = array("comment" => $comment["comment"]);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "share") {
        $id = intval($_GPC["id"]);
        $type = trim($_GPC["type"]);
        gohome_update_activity_flow($type, $id, "sharenum");
        imessage(error(0, ""), "", "ajax");
    }
}

?>