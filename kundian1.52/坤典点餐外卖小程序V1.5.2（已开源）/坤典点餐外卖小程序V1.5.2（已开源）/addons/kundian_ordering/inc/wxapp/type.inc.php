<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/23 0023
 * Time: 17:03
 */
defined("IN_IA") or exit("Access Denied");
global $_W,$_GPC;
$ops=array('index','getType','changeType');
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :'index';
switch ($op){
    case 'getType':
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $type_where=array(
            'uniacid'=>$uniacid,
        );
        $typeData=pdo_getall("cqkundian_ordering_product_type",$type_where,'','','rank asc');
        $productData=pdo_getall("cqkundian_ordering_product",array('tid'=>$typeData[0]['id'],'uniacid'=>$uniacid),'','','rank asc',array(0,10));
        $request['typeData']=$typeData;
        $request['productData']=$productData;
        echo json_encode($request);
        break;

    case 'changeType':
        $request=array();
        $uniacid=$_GPC['uniacid'];
        $tid=$_GPC['tid'];
        $productData=pdo_getall("cqkundian_ordering_product",array('tid'=>$tid,'uniacid'=>$uniacid),'','','rank asc',array(0,10));
        $request['productData']=$productData;
        echo json_encode($request);
        break;
}