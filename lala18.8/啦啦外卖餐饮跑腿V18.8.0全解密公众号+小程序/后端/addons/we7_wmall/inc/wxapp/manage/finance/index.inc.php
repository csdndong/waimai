<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$result = array("store" => $store);
imessage(error(0, $result), "", "ajax");

?>