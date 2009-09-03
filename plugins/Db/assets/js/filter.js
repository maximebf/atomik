jQuery(function($) {
	
	$('.model-filters li .by').click(function() {
		var self 	= $(this),
			ul		= self.next();
		
		if (ul.is(':visible')) {
			ul.slideUp(500);
		} else {
			ul.slideDown(500);
		}
		self.toggleClass('visible');
	});
	
});