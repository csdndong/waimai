<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "运行SQL";
    if ($_W["ispost"]) {
        $sql = $_POST["sql"];
        pdo_run($sql);
        imessage(error(0, "执行成功"), referer(), "ajax");
    }
    include itemplate("system/runsql");
}

?>