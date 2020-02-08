<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/4 0004
 * Time: 17:29
 */
defined('IN_IA')or exit("Access Denied");
!defined('ROOT_KUNDIAN_ORDERING') && define('ROOT_KUNDIAN_ORDERING', IA_ROOT . '/addons/kundian_ordering/');
require_once ROOT_KUNDIAN_ORDERING.'model/order.php';
$orderModel=new Order_KundianOrderingModel();

global $_W,$_GPC;
$op=$_GPC['op'] ?$_GPC['op'] :'getAllOrder';
$uniacid=$_GPC['uniacid'];

if($op=='getAllOrder'){
    $request=array();
    $is_active=$_GPC['is_active'];
    if($is_active==1) {
        $condition = array(
            'uniacid' => $uniacid,
            'is_change in' => array(1, 2),
            'is_fast_food'=>0,
        );

        $orderData=$orderModel->getMerchentOrderList($condition,$uniacid,1,10);

    }elseif ($is_active==3){
        $orderData=$orderModel->getMakeOrderList(['uniacid'=>$uniacid],1,10);
    }elseif ($is_active==4){
        $condition = array(
            'uniacid' => $uniacid,
            'is_change in' => array(1, 2),
            'is_fast_food'=>1,
        );
        $orderData=$orderModel->getMerchentOrderList($condition,$uniacid,1,10);
    }
    $request['orderData'] = $orderData;
    echo json_encode($request);die;
}

//懒加载更多数据
if($op=='getMoreData'){
    $is_active=$_GPC['is_active'];
    $page=intval($_GPC['page']);
    $request=array();
    if($is_active==1){
        $condition=array(
            'uniacid'=>$uniacid,
            'is_change in'=>array(1,2),
            'is_fast_food'=>0,
        );
        $orderData=$orderModel->getMerchentOrderList($condition,$uniacid,$page+1,10);
    }elseif ($is_active==3){
        $orderData=$orderModel->getMakeOrderList(['uniacid'=>$uniacid],$page+1,10);
    }elseif ($is_active==4){
        $condition = array(
            'uniacid' => $uniacid,
            'is_change in' => array(1, 2),
            'is_fast_food'=>1,
        );
        $orderData=$orderModel->getMerchentOrderList($condition,$uniacid,$page+1,10);
    }
    $request['orderData']=$orderData;
    echo json_encode($request);die;
}

//取消订单
if($op=='cancelOrder'){
    $order_id=$_GPC['orderid'];
    $res=$orderModel->updateOrderData(['is_pay'=>5],['uniacid'=>$uniacid,'id'=>$order_id]);
    if($res){
        echo json_encode(array('code'=>1));
    }else{
        echo json_encode(array('code'=>2));
    }
    die;
}

//开始配送
if($op=='beginSend'){
    $order_id=$_GPC['orderid'];
    $res=$orderModel->updateOrderData(['is_pay'=>2],['uniacid'=>$uniacid,'id'=>$order_id]);
    if($res){
        echo json_encode(array('code'=>1));
    }else{
        echo json_encode(array('code'=>2));
    }
    die;
}

//完成配送
if($op=='completeSend'){
    $order_id=$_GPC['orderid'];
    $res=$orderModel->updateOrderData(['is_pay'=>3],['uniacid'=>$uniacid,'id'=>$order_id]);
    if($res){
        echo json_encode(array('code'=>1));
    }else{
        echo json_encode(array('code'=>2));
    }
    die;
}

//已用餐
if($op=='useMake'){
    $order_id=$_GPC['orderid'];
    $res=$orderModel->updateMakeOrder(['is_use'=>1],['uniacid'=>$uniacid,'id'=>$order_id]);
    if($res){
        echo json_encode(array('code'=>1));
    }else{
        echo json_encode(array('code'=>2));
    }
    die;
}

//取消预订
if($op=='cancelMake'){
    $order_id=$_GPC['orderid'];
    $res=$orderModel->updateMakeOrder(['is_use'=>3],['uniacid'=>$uniacid,'id'=>$order_id]);
    if($res){
        echo json_encode(array('code'=>1));
    }else{
        echo json_encode(array('code'=>2));
    }
    die;
}