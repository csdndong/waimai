<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 21:52:12
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\specialpage.html" */ ?>
<?php /*%%SmartyHeaderCode:257555cd5820c4f7348-53394751%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f20bf1357671304c43ded5038ee720a91bf9139' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\specialpage.html',
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
  'nocache_hash' => '257555cd5820c4f7348-53394751',
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
  'unifunc' => 'content_5cd5820c6942a8_76438314',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5820c6942a8_76438314')) {function content_5cd5820c6942a8_76438314($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
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
   	        	 	   <div class="showtop_t" id="positionname">专题页列表 </div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
 <style>
     #addzty{
         float:left;
         display:block;
         color:#fff;
         background-color: #169BD5;
         width:86px;
         height:28px;
         border-radius:5px;
         line-height:28px;
         text-align:center;
         margin:7px 15px;
     }
     #search{
         float:right;
         margin:7px 15px;
     }
     #search input{
         height:28px;
     }
 </style>
          <div style="width:auto;overflow-x:hidden;overflow-y:auto">
           <div class="tags">
          <div id="tagscontent">
            <div id="con_one_1">
              <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/addspecialpage"),$_smarty_tpl);?>
" id="addzty">添加专题页</a>
                <div id="search">
                    <form method="get" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/specialpage"),$_smarty_tpl);?>
">
                        <input type="hidden" name="ctrl" value="adminpage">
                        <input type="hidden" name="action" value="card">
                        <input type="hidden" name="module" value="specialpage">
                         <input type="text" name="name" placeholder="专题页活动名称" value="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" style="border-radius: 5px;border:1px solid #ccc;">
                         <input type="submit" value="查询" style="background: #169BD5;color:#fff;border:1px solid #169BD5;width:50px; border-radius:5px;cursor:pointer;">
                    </form>
                </div>
            </div>
              <div class="table_td" style="margin-top:10px;">
                  <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                    <thead>
                      <tr>
                        <th align="center">专题名称</th>
                        <th align="center">专题类型</th>
                        <th align="center">添加时间</th>
                        <th align="center">状态</th>
                        <th align="center">操作</th>
                      </tr>
                    </thead>
                     <tbody>
                       <?php echo smarty_function_load_data(array('assign'=>"list",'table'=>"specialpage",'showpage'=>"true",'where'=>" id > 0  and ( cityid = '".((string)$_smarty_tpl->tpl_vars['default_cityid']->value)."' or cityid = 0 )  ".((string)$_smarty_tpl->tpl_vars['where']->value),'orderby'=>"orderid  asc"),$_smarty_tpl);?>

                        <?php if (!empty($_smarty_tpl->tpl_vars['list']->value['list'])){?>
                            <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
							 <?php echo smarty_function_load_data(array('assign'=>"ztyinfo",'type'=>"one",'table'=>"ztyimginfo",'where'=>" ztyid = ".((string)$_smarty_tpl->tpl_vars['items']->value['id'])." "),$_smarty_tpl);?>

                             <td align="center"> <?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
 </td>
                             <td align="center">
                                <?php if ($_smarty_tpl->tpl_vars['items']->value['zttype']==1){?>
                                 店铺
                                 <?php }elseif($_smarty_tpl->tpl_vars['items']->value['zttype']==2){?>
                                 商品
                                 <?php }elseif($_smarty_tpl->tpl_vars['items']->value['zttype']==3){?>
                                 店铺分类
                                 <?php }elseif($_smarty_tpl->tpl_vars['items']->value['zttype']==4){?>
                                 活动
                                 <?php }elseif($_smarty_tpl->tpl_vars['items']->value['zttype']==5){?>
                                 其他
                                 <?php }else{ ?>
                                 自定义链接
                                 <?php }?>
                             </td>
                            <td align="center"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['items']->value['addtime'],"%Y-%m-%d %H:%M:%S");?>
</td>
                            <td align="center"><?php if (!empty($_smarty_tpl->tpl_vars['ztyinfo']->value)){?><?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['is_show']==1){?><font color=#ff6000>使用中</font><?php }else{ ?>未使用<?php }?><?php }else{ ?>未使用<?php }?></td>
                             <td align="center">
                                <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/addspecialpage/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])),$_smarty_tpl);?>
" ><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></a>
                                <a onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/delspecialpage/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/datatype/json"),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/del.jpg"></a></td>
                          </tr>
                           <?php } ?>
                        <?php }?>
                    </tbody>
                  </table>
                <div class="blank20"></div>
                <div class="page_newc">
                 	      <!--<div class="select_page"><a href="#" onclick="checkword(true);">全选</a>/<a href="#" onclick="checkword(false);">取消</a> -->
						  <!--<a onclick="return remindall(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/delspecialpage/datatype/json"),$_smarty_tpl);?>
" class="delurl">删除</a> -->
						  <!--<a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/addspecialpage"),$_smarty_tpl);?>
" class="delurl">添加专题页</a></div>-->
                       <div class="show_page" style="width:580px;"><ul><?php echo $_smarty_tpl->tpl_vars['list']->value['pagecontent'];?>
</ul></div>
                 </div>
               <div class="blank20"></div>
              </div>
            </div>
          </div>
        </div>
  </div>
</div>

<div id="xianshiimg" style="display:none;">
<img  src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/m7/public/images/goodmodule1.png" />
</div>
<style>
.showimg{ cursor:pointer;}
#xianshiimg{ position:absolute; top:50%; left:50%;  z-index:9999999;}
</style>
<script>
		$(".showimg").hover(function(){
					 var showimg = $(this).attr('dtsrc');
					 $("#xianshiimg").show();
					 $("#xianshiimg img").attr('src',showimg);
					 var imgwidth = $('#xianshiimg img').width();
					 var imgheight = $('#xianshiimg img').height();
					 $("#xianshiimg img").css('margin-left',-imgwidth/2);
					 $("#xianshiimg img").css('margin-top',-imgheight/2); 
 				},function(){
					$("#xianshiimg img").attr('src','');
					 $("#xianshiimg").hide();
					 
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