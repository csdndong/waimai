<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth(true);
if ($op == "index") {
    $categorys = tongcheng_get_categorys(array("status" => 1));
    $result = array("categorys" => $categorys);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "post") {
        $childid = intval($_GPC["childid"]);
        $id = $childid ? $childid : intval($_GPC["parentid"]);
        $information_id = intval($_GPC["information_id"]);
        if (!empty($information_id)) {
            $information = pdo_get("tiny_wmall_tongcheng_information", array("uniacid" => $_W["uniacid"], "id" => $information_id, "uid" => $_W["member"]["uid"]));
            if (!empty($information)) {
                $thumbs = array();
                if (!empty($information["thumbs"])) {
                    $information["thumbs"] = iunserializer($information["thumbs"]);
                    foreach ($information["thumbs"] as $val) {
                        $thumbs[] = array("url" => tomedia($val), "filename" => $val);
                    }
                    $information["thumbs"] = $thumbs;
                }
                if (!empty($information["keyword"])) {
                    $information["keyword"] = iunserializer($information["keyword"]);
                }
            }
        } else {
            $can_publish = tongcheng_can_publish_information();
            if (is_error($can_publish)) {
                imessage($can_publish, "", "ajax");
            }
        }
        $category = tongcheng_get_category($id);
        if (empty($category)) {
            imessage(error(-1, "分类不存在"), "", "ajax");
        }
        $publish = json_decode(htmlspecialchars_decode($_GPC["publish"]), true);
        $publish["information_id"] = $information_id;
        $calculate = tongcheng_information_publish_calculate($category, $publish);
        if ($_W["ispost"]) {
            $update = array("content" => trim($publish["content"]), "nickname" => trim($publish["nickname"]), "mobile" => trim($publish["mobile"]));
            if (!empty($publish["keyword"])) {
                $update["keyword"] = iserializer($publish["keyword"]);
            }
            if (!empty($publish["thumbs"])) {
                foreach ($publish["thumbs"] as $val) {
                    $update["thumbs"][] = $val["filename"];
                }
                $update["thumbs"] = iserializer($update["thumbs"]);
            }
            if (empty($information_id)) {
                $update["uniacid"] = $_W["uniacid"];
                $update["agentid"] = $_W["agentid"];
                $update["uid"] = $_W["member"]["uid"];
                $update["openid"] = $_W["openid"];
                $update["parentid"] = empty($category["parentid"]) ? $category["id"] : $category["parentid"];
                $update["childid"] = empty($category["parentid"]) ? 0 : $category["id"];
                $update["is_stick"] = 0;
                $update["sid"] = intval($publish["sid"]);
                $update["addtime"] = TIMESTAMP;
                $update["channel"] = $_W["ochannel"];
                if (0 < $calculate["final_fee"]) {
                    $update["is_stick"] = $calculate["is_stick"];
                    $update["status"] = 1;
                } else {
                    $audit_status = intval($_config_plugin["tongcheng"]["audit"]["new"]);
                    $update["status"] = $audit_status == 1 ? 2 : 3;
                }
                pdo_insert("tiny_wmall_tongcheng_information", $update);
                $infor_id = pdo_insertid();
                tongcheng_flow_update("falsefabunum");
            } else {
                $update["edit_status"] = 1;
                if (0 < $calculate["final_fee"]) {
                    $update["is_stick"] = $calculate["is_stick"];
                    $update["status"] = 1;
                } else {
                    $audit_status = intval($_config_plugin["tongcheng"]["audit"]["edit"]);
                    if ($audit_status == 1) {
                        $update["status"] = 3;
                    } else {
                        if ($audit_status == 2) {
                            $update["status"] = 2;
                        } else {
                            $update["status"] = $information["status"];
                        }
                    }
                }
                pdo_update("tiny_wmall_tongcheng_information", $update, array("uniacid" => $_W["uniacid"], "id" => $information_id));
                $infor_id = $information["id"];
            }
            if (0 < $calculate["final_fee"]) {
                $update_trade = array("uniacid" => intval($_W["uniacid"]), "agentid" => $_W["agentid"], "tid" => $infor_id, "uid" => $_W["member"]["uid"], "type" => $calculate["is_stick"], "price" => $calculate["price"], "stick_price" => $calculate["stick_price"], "final_fee" => $calculate["final_fee"], "days" => $calculate["days"], "ordersn" => date("YmdHis") . random(6, true), "addtime" => TIMESTAMP, "stat_day" => date("Ymd", TIMESTAMP));
                $update_trade = tongcheng_tiezi_order_bill($update_trade);
                pdo_insert("tiny_wmall_tongcheng_order", $update_trade);
                $trade_id = pdo_insertid();
            }
            if ($update["status"] == 1) {
                $result = array("id" => $trade_id, "need_pay" => 1);
            } else {
                if ($update["status"] == 2) {
                    $result = array("need_pay" => 0, "message" => "请等待审核", "information_id" => $infor_id);
                } else {
                    if ($update["status"] == 3) {
                        $result = array("need_pay" => 0, "message" => "信息发步成功", "information_id" => $infor_id);
                    }
                }
            }
            imessage(error(0, $result), "", "ajax");
        }
        $result = array("category" => $category, "member" => array("realname" => $_W["member"]["realname"], "mobile" => $_W["member"]["mobile"]), "calculate" => $calculate, "publish" => $information);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($op == "stick") {
            $information_id = intval($_GPC["information_id"]);
            $information = pdo_get("tiny_wmall_tongcheng_information", array("uniacid" => $_W["uniacid"], "id" => $information_id, "uid" => $_W["member"]["uid"]));
            if (empty($information)) {
                imessage(error(-1, "置顶帖子不存在"), "", "ajax");
            }
            $category_id = $information["childid"] ? $information["childid"] : $information["parentid"];
            $category = tongcheng_get_category($category_id);
            if (empty($category)) {
                imessage(error(-1, "分类不存在"), "", "ajax");
            }
            if (empty($category["config"]["stick_price"])) {
                imessage(error(-1, "帖子所在分类未设置置顶选项"), "", "ajax");
            }
            $calculate = tongcheng_information_publish_calculate($category, array("days" => intval($_GPC["days"]), "information_id" => $information_id));
            if ($_W["ispost"]) {
                if (0 < $calculate["final_fee"]) {
                    $update_trade = array("uniacid" => intval($_W["uniacid"]), "agentid" => $_W["agentid"], "tid" => $information_id, "uid" => $_W["member"]["uid"], "type" => 2, "price" => 0, "stick_price" => $calculate["stick_price"], "final_fee" => $calculate["final_fee"], "days" => $calculate["days"], "ordersn" => date("YmdHis") . random(6, true), "addtime" => TIMESTAMP);
                    $update_trade = tongcheng_tiezi_order_bill($update_trade);
                    pdo_insert("tiny_wmall_tongcheng_order", $update_trade);
                    $trade_id = pdo_insertid();
                    imessage(error(0, intval($trade_id)), "", "ajax");
                }
                imessage(error(-1, "置顶信息有误，请重新选择"), "", "ajax");
            }
            foreach ($category["config"]["stick_price"] as $val) {
                $calculate["default_days"] = $val["day"];
                break;
            }
            $result = array("category" => $category, "calculate" => $calculate);
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>