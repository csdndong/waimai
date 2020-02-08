<?php
global $_GPC, $_W;
$str=$_W["referer"];
$newstr = substr($str,strrpos($str,'=')+1); 
setcookie("newstr",$newstr);
//echo $_COOKIE['newstr'];die;
$GLOBALS['frames'] = $this->getMainMenu();
$system=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
$time=time()-$system['sh_time']*24*60*60;
//echo $system['sh_time'];die;
if($system['sh_time']>0){
    $sql="select  * from " . tablename("cjdc_order") ." where uniacid={$_W['uniacid']} and UNIX_TIMESTAMP(jd_time)<={$time} and state=3";
    $res=pdo_fetchall($sql);
    //var_dump($res);die;
    for($i=0;$i<count($res);$i++){
        pdo_update('cjdc_order',array('state'=>4,'complete_time'=>date("Y-m-d H:i:s")),array('id'=>$res[$i]['id']));
        file_get_contents("".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&a=wxapp&do=addintegral&m=zh_cjdianc&type=1&order_id=".$res[$i]['id']);
        $this->updcommission($res[$i]['id']);
    }
}

$area=pdo_getall('cjdc_area',array('uniacid'=>$_W['uniacid']),array(),'','num asc');
$operation = empty($_GPC['op']) ? 'display' : trim($_GPC['op']);
$type=pdo_getall('cjdc_storetype',array('uniacid'=>$_W['uniacid']),array(),'','num asc');
$where="WHERE a.uniacid=:uniacid and state in (2,4)";
$data[':uniacid']=$_W['uniacid'];

    if($_GPC['keywords']){
    	$where .=" and a.name LIKE :name ";
    	$op=$_GPC['keywords'];
        $data[':name']="%$op%";
    }
    if($_GPC['type']){
    	$where .=" and b.id=:bid";
    	$data[':bid']=$_GPC['type'];
    }
    if($_GPC['area']){
    	$where .=" and c.id=:cid";
    	$data[':cid']=$_GPC['area'];
    }
 
if($_W['role']=='operator'){
    //查找商家ID;   
    $seller=pdo_get('cjdc_account',array('weid'=>$_W['uniacid'],'uid'=>$_W['user']['uid']));
    $seller_id=$seller['storeid'];
    $where.=" and a.id =:id";
    $data[':id']=$seller_id;
}
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select a.*,b.type_name,c.area_name from " . tablename("cjdc_store") . " a"  . " left join " . tablename("cjdc_storetype") . " b on b.id=a.md_type " . " left join " . tablename("cjdc_area") . " c on c.id=a.md_area ".$where." order by a.number asc";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_store") . " a"  . " left join " . tablename("cjdc_storetype") . " b on b.id=a.md_type  ".$where." order by a.number asc",$data);
$pager = pagination($total, $pageindex, $pagesize);
$list=pdo_fetchall($select_sql,$data);	
if($operation=='delete'){
	$res=pdo_delete('cjdc_store',array('id'=>$_GPC['id']));
	if($res){
		message('删除成功！', $this->createWebUrl('store'), 'success');
	}else{
		message('删除失败！','','error');
	}
}
if($_GPC['is_open']){
   $res=pdo_update('cjdc_store',array('is_open'=>$_GPC['is_open']),array('id'=>$_GPC['updid']));
    if($res){
        message('修改成功！', $this->createWebUrl('store'), 'success');
    }else{
        message('修改失败！','','error');
    } 
}
//一键复制
if($operation=='copy'){
$id = intval($_GPC['id']);
    $store = pdo_get("cjdc_store",array('uniacid'=>$_W['uniacid'],'id'=>$id));
   if(empty($store)){  
     message('门店不存在或已删除！','','error');
    } 
    // //复制门店数据表
    $sql = "INSERT INTO ".tablename('cjdc_store')."(name,address,time,time2,time3,time4,tel,announcement,is_rest,img,start_at,freight,logo,details,color,coordinates,yyzz,md_area,md_type,sales,score,capita,is_open,uniacid,number,environment,is_brand,state,rz_time,rzdq_time,is_mp3,is_video,store_mp3,store_video,ps_poundage) SELECT REPLACE(name,name,CONCAT(name,'-".random(5)."')),address,time,time2,time3,time4,tel,announcement,is_rest,img,start_at,freight,logo,details,color,coordinates,yyzz,md_area,md_type,sales,score,capita,is_open,uniacid,number,environment,is_brand,state,rz_time,rzdq_time,is_mp3,is_video,store_mp3,store_video,ps_poundage FROM ".tablename('cjdc_store')." WHERE id='".$id."';";
    $result = pdo_query($sql);
    $new_storeid = pdo_insertid();
    //复制商家设置
    $set_sql="INSERT INTO ".tablename('cjdc_storeset')."(xyh_money,xyh_open,integral,integral2,is_jd,store_mp3,store_video,is_mp3,is_video,is_jfpay,is_yuepay,is_yuejf,is_wxpay,poundage,is_pj,is_chzf,is_wxzf,box_name,yhq_name,sy_name,dn_name,wm_name,yy_name,yysm,wmsm,dnsm,sysm,yhq_img,sy_img,dn_img,wm_img,yy_img,is_yhq,is_sy,is_dn,is_wm,is_yy,store_id,ps_time,ps_mode,ps_jl,is_zt,is_hdfk,print_type,ztxy,top_style,info_style,print_mode,is_yydc,is_ps,is_dd,is_cj,is_czztpd,cj_name,wmps_name) SELECT xyh_money,xyh_open,integral,integral2,is_jd,store_mp3,store_video,is_mp3,is_video,is_jfpay,is_yuepay,is_yuejf,is_wxpay,poundage,is_pj,is_chzf,is_wxzf,box_name,yhq_name,sy_name,dn_name,wm_name,yy_name,yysm,wmsm,dnsm,sysm,yhq_img,sy_img,dn_img,wm_img,yy_img,is_yhq,is_sy,is_dn,is_wm,is_yy,REPLACE(store_id,store_id,'".$new_storeid."'),ps_time,ps_mode,ps_jl,is_zt,is_hdfk,print_type,ztxy,top_style,info_style,print_mode,is_yydc,is_ps,is_dd,is_cj,is_czztpd,cj_name,wmps_name FROM ".tablename('cjdc_storeset')." WHERE store_id='".$id."';";
     $set_result = pdo_query($set_sql);
    //复制菜品分类
    $all_class = pdo_getall("cjdc_type",array('uniacid'=>$_W['uniacid'],'store_id'=>$id));
    foreach ($all_class as $key => $classItem) {
        $class_sql = "INSERT INTO ".tablename('cjdc_type')."(type_name,store_id,uniacid,order_by,is_open) SELECT type_name,REPLACE(store_id,store_id,'".$new_storeid."'),uniacid,order_by,is_open FROM ".tablename('cjdc_type')." WHERE id='".$classItem['id']."';";
        $class_result = pdo_query($class_sql);
        $new_classid = pdo_insertid();
        //获取该分类下老的商品列表
        $goods_list = pdo_getall("cjdc_goods",array('uniacid'=>$_W['uniacid'],'store_id'=>$id,'type_id'=>$classItem['id']));
        foreach ($goods_list as $key2 => $goodsItem) {
            $goods_sql = "INSERT INTO ".tablename('cjdc_goods')."(name,type_id,logo,money,money2,vip_money,dn_money,is_show,inventory,content,details,sales,num,is_gg,is_hot,is_tj,is_new,is_zp,store_id,uniacid,type,quantity,box_money,restrict_num,start_num) SELECT name,REPLACE(type_id,type_id,'".$new_classid."'),logo,money,money2,vip_money,dn_money,is_show,inventory,content,details,sales,num,is_gg,is_hot,is_tj,is_new,is_zp,REPLACE(store_id,store_id,'".$new_storeid."'),uniacid,type,quantity,box_money,restrict_num,start_num FROM ".tablename('cjdc_goods')." WHERE id='".$goodsItem['id']."';";
            $goods_result = pdo_query($goods_sql);
            $new_goodsid = pdo_insertid();
            //获取老的商品规格
            $spec_list = pdo_getall("cjdc_spec",array('good_id'=>$goodsItem['id']));
            $spec_check = array();
            foreach ($spec_list as $key3 => $specItem) {
                $spec_sql = "INSERT INTO ".tablename('cjdc_spec')."(name,uniacid,num,good_id) SELECT name,uniacid,num,REPLACE(good_id,good_id,'".$new_goodsid."') FROM ".tablename('cjdc_spec')." WHERE id='".$specItem['id']."';";
                $spec_result = pdo_query($spec_sql);
                $new_specid = pdo_insertid(); 
                //获取老的规格属性
                 $spec_value = pdo_getall("cjdc_spec_val",array('spec_id'=>$specItem['id']));   
                 foreach ($spec_value as $key4 => $valItem) {
                    $val_sql = "INSERT INTO ".tablename('cjdc_spec_val')."(name,spec_id,num,uniacid,good_id) SELECT name,REPLACE(spec_id,spec_id,'".$new_specid."'),num,uniacid,REPLACE(good_id,good_id,'".$new_goodsid."') FROM ".tablename('cjdc_spec_val')." WHERE id='".$valItem['id']."';";
                $val_result = pdo_query($val_sql);
                $new_valid = pdo_insertid(); 
                          
               }          
            }
            //获取老的组合
              $combination_sql = "INSERT INTO ".tablename('cjdc_spec_combination')."(wm_money,dn_money,combination,number,good_id) SELECT wm_money,dn_money,combination,number,REPLACE(good_id,good_id,'".$new_goodsid."') FROM ".tablename('cjdc_spec_combination')." WHERE good_id='".$goodsItem['id']."';";
                $combination_result = pdo_query($combination_sql);
                $new_combinationid = pdo_insertid(); 
          
        }
    }
        //获取所有的标签
        $all_lable = pdo_getall("cjdc_dytag",array('uniacid'=>$_W['uniacid'],'store_id'=>$id));
        //var_dump($all_lable);die;
        foreach ($all_lable as $key => $lableItem) {
         
         $lable_sql = "INSERT INTO ".tablename('cjdc_dytag')."(tag_name,store_id,uniacid,sort,time) SELECT tag_name,REPLACE(store_id,store_id,'".$new_storeid."'),uniacid,sort,time FROM ".tablename('cjdc_dytag')." WHERE store_id='".$lableItem['store_id']."';";
        $lable_result = pdo_query($lable_sql);
        $new_lableid = pdo_insertid();
            break;
        }


          //获取所有配送费
        $all_lable = pdo_getall("cjdc_distribution",array('store_id'=>$id));
        foreach ($all_lable as $key => $psItem) {
         $ps_sql = "INSERT INTO ".tablename('cjdc_distribution')."(store_id,start,end,money,num) SELECT REPLACE(store_id,store_id,'".$new_storeid."'),start,end,money,num FROM ".tablename('cjdc_distribution')." WHERE store_id='".$psItem['store_id']."';";
        $ps_result = pdo_query($ps_sql);
        $new_psid = pdo_insertid();
        break;
        }
    message("门店复制成功",$this->createWebUrl('store'), 'success');
}
include $this->template('web/store');