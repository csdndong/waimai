<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:27:53
         compiled from "D:\wwwroot\demo.52jscn.com\templates\m7\gift\index.html" */ ?>
<?php /*%%SmartyHeaderCode:27145cd5603927bbe0-29081831%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7a540df89ce61408b494f3cf196e72758a911136' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\gift\\index.html',
      1 => 1536024584,
      2 => 'file',
    ),
    '217afa0808885dd89807d7541c2a8af7ba13208d' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\public\\site.html',
      1 => 1538873286,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27145cd5603927bbe0-29081831',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'sitename' => 0,
    'keywords' => 0,
    'description' => 0,
    'metadata' => 0,
    'siteurl' => 0,
    'color' => 0,
    'sitebk' => 0,
    'is_static' => 0,
    'controlname' => 0,
    'mapname' => 0,
    'member' => 0,
    'sitelogo' => 0,
    'footlink' => 0,
    'items' => 0,
    'search_input' => 0,
    'list' => 0,
    'list2' => 0,
    'itv' => 0,
    'appewm' => 0,
    'wxewm' => 0,
    'litel' => 0,
    'toplink' => 0,
    'beian' => 0,
    'footerdata' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd560395e8081_04517131',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd560395e8081_04517131')) {function content_5cd560395e8081_04517131($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
 <title> 礼品街-<?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
</title>
<meta name="Keywords" content="<?php echo $_smarty_tpl->tpl_vars['keywords']->value;?>
" />
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
" />
<?php echo stripslashes($_smarty_tpl->tpl_vars['metadata']->value);?>

	  <link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-16x16.png" type="image/png" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-16x16.png" type="image/png" sizes="16x16" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-32x32.png" type="image/png" sizes="32x32" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-48x48.png" type="image/png" sizes="48x48" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-64x64.png" type="image/png" sizes="64x64" />
<link href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/common.css?v=9.0">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtype.css?v=9.0"> 
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/wmr_login.css?v=9.0">
    

<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtop/ntopcommon.css?v=9.0"> 
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtop/ntopjquery-ui-1.10.4.custom.min.css?v=9.0">
<?php if ($_smarty_tpl->tpl_vars['color']->value=="green"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/green.css?v=9.0">
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['color']->value=="yellow"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/yellow.css?v=9.0">
<?php }?> 
<!-- <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtop/ntopstyles.css">  -->

         <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/2.1/base.css" />
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/2.1/model.css" />
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/2.1/gift.css" />
           <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/gift.css"> 
 <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/ordering.css">  
 <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/ordercf.css"> 
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/gift.css">   
 

<div class="mmbg" <?php if (!empty($_smarty_tpl->tpl_vars['sitebk']->value)){?>style="background:url(<?php echo $_smarty_tpl->tpl_vars['sitebk']->value;?>
) repeat;"<?php }?>></div> 
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquerynew.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=blue"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.lazyload.min.js?v=9.0" type="text/javascript" language="javascript"></script> 
 
  
 <script>
 	$(function() { 
$("img").lazyload({ 
effect : "fadeIn" 
}); 
}); 
</script> 

  <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/gift.js"> </script>
  <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/bootstrap.min.js" type="text/javascript" language="javascript"></script>
  

 <script> 
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	var controllername= '<?php echo $_smarty_tpl->tpl_vars['controlname']->value;?>
';
</script>

<!--[if lte IE 6]>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/DD_belatedPNG_0.0.8a.js" type="text/javascript"></script>
    <script type="text/javascript">
        DD_belatedPNG.fix('div, ul, img, li, input , a'); 
    </script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/ie6.js" type="text/javascript"></script>


<![endif]--> 
</head> 
<body>

<!--谁去拿外卖 -->

       <div id="modal-shuiqunawaimai" class="modal-who-get-dishes modal hide fade" aria-hidden="true" tabindex="-1">
      <div class="modal-header"> <a href="#" class="close" aria-hidden="true">×</a>
        <h3>谁去拿外卖</h3>
      </div>
      <div class="modal-body">
        <div class="who-get-dishes-wrapper">
          <h2 class="wgd-badge"></h2>
          <a id="trigger" class="wgd-btn"></a> <span class="wgd-rules">随机到最小数字的人去拿外卖</span> <span id="lastResult" class="wgd-bg-text">↓ Start</span>
          <ul id="result" class="wgd-result-list">
          </ul>
        </div>
      </div>
    </div>

<script type="text/javascript">

  var eleme = eleme || {};


</script>

<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/whoqunawaimai.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/whonawaimai.css">
<!--谁去拿外卖  end-->







<div class="topCon">
    <div class="topBox">
        <div class="topL">
            <div class="topLbox"><i></i><span>当前位置：</span><b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['mapname']->value)===null||$tmp==='' ? '' : $tmp);?>
</b><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/guide"),$_smarty_tpl);?>
">[切换地址]</a></div>
        </div>
        <div class="topR">
            <div class="topRsignin">
                <ul>
                    <!--<li><span>您好请登录</span></li>-->
                    <?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])){?>
                    <li>
                        <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/base"),$_smarty_tpl);?>
">您好，<?php echo $_smarty_tpl->tpl_vars['member']->value['username'];?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/loginout"),$_smarty_tpl);?>
">退出</a>
                    </li>
                    <?php }else{ ?>
                    <li>
                        <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/login"),$_smarty_tpl);?>
">登录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                       <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/regester"),$_smarty_tpl);?>
">注册</a>
                    </li>
                    <?php }?>
                </ul>
            </div>
         </div>
    </div>
</div>
<!---------------------------------------顶部结束--------------------------------------->

<!---------------------------------------头部开始--------------------------------------->
<div class="headCon">
    <div class="headBox">
        <div class="headLogo"><a href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
"><img style="margin-top:5px;" <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['sitelogo']->value)),$_smarty_tpl);?>
></a></div>
        <div class="headNav" style="background:none;height:0;">
            <ul>
                <?php if (!empty($_smarty_tpl->tpl_vars['footlink']->value)){?>
                <?php $_smarty_tpl->tpl_vars['footlink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['footlink']->value), null, 0);?>
                <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['footlink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>
                <li ><a href="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
"><?php echo $_smarty_tpl->tpl_vars['items']->value['typename'];?>
</a></li>
                <?php } ?>
                <?php }?>
				<!-- class="headNavaA" -->
            </ul>
        </div>
        <div class="headSearch"><input type="text" class="headSeaText" id="search_input" placeholder="输入商家/美食" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['search_input']->value)===null||$tmp==='' ? '' : $tmp);?>
"><input type="button" id="comtopsearch" class="headSeaBut" style="border:0px solid #fff;"></div>
    </div>
</div>
<script type="text/javascript">
    $('#comtopsearch').click(function(){

        search();
    })

    function search()
    {
        var searchname = $('#search_input').val();

        if( searchname != '' )
        {
            var url = siteurl+'/index.php?ctrl=site&action=shoplist&shopsearch='+searchname;
            location.href=url;
        }else{

            alert("搜索条件不能为空！");

        }
    }

</script>


<div style="clear:both;"></div>


 

 
<div class="js_MASK" style="opacity: 0.6; position: fixed;
top: 0;
left: 0;
z-index: 1000;
background: #000; width: 100%; height: 100%; display: none ;"></div>


	<div id="FirstIndex"></div>
       
        
        <!--main-->
        <div id='gift'>
            <div class="webMainModel clearfix">
                <!--左侧礼品列表-->
                <div id='gift-list'>
                    <ul class='clearfix'>
                    	
                    	
                    	 <?php echo smarty_function_load_data(array('assign'=>"list",'table'=>"gift",'fileds'=>"*",'limit'=>"100"),$_smarty_tpl);?>
 
            	    <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
                   <li>
	                            <div class='gift-ImgWarp'>
	                                <img style="width:260px; height:260px;" <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['value']->value['img'])),$_smarty_tpl);?>
 width='260' height='260' />
	                            </div>
	                            <h3 class="gift-itemTitle"><?php echo $_smarty_tpl->tpl_vars['value']->value['title'];?>
</h3>
	                            <p>现金购买：<b>￥<?php echo $_smarty_tpl->tpl_vars['value']->value['market_cost'];?>
</b></p>
	                            <p>库存：<?php echo $_smarty_tpl->tpl_vars['value']->value['stock'];?>
个</p>
                                
	                            <p class="gift-exchange"><i class='gift-price'><?php echo $_smarty_tpl->tpl_vars['value']->value['score'];?>
</i>积分
                                <span class='gift-changeBtn'  onclick="selPrizeItem('<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['value']->value['title'];?>
','<?php echo $_smarty_tpl->tpl_vars['value']->value['score'];?>
','<?php echo $_smarty_tpl->tpl_vars['value']->value['stock'];?>
','<?php echo $_smarty_tpl->tpl_vars['value']->value['market_cost'];?>
')" class="btn_duihuan">我要兑换</span></p>
	                        </li>
                  <?php } ?> 
                    	 
                    </ul>
                </div>
            
                <!--右侧信息-->
                <div class='gift-msn'>
                    <!--积分信息-->
                    <div class="gift-usableInt" style="background:#f38383;">
                        <!--bg-->
                        <div class="back-bg"></div>
                        <h3>我的可用积分</h3>
                        <p class="gift-intNum" id="jifen"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['member']->value['score'])===null||$tmp==='' ? 0 : $tmp);?>
</p>
                        <p class='gift-msnRule'>
                        	<a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/single/show/jfgz"),$_smarty_tpl);?>
 "target="_blank">积分规则</a><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/jifenlog"),$_smarty_tpl);?>
" target="_blank">积分明细</a>
                        	<i class="triangle"></i>    
                        </p>
                    </div>
                    
                    
                    
                    <!--礼品车-->
                    <div class="car shaDow">
                        <h2 class="moudleH2">礼品兑换</h2>
                        <ul class="car-list" id="c_lipin">
                            <!--  <li>
                                <span class="car-goodsTitle">记事贴</span>
                                <!--操作--
                                <div class="car-handle">
                                    <i class="less">-</i>
                                    <input type="text" class='' />
                                    <i class="add">+</i>
                                </div>
                                <b class='car-goodsInt'>1000积分</b>
                                
                                <!--delete button--
                                <i></i>
                            </li>-->
                            
                            
						
						
						<div class="ordered">
							<table id="table_canpin" style="display:none">
								<tbody><tr class="border_bottom">
									<td class="acc">礼品价值</td>
									<td class="mid"></td>
									<td class="orange14b align-right" id="totalScore">0</td>
								</tr>
								</tbody>
                                <tbody id="t_lipin">
								 
								</tbody>

							</table>

							<table id="t_heji" style="display:none">
								<tbody>
								<tr class="ordertip">
									<td colspan="3" class="heji"><p></p></td>
								</tr>
							</tbody></table>
						</div>
				
                            
                        </ul>
                    
                        <!--总积分-->
                        <p class="car-sum">合计<strong id='car-sum'><em class="orange14b" id="total" data="0">￥0.0</em></strong>积分</p>
                    </div>
                    
                    <!--配送详情-->
                    <div class="Shipping-info shaDow">
                        <h2 class="moudleH2">用户信息</h2>
                        <form class="ship-form">
                            <ul class="clearfix">
                                <!--<li class="shipping-name"><label>姓名：</label><input type='text'class='txt name' value=""  id="uname"/></li>
                                
									<li class="shipping-sex" value='1'><i class="radio active" id="dwb_sex_female" value="1">女士</i><i class="radio" id="dwb_sex_male" value="0">先生</i></li>
								
                                <li><label>手机：</label><input type='text' class='txt' id="uphone"  value=""/></li>
                                <li><label>地址：</label><input type='text' class='txt' id="uaddress" value=""/></li>
                                <li><label>备注：</label><input type='text' class='txt' id="unRemark"/></li>
                                <li class="shipping-give"><b class="checkBox checked" id="sendToOther">送给他人</b></li>-->
                                
                                <p class="ordered_title">
							<span class="yleft">姓名</span>
							<span class="ordered_date">
								<input id="uname" maxlength="10" class="msn" value="" >
								<!--
									<input id="dwb_sex_female" name="dwb_sex" type="radio" value="1"   checked="checked">女士
									<input id="dwb_sex_male" name="dwb_sex" type="radio" value="0" disabled="disabled">先生  -->
							</span>
						</p>

						<p class="ordered_title">
							<span class="yleft">手机</span>
							<span class="ordered_date">
								<input id="uphone" maxlength="30"   value="">
							</span>
						</p>

						<p class="ordered_title" id="areatop">
							<span class="yleft">地址</span>
							<span class="ordered_date">
								<input id="uaddress" maxlength="50"   value="">
							</span>
						</p>

						<p class="ordered_title">
							<span class="yleft">备注</span>
							<span class="ordered_date">
							<input id="unRemark" maxlength="50"   value="" style="border: 1px solid #ccc;height: 24px;width:175px;">
							</span>
							
						</p>

					 

						<p class="ordered_title_auto" id="sendremarks" style="display:none">
							<span class="yleft">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
							<span class="ordered_date">
								<textarea id="Usendremark">请输入希望我们帮您转达的消息...</textarea>
							</span>
						</p>
                                
                            </ul>
                            <textarea class="shipping-textarea"  id="Usendremark"  style="display:none">请输入希望我们帮您转达的消息..</textarea>
                        </form>
                    </div>
                    
                    <p class="ship-sub next_btn listSprit" style="background:#f38383;"  id='next'>下一步<span class="ship-subRight"></span></p>
                </div>
            </div>
        </div>
        
        <!--底部-->
        
	<!--底部-->
 <div id="success-list" style="display: none;">
	<div class="title">订单信息核对</div>
	<div class="list" id="orderList">
		<table class="top"><tbody><tr class="tr1"><th colspan="5">积分商品兑换</th><!-- <th colspan="2" class="ps">发送方式：邮箱短信</th> --></tr>
		<tr class="tr2"><td class="cp">餐品</td><td class="jg">价格</td><td class="sl">数量</td><td class="xj">小计</td><td class="bz">备注</td>
        
        </tr></tbody>
			</table>
			<div class="canpin_list">
				<table class="middle">
					<tbody id="updivmidle">
						 
		      </tbody>
		    </table>
		  </div>
		  <table class="bottom">
		  	<tbody>
		  		 
		  	  
		  	  
		  	</tbody>
		  </table>
		 </div>
    <div id="notice">
  <!--   <p><span>充值卡卡号与密码以短信或邮件形式在3个工作日内发送到您注册时填写的邮箱和手机，如需修改邮箱和手机，请到会员中心账户基本资料下修改</span></p> -->
    <!--
    
		<p>①<span>第一次使用</span>我们的服务时，我们将会与您<span>电话确认</span>；若号码无法联系，我们将会把您的订单设为无效。</p>
		<p>②商家商品价格可能会突发调整，若网站上价格与商家实际价格不同，将<span>以商家最新价格为准</span>。</p>
		<p>③您<span>可实时监控</span>自己的订单进度，当进度状态为预订中时可退订或修改，其他状态时均不可变更。</p>
		<p>④高峰期时，订单按下单先后依次排队安排，送达时间会有所延长，敬请谅解。为更准时送达，建议预订。</p>-->
	  </div>
	  <div id="btn">
	  	   <img onmouseover="this.style.cursor='pointer';" onclick="backandmodify()" src="/upload/shop/btn_back.jpg"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  	   <img onmouseover="this.style.cursor='pointer';" onclick="king_tijiaodingdan()" src="/upload/shop/btn_submit.jpg"> 
	  </div>
	  <div id="showsubmit" style="margin: 10px auto 0px;width:700px;display:none;">数据提交中.....</div>
</div>
<div id="fade" class="black_overlay" style="display:none;"></div>   
    
<script id="trgoodlist" type="text/html"> 
	<tr id="tr_<^%=gid%^>" class="orderPart1" gid="<^%=gid%^>" gcost="<^%=gscore%^>" bagcost="0" stock="<^%=gstock%^>">
						     	 <td class="acc"><b class="menuname" title="<^%=gtitle%^>"><^%=gtitle%^></b></td>
						     	 <td class="amount">
						     	 	    <span class="minus_Minus listSprit" id="<^%=gid%^>_Amount_reduce" style="border:1px solid #ccc; width:17px; height:17px; line-height:17px;text-align:center;" onclick="delOrReduceAmount(<^%=gid%^>,1)">-</span>
						     	 	    <input type="text" id="<^%=gid%^>_Amount" value="1" class="minus_add_num" onblur="chnageAmount(<^%=gid%^>)"><span class="minus_add listSprit" id="<^%=gid%^>_Amount_add" style="border:1px solid #ccc;line-height:17px; text-align:center; width:17px; height:17px;"  onclick="addOrReduceAmount(<^%=gid%^>,1)">+</span>
						     	 	</td>
	 <td id="<^%=gid%^>_stPrice" style=" float:right;" class=""><^%=gscore%^></td>
 </tr> 
</script>    
<script> 
	 var getcxinfo = '<?php echo FUNC_function(array('type'=>'url','link'=>"/fastfood/getcx/random/@random@"),$_smarty_tpl);?>
';
	 var arealist = <?php echo json_encode($_smarty_tpl->tpl_vars['myarealist']->value);?>
;
	 var areagrade = <?php echo $_smarty_tpl->tpl_vars['area_grade']->value;?>
; 
	 var submithtml = '<?php echo FUNC_function(array('type'=>'url','link'=>"/gift/newexchang/datatype/json/random/@random@"),$_smarty_tpl);?>
';
	 <?php if (empty($_smarty_tpl->tpl_vars['member']->value['uid'])){?>
	 var memberscore = 0;
	 <?php }else{ ?>
	 	var memberscore = <?php echo $_smarty_tpl->tpl_vars['member']->value['score'];?>
;
	 	<?php }?>
	  var mydefaultadress = <?php echo json_encode($_smarty_tpl->tpl_vars['mydefaultadress']->value);?>
;
</script>
<script id="areaid1" type="text/html">  
<p class="ordered_title" id="liarea1">
                    <span class="yleft">区域:</span>
                    <span class="ordered_date" style="margin-left: 10px;" id="belongcity1">
                       <select name="area1" id="area1" onchange="dotype2();">
				<^%for(i = 0; i < list.length; i ++) {%^>
					<^%if(list[i].parent_id == showid){%^>
             <option value="<^%=list[i].id%^>" <^%if(list[i].id == selectid){%^> selected <^%}%^>>
             <^%=list[i].name%^>
             </option>
             <^%}%^>
        <^%}%^> 
				
				</select>
				
                    </span>
 </p>
</script>
<script id="areaid2" type="text/html">  
<p class="ordered_title" id="liarea2">
                    <span class="yleft">区域:</span>
                    <span class="ordered_date" style="margin-left: 10px;"  id="belongcity2">
                       <select name="area2" id="area2" onchange="dotype3();">
				<^%for(i = 0; i < list.length; i ++) {%^>
					<^%if(list[i].parent_id == showid){%^>
             <option value="<^%=list[i].id%^>" <^%if(list[i].id == selectid){%^> selected <^%}%^>>
             <^%=list[i].name%^>
             </option>
             <^%}%^>
        <^%}%^>  
				</select>
                    </span>
 </p>
</script>
<script id="areaid3" type="text/html">  
<p class="ordered_title" id="liarea3">
                    <span class="yleft">区域:</span>
                    <span class="ordered_date"style="margin-left: 10px;"  id="belongcity3">
                       <select name="area3" id="area3" onchange="dotype4();">
				<^%for(i = 0; i < list.length; i ++) {%^>
					<^%if(list[i].parent_id == showid){%^>
             <option value="<^%=list[i].id%^>" <^%if(list[i].id == selectid){%^> selected <^%}%^>>
             <^%=list[i].name%^>
             </option>
             <^%}%^>
        <^%}%^> 
				
				</select>
                    </span>
 </p>
</script>
<script>
$(function(){  
	     $('#uname').val(mydefaultadress.contactname);
	     $('#uphone').val(mydefaultadress.phone); 
	     $('#uaddress').val(mydefaultadress.address);
	     if(mydefaultadress.sex == 0){
	     	 
	     	$('#dwb_sex_male').attr('checked',true);//('click');
       }     
	  if(arealist.length > 0){ 
 	   	var htmls = template.render('areaid1', {list:arealist,showid:0,selectid:mydefaultadress.areaid1});
          $('#areatop').before(htmls);
          if(areagrade > 1){
           dotype2();
         } 
    }
});
function dotype2(){
	if(areagrade > 1){
	var findvalue = $('#area1').find("option:selected").val();
 	$('#liarea2').remove();
 		var htmls = template.render('areaid2', {list:arealist,showid:findvalue,selectid:mydefaultadress.areaid2});
          $('#areatop').before(htmls);
          if(areagrade > 2){
 		 dotype3();
 		} 
 	} 
}
function dotype3(){
	if(areagrade > 2){
	var findvalue = $('#area2').find("option:selected").val();
 	$('#liarea3').remove();
 		var htmls = template.render('areaid3', {list:arealist,showid:findvalue,selectid:mydefaultadress.areaid3});
          $('#areatop').before(htmls); 
  } 
}
 
	</script> 
<div style="height:20px;clear:both;"></div>
 
<style>

.footer01 .dianhua{  width:250px; height:83px; float:left; font-family:微软雅黑;}
.footer01 .dianhua ul{}
.footer01 .dianhua li{ float:left; display:inline; width:154px; padding-left:80px; color:#FFFFFF}
.footer01 .dianhua li b{ color:#6CCB3B}
.footer01 .dianhua li b#font24{ font-size:20px; color:#ffffff;}
.footer01 .dianhua li.li1{ background:url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/img/index_r62_c6.png) left center no-repeat; height:74px; width:170px}
.footer01 .dianhua li.li2{ background: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/img/iconfont-mark.png) left center no-repeat; height:74px;background-size: 50px 50px;}
.footer01 .dianhua li b#font24 a {
color: #fff;
}
</style>



<div class="footer01" style="height:245px;">
<div class="zdy1" style="background:#f38383; height:20px;"></div>
		  <div class="zdy2"  style="background:#f38383; width:100%; margin:0 auto;height:170px;">
				  <div class="box02">

					
					   <div style="float:left; width:490px">
					   
						 <?php echo smarty_function_load_data(array('assign'=>"list",'table'=>"newstype",'where'=>"displaytype=1 and parent_id=0",'fileds'=>"*",'limit'=>3,'orderby'=>"orderid asc"),$_smarty_tpl);?>
 
								 <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['mykey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['mykey']->value = $_smarty_tpl->tpl_vars['items']->key;
?>   
					   
						<dl>
						   <dt ><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</dt>
								<?php if ($_smarty_tpl->tpl_vars['items']->value['type']==2){?>
									  <?php echo smarty_function_load_data(array('assign'=>"list2",'table'=>"newstype",'fileds'=>"*",'where'=>"parent_id=".((string)$_smarty_tpl->tpl_vars['items']->value['id']),'limit'=>4),$_smarty_tpl);?>
 
									   <?php  $_smarty_tpl->tpl_vars['itv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list2']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itv']->key => $_smarty_tpl->tpl_vars['itv']->value){
$_smarty_tpl->tpl_vars['itv']->_loop = true;
?>  
						   
						   <dd><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/newstype/id/".((string)$_smarty_tpl->tpl_vars['itv']->value['id'])),$_smarty_tpl);?>
" ><?php echo $_smarty_tpl->tpl_vars['itv']->value['name'];?>
</a></dd>
						   
							<?php } ?> 
							
							
							
									  
									  
								  <?php }else{ ?>
									  <?php echo smarty_function_load_data(array('assign'=>"list2",'table'=>"news",'fileds'=>"id,title,typeid",'where'=>"typeid=".((string)$_smarty_tpl->tpl_vars['items']->value['id']),'limit'=>4),$_smarty_tpl);?>
 
									  <?php  $_smarty_tpl->tpl_vars['itv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list2']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itv']->key => $_smarty_tpl->tpl_vars['itv']->value){
$_smarty_tpl->tpl_vars['itv']->_loop = true;
?>    
									   <dd><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/news/id/".((string)$_smarty_tpl->tpl_vars['itv']->value['id'])),$_smarty_tpl);?>
" ><?php echo $_smarty_tpl->tpl_vars['itv']->value['title'];?>
</a></dd>
									  
										<?php } ?> 
								 <?php }?>
						  
						</dl>
						 <?php } ?>   
						</div>
						
						
						   <div class="dianhua" style="width:320px;">
						 <ul>
						   <li class="li1" style="background:none; margin-left:20px; width:135px; padding-left:0px;">
                               <img <?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['appewm']->value==''){?><?php echo (string)$_smarty_tpl->tpl_vars['siteurl']->value;?><?php echo "/upload/app/m6app_ewm.png";?><?php }?><?php $_tmp1=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['appewm']->value).$_tmp1),$_smarty_tpl);?>
 alt="手机APP下载" width="122" height="122"  /><br><br>

                               <span style="margin-left:15px;font-size:14px">手机APP下载</span>
							</li>
						   <li class="li2" style="background:none; margin-left:20px;  width:135px;padding-left:0px;">

                               <img <?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['wxewm']->value==''){?><?php echo (string)$_smarty_tpl->tpl_vars['siteurl']->value;?><?php echo "/upload/app/m6wx_ewm.png";?><?php }?><?php $_tmp2=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['wxewm']->value).$_tmp2),$_smarty_tpl);?>
  alt="微信端扫描二维码" width="122" height="122"  /><br><br>
                               <span style="margin-left:8px;font-size:14px">微信端扫描二维码</span>
						   </li>
						 </ul>
					   </div>
						
						
						   <div class="dianhua" style="float:right;">
						 <ul>
						   <li class="li1"><strong>客服电话</strong><br /><b id="font24"><?php echo $_smarty_tpl->tpl_vars['litel']->value;?>
</b><br />周一至周日&nbsp;09:30-21:30
						   
						  
											 </li>
						   <li class="li2"><strong>欢迎留言 </strong><br /><b id="font24"><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/ask/index"),$_smarty_tpl);?>
" >反馈留言</a></b><br />您的意见对我们至关重要</li>
						 </ul>
					   </div>
						
						
				</div> 
		</div>
				 <div class="footer02" style="height:50px; margin-top:5px;">
			  <p>
			  
			  <P class="" >
			     <?php if (!empty($_smarty_tpl->tpl_vars['toplink']->value)){?>
	 	     	<?php $_smarty_tpl->tpl_vars['toplink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['toplink']->value), null, 0);?>
       <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['toplink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['items']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['items']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
 $_smarty_tpl->tpl_vars['items']->iteration++;
 $_smarty_tpl->tpl_vars['items']->last = $_smarty_tpl->tpl_vars['items']->iteration === $_smarty_tpl->tpl_vars['items']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listname']['last'] = $_smarty_tpl->tpl_vars['items']->last;
?> 
			        
					   <a href="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
" class=""  data="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
"><span style="line-height:16px;"><?php echo $_smarty_tpl->tpl_vars['items']->value['typename'];?>
</span>
					    <?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['listname']['last']){?><span> &nbsp;&nbsp;|&nbsp;&nbsp; </span><?php }?>
						</a> 
		  <?php } ?>
		  <?php }?>
         	
			  
			  </P>
			  <P class="zdy3">Copyright©2018-2019 <?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
  | <?php echo $_smarty_tpl->tpl_vars['beian']->value;?>
    <?php echo $_smarty_tpl->tpl_vars['footerdata']->value;?>
 </P>
			   
			  </div> 

 
</div>



</body>
</html><?php }} ?>