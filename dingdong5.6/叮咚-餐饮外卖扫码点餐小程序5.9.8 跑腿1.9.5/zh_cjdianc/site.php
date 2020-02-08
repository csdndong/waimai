<?php
defined('IN_IA') or exit('Access Denied');
//require 'inc/func/core.php';
require IA_ROOT.'/addons/zh_cjdianc/inc/func/core.php';
class zh_cjdiancModuleSite extends Core {

    
    public function doMobileNewOrder(){
        global $_W,$_GPC;
        $time=time();
        $time2=$time-10;

        $store_id=$_GPC['store'];
        $res=pdo_get('cjdc_order',array('state'=>2,'store_id'=>$store_id,'type'=>1));//外卖
        $sql=" select 1 from ".tablename('cjdc_order')." where type=2 and store_id={$store_id} and UNIX_TIMESTAMP(time)>={$time2}";
        $res2=pdo_fetch($sql);
       // $res2=pdo_get('cjdc_order',array('time2 >='=>,'type'=>2,'store_id'=>$store_id));//店内
        if($res){
            echo 1;
        }elseif($res2){
            echo 2;
        }elseif($res3){
             echo 3;
        }else{
            echo '暂无新订单!';
        }
      
}

    public function doMobileJdOrder(){
        global $_W,$_GPC;
          $store_id=$_GPC['id'];
             $store=pdo_get('cjdc_store',array('id'=>$store_id));
             if($store['is_jd']==1){
                $time=time()-$store['jd_time'];
                $data['state']=3;
                $res=pdo_update("cjdc_order",$data,array('store_id'=>$store_id,'time2 <='=>$time,'state'=>2));
                if($res){
                    echo  '1';
                }else{
                    echo  '2';
                }
             }
    }
    public function doMobileUpdate(){
        global $_W,$_GPC;
        if($_GPC['name']){
           $data['name']=$_GPC['name']; 
        }
        if($_GPC['money']){
           $data['money']=$_GPC['money']; 
        }
        if($_GPC['dn_money']){
           $data['dn_money']=$_GPC['dn_money']; 
        }
        if($_GPC['box_money']){
           $data['box_money']=$_GPC['box_money']; 
        }
        if($_GPC['num']){
           $data['inventory']=$_GPC['num']; 
        }
        if($_GPC['sales']){
           $data['sales']=$_GPC['sales']; 
        }
        $res=pdo_update('cjdc_goods',$data,array('id'=>$_GPC['id']));
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }
    public function doMobileUpdUser(){
        global $_W,$_GPC;
        //var_dump($_GPC['id']);die;
        $res=pdo_delete('cjdc_user',array('id'=>$_GPC['id']));
         if($res){
            echo '1';
        }else{
            echo '2';
        }
    }
    public function doMobileUpdCai(){
        global $_W,$_GPC;
        $res=pdo_delete('cjdc_goods',array('id'=>$_GPC['id']));
        $res=pdo_delete('cjdc_spec',array('goods_id'=>$_GPC['id']));
         if($res){
            echo '1';
        }else{
            echo '2';
        }
    }
    //删除充值活动
    public function doMobileDelCz(){
		global $_W,$_GPC;
		$res=pdo_delete('cjdc_czhd',array('id'=>$_GPC['id']));
		if($res){
			echo '1';
		}else{
			echo '2';
		}
    }
    //添加充值活动
    public function doMobileAddCz(){
    	global $_W,$_GPC;
    	for($i=0;$i<count($_GPC['list']);$i++){
    		$data['full']=$_GPC['list'][$i]['full'];
    		$data['reduction']=$_GPC['list'][$i]['reduction'];
    		$data['uniacid']=$_W['uniacid'];
    		pdo_insert('cjdc_czhd',$data);
    	}
    }
  
//下架
    public function doMobileXj(){
    	global $_W,$_GPC;
    	$res=pdo_update('cjdc_goods',array('is_show'=>2),array('id'=>$_GPC['id']));
    	if($res){
    		echo  '1';
    	}else{
    		echo  '2';
    	}
    }
     public function doMobileSj(){
    	global $_W,$_GPC;
    	$res=pdo_update('cjdc_goods',array('is_show'=>1),array('id'=>$_GPC['id']));
    	if($res){
    		echo  '1';
    	}else{
    		echo  '2';
    	}
    }
    public function doMobileTime(){
          global $_W,$_GPC;
        $m=$_GPC['yue'];
        $y=$_GPC['nian'];
        //  $m=2;
        // $y=2017;
        @$day=date("t",strtotime("$y-$m"));
        $storeid=$_GPC['store_id'];
         if($m>9){
                $m=$m;
            }elseif($m<=9){
                $m="0".$m;
            }
        $data=array();
        for($i=1;$i<=$day;$i++){
           
            if($i>9){
                $i=$i;
            }elseif($i<=9){
                $i="0".$i;
            }
            $time=$y."-".$m."-".$i;
            $time="'%$time%'";
            $wm = "select sum(money) as total from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and state not in (5,1,8) and type=1 and pay_time !=''";
            $wm = pdo_fetch($wm);//外卖销售额
            $dn = "select sum(money) as total from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and dn_state not in (3,1) and type=2 and pay_time !=''";
            $dn = pdo_fetch($dn);//店内销售额
            $yd = "select sum(pay_money) as total from " . tablename("cjdc_ydorder")." WHERE created_time LIKE ".$time." and store_id=".$storeid." and state not in (0,6)";
            $yd = pdo_fetch($yd);//预定销售额
            $dmf = "select sum(money) as total from " . tablename("cjdc_dmorder")." WHERE time LIKE ".$time." and state=2 and store_id=".$storeid;
            $dmf = pdo_fetch($dmf);//当面付销售额
            $total = $wm['total']+$dn['total']+$yd['total']+$dmf['total'];//销售额


            $wm2 = "select * from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and state not in (5,1,8) and type=1 and pay_time !=''";
            $wm2 = count(pdo_fetchall($wm2));//外卖销售量

            $dn2 = "select * from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and dn_state not in (3,1) and type=2 and pay_time !=''";
            $dn2 = count(pdo_fetchall($dn2));//店内销售量
            $yd2 = "select * from " . tablename("cjdc_ydorder")." WHERE created_time LIKE ".$time." and store_id=".$storeid." and state not in (0,6)";
            $yd2 = count(pdo_fetchall($yd2));//预定销售量
            $dmf2 = "select * from " . tablename("cjdc_dmorder")." WHERE time LIKE ".$time." and state=2 and store_id=".$storeid;
            $dmf2 = count(pdo_fetchall($dmf2));//当面付销售量
            $number=$wm2+$dn2+$yd2+$dmf2;//销售量
           
            $data[]=array(
                    'money'=>$total,
                    'number'=>$number
                );
        }
       // print_r($data);die;
       echo json_encode($data);

    }
     public function doMobileTime2(){
          global $_W,$_GPC;
      
        $y=$_GPC['nian'];
        $storeid=$_GPC['store_id'];
        $data=array();
        for($i=1;$i<=12;$i++){
            if($i>9){
                $i=$i;
            }elseif($i<=9){
                $i="0".$i;
            }
            $time=$y."-".$i;
            $time="'%$time%'";
            $wm = "select sum(money) as total from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and state not in (5,1,8) and type=1 and pay_time !=''";
            $wm = pdo_fetch($wm);//外卖销售额
            $dn = "select sum(money) as total from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and dn_state not in (3,1) and type=2 and pay_time !=''";
            $dn = pdo_fetch($dn);//店内销售额
            $yd = "select sum(pay_money) as total from " . tablename("cjdc_ydorder")." WHERE created_time LIKE ".$time." and store_id=".$storeid." and state not in (0,6)";
            $yd = pdo_fetch($yd);//预定销售额
            $dmf = "select sum(money) as total from " . tablename("cjdc_dmorder")." WHERE time LIKE ".$time." and state=2 and store_id=".$storeid;
            $dmf = pdo_fetch($dmf);//当面付销售额
            $total = $wm['total']+$dn['total']+$yd['total']+$dmf['total'];//销售额


            $wm2 = "select * from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and state not in (5,1,8) and type=1 and pay_time !=''";
            $wm2 = count(pdo_fetchall($wm2));//外卖销售量
            $dn2 = "select * from " . tablename("cjdc_order")." WHERE time LIKE ".$time." and store_id=".$storeid." and dn_state not in (3,1) and type=2 and pay_time !=''";
            $dn2 = count(pdo_fetchall($dn2));//店内销售量
            $yd2 = "select * from " . tablename("cjdc_ydorder")." WHERE created_time LIKE ".$time." and store_id=".$storeid." and state not in (0,6)";
            $yd2 = count(pdo_fetchall($yd2));//预定销售量
            $dmf2 = "select * from " . tablename("cjdc_dmorder")." WHERE time LIKE ".$time." and state=2 and store_id=".$storeid;
            $dmf2 = count(pdo_fetchall($dmf2));//当面付销售量
            $number=$wm2+$dn2+$yd2+$dmf2;//销售量
           
            $data[]=array(
                    'money'=>$total,
                    'number'=>$number
                );
        }
       echo json_encode($data);

    }


public function doMobileSelectUser(){
   global $_W, $_GPC;
  // echo  $_GPC['keywords'];
    //查出已是商家用户
$sjuser=pdo_getall('cjdc_store',array('uniacid'=>$_W['uniacid']),'user_id');
//二维数组转一维
function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){ 
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
        $indexKeyIsNull            = (is_null($indexKey))?true :false; 
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
        $result                         = array(); 
        foreach((array)$input as $key=>$row){ 
            if($columnKeyIsNumber){ 
                $tmp= array_slice($row, $columnKey, 1); 
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
            }else{ 
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
            } 
            if(!$indexKeyIsNull){ 
                if($indexKeyIsNumber){ 
                  $key = array_slice($row, $indexKey, 1); 
                  $key = (is_array($key) && !empty($key))?current($key):null; 
                  $key = is_null($key)?0:$key; 
                }else{ 
                  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                } 
            } 
            $result[$key] = $tmp; 
        } 
        return $result; 
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}
$yuser=i_array_column($sjuser, 'user_id');
$string='';
if($yuser){
foreach($yuser as $v){
    $string.="'".$v."',";
}
$string=rtrim($string, ",");
}
if($yuser){
$sql =" select id,name from ".tablename('cjdc_user')." where uniacid={$_W['uniacid']}  and id not in ({$string}) and  (name like '%{$_GPC['keywords']}%' || openid like '%{$_GPC['keywords']}%') and name !=''";  
}else{
 $sql =" select id,name from ".tablename('cjdc_user')." where uniacid={$_W['uniacid']}   and  (name like '%{$_GPC['keywords']}%' || openid like '%{$_GPC['keywords']}%') and name !=''";     
}
$user=pdo_fetchall($sql);
echo json_encode($user);
}



