<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:25:29
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\system\otherset.html" */ ?>
<?php /*%%SmartyHeaderCode:45545cd55fa91279c7-85440522%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '086bd06623966a314f05d68eac5afcf52838c619' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\system\\otherset.html',
      1 => 1538893251,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '45545cd55fa91279c7-85440522',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'siteurl' => 0,
    'is_static' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd55fa924afa4_91372507',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55fa924afa4_91372507')) {function content_5cd55fa924afa4_91372507($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<meta http-equiv="Content-Language" content="zh-CN"> 
<meta content="all" name="robots"> 
<meta name="description" content=""> 
<meta content="" name="keywords"> 
<title>后台管理中心 </title>  
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/admin1.css?v=9.0"> 
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/public.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=wmrPopup"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js?v=9.0" type="text/javascript" language="javascript"></script>

<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/datepicker/WdatePicker.js"></script>
 
<script>
	var menu = null;
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	 
</script> 
</head> 
<body style="position:relative;"> 
<div id="cat_zhe" class="cart_zhe" style="display:none;"></div>
 
 
<div style="clear:both;"></div>
<div class="newmain" style='height:auto!important'>
 
   <!-- 主内容区-->
   <div class="newmain_all">
   	 <!-- 主内左区-->
   	 
   	  
 
 
  
 
 
 <div class="right_content">
	<div class="show_content_m">
   	        	 <div class="show_content_m_ti">
   	        	 	   <div class="showtop_t" id="positionname">网站限制</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
	 <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
         
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/saveotherset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
                  	 
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">用户注册发送验证码</td>
                      <td><input type="checkbox" name="regestercode" id="regestercode" value="1"  <?php if ($_smarty_tpl->tpl_vars['regestercode']->value==1){?>checked<?php }?>>勾选允许&nbsp;&nbsp;&nbsp;(PC端、微信端、APP端同时生效)</td>
                    </tr>
                    
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">微信端登录方式</td>
                        <td><input type="radio" name="wxLoginType"   value="0"  <?php if ($_smarty_tpl->tpl_vars['wxLoginType']->value==0){?>checked<?php }?>>自动登录（微信接口） 
                       <input type="radio" name="wxLoginType"   value="1"  <?php if ($_smarty_tpl->tpl_vars['wxLoginType']->value==1){?>checked<?php }?>>其他登录方式  &nbsp;&nbsp;&nbsp;（仅微信端和触屏端有效）</td>
                    </tr>
					
					<!-- <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">是否开启前台促销规则</td>
                        <td><input type="checkbox" name="open_wxcx" id="open_wxcx" value="1" <?php if ($_smarty_tpl->tpl_vars['open_wxcx']->value==1){?>checked<?php }?>>勾选开启,商家列表显示促销信息 &nbsp;&nbsp;&nbsp;（微信端、APP端同时生效）</td>
                    </tr>  -->       
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">下单成功后通知买家</td>
                      <td>
                      	<input type="checkbox" name="allowedsendbuyer" id="allowedsendbuyer" value="1" <?php if ($_smarty_tpl->tpl_vars['allowedsendbuyer']->value==1){?>checked<?php }?>>短信通知 
                      	</td>
                    </tr>
                   <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">退款申请是否需要平台审核</td>
                      <td>
                      	<input type="checkbox" name="shenhedrawback" id="shenhedrawback" value="1" <?php if ($_smarty_tpl->tpl_vars['shenhedrawback']->value==1){?>checked<?php }?>>  勾选后，申请退款订单需平台审核确认后才能退款；不勾选，不需平台审核，直接退款。                      	</td>
                    </tr>
                   
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
						<td class="left">商家制作后是否支持申请退款</td>                      					  
                      	<td><input type="radio" name="allowreback"   value="1"  <?php if ($_smarty_tpl->tpl_vars['allowreback']->value==1){?>checked<?php }?>> 是（支持退款） 
						<input type="radio" name="allowreback"   value="0"  <?php if ($_smarty_tpl->tpl_vars['allowreback']->value==0){?>checked<?php }?>> 否（不支持退款） &nbsp&nbsp&nbsp&nbsp该设置只对在线支付订单有效 </td>      
                    </tr>
					
                   
				   <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">管理员后台审核订单</td>
                      <td><input type="checkbox" name="man_ispass" id="man_ispass" value="1"  <?php if ($_smarty_tpl->tpl_vars['man_ispass']->value==1){?>checked<?php }?>>勾选审核,开启后管理员审核通过商家才能看到订单并同时发送通知(只针对货到支付，若是在线支付 支付成功后自动发送通知)</td>
                    </tr>                   
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">是否是提交苹果审核</td>
                      <td><input type="checkbox" name="ios_waiting" id="ios_waiting" value="1"  <?php if ($_smarty_tpl->tpl_vars['ios_waiting']->value==1){?>checked<?php }?>>勾选表示提交审核中,IOS程序限制距离无效
                      	</td>
                    </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">是否启用一键打款到微信零钱</td>
                      <td><input type="checkbox" name="pay_wechat" id="pay_wechat" value="1"  <?php if ($_smarty_tpl->tpl_vars['pay_wechat']->value==1){?>checked<?php }?>>开启后如后台确认提现，则直接转账到微信零钱
                      	</td>
                    </tr>
					 <!-- 
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff" style="display:none;">
                        <td class="left">Memcached缓存</td>
                        <td><input type="radio" name="datacache"   value="0"  checked>不使用 
                       <input type="radio" name="datacache"   value="1"  <?php if ($_smarty_tpl->tpl_vars['datacache']->value==1){?>checked<?php }?>>使用</td>
                    </tr>
                     <tr class="gf" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">常用数据缓存时长</td>
                      <td> <input type="input" name="datacachetime" id="datacachetime" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['datacachetime']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:200px;">秒不能低于300秒 </td>
                    </tr>
                     <tr class="gf" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">不常用数据缓存时长</td>
                      <td> <input type="input" name="datacachelongtime" id="datacachelongtime" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['datacachelongtime']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:200px;">秒不能少于1800秒 例如公告和广告</td>
                    </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">平台采购店铺id</td>
                      <td><input type="text" name="plateshopid" id="plateshopid" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['plateshopid']->value)===null||$tmp==='' ? '0' : $tmp);?>
" class="skey" style="width:50px;"></td>
                    </tr> -->
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="siteset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
        </div> 
        
        
   
    </div> 
<script>
$(function(){
	$("[name='datacache']").bind('click',function(){
		changechoice();
	});
     changechoice();
});
function changechoice(){
   var tempid = $("[name='datacache']:checked").val(); 
   if(tempid == 0){
		$('.gf').hide();
		 
   }else if(tempid == 1){ 
		$('.gf').show(); 
   }
}

</script>
 
   	        	 	
               <div class="show_content_m_t3">
   	        	 </div>
   	        	 
   	       </div>
   	       <!-- show_content_m结束-->


   	  </div>
   	  <!-- right_content 结束-->
   	  
   </div>
   <!-- newmain_all 结束-->
</div>	
	
<!--newmain 结束-->
















<div style="clear:both;"></div>
 
<script>
$(function(){ 
 $('.show_page a').wrap('<li></li>');
 $('.show_page').find('.current').eq(0).parent().css({'background':'#f60'}); 
});
   function limitalert(){
		diaerror("您暂无权限设置,如有疑问请联系QQ：375952873  Tel：18538930577");
	}
</script>
</body>
</html>





<?php }} ?>