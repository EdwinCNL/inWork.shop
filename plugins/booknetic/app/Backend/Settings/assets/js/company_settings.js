(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$('#booknetic_settings_area').on('click', '#company_image_img', function ()
		{
			$('#company_image_input').click();
		}).on('change', '#company_image_input', function ()
		{
			if( $(this)[0].files && $(this)[0].files[0] )
			{
				var reader = new FileReader();

				reader.onload = function(e)
				{
					$('#company_image_img').attr('src', e.target.result);
				}

				reader.readAsDataURL( $(this)[0].files[0] );
			}
		}).on('click', '.settings-save-btn', function ()
		{
			var company_name		= $("#input_company_name").val(),
				company_address		= $("#input_company_address").val(),
				company_phone		= $("#input_company_phone").val(),
				company_website		= $("#input_company_website").val(),
				company_image		= $("#company_image_input")[0].files[0];

			var data = new FormData();

			data.append('company_name', company_name);
			data.append('company_address', company_address);
			data.append('company_phone', company_phone);
			data.append('company_website', company_website);
			data.append('company_image', company_image);

			booknetic.ajax('save_company_settings', data, function()
			{
				booknetic.toast(booknetic.__('saved_successfully'), 'success');
			});
		});

	});

})(jQuery);