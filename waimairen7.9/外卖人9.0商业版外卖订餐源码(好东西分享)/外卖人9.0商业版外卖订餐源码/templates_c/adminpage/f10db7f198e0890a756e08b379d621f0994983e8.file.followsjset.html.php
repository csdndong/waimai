<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 21:53:02
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\followsjset.html" */ ?>
<?php /*%%SmartyHeaderCode:323995cd5823e609ee5-06473481%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f10db7f198e0890a756e08b379d621f0994983e8' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\followsjset.html',
      1 => 1536024619,
      2 => 'file',
    ),
    '04d88dff3f51fa78bcc566cfea3ffa66cd735c72' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin1.html',
      1 => 1538187010,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '323995cd5823e609ee5-06473481',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'siteurl' => 0,
    'is_static' => 0,
    'tmodule' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd5823e8c8e00_73917665',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5823e8c8e00_73917665')) {function content_5cd5823e8c8e00_73917665($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
?>﻿ <html xmlns="http://www.w3.org/1999/xhtml"><head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<meta http-equiv="Content-Language" content="zh-CN"> 
<meta content="all" name="robots"> 
<meta name="description" content=""> 
<meta content="" name="keywords"> 
<title>后台管理中心 </title>  
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/admin1.css"> 
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.js" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/public1.js" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=wmrPopup"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/datepicker/WdatePicker.js"></script>
 
<script>
	var menu = null;
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	 
	
</script> 
</head> 

<style>
.showtop_t{
   padding-top: 6px!important;
   border: none;
    padding: 0;
}
 .navs{
	display: inline-block;
	height: 30px;
	width: 115px;
	text-align: center; 
	border-top-left-radius: 7px;   
    border-top-right-radius: 7px;   	
 }
 .navon{
	 background-color: #fff!important;
 }
</style>
<body> 
<div id="cat_zhe" class="cart_zhe" style="display:none;"></div>
 
<div style="clear:both;"></div>
<div class="newmain">
	 
   <!-- 主内容区-->
   <div class="newmain_all">
   	 <!-- 主内左区-->
   	 
 
 
  
 
 
 <div class="right_content">
	<div class="show_content_m">
   	        	 <div class="show_content_m_ti">
   	        	 	   
					   <div class="showtop_t" id="positionname">
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='followsjset'){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/followsjset"),$_smarty_tpl);?>
">关注送优惠券</a>
						  </div>						  
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='registersjset'){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/registersjset"),$_smarty_tpl);?>
">注册送优惠券</a>
						  </div>
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='rechargesjset'){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/rechargesjset"),$_smarty_tpl);?>
">充值送优惠券</a>
						  </div>
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='makeordersjset'){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/makeordersjset"),$_smarty_tpl);?>
">下单发红包</a>
						  </div>
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='invitesjset'){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/invitesjset"),$_smarty_tpl);?>
">邀请好友送红包</a>
						  </div>
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='handsendjuan'){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/handsendjuan"),$_smarty_tpl);?>
">手动群发优惠券</a>
						  </div>
					   </div>
					   
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
 <style>
.set{border:1px solid #999d9c;width:80%}
.settop div {text-align:center;	display:inline-block!important;}
.setconc div{text-align:center;	display:inline-block!important;background-color:#F5F5F5;}
.setconrs{border-bottom:1px solid #999d9c;}
.setconrs div{text-align:center;display:inline-block!important;}
.setcon div{text-align:center;}
.setcon{display:table;}
.setconl{width:15.6%;height:100%;display: table-cell;vertical-align: middle;border-bottom:1px solid #999d9c;} 
.fsetconr{width:84.3%; display: table-cell; border-left:1px solid #999d9c;margin-left:-5px;}
.rsetconr{width:84.3%;display:table-cell;border-left:1px solid #999d9c;margin-left:-5px;}
.lqjm {margin-top: 7px;margin-right: 20px;width: 70px;text-align: center;float: right;height: 25px;border-radius: 5px;line-height: 25px;border: 1px solid #dddbdc;background-color: #fff;color: #0099CC;}
 
 </style>
	 <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
         <div class="search">
            <div class="search_content" style='font-size:12px'>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			开启后，用户首次关注微信公众号可以通过系统自动回复内容中的链接领取优惠券。优惠券链接：<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/index.php?ctrl=wxsite&action=gzwx
			<div class='lqjm'><a href='<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/addsjsinfo/id/3"),$_smarty_tpl);?>
'>领取界面</a></div>			 
			</div>
        </div>
		  
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/savefollowjuanset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>                  	 
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">是否开启</td>
                        <td><input type="radio" name="followjuan"   value="1"  <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['status']==1){?>checked<?php }?>>&nbsp;开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="followjuan"   value="0"  <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['status']==0){?>checked<?php }?>>&nbsp;关闭</td>
                     </tr>
					
					  <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">优惠券面值</td>
                        <td><input type="radio" name="costtype" id='fixedcost'  value="1"  <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['costtype']==1){?>checked<?php }?>>&nbsp;固定&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="costtype" id='randomcost'  value="2"  <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['costtype']==2){?>checked<?php }?>>&nbsp;随机</td>
                     </tr>
					 <tr  onmouseout="this.bgColor='ffffff';">
                        <td class="left">优惠券设置</td>
                        <td>
						 	<div class='set' >	
                                <div class='settop' style='width:100%;border-bottom:1px solid #999d9c;background-color:#F5F5F5'>
								    <div style='width:15%'>每人可领取</div>
									<div style='width:25%'>面值</div>
									<div style='width:29%'>使用条件</div>
									<div style='width:29%'>操作</div>
								</div>								
								
								<div class='setcon fixedcost' style='width:100%;<?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['costtype']==2){?>display:none<?php }?>'>
									<div class='setconl'>
									<span class='fjuannum'><?php echo count($_smarty_tpl->tpl_vars['juaninfo']->value,0);?>
</span>张								
								    </div>
                                    <div class='fsetconr' >
									   <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['keys'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['juaninfo']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['keys']->value = $_smarty_tpl->tpl_vars['items']->key;
?>
									   <?php if ($_smarty_tpl->tpl_vars['keys']->value==0){?>
									   <div class='setconrs'>
										   <div style='width:30%'><input type="text" name="fjuancost[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["cost"];?>
" style='width:45px' >元</div>
										   <div style='width:34%'>满 <input type="text" name="fjuanlimitcost[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["limitcost"];?>
" style='width:45px' >元可使用</div>
										   <div style='width:34%;color:#2585a6'><span class="faddjuan">+添加优惠券</span></div>
									   </div>
									  <?php }else{ ?>
									  <div class='setconrs'>
										   <div style='width:30%'><input type="text" name="fjuancost[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["cost"];?>
" style='width:45px' >元</div>
										   <div style='width:34%'>满 <input type="text" name="fjuanlimitcost[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["limitcost"];?>
" style='width:45px' >元可使用</div>
										   <div style='width:34%;color:red'><span class="fdeljuan">删除</span></div>
									   </div>
									   <?php }?>
									   <?php } ?>
									    
								    </div>	
								</div>

								<div class='setcon randomcost' style='width:100%;<?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['costtype']==1){?>display:none<?php }?>'>
									<div class='setconl'>
									<span class='rjuannum'><?php echo count($_smarty_tpl->tpl_vars['juaninfo']->value,0);?>
</span>张							
								    </div>
                                    <div class='rsetconr' >
									   <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['keys'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['juaninfo']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['keys']->value = $_smarty_tpl->tpl_vars['items']->key;
?>
									   <?php if ($_smarty_tpl->tpl_vars['keys']->value==0){?>
									   <div class='setconrs'>
										   <div style='width:30%'><input type="text" name="rjuancostmin[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["costmin"];?>
" style='width:45px' >
										   至 <input type="text" name="rjuancostmax[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["costmax"];?>
" style='width:45px' >元</div>
										   <div style='width:34%'>满 <input type="text" name="rjuanlimitcost[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["limitcost"];?>
" style='width:45px' >元可使用</div>
										   <div style='width:34%;color:#2585a6'><span class="raddjuan">+添加优惠券</span></div>
									   </div>
									   <?php }else{ ?>
									   <div class='setconrs'>
										   <div style='width:30%'><input type="text" name="rjuancostmin[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["costmin"];?>
" style='width:45px' >
											至 <input type="text" name="rjuancostmax[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["costmax"];?>
" style='width:45px' >元</div>
										   <div style='width:34%'>满 <input type="text" name="rjuanlimitcost[]" value="<?php echo $_smarty_tpl->tpl_vars['items']->value["limitcost"];?>
" style='width:45px' >元可使用</div>
										   <div style='width:34%;color:red'><span class="rdeljuan">删除</span></div>
									   </div>								   
									   <?php }?>
									   <?php } ?>  
								    </div>	
								</div>
							   
							    <div class='setfoot' style='width:100%;' >
								    
									&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="paytype"   value="1" <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['paytype']=='1'){?> checked <?php }?>  >&nbsp;
									仅支持在线支付订单使用  
								</div>							
							</div>		
							
							
							
							</div>		
						</td>
                     </tr>
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">使用有效期</td>
                        <td>
						<input type="radio" name="timetype"  class='days' value="1"  <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['timetype']==1){?> checked <?php }?>>
						领劵当日起&nbsp;<input type="text" name="juanday"   value="<?php echo $_smarty_tpl->tpl_vars['juansetinfo']->value['days'];?>
" style='width:40px' > 天内有效
                        </td>   
                     </tr>
					 <tr bgcolor="#ffffff">
                        <td class="left"></td>
                        <td>
						<input type="radio" name="timetype" class='gdsj'  value="2"  <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['timetype']==2){?> checked <?php }?>>&nbsp;固定时间
                        </td>   
                     </tr>
					 <tr bgcolor="#ffffff" class="gdtime" <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['timetype']==1){?> style="display:none"<?php }?>>
                        <td class="left"></td>
                        <td>
						&nbsp;&nbsp;&nbsp;&nbsp;生效时间：<input class="Wdate datefmt" type="text" name="starttime" id="starttime" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['juansetinfo']->value['starttime'],"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" />
                        </td>   
                     </tr>
					 <tr bgcolor="#ffffff" class="gdtime" <?php if ($_smarty_tpl->tpl_vars['juansetinfo']->value['timetype']==1){?> style="display:none"<?php }?>>
                        <td class="left"></td>
                        <td>
						&nbsp;&nbsp;&nbsp;&nbsp;过期时间：<input class="Wdate datefmt" type="text" name="endtime" id="endtime" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['juansetinfo']->value['endtime'],"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" />
                        </td>   
                     </tr>
                   
                    
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="savefollowjuanset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
        </div> 
    </div> 
	<script>
	   $('.days').live('click',function(){ 
		$('.gdtime').hide();        
	  }); 
	  $('.gdsj').live('click',function(){ 
		$('.gdtime').show();        
	  });
      $('#fixedcost').live('click',function(){ 
		$('.fixedcost').show();  
        $('.randomcost').hide();		
	  });
	  $('#randomcost').live('click',function(){ 
		$('.randomcost').show();  
        $('.fixedcost').hide();  		
	  });			
	  
	  $('.raddjuan').live('click',function(){ 
	    var num = $('.rjuannum').text();
		if(num < 5){
		   $('.rjuannum').text(Number(num)+1);
		   var html  = '<div class="setconrs">';
            html += '<div style="width:30%">';
			html += '<input type="text" name="rjuancostmin[]" value=" " style="width:45px" > 至 ';
			html += '<input type="text" name="rjuancostmax[]" value=" " style="width:45px" >元</div>';
			html += '<div style="width:35%">满 ';
			html += '<input type="text" name="rjuanlimitcost[]" value=" " style="width:45px" >元可使用</div>';	
			html += '<div style="width:34%;color:red"><span class="rdeljuan">删除</span></div></div>';
		    $('.rsetconr').append(html); 
		}else{
		   alert('最多只能添加5张优惠券噢~');
		}
		
	  });
	  $('.rdeljuan').live('click',function(){ 
	     var num = $('.rjuannum').text();
		 $('.rjuannum').text(Number(num)-1);
	     $(this).parent().parent().remove();
	  });
	 
	 $('.faddjuan').live('click',function(){ 
	    var num = $('.fjuannum').text();
		if(num < 5){
		   $('.fjuannum').text(Number(num)+1);
		   var html  = '<div class="setconrs">';
            html += '<div style="width:30%">';			
			html += '<input type="text" name="fjuancost[]" value=" " style="width:45px" >元</div>';
			html += '<div style="width:35%">满 ';
			html += '<input type="text" name="fjuanlimitcost[]" value=" " style="width:45px" >元可使用</div>';	
			html += '<div style="width:34%;color:red"><span class="fdeljuan">删除</span></div></div>';
		    $('.fsetconr').append(html); 
		}else{
		   alert('最多只能添加5张优惠券噢~');
		}
		
	  });
	  $('.fdeljuan').live('click',function(){ 
	     var num = $('.fjuannum').text();
		 $('.fjuannum').text(Number(num)-1);
	     $(this).parent().parent().remove();
	  });
	  
	  $("input[type='text']").change(function(){
          var cnum = $(this).val();	  
		  if(isNaN(cnum) || Number(cnum) <= 0){
		     alert('请输入大于0的数字~');
			 $(this).val('1');	
             return false;					 
		  }
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