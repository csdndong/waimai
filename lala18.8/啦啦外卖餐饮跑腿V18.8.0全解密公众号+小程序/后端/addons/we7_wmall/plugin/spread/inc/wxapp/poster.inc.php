<?php
/*
 * @ 买卖跑腿系统
 * @ APP公众号小程序版
 * @ PHP开源站，遵从PHP开源精神
 * @ 源码仅供学习研究，禁止商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$_W["page"]["title"] = "推广海报";
if ($op == "index") {
    $spread_group = pdo_fetch("select a.spread_groupid, b.become_child_limit, b.valid_period from " . tablename("tiny_wmall_members") . " as a left join " . tablename("tiny_wmall_spread_groups") . " as b on a.spread_groupid =b.id where a.uniacid = :uniacid and a.uid = :uid", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]));
    mload()->model("poster");
    mload()->model("qrcode");
    $_config_plugin["poster"]["data"] = json_decode(base64_decode($_config_plugin["poster"]["data"]), true);
    $_config_qrcode = $_config_plugin["poster"]["qrcode"];
    $array = array("url" => "pages/home/index", "scene" => "spread:" . $_W["member"]["uid"], "path" => "/we7_wmall/wxappqrcode/spread/" . $_W["uniacid"] . "/" . $_W["member"]["uid"] . "_spread.png");
    $qrcode_url = qrcode_wxapp_build($array);
    if (is_error($qrcode_url)) {
        $respon = array("errno" => 1, "message" => "生成二维码失败，失败原因：" . $qrcode_url["message"]);
        imessage($respon, "", "ajax");
    }
    $qrcode_url = tomedia($qrcode_url);
    $_config_plugin["poster"]["qrcode_url"] = $qrcode_url;
    $params = array("config" => $_config_plugin["poster"], "member" => $_W["member"], "name" => "spread_wxapp_" . $_W["member"]["uid"], "plugin" => "spread", "extra" => $_W["member"]);
    $url = poster_create($params);
    if (is_error($url)) {
        $respon = array("errno" => 1, "message" => "生成海报失败，失败原因：" . $url["message"]);
        imessage($respon, "", "ajax");
    }
    $reslut = array("relate" => $_config_plugin["relate"], "settle" => $_config_plugin["settle"], "respon" => $url, "group_relate" => $spread_group);
    imessage(error(0, $reslut), "", "ajax");
}
if ($op == "vue_index") {
    $spread_group = pdo_fetch("select a.spread_groupid, b.become_child_limit, b.valid_period from " . tablename("tiny_wmall_members") . " as a left join " . tablename("tiny_wmall_spread_groups") . " as b on a.spread_groupid =b.id where a.uniacid = :uniacid and a.uid = :uid", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]));
    mload()->model("poster");
    mload()->model("qrcode");
    $_config_plugin["poster"]["data"] = json_decode(base64_decode($_config_plugin["poster"]["data"]), true);
    $_config_qrcode = $_config_plugin["poster"]["qrcode"];
    if ($_config_qrcode["params"]["type"] == "system" || empty($_config_qrcode["params"]["type"])) {
        $url = ivurl("/pages/home/index", array("code" => $_W["member"]["uid"]), true);
        $params = array("url" => $url, "size" => 4);
        $qrcode_url = qrcode_normal_build($params);
        if (is_error($qrcode_url)) {
            $respon = array("errno" => -1, "message" => "生成二维码失败， 失败原因：" . $qrcode_url["message"]);
            imessage($respon, "", "ajax");
        }
    } else {
        $params = array("scene_str" => "we7_wmall_spread_" . $_W["uniacid"] . "_" . $_W["member"]["uid"], "qrcode_type" => "fixed", "uid" => $_W["member"]["uid"], "name" => "外卖推广海报", "type" => "spread");
        $qrcode = qrcode_wechat_build($params);
        if (is_error($qrcode)) {
            $respon = array("errno" => -1, "message" => "生成二维码失败，失败原因：" . $qrcode["message"]);
            imessage($respon, "", "ajax");
        }
        $qrcode_url = qrcode_url($qrcode["ticket"]);
    }
    $_config_plugin["poster"]["qrcode_url"] = $qrcode_url;
    $params = array("config" => $_config_plugin["poster"], "extra" => $_W["member"], "name" => "spread_" . $_W["member"]["uid"], "plugin" => "spread");
    $url = poster_create($params);
    if (is_error($url)) {
        $respon = array("errno" => -1, "message" => "生成海报失败，失败原因：" . $url["message"]);
        imessage($respon, "", "ajax");
    }
    $reslut = array("relate" => $_config_plugin["relate"], "settle" => $_config_plugin["settle"], "respon" => $url, "group_relate" => $spread_group);
    imessage(error(0, $reslut), "", "ajax");
}
if ($op == "qrcode") {
    $reslut = array("url" => ivurl("pages/home/index", array("code" => $_W["member"]["uid"]), true), "settle" => $_config_plugin["settle"]);
    imessage(error(0, $reslut), "", "ajax");
}

?>