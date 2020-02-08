<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30 0030
 * Time: 15:07
 */
defined("IN_IA") or exit("Access Denied");
global $_W,$_GPC;
$op=$_GPC['op']? $_GPC['op'] :'index';
$uniacid=$_GPC['uniacid'];
$uid=$_GPC['uid'];
//获取订餐页面信息
if($op=='index'){
    $request=array();

    //店铺信息
    $aboutData=pdo_get('cqkundian_ordering_about',array('uniacid'=>$uniacid));
    $request['aboutData']=$aboutData;

    //分类信息
    $typeData=pdo_getall('cqkundian_ordering_product_type',array('uniacid'=>$uniacid,'is_use'=>1),'','','rank asc');
    for ($i=0;$i<count($typeData);$i++){
        if($i==0){
            $typeData[$i]['type_cart_count'] = 0;
            $condition = array(
                'is_putaway' => 1,
                'uniacid' => $uniacid,
                'tid'=>$typeData[$i]['id'],
            );

            $goodsData=pdo_getall('cqkundian_ordering_product',$condition,'','','rank asc',array(1,10));
            for ($j = 0; $j < count($goodsData); $j++) {
                //获取购物车中的的数量
                $cart_where = array(
                    'uid' => $uid,
                    'pid' => $goodsData[$j]['id'],
                    'uniacid' => $uniacid,
                );
                $cartData = pdo_get('cqkundian_ordering_cart', $cart_where);
                if ($cartData) {
                    $goodsData[$j]['cart_count'] = $cartData['count'];   //购物车中的数量
                    $typeData[$i]['type_cart_count']+=$cartData['count'];
                } else {
                    $goodsData[$j]['cart_count'] = 0;
                }
            }
            $request['goodsData'] = $goodsData;
            $typeData[$i]['active'] = 1;   //默认选中第一个
        }else{
            $typeData[$i]['active'] = 0;   //没有选中的
            //查询购物车中的数量
            $cart_where = array(
                'uid' => $uid,
                'type_id' => $typeData[$i]['id'],
                'uniacid' => $uniacid,
            );
            $cartData = pdo_getall('cqkundian_ordering_cart', $cart_where);
            if ($cartData) {
                for ($m = 0; $m < count($cartData); $m++) {
                    $typeData[$i]['type_cart_count'] += $cartData[$m]['count'];
                }
            } else {
                $typeData[$i]['type_cart_count'] = 0;
            }
        }

    }
    $request['typeData']=$typeData;

    //查询购物车信息
    $cart_where=array(
        'uid'=>$uid,
        'uniacid'=>$uniacid,
    );
    $cartData=pdo_getall("cqkundian_ordering_cart",$cart_where);
    $request['cartData']=$cartData;

    //获取购物车中的总数及数量
    $totalData=getTotalData($uid,$uniacid);
    $request['totalPrice']=$totalData['totalPrice'];
    $request['totalCount']=$totalData['totalCount'];

    echo json_encode($request);die;
}

//根据分类查看商品信息
if($op=='changeTypeGoods'){
    $request=array();
    $uniacid=$_GPC['uniacid'];
    $type_id=$_GPC['type_id'];
    $uid=$_GPC['uid'];
    $goods_where=array(
        'uniacid'=>$uniacid,
        'is_putaway'=>1,
        'tid'=>$type_id,
    );
    $userData=pdo_get('cqkundian_ordeing_user',array('uid'=>$uid,'uniacid'=>$uniacid));
    $goodsData=getTypeGoodsData($goods_where,0,10,$uid);
    $request['goodsData']=$goodsData;
    echo json_encode($request);
}

//懒加载
if($op=='getMoreTypeGoods'){
    $type_id=$_GPC['type_id'];
    $page=$_GPC['page']+1;
    $goods_where=array(
        'uniacid'=>$uniacid,
        'is_putaway'=>1,
    );

    $goods_where['tid']=$type_id;
    $goodsData=getTypeGoodsData($goods_where,$page,10,$uid);
    $request['goodsData']=$goodsData;
    echo json_encode($request);
}

//添加商品到购物车
if($op=='addCart'){
    $uid=$_GPC['uid'];
    $goods_id=$_GPC['goods_id'];
    $type_id=$_GPC['type_id'];
    $cart_where=array(
        'uniacid'=>$uniacid,
        'uid'=>$uid,
        'pid'=>$goods_id,
    );
    $cartData=pdo_get('cqkundian_ordering_cart',$cart_where);
    if($cartData){   //购物车中存在此商品信息  增加商品数量
        $res=pdo_update('cqkundian_ordering_cart',array('count'=>$cartData['count']+1),$cart_where);
    }else{
        //查询商品信息
        $goodsData=pdo_get('cqkundian_ordering_product',array('uniacid'=>$uniacid,'id'=>$goods_id));
        $updateData=array(
            'uniacid'=>$uniacid,
            'uid'=>$uid,
            'pid'=>$goods_id,
            'count'=>1,
            'goods_name'=>$goodsData['product_name'],
            'price'=>$goodsData['price'],
            'create_time'=>time(),
            'type_id'=>$type_id,
        );
        $res=pdo_insert('cqkundian_ordering_cart',$updateData);
    }
    if($res){
        $totalData=getTotalData($uid,$shop_id,$uniacid);
        $request['code']=1;
        $request['totalCount']=$totalData['totalCount'];
        $request['totalPrice']=$totalData['totalPrice'];
        echo json_encode($request);
    }else{
        echo json_encode(array('code'=>2));
    }
}

