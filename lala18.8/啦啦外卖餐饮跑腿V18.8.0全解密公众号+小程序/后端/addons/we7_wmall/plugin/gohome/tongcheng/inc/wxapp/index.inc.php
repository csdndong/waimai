<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth(true);
if ($op == "index") {
    if ($_W["ochannel"] == "wxapp" && $_W["is_agent"] && $_W["agentid"] == -1) {
        imessage(error(-2, "您所在的区域暂未获取到同城信息,建议您手动搜索地址或切换到此前常用的地址再试试"), "", "ajax");
    }
    tongcheng_cron();
    tongcheng_flow_update("falselooknum");
    mload()->model("diy");
    if ($_config_wxapp["diy"]["use_diy_tongcheng"] != 1) {
        $pageOrid = get_wxapp_defaultpage("tongcheng");
        $config_share = $_config_plugin["share"];
        $share = array("title" => $config_share["title"], "desc" => $config_share["detail"], "link" => empty($config_share["link"]) ? ivurl("gohome/pages/tongcheng/index", array(), true) : $config_share["link"], "imgUrl" => tomedia($config_share["thumb"]));
    } else {
        $pageOrid = $_config_wxapp["diy"]["shopPage"]["tongcheng"];
        if (empty($pageOrid)) {
            imessage(error(-1, "未设置同城DIY页面"), "", "ajax");
        }
    }
    $page = get_wxapp_diy($pageOrid, true);
    if (empty($page)) {
        imessage(error(-1, "页面不能为空"), "", "ajax");
    }
    $_W["_share"] = array("title" => $page["data"]["page"]["title"], "desc" => $page["data"]["page"]["desc"], "link" => ivurl("gohome/pages/tongcheng/index", array(), true), "imgUrl" => tomedia($page["data"]["page"]["thumb"]));
    if ($_config_wxapp["diy"]["use_diy_tongcheng"] != 1) {
        $_W["_share"] = $share;
    }
    $default_location = array();
    if (empty($_GPC["lat"]) || empty($_GPC["lng"])) {
        $config_takeout = $_W["we7_wmall"]["config"]["takeout"]["range"];
        if (!empty($config_takeout["map"]["location_x"]) && !empty($config_takeout["map"]["location_y"])) {
            $_GPC["lat"] = $config_takeout["map"]["location_x"];
            $_GPC["lng"] = $config_takeout["map"]["location_y"];
            $default_location = array("location_x" => $config_takeout["map"]["location_x"], "location_y" => $config_takeout["map"]["location_y"], "address" => $config_takeout["city"]);
        }
    }
    $result = array("cart_sum" => $page["is_show_cart"] == 1 ? get_member_cartnum() : 0, "config" => $_W["we7_wmall"]["config"]["mall"], "diy" => $page);
    $result["config"]["default_location"] = $default_location;
    $_W["_nav"] = 1;
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "information") {
        $informations = tongcheng_get_informations();
        $result = array("informations" => $informations["informations"]);
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($op == "mine") {
            $filter = $_GPC;
            $filter["uid"] = $_W["member"]["uid"];
            $informations = tongcheng_get_informations($filter);
            $result = array("informations" => $informations["informations"]);
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($op == "detail") {
                $id = intval($_GPC["id"]);
                if (empty($id)) {
                    $trade_id = intval($_GPC["trade_id"]);
                    $trade = pdo_get("tiny_wmall_tongcheng_order", array("uniacid" => $_W["uniacid"], "id" => $trade_id), array("tid"));
                    $id = $trade["tid"];
                }
                gohome_update_activity_flow("tongcheng", $id, "looknum");
                $information = tongcheng_get_information($id, array("like_member_show" => 1));
                $comments = tongcheng_get_comments($id);
                $_W["_share"] = array("title" => cutstr($information["content_share"], 20, true), "desc" => cutstr($information["content_share"], 50, true), "link" => ivurl("gohome/pages/tongcheng/detail", array("id" => $id), true), "imgUrl" => $information["thumbs"][0]);
                $result = array("detail" => $information, "comments" => $comments);
                imessage(error(0, $result), "", "ajax");
            } else {
                if ($op == "category") {
                    $cid = intval($_GPC["id"]);
                    $childid = intval($_GPC["childid"]);
                    $filter = array("parentid" => $cid, "childid" => $childid, "keyword" => trim($_GPC["keyword"]));
                    $informations = tongcheng_get_informations($filter);
                    $categorys = tongcheng_get_categorys(array("status" => 1, "type" => "parent_child"));
                    $result = array("informations" => $informations["informations"], "categorys" => $categorys);
                    $_W["_share"] = array("title" => $categorys[$cid]["title"], "desc" => $categorys[$cid]["content"], "link" => ivurl("gohome/pages/tongcheng/category", array("id" => $cid, "childid" => $childid), true), "imgUrl" => $categorys[$cid]["thumb"]);
                    imessage(error(0, $result), "", "ajax");
                } else {
                    if ($op == "comment") {
                        $id = intval($_GPC["id"]);
                        $update = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "tid" => $id, "content" => trim($_GPC["content"]), "uid" => $_W["member"]["uid"], "nickname" => $_W["member"]["nickname"], "avatar" => $_W["member"]["avatar"], "addtime" => TIMESTAMP);
                        pdo_insert("tiny_wmall_tongcheng_comment", $update);
                        $extra = array("content" => $update["content"], "nickname" => $update["nickname"], "addtime" => $update["addtime"]);
                        tongcheng_tiezi_notice($id, "comment", $extra);
                        imessage(error(0, "评论成功"), "", "ajax");
                    } else {
                        if ($op == "reply") {
                            $id = intval($_GPC["id"]);
                            $tid = intval($_GPC["tid"]);
                            $to_uid = intval($_GPC["to_uid"]);
                            $to_member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $to_uid), array("uid", "nickname", "avatar"));
                            $update = array("uniacid" => $_W["uniacid"], "tid" => $tid, "cid" => $id, "content" => trim($_GPC["content"]), "from_uid" => $_W["member"]["uid"], "from_nickname" => $_W["member"]["nickname"], "from_avatar" => $_W["member"]["avatar"], "to_uid" => $to_member["uid"], "to_nickname" => $to_member["nickname"], "to_avatar" => $to_member["avatar"], "addtime" => TIMESTAMP);
                            pdo_insert("tiny_wmall_tongcheng_reply", $update);
                            $extra = array("content" => "回复:" . $update["content"], "nickname" => $update["from_nickname"], "addtime" => $update["addtime"]);
                            tongcheng_tiezi_notice($id, "reply", $extra);
                            imessage(error(0, "回复成功"), "", "ajax");
                        } else {
                            if ($op == "del") {
                                $id = intval($_GPC["id"]);
                                $type = trim($_GPC["type"]);
                                $result = tongcheng_information_delete($id, $type);
                                imessage($result, "", "ajax");
                            } else {
                                if ($op == "like") {
                                    $id = intval($_GPC["id"]);
                                    $information = pdo_get("tiny_wmall_tongcheng_information", array("uniacid" => $_W["uniacid"], "id" => $id), array("id", "likenum", "like_uid"));
                                    if (!empty($information)) {
                                        $like_uid = iunserializer($information["like_uid"]);
                                        if (empty($like_uid)) {
                                            $like_uid = array();
                                        }
                                        if (in_array($_W["member"]["uid"], $like_uid)) {
                                            imessage(error(-1, "您已赞过了"), "", "ajax");
                                        }
                                        $like_uid[] = $_W["member"]["uid"];
                                    }
                                    tongcheng_flow_update("falselikenum");
                                    $update = array("like_uid" => iserializer($like_uid), "likenum" => $information["likenum"] + 1);
                                    pdo_update("tiny_wmall_tongcheng_information", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
                                    $extra = array("content" => "点赞", "addtime" => TIMESTAMP, "nickname" => $_W["member"]["nickname"]);
                                    tongcheng_tiezi_notice($id, "like", $extra);
                                    imessage(error(0, ""), "", "ajax");
                                } else {
                                    if ($op == "get_search") {
                                        $result = array("categorys" => pdo_getall("tiny_wmall_tongcheng_category", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "status" => 1, "is_hot" => 1), array("id", "parentid", "title")));
                                        imessage(error(0, $result), "", "ajax");
                                    } else {
                                        if ($op == "search") {
                                            $keyword = trim($_GPC["keyword"]);
                                            if (empty($keyword)) {
                                                imessage(error(-1, "请先输入搜索条件"), "", "ajax");
                                            }
                                            $informations = tongcheng_get_informations(array("keyword" => $keyword));
                                            $result = array("informations" => $informations["informations"]);
                                            imessage(error(0, $result), "", "ajax");
                                        } else {
                                            if ($op == "cart") {
                                                $result = array("cart_sum" => get_member_cartnum());
                                                imessage(error(0, $result), "", "ajax");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>