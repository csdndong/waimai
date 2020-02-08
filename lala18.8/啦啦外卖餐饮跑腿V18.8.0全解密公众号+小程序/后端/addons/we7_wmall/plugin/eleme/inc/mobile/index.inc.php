<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$description = get_config_text("eleme:description");
include itemplate("index");

?>