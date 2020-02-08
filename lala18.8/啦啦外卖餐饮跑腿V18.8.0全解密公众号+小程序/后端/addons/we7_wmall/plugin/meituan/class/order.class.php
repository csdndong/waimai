<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("meituan");
class order extends meituan
{
    public function getOrder($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("order/queryById", $params);
        return $data;
    }
    public function confirmOrderLite($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("order/confirm", $params);
        return $data;
    }
    public function cancelOrderLite($id, $type, $remark = "")
    {
        $params = array("orderId" => $id, "reasonCode" => "2007", "reason" => $remark);
        $data = $this->httpPost("order/cancel", $params);
        return $data;
    }
    public function updateOrderDeliverying($id, $deliveryer = array())
    {
        $params = array("orderId" => $id, "courierName" => $deliveryer["title"], "courierPhone" => $deliveryer["mobile"]);
        $data = $this->httpPost("order/delivering", $params);
        return $data;
    }
    public function receivedOrderLite($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("order/delivered", $params);
        return $data;
    }
    public function agreeRefundLite($id, $reason = "同意退款")
    {
        $params = array("orderId" => $id, "reason" => $reason);
        $data = $this->httpPost("order/agreeRefund", $params);
        return $data;
    }
    public function disagreeRefundLite($id, $reason = "")
    {
        $params = array("orderId" => $id, "reason" => $reason);
        $data = $this->httpPost("order/rejectRefund", $params);
        return $data;
    }
    public function batchPullPhoneNumber($offset = 0)
    {
        $params = array("developerId" => $this->app["developerId"], "degradOffset" => $offset, "degradLimit" => 1000);
        $data = $this->httpPost("order/batchPullPhoneNumber", $params);
        return $data;
    }
}

?>