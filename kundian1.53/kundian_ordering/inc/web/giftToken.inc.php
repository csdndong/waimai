<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/16 0016
 * Time: 14:09
 */
defined("IN_IA") or exit("Access denied");
checklogin();
global $_W,$_GPC;
$op=$_GPC['op'] ? $_GPC['op'] :'list';
$uniacid=$_W['uniacid'];

//礼品券列表
if($op=='list'){
    $condition=array();
    if(!empty($_GPC['title'])){
        $title=trim($_GPC['title']);
        $condition['title LIKE']= '%'.$title.'%';
    }
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_batch",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_batch",$condition,'','rank asc',array($pageIndex,$pageSize));
    for ($i=0;$i<count($list);$i++){
        $list[$i]['create_time']=date("Y-m-d H:i:s",$list[$i]['create_time']);
        $list[$i]['expire_time']=date("Y-m-d H:i:s",$list[$i]['expire_time']);
        $levelData=pdo_get("cqkundian_ordering_giftlevel",array('id'=>$list[$i]['lid']));
        $list[$i]['price_batch']=$levelData['price'];
    }
    include $this->template("web/giftToken/index");
}

//礼品券新增/编辑
if($op=='edit'){
    $level_where=array(
        'status'=>1,
        'uniacid'=>$uniacid,
    );
    $giftLevel=pdo_getall("cqkundian_ordering_giftlevel",$level_where);
    $edit_where=array(
        'id'=>$_GPC['id'],
        'uniacid'=>$uniacid,
    );
    $list=pdo_get("cqkundian_ordering_batch",$edit_where);
    include $this->template("web/giftToken/edit");
}

if($op=='statusChange'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_batch",array('status'=>$_GPC['status']),$condition);
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
    $request=pdo_delete("cqkundian_ordering_batch",$condition);
    $request1=pdo_delete("cqkundian_ordering_token",array(''));
    if($request){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}

if($op=='saveModel'){
    $updateData=array(
        'title'=>trim($_GPC['title']),
        'rank'=>$_GPC['rank'],
        'price'=>$_GPC['price'],
        'count'=>$_GPC['count'],
        'lid'=>$_GPC['lid'],
        'status'=>$_GPC['status'],
        'prefix'=>$_GPC['prefix'],
        'create_time'=>time(),
        'uniacid'=>$uniacid,
        'expire_time'=>strtotime($_GPC['expire_time']),
    );
    if(empty($_GPC['id'])){
        $updateData['is_create']=1;  //未生成卡券
        $request=pdo_insert("cqkundian_ordering_batch",$updateData);
    }else{
        $condition=array();
        $condition['id']=$_GPC['id'];
        $condition['uniacid']=$uniacid;
        $request=pdo_update("cqkundian_ordering_batch",$updateData,$condition);
    }
    if($request){
        message("操作成功",$this->createWebUrl('giftToken'));
    }else{
        message("操作失败");
    }
}

if($op=='create_token'){
    $bid=$_GPC['id'];  //批次id
    $create_where=array(
        'id'=>$bid,
        'uniacid'=>$uniacid,
    );
    //批次信息
    $batchData=pdo_get("cqkundian_ordering_batch",$create_where);
    if($batchData['is_create']==2){
        echo json_encode(array('status'=>3,'msg'=>'卡券已经生成过了！'));
        die;
    }
    $length=$batchData['count'];
    //生成随机数
    $count=0;
    $temp=array();
    while($count<$length){
        $temp[]=mt_rand(100000,999999);
        $data=array_unique($temp);
        $count=count($data);
    }
    //批量生成卡券
    $res=0;
    for ($i=0;$i<count($temp);$i++){
        $tokenData=array(
            'card_num'=>$batchData['prefix'].$temp[$i],
            'password'=>rand(100000,999999),
            'is_use'=>0, //未使用
            'create_time'=>time(),
            'bid'=>$bid,
            'uniacid'=>$uniacid,
        );
        $res+=pdo_insert("cqkundian_ordering_token",$tokenData);
    }
    //改变该批次的卡券生成状态
    $res1=pdo_update("cqkundian_ordering_batch",array('is_create'=>2),array('id'=>$bid,'uniacid'=>$uniacid));
    if($res>0 && $res1){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>1,'msg'=>'操作失败'));
    }
}

