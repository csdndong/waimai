<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
// $stores=pdo_getall('wpdc_store',array('uniacid'=>$_W['uniacid']));
//  $item=pdo_get('wpdc_system',array('uniacid'=>$_W['uniacid']));
// // print_r($item);die;
//     if(checksubmit('submit')){
//             $data['support']=$_GPC['support'];
//             $data['pt_name']=$_GPC['pt_name'];
//             $data['bq_name']=$_GPC['bq_name'];
//             $data['default_store']=$_GPC['default_store'];
//             $data['more']=$_GPC['more'];
//             $data['link_name']=$_GPC['link_name'];
//             $data['link_logo']=$_GPC['link_logo'];
//             $data['bq_logo']=$_GPC['bq_logo'];
//              $data['wm_name']=$_GPC['wm_name'];
//               $data['dc_name']=$_GPC['dc_name'];
//                $data['yd_name']=$_GPC['yd_name'];
//              $data['tel']=$_GPC['tel'];
//             $data['tz_appid']=trim($_GPC['tz_appid']);
//             $data['tz_name']=$_GPC['tz_name'];
//             $data['day']=$_GPC['day'];  
//             $data['details']=html_entity_decode($_GPC['details']);
//             $data['uniacid']=$_W['uniacid'];
//             if($_GPC['color']){
//                 $data['color']=$_GPC['color'];
//             }else{
//                 $data['color']="#34AAFF";
//             }
//             if($_GPC['id']==''){                
//                 $res=pdo_insert('wpdc_system',$data);
//                 if($res){
//                     message('添加成功',$this->createWebUrl('settings',array()),'success');
//                 }else{
//                     message('添加失败','','error');
//                 }
//             }else{
//                 $res = pdo_update('wpdc_system', $data, array('id' => $_GPC['id']));
//                 if($res){
//                     message('编辑成功',$this->createWebUrl('settings',array()),'success');
//                 }else{
//                     message('编辑失败','','error');
//                 }
//             }
//         }
include $this->template('web/statistics');