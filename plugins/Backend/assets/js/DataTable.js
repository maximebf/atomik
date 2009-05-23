(function($) {
	
	$.fn.dataTable = function(options) {
		options = $.extend({
			'remote': 'remote.php',
			'url': 'edit.php',
			'url_param': 'id'
		}, options);
		
		this.each(function() {
			var self = $(this);
			
			self.addClass('datatable');
			
			self.find('tbody tr').hover(
				function() { $(this).addClass('hover'); },
				function() { $(this).removeClass('hover'); }
			);
			
			self.find('tbody td.clickable').click(function() {
				var queryString = options.url_param + '=' + $(this).parent().attr('rel');
				document.location = options.url + (options.url.indexOf('?') != -1 ? '&' : '?') + queryString;
			});
			
			self.find('tbody td.actions a.action').click(function() {
				var queryString = options.url_param + '=' + $(this).parent().parent().attr('rel');
				document.location = this.href + (this.href.indexOf('?') != -1 ? '&' : '?') + queryString;
				return false;
			});
		});
	};
	
})(jQuery);