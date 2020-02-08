<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where uniacid = :uniacid AND sid = :sid and parentid = 0";
    $params[":uniacid"] = $_W["uniacid"];
    $params[":sid"] = $sid;
    $lists = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_goods_category") . $condition . " ORDER BY displayorder DESC,id ASC", $params);
    if (!empty($lists)) {
        foreach ($lists as &$val) {
            $val["child"] = pdo_fetchall("select * from" . tablename("tiny_wmall_goods_category") . "where uniacid = :uniacid and sid = :sid and parentid = :parentid order by displayorder desc,id asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":parentid" => $val["id"]));
            if (!empty($val["child"])) {
                $child_goods_nums = pdo_fetchall("SELECT count(*) AS num,child_id FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND cid = :cid GROUP BY child_id", array(":uniacid" => $_W["uniacid"], ":cid" => $val["id"]), "child_id");
                $val["goods_num"] = 0;
                foreach ($val["child"] as &$v) {
                    $v["goods_num"] = $child_goods_nums[$v["id"]]["num"];
                    $val["goods_num"] += $v["goods_num"];
                }
            } else {
                $val["child"] = array();
                $val["goods_num"] = pdo_fetchcolumn("SELECT count(*) FROM " . tablename("tiny_wmall_goods") . " WHERE uniacid = :uniacid AND cid = :cid GROUP BY cid", array(":uniacid" => $_W["uniacid"], ":cid" => $val["id"]));
            }
        }
    }
    $result = array("categorys" => $lists);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "post") {
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $params = json_decode(htmlspecialchars_decode($_GPC["params"]), true);
            $title = trim($params["title"]) ? trim($params["title"]) : imessage(error(-1, "分组名称不能为空"), "", "ajax");
            $update = array("title" => $title, "displayorder" => intval($params["displayorder"]), "description" => trim($params["description"]), "min_fee" => intval($params["min_fee"]), "is_showtime" => intval($params["is_showtime"]), "thumb" => trim($params["thumb"]));
            if (0 < $id) {
                $category = pdo_get("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            }
            if ($update["is_showtime"] == 1 && (empty($id) && empty($params["parentid"]) || 0 < $id && $category["parentid"] == 0)) {
                $starttime = strtotime($params["start_time"]);
                $endtime = strtotime($params["end_time"]);
                if (empty($params["start_time"])) {
                    imessage(error(-1, "请选择分类显示时段起始时间"), "", "ajax");
                }
                if ($endtime <= $starttime) {
                    imessage(error(-1, "分类显示时段 起始时间需小于结束时间，请重新设置"), "", "ajax");
                }
                $update["start_time"] = trim($params["start_time"]);
                $update["end_time"] = trim($params["end_time"]);
                if (!empty($params["limit_week"])) {
                    $weeks = array_values($params["limit_week"]);
                    if (count($weeks) < 7) {
                        $update["week"] = implode(",", $weeks);
                    }
                }
            }
            if (0 < $id) {
                if (empty($category)) {
                    imessage(error(-1, "商品分组不存在"), "", "ajax");
                }
                pdo_update("tiny_wmall_goods_category", $update, array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            } else {
                $update["uniacid"] = $_W["uniacid"];
                $update["sid"] = $sid;
                $update["status"] = 1;
                $update["parentid"] = intval($params["parentid"]);
                pdo_insert("tiny_wmall_goods_category", $update);
            }
            imessage(error(0, "编辑成功"), "", "ajax");
        }
        $categorys = pdo_getall("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "parentid" => 0), array("id", "title"));
        $now_category = array();
        if (0 < $id) {
            $now_category = pdo_get("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            $now_category["limit_week"] = array();
            if ($now_category["is_showtime"] == 1) {
                if (!empty($now_category["week"])) {
                    $now_category["week"] = explode(",", $now_category["week"]);
                    foreach ($now_category["week"] as $val) {
                        $now_category["limit_week"][$val] = $val;
                    }
                } else {
                    $now_category["limit_week"] = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7);
                }
            }
            $now_category["thumb_"] = tomedia($now_category["thumb"]);
            $now_category["thumb"] = $now_category["thumb_"];
        }
        $result = array("categorys" => $categorys, "now_category" => $now_category);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($ta == "status") {
            $id = intval($_GPC["id"]);
            $status = intval($_GPC["status"]);
            pdo_update("tiny_wmall_goods_category", array("status" => $status), array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            $message = $status == 0 ? "下架成功" : "上架成功";
            imessage(error(0, $message), referer(), "ajax");
        } else {
            if ($ta == "del") {
                $id = intval($_GPC["id"]);
                pdo_delete("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
                pdo_delete("tiny_wmall_goods_category", array("uniacid" => $_W["uniacid"], "sid" => $sid, "parentid" => $id));
                pdo_delete("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $id));
                imessage(error(0, "删除分组成功"), referer(), "ajax");
            }
        }
    }
}

?>
