<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->func("tpl.app");
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $sid = intval($_GPC["sid"]);
    $store = store_fetch($sid, array("title", "id"));
    if (empty($store)) {
        imessage(error(-1, "门店不存在或已删除"), "", "ajax");
    }
    $reasons = $_W["we7_wmall"]["config"]["report"];
    $result = array("store" => $store, "reasons" => $reasons, "member" => $_W["member"]);
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "post") {
    $title = !empty($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "投诉类型有误"), "", "ajax");
    $data = array("uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "sid" => intval($_GPC["sid"]), "uid" => $_W["member"]["uid"], "openid" => $_W["openid"], "title" => $title, "note" => trim($_GPC["note"]), "mobile" => trim($_GPC["mobile"]), "addtime" => TIMESTAMP);
    $thumbs = json_decode(htmlspecialchars_decode($_GPC["thumbs"]), true);
    if (!empty($_GPC["thumbs"])) {
        foreach ($_GPC["thumbs"] as $row) {
            if (empty($row)) {
                continue;
            }
            $thumbs[] = $row["filename"];
        }
        $data["thumbs"] = iserializer($thumbs);
    }
    pdo_insert("tiny_wmall_report", $data);
    imessage(error(0, "投诉成功"), "", "ajax");
}

?>