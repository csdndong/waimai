<?php

defined("IN_IA") or exit("Access Denied");
pload()->classs("shansong");
class subscribe extends shanSong
{
    public $notice = "";
    public function checkSign($param)
    {
        $signature = $param["signature"];
        if ($signature != $this->buildSign($param)) {
            return false;
        }
        return true;
    }
    public function start($data)
    {
        $this->notice = $data;
        if (!$this->checkSign($this->notice)) {
            exit("Check Sign Fail.");
        }
        $this->parse();
    }
    public function parse()
    {
        $ordersn = $this->notice["orderno"];
        $order = pdo_get("tiny_wmall_order", array("ordersn" => $ordersn), array("id", "sid"));
        if (empty($order)) {
            exit("order is not exit");
        }
        mload()->model("order");
        $statusDd = $this->notice["statuscode"];
        if ($statusDd == 30) {
            $deliveryer = array("id" => 0, "title" => $this->notice["couriername"], "mobile" => $this->notice["couriermobile"]);
            order_deliveryer_update_status($order["id"], "delivery_assign", array("role" => "shansong", "deliveryer" => $deliveryer));
        } else {
            if ($statusDd == 42) {
                order_deliveryer_update_status($order["id"], "delivery_instore", array("role" => "shansong"));
            } else {
                if ($statusDd == 44) {
                    order_deliveryer_update_status($order["id"], "delivery_takegoods", array("role" => "shansong"));
                } else {
                    if ($statusDd == 60) {
                        order_status_update($order["id"], "end", array("role" => "shansong"));
                    } else {
                        if ($statusDd == 64) {
                            order_status_update($order["id"], "notify_deliveryer_collect", array("force" => 1, "channel" => "re_notify_deliveryer_collect", "role" => "shansong"));
                        }
                    }
                }
            }
        }
        echo '{"message":"ok"}';
        exit;
    }
}

?>
