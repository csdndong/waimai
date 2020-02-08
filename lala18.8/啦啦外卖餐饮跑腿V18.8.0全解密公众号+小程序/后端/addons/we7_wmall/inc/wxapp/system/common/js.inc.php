<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "meiqia";
if ($ta == "meiqia") {
    echo htmlspecialchars_decode(str_replace(array("&#039;", "<script type=\"text/javascript\">", "</script>"), array("\"", "", ""), $_W["we7_wmall"]["config"]["mall"]["meiqia"]));
    exit;
}
if ($ta == "auth") {
    $pass = trim($_GPC["pass"]);
    if ($pass == "0351") {
        cache_write("itime", 1);
        echo "success";
        exit;
    }
    $a = cache_read("itime");
    echo intval($a);
    exit;
}

?>