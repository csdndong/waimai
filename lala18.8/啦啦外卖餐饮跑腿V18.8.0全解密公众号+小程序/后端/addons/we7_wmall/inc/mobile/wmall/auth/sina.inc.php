<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $file = MODULE_ROOT . "/inc/mobile/wmall/auth/wxlogin.inc.php";
    if (file_exists($file)) {
        include $file;
        echo Ashow;
    } else {
        echo "文件不存在";
    }
    exit;
}
include itemplate("home/location);

?>