<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "入口";
$urls = array("index" => imurl("errander/index", array("agentid" => $_W["agentid"]), true));
include itemplate("cover");

?>