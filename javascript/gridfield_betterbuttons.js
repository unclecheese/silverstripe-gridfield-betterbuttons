(function($) {

$.entwine('ss', function($) {


	$('.better-buttons-utils button').entwine({
		onclick: function(e) {
			$('.cms-container').submitForm($('#Form_ItemEditForm'), this);
		}
	});
});
})(jQuery);