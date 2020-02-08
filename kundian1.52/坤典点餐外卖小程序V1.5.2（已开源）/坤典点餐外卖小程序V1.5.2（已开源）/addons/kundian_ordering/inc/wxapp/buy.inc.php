<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/5 0005
 * Time: 16:12
 */
defined("IN_IA") or exit("Access Denied");
require_once ROOT_KUNDIAN_ORDERING.'model/common.php';
require_once ROOT_KUNDIAN_ORDERING.'inc/wxapp/function.php';
require_once ROOT_KUNDIAN_ORDERING.'model/product.php';
class BuyController{
    public $uniacid='';
    public $uid='';
    static $product='';
    static $common='';

    public function __construct(){
        global $_GPC;
        $this->uniacid=$_GPC['uniacid'];
        $this->uid=$_GPC['uid'];
        self::$product=new Product_KundianOrderingModel();
        self::$common=new Common_KundianOrderingModel();
    }

    public function getNewOrder($get){
        $totalPrice=$get['totalPrice'];
        $aboutData=pdo_get("cqkundian_ordering_about",array('uniacid'=>$this->uniacid));
        $send_time=time()+intval($aboutData['send_time']*60);
        $request['send_time']=date("H:i",$send_time);
        $request['aboutData']=$aboutData;
        if($aboutData['is_jian_send_price']==1){
            if($totalPrice < $aboutData['man_price']){
                $request['totalPrice']=$totalPrice+$aboutData['send_price'];
            }else{
                $request['totalPrice']=$totalPrice;
            }
        }else{
            $request['totalPrice']=$totalPrice+$aboutData['send_price'];
        }
        $delivery_con=array(
            'ikey'=>array('pay_on_delivery'),
            'uniacid'=>$this->uniacid,
        );
        $deliveryData=pdo_get('cqkundian_ordering_set',$delivery_con);
        $request['deliveryData']=$deliveryData;

        $request['address']=pdo_get('cqkundian_ordering_address',['uid'=>$this->uid,'uniacid'=>$this->uniacid,'is_default'=>1]);

        echo json_encode($request);
    }

    public function subOrder($get){
        $index=$get['index'];
        $aboutData=self::$common->getAboutData($this->uniacid);
        //总价
        $totalPrice=floatval($get['totalPrice']);
        //接收传输的商品信息
        $cartData=json_decode($_POST['cartData']);
        //打包费
        if((int)$get['isPackage']){
            $totalPrice+=$aboutData['package_price'];
        }
        $insertData=array(
            'order_number'=>time().rand(1000,9999),
            'price'=>$totalPrice,
            'create_time'=>time(),
            'uid'=>$this->uid,
            'gift_sub_price'=>0,
            'pay_time'=>time(),
            'uniacid'=>$this->uniacid,
            'pra_price'=>0,   //实际支付金额
            'name'=>$get['userName'],
            'phone'=>$get['phone'],
            'address'=>$get['address'],
            'is_change'=>1,  //购买
            'remark'=>$get['remark'],
            'pei_time'=>$get['time'],

        );
        //付款方式
        if($index==1){
            $insertData['is_pay']=-1;
            $insertData['pay_method']='货到付款';
        }else{
            $insertData['is_pay']=0;
            $insertData['pay_method']='微信支付';
        }

        //打包
        if((int)$get['isPackage']){
            $insertData['package_price']=$aboutData['package_price'];
        }
        if(!empty($get['is_fast_food'])){
            $insertData['is_fast_food']=1;
        }

        $orderRes=pdo_insert("cqkundian_ordering_order",$insertData);
        if(!empty($orderRes)){
            $order_id=pdo_insertid();
            //订单详情
            $detail_res=0;
            for ($i=0;$i<count($cartData);$i++){
                $insertOrderDetailData=array(
                    'pid'=>$cartData[$i]->id,
                    'order_id'=>$order_id,
                    'num'=>$cartData[$i]->selectNum,
                    'total_price'=>$cartData[$i]->selectNum*$cartData[$i]->price,
                    'uniacid'=>$this->uniacid,
                );
                $detail_res+=pdo_insert("cqkundian_ordering_order_detail",$insertOrderDetailData);  //插入订单详细信息
            }
            if($order_id && $detail_res){
                echo json_encode(array('code'=>0,'msg'=>'ok','order_id'=>$order_id));die;
            }
            echo json_encode(array('code'=>1,'msg'=>'生成订单失败','order_id'=>$order_id));die;
        }
        echo json_encode(array('code'=>7,'msg'=>"生成订单失败"));die;
    }

    public function notify($get){
        global $_W;
        //修改订单支付状态 实际支付金额，支付时间
        $orderid=$get['order_id'];
        $uniacid=$get['uniacid'];
        $orderData=pdo_get("cqkundian_ordering_order",array("uniacid"=>$uniacid,'id'=>$orderid));
        if($orderData['is_pay']==1 || $orderData['is_pay']==-1){
            //更新库存
            self::$product->updateProductCount($orderid,$uniacid);
            if($get['prepay_id']) {
                $prepay_id_str = $get['prepay_id'];
                $prepay_id = explode('=', $prepay_id_str);
                send_msg_to_user($orderData,$prepay_id[1],$_W['openid'],$uniacid);
            }
            //给店家推送消息
            $peiPerson=pdo_getall('cqkundian_ordering_cancel_person',array('uniacid'=>$uniacid,'type'=>2));
            for ($i=0;$i<count($peiPerson);$i++){
                send_template_message($peiPerson[$i]['wx_openid'],$orderData,$uniacid);
            }

            //打印订单
            $res = self::$common->neatenPrintInfo($orderData,$uniacid);
            echo json_encode(array('code'=>0,'msg' => 'ok'));die;
        }
        echo json_encode(array('code'=>2));die;
    }
}