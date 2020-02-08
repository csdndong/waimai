$(function(){
	
	$("#radius1").click(function(){
		$(".radius").addClass("hidden");
	})
	
	$("#radius2").click(function(){
		$(".radius").removeClass("hidden");
	})
		
	$("#platform1").click(function(){
		$(".platform_prompt").addClass("hidden");
		$(".citywide_show").removeClass("hidden");
	})
	
	$("#platform2").click(function(){
		$(".platform_prompt").removeClass("hidden");
		$(".citywide_show").addClass("hidden");
	})
	
	$("#fixedfee1").click(function(){
		$(".setup_price").addClass("hidden");
		$(".citywide_fixed").removeClass("hidden");
	})
	
	$("#fixedfee2").click(function(){
		$(".setup_price").removeClass("hidden");
		$(".citywide_fixed").addClass("hidden");
	})
	
	$("#setupfree").click(function(){
		$(".setup_free_price").toggleClass('hidden');
	});
	
	$("#pre1").click(function(){
		$(".pre_prompt").addClass("hidden");
	})
	
	$("#pre2").click(function(){
		$(".pre_prompt").removeClass("hidden");
	})
	
	$(".interval_box li").click(function(){
		$(this).toggleClass('navaA');
	})
	
	$(".goodsclass_list_box ul li").on('click','.fa-plus-square-o',function(){
		$(this).toggleClass('fa-minus-square-o')
		$(this).parents('.goodsclass_list_head').siblings('.goodsclass_list_sub').toggleClass('hidden');
	})
	

	/*-------------底部漂浮按钮-------------*/
	$(window).scroll(function(){
		$(window).scrollTop();
		$windowH = parseInt($(window).height());
		$documentH = parseInt($(document).height());
		var bot = 100; 
        if ((bot + $(window).scrollTop()) >= ($(document).height() - $(window).height())) {
        	$(".bottom_btn_content").hide();
        }else {
        	$(".bottom_btn_content").show();
        }
	});
	
	
	$(".select_standard_head button").click(function(){
		$(".select_standard_input").toggle();
	})
	
	$(".select_standard_input").click(function(event){
		event.stopPropagation();
	})
	
	$(document).click(function(){
		$(".select_standard_input").hide();
	})
	
	$(".select_standard_head .dropdown-menu > li > a").click(function(){
		$(".select_standard_input").hide();
	})
	
	$(".spec_list ul li span i").click(function(){
		$(this).parents('.spec_list ul li').remove()
	})
	
	$(".spec_addto_txt ul li i").click(function(){
		$(this).parents('.spec_addto_txt ul li').remove()
	})
	
	
	$(".order_list_main .order_list").on('click','table thead tr td input',function(){
		if ($(this).is(':checked')) {
			$(this).parents('.order_list').addClass('navaA')
		} else{
			$(this).parents('.order_list').removeClass('navaA')
		}
	})
	
	
	
	
})





