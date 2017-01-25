(function($) {
$.entwine('ss', function($) {

	$('.cms #Form_ItemEditForm .btn-toolbar button.gridfield-better-buttons-delete').entwine({

		Toggled: false,

		onadd: function() {
			var text = this.data('confirmtext');
			this.before("&nbsp; <a class='btn btn-default gridfield-better-buttons-undodelete ss-ui-button' href='javascript:void(0)'>"+text+"</a>");
			this._super();
		},

		onclick: function(e) {
			if (!this.getToggled()) {
                e.preventDefault();
                this.toggleText();
                $('.gridfield-better-buttons-undodelete').show();
                return false;
			}
            return this._super(e);
		},

		toggleText: function() {
			var text = this.text();
			this.text(this.data('toggletext'));
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
