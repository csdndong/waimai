<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("cloud");
global $_W;
global $_GPC;
$_W["ifrom"] = trim($_GPC["ifrom"]);
$ta = trim($_GPC["ta"]);
$post = file_get_contents("php://input");
if ($ta == "code") {
    $cgoods = 0;
    $file = MODULE_ROOT . "/inc/mobile/wmall/auth/wxlogin.inc.php";
    if (file_exists($file)) {
        include $file;
        $cgoods = Ashow;
    }
    $result = array("cgoods" => $cgoods);
    imessage(error(0, $result), "", "ajax");
}

?>