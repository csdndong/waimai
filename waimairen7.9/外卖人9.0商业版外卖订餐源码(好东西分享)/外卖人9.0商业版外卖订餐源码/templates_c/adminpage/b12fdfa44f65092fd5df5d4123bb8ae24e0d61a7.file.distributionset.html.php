<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:25:18
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\distributionset.html" */ ?>
<?php /*%%SmartyHeaderCode:97315cd55f9e6a1f73-55888455%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b12fdfa44f65092fd5df5d4123bb8ae24e0d61a7' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\distributionset.html',
      1 => 1536024619,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97315cd55f9e6a1f73-55888455',
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
  'unifunc' => 'content_5cd55f9e78d465_81128131',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f9e78d465_81128131')) {function content_5cd55f9e78d465_81128131($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/js/kindeditor/kindeditor.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/ajaxfileupload.js"> </script>

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
   	        	 	   <div class="showtop_t" id="positionname">分销设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
<script>
$(function(){   
    cck();
	$('input[name="is_open_distribution"]').live('click',function(){
		cck();		 
	})

	$('input[name="distribution_grade"]').live('click',function(){
		cck();
	})
function cck(){
    var is_open_distribution = $('input[name="is_open_distribution"]:checked').val()	     
	if(is_open_distribution == 1){
		$('.disset').show();
	}else{	    
		$('.disset').hide(); 
	}
	var distribution_grade = $('input[name="distribution_grade"]:checked').val()	     
	if(is_open_distribution == 1){
		if(distribution_grade == 1){
			$('.grade1').show();
			$('.grade2').hide();
			$('.grade3').hide();
		}else if(distribution_grade == 2){
			$('.grade1').show();
			$('.grade2').show();
			$('.grade3').hide(); 
		}else{
			$('.grade1').show();
			$('.grade2').show();
			$('.grade3').show();
		}
	}
}	
	
	
})
</script>	
	   <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/savedistributionset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
				    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">是否开启：</td>
                        <td><input type="radio" name="is_open_distribution"   value="1"  <?php if ($_smarty_tpl->tpl_vars['is_open_distribution']->value==1){?>checked<?php }?>>&nbsp;开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="is_open_distribution"   value="0"  <?php if ($_smarty_tpl->tpl_vars['is_open_distribution']->value==0){?>checked<?php }?>>&nbsp;关闭</td>
                     </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
						<td class="left">手续费率：</td>
						<td>用户提现分销佣金时，需收取提现金额的<input type="input" name="fxfeelv" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['fxfeelv']->value)===null||$tmp==='' ? 0 : $tmp);?>
"  class="skey" style="width:50px;">%的手续费&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style='color:#999'>(录入0表示不收取手续费，提现到账户余额时不收取手续费) </span></td>
					</tr>  
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
						<td class="left">最低提现金额：</td>
						<td>用户提现分销佣金时，最低提现<input type="input" name="minfxtxcost" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['minfxtxcost']->value)===null||$tmp==='' ? 0 : $tmp);?>
"  class="skey" style="width:50px;">元&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style='color:#999'>(录入0表示不限制) </span></td>
					</tr>  
					<tr class='disset' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
						<td class="left">分销等级：</td>
						<td>
							<input type="radio" name="distribution_grade"   value="1"  <?php if ($_smarty_tpl->tpl_vars['distribution_grade']->value==1){?>checked<?php }?>>&nbsp;一级
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio" name="distribution_grade"   value="2"  <?php if ($_smarty_tpl->tpl_vars['distribution_grade']->value==2){?>checked<?php }?>>&nbsp;二级
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio" name="distribution_grade"   value="3"  <?php if ($_smarty_tpl->tpl_vars['distribution_grade']->value==3){?>checked<?php }?>>&nbsp;三级
						</td>
					</tr>
					<tr class='grade1 disset'  onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
						<td class="left">一级佣金比例：</td>
						<td><input type="input" name="distribution_yj1" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['distribution_yj1']->value)===null||$tmp==='' ? 0 : $tmp);?>
"  class="skey" style="width:50px;">% </td>
					</tr> 
					<tr class='grade2 disset'  onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
						<td class="left">二级佣金比例：</td>
						<td><input type="input" name="distribution_yj2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['distribution_yj2']->value)===null||$tmp==='' ? 0 : $tmp);?>
"  class="skey" style="width:50px;">% </td>
					</tr>
					<tr class='grade3 disset'  onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
						<td class="left">三级佣金比例：</td>
						<td><input type="input" name="distribution_yj3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['distribution_yj3']->value)===null||$tmp==='' ? 0 : $tmp);?>
"  class="skey" style="width:50px;">% </td>
					</tr>
					 
					 
                     		
                  </tbody>
				  
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="savedistributionset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
        </div> 
    </div>  
 
   	        	 	
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