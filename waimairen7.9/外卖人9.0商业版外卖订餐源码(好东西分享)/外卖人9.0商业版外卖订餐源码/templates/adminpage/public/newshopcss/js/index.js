$(function(){  
	var url = $.cookie('admin_ifurl'); 
    var act = $.cookie('admin_ifact'); 
	var mod = $.cookie('admin_ifmod');

	if(url == '' || url == null || url == undefined || url == 'undefined' || url == 'null'){
		var url = siteurl+'/index.php?ctrl=adminpage&action=system&module=sindex';
	}	 
	$('#index_iframe').attr('src',url); 
	$(".nav_top li>.dropdown-menu").each(function(){ 
		$(this).parent().click(function(){ 
  			var navtop = $(this).offset().top-61; 
			$(this).find("ul").css("top", -navtop+"px");
			$(this).siblings().find("ul").css("display", "none");
			$(this).find("ul").css("display", "block"); 
		})
	});
	
	if(act == '' || act == null || act == undefined || act == 'undefined' || act == 'null'){
		
	}else{		 
		$(".onebtn").removeClass('open');
		$('.nav_top').find('li[act='+act+']').addClass('open');		 
	}
	if(mod == '' || mod == null || mod == undefined || mod == 'undefined' || mod == 'null'){
		
	}else{
		$(".dropdown-menu li").removeClass('subnavaA');
		$(".onebtn").removeClass('open');
		$('.dropdown-menu').find('li[data='+mod+']').addClass('subnavaA');
		$('.dropdown-menu').find('li[data='+mod+']').parent().parent().addClass('open');		 
	}
    $(".dropdown-menu li").removeClass('open');	
	var navtop = $('.open .dropdown-menu').offset().top-56;
	console.log('navtop---'+navtop);
	$('.open .dropdown-menu').css("top", -navtop+"px");
	$('.dropdown-menu').css("display", "none");
	$('.open .dropdown-menu').css("display", "block");

	$('.index_navtit').show();
	$('.index_left').css('width','210px');
	$('.col-sm-5').css('width','41.66666667%');
	$('.index_right.left, .index_right_head.left').css('left','210px');

	$(".onebtn").click(function(){	 		 
		$(".onebtn").removeClass('open');
		$(this).addClass('open');
		var act = $(this).attr('act');
		var mod = $(this).attr('data');	   
		$(".dropdown-menu li").removeClass('subnavaA');
		$('.dropdown-menu').find('li[data='+mod+']').addClass('subnavaA');
		var url = siteurl+'/index.php?ctrl=adminpage&action='+act+'&module='+mod;
		$.cookie("admin_ifurl",url,{path: "/", expiress: 1});
		$.cookie("admin_ifact",act,{path: "/", expiress: 1});
		$.cookie("admin_ifmod",mod,{path: "/", expiress: 1}); 
		$('.index_navtit').show();
		$('.index_left').css('width','210px');
		$('.col-sm-5').css('width','41.66666667%');
		$('.index_right.left, .index_right_head.left').css('left','210px');
		$('#index_iframe').attr('src',url);		
		return false;
	})
	$(".dropdown-menu li").click(function(){
        
		var act = $(this).attr('act');
		var mod = $(this).attr('data'); 	
		var url = siteurl+'/index.php?ctrl=adminpage&action='+act+'&module='+mod;
        $.cookie("admin_ifurl",url,{path: "/", expiress: 1});
		$.cookie("admin_ifact",act,{path: "/", expiress: 1});
		$.cookie("admin_ifmod",mod,{path: "/", expiress: 1});			
		$('#index_iframe').attr('src',url);
		$(".dropdown-menu li").removeClass('subnavaA');
		$(".dropdown-menu li").removeClass('open');		
		$(this).addClass('subnavaA');	      
		return false;
	})
	
	 
	
})
 