if($op=='token_list'){
    $condition=array();
    if(!empty($_GPC['card_num'])){
        $card_num=trim($_GPC['card_num']);
        $condition['card_num LIKE']= '%'.$card_num.'%';
    }
    $bid=$_GPC['id'];
    $condition['bid']=$bid;
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_token",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=20; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);

    //统计卡券使用情况
    $useData=pdo_getall("cqkundian_ordering_token",array('bid'=>$bid,'uniacid'=>$uniacid,'is_use'=>1));
    $totalUse=count($useData);
    $list=pdo_getall("cqkundian_ordering_token",$condition,'','','',array($pageIndex,$pageSize));
    include $this->template("web/giftToken/token_list");
}

if($op=='statusTokenChange'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_token",array('is_use'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}

if($op=='statusTokenChangeSale'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_token",array('is_sale'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}

if($op=='deleteToken'){
    $condition=array();
    $condition['id']=$_GPC['id'];
    $condition['uniacid']=$uniacid;
    $request=pdo_delete("cqkundian_ordering_token",$condition);
    if($request){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}

if($op=='outToken'){
    //导入PHPExcel类库
    $data[][0]=array('ID','卡券ID','卡券密码','卡券面值','是否使用','卡券生成时间');
    $bid=$_GPC['id'];
    //查询要导出的卡券等级
    $out_where=array(
        'id'=>$bid,
        'uniacid'=>$uniacid,
    );
    $batch=pdo_get("cqkundian_ordering_batch",$out_where);
    //查询该批次卡券的面值
    $mianzhi=pdo_get("cqkundian_ordering_giftlevel",array('id'=>$batch['lid'],'uniacid'=>$uniacid));
    //查询出该等级下的所有卡券
    $condition=array();
    $condition['bid']=$bid;
    $condition['uniacid']=$uniacid;
    $listData=array();
    $list=pdo_getall("cqkundian_ordering_token",$condition,array('id','card_num','password','bid','is_use','create_time'));
    //循环遍历整理卡券信息
    for ($i=0;$i<count($list);$i++){
        $listData[$i]['id']=$list[$i]['id'];
        $listData[$i]['card_num']=$list[$i]['card_num'];
        $listData[$i]['password']=$list[$i]['password'];
        $listData[$i]['price']=$mianzhi['price'];
        if($list[$i]['is_use']==1){
            $listData[$i]['is_use']="未使用";
        }else{
            $listData[$i]['is_use']="已使用";
        }
        $listData[$i]['create_time']=date("Y-m-d H:i:s",$list[$i]['create_time']);
    }
    $data[]=$listData;
    require_once "Org/PHPExcel.class.php";
    require_once "Org/PHPExcel/Writer/Excel5.php";
    require_once "Org/PHPExcel/IOFactory.php";
    require_once "Org/function.php";
    $filename="卡券";
    getExcel($filename,$data);
}

//礼品券等级列表
if($op=='gift_token_level'){
    $condition=array();
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_giftlevel",$condition);
    $total=count($listCount);
    $pageSize=8;
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_giftlevel",$condition,'','','rank asc',array($pageIndex,$pageSize));
    include $this->template('web/giftLevel/index');
}

//礼品券等级修改、新增
if($op=='giftLevelEdit'){
    $condition=array();
    $condition=array(
        'id'=>$_GPC['id'],
        'uniacid'=>$uniacid,
    );
    $list=pdo_get("cqkundian_ordering_giftlevel",$condition);
    include $this->template("web/giftLevel/edit");
}

//礼品券等级保存
if($op=='giftLevelSaveModel'){
    $updateData=array(
        'price'=>$_GPC['price'],
        'status'=>$_GPC['status'],
        'rank'=>$_GPC['rank'],
        'create_time'=>time(),
        'remark'=>$_GPC['remark'],
        'uniacid'=>$uniacid,
    );
    if(empty($_GPC['id'])){  //新增
        $request=pdo_insert("cqkundian_ordering_giftlevel",$updateData);
    }else{
        $condition=array();
        $condition=array(
            'id'=>$_GPC['id'],
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_giftlevel",$updateData,$condition);
    }
    if($request){
        message("操作成功",$this->createWebUrl('giftToken'));
    }else{
        message("操作失败");
    }
}

//礼品券等级状态修改
if($op=='giftLevel_statusChange'){
    $condition=array();
    $condition=array(
        'id'=>$_GPC['id'],
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_giftlevel",array('status'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>2,'msg'=>'操作失败'));
    }
}

//礼品券等级删除
if($op=='giftLevel_delete'){
    $condition=array();
    $condition=array(
        'id'=>$_GPC['id'],
        'uniacid'=>$uniacid,
    );
    $request=pdo_delete("cqkundian_ordering_giftlevel",$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>2,'msg'=>'操作失败'));
    }
}

//客户信息列表
if($op=='customer_list'){
    $condition=array();
    if(!empty($_GPC['customer_name'])){
        $customer_name=trim($_GPC['customer_name']);
        $condition['customer_name LIKE']= '%'.$customer_name.'%';
    }
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_customer",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_customer",$condition,'','','rank asc',array($pageIndex,$pageSize));
    include  $this->template("web/customer/index");
}

//编辑客户信息
if($op=='customer_edit'){
    $edit_where=array(
        'id'=>$_GPC['id'],
        'uniacid'=>$uniacid,
    );
    $list=pdo_get("cqkundian_ordering_customer",$edit_where);
    include $this->template("web/customer/edit");
}

//客户信息保存
if($op=='customer_save'){
    $updateData=array(
        'customer_name'=>$_GPC['customer_name'],
        'phone'=>$_GPC['phone'],
        'address'=>$_GPC['address'],
        'rank'=>$_GPC['rank'],
        'create_time'=>time(),
        'uniacid'=>$uniacid,
        'remark'=>$_GPC['remark'],
    );
    if(empty($_GPC['id'])){
        $request=pdo_insert("cqkundian_ordering_customer",$updateData);
    }else{
        $model_where['id']=$_GPC['id'];
        $model_where['uniacid']=$uniacid;
        $request=pdo_update("cqkundian_ordering_customer",$updateData,$model_where);
    }
    if($request){
        message("操作成功",$this->createWebUrl('customer'));
    }else{
        message("操作失败");
    }
}

//删除客户信息
if($op=='customer_delete'){
    $condition=array();
    $condition['id']=$_GPC['id'];
    $condition['uniacid']=$uniacid;
    $request=pdo_delete("cqkundian_ordering_customer",$condition);
    if($request){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
    die;
}

//卡券出售列表
if($op=='tokenSale_list'){
    $condition=array();
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_sale",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_sale",$condition,'','','',array($pageIndex,$pageSize));
    for ($i=0;$i<count($list);$i++){
        $list_where=array(
            'id'=>$list[$i]['cid'],
            'uniacid'=>$uniacid,
        );
        $typeData=pdo_get("cqkundian_ordering_customer",$list_where);
        $list[$i]['customer_name']=$typeData['customer_name'];
    }
    include $this->template("web/sale/index");
}

//卡券编辑
if($op=='tokenSale_edit'){
    //客户信息
    $customer_where=array(
        'uniacid'=>$uniacid,
    );
    $customerData=pdo_getall("cqkundian_ordering_customer",$customer_where);
    //卡券批次
    $batch_where=array(
        'status'=>1,
        'uniacid'=>$uniacid,
    );
    $batchData=pdo_getall("cqkundian_ordering_batch",$batch_where);
    include $this->template("web/sale/edit");
}

//保存卡券信息
if($op=='tokenSale_save'){
    $bid=$_GPC['bid'];
    $get_where=array(
        'bid'=>$bid,
        'uniacid'=>$uniacid,
        'is_sale'=>0,
        'is_use'=>0,
    );
    $tokenData=pdo_getall("cqkundian_ordering_token",$get_where);
    $batchOneData=pdo_get("cqkundian_ordering_batch",array('id'=>$bid,'uniacid'=>$uniacid));
    echo json_encode(array('tokenData'=>$tokenData,'batchOneData'=>$batchOneData));die;
}

//出售卡券===》查看卡券
if($op=='tokenSale_selectToken'){
    $sid=$_GPC['id'];
    $sale_where=array(
        'id'=>$sid,
        'uniacid'=>$uniacid,
    );
    $saleData=pdo_get("cqkundian_ordering_sale",$sale_where);
    $tid=unserialize($saleData['tid']);
    $tokenData=array();
    for ($i=0;$i<count($tid);$i++){
        $token_where=array(
            'id'=>$tid[$i],
            'uniacid'=>$uniacid,
            'cid'=>$saleData['cid'],
        );
        $tokenData[$i]=pdo_get("cqkundian_ordering_token",$token_where);
    }
    include $this->template("web/sale/token_list");
}

if($op=='tokenSale_tokenListCus'){
    $cid=$_GPC['id'];
    $tokenData=array();
    $token_where=array(
        'uniacid'=>$uniacid,
        'cid'=>$cid,
    );
    $tokenCount=pdo_getall("cqkundian_ordering_token",$token_where);
    $total=count($tokenCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $tokenData=pdo_getall("cqkundian_ordering_token",$token_where,'','','',array($pageIndex,$pageSize));
    include $this->template("web/sale/token_list");
}

//礼品券兑换商品列表
if($op=='token_product_list'){
    $condition=array();
    if(!empty($_GPC['product_name'])){
        $product_name=trim($_GPC['product_name']);
        $condition['product_name LIKE']= '%'.$product_name.'%';
    }
    $condition['uniacid']=$uniacid;
    $condition['is_change']=1;  //礼品券兑换商品
    $listCount=pdo_getall("cqkundian_ordering_product",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_product",$condition,'','','rank asc',array($pageIndex,$pageSize));
    include $this->template("web/giftToken/token_product_list");
}

//礼品券兑换商品编辑、新增
if($op=='product_list_edit'){
    if(!empty($_GPC['id'])){
        $list=pdo_get('cqkundian_ordering_product',array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    include $this->template("web/giftToken/token_product_list_edit");
}
//礼品券兑换商品保存
if($op=='product_list_save'){
    $updateData=array(
        'product_name'=>$_GPC['product_name'],
        'price'=>$_GPC['price'],
        'old_price'=>$_GPC['old_price'],
        'sale_count'=>$_GPC['sale_count'],
        'count'=>$_GPC['count'],
        'cover'=>tomedia($_GPC['cover']),
        'is_putaway'=>$_GPC['is_putaway'],
        'rank'=>$_GPC['rank'],
        'is_change'=>1,
        'uniacid'=>$uniacid,
    );
    if(empty($_GPC['id'])){
        $updateData['create_time']=time();
        $res=pdo_insert('cqkundian_ordering_product',$updateData);
    }else{
        $res=pdo_update('cqkundian_ordering_product',$updateData,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    $res ? message('操作成功',$this->createWebUrl('giftToken')) :message('操作失败');die;
}

//更新商品的上架状态
if($op=='product_list_is_putaway'){
    if(!empty($_GPC['id'])){
        $res=pdo_update('cqkundian_ordering_product',array('is_putaway'=>$_GPC['status']),array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
        echo $res ?json_encode(array('status'=>1,'msg'=>"操作成功")) :json_encode(array('status'=>2,'msg'=>"操作失败"));die;
    }
}

//删除商品信息
if($op=='product_list_delete'){
    if(!empty($_GPC['id'])){
        $res=pdo_delete('cqkundian_ordering_product',array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
        echo $res ?json_encode(array('status'=>1,'msg'=>"操作成功")) :json_encode(array('status'=>2,'msg'=>"操作失败"));die;
    }
}


if($op=='token_set'){
    $condition=array(
        'ikey'=>array('is_open_token'),
        'uniacid'=>$uniacid,
    );
    $list=pdo_get('cqkundian_ordering_set',$condition);
    include $this->template("web/giftToken/token_set");
}

if($op=='is_open_token_save'){
    if($_GPC['is_open_token']){
        $value=$_GPC['is_open_token'];
    }else{
        $value=0;
    }
    $data=array(
        'ikey'=>'is_open_token',
        'value'=>$value,
        'uniacid'=>$uniacid,
    );
    $deliveryData=pdo_get('cqkundian_ordering_set',array('uniacid'=>$uniacid,'ikey'=>'is_open_token'));
    if(empty($deliveryData)){
        $res=pdo_insert('cqkundian_ordering_set',$data);
    }else{
        $res=pdo_update('cqkundian_ordering_set',$data,array('uniacid'=>$uniacid,'ikey'=>'is_open_token'));
    }
    if($res){
        message('操作成功',url('site/entry/giftToken',array('m'=>'kundian_ordering','op'=>'token_set')));die;
    }else{
        message('操作失败或没有修改任何信息');die;
    }
}

