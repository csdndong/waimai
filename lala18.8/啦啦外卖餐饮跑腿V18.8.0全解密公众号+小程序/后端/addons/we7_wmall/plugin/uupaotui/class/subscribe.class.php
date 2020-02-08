<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("uu");
class subscribe
{
    public $notice = array();
    public $order = array();
    public function start($data)
    {
        $this->notice = $data;
        $order_id = $this->notice["origin_id"];
        $this->order = pdo_get("tiny_wmall_order", array("ordersn" => $order_id), array("id", "sid"));
        if (empty($this->order)) {
            exit("order is not exist");
        }
        $checksign = $this->buildSign($data);
        if ($checksign != $data["sign"]) {
            exit("Check Sign Fail.");
        }
        $this->parse();
    }
    public function buildSign($params)
    {
        unset($params["sign"]);
        ksort($params);
        $arr = array();
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $arr[] = $key . "=" . $value;
            }
        }
        mload()->model("store");
        $config_uupaotui = store_get_data($this->order["sid"], "uupaotui");
        $arr[] = "key=" . $config_uupaotui["appkey"];
        $str = strtoupper(implode("&", $arr));
        $sign = strtoupper(md5($str));
        return $sign;
    }
    public function parse()
    {
        global $_W;
        mload()->model("order");
        $order = $this->order;
        $statusDd = $this->notice["state"];
        if ($statusDd == 3) {
            $deliveryer = array("id" => 0, "title" => $this->notice["driver_name"], "mobile" => $this->notice["driver_mobile"]);
            order_deliveryer_update_status($order["id"], "delivery_assign", array("role" => "uupaotui", "deliveryer" => $deliveryer));
        } else {
            if ($statusDd == 4) {
                order_deliveryer_update_status($order["id"], "delivery_instore", array("role" => "uupaotui"));
            } else {
                if ($statusDd == 5) {
                    order_deliveryer_update_status($order["id"], "delivery_takegoods", array("role" => "uupaotui"));
                } else {
                    if ($statusDd == 10) {
                        order_status_update($order["id"], "end", array("role" => "uupaotui"));
                    } else {
                        if ($statusDd == -1) {
                            order_status_update($order["id"], "notify_deliveryer_collect", array("force" => 1, "channel" => "re_notify_deliveryer_collect", "role" => "uupaotui"));
                        } else {
                            if ($statusDd == 6) {
                            } else {
                                if ($statusDd == 1) {
                                } else {
                                    if ($statusDd == 9) {
                                    } else {
                                        if ($statusDd == 10) {
                                        } else {
                                            if ($statusDd == 1000) {
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
}

?>