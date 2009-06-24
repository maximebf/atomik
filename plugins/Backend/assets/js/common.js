jQuery.noConflict();

(function($) {
	
	$.ajaxSetup({
		error: function(xhr, textStatus, errorThrown) {
			alert('An error occured:' + textStatus);
		}
	});
	
})(jQuery);