<?php
/*
 * @ 买卖跑腿系统
 * @ APP公众号小程序版
 * @ PHP开源站，遵从PHP开源精神
 * @ 源码仅供学习研究，禁止商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "活动列表";
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and title like '%" . $keyword . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) FROM " . tablename("tiny_wmall_wheel") . $condition, $params);
    $wheels = pdo_fetchall("select * from " . tablename("tiny_wmall_wheel") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    include itemplate("list");
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑活动";
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $data = $_GPC["wheel"];
            $starttime = strtotime($data["params"]["starttime"]);
            $endtime = strtotime($data["params"]["endtime"]);
            $data["params"]["desc"] = trim($data["params"]["desc"]);
            $data["params"]["desc"] = explode("\n", $data["params"]["desc"]);
            if ($endtime <= $starttime) {
                imessage(error(-1, "开始时间不能小于结束时间"), "", "ajax");
            }
            $total = 0;
            foreach ($data["data"] as $key => $val) {
                $awardtotal = intval($val["awardtotal"]);
                $total += $awardtotal;
            }
            if (!$total) {
                imessage(error(-1, "奖项总数不能为0个"), "", "ajax");
            }
            if ($data["params"]["memberlimit"] == 1) {
                $data["params"]["per_day"] = 0;
            }
            $update = array("uniacid" => $_W["uniacid"], "title" => $data["params"]["name"], "status" => $data["params"]["status"], "addtime" => TIMESTAMP, "starttime" => $starttime, "endtime" => $endtime, "total" => $total, "data" => base64_encode(json_encode($data)));
            if (!empty($id)) {
                pdo_update("tiny_wmall_wheel", $update, array("id" => $id, "uniacid" => $_W["uniacid"]));
            } else {
                $update["addtime"] = TIMESTAMP;
                pdo_insert("tiny_wmall_wheel", $update);
                $id = pdo_insertid();
            }
            imessage(error(0, "幸运抽奖设置成功"), iurl("wheel/activity/list"), "ajax");
        }
        if (!empty($id)) {
            $wheel = pdo_fetch("select * from " . tablename("tiny_wmall_wheel") . " where id = :id and uniacid = :uniacid", array(":id" => $id, ":uniacid" => $_W["uniacid"]));
            if (!empty($wheel)) {
                $wheel["data"] = json_decode(base64_decode($wheel["data"]), true);
                $wheel["data"]["params"]["desc_arr"] = $wheel["data"]["params"]["desc"];
                $wheel["data"]["params"]["desc"] = implode("\n", $wheel["data"]["params"]["desc"]);
            }
        }
        include itemplate("activity");
        return 1;
    } else {
        if ($op == "del") {
            $ids = $_GPC["id"];
            if ($_W["ispost"]) {
                if (!is_array($ids)) {
                    $ids = array($ids);
                }
                foreach ($ids as $id) {
                    pdo_delete("tiny_wmall_wheel", array("uniacid" => $_W["uniacid"], "id" => $id));
                }
                imessage(error(0, "删除成功"), referer(), "ajax");
            }
            include itemplate("list");
            return 1;
        } else {
            if ($op == "status") {
                $id = intval($_GPC["id"]);
                if ($_W["ispost"]) {
                    $status = intval($_GPC["value"]) == 1 ? 0 : 1;
                    pdo_update("tiny_wmall_wheel", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
                    imessage(error(0, "更改状态成功"), "", "ajax");
                }
                include itemplate("list");
            }
        }
    }
}

?>