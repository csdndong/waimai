<?php
/*

 
 
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "兑换码列表";
    $all_status = svip_code_status();
    if (!empty($_GPC["exchangetime"]["start"]) && !empty($_GPC["exchangetime"]["end"])) {
        $_GPC["starttime"] = strtotime($_GPC["exchangetime"]["start"]);
        $_GPC["endtime"] = strtotime($_GPC["exchangetime"]["end"]);
    }
    $status = intval($_GPC["status"]);
    $data = svip_code_fetchall();
    $codes = $data["codes"];
    $pager = $data["pager"];
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "批量创建兑换码";
        $meals = svip_meal_getall(array("status" => 1, "haskey" => 1));
        if ($_W["ispost"]) {
            $meal_id = intval($_GPC["meal_id"]);
            $ids = array_keys($meals);
            if (!in_array($meal_id, $ids)) {
                imessage(error(-1, "请选择有效的套餐类型"), "", "ajax");
            }
            $number = intval($_GPC["number"]);
            if ($number <= 0) {
                imessage(error(-1, "兑换码数量应大于零"), "", "ajax");
            }
            $endtime = trim($_GPC["endtime"]);
            if (empty($endtime)) {
                imessage(error(-1, "兑换码兑换截止期不能为空"), "", "ajax");
            }
            $endtime = strtotime($endtime);
            if ($endtime <= TIMESTAMP) {
                imessage(error(-1, "兑换码兑换截止期不能小于当前时间"), "", "ajax");
            }
            for ($i = 0; $i < $number; $i++) {
                $insert = array("uniacid" => $_W["uniacid"], "code" => random(16, true), "days" => $meals[$meal_id]["days"], "endtime" => $endtime + 86399, "status" => 1);
                pdo_insert("tiny_wmall_svip_code", $insert);
            }
            imessage(error(0, "批量创建兑换码成功"), iurl("svip/code/list"), "ajax");
        }
    } else {
        if ($op == "del") {
            $ids = $_GPC["id"];
            if (!is_array($ids)) {
                $ids = array($ids);
            }
            foreach ($ids as $id) {
                pdo_delete("tiny_wmall_svip_code", array("uniacid" => $_W["uniacid"], "id" => $id));
            }
            imessage(error(0, "删除兑换码成功"), "", "ajax");
        }
    }
}
include itemplate("code");

?>