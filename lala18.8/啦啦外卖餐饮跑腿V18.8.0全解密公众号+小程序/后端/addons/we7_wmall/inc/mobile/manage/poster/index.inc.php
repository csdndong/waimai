<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
$_W["page"]["title"] = "商户海报";
$_config_plugin = get_plugin_config("poster.store");
if ($ta == "index") {
    mload()->model("poster");
    mload()->model("qrcode");
    $_config_plugin["data"] = json_decode(base64_decode($_config_plugin["data"]), true);
    $_config_qrcode = $_config_plugin["qrcode"];
    if ($_config_qrcode["params"]["type"] == "system" || empty($_config_qrcode["params"]["type"])) {
        $url = imurl("wmall/store/goods", array("sid" => $sid), true);
        $params = array("url" => $url, "size" => 4);
        $qrcode_url = qrcode_normal_build($params);
        if (is_error($qrcode)) {
            $respon = array("errno" => 1, "message" => "生成二维码失败");
            imessage($respon, "", "ajax");
        }
    } else {
        $params = array("scene_str" => "we7_wmall__store_" . $sid, "qrcode_type" => "fixed", "name" => (string) $store["title"] . "门店二维码");
        $qrcode = qrcode_wechat_build($params);
        if (is_error($qrcode)) {
            $respon = array("errno" => 1, "message" => "生成二维码失败" . $qrcode["message"]);
            imessage($respon, "", "ajax");
        }
        $qrcode_url = qrcode_url($qrcode["ticket"]);
    }
    $_config_plugin["poster"]["qrcode_url"] = $qrcode_url;
    $_config_plugin["poster"]["bg"] = $_config_plugin["bg"];
    $_config_plugin["poster"]["data"] = $_config_plugin["data"];
    $params = array("config" => $_config_plugin["poster"], "name" => "store_" . $sid, "extra" => array("nickname" => $store["title"], "avatar" => tomedia($store["logo"])), "plugin" => "store");
    $url = poster_create($params);
    if (is_error($data)) {
        $respon = array("errno" => 1, "message" => "生成海报失败");
        imessage($respon, "", "ajax");
    }
    $respon = array("errno" => 0, "message" => $url);
    imessage($respon, "", "ajax");
}
include itemplate("poster/index");

?>