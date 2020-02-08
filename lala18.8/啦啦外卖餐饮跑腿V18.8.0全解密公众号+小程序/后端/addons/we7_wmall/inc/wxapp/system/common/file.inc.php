<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "image";
if ($ta == "image") {
    $media_id = trim($_GPC["media_id"]);
    $status = media_id2url($media_id);
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    $data = array("errno" => 0, "message" => $status, "url" => tomedia($status));
    imessage($data, "", "ajax");
}

?>