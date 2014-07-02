(function($) {
$.entwine('ss', function($) {

	$('.cms #Form_ItemEditForm .Actions button.gridfield-better-buttons-delete').entwine({
		
		Toggled: false,

		onadd: function() {
			var text = this.data('confirmtext');
			this.before("&nbsp; <a class='gridfield-better-buttons-undodelete ss-ui-button' href='javascript:void(0)'>"+text+"</a>");
			this._super();
		},

		onclick: function(e) {
			e.preventDefault();
			
			if(this.getToggled()) {
				return this._super(e);
			}
			this.toggleText();			
			$('.gridfield-better-buttons-undodelete').show();


		},


		toggleText: function() {
			var text = this.find(".ui-button-text").text();
			this.find(".ui-button-text").text(this.data('toggletext'));
			this.data('toggletext', text);
			this.setToggled(!this.getToggled());
		}
	});


	$('.gridfield-better-buttons-undodelete').entwine({

		onclick: function(e) {			
			e.preventDefault();
			$('.gridfield-better-buttons-delete').toggleText();
			this.hide();
		}
	})


});
})(jQuery);