<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 20:12:53
         compiled from "D:\wwwroot\demo.52jscn.com\templates\m7\public\bottom.html" */ ?>
<?php /*%%SmartyHeaderCode:197005cd56ac52babb4-66935046%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e16b6b89ef158c01bb8b710d1d5f1c45cde3e72a' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\public\\bottom.html',
      1 => 1536572159,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '197005cd56ac52babb4-66935046',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'siteurl' => 0,
    'shangou' => 0,
    'paotui' => 0,
    'say' => 0,
    'color' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd56ac543cc07_55735871',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd56ac543cc07_55735871')) {function content_5cd56ac543cc07_55735871($_smarty_tpl) {?>
 
    <div class="bottom-bar-warp">
        <div class="bottom-bar" id="bottom-bar" style='width:100%;display: flex;flex-direction: row;'>
            <div class="bbar-btn tap-click" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/index"),$_smarty_tpl);?>
');"  >
			    <img style='width:25px;margin-top:5px' src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/images/bottom/home/home.png'>
				<div class="text homebtn" style="margin-top:-8px;">首页</div>
			</div>
			<?php if ($_smarty_tpl->tpl_vars['shangou']->value==1){?>
            <div class="bbar-btn tap-click" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/marketshop"),$_smarty_tpl);?>
');"  >
			    <img style='width:25px;margin-top:5px' src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/images/bottom/shangou/shangou.png'>
				<div class="text shangoubtn" style="margin-top:-8px;">闪购</div>
			</div>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['paotui']->value==1){?>
            <div class="bbar-btn tap-click" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/paotui"),$_smarty_tpl);?>
');"  >
			    <img style='width:25px;margin-top:5px' src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/images/bottom/paotui/paotui.png'>
				<div class="text paotuibtn" style="margin-top:-8px;">跑腿</div>
			</div>
			<?php }?>
			<div class="bbar-btn tap-click" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/order"),$_smarty_tpl);?>
');" >
				<img style='width:25px;margin-top:5px' src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/images/bottom/order/order.png'>
				<div class="text orderbtn" style="margin-top:-8px;">订单</div>
			</div>
			<?php if ($_smarty_tpl->tpl_vars['say']->value==1){?>
            <div class="bbar-btn tap-click" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/togethersay"),$_smarty_tpl);?>
');"  >
				<img style='width:25px;margin-top:5px' src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/images/bottom/say/say.png'>
				<div class="text saybtn" style="margin-top:-8px;">一起说</div>
			</div>
			<?php }?>
            <div class="bbar-btn tap-click"  onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/member"),$_smarty_tpl);?>
');">
				<img style='width:25px;margin-top:5px' src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/images/bottom/mycenter/mycenter.png'>
				<div class="text memberbtn"  style="margin-top:-10px;">我的</div>
			</div>
        </div>
    </div>




<?php if ($_smarty_tpl->tpl_vars['color']->value=="green"){?>
<script>
    var siteurl = '<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
';
	$(function(){
		if( taction  == 'index' ){          	
			$(".homebtn").css('color','#00cd85');			
			$(".homebtn").prev().attr('src',siteurl+'/images/bottom/home/home_g.png');		
		}
		if( taction  == 'member' ){		
			$(".memberbtn").css('color','#00cd85');
			$(".memberbtn").prev().attr('src',siteurl+'/images/bottom/mycenter/mycenter_g.png');		
		}
		if( taction  == 'order' ){		
			$(".orderbtn").css('color','#00cd85');
			$(".orderbtn").prev().attr('src',siteurl+'/images/bottom/order/order_g.png');		
		}
		if( taction  == 'togethersay' ){		
			$(".saybtn").css('color','#00cd85');
			$(".saybtn").prev().attr('src',siteurl+'/images/bottom/say/say_g.png');		
		}
		if( taction  == 'paotui' ){		
			$(".paotuibtn").css('color','#00cd85');
			$(".paotuibtn").prev().attr('src',siteurl+'/images/bottom/paotui/paotui_g.png');		
		}
		if( taction  == 'marketshop' ){		
			$(".shangoubtn").css('color','#00cd85');
			$(".shangoubtn").prev().attr('src',siteurl+'/images/bottom/shangou/shangou_g.png');		
		}
	});
</script>
<?php }?>


<?php if ($_smarty_tpl->tpl_vars['color']->value=="yellow"){?>
<script>
	$(function(){
		if( taction  == 'index' ){          	
			$(".homebtn").css('color','#ff7600 ');			
			$(".homebtn").prev().attr('src',siteurl+'/images/bottom/home/home_y.png');		
		}
		if( taction  == 'member' ){		
			$(".memberbtn").css('color','#ff7600 ');
			$(".memberbtn").prev().attr('src',siteurl+'/images/bottom/mycenter/mycenter_y.png');		
		}
		if( taction  == 'order' ){		
			$(".orderbtn").css('color','#ff7600 ');
			$(".orderbtn").prev().attr('src',siteurl+'/images/bottom/order/order_y.png');		
		}
		if( taction  == 'togethersay' ){		
			$(".saybtn").css('color','#ff7600 ');
			$(".saybtn").prev().attr('src',siteurl+'/images/bottom/say/say_y.png');		
		}
		if( taction  == 'paotui' ){		
			$(".paotuibtn").css('color','#ff7600');
			$(".paotuibtn").prev().attr('src',siteurl+'/images/bottom/paotui/paotui_y.png');		
		}
		if( taction  == 'marketshop' ){		
			$(".shangoubtn").css('color','#ff7600');
			$(".shangoubtn").prev().attr('src',siteurl+'/images/bottom/shangou/shangou_y.png');		
		}
	});
</script>

<?php }?>

<?php if ($_smarty_tpl->tpl_vars['color']->value=="red"||empty($_smarty_tpl->tpl_vars['color']->value)){?>
<script>
	$(function(){
		if( taction  == 'index' ){          	
			$(".homebtn").css('color','#ff6e6e');			
			$(".homebtn").prev().attr('src',siteurl+'/images/bottom/home/home_r.png');		
		}
		if( taction  == 'member' ){		
			$(".memberbtn").css('color','#ff6e6e');
			$(".memberbtn").prev().attr('src',siteurl+'/images/bottom/mycenter/mycenter_r.png');		
		}
		if( taction  == 'order' ){		
			$(".orderbtn").css('color','#ff6e6e');
			$(".orderbtn").prev().attr('src',siteurl+'/images/bottom/order/order_r.png');		
		}
			if( taction  == 'togethersay' ){		
			$(".saybtn").css('color','#ff6e6e');
			$(".saybtn").prev().attr('src',siteurl+'/images/bottom/say/say_r.png');		
		}
		if( taction  == 'paotui' ){		
			$(".paotuibtn").css('color','#ff6e6e');
			$(".paotuibtn").prev().attr('src',siteurl+'/images/bottom/paotui/paotui_r.png');		
		}
		if( taction  == 'marketshop' ){		
			$(".shangoubtn").css('color','#ff6e6e');
			$(".shangoubtn").prev().attr('src',siteurl+'/images/bottom/shangou/shangou_r.png');		
		}
	});
</script>
 
<?php }?><?php }} ?>