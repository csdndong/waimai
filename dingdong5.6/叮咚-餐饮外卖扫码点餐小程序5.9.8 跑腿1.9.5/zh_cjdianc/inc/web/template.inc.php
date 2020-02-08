<?php
global $_GPC, $_W;
// $action = 'ad';
// $title = $this->actions_titles[$action];
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
 $item=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
    if(checksubmit('submit')){
            $data['xd_tid']=trim($_GPC['xd_tid']);
            $data['jd_tid']=trim($_GPC['jd_tid']);
            $data['jj_tid']=trim($_GPC['jj_tid']);
            $data['yy_tid']=trim($_GPC['yy_tid']);
            $data['tk_tid']=trim($_GPC['tk_tid']);
             $data['rzsh_tid']=trim($_GPC['rzsh_tid']);
            $data['rzcg_tid']=trim($_GPC['rzcg_tid']);
            $data['rzjj_tid']=trim($_GPC['rzjj_tid']);
             $data['cz_tid']=trim($_GPC['cz_tid']);
             $data['xdd_tid']=trim($_GPC['xdd_tid']);
             $data['xdd_tid2']=trim($_GPC['xdd_tid2']);
             $data['qf_tid']=trim($_GPC['qf_tid']);
             $data['qh_tid']=trim($_GPC['qh_tid']);
            $data['sjyy_tid']=trim($_GPC['sjyy_tid']);
            $data['shtk_tid']=trim($_GPC['shtk_tid']);
            $data['uniacid']=trim($_W['uniacid']);
            if($_GPC['id']==''){                
                $res=pdo_insert('cjdc_message',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('template',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('cjdc_message', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('template',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
    include $this->template('web/template');