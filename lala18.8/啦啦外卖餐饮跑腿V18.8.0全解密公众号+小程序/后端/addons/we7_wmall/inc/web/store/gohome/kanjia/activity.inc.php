<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
$config = get_plugin_config("gohome.basic");
if ($config["status"]["kanjia"] != 1) {
    imessage("平台已关闭砍价功能，详情请联系平台管理员", "", "info");
}
if ($ta == "list") {
    $_W["page"]["title"] = "活动列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("name" => trim($_GPC["name"][$k]), "total" => floatval($_GPC["total"][$k]));
                pdo_update("tiny_wmall_kanjia", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("store/kanjia/activity/list"), "ajax");
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
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_kanjia") . $condition, $params);
    $kanjia = pdo_fetchall("select * from " . tablename("tiny_wmall_kanjia") . (string) $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    mload()->model("plugin");
    pload()->model("gohome");
    $goods_status = gohome_goods_status();
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($ta == "post") {
        $_W["page"]["title"] = "添加砍价";
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $submitmoneylimit = floatval($_GPC["submitmoneylimit"]);
            $price = floatval($_GPC["price"]);
            $oldprice = floatval($_GPC["oldprice"]);
            if ($oldprice < $submitmoneylimit) {
                imessage(error(-1, "允许提交订单金额不能大于商品原价"), "", "ajax");
            }
            if ($submitmoneylimit < $price) {
                imessage(error(-1, "允许提交订单金额不能小于商品现价"), "", "ajax");
            }
            $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "cateid" => intval($_GPC["cateid"]), "oldprice" => $oldprice, "price" => $price, "submitmoneylimit" => $submitmoneylimit, "starttime" => strtotime($_GPC["addtime"]["start"]), "endtime" => strtotime($_GPC["addtime"]["end"]), "helplimit" => intval($_GPC["helplimit"]), "dayhelplimit" => intval($_GPC["dayhelplimit"]), "joinlimit" => intval($_GPC["joinlimit"]), "falsejoinnum" => intval($_GPC["falsejoinnum"]), "falselooknum" => intval($_GPC["falselooknum"]), "falsesharenum" => intval($_GPC["falsesharenum"]), "status" => intval($_GPC["status"]), "code" => trim($_GPC["code"]), "name" => trim($_GPC["name"]), "total" => intval($_GPC["total"]), "total_update_type" => intval($_GPC["total_update_type"]), "thumb" => trim($_GPC["thumb"]), "unit" => trim($_GPC["unit"]), "detail" => htmlspecialchars_decode($_GPC["detail"]), "activity_rules" => htmlspecialchars_decode($_GPC["activity_rules"]), "addtime" => TIMESTAMP);
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
            $data["rules"] = array();
            foreach ($_GPC["rules"]["range"] as $key => $val) {
                $val = trim($val);
                $range_start = floatval($_GPC["rules"]["range_start"][$key]);
                $range_end = floatval($_GPC["rules"]["range_end"][$key]);
                if (empty($val) || empty($range_start) || empty($range_end)) {
                    continue;
                }
                $data["rules"][] = array("range" => $val, "range_start" => $range_start, "range_end" => $range_end);
            }
            if (empty($data["rules"])) {
                imessage(error(-1, "没有设置有效的砍价规则"), "", "ajax");
            } else {
                $data["rules"] = iserializer($data["rules"]);
            }
            $data["share"] = array();
            $share_thumb = trim($_GPC["share_thumb"]) ? trim($_GPC["share_thumb"]) : trim($_GPC["thumb"]);
            $share_title = trim($_GPC["share_title"]) ? trim($_GPC["share_title"]) : trim($_GPC["name"]);
            $share_detail = trim($_GPC["share_detail"]);
            $data["share"] = array("share_thumb" => $share_thumb, "share_title" => $share_title, "share_detail" => $share_detail);
            $data["share"] = iserializer($data["share"]);
            if (!empty($id)) {
                pdo_update("tiny_wmall_kanjia", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            } else {
                pdo_insert("tiny_wmall_kanjia", $data);
            }
            imessage(error(0, "编辑活动成功"), iurl("store/kanjia/activity/list"), "ajax");
        }
        if ($id) {
            $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_kanjia") . " WHERE uniacid = :uniacid AND sid = :sid AND id = :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id));
            if (empty($item)) {
                imessage("活动不存在或已删除", iurl("store/kanjia/activity/list"), "info");
            }
            $starttime = $item["starttime"];
            $endtime = $item["endtime"];
            $item["thumbs"] = iunserializer($item["thumbs"]);
            $item["rules"] = iunserializer($item["rules"]);
            $item["share"] = iunserializer($item["share"]);
        }
        if (empty($item)) {
            $starttime = TIMESTAMP;
            $endtime = strtotime("7 day");
        }
        $category = pdo_getall("tiny_wmall_kanjia_category", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    } else {
        if ($ta == "del") {
            $ids = $_GPC["id"];
            if (!is_array($ids)) {
                $ids = array($ids);
            }
            foreach ($ids as $id) {
                $id = intval($id);
                if (0 < $id) {
                    pdo_delete("tiny_wmall_kanjia", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
                }
            }
            imessage(error(0, "删除活动成功"), "", "ajax");
        }
    }
}
include itemplate("store/gohome/kanjia/activity");

?>