(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$('#invoice_body').summernote({
			placeholder: '',
			tabsize: 2,
			height: 350,
			toolbar: [
				['style', ['style']],
				['style', ['bold', 'italic', 'underline', 'clear']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'picture']],
				['view', ['fullscreen', 'codeview']],
				['height', ['height']]
			],
			hint: {
				mentions: $( '.fsn_shorttags_element' ).map( function ( i, val ) { return $( val ).text().match( /[a-zA-Z0-9_]+/g ); } ),
				match: /\B\{(\w*)$/,
				search: function (keyword, callback)
				{
					callback($.grep(this.mentions, function (item)
					{
						return item.indexOf(keyword) == 0;
					}));
				},
				content: function ( item )
				{
					return '{' + item + '}';
				}
			}
		});

		$(document).on('click', '#invoice_save_btn', function ()
		{
			var invoiceId = $('#invoice-script').data('id');
			var name = $('#input_name').val();
			var content = $('#invoice_body').summernote('code');

			booknetic.ajax('save', {
				id: invoiceId,
				name: name,
				content: content
			}, function ()
			{
				booknetic.toast(booknetic.__('changes_saved'), 'success');

				location.href = 'admin.php?page=booknetic&module=invoices';
			});
		}).on('click', '#download_preview', function ()
		{
			var invoiceId = $('#invoice-script').data('id');
			var name = $('#input_name').val();
			var content = $('#invoice_body').summernote('code');

			booknetic.ajax('save', {
				id: invoiceId,
				name: name,
				content: content
			}, function ( result )
			{
				var id = result['id']
				booknetic.loading(1);

				location.href = 'admin.php?page=booknetic&module=invoices&action=download&invoice_id=' + id;

				setTimeout(function ()
				{
					booknetic.loading(0);
				}, 4000);
			});
		});

	});

})(jQuery);
