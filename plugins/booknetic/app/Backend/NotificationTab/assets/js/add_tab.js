(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		$("#add_tab_service_ids").select2({
			theme:			'bootstrap',
			placeholder:	booknetic.__('select'),
			allowClear:		true
		});
		$("#add_tab_staff_ids").select2({
			theme:			'bootstrap',
			placeholder:	booknetic.__('select'),
			allowClear:		true
		});
		$("#add_tab_location_ids").select2({
			theme:			'bootstrap',
			placeholder:	booknetic.__('select'),
			allowClear:		true
		});
		$("#add_tab_notification_types").select2({
			theme:			'bootstrap',
			placeholder:	booknetic.__('select'),
			allowClear:		true
		});

		$('.fs-modal').on('click', '#saveTabBtn', function(e)
		{
			e.preventDefault();
			var modal = $(this).closest('.modal');
			var id 			= $('#add_new_JS').data('tab-id'),
				name 		= $("#add_tab_name").val(),
				languages 	= $("#add_tab_language").val(),
				services 	= $("#add_tab_service_ids").val(),
				locations 	= $("#add_tab_location_ids").val(),
				notification_types 	= $("#add_tab_notification_types").val(),
				current_module      = $("#current_module").val(),
				staff 		= $("#add_tab_staff_ids").val();

			if( name == '' )
			{
				booknetic.toast(booknetic.__('fill_all_required'), 'unsuccess');
				return;
			}

			if (id){
				booknetic.confirm(booknetic.__('Your data may be lost after the update.'), 'success', 'notification', function()
				{
					booknetic.ajax('NotificationTab.save_tab', { id, name, languages,services,locations,staff,notification_types, current_module: current_module }, function(data) {
						location.href = '?page=booknetic&module='+current_module+'&tab_id='+data.id;
						booknetic.modalHide( modal );
					});
				},booknetic.__('OKAY'))
			}else {
				booknetic.ajax('NotificationTab.save_tab', { id, name, languages,services,locations,staff,notification_types, current_module: current_module }, function(data) {
					location.href = '?page=booknetic&module='+current_module+'&tab_id='+data.id;
					booknetic.modalHide( modal );
				});
			}

		});
	});

})(jQuery);