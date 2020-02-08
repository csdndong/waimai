<?php
/**
 * 点餐外卖模块小程序接口定义
 *
 * @author cqkundian
 * @url https://s.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
!defined('ROOT_KUNDIAN_ORDERING') && define('ROOT_KUNDIAN_ORDERING', IA_ROOT . '/addons/kundian_ordering/');
class Kundian_orderingModuleWxapp extends WeModuleWxapp {


    public function doPageOrder(){
        global $_GPC, $_W;
        require_once ROOT_KUNDIAN_ORDERING.'inc/wxapp/'.$_GPC['control'].'.inc.php';
        $class=ucfirst($_GPC['control'].'Controller');
        $actionModel=new $class();
        $op=$_GPC['op'];
        $actionModel->$op($_GPC);
    }


    public function doPageIndex(){
        global $_GPC, $_W;
        include 'inc/wxapp/index.inc.php';
        $index=new IndexController();
        $index->$_GPC['op']($_GPC);
    }
    public function doPageProduct(){
        include 'inc/wxapp/product.inc.php';
    }
    public function doPageCart(){
        include 'inc/wxapp/cart.inc.php';
    }
    public function doPageBuy(){
        include 'inc/wxapp/buy.inc.php';
    }
    public function doPagePay() {
        global $_GPC, $_W;
        //获取订单号，保证在业务模块中唯一即可
        $orderid = intval($_GPC['orderid']);
        $orderData=pdo_get("cqkundian_ordering_order",array('id'=>$orderid,'uniacid'=>$_GPC['uniacid']));
        $order = array(
            'tid' => $orderData['order_number'],
            'user' => $_W['openid'], //用户OPENID
            'fee' => 0.01, //金额
            'title' => '购物',
        );

        //生成支付参数，返回给小程序端
        $pay_params = $this->pay($order);
        if (is_error($pay_params)) {
            return $this->result(1, $pay_params['message'],$pay_params);
        }
        cache_write("kundian_ordering_pay_notify_".$_W['openid'],1);  //外卖支付
        return $this->result(0, $pay_params['message'], $pay_params);
    }

    public function doPageDeskPay() {
        global $_GPC, $_W;
        //获取订单号，保证在业务模块中唯一即可
        $orderid = intval($_GPC['orderid']);
        //更新订单号
        $order_number=rand(100, 999) . time() . rand(100, 999);
        $res=pdo_update('cqkundian_ordering_desk_order',array('order_number'=>$order_number),array('id'=>$orderid,'uniacid'=>$_GPC['uniacid']));
        if($res) {
            //查询订单信息
            $orderData = pdo_get("cqkundian_ordering_desk_order", array('id' => $orderid, 'uniacid' => $_GPC['uniacid']));
            $deskData = pdo_get('cqkundian_ordering_desk', array('uniacid' => $_GPC['uniacid'], 'id' => $orderData['desk_id']));
            $order = array(
                'tid' => $orderData['order_number'],
                'user' => $_W['openid'], //用户OPENID
                'fee' => $orderData['total_price'], //金额
                'title' => $deskData['name'] . '在线结账',
            );

            //生成支付参数，返回给小程序端
            $pay_params = $this->pay($order);
            if (is_error($pay_params)) {
                return $this->result(1, '支付失败，请重试', $pay_params);
            }

            return $this->result(0, '', $pay_params);
        }else{
            return $this->result(0,'订单号更新失败');
        }
    }

    public function payResult($log){
        //载入日志函数
        load()->func('logging');
        //记录数组数据
        logging_run($log);
        $order_id = $log['tid'];
        $uniontid = $log['uniontid'];
        $fee = $log['fee'];
        $user=$log['user'];
        $remark=cache_load('kundian_ordering_pay_notify_'.$user);
        if($remark==1){ //外卖支付
            $update_save=array(
                'is_pay'=>1,
                'pra_price'=>$fee,
                'pay_time'=>time(),
                'pay_method'=>'微信支付',
                'uniontid' => $uniontid,
            );
            $res=pdo_update('cqkundian_ordering_order',$update_save,array('order_number'=>$order_id,'uniacid'=>$log['uniacid']));
        }
        if($res){
            cache_delete('kundian_ordering_pay_notify_'.$user);
        }

    }

    /** delete */
    public function doPageShopOrder(){
        include "inc/wxapp/shop_order.inc.php";
    }
    // delete
    public function doPageDesk(){
        include "inc/wxapp/desk.inc.php";
    }
}