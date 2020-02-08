<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if ($op == "page") {
    if (isset($_GPC["key"])) {
        $key = trim($_GPC["key"]);
        $data = pdo_fetchall("select id,name,thumb,type from " . tablename("tiny_wmall_errander_page") . " where uniacid = :uniacid and name like :key order by id desc limit 50", array(":uniacid" => $_W["uniacid"], ":key" => "%" . $key . "%"), "id");
        if (!empty($data)) {
            foreach ($data as &$row) {
                $row["thumb_cn"] = tomedia($row["thumb"]);
            }
            $pages = array_values($data);
        }
        message(array("errno" => 0, "message" => $pages, "data" => $data), "", "ajax");
    }
    include itemplate("public/errander");
}

?>