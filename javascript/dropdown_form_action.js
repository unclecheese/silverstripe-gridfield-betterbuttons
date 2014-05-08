(function($) {

$.entwine('ss', function($) {

	$('[data-form-action-dropdown]').entwine({
		
		onmatch: function() {
			$(document).bind("click", function(e) {
				if($(e.target).closest("[data-form-action-dropdown]").length) {
					return;
				}
				else {									
					$('.dropdown-form-action').hide();
				}
			});


		},


		onclick: function(e) {
			e.preventDefault();
			e.stopPropagation();
			$('.dropdown-form-action').not(this.getDropdown()).hide();
			if(!this.getDropdown().is(":visible")) {
				this.getDropdown().show();				
			}
			else {
				this.getDropdown().hide();
			}
		},



		getDropdown: function() {
			return $(this.attr('data-form-action-dropdown'));
		}


	});


	$('.dropdown-form-action').entwine({

		onmatch: function() {
			this.find("li > button").each(function() {
				$(this).button('destroy');
			})
		}
		
	});


	$('.dropdown-form-action li button').entwine({

		onmatch: function() {
			if(this.is(".disabled")) {
				this.attr('disabled', true);
			}
		}

	});


});
})(jQuery);