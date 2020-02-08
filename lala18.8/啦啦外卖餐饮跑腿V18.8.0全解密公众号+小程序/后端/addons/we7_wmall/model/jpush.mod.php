<?php
defined("IN_IA") or exit("Access Denied");
load()->func("communication");

function jpush_get_devices($registration_id)
{
    global $_W;
    if (empty($registration_id)) {
        return error(-1, "用户极光id为空");
    }
    $config = $_W["we7_wmall"]["config"]["app"]["deliveryer"];
    if (empty($config["push_key"]) || empty($config["push_secret"])) {
        return error(-1, "key或secret不完善");
    }
    if (empty($config["serial_sn"])) {
        return error(-1, "app序列号不完善");
    }
    $extra = array("Authorization" => "Basic " . base64_encode((string) $config["push_key"] . ":" . $config["push_secret"]), "Accept" => "application/json");
    $response = ihttp_request("https://device.jpush.cn/v3/devices/" . $registration_id, "", $extra);
    if (is_error($response)) {
        return $response;
    }
    $result = @json_decode($response["content"], true);
    if (!empty($result["error"])) {
        return error(-1, "错误代码: " . $result["error"]["code"] . ", 错误信息: " . $result["error"]["message"]);
    }
    return $result;
}
function jpush_update_devices($registration_id, $original, $dest)
{
    global $_W;
    if (empty($registration_id)) {
        return error(-1, "用户极光id为空");
    }
    $config = $_W["we7_wmall"]["config"]["app"]["deliveryer"];
    if (empty($config["push_key"]) || empty($config["push_secret"])) {
        return error(-1, "key或secret不完善");
    }
    if (empty($config["serial_sn"])) {
        return error(-1, "app序列号不完善");
    }
    $extra = array("Authorization" => "Basic " . base64_encode((string) $config["push_key"] . ":" . $config["push_secret"]), "Accept" => "application/json");
    $add = array_diff($dest["tags"], $original["tags"]);
    $remove = array_diff($original["tags"], $dest["tags"]);
    $params = array("tags" => array("add" => array_values($add), "remove" => array_values($remove)), "alias" => $original["alias"], "mobile" => $original["mobile"]);
    $response = ihttp_request("https://device.jpush.cn/v3/devices/" . $registration_id, json_encode($params), $extra);
    if (is_error($response)) {
        return $response;
    }
    $result = @json_decode($response["content"], true);
    if (!empty($result["error"])) {
        return error(-1, "错误代码: " . $result["error"]["code"] . ", 错误信息: " . $result["error"]["message"]);
    }
    return true;
}
function Jpush_platefrom_send($extras = array())
{
    global $_W;
    $_W["we7_wmall"]["config"]["app"]["plateform"] = get_plugin_config("plateformApp");
    $config = $_W["we7_wmall"]["config"]["app"]["plateform"]["app"];
    if (empty($config["push_key"]) || empty($config["push_secret"])) {
        return error(-1, "key或secret不完善");
    }
    if (empty($config["serial_sn"])) {
        return error(-1, "app序列号不完善");
    }
    $sound_router = array("takeout" => array("ordernew" => "orderNew.wav", "orderrefund" => "orderRefund.wav"), "errander" => array("ordernew" => "erranderOrderNew.wav", "orderrefund" => "erranderOrderRefund.wav"));
    $sound = $sound_router[$extras["redirect_type"]][$extras["notify_type"]];
    if (empty($sound)) {
        $sound = "default";
    }
    $extras["resource"] = (string) $_W["siteroot"] . "/addons/we7_wmall/resource/mp3/" . $sound;
    $tag = trim($config["push_tags"]["all"]);
    if (empty($audience)) {
        $audience = array("tag" => array($tag));
    }
    $jpush = array("platform" => "android", "audience" => $audience, "message" => array("msg_content" => $extras["voice_text"] ? $extras["voice_text"] : $extras["title"], "title" => $extras["title"], "extras" => $extras));
    load()->func("communication");
    $extra = array("Authorization" => "Basic " . base64_encode((string) $config["push_key"] . ":" . $config["push_secret"]));
    $cloud_extra = array("Authorization" => "Basic " . base64_encode("0c6d4c4fa27202b3a3cde173:27c13954167b692bc6f8a3be"));
    if (empty($config["android_build_type"])) {
        $extra = $cloud_extra;
    }
    $response = ihttp_request("https://api.jpush.cn/v3/push", json_encode($jpush), $extra);
    $return = Jpush_response_parse($response);
    if (is_error($return)) {
        slog("plateformappJpush", "平台管理App极光推送(andriod)通知管理员", $jpush, $return["message"]);
    }
    if (empty($config["ios_build_type"])) {
        $extra = $cloud_extra;
    }
    $jpush_ios = array("platform" => "ios", "audience" => $audience, "notification" => array("alert" => $extras["voice_text"] ? $extras["voice_text"] : $extras["title"], "ios" => array("alert" => $extras["voice_text"] ? $extras["voice_text"] : $extras["title"], "sound" => $sound, "badge" => "+1", "extras" => $extras)), "options" => array("apns_proudction" => 1));
    $response = ihttp_request("https://api.jpush.cn/v3/push", json_encode($jpush_ios), $extra);
    $return = Jpush_response_parse($response);
    if (is_error($return)) {
        slog("plateformappJpush", "平台管理App极光推送(ios)通知管理员", $jpush_ios, $return["message"]);
    }
    return true;
}

?>