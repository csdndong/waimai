<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17 0017
 * Time: 16:33
 */
defined("IN_IA") or exit("Access denied");
!defined('ROOT_KUNDIAN_ORDERING') && define('ROOT_KUNDIAN_ORDERING', IA_ROOT . '/addons/kundian_ordering/');
checklogin();
global $_W,$_GPC;
$ops=array('edit','saveModel');
$uniacid=$_W['uniacid'];
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :'edit';
switch ($op){
    case 'edit':
        $listData=pdo_getall("cqkundian_ordering_set",array('uniacid'=>$uniacid));
        $list=array();
        foreach ($listData as $key=>$v){
            $list[$v['ikey']]=$v['value'];
        }
//        var_dump($list);
        include $this->template('web/config/edit');
        break;
    case 'saveModel':
        $updateData=array(
            'phone'=>$_GPC['phone'],
            'address'=>$_GPC['address'],
        );
        $insertData=array();
        $request=0;
        foreach ($updateData as $key=> $v){
            $model_where=array(
                'ikey'=>$key,
                'uniacid'=>$uniacid,
            );
            $insertData=array(
                'ikey'=>$key,
                'value'=>$v,
                'uniacid'=>$uniacid,
            );
            $isHave=pdo_get("cqkundian_ordering_set",$model_where);
            if($isHave){
                $request+=pdo_update("cqkundian_ordering_set",$insertData,$model_where);
            }else{
                $request+=pdo_insert("cqkundian_ordering_set",$insertData);
            }

        }
        if($request){
            message("操作成功",$this->createWebUrl("config"));
        }else{
            message("操作失败");
        }
        break;
}
