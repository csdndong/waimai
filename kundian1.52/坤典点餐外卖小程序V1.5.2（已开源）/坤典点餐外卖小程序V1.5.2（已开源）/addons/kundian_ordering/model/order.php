<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/22
 * Time: 17:50
 */
defined("IN_IA") or exit("Access Denied");
class Order_KundianOrderingModel{

    /**
     * 获取外卖、快餐订单列表信息
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param array $field
     * @param string $order_by
     * @return array
     */
    public function getOrderList($cond,$pageIndex='',$pageSize='',$field=array(),$order_by='create_time desc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall('cqkundian_ordering_order',$cond,$field,'',$order_by,[$pageIndex,$pageSize]);
        }else{
            $list=pdo_getall('cqkundian_ordering_order',$cond,$field,'',$order_by);
        }
        return $list;
    }

    /**
     * 获取订单详细信息
     * @param $order_id
     * @param $uniacid
     * @return array
     */
    public function getOrderDetailList($order_id,$uniacid){
        if(!empty($order_id)){
            $list=pdo_getall('cqkundian_ordering_order_detail',['order_id'=>$order_id,'uniacid'=>$uniacid]);
            return $list;
        }else{
            return [];
        }
    }

    /**
     * 获取订单列表信息
     * @param $cond
     * @param $uniacid
     * @return array
     */
    public function selectOrderList($cond,$uniacid,$pageIndex=1,$pageSize=10){
        $orderData=$this->getOrderList($cond,$pageIndex,$pageSize);
        for ($i = 0; $i < count($orderData); $i++) {
            $detailData=$this->getOrderDetailList($orderData[$i]['id'],$uniacid);
            for ($j = 0; $j < count($detailData); $j++) {
                $productData = pdo_get("cqkundian_ordering_product", array('id' => $detailData[$j]['pid'], 'uniacid' => $uniacid));
                $detailData[$j]['product_name'] = $productData['product_name'];
                $detailData[$j]['cover'] = $productData['cover'];
                $detailData[$j]['price'] = $productData['price'];
            }
            $orderData[$i]['detailData'] = $detailData;
            $orderData[$i]=$this->neatenOrderStatus($orderData[$i]);
        }

        return $orderData;
    }

    /**
     * 订单状态整理
     * @param $orderData
     * @return mixed
     */
    public function neatenOrderStatus($orderData){
        if($orderData['is_fast_food']!=1){
            switch ($orderData['is_pay']){
                case '-1':
                    $orderData['status_txt']='货到付款';
                    break;
                case '0':
                    $orderData['status_txt']='未支付';
                    break;
                case '1':
                    $orderData['status_txt']='已支付,待配送';
                    break;
                case '2':
                    $orderData['status_txt']='开始配送';
                    break;
                case '3':
                    $orderData['status_txt']='完成配送';
                    break;
                case '4':
                    $orderData['status_txt']='申请取消';
                    break;
                case '5':
                    $orderData['status_txt']='已取消';
                    break;

            }
        }else{
            switch ($orderData['is_pay']){
                case '-1':
                    $orderData['status_txt']='货到付款';
                    break;
                case '0':
                    $orderData['status_txt']='未支付';
                    break;
                case '1':
                    $orderData['status_txt']='已支付';
                    break;

            }
        }

        return $orderData;
    }

    /**
     * 商家端订单信息
     * @param $cond
     * @param $uniacid
     * @param int $pageIndex
     * @param int $pageSize
     * @return array
     */
    public function getMerchentOrderList($cond,$uniacid,$pageIndex=1,$pageSize=10){
        $orderData=$this->getOrderList($cond,$pageIndex,$pageSize);
        for ($i = 0; $i < count($orderData); $i++) {
            $orderDetail = pdo_getall('cqkundian_ordering_order_detail', array('uniacid' => $uniacid, 'order_id' => $orderData[$i]['id']));
            for ($j = 0; $j < count($orderDetail); $j++) {
                $productData = pdo_get("cqkundian_ordering_product", array('id' => $orderDetail[$j]['pid'], 'uniacid' => $uniacid));
                $orderDetail[$j]['product_name'] = $productData['product_name'];
                $orderDetail[$j]['cover'] = $productData['cover'];
                $orderDetail[$j]['price'] = $productData['price'];
                $orderDetail[$j]['old_price'] = $productData['old_price'];
            }
            $orderData[$i]['orderDetail'] = $orderDetail;
            $userData = pdo_get('cqkundian_ordering_user', array('uniacid' => $uniacid, 'uid' => $orderData[$i]['uid']));
            $orderData[$i]['nickname'] = $userData['nickname'];
            $orderData[$i]['avatarurl'] = $userData['avatarurl'];
            $orderData[$i]['create_time'] = date("Y-m-d H:i:s", $orderData[$i]['create_time']);
            $orderData[$i]=$this->neatenOrderStatus($orderData[$i]);
        }
        return $orderData;
    }


    /**
     * 获取预约的订单信息
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param string $order_by
     * @return array
     */
    public function getMakeOrderList($cond,$pageIndex='',$pageSize='',$order_by='create_time desc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall('cqkundian_ordering_make_order',$cond,'','',$order_by,[$pageIndex,$pageSize]);
        }else{
            $list=pdo_getall('cqkundian_ordering_make_order',$cond,'','',$order_by);
        }
        return $list;
    }

    /**
     * 获取点餐订单信息
     * @param $cond
     * @param bool $mutilple
     * @return array|bool
     */
    public function getDeskOrder($cond,$mutilple=true){
        if($mutilple){
            $list=pdo_getall('cqkundian_ordering_desk_order',$cond,'','','create_time desc');
        }else{
            $list=pdo_get('cqkundian_ordering_desk_order',$cond);
        }
        return $list;
    }

    /**
     * 更新订单信息
     * @param $updateData
     * @param $cond
     * @return bool
     */
    public function updateOrderData($updateData,$cond){
        if(!empty($cond)){
            $res=pdo_update('cqkundian_ordering_order',$updateData,$cond);
            return $res ? true: false;
        }else{
            return false;
        }
    }

    /**
     * 更新预约订单信息
     * @param $updateData
     * @param $cond
     * @return bool
     */
    public function updateMakeOrder($updateData,$cond){
        if(!empty($cond)){
            $res=pdo_update('cqkundian_ordering_make_order',$updateData,$cond);
            return $res ? true: false;
        }else{
            return false;
        }
    }

}