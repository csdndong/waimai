<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$slides = sys_fetch_slide("homeTop", true);
$categorys = store_fetchall_category("parent_child");
$notices = pdo_fetchall("select id,title,link,wxapp_link,displayorder,status from" . tablename("tiny_wmall_notice") . " where uniacid = :uniacid and type = :type and status = 1 order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":type" => "member"));
$cubes = pdo_fetchall("select * from " . tablename("tiny_wmall_cube") . " where uniacid = :uniacid order by displayorder desc", array(":uniacid" => $_W["uniacid"]));
if (!empty($cubes)) {
    foreach ($cubes as &$c) {
        $c["thumb"] = tomedia($c["thumb"]);
    }
}
$result = array("slides" => $slides, "categorys" => $categorys, "notices" => $notices, "cubes" => $cubes);
imessage(error(0, $result), "", "ajax");

?>
