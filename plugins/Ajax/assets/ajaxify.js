
var Ajaxify = (function() {
	
	var ajaxify = function(container) {
		if (!container) {
			container = 'body';
		}
		
		$(container).find('a.ajaxify').live('click', function() {
			if ($(this).hasClass('confirm')) {
				var msg = $(this).attr('confirmMsg') || "Are you sure?";
				if (!confirm(msg)) {
					return false;
				}
			}
			ajaxify.load(this.href, null, 'get', $(this));
			return false;
		});
		
		$(container).find('form.ajaxify').live('submit', function() {
	        var url = $(this).attr('action');
	        var data = $(this).find(':input').serialize();
	        ajaxify.load(url, data, $(this).attr('method'), $(this));
	        return false;
		});
	};
	
	ajaxify.flashMessage = function(message, label) {
		alert(label + ': ' + message);
	};
	
	ajaxify.load = function(url, data, method, subject) {
		if (ajaxify.overlay.enabled) {
			ajaxify.overlay.show();
		}
		
		var xhr = $.ajax({
			'url': url,
			'type': method,
			'data': data, 
			'dataType': 'html',
			'success': function(result) {
				if (ajaxify.overlay.enabled) {
					ajaxify.overlay.hide();
				}
				
		        // retrieve headers
		        var messages = xhr.getResponseHeader('Flash-messages'),
		        	updateText = xhr.getResponseHeader('X-Ajax-Update-Text'),
		            updateHref = xhr.getResponseHeader('X-Ajax-Update-Href'),
		            updateClass = xhr.getResponseHeader('X-Ajax-Update-Class'),
		            callback = xhr.getResponseHeader('X-Ajax-Callback'),
		            target = xhr.getResponseHeader('X-Ajax-Target'),
		            title = xhr.getResponseHeader('X-Ajax-Title');
		        
				if (messages) {
					messages = eval('(' + messages  + ')');
					for (var label in messages) {
						for (var i in messages[label]) {
							ajaxify.flashMessage(messages[label][i], label);
						}
					}
				}
		
		        if (subject && updateText) subject.html(updateText);
		        if (subject && updateHref) subject.attr('href', updateHref);
		        if (subject && updateClass) subject.attr('class', updateClass);
		        if (title) document.title = title;
		        if (target) $(target).html(result);
		        if (callback) window[callback](xhr, result, subject);
			}
		});
	};
	
	ajaxify.overlay = {
		'enabled': false,
		'element': $('<div></div>').css({
						'background': '#fff', 
						'opacity': 0.6, 
						'display': 'none'}).appendTo('body'),
		'subject': $('body'),
		'show': function() {
			var s = ajaxify.overlay.subject;
			ajaxify.overlay.element.css({
				top: s.position().top,
				left: s.position().left,
				width: s.width(),
				height: s.height(),
				display: 'block'
			});
		},
		'hide': function() {
			ajaxify.overlay.element.css('display', 'none');
		}
	};
	
	return ajaxify;
	
})();
