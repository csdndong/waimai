<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1 0001
 * Time: 下午 3:17
 */
defined('IN_IA')or exit("Access Denied");
require_once ROOT_KUNDIAN_ORDERING.'model/order.php';
class ManagerController{
    public $uniacid='';
    static $order='';
    public function __construct(){
        global $_GPC;
        $this->uniacid=$_GPC['uniacid'];
        self::$order=new Order_KundianOrderingModel();
    }

    public function getAllOrder($get){
        $is_active=$get['is_active'];
        if($is_active==1) {
            $condition = array(
                'uniacid' => $this->uniacid,
                'is_change in' => array(1, 2),
                'is_fast_food'=>0,
            );

            $orderData=self::$order->getMerchentOrderList($condition,$this->uniacid,1,10);

        }elseif ($is_active==3){
            $orderData=self::$order->getMakeOrderList(['uniacid'=>$this->uniacid],1,10);
        }elseif ($is_active==4){
            $condition = array(
                'uniacid' => $this->uniacid,
                'is_change in' => array(1, 2),
                'is_fast_food'=>1,
            );
            $orderData=self::$order->getMerchentOrderList($condition,$this->uniacid,1,10);
        }
        $request['orderData'] = $orderData;
        echo json_encode($request);die;
    }

    public function getMoreData($get){
        $is_active=$get['is_active'];
        $page=intval($get['page']);
        $request=array();
        if($is_active==1){
            $condition=array(
                'uniacid'=>$this->uniacid,
                'is_change in'=>array(1,2),
                'is_fast_food'=>0,
            );
            $orderData=self::$order->getMerchentOrderList($condition,$this->uniacid,$page+1,10);
        }elseif ($is_active==3){
            $orderData=self::$order->getMakeOrderList(['uniacid'=>$this->uniacid],$page+1,10);
        }elseif ($is_active==4){
            $condition = array(
                'uniacid' => $this->uniacid,
                'is_change in' => array(1, 2),
                'is_fast_food'=>1,
            );
            $orderData=self::$order->getMerchentOrderList($condition,$this->uniacid,$page+1,10);
        }
        $request['orderData']=$orderData;
        echo json_encode($request);die;
    }

    public function cancelOrder($get){
        $res=self::$order->updateOrderData(['is_pay'=>5],['uniacid'=>$this->uniacid,'id'=>$get['orderid']]);
        echo $res ? json_encode(['code'=>1,'msg'=>'操作成功']) : json_encode(['code'=>2,'msg'=>'操作失败']);die;
    }

    public function beginSend($get){
        $res=self::$order->updateOrderData(['is_pay'=>2],['uniacid'=>$this->uniacid,'id'=>$get['orderid']]);
        echo $res ? json_encode(['code'=>1,'msg'=>'操作成功']) : json_encode(['code'=>2,'msg'=>'操作失败']);die;
    }

    public function completeSend($get){
        $res=self::$order->updateOrderData(['is_pay'=>3],['uniacid'=>$this->uniacid,'id'=>$get['orderid']]);
        echo $res ? json_encode(['code'=>1,'msg'=>'操作成功']) : json_encode(['code'=>2,'msg'=>'操作失败']);die;
    }

    public function useMake($get){
        $res=self::$order->updateMakeOrder(['is_use'=>1],['uniacid'=>$this->uniacid,'id'=>$get['orderid']]);
        echo $res ? json_encode(['code'=>1,'msg'=>'操作成功']) : json_encode(['code'=>2,'msg'=>'操作失败']);die;
    }
    public function cancelMake($get){
        $res=self::$order->updateMakeOrder(['is_use'=>3],['uniacid'=>$this->uniacid,'id'=>$get['orderid']]);
        echo $res ? json_encode(['code'=>1,'msg'=>'操作成功']) : json_encode(['code'=>2,'msg'=>'操作失败']);die;
    }
}