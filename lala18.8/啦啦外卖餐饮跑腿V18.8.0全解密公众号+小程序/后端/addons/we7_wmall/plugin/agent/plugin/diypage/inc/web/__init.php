<?php

defined("IN_IA") or exit("Access Denied");
function show_json($status = 1, $return = NULL)
{
    $ret = array("status" => $status, "result" => $status == 1 ? array("url" => referer()) : array());
    if (!is_array($return)) {
        if ($return) {
            $ret["result"]["message"] = $return;
        }
        exit(json_encode($ret));
    }
    $ret["result"] = $return;
    if (isset($return["url"])) {
        $ret["result"]["url"] = $return["url"];
    } else {
        if ($status == 1) {
            $ret["result"]["url"] = referer();
        }
    }
    exit(json_encode($ret));
}

?>
