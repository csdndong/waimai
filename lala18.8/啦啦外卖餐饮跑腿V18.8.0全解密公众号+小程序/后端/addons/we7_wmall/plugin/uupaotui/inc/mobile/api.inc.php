<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
pload()->classs("subscribe");
$subscribe = new subscribe();
$subscribe->start();
echo 1234564;

?>