<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("meituan");
class subscribe extends Meituan
{
    public $notice = "";
    public function buildSign($params)
    {
        unset($params["sign"]);
        ksort($params);
        $string = $this->app["signKey"];
        foreach ($params as $key => $value) {
            $string .= (string) $key . $value;
        }
        return strtolower(sha1($string));
    }
    public function checkSign($param)
    {
        $signature = $param["sign"];
        unset($param["sign"]);
        if ($signature != $this->buildSign($param)) {
            return false;
        }
        return true;
    }
    public function parse()
    {
        global $_W;
        $type = $this->notice["type"];
        if (1 || in_array($type, array(10, 12))) {
            $shopid = trim($this->notice["ePoiId"]);
            if (empty($shopid)) {
                exit("shopid is not exist");
            }
            $store = pdo_get("tiny_wmall_store", array("meituanShopid" => $shopid), array("id", "uniacid", "agentid", "title", "location_y", "location_x", "data", "meituan_status"));
            if (empty($store)) {
                exit("store is not exist");
            }
            $store["data"] = iunserializer($store["data"]);
            $config_meituan = $store["data"]["meituan"];
            $config_meituan_delivery = $config_meituan["delivery"];
            $config_meituan_order = $config_meituan["order"];
            if ($type == "order" && (!$store["meituan_status"] || !$config_meituan_order["accept_order"])) {
                echo "{\"data\":\"ok\"}";
                exit;
            }
            $strans_type = $type;
            if (in_array($type, array("orderConfirm", "orderEnd"))) {
                $strans_type = "order";
            }
            $message = $this->notice[$strans_type];
            $orderid = intval($message["orderId"]);
            $order = pdo_get("tiny_wmall_order", array("meituanOrderId" => $orderid), array("uniacid", "id", "sid"));
            if (in_array($type, array("order"))) {
                if (!empty($order)) {
                    exit("order is repeat");
                }
            } else {
                if (empty($order)) {
                    exit("order is not exist");
                }
            }
        }
        if ($type == "order") {
            $deliveryGeo = explode(",", $message["deliveryGeo"]);
            $address = explode("@#", $message["recipientAddress"]);
            $order = array("uniacid" => $_W["uniacid"], "agentid" => $store["agentid"], "acid" => $_W["acid"], "sid" => $store["id"], "uid" => 0, "order_type" => $address[0] == "到店自取" ? 2 : 1, "meituanOrderId" => $message["orderId"], "ordersn" => $message["orderId"], "address" => $address[0], "addtime" => $message["utime"], "paytime" => $message["utime"], "note" => $message["caution"], "invoice" => $message["invoiceTitle"], "mobile" => $message["recipientPhone"], "total_fee" => $message["originalPrice"], "final_fee" => $message["total"], "store_final_fee" => 0, "meituan_store_final_fee" => round($message["poiReceiveDetail"]["wmPoiReceiveCent"] / 100, 2), "username" => substr($message["recipientName"], 0, strpos($message["recipientName"], "(")), "sex" => substr($message["recipientName"], strpos($message["recipientName"], "(") + 1, 6), "location_x" => $message["latitude"], "location_y" => $message["longitude"], "order_plateform" => "meituan", "order_channel" => "meituan", "delivery_type" => 0, "delivery_fee" => $message["shippingFee"], "delivery_time" => !empty($message["deliveryTime"]) ? date("Y-m-d H:i", $message["deliveryTime"]) : "立即配送", "is_pay" => 1, "pay_type" => $message["payType"] == 2 ? "meituan" : "delivery", "serial_sn" => $message["daySeq"], "plateform_serve_rate" => 0, "plateform_serve_fee" => 0, "box_price" => 0, "plateform_discount_fee" => $message["poiReceiveDetail"]["actOrderChargeByMt"]["moneyCent"], "store_discount_fee" => $message["poiReceiveDetail"]["actOrderChargeByPoi"]["moneyCent"], "discount_fee" => 0, "plateform_delivery_fee" => 0, "person_num" => $message["dinnersNumber"], "status" => $this->orderStatusTransform($message["status"]), "stat_year" => date("Y", $message["utime"]), "stat_month" => date("Ym", $message["utime"]), "stat_day" => date("Ymd", $message["utime"]));
            $distance = distanceBetween($order["location_y"], $order["location_x"], $store["location_y"], $store["location_x"]);
            $distance = round($distance / 1000, 2);
            $order["distance"] = $distance;
            if (!empty($config_meituan)) {
                if ($config_meituan_delivery["delivery_mode"] == 1) {
                    $order["delivery_type"] = 1;
                } else {
                    if ($config_meituan_delivery["delivery_mode"] == 2) {
                        $order["delivery_type"] = 2;
                        if ($config_meituan_delivery["delivery_fee_mode"] == 1) {
                            $order["plateform_delivery_fee"] = $config_meituan_delivery["delivery_price"];
                        } else {
                            $delivery_price = $config_meituan_delivery["delivery_price"]["start_fee"];
                            if ($config_meituan_delivery["delivery_price"]["start_km"] < $distance) {
                                $delivery_price += ($distance - $config_meituan_delivery["delivery_price"]["start_km"]) * $config_meituan_delivery["delivery_price"]["pre_km_fee"];
                            }
                            $delivery_price = round($delivery_price, 2);
                            $order["plateform_delivery_fee"] = $delivery_price;
                        }
                        $order["plateform_deliveryer_fee"] = order_calculate_deliveryer_fee($order);
                    }
                }
            }
            $account = store_account($order["sid"], array("fee_meituan"));
            $meituan = array("fee_type" => 2, "fee_rate" => 0, "fee" => 0, "note" => "");
            $plateform_serve = array("fee_type" => 1, "fee_rate" => 0, "fee" => 0, "note" => "每单固定0元", "meituan" => $meituan);
            if (!empty($account["fee_meituan"])) {
                if ($account["fee_meituan"]["fee_type"] == 1) {
                    $platform_serve_fee = round($order["meituan_store_final_fee"] * $account["fee_meituan"]["fee_rate"] / 100, 2);
                    if ($platform_serve_fee < $account["fee_eleme"]["fee_min"]) {
                        $platform_serve_fee = $account["fee_eleme"]["fee_min"];
                        $text = "佣金小于最少抽佣金额，以最少抽佣金额 ￥" . $platform_serve_fee . "计";
                    }
                    $plateform_serve = array("fee_type" => 1, "fee_rate" => $account["fee_meituan"]["fee_rate"], "fee" => $platform_serve_fee, "note" => "(美团平台最终入账 ￥" . $order["meituan_store_final_fee"] . ") x " . $account["fee_meituan"]["fee_rate"] . "% " . $text, "meituan" => $meituan);
                } else {
                    $platform_serve_fee = floatval($account["fee_meituan"]["fee"]);
                    $plateform_serve = array("fee_type" => 2, "fee_rate" => 0, "fee" => $platform_serve_fee, "note" => "每单固定" . $platform_serve_fee . "元", "meituan" => $meituan);
                }
            }
            if ($_W["is_agent"]) {
                $plateform_serve["final"] = "(代理抽取美团订单 ￥" . $platform_serve_fee . " + 代理商配送费 ￥" . $order["plateform_delivery_fee"] . " - 代理商支付给配送员配送费 ￥" . $order["plateform_deliveryer_fee"] . ")";
                $order["agent_serve"] = iserializer($plateform_serve);
                $order["agent_final_fee"] = $platform_serve_fee + $order["plateform_delivery_fee"] - $order["plateform_deliveryer_fee"];
            } else {
                $order["plateform_serve"] = iserializer($plateform_serve);
            }
            $order["store_final_fee"] = 0 - ($platform_serve_fee + $order["plateform_delivery_fee"]);
            $invoice = array();
            if (!empty($order["invoice"])) {
                $invoice = array("invoiceTitle" => $message["invoiceTitle"], "taxpayerId" => $message["taxpayerId"]);
            }
            $order["data"] = iserializer(array("invoice" => $invoice));
            pdo_insert("tiny_wmall_order", $order);
            $order_id = pdo_insertid();
            $order["id"] = $order_id;
            foreach ($message["detail"] as $item) {
                $title = $item["food_name"];
                $attrs = array();
                if (!empty($item["spec"])) {
                    $attrs[] = $item["spec"];
                }
                if (!empty($item["food_property"])) {
                    $item["food_property"] = str_replace(",", "+", $item["food_property"]);
                    $attrs[] = $item["food_property"];
                }
                if (!empty($attrs)) {
                    $attrs = implode("+", $attrs);
                    $title = (string) $title . "(" . $attrs . ")";
                }
                $stat = array("uniacid" => $_W["uniacid"], "uid" => $order["uid"], "oid" => $order_id, "sid" => $order["sid"], "goods_title" => $title, "goods_unit_price" => $item["price"], "goods_num" => $item["quantity"], "goods_price" => round($item["quantity"] * $item["price"], 2), "goods_original_price" => round($item["quantity"] * $item["price"], 2), "addtime" => $order["addtime"], "order_plateform" => "meituan", "stat_year" => date("Y", $order["addtime"]), "stat_month" => date("Ym", $order["addtime"]), "stat_day" => date("Ymd", $order["addtime"]), "stat_week" => date("YW", $order["addtime"]));
                pdo_insert("tiny_wmall_order_stat", $stat);
            }
            if (!empty($message["extras"])) {
                foreach ($message["extras"] as $orderActivity) {
                    $discount = array("uniacid" => $_W["uniacid"], "oid" => $order_id, "sid" => $order["sid"], "type" => "discount", "icon" => "discount_b.png", "name" => $orderActivity["remark"], "note" => "-￥" . $orderActivity["reduce_fee"], "fee" => $orderActivity["reduce_fee"], "store_discount_fee" => $orderActivity["poi_charge"], "plateform_discount_fee" => $orderActivity["mt_charge"]);
                    pdo_insert("tiny_wmall_order_discount", $discount);
                }
            }
            if ($config_meituan_order["auto_handel_order"] == 1) {
                $result = order_status_update($order["id"], "handle", array("role" => "system"));
                if (!is_error($result)) {
                }
            }
            if ($config_meituan_order["auto_print"] == 1) {
                order_print($order["id"]);
            }
            order_clerk_notice($order["id"], "place_order");
        } else {
            if ($type == "orderConfirm") {
                order_status_update($order["id"], "handle", array("role" => "meituan", "role_cn" => $role_cn));
            } else {
                if ($type == "orderCancel") {
                    order_status_update($order["id"], "cancel", array("force_cancel" => 1, "role" => "meituan", "role_cn" => $role_cn, "note" => $message["reason"]));
                } else {
                    if ($type == "orderEnd") {
                        order_status_update($order["id"], "end", array("role" => "meituan"));
                    } else {
                        if ($type == "orderShippingStatus") {
                            if (in_array($message["shippingStatus"], array(20, 40))) {
                                $deliveryer = array("id" => 0, "title" => $message["dispatcherName"], "mobile" => $message["dispatcherMobile"]);
                                order_deliveryer_update_status($order["id"], "delivery_assign", array("role" => "meituan", "deliveryer" => $deliveryer));
                            }
                            if ($message["shippingStatus"] == 20) {
                                order_deliveryer_update_status($order["id"], "delivery_instore", array("role" => "meituan"));
                            } else {
                                if ($message["shippingStatus"] == 40) {
                                    order_status_update($order["id"], "end", array("role" => "meituan"));
                                }
                            }
                        }
                    }
                }
            }
        }
        echo "{\"data\":\"ok\"}";
        exit;
    }
    public function start()
    {
        global $_W;
        global $_GPC;
        $op = $_GPC["op"];
        if (strtolower($_SERVER["REQUEST_METHOD"]) == "post") {
            if ($op == "storemap") {
                $this->saveAppAuthToken($_POST);
                return NULL;
            }
            if ($op == "releasebinding") {
                $this->releasebinding($_POST);
                return NULL;
            }
            if ($op == "yinsihaojiangji") {
                if (!$this->checkSign($_POST)) {
                    exit("Check Sign Fail.");
                }
                $this->yinsihaojiangji($_POST);
                return NULL;
            }
            if (!$this->checkSign($_POST)) {
                exit("Check Sign Fail.");
            }
            $postStr = $_POST;
            if (isset($postStr["order"])) {
                $postStr["order"] = json_decode($postStr["order"], true);
                $postStr["order"]["detail"] = json_decode($postStr["order"]["detail"], true);
                $postStr["order"]["poiReceiveDetail"] = json_decode($postStr["order"]["poiReceiveDetail"], true);
                $postStr["order"]["extras"] = json_decode($postStr["order"]["extras"], true);
            }
            if (isset($postStr["orderCancel"])) {
                $postStr["orderCancel"] = json_decode($postStr["orderCancel"], true);
            }
            if (isset($postStr["orderRefund"])) {
                $postStr["orderRefund"] = json_decode($postStr["orderRefund"], true);
            }
            $postStr["type"] = $op;
            $this->notice = $postStr;
            $this->parse();
        }
    }
}

?>