<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $perms = array(array("title" => "跑腿", "name" => "errander", "thumb" => "https://1.xinzuowl.com/attachment/images/1/2016/09/BHhh5xNRhUEz7Enn43Hh6Y76cc5466.png", "badge" => 3), array("title" => "配送员", "name" => "deliveryer", "thumb" => "https://1.xinzuowl.com/attachment/images/1/2016/09/BHhh5xNRhUEz7Enn43Hh6Y76cc5466.png", "badge" => 0));
    $result = array("perms" => $perms);
    message(ierror(0, "", $result), "", "ajax");
}

?>