$(function(){  
	var url = $.cookie('cook_ifurl'); 
    var ctr = $.cookie('cook_ifctr'); 
	var act = $.cookie('cook_ifact');
 	var is_cz = true;
	$(".nav_top li").each(function(){ 
		var curattr = $(this).attr('data');
		if( curattr != ctr ){
			is_cz =false;
		}else{
			is_cz = true;
			return false;
		}
	}); 
	if (is_cz){
 	}else{
		console.log("不存在--"+ctr);
		url = '';
		ctr = '';
		act = '';
	}
	if(url == '' || url == null || url == undefined || url == 'undefined' || url == 'null'){
		var url = siteurl+'/index.php?ctrl=shopcenter&action=useredit';
	}	 
	$('#index_iframe').attr('src',url); 
	
	$(".nav_top li>.dropdown-menu").each(function(){ 
		$(this).parent().click(function(){ 
  			var navtop = $(this).offset().top-50; 
			$(this).find("ul").css("top", -navtop+"px");
			$(this).siblings().find("ul").css("display", "none");
			$(this).find("ul").css("display", "block"); 
		})
	});
	
	if(ctr == '' || ctr == null || ctr == undefined || ctr == 'undefined' || ctr == 'null'){
		
	}else{
		$('.index_navtit ul li').removeClass('navaA');
		$('.index_navtit ').find('li[data='+ctr+']').addClass('navaA');
		$(".onebtn").removeClass('open');
		$('.nav_top').find('li[data='+ctr+']').addClass('open');		 
	}
	if(act == '' || act == null || act == undefined || act == 'undefined' || act == 'null'){
		
	}else{
		$(".dropdown-menu li").removeClass('subnavaA');
		$('.dropdown-menu').find('li[data='+act+']').addClass('subnavaA');
	}
    $(".dropdown-menu li").removeClass('open');	
	var navtop = $('.open .dropdown-menu').offset().top-30;
	console.log('navtop---'+navtop);
	$('.open .dropdown-menu').css("top", -navtop+"px");
	$('.dropdown-menu').css("display", "none");
	$('.open .dropdown-menu').css("display", "block");
	var  select_first_menu_obj = $(".nav_top li.open");
	var menu_son_html = select_first_menu_obj.find('ul.dropdown-menu').html();
 	if(menu_son_html==''){
		$('.index_navtit').hide();
		$('.index_left').css('width','88px');
		$('.col-sm-5').css('width','100%');
		$('.index_right.left, .index_right_head.left').css('left','88px');	
		var yxmtext = select_first_menu_obj.find('a').text();
		 
	}else{
		$('.index_navtit').show();
		$('.index_left').css('width','210px');
		$('.col-sm-5').css('width','41.66666667%');
		$('.index_right.left, .index_right_head.left').css('left','210px');
		var yxmtext = select_first_menu_obj.find('ul.dropdown-menu li.subnavaA a').text();
		 
	}
   checktext(yxmtext);
	$(".onebtn").click(function(){	 		 
		$(".onebtn").removeClass('open');
		$(this).addClass('open');
		var act = $(this).attr('data');
 		$('.index_navtit ul li').removeClass('navaA');
		$('.index_navtit').find('li[data='+act+']').addClass('navaA');
	    $.cookie('cook_ifact', null);
		$(".dropdown-menu li").removeClass('subnavaA');
		$('.dropdown-menu').find('li[data='+act+']').addClass('subnavaA');
		var url = siteurl+'/index.php?ctrl=shopcenter&action='+act;
		$.cookie("cook_ifurl",url,{path: "/", expiress: 1});
		$.cookie("cook_ifctr",act,{path: "/", expiress: 1});
		//var yxmtext = $('.subnavaA a').text();	 
		//$('.yxmcl').text(yxmtext);	
		
		var menu_son_html = $(this).find('ul.dropdown-menu').html();
		if(menu_son_html==''){
			$('.index_navtit').hide();
			$('.index_left').css('width','88px');
			$('.col-sm-5').css('width','100%');
			$('.index_right.left, .index_right_head.left').css('left','88px');	
			var yxmtext = $('.nav_top li.open a').text();
		}else{
			$('.index_navtit').show();
			$('.index_left').css('width','210px');
			$('.col-sm-5').css('width','41.66666667%');
			$('.index_right.left, .index_right_head.left').css('left','210px');
			var yxmtext = $('.subnavaA a').text();	 
		}
		checktext(yxmtext);   
		
		$('#index_iframe').attr('src',url);		
		return false;
	})
	$(".dropdown-menu li").click(function(){
        
		var act = $(this).attr('data');		
		var url = siteurl+'/index.php?ctrl=shopcenter&action='+act;	 
        $.cookie("cook_ifurl",url,{path: "/", expiress: 1});	
        $.cookie("cook_ifact",act,{path: "/", expiress: 1});			
		$('#index_iframe').attr('src',url);
		$(".dropdown-menu li").removeClass('subnavaA');
		$(".dropdown-menu li").removeClass('open');		
		$(this).addClass('subnavaA');	
        var yxmtext = $('.subnavaA a').text();
		checktext(yxmtext);
		return false;
	})
	function checktext(yxmtext){
		var orderhtml =  '<li data="1" class="col-sm-1 col-xs-3 navaA"><a class="yxmcl" href="javascript:;">待接单</a></li>' 
						+'<li data="2" class="col-sm-1 col-xs-3 "><a class="yxmcl" href="javascript:;">待发货</a></li>'
						+'<li data="3" class="col-sm-1 col-xs-3 "><a class="yxmcl" href="javascript:;">已发货</a></li>' 
						+'<li data="4" class="col-sm-1 col-xs-3 "><a class="yxmcl" href="javascript:;">已完成</a></li>'
						+'<li data="5" class="col-sm-1 col-xs-3 "><a class="yxmcl" href="javascript:;">退款单</a></li>'
						+'<li data="6" class="col-sm-1 col-xs-3 "><a class="yxmcl" href="javascript:;">已取消</a></li>'
		var normalhtml = '<li class="col-sm-1 col-xs-3 navaA"><a class="yxmcl" href="javascript:;">'+yxmtext+'</a></li>' 
		if(yxmtext =='订单查询'){
			$('.index_right_head_subnav .list-inline').html(orderhtml);		
            doselect();			
		}else{
			$('.index_right_head_subnav .list-inline').html(normalhtml); 
		}	
	}
	function doselect(){
		$('.index_right_head_subnav .list-inline li').click(function(){
			$('.index_right_head_subnav .list-inline li').removeClass('navaA');
			$(this).addClass('navaA');	
			var orderstatus = $(this).attr('data');
			var url = siteurl+'/index.php?ctrl=shopcenter&action=shoporderlist&orderSource='+orderstatus;	
			$('#index_iframe').attr('src',url);		           
		});
	}
	
})
 


