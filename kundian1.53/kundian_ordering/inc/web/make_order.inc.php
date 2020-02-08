<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26 0026
 * Time: 16:16
 */
defined("IN_IA") or exit("Access denied");
checklogin();
global $_W,$_GPC;
$ops=array('list','edit','is_pay_change','is_use_change','cancelMake','delete');
$uniacid=$_W['uniacid'];
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :"list";
switch ($op){
    case 'list':
        $condition=array();
//        if(!empty($_GPC['order_number'])){
//            $order_number=trim($_GPC['order_number']);
//            $condition['order_number LIKE']= '%'.$order_number.'%';
//        }
        $condition['uniacid']=$uniacid;
        $listCount=pdo_getall("cqkundian_ordering_make_order",$condition);
        $total=count($listCount);   //数据的总条数
        $pageSize=10; //每页显示的数据条数
        $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
        $pager=pagination($total,$pageIndex,$pageSize);
        $list=pdo_getall("cqkundian_ordering_make_order",$condition,'','','create_time desc',array($pageIndex,$pageSize));
        include $this->template("web/make_order/index");
        break;

    case 'edit':
        $id=$_GPC['id'];
        $makeOrderData=pdo_get("cqkundian_ordering_make_order",array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
        $makeOrderDetailData=pdo_getall("cqkundian_ordering_make_order_detail",array('mid'=>$id,'uniacid'=>$uniacid));
        include $this->template("web/make_order/edit");
        break;

    case 'is_pay_change':
        $id=$_GPC['id'];
        $condition=array(
            'id'=>$id,
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_make_order",array('is_pay'=>$_GPC['status']),$condition);
        if($request){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
        }else{
            echo json_encode(array('status'=>0,'msg'=>'操作失败'));
        }
        break;
    case 'is_use_change':
        $id=$_GPC['id'];
        $condition=array(
            'id'=>$id,
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_make_order",array('is_use'=>$_GPC['status']),$condition);
        if($request){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
        }else{
            echo json_encode(array('status'=>0,'msg'=>'操作失败'));
        }
        break;

    case 'cancelMake':
        $id=$_GPC['id'];
        $condition=array(
            'id'=>$id,
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_make_order",array('is_use'=>3),$condition);
        if($request){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
        }else{
            echo json_encode(array('status'=>0,'msg'=>'操作失败'));
        }
        break;
    case 'delete':
        $id=$_GPC['id'];
        $res=pdo_delete('cqkundian_ordering_make_order',array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
        if($res){
            echo json_encode(array('status'=>1));die;
        }else{
            echo json_encode(array('status'=>2));die;
        }
        break;
}