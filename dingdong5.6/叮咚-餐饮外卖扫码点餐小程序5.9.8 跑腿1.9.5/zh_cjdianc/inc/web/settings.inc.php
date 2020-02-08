<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$stores=pdo_getall('cjdc_store',array('uniacid'=>$_W['uniacid'],'state'=>2));
$item=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
if($item['gs_img']){


if(strpos($item['gs_img'],',')){
    $gs_img= explode(',',$item['gs_img']);
}else{
    $gs_img=array(
      0=>$item['gs_img']
    );
}
}
// print_r($item);die;
    if(checksubmit('submit')){
            $data['url_name']=$_GPC['url_name'];
            $data['tel']=$_GPC['tel'];
            if($_GPC['model']){
             $data['model']=$_GPC['model']; 
            }
            if($_GPC['color']){
                $data['color']=$_GPC['color'];
            }else{
                $data['color']="#34AAFF";
            }
            if($_GPC['default_store']){
                 $data['default_store']=$_GPC['default_store'];
            }
           
            $data['wm_name']=$_GPC['wm_name'];
            $data['dc_name']=$_GPC['dc_name'];
            $data['yd_name']=$_GPC['yd_name'];
            // $data['is_psxx']=$_GPC['is_psxx'];
            $data['details']=html_entity_decode($_GPC['details']);
            $data['gs_details']=html_entity_decode($_GPC['gs_details']);
            $data['gs_add']=$_GPC['gs_add'];
            $data['gs_time']=$_GPC['gs_time'];
            $data['gs_tel']=$_GPC['gs_tel'];
            $data['fl_more']=$_GPC['fl_more'];
            $data['gs_zb']=$_GPC['gs_zb'];
            $data['is_brand']=$_GPC['is_brand'];
            $data['countdown']=$_GPC['countdown'];
            $data['distance']=$_GPC['distance'];     
            $data['fx_title']=$_GPC['fx_title']; 
            $data['is_zb']=$_GPC['is_zb']; 
            $data['is_qg']=$_GPC['is_qg']; 
            $data['isyykg']=$_GPC['isyykg'];  
            $data['is_tzms']=$_GPC['is_tzms'];
            $data['is_tj']=$_GPC['is_tj'];
            $data['is_pay']=$_GPC['is_pay'];
            
            if($_GPC['is_pay']==1){
                 $data['pay_money']=$_GPC['pay_money'];
            }
           // $data['mph_name']=$_GPC['mph_name'];       
            for($i=0;$i<count($_GPC['gs_img']);$i++){
                if(strlen($_GPC['gs_img'][$i])<=60){
                   $_GPC['gs_img'][$i]= $_W['attachurl'].$_GPC['gs_img'][$i];
                }
            }
            if($_GPC['gs_img']){
            $data['gs_img']=implode(",",$_GPC['gs_img']);
            }else{
                $data['gs_img']='';
            }

          
            $data['uniacid']=$_W['uniacid'];
            if($_GPC['id']==''){                
                $res=pdo_insert('cjdc_system',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('settings',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('cjdc_system', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('settings',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
include $this->template('web/settings');