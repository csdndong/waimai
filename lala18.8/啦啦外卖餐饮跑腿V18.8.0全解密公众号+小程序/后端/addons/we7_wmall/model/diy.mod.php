<?php
echo "\r\n";
defined("IN_IA") or exit("Access Denied");
function get_wxapp_diy($pageOrid, $mobile = false, $extra = array())
{
    global $_W;
    if (is_array($pageOrid)) {
        $page = $pageOrid;
    } else {
        $id = intval($pageOrid);
        if (empty($id)) {
            return false;
        }
        $params = array("uniacid" => $_W["uniacid"], "id" => $id, "version" => 2);
        if (0 < $_W["agentid"]) {
            $params["agentid"] = $_W["agentid"];
        }
        $page = pdo_get("tiny_wmall_diypage", $params);
        if (empty($page)) {
            unset($params["agentid"]);
            $page = pdo_get("tiny_wmall_diypage", $params);
            if (empty($page) && $extra["pagepath"] == "home") {
                $page = get_wxapp_defaultpage();
            }
        }
    }
    if (empty($page)) {
        return false;
    }
    $page["data"] = base64_decode($page["data"]);
    $page["data"] = json_decode($page["data"], true);
    $page["parts"] = array();
    $page["cid"] = 0;
    $page["is_has_storesTab"] = $page["cid"];
    $page["is_has_goodsTab"] = $page["is_has_storesTab"];
    $page["is_has_svip"] = $page["is_has_goodsTab"];
    $page["is_show_service"] = $page["is_has_svip"];
    $page["is_show_redpacket"] = $page["is_show_service"];
    $page["is_show_cart"] = $page["is_show_redpacket"];
    $page["is_has_allstore"] = $page["is_show_cart"];
    $page["is_has_location"] = $page["is_has_allstore"];
    $page["danmu"] = array();
    $page["is_show_kefu"] = 0;
    if ($extra["pagepath"] == "home" && $extra["pagetype"] == "default") {
        if (!empty($_W["we7_wmall"]["config"]["mall"]["meiqia"])) {
            $page["is_show_kefu"] = 1;
        }
        if ($_W["ochannel"] == "wxapp" && !empty($_W["we7_wxapp"]["config"])) {
            $hometheme = $_W["we7_wxapp"]["config"]["extPages"]["pages/home/index"];
            if (!empty($hometheme)) {
                $page["data"]["page"]["title"] = $hometheme["navigationBarTitleText"];
                $page["data"]["page"]["navigationtextcolor"] = $hometheme["navigationBarTextStyle"];
                $page["data"]["page"]["navigationbackground"] = $hometheme["navigationBarBackgroundColor"];
            }
        }
    }
    if (empty($page["data"]["page"]["title"]) || strexists($page["data"]["page"]["title"], "啦啦外卖")) {
        $page["data"]["page"]["title"] = $_W["we7_wmall"]["config"]["mall"]["title"];
    }
    foreach ($page["data"]["items"] as &$item) {
        $page["parts"][] = $item["id"];
        if ($item["id"] == "fixedsearch") {
            $page["is_has_location"] = 1;
            if (!$item["params"]["linkto"]) {
                $item["params"]["link"] = "/pages/home/search";
            } else {
                if ($item["params"]["linkto"] == 1) {
                    $item["params"]["link"] = "/gohome/pages/tongcheng/search";
                } else {
                    if ($item["params"]["linkto"] == 2) {
                        $item["params"]["link"] = "/gohome/pages/haodian/search";
                    }
                }
            }
            $page["fixedsearch"] = $item;
        } else {
            if ($item["id"] == "waimai_allstores") {
                $page["is_has_allstore"] = 1;
                if (check_plugin_perm("svip")) {
                    $page["is_has_svip"] = 1;
                }
                if (check_plugin_perm("zhunshibao")) {
                    $page["is_has_zhunshibao"] = 1;
                }
                if ($extra["pagetype"] == "default") {
                    $discountstyle = get_plugin_config("diypage.diyTheme.store");
                    $item["params"]["discountstyle"] = $discountstyle["discount_style"];
                    $item["params"]["showhotgoods"] = $discountstyle["showhotgoods"];
                }
                $page["stores_list"]["diyitems"] = $item;
                $page["stores_list"]["orderbys"] = store_orderbys();
                $page["stores_list"]["discounts"] = store_discounts();
                if (empty($page["stores_list"]["diyitems"]["params"]["discountstyle"])) {
                    $page["stores_list"]["diyitems"]["params"]["discountstyle"] = "1";
                }
                if (!isset($item["params"]["datafrom"])) {
                    $item["params"]["datafrom"] = 0;
                    $item["params"]["categoryid"] = 0;
                    $item["params"]["categorytitle"] = "商户分类";
                    $item["params"]["showchildcategory"] = 0;
                    $item["params"]["store_categorys"] = array();
                    $item["style"]["childcategorycolor"] = "#333333";
                    $item["style"]["childcategoryactivecolor"] = "#ff2d4b";
                }
                if ($item["params"]["datafrom"] == 1) {
                    $cid = intval($item["params"]["categoryid"]);
                    if (empty($cid)) {
                        $item["params"]["datafrom"] = 0;
                        $item["params"]["showchildcategory"] = 0;
                    } else {
                        $page["cid"] = $cid;
                        $categorys = pdo_fetchall("select id, title, thumb, parentid from " . tablename("tiny_wmall_store_category") . " where uniacid = :uniacid and (id = :id or parentid = :parentid) order by parentid asc ", array(":uniacid" => $_W["uniacid"], ":id" => $cid, ":parentid" => $cid), "id");
                        if (empty($categorys)) {
                            $item["params"]["datafrom"] = 0;
                            $item["params"]["categoryid"] = 0;
                            $item["params"]["showchildcategory"] = 0;
                        } else {
                            foreach ($categorys as &$cate) {
                                $cate["thumb"] = tomedia($cate["thumb"]);
                                if ($cate["parentid"] == 0) {
                                    $item["params"]["categoryid"] = $cate["id"];
                                    $item["params"]["categorytitle"] = $cate["title"];
                                    $cate["title"] = "全部";
                                }
                            }
                            $item["params"]["store_categorys"] = array_values($categorys);
                        }
                        if ($item["params"]["showchildcategory"] == 1 && count($categorys) < 2) {
                            $item["params"]["showchildcategory"] = 0;
                        }
                    }
                }
            } else {
                if ($item["id"] == "cart") {
                    if ($item["params"]["showcart"] == 1) {
                        $page["is_show_cart"] = 1;
                    }
                } else {
                    if ($item["id"] == "redpacket") {
                        if ($item["params"]["showredpacket"] == 1) {
                            $page["is_show_redpacket"] = 1;
                        }
                    } else {
                        if ($item["id"] == "guide") {
                            $page["guide"] = $item;
                            if (!isset($item["params"]["guidedata"])) {
                                $item["params"]["guidedata"] = 0;
                            }
                            if (empty($item["params"]["guidedata"])) {
                                if (!empty($item["data"])) {
                                    foreach ($item["data"] as &$gvalue) {
                                        $gvalue["imgUrl"] = tomedia($gvalue["imgUrl"]);
                                    }
                                }
                            } else {
                                if ($item["params"]["guidedata"] == 1) {
                                    $table = "tiny_wmall_slide";
                                    $keys = "id,title,thumb,link,displayorder";
                                    $type = "startpage";
                                }
                                $condition = " where uniacid = :uniacid and type = :type and status = 1 ";
                                $params = array(":uniacid" => $_W["uniacid"], ":type" => $type);
                                if ($mobile || 0 < $_W["agentid"]) {
                                    $condition .= " and agentid = :agentid ";
                                    $params[":agentid"] = $_W["agentid"];
                                }
                                $slides = pdo_fetchall("select " . $keys . " from " . tablename($table) . $condition . " order by displayorder desc", $params);
                                $item["data"] = array();
                                if (!empty($slides)) {
                                    foreach ($slides as $val) {
                                        $childid = rand(1000000000, 9999999999.0);
                                        $childid = "C" . $childid;
                                        $item["data"][$childid] = array("pagePath" => $val["link"], "imgUrl" => tomedia($val["thumb"]));
                                    }
                                }
                            }
                            if (empty($item["data"])) {
                                unset($page["guide"]);
                            }
                        } else {
                            if ($item["id"] == "copyright") {
                                if (!isset($item["params"]["datafrom"])) {
                                    $item["params"]["datafrom"] = 0;
                                    $item["params"]["config"] = "";
                                }
                                if ($item["params"]["datafrom"] == 1) {
                                    $item["params"]["config"] = $_W["we7_wmall"]["config"]["mall"]["copyright"];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if (!$mobile) {
        if (!empty($page["data"]["items"]) && is_array($page["data"]["items"])) {
            foreach ($page["data"]["items"] as $itemid => &$item) {
                if ($item["id"] == "waimai_goods") {
                    $item["data"] = get_wxapp_waimai_goods($item);
                    if (empty($item["data"])) {
                        unset($page["data"]["items"][$itemid]);
                    }
                    if (empty($item["style"]["marginbottom"])) {
                        $item["style"]["marginbottom"] = "0";
                    }
                    if (empty($item["params"]["storeshow"])) {
                        $item["params"]["storeshow"] = "1";
                    }
                } else {
                    if ($item["id"] == "goodsTab") {
                        $item["data"] = get_wxapp_goodsTab($item);
                        if (empty($item["data"])) {
                            unset($page["data"]["items"][$itemid]);
                        }
                    } else {
                        if ($item["id"] == "waimai_stores") {
                            $item["data"] = get_wxapp_waimai_store($item);
                            if (empty($item["data"])) {
                                unset($page["data"]["items"][$itemid]);
                            }
                            if (empty($item["params"]["discountstyle"])) {
                                $item["params"]["discountstyle"] = "1";
                            }
                        } else {
                            if ($item["id"] == "storesTab") {
                                $item["data"] = get_wxapp_storesTab($item);
                                if (empty($item["data"])) {
                                    unset($page["data"]["items"][$itemid]);
                                }
                            } else {
                                if ($item["id"] == "notice") {
                                    $item["data"] = get_wxapp_notice($item, false);
                                    if (empty($item["data"])) {
                                        unset($page["data"]["items"][$itemid]);
                                    }
                                } else {
                                    if ($item["id"] == "bargain") {
                                        $result = get_wxapp_bargains($item);
                                        $item["data"] = $result["data"];
                                        $item["data_num"] = $result["data_num"];
                                        if (empty($item["data"])) {
                                            unset($page["data"]["items"][$itemid]);
                                        }
                                    } else {
                                        if ($item["id"] == "selective") {
                                            $result = get_wxapp_waimai_recommend_store($item);
                                            $item["data"] = $result["data"];
                                            if (empty($item["data"])) {
                                                unset($page["data"]["items"][$itemid]);
                                            }
                                        } else {
                                            if ($item["id"] == "navs") {
                                                $result = get_wxapp_navs($item);
                                                $item["data"] = $result["data"];
                                                $item["data_num"] = $result["data_num"];
                                                $item["row"] = $result["row"];
                                                if (empty($item["data"])) {
                                                    unset($page["data"]["items"][$itemid]);
                                                }
                                            } else {
                                                if ($item["id"] == "richtext") {
                                                    $item["params"]["content"] = htmlspecialchars_decode($item["params"]["content"]);
                                                } else {
                                                    if ($item["id"] == "activity") {
                                                        $result = get_wxapp_cubes($item);
                                                        $item["data"] = $result["data"];
                                                        if (empty($item["data"])) {
                                                            unset($page["data"]["items"][$itemid]);
                                                        }
                                                    } else {
                                                        if ($item["id"] == "picture") {
                                                            if (empty($item["style"])) {
                                                                $item["style"] = array("background" => "#ffffff", "paddingtop" => "0", "paddingleft" => "0");
                                                            }
                                                            if (empty($item["params"])) {
                                                                $item["params"] = array("picturedata" => 0);
                                                            }
                                                            $result = get_wxapp_slides($item);
                                                            $item["data"] = $result["data"];
                                                            if (empty($item["data"])) {
                                                                unset($page["data"]["items"][$itemid]);
                                                            }
                                                        } else {
                                                            if ($item["id"] == "selftake_stores") {
                                                                $item["data"] = get_wxapp_selftake_store($item);
                                                                if (empty($item["data"])) {
                                                                    unset($page["data"]["items"][$itemid]);
                                                                }
                                                            } else {
                                                                if ($item["id"] == "brand_stores") {
                                                                    $item["data"] = get_wxapp_brand_store($item);
                                                                    if (empty($item["data"])) {
                                                                        unset($page["data"]["items"][$itemid]);
                                                                    }
                                                                } else {
                                                                    if ($item["id"] == "service") {
                                                                        if (empty($item["params"]["servicefrom"])) {
                                                                            $item["params"]["servicefrom"] = "meiqia";
                                                                        }
                                                                        if (!isset($item["params"]["iconImg"])) {
                                                                            $item["params"]["iconImg"] = "";
                                                                        }
                                                                        if (!isset($item["params"]["qq"])) {
                                                                            $item["params"]["iconImg"] = "";
                                                                        }
                                                                        if (!isset($item["params"]["wxqrcode"])) {
                                                                            $item["params"]["wxqrcode"] = "";
                                                                        }
                                                                    } else {
                                                                        if ($item["id"] == "picturew") {
                                                                            if (empty($item["style"])) {
                                                                                $item["style"] = array("background" => "#ffffff", "paddingtop" => "0", "paddingleft" => "0");
                                                                            }
                                                                        } else {
                                                                            if (empty($item["id"])) {
                                                                                unset($page["data"]["items"][$itemid]);
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
                            }
                        }
                    }
                }
            }
            unset($item);
            pdo_update("tiny_wmall_diypage", array("data" => base64_encode(json_encode($page["data"]))), array("uniacid" => $_W["uniacid"], "id" => $id));
        }
    } else {
        if (!empty($page["data"]["items"]) && is_array($page["data"]["items"])) {
            foreach ($page["data"]["items"] as $itemid => &$item) {
                if ($item["id"] == "richtext") {
                    $item["params"]["content"] = base64_decode($item["params"]["content"]);
                } else {
                    if ($item["id"] == "waimai_goods") {
                        $item["data"] = get_wxapp_waimai_goods($item, true);
                        if (empty($item["data"])) {
                            unset($page["data"]["items"][$itemid]);
                        }
                    } else {
                        if ($item["id"] == "goodsTab") {
                            $item["data"] = get_wxapp_goodsTab($item);
                            if (empty($item["data"])) {
                                unset($page["data"]["items"][$itemid]);
                            } else {
                                $page["is_has_goodsTab"] = 1;
                            }
                        } else {
                            if ($item["id"] == "waimai_stores") {
                                $item["data"] = get_wxapp_waimai_store($item, true);
                                if (empty($item["data"])) {
                                    unset($page["data"]["items"][$itemid]);
                                }
                            } else {
                                if ($item["id"] == "storesTab") {
                                    $item["data"] = get_wxapp_storesTab($item, true);
                                    if (empty($item["data"])) {
                                        unset($page["data"]["items"][$itemid]);
                                    } else {
                                        $page["is_has_storesTab"] = 1;
                                    }
                                } else {
                                    if ($item["id"] == "selective") {
                                        $result = get_wxapp_waimai_recommend_store($item, true);
                                        $item["data"] = $result["data"];
                                        $item["data_num"] = $result["data_num"];
                                        if (empty($item["data"])) {
                                            unset($page["data"]["items"][$itemid]);
                                        }
                                    } else {
                                        if ($item["id"] == "bargain") {
                                            $_config_bargain["status"] = 1;
                                            if ($extra["pagetype"] == "default") {
                                                $_config_bargain = get_plugin_config("bargain");
                                                if ($_config_bargain["status"] != 1 || $_config_bargain["is_home_display"] != 1) {
                                                    $_config_bargain["status"] = 0;
                                                } else {
                                                    $item["params"]["bargainnum"] = $_config_bargain["home_number"] ? $_config_bargain["home_number"] : 8;
                                                }
                                            }
                                            $result = get_wxapp_bargains($item, true);
                                            $item["data"] = $result["data"];
                                            $item["data_num"] = $result["data_num"];
                                            if (empty($item["data"]) || !$_config_bargain["status"]) {
                                                unset($page["data"]["items"][$itemid]);
                                            }
                                        } else {
                                            if (in_array($item["id"], array("copyright", "notice", "img_card"))) {
                                                $item["params"]["imgurl"] = tomedia($item["params"]["imgurl"]);
                                                if ($item["id"] == "notice") {
                                                    $item["data"] = get_wxapp_notice($item, true);
                                                    if (empty($item["data"])) {
                                                        unset($page["data"]["items"][$itemid]);
                                                    }
                                                }
                                            } else {
                                                if (in_array($item["id"], array("banner", "graphic")) && !empty($item["data"])) {
                                                    foreach ($item["data"] as &$v) {
                                                        $v["imgurl"] = tomedia($v["imgurl"]);
                                                    }
                                                } else {
                                                    if ($item["id"] == "picturew" && !empty($item["data"])) {
                                                        foreach ($item["data"] as &$v) {
                                                            $v["imgurl"] = tomedia($v["imgurl"]);
                                                        }
                                                        $item["data_num"] = count($item["data"]);
                                                        if (in_array($item["params"]["row"], array("1", "5", "6"))) {
                                                            $item["data"] = array_values($item["data"]);
                                                        } else {
                                                            if ($item["params"]["showtype"] == 1 && $item["params"]["pagenum"] < count($item["data"])) {
                                                                $item["data"] = array_chunk($item["data"], $item["params"]["pagenum"]);
                                                                $item["style"]["rows_num"] = ceil($item["params"]["pagenum"] / $item["params"]["row"]);
                                                                $row_base_height = array("2" => 122, "3" => 85, "4" => 65);
                                                                $item["style"]["base_height"] = $row_base_height[$item["params"]["row"]];
                                                            }
                                                        }
                                                    } else {
                                                        if ($item["id"] == "navs" && !empty($item["data"])) {
                                                            $result = get_wxapp_navs($item, true);
                                                            $item["data"] = $result["data"];
                                                            $item["data_num"] = $result["data_num"];
                                                            $item["row"] = $result["row"];
                                                            if (empty($item["data"])) {
                                                                unset($page["data"]["items"][$itemid]);
                                                            }
                                                        } else {
                                                            if ($item["id"] == "danmu") {
                                                                $config_danmu["params"] = $item["params"];
                                                                $result = get_wxapp_danmu($config_danmu);
                                                                if (empty($result["members"])) {
                                                                    unset($page["data"]["items"][$itemid]);
                                                                } else {
                                                                    $item["members"] = $result["members"];
                                                                    $page["danmu"] = $result["members"];
                                                                }
                                                            } else {
                                                                if ($item["id"] == "memberHeader") {
                                                                    $item["member"] = $_W["member"];
                                                                    if ($item["params"]["headerstyle"] == "img") {
                                                                        $item["params"]["backgroundimgurl"] = tomedia($item["params"]["backgroundimgurl"]);
                                                                    }
                                                                } else {
                                                                    if ($item["id"] == "memberBindMobile") {
                                                                        if (!empty($_W["member"]["mobile"])) {
                                                                            $item["has_mobile"] = 1;
                                                                        }
                                                                    } else {
                                                                        if ($item["id"] == "blockNav") {
                                                                            if (!empty($item["data"])) {
                                                                                foreach ($item["data"] as &$value) {
                                                                                    $value["imgurl"] = tomedia($value["imgurl"]);
                                                                                    if ($value["linkurl"] == "pages/member/redPacket/index") {
                                                                                        $redpacket_nums = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and uid = :uid and status = 1", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"])));
                                                                                        if (0 < $redpacket_nums) {
                                                                                    $value["placeholder"] = (string) $redpacket_nums . "个未使用";
                                                                                        }
                                                                                    } else {
                                                                                        if ($value["linkurl"] == "pages/member/coupon/index") {
                                                                                            $coupon_nums = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_activity_coupon_record") . " where uniacid = :uniacid and uid = :uid and status = 1", array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"])));
                                                                                            if (0 < $coupon_nums) {
                                                                                        $value["placeholder"] = (string) $coupon_nums . "个未使用";
                                                                                            }
                                                                                        } else {
                                                                                            if ($value["linkurl"] == "package/pages/deliveryCard/index") {
                                                                                                $deliveryCard_status = check_plugin_perm("deliveryCard") && get_plugin_config("deliveryCard.card_apply_status");
                                                                                            $value["placeholder"] = "暂未购买";
                                                                                                if ($deliveryCard_status && 0 < $_W["member"]["setmeal_id"] && TIMESTAMP < $_W["member"]["setmeal_endtime"]) {
                                                                                            $value["placeholder"] = "已购买";
                                                                                            }
                                                                                        } else {
                                                                                            if ($value["linkurl"] == "pages/member/recharge") {
                                                                                                    $value["placeholder"] = "楼" . $_W["member"]["credit2"];
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        } else {
                                                                            if ($item["id"] == "activity") {
                                                                                $result = get_wxapp_cubes($item, true);
                                                                                $item["data"] = array_values($result["data"]);
                                                                                if (empty($item["data"])) {
                                                                                    unset($page["data"]["items"][$itemid]);
                                                                                }
                                                                            } else {
                                                                                if ($item["id"] == "picture") {
                                                                                    $result = get_wxapp_slides($item, true);
                                                                                    $item["data"] = array_values($result["data"]);
                                                                                    if (empty($item["data"])) {
                                                                                        unset($page["data"]["items"][$itemid]);
                                                                                    }
                                                                                } else {
                                                                                    if ($item["id"] == "gohomeActivity") {
                                                                                        $item["data"] = get_wxapp_gohome_goods($item, true);
                                                                                        if (empty($item["data"])) {
                                                                                            unset($page["data"]["items"][$itemid]);
                                                                                        }
                                                                                    } else {
                                                                                        if ($item["id"] == "tongchengStatistics") {
                                                                                            $item["params"]["imgurl"] = tomedia($item["params"]["imgurl"]);
                                                                                            mload()->model("plugin");
                                                                                            pload()->model("tongcheng");
                                                                                            $item["data"] = tongcheng_flow_update();
                                                                                        } else {
                                                                                            if ($item["id"] == "tongcheng") {
                                                                                                mload()->model("plugin");
                                                                                                pload()->model("tongcheng");
                                                                                                $infor_filter = array();
                                                                                                if ($item["params"]["informationdata"] != 1) {
                                                                                                    $infor_filter["psize"] = $item["params"]["informationnum"];
                                                                                                }
                                                                                                $informations = tongcheng_get_informations($infor_filter);
                                                                                                $page["tongcheng"]["informationdata"] = $informations["informations"];
                                                                                                $page["tongcheng"]["has_get_all"] = !$item["params"]["informationdata"];
                                                                                            } else {
                                                                                                if ($item["id"] == "haodianSettle") {
                                                                                                    $item["params"]["imgurl"] = tomedia($item["params"]["imgurl"]);
                                                                                                    mload()->model("plugin");
                                                                                                    pload()->model("haodian");
                                                                                                    $item["data"] = haodian_new_settle_info();
                                                                                                } else {
                                                                                                    if ($item["id"] == "haodianList") {
                                                                                                        mload()->model("plugin");
                                                                                                        pload()->model("haodian");
                                                                                                        $stores = haodian_store_fetchall(array("get_activity" => 1));
                                                                                                        $page["haodian"]["store"] = $stores["store"];
                                                                                                        $page["haodian"]["haodian_child_id"] = 0;
                                                                                                        $categorys = pdo_fetchall("select id, title as text from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid and parentid = :parentid order by displayorder desc,id asc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":parentid" => 0));
                                                                                                        if (!empty($categorys)) {
                                                                                                            foreach ($categorys as &$cate) {
                                                                                                                $cate["children"] = pdo_fetchall("select id, title as text from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid and parentid = :parentid order by displayorder desc,id asc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":parentid" => $cate["id"]));
                                                                                                                if (!empty($cate["children"])) {
                                                                                                                    foreach ($cate["children"] as &$child) {
                                                                                                                        $child["id"] = intval($child["id"]);
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                            if (!empty($categorys[0]["children"])) {
                                                                                                                $page["haodian"]["haodian_child_id"] = $categorys[0]["children"][0]["id"];
                                                                                                            }
                                                                                                        }
                                                                                                        $page["haodian"]["category"] = $categorys;
                                                                                                    } else {
                                                                                                        if ($item["id"] == "haodianGroup") {
                                                                                                            $result = get_wxapp_haodian_store($item, true);
                                                                                                            $item["data"] = $result["data"];
                                                                                                            $item["data_num"] = $result["data_num"];
                                                                                                            if (empty($item["data"])) {
                                                                                                                unset($page["data"]["items"][$itemid]);
                                                                                                            }
                                                                                                        } else {
                                                                                                            if ($item["id"] == "svipGuide") {
                                                                                                                if (check_plugin_perm("svip")) {
                                                                                                                    if ($_W["member"]["svip_status"] == 1) {
                                                                                                                        $item["params"]["link"] = "/package/pages/svip/mine";
                                                                                                                        mload()->model("plugin");
                                                                                                                        pload()->model("svip");
                                                                                                                        $total = svip_member_redpacket_total();
                                                                                                                $item["params"]["text_left"] = "已为我节省" . $total . "元";
                                                                                                                $item["params"]["text_right"] = (string) $_W["member"]["svip_credit1"] . "个奖励金";
                                                                                                                } else {
                                                                                                                    $item["params"]["link"] = "/package/pages/svip/index";
                                                                                                                    $config_svip = get_plugin_config("svip.basic");
                                                                                                                    $exchange_max = intval($config_svip["exchange_max"]);
                                                                                                                $item["params"]["text_left"] = "每月领" . $exchange_max . "个红包";
                                                                                                                $item["params"]["text_right"] = "立即开通";
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    unset($page["data"]["items"][$itemid]);
                                                                                                                }
                                                                                                            } else {
                                                                                                                if ($item["id"] == "selftake_stores") {
                                                                                                                    $item["data"] = get_wxapp_selftake_store($item, true);
                                                                                                                    if (empty($item["data"])) {
                                                                                                                        unset($page["data"]["items"][$itemid]);
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    if ($item["id"] == "brand_stores") {
                                                                                                                        $item["data"] = get_wxapp_brand_store($item, true);
                                                                                                                        if (empty($item["data"])) {
                                                                                                                            unset($page["data"]["items"][$itemid]);
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        if ($item["id"] == "service") {
                                                                                                                            $page["is_show_kefu"] = 0;
                                                                                                                            if (empty($item["params"]["servicefrom"])) {
                                                                                                                                $item["params"]["servicefrom"] = "meiqia";
                                                                                                                            }
                                                                                                                            if ($item["params"]["showservice"] == 1) {
                                                                                                                                if ($item["params"]["servicefrom"] == "meiqia") {
                                                                                                                                    $page["is_show_kefu"] = 1;
                                                                                                                                }
                                                                                                                                $item["params"]["iconImg"] = tomedia($item["params"]["iconImg"]);
                                                                                                                                if ($item["params"]["servicefrom"] == "qq") {
                                                                                                                                    if (empty($item["params"]["qq"])) {
                                                                                                                                        unset($page["data"]["items"][$itemid]);
                                                                                                                                    } else {
                                                                                                                                        $item["params"]["qq_url"] = "http://wpa.qq.com/msgrd?v=3&uin=" . $item["params"]["qq"] . "&site=qq&menu=yes";
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    if ($item["params"]["servicefrom"] == "weixin") {
                                                                                                                                        if (empty($item["params"]["wxqrcode"])) {
                                                                                                                                            unset($page["data"]["items"][$itemid]);
                                                                                                                                        } else {
                                                                                                                                            $item["params"]["wxqrcode"] = tomedia($item["params"]["wxqrcode"]);
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
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            unset($item);
        }
    }
    return $page;
}
function get_wxapp_gohome_goods($item, $mobile = false)
{
    global $_W;
    $type = $item["params"]["type"];
    $config = get_plugin_config("gohome.basic");
    if ($config["status"][$type] != 1) {
        return array();
    }
    mload()->model("plugin");
    pload()->model($type);
    $filter = array("status" => 1, "sid" => $item["params"]["sid"]);
    if ($item["params"]["goodsdata"] == "0") {
        if (!empty($item["data"]) && is_array($item["data"])) {
            $goodsids = array();
            foreach ($item["data"] as $data) {
                if (!empty($data["id"])) {
                    $goodsids[] = $data["id"];
                }
            }
            if (!empty($goodsids)) {
                $filter["ids"] = array_unique($goodsids);
            } else {
                return array();
            }
        }
        $filter["psize"] = 50;
    } else {
        if ($item["params"]["goodsdata"] == "1") {
            if (empty($mobile)) {
                return $item["data"];
            }
            $filter["psize"] = isset($item["params"]["goodsnum"]) ? intval($item["params"]["goodsnum"]) : 10;
        }
    }
    if ($type == "kanjia") {
        $goods = kanjia_get_activitylist($filter);
    } else {
        if ($type == "pintuan") {
            $goods = pintuan_get_activitylist($filter);
        } else {
            if ($type == "seckill") {
                $goods = seckill_allgoods($filter);
            }
        }
    }
    $item["data"] = array();
    if (!empty($goods)) {
        foreach ($goods as $val) {
            $peoplenum = $val["peoplenum"];
            $userlists = array();
            if (!empty($val["userlist"])) {
                $peoplenum = count($val["userlist"]);
                foreach ($val["userlist"] as $key => $userlist) {
                    $userlists[] = tomedia($userlist["avatar"]);
                }
            }
            $item["data"][] = array("id" => $val["id"], "sid" => $val["sid"], "thumb" => $val["thumb"], "price" => $val["price"], "old_price" => $val["oldprice"], "title" => $val["name"], "discount" => $val["discount"], "falesailed_total" => $val["falesailed_total"] ? $val["falesailed_total"] : $val["sailed"], "sailed_percent" => $val["sailed_percent"], "peoplenum" => $peoplenum, "peopleimg" => array_slice($userlists, 0, 3), "total_joinnum" => $val["total_joinnum"]);
        }
    }
    return $item["data"];
}
function get_wxapp_defaultpage($type = "home", $from = "")
{
    global $_W;
    global $_GPC;
    if (empty($from)) {
        $from = $_GPC["from"];
    }
    $type = (string) $from . $type;
    $pages = array("vuehome" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "1", "data" => "eyJwYWdlIjp7InR5cGUiOiIxIiwidGl0bGUiOiJcdTU1NjZcdTU1NjZcdTU5MTZcdTUzNTZcdTZmMTRcdTc5M2FcdTdhZDkiLCJuYW1lIjoiamRraiIsImRlc2MiOiJcdTUwNjVcdTVlYjdcdTc2ODRcdTUwNjVcdTVlYjdcdTc2ODRcdTVjMzFcdTc3MGJcdTg5YzlcdTVmOTciLCJrZXl3b3JkIjoiIiwiYmFja2dyb3VuZCI6IiNGM0YzRjMiLCJkaXlnb3RvcCI6IjAiLCJuYXZpZ2F0aW9uYmFja2dyb3VuZCI6IiMwMDAwMDAiLCJuYXZpZ2F0aW9udGV4dGNvbG9yIjoiI2ZmZmZmZiIsInRodW1iIjoiaW1hZ2VzXC8xXC8yMDE4XC8wN1wvZENUQ1JLUlFLc1VUUjdxVXU3Z3dTRnJkZER1QzdGLmpwZyIsImRpeW1lbnUiOiItMSJ9LCJpdGVtcyI6eyJNMTUzMjUwNjQ1NDM5OSI6eyJwYXJhbXMiOnsibG9jYXRpb24iOiJcdTViOWFcdTRmNGQiLCJ0ZXh0IjoiXHU4YmY3XHU4ZjkzXHU1MTY1XHU1NTQ2XHU2MjM3XHU2MjE2XHU1NTQ2XHU1NGMxXHU1NDBkXHU3OWYwIiwibGluayI6IlwvcGFnZXNcL2hvbWVcL3NlYXJjaCJ9LCJzdHlsZSI6eyJsb2NzdHlsZSI6InJhZGl1cyIsInNlYXJjaHN0eWxlIjoicmFkaXVzIiwiZml4ZWRiYWNrZ3JvdW5kIjoiI2ZmMmI0ZCIsImxvY2JhY2tncm91bmQiOiIjOTk5OTk5Iiwic2VhcmNoYmFja2dyb3VuZCI6IiNmNGY0ZjQiLCJsb2Njb2xvciI6IiNmZmZmZmYiLCJzZWFyY2hjb2xvciI6IiM2NTY1NjUifSwibWF4IjoiMSIsImlzdG9wIjoiMSIsImlkIjoiZml4ZWRzZWFyY2gifSwiTTE1MzMwMzE1ODAzMTAiOnsicGFyYW1zIjp7InBpY3R1cmVkYXRhIjoiMSJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTAiLCJwYWRkaW5nbGVmdCI6IjEwIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIiLCJiYWNrZ3JvdW5kIjoiI2ZhZmFmYSJ9LCJkYXRhIjp7IkMxMjUyNDIzOTkxIjp7Imxpbmt1cmwiOiJodHRwczpcL1wvd3d3LmFpc2t6LmNjXC9hcHBcL2luZGV4LnBocD9pPTYmYz1lbnRyeSZtPWV3ZWlfc2hvcHYyJmRvPW1vYmlsZSZyPW1lcmNoJm1lcmNoaWQ9MTg5IiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOVwvMDZcL29tM214N1BXWGUxWGV3N1ZPdzg4TXBXN0V3VVY2Ry5qcGcifSwiQzEwNzI1ODIzNzgiOnsibGlua3VybCI6IndlYnZpZXc6aHR0cHM6XC9cL3d3dy5haXNrei5jY1wvYXBwXC9pbmRleC5waHA/aT02JmM9ZW50cnkmbT1ld2VpX3Nob3B2MiZkbz1tb2JpbGUmcj1tZXJjaCZtZXJjaGlkPTE0MCIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9hb0tPNUU1cG8zZTNrNmtPek8wRzdvVTMzY2RFb2cuanBnIn0sIkMxMDE4NDMzNDIxIjp7Imxpbmt1cmwiOiJodHRwczpcL1wvd3d3LmFpc2t6LmNjXC9hcHBcL2luZGV4LnBocD9pPTYmYz1lbnRyeSZtPWV3ZWlfc2hvcHYyJmRvPW1vYmlsZSZyPW1lcmNoJm1lcmNoaWQ9MTkxIiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOVwvMDZcL25yMVZ0UDlwcHY4UFE4MWI1VHBaQlRRUTY5dFRSMS5qcGcifX0sImlkIjoicGljdHVyZSJ9LCJNMTUzMjU3Nzg5MzcyOCI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIxIiwic2hvd2RvdCI6IjEiLCJyb3dudW0iOiI1IiwicGFnZW51bSI6IjEwIiwibmF2c2RhdGEiOiIxIiwibmF2c251bSI6IjUwIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJuYXZzdHlsZSI6IiIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIn0sImRhdGEiOnsiQzEzNjMyODczMTYiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MSIsInRleHQiOiJcdTcwZWRcdTUzNTZcdTdmOGVcdTk4ZGYiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL3UwMGg2MjZQNkgxODkxTHNQeXk5NlB4SHphWVRoeC5wbmcifSwiQzEwNDA1MzM1MDMiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MiIsInRleHQiOiJcdTg0MjVcdTUxN2JcdTY1ZTlcdTk5MTAiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL3VVNjZLblJmNm5nNnY2TXY2Q1ZLbjdSbGxhclZyai5wbmcifSwiQzEzNzU4NzY2MDUiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9OCIsInRleHQiOiJcdTljOWNcdTgyYjFcdTdjZDVcdTcwYjkiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL1lrZnBCV3c1eWlQanhKUnF6cmpEaWpnendCcWRlay5wbmcifSwiQzExOTUzODQyNTMiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NiIsInRleHQiOiJcdTdjYmVcdTU0YzFcdTVjMGZcdTU0MDMiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL0o0OGk0SUtLcEo4NzhKRUZ6M3JSOGZ6WGZaN2l4ei5wbmcifSwiQzExOTg0NTAyMzMiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NCIsInRleHQiOiJcdTRlMmRcdTg5N2ZcdTVmZWJcdTk5MTAiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL2Zia05TdnpzMHo2NVY1QTU3ZFcwNmt5NGF3bno3ZC5wbmcifSwiQzEyNDQzMTQ3ODciOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NyIsInRleHQiOiJcdTdmOGVcdTU0NzNcdTU5MWNcdTViYjUiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL20xMzVjZ2tsOG44dGdvVEtPS0c4VzMxMTM4NVJTbi5wbmcifSwiQzEwNjAwNjgxNzUiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MyIsInRleHQiOiJcdTc1MWNcdTcwYjlcdTk5NmVcdTU0YzEiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL3RTRHZOY2NoOUFTajl2dUpoWkpNcDk4Y25QOE52SC5wbmcifSwiQzExNDA4NzIzODQiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MTUiLCJ0ZXh0IjoiXHU3NTFmXHU5YzljIiwiaW1ndXJsIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTdcLzAxXC9KZ1RBNWJHdGcyazFDYWlUR3c1OTFHNW9NZ21jNEMucG5nIn0sIkMxMjI4MjM0MDM2Ijp7Imxpbmt1cmwiOiJnb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9pbmRleCIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTk5OTZcdTk4NzUiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL1M2Tzg2dE1PaTE4a0tVUFA4dW5UdHQ4dFJ1NDYzMS5wbmcifSwiQzEzMzgzNzE1MzgiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NDgiLCJ0ZXh0IjoiXHU1MjllXHU1MTZjXHU2NTg3XHU1MTc3IiwiaW1ndXJsIjoiIn19LCJpZCI6Im5hdnMiLCJkYXRhX251bSI6MTAsInJvdyI6Mn0sIk0xNTM2NzQxNDcxMjg1Ijp7InBhcmFtcyI6eyJzaG93cmVkcGFja2V0IjoiMSJ9LCJtYXgiOiIxIiwiaWQiOiJyZWRwYWNrZXQifSwiTTE1MzI2MDI1NTI4MzUiOnsicGFyYW1zIjp7Im5vdGljZWRhdGEiOiIwIiwibm90aWNlbnVtIjoiMTAiLCJzcGVlZCI6IjMiLCJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3N0YXRpY1wvaW1nXC9oZWFkX2xpbmUucG5nIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIwIiwidGV4dGNvbG9yIjoiIzY2NjY2NiIsImljb25jb2xvciI6IiNmZDU0NTQiLCJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiJ9LCJkYXRhIjp7IkMxMDg2MjQ3ODg3Ijp7ImlkIjoiMiIsInRpdGxlIjoiXHU1OTgyXHU5MDQ3XHU5NWVlXHU5ODk4XHU4YmY3XHU4MDU0XHU3Y2ZiXHVmZjFhMDIzLTUyMTE0OTg4IiwibGlua3VybCI6IiJ9LCJDMTA0NzExNTk3NCI6eyJpZCI6IjMiLCJ0aXRsZSI6Ilx1NTk4Mlx1OTA0N1x1OTVlZVx1OTg5OFx1OGJmN1x1ODA1NFx1N2NmYlx1ZmYxYTAyMy01MjExNDk4OCIsImxpbmt1cmwiOiIifX0sImlkIjoibm90aWNlIn0sIk0xNTMyNjgwODkwNDQwIjp7InN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmZmZmZmIiwibWFyZ2luVG9wIjoiMTAiLCJtYXJnaW5Cb3R0b20iOiIwIn0sInBhcmFtcyI6eyJhY3Rpdml0eWRhdGEiOiIxIn0sImRhdGEiOnsiQzEwODEwMTY5NTEiOnsibGlua3VybCI6Imh0dHBzOlwvXC93d3cuYWlza3ouY2NcL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvdnVlXC9pbmRleC5odG1sP21lbnU9I1wvcGFnZXNcL3N0b3JlXC9nb29kcz9zaWQ9MzcmaT02IiwidGV4dCI6Ilx1NzIzMVx1NGUwYVx1OGQ4NVx1NWUwMiIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9FS0VKY3hlMnNqUEZKR3AyZnBKQ3ZwZlBQZUpnQ3MuanBnIiwicGxhY2Vob2xkZXIiOiIyNFx1NWMwZlx1NjVmNlx1NTE2OFx1NTdjZVx1OTE0ZFx1OTAwMSIsImNvbG9yIjoiI2ZmMmQ0YiIsInBsYWNlaG9sZGVyQ29sb3IiOiIjN2I3YjdiIn0sIkMxMDAyMzQwMTU1Ijp7Imxpbmt1cmwiOiJodHRwczpcL1wvd3d3LmFpc2t6LmNjXC9hcHBcL2luZGV4LnBocD9pPTYmYz1lbnRyeSZtPWV3ZWlfc2hvcHYyJmRvPW1vYmlsZSZyPW1lcmNoJm1lcmNoaWQ9MTQwIiwidGV4dCI6Ilx1NWJiNlx1NjUzZlx1NjcwZFx1NTJhMSIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9uVGg0UnQ0U0Q0TFhRYmxvZUhESFJRRjB0c0dET3guanBnIiwicGxhY2Vob2xkZXIiOiJcdThiYTlcdTc1MWZcdTZkM2JcdTY2ZjRcdTdmOGVcdTU5N2QiLCJjb2xvciI6IiNmZjJkNGIiLCJwbGFjZWhvbGRlckNvbG9yIjoiIzdiN2I3YiJ9LCJDMTM5MDM0MzAyMyI6eyJsaW5rdXJsIjoiaHR0cHM6XC9cL3d3dy5haXNrei5jY1wvYXBwXC9pbmRleC5waHA/aT02JmM9ZW50cnkmbT1ld2VpX3Nob3B2MiZkbz1tb2JpbGUmcj1jcmVkaXRzaG9wIiwidGV4dCI6Ilx1NzllZlx1NTIwNlx1NTU0Nlx1NTdjZSIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9EbDVIQ2QxY05YSzJaMnhybVpVVW44NnU4TTdoOFouanBnIiwicGxhY2Vob2xkZXIiOiJcdTc1MjhcdTYyMzdcdThkODVcdTUwM2NcdTU2ZGVcdTk5ODgiLCJjb2xvciI6IiNmZjJkNGIiLCJwbGFjZWhvbGRlckNvbG9yIjoiIzdiN2I3YiJ9LCJDMTMxMTM2NTg4NyI6eyJsaW5rdXJsIjoiaHR0cHM6XC9cL3d3dy5haXNrei5jY1wvYXBwXC9pbmRleC5waHA/aT02JmM9ZW50cnkmbT1ld2VpX3Nob3B2MiZkbz1tb2JpbGUmcj1tZXJjaCZtZXJjaGlkPTE4OSIsInRleHQiOiJcdTViYjZcdTc1MzVcdTZlMDVcdTZkMTciLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE5XC8wNlwvTTJpbzJubzhPd2h2aGRXN25ITzd4UFdESW91ZFY3LmpwZyIsInBsYWNlaG9sZGVyIjoiXHU1NTJlXHU1NDBlXHU2NzBkXHU1MmExXHU0ZmRkXHU5NjljIiwiY29sb3IiOiIjZmYyZDRiIiwicGxhY2Vob2xkZXJDb2xvciI6IiM3YjdiN2IifX0sImlkIjoiYWN0aXZpdHkifSwiTTE1MzQxNDExNjczMDEiOnsicGFyYW1zIjp7InNob3d0eXBlIjoiMCIsInBhZ2VudW0iOiIzIiwic3RvcmVkYXRhIjoiMSIsInN0b3JlbnVtIjoiNiIsInRpdGxlIjoiXHU0ZTNhXHU2MGE4XHU0ZjE4XHU5MDA5In0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwidGl0bGVjb2xvciI6IiMzMzMzMzMiLCJzdG9yZWNvbG9yIjoiIzMzMzMzMyIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIiwic2hvd2RvdCI6IjAifSwiZGF0YSI6eyJDMTAwMDI3NTMxMiI6eyJpZCI6IjMiLCJ0aXRsZSI6Ilx1ODMzNlx1NGUwZFx1NjAxZCIsImxvZ28iOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8xMVwva3Z5T29JeXRJZWEyU3liVDBFVnNMTDNZaTkyYnYwLmpwZyIsImZvcndhcmRfbW9kZSI6IjEiLCJmb3J3YXJkX3VybCI6IiIsInVybCI6IlwvcGFnZXNcL3N0b3JlXC9ob21lP3NpZD0zIiwic3RvcmVfaWQiOiIzIn0sIkMxMzUxMDg1OTY4Ijp7ImlkIjoiODMiLCJ0aXRsZSI6Ilx1OTc2Mlx1OTk4NiIsImxvZ28iOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxNlwvMDhcL1hsRHpjTkQzN3hDMU1BZFhEQzYwWlhkRENMWGRpOS5wbmciLCJmb3J3YXJkX21vZGUiOiIwIiwiZm9yd2FyZF91cmwiOiIiLCJ1cmwiOiJcL3BhZ2VzXC9zdG9yZVwvZ29vZHM/c2lkPTgzIiwic3RvcmVfaWQiOiI4MyJ9LCJDMTI5MzE4Mjc1MCI6eyJpZCI6IjU1NyIsInRpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NTI4XHU1NGMxXHU1ZTk3IiwibG9nbyI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzEyXC9jbGhKMjI4Ykw2VW0wYjhMMkIwMjdNbXU4TDJMS2ouanBnIiwiZm9yd2FyZF9tb2RlIjoiMCIsImZvcndhcmRfdXJsIjoiIiwidXJsIjoiXC9wYWdlc1wvc3RvcmVcL2dvb2RzP3NpZD01NTciLCJzdG9yZV9pZCI6IjU1NyJ9LCJDMTMwNTcwOTQ2OCI6eyJpZCI6IjY0IiwidGl0bGUiOiJcdTZjMTFcdTc5OGZcdTkxNTJcdTY5N2MiLCJsb2dvIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTZcLzA4XC9ORGZvaXpFekIyS0ZGSzU3S0tmaU9NS0tiNzJFNzUucG5nIiwiZm9yd2FyZF9tb2RlIjoiMCIsImZvcndhcmRfdXJsIjoiIiwidXJsIjoiXC9wYWdlc1wvc3RvcmVcL2dvb2RzP3NpZD02NCIsInN0b3JlX2lkIjoiNjQifSwiQzEwMjQ0NTI3NTIiOnsiaWQiOiI2OCIsInRpdGxlIjoiXHU5MGVkXHU4YmIwXHU1OTFhXHU1NDczXHU5OTEwXHU5OTg2IiwibG9nbyI6Imh0dHA6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTZcLzA4XC9ENnVXelhXd1hUR1U1OXc5N3Q3M1FHMzk5OTUzVHguanBnIiwiZm9yd2FyZF9tb2RlIjoiMCIsImZvcndhcmRfdXJsIjoiIiwidXJsIjoiXC9wYWdlc1wvc3RvcmVcL2dvb2RzP3NpZD02OCIsInN0b3JlX2lkIjoiNjgifSwiQzEyNDQxMTQ1NjAiOnsiaWQiOiI2OSIsInRpdGxlIjoiXHU2ODQzXHU2ZTkwXHU5OGRmXHU1ZTk3IiwibG9nbyI6Imh0dHBzOlwvXC8xLnhpbnp1b3dsLmNvbVwvYXR0YWNobWVudFwvaW1hZ2VzXC8xXC8yMDE2XC8wOFwvS043ZVU2c25hRjllNjZDNW41NHBuakc1NjR6Nzk1LmpwZyIsImZvcndhcmRfbW9kZSI6IjAiLCJmb3J3YXJkX3VybCI6IiIsInVybCI6IlwvcGFnZXNcL3N0b3JlXC9nb29kcz9zaWQ9NjkiLCJzdG9yZV9pZCI6IjY5In19LCJpZCI6InNlbGVjdGl2ZSJ9LCJNMTUzMjY4MDg1MDAwMSI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIwIiwicGFnZW51bSI6IjQiLCJ0aXRsZSI6Ilx1NTkyOVx1NTkyOVx1NzI3OVx1NGVmNyIsImJhcmdhaW5udW0iOiI4In0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIiLCJ0aXRsZWNvbG9yIjoiIzMzMzMzMyIsImdvb2RzbmFtZWNvbG9yIjoiIzMzMzMzMyIsInNob3dkb3QiOiIxIn0sImRhdGEiOnsiQzExNDA3NTk3NTYiOnsidGh1bWIiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMTJcL3BaVDhOdTVYd1c4NTI5dTVOeHFXaWlVWGJ3MjVCTi5qcGciLCJkaXNjb3VudCI6MTAsImdvb2RzX2lkIjoiOTgxOSIsImJhcmdhaW5faWQiOiIxOSIsInRpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NmQyIiwiZGlzY291bnRfcHJpY2UiOiI2LjUiLCJwcmljZSI6IjYuNSIsInNpZCI6IjU1NyIsInN0b3JlX3RpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NTI4XHU1NGMxXHU1ZTk3In0sIkMxMDE3MzMyMTcxIjp7InRodW1iIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTdcLzEyXC9yQ242ZTU1NXVuZHU2VTAwZmNSNWZNREdOMkNNR2suanBnIiwiZGlzY291bnQiOjEwLCJnb29kc19pZCI6Ijk4MTgiLCJiYXJnYWluX2lkIjoiMTkiLCJ0aXRsZSI6Ilx1NThhOFx1NmMzNCIsImRpc2NvdW50X3ByaWNlIjoiNSIsInByaWNlIjoiNSIsInNpZCI6IjU1NyIsInN0b3JlX3RpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NTI4XHU1NGMxXHU1ZTk3In0sIkMxMzk3MDI1NjA4Ijp7InRodW1iIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTdcLzEyXC95enozZmRJRFhJbHFmWFhoZGJwbG1FdmJieHZmZEguanBnIiwiZGlzY291bnQiOjEwLCJnb29kc19pZCI6Ijk4MTciLCJiYXJnYWluX2lkIjoiMTkiLCJ0aXRsZSI6Ilx1OTRhMlx1N2IxNCIsImRpc2NvdW50X3ByaWNlIjoiMjMiLCJwcmljZSI6IjIzIiwic2lkIjoiNTU3Iiwic3RvcmVfdGl0bGUiOiJcdTY1ODdcdTUxNzdcdTc1MjhcdTU0YzFcdTVlOTcifSwiQzEzODYyMjU4NTEiOnsidGh1bWIiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMTJcL205SkoxYkUwYlc2aGp6MTY2OUV6OWE2MUFBckhoaC5qcGciLCJkaXNjb3VudCI6MTAsImdvb2RzX2lkIjoiOTgxNSIsImJhcmdhaW5faWQiOiIxOSIsInRpdGxlIjoiXHU3YjE0XHU4YmIwXHU2NzJjIiwiZGlzY291bnRfcHJpY2UiOiIxMCIsInByaWNlIjoiMTAiLCJzaWQiOiI1NTciLCJzdG9yZV90aXRsZSI6Ilx1NjU4N1x1NTE3N1x1NzUyOFx1NTRjMVx1NWU5NyJ9LCJDMTA3NDAzNDAyNSI6eyJ0aHVtYiI6Imh0dHBzOlwvXC8xLnhpbnp1b3dsLmNvbVwvYXR0YWNobWVudFwvaW1hZ2VzXC8xXC8yMDE3XC8xMlwvYjc3azIyN3lLMkJpcjVCUFY3TG9CbHo3NTdZN3k5LmpwZyIsImRpc2NvdW50IjoxMCwiZ29vZHNfaWQiOiI5ODE2IiwiYmFyZ2Fpbl9pZCI6IjE5IiwidGl0bGUiOiJcdTVjM2FcdTViNTAiLCJkaXNjb3VudF9wcmljZSI6IjMiLCJwcmljZSI6IjMiLCJzaWQiOiI1NTciLCJzdG9yZV90aXRsZSI6Ilx1NjU4N1x1NTE3N1x1NzUyOFx1NTRjMVx1NWU5NyJ9LCJDMTA0ODUxNzU2NSI6eyJ0aHVtYiI6Imh0dHBzOlwvXC9mdXNzMTAuZWxlbWVjZG4uY29tXC80XC9lOFwvMzlkYmE0YWViZWEwYjQ2MDU2ZDAyYmNkZTQwOTNqcGVnLmpwZWciLCJkaXNjb3VudCI6MTAsImdvb2RzX2lkIjoiOTczNiIsImJhcmdhaW5faWQiOiIzMCIsInRpdGxlIjoiXHU4MWVhXHU3OGU4XHU4YzQ2XHU2ZDQ2IiwiZGlzY291bnRfcHJpY2UiOiIyIiwicHJpY2UiOiIyIiwic2lkIjoiMyIsInN0b3JlX3RpdGxlIjoiXHU4MzM2XHU0ZTBkXHU2MDFkIn19LCJpZCI6ImJhcmdhaW4iLCJkYXRhX251bSI6Nn0sIk0xNTMxOTg1OTY1MjM3Ijp7InBhcmFtcyI6eyJzaG93Y2FydCI6IjEifSwibWF4IjoiMSIsImlkIjoiY2FydCJ9LCJNMTU1MjcxOTM5ODY5MCI6eyJwYXJhbXMiOnsic3RhdHVzIjoiMSIsInNob3dfc2V0dGluZyI6ImludGVydmFsIiwiaW50ZXJ2YWxfdGltZSI6IjYwIiwiZ3VpZGVkYXRhIjoiMSJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kY29sb3IiOiIjMDAwMDAwIn0sIm1heCI6IjEiLCJpZCI6Imd1aWRlIiwiZGF0YSI6W119LCJNMTUzMTk4NTk2MTQwMyI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjAiLCJob3Rnb29kc251bSI6IjMiLCJkYXRhZnJvbSI6IjAiLCJjYXRlZ29yeWlkIjoiMCIsImNhdGVnb3J5dGl0bGUiOiJcdTU1NDZcdTYyMzdcdTUyMDZcdTdjN2IiLCJzaG93Y2hpbGRjYXRlZ29yeSI6IjAifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMTAiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiIsImNoaWxkY2F0ZWdvcnljb2xvciI6IiMzMzMzMzMiLCJjaGlsZGNhdGVnb3J5YWN0aXZlY29sb3IiOiIjZmYyZDRiIn0sImRhdGEiOnsiQzE1MzE5ODU5NjE0MDMiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InR5cGUiOiJkaXNjb3VudCIsInRpdGxlIjoiXHU2ZWUxMzVcdTUxY2YxMjtcdTZlZTE2MFx1NTFjZjIwIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InR5cGUiOiJjb3Vwb25Db2xsZWN0IiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvd3hhcHBcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTMxOTg1OTYxNDA0Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvd3hhcHBcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0yLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNDUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvd3hhcHBcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifX19LCJDMTUzMTk4NTk2MTQwNSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjU1IiwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJtYXgiOiIxIiwiaXNib3R0b20iOiIxIiwicHJpb3JpdHkiOiIxIiwiaWQiOiJ3YWltYWlfYWxsc3RvcmVzIn0sIk0xNTYwNDk3NjU0NDgyIjp7InBhcmFtcyI6eyJjb250ZW50IjoiXHU4YmY3XHU1ODZiXHU1MTk5XHU3MjQ4XHU2NzQzXHU4YmY0XHU2NjBlIiwiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9jb3B5cmlnaHQucG5nIiwiZGF0YWZyb20iOiIxIiwiY29uZmlnIjoiPHAgc3R5bGU9XCJ0ZXh0LWFsaWduOiBjZW50ZXI7XCI+XHU1NTY2XHU1NTY2XHU1OTE2XHU1MzU2XHU3MjQ4XHU2NzQzXHU2MjQwXHU2NzA5PFwvcD48cD48c3BhbiBzdHlsZT1cImNvbG9yOiByZ2IoMjQyLCAyNDIsIDI0Mik7IGJhY2tncm91bmQtY29sb3I6IHJnYigyNTUsIDAsIDApO1wiPjxiclwvPjxcL3NwYW4+PFwvcD4ifSwic3R5bGUiOnsic2hvd2ltZyI6IjEiLCJzdHlsZSI6IjEiLCJjb2xvciI6IiNDRUNFQ0UiLCJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiJ9LCJtYXgiOiIxIiwiaXNib3R0b20iOiIxIiwicHJpb3JpdHkiOiIyIiwiaWQiOiJjb3B5cmlnaHQifX19", "updatetime" => 1531985983, "version" => 2), "wxapphome" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "1", "data" => "eyJwYWdlIjp7InR5cGUiOiIxIiwidGl0bGUiOiJcdTU1NjZcdTU1NjZcdTU5MTZcdTUzNTZcdTZmMTRcdTc5M2FcdTdhZDkiLCJuYW1lIjoiamRraiIsImRlc2MiOiJcdTUwNjVcdTVlYjdcdTc2ODRcdTUwNjVcdTVlYjdcdTc2ODRcdTVjMzFcdTc3MGJcdTg5YzlcdTVmOTciLCJrZXl3b3JkIjoiIiwiYmFja2dyb3VuZCI6IiNGM0YzRjMiLCJkaXlnb3RvcCI6IjAiLCJuYXZpZ2F0aW9uYmFja2dyb3VuZCI6IiMwMDAwMDAiLCJuYXZpZ2F0aW9udGV4dGNvbG9yIjoiI2ZmZmZmZiIsInRodW1iIjoiaW1hZ2VzXC8xXC8yMDE4XC8wN1wvZENUQ1JLUlFLc1VUUjdxVXU3Z3dTRnJkZER1QzdGLmpwZyIsImRpeW1lbnUiOiItMSJ9LCJpdGVtcyI6eyJNMTUzMjUwNjQ1NDM5OSI6eyJwYXJhbXMiOnsibG9jYXRpb24iOiJcdTViOWFcdTRmNGQiLCJ0ZXh0IjoiXHU4YmY3XHU4ZjkzXHU1MTY1XHU1NTQ2XHU2MjM3XHU2MjE2XHU1NTQ2XHU1NGMxXHU1NDBkXHU3OWYwIiwibGluayI6IlwvcGFnZXNcL2hvbWVcL3NlYXJjaCJ9LCJzdHlsZSI6eyJsb2NzdHlsZSI6InJhZGl1cyIsInNlYXJjaHN0eWxlIjoicmFkaXVzIiwiZml4ZWRiYWNrZ3JvdW5kIjoiI2ZmMmI0ZCIsImxvY2JhY2tncm91bmQiOiIjOTk5OTk5Iiwic2VhcmNoYmFja2dyb3VuZCI6IiNmNGY0ZjQiLCJsb2Njb2xvciI6IiNmZmZmZmYiLCJzZWFyY2hjb2xvciI6IiM2NTY1NjUifSwibWF4IjoiMSIsImlzdG9wIjoiMSIsImlkIjoiZml4ZWRzZWFyY2gifSwiTTE1MzMwMzE1ODAzMTAiOnsicGFyYW1zIjp7InBpY3R1cmVkYXRhIjoiMSJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTAiLCJwYWRkaW5nbGVmdCI6IjEwIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIiLCJiYWNrZ3JvdW5kIjoiI2ZhZmFmYSJ9LCJkYXRhIjp7IkMxMjUyNDIzOTkxIjp7Imxpbmt1cmwiOiJodHRwczpcL1wvd3d3LmFpc2t6LmNjXC9hcHBcL2luZGV4LnBocD9pPTYmYz1lbnRyeSZtPWV3ZWlfc2hvcHYyJmRvPW1vYmlsZSZyPW1lcmNoJm1lcmNoaWQ9MTg5IiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOVwvMDZcL29tM214N1BXWGUxWGV3N1ZPdzg4TXBXN0V3VVY2Ry5qcGcifSwiQzEwNzI1ODIzNzgiOnsibGlua3VybCI6IndlYnZpZXc6aHR0cHM6XC9cL3d3dy5haXNrei5jY1wvYXBwXC9pbmRleC5waHA/aT02JmM9ZW50cnkmbT1ld2VpX3Nob3B2MiZkbz1tb2JpbGUmcj1tZXJjaCZtZXJjaGlkPTE0MCIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9hb0tPNUU1cG8zZTNrNmtPek8wRzdvVTMzY2RFb2cuanBnIn0sIkMxMDE4NDMzNDIxIjp7Imxpbmt1cmwiOiJodHRwczpcL1wvd3d3LmFpc2t6LmNjXC9hcHBcL2luZGV4LnBocD9pPTYmYz1lbnRyeSZtPWV3ZWlfc2hvcHYyJmRvPW1vYmlsZSZyPW1lcmNoJm1lcmNoaWQ9MTkxIiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOVwvMDZcL25yMVZ0UDlwcHY4UFE4MWI1VHBaQlRRUTY5dFRSMS5qcGcifX0sImlkIjoicGljdHVyZSJ9LCJNMTUzMjU3Nzg5MzcyOCI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIxIiwic2hvd2RvdCI6IjEiLCJyb3dudW0iOiI1IiwicGFnZW51bSI6IjEwIiwibmF2c2RhdGEiOiIxIiwibmF2c251bSI6IjUwIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJuYXZzdHlsZSI6IiIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIn0sImRhdGEiOnsiQzEzNjMyODczMTYiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MSIsInRleHQiOiJcdTcwZWRcdTUzNTZcdTdmOGVcdTk4ZGYiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL3UwMGg2MjZQNkgxODkxTHNQeXk5NlB4SHphWVRoeC5wbmcifSwiQzEwNDA1MzM1MDMiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MiIsInRleHQiOiJcdTg0MjVcdTUxN2JcdTY1ZTlcdTk5MTAiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL3VVNjZLblJmNm5nNnY2TXY2Q1ZLbjdSbGxhclZyai5wbmcifSwiQzEzNzU4NzY2MDUiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9OCIsInRleHQiOiJcdTljOWNcdTgyYjFcdTdjZDVcdTcwYjkiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL1lrZnBCV3c1eWlQanhKUnF6cmpEaWpnendCcWRlay5wbmcifSwiQzExOTUzODQyNTMiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NiIsInRleHQiOiJcdTdjYmVcdTU0YzFcdTVjMGZcdTU0MDMiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL0o0OGk0SUtLcEo4NzhKRUZ6M3JSOGZ6WGZaN2l4ei5wbmcifSwiQzExOTg0NTAyMzMiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NCIsInRleHQiOiJcdTRlMmRcdTg5N2ZcdTVmZWJcdTk5MTAiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL2Zia05TdnpzMHo2NVY1QTU3ZFcwNmt5NGF3bno3ZC5wbmcifSwiQzEyNDQzMTQ3ODciOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NyIsInRleHQiOiJcdTdmOGVcdTU0NzNcdTU5MWNcdTViYjUiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL20xMzVjZ2tsOG44dGdvVEtPS0c4VzMxMTM4NVJTbi5wbmcifSwiQzEwNjAwNjgxNzUiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MyIsInRleHQiOiJcdTc1MWNcdTcwYjlcdTk5NmVcdTU0YzEiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL3RTRHZOY2NoOUFTajl2dUpoWkpNcDk4Y25QOE52SC5wbmcifSwiQzExNDA4NzIzODQiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9MTUiLCJ0ZXh0IjoiXHU3NTFmXHU5YzljIiwiaW1ndXJsIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTdcLzAxXC9KZ1RBNWJHdGcyazFDYWlUR3c1OTFHNW9NZ21jNEMucG5nIn0sIkMxMjI4MjM0MDM2Ijp7Imxpbmt1cmwiOiJnb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9pbmRleCIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTk5OTZcdTk4NzUiLCJpbWd1cmwiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMDFcL1M2Tzg2dE1PaTE4a0tVUFA4dW5UdHQ4dFJ1NDYzMS5wbmcifSwiQzEzMzgzNzE1MzgiOnsibGlua3VybCI6InBhZ2VzXC9ob21lXC9jYXRlZ29yeT9jaWQ9NDgiLCJ0ZXh0IjoiXHU1MjllXHU1MTZjXHU2NTg3XHU1MTc3IiwiaW1ndXJsIjoiIn19LCJpZCI6Im5hdnMiLCJkYXRhX251bSI6MTAsInJvdyI6Mn0sIk0xNTM2NzQxNDcxMjg1Ijp7InBhcmFtcyI6eyJzaG93cmVkcGFja2V0IjoiMSJ9LCJtYXgiOiIxIiwiaWQiOiJyZWRwYWNrZXQifSwiTTE1MzI2MDI1NTI4MzUiOnsicGFyYW1zIjp7Im5vdGljZWRhdGEiOiIwIiwibm90aWNlbnVtIjoiMTAiLCJzcGVlZCI6IjMiLCJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3N0YXRpY1wvaW1nXC9oZWFkX2xpbmUucG5nIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIwIiwidGV4dGNvbG9yIjoiIzY2NjY2NiIsImljb25jb2xvciI6IiNmZDU0NTQiLCJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiJ9LCJkYXRhIjp7IkMxMDg2MjQ3ODg3Ijp7ImlkIjoiMiIsInRpdGxlIjoiXHU1OTgyXHU5MDQ3XHU5NWVlXHU5ODk4XHU4YmY3XHU4MDU0XHU3Y2ZiXHVmZjFhMDIzLTUyMTE0OTg4IiwibGlua3VybCI6IiJ9LCJDMTA0NzExNTk3NCI6eyJpZCI6IjMiLCJ0aXRsZSI6Ilx1NTk4Mlx1OTA0N1x1OTVlZVx1OTg5OFx1OGJmN1x1ODA1NFx1N2NmYlx1ZmYxYTAyMy01MjExNDk4OCIsImxpbmt1cmwiOiIifX0sImlkIjoibm90aWNlIn0sIk0xNTMyNjgwODkwNDQwIjp7InN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmZmZmZmIiwibWFyZ2luVG9wIjoiMTAiLCJtYXJnaW5Cb3R0b20iOiIwIn0sInBhcmFtcyI6eyJhY3Rpdml0eWRhdGEiOiIxIn0sImRhdGEiOnsiQzEwODEwMTY5NTEiOnsibGlua3VybCI6Imh0dHBzOlwvXC93d3cuYWlza3ouY2NcL2FkZG9uc1wvd2U3X3dtYWxsXC90ZW1wbGF0ZVwvdnVlXC9pbmRleC5odG1sP21lbnU9I1wvcGFnZXNcL3N0b3JlXC9nb29kcz9zaWQ9MzcmaT02IiwidGV4dCI6Ilx1NzIzMVx1NGUwYVx1OGQ4NVx1NWUwMiIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9FS0VKY3hlMnNqUEZKR3AyZnBKQ3ZwZlBQZUpnQ3MuanBnIiwicGxhY2Vob2xkZXIiOiIyNFx1NWMwZlx1NjVmNlx1NTE2OFx1NTdjZVx1OTE0ZFx1OTAwMSIsImNvbG9yIjoiI2ZmMmQ0YiIsInBsYWNlaG9sZGVyQ29sb3IiOiIjN2I3YjdiIn0sIkMxMDAyMzQwMTU1Ijp7Imxpbmt1cmwiOiJodHRwczpcL1wvd3d3LmFpc2t6LmNjXC9hcHBcL2luZGV4LnBocD9pPTYmYz1lbnRyeSZtPWV3ZWlfc2hvcHYyJmRvPW1vYmlsZSZyPW1lcmNoJm1lcmNoaWQ9MTQwIiwidGV4dCI6Ilx1NWJiNlx1NjUzZlx1NjcwZFx1NTJhMSIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9uVGg0UnQ0U0Q0TFhRYmxvZUhESFJRRjB0c0dET3guanBnIiwicGxhY2Vob2xkZXIiOiJcdThiYTlcdTc1MWZcdTZkM2JcdTY2ZjRcdTdmOGVcdTU5N2QiLCJjb2xvciI6IiNmZjJkNGIiLCJwbGFjZWhvbGRlckNvbG9yIjoiIzdiN2I3YiJ9LCJDMTM5MDM0MzAyMyI6eyJsaW5rdXJsIjoiaHR0cHM6XC9cL3d3dy5haXNrei5jY1wvYXBwXC9pbmRleC5waHA/aT02JmM9ZW50cnkmbT1ld2VpX3Nob3B2MiZkbz1tb2JpbGUmcj1jcmVkaXRzaG9wIiwidGV4dCI6Ilx1NzllZlx1NTIwNlx1NTU0Nlx1NTdjZSIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMTlcLzA2XC9EbDVIQ2QxY05YSzJaMnhybVpVVW44NnU4TTdoOFouanBnIiwicGxhY2Vob2xkZXIiOiJcdTc1MjhcdTYyMzdcdThkODVcdTUwM2NcdTU2ZGVcdTk5ODgiLCJjb2xvciI6IiNmZjJkNGIiLCJwbGFjZWhvbGRlckNvbG9yIjoiIzdiN2I3YiJ9LCJDMTMxMTM2NTg4NyI6eyJsaW5rdXJsIjoiaHR0cHM6XC9cL3d3dy5haXNrei5jY1wvYXBwXC9pbmRleC5waHA/aT02JmM9ZW50cnkmbT1ld2VpX3Nob3B2MiZkbz1tb2JpbGUmcj1tZXJjaCZtZXJjaGlkPTE4OSIsInRleHQiOiJcdTViYjZcdTc1MzVcdTZlMDVcdTZkMTciLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE5XC8wNlwvTTJpbzJubzhPd2h2aGRXN25ITzd4UFdESW91ZFY3LmpwZyIsInBsYWNlaG9sZGVyIjoiXHU1NTJlXHU1NDBlXHU2NzBkXHU1MmExXHU0ZmRkXHU5NjljIiwiY29sb3IiOiIjZmYyZDRiIiwicGxhY2Vob2xkZXJDb2xvciI6IiM3YjdiN2IifX0sImlkIjoiYWN0aXZpdHkifSwiTTE1MzQxNDExNjczMDEiOnsicGFyYW1zIjp7InNob3d0eXBlIjoiMCIsInBhZ2VudW0iOiIzIiwic3RvcmVkYXRhIjoiMSIsInN0b3JlbnVtIjoiNiIsInRpdGxlIjoiXHU0ZTNhXHU2MGE4XHU0ZjE4XHU5MDA5In0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwidGl0bGVjb2xvciI6IiMzMzMzMzMiLCJzdG9yZWNvbG9yIjoiIzMzMzMzMyIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIiwic2hvd2RvdCI6IjAifSwiZGF0YSI6eyJDMTAwMDI3NTMxMiI6eyJpZCI6IjMiLCJ0aXRsZSI6Ilx1ODMzNlx1NGUwZFx1NjAxZCIsImxvZ28iOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8xMVwva3Z5T29JeXRJZWEyU3liVDBFVnNMTDNZaTkyYnYwLmpwZyIsImZvcndhcmRfbW9kZSI6IjEiLCJmb3J3YXJkX3VybCI6IiIsInVybCI6IlwvcGFnZXNcL3N0b3JlXC9ob21lP3NpZD0zIiwic3RvcmVfaWQiOiIzIn0sIkMxMzUxMDg1OTY4Ijp7ImlkIjoiODMiLCJ0aXRsZSI6Ilx1OTc2Mlx1OTk4NiIsImxvZ28iOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxNlwvMDhcL1hsRHpjTkQzN3hDMU1BZFhEQzYwWlhkRENMWGRpOS5wbmciLCJmb3J3YXJkX21vZGUiOiIwIiwiZm9yd2FyZF91cmwiOiIiLCJ1cmwiOiJcL3BhZ2VzXC9zdG9yZVwvZ29vZHM/c2lkPTgzIiwic3RvcmVfaWQiOiI4MyJ9LCJDMTI5MzE4Mjc1MCI6eyJpZCI6IjU1NyIsInRpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NTI4XHU1NGMxXHU1ZTk3IiwibG9nbyI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzEyXC9jbGhKMjI4Ykw2VW0wYjhMMkIwMjdNbXU4TDJMS2ouanBnIiwiZm9yd2FyZF9tb2RlIjoiMCIsImZvcndhcmRfdXJsIjoiIiwidXJsIjoiXC9wYWdlc1wvc3RvcmVcL2dvb2RzP3NpZD01NTciLCJzdG9yZV9pZCI6IjU1NyJ9LCJDMTMwNTcwOTQ2OCI6eyJpZCI6IjY0IiwidGl0bGUiOiJcdTZjMTFcdTc5OGZcdTkxNTJcdTY5N2MiLCJsb2dvIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTZcLzA4XC9ORGZvaXpFekIyS0ZGSzU3S0tmaU9NS0tiNzJFNzUucG5nIiwiZm9yd2FyZF9tb2RlIjoiMCIsImZvcndhcmRfdXJsIjoiIiwidXJsIjoiXC9wYWdlc1wvc3RvcmVcL2dvb2RzP3NpZD02NCIsInN0b3JlX2lkIjoiNjQifSwiQzEwMjQ0NTI3NTIiOnsiaWQiOiI2OCIsInRpdGxlIjoiXHU5MGVkXHU4YmIwXHU1OTFhXHU1NDczXHU5OTEwXHU5OTg2IiwibG9nbyI6Imh0dHA6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTZcLzA4XC9ENnVXelhXd1hUR1U1OXc5N3Q3M1FHMzk5OTUzVHguanBnIiwiZm9yd2FyZF9tb2RlIjoiMCIsImZvcndhcmRfdXJsIjoiIiwidXJsIjoiXC9wYWdlc1wvc3RvcmVcL2dvb2RzP3NpZD02OCIsInN0b3JlX2lkIjoiNjgifSwiQzEyNDQxMTQ1NjAiOnsiaWQiOiI2OSIsInRpdGxlIjoiXHU2ODQzXHU2ZTkwXHU5OGRmXHU1ZTk3IiwibG9nbyI6Imh0dHBzOlwvXC8xLnhpbnp1b3dsLmNvbVwvYXR0YWNobWVudFwvaW1hZ2VzXC8xXC8yMDE2XC8wOFwvS043ZVU2c25hRjllNjZDNW41NHBuakc1NjR6Nzk1LmpwZyIsImZvcndhcmRfbW9kZSI6IjAiLCJmb3J3YXJkX3VybCI6IiIsInVybCI6IlwvcGFnZXNcL3N0b3JlXC9nb29kcz9zaWQ9NjkiLCJzdG9yZV9pZCI6IjY5In19LCJpZCI6InNlbGVjdGl2ZSJ9LCJNMTUzMjY4MDg1MDAwMSI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIwIiwicGFnZW51bSI6IjQiLCJ0aXRsZSI6Ilx1NTkyOVx1NTkyOVx1NzI3OVx1NGVmNyIsImJhcmdhaW5udW0iOiI4In0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIiLCJ0aXRsZWNvbG9yIjoiIzMzMzMzMyIsImdvb2RzbmFtZWNvbG9yIjoiIzMzMzMzMyIsInNob3dkb3QiOiIxIn0sImRhdGEiOnsiQzExNDA3NTk3NTYiOnsidGh1bWIiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMTJcL3BaVDhOdTVYd1c4NTI5dTVOeHFXaWlVWGJ3MjVCTi5qcGciLCJkaXNjb3VudCI6MTAsImdvb2RzX2lkIjoiOTgxOSIsImJhcmdhaW5faWQiOiIxOSIsInRpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NmQyIiwiZGlzY291bnRfcHJpY2UiOiI2LjUiLCJwcmljZSI6IjYuNSIsInNpZCI6IjU1NyIsInN0b3JlX3RpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NTI4XHU1NGMxXHU1ZTk3In0sIkMxMDE3MzMyMTcxIjp7InRodW1iIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTdcLzEyXC9yQ242ZTU1NXVuZHU2VTAwZmNSNWZNREdOMkNNR2suanBnIiwiZGlzY291bnQiOjEwLCJnb29kc19pZCI6Ijk4MTgiLCJiYXJnYWluX2lkIjoiMTkiLCJ0aXRsZSI6Ilx1NThhOFx1NmMzNCIsImRpc2NvdW50X3ByaWNlIjoiNSIsInByaWNlIjoiNSIsInNpZCI6IjU1NyIsInN0b3JlX3RpdGxlIjoiXHU2NTg3XHU1MTc3XHU3NTI4XHU1NGMxXHU1ZTk3In0sIkMxMzk3MDI1NjA4Ijp7InRodW1iIjoiaHR0cHM6XC9cLzEueGluenVvd2wuY29tXC9hdHRhY2htZW50XC9pbWFnZXNcLzFcLzIwMTdcLzEyXC95enozZmRJRFhJbHFmWFhoZGJwbG1FdmJieHZmZEguanBnIiwiZGlzY291bnQiOjEwLCJnb29kc19pZCI6Ijk4MTciLCJiYXJnYWluX2lkIjoiMTkiLCJ0aXRsZSI6Ilx1OTRhMlx1N2IxNCIsImRpc2NvdW50X3ByaWNlIjoiMjMiLCJwcmljZSI6IjIzIiwic2lkIjoiNTU3Iiwic3RvcmVfdGl0bGUiOiJcdTY1ODdcdTUxNzdcdTc1MjhcdTU0YzFcdTVlOTcifSwiQzEzODYyMjU4NTEiOnsidGh1bWIiOiJodHRwczpcL1wvMS54aW56dW93bC5jb21cL2F0dGFjaG1lbnRcL2ltYWdlc1wvMVwvMjAxN1wvMTJcL205SkoxYkUwYlc2aGp6MTY2OUV6OWE2MUFBckhoaC5qcGciLCJkaXNjb3VudCI6MTAsImdvb2RzX2lkIjoiOTgxNSIsImJhcmdhaW5faWQiOiIxOSIsInRpdGxlIjoiXHU3YjE0XHU4YmIwXHU2NzJjIiwiZGlzY291bnRfcHJpY2UiOiIxMCIsInByaWNlIjoiMTAiLCJzaWQiOiI1NTciLCJzdG9yZV90aXRsZSI6Ilx1NjU4N1x1NTE3N1x1NzUyOFx1NTRjMVx1NWU5NyJ9LCJDMTA3NDAzNDAyNSI6eyJ0aHVtYiI6Imh0dHBzOlwvXC8xLnhpbnp1b3dsLmNvbVwvYXR0YWNobWVudFwvaW1hZ2VzXC8xXC8yMDE3XC8xMlwvYjc3azIyN3lLMkJpcjVCUFY3TG9CbHo3NTdZN3k5LmpwZyIsImRpc2NvdW50IjoxMCwiZ29vZHNfaWQiOiI5ODE2IiwiYmFyZ2Fpbl9pZCI6IjE5IiwidGl0bGUiOiJcdTVjM2FcdTViNTAiLCJkaXNjb3VudF9wcmljZSI6IjMiLCJwcmljZSI6IjMiLCJzaWQiOiI1NTciLCJzdG9yZV90aXRsZSI6Ilx1NjU4N1x1NTE3N1x1NzUyOFx1NTRjMVx1NWU5NyJ9LCJDMTA0ODUxNzU2NSI6eyJ0aHVtYiI6Imh0dHBzOlwvXC9mdXNzMTAuZWxlbWVjZG4uY29tXC80XC9lOFwvMzlkYmE0YWViZWEwYjQ2MDU2ZDAyYmNkZTQwOTNqcGVnLmpwZWciLCJkaXNjb3VudCI6MTAsImdvb2RzX2lkIjoiOTczNiIsImJhcmdhaW5faWQiOiIzMCIsInRpdGxlIjoiXHU4MWVhXHU3OGU4XHU4YzQ2XHU2ZDQ2IiwiZGlzY291bnRfcHJpY2UiOiIyIiwicHJpY2UiOiIyIiwic2lkIjoiMyIsInN0b3JlX3RpdGxlIjoiXHU4MzM2XHU0ZTBkXHU2MDFkIn19LCJpZCI6ImJhcmdhaW4iLCJkYXRhX251bSI6Nn0sIk0xNTMxOTg1OTY1MjM3Ijp7InBhcmFtcyI6eyJzaG93Y2FydCI6IjEifSwibWF4IjoiMSIsImlkIjoiY2FydCJ9LCJNMTU1MjcxOTM5ODY5MCI6eyJwYXJhbXMiOnsic3RhdHVzIjoiMSIsInNob3dfc2V0dGluZyI6ImludGVydmFsIiwiaW50ZXJ2YWxfdGltZSI6IjYwIiwiZ3VpZGVkYXRhIjoiMSJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kY29sb3IiOiIjMDAwMDAwIn0sIm1heCI6IjEiLCJpZCI6Imd1aWRlIiwiZGF0YSI6W119LCJNMTUzMTk4NTk2MTQwMyI6eyJwYXJhbXMiOnsic2hvd2Rpc2NvdW50IjoiMSIsInNob3dob3Rnb29kcyI6IjAiLCJob3Rnb29kc251bSI6IjMiLCJkYXRhZnJvbSI6IjAiLCJjYXRlZ29yeWlkIjoiMCIsImNhdGVnb3J5dGl0bGUiOiJcdTU1NDZcdTYyMzdcdTUyMDZcdTdjN2IiLCJzaG93Y2hpbGRjYXRlZ29yeSI6IjAifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMTAiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInNjb3JlY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWJnY29sb3IiOiIjZmYyZDRiIiwiZGVsaXZlcnl0aXRsZWNvbG9yIjoiI2ZmZiIsImNoaWxkY2F0ZWdvcnljb2xvciI6IiMzMzMzMzMiLCJjaGlsZGNhdGVnb3J5YWN0aXZlY29sb3IiOiIjZmYyZDRiIn0sImRhdGEiOnsiQzE1MzE5ODU5NjE0MDMiOnsic3RvcmVfaWQiOiIwIiwibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJzY29yZSI6IjUiLCJzYWlsZWQiOiI4ODgiLCJzZW5kX3ByaWNlIjoiMTUiLCJkZWxpdmVyeV9wcmljZSI6IjUiLCJkZWxpdmVyeV90aXRsZSI6Ilx1NWU3M1x1NTNmMFx1NGUxM1x1OTAwMSIsImRlbGl2ZXJ5X3RpbWUiOiIzMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InR5cGUiOiJkaXNjb3VudCIsInRpdGxlIjoiXHU2ZWUxMzVcdTUxY2YxMjtcdTZlZTE2MFx1NTFjZjIwIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InR5cGUiOiJjb3Vwb25Db2xsZWN0IiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn0sImhvdF9nb29kcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvd3hhcHBcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDMiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX0sIkMxNTMxOTg1OTYxNDA0Ijp7InN0b3JlX2lkIjoiMCIsImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvd3hhcHBcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0yLmpwZyIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwic2NvcmUiOiI1Iiwic2FpbGVkIjoiODg4Iiwic2VuZF9wcmljZSI6IjE1IiwiZGVsaXZlcnlfcHJpY2UiOiI1IiwiZGVsaXZlcnlfdGl0bGUiOiJcdTVlNzNcdTUzZjBcdTRlMTNcdTkwMDEiLCJkZWxpdmVyeV90aW1lIjoiNDUiLCJob3RfZ29vZHMiOnsiQzAxMjM0NTY3ODkxMDEiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvd3hhcHBcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMjAuMDAiLCJvbGRfcHJpY2UiOiIxMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4Iiwic3RvcmVfdGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJkaXNjb3VudCI6IjUiLCJzYWlsZWQiOiIyMCIsImNvbW1lbnRfZ29vZF9wZXJjZW50IjoiODglIn0sIkMwMTIzNDU2Nzg5MTAzIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifX19LCJDMTUzMTk4NTk2MTQwNSI6eyJzdG9yZV9pZCI6IjAiLCJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsInNjb3JlIjoiNSIsInNhaWxlZCI6Ijg4OCIsInNlbmRfcHJpY2UiOiIxNSIsImRlbGl2ZXJ5X3ByaWNlIjoiNSIsImRlbGl2ZXJ5X3RpdGxlIjoiXHU1ZTczXHU1M2YwXHU0ZTEzXHU5MDAxIiwiZGVsaXZlcnlfdGltZSI6IjU1IiwiaG90X2dvb2RzIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC93eGFwcFwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIyMC4wMCIsIm9sZF9wcmljZSI6IjEwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJzdG9yZV90aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImRpc2NvdW50IjoiNSIsInNhaWxlZCI6IjIwIiwiY29tbWVudF9nb29kX3BlcmNlbnQiOiI4OCUifSwiQzAxMjM0NTY3ODkxMDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL3d4YXBwXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNS5qcGciLCJwcmljZSI6IjIwLjAwIiwib2xkX3ByaWNlIjoiMTAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsInN0b3JlX3RpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiZGlzY291bnQiOiI1Iiwic2FpbGVkIjoiMjAiLCJjb21tZW50X2dvb2RfcGVyY2VudCI6Ijg4JSJ9fX19LCJtYXgiOiIxIiwiaXNib3R0b20iOiIxIiwicHJpb3JpdHkiOiIxIiwiaWQiOiJ3YWltYWlfYWxsc3RvcmVzIn0sIk0xNTYwNDk3NjU0NDgyIjp7InBhcmFtcyI6eyJjb250ZW50IjoiXHU4YmY3XHU1ODZiXHU1MTk5XHU3MjQ4XHU2NzQzXHU4YmY0XHU2NjBlIiwiaW1ndXJsIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9jb3B5cmlnaHQucG5nIiwiZGF0YWZyb20iOiIxIiwiY29uZmlnIjoiPHAgc3R5bGU9XCJ0ZXh0LWFsaWduOiBjZW50ZXI7XCI+XHU1NTY2XHU1NTY2XHU1OTE2XHU1MzU2XHU3MjQ4XHU2NzQzXHU2MjQwXHU2NzA5PFwvcD48cD48c3BhbiBzdHlsZT1cImNvbG9yOiByZ2IoMjQyLCAyNDIsIDI0Mik7IGJhY2tncm91bmQtY29sb3I6IHJnYigyNTUsIDAsIDApO1wiPjxiclwvPjxcL3NwYW4+PFwvcD4ifSwic3R5bGUiOnsic2hvd2ltZyI6IjEiLCJzdHlsZSI6IjEiLCJjb2xvciI6IiNDRUNFQ0UiLCJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiJ9LCJtYXgiOiIxIiwiaXNib3R0b20iOiIxIiwicHJpb3JpdHkiOiIyIiwiaWQiOiJjb3B5cmlnaHQifX19", "updatetime" => 1531985983, "version" => 2), "vuegohome" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "1", "data" => "eyJwYWdlIjp7InR5cGUiOiI0IiwidGl0bGUiOiJcdTc1MWZcdTZkM2JcdTU3MDgiLCJuYW1lIjoiXHU3NTFmXHU2ZDNiXHU1NzA4XHU5OTk2XHU5ODc1IiwidGh1bWIiOiIiLCJkZXNjIjoiXHU3NTFmXHU2ZDNiXHU1NzA4Iiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjRjNGM0YzIiwiZGl5Z290b3AiOiIwIiwibmF2aWdhdGlvbmJhY2tncm91bmQiOiIjMDAwMDAwIiwibmF2aWdhdGlvbnRleHRjb2xvciI6IiNmZmZmZmYiLCJkaXltZW51IjoiMCIsImZvbGxvd2JhciI6IjEifSwiaXRlbXMiOnsiTTE1NDQxNjMzMTU4ODYiOnsicGFyYW1zIjp7ImxvY2F0aW9uIjoiXHU1YjlhXHU0ZjRkIiwidGV4dCI6Ilx1OGJmN1x1OGY5M1x1NTE2NVx1NGZlMVx1NjA2Zlx1NTE4NVx1NWJiOSIsImxpbmsiOiJcL2dvaG9tZVwvcGFnZXNcL3RvbmdjaGVuZ1wvc2VhcmNoIiwibGlua3RvIjoiMSJ9LCJzdHlsZSI6eyJsb2NzdHlsZSI6InJhZGl1cyIsInNlYXJjaHN0eWxlIjoicmFkaXVzIiwiZml4ZWRiYWNrZ3JvdW5kIjoiI2ZmMmQ0YiIsImxvY2JhY2tncm91bmQiOiIjOTk5OTk5Iiwic2VhcmNoYmFja2dyb3VuZCI6IiNmNGY0ZjQiLCJsb2Njb2xvciI6IiNmZmZmZmYiLCJzZWFyY2hjb2xvciI6IiM2NTY1NjUifSwibWF4IjoiMSIsImlzdG9wIjoiMSIsImlkIjoiZml4ZWRzZWFyY2gifSwiTTE1NDQxNjkwMDc0MjYiOnsicGFyYW1zIjp7InBpY3R1cmVkYXRhIjoiMiIsImhhc19nb2hvbWUiOiJ0cnVlIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIxMCIsInBhZGRpbmdsZWZ0IjoiMTAiLCJkb3RiYWNrZ3JvdW5kIjoiI2ZmMmQ0YiIsImJhY2tncm91bmQiOiIjZmFmYWZhIn0sImRhdGEiOnsiQzExMzMzMDEyOTEiOnsibGlua3VybCI6bnVsbCwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL2RDVENSS1JRS3NVVFI3cVV1N2d3U0ZyZGREdUM3Ri5qcGcifX0sImlkIjoicGljdHVyZSJ9LCJNMTU0NDE2OTEyMzI1OSI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIxIiwic2hvd2RvdCI6IjEiLCJyb3dudW0iOiI1IiwicGFnZW51bSI6IjEwIiwibmF2c2RhdGEiOiIyIiwibmF2c251bSI6IjQ3IiwiaGFzX2dvaG9tZSI6InRydWUifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMCIsIm5hdnN0eWxlIjoiY2lyY2xlIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIifSwiZGF0YSI6eyJDMTM1NzY2ODQzOSI6eyJsaW5rdXJsIjoiIiwidGV4dCI6Ilx1NzgwZFx1NGVmNyIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzA3XC9kQ1RDUktSUUtzVVRSN3FVdTdnd1NGcmRkRHVDN0YuanBnIn19LCJpZCI6Im5hdnMiLCJkYXRhX251bSI6MSwicm93IjoyfSwiTTE1NDQxNjg4Nzc0MTgiOnsicGFyYW1zIjp7Im5vdGljZWRhdGEiOiIyIiwibm90aWNlbnVtIjoiMTAiLCJzcGVlZCI6IjIiLCJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3N0YXRpY1wvaW1nXC9oZWFkX2xpbmVfdG9uZ2NoZW5nLnBuZyIsImhhc19nb2hvbWUiOiJ0cnVlIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIwIiwidGV4dGNvbG9yIjoiIzY2NjY2NiIsImljb25jb2xvciI6IiNmZDU0NTQiLCJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiJ9LCJkYXRhIjp7IkMxNDA4Mzg4NTA0Ijp7ImlkIjoiMyIsInRpdGxlIjoiXHU3NTFmXHU2ZDNiXHU1NzA4XHU0ZTBhXHU3ZWJmXHU1NWJkIiwibGlua3VybCI6IiJ9fSwiaWQiOiJub3RpY2UifSwiTTE1NDQxNjM0MDczMTkiOnsicGFyYW1zIjp7InR5cGUiOiJrYW5qaWEiLCJoZWFkbGluZSI6Ilx1NzVhZlx1NzJjMlx1NzgwZFx1NGVmNyIsImhlYWRsaW5lX3Nob3ciOiIxIiwibW9yZSI6Ilx1NjZmNFx1NTkxYSIsIm1vcmVfbGluayI6IlwvZ29ob21lXC9wYWdlc1wva2FuamlhXC9pbmRleCIsInNob3dvbGRwcmljZSI6IjEiLCJidXlidG50ZXh0IjoiXHU1M2JiXHU2MmZjXHU1NmUyIiwiYnV5YnRudGV4dF9rYW5qaWEiOiJcdTUzYmJcdTc4MGRcdTRlZjciLCJidXlidG50ZXh0X3BpbnR1YW4iOiJcdTUzYmJcdTYyZmNcdTU2ZTIiLCJidXlidG50ZXh0X3NlY2tpbGwiOiJcdTdhY2JcdTUzNzNcdTYyYTIiLCJnb29kc2RhdGEiOiIxIiwiZ29vZHNudW0iOiIzIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiYmFja2dyb3VuZCI6IiNmNWY1ZjUiLCJtYXJnaW5ib3R0b20iOiIwIiwibGlzdHN0eWxlIjoiMSIsInRpdGxlY29sb3IiOiIjMzMzIiwicHJpY2Vjb2xvciI6IiNmZjJkNGIiLCJvbGRwcmljZWNvbG9yIjoiIzk5OSIsImJ1eWJ0bmNvbG9yIjoiI2ZmZiIsImJ1eWJ0bmJhY2tncm91bmQiOiIiLCJiYXJiYWNrZ3JvdW5kIjoiI0ZFRDRENSIsImJhcmlubmVyYmFja2dyb3VuZCI6IiIsImJhcnRleHRjb2xvciI6IiNmZmYiLCJoZWFkbGluZWNvbG9yIjoiIzMzMzMzMyIsIm1vcmVjb2xvciI6IiM5OTk5OTkifSwiZGF0YSI6eyJDMTU0NDE2MzQwNzMyMCI6eyJpZCI6IjAiLCJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19LCJDMTU0NDE2MzQwNzMyMSI6eyJpZCI6IjAiLCJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19LCJDMTU0NDE2MzQwNzMyMiI6eyJpZCI6IjAiLCJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19LCJDMTU0NDE2MzQwNzMyMyI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19fSwiaWQiOiJnb2hvbWVBY3Rpdml0eSJ9LCJNMTU0NDE2OTI1OTU5OSI6eyJwYXJhbXMiOnsidHlwZSI6InBpbnR1YW4iLCJoZWFkbGluZSI6Ilx1OTQ5Y1x1NjBlMFx1NjJmY1x1NTZlMiIsImhlYWRsaW5lX3Nob3ciOiIxIiwibW9yZSI6Ilx1NjZmNFx1NTkxYSIsIm1vcmVfbGluayI6IlwvZ29ob21lXC9wYWdlc1wvcGludHVhblwvaW5kZXgiLCJzaG93b2xkcHJpY2UiOiIxIiwiYnV5YnRudGV4dCI6Ilx1NTNiYlx1NjJmY1x1NTZlMiIsImJ1eWJ0bnRleHRfa2FuamlhIjoiXHU1M2JiXHU3ODBkXHU0ZWY3IiwiYnV5YnRudGV4dF9waW50dWFuIjoiXHU1M2JiXHU2MmZjXHU1NmUyIiwiYnV5YnRudGV4dF9zZWNraWxsIjoiXHU3YWNiXHU1MzczXHU2MmEyIiwiZ29vZHNkYXRhIjoiMSIsImdvb2RzbnVtIjoiNiJ9LCJzdHlsZSI6eyJtYXJnaW50b3AiOiIxMCIsImJhY2tncm91bmQiOiIjZjVmNWY1IiwibWFyZ2luYm90dG9tIjoiMCIsImxpc3RzdHlsZSI6IjQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmYyZDRiIiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmZmYiLCJidXlidG5iYWNrZ3JvdW5kIjoiIiwiYmFyYmFja2dyb3VuZCI6IiNGRUQ0RDUiLCJiYXJpbm5lcmJhY2tncm91bmQiOiIiLCJiYXJ0ZXh0Y29sb3IiOiIjZmZmIiwiaGVhZGxpbmVjb2xvciI6IiMzMzMzMzMiLCJtb3JlY29sb3IiOiIjOTk5OTk5In0sImRhdGEiOnsiQzE1NDQxNjkyNTk1OTkiOnsiaWQiOiIwIiwic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fSwiQzE1NDQxNjkyNTk2MDAiOnsiaWQiOiIwIiwic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fSwiQzE1NDQxNjkyNTk2MDEiOnsiaWQiOiIwIiwic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fSwiQzE1NDQxNjkyNTk2MDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fX0sImlkIjoiZ29ob21lQWN0aXZpdHkifSwiTTE1NDQxNjM2NzkwOTMiOnsicGFyYW1zIjp7InR5cGUiOiJzZWNraWxsIiwiaGVhZGxpbmUiOiJcdTcyMzFcdTRlMGFcdTYyYTJcdThkMmQiLCJoZWFkbGluZV9zaG93IjoiMSIsIm1vcmUiOiJcdTY2ZjRcdTU5MWEiLCJtb3JlX2xpbmsiOiJcL2dvaG9tZVwvcGFnZXNcL3NlY2tpbGxcL2luZGV4Iiwic2hvd29sZHByaWNlIjoiMSIsImJ1eWJ0bnRleHQiOiJcdTUzYmJcdTYyZmNcdTU2ZTIiLCJidXlidG50ZXh0X2thbmppYSI6Ilx1NTNiYlx1NzgwZFx1NGVmNyIsImJ1eWJ0bnRleHRfcGludHVhbiI6Ilx1NTNiYlx1NjJmY1x1NTZlMiIsImJ1eWJ0bnRleHRfc2Vja2lsbCI6Ilx1N2FjYlx1NTM3M1x1NjJhMiIsImdvb2RzZGF0YSI6IjEiLCJnb29kc251bSI6IjQifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMTAiLCJiYWNrZ3JvdW5kIjoiI2Y1ZjVmNSIsIm1hcmdpbmJvdHRvbSI6IjAiLCJsaXN0c3R5bGUiOiIyIiwidGl0bGVjb2xvciI6IiMzMzMiLCJwcmljZWNvbG9yIjoiI2ZmMmQ0YiIsIm9sZHByaWNlY29sb3IiOiIjOTk5IiwiYnV5YnRuY29sb3IiOiIjZmZmIiwiYnV5YnRuYmFja2dyb3VuZCI6IiIsImJhcmJhY2tncm91bmQiOiIjRkVENEQ1IiwiYmFyaW5uZXJiYWNrZ3JvdW5kIjoiIiwiYmFydGV4dGNvbG9yIjoiI2ZmZiIsImhlYWRsaW5lY29sb3IiOiIjMzMzMzMzIiwibW9yZWNvbG9yIjoiIzk5OTk5OSJ9LCJkYXRhIjp7IkMxNTQ0MTYzNjc5MDkzIjp7ImlkIjoiMCIsInNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX0sIkMxNTQ0MTYzNjc5MDk0Ijp7ImlkIjoiMCIsInNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX0sIkMxNTQ0MTYzNjc5MDk1Ijp7ImlkIjoiMCIsInNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX0sIkMxNTQ0MTYzNjc5MDk2Ijp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNC5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX19LCJpZCI6ImdvaG9tZUFjdGl2aXR5In0sIk0xNTQ1MzkxNTYwOTEyIjp7InBhcmFtcyI6eyJ0aXRsZSI6Ilx1N2NiZVx1OTAwOVx1NTk3ZFx1NWU5NyIsInNob3d0eXBlIjoiMSIsInBhZ2VudW0iOiI4Iiwic3RvcmVudW0iOiIyMCJ9LCJzdHlsZSI6eyJtYXJnaW50b3AiOiIxMCIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIiwidGl0bGVjb2xvciI6IiMzMzMzMzMiLCJzdG9yZXRpdGxlY29sb3IiOiIjMzMzMzMzIiwic2hvd2RvdCI6IjEifSwiZGF0YSI6eyJDMTU0NTM5MTU2MDkxMiI6eyJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIn0sIkMxNTQ1MzkxNTYwOTEzIjp7ImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAifSwiQzE1NDUzOTE1NjA5MTQiOnsibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJ0aXRsZSI6Ilx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCJ9LCJDMTU0NTM5MTU2MDkxNSI6eyJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS00LmpwZyIsInRpdGxlIjoiXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIn19LCJpZCI6Imhhb2RpYW5Hcm91cCJ9LCJNMTU0NDE2MzY3NTQwNSI6eyJwYXJhbXMiOnsiaGVhZGxpbmVfc2hvdyI6IjEiLCJoZWFkbGluZSI6Ilx1NTQwY1x1NTdjZVx1NGZlMVx1NjA2ZiIsIm1vcmUiOiJcdTY2ZjRcdTU5MWEiLCJtb3JlX2xpbmsiOiJcL2dvaG9tZVwvcGFnZXNcL3RvbmdjaGVuZ1wvaW5kZXgiLCJpbmZvcm1hdGlvbmRhdGEiOiIxIiwiaW5mb3JtYXRpb25udW0iOiIxMCJ9LCJzdHlsZSI6eyJtYXJnaW50b3AiOiIxMCIsImhlYWRsaW5lY29sb3IiOiIjMzMzMzMzIiwibW9yZWNvbG9yIjoiIzk5OTk5OSJ9LCJkYXRhIjp7IkMxNTQ0MTYzNjc1NDA1Ijp7InRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9tZW1iZXIuanBnIiwidGllemlpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwiQzAxMjM0NTY3ODkxMDQiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTQuanBnIn19fSwiaWQiOiJ0b25nY2hlbmcifX19", "updatetime" => 1531985983, "version" => 2), "wxappgohome" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "1", "data" => "eyJwYWdlIjp7InR5cGUiOiI0IiwidGl0bGUiOiJcdTc1MWZcdTZkM2JcdTU3MDgiLCJuYW1lIjoiXHU3NTFmXHU2ZDNiXHU1NzA4XHU5OTk2XHU5ODc1IiwidGh1bWIiOiIiLCJkZXNjIjoiXHU3NTFmXHU2ZDNiXHU1NzA4Iiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjRjNGM0YzIiwiZGl5Z290b3AiOiIwIiwibmF2aWdhdGlvbmJhY2tncm91bmQiOiIjMDAwMDAwIiwibmF2aWdhdGlvbnRleHRjb2xvciI6IiNmZmZmZmYiLCJkaXltZW51IjoiMCIsImZvbGxvd2JhciI6IjEifSwiaXRlbXMiOnsiTTE1NDQxNjMzMTU4ODYiOnsicGFyYW1zIjp7ImxvY2F0aW9uIjoiXHU1YjlhXHU0ZjRkIiwidGV4dCI6Ilx1OGJmN1x1OGY5M1x1NTE2NVx1NGZlMVx1NjA2Zlx1NTE4NVx1NWJiOSIsImxpbmsiOiJcL2dvaG9tZVwvcGFnZXNcL3RvbmdjaGVuZ1wvc2VhcmNoIiwibGlua3RvIjoiMSJ9LCJzdHlsZSI6eyJsb2NzdHlsZSI6InJhZGl1cyIsInNlYXJjaHN0eWxlIjoicmFkaXVzIiwiZml4ZWRiYWNrZ3JvdW5kIjoiI2ZmMmQ0YiIsImxvY2JhY2tncm91bmQiOiIjOTk5OTk5Iiwic2VhcmNoYmFja2dyb3VuZCI6IiNmNGY0ZjQiLCJsb2Njb2xvciI6IiNmZmZmZmYiLCJzZWFyY2hjb2xvciI6IiM2NTY1NjUifSwibWF4IjoiMSIsImlzdG9wIjoiMSIsImlkIjoiZml4ZWRzZWFyY2gifSwiTTE1NDQxNjkwMDc0MjYiOnsicGFyYW1zIjp7InBpY3R1cmVkYXRhIjoiMiIsImhhc19nb2hvbWUiOiJ0cnVlIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIxMCIsInBhZGRpbmdsZWZ0IjoiMTAiLCJkb3RiYWNrZ3JvdW5kIjoiI2ZmMmQ0YiIsImJhY2tncm91bmQiOiIjZmFmYWZhIn0sImRhdGEiOnsiQzExMzMzMDEyOTEiOnsibGlua3VybCI6bnVsbCwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL2RDVENSS1JRS3NVVFI3cVV1N2d3U0ZyZGREdUM3Ri5qcGcifX0sImlkIjoicGljdHVyZSJ9LCJNMTU0NDE2OTEyMzI1OSI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIxIiwic2hvd2RvdCI6IjEiLCJyb3dudW0iOiI1IiwicGFnZW51bSI6IjEwIiwibmF2c2RhdGEiOiIyIiwibmF2c251bSI6IjQ3IiwiaGFzX2dvaG9tZSI6InRydWUifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMCIsIm5hdnN0eWxlIjoiY2lyY2xlIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIifSwiZGF0YSI6eyJDMTM1NzY2ODQzOSI6eyJsaW5rdXJsIjoiIiwidGV4dCI6Ilx1NzgwZFx1NGVmNyIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzA3XC9kQ1RDUktSUUtzVVRSN3FVdTdnd1NGcmRkRHVDN0YuanBnIn19LCJpZCI6Im5hdnMiLCJkYXRhX251bSI6MSwicm93IjoyfSwiTTE1NDQxNjg4Nzc0MTgiOnsicGFyYW1zIjp7Im5vdGljZWRhdGEiOiIyIiwibm90aWNlbnVtIjoiMTAiLCJzcGVlZCI6IjIiLCJpbWd1cmwiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3N0YXRpY1wvaW1nXC9oZWFkX2xpbmVfdG9uZ2NoZW5nLnBuZyIsImhhc19nb2hvbWUiOiJ0cnVlIn0sInN0eWxlIjp7InBhZGRpbmd0b3AiOiIwIiwicGFkZGluZ2xlZnQiOiIwIiwidGV4dGNvbG9yIjoiIzY2NjY2NiIsImljb25jb2xvciI6IiNmZDU0NTQiLCJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiJ9LCJkYXRhIjp7IkMxNDA4Mzg4NTA0Ijp7ImlkIjoiMyIsInRpdGxlIjoiXHU3NTFmXHU2ZDNiXHU1NzA4XHU0ZTBhXHU3ZWJmXHU1NWJkIiwibGlua3VybCI6IiJ9fSwiaWQiOiJub3RpY2UifSwiTTE1NDQxNjM0MDczMTkiOnsicGFyYW1zIjp7InR5cGUiOiJrYW5qaWEiLCJoZWFkbGluZSI6Ilx1NzVhZlx1NzJjMlx1NzgwZFx1NGVmNyIsImhlYWRsaW5lX3Nob3ciOiIxIiwibW9yZSI6Ilx1NjZmNFx1NTkxYSIsIm1vcmVfbGluayI6IlwvZ29ob21lXC9wYWdlc1wva2FuamlhXC9pbmRleCIsInNob3dvbGRwcmljZSI6IjEiLCJidXlidG50ZXh0IjoiXHU1M2JiXHU2MmZjXHU1NmUyIiwiYnV5YnRudGV4dF9rYW5qaWEiOiJcdTUzYmJcdTc4MGRcdTRlZjciLCJidXlidG50ZXh0X3BpbnR1YW4iOiJcdTUzYmJcdTYyZmNcdTU2ZTIiLCJidXlidG50ZXh0X3NlY2tpbGwiOiJcdTdhY2JcdTUzNzNcdTYyYTIiLCJnb29kc2RhdGEiOiIxIiwiZ29vZHNudW0iOiIzIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiYmFja2dyb3VuZCI6IiNmNWY1ZjUiLCJtYXJnaW5ib3R0b20iOiIwIiwibGlzdHN0eWxlIjoiMSIsInRpdGxlY29sb3IiOiIjMzMzIiwicHJpY2Vjb2xvciI6IiNmZjJkNGIiLCJvbGRwcmljZWNvbG9yIjoiIzk5OSIsImJ1eWJ0bmNvbG9yIjoiI2ZmZiIsImJ1eWJ0bmJhY2tncm91bmQiOiIiLCJiYXJiYWNrZ3JvdW5kIjoiI0ZFRDRENSIsImJhcmlubmVyYmFja2dyb3VuZCI6IiIsImJhcnRleHRjb2xvciI6IiNmZmYiLCJoZWFkbGluZWNvbG9yIjoiIzMzMzMzMyIsIm1vcmVjb2xvciI6IiM5OTk5OTkifSwiZGF0YSI6eyJDMTU0NDE2MzQwNzMyMCI6eyJpZCI6IjAiLCJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19LCJDMTU0NDE2MzQwNzMyMSI6eyJpZCI6IjAiLCJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19LCJDMTU0NDE2MzQwNzMyMiI6eyJpZCI6IjAiLCJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTMuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19LCJDMTU0NDE2MzQwNzMyMyI6eyJzaWQiOiIwIiwidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTQuanBnIiwicHJpY2UiOiIxMC4wMCIsIm9sZF9wcmljZSI6IjIwLjAwIiwidGl0bGUiOiJcdThmZDlcdTkxY2NcdTY2MmZcdTU1NDZcdTU0YzFcdTY4MDdcdTk4OTgiLCJkaXNjb3VudCI6IjUiLCJmYWxlc2FpbGVkX3RvdGFsIjoiMTAiLCJzYWlsZWRfcGVyY2VudCI6IjgwIiwicGVvcGxlbnVtIjoiMTAiLCJwZW9wbGVpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIn19fSwiaWQiOiJnb2hvbWVBY3Rpdml0eSJ9LCJNMTU0NDE2OTI1OTU5OSI6eyJwYXJhbXMiOnsidHlwZSI6InBpbnR1YW4iLCJoZWFkbGluZSI6Ilx1OTQ5Y1x1NjBlMFx1NjJmY1x1NTZlMiIsImhlYWRsaW5lX3Nob3ciOiIxIiwibW9yZSI6Ilx1NjZmNFx1NTkxYSIsIm1vcmVfbGluayI6IlwvZ29ob21lXC9wYWdlc1wvcGludHVhblwvaW5kZXgiLCJzaG93b2xkcHJpY2UiOiIxIiwiYnV5YnRudGV4dCI6Ilx1NTNiYlx1NjJmY1x1NTZlMiIsImJ1eWJ0bnRleHRfa2FuamlhIjoiXHU1M2JiXHU3ODBkXHU0ZWY3IiwiYnV5YnRudGV4dF9waW50dWFuIjoiXHU1M2JiXHU2MmZjXHU1NmUyIiwiYnV5YnRudGV4dF9zZWNraWxsIjoiXHU3YWNiXHU1MzczXHU2MmEyIiwiZ29vZHNkYXRhIjoiMSIsImdvb2RzbnVtIjoiNiJ9LCJzdHlsZSI6eyJtYXJnaW50b3AiOiIxMCIsImJhY2tncm91bmQiOiIjZjVmNWY1IiwibWFyZ2luYm90dG9tIjoiMCIsImxpc3RzdHlsZSI6IjQiLCJ0aXRsZWNvbG9yIjoiIzMzMyIsInByaWNlY29sb3IiOiIjZmYyZDRiIiwib2xkcHJpY2Vjb2xvciI6IiM5OTkiLCJidXlidG5jb2xvciI6IiNmZmYiLCJidXlidG5iYWNrZ3JvdW5kIjoiIiwiYmFyYmFja2dyb3VuZCI6IiNGRUQ0RDUiLCJiYXJpbm5lcmJhY2tncm91bmQiOiIiLCJiYXJ0ZXh0Y29sb3IiOiIjZmZmIiwiaGVhZGxpbmVjb2xvciI6IiMzMzMzMzMiLCJtb3JlY29sb3IiOiIjOTk5OTk5In0sImRhdGEiOnsiQzE1NDQxNjkyNTk1OTkiOnsiaWQiOiIwIiwic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fSwiQzE1NDQxNjkyNTk2MDAiOnsiaWQiOiIwIiwic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0yLmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fSwiQzE1NDQxNjkyNTk2MDEiOnsiaWQiOiIwIiwic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0zLmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fSwiQzE1NDQxNjkyNTk2MDIiOnsic2lkIjoiMCIsInRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy00LmpwZyIsInByaWNlIjoiMTAuMDAiLCJvbGRfcHJpY2UiOiIyMC4wMCIsInRpdGxlIjoiXHU4ZmQ5XHU5MWNjXHU2NjJmXHU1NTQ2XHU1NGMxXHU2ODA3XHU5ODk4IiwiZGlzY291bnQiOiI1IiwiZmFsZXNhaWxlZF90b3RhbCI6IjEwIiwic2FpbGVkX3BlcmNlbnQiOiI4MCIsInBlb3BsZW51bSI6IjEwIiwicGVvcGxlaW1nIjp7IkMwMTIzNDU2Nzg5MTAxIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAyIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9nb29kcy0xLmpwZyIsIkMwMTIzNDU2Nzg5MTAzIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyJ9fX0sImlkIjoiZ29ob21lQWN0aXZpdHkifSwiTTE1NDQxNjM2NzkwOTMiOnsicGFyYW1zIjp7InR5cGUiOiJzZWNraWxsIiwiaGVhZGxpbmUiOiJcdTcyMzFcdTRlMGFcdTYyYTJcdThkMmQiLCJoZWFkbGluZV9zaG93IjoiMSIsIm1vcmUiOiJcdTY2ZjRcdTU5MWEiLCJtb3JlX2xpbmsiOiJcL2dvaG9tZVwvcGFnZXNcL3NlY2tpbGxcL2luZGV4Iiwic2hvd29sZHByaWNlIjoiMSIsImJ1eWJ0bnRleHQiOiJcdTUzYmJcdTYyZmNcdTU2ZTIiLCJidXlidG50ZXh0X2thbmppYSI6Ilx1NTNiYlx1NzgwZFx1NGVmNyIsImJ1eWJ0bnRleHRfcGludHVhbiI6Ilx1NTNiYlx1NjJmY1x1NTZlMiIsImJ1eWJ0bnRleHRfc2Vja2lsbCI6Ilx1N2FjYlx1NTM3M1x1NjJhMiIsImdvb2RzZGF0YSI6IjEiLCJnb29kc251bSI6IjQifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMTAiLCJiYWNrZ3JvdW5kIjoiI2Y1ZjVmNSIsIm1hcmdpbmJvdHRvbSI6IjAiLCJsaXN0c3R5bGUiOiIyIiwidGl0bGVjb2xvciI6IiMzMzMiLCJwcmljZWNvbG9yIjoiI2ZmMmQ0YiIsIm9sZHByaWNlY29sb3IiOiIjOTk5IiwiYnV5YnRuY29sb3IiOiIjZmZmIiwiYnV5YnRuYmFja2dyb3VuZCI6IiIsImJhcmJhY2tncm91bmQiOiIjRkVENEQ1IiwiYmFyaW5uZXJiYWNrZ3JvdW5kIjoiIiwiYmFydGV4dGNvbG9yIjoiI2ZmZiIsImhlYWRsaW5lY29sb3IiOiIjMzMzMzMzIiwibW9yZWNvbG9yIjoiIzk5OTk5OSJ9LCJkYXRhIjp7IkMxNTQ0MTYzNjc5MDkzIjp7ImlkIjoiMCIsInNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX0sIkMxNTQ0MTYzNjc5MDk0Ijp7ImlkIjoiMCIsInNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX0sIkMxNTQ0MTYzNjc5MDk1Ijp7ImlkIjoiMCIsInNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMy5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX0sIkMxNTQ0MTYzNjc5MDk2Ijp7InNpZCI6IjAiLCJ0aHVtYiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtNC5qcGciLCJwcmljZSI6IjEwLjAwIiwib2xkX3ByaWNlIjoiMjAuMDAiLCJ0aXRsZSI6Ilx1OGZkOVx1OTFjY1x1NjYyZlx1NTU0Nlx1NTRjMVx1NjgwN1x1OTg5OCIsImRpc2NvdW50IjoiNSIsImZhbGVzYWlsZWRfdG90YWwiOiIxMCIsInNhaWxlZF9wZXJjZW50IjoiODAiLCJwZW9wbGVudW0iOiIxMCIsInBlb3BsZWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMS5qcGcifX19LCJpZCI6ImdvaG9tZUFjdGl2aXR5In0sIk0xNTQ1MzkxNTYwOTEyIjp7InBhcmFtcyI6eyJ0aXRsZSI6Ilx1N2NiZVx1OTAwOVx1NTk3ZFx1NWU5NyIsInNob3d0eXBlIjoiMSIsInBhZ2VudW0iOiI4Iiwic3RvcmVudW0iOiIyMCJ9LCJzdHlsZSI6eyJtYXJnaW50b3AiOiIxMCIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIiwidGl0bGVjb2xvciI6IiMzMzMzMzMiLCJzdG9yZXRpdGxlY29sb3IiOiIjMzMzMzMzIiwic2hvd2RvdCI6IjEifSwiZGF0YSI6eyJDMTU0NTM5MTU2MDkxMiI6eyJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0xLmpwZyIsInRpdGxlIjoiXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIn0sIkMxNTQ1MzkxNTYwOTEzIjp7ImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTIuanBnIiwidGl0bGUiOiJcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAifSwiQzE1NDUzOTE1NjA5MTQiOnsibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJ0aXRsZSI6Ilx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCJ9LCJDMTU0NTM5MTU2MDkxNSI6eyJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS00LmpwZyIsInRpdGxlIjoiXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIn19LCJpZCI6Imhhb2RpYW5Hcm91cCJ9LCJNMTU0NDE2MzY3NTQwNSI6eyJwYXJhbXMiOnsiaGVhZGxpbmVfc2hvdyI6IjEiLCJoZWFkbGluZSI6Ilx1NTQwY1x1NTdjZVx1NGZlMVx1NjA2ZiIsIm1vcmUiOiJcdTY2ZjRcdTU5MWEiLCJtb3JlX2xpbmsiOiJcL2dvaG9tZVwvcGFnZXNcL3RvbmdjaGVuZ1wvaW5kZXgiLCJpbmZvcm1hdGlvbmRhdGEiOiIxIiwiaW5mb3JtYXRpb25udW0iOiIxMCJ9LCJzdHlsZSI6eyJtYXJnaW50b3AiOiIxMCIsImhlYWRsaW5lY29sb3IiOiIjMzMzMzMzIiwibW9yZWNvbG9yIjoiIzk5OTk5OSJ9LCJkYXRhIjp7IkMxNTQ0MTYzNjc1NDA1Ijp7InRodW1iIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9tZW1iZXIuanBnIiwidGllemlpbWciOnsiQzAxMjM0NTY3ODkxMDEiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTEuanBnIiwiQzAxMjM0NTY3ODkxMDIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL2dvb2RzLTIuanBnIiwiQzAxMjM0NTY3ODkxMDMiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTMuanBnIiwiQzAxMjM0NTY3ODkxMDQiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTQuanBnIn19fSwiaWQiOiJ0b25nY2hlbmcifX19", "updatetime" => 1531985983, "version" => 2), "vuetongcheng" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "1", "data" => "eyJwYWdlIjp7InR5cGUiOiI0IiwidGl0bGUiOiJcdTU0MGNcdTU3Y2VcdTRmZTFcdTYwNmYiLCJuYW1lIjoiXHU1NDBjXHU1N2NlXHU0ZmUxXHU2MDZmIiwidGh1bWIiOiIiLCJkZXNjIjoiXHU1NDBjXHU1N2NlXHU0ZmUxXHU2MDZmIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjRjNGM0YzIiwiZGl5Z290b3AiOiIwIiwibmF2aWdhdGlvbmJhY2tncm91bmQiOiIjMDAwMDAwIiwibmF2aWdhdGlvbnRleHRjb2xvciI6IiNmZmZmZmYiLCJkaXltZW51IjoiLTEiLCJmb2xsb3diYXIiOiIwIn0sIml0ZW1zIjp7Ik0xNTQ0MTc2NzA3OTUyIjp7InBhcmFtcyI6eyJsb2NhdGlvbiI6Ilx1NWI5YVx1NGY0ZCIsInRleHQiOiJcdThiZjdcdThmOTNcdTUxNjVcdTRmZTFcdTYwNmZcdTUxODVcdTViYjkiLCJsaW5rdG8iOiIxIiwibGluayI6IlwvZ29ob21lXC9wYWdlc1wvdG9uZ2NoZW5nXC9zZWFyY2gifSwic3R5bGUiOnsibG9jc3R5bGUiOiJyYWRpdXMiLCJzZWFyY2hzdHlsZSI6InJhZGl1cyIsImZpeGVkYmFja2dyb3VuZCI6IiNmZjJkNGIiLCJsb2NiYWNrZ3JvdW5kIjoiIzk5OTk5OSIsInNlYXJjaGJhY2tncm91bmQiOiIjZjRmNGY0IiwibG9jY29sb3IiOiIjZmZmZmZmIiwic2VhcmNoY29sb3IiOiIjNjU2NTY1In0sIm1heCI6IjEiLCJpc3RvcCI6IjEiLCJpZCI6ImZpeGVkc2VhcmNoIn0sIk0xNTQ0MTc2NzM3MTE0Ijp7InBhcmFtcyI6eyJwaWN0dXJlZGF0YSI6IjMiLCJoYXNfZ29ob21lIjoidHJ1ZSJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTAiLCJwYWRkaW5nbGVmdCI6IjEwIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIiLCJiYWNrZ3JvdW5kIjoiI2ZhZmFmYSJ9LCJkYXRhIjp7IkMxMTc1MjExMzU3Ijp7Imxpbmt1cmwiOm51bGwsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzExXC9mWDFXMlJ3b3ExZFc0UTU1UW13T1A1VHg0NTJudzYuanBnIn19LCJpZCI6InBpY3R1cmUifSwiTTE1NDQxNzY4OTM5NTYiOnsicGFyYW1zIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvc3RhdGljXC9pbWdcL3N0YXRpc3RpY3MuZ2lmIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJiYWNrZ3JvdW5kY29sb3IiOiIjZmZmZmZmIiwidGV4dGNvbG9yIjoiIzY2NjY2NiJ9LCJpZCI6InRvbmdjaGVuZ1N0YXRpc3RpY3MifSwiTTE1NDQxNzY3NDg4MTAiOnsicGFyYW1zIjp7InNob3d0eXBlIjoiMSIsInNob3dkb3QiOiIxIiwicm93bnVtIjoiNSIsInBhZ2VudW0iOiIxMCIsIm5hdnNkYXRhIjoiMyIsIm5hdnNudW0iOiI1MCIsImhhc19nb2hvbWUiOiJ0cnVlIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJuYXZzdHlsZSI6ImNpcmNsZSIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIn0sImRhdGEiOnsiQzEzMzE4NTE2MzgiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvdG9uZ2NoZW5nXC9jYXRlZ29yeT9pZD01MCIsInRleHQiOiJcdTYyZGJcdTgwNTgiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8xMVwvbzdWOTU4Z2lCOFRvSWVnRnRqOTdJaTI4akIyRk92LnBuZyJ9fSwiaWQiOiJuYXZzIiwiZGF0YV9udW0iOjEsInJvdyI6Mn0sIk0xNTQ0MTc2ODQ1MTQ1Ijp7InBhcmFtcyI6eyJoZWFkbGluZV9zaG93IjoiMSIsImhlYWRsaW5lIjoiXHU1NDBjXHU1N2NlXHU0ZmUxXHU2MDZmIiwibW9yZSI6Ilx1NjZmNFx1NTkxYSIsIm1vcmVfbGluayI6IlwvZ29ob21lXC9wYWdlc1wvdG9uZ2NoZW5nXC9pbmRleCIsImluZm9ybWF0aW9uZGF0YSI6IjEiLCJpbmZvcm1hdGlvbm51bSI6IjEwIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiaGVhZGxpbmVjb2xvciI6IiMzMzMzMzMiLCJtb3JlY29sb3IiOiIjOTk5OTk5In0sImRhdGEiOnsiQzE1NDQxNzY4NDUxNDUiOnsidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL21lbWJlci5qcGciLCJ0aWV6aWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJDMDEyMzQ1Njc4OTEwNCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtNC5qcGcifX19LCJpZCI6InRvbmdjaGVuZyJ9fX0=", "updatetime" => 1531985983, "version" => 2), "wxapptongcheng" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "1", "data" => "eyJwYWdlIjp7InR5cGUiOiI0IiwidGl0bGUiOiJcdTU0MGNcdTU3Y2VcdTRmZTFcdTYwNmYiLCJuYW1lIjoiXHU1NDBjXHU1N2NlXHU0ZmUxXHU2MDZmIiwidGh1bWIiOiIiLCJkZXNjIjoiXHU1NDBjXHU1N2NlXHU0ZmUxXHU2MDZmIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjRjNGM0YzIiwiZGl5Z290b3AiOiIwIiwibmF2aWdhdGlvbmJhY2tncm91bmQiOiIjMDAwMDAwIiwibmF2aWdhdGlvbnRleHRjb2xvciI6IiNmZmZmZmYiLCJkaXltZW51IjoiLTEiLCJmb2xsb3diYXIiOiIwIn0sIml0ZW1zIjp7Ik0xNTQ0MTc2NzA3OTUyIjp7InBhcmFtcyI6eyJsb2NhdGlvbiI6Ilx1NWI5YVx1NGY0ZCIsInRleHQiOiJcdThiZjdcdThmOTNcdTUxNjVcdTRmZTFcdTYwNmZcdTUxODVcdTViYjkiLCJsaW5rdG8iOiIxIiwibGluayI6IlwvZ29ob21lXC9wYWdlc1wvdG9uZ2NoZW5nXC9zZWFyY2gifSwic3R5bGUiOnsibG9jc3R5bGUiOiJyYWRpdXMiLCJzZWFyY2hzdHlsZSI6InJhZGl1cyIsImZpeGVkYmFja2dyb3VuZCI6IiNmZjJkNGIiLCJsb2NiYWNrZ3JvdW5kIjoiIzk5OTk5OSIsInNlYXJjaGJhY2tncm91bmQiOiIjZjRmNGY0IiwibG9jY29sb3IiOiIjZmZmZmZmIiwic2VhcmNoY29sb3IiOiIjNjU2NTY1In0sIm1heCI6IjEiLCJpc3RvcCI6IjEiLCJpZCI6ImZpeGVkc2VhcmNoIn0sIk0xNTQ0MTc2NzM3MTE0Ijp7InBhcmFtcyI6eyJwaWN0dXJlZGF0YSI6IjMiLCJoYXNfZ29ob21lIjoidHJ1ZSJ9LCJzdHlsZSI6eyJwYWRkaW5ndG9wIjoiMTAiLCJwYWRkaW5nbGVmdCI6IjEwIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIiLCJiYWNrZ3JvdW5kIjoiI2ZhZmFmYSJ9LCJkYXRhIjp7IkMxMTc1MjExMzU3Ijp7Imxpbmt1cmwiOm51bGwsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzExXC9mWDFXMlJ3b3ExZFc0UTU1UW13T1A1VHg0NTJudzYuanBnIn19LCJpZCI6InBpY3R1cmUifSwiTTE1NDQxNzY4OTM5NTYiOnsicGFyYW1zIjp7ImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvc3RhdGljXC9pbWdcL3N0YXRpc3RpY3MuZ2lmIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJiYWNrZ3JvdW5kY29sb3IiOiIjZmZmZmZmIiwidGV4dGNvbG9yIjoiIzY2NjY2NiJ9LCJpZCI6InRvbmdjaGVuZ1N0YXRpc3RpY3MifSwiTTE1NDQxNzY3NDg4MTAiOnsicGFyYW1zIjp7InNob3d0eXBlIjoiMSIsInNob3dkb3QiOiIxIiwicm93bnVtIjoiNSIsInBhZ2VudW0iOiIxMCIsIm5hdnNkYXRhIjoiMyIsIm5hdnNudW0iOiI1MCIsImhhc19nb2hvbWUiOiJ0cnVlIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJuYXZzdHlsZSI6ImNpcmNsZSIsImRvdGJhY2tncm91bmQiOiIjZmYyZDRiIn0sImRhdGEiOnsiQzEzMzE4NTE2MzgiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvdG9uZ2NoZW5nXC9jYXRlZ29yeT9pZD01MCIsInRleHQiOiJcdTYyZGJcdTgwNTgiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8xMVwvbzdWOTU4Z2lCOFRvSWVnRnRqOTdJaTI4akIyRk92LnBuZyJ9fSwiaWQiOiJuYXZzIiwiZGF0YV9udW0iOjEsInJvdyI6Mn0sIk0xNTQ0MTc2ODQ1MTQ1Ijp7InBhcmFtcyI6eyJoZWFkbGluZV9zaG93IjoiMSIsImhlYWRsaW5lIjoiXHU1NDBjXHU1N2NlXHU0ZmUxXHU2MDZmIiwibW9yZSI6Ilx1NjZmNFx1NTkxYSIsIm1vcmVfbGluayI6IlwvZ29ob21lXC9wYWdlc1wvdG9uZ2NoZW5nXC9pbmRleCIsImluZm9ybWF0aW9uZGF0YSI6IjEiLCJpbmZvcm1hdGlvbm51bSI6IjEwIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiaGVhZGxpbmVjb2xvciI6IiMzMzMzMzMiLCJtb3JlY29sb3IiOiIjOTk5OTk5In0sImRhdGEiOnsiQzE1NDQxNzY4NDUxNDUiOnsidGh1bWIiOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL21lbWJlci5qcGciLCJ0aWV6aWltZyI6eyJDMDEyMzQ1Njc4OTEwMSI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMS5qcGciLCJDMDEyMzQ1Njc4OTEwMiI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvZ29vZHMtMi5qcGciLCJDMDEyMzQ1Njc4OTEwMyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMy5qcGciLCJDMDEyMzQ1Njc4OTEwNCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtNC5qcGcifX19LCJpZCI6InRvbmdjaGVuZyJ9fX0=", "updatetime" => 1531985983, "version" => 2), "vuehaodian" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "5", "data" => "eyJwYWdlIjp7InR5cGUiOiI0IiwidGl0bGUiOiJcdTU5N2RcdTVlOTciLCJuYW1lIjoiXHU1OTdkXHU1ZTk3XHU5ZWQ4XHU4YmE0XHU5ODc1XHU5NzYyIiwidGh1bWIiOiIiLCJkZXNjIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjRjNGM0YzIiwiZGl5Z290b3AiOiIwIiwibmF2aWdhdGlvbmJhY2tncm91bmQiOiIjZmYyYjRkIiwibmF2aWdhdGlvbnRleHRjb2xvciI6IiNmZmZmZmYiLCJkaXltZW51IjoiLTEiLCJmb2xsb3diYXIiOiIwIn0sIml0ZW1zIjp7Ik0xNTQ1MDM3MDM3OTc3Ijp7InBhcmFtcyI6eyJsb2NhdGlvbiI6Ilx1NWI5YVx1NGY0ZCIsInRleHQiOiJcdThiZjdcdThmOTNcdTUxNjVcdTU1NDZcdTYyMzdcdTU0MGRcdTc5ZjAiLCJsaW5rdG8iOiIyIiwibGluayI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvc2VhcmNoIn0sInN0eWxlIjp7ImxvY3N0eWxlIjoicmFkaXVzIiwic2VhcmNoc3R5bGUiOiJyYWRpdXMiLCJmaXhlZGJhY2tncm91bmQiOiIjZmYyZDRiIiwibG9jYmFja2dyb3VuZCI6IiM5OTk5OTkiLCJzZWFyY2hiYWNrZ3JvdW5kIjoiI2Y0ZjRmNCIsImxvY2NvbG9yIjoiI2ZmZmZmZiIsInNlYXJjaGNvbG9yIjoiIzY1NjU2NSJ9LCJtYXgiOiIxIiwiaXN0b3AiOiIxIiwiaWQiOiJmaXhlZHNlYXJjaCJ9LCJNMTU0NTAzNzEyODIxNSI6eyJwYXJhbXMiOnsicGljdHVyZWRhdGEiOiI0IiwiaGFzX2dvaG9tZSI6InRydWUifSwic3R5bGUiOnsicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjAiLCJkb3RiYWNrZ3JvdW5kIjoiI2ZmMmQ0YiIsImJhY2tncm91bmQiOiIjZmFmYWZhIn0sImRhdGEiOnsiQzEyODcwMzgyNzciOnsibGlua3VybCI6bnVsbCwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL1k1ZWFqMjNuYUx6MmFtNThBWXVBVjJ2ZWFBT0xyUi5qcGcifSwiQzEzNzI3OTgxMTEiOnsibGlua3VybCI6bnVsbCwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL24weng0NGV2cXUwTW5YdkROYXc3em0wZUEwZUREWS5qcGcifX0sImlkIjoicGljdHVyZSJ9LCJNMTU0NTAzNzE5MDM1NSI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIxIiwic2hvd2RvdCI6IjEiLCJyb3dudW0iOiI1IiwicGFnZW51bSI6IjEwIiwibmF2c2RhdGEiOiI0IiwibmF2c251bSI6IjUwIiwiaGFzX2dvaG9tZSI6InRydWUifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMCIsIm5hdnN0eWxlIjoiIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIifSwiZGF0YSI6eyJDMTMwODMzNzQ1MCI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MSIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTUyMDZcdTdjN2IxIiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL001Nnk0NjU1djY1Ym42MTNwNTZaNjNaUlJybmh2Ti5wbmcifSwiQzEyOTgwODgzMTgiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvY2F0ZWdvcnk/Y2lkPTMiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMyIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzA3XC9NNTZ5NDY1NXY2NWJuNjEzcDU2WjYzWlJScm5odk4ucG5nIn0sIkMxMzc5MDkyNzU1Ijp7Imxpbmt1cmwiOiJcL2dvaG9tZVwvcGFnZXNcL2hhb2RpYW5cL2NhdGVnb3J5P2NpZD01IiwidGV4dCI6Ilx1NTk3ZFx1NWU5N1x1NTIwNlx1N2M3YjUiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTM4NTc2MjgyNSI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9NyIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTUyMDZcdTdjN2I3IiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL001Nnk0NjU1djY1Ym42MTNwNTZaNjNaUlJybmh2Ti5wbmcifSwiQzEyMDkzNzUxMzIiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvY2F0ZWdvcnk/Y2lkPTE1IiwidGV4dCI6Ilx1NTk3ZFx1NWU5N1x1NTIwNlx1N2M3YjIiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTE5ODUyNTMxOCI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MTciLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiNCIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzA3XC9NNTZ5NDY1NXY2NWJuNjEzcDU2WjYzWlJScm5odk4ucG5nIn0sIkMxMTExNTM5MDkyIjp7Imxpbmt1cmwiOiJcL2dvaG9tZVwvcGFnZXNcL2hhb2RpYW5cL2NhdGVnb3J5P2NpZD0xOCIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTUyMDZcdTdjN2I2IiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL001Nnk0NjU1djY1Ym42MTNwNTZaNjNaUlJybmh2Ti5wbmcifSwiQzExNTc2NTM5MzAiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvY2F0ZWdvcnk/Y2lkPTE5IiwidGV4dCI6Ilx1NTk3ZFx1NWU5N1x1NTIwNlx1N2M3YjgiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTA3NzU3NTU0NSI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjAiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTAiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTIxMDI4ODY2OSI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjEiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTIiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTA4NjY2MDg1NiI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjIiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTQiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTEzNjk4MDQ2NyI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjMiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTYiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9fSwiaWQiOiJuYXZzIiwiZGF0YV9udW0iOjEyLCJyb3ciOjJ9LCJNMTU0NTAzNzM4NjI5OCI6eyJwYXJhbXMiOnsiYnRudGV4dCI6Ilx1NjIxMVx1ODk4MVx1NTE2NVx1OWE3YiIsImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvc3RhdGljXC9pbWdcL25ld19zZXR0bGUucG5nIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJiYWNrZ3JvdW5kY29sb3IiOiIjZmZmZmZmIiwibmV3c2NvbG9yIjoiIzMzMzMzMyIsInRpdGxlY29sb3IiOiIjZmYyZDRiIiwiYnRuY29sb3IiOiIjZmZmZmZmIiwiYnRuYmFja2dyb3VuZCI6IiNmZjJkNGIifSwiaWQiOiJoYW9kaWFuU2V0dGxlIn0sIk0xNTQ1MDM3MzAwODk5Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiYmFja2dyb3VuZGNvbG9yIjoiI2ZmZmZmZiIsInN0b3JldGl0bGVjb2xvciI6IiMzMzMzMzMiLCJzdGFyc2NvbG9yIjoiI2ZmMmQ0YiIsInN0YXJ0ZXh0Y29sb3IiOiIjOTk5OTk5Iiwic3RvcmVkaXN0YW5jZWNvbG9yIjoiI2ZmMmQ0YiIsInN0b3JldGFnc3RleHRjb2xvciI6IiNmZjJkNGIiLCJ0YWdzYmFja2dyb3VuZGNvbG9yIjoiI0ZGRTNFNyJ9LCJkYXRhIjp7IkMxNTQ1MDM3MzAwOTAwIjp7ImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJ0eXBlIjoiZGlzY291bnQiLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJ0eXBlIjoiY291cG9uQ29sbGVjdCIsInRpdGxlIjoiXHU1M2VmXHU5ODg2Mlx1NTE0M1x1NGVlM1x1OTFkMVx1NTIzOCJ9fSwibnVtIjoiMiJ9fSwiQzE1NDUwMzczMDA5MDIiOnsibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InR5cGUiOiJkaXNjb3VudCIsInRpdGxlIjoiXHU2ZWUxMzVcdTUxY2YxMjtcdTZlZTE2MFx1NTFjZjIwIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InR5cGUiOiJjb3Vwb25Db2xsZWN0IiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn19LCJDMTU0NTAzNzMwMDkwMyI6eyJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiYWN0aXZpdHkiOnsiaXRlbXMiOnsiQzAxMjM0NTY3ODkxMDEiOnsidHlwZSI6ImRpc2NvdW50IiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsidHlwZSI6ImNvdXBvbkNvbGxlY3QiLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifX19LCJpZCI6Imhhb2RpYW5MaXN0In19fQ==", "updatetime" => 1531985983, "version" => 2), "wxapphaodian" => array("uniacid" => $_W["uniacid"], "name" => "自定义DIY", "type" => "5", "data" => "eyJwYWdlIjp7InR5cGUiOiI0IiwidGl0bGUiOiJcdTU5N2RcdTVlOTciLCJuYW1lIjoiXHU1OTdkXHU1ZTk3XHU5ZWQ4XHU4YmE0XHU5ODc1XHU5NzYyIiwidGh1bWIiOiIiLCJkZXNjIjoiIiwia2V5d29yZCI6IiIsImJhY2tncm91bmQiOiIjRjNGM0YzIiwiZGl5Z290b3AiOiIwIiwibmF2aWdhdGlvbmJhY2tncm91bmQiOiIjZmYyYjRkIiwibmF2aWdhdGlvbnRleHRjb2xvciI6IiNmZmZmZmYiLCJkaXltZW51IjoiLTEiLCJmb2xsb3diYXIiOiIwIn0sIml0ZW1zIjp7Ik0xNTQ1MDM3MDM3OTc3Ijp7InBhcmFtcyI6eyJsb2NhdGlvbiI6Ilx1NWI5YVx1NGY0ZCIsInRleHQiOiJcdThiZjdcdThmOTNcdTUxNjVcdTU1NDZcdTYyMzdcdTU0MGRcdTc5ZjAiLCJsaW5rdG8iOiIyIiwibGluayI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvc2VhcmNoIn0sInN0eWxlIjp7ImxvY3N0eWxlIjoicmFkaXVzIiwic2VhcmNoc3R5bGUiOiJyYWRpdXMiLCJmaXhlZGJhY2tncm91bmQiOiIjZmYyZDRiIiwibG9jYmFja2dyb3VuZCI6IiM5OTk5OTkiLCJzZWFyY2hiYWNrZ3JvdW5kIjoiI2Y0ZjRmNCIsImxvY2NvbG9yIjoiI2ZmZmZmZiIsInNlYXJjaGNvbG9yIjoiIzY1NjU2NSJ9LCJtYXgiOiIxIiwiaXN0b3AiOiIxIiwiaWQiOiJmaXhlZHNlYXJjaCJ9LCJNMTU0NTAzNzEyODIxNSI6eyJwYXJhbXMiOnsicGljdHVyZWRhdGEiOiI0IiwiaGFzX2dvaG9tZSI6InRydWUifSwic3R5bGUiOnsicGFkZGluZ3RvcCI6IjAiLCJwYWRkaW5nbGVmdCI6IjAiLCJkb3RiYWNrZ3JvdW5kIjoiI2ZmMmQ0YiIsImJhY2tncm91bmQiOiIjZmFmYWZhIn0sImRhdGEiOnsiQzEyODcwMzgyNzciOnsibGlua3VybCI6bnVsbCwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL1k1ZWFqMjNuYUx6MmFtNThBWXVBVjJ2ZWFBT0xyUi5qcGcifSwiQzEzNzI3OTgxMTEiOnsibGlua3VybCI6bnVsbCwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL24weng0NGV2cXUwTW5YdkROYXc3em0wZUEwZUREWS5qcGcifX0sImlkIjoicGljdHVyZSJ9LCJNMTU0NTAzNzE5MDM1NSI6eyJwYXJhbXMiOnsic2hvd3R5cGUiOiIxIiwic2hvd2RvdCI6IjEiLCJyb3dudW0iOiI1IiwicGFnZW51bSI6IjEwIiwibmF2c2RhdGEiOiI0IiwibmF2c251bSI6IjUwIiwiaGFzX2dvaG9tZSI6InRydWUifSwic3R5bGUiOnsibWFyZ2ludG9wIjoiMCIsIm5hdnN0eWxlIjoiIiwiZG90YmFja2dyb3VuZCI6IiNmZjJkNGIifSwiZGF0YSI6eyJDMTMwODMzNzQ1MCI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MSIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTUyMDZcdTdjN2IxIiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL001Nnk0NjU1djY1Ym42MTNwNTZaNjNaUlJybmh2Ti5wbmcifSwiQzEyOTgwODgzMTgiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvY2F0ZWdvcnk/Y2lkPTMiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMyIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzA3XC9NNTZ5NDY1NXY2NWJuNjEzcDU2WjYzWlJScm5odk4ucG5nIn0sIkMxMzc5MDkyNzU1Ijp7Imxpbmt1cmwiOiJcL2dvaG9tZVwvcGFnZXNcL2hhb2RpYW5cL2NhdGVnb3J5P2NpZD01IiwidGV4dCI6Ilx1NTk3ZFx1NWU5N1x1NTIwNlx1N2M3YjUiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTM4NTc2MjgyNSI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9NyIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTUyMDZcdTdjN2I3IiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL001Nnk0NjU1djY1Ym42MTNwNTZaNjNaUlJybmh2Ti5wbmcifSwiQzEyMDkzNzUxMzIiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvY2F0ZWdvcnk/Y2lkPTE1IiwidGV4dCI6Ilx1NTk3ZFx1NWU5N1x1NTIwNlx1N2M3YjIiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTE5ODUyNTMxOCI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MTciLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiNCIsImltZ3VybCI6Imh0dHA6XC9cL21pbmVcL3dlN1wvYXR0YWNobWVudFwvXC9pbWFnZXNcLzFcLzIwMThcLzA3XC9NNTZ5NDY1NXY2NWJuNjEzcDU2WjYzWlJScm5odk4ucG5nIn0sIkMxMTExNTM5MDkyIjp7Imxpbmt1cmwiOiJcL2dvaG9tZVwvcGFnZXNcL2hhb2RpYW5cL2NhdGVnb3J5P2NpZD0xOCIsInRleHQiOiJcdTU5N2RcdTVlOTdcdTUyMDZcdTdjN2I2IiwiaW1ndXJsIjoiaHR0cDpcL1wvbWluZVwvd2U3XC9hdHRhY2htZW50XC9cL2ltYWdlc1wvMVwvMjAxOFwvMDdcL001Nnk0NjU1djY1Ym42MTNwNTZaNjNaUlJybmh2Ti5wbmcifSwiQzExNTc2NTM5MzAiOnsibGlua3VybCI6IlwvZ29ob21lXC9wYWdlc1wvaGFvZGlhblwvY2F0ZWdvcnk/Y2lkPTE5IiwidGV4dCI6Ilx1NTk3ZFx1NWU5N1x1NTIwNlx1N2M3YjgiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTA3NzU3NTU0NSI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjAiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTAiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTIxMDI4ODY2OSI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjEiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTIiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTA4NjY2MDg1NiI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjIiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTQiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9LCJDMTEzNjk4MDQ2NyI6eyJsaW5rdXJsIjoiXC9nb2hvbWVcL3BhZ2VzXC9oYW9kaWFuXC9jYXRlZ29yeT9jaWQ9MjMiLCJ0ZXh0IjoiXHU1OTdkXHU1ZTk3XHU1MjA2XHU3YzdiMTYiLCJpbWd1cmwiOiJodHRwOlwvXC9taW5lXC93ZTdcL2F0dGFjaG1lbnRcL1wvaW1hZ2VzXC8xXC8yMDE4XC8wN1wvTTU2eTQ2NTV2NjVibjYxM3A1Nlo2M1pSUnJuaHZOLnBuZyJ9fSwiaWQiOiJuYXZzIiwiZGF0YV9udW0iOjEyLCJyb3ciOjJ9LCJNMTU0NTAzNzM4NjI5OCI6eyJwYXJhbXMiOnsiYnRudGV4dCI6Ilx1NjIxMVx1ODk4MVx1NTE2NVx1OWE3YiIsImltZ3VybCI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvc3RhdGljXC9pbWdcL25ld19zZXR0bGUucG5nIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjAiLCJiYWNrZ3JvdW5kY29sb3IiOiIjZmZmZmZmIiwibmV3c2NvbG9yIjoiIzMzMzMzMyIsInRpdGxlY29sb3IiOiIjZmYyZDRiIiwiYnRuY29sb3IiOiIjZmZmZmZmIiwiYnRuYmFja2dyb3VuZCI6IiNmZjJkNGIifSwiaWQiOiJoYW9kaWFuU2V0dGxlIn0sIk0xNTQ1MDM3MzAwODk5Ijp7InBhcmFtcyI6eyJzaG93ZGlzY291bnQiOiIxIn0sInN0eWxlIjp7Im1hcmdpbnRvcCI6IjEwIiwiYmFja2dyb3VuZGNvbG9yIjoiI2ZmZmZmZiIsInN0b3JldGl0bGVjb2xvciI6IiMzMzMzMzMiLCJzdGFyc2NvbG9yIjoiI2ZmMmQ0YiIsInN0YXJ0ZXh0Y29sb3IiOiIjOTk5OTk5Iiwic3RvcmVkaXN0YW5jZWNvbG9yIjoiI2ZmMmQ0YiIsInN0b3JldGFnc3RleHRjb2xvciI6IiNmZjJkNGIiLCJ0YWdzYmFja2dyb3VuZGNvbG9yIjoiI0ZGRTNFNyJ9LCJkYXRhIjp7IkMxNTQ1MDM3MzAwOTAwIjp7ImxvZ28iOiIuLlwvYWRkb25zXC93ZTdfd21hbGxcL3BsdWdpblwvZGl5cGFnZVwvc3RhdGljXC9pbWdcL2RlZmF1bHRcL3N0b3JlLTEuanBnIiwidGl0bGUiOiJcdTk1ZThcdTVlOTdcdTU0MGRcdTc5ZjAiLCJhY3Rpdml0eSI6eyJpdGVtcyI6eyJDMDEyMzQ1Njc4OTEwMSI6eyJ0eXBlIjoiZGlzY291bnQiLCJ0aXRsZSI6Ilx1NmVlMTM1XHU1MWNmMTI7XHU2ZWUxNjBcdTUxY2YyMCJ9LCJDMDEyMzQ1Njc4OTEwMiI6eyJ0eXBlIjoiY291cG9uQ29sbGVjdCIsInRpdGxlIjoiXHU1M2VmXHU5ODg2Mlx1NTE0M1x1NGVlM1x1OTFkMVx1NTIzOCJ9fSwibnVtIjoiMiJ9fSwiQzE1NDUwMzczMDA5MDIiOnsibG9nbyI6Ii4uXC9hZGRvbnNcL3dlN193bWFsbFwvcGx1Z2luXC9kaXlwYWdlXC9zdGF0aWNcL2ltZ1wvZGVmYXVsdFwvc3RvcmUtMi5qcGciLCJ0aXRsZSI6Ilx1OTVlOFx1NWU5N1x1NTQwZFx1NzlmMCIsImFjdGl2aXR5Ijp7Iml0ZW1zIjp7IkMwMTIzNDU2Nzg5MTAxIjp7InR5cGUiOiJkaXNjb3VudCIsInRpdGxlIjoiXHU2ZWUxMzVcdTUxY2YxMjtcdTZlZTE2MFx1NTFjZjIwIn0sIkMwMTIzNDU2Nzg5MTAyIjp7InR5cGUiOiJjb3Vwb25Db2xsZWN0IiwidGl0bGUiOiJcdTUzZWZcdTk4ODYyXHU1MTQzXHU0ZWUzXHU5MWQxXHU1MjM4In19LCJudW0iOiIyIn19LCJDMTU0NTAzNzMwMDkwMyI6eyJsb2dvIjoiLi5cL2FkZG9uc1wvd2U3X3dtYWxsXC9wbHVnaW5cL2RpeXBhZ2VcL3N0YXRpY1wvaW1nXC9kZWZhdWx0XC9zdG9yZS0zLmpwZyIsInRpdGxlIjoiXHU5NWU4XHU1ZTk3XHU1NDBkXHU3OWYwIiwiYWN0aXZpdHkiOnsiaXRlbXMiOnsiQzAxMjM0NTY3ODkxMDEiOnsidHlwZSI6ImRpc2NvdW50IiwidGl0bGUiOiJcdTZlZTEzNVx1NTFjZjEyO1x1NmVlMTYwXHU1MWNmMjAifSwiQzAxMjM0NTY3ODkxMDIiOnsidHlwZSI6ImNvdXBvbkNvbGxlY3QiLCJ0aXRsZSI6Ilx1NTNlZlx1OTg4NjJcdTUxNDNcdTRlZTNcdTkxZDFcdTUyMzgifX0sIm51bSI6IjIifX19LCJpZCI6Imhhb2RpYW5MaXN0In19fQ==", "updatetime" => 1531985983, "version" => 2));
    return $pages[$type];
}
function get_wxapp_goodsTab($item, $mobile = false)
{
    global $_W;
    if (!empty($item["data"])) {
        foreach ($item["data"] as $goodsTabIndex => &$goodsTabItem) {
            $goodsTabItem["imgTitle"] = tomedia($goodsTabItem["imgTitle"]);
            $tabGoods = array();
            if ($goodsTabItem["goodsdata"] == "0") {
                if (!empty($goodsTabItem["goods"])) {
                    $goodsIds = array();
                    foreach ($goodsTabItem["goods"] as $goodsItem) {
                        $goodsIds[] = $goodsItem["goods_id"];
                    }
                    $goodsIdsStr = implode(",", $goodsIds);
                    $condition = " where a.uniacid = :uniacid and a.status = 1 and a.id in (" . $goodsIdsStr . ") order by FIELD(a.`id`, " . $goodsIdsStr . ") ";
                    $params = array(":uniacid" => $_W["uniacid"]);
                    $goods = pdo_fetchall("select a.*, b.id as store_id, b.agentid, b.title as store_title, b.logo, b.send_price, b.delivery_price, b.delivery_time from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition, $params);
                    if (!empty($goods)) {
                        foreach ($goods as $val) {
                            if ($val["sid"] != $val["store_id"]) {
                                continue;
                            }
                            $childid = rand(1000000000, 9999999999.0);
                            $childid = "C" . $childid;
                            $tabGoods[$childid] = array("sid" => $val["sid"], "goods_id" => $val["id"], "thumb" => tomedia($val["thumb"]), "price" => $val["price"], "old_price" => $val["old_price"] ? $val["old_price"] : $val["price"], "title" => $val["title"], "store_title" => $val["store_title"], "discount" => $val["old_price"] == 0 ? 0 : round($val["price"] / $val["old_price"] * 10, 1), "sailed" => $val["sailed"], "comment_good_percent" => $val["comment_total"] == 0 ? 0 : round($val["comment_good"] / $val["comment_total"] * 100, 2) . "%", "store" => array("id" => $val["sid"], "title" => $val["store_title"], "logo" => tomedia($val["logo"]), "send_price" => $val["send_price"], "delivery_price" => $val["delivery_price"], "delivery_time" => $val["delivery_time"]), "svip_status" => $val["svip_status"], "svip_price" => $val["svip_price"]);
                            if ($val["svip_status"] == 1) {
                                $tabGoods[$childid]["price"] = $val["svip_price"];
                                $tabGoods[$childid]["discount"] = round($val["svip_price"] / $tabGoods[$childid]["old_price"] * 10, 1);
                            }
                        }
                    }
                }
            } else {
                $condition = " where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1 ";
                $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
                $goods = pdo_fetchall("select a.discount_price, a.goods_id, a.discount_available_total, b.* from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id " . $condition . " order by a.mall_displayorder desc ", $params);
                if (!empty($goods)) {
                    $stores = pdo_fetchall("select distinct(a.sid),b.id as store_id,b.is_rest, b.title as store_title, b.logo, b.send_price, b.delivery_price, b.delivery_time from  " . tablename("tiny_wmall_activity_bargain") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "sid");
                    foreach ($goods as &$val) {
                        if (empty($stores[$val["sid"]]["store_id"])) {
                            continue;
                        }
                        $childid = rand(1000000000, 9999999999.0);
                        $childid = "C" . $childid;
                        $tabGoods[$childid] = array("sid" => $val["sid"], "goods_id" => $val["goods_id"], "thumb" => tomedia($val["thumb"]), "price" => $val["discount_price"], "old_price" => $val["old_price"] ? $val["old_price"] : $val["price"], "title" => $val["title"], "store_title" => $stores[$val["sid"]]["store_title"], "discount" => $val["old_price"] == 0 ? 0 : round($val["discount_price"] / $val["old_price"] * 10, 1), "sailed" => $val["sailed"], "comment_good_percent" => $val["comment_total"] == 0 ? 0 : round($val["comment_good"] / $val["comment_total"] * 100, 2) . "%", "store" => array("id" => $stores[$val["sid"]]["store_id"], "title" => $stores[$val["sid"]]["store_title"], "logo" => tomedia($stores[$val["sid"]]["logo"]), "send_price" => $stores[$val["sid"]]["send_price"], "delivery_time" => $stores[$val["sid"]]["delivery_time"], "delivery_price" => $stores[$val["sid"]]["delivery_price"]), "svip_status" => $val["svip_status"], "svip_price" => $val["svip_price"]);
                        if ($val["svip_status"] == 1) {
                            $tabGoods[$childid]["price"] = $val["svip_price"];
                            $tabGoods[$childid]["discount"] = round($val["svip_price"] / $tabGoods[$childid]["old_price"] * 10, 1);
                        }
                    }
                }
            }
            if (!empty($tabGoods)) {
                $goodsTabItem["goods"] = $tabGoods;
            } else {
                unset($item["data"][$goodsTabIndex]);
            }
        }
    }
    return $item["data"];
}
function get_wxapp_waimai_goods($item, $mobile = false)
{
    global $_W;
    if ($item["params"]["goodsdata"] == "0") {
        if (!empty($item["data"]) && is_array($item["data"])) {
            $goodsids = array();
            foreach ($item["data"] as $data) {
                if (!empty($data["goods_id"])) {
                    $goodsids[] = $data["goods_id"];
                }
            }
            if (!empty($goodsids)) {
                $item["data"] = array();
                $goodsids_str = implode(",", $goodsids);
                $goods = pdo_fetchall("select * from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and status = 1 and id in (" . $goodsids_str . ") order by FIELD(`id`, " . $goodsids_str . ")", array(":uniacid" => $_W["uniacid"]));
                if (!empty($goods)) {
                    foreach ($goodsids as $goodsid) {
                        foreach ($goods as $good) {
                            if ($good["id"] == $goodsid) {
                                $childid = rand(1000000000, 9999999999.0);
                                $childid = "C" . $childid;
                                $item["data"][$childid] = array("goods_id" => $good["id"], "sid" => $good["sid"], "thumb" => tomedia($good["thumb"]), "title" => $good["title"], "price" => $good["price"], "old_price" => $good["old_price"] ? $good["old_price"] : $good["price"], "sailed" => $good["sailed"], "total" => $good["total"] != -1 ? $good["total"] : "无限", "discount" => $good["old_price"] == 0 ? 0 : round($good["price"] / $good["old_price"] * 10, 1), "comment_good_percent" => $good["comment_total"] == 0 ? 0 : round($good["comment_good"] / $good["comment_total"] * 100, 2) . "%", "svip_status" => $good["svip_status"], "svip_price" => $good["svip_price"]);
                                if ($good["svip_status"] == 1) {
                                    $item["data"][$childid]["price"] = $good["svip_price"];
                                    $item["data"][$childid]["discount"] = round($good["svip_price"] / $item["data"][$childid]["old_price"] * 10, 1);
                                }
                                $item["data"][$childid]["store"] = pdo_fetch("select id as store_id, title as store_title, logo, send_price, delivery_price, delivery_time from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $good["sid"]));
                                $item["data"][$childid]["store"]["price"] = store_order_condition($good["sid"]);
                                $item["data"][$childid]["store"]["logo"] = tomedia($item["data"][$childid]["store"]["logo"]);
                                $item["data"][$childid]["store_title"] = $item["data"][$childid]["store"]["store_title"];
                                if ($item["data"][$childid]["store"]["store_id"]) {
                                    $item["data"][$childid]["store"] = array("id" => $item["data"][$childid]["store"]["store_id"] ? $item["data"][$childid]["store"]["store_id"] : 0, "title" => $item["data"][$childid]["store"]["store_title"], "logo" => $item["data"][$childid]["store"]["logo"], "send_price" => $item["data"][$childid]["store"]["price"]["send_price"], "delivery_time" => $item["data"][$childid]["store"]["delivery_time"], "price" => $item["data"][$childid]["store"]["price"], "delivery_price" => $item["data"][$childid]["store"]["price"]["delivery_price"]);
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        if ($item["params"]["goodsdata"] == "1") {
            if (empty($mobile)) {
                return $item["data"];
            }
            $item["data"] = array();
            $condition = " where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1";
            $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
            $limit = intval($item["params"]["goodsnum"]);
            $limit = $limit ? $limit : 20;
            $goods = pdo_fetchall("select a.discount_price,a.goods_id,a.discount_available_total,b.* from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id " . $condition . " order by a.mall_displayorder desc limit " . $limit, $params);
            if (!empty($goods)) {
                $stores = pdo_fetchall("select distinct(a.sid),b.id as store_id,b.is_rest, b.title as store_title, b.logo, b.send_price, b.delivery_price, b.delivery_time from  " . tablename("tiny_wmall_activity_bargain") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.status = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "sid");
                foreach ($goods as &$good) {
                    $childid = rand(1000000000, 9999999999.0);
                    $childid = "C" . $childid;
                    $item["data"][$childid] = array("goods_id" => $good["id"], "sid" => $good["sid"], "store_title" => $stores[$good["sid"]]["store_title"], "thumb" => tomedia($good["thumb"]), "title" => $good["title"], "price" => $good["discount_price"], "old_price" => $good["old_price"] ? $good["old_price"] : $good["price"], "sailed" => $good["sailed"], "total" => $good["discount_available_total"] != -1 ? $good["discount_available_total"] : "无限", "discount" => $good["old_price"] == 0 ? 0 : round($good["discount_price"] / $good["old_price"] * 10, 1), "comment_good_percent" => $good["comment_total"] == 0 ? 0 : round($good["comment_good"] / $good["comment_total"] * 100, 2) . "%", "store" => array());
                    if ($stores[$good["sid"]]["store_id"]) {
                        $item["data"][$childid]["store"] = array("id" => $stores[$good["sid"]]["store_id"], "title" => $stores[$good["sid"]]["store_title"], "logo" => tomedia($stores[$good["sid"]]["logo"]), "send_price" => $stores[$good["sid"]]["send_price"], "delivery_time" => $stores[$good["sid"]]["delivery_time"], "price" => store_order_condition($good["sid"]), "delivery_price" => $item["data"][$childid]["store"]["price"]["delivery_price"]);
                    }
                }
            }
        }
    }
    return $item["data"];
}
function get_wxapp_waimai_recommend_store($item, $mobile = false)
{
    global $_W;
    if ($item["params"]["storedata"] == "0") {
        if (!empty($item["data"]) && is_array($item["data"])) {
            $storeids = array();
            foreach ($item["data"] as $data) {
                if (!empty($data["store_id"])) {
                    $storeids[] = $data["store_id"];
                }
            }
            if (!empty($storeids)) {
                $item["data"] = array();
                $storeids_str = implode(",", $storeids);
                if ($mobile || 0 < $_W["agentid"]) {
                    $condition = " where uniacid = :uniacid and agentid = :agentid and status = 1 and id in (" . $storeids_str . ") order by is_rest asc, displayorder desc";
                    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
                } else {
                    $condition = " where uniacid = :uniacid and status = 1 and id in (" . $storeids_str . ") order by is_rest asc, displayorder desc";
                    $params = array(":uniacid" => $_W["uniacid"]);
                }
                $stores = pdo_fetchall("select id, title, logo, is_rest, forward_mode, forward_url from " . tablename("tiny_wmall_store") . $condition, $params);
            }
        }
    } else {
        if ($item["params"]["storedata"] == "1") {
            $limit = intval($item["params"]["storenum"]);
            $limit = $limit ? $limit : 20;
            if ($mobile || 0 < $_W["agentid"]) {
                $condition = " where uniacid = :uniacid and agentid = :agentid and status = 1 and is_recommend = 1 order by is_rest asc, displayorder desc limit " . $limit;
                $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
            } else {
                $condition = " where uniacid = :uniacid and status = 1 and is_recommend = 1 order by is_rest asc, displayorder desc limit " . $limit;
                $params = array(":uniacid" => $_W["uniacid"]);
            }
            $stores = pdo_fetchall("select id, title, logo, forward_mode, forward_url from " . tablename("tiny_wmall_store") . $condition, $params);
        }
    }
    $item["data"] = array();
    if (!empty($stores)) {
        foreach ($stores as &$row) {
            $row["url"] = store_forward_url($row["id"], $row["forward_mode"], $row["forward_url"]);
            $row["store_id"] = $row["id"];
            $row["logo"] = tomedia($row["logo"]);
            $childid = rand(1000000000, 9999999999.0);
            $childid = "C" . $childid;
            $item["data"][$childid] = $row;
            unset($row);
        }
    }
    $item["data_num"] = count($item["data"]);
    if ($mobile && $item["params"]["showtype"] == 1 && $item["params"]["pagenum"] < count($item["data"])) {
        $item["data"] = array_chunk($item["data"], $item["params"]["pagenum"]);
    }
    $result = array("data" => $item["data"], "data_num" => $item["data_num"]);
    return $result;
}
function get_wxapp_waimai_store($item, $mobile = false)
{
    global $_W;
    global $_GPC;
    $condition = " where uniacid = :uniacid and status = 1 and is_waimai = 1 ";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($mobile) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    if ($item["params"]["storedata"] == "0") {
        if (!empty($item["data"]) && is_array($item["data"])) {
            $storeids = array();
            foreach ($item["data"] as $data) {
                if (!empty($data["store_id"])) {
                    $storeids[] = $data["store_id"];
                }
            }
            if (!empty($storeids)) {
                $item["data"] = array();
                $storeids_str = implode(",", $storeids);
                $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from " . tablename("tiny_wmall_store") . $condition . " and id in (" . $storeids_str . ") order by FIELD(`is_rest`, 0, 1), FIELD(`id`, " . $storeids_str . ")", $params);
            }
        }
    } else {
        if ($item["params"]["storedata"] == "1") {
            if (empty($mobile)) {
                return $item["data"];
            }
            $limit = intval($item["params"]["storenum"]);
            $limit = $limit ? $limit : 20;
            $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url,business_hours from " . tablename("tiny_wmall_store") . (string) $condition . " and is_recommend = 1 order by is_rest asc, displayorder desc limit " . $limit, $params);
        } else {
            if ($item["params"]["storedata"] == "2") {
                $limit = intval($item["params"]["storenum"]);
                $limit = $limit ? $limit : 20;
                if (0 < $item["params"]["categoryid"]) {
                    $condition .= " and (cate_parentid1 = :cid or cate_parentid2 = :cid or cate_childid1 = :cid or cate_childid2 = :cid)";
                    $params[":cid"] = $item["params"]["categoryid"];
                }
                $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from " . tablename("tiny_wmall_store") . $condition . " order by is_rest asc, displayorder desc limit " . $limit, $params);
            } else {
                if ($item["params"]["storedata"] == "3") {
                    unset($item["data"]);
                    $store_activity = pdo_getall("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "status" => 1, "type" => $item["params"]["activitytype"]), array("sid"), "sid");
                    if (!empty($store_activity)) {
                        $store_ids = array_keys($store_activity);
                        $storeids_str = implode(",", $store_ids);
                        $condition .= " and id in (" . $storeids_str . ")";
                        $limit = intval($item["params"]["storenum"]);
                        $limit = $limit ? $limit : 20;
                        $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from " . tablename("tiny_wmall_store") . $condition . " order by is_rest asc, displayorder desc limit " . $limit, $params);
                    }
                }
            }
        }
    }
    $item["data"] = array();
    if (!empty($stores)) {
        $_config_mall = $_W["we7_wmall"]["config"]["mall"];
        if (empty($_config_mall["delivery_title"])) {
            $_config_mall["delivery_title"] = "平台专送";
        }
        $store_label = category_store_label();
        foreach ($stores as &$row) {
            $row["url"] = store_forward_url($row["id"], $row["forward_mode"], $row["forward_url"]);
            $row["store_id"] = $row["id"];
            if (0 < $row["label"]) {
                $row["label_color"] = $store_label[$row["label"]]["color"];
                $row["label_cn"] = $store_label[$row["label"]]["title"];
            }
            $row["logo"] = tomedia($row["logo"]);
            $row["price"] = store_order_condition($row["id"]);
            $row["send_price"] = $row["price"]["send_price"];
            $row["delivery_price"] = $row["price"]["delivery_price"];
            if ($row["delivery_mode"] == 2 && $row["delivery_type"] != 2) {
                $row["delivery_title"] = $_config_mall["delivery_title"];
            }
            $row["score"] = floatval($row["score"]);
            $row["score_cn"] = round($row["score"] / 5, 2) * 100;
            $row["hot_goods"] = array();
            $hot_goods = pdo_fetchall("select id,title,price,old_price,thumb,svip_status,svip_price from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3", array(":uniacid" => $_W["uniacid"], ":sid" => $row["id"]));
            if (!empty($hot_goods)) {
                foreach ($hot_goods as &$goods) {
                    $goods["thumb"] = tomedia($goods["thumb"]);
                    if (0 < $goods["old_price"] && $goods["price"] < $goods["old_price"]) {
                        $old_price = $goods["old_price"];
                        $goods["discount"] = round($goods["price"] / $goods["old_price"] * 10, 1);
                    } else {
                        $old_price = $goods["price"];
                        $goods["old_price"] = 0;
                        $goods["discount"] = 0;
                    }
                    if ($goods["svip_status"] == 1) {
                        $goods["price"] = $goods["svip_price"];
                        $goods["old_price"] = $old_price;
                        $goods["discount"] = round($goods["price"] / $old_price * 10, 1);
                    }
                    $childid = rand(1000000000, 9999999999.0);
                    $childid = "C" . $childid;
                    $row["hot_goods"][$childid] = $goods;
                }
                $row["hot_goods_num"] = count($row["hot_goods"]);
                unset($hot_goods);
            }
            $row["activity"] = array();
            $activitys = store_fetch_activity($row["id"]);
            if (!empty($activitys["items"])) {
                if (!empty($activitys["items"]["zhunshibao"])) {
                    $row["zhunshibao_cn"] = "准时宝";
                    unset($activitys["items"]["zhunshibao"]);
                }
                foreach ($activitys["items"] as $avtivity_item) {
                    if (empty($avtivity_item["title"])) {
                        continue;
                    }
                    $row["activity"]["items"][] = array("type" => $avtivity_item["type"], "title" => $avtivity_item["title"]);
                }
                $row["activity"]["num"] = $activitys["num"];
                $row["activity"]["is_show_all"] = 0;
                $row["activity"]["labels"] = $activitys["labels"];
                $row["activity"]["labels_num"] = count($row["activity"]["labels"]);
                unset($activitys);
            }
            $row["business_hours"] = iunserializer($row["business_hours"]);
            if (!$row["is_rest"] && !store_is_in_business_hours($row["business_hours"])) {
                $row["is_rest_reserve"] = 1;
                $rest_order_info = store_rest_start_delivery_time($row);
                $row["rest_reserve_cn"] = $rest_order_info["delivery_time_cn"];
            }
            unset($row["business_hours"]);
            $childid = rand(1000000000, 9999999999.0);
            $childid = "C" . $childid;
            $item["data"][$childid] = $row;
            unset($row);
        }
    }
    return $item["data"];
}
function get_wxapp_storesTab($item, $mobile = false)
{
    global $_W;
    $condition = " where uniacid = :uniacid and status = 1 and is_waimai = 1 ";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($mobile) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    if (!empty($item["data"])) {
        foreach ($item["data"] as $storesTabIndex => &$storesTabItem) {
            $storesTabItem["imgTitle"] = tomedia($storesTabItem["imgTitle"]);
            $tabStores = array();
            $limit = intval($storesTabItem["storenum"]) ? intval($storesTabItem["storenum"]) : 20;
            if ($storesTabItem["storedata"] == "0") {
                $storesIds = array();
                foreach ($storesTabItem["stores"] as $storesItem) {
                    $storesIds[] = $storesItem["store_id"];
                }
                $storesIdsStr = implode(",", $storesIds);
                $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from " . tablename("tiny_wmall_store") . $condition . " and id in (" . $storesIdsStr . ") order by FIELD(`is_rest`, 0, 1), FIELD(`id`, " . $storesIdsStr . ")", $params);
            } else {
                if ($storesTabItem["storedata"] == "1") {
                    if (empty($mobile)) {
                        return $item["data"];
                    }
                    $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score, is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url,business_hours from " . tablename("tiny_wmall_store") . (string) $condition . " and is_recommend = 1 order by is_rest asc, displayorder desc limit " . $limit, $params);
                } else {
                    if ($storesTabItem["storedata"] == "2") {
                        if (0 < $storesTabItem["categoryid"]) {
                            $condition .= " and (cate_parentid1 = :cid or cate_parentid2 = :cid or cate_childid1 = :cid or cate_childid2 = :cid)";
                            $params[":cid"] = $storesTabItem["categoryid"];
                        }
                        $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from " . tablename("tiny_wmall_store") . $condition . " order by is_rest asc, displayorder desc limit " . $limit, $params);
                    } else {
                        if ($storesTabItem["storedata"] == "3") {
                            $store_activity = pdo_getall("tiny_wmall_store_activity", array("uniacid" => $_W["uniacid"], "status" => 1, "type" => $storesTabItem["activitytype"]), array("sid"), "sid");
                            if (!empty($store_activity)) {
                                $store_ids = array_keys($store_activity);
                                $storeids_str = implode(",", $store_ids);
                                $condition .= " and id in (" . $storeids_str . ")";
                                $stores = pdo_fetchall("select id, title, logo, delivery_free_price, score,is_rest,delivery_time,sailed,delivery_mode,label, forward_mode, forward_url, business_hours from " . tablename("tiny_wmall_store") . $condition . " order by is_rest asc, displayorder desc limit " . $limit, $params);
                            }
                        }
                    }
                }
            }
            if (!empty($stores)) {
                $_config_mall = $_W["we7_wmall"]["config"]["mall"];
                if (empty($_config_mall["delivery_title"])) {
                    $_config_mall["delivery_title"] = "平台专送";
                }
                $store_label = category_store_label();
                foreach ($stores as &$row) {
                    $childid = rand(1000000000, 9999999999.0);
                    $childid = "C" . $childid;
                    $price = store_order_condition($row["id"]);
                    $tabStores[$childid] = array("title" => $row["title"], "delivery_time" => $row["delivery_time"], "sailed" => $row["sailed"], "url" => store_forward_url($row["id"], $row["forward_mode"], $row["forward_url"]), "store_id" => $row["id"], "logo" => tomedia($row["logo"]), "send_price" => $price["send_price"], "delivery_price" => $price["delivery_price"], "score" => floatval($row["score"]), "score_cn" => round($row["score"] / 5, 2) * 100);
                    if (0 < $row["label"]) {
                        $tabStores[$childid]["label_color"] = $store_label[$row["label"]]["color"];
                        $tabStores[$childid]["label_cn"] = $store_label[$row["label"]]["title"];
                    }
                    if ($row["delivery_mode"] == 2 && $row["delivery_type"] != 2) {
                        $tabStores[$childid]["delivery_title"] = $_config_mall["delivery_title"];
                    }
                    $row["activity"] = array();
                    $activitys = store_fetch_activity($row["id"]);
                    if (!empty($activitys["items"])) {
                        if (!empty($activitys["items"]["zhunshibao"])) {
                            $tabStores[$childid]["zhunshibao_cn"] = "准时宝";
                            unset($activitys["items"]["zhunshibao"]);
                        }
                        foreach ($activitys["items"] as $avtivity_item) {
                            if (empty($avtivity_item["title"])) {
                                continue;
                            }
                            $row["activity"]["items"][] = array("type" => $avtivity_item["type"], "title" => $avtivity_item["title"]);
                        }
                        $row["activity"]["num"] = $activitys["num"];
                        $row["activity"]["is_show_all"] = 0;
                        $row["activity"]["labels"] = $activitys["labels"];
                        $row["activity"]["labels_num"] = count($row["activity"]["labels"]);
                        $tabStores[$childid]["activity"] = $row["activity"];
                        unset($activitys);
                    }
                    $tabStores[$childid]["business_hours"] = iunserializer($row["business_hours"]);
                    if (!$row["is_rest"] && !store_is_in_business_hours($row["business_hours"])) {
                        $tabStores[$childid]["is_rest_reserve"] = 1;
                        $rest_order_info = store_rest_start_delivery_time($row);
                        $tabStores[$childid]["rest_reserve_cn"] = $rest_order_info["delivery_time_cn"];
                    }
                    unset($row["business_hours"]);
                    $hot_goods = pdo_fetchall("select id,title,price,old_price,thumb,svip_status,svip_price from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3", array(":uniacid" => $_W["uniacid"], ":sid" => $row["id"]));
                    if (!empty($hot_goods)) {
                        foreach ($hot_goods as &$goods) {
                            $goods["thumb"] = tomedia($goods["thumb"]);
                            if (0 < $goods["old_price"] && $goods["price"] < $goods["old_price"]) {
                                $old_price = $goods["old_price"];
                                $goods["discount"] = round($goods["price"] / $goods["old_price"] * 10, 1);
                            } else {
                                $old_price = $goods["price"];
                                $goods["old_price"] = 0;
                                $goods["discount"] = 0;
                            }
                            if ($goods["svip_status"] == 1) {
                                $goods["price"] = $goods["svip_price"];
                                $goods["old_price"] = $old_price;
                                $goods["discount"] = round($goods["price"] / $old_price * 10, 1);
                            }
                            $hot_childid = rand(1000000000, 9999999999.0);
                            $hot_childid = "C" . $hot_childid;
                            $row["hot_goods"][$hot_childid] = $goods;
                        }
                        $tabStores[$childid]["hot_goods"] = $row["hot_goods"];
                        $tabStores[$childid]["hot_goods_num"] = count($row["hot_goods"]);
                        unset($hot_goods);
                    }
                    unset($row);
                }
            }
            if (!empty($tabStores)) {
                $storesTabItem["stores"] = $tabStores;
            } else {
                unset($item["data"][$storesTabIndex]);
            }
        }
    }
    return $item["data"];
}
function get_wxapp_brand_store($item, $mobile = false)
{
    global $_W;
    global $_GPC;
    $condition = " where uniacid = :uniacid and status = 1 and is_waimai = 1 ";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($mobile || 0 < $_W["agentid"]) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    $contents = array();
    if ($item["params"]["storedata"] == "0" && !empty($item["data"]) && is_array($item["data"])) {
        $storeids = array();
        foreach ($item["data"] as $data) {
            if (!empty($data["store_id"])) {
                $storeids[] = $data["store_id"];
                $contents[$data["store_id"]] = $data["content"];
            }
        }
        if (!empty($storeids)) {
            $item["data"] = array();
            $storeids_str = implode(",", $storeids);
            $stores = pdo_fetchall("select id, title, logo, is_rest, forward_mode, forward_url, `data` from " . tablename("tiny_wmall_store") . $condition . " and id in (" . $storeids_str . ") order by FIELD(`id`, " . $storeids_str . "), is_rest asc", $params);
        }
    }
    $item["data"] = array();
    if (!empty($stores)) {
        foreach ($stores as &$row) {
            $row["url"] = store_forward_url($row["id"], $row["forward_mode"], $row["forward_url"]);
            $row["store_id"] = $row["id"];
            $row["logo"] = tomedia($row["logo"]);
            $row["content"] = $contents[$row["id"]];
            $row["data"] = iunserializer($row["data"]);
            $row["shopSign"] = tomedia($row["data"]["shopSign"]);
            $row["hot_goods"] = array();
            $hot_goods = pdo_fetchall("select id,title,price,old_price,thumb,svip_status,svip_price from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid and is_hot = 1 and status = 1 limit 3", array(":uniacid" => $_W["uniacid"], ":sid" => $row["id"]));
            if (!empty($hot_goods)) {
                foreach ($hot_goods as &$goods) {
                    $goods["thumb"] = tomedia($goods["thumb"]);
                    if (0 < $goods["old_price"] && $goods["price"] < $goods["old_price"]) {
                        $old_price = $goods["old_price"];
                        $goods["discount"] = round($goods["price"] / $goods["old_price"] * 10, 1);
                    } else {
                        $old_price = $goods["price"];
                        $goods["old_price"] = 0;
                        $goods["discount"] = 0;
                    }
                    if ($goods["svip_status"] == 1) {
                        $goods["price"] = $goods["svip_price"];
                        $goods["old_price"] = $old_price;
                        $goods["discount"] = round($goods["price"] / $old_price * 10, 1);
                    }
                    $childid = rand(1000000000, 9999999999.0);
                    $childid = "C" . $childid;
                    $row["hot_goods"][$childid] = $goods;
                }
                $row["hot_goods_num"] = count($row["hot_goods"]);
                unset($hot_goods);
            }
            $row["activity"] = array();
            $activitys = store_fetch_activity($row["id"]);
            if (!empty($activitys["items"])) {
                foreach ($activitys["items"] as $avtivity_item) {
                    if (empty($avtivity_item["title"])) {
                        continue;
                    }
                    $row["activity"]["items"][] = array("type" => $avtivity_item["type"], "title" => $avtivity_item["title"]);
                }
                $row["activity"]["num"] = $activitys["num"];
                $row["activity"]["is_show_all"] = 0;
                $row["activity"]["labels"] = $activitys["labels"];
                $row["activity"]["labels_num"] = count($row["activity"]["labels"]);
                unset($activitys);
            }
            $childid = rand(1000000000, 9999999999.0);
            $childid = "C" . $childid;
            $item["data"][$childid] = $row;
            unset($row);
        }
    }
    return $item["data"];
}
function get_wxapp_selftake_store($item, $mobile = false)
{
    global $_W;
    global $_GPC;
    $condition = " where uniacid = :uniacid and status = 1 and is_waimai = 1 and delivery_type > 1 ";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($mobile || 0 < $_W["agentid"]) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    $lat = trim($_GPC["lat"]) ? trim($_GPC["lat"]) : "37.80081";
    $lng = trim($_GPC["lng"]) ? trim($_GPC["lng"]) : "112.57543";
    if ($item["params"]["storedata"] == "1") {
        $limit = intval($item["params"]["storenum"]);
        $limit = $limit ? $limit : 10;
        $stores = pdo_fetchall("select id, title, logo, delivery_time, delivery_mode,forward_mode, forward_url, business_hours, \t\t\t\tROUND(\r\n\t\t\t\t\t6378.138 * 2 * ASIN(\r\n\t\t\t\t\t\tSQRT(\r\n\t\t\t\t\t\t\tPOW(\r\n\t\t\t\t\t\t\t\tSIN(\r\n\t\t\t\t\t\t\t\t\t(\r\n\t\t\t\t\t\t\t\t\t\t" . $lat . " * PI() / 180 - location_x * PI() / 180\r\n\t\t\t\t\t\t\t\t\t) / 2\r\n\t\t\t\t\t\t\t\t),\r\n\t\t\t\t\t\t\t\t2\r\n\t\t\t\t\t\t\t) + COS(" . $lat . " * PI() / 180) * COS(location_x * PI() / 180) * POW(\r\n\t\t\t\t\t\t\t\tSIN(\r\n\t\t\t\t\t\t\t\t\t(\r\n\t\t\t\t\t\t\t\t\t   " . $lng . "  * PI() / 180 - location_y * PI() / 180\r\n\t\t\t\t\t\t\t\t\t) / 2\r\n\t\t\t\t\t\t\t\t),\r\n\t\t\t\t\t\t\t\t2\r\n\t\t\t\t\t\t\t)\r\n\t\t\t\t\t\t)\r\n\t\t\t) * 1000) as distance from " . tablename("tiny_wmall_store") . $condition . " order by is_rest asc, displayorder desc limit " . $limit, $params);
    }
    $item["data"] = array();
    if (!empty($stores)) {
        foreach ($stores as &$row) {
            $row["url"] = store_forward_url($row["id"], $row["forward_mode"], $row["forward_url"]);
            $row["store_id"] = $row["id"];
            $row["logo"] = tomedia($row["logo"]);
            $row["price"] = store_order_condition($row["id"]);
            $row["send_price"] = $row["price"]["send_price"];
            $row["delivery_price"] = $row["price"]["delivery_price"];
            $row["distance"] = round($row["distance"] / 1000, 1);
            $row["activity"] = array();
            $activitys = store_fetch_activity($row["id"]);
            if (!empty($activitys["items"])) {
                foreach ($activitys["items"] as $avtivity_item) {
                    if (empty($avtivity_item["title"])) {
                        continue;
                    }
                    $row["activity"]["items"][] = array("type" => $avtivity_item["type"], "title" => $avtivity_item["title"]);
                }
                $row["activity"]["num"] = $activitys["num"];
                unset($activitys);
            }
            $childid = rand(1000000000, 9999999999.0);
            $childid = "C" . $childid;
            $item["data"][$childid] = $row;
            unset($row);
        }
    }
    return $item["data"];
}
function get_wxapp_notice($item, $mobile = false, $from = "wxapp")
{
    global $_W;
    if ($item["params"]["noticedata"] == 0 || $item["params"]["noticedata"] == 2) {
        if ($item["params"]["noticedata"] == 0) {
            $table = "tiny_wmall_notice";
            $keys = "id, title, displayorder, link, status, wxapp_link";
        } else {
            if ($item["params"]["noticedata"] == 2) {
                $table = "tiny_wmall_gohome_notice";
                $keys = "id, title, displayorder, status, wxapp_link";
            }
        }
        $condition = " where uniacid = :uniacid";
        $params = array(":uniacid" => $_W["uniacid"]);
        if ($item["params"]["noticedata"] == 0) {
            $condition .= " and type = :type";
            $params[":type"] = "member";
        }
        if ($mobile || 0 < $_W["agentid"]) {
            $condition .= " and agentid = :agentid";
            $params[":agentid"] = $_W["agentid"];
        }
        $noticenum = $item["params"]["noticenum"];
        $notice = pdo_fetchall("select " . $keys . " from " . tablename($table) . $condition . " and status = 1 order by displayorder desc limit " . $noticenum, $params);
        $item["data"] = array();
        if (!empty($notice)) {
            foreach ($notice as &$data) {
                $childid = rand(1000000000, 9999999999.0);
                $childid = "C" . $childid;
                $item["data"][$childid] = array("id" => $data["id"], "title" => $data["title"], "linkurl" => $data["wxapp_link"]);
            }
        }
    }
    return $item["data"];
}

//获取好店组的商户数据
function get_wxapp_haodian_store($item, $mobile = false)
{
    global $_W;
    mload()->model("plugin");
    pload()->model("haodian");
    $psize = intval($item["params"]["storenum"]) ? intval($item["params"]["storenum"]) : 20;
    $stores = haodian_store_fetchall(array("psize" => $psize));
    $item["data"] = $stores["store"];
    $item["data_num"] = count($item["data"]);
    if ($mobile && $item["params"]["showtype"] == 1 && $item["params"]["pagenum"] < count($item["data"])) {
        $item["data"] = array_chunk($item["data"], $item["params"]["pagenum"]);
    }
    $result = array("data" => $item["data"], "data_num" => $item["data_num"]);
    return $result;
}
function get_wxapp_bargains($item, $mobile = false)
{
    global $_W;
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($mobile || 0 < $_W["agentid"]) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $_W["agentid"];
    }
    $limit = intval($item["params"]["bargainnum"]);
    $limit = $limit ? $limit : 20;
    $bargains = pdo_fetchall("select a.discount_price,a.goods_id, a.bargain_id,b.title,b.thumb,b.price,b.sid,c.title as store_title, c.is_rest from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id left join " . tablename("tiny_wmall_store") . "as c on b.sid = c.id " . $condition . " and a.status = 1 and b.status = 1 order by c.is_rest asc, a.mall_displayorder desc limit " . $limit, $params);
    $item["data"] = array();
    if (!empty($bargains)) {
        foreach ($bargains as $val) {
            $childid = rand(1000000000, 9999999999.0);
            $childid = "C" . $childid;
            $item["data"][$childid] = array("thumb" => tomedia($val["thumb"]), "discount" => round($val["discount_price"] / $val["price"] * 10, 1), "goods_id" => $val["goods_id"], "bargain_id" => $val["bargain_id"], "title" => $val["title"], "discount_price" => $val["discount_price"], "price" => $val["price"], "sid" => $val["sid"], "store_title" => $val["store_title"]);
        }
    }
    $item["data_num"] = count($item["data"]);
    if ($mobile && $item["params"]["showtype"] == 1 && $item["params"]["pagenum"] < count($item["data"])) {
        $item["data"] = array_chunk($item["data"], $item["params"]["pagenum"]);
    }
    $result = array("data" => $item["data"], "data_num" => $item["data_num"]);
    return $result;
}
function get_wxapp_navs($item, $mobile = false)
{
    global $_W;
    if ($item["params"]["navsdata"] == 0) {
        if (!empty($item["data"])) {
            foreach ($item["data"] as &$val) {
                $val["imgurl"] = tomedia($val["imgurl"]);
            }
        }
    } else {
        if ($item["params"]["navsdata"] == 1) {
            $table = "tiny_wmall_store_category";
            $keys = "id,parentid,title,thumb,wxapp_link,displayorder";
            $empty_link = "pages/home/category?cid=";
        } else {
            if ($item["params"]["navsdata"] == 2) {
                $table = "tiny_wmall_gohome_category";
                $keys = "id,title,thumb,wxapp_link,displayorder";
                $empty_link = "";
            } else {
                if ($item["params"]["navsdata"] == 3) {
                    $table = "tiny_wmall_tongcheng_category";
                    $keys = "id,title,thumb,link,displayorder";
                    $empty_link = "/gohome/pages/tongcheng/category?id=";
                } else {
                    if ($item["params"]["navsdata"] == 4) {
                        $table = "tiny_wmall_haodian_category";
                        $keys = "id,title,thumb,link,displayorder";
                        $empty_link = "/gohome/pages/haodian/category?cid=";
                    }
                }
            }
        }
        $condition = " where uniacid = :uniacid";
        $params = array(":uniacid" => $_W["uniacid"]);
        if ($mobile || 0 < $_W["agentid"]) {
            $condition .= " and agentid = :agentid";
            $params[":agentid"] = $_W["agentid"];
        }
        if (in_array($item["params"]["navsdata"], array(1, 3, 4))) {
            $condition .= " and parentid = 0";
        }
        $limit = intval($item["params"]["navsnum"]) ? intval($item["params"]["navsnum"]) : 4;
        $navs = pdo_fetchall("select " . $keys . " from " . tablename($table) . $condition . " and status = 1 order by displayorder desc limit " . $limit, $params);
        $item["data"] = array();
        if (!empty($navs)) {
            foreach ($navs as $val) {
                $childid = rand(1000000000, 9999999999.0);
                $childid = "C" . $childid;
                if (in_array($item["params"]["navsdata"], array(3, 4))) {
                    $val["wxapp_link"] = $val["link"];
                }
                $item["data"][$childid] = array("linkurl" => empty($val["wxapp_link"]) ? empty($empty_link) ? "" : (string) $empty_link . $val["id"] : $val["wxapp_link"], "text" => $val["title"], "imgurl" => tomedia($val["thumb"]));
            }
        }
    }
    $item["data_num"] = count($item["data"]);
    if ($mobile && $item["params"]["showtype"] == 1 && $item["params"]["pagenum"] < $item["data_num"]) {
        $item["data"] = array_chunk($item["data"], $item["params"]["pagenum"]);
    }
    $result = array("data" => $item["data"], "data_num" => $item["data_num"], "row" => ceil($item["params"]["pagenum"] / $item["params"]["rownum"]));
    return $result;
}
function get_wxapp_danmu($config_danmu = array())
{
    global $_W;
    if (empty($config_danmu)) {
        $config_danmu = get_plugin_config("diypage.danmu");
    }
    if (!is_array($config_danmu) || !$config_danmu["params"]["status"]) {
        return error(-1, "");
    }
    if ($config_danmu["params"]["dataType"] == 1) {
        $members = pdo_fetchall("select b.nickname, b.avatar from " . tablename("tiny_wmall_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid where a.uniacid = :uniacid and a.agentid = :agentid and b.nickname != '' and b.avatar != '' order by a.id desc limit 10;", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
    }
    if (empty($members)) {
        $members = pdo_fetchall("select nickname, avatar from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and nickname != '' and avatar != '' order by id desc limit 10;", array(":uniacid" => $_W["uniacid"]));
    }
    if (!empty($members)) {
        foreach ($members as &$val) {
            $val["avatar"] = tomedia($val["avatar"]);
            $val["time"] = mt_rand($config_danmu["params"]["starttime"], $config_danmu["params"]["endtime"]);
            if ($val["time"] <= 0) {
                $val["time"] = "刚刚";
            } else {
                if (0 < $val["time"] && $val["time"] < 60) {
                    $val["time"] = (string) $val["time"] . "秒前";
                } else {
                    if (60 < $val["time"]) {
                        $val["time"] = floor($val["time"] / 60);
                        $val["time"] = (string) $val["time"] . "分钟前";
                    }
                }
            }
        }
    }
    $config_danmu["members"] = $members;
    return $config_danmu;
}
function get_wxapp_cubes($item, $mobile = false)
{
    global $_W;
    if (empty($item["params"]["activitydata"])) {
        if (!empty($item["data"])) {
            foreach ($item["data"] as &$val) {
                $val["imgurl"] = tomedia($val["imgurl"]);
            }
        }
    } else {
        $condition = " where uniacid = :uniacid";
        $params = array(":uniacid" => $_W["uniacid"]);
        if ($mobile || 0 < $_W["agentid"]) {
            $condition .= " and agentid = :agentid";
            $params[":agentid"] = $_W["agentid"];
        }
        $cubes = pdo_fetchall("select id,title,tips,thumb,wxapp_link,link from" . tablename("tiny_wmall_cube") . $condition . " order by displayorder desc", $params);
        $item["data"] = array();
        if (!empty($cubes)) {
            foreach ($cubes as $val) {
                $childid = rand(1000000000, 9999999999.0);
                $childid = "C" . $childid;
                $item["data"][$childid] = array("linkurl" => $val["wxapp_link"], "text" => $val["title"], "imgurl" => tomedia($val["thumb"]), "placeholder" => $val["tips"], "color" => "#ff2d4b", "placeholderColor" => "#7b7b7b");
            }
        }
    }
    $result = array("data" => $item["data"]);
    return $result;
}
function get_wxapp_slides($item, $mobile = false)
{
    global $_W;
    if (empty($item["params"]["picturedata"])) {
        if (!empty($item["data"])) {
            foreach ($item["data"] as &$val) {
                $val["imgurl"] = tomedia($val["imgurl"]);
            }
        }
    } else {
        if ($item["params"]["picturedata"] == 1) {
            $table = "tiny_wmall_slide";
            $keys = "id,title,thumb,wxapp_link,link,displayorder";
            $type = "homeTop";
        } else {
            if ($item["params"]["picturedata"] == 2) {
                $table = "tiny_wmall_gohome_slide";
                $keys = "id,title,thumb,wxapp_link,displayorder";
                $type = "gohome";
            } else {
                if ($item["params"]["picturedata"] == 3) {
                    $table = "tiny_wmall_gohome_slide";
                    $keys = "id,title,thumb,wxapp_link,displayorder";
                    $type = "tongcheng";
                } else {
                    if ($item["params"]["picturedata"] == 4) {
                        $table = "tiny_wmall_gohome_slide";
                        $keys = "id,title,thumb,wxapp_link,displayorder";
                        $type = "haodian";
                    }
                }
            }
        }
        $condition = " where uniacid = :uniacid and type = :type and status = 1 ";
        $params = array(":uniacid" => $_W["uniacid"], ":type" => $type);
        if ($mobile || 0 < $_W["agentid"]) {
            $condition .= " and agentid = :agentid ";
            $params[":agentid"] = $_W["agentid"];
        }
        $slides = pdo_fetchall("select " . $keys . " from " . tablename($table) . $condition . " order by displayorder desc", $params);
        $item["data"] = array();
        if (!empty($slides)) {
            foreach ($slides as $val) {
                $childid = rand(1000000000, 9999999999.0);
                $childid = "C" . $childid;
                $item["data"][$childid] = array("linkurl" => empty($val["wxapp_link"]) ? $val["link"] : $val["wxapp_link"], "imgurl" => tomedia($val["thumb"]));
            }
        }
    }
    $result = array("data" => $item["data"]);
    return $result;
}
function get_wxapp_pages($filter = array(), $search = array("*"))
{
    global $_W;
    $condition = " where uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $table = "tiny_wmall_diypage";
    if ($filter["from"] == "wechat") {
        $condition .= " and `version` = :version";
        $params[":version"] = 2;
    }
    if (!empty($filter) && !empty($filter["type"])) {
        $condition .= " and type = :type";
        $params[":type"] = intval($filter["type"]);
    }
    if (!empty($search)) {
        $search = implode(",", $search);
    }
    $pages = pdo_fetchall("select " . $search . " from " . tablename($table) . $condition, $params);
    return $pages;
}

?>
