<?php

defined("IN_IA") or exit("Access Denied");
include IA_ROOT . "/addons/we7_wmall/version.php";
include "defines.php";
include "model.php";
class We7_wmallModule extends WeModule
{
    public function welcomeDisplay()
    {
        header("location: " . iurl("dashboard/index"));
        exit;
    }
}

?>