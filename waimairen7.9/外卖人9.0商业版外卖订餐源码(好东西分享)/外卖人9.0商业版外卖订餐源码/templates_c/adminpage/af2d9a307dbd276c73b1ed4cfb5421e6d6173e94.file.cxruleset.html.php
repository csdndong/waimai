<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:25:14
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\cxruleset.html" */ ?>
<?php /*%%SmartyHeaderCode:193475cd55f9ad035d4-07322349%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'af2d9a307dbd276c73b1ed4cfb5421e6d6173e94' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\cxruleset.html',
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
  'nocache_hash' => '193475cd55f9ad035d4-07322349',
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
  'unifunc' => 'content_5cd55f9ae34d52_91698587',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f9ae34d52_91698587')) {function content_5cd55f9ae34d52_91698587($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
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
   	        	 	   <div class="showtop_t" id="positionname">优惠活动设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	

    <div style="width:auto;overflow-x:hidden;overflow-y:auto">
        <div class="search" style='height: 38px; line-height: 38px;'>
            <div class="search_content" style='font-size:12px'>
			注：优惠活动默认支持全部订单和全部平台，商家后台添加优惠活动也将使用此处对应的活动设置，若修改此处设置只对修改后添加的活动有效，修改前添加的活动还按照原有设置。
			</div>
        </div>
 
 
 
          <div class="tags">
          <div id="tagscontent">

            <div id="con_one_1">

              <div class="table_td" style="margin-top:10px;">

              <form method="post" action="" onsubmit="return remind();" id="delform">

                  <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">

                    <thead>

                      <tr>

                        

                        <th align="center">活动标签</th>
                        <th align="center">活动类型</th>
                        <th align="center">支持订单</th>                       
                        <th align="center">支持平台</th>                         
                        <th align="center">操作</th>

                      </tr>

                    </thead>

                     <tbody>
                 <?php echo smarty_function_load_data(array('assign'=>"list",'table'=>"cxruleset",'fileds'=>"*",'where'=>" id >0 ",'limit'=>"5"),$_smarty_tpl);?>
        
                 <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
                        <td align="center"><img style='width: 20px;' <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['items']->value['imgurl'])),$_smarty_tpl);?>
  /></td>
                        <td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</td>
                        <td align="center"><?php if ($_smarty_tpl->tpl_vars['items']->value['supportorder']==1){?>全部<?php }else{ ?>仅在线支付订单有效<?php }?></td>
                        <td align="center">
						<?php if (in_array(1,explode(',',$_smarty_tpl->tpl_vars['items']->value['supportplat']))){?>PC端,<?php }?>
						<?php if (in_array(2,explode(',',$_smarty_tpl->tpl_vars['items']->value['supportplat']))){?>微信端,<?php }?>
						<?php if (in_array(3,explode(',',$_smarty_tpl->tpl_vars['items']->value['supportplat']))){?>web端,<?php }?>
						<?php if (in_array(4,explode(',',$_smarty_tpl->tpl_vars['items']->value['supportplat']))){?>app(安卓,苹果)<?php }?>				
						</td>                       
			            <td align="center">
                            <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/editrule/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])),$_smarty_tpl);?>
"><img style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></a>                             
                        </td>                         
                      </tr>
                  <?php } ?>
                 
                    </tbody>

                  </table>

                <div class="blank20" style="padding-left: 20px;"><font style="color:red;"></font></div>

                </form>
 
                <div class="page_newc">
				
					 
				 
                 	     </div>
                      
                 </div>
  <div class="page_newc" style='margin-left: 18px;height: 30px;line-height: 20px;'>
			  
					<div class="select_page" style="color:red;width:800px;">
					
						  </div>
				 
                 	     </div>
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