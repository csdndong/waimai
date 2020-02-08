<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("eleme");
class order extends Eleme
{
    public function getOrder($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.getOrder", $params);
        return $data;
    }
    public function confirmOrderLite($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.confirmOrderLite", $params);
        return $data;
    }
    public function cancelOrderLite($id, $type, $remark = "")
    {
        $params = array("orderId" => $id, "type" => $type, "remark" => $remark);
        $data = $this->httpPost("eleme.order.cancelOrderLite", $params);
        return $data;
    }
    public function agreeRefundLite($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.agreeRefundLite", $params);
        return $data;
    }
    public function disagreeRefundLite($id, $reason = "")
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.disagreeRefundLite", $params);
        return $data;
    }
    public function getDeliveryStateRecord($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.getDeliveryStateRecord", $params);
        return $data;
    }
    public function receivedOrderLite($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.receivedOrderLite", $params);
        return $data;
    }
    public function replyReminder($id, $type, $content = "")
    {
        $params = array("remindId" => $id, "type" => $type, "content" => $content);
        $data = $this->httpPost("eleme.order.replyReminder", $params);
        return $data;
    }
    public function callDelivery($id, $fee = 0)
    {
        $params = array("orderId" => $id, "fee" => $fee);
        $data = $this->httpPost("eleme.order.callDelivery", $params);
        return $data;
    }
    public function cancelDelivery($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.cancelDelivery", $params);
        return $data;
    }
    public function getUnreplyReminders($shopId)
    {
        $params = array("shopId" => $shopId);
        $data = $this->httpPost("eleme.order.getUnreplyReminders", $params);
        return $data;
    }
    public function getUnprocessOrders($shopId)
    {
        $params = array("shopId" => $shopId);
        $data = $this->httpPost("eleme.order.getUnprocessOrders", $params);
        return $data;
    }
    public function deliveryBySelfLite($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.deliveryBySelfLite", $params);
        return $data;
    }
    public function noMoreDeliveryLite($shopId)
    {
        $params = array("shopId" => $shopId);
        $data = $this->httpPost("eleme.order.noMoreDeliveryLite", $params);
        return $data;
    }
    public function getRefundOrder($id)
    {
        $params = array("orderId" => $id);
        $data = $this->httpPost("eleme.order.getRefundOrder", $params);
        return $data;
    }
}

?>