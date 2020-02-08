<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:24:48
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\analysis\js_statisyic.html" */ ?>
<?php /*%%SmartyHeaderCode:132325cd55f801922b1-30191624%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '17302254b4ad5436a94ca9975807110cd3284917' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\analysis\\js_statisyic.html',
      1 => 1537492579,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '132325cd55f801922b1-30191624',
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
  'unifunc' => 'content_5cd55f80448a60_66262002',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f80448a60_66262002')) {function content_5cd55f80448a60_66262002($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
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

<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.PrintArea.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/datepicker/WdatePicker.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js"></script>
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
   	        	 	   <div class="showtop_t" id="positionname">结算统计</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
      <div style="width:100%;margin-top:-10px;"> 
      	
      	<div class="search" style="height:60px;line-height: 60px;"> 
            <div class="search_content">
            	 
            	 <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/analysis/module/js_statisyic"),$_smarty_tpl);?>
">
            	 	<label>所属站点：</label>
            	 	<select name="admin_id">
            	 		<option value="0">选择站点</option>
            	 		<?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['arealist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
            	 		<option value="<?php echo $_smarty_tpl->tpl_vars['items']->value['adcode'];?>
" <?php if ($_smarty_tpl->tpl_vars['admin_id']->value==$_smarty_tpl->tpl_vars['items']->value['adcode']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</option>
            	 		<?php } ?>
            	 	</select>
					<label>店铺：</label>
            	  <input type="text" name="shopname" value="<?php echo $_smarty_tpl->tpl_vars['shopname']->value;?>
"> 
				  时间：
            	   <input class="Wdate datefmt" type="text" name="starttime" id="starttime" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['starttime']->value,"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" style="width:100;"  />  
			   		 	  至<input class="Wdate datefmt" type="text" name="endtime" id="endtime" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['endtime']->value,"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" style="width:100;" />                  

            	    <input type="submit" value="查询" class="button">  
            	   <span class="<?php if ($_smarty_tpl->tpl_vars['datetype']->value==1){?>selectcolor<?php }else{ ?>noselectcolor<?php }?>"><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/analysis/module/js_statisyic/datetype/1"),$_smarty_tpl);?>
">近7天</a></span><span data="2" class="<?php if ($_smarty_tpl->tpl_vars['datetype']->value==2){?>selectcolor<?php }else{ ?>noselectcolor<?php }?>"><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/analysis/module/js_statisyic/datetype/2"),$_smarty_tpl);?>
">近30天</a></span>
            	 </form>
            </div>
        
      	</div>
      	<style>
      		 body{color:#666;}
      		.trade_des{text-align: left;}
      		.trade_title{height: 40px;line-height: 40px;padding:0 20px;text-align: left;}
      		.trade_title i{width: 4px;display: inherit;float: left; height: 16px; margin-top: 11px;background-color: rgba(22, 155, 213, 1);}
      		.trade_title span{color: #333;font-weight: 700; margin-left: 10px;}
			.trade_title .delurl{line-height:24px;float:right;margin-top: 8px;padding: 0px 5px;font-size:12px;color:#0076cf;}
      		.firstul{padding: 0 20px;}
      		.firstli{padding: 20px 10px;background-color: rgba(250, 250, 250, 1);height: 120px;}
      		.secondul{padding: 0 20px 0 10px;}
      		.secondli{width: 25%;float: left;}
      		.thirdul{border-right: 1px solid #ddd;width: 90%;}
      		.thirdul .third_first{color:#666;}
      		.third_first i{background-image: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/askicon.png);width: 11px;height: 11px;display: inline-block;border-radius: 50%;background-size: 12px 12px;margin-left: 4px;cursor: pointer;position:relative;}
			.third_first i p{display: none;position: absolute;top: -20px;left: 20px;color: #000;width: 88px;background-color: #fff;padding: 5px;border-radius: 7px;border: 1px solid #ccc;font-size: 12px;font-style: normal;}
      		.third_second span{color: #000;font-weight: bold;font-size: 18px; height: 40px;line-height: 40px;}
      		.third_third div span{color:#999;}
			.third_third div .uparrow{background-image: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/uparrow.png);width: 12px;height: 12px;display: inline-block;border-radius: 50%;background-size: 12px 12px;transform: rotateZ(180deg);}
			.third_third div .downarrow{background-image: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/downarrow.png);width: 12px;height: 12px;display: inline-block;border-radius: 50%;background-size: 12px 12px;}
			.third_third div .upcolor{color:#01CD88;}
			.third_third div .downcolor{color:#FF7600;}
			.selectcolor{color: #1783b2;font-size: 12px;padding: 2px;border: 1px solid #1783b2;margin: 0 0 0 10px;cursor:pointer;}
 			.noselectcolor{color: #666;font-size: 12px;padding: 2px;border: 1px solid #ccc;margin: 0 0 0 10px;cursor:pointer;}
			.selectcolor a{color:#0076cf;}
			.noselectcolor a{color:#666;}
			.selectcolor a:link{color:#0076cf;}
			.noselectcolor a:link{color:#666;}
			.show_content_m_t3{display:none}
      	</style>
      	<div class="trade_des">
      		<div class="trade_title">
      			<i></i>
      			<span>结算统计</span>
				<a  href="<?php echo $_smarty_tpl->tpl_vars['outlink1']->value;?>
" target="_blank" class="delurl">导出数据</a>
      		</div>
      		<ul class="firstul">
      			<li class="firstli">
      				<ul class="secondul">
      					<li class="secondli">
      						<ul class="thirdul">
      							<li class="third_first"><span>订单总金额</span><i onmouseover="mouseover(this)" onmouseout="mouseout(this)"><p>交易完成的订单金额总和</p></i></li>
      							<li class="third_second"><span>￥<?php echo $_smarty_tpl->tpl_vars['totalcost']->value;?>
</span></li>
								<li class="third_third">
      								<div>
      									<span>比前<?php echo $_smarty_tpl->tpl_vars['beforeday']->value;?>
日</span>
      									<i class="<?php if ($_smarty_tpl->tpl_vars['totalbilitype']->value==1){?>uparrow<?php }else{ ?>downarrow<?php }?>"></i>
      									<span class="<?php if ($_smarty_tpl->tpl_vars['totalbilitype']->value==1){?>upcolor<?php }else{ ?>downcolor<?php }?>"><?php echo $_smarty_tpl->tpl_vars['totalbili']->value;?>
%</span>
      								</div>
      							</li>
      						</ul>
      				    </li>
      					<li class="secondli">
      						<ul class="thirdul">
      							<li class="third_first"><span>商家结算金额</span><i onmouseover="mouseover(this)" onmouseout="mouseout(this)"><p>交易完成的订单商家实际收入</p></i></li>
      							<li class="third_second"><span>￥<?php echo $_smarty_tpl->tpl_vars['jscost']->value;?>
</span></li>
								<li class="third_third">
      								<div>
      									<span>比前<?php echo $_smarty_tpl->tpl_vars['beforeday']->value;?>
日</span>
      									<i class="<?php if ($_smarty_tpl->tpl_vars['jsbilitype']->value==1){?>uparrow<?php }else{ ?>downarrow<?php }?>"></i>
      									<span class="<?php if ($_smarty_tpl->tpl_vars['jsbilitype']->value==1){?>upcolor<?php }else{ ?>downcolor<?php }?>"><?php echo $_smarty_tpl->tpl_vars['jsbili']->value;?>
%</span>
      								</div>
      							</li>
      						</ul>
      					</li>
      					<li class="secondli">
      						<ul class="thirdul">
      							<li class="third_first"><span>平台佣金收入</span><i onmouseover="mouseover(this)" onmouseout="mouseout(this)"><p>交易完成的订单平台佣金收入</p></i></li>
      							<li class="third_second"><span>￥<?php echo $_smarty_tpl->tpl_vars['ordyj']->value;?>
</span></li>
								<li class="third_third">
      								<div>
      									<span>比前<?php echo $_smarty_tpl->tpl_vars['beforeday']->value;?>
日</span>
      									<i class="<?php if ($_smarty_tpl->tpl_vars['ordyjbilitype']->value==1){?>uparrow<?php }else{ ?>downarrow<?php }?>"></i>
      									<span class="<?php if ($_smarty_tpl->tpl_vars['ordyjbilitype']->value==1){?>upcolor<?php }else{ ?>downcolor<?php }?>"><?php echo $_smarty_tpl->tpl_vars['ordyjbili']->value;?>
%</span>
      								</div>
      							</li>
      						</ul>
      					</li>
      					<li class="secondli">
      						<ul class="thirdul" style="border:none;">
      							<li class="third_first"><span>活动补贴金额</span><i onmouseover="mouseover(this)" onmouseout="mouseout(this)"><p style="width:150px;">平台补贴：<?php echo $_smarty_tpl->tpl_vars['ptcxcost']->value;?>
</br>商家补贴：<?php echo $_smarty_tpl->tpl_vars['shopcxcost']->value;?>
</p></i></li>
      							<li class="third_second"><span>￥<?php echo $_smarty_tpl->tpl_vars['allcxcost']->value;?>
</span></li>
								<li class="third_third">
      								<div>
      									<span>比前<?php echo $_smarty_tpl->tpl_vars['beforeday']->value;?>
日</span>
      									<i class="<?php if ($_smarty_tpl->tpl_vars['cxbilitype']->value==1){?>uparrow<?php }else{ ?>downarrow<?php }?>"></i>
      									<span class="<?php if ($_smarty_tpl->tpl_vars['cxbilitype']->value==1){?>upcolor<?php }else{ ?>downcolor<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cxbili']->value;?>
%</span>
      								</div>
      							</li>
      						</ul>
      					</li>
      				</ul>
      			</li>
      		</ul>
      	</div>
		<style>
		.showtishi{background-image: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/askicon.png);width: 11px;height: 11px;display: inline-block;border-radius: 50%;background-size: 12px 12px;margin-left: 4px;cursor: pointer;position:relative;}
		.showtishi p{display:none;position: absolute;top: -56px;left: -114px;color: #000;width: 185px;background-color: #fff;padding: 5px;border-radius: 7px;border: 1px solid #ccc;font-size: 12px;font-style: normal;}
		</style>
		<div class="tags">
			<div id="tagscontent">
				<div id="con_one_1">
					<div class="table_td" style="margin-top:10px;">
						<div class="trade_title" style="font-size:14px;">
							<i></i>
							<span>结算记录</span>
							<a  href="<?php echo $_smarty_tpl->tpl_vars['outlink2']->value;?>
" target="_blank" class="delurl">导出数据</a>
						</div>
						<form method="post" action="" onsubmit="return remind();">
						<table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
							<thead>
								<tr>
									<th align="center">结算时间</th>
									<th align="center">订单号</th>
									<th align="center">商家名称</th>
									<th align="center">订单金额</th>
									<th align="center">优惠金额</th>
									<th align="center">订单佣金<i class="showtishi" onmouseover="mouseover(this)" onmouseout="mouseout(this)"><p>交易完成的订单商家实际收入</p></i></th>
									<th align="center">结算金额<i class="showtishi" onmouseover="mouseover(this)" onmouseout="mouseout(this)"><p>交易完成的订单商家实际收入</p></i></th>
									<th align="center">操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($_smarty_tpl->tpl_vars['jsordlist']->value)){?>
									<tr><td align="center" colspan="8">暂无数据</td></tr>
								<?php }?>
								<?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['jsordlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>
								<tr>
									<td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['jstime'];?>
</td>
									<td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
</td>
									<td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
</td>
									<td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['allcost'];?>
</td>
									<td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['cxcost'];?>
</td>
									<td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['yjcost'];?>
</td>
									<td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['acountcost'];?>
</td>
									<td align="center"><span onclick="showdetail(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
);" style="color: #1783b2;cursor: pointer;">明细</span></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						</form>
						<div class="blank20"></div>
						<div class="show_page"><ul><?php echo $_smarty_tpl->tpl_vars['pagecontent']->value;?>
</ul></div>
						<div class="blank20"></div> 
					</div>
				</div>
			</div>
		</div>

          </div>

        </div>

        
  </div>
  <div id="print_area" style="display:none;"></div>
 </div>
 
<script type="text/javascript">
   function colorred(obj){
   	$(obj).css('background','#eee'); 
   }
   function tcolorred(obj){
   	$(obj).css('background','');
   }
   function mouseover(obj){
		//console.log(1111);
		$(obj).parent().find('p').css('display','block');
   }
   function mouseout(obj){
		//console.log(2222);
		$(obj).parent().find('p').css('display','none');
   }
   var dialogs ;
   function showdetail(orderid){
		dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=analysis&module=showdetail&orderid='+orderid,{height:'650px',width:'400px'},false);
	 	 dialogs.title('查看订单详情'); 
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