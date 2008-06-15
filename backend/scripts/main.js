/**
 * Helpers functions for the backend
 */
 
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