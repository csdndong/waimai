<?php


defined("IN_IA") or exit("Access Denied");

function h5app_push($users, $title, $msg = "", $url = "")
{
    global $_W;
    $config = $_W["we7_wmall"]["config"]["app"]["customer"];
    if (empty($config["appid"]) || empty($config["key"])) {
        return error(-1, "appid或key不完善");
    }
    if (empty($config["serial_sn"]) || strlen($config["serial_sn"]) != 32) {
    }
    load()->func("communication");
    if (!is_array($users)) {
        $users = array($users);
    }
    $array = array("appid" => $config["appid"], "key" => $config["key"], "title" => $title, "users" => implode(",", $users), "msg" => $msg, "url" => !empty($url) ? urlencode($url) : "");
    $query = http_build_query($array);
    $url = "http://pushmsg.ydbimg.com/rest/weblsq/1.0/PushMsg.aspx?" . $query;
    $response = ihttp_get($url);
    if (is_error($response)) {
        return $response;
    }
    $result = @json_decode($response["content"], true);
    if ($result["status"] != 1) {
        return error(-1, "错误代码: " . $result["status"] . ", 错误信息: " . $result["msg"]);
    }
    return true;
}

?>