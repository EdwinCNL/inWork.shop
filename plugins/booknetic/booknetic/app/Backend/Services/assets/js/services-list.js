(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$('.m_head_actions').prepend('<button type="button" class="btn btn-primary btn-lg" id="addCategoryBtn"><i class="fa fa-plus"></i> '+booknetic.__('add_category')+'</button>');
		$('.m_head_actions').prepend('<a href="?page=booknetic&module=services&view=org" type="button" class="btn btn-outline-secondary btn-lg">'+booknetic.__('graphic_view')+'</a>');

		$(document).on('click', '#addBtn', function()
		{
			booknetic.loadModal('add_new', {});
		}).on('click', '#fs_data_table_div .edit_action_btn', function()
		{
			var rid = $(this).closest('tr').data('id');

			booknetic.loadModal('add_new', {'id': rid});
		}).on('click', '#addCategoryBtn', function()
		{
			booknetic.loadModal('add_new_category', {'id': 0});
		});


	});

})(jQuery);