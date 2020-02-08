<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:24:26
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\shop\goodslibrary.html" */ ?>
<?php /*%%SmartyHeaderCode:127285cd55f6a519843-12197528%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '08aea5478517101965f2f5ed8eadc47652ccb9fc' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\shop\\goodslibrary.html',
      1 => 1536024620,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '127285cd55f6a519843-12197528',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'siteurl' => 0,
    'tempdir' => 0,
    'list' => 0,
    'items' => 0,
    'ite' => 0,
    'toplink' => 0,
    'sitename' => 0,
    'beian' => 0,
    'footerdata' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd55f6a66a397_32469919',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f6a66a397_32469919')) {function content_5cd55f6a66a397_32469919($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>商品库管理后台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/commom.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/shangjiaAdmin.css" />
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=blue"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js" type="text/javascript" language="javascript"></script>


<link rel="stylesheet" type="text/css" href="/photo/sysimg/css.css" />
<link rel="stylesheet" type="text/css" href="/photo/js/jquery.lightbox.css" />

<script> 
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
"; 
</script>
</head>
<body>
	<div style="position: fixed;top: 0;left: 0;right: 0;bottom: 0;z-index: -1;background:url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/background.png);background-size:cover;"></div>
<!---header start--->
<div class="header" style=" height:50px;">
  <div class="top" style=" height:50px;">
      <div class="topLeft fl">
            	<ul style="padding:0px;">
                 <li style="width:80px;padding:0px;margin:0px;height:50px;list-style: none; line-height:50px;"><a style="color: #27A9E3;  font-size: 16px;  font-weight: bold;" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/goodslibrary"),$_smarty_tpl);?>
">商品管理</a></li>
                 <li style="width:80px;padding:0px;margin:0px;height:50px;list-style: none;  line-height:50px;"><a  href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/swfupload"),$_smarty_tpl);?>
">上传图库</a></li>
                 <li style="width:80px;padding:0px;margin:0px;height:50px;list-style: none;  line-height:50px;"><a  href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/showimglist"),$_smarty_tpl);?>
">图库预览</a></li>
                </ul>
                 <div class="cl"></div>
            </div>
   
    <div class="topRight fr">  <span style=" height:50px; line-height:50px;cursor: pointer;" class="username" onclick="openlink('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage"),$_smarty_tpl);?>
');">返回后台管理<img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/usernameBg.png" /></span> </div>
    <div class="cl"></div>
    <div class="shangjiaTop" style=" top:-22px; margin-left:-150px;">
      <div class="sjglaa" style="background: rgba(0, 0, 0, 0) url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/goodslibtopbg.png) no-repeat scroll 0 0;"> </div>
    </div>
  </div>
</div>

<!---header end---> 
	

<div class="content">
	
	
	
  <!---content right start---> 
        <div class="conWaiSet fr">
        	
            <div class="shopSetTop">
            	<span>菜单设置</span>
            </div>
            
            <div class="orderset">
			
			<div style="padding:35px; float:left;">
			   <a style=" background:#ec7501; padding:10px; color:#fff; " href="#" onclick="doinputexcel();">Excel 快速导入商品</a>
			</div>
			<div style="padding:25px;float:left;">
<input type="text"  placeholder="快速搜索商品" id="bldsearch"  value="" style="padding:0px 10px;width:250px; height:33px; line-height:33px;"/>
			</div>
			
				<script>
		
		
							//输入即使搜索商品
									$("#bldsearch").keyup(function(){
											
											var  name= $('#bldsearch').val();
										//$(".goodlist").hide();
											 
											 name = escape(name);
											// alert(name);
											var templist = $('.div_orderList').find('.order_list');
											for(var i=0;i<$(templist).length;i++){
											   var checkstr = $(templist).eq(i).attr('goodname');
											 
											   checkstr = escape(checkstr); 
											   if(checkstr.indexOf(name) < 0){
											      $(templist).eq(i).hide();
											   }else{
											   //还需要检测 是否是显示分类的;											   
												/* 	if( $(templist).eq(i).is(":visible") ){
													alert(1);
													$(templist).eq(i).show();
													}
													
													if( $(templist).eq(i).is(":hidden") ){
														alert(0);
														$(templist).eq(i).hide();
													}
													*/
												$(templist).eq(i).show();		
													
											   }
											}
											 
											
											
											
											
											//var url = siteurl+'/index.php?ctrl=wxsite&action=shopshow&id='+shopid+'&shopsearch='+name;
																		
										   // location.href=url;
										   
										
										   
								
									});
		
		</script>
			
			<div style="clear:both;"></div>
            	<div class="addFenlei">
                <span class="fl">
                	<input type="text" id="shoptypename" name="shoptypename" value=""  /></span>
                    <div class="addButton curbtn fl"  id="add_FoodType"></div>
                </div>
                
                  <div class="cl"></div>
            </div>
             <div class="cl"></div>
                       
                <form action="" method="post">
                 <div class="caidanSet">
        			<div style="    background: #544e48;">
                    <div class="caidanTitle" style="height:auto; line-height:42px;">
                	<ul>
                		<?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>  
                    	<li  data="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
