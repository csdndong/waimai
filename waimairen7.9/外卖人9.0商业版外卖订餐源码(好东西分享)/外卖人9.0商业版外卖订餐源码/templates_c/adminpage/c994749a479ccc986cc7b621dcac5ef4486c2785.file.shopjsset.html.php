<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 22:03:05
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\shop\shopjsset.html" */ ?>
<?php /*%%SmartyHeaderCode:230625cd58499a5d727-43891514%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c994749a479ccc986cc7b621dcac5ef4486c2785' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\shop\\shopjsset.html',
      1 => 1536024620,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '230625cd58499a5d727-43891514',
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
  'unifunc' => 'content_5cd58499d3e620_86990817',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd58499d3e620_86990817')) {function content_5cd58499d3e620_86990817($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">结算设置</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
 
	  <div style="width:auto;overflow-x:hidden;overflow-y:auto;">   
          <div id="tagscontent">
               <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
                     
                     
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left"><span style="font-weight: bolder;">佣金设置</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                      <td>
                      	 
                      </td>
                    </tr>
					 </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">平台配送</td>
                        <td>
                         佣金计算内容：
						 <input name="ptyjps" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['ptyj']->value['pscost'];?>
" <?php if ($_smarty_tpl->tpl_vars['ptyj']->value['pscost']==1){?>checked<?php }?> /> + 配送费
						 <input name="ptyjbag" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['ptyj']->value['bagcost'];?>
"  <?php if ($_smarty_tpl->tpl_vars['ptyj']->value['bagcost']==1){?>checked<?php }?> /> + 打包费
						 <input name="ptyjcx" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['ptyj']->value['shopdowncost'];?>
"  <?php if ($_smarty_tpl->tpl_vars['ptyj']->value['shopdowncost']==1){?>checked<?php }?> /> - 促销金额中商家承担的部分						 
						 （除了商品费用外是否包含这些费用）
                        </td>
                    </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left"> </td>
                        <td>
                         佣金计算公式 =（商品总价
						 <span id="ptyjps" <?php if ($_smarty_tpl->tpl_vars['ptyj']->value['pscost']==0){?>style="display:none"<?php }?>> + 配送费</span>
						 <span id="ptyjbag" <?php if ($_smarty_tpl->tpl_vars['ptyj']->value['bagcost']==0){?>style="display:none"<?php }?>> + 打包费</span>
						 <span id="ptyjcx" <?php if ($_smarty_tpl->tpl_vars['ptyj']->value['shopdowncost']==0){?>style="display:none"<?php }?>> - 促销金额中商家承担的部分</span>）× 佣金比例 
                        <input type="button" value="保存" class="button" data="ptyj" des="1" >  						 
                        </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">商家配送</td>
                      <td>
                      	佣金计算内容：
						 <input name="sjyjps" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['sjyj']->value['pscost'];?>
" <?php if ($_smarty_tpl->tpl_vars['sjyj']->value['pscost']==1){?>checked<?php }?>  /> + 配送费
						 <input name="sjyjbag" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['sjyj']->value['bagcost'];?>
" <?php if ($_smarty_tpl->tpl_vars['sjyj']->value['bagcost']==1){?>checked<?php }?> /> + 打包费
						 <input name="sjyjcx" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['sjyj']->value['shopdowncost'];?>
" <?php if ($_smarty_tpl->tpl_vars['sjyj']->value['shopdowncost']==1){?>checked<?php }?> /> - 促销金额中商家承担的部分		
						 （除了商品费用外是否包含这些费用）
                      </td>
                    </tr>
				    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left"></td>
                      <td>
                      	 佣金计算公式 =（商品总价
						 <span id="sjyjps" <?php if ($_smarty_tpl->tpl_vars['sjyj']->value['pscost']==0){?>style="display:none"<?php }?>> + 配送费</span>
						 <span id="sjyjbag" <?php if ($_smarty_tpl->tpl_vars['sjyj']->value['bagcost']==0){?>style="display:none"<?php }?>> + 打包费</span>
						 <span id="sjyjcx" <?php if ($_smarty_tpl->tpl_vars['sjyj']->value['shopdowncost']==0){?>style="display:none"<?php }?>> - 促销金额中商家承担的部分</span>）× 佣金比例 
                          <input type="button" value="保存" class="button" data="sjyj" des="2">
					  </td>
                    </tr>
				    
				    
				    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left"><span style="font-weight: bolder;">结算设置</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                      <td>
                      	 
                      </td>
                    </tr>
					 </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">平台配送</td>
                        <td>
                         结算金额内容：
						 <input name="ptjsps" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['ptjs']->value['pscost'];?>
" <?php if ($_smarty_tpl->tpl_vars['ptjs']->value['pscost']==1){?>checked<?php }?>  /> + 配送费
						 <input name="ptjsbag" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['ptjs']->value['bagcost'];?>
" <?php if ($_smarty_tpl->tpl_vars['ptjs']->value['bagcost']==1){?>checked<?php }?> /> + 打包费
						 <input name="ptjscx" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['ptjs']->value['shopdowncost'];?>
" <?php if ($_smarty_tpl->tpl_vars['ptjs']->value['shopdowncost']==1){?>checked<?php }?> /> - 促销金额中商家承担的部分
						 （除了商品费用外是否包含这些费用）
 
                        </td>
                    </tr>
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left"> </td>
                        <td>
                         结算金额公式 =（商品总价
						 <span id="ptjsps" <?php if ($_smarty_tpl->tpl_vars['ptjs']->value['pscost']==0){?>style="display:none"<?php }?> > + 配送费</span>
						 <span id="ptjsbag" <?php if ($_smarty_tpl->tpl_vars['ptjs']->value['bagcost']==0){?>style="display:none"<?php }?>> + 打包费</span>
						 <span id="ptjscx" <?php if ($_smarty_tpl->tpl_vars['ptjs']->value['shopdowncost']==0){?>style="display:none"<?php }?>> - 促销金额中商家承担的部分</span>）-佣金  
                         <input type="button" value="保存" class="button" data="ptjs" des="3">
						</td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">商家配送</td>
                      <td>
                      	结算金额内容：
						 <input name="sjjsps" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['sjjs']->value['pscost'];?>
" <?php if ($_smarty_tpl->tpl_vars['sjjs']->value['pscost']==1){?>checked<?php }?>  /> + 配送费
						 <input name="sjjsbag" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['sjjs']->value['bagcost'];?>
" <?php if ($_smarty_tpl->tpl_vars['sjjs']->value['bagcost']==1){?>checked<?php }?> /> + 打包费
						 <input name="sjjscx" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['sjjs']->value['shopdowncost'];?>
" <?php if ($_smarty_tpl->tpl_vars['sjjs']->value['shopdowncost']==1){?>checked<?php }?> /> - 促销金额中商家承担的部分
						（除了商品费用外是否包含这些费用）
                      </td>
                   </tr>
				    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left"></td>
                      <td>
                      	 结算金额公式 =（商品总价
						 <span id="sjjsps" <?php if ($_smarty_tpl->tpl_vars['sjjs']->value['pscost']==0){?>style="display:none"<?php }?> > + 配送费
						 </span><span id="sjjsbag" <?php if ($_smarty_tpl->tpl_vars['sjjs']->value['bagcost']==0){?>style="display:none"<?php }?> > + 打包费
						 </span><span id="sjjscx" <?php if ($_smarty_tpl->tpl_vars['sjjs']->value['shopdowncost']==0){?>style="display:none"<?php }?> > - 促销金额中商家承担的部分</span>）- 佣金 
                         <input type="button" value="保存" class="button" data="sjjs" des="4">
					 </td>
                   </tr>
				     
                        
		 
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
             
            
          </div>
         
           
         </div>
      
    

          </div> 
<style>
.button{
    float: right;
    margin-right: 300px;
	margin-top: 6px;
}

</style>
		  
		  
<script>
	$("input").bind('click', function () {
	var type = $(this).attr('type');
	if(type == "radio"){
	  var name = $(this).attr('name');
	  var val = $(this).val();	    
	  if (val == 1){
	      $(this).val('0') ;
		  $(this).attr('checked',false) ;
	      $('#'+name).hide() ;
	  }else{
	      $(this).val('1') ;
		  $(this).attr('checked',true) ;
	      $('#'+name).show() ;
	    }
	  }
	if(type == "button"){
	    var name = $(this).attr('data');
	    var des = $(this).attr('des');
	    var pscost = $("input[name="+name+"ps]").val() ;
	    var bagcost = $("input[name="+name+"bag]").val() ;
	    var shopdowncost = $("input[name="+name+"cx]").val() ;
	    url = siteurl+'/index.php?ctrl=adminpage&action=shop&module=saveshopjsset&datatype=json&random=@random@';
     	url = url.replace('@random@', 1+Math.round(Math.random()*1000));
           $.ajax({          
                 url: url.replace('@random@', 1+Math.round(Math.random()*1000)),
                 dataType: "json",
                 async:true,
                 data:{'des':des,'pscost':pscost,'bagcost':bagcost,'shopdowncost':shopdowncost},
                 success:function(content) { 
                 	if(content.error ==  false){					
						alert('保存成功');	
                        window.location.reload();						 
                 	}else{
                 		alert(content.msg);
                 	}
                },      
           }); 
	    }	  
	})	
	
     
 	
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