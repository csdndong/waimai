<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 21:52:18
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\card\addspecialpage.html" */ ?>
<?php /*%%SmartyHeaderCode:281835cd582121a5528-81788419%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e2a6f394cfe3498113831aee0be2fe664da31d2' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\card\\addspecialpage.html',
      1 => 1536024619,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '281835cd582121a5528-81788419',
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
  'unifunc' => 'content_5cd58212418869_11943000',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd58212418869_11943000')) {function content_5cd58212418869_11943000($_smarty_tpl) {?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
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
/js/kindeditor/kindeditor.js" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/datepicker/WdatePicker.js" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js"></script>
 <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/adminpage/public/css/zty.css">
  
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
   	        	 	   <div class="showtop_t" id="positionname">编辑/添加专题页</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
 <style>
	 .right_content,.show_content_m,.show_content_m_t2{
		 min-height:600px!important;
	 }
     .show_content_m_t3{
         display:none;
     }
     #tagscontent li{
         width:auto!important;
     }
 </style>
       <div style="width:98%;overflow-y:auto;white-space:nowrap;min-height:600px;">
          <div class="tags">
          <div id="tagscontent" class="addzty_main">
                <div class="zty_type">
                    <span>*</span>
                    <span>专题名称：</span>
                    <input type="text" name="name" value="<?php echo $_smarty_tpl->tpl_vars['ztyinfo']->value['name'];?>
" maxlength = 5 style="width:120px;height:28px;" id="ztyname" onkeyup="maxinput();"><font id="znlength">0</font>/5
                </div>
                <div class="zty_type">
                    <span>*</span>
                    <span>专题类型：</span>
                    <span><input type="radio" name="type" value="1" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']==1){?>checked<?php }?> checked autocomplete="off">店铺</span>&nbsp;&nbsp;
                    <span><input type="radio" name="type" value="2" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']==2){?>checked<?php }?> autocomplete="off">商品</span>&nbsp;&nbsp;
                    <span><input type="radio" name="type" value="3" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']==3){?>checked<?php }?> autocomplete="off">店铺分类</span>&nbsp;&nbsp;
                    <span><input type="radio" name="type" value="4" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']==4){?>checked<?php }?> autocomplete="off">活动</span>&nbsp;&nbsp;
                    <span><input type="radio" name="type" value="5" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']==5){?>checked<?php }?> autocomplete="off">其他</span>&nbsp;&nbsp;
                    <span><input type="radio" name="type" value="6" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']==6){?>checked<?php }?> autocomplete="off">自定义链接</span>&nbsp;&nbsp;
                </div>
                <div class="zty_type" id="shopattr">
                    <span>*</span>
                    <span>店铺分类：</span>
                    <select name="shopclass" id="shopclass" style="width:150px;height:28px;" onchange="optshop();">
                        <option value="0">请选择</option>
                        <option value="1" id="allc" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['id']>0&&$_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']!=3){?>selected <?php }?> >全部</option>
                        <option value="marketshop" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['listids']=='marketshop'){?>selected<?php }?>   shoptype =" marketshop">超市便利店</option>
						<?php if (!empty($_smarty_tpl->tpl_vars['catlist']->value)){?>
                        <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['catlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
"  <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['zttype']==3&&$_smarty_tpl->tpl_vars['ztyinfo']->value['listids']==$_smarty_tpl->tpl_vars['items']->value['id']){?>selected<?php }?>   shoptype = "<?php echo $_smarty_tpl->tpl_vars['items']->value['cattype'];?>
"><?php echo $_smarty_tpl->tpl_vars['catarr']->value[$_smarty_tpl->tpl_vars['items']->value['cattype']];?>
 - <?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</option>
                        <?php } ?>
                        <?php }?>
                    </select>
                </div>
                  <div class="zty_type" id="activity" style="display:none;">
                      <span>*</span>
                      <span>活动类型：</span>
                      <span><input type="radio" name="activity_type" value="0" checked <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['showtype']==0){?>checked<?php }?> >店铺活动</span>&nbsp;&nbsp;
                      <span><input type="radio" name="activity_type" value="1" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['showtype']==1){?>checked<?php }?> >商品活动</span>;
                        <ul style="margin-left:100px;line-height:0px;" id="cxtypelist">
                            <li><input type="radio" name="cxtype" value="3"  <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==3){?>checked<?php }?>  checked>折扣</li>
                            <li><input type="radio" name="cxtype" value="2"  <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==2){?>checked<?php }?>  >减费用</li>
                            <li><input type="radio" name="cxtype" value="4"  <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==4){?>checked<?php }?>  >免配送费</li>
                            <li><input type="radio" name="cxtype" value="1"  <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==1){?>checked<?php }?>  >送赠品</li>
							<li><input type="radio" name="cxtype" value="5"  <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==5){?>checked<?php }?>  >首单立减</li>
                        </ul>
                  </div>

                  <div class="zty_type" id="moremode" style="display:none;">
                      <ul style="margin-left:100px;line-height:0px;" id="moremodelist">
                          <li><input type="radio" name="mode" value="9" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==9){?>checked<?php }?>  checked>跑腿模块</li>
                          <li><input type="radio" name="mode" value="10" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==10){?>checked<?php }?>  >商家入驻</li>
                          <li><input type="radio" name="mode" value="11" <?php if ($_smarty_tpl->tpl_vars['ztyinfo']->value['cx_type']==11){?>checked<?php }?>  >邀请好友</li>
                      </ul>
                  </div>
              <div class="zty_type" id="link" style="display:none;">
                  <span>*</span>
                  <span>链接地址：</span>
                  <span><input type="text" name="link" value="<?php echo $_smarty_tpl->tpl_vars['ztyinfo']->value['zdylink'];?>
" style="width:240px;height:28px;"></span>
              </div>
              <div>
                  <div class="shoplist" style="max-height:300px;overflow:auto;display:none;">
                    <div id="shoplist_top">
                        <font>选择店铺</font>&nbsp;&nbsp;&nbsp;<span><input type="checkbox" name="allopt">全选</span>
                        <span style="float:right;margin-top:6px;margin-right:10px;">
                            <input type="text" name="search_shop" id="search_shop" placeholder="输入店铺名称" style="border-radius:5px;border:1px solid #fff;height:26px;">
                            <span style="position:absolute;margin-top:3px;margin-left:-25px;cursor:pointer;" onclick="optshop();"><img
                                    src="/templates/m7/public/wxsite/images/059.png" alt="" style="width:18px;"></span>
                        </span>
                    </div>
                      <ul id="shoplist" style="padding:5px 10px;">

                      </ul>
                  </div>
              </div>
          </div>

              <div class="select_shop_content" style="display:none;">
                  <div class="zty_type" style="float:left;'">
                      <span>*</span>
                      <span style="color:#000;">选择商品：&nbsp;</span>
                  </div>
                  <div class="select_shop_box">
                      <div class="select_shop_top">
                          <h3>选择店铺商品</h3>
                          <div class="select_shop_search">
                              <input type="text" id="search_shop2" placeholder="输入店铺名称" />
                              <span style="position:absolute;margin-top:3px;margin-left:-19px;cursor:pointer;" onclick="optshop();"><img
                                      src="/templates/m7/public/wxsite/images/059.png" alt="" style="width:18px;"></span>
                          </div>
                          <div class="clear"></div>
                      </div>
                      <div class="select_shop_left">
                          <ul>

                          </ul>
                      </div>
                  </div>
                  <div class="select_shop_addto">添加</div>
                  <div class="select_shop_box">
                      <div class="select_shop_top">
                          <h3>已添加店铺商品</h3>
                          <div class="clear"></div>
                      </div>
                      <div class="select_shop_right">
                          <ul>

                          </ul>
                      </div>
                  </div>
                  <div class="clear"></div>
              <!--</div>-->
          </div>
           <div id="next" onclick="next(1);">下一步</div>
           <div id="save" onclick="next(2);">保存</div>
           <div class="blank20"></div>
       </div>
 <div class="clear"></div>
 </div>
 <style>
     #save {
         display: inline-block;
         width: 90px;
         height: 34px;
         text-align: center;
         line-height: 34px;
         background-color: #169BD5;
         color: #fff;
         border-radius: 5px;
         margin: 10px 85px;
         cursor: pointer;
         float: left;
         margin-left: 140px;
     }
 </style>
 </div>
 <!--//选择器-->
 <script>
    var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
    var ids = "<?php echo $_smarty_tpl->tpl_vars['ztyinfo']->value['listids'];?>
";
    var id = "<?php echo $_smarty_tpl->tpl_vars['ztyinfo']->value['id'];?>
";
    $(function(){
        optshop();
        showhtml();
        maxinput();
    })
    $("input[name='type']").click(function(){
        showhtml();
    })
    $("input[name='activity_type']").click(function(){
        cxtypeshow();
    })
    $("input[name='allopt']").click(function(){
        if($("input[name='allopt']").attr("checked")==false){
            $("input[name='oneshop[]']").attr("checked",false);
        }else{
            $("input[name='oneshop[]']").attr("checked",true);
        }
    })
    function optshop(){
        var shopclassid = $("#shopclass").val();
        var opttype = $("input[name='type']:checked").val();
        var addhtml = ' ';
        var idsarr =  ids.split(",");
        if((shopclassid>0 || shopclassid=='marketshop' )&& opttype==1){
            var search_shop = $("#search_shop").val();
            var url =siteurl+'/index.php?ctrl=adminpage&action=other&module=getshoplist&datatype=json';
            $("#shoplist").html(' ');
            $.post(url,{'shopclassid':shopclassid,'search_shop':search_shop},function(e){
                if(e.error==false){
                    for(var i=0;i<e.msg.length;i++){
                        //判断id是否在数组中进行判断
                        if(idsarr.indexOf(e.msg[i].id)>=0){
                            addhtml +='<li><input type="checkbox" name="oneshop[]" checked  value="'+e.msg[i].id+'">'+e.msg[i].shopname+'</li>';
                        }else{
                            addhtml +='<li><input type="checkbox" name="oneshop[]"  value="'+e.msg[i].id+'">'+e.msg[i].shopname+'</li>';
                        }
                    }
                    $("#shoplist").html(addhtml);
                }else{
                    alert(e.msg);
                }
            },'json')
            $(".shoplist").show();
        }else{
            $(".shoplist").hide();
        }
        if(shopclassid>0 && opttype==2){
            var search_shop2 = $("#search_shop2").val();
            var url =siteurl+'/index.php?ctrl=adminpage&action=other&module=getgoods&datatype=json';
            $(".select_shop_left ul").html(' ');
            $.post(url,{'shopclassid':shopclassid,'search_shop':search_shop2},function(e){
                if(e.error==false){
                    for(var i=0;i<e.msg.list.length;i++){
                        addhtml +='<li>'
                        addhtml +='    <div class="select_shop_title" shopid ="'+e.msg.list[i].id+'">'
                        addhtml +='        <div class="select_shop_checkbox">'
                        addhtml +='           <input type="checkbox" class="shopc" />'
                        addhtml +='           <i class="icon_jia" data="1">+</i>'
                        addhtml +='            <i class="icon_jian">-</i>'
                        addhtml +='        </div>'
                        addhtml +='        <span class="shopname">'+e.msg.list[i].shopname+'</span>'
                        addhtml +='       <div class="select_shop_btn">'
                        addhtml +='            <i class="icon_jia">+</i>'
                        addhtml +='           <i class="icon_jian">-</i>'
                        addhtml +='            <i class="icon_cha">x</i>'
                        addhtml +='       </div>'
                        addhtml +='       <div class="clear"></div>'
                        addhtml +='   </div>'
                        addhtml +='   <div class="select_shop_list">'
                        addhtml +='        <dl>'
                        for(var j=0;j<e.msg.list[i].goods.length;j++){
                            addhtml +='           <dd>'
                            if(idsarr.indexOf(e.msg.list[i].goods[j].id)>=0){
                                addhtml +='               <input type="checkbox" checked name="goods[]" class="optgoods" />'
                            }else{
                                addhtml +='               <input type="checkbox"  name="goods[]" class="optgoods" />'
                            }
                            addhtml +='                <span goodsid = '+e.msg.list[i].goods[j].id+'>'+e.msg.list[i].goods[j].name+'</span>'
                            addhtml +='               <i class="icon_cha">x</i>'
                            addhtml +='           </dd>'
                        }
                        addhtml +='       </dl>'
                        addhtml +='   </div>'
                        addhtml +='</li>'
                    }
                    $(".select_shop_left ul").html(addhtml);
                    $("input[name='goods[]']").each(function(){
                        if($(this).attr("checked")==true){
                            $(this).parents("li").find(".shopc").attr("checked",true);
                        }
                    })
                }else{
                    alert(e.msg);
                }
            },'json')
            $(".select_shop_content").show();
        }else{
            $(".select_shop_content").hide();
        }
    }
    $(".optgoods").live("click",function(){
        $($(this).parents("li").find("input[name='goods[]']")).each(function(){
            if($(this).attr("checked")==true){
                $(this).parents("li").find(".shopc").attr("checked",true);
                return false;
            }else{
                $(this).parents("li").find(".shopc").attr("checked",false);

            }
        })
    })
    $(".shopc").live("click",function(){
        if($(this).attr("checked")==false){
            $(this).parents("li").find("input[name='goods[]']").attr("checked",false);
        }else{
            $(this).parents("li").find("input[name='goods[]']").attr("checked",true);
        }
    })
    $(".icon_jia").live("click",function(){
        var iflag =  $(this).attr("data");
        if(iflag==1){
            $(this).parents("li").find(".select_shop_list").show();
            $(this).attr("data",'0');
            $(this).html("+");
        }else{
            $(this).parents("li").find(".select_shop_list").hide();
            $(this).attr("data",'1');
            $(this).html("-");
        }
    })
    $(".icon_jian").live("click",function(){
        var iflag =  $(this).attr("data");
        if(iflag==1){
            $(this).parents("li").find(".select_shop_list").show();
            $(this).attr("data",'0');
            $(this).html("-");
        }else{
            $(this).parents("li").find(".select_shop_list").hide();
            $(this).attr("data",'1');
            $(this).html("+");
        }
    })
    $(".jian_s").live("click",function(){
        $(this).parents("li").remove();
    })
    $(".jian_g").live("click",function(){
        $(this).parents(".select_shop_list").remove();
    })

    $(".select_shop_addto").click(function(){
       var goodshtml = ''
//        $(".select_shop_right").html("");
        $(".shopc").each(function(){
            if($(this).attr("checked")==true){
                var shopname=$(this).parents("li").find(".shopname").html();
                var shopid=$(this).parents("li").find(".select_shop_title").attr("shopid");
                $(".select_shop_right ul li").each(function(){
                    if($(this).find('.select_shop_title').attr("shopid") ==shopid ){
                        $(this).remove();
                    }
                })
                goodshtml+= '<li>'
                goodshtml+= '        <div class="select_shop_title" shopid="'+shopid+'">'
                goodshtml+= '        <div class="select_shop_checkbox">'
                goodshtml+= '            <input type="checkbox" class="shopc2" />'
                goodshtml+= '            <i class="icon_jia">+</i>'
                goodshtml+= '           <i class="icon_jian" data="0">-</i>'
                goodshtml+= '        </div>'
                goodshtml+= '        <span>'+shopname+'</span>'
                goodshtml+= '        <div class="select_shop_btn">'
                goodshtml+= '           <i class="icon_jia">+</i>'
                goodshtml+= '           <i class="icon_jian">-</i>'
                goodshtml+= '           <i class="icon_cha jian_s">x</i>'
                goodshtml+= '        </div>'
                goodshtml+= '        <div class="clear"></div>'
                goodshtml+= '   </div>'
                $(this).parents("li").find(".optgoods").each(function(){
                    if($(this).attr("checked")==true){
                        var goodsname = $(this).next().html();
                        var goodsid = $(this).next().attr("goodsid");
                        goodshtml+= '   <div class="select_shop_list">'
                        goodshtml+= '        <dl>'
                        goodshtml+= '            <dd>'
                        goodshtml+= '                <input type="checkbox" name="optgoods[]" />'
                        goodshtml+= '               <span gid='+goodsid+'>'+goodsname+'</span>'
                        goodshtml+= '               <i class="icon_cha jian_g">x</i>'
                        goodshtml+= '           </dd>'
                        goodshtml+= '       </dl>'
                        goodshtml+= '   </div>'
                    }
                })
                goodshtml+= '</li>'
            }
        })
        $(".select_shop_right ul").append(goodshtml);
    })

    function showhtml(){

        var i = $("input[name='type']:checked").val();
        if(i==1 || i==2 ||  i==4){
            $("#next").show();
            $("#save").hide();
        }else{
            $("#next").hide();
            $("#save").show();
        }
        if(i==1 || i==2 || i==3){
            $("#shopattr").show();
            optshop();
        }else{
            $("#shopattr").hide();
            optshop();
        }
        if(i==3){
            $("#allc").hide();
        }else{
            $("#allc").show();
        }
        if(i==4){
            $("#activity").show();
            cxtypeshow();
        }else{
            $("#activity").hide();
        }
        if(i==5){
            $("#moremode").show();
        }else{
            $("#moremode").hide();
        }
        if(i==6){
            $("#link").show();
        }else{
            $("#link").hide();
        }
    }
    function cxtypeshow(){
        var t =  $("input[name='activity_type']:checked").val();
        if(t==0){
            $("#cxtypelist").show();
        }else{
            $("#cxtypelist").hide();
        }
    }
    function maxinput(){
        var ss = $("#ztyname").val();
        $("#znlength").html(ss.length);
    }
    function next(istype){
        var name = $("input[name='name']").val();
        if(name==''){
            alert("请输入专题页名称");
            return false;
        }
        var params
        var cxtype
        var classid
        var shoptype
        var opttype = $("input[name='type']:checked").val();
        var shopids = [];
        var goodsids = [];
        if(opttype==1){
            $("input[name='oneshop[]']:checked").each(function(){
                shopids.push($(this).val());
            });
             params = shopids.join();
            if(params==''){
                alert("请选择店铺");
                return false;
            }
        }else if(opttype==2){
            $("input[name='optgoods[]']").each(function(){
                goodsids.push($(this).next().attr("gid"));
            });
            params = goodsids.join();
            if(params==''){
                alert("请选择商品");
                return false;
            }
        }else if(opttype==3){
            classid = $("select[name='shopclass']").val();
            shoptype = $("select[name='shopclass']").find("option:selected").attr("shoptype");
            params = classid+'&shoptype='+shoptype;
            if(classid==0){
                alert("请选择分类");
                return false;
            }
        }else if(opttype==4){
            var acclass = $("input[name='activity_type']:checked").val();
            if(acclass==0){
                cxtype= $("input[name='cxtype']:checked").val();
            }else{
                cxtype = '';
            }
            params = acclass+'&cxtype='+cxtype;
        }else if(opttype==5){
            params = $("input[name='mode']:checked").val();
        }else{
            params = $("input[name='link']").val();
            params = encodeURIComponent(params);
//            alert(params);
        }
        if(istype==1){
            window.location.href=siteurl+"/index.php?ctrl=adminpage&action=card&module=addspecialpagenext&name="+name+"&params="+params+"&opttype="+opttype+"&oriid="+id;
        }else{
            var color = "#000000";
            var img = "emptyimg";
            var content = "emptyContent";
            var url = siteurl+"/index.php?ctrl=adminpage&action=other&module=savezty&params="+params+"&datatype=json";
            $.post(url,{'name':name,'opttype':opttype,'color':color,'img':img,'content':content,'ztyid':id},function(content){
                if(content.error == false){
                    alert('保存成功');
                    window.location.href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/index.php?ctrl=adminpage&action=card&module=specialpage";
                }else{
                    alert(content.msg);
                }
            },'json')
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