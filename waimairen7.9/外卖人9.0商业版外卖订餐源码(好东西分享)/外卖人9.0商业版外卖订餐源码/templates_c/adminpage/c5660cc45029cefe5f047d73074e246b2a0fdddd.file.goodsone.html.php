<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:24:36
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\shop\goodsone.html" */ ?>
<?php /*%%SmartyHeaderCode:79495cd55f7421c002-70923181%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5660cc45029cefe5f047d73074e246b2a0fdddd' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\shop\\goodsone.html',
      1 => 1536024620,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '79495cd55f7421c002-70923181',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'siteurl' => 0,
    'tempdir' => 0,
    'goodsinfo' => 0,
    'listtype' => 0,
    'items' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd55f742f8d00_02492502',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f742f8d00_02492502')) {function content_5cd55f742f8d00_02492502($_smarty_tpl) {?><html xmlns="http://www.w3.org/1999/xhtml"><head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<meta http-equiv="Content-Language" content="zh-CN"> 
<meta content="all" name="robots"> 
<meta name="description" content=""> 
<meta content="" name="keywords"> 
<title></title>  
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/ajaxdialog.css" />
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquerynew.js" type="text/javascript" language="javascript"></script>   
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/js/kindeditor/kindeditor.js" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/ajaxfileupload.js"> </script>
</head>
<body >
	<div class="content">
		<form id="kloginForm" method="post" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/savegoodsall"),$_smarty_tpl);?>
" >
		 <div class="goodsbase">
		    <div class="baseinfo">基本信息：</div>	
		    <div class="basetext"> <p><?php echo $_smarty_tpl->tpl_vars['goodsinfo']->value['name'];?>
</p><p>单价:<?php echo $_smarty_tpl->tpl_vars['goodsinfo']->value['cost'];?>
</p></div> 
		    <div class="imgdiv">
		    	
		    	
		    	
		    	
		    </div>
		 </div>
				 
		 <div class="hangtiao clr">
		    <div class="labeltext">所在分类：</div>	
		    <div class="inputtext">
		    	<div class="showdiv">
		    	    <select name="typeid">
		    	   	  <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['listtype']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
		    	   	  <option value="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['goodsinfo']->value['typeid']==$_smarty_tpl->tpl_vars['items']->value['id']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</option>
		    	   	  <?php } ?>
		    	   	</select> 
		    	</div>
		    </div> 
		 </div>
		 
		  <div class="hangtiao clr" style="height:195px;">
		    <script>KE.show({id:'instro',allowFileManager : true,imageUploadJson:'<?php echo FUNC_function(array('type'=>'url','link'=>"/other/saveupload/uploaddir/goodspub"),$_smarty_tpl);?>
',fileManagerJson:'<?php echo FUNC_function(array('type'=>'url','link'=>"/other/saveupload/uploaddir/goodspub"),$_smarty_tpl);?>
',items:['source','|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', '|', 'fontname', 'fontsize', '|', 'textcolor', 'bgcolor', 'bold','italic', 'underline', 'removeformat', '|', 'image', 'advtable', 'hr','link', 'unlink']});</script><textarea name="instro" id="instro" style='width:970px; height:190px;'><?php echo $_smarty_tpl->tpl_vars['goodsinfo']->value['instro'];?>
</textarea>
		 </div>
		 <input type="hidden" name="gid" value="<?php echo $_smarty_tpl->tpl_vars['goodsinfo']->value['id'];?>
">
		 <div class="hangtiao clr">
		  
		    <input type="submit" value="确认提交" class="labeltext" style="background-color:#ec7501;text-align:center;cursor: pointer;">  
		     
		 </div>
		 
		 
		
	</form>
</div>
	
<div id="newimg">
		        	<div class="file_img" style="height:136px;width:160px;">
                        	 <img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['goodsinfo']->value['img'])),$_smarty_tpl);?>
 width="136" height="130" id="imgshow" <?php if (empty($_smarty_tpl->tpl_vars['goodsinfo']->value['img'])){?> style="display:none;"<?php }?>>  
               </div>
                <div class="file_xxiang">
                        	<input type="hidden" name="goodsimg" id="goodsimg" value="<?php echo $_smarty_tpl->tpl_vars['goodsinfo']->value['img'];?>
" class="skey" style="width:130px;" > 
                     <div id="div-headpicUpload" style="display:block;"> 
		                  <form id="form1" name="form1" method="post"  enctype="multipart/form-data" target="ifr-headpic-upload" onsubmit="return checkImagetype('1');">    
		                  	<div class=""> 
		               	      	<input name="head" type="file" id="imgFile" style="width:68px;float:left;" name="imgFile" onchange="$('#input1').val($(this).val())"  class="curbtn">
		               		     <input id="submitImg" type="button" value="上传" class="ss_sc curbtn" style="width:40px;float:left; border:1px solid #ccc;background-color:white;height:22px;line-height:12px;margin-top:5px;margin-left:5px;" > 
		               		     <input type="button" id="imgdel" value="删除" class="ss_sc curbtn" <?php if (empty($_smarty_tpl->tpl_vars['goodsinfo']->value['img'])){?> style="display:none;"<?php }?>>
		               		     <input type="button" id="xuanzekuimg" value="从商品库中选择" class="ss_sc curbtn" >
		               	    </div>  
		               </form> 
		             </div>
		           </div>
		    </div> 
			
			
				
		
	<div id="shangpinku" style="width:700px; height:500px;display:none; position:fixed; top:50%; left:50%; margin-top:-250px; margin-left:-350px; background:#fff;">
		<div class="shopSetTop" style="">
              <A href="/photo/index.php">	<span style="color: #000;  font-weight: bold;">从商品库中选择商品图片</span></A>
          
            </div>

	<DIV id=content style="background: url(/templates/adminpage/public/shopcenter/images/fenleiBg.png) repeat; padding:20px 40px;width:930px;">

		<<?php ?>?php showImgList($imgArr, $_GET['page'], $pageSize);?<?php ?>>
	</DIV>

	</div>
	
	
	
<script>
  $('#tijiaofrom').click(function(){
  	 $('#kloginForm').submit();
  });
	$('#submitImg').click(function(){
		ajaxFileUpload();
	});
	$('#imgdel').click(function(){
		var newlink = '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/delgoodsimg/id/".((string)$_smarty_tpl->tpl_vars['goodsinfo']->value['id'])."/datatype/json/random/@random@"),$_smarty_tpl);?>
';
		   $.ajax({
     type: 'get',
     async:false, 
     url: newlink.replace('@random@', 1+Math.round(Math.random()*1000)), 
     dataType: 'json',success: function(content) {  
     	if(content.error == false){ 
     		  $('#goodsimg').val('');
 	             $('#imgshow').attr('src','');
 	                $('#imgshow').hide(); 
 	                $('#imgdel').hide();
     	}else{
     		if(content.error == true)
     		{
     		  alert(content.msg);
     		}else{
     			diaerror(content); 
     		}
     	} 
		},
    error: function(content) { 
       alert('调用文件失败');
	  }
   }); 
		  
		  
		  
		 
	});
	function ajaxFileUpload()
	{
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});
  
		$.ajaxFileUpload
		(
			{
				url:'<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/userupload/datatype/json"),$_smarty_tpl);?>
',
				secureuri:false,
				fileElementId:'imgFile',
				dataType: 'json',
				data:{'gid':'<?php echo $_smarty_tpl->tpl_vars['goodsinfo']->value['id'];?>
'},
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error == false)
						{ 
						 
							$('#goodsimg').val(data.msg);
 	             $('#imgshow').attr('src',data.msg);
 	                $('#imgshow').show(); 
 	                $('#imgdel').show();
						}else
						{
							alert(data.msg);
						}
					}else{
					  alert(data);
					}
				},
				error: function (data, status, e)
				{
					alert(e);
				}
			}
		)
		
		return false;

	}
	
	$("#xuanzekuimg").click(function(){
	
		//&gid= 
		window.location.href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/selectmarketimg/gid/".((string)$_smarty_tpl->tpl_vars['goodsinfo']->value['id'])),$_smarty_tpl);?>
";			
	
	});
	</script>

</body>
</html>
 
  
 
 
 
 
 
 
 
 
 <?php }} ?>