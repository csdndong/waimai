<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
$config = get_plugin_config("gohome.basic");
if ($config["status"]["pintuan"] != 1) {
    imessage("平台已关闭拼团功能，详情请联系平台管理员", "", "info");
}
if ($ta == "list") {
    $_W["page"]["title"] = "活动列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("name" => trim($_GPC["name"][$k]), "price" => trim($_GPC["price"][$k]), "aloneprice" => trim($_GPC["aloneprice"][$k]), "oldprice" => trim($_GPC["oldprice"][$k]), "total" => floatval($_GPC["total"][$k]));
                pdo_update("tiny_wmall_pintuan_goods", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("store/pintuan/activity/list"), "ajax");
    }
    $condition = " where uniacid = :uniacid and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and name like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : "-1";
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_pintuan_goods") . $condition, $params);
    $pintuan = pdo_fetchall("select * from " . tablename("tiny_wmall_pintuan_goods") . (string) $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    mload()->model("plugin");
    pload()->model("gohome");
    $goods_status = gohome_goods_status();
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($ta == "post") {
        $_W["page"]["title"] = "添加拼团";
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "name" => trim($_GPC["name"]), "unit" => trim($_GPC["unit"]), "thumb" => trim($_GPC["thumb"]), "detail" => htmlspecialchars_decode($_GPC["detail"]), "status" => intval($_GPC["status"]), "price" => floatval($_GPC["price"]), "aloneprice" => floatval($_GPC["aloneprice"]), "oldprice" => floatval($_GPC["oldprice"]), "peoplenum" => intval($_GPC["peoplenum"]), "grouptime" => intval($_GPC["grouptime"]), "cateid" => intval($_GPC["cateid"]), "total" => intval($_GPC["total"]), "total_update_type" => intval($_GPC["total_update_type"]), "falesailed" => intval($_GPC["falesailed"]), "falselooknum" => intval($_GPC["falselooknum"]), "falsesharenum" => intval($_GPC["falsesharenum"]), "buylimit" => intval($_GPC["buylimit"]), "starttime" => strtotime($_GPC["addtime"]["start"]), "endtime" => strtotime($_GPC["addtime"]["end"]));
            if (is_array($_GPC["tag"])) {
                array_map("intval", $_GPC["tag"]);
                $data["tag"] = implode(",", $_GPC["tag"]);
            }
            $data["thumbs"] = array();
            if (!empty($_GPC["thumbs"])) {
                foreach ($_GPC["thumbs"] as $val) {
                    if (empty($val)) {
                        continue;
                    }
                    $data["thumbs"][] = trim($val);
                }
            }
            $data["thumbs"] = iserializer($data["thumbs"]);
            $data["share"] = array();
            $share_thumb = trim($_GPC["share_thumb"]) ? trim($_GPC["share_thumb"]) : trim($_GPC["thumb"]);
            $share_title = trim($_GPC["share_title"]) ? trim($_GPC["share_title"]) : trim($_GPC["name"]);
            $share_detail = trim($_GPC["share_detail"]);
            $data["share"] = array("share_thumb" => $share_thumb, "share_title" => $share_title, "share_detail" => $share_detail);
            $data["share"] = iserializer($data["share"]);
            if (!empty($id)) {
                pdo_update("tiny_wmall_pintuan_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            } else {
                pdo_insert("tiny_wmall_pintuan_goods", $data);
            }
            imessage(error(0, "编辑活动成功"), iurl("store/pintuan/activity/list"), "ajax");
        }
        if ($id) {
            $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_pintuan_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id = :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id));
            if (empty($item)) {
                imessage("活动不存在或已删除", iurl("store/pintuan/activity/list"), "info");
            }
            $starttime = $item["starttime"];
            $endtime = $item["endtime"];
            $item["thumbs"] = iunserializer($item["thumbs"]);
            $item["share"] = iunserializer($item["share"]);
            $item["tag"] = explode(",", $item["tag"]);
        }
        if (empty($item)) {
            $starttime = TIMESTAMP;
            $endtime = strtotime("7 day");
        }
        $category = pdo_getall("tiny_wmall_pintuan_category", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    } else {
        if ($ta == "del") {
            $ids = $_GPC["id"];
            if (!is_array($ids)) {
                $ids = array($ids);
            }
            foreach ($ids as $id) {
                $id = intval($id);
                if (0 < $id) {
                    pdo_delete("tiny_wmall_pintuan_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
                }
            }
            imessage(error(0, "删除活动成功"), "", "ajax");
        }
    }
}
include itemplate("store/gohome/pintuan/activity");

?>