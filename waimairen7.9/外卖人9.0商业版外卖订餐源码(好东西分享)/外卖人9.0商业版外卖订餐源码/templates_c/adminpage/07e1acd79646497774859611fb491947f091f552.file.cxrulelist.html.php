<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 21:53:03
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\cxrulelist.html" */ ?>
<?php /*%%SmartyHeaderCode:25035cd5823f4626e8-40206223%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '07e1acd79646497774859611fb491947f091f552' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\cxrulelist.html',
      1 => 1536024619,
      2 => 'file',
    ),
    '2094f96c9c9581c454e41795ec561919d55c8dcb' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin4.html',
      1 => 1538873456,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25035cd5823f4626e8-40206223',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'siteurl' => 0,
    'is_static' => 0,
    'type' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd5823f5f4ef7_79318339',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5823f5f4ef7_79318339')) {function content_5cd5823f5f4ef7_79318339($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
?>﻿ <html xmlns="http://www.w3.org/1999/xhtml"><head> 
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
/public/js/public1.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=wmrPopup"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js?v=9.0" type="text/javascript" language="javascript"></script>


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
	width: 90px;
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
 
   <div class="newmain_all">
   	  
 <div class="right_content">
	<div class="show_content_m">
   	        	 <div class="show_content_m_ti">
   	        	 	   
					   <div class="showtop_t" id="positionname">
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['type']->value==0){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/cxrulelist/type/0"),$_smarty_tpl);?>
">全部活动</a>
						  </div>						  
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['type']->value==1){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/cxrulelist/type/1"),$_smarty_tpl);?>
">待生效</a>
						  </div>	
                          <div class="navs <?php if ($_smarty_tpl->tpl_vars['type']->value==2){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/cxrulelist/type/2"),$_smarty_tpl);?>
">进行中</a>
						  </div>						  
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['type']->value==3){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/cxrulelist/type/3"),$_smarty_tpl);?>
">已结束</a>
						  </div>	
                          <div class="navs <?php if ($_smarty_tpl->tpl_vars['type']->value==4){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/cxrulelist/type/4"),$_smarty_tpl);?>
">未启用</a>
						  </div>							  
					   </div>
					   
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
<style>
#close{text-align: center;  color: #f5f5f5;background-color: red;font-weight: bold; position: absolute;top: -10px;right: -10px; width: 25px;height: 25px;border-radius: 20px;line-height: 25px;}
.search_content div{display:inline-block}
#addact{background: #169bd5;padding: 5px 21px;margin-top: 8px;border-radius: 5px;color: #fff;}
</style>
<script>
$('#actsm').live('click',function(){
    $('#actcon').toggle();
})
$('#close').live('click',function(){
    $('#actcon').hide();
})
</script>
    <div style="width:auto;overflow-x:hidden;overflow-y:auto">
       <div id='actcon' style ='width: 400px;position: absolute;top: 300px;left: 43%;background-color: #fff;box-shadow: 6px 6px 20px #888888;display:none'>
	   <div id='close' style='cursor:pointer'>X</div>
	   <div style='padding: 20px 20px 20px 20px;color:#000;font-size:12px;text-align:left'>用户下单计算优惠金额时，促销规则按满赠、满减、折扣、免配送费、首单立减类型顺序依次使用计算，每个规则的金额满足条件都以最初商品总价+打包费为基准。	计算下个规则产生的优惠金额时以上个规则优惠后的金额为基准，同类型的规则取优惠金额最大的使用。首单立减类型优惠与其他满减类型优惠不可共享，后台添加优惠活动时，同类型的平台优惠活动每个商家只能被勾选一次。</div>
       </div>
	   <div class="search" style='height: 50px; line-height: 25px;'>
            <div class="search_content" style='font-size:12px'>
			<a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/addcxrule"),$_smarty_tpl);?>
"><div id='addact'>添加活动</div></a>
			<div id='actsm' style='color: #169bd5;margin-left: 25px;cursor:pointer'>优惠规则说明</div>
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
                        <th align="center">活动类型</th>
                        <th align="center">活动规则</th>                       
                        <th align="center">活动时间</th>                        
                        <th align="center">状态</th>
                        <th align="center">操作</th>

                      </tr>

                    </thead>

                     <tbody>
                      
                    <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cxrulelist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>
                      <tr class="s_out" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">                         
						<td align="center"> 
						<?php if ($_smarty_tpl->tpl_vars['items']->value['controltype']==1){?>满赠活动<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['items']->value['controltype']==2){?>满减活动<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['items']->value['controltype']==3){?>折扣活动<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['items']->value['controltype']==4){?>免配送费<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['items']->value['controltype']==5){?>首单立减<?php }?>
						</td>
                        <td align="center"> <span id="showrule_<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
" data=""></span> </td>
                        <td align="center">
						
						<?php if ($_smarty_tpl->tpl_vars['items']->value['limittype']==1){?>不限制<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['items']->value['limittype']==2){?>每周(星期<?php echo $_smarty_tpl->tpl_vars['items']->value['limittime'];?>
)<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['items']->value['limittype']==3){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['items']->value['starttime'],"Y-m-d H:i:s");?>
 至 <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['items']->value['endtime'],"Y-m-d H:i:s");?>
<?php }?>
						</td>	
                        <td align="center">						
						<?php if ($_smarty_tpl->tpl_vars['items']->value['status']==0){?>
						未启用
						<?php }else{ ?>
							<?php if ($_smarty_tpl->tpl_vars['items']->value['limittype']==3&&$_smarty_tpl->tpl_vars['items']->value['starttime']>$_smarty_tpl->tpl_vars['nowtime']->value){?>待生效<?php }?>	
							<?php if ($_smarty_tpl->tpl_vars['items']->value['limittype']<3||($_smarty_tpl->tpl_vars['items']->value['limittype']==3&&$_smarty_tpl->tpl_vars['items']->value['endtime']>$_smarty_tpl->tpl_vars['nowtime']->value&&$_smarty_tpl->tpl_vars['items']->value['starttime']<$_smarty_tpl->tpl_vars['nowtime']->value)){?>进行中<?php }?>	
							<?php if ($_smarty_tpl->tpl_vars['items']->value['limittype']==3&&$_smarty_tpl->tpl_vars['items']->value['endtime']<$_smarty_tpl->tpl_vars['nowtime']->value){?>已结束<?php }?>							 
						<?php }?></td> 
			            <td align="center">
                            <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/addcxrule/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])),$_smarty_tpl);?>
"><img style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></a> 
                             <a onclick="return remind(this);"href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/card/module/delcxrule/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/datatype/json"),$_smarty_tpl);?>
"> <img style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/del.jpg"></a> 
                        </td> 
                                               
                      </tr>
                    <?php } ?>
				    
                    </tbody>

                  </table>
                <?php if (empty($_smarty_tpl->tpl_vars['cxrulelist']->value)){?><p style='text-align:center;margin-top: 20px;font-size: 14px;'>暂无满足条件的数据~~~</p><?php }?>
                <div class="blank20" style="padding-left: 20px;"><font style="color:red;"></font></div>

                </form>
                 </div>
 
                <div class="blank20"></div>

              </div>

            </div>

          </div>

        </div>


  </div>

</div>
<script>
	var alllist = <?php echo json_encode($_smarty_tpl->tpl_vars['cxrulelist']->value);?>
;
$(function(){  
	$.each(alllist,function(i,field){
         
		var showcontent = '';	
		if(field.supporttype == 2){
			showcontent1 ='在线支付满';
		}else{
			showcontent1 ='订单满';
		} 		 
		if(field.controltype ==  1){
			showcontent +=showcontent1+''+field.limitcontent+'赠送'+field.presenttitle;
		}
		if(field.controltype ==  2){
			showcontent +=field.name;
		}
		if(field.controltype ==  3){
			var times = field.controlcontent;
			showcontent +=showcontent1+''+field.limitcontent+'享受'+times+'折优惠';
		}
		if(field.controltype ==  4){
			showcontent +=showcontent1+''+field.limitcontent+'免基础配送费';
		}
		if(field.controltype ==  5){
			showcontent +='新用户下单满'+field.limitcontent+'立减'+field.controlcontent+'元';
		}
		 
		
		 $('#showrule_'+field.id).text(showcontent);
	});
	
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