<?php 
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "帖子列表";
    $is_stick = isset($_GPC["is_stick"]) ? intval($_GPC["is_stick"]) : "-1";
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
    } else {
        $starttime = strtotime("-7 day");
        $endtime = TIMESTAMP;
    }
    $_GPC["starttime"] = $starttime;
    $_GPC["endtime"] = $endtime;
    if (!empty($_GPC["cid"])) {
        $cid = $_GPC["cid"];
        if (strexists($cid, ":")) {
            $cid = explode(":", $cid);
            $_GPC["parentid"] = intval($cid[0]);
            $_GPC["childid"] = intval($cid[1]);
        } else {
            $_GPC["parentid"] = intval($cid);
        }
    }
    $filter = $_GPC;
    $filter["orderby"] = "addtime";
    $filter["psize"] = 20;
    $filter["status"] = isset($filter["status"]) ? intval($filter["status"]) : -1;
    $informations = tongcheng_get_informations($filter);
    $information = $informations["informations"];
    $pager = $informations["pager"];
    $categorys = tongcheng_get_categorys();
} else {
    if ($op == "order_list") {
        $_W["page"]["title"] = "订单列表";
        $type = isset($_GPC["type"]) ? intval($_GPC["type"]) : -1;
        if (!empty($_GPC["addtime"])) {
            $starttime = strtotime($_GPC["addtime"]["start"]);
            $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
        } else {
            $starttime = strtotime("-7 day");
            $endtime = TIMESTAMP;
        }
        $_GPC["starttime"] = $starttime;
        $_GPC["endtime"] = $endtime;
        $data = tongcheng_get_orders();
        $orders = $data["orders"];
        $pager = $data["pager"];
    } else {
        if ($op == "detail") {
            $_W["page"]["title"] = "帖子详情";
            tongcheng_cron();
            $id = intval($_GPC["id"]);
            $information = tongcheng_get_information($id);
            $status = $information["status"];
            $categorys = tongcheng_get_categorys(array("type" => "parent&child"), array("id", "title", "parentid"));
            if ($_W["ispost"]) {
                $data = array("mobile" => trim($_GPC["mobile"]), "content" => trim($_GPC["content"]), "looknum" => intval($_GPC["looknum"]), "likenum" => intval($_GPC["likenum"]), "sharenum" => intval($_GPC["sharenum"]), "is_stick" => intval($_GPC["is_stick"]), "status" => intval($_GPC["status"]), "parentid" => intval($_GPC["category"]["parentid"]), "childid" => intval($_GPC["category"]["childid"]));
                $data["thumbs"] = array();
                if (!empty($_GPC["thumbs"])) {
                    foreach ($_GPC["thumbs"] as $thumb) {
                        if (empty($thumb)) {
                            continue;
                        }
                        $data["thumbs"][] = $thumb;
                    }
                }
                $data["thumbs"] = iserializer($data["thumbs"]);
                if ($data["is_stick"] == "1") {
                    $overtime = trim($_GPC["overtime"]);
                    $data["overtime"] = strtotime($overtime);
                }
                pdo_update("tiny_wmall_tongcheng_information", $data, array( "uniacid" => $_W["uniacid"], "id" => $id ));
                imessage(error(0, "编辑帖子成功"), iurl("tongcheng/information/detail", array( "id" => $id )), "ajax");
            }
        } else {
            if ($op == "del") {
                $ids = $_GPC["id"];
                $result = tongcheng_information_delete($ids);
                imessage($result, "", "ajax");
            } else {
                if ($op == "status") {
                    $status = intval($_GPC["status"]);
                    $ids = $_GPC["id"];
                    $result = tongcheng_information_update_status($ids, $status);
                    imessage($result, referer(), "ajax");
                } else {
                    if ($op == "toblack") {
                        mload()->model("member.extra");
                        $uid = intval($_GPC["uid"]);
                        $status = member_to_black($uid, "tongcheng");
                        if ($status) {
                            imessage(error(0, "加入黑名单成功"), referer(), "ajax");
                        } else {
                            imessage(error(-1, "加入黑名单失败"), referer(), "ajax");
                        }
                    }
                }
            }
        }
    }
}
include(itemplate("information"));
?>
