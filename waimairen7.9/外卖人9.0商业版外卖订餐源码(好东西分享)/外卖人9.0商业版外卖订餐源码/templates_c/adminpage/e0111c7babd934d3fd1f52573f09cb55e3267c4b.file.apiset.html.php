<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 20:15:10
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\other\apiset.html" */ ?>
<?php /*%%SmartyHeaderCode:288235cd56b4e5b7112-58271792%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e0111c7babd934d3fd1f52573f09cb55e3267c4b' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\other\\apiset.html',
      1 => 1536024617,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '288235cd56b4e5b7112-58271792',
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
  'unifunc' => 'content_5cd56b4e71e8d8_89544959',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd56b4e71e8d8_89544959')) {function content_5cd56b4e71e8d8_89544959($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">接口管理</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
	
	  <div style="width:auto;overflow-x:hidden;overflow-y:auto;">   
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/saveapiset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
             
					
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <th  colspan="3"   style="font-weight:bolder">配送宝接口设置</th>
                       
                    </tr>
                     <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">启用配送宝</td>
                      <td>  
						<input type="radio" name="psbopen"  value="2" checked>不启用
						<input type="radio" name="psbopen"  value="1" <?php if (isset($_smarty_tpl->tpl_vars['psbopen']->value)&&$_smarty_tpl->tpl_vars['psbopen']->value==1){?>checked<?php }?> >启用
						&nbsp;&nbsp;&nbsp;(启用后才能为商家设置配送宝对接) 
					  </td>
                    </tr>  
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="pspsb1">
                      <td class="left">自动对接配送宝域名</td>
                      <td>
                       <input type="text" name="autopsblink" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['autopsblink']->value)===null||$tmp==='' ? '' : $tmp);?>
" style="width:200px;">
                      </td>
                    </tr>  
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="pspsb1">
                      <td class="left">自动对接配送宝key</td>
                      <td>
                       <input type="text" name="autopsbkey" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['autopsbkey']->value)===null||$tmp==='' ? '' : $tmp);?>
"  style="width:200px;">
                      </td>
                    </tr>
				   <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="pspsb1">
                      <td class="left">配送宝接单失败通知账号</td>
                      <td>
                       <input type="text" name="managephone" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['managephone']->value)===null||$tmp==='' ? '' : $tmp);?>
"  style="width:200px;">
                      </td>
                    </tr>
				 
				 

				 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                     <th  colspan="3"   style="font-weight:bolder">跑腿接口设置</th>
                    </tr>
					
					
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">是否对接跑腿到配送宝</td>
                      <td>  
						<input type="radio" name="pttopsb"  value="2" checked onclick="showpsb();">否
						<input type="radio" name="pttopsb"  value="1" <?php if (isset($_smarty_tpl->tpl_vars['psinfo']->value['pttopsb'])&&$_smarty_tpl->tpl_vars['psinfo']->value['pttopsb']==1){?>checked<?php }?> onclick="showpsb();" >是
						&nbsp;&nbsp;&nbsp;(启用后才能为将跑腿订单对接到配送宝和商家对接无关联) 
					  </td>
                    </tr> 
					
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="pspsb">
                      <td class="left">跑腿配送宝对接链接</td>
                      <td>
                       <input type="text" name="ptpsblink" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['ptpsblink'])===null||$tmp==='' ? '' : $tmp);?>
" style="width:200px;">
                      </td>
                    </tr>  
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="pspsb">
                      <td class="left">跑腿对接订单归属id</td>
                      <td>
                       <input type="text" name="ptpsbaccid" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['ptpsbaccid'])===null||$tmp==='' ? '' : $tmp);?>
"  style="width:50px;">
                      </td>
                    </tr> 
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="pspsb">
                      <td class="left">跑腿对接账号key</td>
                      <td>
                       <input type="text" name="ptpsbkey" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['ptpsbkey'])===null||$tmp==='' ? '' : $tmp);?>
">
                      </td>
                    </tr> 
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="pspsb">
                      <td class="left">跑腿对接账号code</td>
                      <td>
                       <input type="text" name="ptpsbcode" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['ptpsbcode'])===null||$tmp==='' ? '' : $tmp);?>
"><a href="javascript:teskpsb();" id="testrestul">测试对接</a>
                      </td>
                    </tr> 
					
					
					 
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="apiset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
         
           
         </div>
      
    

          </div> 
		  <script>
	$(function(){ 
		 
		showpsb();
		showdata();
	});
	function doshowd(){
	    showdata();
	}
	function showdata(){
		var checkid = $("input[name='psycostset']:checked").val();
		if(checkid == 1){
		   $('#guti').show();
		   $('#biliti').hide();
		}else{
			$('#guti').hide();
		   $('#biliti').show();
		}
	}
	function showpsb(){
		var openid =  $("input[name='pttopsb']:checked").val();
		if(openid == 1){
			$('.pspsb').show();
		}else{
			$('.pspsb').hide();
		}
	}
	function teskpsb(){ 
		var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/area/module/testpsblink/datatype/json/random/@random@"),$_smarty_tpl);?>
'; 
		 $.ajax({
		 type: 'post',
		 async:true,
		 data:{'psblink':$('input[name="ptpsblink"]').val(),'bizid':$('input[name="ptpsbaccid"]').val(),'psbkey':$('input[name="ptpsbkey"]').val(),'psbcode':$('input[name="ptpsbcode"]').val()},
		 url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
		 dataType: 'json',success: function(content) {  
			if(content.error == false){
				 $('#testrestul').html('测试成功');
			}else{
				if(content.error == true)
				{
					 $('#testrestul').html(content.msg); 
				}else{
					 $('#testrestul').html(content); 
				}
			} 
			},
		error: function(content) {   
			 $('#testrestul').html('数据获取失败'); 
		  }
	   });   
	}
   
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