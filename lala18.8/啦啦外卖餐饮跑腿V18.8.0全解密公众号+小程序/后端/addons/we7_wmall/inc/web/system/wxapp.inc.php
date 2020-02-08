<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("cloud");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "小程序授权";
    $result = cloud_w_get_wxapp_authorize_list();
    if (is_error($result)) {
        imessage($result, "", "info");
    }
    $records = $result["message"]["records"];
    $url = $result["message"]["url"];
    $auth_num = $result["message"]["auth_num"];
    $max_wxapp = $result["message"]["max_wxapp"];
    include itemplate("system/wxapp");
}
if ($op == "del" && $_W["ispost"]) {
    imessage(error(0, "暂不支持删除功能"), "", "ajax");
    $ids = $_GPC["id"];
    $result = cloud_w_get_wxapp_authorize_del($ids);
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "删除公众号权限成功"), "", "ajax");
}

?>