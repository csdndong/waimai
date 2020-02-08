<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/16 0016
 * Time: 10:04
 */
defined("IN_IA") or exit("Access Denied");
checklogin();
$ops=array('list','edit','saveModel','delete','statusChange');
global $_W,$_GPC;
//当前小程序唯一id
$uniacid=$_W['uniacid'];
$op=in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'list';
switch ($op){
    case "list":
        $condition=array();
        if(!empty($_GPC['title'])){
            $title=trim($_GPC['title']);
            $condition['title LIKE']= '%'.$title.'%';
        }
        $condition['uniacid']=$uniacid;
        $listCount=pdo_getall("cqkundian_ordering_slide",$condition);
        $total=count($listCount);   //数据的总条数
        $pageSize=10; //每页显示的数据条数
        $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
        $pager=pagination($total,$pageIndex,$pageSize);
        $list=pdo_getall("cqkundian_ordering_slide",$condition,'','','rank asc',array($pageIndex,$pageSize));
        for ($i=0;$i<count($list);$i++){
            $list[$i]['create_time']=date("Y-m-d H:i:s",$list[$i]['create_time']);
        }
        include $this->template("web/slide/index");
        break;
    case "edit":
        $id=trim($_GPC['id']);
        $list=pdo_get('cqkundian_ordering_slide',array('id'=>$id,'uniacid'=>$uniacid));
        include $this->template("web/slide/edit");
        break;

    case 'saveModel':
        $data=array(
            'title'=>trim($_GPC['title']),
            'src'=>$_GPC['src'],
            'status'=>$_GPC['status'],
            'rank'=>$_GPC['rank'],
            'create_time'=>time(),
            'uniacid'=>$uniacid,
        );
        if(empty($_GPC['id'])){  //新增
            $request=pdo_insert("cqkundian_ordering_slide",$data);
        }else{
            $condition=array(
                'id'=>$_GPC['id'],
                'uniacid'=>$uniacid,
            );
            $request=pdo_update("cqkundian_ordering_slide",$data,$condition);
        }
        if($request){
            message("操作成功",$this->createWebUrl("slide"));
        }else {
            message("操作失败", '', 'warning');
        }
        break;

    case 'statusChange':
        $id=$_GPC['id'];
        $condition=array(
            'id'=>$id,
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_slide",array('status'=>$_GPC['status']),$condition);
        if($request){
            echo json_encode(array('status'=>1,'msg'=>'操作成功'));
        }else{
            echo json_encode(array('status'=>0,'msg'=>'操作失败'));
        }
        break;

    case 'delete':
        $condition=array();
        $condition['id']=$_GPC['id'];
        $condition['uniacid']=$uniacid;
        $request=pdo_delete("cqkundian_ordering_slide",$condition);
        if($request){
            echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
        }else{
            echo json_encode(array('status'=>2,'msg'=>"操作失败"));
        }
        break;
}