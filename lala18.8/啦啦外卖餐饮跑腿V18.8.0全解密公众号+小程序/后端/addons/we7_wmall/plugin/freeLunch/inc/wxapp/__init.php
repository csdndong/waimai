<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
$activity = pdo_get("tiny_wmall_freelunch", array("uniacid" => $_W["uniacid"]));
if (!empty($activity["share"])) {
    $activity["share"] = iunserializer($activity["share"]);
    $activity["share"]["imgUrl"] = tomedia($activity["share"]["imgUrl"]);
    $_W["_share"] = $activity["share"];
}

?>