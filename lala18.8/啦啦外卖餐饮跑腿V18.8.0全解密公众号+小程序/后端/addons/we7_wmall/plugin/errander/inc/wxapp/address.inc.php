<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
mload()->model("member");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "favorite";
if ($op == "favorite") {
    $address = pdo_fetchall("select * from" . tablename("tiny_wmall_address") . " where uniacid = :uniacid and uid = :uid and type = 3 and mode = :mode order by id desc", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":mode" => "favorite"));
    imessage(error(0, $address), "", "ajax");
}
if ($op == "history") {
    $address = pdo_fetchall("select * from" . tablename("tiny_wmall_address") . " where uniacid = :uniacid and uid = :uid and type = 3 and mode = :mode order by id desc", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"], ":mode" => "history"));
    imessage(error(0, $address), "", "ajax");
}
if ($op == "turncate") {
    $address = pdo_delete("tiny_wmall_address", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "mode" => "history"));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "favorite_add") {
    if (empty($_GPC["name"]) || empty($_GPC["address"]) || empty($_GPC["location_x"]) || empty($_GPC["location_y"])) {
        imessage(error(-1, "该地址无效"), "", "ajax");
    }
    $address = array("name" => trim($_GPC["name"]), "address" => trim($_GPC["address"]), "location_x" => floatval($_GPC["location_x"]), "location_y" => floatval($_GPC["location_y"]));
    $id = member_errander_address_add($address);
    imessage(error(0, $id), "", "ajax");
}
if ($op == "del") {
    if (empty($_GPC["id"])) {
        imessage(error(-1, "该地址不存在或已删除"), "", "ajax");
    }
    member_errander_address_del($_GPC["id"]);
    imessage(error(0, "地址删除成功"), "", "ajax");
}

?>