public function doMobileSelectUser2(){
   global $_W, $_GPC;
  // echo  $_GPC['keywords'];
    //查出已是商家用户
$sjuser=pdo_getall('cjdc_store',array('uniacid'=>$_W['uniacid']),'admin_id');
//二维数组转一维
function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){ 
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
        $indexKeyIsNull            = (is_null($indexKey))?true :false; 
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
        $result                         = array(); 
        foreach((array)$input as $key=>$row){ 
            if($columnKeyIsNumber){ 
                $tmp= array_slice($row, $columnKey, 1); 
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
            }else{ 
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
            } 
            if(!$indexKeyIsNull){ 
                if($indexKeyIsNumber){ 
                  $key = array_slice($row, $indexKey, 1); 
                  $key = (is_array($key) && !empty($key))?current($key):null; 
                  $key = is_null($key)?0:$key; 
                }else{ 
                  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                } 
            } 
            $result[$key] = $tmp; 
        } 
        return $result; 
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}
$yuser=i_array_column($sjuser, 'admin_id');
$string='';
if($yuser){
foreach($yuser as $v){
    $string.="'".$v."',";
}
$string=rtrim($string, ",");
}
if($yuser){
$sql =" select id,name from ".tablename('cjdc_user')." where uniacid={$_W['uniacid']}  and id not in ({$string}) and  (name like '%{$_GPC['keywords']}%' || openid like '%{$_GPC['keywords']}%') and name !=''";  
}else{
 $sql =" select id,name from ".tablename('cjdc_user')." where uniacid={$_W['uniacid']}   and  (name like '%{$_GPC['keywords']}%' || openid like '%{$_GPC['keywords']}%') and name !=''";     
}
$user=pdo_fetchall($sql);
echo json_encode($user);
}


