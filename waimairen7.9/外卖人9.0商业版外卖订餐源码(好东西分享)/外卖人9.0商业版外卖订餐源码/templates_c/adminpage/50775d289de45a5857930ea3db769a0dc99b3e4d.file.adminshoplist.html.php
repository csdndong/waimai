<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:25:23
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\shop\adminshoplist.html" */ ?>
<?php /*%%SmartyHeaderCode:128035cd55fa3530471-74874990%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '50775d289de45a5857930ea3db769a0dc99b3e4d' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\shop\\adminshoplist.html',
      1 => 1537876910,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '128035cd55fa3530471-74874990',
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
  'unifunc' => 'content_5cd55fa3758fd6_02672304',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55fa3758fd6_02672304')) {function content_5cd55fa3758fd6_02672304($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
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
   	        	 	   <div class="showtop_t" id="positionname">店铺列表</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	

      <div style="width:auto;overflow-x:hidden;overflow-y:auto">  
      	 
      	<div class="search">
      		 
            
            <div class="search_content">
            	 
            	 <form method="get" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/adminshoplist"),$_smarty_tpl);?>
"> 
				 
            	   <input type="hidden" name="ctrl" value="adminpage">
            	   <input type="hidden" name="action" value="shop">
            	   <input type="hidden" name="module" value="adminshoplist">
            	   <label>店铺名:</label>
            	   <input type="text" name="shopname" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['shopname']->value)===null||$tmp==='' ? '' : $tmp);?>
">&nbsp;&nbsp;  
            	   <label>用户名:</label>
            	   <input type="text" name="username" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['username']->value)===null||$tmp==='' ? '' : $tmp);?>
">&nbsp;&nbsp;                   
                  <label>手机:</label>
            	   <input type="text" name="phone" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['phone']->value)===null||$tmp==='' ? '' : $tmp);?>
">    
            	  
            	    <input type="submit" value="提交查询" class="button">  
            	 </form>
            </div>       
        
      	</div>
      	
      	
           <div class="tags">

      	  
          <div id="tagscontent">

            <div id="con_one_1">

              <div class="table_td" style="margin-top:10px;">

              <form method="post" action="" onsubmit="return remind();"  id="delform">

                  <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">

                    <thead>

                      <tr>

                       

                        <th align="center">店铺名称</th>
						<th align="center">店铺类型</th>
                        <th align="center">所属城市</th>
						<th align="center">入驻资料</th> 
                        <th align="center">会员名称</th>  
                        <th align="center">店铺标签</th>
                        <th align="center">通知管理</th>						
                        <th align="center">营业</th> 
                        <th align="center">佣金设置</th>
						<th align="center">结算额</th>
                        <th align="center">排序</th>
                        <th align="center">有效时间</th>
						<th align="center">绑定微信</th>
                        <th align="center">配送设置</th>
                        <th align="center">操作</th>

                      </tr>

                    </thead> 

                     <tbody>

                     <?php echo smarty_function_load_data(array('assign'=>"list",'table'=>"shop",'showpage'=>"true",'where'=>"is_pass='1' ".((string)$_smarty_tpl->tpl_vars['where']->value),'orderby'=>" sort asc "),$_smarty_tpl);?>
 
                      <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?> 
                      <tr class="s_out" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff"> 
                      
                        <td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
[<font color=red><?php echo $_smarty_tpl->tpl_vars['shoptype']->value[$_smarty_tpl->tpl_vars['items']->value['shoptype']];?>
</font>]</td> 
						<td align="center"><?php echo $_smarty_tpl->tpl_vars['shoptype']->value[$_smarty_tpl->tpl_vars['items']->value['shoptype']];?>
</td> 
                        <td align="center"><?php echo smarty_function_load_data(array('assign'=>"cityinfo",'table'=>"area",'type'=>"one",'where'=>"adcode='".((string)$_smarty_tpl->tpl_vars['items']->value['admin_id'])."'",'fileds'=>"name"),$_smarty_tpl);?>

						<?php echo $_smarty_tpl->tpl_vars['cityinfo']->value['name'];?>
</td> 
						   <td align="center"> <a onclick="showshopdetail('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
');" href="#">查看详情</a></td> 
                        <td align="center"> 
                        	<?php echo smarty_function_load_data(array('assign'=>"userinfo",'table'=>"member",'type'=>"one",'where'=>"uid='".((string)$_smarty_tpl->tpl_vars['items']->value['uid'])."'",'fileds'=>"username,shopcost,backacount"),$_smarty_tpl);?>
 
                          	<?php echo $_smarty_tpl->tpl_vars['userinfo']->value['username'];?>
  
                        	</td> 
                        <td align="center">
                        <a onclick="starttask('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
');" href="#">设置</a> </td>  
						<td align="center">
                        <a onclick="setps('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
');" href="#">订单通知</a></td>  
                        <td align="center"><?php if ($_smarty_tpl->tpl_vars['items']->value['is_open']==1){?>是<?php }else{ ?>否<?php }?></td> 
                   
                         <td align="center"><?php if ($_smarty_tpl->tpl_vars['items']->value['yjin']=='0'){?>默认<?php echo $_smarty_tpl->tpl_vars['yjin']->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['items']->value['yjin'];?>
<?php }?>%&nbsp;&nbsp;&nbsp;<a onclick="setyjin('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['yjin'];?>
','<?php echo $_smarty_tpl->tpl_vars['yjin']->value;?>
','<?php echo (($tmp = @$_smarty_tpl->tpl_vars['userinfo']->value['backacount'])===null||$tmp==='' ? '' : $tmp);?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['zitiyjb'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['zitilimityj'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['zitianyj'];?>
');" href="#">修改</a></td>
						 <td align="center"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['userinfo']->value['shopcost'])===null||$tmp==='' ? '0' : $tmp);?>
&nbsp;&nbsp;&nbsp;<a href="#" onclick="addcost(<?php echo $_smarty_tpl->tpl_vars['items']->value['uid'];?>
);">变动</a></td>
                        <td align="center"><input type="text" name="pxinput" data="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['items']->value['sort'];?>
" style="width:30px;padding:0px;"></td>
                        <td align="center"><a href="#" onclick="doshow('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo intval(($_smarty_tpl->tpl_vars['items']->value['endtime']-time())/86400);?>
');"> <?php if ($_smarty_tpl->tpl_vars['items']->value['endtime']>0){?>    <?php echo intval(($_smarty_tpl->tpl_vars['items']->value['endtime']-time())/86400);?>
    <?php }else{ ?>设置 <?php }?></a></td>
                        <td align="center"><?php if ($_smarty_tpl->tpl_vars['items']->value['is_bdwx']==1){?><a href="#" onclick="showinfo('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['wxusername'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['wxuserlogo'];?>
');">查看</a><?php }else{ ?><a href="#" onclick="bdwx('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
');" style="color:#333;"> 绑定 </a><?php }?></td>
						<td align="center">
                        <a onclick="psset('<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
');" href="#">配送设置</a></td>  
                         <td align="center"><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/resetdefualt/shopid/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])),$_smarty_tpl);?>
" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/but.png" style='width:100px'></a></td> 
                      </tr> 
                       <?php } ?> 

                    </tbody> 

                  </table>

                <div class="blank20"></div>

                 
                </form>

                <div class="page_newc">
                 	     <div class="select_page">
                 	      
                 	     	
                 	     	
                 	     	</div>
                       <div class="show_page"><ul><?php echo $_smarty_tpl->tpl_vars['list']->value['pagecontent'];?>
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
	 	var dialogs ;
		var checkis_bd = true;
		var checkshopid = 0;
	 function starttask(shopid,shopname)
	 {
	 	 dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=shop&module=shopbiaoqian&id='+shopid,{height:'300px',width:'400px'},false); 
	 }
	  function showshopdetail(shopid,shopname)
	 {
	 	 dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=shop&module=showshopdetail&id='+shopid,{height:'650px',width:'800px'},false);
	 	 dialogs.title('查看'+shopname+'入驻资料'); 
	 }
function uploadsucess(linkurl){
 	dialogs.close(); 
 	window.location.reload(); 
}
function uploaderror(msg){
	 alert(msg); 
	 return false;
}
function addcost(uid){
	 
	  var	htmls = '<form method="post" id="subyjxx" action="#" style="text-align:center;"><table>';
		htmls += '<tbody><tr>';
		htmls += '<td height="50px">金额:</td>';
		htmls += '<td> <input type="text" name="cost" value="" style="width:100px;"></td></tr>';  
		
			htmls += '<tr><td height="50px">类型:</td>';
		htmls += '<td> <input type="radio" name="dotype" value="0">增加 <input type="radio" name="dotype" value="1">减少</td></tr>';  
		
		htmls += '<tr><td height="50px">原因:</td>';
		htmls += '<td> <input type="text" name="reason" value="" style="width:150px;"> </tr>';  
		
		htmls += '</tbody></table> ';
	  htmls += '<input type="hidden" value="'+uid+'" name="uid"> ';
		htmls += '<input type="button" value="确认提交" class="button" id="buttonsubyjcc" ></form>';
	  art.dialog({
		id: 'testID4',
		title:'编辑店铺余额',
		content: htmls
	  });
}
$('#buttonsubyjcc').live('click',function(){ 
	$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/adminpay/datatype/json"),$_smarty_tpl);?>
', $('#subyjxx').serialize() ,function (data, textStatus){  
			if(data.error == false){
     	  	diasucces('操作成功','');
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

function setyjin(shopid,shopname,myongjin,defaultyj,yinhang,zitiyjb,zitilimityj,zitianyj)
{
	var mytj = myongjin==0?defaultyj:myongjin;
    var	htmls = '<form method="post" id="subyj" action="#" style="text-align:center;"><table>';
	htmls += '<tbody><tr>';
	htmls += '<td height="50px">外卖佣金比例:</td>';
	htmls += '<td> <input type="text" name="yjin" value="'+mytj+'" style="width:50px;">%</td></tr>';
	htmls += '<td height="50px">自取佣金比例:</td>';
	htmls += '<td> <input type="text" name="zitiyjb" value="'+zitiyjb+'" style="width:50px;">%</td></tr>';
	htmls += '<td height="50px">自取佣金限制:</td>';
	htmls += '<td>自取订单佣金每单若不满<input type="text" name="zitilimityj" value="'+zitilimityj+'" style="width:40px;">元按<input type="text" name="zitianyj" value="'+zitianyj+'" style="width:40px;">元计算</td></tr>';
	htmls += '<td height="50px">店铺提现账号:</td>';
	htmls += '<td> <input type="text" name="backacount" value="'+yinhang+'" style="width:200px;"></td></tr>';
	
	htmls += '</tbody></table> ';
  htmls += '<input type="hidden" value="'+shopid+'" name="shopid"> ';
	htmls += '<input type="button" value="确认提交" class="button" id="buttonsubyj" ></form>';
  art.dialog({
    id: 'testID4',
    title:'设置'+shopname+'佣金',
    content: htmls
  });
} 
$('#buttonsubyj').live('click',function(){ 
	$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/savesetshopyjin/datatype/json"),$_smarty_tpl);?>
', $('#subyj').serialize() ,function (data, textStatus){  
			if(data.error == false){
     	  	diasucces('操作成功','');
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
$("input[name='pxinput']").live("change", function() {   
	$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/adminshoppx/datatype/json"),$_smarty_tpl);?>
', {'id':$(this).attr('data'),'pxid':$(this).val()},function (data, textStatus){  
			if(data.error == false){
     		diasucces(data.msg,newurl);
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
function doshow(shopid,shoptian){
var	htmls = '<form method="post" id="doshwoform" action="#" style="text-align:center;"><table>';
	htmls += '<tbody><tr>';
	htmls += '<td height="50px">有效天数:</td>';
	htmls += '<td> <input type="text" name="mysetclosetime" value="'+shoptian+'" style="width:100px;"></td></tr>';
	htmls += '</tbody></table> ';
  htmls += '<input type="hidden" value="'+shopid+'" name="shopid"> ';
	htmls += '<input type="button" value="确认提交" class="button" id="dosetclosetime" ></form>';
  art.dialog({
    id: 'testID3',
    title:'设置开店有效时间',
    content: htmls
  });
}
$('#dosetclosetime').live('click',function(){ 
	$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/shopactivetime/datatype/json"),$_smarty_tpl);?>
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
function setps(shopid,shopname)
{
	 	 dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=shop&module=setnotice&shopid='+shopid,{height:'180px',width:'400px'},false); 
} 	
function psset(shopid,shopname){
 
	 dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=area&module=setps&shopid='+shopid,{height:'300px',width:'700px'},false); 
} 
function bdwx(shopid){	
	var url = siteurl+'/index.php?ctrl=adminpage&action=shop&module=createwm&datatype=json&random=@random@'; 
	$.ajax({
		type: 'post',
		async:true,
		data:{'shopid':shopid},
		url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
		dataType: 'json',success: function(content) {  
			if(content.error==false){					
				var	htmls = '<p style="text-align:center;margin-bottom:10px;">检测中,请及时扫描....</p>';
					htmls += '<img src='+content.msg+' style="width:160px;height:160px;display:block;margin:0 auto;"/>';
					art.dialog({
					id: shopid,
					title:'绑定微信',
					content: htmls,
					init:function(){
						checkis_bd = false;	
						checkshopid = shopid;
						if(checkis_bd==false && checkshopid>0){
							setInterval("checkbdwx("+shopid+")",6000);
						}						
					},
					close: function(){
						checkis_bd = true;
						checkshopid = 0;
						window.location.reload();
					},   
				});				
			}else{
				if(content.error == true){
					alert(content.msg);	
				}else{
					alert(content);
				}
			} 
		},
		error: function(content) {   
			alert('数据获取失败');
		}
	});		
}
function showinfo(shopid,wxusername,wxuserlogo){							
	var	htmls = '<img src='+wxuserlogo+' style="width:80px;height:80px;margin:0 auto;display:block;"/>';
		htmls += '<p style="text-align:center;">'+wxusername+'</p>';
		htmls += '<p style="text-align:center;color:#f00;cursor: pointer;" data="'+shopid+'"   id="freebdwx">解绑</p>';
		art.dialog({
		id: 'testID3',
		title:'微信用户信息',
		content: htmls
	});
				
}
function checkbdwx(shopid){
	if(checkis_bd==false && checkshopid>0){
		var url = siteurl+'/index.php?ctrl=adminpage&action=shop&module=checkbdwx&datatype=json&random=@random@';
		$.ajax({
			type: 'post',
			async:true,
			data:{'shopid':shopid},
			url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
			dataType: 'json',success: function(content) {  
				if(content.error==false){
					if(content.msg.wxuserlogo=='' || content.msg.wxusername==''){
						checkis_bd = false;
						checkshopid = shopid;
					}else{
						var	htmls = '<img src='+content.msg.wxuserlogo+' style="width:80px;height:80px;margin:0 auto;display:block;"/>';
							htmls += '<p style="text-align:center;">'+content.msg.wxusername+'</p>';
							htmls += '<p style="text-align:center;color:#f00;cursor: pointer;" data="'+shopid+'"   id="freebdwx">解绑</p>';
						art.dialog({id:shopid}).content(htmls);
						checkis_bd = true;
						checkshopid = 0;
					}					
				}else{
					alert(content.msg);
					checkis_bd = false;
					checkshopid = shopid;
				} 	
			}
		});	
	}	
}
$('#freebdwx').live('click',function(){
	var url = siteurl+'/index.php?ctrl=adminpage&action=shop&module=updateshopbd&datatype=json&random=@random@';
	var shopid = $('#freebdwx').attr('data');
	//alert(shopid);
	//return false;
	$.ajax({
		type: 'post',
		async:true,
		data:{'shopid':shopid},
		url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
		dataType: 'json',success: function(content) {  
			if(content.error==false){
				alert(content.msg);
				window.location.reload();
			}else{
				if(content.error == true){
					alert(content.msg);	
				}else{
					alert(content);
				}
			} 
		},
		error: function(content) {   
			alert('数据获取失败');
		}
	});
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