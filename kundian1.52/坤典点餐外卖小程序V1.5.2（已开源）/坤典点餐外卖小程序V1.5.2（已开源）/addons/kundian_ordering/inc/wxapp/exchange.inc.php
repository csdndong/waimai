<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29 0029
 * Time: 9:59
 */
defined("IN_IA") or exit("Access Denied");
global $_W,$_GPC;
$ops=array('index','getRecord','cancelToken');
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :'index';
switch ($op){
    case 'index':
        echo json_encode(array('code'=>0));
        break;

    case 'getRecord':
        $uniacid=$_GPC['uniacid'];
        $uid=$_GPC['uid'];
        $recordData=pdo_getall("cqkundian_ordering_order",array('uid'=>$uid,'uniacid'=>$uniacid,'is_change'=>2));
        for ($i=0;$i<count($recordData);$i++){
            $detailData=pdo_get("cqkundian_ordering_order_detail",array('order_id'=>$recordData[$i]['id'],'uniacid'=>$uniacid));
            $productData=pdo_get("cqkundian_ordering_product",array('id'=>$detailData['pid'],'uniacid'=>$uniacid));
            $recordData[$i]['product_name']=$productData['product_name'];
            $recordData[$i]['cover']=$productData['cover'];
            $recordData[$i]['create_time']=date("Y年m月d日");
        }
        echo json_encode(array('recordData'=>$recordData));
        break;

    case 'cancelToken':
        $uniacid=$_GPC['uniacid'];
        $uid=$_GPC['uid'];
        $card_num=$_GPC['card_num'];
        $password=$_GPC['password'];
        $pwd=$_GPC['pwd'];

        //查询卡券是否存在
        $token_where=array(
            'card_num'=>$card_num,
            'password'=>$password,
            'uniacid'=>$uniacid,
        );
        $tokenData=pdo_get("cqkundian_ordering_token",$token_where);
        //查询卡券批次
        $batchData=pdo_get("cqkundian_ordering_batch",array('uniacid'=>$uniacid,'id'=>$tokenData['bid']));
        //查询卡券等级
        $levelData=pdo_get("cqkundian_ordering_giftlevel",array('uniacid'=>$uniacid,'id'=>$batchData['lid']));

        if($tokenData){  //存在卡券
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
        }else{
            echo json_encode(array('code'=>1,'卡号或者密码输入错误'));die;
        }

        $personData=pdo_get("cqkundian_ordering_cancel_person",array('uid'=>$uid,'uniacid'=>$uniacid,'pwd'=>$pwd,'type'=>1));
        if(empty($personData)){
            echo json_encode(array('code'=>5,'msg'=>'核销员不存在'));die;
        }

        //开始核销  1//修改卡号为已使用 2//增加核销记录
        $res=pdo_update("cqkundian_ordering_token",array('is_use'=>1),$token_where);
        $updateData=array(
            'tid'=>$tokenData['id'],
            'cid'=>$personData['id'],
            'card_num'=>$card_num,
            'phone'=>$personData['phone'],
            'create_time'=>time(),
            'uniacid'=>$uniacid,
        );
        $res1=pdo_insert("cqkundian_ordering_cancel_record",$updateData);
        if($res && $res1){
            echo json_encode(array('code'=>6,'msg'=>'核销成功'));die;
        }else{
            echo json_encode(array('code'=>7,'msg'=>'核销失败'));die;
        }
        break;
}