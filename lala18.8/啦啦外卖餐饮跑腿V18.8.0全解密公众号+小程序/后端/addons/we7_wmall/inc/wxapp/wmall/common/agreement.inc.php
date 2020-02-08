<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "协议";
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "agreement";
if ($ta == "agreement") {
    $key = trim($_GPC["key"]);
    $pageid = trim($_GPC["pageid"]);
    if ($key == "errander_diypage_agreement") {
        $config = pdo_get("tiny_wmall_errander_page", array("uniacid" => $_W["uniacid"], "id" => $pageid));
        $result = array("agreement" => $config["agreement"], "title" => "服务协议");
    } else {
        if ($key == "mealPlus_rules") {
            $redpacket = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "type" => "meal", "status" => 1));
            if (!empty($redpacket)) {
                $redpacket["data"] = json_decode(base64_decode($redpacket["data"]), true);
                if (!empty($redpacket["data"]["rules"])) {
                    $redpacket["data"]["rules"] = htmlspecialchars_decode(base64_decode($redpacket["data"]["rules"]));
                }
            }
            $result = array("agreement" => $redpacket["data"]["rules"], "title" => "套餐红包规则");
        } else {
            if ($key == "meal_rules") {
                mload()->model("plugin");
                pload()->model("mealRedpacket");
                $mealRedpacket = mealRedpacket_available_get();
                $result = array("agreement" => $mealRedpacket["data"]["rules"], "title" => "特权说明");
            } else {
                if ($key == "help") {
                    $helpid = trim($_GPC["helpid"]);
                    if (0 < $helpid) {
                        $config = pdo_get("tiny_wmall_help", array("uniacid" => $_W["uniacid"], "id" => $helpid));
                        $result = array("agreement" => $config["content"], "title" => $config["title"]);
                    }
                } else {
                    if ($key == "notice") {
                        $noticeid = intval($_GPC["noticeid"]);
                        $notice = pdo_get("tiny_wmall_notice", array("uniacid" => $_W["uniacid"], "id" => $noticeid), array("title", "content"));
                        $result = array("agreement" => $notice["content"], "title" => $notice["title"]);
                    } else {
                        if ($key == "zhunshibao_agreement") {
                            $config = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "name" => "zhunshibao:agreement"));
                            $result = array("agreement" => $config["value"], "title" => $config["title"]);
                        } else {
                            if ($key == "yinsihao_agreement") {
                                $config = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "name" => "yinsihao:agreement"));
                                $result = array("agreement" => $config["value"], "title" => $config["title"]);
                            } else {
                                if ($key == "spread_agreement") {
                                    $config = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "name" => "spread:agreement"));
                                    $result = array("agreement" => $config["value"], "title" => $config["title"]);
                                } else {
                                    $config = pdo_get("tiny_wmall_text", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "name" => $key));
                                    $result = array("agreement" => $config["value"], "title" => $config["title"]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    imessage(error(0, $result), "", "ajax");
}

?>