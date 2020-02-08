define(['jquery.onepage'], function() {
	var guide = {};
	guide.init = function(url) {
		$(".main").onepage_scroll({
			sectionContainer: "section",
			easing: "ease-in-out",
			animationTime: 1000,
			pagination: true,
			updateURL: false,
			loop: false,
			direction: 'up'
		});

		var i = 3;
		var interval=setInterval(function(){
			$('#time span').html(i);
			i--;
			if(i < 0){
				window.location.href = url;
				clearInterval(interval);
			}
		}, 1000);
	};
	return guide;
});