<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:25:58
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\system\toplink.html" */ ?>
<?php /*%%SmartyHeaderCode:277085cd55fc685e8c1-73666798%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ac4bc8282782dde905ca85e8290cc05105287424' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\system\\toplink.html',
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
  'nocache_hash' => '277085cd55fc685e8c1-73666798',
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
  'unifunc' => 'content_5cd55fc6926b27_66865828',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55fc6926b27_66865828')) {function content_5cd55fc6926b27_66865828($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">网站导航</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
   	        	 	
   	        	 	
      <div style="width:auto;margin-top:10px; "> 
          
          <div id="tagscontent">

            <div id="con_one_1">

              <div class="table_td" > 
              <form method="post" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/savetoplink/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/toplink"),$_smarty_tpl);?>
',this);">

                  <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">

                    <thead>

                      <tr>                        
                        <th align="center">导航名称</th>
                        <th align="center">链接</th>
                        <th align="center">排序</th>
                        <th align="center">操作</th>
                      </tr>

                    </thead>

                    <tbody id="type">    
                    	  	<?php if (!empty($_smarty_tpl->tpl_vars['footlink']->value)){?>
	 	                 	<?php $_smarty_tpl->tpl_vars['footlink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['footlink']->value), null, 0);?>
                       <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['footlink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>	 
                        <tr class="s_out" onmouseover="this.bgColor='#fdfbfb';" 
                         <?php if ($_smarty_tpl->tpl_vars['myid']->value%2==0){?>	
                         onmouseout="this.bgColor='f4f4f4';" bgcolor="#f4f4f4"<?php }else{ ?>
                          onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff"
                         <?php }?>
                        	
                        	>
                        
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
                        <td align="center"> <a onclick="$(this).parent().parent().remove();" href="javascript:;"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/del.jpg"></a></td>
                      </tr>
                    <?php } ?>  
                    <?php }?>

                    </tbody>      



                  </table>

                <div class="blank20">
				 
				</div>
                 <div style='width:100%;text-align:center'>
                 <input type="hidden" name="stopoutenable" id="stopoutenable" value="07a4c77a48ab3d859107568f7a1e1e68"/>

               <input type="submit" name="button" id="button" value="提交保存" class='button' style='margin-left: 50px;'> 

                 <input type="button" name="button" id="typebutton" value="添加一个分类" onclick="adddaohang();" class="button" />
                </div>
              </form>
              <div class="blank20">			
			  </div>
              </div>
			  <div class='xx' style='padding-bottom: 20px;width:100%;text-align:center'>
			   <p style=' text-align:center;'>常用导航链接</p>
				  <div style='display:inline-block'>	 
					  <p>首页：/index.php?ctrl=site&action=index</p>
					  <p>超市：/index.php?ctrl=market&action=index</p>
					  <p>跑腿：/index.php?ctrl=site&action=paotui</p>
				  </div>
				  <div style='display:inline-block;margin-left: 30px;'>
					  <p>礼品兑换：/index.php?ctrl=gift&action=index</p>
					  <p>评价留言：/index.php?ctrl=ask&action=index</p>
					  <p>会员中心：/index.php?ctrl=member&action=base</p>
				  </div>			  
			  </div>
			  <style>
			  .xx p{
			     text-align:left;
			  }
			  </style>
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