"  dataname="<?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
" dataorder="<?php echo $_smarty_tpl->tpl_vars['items']->value['orderid'];?>
"><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</li> 
                     <?php } ?>
                    </ul>
                    <div class="editGtype" id="editGtype">
                         <div class="delGDtype curbtn" ></div>
                         <div class="editGDtype curbtn"></div>
                    
                    </div>
                </div></div>
                <div style="clear:both;"></div>
                	<div class="div_orderList">
                    	
                        <div class="div_orderList_show">
                    
                        <div class="orderList_show_goodli">
                            <div class="ord_goodname">
                                <span>食品名称</span>
                            </div>
                            <div class="ord_price">
                                 <span>价格（元）</span>
                            </div>
                         
							<div class="ord_day_order" >
                                 <span>排序</span>
                            </div>
                             <div class="order_caozuo">
                                 <span>操作</span>
                            </div>
                        </div>
                        
                        <div class="cl"></div>
                        <style>
						.ord_day_order{height: 67px;
float: left;
line-height: 67px;
font-size: 14px;
color: #aaaaaa;
font-family: "宋体";
font-weight: 700;
text-align: center;
border-right: 1px solid #45423e;
border-left: 1px solid #4c4a48;
}

.cd_order{height: 50px;
float: left;
line-height: 50px;
font-size: 14px;
color: #c3c3c3;
font-family: "宋体";
text-align: center;
border-right: 1px solid #4c4a48;}
						</style>
                      <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>  
                      <?php  $_smarty_tpl->tpl_vars['ite'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ite']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value['det']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ite']->key => $_smarty_tpl->tpl_vars['ite']->value){
$_smarty_tpl->tpl_vars['ite']->_loop = true;
?>  
                    	<div class="order_list	listgoodsdet goodsdiv_<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
" goodname="<?php echo $_smarty_tpl->tpl_vars['ite']->value['name'];?>
" data="<?php echo $_smarty_tpl->tpl_vars['ite']->value['id'];?>
" id="goodstr_<?php echo $_smarty_tpl->tpl_vars['ite']->value['id'];?>
" >
                       		 <div class="order_goodlist">
                                <div class="cd_name">
                                    <p class="name"><?php echo $_smarty_tpl->tpl_vars['ite']->value['name'];?>
</p>
                                </div>
                                <div class="cd_price">
                                    <p class="cost"><?php echo $_smarty_tpl->tpl_vars['ite']->value['cost'];?>
</p>  
                                </div>
                  
								  <div class="cd_order">
                                     <p class="good_order"><?php echo $_smarty_tpl->tpl_vars['ite']->value['good_order'];?>
</p>   
                                </div>
                                 <div class="cd_caozuo">
								 
                                     <span style=" background:#27a9e3; padding:8px; color:#fff;" class="curbtn" onclick="editgoods('<?php echo $_smarty_tpl->tpl_vars['ite']->value['id'];?>
');">编辑</span>
                                      <span style=" background:#ec7501; padding:8px; color:#fff;" class="curbtn" onclick="delgoods('<?php echo $_smarty_tpl->tpl_vars['ite']->value['id'];?>
');">删除</span>
                                </div>
                           </div> 
                        </div>
                       <?php } ?>
                      <?php } ?>
                      
                      <div class="order_list"  id="additem" style="display:none;">
                      	    <input type="hidden" name="adgoodstypeid" value="">
                       		 <div class="order_goodlist">
                                <div class="cd_name">
                                    <input style="width:50%;" type="text" value="" name="adgoodname" placeholder="商品名称"/>
                                </div>
                                <div class="cd_price">
                                   <input  style="width:50%;" type="text" value="" name="adgoodcost" placeholder="商品单价"/>
                                </div>
                        
								  <div class="cd_order">
                                    <input  style="width:50%;" type="text" value="" name="adgoodpaixu" placeholder="商品排序"/>   
                                </div>
                                 <div class="cd_caozuo">
                                     <span style=" background:#27a9e3; padding:8px; color:#fff;" class="curbtn" id="saveaddgoods">保存</span>
                                     
                                </div>
                           </div> 
                      </div>
                      <div class="order_list" style="background:#303337;">
                       		 <div class="order_goodlist">
                              <div class="cd_name" style=" border:none;">
                               <span style=" background:#27a9e3; padding:10px; color:#fff;" class="curbtn" id="addgoods">添加商品</span>
                              </div>
                            
                           </div>
                    
                      </div>
                      
                        <div class="cl"></div>
                        
                        
                     </div>
                    	
                        
                        
                        
                    </div>
                    
       			 </div>
                 
                 
              </form>  
                
                
        </div>
        <div class="cl"></div>
        
        
       
        
        
        <!---content right end---> 
 
 
 </div>
 
 
	

 
 
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js"></script>
<script>
$(function(){
//删除分类
var checkisexti =  $('.caidanTitle').find('li').length;
if(checkisexti == 0){
 
  $('.caidanSet').hide();
}
$('.delGDtype').live('click',function(){
	if(confirm('确定删除商品操作吗？删除后将同时删除该分类下的所有商品')){ 
		var allobj = $(".caidanSet").find('li');
		var typeid = 0;
		for(var i=0;i< $(allobj).length;i++){ 
		   if($(allobj).eq(i).hasClass("cur") ==  true){
		      typeid = $(allobj).eq(i).attr('data');
		    }
		}
		 
    myajax('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/delgoodstype/datatype/json"),$_smarty_tpl);?>
',{'addressid':typeid});
  }
});	
//编辑分类
$('.editGDtype').live('click',function(){
	var allobj = $(".caidanSet").find('li');
		var typeid = 0;
		var typename = '';
		var typeorder = '';
		for(var i=0;i< $(allobj).length;i++){ 
		   if($(allobj).eq(i).hasClass("cur") ==  true){
		      typeid = $(allobj).eq(i).attr('data');
		      typename = $(allobj).eq(i).attr('dataname');
		      typeorder = $(allobj).eq(i).attr('dataorder');
		    }
		}
	var	htmls = '<form method="post" id="doshwoform" action="#" style="text-align:center;"><table>';
	htmls += '<tbody><tr>';
	htmls += '<td height="50px">分类名称:</td>';
	htmls += '<td> <input type="text" name="newtypename" value="'+typename+'" style="width:100px;"></td></tr>';
	htmls +='<tr><td height="50px">排序ID:</td><td style="text-align:left;"> <input type="text" name="newtypeorderid" value="'+typeorder+'" style="width:50px;"></td></tr>'
	htmls += '</tbody></table> ';
  htmls += '<input type="hidden" value="'+typeid+'" name="newtypeid"> ';
	htmls += '<input type="button" value="确认提交" class="button" id="updategoodstype" ></form>';
  art.dialog({
    id: 'testID3',
    title:'保存店铺分类',
    content: htmls
  });
	
	
});
//保持编辑分类
$('#updategoodstype').live('click',function(){
 
	  showop('保存商品分类信息');
	   myajax('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/editgoodstype/datatype/json"),$_smarty_tpl);?>
',{'what':'allinfo','name':$('input[name="newtypename"]').val(),'orderid':$('input[name="newtypeorderid"]').val(),'addressid':$('input[name="newtypeid"]').val()}); 
});
	
//添加商品分类
$("#add_FoodType").live("click", function() {   
	if($('#shoptypename').val() == ''||$('#shoptypename').val() ==null){
		diaerror('分类不能空'); 
	}else{
		var mm = $('#shoptypename').val();
		$('#shoptypename').val('')
	   myajax('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/savegoodstype/datatype/json"),$_smarty_tpl);?>
',{'name':mm,'orderid':$(".caidanSet").find('li').length}); 
  }
});


//导航条切换	
    $(".caidanSet ul li").click(function(){
        $(this).addClass('cur').siblings().removeClass('cur');
        var tempid = $(this).attr('data');
        $('.listgoodsdet').hide();
        $('.goodsdiv_'+tempid).show();
        $('input[name="adgoodstypeid"]').val(tempid);
        $('#additem').hide();
        var fleft = $('.caidanSet').offset().left;
        var zleft = $(this).offset().left;
        var ftop = $('.caidanSet').offset().top;
        var ztop = $(this).offset().top;
        var resulte = Number(zleft) -Number(fleft);
        var resulteR = Number(ztop) -Number(ftop);
        $('#editGtype').css({'margin-left':resulte,'margin-top':resulteR});
    });
$(".caidanSet").find("li").eq(0).trigger("click");//设置默认第一个
					
//快捷编辑商品					
$(".listgoodsdet p").live("click", function() {  
	var is_save = $(this).hasClass('now_edit');
	var typename = $(this).attr('class');
	var doc = $(this).text();
	 if(is_save){
	 	 
	 }else{
	 	$(this).addClass('now_edit');
	 	var goodsid = $(this).parent().parent().parent().attr('data');
	 	 
	 	 $(this).html('<input style="width:45%;" type="text" value="'+doc+'" id="'+typename+goodsid+'" \/> <span class="curbtn" onclick="savegoodtxt(\''+goodsid+'\',\''+typename+'\');">保存<\/span>');
	 } 
});

//显示添加商品
$('#addgoods').live('click',function(){
	$('#additem').show();
});
//提交添加商品
$('#saveaddgoods').live('click',function(){
	var typeid =   $('input[name="adgoodstypeid"]').val(); 
	var data1 = $('input[name="adgoodname"]').val(); 
	var data2 = $('input[name="adgoodcost"]').val(); 
	var data5 = $('input[name="adgoodpaixu"]').val(); 
	if(typeid == ''){
	 alert('请选择商品类型');
	 return false;
	}
	if(data1 == ''){
	  alert('请录入商品名称');
	  return false;
	} 
		if(confirm('确定操作吗？')){ 
			 showop('保存商品信息');  
	    var backinfo = ajaxback('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/addgoods/datatype/json"),$_smarty_tpl);?>
',{'name':data1,'typeid':typeid,'cost':data2,'good_order':data5});
	    if(backinfo.flag == true)
	    {
		      hideop();
		     diaerror(backinfo.content); 
	     }else{
	  	    hideop(); 
	  	    var htmldata = '<div class="order_list	listgoodsdet goodsdiv_'+backinfo.content.typeid+'" data="'+backinfo.content.id+'" id="goodstr_'+backinfo.content.id+'" >';
              htmldata += ' <div class="order_goodlist">';
              htmldata += '       <div class="cd_name">';
              htmldata += '          <p class="name">'+backinfo.content.name+'</p>';
              htmldata += '       </div>';
              htmldata += '      <div class="cd_price">';
              htmldata += '          <p class="cost">'+backinfo.content.cost+'</p>  ';
              htmldata += '     </div>';
			    htmldata += '      <div class="cd_order">';
              htmldata += '           <p class="good_order">'+backinfo.content.good_order+'</p>   ';
              htmldata += '      </div>';
              htmldata += '       <div class="cd_caozuo">';
              htmldata += '          <span style=" background:#27a9e3; padding:8px; color:#fff;" class="curbtn" onclick="editgoods(\''+backinfo.content.id+'\');">编辑</span>';
              htmldata += '            <span style=" background:#ec7501; padding:8px; color:#fff;" class="curbtn" onclick="delgoods(\''+backinfo.content.id+'\');">删除</span>';
              htmldata += '      </div>';
              htmldata += '  </div> ';
              htmldata += '</div>'; 
          $('#additem').before(htmldata); 
          $('input[name="adgoodname"]').val(''); 
	           $('input[name="adgoodcost"]').val(''); 

	            $('#additem').hide();
	  	    artsucces('保存成功');
		     
	     } 
		}
	 
});
//删除商品



 		
});

function openlink(newlinkes){
    window.location.href=newlinkes;
}


function savegoodtxt(goodsid,typename){ 
  	var values = $('#'+typename+goodsid).val();
	   showop('保存商品信息');
	  var backinfo = ajaxback('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/updategoods/datatype/json"),$_smarty_tpl);?>
',{'controlname':typename,'goodsid':goodsid,'values':values});
	  if(backinfo.flag == true)
	  {
		  hideop();
		  diaerror(backinfo.content); 
	  }else{
	  	 hideop();
	  	 $('#goodstr_'+goodsid).find('.'+typename).text(values);
	  	 $('#goodstr_'+goodsid).find('.'+typename).removeClass('now_edit');
	  	 artsucces('保存成功');  
	 } 
}

function delgoods(gid){
   if(confirm('确定删除该商品操作吗？')){ 
	var backinfo = ajaxback('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/delgoods/datatype/json"),$_smarty_tpl);?>
',{'id':gid});
	    if(backinfo.flag == true)
	    {
		    hideop();
		  diaerror(backinfo.content); 
	     }else{
	  	    hideop();
	  	    $('#goodstr_'+gid).remove();
	  	    artsucces('删除成功'); 
	     }  
	}
}
var dialogs ;
function editgoods(gid){
		 dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=shop&module=goodsone&gid='+gid,{height:'500px',width:'1029px'},false);
	 	 dialogs.title('编辑商品'); 
}
function refreshgoods(info){
	 dialogs.close();
	 $('#goodstr_'+info.id).remove();
	 
	var htmldata = '<div class="order_list	listgoodsdet goodsdiv_'+info.typeid+'" data="'+info.id+'" id="goodstr_'+info.id+'" >';
              htmldata += ' <div class="order_goodlist">';
              htmldata += '       <div class="cd_name">';
              htmldata += '          <p class="name">'+info.name+'</p>';
              htmldata += '       </div>';
              htmldata += '      <div class="cd_price">';
              htmldata += '          <p class="cost">'+info.cost+'</p>  ';
              htmldata += '     </div>';          
			      htmldata += '      <div class="cd_order">';
              htmldata += '           <p class="good_order">'+info.good_order+'</p>   ';
              htmldata += '      </div>';
              htmldata += '       <div class="cd_caozuo">';
              htmldata += '          <span style=" background:#27a9e3; padding:8px; color:#fff;" class="curbtn" onclick="editgoods(\''+info.id+'\');">编辑</span>';
              htmldata += '            <span style=" background:#ec7501; padding:8px; color:#fff;" class="curbtn" onclick="delgoods(\''+info.id+'\');">删除</span>';
              htmldata += '      </div>';
              htmldata += '  </div> ';
              htmldata += '</div>'; 
          $('#additem').before(htmldata); 
	var allobj = $(".caidanSet").find('li');
		var typeid = 0;
		for(var i=0;i< $(allobj).length;i++){ 
		   if($(allobj).eq(i).hasClass("cur") ==  true){
		      typeid = $(allobj).eq(i).attr('data');
		    }
		}
		if(typeid != info.typeid){
			$('#goodstr_'+info.id).hide();
		}
	
 
  
}
function doinputexcel(){
var curtypeid = $(".caidanSet .caidanTitle li.cur").attr('data');
	 dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=shop&module=doinputexcel&curtypeid='+curtypeid,{height:'400px',width:'850px'},false);
	  dialogs.title('导入商品'); 
}
function closemydo(){
   window.location.href=siteurl+'/index.php?ctrl=adminpage&action=shop&module=goodslibrary'; 
}
  </script>
 	

	
	
	
	  <div id="palywave" style="display:none;"></div>
<!------以下是公共的底部部分------->
    <div class="footer">
    	<div class="foot1">
        <center>
        	<div class="db">
        	   <?php if (!empty($_smarty_tpl->tpl_vars['toplink']->value)){?>
	 	      <?php $_smarty_tpl->tpl_vars['toplink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['toplink']->value), null, 0);?>
		  	  <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['toplink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?> 
			         <a href="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
"><?php echo $_smarty_tpl->tpl_vars['items']->value['typename'];?>
</a> | 
			    <?php } ?>
			<?php }?> 
         
            </div></center>
            <div class="cl"></div>
        </div>
        <div class="foot2">
        	<p>@2008-2012 <?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['beian']->value;?>
 <?php echo stripslashes($_smarty_tpl->tpl_vars['footerdata']->value);?>
</p>
        </div>
    </div> 

</body>

</html>
<?php }} ?>