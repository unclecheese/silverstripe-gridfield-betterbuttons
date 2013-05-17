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
			trigger = this.getButton();
			button_top = trigger.position().top;
			button_left = trigger.position().left;
			this.css({
				left: button_left,
				top: button_top - 127 // This is so ghetto.
			});

			this.find("li > button").each(function() {
				$(this).button('destroy');
			})

		},

		getButton: function() {
			return $('button[data-form-action-dropdown="#'+this.attr('id')+'"]');
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