<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/23 0023
 * Time: 16:26
 */

defined("IN_IA") or exit("Access Denied");
global $_W,$_GPC;
$ops=array('index','getProductDetail','exchange','getExchange','doExchange','getProductList','getMoreList','getExchangeData');
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :'index';
switch ($op){
    case 'index':
        echo json_encode(array('code'=>0));
        break;
    case 'getProductDetail':   //获取商品的详情
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $pid=$_GPC['pid'];
        $detail_where=array(
            'id'=>$pid,
            'uniacid'=>$uniacid,
        );
        $productData=pdo_get("cqkundian_ordering_product",$detail_where);
        $slide_src=unserialize($productData['slide_src']);

        $aboutData=pdo_get("cqkundian_ordering_about",array('uniacid'=>$uniacid));

        $request['productData']=$productData;
        $request['slide_src']=$slide_src;
        $request['phone']=$aboutData['phone'];
        echo json_encode($request);
        break;

    case 'exchange':    //生成兑换记录
        $uniacid=$_GPC['uniacid'];
        $card_num=trim($_GPC['card_num']);
        $password=trim($_GPC['password']);
        $pid=$_GPC['pid'];
        $uid=$_GPC['uid'];

        //检测卡密是否输入正确
        $token_where=array(
            'card_num'=>$card_num,
            'password'=>$password,
            'uniacid'=>$uniacid,
        );
        $tokenData=pdo_get("cqkundian_ordering_token",$token_where);
        //判断是否存在卡券
        if(empty($tokenData)){
            echo json_encode(array('code'=>1,'msg'=>'卡号或密码输入错误'));die;
        }
        //查询卡券批次
        $batchData=pdo_get("cqkundian_ordering_batch",array('uniacid'=>$uniacid,'id'=>$tokenData['bid']));

        //查询卡券等级
        $levelData=pdo_get("cqkundian_ordering_giftlevel",array('uniacid'=>$uniacid,'id'=>$batchData['lid']));

        //判断卡券是否过期

        if($batchData['expire_time'] < time()){  //已经过期
            echo json_encode(array('code'=>2,'msg'=>'卡券已过期'));die;
        }

        //判断卡券是否售出
        if($tokenData['is_sale']==0){  //卡券未售出
            echo json_encode(array('code'=>3,'msg'=>'卡券还未投入使用'));die;
        }

        //判断卡券是否使用

        if($tokenData['is_use']==1){  //卡券已经使用
            echo json_encode(array('code'=>4,'msg'=>'卡券已使用'));die;
        }

        //判断该卡券是否可以兑换该商品
        $productData=pdo_get("cqkundian_ordering_product",array('id'=>$pid,'uniacid'=>$uniacid));
        if($productData['price'] >$levelData['price']){
            echo json_encode(array('code'=>6,'msg'=>'该卡券不能兑换该礼品'));die;
        }

        //生成兑换记录
        $insertExchange=array(
            'pid'=>$pid,
            'tid'=>$tokenData['id'],
            'uid'=>$uid,
            'uniacid'=>$uniacid,
            'status'=>0,
        );

        $res=pdo_insert("cqkundian_ordering_exchange",$insertExchange);  //新增兑换记录
        $cid=pdo_insertid();
//        $res1=pdo_update("cqkundian_ordering_token",array('is_use'=>1),array('id'=>$tokenData['id'],'uniacid'=>$uniacid));
        if($res){
            echo json_encode(array('code'=>0,'msg'=>'success','data'=>$cid));die;
        }else{
            echo json_encode(array('code'=>5,'msg'=>'error'));die;
        }

        break;

    case 'getExchange':   //确认订单页面获取兑换记录
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $eid=$_GPC['pid'];
        $exchangeData=pdo_get("cqkundian_ordering_exchange",array('id'=>$eid,'uniacid'=>$uniacid));
        $productData=pdo_get("cqkundian_ordering_product",array('id'=>$exchangeData['pid'],'uniacid'=>$uniacid));
        $request['exchangeData']=$exchangeData;
        $request['productData']=$productData;
        echo json_encode($request);
        break;

    case  'doExchange':   //开始兑换，生成订单
        $uniacid=$_GPC['uniacid'];
        $pid=$_GPC['pid'];   //商品id
        $cid=$_GPC['cid'];   //兑换记录id
        $uid=$_GPC['uid'];   //用户id
        $name=$_GPC['name'];
        $phone=$_GPC['phone'];
        $address=$_GPC['address'];
        //查询兑换记录
        $exchangeData=pdo_get("cqkundian_ordering_exchange",array('id'=>$cid,'uniacid'=>$uniacid));
        //查询商品信息
        $productData=pdo_get("cqkundian_ordering_product",array('id'=>$pid,'uniacid'=>$uniacid));
        //查询卡券信息
        $tokenData=pdo_get("cqkundian_ordering_token",array('id'=>$exchangeData['tid'],'uniacid'=>$uniacid));
        //查询卡券批次
        $batchData=pdo_get("cqkundian_ordering_batch",array('uniacid'=>$uniacid,'id'=>$tokenData['bid']));
        //查询卡券等级
        $levelData=pdo_get("cqkundian_ordering_giftlevel",array('uniacid'=>$uniacid,'id'=>$batchData['lid']));

        //判断卡券是否过期
        if($batchData['expire_time'] < time()){  //已经过期
            echo json_encode(array('code'=>2,'msg'=>'卡券已过期'));die;
        }


        //判断卡券是否售出
        if($tokenData['is_sale']==0){  //卡券未售出
            echo json_encode(array('code'=>3,'msg'=>'卡券还未投入使用'));die;
        }

        //判断卡券是否使用
        if($tokenData['is_use']==1){  //卡券已经使用
            echo json_encode(array('code'=>4,'msg'=>'卡券已使用'));die;
        }
        if($productData['price'] >$levelData['price']){
            echo json_encode(array('code'=>6,'msg'=>'该卡券不能兑换该礼品'));die;
        }
        //生成订单
        $insertOrderData=array(
            'order_number'=>time().rand(1000,9999),
            'price'=>$productData['price'],
            'create_time'=>time(),
            'uid'=>$uid,
            'is_send'=>0,
            'is_confirm'=>0,
            'gift_sub_price'=>$levelData['price'],
            'apply_delete'=>0,
            'is_cancel'=>0,
            "is_pay"=>1,   //已支付
            'pay_time'=>time(),
            'uniacid'=>$uniacid,
            'pra_price'=>0,   //实际支付金额
            'pay_method'=>'在线支付',
            'name'=>$name,
            'phone'=>$phone,
            'address'=>$address,
            'is_change'=>2,  //兑换
            'tid'=>$tokenData['id'],
        );
        $orderRes=pdo_insert("cqkundian_ordering_order",$insertOrderData);  //插入订单信息
        if(!empty($orderRes)){
            $order_id=pdo_insertid();
            //订单详情
            $insertOrderDetailData=array(
                'pid'=>$pid,
                'order_id'=>$order_id,
                'num'=>1,
                'total_price'=>0,
                'uniacid'=>$uniacid,
            );
            $detail_res=pdo_insert("cqkundian_ordering_order_detail",$insertOrderDetailData);  //插入订单详细信息

            //修改卡券为使用
            $tokenRes=pdo_update("cqkundian_ordering_token",array('is_use'=>1),array('id'=>$exchangeData['tid'],'uniacid'=>$uniacid));
            //修改兑换记录为兑换成功
            $changeRes=pdo_update("cqkundian_ordering_exchange",array('order_id'=>$order_id,'status'=>1),array('id'=>$exchangeData['id'],'uniacid'=>$uniacid));
            if($order_id && $detail_res && $tokenRes && $changeRes){
                echo json_encode(array('code'=>1,'msg'=>"兑换成功"));die;
            }else{
                echo json_encode(array('code'=>2,'msg'=>"兑换失败"));die;
            }
        }else{
            echo json_encode(array('code'=>5,'msg'=>"兑换失败"));die;
        }
        break;

    case 'getProductList':  //获取商品列表信息
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $is_rank=$_GPC['is_rank'];
        $order_where=array();
        if($is_rank==1){
            $rank='rank asc';
        }else{
            $rank='sale_count desc';
        }
//        var_dump($order_where);die;
        $productData=pdo_getall("cqkundian_ordering_product",array('uniacid'=>$uniacid,'is_putaway'=>1,'is_change'=>0),'','',$rank,array(0,5));
        $request['productData']=$productData;
        echo json_encode($request);
        break;

    case 'getMoreList':  //加载更多数据
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $is_rank=$_GPC['is_rank'];
        if($is_rank==1){
            $rank='rank asc';
        }else{
            $rank='sale_count desc';
        }
        $page=$_GPC['page']+1;
        $productData=pdo_getall("cqkundian_ordering_product",array('uniacid'=>$uniacid,'is_putaway'=>1,'is_change'=>0),'','',$rank,array($page,5));
        $request['productData']=$productData;
        echo json_encode($request);
        break;

    case 'getExchangeData':   //获取可兑换的商品信息
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $productData=pdo_getall("cqkundian_ordering_product",array('uniacid'=>$uniacid,'is_change'=>1),'','','rank asc',array(0,5));
        $request['productData']=$productData;
        echo json_encode($request);
        break;
}