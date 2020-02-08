<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 21:52:59
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\juanlist.html" */ ?>
<?php /*%%SmartyHeaderCode:325405cd5823bba0e39-69214334%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ee5f73902f7cb9aac756da6520172abbaa76d5c' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\juanlist.html',
      1 => 1537942455,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '325405cd5823bba0e39-69214334',
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
  'unifunc' => 'content_5cd5823bd63387_93923505',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5823bd63387_93923505')) {function content_5cd5823bd63387_93923505($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
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

<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/datepicker/WdatePicker.js" type="text/javascript" language="javascript"></script>

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
   	        	 	   <div class="showtop_t" id="positionname">优惠券列表</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
    <div style="width:auto;overflow-x:hidden;overflow-y:auto">

      	    <div class="search">


            <div class="search_content">

            	 <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/juanlist"),$_smarty_tpl);?>
">

            	  <label>优惠劵限制金额：</label>
            	   <input type="text" name="searchvalue" value="<?php echo $_smarty_tpl->tpl_vars['searchvalue']->value;?>
" style="width:50px;">&nbsp;元
            	   &nbsp;&nbsp;&nbsp;&nbsp;
				   <label>优惠劵状态：</label>
				   <select name="orderstatus">
            	   	  <option value="0">选择优惠劵状态</option>
					  <option value="1" <?php if ($_smarty_tpl->tpl_vars['orderstatus']->value=='1'){?>selected<?php }?>>未使用</option>
					  <option value="2" <?php if ($_smarty_tpl->tpl_vars['orderstatus']->value=='2'){?>selected<?php }?>>已绑定</option>
					  <option value="3" <?php if ($_smarty_tpl->tpl_vars['orderstatus']->value=='3'){?>selected<?php }?>>已使用</option>
					  <option value="4" <?php if ($_smarty_tpl->tpl_vars['orderstatus']->value=='4'){?>selected<?php }?>>已失效</option>
            	  </select>
				  &nbsp;&nbsp;&nbsp;&nbsp;
				   <label>生成时间：</label>
            	  
            	   从<input class="Wdate datefmt" type="text" name="starttime" id="starttime" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['starttime']->value,"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" />
			   		 	  到&nbsp;<input class="Wdate datefmt" type="text" name="endtime" id="endtime" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['endtime']->value,"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" />


            	    <input type="submit" value="提交查询" class="button">
            	 </form>
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

                        <th width="60px"><input type="checkbox" id="chkall" name="chkall" onclick="checkall()"></th>

                        <th align="center">id</th>
						<th align="center">优惠券名称</th>
                         <th align="center">优惠券号</th>
                        <th align="center">优惠券密码</th>
                        <th align="center">最低限制金额</th>
                        <th align="center">优惠劵面值</th>
                         <th align="center">使用者</th>
                         <th align="center">状态</th>
                        <th align="center">操作</th>

                      </tr>

                    </thead>

                     <tbody>
                     
                      <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['juanlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
                      <tr class="s_out" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td align="center" ><input type="checkbox" name="id[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
"></td>
                        <td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
</td>
						 <td align="center"> <?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
 </td>
                        <td align="center"> <?php echo (($tmp = @$_smarty_tpl->tpl_vars['items']->value['card'])===null||$tmp==='' ? '--' : $tmp);?>
 </td>
                        <td align="center" ><?php echo (($tmp = @$_smarty_tpl->tpl_vars['items']->value['card_password'])===null||$tmp==='' ? '--' : $tmp);?>
</td>
                         <td align="center"><?php if ($_smarty_tpl->tpl_vars['items']->value['limitcost']>0){?><?php echo $_smarty_tpl->tpl_vars['items']->value['limitcost'];?>
元<?php }else{ ?>无门槛<?php }?></td>
                        <td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['cost'];?>
元</td>
                        <td align="center"><?php if (empty($_smarty_tpl->tpl_vars['items']->value['username'])){?>未绑定用户<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['items']->value['username'];?>
<?php }?></td>
                        <td align="center">
                        	        <?php if ($_smarty_tpl->tpl_vars['items']->value['status']==0){?><?php if ($_smarty_tpl->tpl_vars['items']->value['endtime']>$_smarty_tpl->tpl_vars['nowtime']->value||empty($_smarty_tpl->tpl_vars['items']->value['endtime'])){?> 未使用<?php }else{ ?>已失效<?php }?><?php }else{ ?>

                        	        <?php echo $_smarty_tpl->tpl_vars['statustype']->value[$_smarty_tpl->tpl_vars['items']->value['status']];?>


                        	        <?php }?>



                         </td>
                        <td align="center">
                        	<a onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/deljuan/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/datatype/json"),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/del.jpg"></a></td>
                      </tr>
                       <?php } ?>

                    </tbody>

                  </table>

                <div class="blank20"></div>


                </form>

                <div class="page_newc">
                 	      <div class="select_page" style='width:500px'><a href="#" onclick="checkword(true);">全选</a>/<a href="#" onclick="checkword(false);">取消</a>
                 	     <a onclick="return remindall(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/deljuan/datatype/json"),$_smarty_tpl);?>
" class="delurl">删除</a>
                 	     <a  onclick="outchoice(this);" href="#"  data="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/outjuan"),$_smarty_tpl);?>
" class="delurl">导出选择项</a>
                 	     <a  href="<?php echo $_smarty_tpl->tpl_vars['outlink']->value;?>
" target="_blank" class="delurl">导出查询结果</a>
						 <a style='color:#0076cf' href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/addjuan"),$_smarty_tpl);?>
"  class="delurl">添加优惠券</a></div>
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