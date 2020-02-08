<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17 0017
 * Time: 17:38
 */
defined("IN_IA") or exit("Access denied");
checklogin();
global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$op=$_GPC['op']? $_GPC['op'] :"list";

if($op=='list'){
    $condition=array();
    $old_time=array(
        'start'=>'1900-01-01',
        'end'=>date('Y-m-d',strtotime('+1 days'))
    );
    if(!empty($_GPC['order_number'])){
        $order_number=trim($_GPC['order_number']);
        $condition['order_number LIKE']= '%'.$order_number.'%';
    }

    $status=$_GPC['status'];    //订单状态  1/全部订单 2/未支付 3/待发货 4/待收货 5/已完成 6/已取消
    if($status==2){
        $condition['is_pay']=0;  //未支付
    }elseif ($status==3){
        $condition['is_pay']=1;
    }elseif ($status==4){
        $condition['is_pay']=2;
    }elseif ($status==5){
        $condition['is_pay']=3;
    }elseif ($status==6){
        $condition['is_pay']=5;
    }
    $time=$_GPC['time'];
    if($time){
        $condition['create_time >']=strtotime($time['start']);
        $condition['create_time <']=strtotime($time['end']);
        if($time['start']==$old_time['start']){
            $un_time=true;
        }else{
            $old_time=$time;
            $un_time=false;
        }
    }else{
        $un_time=true;
    }
    $condition['uniacid']=$uniacid;
    if($_GPC['order_number']||$_GPC['status']||$un_time==false){
        $list=pdo_getall("cqkundian_ordering_order",$condition,'','','create_time desc');
        $totalMoney = 0;
        for ($i = 0; $i < count($list); $i++) {
            $totalMoney += $list[$i]['price'];
        }
    }else {
        $listCount = pdo_getall("cqkundian_ordering_order", $condition);
        $totalMoney = 0;
        for ($i = 0; $i < count($listCount); $i++) {
            $totalMoney += $listCount[$i]['price'];
        }
        $total = count($listCount);   //数据的总条数
        $pageSize = 15; //每页显示的数据条数
        $pages = ceil($total / $pageSize);
        if (empty($_GPC['page'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $_GPC['page'] + 1;
        }
        $pageIndex = intval($_GPC['page']) ? intval($_GPC['page']) : 1;  //当前页
        $pager = pagination($total, $pageIndex, $pageSize);
        $list = pdo_getall("cqkundian_ordering_order", $condition, '', '', 'create_time desc', array($pageIndex, $pageSize));
    }
    include $this->template('web/order/index');
}
if($op=='edit'){
//查询产品信息
    $id=$_GPC['id'];
    $orderData=pdo_get("cqkundian_ordering_order",array('id'=>$id,'uniacid'=>$uniacid));
    $orderDetail=pdo_getall("cqkundian_ordering_order_detail",array('order_id'=>$id,'uniacid'=>$uniacid));
    for ($i=0;$i<count($orderDetail);$i++){
        $productData=pdo_get("cqkundian_ordering_product",array('id'=>$orderDetail[$i]['pid'],'uniacid'=>$uniacid));
        $orderDetail[$i]['product_name']=$productData['product_name'];
        $orderDetail[$i]['cover']=$productData['cover'];
        $orderDetail[$i]['dan_price']=$productData['price'];
    }

    //查询卡券信息
    $tokenData=pdo_get("cqkundian_ordering_token",array('id'=>$orderData['tid'],'uniacid'=>$uniacid));
    $merchantData=pdo_get("cqkundian_ordering_customer",array('id'=>$tokenData['cid'],'uniacid'=>$uniacid));

    include $this->template("web/order/edit");
}
if($op=='sendGoods'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_order",array('is_send'=>1),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}
if($op=='is_pay_change'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_order",array('is_pay'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}
if($op=='is_send_change'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_order",array('is_send'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}
if($op=='is_confirm_change'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_order",array('is_confirm'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}
if($op=='delete'){
    $condition=array();
    $condition['id']=$_GPC['id'];
    $condition['uniacid']=$uniacid;
    $request=pdo_delete("cqkundian_ordering_order",$condition);
    $request1=pdo_delete("cqkundian_ordering_order_detail",array('order_id'=>$_GPC['id'],'uniacid'=>$uniacid));
    if($request || $request1){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}
if($op=='send_goods'){
    $order_id=$_GPC['order_id'];
    $send_number=$_GPC['send_number'];
    $res=pdo_update("cqkundian_ordering_order",array('is_pay'=>2,'send_number'=>$send_number,'sent_time'=>time()),array('id'=>$order_id,'uniacid'=>$uniacid));
    if($res){
        echo json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}
if($op=='confirmGoods'){
    $order_id=$_GPC['order_id'];
    $send_number=$_GPC['send_number'];
    $res=pdo_update("cqkundian_ordering_order",array('is_pay'=>3),array('id'=>$order_id,'uniacid'=>$uniacid));
    if($res){
        echo json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}
if($op=='outOrder'){
    $data[][0]=array('订单编号','订单金额','收货人姓名','联系电话','下单时间','是否支付','订单类型','快递单号');
    $bid=$_GPC['id'];
    $condition=array();
    if(!empty($_GPC['order_number'])){
        $order_number=trim($_GPC['order_number']);
        $condition['order_number LIKE']= '%'.$order_number.'%';
    }

    $status=$_GPC['status'];    //订单状态  1/全部订单 2/未支付 3/待发货 4/待收货 5/已完成 6/已取消
    if($status==2){
        $condition['is_pay']=0;  //未支付
    }elseif ($status==3){
        $condition['is_pay']=1;
        $condition['is_send']=0;
    }elseif ($status==4){
        $condition['is_pay']=1;
        $condition['is_send']=1;
        $condition['is_confirm']=0;
    }elseif ($status==5){
        $condition['is_pay']=1;
        $condition['is_send']=1;
        $condition['is_confirm']=1;
    }elseif ($status==6){
        $condition['is_cancel']=1;
    }
    if($_GPC['begin_time'] && $_GPC['end_time']){
        $begin_time=$_GPC['begin_time'];
        $end_time=$_GPC['end_time'];
        $condition['create_time >']=strtotime($begin_time);
        $condition['create_time <']=strtotime($end_time);
    }
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_order",$condition,'','','create_time desc');

    //循环遍历整理卡券信息
    $orderData=array();
    for ($i=0;$i<count($listCount);$i++){
        $orderData[$i]['order_number']=' '.$listCount[$i]['order_number'];
        $orderData[$i]['price']=$listCount[$i]['price'];
        $orderData[$i]['name']=' '.$listCount[$i]['name'];
        $orderData[$i]['phone']=' '.$listCount[$i]['phone'];
        $orderData[$i]['create_time']=' '.date("Y-m-d H:i:s",$listCount[$i]['create_time']);
        if($listCount[$i]['is_pay']==1){
            $orderData[$i]['is_pay']="已支付";
        }else{
            $orderData[$i]['is_pay']="未支付";
        }

        if($listCount[$i]['is_change']==1){
            $orderData[$i]['is_change']="购买";
        }else{
            $orderData[$i]['is_change']="兑换";
        }
        $orderData[$i]['send_number']=$listCount[$i]['send_number'];
    }
    $data[]=$orderData;
    require_once "Org/PHPExcel.class.php";
    require_once "Org/PHPExcel/Writer/Excel5.php";
    require_once "Org/PHPExcel/IOFactory.php";
    require_once "Org/function.php";
    $filename="订单";
    getExcel($filename,$data);
}


//取消订单
if($op=='cancelGoods'){
    $order_id=$_GPC['order_id'];
    $res=pdo_update('cqkundian_ordering_order',array('is_pay'=>5),array('uniacid'=>$uniacid,'id'=>$order_id));
    if($res){
        echo json_encode(array('status'=>1));
    }else{
        echo json_encode(array('status'=>2));
    }
    die;
}

if($op=='cancelPayOrder'){
    $order_id=$_GPC['order_id'];
    $res=pdo_update('cqkundian_ordering_order',array('is_pay'=>4),array('uniacid'=>$uniacid,'id'=>$order_id));
    if($res){
        echo json_encode(array('status'=>1));
    }else{
        echo json_encode(array('status'=>2));
    }
    die;
}

