<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17 0017
 * Time: 14:55
 */
defined("IN_IA") or exit("Access denied");
checklogin();
global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$op=$_GPC['op'] ? $_GPC['op'] :'product_list';

//产品列表
if($op=='product_list'){
    $condition=array();
    if(!empty($_GPC['product_name'])){
        $product_name=trim($_GPC['product_name']);
        $condition['product_name LIKE']= '%'.$product_name.'%';
    }
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_product",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_product",$condition,'','','rank asc',array($pageIndex,$pageSize));
    for ($i=0;$i<count($list);$i++){
        $list_where=array(
            'id'=>$list[$i]['tid'],
            'uniacid'=>$uniacid,
        );
        $typeData=pdo_get("cqkundian_ordering_product_type",$list_where);
        $list[$i]['type_name']=$typeData['type_name'];
    }
    include $this->template("web/product/index");
}

//编辑产品信息
if($op=='edit'){
    //产品分类信息
    $type_where=array(
        'is_use'=>1,
        'uniacid'=>$uniacid,
    );
    $typeData=pdo_getall("cqkundian_ordering_product_type",$type_where);
    if($_GPC['id']){
        $edit_where=array(
            'id'=>$_GPC['id'],
            'uniacid'=>$uniacid,
        );
        $list=pdo_get("cqkundian_ordering_product",$edit_where);
    }
    include $this->template("web/product/edit");
}

//保存产品信息
if($op=='saveModel'){
    $updateData=array(
        'product_name'=>$_GPC['product_name'],
        'old_price'=>$_GPC['old_price'],
        'price'=>$_GPC['price'],
        'sale_count'=>$_GPC['sale_count'],
        'count'=>$_GPC['count'],
        'create_time'=>time(),
        'is_putaway'=>$_GPC['is_putaway'],
        'cover'=>tomedia($_GPC['cover']),
        'tid'=>$_GPC['tid'],
        'is_change'=>$_GPC['is_change'],
        'detail_desc'=>$_GPC['detail_desc'],
        'uniacid'=>$uniacid,
        'rank'=>$_GPC['rank'],
        'is_recommend'=>$_GPC['is_recommend'],
    );
    if(empty($_GPC['id'])){
        $res=pdo_insert("cqkundian_ordering_product",$updateData);
    }else{
        $res=pdo_update('cqkundian_ordering_product',$updateData,array('uniacid'=>$uniacid,'id'=>$_GPC['id']));
    }
    if($res){
        message("操作成功",url('site/entry/product',array('m'=>'kundian_ordering','op'=>'product_list')));die;
    }else{
        message("操作失败");die;
    }
}

//修改商品信息时初始化获取规格项
if($op=='getEditSpecItem'){
    $request=array();
    $goods_id=$_GPC['goods_id'];    //商品id
    //根据商品获取规格值
    $editSpecVal=pdo_getall('cqkundian_ordering_product_spec',array('uniacid'=>$uniacid,'goods_id'=>$goods_id));
    //根据规格项id获取规格值
    $spec_id=array();
    for($i=0;$i<count($editSpecVal);$i++){
        $specItem=pdo_getall('cqkundian_ordering_product_spec_value',array('uniacid'=>$uniacid,'spec_id'=>$editSpecVal[$i]['id']));
        $editSpecVal[$i]['specValue']=$specItem;
        $spec_id[]=$editSpecVal[$i]['id'];
    }
    $returnStr=getSpecSku($spec_id,$uniacid);

    $newSkuSpec=$returnStr['newSkuSpec'];
    $goodsSpecVal=array();
    for($j=1;$j<=count($newSkuSpec);$j++){
        $goodsSpecVal[$j-1]=pdo_get('cqkundian_ordering_product_spec_sku',array('sku_name'=>$newSkuSpec[$j],'goods_id'=>$goods_id,'uniacid'=>$uniacid));
    }
    $request['specVal']=$editSpecVal;
    $request['specItem']= $returnStr['specItem'];
    $request['newSkuSpec']=$returnStr['newSkuSpec'];
    $request['skuSpec']=$returnStr['skuSpec'];
    $request['goodsSpecVal']=$goodsSpecVal;
    echo json_encode($request);die;
}

