<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:26:26
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\system\siteset.html" */ ?>
<?php /*%%SmartyHeaderCode:123175cd55fe29efcd8-84230757%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a80e83ca4af6becc2225c16c0811d91238ff0e45' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\system\\siteset.html',
      1 => 1537495131,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '123175cd55fe29efcd8-84230757',
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
  'unifunc' => 'content_5cd55fe2b37773_67793910',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55fe2b37773_67793910')) {function content_5cd55fe2b37773_67793910($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
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
   	        	 	   <div class="showtop_t" id="positionname">网站设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
	
	  <div style="width:auto;overflow-x:hidden;overflow-y:auto;">   
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/saveset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">网站名称</td>
                      <td><input type="text" name="sitename" id="sitename" value="<?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
" class="skey" style="width:150px;"></td>
                    </tr>
                    
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="ffffff">
                      <td class="left">SEO描述</td>
                      <td><input type="text" name="description" id="description" value="<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
" class="skey" style="width:150px;"></td>
                    </tr>
                     <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">SEO关键词</td>
                      <td><input type="text" name="keywords" id="keywords" value="<?php echo $_smarty_tpl->tpl_vars['keywords']->value;?>
" class="skey" style="width:150px;"></td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">公司名称</td>
                      <td><input type="text" name="companyname" id="companyname" value="<?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
" class="skey" style="width:150px;"></td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">官网地址</td>
                      <td><input type="text" name="website" id="website" value="<?php echo $_smarty_tpl->tpl_vars['website']->value;?>
" class="skey" style="width:150px;"></td>
                    </tr>
                     
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">备案号</td>
                      <td><input type="text" name="beian" id="beian" value="<?php echo $_smarty_tpl->tpl_vars['beian']->value;?>
" class="skey" style="width:150px;"></td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">客服电话</td>
                      <td><input type="text" name="litel" id="litel" value="<?php echo $_smarty_tpl->tpl_vars['litel']->value;?>
" class="skey" style="width:150px;"></td>
                    </tr>
                   <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">自营城市</td>
                      <td>
                        <select name="default_cityid" style='width:159px'>
                             <option value="0">请选择网站自营城市</option> 
                                      <?php echo smarty_function_load_data(array('assign'=>"arealist",'table'=>"area",'fileds'=>"*",'where'=>" parent_id=0 ",'orderby'=>" orderid asc ",'limit'=>"1000"),$_smarty_tpl);?>
  
                                             <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['arealist']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>  
                                                <option value="<?php echo $_smarty_tpl->tpl_vars['items']->value['adcode'];?>
" <?php if ($_smarty_tpl->tpl_vars['default_cityid']->value==$_smarty_tpl->tpl_vars['items']->value['adcode']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</option>
                                             <?php } ?> 
                               </select>
                       </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff" >
                      <td class="left">佣金比例</td>
                      <td><input type="text" name="yjin" id="yjin" value="<?php echo $_smarty_tpl->tpl_vars['yjin']->value;?>
" class="skey" style="width:50px;">%</td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff" >
                      <td class="left">商家最低提现金额</td>
                      <td><input type="text" name="leasttx" id="leasttx" value="<?php echo $_smarty_tpl->tpl_vars['leasttx']->value;?>
" class="skey" style="width:50px;">元<span style='color:#999'>（录入0时表示没有最低提现限制）</span></td>
                    </tr>
					
					
					
					
					<?php if (!empty($_smarty_tpl->tpl_vars['default_cityid']->value)){?>
					
					
				 <!--  <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left" style="color:red;">设置相关数据全部绑定当前城市</td>
                      <td><input type="button" id="bangdingDefCity" value="点击设置" class="skey" style="width:100px;" />
					  <br /><span style="color:red;font-weight:bold;">提示：设置后无法撤销，请谨慎操作，如果不懂请咨询客服，点击设置会将以下数据自动绑定为上面所绑定的网站自营城市：广告、分类、店铺、配送员所属城市、网站通知、生活服务、跑腿信息设置、专题页管理</span>
					  </td>
                    </tr> -->
					<script>
					$('#bangdingDefCity').click(function(){
					
						if(confirm("确定要把相关数据绑定为当前城市吗?\n设置后无法撤销，请谨慎操作"))
						 {
						 
						 
						var url= siteurl+'/index.php?ctrl=adminpage&action=system&module=setCityDatas&datatype=json&random=@random@';
						url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
						var bk = ajaxback(url,{}); 
						if(bk.flag == false){
							diaerror("设置成功！");
						}else{
							diaerror(bk.content);
						}
						 
						 
						 
						 }
					
					});
					</script>
					
					<?php }?>
         
                   
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">店铺公告</td>
                      <td><textarea style='width:335px;height:90px' name="shopnotice"><?php echo $_smarty_tpl->tpl_vars['shopnotice']->value;?>
</textarea>&nbsp;&nbsp;&nbsp;<span style='color:#999'>在店铺没有自行设置店铺公告的情况下,默认显示该公告</span></td>
                    </tr>

                    
                    
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">统计代码</td>
                      <td><textarea style='width:335px;height:90px' name="footerdata"><?php echo stripslashes($_smarty_tpl->tpl_vars['footerdata']->value);?>
</textarea></td>
                    </tr>
				
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="siteset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
         
           
         </div>
      
    

          </div> 



  
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