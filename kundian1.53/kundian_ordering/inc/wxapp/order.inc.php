<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 10:07
 */
defined("IN_IA") or exit("Access Denied");
require_once ROOT_KUNDIAN_ORDERING.'model/order.php';
require_once ROOT_KUNDIAN_ORDERING.'model/product.php';
class OrderController{
    public $uniacid='';
    public $uid='';
    static $order='';
    static $product='';
    public function __construct(){
        global $_GPC;
        $this->uniacid=$_GPC['uniacid'];
        $this->uid=$_GPC['uid'];
        self::$order=new Order_KundianOrderingModel();
        self::$product=new Product_KundianOrderingModel();
    }

    public function getAll($get){
        $is_active=$get['is_active'];
        if($is_active==1) { //外卖
            $order_where = array(
                'uniacid' => $this->uniacid,
                'uid' => $this->uid,
                'is_change in' => array(1, 2),
                'is_fast_food' => 0 //非快餐
            );
            $orderData=self::$order->selectOrderList($order_where,$this->uniacid);
        }elseif ($is_active==3){
            $orderData=self::$order->getMakeOrderList(['uid'=>$this->uid,'uniacid'=>$get['uniacid']],1,10);

        }elseif ($is_active==4){
            $order_where = array(
                'uniacid' => $this->uniacid,
                'uid' => $this->uid,
                'is_fast_food' => 1 //快餐
            );
            $orderData=self::$order->selectOrderList($order_where,$this->uniacid);
        }
        $request['orderData']=$orderData;
        if(!empty($request['orderData'])) {
            foreach ($request['orderData'] as $k => $v) {
                $request['orderData'][$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            }
        }
        echo json_encode($request);die;
    }

    public function getMoreData($get){
        $is_active=$get['is_active'];
        $page=$get['page'];
        if($is_active==1) {
            $order_where = array(
                'uniacid' => $this->uniacid,
                'uid' => $this->uid,
                'is_change in' => array(1, 2),
                'is_fast_food'=>0,
            );
            $orderData=self::$order->selectOrderList($order_where,$this->uniacid,$page+1,10);
        }elseif ($is_active==3){
            $orderData=self::$order->getMakeOrderList(['uniacid'=>$this->uniacid,'uid'=>$this->uid],$page+1,10);
        }elseif ($is_active==4){
            $order_where = array(
                'uniacid' => $this->uniacid,
                'uid' => $this->uid,
                'is_change in' => array(1, 2),
                'is_fast_food'=>1,
            );
            $orderData=self::$order->selectOrderList($order_where,$this->uniacid,$page+1,10);
        }
        $request['orderData']=$orderData;
        echo json_encode($request);die;
    }

    public function confirmGoods($get){
        $order_id=$get['orderid'];
        $res=pdo_update("cqkundian_ordering_order",array('is_pay'=>3,'sent_time'=>time()), array('uniacid'=>$this->uniacid,'id'=>$order_id));
        if($res){
            echo json_encode(array('code'=>1));die;
        }else{
            echo json_encode(array('code'=>0));die;
        }
    }

    public function deleteOrder($get){
        $order_id=$get['orderid'];
        $orderData=pdo_get("cqkundian_ordering_order",array('id'=>$order_id,'uniacid'=>$this->uniacid));

        if($orderData['is_pay']==0){
            $res=pdo_delete("cqkundian_ordering_order",array('id'=>$order_id,'uniacid'=>$this->uniacid));
            if($res){
                echo json_encode(array('code'=>1));die;
            }else{
                echo json_encode(array('code'=>0));die;
            }
        }elseif ($orderData['is_confirm']==1){
            $res=pdo_delete("cqkundian_ordering_order",array('id'=>$order_id,'uniacid'=>$this->uniacid));
            if($res){
                echo json_encode(array('code'=>1));die;
            }else{
                echo json_encode(array('code'=>0));die;
            }
        }else{
            echo json_encode(array('code'=>2));die;
        }
    }

    public function cancelWeiOrder($get){
        $uid=$get['uid'];
        $orderid=$get['orderid'];
        $orderData=pdo_get('cqkundian_ordering_order',array('uniacid'=>$this->uniacid,'id'=>$orderid));
        $res=pdo_update('cqkundian_ordering_order',array('is_pay'=>5),array('id'=>$orderid,'uid'=>$this->uid,'uniacid'=>$this->uniacid));
        if($res){
            include 'function.php';
            $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$this->uniacid));
            //给店家推送消息
            $peiPerson=pdo_getall('cqkundian_ordering_cancel_person',array('uniacid'=>$this->uniacid,'type'=>2));
            for ($i=0;$i<count($peiPerson);$i++){
                $res_send_shop=send_template_cancel_message($peiPerson[$i]['wx_openid'],$msgConfig['wx_cancel_order_template'],$orderData,$msgConfig['wx_appid'],$msgConfig['wx_secret'],$this->uniacid);
            }
            echo json_encode(array('code'=>1,'send_res'=>$res_send_shop));
        }else{
            echo json_encode(array('code'=>2));
        }
        die;
    }

    public function cancelPayOrder($get){
        $orderid=$get['orderid'];
        $orderData=pdo_get('cqkundian_ordering_order',array('uniacid'=>$this->uniacid,'id'=>$orderid));
        $res=pdo_update('cqkundian_ordering_order',array('is_pay'=>4),array('id'=>$orderid,'uid'=>$this->uid,'uniacid'=>$this->uniacid));
        if($res){
            include 'function.php';
            $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$this->uniacid));
            //给店家推送消息
            $peiPerson=pdo_getall('cqkundian_ordering_cancel_person',array('uniacid'=>$this->uniacid,'type'=>2));
            for ($i=0;$i<count($peiPerson);$i++){
                $res_send_shop=send_template_cancel_message($peiPerson[$i]['wx_openid'],$msgConfig['wx_cancel_order_template'],$orderData,$msgConfig['wx_appid'],$msgConfig['wx_secret'],$this->uniacid);
            }
            echo json_encode(array('code'=>1));
        }else{
            echo json_encode(array('code'=>2));
        }
    }
}