//删除产品信息
if($op=='delete'){
    $condition=array();
    $condition['id']=$_GPC['id'];
    $condition['uniacid']=$uniacid;
    $request=pdo_delete("cqkundian_ordering_product",$condition);
    if($request){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}

//更新产品的上架下架状态
if($op=='is_putawayChange'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_product",array('is_putaway'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}

//更新产品状态
if($op=='is_changeChange'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_product",array('is_change'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}
//更新产品推荐信息
if($op=='is_recommendChange'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_product",array('is_recommend'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}
//产品分类列表
if($op=='product_type_list'){
    $condition=array();
    if(!empty($_GPC['type_name'])){
        $type_name=trim($_GPC['type_name']);
        $condition['type_name LIKE']= '%'.$type_name.'%';
    }
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_product_type",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_product_type",$condition,'','','rank asc',array($pageIndex,$pageSize));
    for ($i=0;$i<count($list);$i++){
        $goods=pdo_getall('cqkundian_ordering_product',array('uniacid'=>$uniacid,'tid'=>$list[$i]['id']));
        $list[$i]['count']=count($goods);
    }
    include $this->template("web/product_type/index");
}

//修改编辑产品分类信息
if($op=='product_type_edit'){
    $edit_where=array(
        'id'=>$_GPC['id'],
        'uniacid'=>$uniacid,
    );
    $list=pdo_get("cqkundian_ordering_product_type",$edit_where);
    include $this->template("web/product_type/edit");
}

//保存产品信息
if($op=='product_type_save'){
    $updataData=array(
        'type_name'=>$_GPC['type_name'],
        'icon'=>tomedia($_GPC['icon']),
        'is_use'=>$_GPC['is_use'],
        'is_recommend'=>$_GPC['is_recommend'],
        'create_time'=>time(),
        'rank'=>$_GPC['rank'],
        'uniacid'=>$uniacid,
    );
    if(empty($_GPC['id'])){
        $request=pdo_insert("cqkundian_ordering_product_type",$updataData);
    }else{
        $model_where=array(
            'id'=>$_GPC['id'],
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_product_type",$updataData,$model_where);
    }
    if($request){
        message("操作成功",url('site/entry/product',array('m'=>'kundian_ordering','op'=>'product_type_list')));
    }else{
        message("操作失败");
    }
}

//删除产品分类信息
if($op=='product_type_delete'){
    $condition=array();
    $condition['id']=$_GPC['id'];
    $condition['uniacid']=$uniacid;
    $request=pdo_delete("cqkundian_ordering_product_type",$condition);
    $request1=pdo_delete('cqkundian_ordering_product',array('tid'=>$_GPC['id'],'uniacid'=>$uniacid));
    if($request && $request1){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}

if($op=='product_type_changeIsUse'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_product_type",array('is_use'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}

/**2018-06-06 update*/

//添加规格组
if($op=='addSkuItem'){
    $name=$_GPC['sku_name'];
    $res=pdo_insert('cqkundian_ordering_product_spec',array('name'=>$name,'uniacid'=>$uniacid));
    $spec_id=pdo_insertid();
    echo $res ? json_encode(array('code'=>1,'spec_id'=>$spec_id)) : json_encode(array('code'=>2));die;
}

//删除规格项
if($op=='deleteSkuItem'){
    $id=$_GPC['id'];
    $spec_id_str=explode('_', $_GPC['spec_id_str']);
    $returnStr=getSpecSku($spec_id_str,$uniacid);
    $returnStr['code']=1;
    $res=pdo_delete('cqkundian_ordering_product_spec',array('uniacid'=>$uniacid,'id'=>$id));
    echo $res ? json_encode($returnStr) : json_encode(array('code'=>2));die;
}

//添加规格值
if($op=='addSkuVal'){
    $is_type=$_GPC['is_type'];
    $spec_id=$_GPC['specid'];
    $value=$_GPC['spec_value'];
    $spec_id_str=explode('_', $_GPC['spec_id_str']);
    $insertData=array(
        'uniacid'=>$uniacid,
        'spec_id'=>$spec_id,
        'spec_value'=>$value,
    );
    $res=pdo_insert('cqkundian_ordering_product_spec_value',$insertData);  //插入数据
    $spec_val_id=pdo_insertid();
    $returnStr=getSpecSku($spec_id_str,$uniacid);
    if(!empty($_GPC['is_type'])){  //编辑
        $goods_id=$_GPC['goods_id'];
        $newSkuSpec=$returnStr['newSkuSpec'];
        $goodsSpecVal=array();
        for($j=1;$j<=count($newSkuSpec);$j++){
            $goodsSpecVal[$j-1]=pdo_get('cqkundian_ordering_product_spec_sku',array('sku_name'=>$newSkuSpec[$j],'goods_id'=>$goods_id,'uniacid'=>$uniacid));
        }
        $returnStr['goodsSpecVal']=$goodsSpecVal;
    }

    $returnStr['spec_val_id']=$spec_val_id;
    $returnStr['code']=1;
    echo json_encode($returnStr);die;
}

//删除规格值
if($op=='deleteSkuVal'){
    $spec_id=$_GPC['spec_id'];
    $spec_val_id=$_GPC['spec_val_id'];
    $spec_id_str=explode('_', $_GPC['spec_id_str']);  //当前规格项
    $res=pdo_delete("cqkundian_ordering_product_spec_value",array('spec_id'=>$spec_id,'id'=>$spec_val_id,'uniacid'=>$uniacid));
    $returnStr=getSpecSku($spec_id_str,$uniacid);
    if(!empty($_GPC['is_type'])){  //编辑
        $goods_id=$_GPC['goods_id'];
        $specValue=pdo_getall('cqkundian_ordering_product_spec_sku',array('goods_id'=>$goods_id,'uniacid'=>$uniacid));//获取该商品的所有规格值
        $newSkuSpec=$returnStr['newSkuSpec'];
        $goodsSpecVal=array();
        for($j=1;$j<=count($newSkuSpec);$j++){
            $goodsSpecVal[$j-1]=pdo_get('cqkundian_ordering_product_spec_sku',array('sku_name'=>$newSkuSpec[$j],'goods_id'=>$goods_id,'uniacid'=>$uniacid));
        }
        $delete_where['sku_name LIKE']="%".$spec_val_id.'%';
        $delete_where['goods_id']=$goods_id;
        $delete_where['uniacid']=$uniacid;
        pdo_delete('cqkundian_ordering_product_spec_sku',$delete_where);
        $returnStr['goodsSpecVal']=$goodsSpecVal;
    }
    echo  $res ?json_encode($returnStr) : json_encode(array('code'=>2));die;
}

/**
 * 获取规格的组合值，当前选中的规格项，规格值对应的详细信息
 * @param  [array] $spec_id [规格项id]
 * @param  [int] $uniacid [当前小程序id]
 * @return [array]          [array]
 */
function getSpecSku($spec_id,$uniacid){
    sort($spec_id);  //将数组排序
    $returnStr=array();
    $specItem=pdo_getall("cqkundian_ordering_product_spec",array('id in'=>$spec_id,'uniacid'=>$uniacid));
    $arr=array();
    for($i=0;$i<count($specItem);$i++) {
        $spec_where['spec_id'] = $specItem[$i]['id'];  //所有的规格项
        $spec_where['uniacid']=$uniacid;
        $specValue = pdo_getall('cqkundian_ordering_product_spec_value', $spec_where, 'id');
        for ($j = 0; $j < count($specValue); $j++) {
            $arr[$specItem[$i]['id']][] = $specValue[$j]['id'];
        }
    }
    //得到组合后的SKU数组
    $newSpec=ok($arr);
    $skuSpec=array();
    for($i=1;$i<count($newSpec)+1;$i++){
        $new_spec_arr=explode(",",$newSpec[$i]);
        $skuSpec[$i-1]=pdo_getall('cqkundian_ordering_product_spec_value',array('id in'=>$new_spec_arr,'uniacid'=>$uniacid));
    }

    $returnStr['specItem']=$specItem;
    $returnStr['newSkuSpec']=$newSpec;
    $returnStr['skuSpec']=$skuSpec;
    return $returnStr;
}

/**
 *  要解决的数学问题    ：算出C(a,1) * C(b, 1) * ... * C(n, 1)的组合情况，其中C(n, 1)代表从n个元素里任意取一个元素
 *  要解决的实际问题样例：某年级有m个班级，每个班的人数不同，现在要从每个班里抽选一个人组成一个小组，
 *  由该小组来代表该年级参加学校的某次活动，请给出所有可能的组合
 *
 *  需要进行排列组合的数组
 *  数组说明：该数组是一个二维数组，第一维索引代表班级编号，第二维索引代表学生编号
 *  $CombinList = array(1 => array("Student10", "Student11"),
 *                      2 => array("Student20", "Student21", "Student22"),
 *                      3 => array("Student30"),
 *                      4 => array("Student40", "Student41", "Student42", "Student43"));
 *
 *  计算C(a,1) * C(b, 1) * ... * C(n, 1)的值
 *
 *
 * @param  [array] $CombinList [description]
 * @return [type]             [description]
 */
function ok($CombinList) {
    $CombineCount = 1;
    foreach ($CombinList as $Key => $Value) {
        $CombineCount *= count($Value);
    }
    $RepeatTime = $CombineCount;
    foreach ($CombinList as $ClassNo => $StudentList) {
        // $StudentList中的元素在拆分成组合后纵向出现的最大重复次数
        $RepeatTime = $RepeatTime / count($StudentList);
        $StartPosition = 1;
        // 开始对每个班级的学生进行循环
        foreach ($StudentList as $Student) {
            $TempStartPosition = $StartPosition;
            $SpaceCount = $CombineCount / count($StudentList) / $RepeatTime;
            for ($J = 1; $J <= $SpaceCount; $J ++) {
                for ($I = 0; $I < $RepeatTime; $I ++) {
                    $Result[$TempStartPosition + $I][$ClassNo] = $Student;
                }
                $TempStartPosition += $RepeatTime * count($StudentList);
            }
            $StartPosition += $RepeatTime;
        }
    }
    if($Result){
        foreach ($Result as $k => $v) {
            $Result[$k] = implode(',', $v);
        }
    }

    return $Result;
}