//减少购物车数量
if($op=='reduceCart'){
    $uniacid=$_GPC['uniacid'];
    $uid=$_GPC['uid'];
    $goods_id=$_GPC['goods_id'];
    $type_id=$_GPC['type_id'];

    $cart_where=array(
        'uniacid'=>$uniacid,
        'uid'=>$uid,
        'pid'=>$goods_id,
    );
    $cartData=pdo_get('cqkundian_ordering_cart',$cart_where);
    if($cartData['count']>1){  //减少数量
        $res=pdo_update('cqkundian_ordering_cart',array('count'=>$cartData['count']-1),$cart_where);
    }else{      //从购物车中删除该商品
        $res=pdo_delete('cqkundian_ordering_cart',$cart_where);
    }
    if($res){
        $totalData=getTotalData($uid,$uniacid);
        $request['code']=1;
        $request['totalCount']=$totalData['totalCount'];
        $request['totalPrice']=$totalData['totalPrice'];
        echo json_encode($request);
    }else{
        echo json_encode(array('code'=>2));
    }
}

//查看商品详情
if($op=='getGoodsDetail'){
    $goods_id=$_GPC['goods_id'];
    $goodsDetailData=pdo_get('cqkundian_ordering_product',array('id'=>$goods_id,'uniacid'=>$uniacid));
    echo json_encode(array('goodsDetailData'=>$goodsDetailData));die;
}

//获取购物车中的商品信息
if($op=='getCartData'){
    $uid=$_GPC['uid'];
    $cartData=pdo_getall('cqkundian_ordering_cart',array('uniacid'=>$uniacid,'uid'=>$uid));
    echo json_encode(array('cartData'=>$cartData));die;
}

//清空购物车
if($op=='clearCart'){
    $res=pdo_delete('cqkundian_ordering_cart',array('uid'=>$uid,'uniacid'=>$uniacid));
    echo $res ?json_encode(array('code'=>1)) : json_encode(array('code'=>2));die;
}


/**
 * 统计数量/总价
 * @param $uid 用户uid
 * @param $shop_id 店铺id
 * @param $uniacid  小程序id
 * @return array 返回值
 */
function getTotalData($uid,$uniacid){
    $totalCount=0;
    $totalPirce=0;
    $cart_where=array(
        'uid'=>$uid,
        'uniacid'=>$uniacid,
    );
    $cartData=pdo_getall('cqkundian_ordering_cart',$cart_where);
    for ($i=0;$i<count($cartData);$i++){
        $totalCount+=$cartData[$i]['count'];
        $totalPirce+=$cartData[$i]['price']*$cartData[$i]['count'];
    }
    return array('totalCount'=>$totalCount,'totalPrice'=>$totalPirce);
}
/**
 * 查询商品信息
 * @param $condition  查询条件
 * @param $page 当前页数
 * @param $pageSize 查询条数
 * @param $uid  用户uid
 * @return array  返回值
 */
function getTypeGoodsData($condition,$page,$pageSize,$uid){
    if($page && $pageSize){
        $goodsData=pdo_getall("cqkundian_ordering_product",$condition,'','','rank asc',array($page,$pageSize));
    }else{
        $goodsData=pdo_getall("cqkundian_ordering_product",$condition,'','','rank asc',array(0,10));
    }
    for ($i=0;$i<count($goodsData);$i++){
        $cartData=getCartData($goodsData[$i]['id'],$condition['uniacid'],$uid);
        if($cartData){
            $goodsData[$i]['cart_count']=$cartData['count'];
        }else{
            $goodsData[$i]['cart_count']=0;
        }
    }
    return $goodsData;
}
/**
 * 查询单条购物车的信息
 * @param $goods_id 商品id
 * @param $uniacid  小程序id
 * @param $uid      用户uid
 * @param $shop_id  店铺id
 * @return bool 返回值
 */
function getCartData($goods_id,$uniacid,$uid){
    $cart_where=array(
        'pid'=>$goods_id,
        'uniacid'=>$uniacid,
        'uid'=>$uid,
    );
    $cartData=pdo_get("cqkundian_ordering_cart",$cart_where);
    return $cartData;
}