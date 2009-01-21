/**
 * Helpers functions for the backend
 */
 
$(document).ready(function() {
	$('.file-list-row').hover(
		function() { $(this).addClass('hover'); },
		function() { $(this).removeClass('hover'); }
	);
});

var Atomik = {
	
	/**
	 * Hides messages
	 */
	hideMessages: function()
	{
		$('#messages .message:first-child').slideUp('slow', function() { $(this).remove() });
	},
	
	addMessage: function(msg, label)
	{
		var el = $('<div class="message ' + label + '">' + msg + '</div>');
		$('#messages').append(el).slideDown('slow');
		
		setTimeout('Atomik.hideMessages()', 5000);
	}

}