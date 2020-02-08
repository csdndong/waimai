<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("cloud");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$wxapp_type = trim($_GPC["type"]) ? trim($_GPC["type"]) : "we7_wmall";
if ($op == "index") {
    $_W["page"]["title"] = "基础设置";
    if ($_W["ispost"]) {
        $url = cloud_w_get_wxapp_authorize_url($wxapp_type);
        imessage($url, "", "ajax");
    }
    $wxapp = cloud_w_get_wxapp_authorize_info($wxapp_type);
    if (is_error($wxapp)) {
        imessage($wxapp["message"], "", "error");
    }
    $wxapp = $wxapp["message"];
    include itemplate("release/index");
    exit;
}
if ($op == "get_url") {
    $result = cloud_w_wxapp_get_url($wxapp_type);
    if (is_error($result)) {
        imessage($result, iurl("wxapp/release/index", array("type" => $_GPC["type"])), "error");
    }
    include itemplate("release/op");
    exit;
}
if ($op == "commit") {
    $result = cloud_w_wxapp_commit($wxapp_type);
    imessage($result, iurl("wxapp/release/index", array("type" => $_GPC["type"])), "success");
}
if ($op == "get_category") {
    $result = cloud_w_wxapp_get_category($wxapp_type);
    if (is_error($result)) {
        imessage($result, iurl("wxapp/release/index", array("type" => $_GPC["type"])), "error");
    }
    include itemplate("release/op");
    exit;
}
if ($op == "submit_audit" && $_W["ispost"]) {
    if (!$_GPC["first_id"] || !$_GPC["second_id"] || !$_GPC["first_class"] || !$_GPC["second_class"]) {
        imessage("所选信息有误,请重新选择", iurl("wxapp/release/index", array("type" => $_GPC["type"])), "error");
    }
    $result = cloud_w_wxapp_submit_audit($wxapp_type);
    imessage($result["message"], iurl("wxapp/release/index", array("type" => $_GPC["type"])), "error");
}
if ($op == "release") {
    $result = cloud_w_wxapp_release($wxapp_type);
    if (is_error($result)) {
        imessage($result, iurl("wxapp/release/index", array("type" => $_GPC["type"])), "error");
    } else {
        $wxapp = $result["message"];
        set_plugin_config("wxapp.basic.release_version", $wxapp["release_version"]);
        $result = error(0, "发布小程序成功");
        imessage($result, iurl("wxapp/release/index", array("type" => $_GPC["type"])), "success");
    }
}
if ($op == "bind_tester") {
    if ($_W["ispost"]) {
        $wechatid = trim($_GPC["wechatid"]);
        if (empty($wechatid)) {
            imessage("体验者微信号不能为空", iurl("wxapp/release/index", array("type" => $_GPC["type"])), "error");
        }
        $result = cloud_w_wxapp_bind_tester($wechatid, $wxapp_type);
        imessage($result, iurl("wxapp/release/index", array("type" => $_GPC["type"])), "success");
    }
    include itemplate("release/op");
    exit;
}
if ($op == "undocodeaudit") {
    $result = cloud_w_wxapp_undocodeaudit($wxapp_type);
    if (is_error($result)) {
        imessage($result, iurl("wxapp/release/index"), "error");
    }
    imessage(error(0, "撤销审核成功"), iurl("wxapp/release/index", array("type" => $_GPC["type"])), "success");
}

?>
