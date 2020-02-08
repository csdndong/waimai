<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "商户海报";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "post";
if ($op == "post") {
    if ($_W["ispost"]) {
        $poster = array("status" => $_GPC["data"]["page"]["status"]);
        $data = base64_encode(json_encode($_GPC["data"]));
        $poster["data"] = $data;
        $poster["bg"] = "";
        foreach ($_GPC["data"]["items"] as $part) {
            if ($part["id"] == "background") {
                $poster["bg"] = $part["params"]["imgurl"];
            } else {
                if ($part["id"] == "qrcode") {
                    $poster["qrcode"] = $part;
                }
            }
        }
        set_plugin_config("poster.store", $poster);
        imessage(error(0, "海报参数保存成功"), iurl("poster/store"), "ajax");
    }
    $poster = get_plugin_config("poster.store");
    if (!empty($poster["data"])) {
        $data = json_decode(base64_decode($poster["data"]), true);
    }
}
if ($op == "clear") {
    load()->func("file");
    @rmdirs(MODULE_ROOT . "/resource/poster/qrcode/" . $_W["uniacid"]);
    @rmdirs(MODULE_ROOT . "/resource/poster/store/" . $_W["uniacid"]);
    imessage("清除海报缓存成功", iurl("poster/store"), "success");
}
include itemplate("poster");

?>