(function($) {

$.entwine('ss', function($) {


	$('.better-buttons-utils button').entwine({
		onclick: function(e) {
			$('.cms-container').submitForm($('#Form_ItemEditForm'), this);
		}
	});

	$('.cms form#Form_ItemEditForm .better-button-nested-form').entwine({
		onclick: function (e) {
			e.preventDefault();
			var dialog = $('<div class="better-button-dialog"/>');			
			dialog.ssdialog({iframeUrl: this.attr('href'), height: 550});
			dialog.ssdialog('open');
		}
	});
});
})(jQuery);