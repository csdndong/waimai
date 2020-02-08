<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9 0009
 * Time: 15:59
 */

defined("IN_IA") or exit("Access Denied");
global $_W,$_GPC;
$ops=array('index','doTransfer','notify','getIndex');
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :'index';
switch ($op){
    case 'index':
        echo json_encode(array('code'=>1));
        break;

    case 'doTransfer':
        $uniacid=$_GPC['uniacid'];
        $uid=$_GPC['uid'];
        $price=$_GPC['price'];

        //生成订单信息
        $insertData=array(
            'order_number'=>time().rand(1000,9999),
            'price'=>$price,
            'create_time'=>time(),
            'uid'=>$uid,
            'gift_sub_price'=>0,
            "is_pay"=>0,   //已支付
            'pay_time'=>time(),
            'uniacid'=>$uniacid,
            'pra_price'=>0,   //实际支付金额
            'is_change'=>3,  //转账
        );
        $orderRes=pdo_insert("cqkundian_ordering_order",$insertData);

        $order_id=pdo_insertid();
        if($order_id){
            echo json_encode(array('code'=>1,'order_id'=>$order_id));
        }else{
            echo json_encode(array('code'=>0));
        }
        break;


    case 'notify':
        $orderid=$_GPC['order_id'];
        $uniacid=$_GPC['uniacid'];
        $res=pdo_update("cqkundian_ordering_order",array('is_pay'=>1,'pay_time'=>time()),array('uniacid'=>$uniacid,'id'=>$orderid));
        if($res){
            echo json_encode(array('code'=>1));
        }else{
            echo json_encode(array('code'=>2));
        }
        break;

    case 'getIndex':
        $uniacid=$_GPC['uniacid'];
        $aboutData=pdo_get("cqkundian_ordering_about",array('uniacid'=>$uniacid));
        echo json_encode(array('aboutData'=>$aboutData));
        break;
}
