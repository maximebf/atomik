(function($) {
	
	$.fn.dataTable = function(options) {
		options = $.extend({
			'remote': 'remote.php',
			'url': 'edit.php'
		}, options);
		
		this.each(function() {
			var self = $(this);
			
			self.addClass('datatable');
			
			self.find('tbody tr').hover(
				function() { $(this).addClass('hover'); },
				function() { $(this).removeClass('hover'); }
			);
			
			self.find('tbody td.clickable').click(function() {
				document.location = options.url + '?id=' + $(this).parent().attr('rel');
			});
		});
	};
	
})(jQuery);