//积分商品批量上架
public function doMobileJfGoodsSj(){
     global $_W, $_GPC;
        $res=pdo_update('cjdc_jfgoods',array('is_open'=>1),array('id'=>$_GPC['id']));
        if($res){
            message('操作成功',$this->createWebUrl('jfgoods',array()),'success');
        }else{
            message('操作失败','','error');
        }
}
//积分商品批量下架
public function doMobileJfGoodsXj(){
     global $_W, $_GPC;
        $res=pdo_update('cjdc_jfgoods',array('is_open'=>2),array('id'=>$_GPC['id']));
        if($res){
            message('操作成功',$this->createWebUrl('jfgoods',array()),'success');
        }else{
            message('操作失败','','error');
        }
}
//积分商品批量删除
public function doMobileDelJfGoods(){
     global $_W, $_GPC;
        $res=pdo_delete('cjdc_jfgoods',array('id'=>$_GPC['id']));
        if($res){
            message('删除成功',$this->createWebUrl('jfgoods',array()),'success');
        }else{
            message('删除失败','','error');
        }
}



//抢购商品批量上架
public function doMobileqgGoodsSj(){
     global $_W, $_GPC;
        $res=pdo_update('cjdc_qggoods',array('state'=>1),array('id'=>$_GPC['id']));
        if($res){
            message('操作成功',$this->createWebUrl('qggoods',array()),'success');
        }else{
            message('操作失败','','error');
        }
}
//抢购商品批量下架
public function doMobileqgGoodsXj(){
     global $_W, $_GPC;
        $res=pdo_update('cjdc_qggoods',array('state'=>2),array('id'=>$_GPC['id']));
        if($res){
            message('操作成功',$this->createWebUrl('qggoods',array()),'success');
        }else{
            message('操作失败','','error');
        }
}
//抢购商品批量显示
public function doMobileqgGoodsXs(){
     global $_W, $_GPC;
        $res=pdo_update('cjdc_qggoods',array('state2'=>1),array('id'=>$_GPC['id']));
        if($res){
            message('操作成功',$this->createWebUrl('qggoods',array()),'success');
        }else{
            message('操作失败','','error');
        }
}
//抢购商品批量隐藏
public function doMobileqgGoodsYc(){
     global $_W, $_GPC;
        $res=pdo_update('cjdc_qggoods',array('state2'=>2),array('id'=>$_GPC['id']));
        if($res){
            message('操作成功',$this->createWebUrl('qggoods',array()),'success');
        }else{
            message('操作失败','','error');
        }
}
//抢购商品批量删除
public function doMobileDelqgGoods(){
     global $_W, $_GPC;
        $res=pdo_delete('cjdc_qggoods',array('id'=>$_GPC['id']));
        if($res){
            message('删除成功',$this->createWebUrl('qggoods',array()),'success');
        }else{
            message('删除失败','','error');
        }
}

 //添加商品
    public function doMobileAddGood(){
        global $_W,$_GPC;
        // echo $_GPC['checkval'];die;
        // print_r($_GPC['menu']);die;
        $data['name']=$_GPC['goodname'];
        $data['type_id']=$_GPC['fenlei'];
        $data['logo']=$_W['attachurl'].$_GPC['logo'];
        $data['money']=$_GPC['flshou'];
        $data['money2']=$_GPC['money2'];
        $data['is_show']=$_GPC['statu'];
        $data['inventory']=$_GPC['flstock'];
        $data['restrict_num']=$_GPC['restrict_num'];
        $data['start_num']=$_GPC['start_num'];

        $data['is_hot']=$_GPC['is_hot'];
        $data['is_zp']=$_GPC['is_zp'];
        $data['label_id']=$_GPC['con'];
        $data['type']=$_GPC['shop_state'];
        $data['vip_money']=$_GPC['vip_money'];
        $data['box_money']=$_GPC['box_money'];
        $data['dn_money']=$_GPC['dn_money'];
        $data['content']=$_GPC['content'];

        $data['is_tj']=$_GPC['is_recommend'];
        $data['is_new']=$_GPC['is_new'];

        $data['details']=html_entity_decode($_GPC['viewval']);
        $data['store_id']=$_GPC['storeid'];
        $data['sales']=$_GPC['flxiao'];
        $data['num']=$_GPC['numpai'];
        $data['is_gg']=$_GPC['checkval'];
        $data['uniacid']=$_W['uniacid'];
        $data['dn_hymoney']=$_GPC['dn_hymoney'];
        $res=pdo_insert('cjdc_goods',$data);
        $good_id=pdo_insertid();
        for($i=0;$i<count($_GPC['list']);$i++){
            $data2['name']=$_GPC['list'][$i]['color'];
            $data2['good_id']=$good_id;
            $data2['num']=$i;
            $data2['uniacid']=$_W['uniacid'];
            pdo_insert('cjdc_spec',$data2);
            $specid=pdo_insertid();
            for($k=0;$k<count($_GPC['list'][$i]['ggarr']);$k++){
                $data3['name']=$_GPC['list'][$i]['ggarr'][$k]['guigebig'];
                $data3['spec_id']=$specid;
                $data3['num']=$k;
                $data3['uniacid']=$_W['uniacid'];
                $data3['good_id']=$good_id;
                pdo_insert('cjdc_spec_val',$data3);
            }
        }

        for($j=0;$j<count($_GPC['menu']);$j++){
            $data4['combination']='';
            for($l=0;$l<(count($_GPC['menu'][$j]['biao'])-3);$l++){
                    $data4['combination'] .=$_GPC['menu'][$j]['biao'][$l]['inpval'].",";
            }
             $data4['combination']=substr($data4['combination'],0,strlen($data4['combination'])-1); 
        //   $data4['combination']=$_GPC['menu'][$j]['biao'][0]['inpval'].','.$_GPC['menu'][$j]['biao'][1]['inpval'];
            $count=count($_GPC['menu'][$j]['biao'])-3;
            $count2=count($_GPC['menu'][$j]['biao'])-2;
            $count3=count($_GPC['menu'][$j]['biao'])-1;
            $data4['number']=$_GPC['menu'][$j]['biao'][$count]['inpval'];
            $data4['wm_money']=$_GPC['menu'][$j]['biao'][$count2]['inpval'];
            $data4['dn_money']=$_GPC['menu'][$j]['biao'][$count3]['inpval'];
            
            $data4['good_id']=$good_id;

            pdo_insert("cjdc_spec_combination",$data4);
        }



    }





     //修改商品
    public function doMobileUpdGood(){
       global $_W,$_GPC;
      // print_r($_GPC['menu']);die;
      pdo_delete('cjdc_shopcar',array('good_id'=>$_GPC['good_id']));
        if($_GPC['scid']){
              pdo_delete('cjdc_spec_combination',array('good_id'=>$_GPC['good_id']));
            pdo_delete('cjdc_shopcar',array('good_id'=>$_GPC['good_id']));
          for($t=0;$t<count($_GPC['scid']);$t++){
            pdo_delete('cjdc_spec',array('id'=>$_GPC['scid'][$t]['id']));
            pdo_delete('cjdc_spec_val',array('spec_id'=>$_GPC['scid'][$t]['id']));
          
        }  
        }
        if($_GPC['smallid']){
            pdo_delete('cjdc_spec_combination',array('good_id'=>$_GPC['good_id']));
            pdo_delete('cjdc_shopcar',array('good_id'=>$_GPC['good_id']));
            for($y=0;$y<count($_GPC['smallid']);$y++){
            pdo_delete('cjdc_spec_val',array('id'=>$_GPC['smallid'][$y]['id']));
            
        }  
        }
        $data['name']=$_GPC['goodname'];
        $data['type_id']=$_GPC['fenlei'];
        $goodlist=pdo_get('cjdc_goods',array('id'=>$_GPC['good_id']));
        if($goodlist['logo']!=$_GPC['logo']){
            $data['logo']=$_W['attachurl'].$_GPC['logo'];
        }
        
        $data['money']=$_GPC['flshou'];
        $data['money2']=$_GPC['money2'];
        $data['restrict_num']=$_GPC['restrict_num'];
        $data['start_num']=$_GPC['start_num'];
        $data['is_hot']=$_GPC['is_hot'];
        $data['is_zp']=$_GPC['is_zp'];
        $data['label_id']=$_GPC['con'];
        $data['type']=$_GPC['shop_state'];
        $data['vip_money']=$_GPC['vip_money'];
        $data['box_money']=$_GPC['box_money'];
        $data['dn_money']=$_GPC['dn_money'];
        $data['content']=$_GPC['content'];

        $data['is_tj']=$_GPC['is_recommend'];
        $data['is_new']=$_GPC['is_new'];
        
        $data['is_show']=$_GPC['statu'];
        $data['inventory']=$_GPC['flstock'];
        $data['details']=html_entity_decode($_GPC['viewval']);
        $data['store_id']=$_GPC['storeid'];
        $data['sales']=$_GPC['flxiao'];
        $data['num']=$_GPC['numpai'];
        $data['is_gg']=$_GPC['checkval'];
        $data['uniacid']=$_W['uniacid'];
        $data['dn_hymoney']=$_GPC['dn_hymoney'];
        $res=pdo_update('cjdc_goods',$data,array('id'=>$_GPC['good_id']));   
        for($i=0;$i<count($_GPC['list']);$i++){
            if($_GPC['list'][$i]['coid']){
                pdo_update('cjdc_spec',array('name'=>$_GPC['list'][$i]['color']),array('id'=>$_GPC['list'][$i]['coid']));
            }else{
                pdo_delete('cjdc_spec_combination',array('good_id'=>$_GPC['good_id']));
                pdo_delete('cjdc_shopcar',array('good_id'=>$_GPC['good_id']));
                pdo_insert('cjdc_spec',array('name'=>$_GPC['list'][$i]['color'],'good_id'=>$_GPC['good_id'],'num'=>$i,'uniacid'=>$_W['uniacid']));
                $specid=pdo_insertid();
            }
            
            for($j=0;$j<count($_GPC['list'][$i]['ggarr']);$j++){
                if(is_numeric($_GPC['list'][$i]['ggarr'][$j]['shuid'])){
                    pdo_update('cjdc_spec_val',array('name'=>$_GPC['list'][$i]['ggarr'][$j]['guigebig']),array('id'=>$_GPC['list'][$i]['ggarr'][$j]['shuid']));
                }else{
                    if($_GPC['list'][$i]['coid']){
                        $spec_id=$_GPC['list'][$i]['coid'];
                    }else{
                        $spec_id=$specid;
                    }
                    pdo_delete('cjdc_spec_combination',array('good_id'=>$_GPC['good_id']));
                    pdo_delete('cjdc_shopcar',array('good_id'=>$_GPC['good_id']));
                    pdo_insert('cjdc_spec_val',array('name'=>$_GPC['list'][$i]['ggarr'][$j]['guigebig'],'spec_id'=>$spec_id,'num'=>$j,'uniacid'=>$_W['uniacid'],'good_id'=>$_GPC['good_id']));
                }
            }
        }
        for($k=0;$k<count($_GPC['menu']);$k++){
             $data4['combination']='';
            for($l=0;$l<(count($_GPC['menu'][$k]['biao'])-3);$l++){
                    $data4['combination'] .=$_GPC['menu'][$k]['biao'][$l]['inpval'].",";
            }
            $data4['combination']=substr($data4['combination'],0,strlen($data4['combination'])-1); 
             $count=count($_GPC['menu'][$k]['biao'])-3;
            $count2=count($_GPC['menu'][$k]['biao'])-2;
            $count3=count($_GPC['menu'][$k]['biao'])-1;
            $data4['number']=$_GPC['menu'][$k]['biao'][$count]['inpval'];
            $data4['wm_money']=$_GPC['menu'][$k]['biao'][$count2]['inpval'];
            $data4['dn_money']=$_GPC['menu'][$k]['biao'][$count3]['inpval'];
            $data4['good_id']=$_GPC['good_id'];
            if($_GPC['menu'][$k]['id']){
                pdo_update("cjdc_spec_combination",$data4,array('id'=>$_GPC['menu'][$k]['id']));
            }else{
                pdo_insert("cjdc_spec_combination",$data4);
            }
            
        }


    }

