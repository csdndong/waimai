<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("eleme");
class subscribe extends Eleme
{
    public $notice = "";
    public function buildSign($param)
    {
        unset($param["signature"]);
        ksort($param);
        $string = "";
        foreach ($param as $key => $value) {
            $string .= $key . "=" . $value;
        }
        $splice = $string . $this->app["secret"];
        $md5 = strtoupper(md5($splice));
        return $md5;
    }
    public function checkSign($param)
    {
        $signature = $param["signature"];
        unset($param["signature"]);
        if ($signature != $this->buildSign($param)) {
            return false;
        }
        return true;
    }
    public function parse()
    {
        global $_W;
        $type = $this->notice["type"];
        $message = $this->notice["message"];
        $role_cn = "";
        if (!empty($message["role"])) {
            $role_cn = $this->getRolecn($message["role"]);
        }
        if (1 || in_array($type, array(10, 12))) {
            $shopid = intval($message["shopId"]);
            if (empty($shopid)) {
                exit("shopid is not exist");
            }
            $store = pdo_get("tiny_wmall_store", array("elemeShopid" => $shopid), array("id", "uniacid", "agentid", "title", "location_y", "location_x", "data", "eleme_status"));
            if (empty($store)) {
                echo "{\"message\":\"ok\"}";
                exit;
            }
            $store["data"] = iunserializer($store["data"]);
            $config_eleme = $store["data"]["eleme"];
            $config_eleme_delivery = $config_eleme["delivery"];
            $config_eleme_order = $config_eleme["order"];
            if ($type == 10 && (!$store["eleme_status"] || !$config_eleme_order["accept_order"])) {
                echo "{\"message\":\"ok\"}";
                exit;
            }
            $orderid = intval($message["orderId"]);
            $order = pdo_get("tiny_wmall_order", array("elemeOrderId" => $orderid), array("uniacid", "id", "sid"));
            if (in_array($type, array(10))) {
                if (!empty($order)) {
                    exit("order is repeat");
                }
            } else {
                if (empty($order)) {
                    exit("order is not exist");
                }
            }
        }
        if ($type == 10) {
            $deliveryGeo = explode(",", $message["deliveryGeo"]);
            $order = array("uniacid" => $_W["uniacid"], "agentid" => $store["agentid"], "acid" => $_W["acid"], "sid" => $store["id"], "uid" => 0, "order_type" => 1, "elemeOrderId" => $message["orderId"], "ordersn" => $message["orderId"], "address" => $message["address"], "addtime" => strtotime($message["activeAt"]), "paytime" => strtotime($message["activeAt"]), "note" => $message["description"], "invoice" => $message["invoice"], "mobile" => $message["phoneList"][0], "total_fee" => $message["originalPrice"], "final_fee" => $message["totalPrice"], "store_final_fee" => 0, "eleme_store_final_fee" => $message["income"], "username" => $message["consignee"], "sex" => "", "location_x" => $deliveryGeo[1], "location_y" => $deliveryGeo[0], "order_plateform" => "eleme", "order_channel" => "eleme", "delivery_type" => 0, "delivery_fee" => $message["deliverFee"], "delivery_time" => !empty($message["deliverTime"]) ? $message["deliverTime"] : "立即配送", "is_pay" => 1, "pay_type" => $message["onlinePaid"] ? "eleme" : "delivery", "serial_sn" => $message["daySn"], "plateform_serve_rate" => $message["serviceRate"], "plateform_serve_fee" => $message["serviceFee"], "box_price" => $message["packageFee"], "plateform_discount_fee" => $message["elemePart"], "store_discount_fee" => $message["shopPart"], "discount_fee" => $message["activityTotal"], "plateform_delivery_fee" => 0, "status" => $this->orderStatusTransform($message["status"]), "stat_year" => date("Y", strtotime($message["activeAt"])), "stat_month" => date("Ym", strtotime($message["activeAt"])), "stat_day" => date("Ymd", strtotime($message["activeAt"])));
            $distance = distanceBetween($order["location_y"], $order["location_x"], $store["location_y"], $store["location_x"]);
            $distance = round($distance / 1000, 2);
            $order["distance"] = $distance;
            if (!empty($config_eleme)) {
                if ($config_eleme_delivery["delivery_mode"] == 1) {
                    $order["delivery_type"] = 1;
                } else {
                    if ($config_eleme_delivery["delivery_mode"] == 2) {
                        $order["delivery_type"] = 2;
                        if ($config_eleme_delivery["delivery_fee_mode"] == 1) {
                            $order["plateform_delivery_fee"] = $config_eleme_delivery["delivery_price"];
                        } else {
                            $delivery_price = $config_eleme_delivery["delivery_price"]["start_fee"];
                            if ($config_eleme_delivery["delivery_price"]["start_km"] < $distance) {
                                $delivery_price += ($distance - $config_eleme_delivery["delivery_price"]["start_km"]) * $config_eleme_delivery["delivery_price"]["pre_km_fee"];
                            }
                            $delivery_price = round($delivery_price, 2);
                            $order["plateform_delivery_fee"] = $delivery_price;
                        }
                        $order["plateform_deliveryer_fee"] = order_calculate_deliveryer_fee($order);
                    }
                }
            }
            $account = store_account($order["sid"], array("fee_eleme"));
            $eleme = array("fee_type" => 2, "fee_rate" => $message["serviceRate"], "fee" => $message["serviceFee"], "note" => "饿了么服务费率为:" . $message["serviceRate"] . ",收取佣金:￥" . $message["serviceFee"]);
            $plateform_serve = array("fee_type" => 1, "fee_rate" => 0, "fee" => 0, "note" => "每单固定0元", "eleme" => $eleme);
            if (!empty($account["fee_eleme"])) {
                if ($account["fee_eleme"]["fee_type"] == 1) {
                    $platform_serve_fee = round($message["income"] * $account["fee_eleme"]["fee_rate"] / 100, 2);
                    if ($platform_serve_fee < $account["fee_eleme"]["fee_min"]) {
                        $platform_serve_fee = $account["fee_eleme"]["fee_min"];
                        $text = "佣金小于最少抽佣金额，以最少抽佣金额 ￥" . $platform_serve_fee . "计";
                    }
                    $plateform_serve = array("fee_type" => 1, "fee_rate" => $account["fee_eleme"]["fee_rate"], "fee" => $platform_serve_fee, "note" => "(饿了么平台最终入账 ￥" . $message["income"] . ") x " . $account["fee_eleme"]["fee_rate"] . "% " . $text, "eleme" => $eleme);
                } else {
                    $platform_serve_fee = floatval($account["fee_eleme"]["fee"]);
                    $plateform_serve = array("fee_type" => 2, "fee_rate" => 0, "fee" => $platform_serve_fee, "note" => "每单固定" . $platform_serve_fee . "元", "eleme" => $eleme);
                }
            }
            if ($_W["is_agent"]) {
                $plateform_serve["final"] = "(代理抽取饿了么订单 ￥" . $platform_serve_fee . " + 代理商配送费 ￥" . $order["plateform_delivery_fee"] . " - 代理商支付给配送员配送费 ￥" . $order["plateform_deliveryer_fee"] . ")";
                $order["agent_serve"] = iserializer($plateform_serve);
                $order["agent_final_fee"] = $platform_serve_fee + $order["plateform_delivery_fee"] - $order["plateform_deliveryer_fee"];
            } else {
                $order["plateform_serve"] = iserializer($plateform_serve);
            }
            $order["store_final_fee"] = 0 - ($platform_serve_fee + $order["plateform_delivery_fee"]);
            $invoice = array();
            if (!empty($order["invoice"])) {
                $invoice = array("invoiceType" => $message["invoiceType"], "taxpayerId" => $message["taxpayerId"]);
            }
            $order["data"] = iserializer(array("invoice" => $invoice));
            pdo_insert("tiny_wmall_order", $order);
            $order_id = pdo_insertid();
            $order["id"] = $order_id;
            foreach ($message["groups"] as $group) {
                foreach ($group["items"] as $item) {
                    $title = $item["name"];
                    $spec = array();
                    if (!empty($item["newSpecs"])) {
                        foreach ($item["newSpecs"] as $newSpec) {
                            $spec[] = $newSpec["value"];
                        }
                    }
                    if (!empty($item["attributes"])) {
                        foreach ($item["attributes"] as $attribute) {
                            $spec[] = $attribute["value"];
                        }
                    }
                    if (!empty($spec)) {
                        $spec = implode("+", $spec);
                        $title = (string) $title . "(" . $spec . ")";
                    }
                    $stat = array("uniacid" => $_W["uniacid"], "uid" => $order["uid"], "oid" => $order_id, "sid" => $order["sid"], "goods_title" => $title, "goods_unit_price" => $item["price"], "goods_num" => $item["quantity"], "goods_price" => $item["total"], "addtime" => $order["addtime"], "order_plateform" => "eleme", "stat_year" => date("Y", $order["addtime"]), "stat_month" => date("Ym", $order["addtime"]), "stat_day" => date("Ymd", $order["addtime"]), "stat_week" => date("YW", $order["addtime"]));
                    pdo_insert("tiny_wmall_order_stat", $stat);
                }
            }
            if (!empty($message["orderActivities"])) {
                foreach ($message["orderActivities"] as $orderActivity) {
                    $discount = array("uniacid" => $_W["uniacid"], "oid" => $order_id, "sid" => $order["sid"], "type" => "discount", "icon" => "discount_b.png", "name" => $orderActivity["name"], "note" => "-￥" . $orderActivity["amount"], "fee" => $orderActivity["amount"]);
                    pdo_insert("tiny_wmall_order_discount", $discount);
                }
            }
            if ($config_eleme_order["auto_handel_order"] == 1) {
                $result = order_status_update($order["id"], "handle", array("role" => "system"));
                if (!is_error($result)) {
                }
            }
            if ($config_eleme_order["auto_print"] == 1) {
                order_print($order["id"]);
            }
            order_clerk_notice($order["id"], "place_order");
        } else {
            if ($type == 12) {
                order_status_update($order["id"], "handle", array("role" => "eleme", "role_cn" => $role_cn));
            } else {
                if ($type == 45) {
                    order_status_update($order["id"], "remind", array("remindId" => $message["remindId"], "addtime" => $message["updateTime"], "role" => "eleme"));
                } else {
                    if ($type == 46) {
                        order_status_update($order["id"], "reply", array("reply" => ""));
                    } else {
                        if (in_array($type, array(14, 15, 17))) {
                            order_status_update($order["id"], "cancel", array("force_cancel" => 1, "role" => "eleme", "role_cn" => $role_cn));
                        } else {
                            if (in_array($type, array(18))) {
                                order_status_update($order["id"], "end", array("role" => "eleme"));
                            } else {
                                if (in_array($type, array(51, 52))) {
                                    order_status_update($order["id"], "notify_deliveryer_collect");
                                } else {
                                    if (in_array($type, array(53))) {
                                        $deliveryer = array("id" => 0, "title" => $message["name"], "mobile" => $message["phone"]);
                                        order_deliveryer_update_status($order["id"], "delivery_assign", array("role" => "eleme", "deliveryer" => $deliveryer));
                                    } else {
                                        if (in_array($type, array(54))) {
                                            order_deliveryer_update_status($order["id"], "delivery_instore", array("role" => "eleme"));
                                        } else {
                                            if (in_array($type, array(56))) {
                                                order_status_update($order["id"], "end", array("role" => "eleme"));
                                            } else {
                                                if (in_array($type, array(71))) {
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
        echo "{\"message\":\"ok\"}";
        exit;
    }
    public function start()
    {
        global $_W;
        if (strtolower($_SERVER["REQUEST_METHOD"]) == "get") {
            echo "{\"message\":\"ok\"}";
            exit;
        }
        if (strtolower($_SERVER["REQUEST_METHOD"]) == "post") {
            $postStr = file_get_contents("php://input");
            $postStr = json_decode($postStr, true);
            if (!$this->checkSign($postStr)) {
                exit("Check Sign Fail.");
            }
            $postStr["message"] = json_decode($postStr["message"], true);
            $this->notice = $postStr;
            $this->parse();
        }
    }
}

?>