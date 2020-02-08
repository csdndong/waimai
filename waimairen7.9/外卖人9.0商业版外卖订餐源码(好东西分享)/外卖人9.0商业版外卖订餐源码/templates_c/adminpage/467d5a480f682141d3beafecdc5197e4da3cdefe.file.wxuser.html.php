<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:17:24
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\weixin\wxuser.html" */ ?>
<?php /*%%SmartyHeaderCode:26435cd63ec42f7159-98982931%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '467d5a480f682141d3beafecdc5197e4da3cdefe' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\weixin\\wxuser.html',
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
  'nocache_hash' => '26435cd63ec42f7159-98982931',
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
  'unifunc' => 'content_5cd63ec43c4cf0_90363789',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63ec43c4cf0_90363789')) {function content_5cd63ec43c4cf0_90363789($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">微信用户列表</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
   <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
      	
      	 
      	
      	
           <div class="tags">

      	  

          <div id="tagscontent">

            <div id="con_one_1">

              <div class="table_td" style="margin-top:10px;">

              <form method="post" action="" onsubmit="return remind();">

                  <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">

                    <thead>

                      <tr>

                     
                        <th align="center">微信号</th>
                         <th align="center">绑定用户名</th>
                        <th align="center">帐号信息</th> 
                        <th align="center">发送信息</th> 
                        
                      

                      </tr>

                    </thead> 

                     <tbody>

                      <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?> 
                      <tr class="s_out" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff"> 
                       
                        <td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['openid'];?>
</td> 
                        <td align="center"><?php if ($_smarty_tpl->tpl_vars['items']->value['is_bang']>0){?> <?php echo $_smarty_tpl->tpl_vars['items']->value['username'];?>
 <?php }else{ ?>未绑定<?php }?></td> 
                        <td align="center"><a href="javascript:void(0);" onclick="showuser('<?php echo $_smarty_tpl->tpl_vars['items']->value['openid'];?>
',this);">查看微信帐号信息</a></td>  
                       <td align="center"><a href="javascript:void(0);" onclick="sendwxtouser('<?php echo $_smarty_tpl->tpl_vars['items']->value['openid'];?>
');">发送客服信息</a></td>  
                       
                      </tr> 
                       <?php } ?> 

                    </tbody> 

                  </table>

                <div class="blank20"></div>

             

                </form>

                <div class="page_newc">
                 	     
                       <div class="show_page"><ul><?php echo $_smarty_tpl->tpl_vars['pagecontent']->value;?>
</ul></div>
                 </div>

               <div class="blank20"></div>

              </div>

            </div>

          </div>

        </div>

        
  </div>
 
</div> 
<script>
	  function showuser(openid,obj){
	  	$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/getoneuser/datatype/json"),$_smarty_tpl);?>
', {'openid':openid},function (data, textStatus){  
			     if(data.error == false){
     	     	   if(data.msg.subscribe == 1){
     	     	   	   $(obj).parent().text('呢称:'+data.msg.nickname+',所在地'+data.msg.province+data.msg.city);
     	     	   }else{
     	     	     $(obj).parent().text('未关注我们');
     	     	   }
     	     	    
     	     }else{
     	     	  if(data.error == true)
     	     	  {
     	     	    	diaerror(data.msg); 
     	       	}else{
     	     		   diaerror(data); 
     	     	  }
     	     } 
	    }, 'json'); 
	  }
	  
	  function sendwxtouser(openid){
   var	htmls = '<form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/sendwxmsg/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform(\'\',this);"><table>';
	htmls += '<tbody><tr>';
	htmls += '<td height="50px">微信内容:</td>';
	htmls += '<td> <textarea name="content"></textarea></td></tr>';
	htmls += '</tbody></table> ';
  htmls += '<input type="hidden" value="'+openid+'" name="openid"> ';
	htmls += '<input type="submit" value="确认提交" class="button" id="dosetclosetime" ></form>';
  art.dialog({
    id: 'testID3',
    title:'发送客服消息',
    content: htmls
  });
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