//查看商品详情
    public function doMobileGoodInfo(){
        global $_W,$_GPC;
        $type=pdo_getall('cjdc_spec',array('uniacid'=>$_W['uniacid'],'good_id'=>$_GPC['good_id']),array(),'','num ASC');
         $list=pdo_getall('cjdc_spec_val',array('uniacid'=>$_W['uniacid'],'good_id'=>$_GPC['good_id']),array(),'','num ASC');
         $data2=array();
         for($i=0;$i<count($type);$i++){
          $data=array();
          for($k=0;$k<count($list);$k++){
            if($type[$i]['id']==$list[$k]['spec_id']){
              $data[]=array(
                'id'=>$list[$k]['id'],
                'name'=>$list[$k]['name']
                );
            }           
          }
          $data2[]=array(
            'id'=>$type[$i]['id'],
            'sepc_name'=>$type[$i]['name'],
            'sepc_val'=>$data
            );
        }
        $combination=pdo_getall('cjdc_spec_combination',array('good_id'=>$_GPC['good_id']));
        $res['spec']=$data2;
        $res['combination']=$combination;
        echo json_encode($res);

}




public function doMobileNewCall(){
    global $_W, $_GPC;
    if($_GPC['type']==2){
          //$src=  str_replace('_','/',$_GPC['src']);
     $src=preg_replace('/_/', '/', $_GPC['src'], 1);
     pdo_update('cjdc_call',array('src'=>$src),array('store_id'=>$_GPC['store_id']));
     echo  json_encode($src);
 }
 if($_GPC['type']==1){
    $res=pdo_get('cjdc_call',array('store_id'=>$_GPC['store_id']));
    $src= $res['src'];
    echo  json_encode($src);
}

}



