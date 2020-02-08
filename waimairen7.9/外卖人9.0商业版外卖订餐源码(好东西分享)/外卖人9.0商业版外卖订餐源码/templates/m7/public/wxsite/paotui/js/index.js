$(function(){
	
	$('.helpme_tab ul li').click(function(){
		var x = $('.helpme_tab ul li').index(this);
		$(this).addClass('navaA').siblings().removeClass('navaA');
		$(".helpme_box").hide();
		$(".helpme_box").eq(x).show();
	});
	
})
