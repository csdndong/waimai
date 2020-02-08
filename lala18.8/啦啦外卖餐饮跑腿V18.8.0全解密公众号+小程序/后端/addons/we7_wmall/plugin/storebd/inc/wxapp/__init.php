<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
if ($_W["_action"] != "login") {
    icheckstorebduser();
}

?>