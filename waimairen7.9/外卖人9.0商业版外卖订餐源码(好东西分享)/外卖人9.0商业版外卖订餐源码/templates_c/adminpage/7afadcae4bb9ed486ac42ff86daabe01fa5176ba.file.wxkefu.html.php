<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:17:25
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\weixin\wxkefu.html" */ ?>
<?php /*%%SmartyHeaderCode:99185cd63ec5a1fac4-60845207%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7afadcae4bb9ed486ac42ff86daabe01fa5176ba' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\weixin\\wxkefu.html',
      1 => 1536024618,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '99185cd63ec5a1fac4-60845207',
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
  'unifunc' => 'content_5cd63ec5b3d017_56219210',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63ec5b3d017_56219210')) {function content_5cd63ec5b3d017_56219210($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">微信客服设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
	 <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
         
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/savewxkefu/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
                  	 
					 
					 <?php if (!empty($_smarty_tpl->tpl_vars['station']->value)){?>
					 
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">微信客服设置</td>
                        <td><input type="radio" name="wxkefu_open"   value="1"  <?php if ($_smarty_tpl->tpl_vars['station']->value['wxkefu_open']==1){?>checked<?php }?>>开启
                            <input type="radio" name="wxkefu_open"   value="0"  <?php if ($_smarty_tpl->tpl_vars['station']->value['wxkefu_open']==0){?>checked<?php }?>>关闭
						</td>
                     </tr>
					
					 
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">个人二维码</td>
                        <td>
                            <input type="hidden" name="wxkefu_ewm" id="wxkefu_ewm" value="<?php echo $_smarty_tpl->tpl_vars['station']->value['wxkefu_ewm'];?>
" class="skey" style="width:100px;">
                            <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['station']->value['wxkefu_ewm'])),$_smarty_tpl);?>
  width=150px height=150px id="imgwxkefu_ewm" <?php if (empty($_smarty_tpl->tpl_vars['station']->value['wxkefu_ewm'])){?> style="display:none;"<?php }?>>
                            <span onclick="uploadw();">上传图片</span>
                        </td>
                    </tr>

					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">微信客服logo</td>
                        <td>
                            <input type="hidden" name="wxkefu_logo" id="wxkefu_logo" value="<?php echo $_smarty_tpl->tpl_vars['station']->value['wxkefu_logo'];?>
" class="skey" style="width:100px;">
                            <img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['station']->value['wxkefu_logo'])),$_smarty_tpl);?>
    width=40px height=40px id="imgwxkefu_logo" <?php if (empty($_smarty_tpl->tpl_vars['station']->value['wxkefu_logo'])){?> style="display:none;"<?php }?>>
                            <span onclick="uploadwx();">上传图片</span>
                        </td>
                    </tr>

					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">电话</td>
                      <td><input type="text" name="wxkefu_phone" id="wxkefu_phone" value="<?php echo $_smarty_tpl->tpl_vars['station']->value['wxkefu_phone'];?>
" class="skey" style="width:250px;"></td>
                    </tr>
					
					<?php }else{ ?>
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">请在分站后台设置</td>
                     </tr> 
					
					 <?php }?>
                   
                    
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
	var dialogs ;
 function uploadw(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsuceswebc"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsuceswebc(flag,obj,linkurl){
        if(flag == true){
            
            dialogs.close();
            uploadw();
        }else{
            
            dialogs.close();
            $('#wxkefu_ewm').val(linkurl);
            $('#imgwxkefu_ewm').attr('src',linkurl);
            $('#imgwxkefu_ewm').show();
        }
    }
function uploadwx(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsuceswx"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsuceswx(flag,obj,linkurl){
        if(flag == true){
            
            dialogs.close();
            uploadwx();
        }else{
            
            dialogs.close();
            $('#wxkefu_logo').val(linkurl);
            $('#imgwxkefu_logo').attr('src',linkurl);
            $('#imgwxkefu_logo').show();
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