//确认用餐
public function doMobileQueryNumber(){
    global $_W, $_GPC;
    $number=pdo_get('cjdc_number',array('id'=>$_GPC['id']));
    $store=pdo_get('cjdc_call',array('store_id'=>$number['store_id']));
   $num=2;
   for($i=0;$i<$num;$i++){
     $content.="请".$number['code']."的顾客用餐,";
 }
 
    $appid=$store['appid'];
    $appkey=$store['apikey'];
    $output_path="../addons/zh_cjdianc/call/yc".$number['code'].$number['id'].".wav";
    $param = [ 'engine_type' => 'intp65',
                'auf' => 'audio/L16;rate=16000',
                'aue' => 'raw',
                'voice_name' => 'xiaoyan', 
                'speed' => '0'
    ];    
    $cur_time = (string)time();    
    $x_param = base64_encode(json_encode($param));    
    $header_data = ['X-Appid:'.$appid,       
                    'X-CurTime:'.$cur_time,       
                    'X-Param:'.$x_param,       
                    'X-CheckSum:'.md5($appkey.$cur_time.$x_param),        
                    'Content-Type:application/x-www-form-urlencoded; charset=utf-8'
    ];
    $body_data = 'text='.urlencode($content);    //Request
    $url = "http://api.xfyun.cn/v1/service/v1/tts";   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body_data);   
    $result = curl_exec($ch);    
    $res_header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);    
    $res_header = substr($result, 0, $res_header_size);
    curl_close($ch);    
    if(stripos($res_header, 'Content-Type: audio/mpeg') === FALSE){ //合成错误
        return substr($result, $res_header_size);
    }else{
        file_put_contents($output_path, substr($result, $res_header_size)); 
    //echo   "<audio src='{$output_path}' autoplay='autoplay' controls='controls'  hidden='true' ></audio>";die;
         //return '语音合成成功，请查看文件！';
        return  json_encode($output_path);
    }


}



    //删除呼叫记录
    public function doMobileDelCall(){
        global $_W,$_GPC;
        $res=pdo_delete('cjdc_calllog',array('id'=>$_GPC['id']));
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }


