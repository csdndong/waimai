<?php

defined("IN_IA") or exit("Access Denied");
mload()->model("storebd");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";

?>
