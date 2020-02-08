<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
mload()->model("cloud");
if ($op == "index") {
    $_W["page"]["title"] = "小程序体验者";
    $testers = cloud_w_wxapp_get_tester();
    if (is_error($testers)) {
        imessage($testers, "", "info");
    }
}
if ($op == "del" && $_W["ispost"]) {
    $wechatids = $_GPC["id"];
    if (!is_array($wechatids)) {
        $wechatids = array($wechatids);
    }
    foreach ($wechatids as $wechatid) {
        $result = cloud_w_wxapp_unbind_tester($wechatid);
        if (is_error($result)) {
            imessage($result, "", "ajax");
        }
    }
    imessage(error(0, "解除绑定成功"), "", "ajax");
}
include itemplate("memberauth");

?>
