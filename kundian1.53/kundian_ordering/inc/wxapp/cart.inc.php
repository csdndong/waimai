<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 16:30
 */
defined("IN_IA") or exit("Access Denied");
global $_W,$_GPC;
$ops=array('index','addCart','getCart','jiaCount','jianCount','deleteCart','orderMake','sendMsg','getOrdering','getDetailOrder','deleteOrder','getOrderingCart');
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :'index';

switch ($op){
    case 'index':
        echo json_encode(array('code'=>0));
        break;

    case 'addCart':   //加入购物车
        $uniacid=$_GPC['uniacid'];
        $uid=$_GPC['uid'];
        $pid=$_GPC['pid'];

        //查询商品信息
        $productData=pdo_get("cqkundian_ordering_product",array('uniacid'=>$uniacid,'id'=>$pid));

        $cartData=pdo_get("cqkundian_ordering_cart",array('uid'=>$uid,'pid'=>$pid,'uniacid'=>$uniacid));
        if(empty($cartData)){
            $insertData=array(
                'uid'=>$uid,
                'uniacid'=>$uniacid,
                'create_time'=>time(),
                'pid'=>$pid,
                'price'=>$productData['price'],
                'count'=>1,
            );
            $res=pdo_insert("cqkundian_ordering_cart",$insertData);
        }else{
            $res=pdo_update("cqkundian_ordering_cart",array('count'=>$cartData['count']+1),array('uid'=>$uid,'pid'=>$pid,'uniacid'=>$uniacid));
        }

        if($res){
            echo json_encode(array('code'=>1,'msg'=>'success'));die;
        }else{
            echo json_encode(array('code'=>0,'msg'=>"error"));die;
        }
        break;

    case 'getCart':  //获取购物车列表
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $uid=$_GPC['uid'];
        $cart_where=array(
            'uniacid'=>$uniacid,
            'uid'=>$uid,
        );
        $cartData=pdo_getall("cqkundian_ordering_cart",$cart_where,'','',array('create_time'=>'desc'),array(0,10));
        for ($i=0;$i<count($cartData);$i++){
            $productData=pdo_get("cqkundian_ordering_product",array('uniacid'=>$uniacid,'id'=>$cartData[$i]['pid']));
            $cartData[$i]['product_name']=$productData['product_name'];
            $cartData[$i]['old_price']=$productData['old_price'];
            $cartData[$i]['cover']=$productData['cover'];
            $cartData[$i]['is_check']=false;
        }
        $request['cartData']=$cartData;
        echo json_encode($request);
        break;

    case 'jiaCount':    //增加订餐数量
        $uniacid=$_GPC['uniacid'];
        $cart_id=$_GPC['cart_id'];
        $cartData=pdo_get("cqkundian_ordering_cart",array('id'=>$cart_id,'uniacid'=>$uniacid));
        $res=pdo_update("cqkundian_ordering_cart",array('count'=>$cartData['count']+1),array('id'=>$cart_id,'uniacid'=>$uniacid));
        if($res){
            echo json_encode(array('code'=>1));die;
        }else{
            echo json_encode(array('code'=>0,'msg'=>'error'));die;
        }
        break;

    case 'jianCount':
        $uniacid=$_GPC['uniacid'];
        $cart_id=$_GPC['cart_id'];
        $cartData=pdo_get("cqkundian_ordering_cart",array('id'=>$cart_id,'uniacid'=>$uniacid));
        if($cartData['count']==1){
            echo json_encode(array('code'=>2));die;
        }else{
            $res=pdo_update("cqkundian_ordering_cart",array('count'=>$cartData['count']-1),array('id'=>$cart_id,'uniacid'=>$uniacid));
            if($res){
                echo json_encode(array('code'=>1));die;
            }else{
                echo json_encode(array('code'=>0,'msg'=>'error'));die;
            }
        }
        break;

    case 'deleteCart':  //删除购物车数据
        $cart_id=trim($_GPC['cart_id']);
        $new_id=explode('_',$cart_id);
        $uniacid=$_GPC['uniacid'];
        $res=0;
        for ($i=0;$i<count($new_id);$i++){
            $res+=pdo_delete("cqkundian_ordering_cart",array('id'=>$new_id[$i],'uniacid'=>$uniacid));
        }
        if($res>0){
            echo json_encode(array('code'=>1));die;
        }else{
            echo json_encode(array('code'=>0));die;
        }
        break;

    case 'orderMake':   //预约
        $order_type=$_GPC['order_type'];
        $uniacid=$_GPC['uniacid'];
        $uid=$_GPC['uid'];
        $name=$_GPC['name'];
        $phone=$_GPC['phone'];
        $date=$_GPC['date'];
        $time=$_GPC['time'];
        $person_count=$_GPC['person_count'];
        $remark=$_GPC['remark'];
        $insertData = array(
            'uid' => $uid,
            'uniacid' => $uniacid,
            'name' => $name,
            'phone' => $phone,
            'use_date' => $date,
            'use_time' => $time,
            'person_count' => $person_count,
            'create_time' => time(),
            'remark' => $remark,
        );
        $res=pdo_insert('cqkundian_ordering_make_order',$insertData);
        $orderid=pdo_insertid();
        if($res){
            //给店家推送消息
            include 'function.php';
            $peiPerson=pdo_getall('cqkundian_ordering_cancel_person',array('uniacid'=>$uniacid,'type'=>2));
            for ($i=0;$i<count($peiPerson);$i++){
                $res_send_shop=send_make_order_message($peiPerson[$i]['wx_openid'],$orderid,$uniacid);
            }
            echo json_encode(array('code'=>1));
        }else{
            echo json_encode(array('code'=>2));
        }
        break;

    case 'sendMsg':
        include "alidayu/top/TopClient.php";
        include  "alidayu/top/request/AlibabaAliqinFcSmsNumSendRequest.php";
        include "alidayu/top/ResultSet.php";
        include "alidayu/top/RequestCheckUtil.php";
        include "alidayu/top/TopLogger.php";
        $uniacid=$_GPC['uniacid'];
        $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
        $c = new TopClient();
        $c->appkey = $msgConfig['appkey'];
        $c->secretKey = $msgConfig['secret'];
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req ->setExtend( "" );
        $req ->setSmsType( "normal" );
        $req ->setSmsFreeSignName( $msgConfig['sign_name'] );
        $req ->setSmsParam( "{orderno:'2324324'}" );
        $req ->setRecNum( $msgConfig['phone'] );
        $req ->setSmsTemplateCode( $msgConfig['template_code'] );
        $resp = $c ->execute( $req );
        var_dump($resp);
        break;

    case 'getOrdering':
        $request=array();
        $uid=$_GPC['uid'];
        $uniacid=$_GPC['uniacid'];
        $orderingData=pdo_getall("cqkundian_ordering_make_order",array('uniacid'=>$uniacid,'uid'=>$uid),'','','create_time desc');
        for ($i=0;$i<count($orderingData);$i++){
            $detailData=pdo_getall("cqkundian_ordering_make_order_detail",array('mid'=>$orderingData[$i]['id'],'uniacid'=>$uniacid));
            $orderingData[$i]['orderDetail']=$detailData;
            $orderingData[$i]['create_time']=date("Y-m-d H:i:s",$orderingData[$i]['create_time']);
        }
        $request['orderingRecord']=$orderingData;
        echo json_encode($request);
        break;

    case 'getDetailOrder':
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $mid=$_GPC['order_id'];
        $orderingData=pdo_get("cqkundian_ordering_make_order",array('uniacid'=>$uniacid,'id'=>$mid));
        $orderingData['create_time']=date("Y-m-d H:i:s",$orderingData['create_time']);
        $orderDetail=pdo_getall("cqkundian_ordering_make_order_detail",array('mid'=>$orderingData['id'],'uniacid'=>$uniacid));
        $request['orderData']=$orderingData;
        $request['orderDetailData']=$orderDetail;
        echo json_encode($request);
        break;

    case 'deleteOrder':
        $uniacid=$_GPC['uniacid'];
        $orderid=$_GPC['orderid'];
        $orderData=pdo_get("cqkundian_ordering_make_order",array('id'=>$orderid,'uniacid'=>$uniacid));
        if($orderData['is_use']==1){
            $res=pdo_delete("cqkundian_ordering_make_order",array('id'=>$orderid,'uniacid'=>$uniacid));
            $res1=pdo_delete("cqkundian_ordering_make_order_detail",array('mid'=>$orderid,'uniacid'=>$uniacid));
            if($res && $res1){
                echo json_encode(array('code'=>1));die;
            }else{
                echo json_encode(array('code'=>0));die;
            }
        }else{
            echo json_encode(array('code'=>2));die;
        }
        break;

    case 'getOrderingCart':
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $cardid=$_GPC['cardid'];
        $order_type=$_GPC['order_type'];
        $total_price = 0;
        if($order_type==1){
            $cartData=array();
            $productData=pdo_get("cqkundian_ordering_product",array('uniacid'=>$uniacid,'id'=>$cardid));
            $cartData[0]['product_name'] = $productData['product_name'];
            $cartData[0]['cover'] = $productData['cover'];
            $cartData[0]['old_price'] = $productData['old_price'];
            $cartData[0]['count'] = 1;
            $cartData[0]['price'] = $productData['price'];
            $cartData[0]['old_price'] = $productData['old_price'];
            $cartData[0]['cover'] = $productData['cover'];
            $total_price = $productData['price'];
        }else {

            $card_id = explode("_", $cardid);
            $cartData = array();

            for ($i = 0; $i < count($card_id); $i++) {
                $cartData[$i] = pdo_get("cqkundian_ordering_cart", array('uniacid' => $uniacid, 'id' => $card_id[$i]));
                $productData = pdo_get("cqkundian_ordering_product", array('uniacid' => $uniacid, 'id' => $cartData[$i]['pid']));
                $cartData[$i]['product_name'] = $productData['product_name'];
                $cartData[$i]['cover'] = $productData['cover'];
                $cartData[$i]['old_price'] = $productData['old_price'];
                $total_price += $cartData[$i]['count'] * $cartData[$i]['price'];
            }
        }
        $request['cartData'] = $cartData;
        $request['total_price'] = $total_price;
        echo json_encode($request);
        break;
}
