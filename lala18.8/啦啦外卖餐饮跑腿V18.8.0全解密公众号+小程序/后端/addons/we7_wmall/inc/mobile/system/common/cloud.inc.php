<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("cloud");
global $_W;
global $_GPC;
$_W["ifrom"] = trim($_GPC["ifrom"]);
$ta = trim($_GPC["ta"]);
$post = file_get_contents("php://input");
if ($ta == "touch") {
    imessage(error(0, "success"), "", "ajax");
}
if ($ta == "build") {
    $data = cloud_w_parse_build($post);
    imessage($data, "", "ajax");
}
if ($ta == "schema") {
    cloud_w_parse_schema($post);
}
if ($ta == "download") {
    $data = cloud_w_parse_download($post);
    imessage($data, "", "ajax");
}
if ($ta == "stat") {
    $data = cloud_w_order_stat();
    imessage($data, "", "ajax");
}
if ($ta == "vue") {
    $isvue = is_file(MODULE_ROOT . "/template/vue/index.html");
    $result = array("vue" => intval($isvue));
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "itime") {
    set_system_config("itime", 0);
    imessage(error(0, $result), "", "ajax");
}

?>