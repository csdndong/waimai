<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $config = $_W["we7_wmall"]["config"]["mall"];
    if ($_W["ispost"]) {
        $result = plateform_login($_POST);
        if (is_error($result)) {
            imessage(error(-1, $result["message"]), "", "ajax");
        }
        $user = plateform_fetch($result["token"], "token");
        $result = array("user" => array("token" => (string) $user["usertype"] . ":" . $user["token"], "perms" => $user["perms"], "role" => $user["role"]));
        imessage(error(0, $result), "", "ajax");
    }
    $result = array("config" => $config);
    imessage(error(0, $result), "", "ajax");
}

?>