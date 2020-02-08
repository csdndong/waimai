<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:14:15
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\system\footinfo.html" */ ?>
<?php /*%%SmartyHeaderCode:277445cd63e07017853-35342112%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8be1681f3503b061a1858032105be14fc46b5c34' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\system\\footinfo.html',
      1 => 1537876910,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '277445cd63e07017853-35342112',
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
  'unifunc' => 'content_5cd63e0712eea9_02501268',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63e0712eea9_02501268')) {function content_5cd63e0712eea9_02501268($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">底部导航</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
   	        	 	
      <div style="width:auto;overflow-x:hidden;overflow-y:auto;margin-top:10px;"> 
          <div class="tags">

      	 
          <div id="tagscontent">

            <div id="con_one_1">

              <div class="table_td"> 
              <form method="post" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/savefootinfo/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/footinfo"),$_smarty_tpl);?>
',this);">

                  <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">

                    <thead>

                      <tr>

                         
                        <th align="center">导航名称</th>
                        <th align="center">连接</th>
                        <th align="center">排序</th>
                        <th align="center">操作</th>
                      </tr>

                    </thead>

                    <tbody id="type">    
                    	  	  <?php if (!empty($_smarty_tpl->tpl_vars['toplink']->value)){?>
	 	                     <?php $_smarty_tpl->tpl_vars['toplink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['toplink']->value), null, 0);?>
                       <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['toplink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>	

                                <tr class="s_out" onmouseover="this.bgColor='#F5F5F5';"  <?php if ($_smarty_tpl->tpl_vars['myid']->value%2==0){?>	
                         onmouseout="this.bgColor='f4f4f4';" bgcolor="#f4f4f4"<?php }else{ ?>
                          onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff"
                         <?php }?>>
                        
                        <td align="center" style="padding-left:10px;">
                          <input type="text" name="typename[]" id="typename[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value['typename'];?>
"  class='skey' style='width:130px;'/>  
                        </td>
                        <td align="center" style="padding-left:10px;">
                        <input type="text" name="typeurl[]" id="typeurl[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
"  class='skey' style='width:250px;'/> 
                        </td>
                        <td align="center" style="padding-left:10px;">
                          <input type="text" name="typeorder[]" id="typeorder[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeorder'];?>
"  class='skey' style='width:50px;'/>   
                        </td>
                        <td align="center"><a onclick="$(this).parent().parent().remove();" href="javascript:;"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/del.jpg"></a></a></td>
                      </tr>
                    <?php } ?>  
                    <?php }?> 

                    </tbody>      



                  </table>

                <div class="blank20"></div>

               <div style='text-align:center'>  

               <input type="submit" name="button" id="button" value="提交保存" class='button'> 

                 <input type="button" name="button" id="typebutton" value="添加一个分类" onclick="adddaohang();" class="button" />
                </div>
              </form>
 <div class="blank20"></div>
              </div>

            </div>

          </div>

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