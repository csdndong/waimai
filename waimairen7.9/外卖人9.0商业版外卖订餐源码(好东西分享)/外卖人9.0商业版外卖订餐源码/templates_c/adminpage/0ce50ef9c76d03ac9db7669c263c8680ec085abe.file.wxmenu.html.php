<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:17:21
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\weixin\wxmenu.html" */ ?>
<?php /*%%SmartyHeaderCode:308305cd63ec1de0f30-38923629%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0ce50ef9c76d03ac9db7669c263c8680ec085abe' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\weixin\\wxmenu.html',
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
  'nocache_hash' => '308305cd63ec1de0f30-38923629',
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
  'unifunc' => 'content_5cd63ec1ec36a9_79852874',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63ec1ec36a9_79852874')) {function content_5cd63ec1ec36a9_79852874($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
   	        	 	   <div class="showtop_t" id="positionname">微信菜单</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
   
      <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
          
          <div id="tagscontent">

            <div id="con_one_1">

              <div class="table_td" > 
              <form method="post" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/updatewxmenu/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/wxmenu"),$_smarty_tpl);?>
',this);">

                  <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">

                    <thead>

                      <tr>

                        <th align="center" width=30%<?php ?>>导航名</th>
                        <th align="center">菜单类型</th>
                        <th align="center">内容</th>
                        <th align="center">排序</th>
                        <th align="center">操作</th>  
                      </tr>

                    </thead>

                    <tbody id="type">    
                      
                        
                   

                    </tbody>      
 
 
                  </table>

                <div class="blank20"></div>

                 

               <input type="submit" name="button" id="button" value="上传菜单数据" class='button'> 

                 <input type="button" name="button" id="typebutton" value="添加菜单" onclick="addwxmenu();" class="button" /> 

              </form>
              <div class="blank20"></div>
              </div>

            </div>
          </div>
        </div>

          
    
    
    
    
    
      </div>
<script>
	var wxmenu = <?php echo json_encode($_smarty_tpl->tpl_vars['wxmenu']->value);?>
;
	var dialogs ; 
	$(function(){ 
		 $.each(wxmenu, function(i,val){  
		 	  if(val.parent_id == 0){
		 	     var htmls = template.render('shoplist', {list:val});
		 	     $('#type').append(htmls);
		 	      $.each(wxmenu, function(k,cal){  
		 	      	if(val.id == cal.parent_id){
		 	      	   var htmls = template.render('shoplist', {list:cal});
		 	          $('#type').append(htmls);
		 	         }
		 	      });
		 	  }
		 });
	});
	function addwxmenu(){
	  var htmls = template.render('addwx', {list:'',menulist:wxmenu});
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
	$('input[name="types"]').live("click", function() {   
		   var checkvalue = $("input[name='types']:checked").val();
      if(checkvalue == 'click'){
     	  $('#valuestr').hide();
     	  $('#textareatr').show();
       }else{
     	 $('#valuestr').show();
     	  $('#textareatr').hide();
       }
	});
	$('input[name="msgtype"]').live("click", function() {   
		 var checkvalue = $("input[name='msgtype']:checked").val();
		//tx_input  news_input 
		 if(checkvalue == '1'){
     	   $('#tw_nb').hide();
     	   $('#tw_wb').show();
     	   $('#tw_btn').hide();
      }else{
     	   $('#tw_nb').show();
     	   $('#tw_wb').hide();
     	   $('#tw_btn').show();
      }
	});
	function editmenu(datas){
		var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/getwxmen/datatype/json/random/@random@"),$_smarty_tpl);?>
';
		$.ajax({
     type: 'post',//2个参数间是用,分割
     async:false,//如果是true将不能作为返回值使用
     data:{id:datas},//表单序列化  也可以{'username':'admin',password:'password1'},
     url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
     dataType: 'json',success: function(content) {   //datatype 可以是json html   data 3种
     	    //  content;//是返回的数据内容
     	    if(content.error ==  false){
     	    	 var htmls = template.render('addwx', {list:content.msg,menulist:wxmenu});
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
	function editinfo(datas){
		var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/getwxmen/datatype/json/random/@random@"),$_smarty_tpl);?>
';
		$.ajax({
     type: 'post',//2个参数间是用,分割
     async:false,//如果是true将不能作为返回值使用
     data:{id:datas},//表单序列化  也可以{'username':'admin',password:'password1'},
     url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
     dataType: 'json',success: function(content) {   //datatype 可以是json html   data 3种
     	    //  content;//是返回的数据内容
     	    if(content.error ==  false){
     	    	  
     	    	  if(content.msg.type=='view'){
     	    	   var htmls = template.render('editcinfo', {list:content.msg,menulist:wxmenu});
  	           dialogs = art.dialog({
                  id: 'fdstestid',
                  title:'编辑超连接地址',
                  content:htmls
                });
              }else{//
              	var htmls = template.render('editotherc', {list:content.msg,menulist:wxmenu});
  	           dialogs = art.dialog({
                  id: 'fdstestid',
                  title:'编辑内容',
                  content:htmls
                });
              }
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
       
       
       
       
<script id="shoplist" type="text/html">   
	<tr class="s_out" onmouseover="this.bgColor='#fdfbfb';"> 
                        <td align="center" <^%if(list.parent_id == 0){%^>style='font-weight:bold;font-size:15px' <^%}%^> >
                        <^%if(list.parent_id > 0){%^>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <^%}%^> <^%=list.name%^>
                        </td>
                        <td align="center" >
                            <^%if(list.type=='view') {%^>跳转连接<^%}else{%^>回复信息<^%}%^>
                        </td>
                        <td align="center" >
                              <^%if(list.type=='view') {%^>  <a onclick="editinfo(<^%=list.id%^>);" href="javascript:void(0);">编辑连接</a><^%}else{%^>  <a  onclick="editinfo(<^%=list.id%^>);" href="javascript:void(0);">编辑内容</a><^%}%^>
                        </td>
                         <td align="center" >
                          <^%=list.sort%^>
                        </td>
                        <td align="center"> 
                        	<a onclick="editmenu(<^%=list.id%^>);" href="javascript:;"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></a> 
                        	<a onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/delwxmenu/id/<^%=list.id%^>/datatype/json"),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/del.jpg"></a>
                        </td>
     </tr>
</script>
<script id="addwx" type="text/html">   
<form method="post" id="doshwoform" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/savewxmenu/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);" style="text-align:center;width:300px;">
    <table width=300px>
       <tr>
          <td style="width:30%;height:30px;">导航名:</td>
          <td style="width:80%">
              <input type="text" name="name" value="<^%=list.name%^>" class="skey" style="width:150px;"> 
          </td>
      </tr>
       <tr>
          <td style="width:30%;height:30px;">code:</td>
          <td style="width:80%">
              <input type="text" name="code" value="<^%=list.code%^>" class="skey" style="width:150px;"> 
          </td>
      </tr> 
       <tr>
         <td style="width:30%;height:30px;">所在分类</td>
         <td>
            <select name="parent_id">
                <option value="0">顶级分类</option>
               <^%for(var i=0;i<menulist.length;i++){%^>
               	   <^% if(menulist[i].parent_id == 0){%^>
               	      	<^%if(list.id != menulist[i].id){ %^> 
               	   	     <option value="<^%=menulist[i].id%^>"  <^%if(list.parent_id == menulist[i].id){ %^> selected <^%}%^> ><^%=menulist[i].name%^></option>
               	   	       <^%}%^>
               	  <^%}%^>
              <^%}%^>
            </select>
         </td>
       </tr>
      <tr>
         <td style="width:30%;height:30px;">类型</td>
         <td>
             <input type="radio" name="types" value="view" checked>跳转连接  <input type="radio" name="types" value="click" <^%if(list.type == 'click'){ %^> checked <^%}%^> >自定义信息
         </td>
     </tr> 
      <tr>
         <td>排序ID</td>
         <td>
           <input type="text" name="sort" value="<^%=list.sort%^>" class="skey" style="width:30px;">
         </td>
       </tr>
       <tr><td style="width:30%;height:30px;">&nbsp; </td>
       <input type="hidden" name="id" value="<^%=list.id%^>">
       <td><input type="submit" value="确认提交" class="button"></td>
       </tr>
    </talbe>

</form>
</script>
<script id="editcinfo" type="text/html">   
<form method="post" id="doshwoform" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/savewxmenucontent/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);" style="text-align:center;width:300px;">
 <table width=300px>
 <tr> 
 <td style="width:30%;">
      地址： <input type="hidden" name="msgtype" value="0">
       </td>
   <td style="width:70%;"><input type="text" name="values" value="<^%=list.msglist.lj_link%^>" class="skey" style="width:200px;"> </td>
       </tr>
 <tr> 
 <tr> 
        <td style="width:30%;">连接描述:</td>
      <td style="width:70%;"><input type="text" name="miaoshu" value="<^%=list.msglist.lj_title%^>" class="skey" style="width:200px;"> </td>
       </tr>
 <tr> 
  <td style="width:30%;">&nbsp;</td>
       <input type="hidden" name="id" value="<^%=list.id%^>">
       <td style="width:70%;"><input type="submit" value="确认提交" class="button"></td>
       </tr>
 </talbe>
</form>
</script>
<script id="editotherc" type="text/html">   
<form method="post" id="doshwoform" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/weixin/module/savewxmenucontent/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);" style="text-align:center;width:300px;">
 <table width=300px>
 <tr> 
       <td >消息类型</td>
       <td><input type="radio" name="msgtype" value="1" checked>文本<input type="radio" name="msgtype" value="2" <^%if(list.msgtype ==2){ %^> checked <^%}%^>>图文</td>
       </tr>
<tr id="tw_wb" <^%if(list.msgtype !=1){ %^> style="display:none;" <^%}%^>> 
   <td >文本内容</td>
    <td><textarea name="wb_content" style="width:200px;height:100px;"><^%if(list.msgtype ==1){ %^><^%=list.values%^>  <^%}%^> </textarea></td>
 </tr>
 <tr id="tw_nb" <^%if(list.msgtype ==1){ %^> style="display:none;" <^%}%^>> 
   <td colspan="2" id="tw_nblist"> 
   <^%if(list.msgtype ==2){ %^> 
   	     <^% var imglist = list.msglist%^> 
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



 <tr id="tw_btn" <^%if(list.msgtype ==1){ %^> style="display:none;" <^%}%^>> 
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
 	  var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucess/obj/@obj@/randowm/@random@"),$_smarty_tpl);?>
';
 	  url = url.replace('@random@', 1+Math.round(Math.random()*1000)).replace('@obj@',findobj); 
 	  updilog = art.dialog.open(url);
 	  updilog.title('上传图片'); 
 }
  function uploadsucess(flag,obj,linkurl){
 	 if(flag == false){
 	  var findobj = Number(obj)-1;
    	var nwlink = siteurl+linkurl;
   	$('input[name="tupian[]"]').eq(findobj).val(nwlink);
 	    updilog.close();  
 	 }else{ 
 	   alert(linkurl);
   }
 }  
 /*
 function uploadsucess(objdata,inkurl){
 	var findobj = Number(objdata)-1;
 	var nwlink = siteurl+inkurl;
 	$('input[name="tupian[]"]').eq(findobj).val(nwlink);
 	updilog.close();  
}*/
function uploaderror(msg){
	 alert(msg); 
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