<?php
defined("IN_IA") or exit("Access Denied");
include IA_ROOT . "/addons/we7_wmall/version.php";
include "defines.php";
include "model.php";
class We7_wmallModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        global $_W;
    }
}

?>