<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 21:58:46
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\virtualinfo.html" */ ?>
<?php /*%%SmartyHeaderCode:20455cd583963d7d32-53575301%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd890165d46275617cb52d41a2da35747c618c170' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\virtualinfo.html',
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
  'nocache_hash' => '20455cd583963d7d32-53575301',
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
  'unifunc' => 'content_5cd5839653a328_04778958',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5839653a328_04778958')) {function content_5cd5839653a328_04778958($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">所有店铺展示列表</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	

      <div style="width:auto;overflow-x:hidden;overflow-y:auto">  
      	<div class="search" style='height: 50px;line-height: 25px;'>
            <div class="search_content">
			 <a style="color#FF6600;font-size:12px" href="jvascript:void(0);"  >
			温馨提示：①增：增加店铺虚拟总销量(展示的总销量=虚拟店铺总销量+实际店铺总销量)
			<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			②查：查看店铺的所有商品，可以增加商品的虚拟评论以及商品的总销量
			</a> 
			 
			 </div>              
      	</div> 
      	<div class="search">
            <div class="search_content">	 
            	 <form method="get" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/virtualinfo"),$_smarty_tpl);?>
">
            	   <input type="hidden" name="ctrl" value="adminpage">
            	   <input type="hidden" name="action" value="card">
             	   <input type="hidden" name="module" value="virtualinfo">
            	   <label>店铺名</label>
            	   <input type="text" name="shopname" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['shopname']->value)===null||$tmp==='' ? '' : $tmp);?>
"> 
				   
             	   <label>用户名：</label>
            	   <input type="text" name="username" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['username']->value)===null||$tmp==='' ? '' : $tmp);?>
">                 
                  <label>手机：</label>
            	   <input type="text" name="phone" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['phone']->value)===null||$tmp==='' ? '' : $tmp);?>
">   
					<label>店铺类型：</label>				   
					<select name="shop_type" >
						<option <?php if ($_smarty_tpl->tpl_vars['shop_type']->value==0){?>selected<?php }?> value="0">请选择店铺类型</option>
						<option <?php if ($_smarty_tpl->tpl_vars['shop_type']->value==1){?>selected<?php }?> value="1">外卖</option>
						<option <?php if ($_smarty_tpl->tpl_vars['shop_type']->value==2){?>selected<?php }?> value="2">超市</option>
					</select>
            	    <input type="submit" value="提交查询" class="button">  
            	 </form>
            </div>       
        
      	</div>
      	<style>
		.selectobjlist{height:auto!Important;padding:10px; border:none!Important;}
		.selectobjlist li{ margin-right:0px!Important; width:33%!Important; text-align:left; float:left; height:auto;  }
		.selectobjlist font{cursor:pointer;}
		</style>
      	
           <div class="tags">
          <div id="tagscontent">
            <div id="con_one_1">
              <div class="table_td" style="margin-top:10px;">
              <form method="post" action="" onsubmit="return remind();"  id="delform">
                    <ul class="selectobjlist">
                       <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['selectlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?> 
                          <li>  
  					  <font color=red><?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
</font>[<font color=#0086ae><?php echo $_smarty_tpl->tpl_vars['shoptype']->value[$_smarty_tpl->tpl_vars['items']->value['shoptype']];?>
</font>]
  					 &nbsp;  <font  onclick="addshopsellcount('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo (($tmp = @$_smarty_tpl->tpl_vars['items']->value['virtualsellcounts'])===null||$tmp==='' ? 0 : $tmp);?>
');"  color=red style="font-weight:bold; font-size:16px;">增</font>&nbsp;&nbsp;&nbsp;
					
					<a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/virtualgoods/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])),$_smarty_tpl);?>
" >
					<font color=#0086ae style="font-weight:bold; font-size:16px;">查</font>
					 </a> 
					  
					  </li> 
 					  
                        <?php } ?> 

					</ul>
                <div class="blank20"></div>
                </form>
                <div class="page_newc">
                        <div class="show_page"><ul>
						<?php echo $_smarty_tpl->tpl_vars['pagecontent']->value;?>
 
						</ul></div>
                 </div>
<div class="page_newc">
						
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
<script>
var dialogs ;
function addshopsellcount(shopid,shopsellcount){
var	htmls = '<form method="post" id="doshwoform" action="#" style="text-align:center;"><table>';
	htmls += '<tbody><tr>';
	htmls += '<td height="50px">虚拟总销量:</td>';
	htmls += '<td> <input type="text" name="savesellcounts" value="'+shopsellcount+'" style="width:100px;"></td></tr>';
	htmls += '</tbody></table> ';
  htmls += '<input type="hidden" value="'+shopid+'" name="shopid"> ';
	htmls += '<input type="button" value="确认提交" class="button" id="saveshopsellcounts" ></form>';
  art.dialog({
    id: 'testID3',
    title:'增加店铺虚拟总销量',
    content: htmls
  });
}

$('#saveshopsellcounts').live('click',function(){ 
	$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/saveshopsellcount/datatype/json"),$_smarty_tpl);?>
', $('#doshwoform').serialize() ,function (data, textStatus){  
			if(data.error == false){
     		diasucces(data.msg,'');
     	}else{
     		if(data.error == true)
     		{
     			diaerror(data.msg); 
     		}else{
     			diaerror(data); 
     		}
     	} 
	 }, 'json'); 
});

	</script>

<!--newmain 结束-->

   	        	 	
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