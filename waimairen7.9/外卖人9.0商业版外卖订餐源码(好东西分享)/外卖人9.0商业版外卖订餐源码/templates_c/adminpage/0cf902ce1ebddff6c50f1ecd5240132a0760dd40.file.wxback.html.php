<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:17:22
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\weixin\wxback.html" */ ?>
<?php /*%%SmartyHeaderCode:225725cd63ec2ca9dc9-09310528%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0cf902ce1ebddff6c50f1ecd5240132a0760dd40' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\weixin\\wxback.html',
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
  'nocache_hash' => '225725cd63ec2ca9dc9-09310528',
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
  'unifunc' => 'content_5cd63ec2da8bf9_82808047',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63ec2da8bf9_82808047')) {function content_5cd63ec2da8bf9_82808047($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">微信回复列表</div>
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

                     
                        <th align="center">id</th>
                         <th align="center">咨询码</th>
                         <th align="center">类型</th>
                       
                        <th align="center">操作</th>

                      </tr>

                    </thead> 

                     <tbody>

                      <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?> 
                      <tr class="s_out" onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">  
                        <td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
</td> 
                         <td align="center"><?php echo $_smarty_tpl->tpl_vars['items']->value['code'];?>
</td> 
                        <td align="center"> <?php if ($_smarty_tpl->tpl_vars['items']->value['msgtype']==1){?>连接<?php }elseif($_smarty_tpl->tpl_vars['items']->value['msgtype']==2){?> 文本  <?php }else{ ?>图文<?php }?>  </td> 
                     
                        <td align="center">
                        	<a href="javascript:void(0);" onclick="editinfo(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
);" ><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></a> 
                        	<a onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/delwxback/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/datatype/json"),$_smarty_tpl);?>
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
                 	     
                 	        <input type="button" name="button" id="typebutton" value="添加回复" onclick="addwxmenu();" class="button" />
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
	var dialogs;
	function addwxmenu(){
	  var htmls = template.render('addwx', {list:''});
  	dialogs = art.dialog({
       id: 'fdstestid',
       title:'添加菜单',
       content:htmls
    });
     //设置显示与否
     var checkvalue = $("input[name='types']:checked").val();
     if(checkvalue == 'click'){
     	  $('#valuestr').hide();
     	  $('#textareatr').show();
     }
	}
	$('input[name="msgtype"]').live("click", function() {   
		   var checkvalue = $("input[name='msgtype']:checked").val();
      if(checkvalue == '1'){
     	  $('#lj_show').show();
     	  $('#wb_show').hide();
     	   $('#nb_show').hide();
     	   $('#tw_btn').hide();
       }else{
       	  if(checkvalue == '2'){
       	  	$('#lj_show').hide();
     	  $('#wb_show').show();
     	   $('#nb_show').hide();
     	   $('#tw_btn').hide();
       	  }else{
       	  	$('#lj_show').hide();
     	  $('#wb_show').hide();
     	   $('#nb_show').show();
     	   $('#tw_btn').show();
       	  }
     	 
       }
	});
	function editinfo(backid){
		var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/getwxback/datatype/json/random/@random@"),$_smarty_tpl);?>
';
		$.ajax({
     type: 'post',//2个参数间是用,分割
     async:false,//如果是true将不能作为返回值使用
     data:{id:backid},//表单序列化  也可以{'username':'admin',password:'password1'},
     url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
     dataType: 'json',success: function(content) {   //datatype 可以是json html   data 3种
     	    //  content;//是返回的数据内容
     	    if(content.error ==  false){
     	    	 var htmls = template.render('addwx', {list:content.msg});
  	dialogs = art.dialog({
       id: 'fdstestid',
       title:'编辑菜单',
       content:htmls
    });
     	    }else{
     	      alert(content.msg);
     	    } 
	   },
    error: function(content) { 
    	    // 提交失败
    	    alert('获取失败');
	  }
    });
	}
	function addtwnr(){
		 
			var htmls = template.render('tw_nblist_s', {});
		   $('#tw_nblist').append(htmls);
		}
  function trremove(obj){
    $(obj).parent().parent().parent().remove();
  }
	 
</script>
<script id="tw_nblist_s" type="text/html">   
 <table style="border-top:1px solid #c4d9e9;border-bottom:1px solid #c4d9e9;width:300px;"">
         <tr><td style="height:30px;width:30%;"> 标题 </td><td> <input type="text" name="biaoti[]" value="" class="skey" style="width:150px;">  </td>          </tr>
        <tr><td> 描述 </td><td> <textarea name="miaoshu[]"  style="width:200px;height:100px;"></textarea>  </td>          </tr>
         <tr><td> 图片 </td><td> <input type="text" name="tupian[]" value="" class="skey" style="width:150px;">   <input type="button" name="shangc" value="上传" onclick="uploads(this);" > </td>       </tr>
        <tr><td style="height:30px;"> 连接 </td><td> <input type="text" name="lianjie[]" value="" class="skey" style="width:150px;">  <a href="javascript:void(0);" onclick="trremove(this);" >移除</a> </td>        </tr>
       </table>
 </script>  
<!--newmain 结束-->
 
 
 
 <script id="addwx" type="text/html">   
<form method="post" id="doshwoform" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/savewxback/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);" style="text-align:center;width:300px;">
    <table width=300px>
       <tr>
          <td style="width:30%;height:30px;">咨询code:</td>
          <td style="width:80%">
              <input type="text" name="code" value="<^%=list.code%^>" class="skey" style="width:150px;"> 
          </td>
      </tr> 
      <tr>
         <td style="width:30%;height:30px;">信息类型</td>
         <td>
             <input type="radio" name="msgtype" value="1" checked>连接 <input type="radio" name="msgtype" value="2" <^%if(list.msgtype == '2'){ %^> checked <^%}%^> >文本 <input type="radio" name="msgtype" value="3" <^%if(list.msgtype == '3'){ %^> checked <^%}%^> >图文信息
         </td>
     </tr> 
      
      <tr id="lj_show" <^%if(list.msgtype == '2' ||list.msgtype == '3'){ %^> style="display:none;" <^%}%^> >
         <td colspan="2">
            <table width=300px>
                <tr>
                  <td style="width:30%;height:30px;">标题:</td>
                  <td style="width:80%">
                              <input type="text" name="lj_title" value="<^%if(list.msgtype == '1'){ %^><^%=list.listcontent.lj_title%^><^%}%^>" class="skey" style="width:150px;"> 
                  </td>
                </tr> 
                
                <tr>
                  <td style="width:30%;height:30px;">连接:</td>
                  <td style="width:80%">
                              <input type="text" name="lj_link" value="<^%if(list.msgtype == '1'){ %^><^%=list.listcontent.lj_link%^><^%}%^>" class="skey" style="width:150px;"> 
                  </td>
                </tr> 
          </table>
         </td>
      </tr>
      <tr id="wb_show"  <^%if(list.msgtype == '2' ){ %^>  <^%}else{%^> style="display:none;" <^%}%^>   >
         <td colspan="2">
            <table width=300px>
                <tr>
                  <td style="width:30%;">内容:</td>
                  <td style="width:80%">
                           <textarea name="wb_content" style="width:200px;height:100px;"><^%if(list.msgtype == '2'){ %^><^%=list.values%^><^%}%^></textarea>
                  </td>
                </tr> 
                </table>
         
         </td>
      </tr>
      <tr id="nb_show"   <^%if(list.msgtype == '3' ){ %^>  <^%}else{%^> style="display:none;" <^%}%^>  >
        <td colspan="2" id="tw_nblist"> 
        
   <^%if(list.msgtype ==3){ %^> 
   	     <^% var imglist = list.listcontent%^> 
          <^%for(i = 0; i < imglist.length; i ++) {%^>
       <table style="border-top:1px solid #c4d9e9;border-bottom:1px solid #c4d9e9;width:300px;"">
         <tr><td style="height:30px;width:30%;"> 标题 </td><td> <input type="text" name="biaoti[]" value="<^%=imglist[i].biaoti%^>" class="skey" style="width:150px;">  </td>          </tr>
        <tr><td> 描述 </td><td> <textarea name="miaoshu[]"  style="width:200px;height:100px;"> <^%=imglist[i].miaoshu%^> </textarea>  </td>          </tr>
         <tr><td> 图片 </td><td> <input type="text" name="tupian[]" value="<^%=imglist[i].tupian%^>" class="skey" style="width:150px;"> <input type="button" name="shangc" value="上传" onclick="uploads(this);" ></td>          </tr>
        <tr><td style="height:30px;"> 连接 </td><td> <input type="text" name="lianjie[]" value="<^%=imglist[i].lianjie%^>" class="skey" style="width:150px;"> <a href="javascript:void(0);" onclick="trremove(this);" >移除</a></td>          </tr>
       </table>
          <^%}%^> 
    <^%}%^>
    
     
    
    </td>
      </tr>
      
      
      <tr id="tw_btn" <^%if(list.msgtype ==3){ %^>  <^%}else{%^> style="display:none;" <^%}%^>> 
       <td >&nbsp;</td>
       <td style="height:40px;"><input type="button" name="name" value="添加图文组" onclick="addtwnr();" > </td>
       </tr>
       
       
       
       
     
       <tr><td style="width:30%;height:30px;">&nbsp; </td>
       <input type="hidden" name="id" value="<^%=list.id%^>">
       <td><input type="submit" value="确认提交" class="button"></td>
       </tr>
    </talbe>

</form>
</script> 
<script>
	var updilog ;
 function uploads(obj){
 	  var findobj = $('input[name="shangc"]').index($(obj));
 	  findobj = Number(findobj)+1;
 	  var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/obj/@obj@/func/uploadsucess/datatype/json/randowm/@random@"),$_smarty_tpl);?>
';
 	  url = url.replace('@random@', 1+Math.round(Math.random()*1000)).replace('@obj@',findobj); 
 	  updilog = art.dialog.open(url);
 	  updilog.title('上传图片'); 
 }
  function uploadsucess(flag,objdata,linkurl){
 	 if(flag == true){
 	   alert(msg); 
	 
 	 }else{ 
 		var findobj = Number(objdata)-1;
    	var nwlink =  linkurl;
 	  $('input[name="tupian[]"]').eq(findobj).val(nwlink);
 	   updilog.close(); 
 
   }
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