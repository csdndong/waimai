<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$do = "notice";
$id = intval($_GPC["id"]);
$notice = pdo_get("tiny_wmall_notice", array("id" => $id, "uniacid" => $_W["uniacid"]));
$_W["page"]["title"] = $notice["title"];
$_share = array("title" => $notice["title"], "desc" => $notice["description"], "link" => imurl("wmall/home/notice", array("id" => $id), true), "imgUrl" => tomedia($notice["thumb"]));
include itemplate("home/notice");

?>