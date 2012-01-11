(function($) {
	$(function() {

/*
		$("#isotope-wrap h3")
			.click(function() {    
				$('.inside').slideUp('slow');    
				$(this).next().stop().slideToggle('slow');    
				return false;  
			})
			.next()
			.hide();
*/

		(function (){
			var link = $("#isotope-example-link-wrap");
			var wrap = $("#isotope-example-wrap");
								
			link.click(function() {
			
				if ( wrap.is(":visible") ) { 
					link.removeClass('screen-meta-active');
					wrap.slideUp('fast');
					link.siblings().show();
				} else {
					link.addClass('screen-meta-active');
					wrap.slideDown('fast');
					link.siblings().hide();
				}
			
			});
			

				
		}());


	});
})(jQuery);