(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$(document).on('click', '#addBtn', function ()
		{
			booknetic.loadModal('add_new', {});
		}).on('click', '#importBtn', function ()
		{
			booknetic.loadModal('import', {}, {'type': 'center', 'width': '650px'});
		}).on('click', '#fs_data_table_div .edit_action_btn', function ()
		{
			var rid = $(this).closest('tr').data('id');

			booknetic.loadModal('add_new', {'id': rid});
		});

	});

})(jQuery);