//排队入座
public function doMobilePdrz(){
      global $_W,$_GPC;
      $rst=pdo_update('cjdc_number',array('state'=>2),array('id'=>$_GPC['id']));
      if($rst){
        echo '1';
         }else{
        echo '2';
        }

}

//排队跳号
public function doMobilePdth(){
      global $_W,$_GPC;
      $rst=pdo_update('cjdc_number',array('state'=>3),array('id'=>$_GPC['id']));
      if($rst){
        echo '1';
        }else{
        echo '2';
        }

}

//获取一级
   public function doMobileGetYj(){
        global $_W,$_GPC;
        $user_id=$_GPC['user_id'];
        $sql=" select b.name as yj_name,c.name as fx_name,a.time from".tablename('cjdc_fxuser')." a left join ".tablename('cjdc_user')." b on a.fx_user=b.id left join ".tablename('cjdc_user')." c on a.user_id=c.id where a.user_id={$user_id}";
        $res=pdo_fetchall($sql);
        //$res=pdo_getall('cjdc_fxuser',array('user_id'=>$user_id));
        echo json_encode($res);     
}
//获取二级
   public function doMobileGetEj(){
        global $_W,$_GPC;
        $user_id=$_GPC['user_id'];
        $fx=pdo_get('cjdc_user',array('id'=> $user_id),'name');
        $res=pdo_getall('cjdc_fxuser',array('user_id'=>$user_id),'fx_user');
      
        foreach ($res as $key => $value) {
              $sql=" select b.name as yj_name,a.time from".tablename('cjdc_fxuser')." a left join ".tablename('cjdc_user')." b on a.fx_user=b.id  where a.user_id={$value['fx_user']}";
        $res2=pdo_fetchall($sql);        
        if($res2){
            foreach ($res2 as $key2 => $value2) {
            $rst[$key2]['fx_name']=$fx['name'];
            $rst[$key2]['yj_name']=$value2['yj_name'];
            $rst[$key2]['time']=$value2['time'];
            }
          
        }     
        }
        echo json_encode($rst);
 
      
}

