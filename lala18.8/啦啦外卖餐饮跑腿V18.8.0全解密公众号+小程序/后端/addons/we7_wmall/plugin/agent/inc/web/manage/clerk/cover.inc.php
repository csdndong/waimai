<?php
/*
 * @ 买卖跑腿系统
 * @ APP公众号小程序版
 * @ PHP开源站，遵从PHP开源精神
 * @ 源码仅供学习研究，禁止商业用途
 */

defined("IN_IA") or exit("Access Denied");
mload()->model("deliveryer");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "店员入口";
    $urls = array("wmerchant" => iurl("store/oauth/login", array(), true), "register" => imurl("manage/auth/register", array(), true), "login" => imurl("manage/auth/login", array(), true));
    include itemplate("clerk/cover");
}

?>