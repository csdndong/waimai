<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23 0023
 * Time: 下午 3:04
 */
defined("IN_IA") or exit('Access Denied');
global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$op=$_GPC['op'] ? $_GPC['op'] :'goods_list';
if($op=='goods_list'){

    $condition=array();
    if(!empty($_GPC['goods_name'])){
        $goods_name=trim($_GPC['goods_name']);
        $condition['goods_name LIKE']= '%'.$goods_name.'%';
    }
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_goods",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_goods",$condition,'','','rank asc',array($pageIndex,$pageSize));
    for ($i=0;$i<count($list);$i++){
        $list_where=array(
            'id'=>$list[$i]['type_id'],
            'uniacid'=>$uniacid,
        );
        $typeData=pdo_get("cqkundian_ordering_goods_type",$list_where);
        $list[$i]['type_name']=$typeData['type_name'];
    }
    include $this->template('web/goods/index');
}

if($op=='goods_edit'){
    $typeData=pdo_getall('cqkundian_ordering_goods_type',array('uniacid'=>$uniacid,'status'=>1));
    if(!empty($_GPC['id'])){
        $list=pdo_get('cqkundian_ordering_goods',array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
        $list['slide_src']=unserialize($list['slide_src']);
    }

    include $this->template('web/goods/edit');
}

if($op=='save_goods'){
    $data=array(
        'goods_name'=>trim($_GPC['goods_name']),
        'goods_number'=>trim($_GPC['goods_number']),
        'type_id'=>$_GPC['type_id'],
        'cover'=>tomedia($_GPC['cover']),
        'price'=>floatval($_GPC['price']),
        'old_price'=>floatval($_GPC['old_price']),
        'count'=>intval($_GPC['count']),
        'sale_count'=>intval($_GPC['sale_count']),
        'detail_desc'=>$_GPC['detail_desc'],
        'is_put_away'=>$_GPC['is_put_away'],
        'uniacid'=>$uniacid,
        'create_time'=>time(),
        'rank'=>$_GPC['rank'],
    );

    $slide_src=$_GPC['slide_src'];
    for($i=0;$i<count($slide_src);$i++){
        $slide_src[$i]=tomedia($slide_src[$i]);
    }
    $data['slide_src']=serialize($slide_src);
    if(empty($_GPC['id'])){
        $res=pdo_insert('cqkundian_ordering_goods',$data);
    }else{
        $res=pdo_update('cqkundian_ordering_goods',$data,array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
    }
    if($res){
        $url=url('site/entry/goods',array('m'=>'kundian_ordering','op'=>'goods_list'));
        message('操作成功',$url);
    }else{
        message('操作失败');
    }

}

//修改商品上架状态
if($op=='goods_is_putaway'){
    $res=pdo_update('cqkundian_ordering_goods',array('is_put_away'=>$_GPC['status']),array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
    if($res){
        echo json_encode(array('status'=>1));die;
    }else{
        echo json_encode(array('status'=>2));die;
    }
}

//删除商品信息
if($op=='delete_goods'){
    $res=pdo_delete('cqkundian_ordering_goods',array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
    if($res){
        echo json_encode(array('status'=>1));die;
    }else{
        echo json_encode(array('status'=>2));die;
    }
}

//点餐分类列表
if($op=='goods_type_list'){
    $all=pdo_getall('cqkundian_ordering_goods_type',array('uniacid'=>$uniacid));
    $total=count($all);
    $pageIndex=$_GPC['page'] ? intval($_GPC['page']) :1;
    $pager=pagination($total,$pageIndex,10);
    $list=pdo_getall('cqkundian_ordering_goods_type',array('uniacid'=>$uniacid),'','','rank asc',array($pageIndex,10));
    include $this->template('web/goods/goods_type');
}

if($op=='goods_type_edit'){
    if(!empty($_GPC['id'])){
        $list=pdo_get('cqkundian_ordering_goods_type',array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    include $this->template('web/goods/goods_type_edit');
}

//保存点餐分类信息
if($op=='goods_type_save'){
    $data=array(
        'type_name'=>trim($_GPC['type_name']),
        'status'=>$_GPC['status'],
        'rank'=>$_GPC['rank'],
        'uniacid'=>$uniacid,
    );
    if(empty($_GPC['id'])){
        $res=pdo_insert('cqkundian_ordering_goods_type',$data);
    }else{
        $res=pdo_update('cqkundian_ordering_goods_type',$data,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    if($res){
        $url=url('site/entry/goods',array('m'=>'kundian_ordering','op'=>'goods_type_list'));
        message('操作成功',$url);
    }else{
        message('操作失败');
    }
}

//商品分类状态修改
if($op=='goods_type_change_status'){
    $res=pdo_update('cqkundian_ordering_goods_type',array('status'=>$_GPC['status']),array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
    if($res){
        echo json_encode(array('status'=>1));die;
    }else{
        echo json_encode(array('status'=>2));die;
    }
}

//删除点餐分类信息
if($op=='goods_type_delete'){
    $res=pdo_delete('cqkundian_ordering_goods_type',array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
    if($res){
        echo json_encode(array('status'=>1));die;
    }else{
        echo json_encode(array('status'=>2));die;
    }
}