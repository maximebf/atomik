
(function($) {
	
	var defaultOptions = {
		confirm: false,
		confirmMessage: 'Are you sure?',
			
		flashMessage: function(message, label) {
			alert(label + ': ' + message);
		},
		
		overlay: {
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
		}
	};
	
	var executeXhr = function(url, data, method, subject) {
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
	
	$.fn.ajaxify = function(options) {
		options = $.extend(defaultOptions, options || {});
		this.each(function(el) {
			var $self = $(this);
			if (!$self.hasClass('ajaxify')) {
				return;
			}
			
			if ($self.hasClass('confirm') || options.confirm) {
				var msg = $self.data('confirm') || options.confirmMessage;
				if (!confirm(msg)) {
					return false;
				}
			}
			
			if (this.tagName == 'a') {
				$self.click(function() {
					executeXhr(this.href, null, 'get', $self);
					return false;
				});
			} else if (this.tagName == 'form') {
				$self.submit(function() {
			        var url = $self.attr('action'),
			        	data = $self.find(':input').serialize();
			        executeXhr(url, data, $self.attr('method'), $self);
			        return false;
				});
			}
		});
	};
	
	$.ajaxify = {
		defaults: defaultOptions,
		load: executeXhr
	};
	
})(jQuery);