//批量通过分销商
public function  doMobileAllAdopt(){
     global $_W,$_GPC;
     $res=pdo_update('cjdc_retail',array('state'=>2),array('id'=>$_GPC['id']));
     echo json_encode($res);
}


//批量删除餐桌
public function  doMobileAllDelTable(){
     global $_W,$_GPC;
     $res=pdo_delete('cjdc_table',array('id'=>$_GPC['id']));
     echo json_encode($res);
}



//跑腿详情
public function doMobileGetPtInfo(){
        global $_W, $_GPC;
        $order_id=$_GPC['order_id'];
        include IA_ROOT.'/addons/zh_cjdianc/peisong/cjpt.php'; 
        $order=pdo_get('cjdc_order',array('id'=>$order_id));
         $bind=pdo_get('cjpt_bind',array('cy_uniacid'=>$_W['uniacid']));
        $newstr = substr($news,0,strlen($news)-1); 
        //下订单
         $data = array(
          'order_num'=> $order['order_num'],
          'uniacid'=>$_W['uniacid'],
          );
        $url=$_W['siteroot']."app/index.php?i=".$bind['pt_uniacid']."&c=entry&a=wxapp&do=GetOrderInfo&m=zh_cjpt";
        $result=cjpt::requestWithPost($url,$data);
        return $result;

    }
    

//达达详情
  public function doMobileGetDadaInfo(){
     global $_W, $_GPC;
      $order_id=$_GPC['order_id'];
     include IA_ROOT.'/addons/zh_cjdianc/peisong/peisong.php';
     $order=pdo_get('cjdc_order',array('id'=>$order_id));
     $set=pdo_get('cjdc_psset',array('store_id'=>$order['store_id']));
     $system=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
//*********************配置项*************************
     $config = array();
     $config['app_key'] = $system['dada_key'];
     $config['app_secret'] = $system['dada_secret'];
     $config['source_id'] =$set['source_id'];
       // $config['app_key'] = 'dada69fa59eef841ee2';
       // $config['app_secret'] = '18e0b16c94f1dab5a920fadc6a6897d7';
       // $config['source_id'] ='73753';
     $config['url'] = 'http://newopen.imdada.cn/api/order/status/query';
     $data2 = array(        
          'order_id'=> $order['order_num'],//订单id
          // 'order_id'=> '201807021442512909',
          );
     $result= Peisong::requestMethod($config,$data2);

    echo json_encode($result);
 }
 
 //快服务详情
 public  function  doMobileGetKfwInfo(){
    global $_W, $_GPC;
    include IA_ROOT.'/addons/zh_jd/peisong/KfwOpenapi.php';
     $order_id=$_GPC['order_id'];
     $order=pdo_get('cjdc_order',array('id'=>$order_id));

     $set=pdo_get('cjdc_kfwset',array('store_id'=>$order['store_id']));
     $system=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
    $app_secret=$system['kfw_appsecret'];
       $data = array(
          'app_id'=>  $system['kfw_appid'],
          'access_token'=> $set['access_token'],
          'ship_id'=> $order['ship_id'],
          );
        $obj= new KfwOpenapi();
         $sign=$obj->getSign($data,$app_secret);
         $data['sign']=$sign;
        $url="http://openapi.kfw.net/openapi/v1/order/status";
        // $url="http://openapi.kfw.net/openapi/v1/order/status";
        $result=$obj->requestWithPost($url,$data);
        //var_dump(json_decode($result));die;
        return $result;
 }


