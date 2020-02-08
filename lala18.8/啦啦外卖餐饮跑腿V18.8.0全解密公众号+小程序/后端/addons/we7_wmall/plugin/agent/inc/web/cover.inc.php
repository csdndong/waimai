<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "入口";
$urls = array("agent" => iurl("oauth/login", array("agent" => 1), true), "index" => imurl("wmall/home/index", array(), true));
include itemplate("cover");

?>