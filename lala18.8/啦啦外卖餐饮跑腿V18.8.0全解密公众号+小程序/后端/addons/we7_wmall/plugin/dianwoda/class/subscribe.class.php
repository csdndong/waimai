<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("dianwoda");
class subscribe extends dianwoda
{
    public $type = "";
    public $data = array();
    public function start($get, $data, $originBody)
    {
        $this->type = $get["type"];
        $this->data = $data;
        $common = array("timestamp" => $get["timestamp"], "nonce" => $get["nonce"], "type" => $get["type"]);
        $sign = $this->buildSign($common, $originBody);
        if ($sign != $get["sign"]) {
            slog("dianwoda", "点我达错误", array("ordersn" => $data["content"]["order_original_id"]), "订单回调签名验证失败);
            exit("Sign error");
        }
        $this->parse();
    }
    public function parse()
    {
        if ($this->type == "dianwoda.order.status-update") {
            $content = $this->data["content"];
            $ordersn = $content["order_original_id"];
            if (strexists($ordersn, "_")) {
                $snArr = explode("_", $ordersn);
                $ordersn = $snArr[1];
            }
            $order = pdo_get("tiny_wmall_order", array("ordersn" => $ordersn), array("id", "sid"));
            if (empty($order)) {
                exit("order is not exit");
            }
            mload()->model("order");
            if (!function_exists("store_fetch")) {
                mload()->model("store");
            }
            $orderStatus = $content["order_status"];
            switch ($orderStatus) {
                case "created":
                    break;
                case "dispatched":
                    $deliveryer = array("id" => $content["rider_code"], "title" => $content["rider_name"], "mobile" => $content["rider_mobile"]);
                    $status = order_deliveryer_update_status($order["id"], "delivery_assign", array("role" => "dianwoda", "deliveryer" => $deliveryer));
                    break;
                case "arrived":
                    $status = order_deliveryer_update_status($order["id"], "delivery_instore", array("role" => "dianwoda"));
                    break;
                case "obtained":
                    $status = order_deliveryer_update_status($order["id"], "delivery_takegoods", array("role" => "dianwoda"));
                    break;
                case "completed":
                    $status = order_status_update($order["id"], "end", array("role" => "dianwoda"));
                    break;
                case "abnormal":
                    break;
                case "canceled":
                    $status = order_status_update($order["id"], "notify_deliveryer_collect", array("force" => 1, "channel" => "re_notify_deliveryer_collect", "role" => "dianwoda"));
                    break;
            }
            if (is_error($status)) {
                slog("dianwoda", "点我达错误", array("order_id" => $order["id"]), "订单状态" . $orderStatus . "回调错误" . $status["message"]);
            }
        }
        return true;
    }
}

?>