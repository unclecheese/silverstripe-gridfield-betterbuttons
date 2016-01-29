(function($) {

$.entwine('ss', function($) {


	$('.better-buttons-utils button').entwine({
		onclick: function(e) {
            if(!$(this).hasClass('ui-state-disabled')){
                $('.cms-container').submitForm($('#Form_ItemEditForm'), this);
            }
            e.preventDefault();
		}
	});

    $('.better-buttons-utils select.add-new-selected').entwine({
        onchange : function(e){
            if(this.val() == ""){
                this.parent().find('button').addClass('ui-state-disabled ssui-state-disabled');
            }
            else {
                this.parent().find('button').removeClass('ui-state-disabled').removeClass('ssui-state-disabled');
            }
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