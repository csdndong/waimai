<?php
/*
 * @169170
 * @tb@开源学习用
 * @ 仅供学习，商业使用后果自负
 * @ 谢谢
 */

defined("IN_IA") or exit("Access Denied");
mload()->model("deliveryer");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "入口链接";
    $records_url = ivurl("plugin/pages/wheel/record", array(), true);
}
include itemplate("cover");

?>