////下架
    public function doMobileGroupXj(){
        global $_W,$_GPC;
        $res=pdo_update('cjdc_groupgoods',array('is_shelves'=>2),array('id'=>$_GPC['id']));
        if($res){
            echo  '1';
        }else{
            echo  '2';
        }
    }
     public function doMobileGroupSj(){
        global $_W,$_GPC;
        $res=pdo_update('cjdc_groupgoods',array('is_shelves'=>1),array('id'=>$_GPC['id']));
        if($res){
            echo  '1';
        }else{
            echo  '2';
        }
    }

    public function doMobileDelGroup(){
        global $_W,$_GPC;
        $res=pdo_delete('cjdc_groupgoods',array('id'=>$_GPC['id']));
        if($res){
            echo  '1';
        }else{
            echo  '2';
        }
    }


    public function doMobileGroupUpdate(){
        global $_W,$_GPC;
        if($_GPC['name']){
         $data['name']=$_GPC['name']; 
     }
     if($_GPC['money']){
         $data['pt_price']=$_GPC['money']; 
     }
     if($_GPC['wm_money']){
         $data['dd_price']=$_GPC['wm_money']; 
     }
     if($_GPC['box_fee']){
         $data['people']=$_GPC['box_fee']; 
     }
     if($_GPC['num']){
         $data['inventory']=$_GPC['num']; 
     }
     if(isset($_GPC['xs_num'])){
         $data['ysc_num']=$_GPC['xs_num']; 
     }
     $res=pdo_update('cjdc_groupgoods',$data,array('id'=>$_GPC['id']));
     if($res){
        echo '1';
    }else{
        echo '2';
    }
}


    public function doMobileSelectHx(){
    global $_W, $_GPC;
    $sql =" select id,name from ".tablename('cjdc_user')." where uniacid={$_W['uniacid']} and id not in (select hx_id  from" .tablename('cjdc_grouphx')."where store_id={$_GPC['store_id']} )  and name != '' and  openid like '%{$_GPC['keywords']}%'";    
    echo json_encode(pdo_fetchall($sql));
  }



    //删除签到活动
    public function doMobileDelQd(){
        global $_W,$_GPC;
        $res=pdo_delete('cjdc_continuous',array('id'=>$_GPC['id']));
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }
    //添加签到规则
    public function doMobileAddQd(){
        global $_W,$_GPC;
        pdo_delete('cjdc_continuous',array('uniacid'=>$_W['uniacid']));
        for($i=0;$i<count($_GPC['list']);$i++){
            $data['day']=$_GPC['list'][$i]['day'];
            $data['integral']=$_GPC['list'][$i]['integral'];
            $data['uniacid']=$_W['uniacid'];
            pdo_insert('cjdc_continuous',$data);
        }
        $data2['one']=$_GPC['one'];
        $data2['integral']=$_GPC['integral'];
        $data2['is_open']=$_GPC['is_open'];
        $data2['qd_img']=$_GPC['qd_img'];
        $data2['is_bq']=$_GPC['is_bq'];
        $data2['bq_integral']=$_GPC['bq_integral'];
        $res=pdo_get('cjdc_signset',array('uniacid'=>$_W['uniacid']));
        if($res){
            pdo_update('cjdc_signset',$data2,array('uniacid'=>$_W['uniacid']));
        }else{
            $data2['uniacid']=$_W['uniacid'];
            pdo_insert('cjdc_signset',$data2);
        }
        $res2=pdo_get('cjdc_special',array('uniacid'=>$_W['uniacid']));
        $data3['day']=$_GPC['day'];
        $data3['integral']=$_GPC['integral2'];
        $data3['title']=$_GPC['title'];
        $data3['color']=$_GPC['color'];
        if($res2){
          
            pdo_update('cjdc_special',$data3,array('uniacid'=>$_W['uniacid']));
        }else{
            $data3['uniacid']=$_W['uniacid'];
            pdo_insert('cjdc_special',$data3);
        }
    }


    public function doMobileSelectStore(){
        global $_W, $_GPC;
        $sql =" select id,name from ".tablename('cjdc_store')." where uniacid={$_W['uniacid']} and state=2 and  name like '%{$_GPC['keywords']}%'";    
        echo json_encode(pdo_fetchall($sql));
    }

    public function doMobileUpdOrderMoney(){
        global $_W,$_GPC;
        $order=
        $res=pdo_update('cjdc_order',array('money'=>$_GPC['money'],'original_money'=>$_GPC['y_money']),array('id'=>$_GPC['id']));
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }


}