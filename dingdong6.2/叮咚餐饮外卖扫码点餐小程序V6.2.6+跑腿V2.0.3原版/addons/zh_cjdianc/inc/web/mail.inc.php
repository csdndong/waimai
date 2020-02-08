<?php
global $_GPC, $_W;
load()->func('tpl');
/*$weid = $this->_weid;
$action = 'point_propor';*/
$GLOBALS['frames'] = $this->getMainMenu($storeid,$action);
// $title = $this->actions_titles[$action];
// $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
// if ($operation == 'display') {
//    $list=pdo_getall('zhjd_region',array('uniacid'=>$_W['uniacid']));
// } elseif ($operation == 'post') {
//      $list=pdo_get('zhjd_region',array('id'=>$_GPC['id']));
//     //var_dump($list);die;
//     if(checksubmit('submit')){
//             $data['city']=$_GPC['city'];
//             $data['sort']=$_GPC['sort'];
//             $data['uniacid']=$_W['uniacid'];
//             $data['created_time']=date('Y-m-d H:i:s');         
//             if($_GPC['id']==''){
//                 $res=pdo_insert('zhjd_region',$data);
//                 if($res){
//                     message('添加成功',$this->createWebUrl('region',array()),'success');
//                 }else{
//                     message('添加失败','','error');
//                 }

//             }else{           
//                 $res = pdo_update('zhjd_region', $data, array('id' => $_GPC['id']));
//                 if($res){
//                     message('编辑成功',$this->createWebUrl('region',array()),'success');
//                 }else{
//                     message('编辑失败','','error');
//                 }
//             }
//         }

// }elseif ($operation == 'delete') {
//             $id=$_GPC['id'];
//         $result = pdo_delete('zhjd_region', array('id'=>$id));
//         if($result){
//             message('删除成功',$this->createWebUrl('region',array()),'success');
//         }else{
//             message('删除失败','','error');
//         }
// }
include $this->template('web/mail');