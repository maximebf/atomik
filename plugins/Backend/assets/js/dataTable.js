(function($) {
	
	/**
	 * 
	 */
	$.fn.dataTable = function(options) {
		
		// options
		options = $.extend({
			'remote': 		'remote.php',
			'url': 			'edit.php',
			'urlParam': 	'id',
			'currentPage':	1,
			'sortable':		true
		}, options);
		
		this.each(function() {
			var self = $(this),
				table = self.find('table.dataTable'),
				pager = self.find('ul.dataTablePager'),
				currentPage, reloadData;
			
			/*----------------------------------------------------------------------------------------
			 * Functions
			 */
			
			/**
			 * Attach event listeners to rows
			 */
			var attachEventListeners = function() {
				// hover
				table.find('tbody tr').hover(
					function() { $(this).addClass('hover'); },
					function() { $(this).removeClass('hover'); }
				);
				
				// clickable cells
				table.find('tbody td.clickable').css('cursor', 'pointer').click(function() {
					var queryString = options.urlParam + '=' + $(this).parent().attr('rel');
					document.location = options.url + (options.url.indexOf('?') != -1 ? '&' : '?') + queryString;
				});
				
				// rows actions
				table.find('tbody td.actions a.action').click(function() {
					var queryString = options.urlParam + '=' + $(this).parent().parent().attr('rel');
					document.location = this.href + (this.href.indexOf('?') != -1 ? '&' : '?') + queryString;
					return false;
				});
			};

			/**
			 * Loads table data and reattach event listeners from the options.remote url
			 * 
			 * @param Object data
			 * @param Function callback
			 */
			var loadTable = function(data, callback) {
				reloadData = data;
				
				var overlay = $('<div class="dataTableOverlay"></div>').appendTo(document.body),
					position = table.position();
				
				overlay.css({
					'position': 'absolute',
					'left': position.left,
					'top': position.top + parseInt(table.css('margin-top')),
					'width': table.width(),
					'height': table.height()
				});
				
				$.get(options.remote, reloadData, function(html) {
					overlay.remove();
					overlay = null;
					
					table.find('tbody').replaceWith(html);
					attachEventListeners();
					
					callback && callback();
				}, 'html');
			};
			
			/**
			 * Reloads the current table data
			 * 
			 * @param Object data Overrides for the current reload data
			 * @param Function callback
			 */
			var reloadTable = function(data, callback) {
				loadTable($.extend(reloadData, data), callback);
			};
			
			/**
			 * Updates the pager
			 */
			var updatePager = function() {
				if (!pager.length) {
					return;
				}
				
				pager.find('a').removeClass('current');
				pager.find('a[href="#' + currentPage + '"]').addClass('current');

				if (currentPage == 1) {
					pager.find('a.previous').hide();
				} else {
					pager.find('a.previous').show();
				}

				if (currentPage == options.numberOfPages) {
					pager.find('a.next').hide();
				} else {
					pager.find('a.next').show();
				}
			};
			
			/**
			 * Reloads the table data and show another page
			 * 
			 * @param int pageNumber
			 * @param Object data Overrides for the current reload data
			 * @param Function callback
			 */
			var reloadTableAndShowPage = function(pageNumber, data, callback) {
				currentPage = pageNumber;
				reloadTable($.extend({'dataTablePage' : pageNumber}, data), function() {
					updatePager();
					callback && callback();
				});
			};
			
			/**
			 * Shows the specified page
			 * 
			 * @param int pageNumber
			 */
			var showPage = function(pageNumber) {
				reloadTableAndShowPage(pageNumber, {});
			};
			
			reloadData = {};
			
			/*----------------------------------------------------------------------------------------
			 * Sorting
			 */
			
			if (options.sortable) {
				if (options.sortColumn) {
					reloadData.dataTableSortColumn = options.sortColumn;
					reloadData.dataTableSortOrder = options.sortOrder;
				}
				
				var columns = table.find('thead th');
				columns.click(function() {
					var column = $(this),
						sortAsc = !$(this).hasClass('sortAsc');
					
					columns.removeClass('sortAsc').removeClass('sortDesc');
					column.addClass(sortAsc ? 'sortAsc' : 'sortDesc');
					
					reloadTable({
						'dataTableSortColumn': column.attr('id'),
						'dataTableSortOrder': sortAsc ? 'asc' : 'desc'
					});
				});
			}
			
			/*----------------------------------------------------------------------------------------
			 * Pager
			 */
			
			if (pager.length) {
				// previous
				pager.find('a.previous').click(function() {
					if (currentPage > 1) {
						showPage(currentPage - 1);
					}
					return false;
				});
				
				// next
				pager.find('a.next').click(function() {
					if (currentPage < options.numberOfPages) {
						showPage(currentPage + 1);
					}
					return false;
				});
				
				// page numbers
				pager.find('a.page').click(function() {
					showPage(parseInt($(this).attr('href').substr(1)));
					return false;
				});
			}
			
			/*self.find('select.dataTableLength').change(function() {
				reloadTableAndShowPage(1, {'dataTableLength': parseInt(this.value)});
			});*/
			
			/*----------------------------------------------------------------------------------------
			 * Start
			 */

			currentPage = options.currentPage;
			attachEventListeners();
			updatePager();
		});
	};
	
})(jQuery);