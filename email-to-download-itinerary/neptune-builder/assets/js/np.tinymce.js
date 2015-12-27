(function($){
	
	$(document).ready(function() {
		initShortCodeBuilderLightBox();
		initShowShortCodeAttrOnSelect();
		initShortCodeInsertEditor();
	});
	
	var initShortCodeBuilderLightBox = function() {
		$('.np-shortcode-builder-button').click(function(e){
			e.preventDefault();
			
			var id = $(this).data('src'),
			    html = $('#' + id).html();
			    
			$.colorbox({
				width: 800,
				height: 600,
				html: html
				});
			
			return false;
		});
	}
	
	var initShowShortCodeAttrOnSelect = function() {
		$(document).on( 'change', '.np-select', function() {
			
			var _this = $(this),
			    _parent = _this.closest('.np-selector');
			
			var val = _this.val();
			if( val != '' ){
				_parent.find('table').hide();
				_parent.find('table#' + val).show();
			}
			
		});
	}
	
	var initShortCodeInsertEditor = function() {
		$(document).on( 'click', '.insert_editor', function(e) {
			e.preventDefault();
			
			var _this = $(this),
			    _parent = _this.closest('.np-selector');
			    
			if( _parent.find('.np-select').val() != '' ) {
			
				var shortcode = '[' + _parent.find('.np-select').val();
				var atts = '';
				
				_parent.find('.np_sc_builder_tbl').filter(':visible').find(':input').filter('[name]').each(function(){
					var $this = $(this);
					if ( ($.trim($this.val()).length == 0 || ($this.attr('data-default') !== undefined && $this.attr('data-default') == $.trim($this.val())))) {
						return; // Don't include empty fields or fields that are default values
					}
					
					//atts += ' ' + $this.attr('name') + '="' + $this.val() + '"';
					
					if ( $this.is(':radio') || $this.is(':checkbox') ) {
						if ( $this.is(':checked') ) {
							atts += ' ' + $this.attr('name') + '="' + $this.val() + '"';
						}
					} else {
						atts += ' ' + $this.attr('name') + '="' + $this.val() + '"';
					}
				});
				
				shortcode += atts + ']';
				window.send_to_editor(shortcode);
				$.colorbox.close();
			}
			
			return false;
		});
	}
	
}(jQuery));