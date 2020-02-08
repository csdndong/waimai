<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if ($op == "list") {
    $key = trim($_GPC["key"]);
    $data = pdo_fetchall("select a.name, b.uniacid from " . tablename("uni_account") . " as a left join " . tablename("account_wechats") . " as b on a.default_acid = b.uniacid where a.name like :akey or b.name like :key order by b.uniacid desc limit 50", array(":akey" => "%" . $key . "%", ":key" => "%" . $key . "%"), "uniacid");
    if (!empty($data)) {
        $account = array_values($data);
    }
    message(array("errno" => 0, "message" => $account, "data" => $data), "", "ajax");
}

?>