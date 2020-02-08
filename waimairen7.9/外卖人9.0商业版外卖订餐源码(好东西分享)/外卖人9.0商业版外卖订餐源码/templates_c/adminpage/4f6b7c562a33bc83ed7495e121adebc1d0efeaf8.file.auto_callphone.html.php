<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:24:50
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\other\auto_callphone.html" */ ?>
<?php /*%%SmartyHeaderCode:170565cd55f82933454-74804922%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f6b7c562a33bc83ed7495e121adebc1d0efeaf8' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\other\\auto_callphone.html',
      1 => 1537945555,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '170565cd55f82933454-74804922',
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
  'unifunc' => 'content_5cd55f82a6a343_23013457',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f82a6a343_23013457')) {function content_5cd55f82a6a343_23013457($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">自动拨打电话设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
	
	  <div style="width:auto;overflow-x:hidden;overflow-y:auto;height:auto;">   
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/savecallphoneset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
             
			 
				<tr style="float: left;" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">是否开启电话通知商家</td>
                      <td>  
						<input type="radio" name="is_auto_callphone"  value="0"  <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==0){?>checked<?php }?>>不启用
						<input type="radio" name="is_auto_callphone"  value="1" <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==1){?>checked<?php }?> >启用
 					  </td>
                    </tr>  
			  
                    <tr  style="float: left; <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==1){?> display:block; <?php }else{ ?> display:none; <?php }?>  " class="imgserverBox" onmouseover="this.bgColor='#F5F5F5';"onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">Access Key Id</td>
                      <td>	  
                      	<input type="text" name="autoCallPhone_accessKeyId" id="autoCallPhone_accessKeyId" value="<?php echo $_smarty_tpl->tpl_vars['autoCallPhone_accessKeyId']->value;?>
" class="skey" style="width:350px;">  
						
                      </td>
                    </tr>
                        <tr  style="float: left; <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==1){?> display:block; <?php }else{ ?> display:none; <?php }?>  " class="imgserverBox" onmouseover="this.bgColor='#F5F5F5';"onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">Access Key Secret</td>
                      <td>	  
                      	<input type="text" name="autoCallPhone_accessKeySecret" id="autoCallPhone_accessKeySecret" value="<?php echo $_smarty_tpl->tpl_vars['autoCallPhone_accessKeySecret']->value;?>
" class="skey" style="width:350px;">  
						
                      </td>
                    </tr>
                    
					   <tr  style="float: left; <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==1){?> display:block; <?php }else{ ?> display:none; <?php }?>  " class="imgserverBox" onmouseover="this.bgColor='#F5F5F5';"onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">被叫显号</td>
                      <td>	  
                      	<input type="text" name="autoCallPhone_tel" id="autoCallPhone_tel" value="<?php echo $_smarty_tpl->tpl_vars['autoCallPhone_tel']->value;?>
" class="skey" style="width:350px;">
						
                      </td>
                    </tr> 
					<tr  style="float: left; <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==1){?> display:block; <?php }else{ ?> display:none; <?php }?>  " class="imgserverBox" onmouseover="this.bgColor='#F5F5F5';"onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">模板ID</td>
                      <td>	  
                      	<input type="text" name="autoCallPhone_TelCode" id="autoCallPhone_TelCode" value="<?php echo $_smarty_tpl->tpl_vars['autoCallPhone_TelCode']->value;?>
" class="skey" style="width:350px;">
						
                      </td>
                    </tr>
					
					
					  <tr  style="float: left; <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==1){?> display:block; <?php }else{ ?> display:none; <?php }?>   " class="imgserverBox" onmouseover="this.bgColor='#F5F5F5';"onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">是否开启未制作订单几分钟内自动电话通知</td>
                      <td>  
						<input type="radio" name="is_make_auto_callphone"  value="0"  <?php if ($_smarty_tpl->tpl_vars['is_make_auto_callphone']->value==0){?>checked<?php }?>>不启用
						<input type="radio" name="is_make_auto_callphone"  value="1" <?php if ($_smarty_tpl->tpl_vars['is_make_auto_callphone']->value==1){?>checked<?php }?> >启用
 					  </td>
                    </tr>
                    
										
                     <tr  style="float: left; <?php if ($_smarty_tpl->tpl_vars['is_auto_callphone']->value==1){?> display:block; <?php }else{ ?> display:none; <?php }?>  margin-top: 40px;" class="imgserverBox" onmouseover="this.bgColor='#F5F5F5';"onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">间隔分钟数</td>
                      <td>	  
                      	<input type="text" name="autoCallPhone_Minute" id="autoCallPhone_Minute" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['autoCallPhone_Minute']->value)===null||$tmp==='' ? 3 : $tmp);?>
" class="skey" style="width:60px;"> 分钟 
						
                      </td>
                    </tr>
                    
					
					 
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="mapset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
			   
			   
			   
			   
			   
			   <div style="height:auto; padding-left: 150px;padding-top: 20px; line-height: 30px;" class="blank20">
				<p><b style="color:red;font-size:15px">设置注意事项：</b>
				 
				<p>1、默认不启用。</p>
				<p>2、如果启用，则需申请<span style="color:red;">阿里云产品《语音服务->文本转语音模板》</span>（<a target="_bank" href="https://www.aliyun.com/product/vms" >点击去申请</a>）和配置相关的服务器配置等，（请慎重操作，具体申请、服务器配置等详细请咨询相关客服或售后专员）</p>
 				<p>启用 电话通知商家说明:</p>
 				<p>1、主要针对于订单需商家主动确认制作的（自动接单的不考虑在内）</p>
				<p>2、用户下单后如果商家在N分钟内未确认制作订单，则系统会自动给商家拨打电话提醒商家操作订单(暂时只自动通知一次)</p>
 				 
				 <p>是否开启未制作订单几分钟内自动电话通知说明:</p>
 				<p>1、不启用则用户下单后直接给用户拨打电话提醒</p>
				<p>2、启用后需要具体配置服务器任务等</p>
 				 
				 
				
				
				
				
				</div>
			   
			   
			   
			   
            </form>
          </div>
         
           
         </div>
      
    

          </div> 
 
 <script>
 $('input[name="is_auto_callphone"]').click(function(){
	var checkid =  $('input[name="is_auto_callphone"]:checked').val();
	if(checkid==1){
		$('.imgserverBox').show();
	}else{
		$('.imgserverBox').hide();
	}
 });
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