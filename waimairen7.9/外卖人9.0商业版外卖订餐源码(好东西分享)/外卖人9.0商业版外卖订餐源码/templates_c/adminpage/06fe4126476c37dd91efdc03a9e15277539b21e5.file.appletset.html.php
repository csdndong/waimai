<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:17:28
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\applet\appletset.html" */ ?>
<?php /*%%SmartyHeaderCode:52805cd63ec8d0ac90-35719587%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '06fe4126476c37dd91efdc03a9e15277539b21e5' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\applet\\appletset.html',
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
  'nocache_hash' => '52805cd63ec8d0ac90-35719587',
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
  'unifunc' => 'content_5cd63ec8e334c8_80237128',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63ec8e334c8_80237128')) {function content_5cd63ec8e334c8_80237128($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">小程序设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
   
      <div style="width:auto;overflow-x:hidden;overflow-y:auto;">   
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/applet/module/appletsave/datatype/json"),$_smarty_tpl);?>
" onsubmit="return newsubformdata('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">小程序appid</td>
                      <td><input type="text" name="appletAppID" id="appletAppID" value="<?php echo $_smarty_tpl->tpl_vars['appletAppID']->value;?>
" class="skey" style="width:500px;"></td>
                    </tr>
                    
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">小程序secret</td>
                      <td><input type="text" name="appletsecret" id="appletsecret" value="<?php echo $_smarty_tpl->tpl_vars['appletsecret']->value;?>
" class="skey" style="width:500px;"></td>
                    </tr>
                     <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">小程序高德KEY值</td>
                      <td><input type="text" name="appletmapkey" id="appletmapkey" value="<?php echo $_smarty_tpl->tpl_vars['appletmapkey']->value;?>
" class="skey" style="width:500px;"></td>
                    </tr> 
					
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">开启小程序审核</td>
                      <td>  
						<input type="radio" name="is_pass_applet"  value="1" <?php if ($_smarty_tpl->tpl_vars['is_pass_applet']->value==1){?>checked<?php }?>>不开启
						<input type="radio" name="is_pass_applet"  value="0" <?php if ($_smarty_tpl->tpl_vars['is_pass_applet']->value==0){?>checked<?php }?> >开启
						<br />(开启后小程序首页将显示下方编辑器中编辑的内容，请认真填写在编辑器中的详细内容，方便提交小程序审核时候选择的 服务类目 与首页内容相符合！！！ ) 
					  </td>
                    </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff" id="appleturlcontent">
                      <td class="left">首页显示内容</td>
                      <td>
					  <?php echo smarty_function_load_data(array('assign'=>"single",'table'=>"single",'type'=>"one",'fileds'=>"content",'where'=>"code='applet' "),$_smarty_tpl);?>

                      <div class='nop'><script>KE.show({id:'content',allowFileManager : true,imageUploadJson:'<?php echo FUNC_function(array('type'=>'url','link'=>"/other/saveupload"),$_smarty_tpl);?>
',fileManagerJson:'<?php echo FUNC_function(array('type'=>'url','link'=>"/other/saveupload"),$_smarty_tpl);?>
',items:['source','|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', '|', 'fontname', 'fontsize', '|', 'textcolor', 'bgcolor', 'bold','italic', 'underline', 'removeformat', '|', 'image', 'advtable', 'hr','link', 'unlink']});</script><textarea name="content" id="content" style='width:510px; height:560px;'><?php echo $_smarty_tpl->tpl_vars['single']->value['content'];?>
</textarea></div>                      </td>
                    </tr>
					
                    
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
             
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
         
           
         </div>
      
    

          </div>
<script>
function newsubformdata(newurl,obj)
{
 document.getElementById("content").value=KE.util.getData('content');
 
 
	$('#cat_zhe').toggle();
	$('#cat_tj').toggle();
	var url = $(obj).attr('action'); 
	$.ajax({
     type: 'post',
     async:true,
     data:$(obj).serialize(),
     url: url , 
     dataType: 'json',success: function(content) {   
     	$('#cat_zhe').toggle();
	      $('#cat_tj').toggle();
     	if(content.error == false){
     		 
     		diasucces(content.msg,newurl);
     	}else{
     		
     		if(content.error == true)
     		{
     			diaerror(content.msg); 
     		}else{
     			diaerror(content); 
     		}
     	} 
		},
    error: function(content) { 
    	$('#cat_zhe').toggle();
	      $('#cat_tj').toggle();
    	diaerror('数据'); 
	  }
   });   
   
	return false;
}
	$(function(){
		<?php if ($_smarty_tpl->tpl_vars['is_pass_applet']->value==1){?>
			$('#appleturlcontent').hide();
		<?php }?>
		 $('input[type=radio][name=is_pass_applet]').change(function(){
			if($(this).val()==1){
				$('#appleturlcontent').hide();
			}else{
				$('#appleturlcontent').show();
			}
		 })
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