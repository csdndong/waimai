<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 22:03:09
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\area\adminpsset.html" */ ?>
<?php /*%%SmartyHeaderCode:21375cd5849ddbb006-41532872%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cae36de1b46709724d214ebb50a75af1e53c1aaf' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\area\\adminpsset.html',
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
  'nocache_hash' => '21375cd5849ddbb006-41532872',
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
  'unifunc' => 'content_5cd5849e080806_26013599',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5849e080806_26013599')) {function content_5cd5849e080806_26013599($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">网站默认配送设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
 <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
          <div class="tags">
           
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/area/module/savepsset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/area/module/adminpsset"),$_smarty_tpl);?>
',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody> 
                  
              
					
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff" class="map">
                      <td class="left">配送半径</td>
                      <td>
                       <input type="text" class="skey" style="width:50px;" name="locationradius" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['locationradius'])===null||$tmp==='' ? '0' : $tmp);?>
">
                      </td>
                    </tr> 
                   
                   <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['name'] = 'loop';
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['psinfo']->value['locationradius']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total']);
?> 
                    <tr id="allps" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff"  class="map">
                      <td class="left"> <?php $_smarty_tpl->tpl_vars['tempgongli'] = new Smarty_variable($_smarty_tpl->getVariable('smarty')->value['section']['loop']['index']+1, null, 0);?>
                        	   <?php echo $_smarty_tpl->tpl_vars['tempgongli']->value-1;?>
-<?php echo $_smarty_tpl->tpl_vars['tempgongli']->value;?>
 公里配送费</td>
                      <td><input type="text" name="radiusvalue<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['loop']['index'];?>
"   value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['radiusvalue'][$_smarty_tpl->getVariable('smarty')->value['section']['loop']['index']])===null||$tmp==='' ? '0' : $tmp);?>
" class="skey" style="width:50px;" >元</td>
                    </tr> 
                   <?php endfor; endif; ?>   
                   <!--
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">配送员提成设置</td>
                      <td> <input type="radio" name="psycostset"  value="1"  checked onclick="doshowd();">固定提成
							<input type="radio" name="psycostset"  value="2" <?php if ($_smarty_tpl->tpl_vars['psinfo']->value['psycostset']==2){?>checked<?php }?> onclick="doshowd();">比例提成			
					  </td>
                    </tr> 
                    <tr id="guti" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">固定提成</td>
                      <td> <input type="text" name="psycost" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['psycost'])===null||$tmp==='' ? '0' : $tmp);?>
" style="width:50px;" >元 </td>
                    </tr> 
					<tr id="biliti"  onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">提成比例</td>
                      <td> <input type="text" name="psybili" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['psinfo']->value['psybili'])===null||$tmp==='' ? '0' : $tmp);?>
" style="width:50px;" >设置5表示 提成费用=提成比例*0.01*订单总金额 </td>
                    </tr> 
					-->
					
					
					<!-- <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
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
					
					 -->
					
					
                  </tbody> 
                </table>
              </div>
              <div class="blank20">
			  <font style="color:red;padding-left:100px">说明：
                  <!--配送方式为【商家】或者【配送宝】的店铺，-->
                  配送方式为平台配送的店铺&nbsp;&nbsp;
                  配送设置统一按照以上设置。</font>
			  </div> 
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
        </div>
        <div class="blank20"></div> 
      
      </div>
      <div class="clear"></div>
    </div>
    
    
    
  </div> 
<script>
	$(function(){ 
		//doselect();
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