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
    $data['tk_tid']=trim($_GPC['tk_tid']);
    $data['shtk_tid']=trim($_GPC['shtk_tid']);
    $data['yy_tid']=trim($_GPC['yy_tid']);
    $data['sjyy_tid']=trim($_GPC['sjyy_tid']);
    $data['rzsh_tid']=trim($_GPC['rzsh_tid']);
    $data['rzcg_tid']=trim($_GPC['rzcg_tid']);
    $data['cz_tid']=trim($_GPC['cz_tid']);
    $data['xdd_tid']=trim($_GPC['xdd_tid']);
    $data['xdd_tid2']=trim($_GPC['xdd_tid2']);
    $data['rush_tid']=trim($_GPC['rush_tid']);
    $data['group_tid']=trim($_GPC['group_tid']);
    $data['qf_tid']=trim($_GPC['qf_tid']);
    $data['qh_tid']=trim($_GPC['qh_tid']);
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


if($_GPC['op']=='generate'){
   $item=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
   $data['xd_tid']= $this->generateTemplate('AT0009',['订单号','联系人姓名','联系人手机号','金额','时间']);
   $data['jd_tid']= $this->generateTemplate('AT0176',['订单编号','订单状态','订单内容','更新时间','备注']);
   $data['jj_tid']= $this->generateTemplate('AT0375',['订单号','拒绝时间','拒绝原因','商家名称','客服电话','支付金额','备注']);
   $data['tk_tid']= $this->generateTemplate('AT0036',['订单编号','退款商家','退款金额','退款方式','退款时间']);
   $data['shtk_tid']= $this->generateTemplate('AT0637',['温馨提示','订单编号','申请时间','退款状态','退款商品','顾客信息']);
   $data['yy_tid']= $this->generateTemplate('AT0080',['预定商家','订单号','联系电话','预定人数','预定座号','预定时间','订单金额','用餐时间','备注']);
   $data['sjyy_tid']= $this->generateTemplate('AT0104',['预约时间','订单号','预约服务','就餐人数','联系人','联系方式']);
   $data['rzsh_tid']= $this->generateTemplate('AT0444',['状态','申请时间','商家名称','联系电话','备注']);
   $data['rzcg_tid']= $this->generateTemplate('AT1709',['审核结果','申请时间','商家名称','审核备注']);
   $data['cz_tid']= $this->generateTemplate('AT0016',['充值金额','赠送金额','充值时间','备注']);
   $data['xdd_tid']= $this->generateTemplate('AT0079',['订单内容','订单类型','下单时间','订单金额','收货人','联系电话','收货地址','订单号']);
   $data['xdd_tid2']= $this->generateTemplate('AT0152',['收款金额','支付方式','订单时间','付款人','交易单号']);
   $data['rush_tid']= $this->generateTemplate('AT0079',['订单号','商品名称','联系电话','订单金额','支付时间','到期时间']);
   $data['group_tid']= $this->generateTemplate('AT0223',['订单编号','商品信息','活动时间','报名人数']);
   $data['qf_tid']= $this->generateTemplate('AT0480',['备注','信息来源','信息内容','通知时间']);
   $data['qh_tid']= $this->generateTemplate('AT0086',['排队状态','排队号码','桌位类型','还需等待','取号时间','商家名称','温馨提示']);
   if($item){
        $data['uniacid']=$_W['uniacid'];
        $res = pdo_update('cjdc_message', $data, array('uniacid' => $_W['uniacid']));
   }else{
        $res=pdo_insert('cjdc_message',$data);
   }
   if($res){
        message('更新成功',$this->createWebUrl('template',array()),'success');
    }else{
        message('更新失败','','error');
    }
}
include $this->template('web/template');